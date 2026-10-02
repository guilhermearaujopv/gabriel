<?php
require_once __DIR__ . '/../models/produto.php';
require_once __DIR__ . '/../models/venda.php';

class VendaController
{
    private const PAGAMENTOS = [
        'PIX',
        'Dinheiro',
        'Cartão de crédito',
        'Cartão de débito',
        'Financiamento',
        'Transferência bancária',
    ];

    // 🔹 Tela de venda: carros disponíveis + carrinho + dados do cliente
    public function index(): void
    {
        $this->check();

        $produtoModel = new Produto();
        $vendaModel = new Venda();

        $produtos = $produtoModel->listarTodos();

        // Monta o carrinho só com carros que ainda existem no estoque
        $idsNoCarrinho = array_map('intval', $_SESSION['carrinho'] ?? []);
        $carrinho = [];
        $total = 0.0;

        foreach ($produtos as $p) {
            if (in_array((int)$p['id_carro'], $idsNoCarrinho, true)) {
                $carrinho[] = $p;
                $total += (float)$p['preco'];
            }
        }

        $_SESSION['carrinho'] = array_map(fn($p) => (int)$p['id_carro'], $carrinho);

        // Mensagem de erro e dados digitados (vêm da tentativa anterior de finalizar)
        $erro = $_SESSION['venda_erro'] ?? null;
        $form = $_SESSION['venda_form'] ?? [];
        unset($_SESSION['venda_erro'], $_SESSION['venda_form']);

        try {
            $ultimas = $vendaModel->listarUltimas(8);
        } catch (PDOException $e) {
            $ultimas = [];
            $erro = $erro ?? 'Não foi possível carregar as últimas vendas.';
        }

        $pagamentos = self::PAGAMENTOS;

        require_once __DIR__ . '/../views/venda.php';
    }

    // 🔹 Adicionar um carro ao carrinho
    public function adicionar(): void
    {
        $this->check();

        $id = (int)($_GET['id'] ?? 0);

        if ($id > 0 && (new Produto())->buscarPorId($id)) {
            $carrinho = array_map('intval', $_SESSION['carrinho'] ?? []);

            if (!in_array($id, $carrinho, true)) {
                $carrinho[] = $id;
            }

            $_SESSION['carrinho'] = $carrinho;
        }

        header("Location: index.php?controller=venda&action=index");
        exit;
    }

    // 🔹 Tirar um carro do carrinho
    public function remover(): void
    {
        $this->check();

        $id = (int)($_GET['id'] ?? 0);
        $carrinho = array_map('intval', $_SESSION['carrinho'] ?? []);

        $_SESSION['carrinho'] = array_values(array_filter(
            $carrinho,
            fn($item) => $item !== $id
        ));

        header("Location: index.php?controller=venda&action=index");
        exit;
    }

    // 🔹 Esvaziar o carrinho
    public function limpar(): void
    {
        $this->check();

        unset($_SESSION['carrinho'], $_SESSION['venda_form']);

        header("Location: index.php?controller=venda&action=index");
        exit;
    }

    // 🔹 Finalizar a venda (somente POST)
    public function finalizar(): void
    {
        $this->check();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=venda&action=index");
            exit;
        }

        $nome = trim($_POST['cliente_nome'] ?? '');
        $cpf = preg_replace('/\D/', '', $_POST['cliente_cpf'] ?? '');
        $pagamento = $_POST['forma_pagamento'] ?? '';
        $ids = array_map('intval', $_SESSION['carrinho'] ?? []);

        // Guarda o que foi digitado para não perder se der erro
        $_SESSION['venda_form'] = [
            'cliente_nome'    => $nome,
            'cliente_cpf'     => $cpf,
            'forma_pagamento' => $pagamento,
        ];

        if (empty($ids)) {
            $this->voltar('Adicione ao menos um carro ao carrinho.');
        }

        if (mb_strlen($nome) < 3) {
            $this->voltar('Informe o nome completo do cliente.');
        }

        if (!$this->cpfValido($cpf)) {
            $this->voltar('CPF inválido. Confira os números digitados.');
        }

        if (!in_array($pagamento, self::PAGAMENTOS, true)) {
            $this->voltar('Escolha uma forma de pagamento.');
        }

        try {
            $cpfFormatado = preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', $cpf);

            $idNota = (new Venda())->finalizar(
                $nome,
                $cpfFormatado,
                $pagamento,
                $ids,
                (int)$_SESSION['usuario_id']
            );
        } catch (RuntimeException $e) {
            $this->voltar($e->getMessage());
        } catch (Throwable $e) {
            error_log('Erro ao finalizar venda: ' . $e->getMessage());
            $this->voltar('Não foi possível concluir a venda. Tente novamente.');
        }

        unset($_SESSION['carrinho'], $_SESSION['venda_form']);

        header("Location: index.php?controller=venda&action=nota&id=" . $idNota);
        exit;
    }

    // 🔹 Nota de venda
    public function nota(): void
    {
        $this->check();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            die("ID inválido.");
        }

        $nota = (new Venda())->buscarNota($id);

        if (!$nota) {
            die("Venda não encontrada.");
        }

        require_once __DIR__ . '/../views/nota.php';
    }

    private function voltar(string $mensagem): void
    {
        $_SESSION['venda_erro'] = $mensagem;

        header("Location: index.php?controller=venda&action=index");
        exit;
    }

    private function cpfValido(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;

            for ($i = 0; $i < $t; $i++) {
                $soma += (int)$cpf[$i] * (($t + 1) - $i);
            }

            $digito = ((10 * $soma) % 11) % 10;

            if ((int)$cpf[$t] !== $digito) {
                return false;
            }
        }

        return true;
    }

    private function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=auth&action=form");
            exit;
        }
    }
}
