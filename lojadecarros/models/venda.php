<?php
require_once __DIR__ . '/../config/db.php';

class Venda
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    // 🔹 Registrar a venda usando as tabelas que já existem no banco:
    //    clientes -> vendas (1 linha por carro) -> pagamentos -> nota_fiscal -> itens_nota_fiscal
    //    Tudo dentro de uma transação: se algo falhar, nada é gravado.
    //    Os carros NÃO são apagados (vendas e itens_nota_fiscal têm chave estrangeira para carros);
    //    um carro vendido simplesmente deixa de aparecer nas listas.
    public function finalizar(
        string $clienteNome,
        string $clienteCpf,
        string $formaPagamento,
        array $idsCarros,
        int $idUsuario
    ): int {

        $this->conn->beginTransaction();

        try {
            // Trava o carro e confirma que ainda não foi vendido.
            // O preço vem SEMPRE do banco, nunca do navegador.
            $busca = $this->conn->prepare("
                SELECT c.id_carro, c.preco
                FROM carros c
                WHERE c.id_carro = :id
                  AND NOT EXISTS (
                      SELECT 1 FROM vendas v WHERE v.id_carro = c.id_carro
                  )
                FOR UPDATE
            ");

            $carros = [];
            $total = 0.0;

            foreach ($idsCarros as $id) {
                $busca->execute([':id' => $id]);
                $carro = $busca->fetch(PDO::FETCH_ASSOC);

                if (!$carro) {
                    throw new RuntimeException('Um dos carros do carrinho já foi vendido ou não existe mais.');
                }

                $carros[] = $carro;
                $total += (float)$carro['preco'];
            }

            $idCliente = $this->obterCliente($clienteNome, $clienteCpf);

            $insereVenda = $this->conn->prepare("
                INSERT INTO vendas (id_usuario, id_cliente, id_carro, valor, data_venda)
                VALUES (:usuario, :cliente, :carro, :valor, CURDATE())
            ");

            $inserePagamento = $this->conn->prepare("
                INSERT INTO pagamentos (id_venda, forma_pagamento, parcelas, juros, status_pagamento)
                VALUES (:venda, :forma, 1, 0, :status)
            ");

            // Se a tabela estoque for usada no futuro, já fica marcada como vendido
            $marcaEstoque = $this->conn->prepare("
                UPDATE estoque
                SET status_veiculo = 'vendido', data_saida = CURDATE()
                WHERE id_carro = :carro
            ");

            $statusPagamento = ($formaPagamento === 'Financiamento') ? 'pendente' : 'pago';
            $idPrimeiraVenda = 0;

            foreach ($carros as $carro) {
                $insereVenda->execute([
                    ':usuario' => $idUsuario,
                    ':cliente' => $idCliente,
                    ':carro'   => $carro['id_carro'],
                    ':valor'   => $carro['preco'],
                ]);

                $idVenda = (int)$this->conn->lastInsertId();

                if ($idPrimeiraVenda === 0) {
                    $idPrimeiraVenda = $idVenda;
                }

                $inserePagamento->execute([
                    ':venda'  => $idVenda,
                    ':forma'  => $formaPagamento,
                    ':status' => $statusPagamento,
                ]);

                $marcaEstoque->execute([':carro' => $carro['id_carro']]);
            }

            // Uma nota por carrinho. nota_fiscal guarda só uma venda (a primeira);
            // todos os carros aparecem em itens_nota_fiscal.
            $stmt = $this->conn->prepare("
                INSERT INTO nota_fiscal (id_venda, valor_total)
                VALUES (:venda, :total)
            ");
            $stmt->execute([
                ':venda' => $idPrimeiraVenda,
                ':total' => $total,
            ]);

            $idNota = (int)$this->conn->lastInsertId();

            $stmt = $this->conn->prepare("
                UPDATE nota_fiscal
                SET numero_nota = :numero
                WHERE id_nota = :id
            ");
            $stmt->execute([
                ':numero' => date('Y') . str_pad((string)$idNota, 6, '0', STR_PAD_LEFT),
                ':id'     => $idNota,
            ]);

            $insereItem = $this->conn->prepare("
                INSERT INTO itens_nota_fiscal (id_nota, id_carro, quantidade, valor_unitario)
                VALUES (:nota, :carro, 1, :valor)
            ");

            foreach ($carros as $carro) {
                $insereItem->execute([
                    ':nota'  => $idNota,
                    ':carro' => $carro['id_carro'],
                    ':valor' => $carro['preco'],
                ]);
            }

            $this->conn->commit();

            return $idNota;

        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    // 🔹 Cliente: reaproveita pelo CPF (campo único) ou cadastra um novo
    private function obterCliente(string $nome, string $cpf): int
    {
        $stmt = $this->conn->prepare("SELECT id_cliente FROM clientes WHERE cpf = :cpf");
        $stmt->execute([':cpf' => $cpf]);
        $id = $stmt->fetchColumn();

        if ($id !== false) {
            return (int)$id;
        }

        $stmt = $this->conn->prepare("INSERT INTO clientes (nome, cpf) VALUES (:nome, :cpf)");
        $stmt->execute([
            ':nome' => $nome,
            ':cpf'  => $cpf,
        ]);

        return (int)$this->conn->lastInsertId();
    }

    // 🔹 Buscar uma nota com cliente, pagamento, vendedor e itens
    public function buscarNota(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT
                nf.id_nota,
                nf.numero_nota,
                nf.data_emissao,
                nf.valor_total,
                c.nome AS cliente_nome,
                c.cpf  AS cliente_cpf,
                u.nome AS vendedor_nome,
                (
                    SELECT p.forma_pagamento
                    FROM pagamentos p
                    WHERE p.id_venda = nf.id_venda
                    LIMIT 1
                ) AS forma_pagamento
            FROM nota_fiscal nf
            JOIN vendas v   ON v.id_venda = nf.id_venda
            JOIN clientes c ON c.id_cliente = v.id_cliente
            JOIN usuario u  ON u.id = v.id_usuario
            WHERE nf.id_nota = :id
        ");
        $stmt->execute([':id' => $id]);
        $nota = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$nota) {
            return null;
        }

        $stmt = $this->conn->prepare("
            SELECT
                i.quantidade,
                i.valor_unitario,
                c.marca,
                c.modelo,
                c.ano,
                c.cor,
                c.n_chassi
            FROM itens_nota_fiscal i
            JOIN carros c ON c.id_carro = i.id_carro
            WHERE i.id_nota = :id
            ORDER BY i.id_item
        ");
        $stmt->execute([':id' => $id]);

        $nota['itens'] = array_map(function (array $i): array {
            $descricao = trim($i['marca'] . ' ' . $i['modelo'] . ' ' . $i['ano']);

            if (!empty($i['cor'])) {
                $descricao .= ' - ' . $i['cor'];
            }

            return [
                'descricao' => $descricao,
                'n_chassi'  => $i['n_chassi'],
                'valor'     => (float)$i['valor_unitario'] * (int)$i['quantidade'],
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));

        return $nota;
    }

    // 🔹 Últimas notas emitidas
    public function listarUltimas(int $limite = 8): array
    {
        $stmt = $this->conn->prepare("
            SELECT
                nf.id_nota,
                nf.numero_nota,
                nf.data_emissao,
                nf.valor_total,
                c.nome AS cliente_nome,
                (
                    SELECT p.forma_pagamento
                    FROM pagamentos p
                    WHERE p.id_venda = nf.id_venda
                    LIMIT 1
                ) AS forma_pagamento
            FROM nota_fiscal nf
            JOIN vendas v   ON v.id_venda = nf.id_venda
            JOIN clientes c ON c.id_cliente = v.id_cliente
            ORDER BY nf.id_nota DESC
            LIMIT :limite
        ");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Números dos cards do dashboard
    //    vendas_mes: carros vendidos no mês atual (cada linha de "vendas" é um carro)
    //    produtos:   carros ainda disponíveis (sem venda registrada)
    public function resumoDashboard(): array
    {
        $vendasMes = (int)$this->conn->query("
            SELECT COUNT(*)
            FROM vendas
            WHERE data_venda >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
              AND data_venda <  DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
        ")->fetchColumn();

        $produtos = (int)$this->conn->query("
            SELECT COUNT(*)
            FROM carros c
            WHERE NOT EXISTS (
                SELECT 1 FROM vendas v WHERE v.id_carro = c.id_carro
            )
        ")->fetchColumn();

        return [
            'vendas_mes' => $vendasMes,
            'produtos'   => $produtos,
        ];
    }
}
