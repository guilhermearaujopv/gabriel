<?php
require_once __DIR__ . '/../config/db.php';

class Produto
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    // 🔹 Listar os carros disponíveis
    //    Carros que já têm venda registrada ficam guardados no banco (o histórico
    //    de vendas depende deles), mas não aparecem mais na lista.
    public function listarTodos(): array
    {
        $sql = "
            SELECT c.*
            FROM carros c
            WHERE NOT EXISTS (
                SELECT 1 FROM vendas v WHERE v.id_carro = c.id_carro
            )
            ORDER BY c.id_carro DESC
        ";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Buscar carro por ID
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM carros
            WHERE id_carro = :id
        ");

        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // 🔹 Inserir novo carro (COM IMAGEM)
    public function inserir(
        string $marca,
        string $modelo,
        int $ano,
        ?string $cor,
        ?string $combustivel,
        ?int $km,
        string $chassi,
        float $preco,
        ?string $imagem
    ): int {

        $stmt = $this->conn->prepare("
            INSERT INTO carros
            (
                marca,
                modelo,
                ano,
                cor,
                combustivel,
                km,
                n_chassi,
                preco,
                imagem
            )
            VALUES
            (
                :marca,
                :modelo,
                :ano,
                :cor,
                :combustivel,
                :km,
                :chassi,
                :preco,
                :imagem
            )
        ");

        $stmt->execute([
            ':marca'        => $marca,
            ':modelo'       => $modelo,
            ':ano'          => $ano,
            ':cor'          => $cor,
            ':combustivel'  => $combustivel,
            ':km'           => $km,
            ':chassi'       => $chassi,
            ':preco'        => $preco,
            ':imagem'       => $imagem
        ]);

        return (int)$this->conn->lastInsertId();
    }

    // 🔹 Atualizar carro (COM IMAGEM)
    public function atualizar(
        int $id,
        string $marca,
        string $modelo,
        int $ano,
        ?string $cor,
        ?string $combustivel,
        ?int $km,
        string $chassi,
        float $preco,
        ?string $imagem
    ): void {

        $stmt = $this->conn->prepare("
            UPDATE carros SET
                marca = :marca,
                modelo = :modelo,
                ano = :ano,
                cor = :cor,
                combustivel = :combustivel,
                km = :km,
                n_chassi = :chassi,
                preco = :preco,
                imagem = :imagem
            WHERE id_carro = :id
        ");

        $stmt->execute([
            ':id'           => $id,
            ':marca'        => $marca,
            ':modelo'       => $modelo,
            ':ano'          => $ano,
            ':cor'          => $cor,
            ':combustivel'  => $combustivel,
            ':km'           => $km,
            ':chassi'       => $chassi,
            ':preco'        => $preco,
            ':imagem'       => $imagem
        ]);
    }

    // 🔹 Deletar carro
    public function deletar(int $id): bool
    {
        $stmt = $this->conn->prepare("
            DELETE FROM carros
            WHERE id_carro = :id
        ");

        return $stmt->execute([':id' => $id]);
    }
}
