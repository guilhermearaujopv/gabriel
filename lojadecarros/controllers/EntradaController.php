<?php
require_once __DIR__ . '/../models/entrada.php';

class EntradaController
{

    public function index()
    {
        $model = new Entrada();
        $entradas = $model->listarTodas();
        require_once __DIR__ . '/../views/entradas/listar.php';
    }

    public function criar()
    {
        require_once __DIR__ . '/../views/entradas/form.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produto_id = $_POST['produto_id'] ?? 0;
            $quantidade = $_POST['quantidade'] ?? 0;
            $valor_compra = $_POST['valor_compra'] ?? 0.0;
            $data = date('Y-m-d H:i:s');

            $model = new Entrada();

            if ($model->registrar($produto_id, $quantidade, $valor_compra, $data)) {
                header("Location: index.php?controller=entrada&action=index&sucesso=1");
                exit;
            } else {
                die("Erro ao registrar entrada.");
            }
        }
    }
}
