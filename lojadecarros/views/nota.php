<?php
$h = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$brl = fn($v) => 'R$ ' . number_format((float)$v, 2, ',', '.');

$numero = $nota['numero_nota'] ?: str_pad((string)$nota['id_nota'], 6, '0', STR_PAD_LEFT);
$cpfFormatado = preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', (string)$nota['cliente_cpf']);
$dataFormatada = date('d/m/Y \à\s H:i', strtotime($nota['data_emissao']));
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Nota de venda Nº <?= $h($numero) ?> - Prime Motors</title>
    <link rel="icon" type="image/png" sizes="512x512" href="public/assets/css/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="public/assets/css/nota.css">
</head>

<body>

    <!-- Fundo com imagem (definido no CSS) -->
    <div class="fundo" aria-hidden="true"></div>

    <main class="nota">

        <header class="nota-topo">
            <img src="public/assets/css/logoprimemotors-removebg-preview.png"
                 alt="Prime Motors"
                 class="nota-logo">
            <div class="nota-titulo">
                <span>Nota de venda</span>
                <strong>Nº <?= $h($numero) ?></strong>
            </div>
        </header>

        <section class="nota-corpo">

            <div class="nota-dados">
                <div class="dado">
                    <span class="dado-rotulo">Cliente</span>
                    <span class="dado-valor"><?= $h($nota['cliente_nome']) ?></span>
                </div>
                <div class="dado">
                    <span class="dado-rotulo">CPF</span>
                    <span class="dado-valor"><?= $h($cpfFormatado) ?></span>
                </div>
                <div class="dado">
                    <span class="dado-rotulo">Forma de pagamento</span>
                    <span class="dado-valor"><?= $h($nota['forma_pagamento']) ?></span>
                </div>
                <div class="dado">
                    <span class="dado-rotulo">Data da venda</span>
                    <span class="dado-valor"><?= $h($dataFormatada) ?></span>
                </div>
                <div class="dado">
                    <span class="dado-rotulo">Vendedor(a)</span>
                    <span class="dado-valor"><?= $h($nota['vendedor_nome'] ?? '-') ?></span>
                </div>
            </div>

            <div class="nota-tabela-wrap">
                <table class="nota-itens">
                    <thead>
                        <tr>
                            <th>Veículo</th>
                            <th>Nº do chassi</th>
                            <th class="num">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($nota['itens'] as $item): ?>
                            <tr>
                                <td><?= $h($item['descricao']) ?></td>
                                <td><?= $h($item['n_chassi'] ?: '-') ?></td>
                                <td class="num"><?= $brl($item['valor']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">Total</td>
                            <td class="num"><?= $brl($nota['valor_total']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <p class="nota-aviso">
                Este documento é um comprovante de venda e não substitui a nota fiscal eletrônica (NF-e).
            </p>

        </section>

    </main>

    <div class="nota-acoes nao-imprimir">
        <button type="button" class="btn btn-primary" onclick="window.print()">Imprimir</button>
        <a class="btn" href="index.php?controller=venda&action=index">Nova venda</a>
        <a class="btn" href="index.php?controller=auth&action=dashboard">Dashboard</a>
    </div>

</body>

</html>
