<?php
require_once __DIR__ . '/../models/venda.php';
require_once __DIR__ . '/../models/produto.php';

class RelatorioController
{

    public function index()
    {
        require_once __DIR__ . '/../views/relatorios/index.php';
    }

    public function vendas()
    {
        $data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $data_fim = $_GET['data_fim'] ?? date('Y-m-t');

        $vendaModel = new Venda();
        $relatorio = $vendaModel->buscarVendasPorPeriodo($data_inicio, $data_fim);

        require_once __DIR__ . '/../views/relatorios/vendas.php';
    }

    public function estoque()
    {
        $produtoModel = new Produto();
        $estoque = $produtoModel->listarTodos();

        require_once __DIR__ . '/../views/relatorios/estoque.php';
    }

    public function financeiro()
    {
        $vendaModel = new Venda();
        $totalVendas = $vendaModel->calcularTotalMensal(date('m'), date('Y'));

        require_once __DIR__ . '/../views/relatorios/financeiro.php';
    }
}
