<?php
require_once __DIR__ . '/../models/produto.php';

class ProdutoController
{
    public function index(): void
    {
        $this->check();

        $produtoModel = new Produto();

        $produtos = $produtoModel->listarTodos();

        $editar = null;
        if (isset($_GET['id'])) {
            $editar = $produtoModel->buscarPorId((int)$_GET['id']);
        }

        require_once __DIR__ . '/../views/produtos.php';
    }

    public function salvar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int)($_POST['id'] ?? 0);

        $marca = trim($_POST['marca'] ?? '');
        $modelo = trim($_POST['modelo'] ?? '');
        $ano = (int)($_POST['ano'] ?? 0);
        $cor = trim($_POST['cor'] ?? '');
        $combustivel = trim($_POST['combustivel'] ?? '');
        $km = (int)($_POST['km'] ?? 0);
        $chassi = trim($_POST['n_chassi'] ?? '');
        $preco = (float)($_POST['preco'] ?? 0);

        if (
            $marca === "" ||
            $modelo === "" ||
            $ano <= 0 ||
            $preco <= 0
        ) {
            die("Dados inválidos.");
        }

        $produtoModel = new Produto();

        $imagemPath = null;

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {

    $pasta = "uploads/";

    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }

    $nomeArquivo = uniqid() . "_" . basename($_FILES['imagem']['name']);
    $caminhoCompleto = $pasta . $nomeArquivo;

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
        $imagemPath = $caminhoCompleto;
    }

}

        if ($id > 0) {
            $produtoModel->atualizar(
    $id,
    $marca,
    $modelo,
    $ano,
    $cor,
    $combustivel,
    $km,
    $chassi,
    $preco,
    $imagemPath
);
        } else {
           $produtoModel->inserir(
    $marca,
    $modelo,
    $ano,
    $cor,
    $combustivel,
    $km,
    $chassi,
    $preco,
    $imagemPath
);
        }

        header("Location: index.php?controller=produto&action=index");
        exit;
    }

    public function deletar(): void
    {
        $this->check();
        $this->onlyAdmin();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            die("ID inválido.");
        }

        $produtoModel = new Produto();
        $produtoModel->deletar($id);

        header("Location: index.php?controller=produto&action=index");
        exit;
    }

    private function check(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?controller=auth&action=form");
            exit;
        }
    }

    private function onlyAdmin(): void
    {
        if (($_SESSION['perfil'] ?? '') !== 'admin') {
            die("Acesso negado.");
        }
    }
}