<?php
$h = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$brl = fn($v) => 'R$ ' . number_format((float)$v, 2, ',', '.');

$nome = $_SESSION['nome'] ?? 'Usuário';
$perfil = $_SESSION['perfil'] ?? 'vendedor';

$produtos = $produtos ?? [];
$carrinho = $carrinho ?? [];
$total = $total ?? 0;
$ultimas = $ultimas ?? [];
$pagamentos = $pagamentos ?? [];
$erro = $erro ?? null;
$form = $form ?? [];

$idsNoCarrinho = array_map(fn($c) => (int)$c['id_carro'], $carrinho);
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Vendas - Prime Motors</title>
    <link rel="icon" type="image/png" sizes="512x512" href="public/assets/css/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="public/assets/css/venda.css">
</head>

<body>

    <!-- Fundo com imagem (definido no CSS) -->
    <div class="fundo" aria-hidden="true"></div>

    <div class="container">

        <header class="topbar">
            <div class="brand">
                <img src="public/assets/css/logoprimemotors-removebg-preview.png"
                     alt="Prime Motors"
                     class="logo">
                <div class="brand-text">
                    <h1>Nova venda</h1>
                    <small>Monte o carrinho e registre a venda</small>
                </div>
            </div>

            <div class="pill">
                <span class="pill-icon" aria-hidden="true">
                    <svg class="ico" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path></svg>
                </span>
                <span class="pill-texto">
                    Logado como <strong><?= $h($nome) ?></strong>
                    (<?= $h($perfil) ?>)
                </span>
                <span class="pill-sep">•</span>
                <a href="index.php?controller=auth&action=logout">Sair</a>
            </div>
        </header>

        <!-- Atalhos dos módulos -->
        <nav class="nav" aria-label="Módulos do sistema">
            <a href="index.php?controller=auth&action=dashboard">
                <span class="nav-icon" aria-hidden="true">
                    <svg class="ico" viewBox="0 0 24 24"><path d="M3 11 12 3l9 8"></path><path d="M5 10v10h14V10"></path></svg>
                </span>
                <span class="nav-info"><span class="nav-titulo">Dashboard</span></span>
                <svg class="ico nav-seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>
            </a>
            <a href="index.php?controller=produto&action=index">
                <span class="nav-icon" aria-hidden="true">
                    <svg class="ico" viewBox="0 0 24 24"><path d="M21 8 12 3 3 8v8l9 5 9-5V8Z"></path><path d="m3 8 9 5 9-5"></path><path d="M12 13v8"></path></svg>
                </span>
                <span class="nav-info"><span class="nav-titulo">Produtos / Categorias</span></span>
                <svg class="ico nav-seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>
            </a>
            <a href="index.php?controller=entrada&action=index">
                <span class="nav-icon" aria-hidden="true">
                    <svg class="ico" viewBox="0 0 24 24"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M4 20h16"></path></svg>
                </span>
                <span class="nav-info"><span class="nav-titulo">Entradas</span></span>
                <svg class="ico nav-seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>
            </a>
            <a href="index.php?controller=venda&action=index" class="ativo" aria-current="page">
                <span class="nav-icon" aria-hidden="true">
                    <svg class="ico" viewBox="0 0 24 24"><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle><path d="M3 3h2l2.6 12.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.5L21 8H6"></path></svg>
                </span>
                <span class="nav-info"><span class="nav-titulo">Vendas</span></span>
                <svg class="ico nav-seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>
            </a>
            <a href="index.php?controller=relatorio&action=index">
                <span class="nav-icon" aria-hidden="true">
                    <svg class="ico" viewBox="0 0 24 24"><path d="M5 20v-9"></path><path d="M12 20V4"></path><path d="M19 20v-6"></path><path d="M3 20h18"></path></svg>
                </span>
                <span class="nav-info"><span class="nav-titulo">Relatórios</span></span>
                <svg class="ico nav-seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>
            </a>
        </nav>

        <?php if ($erro): ?>
            <div class="alerta" role="alert"><?= $h($erro) ?></div>
        <?php endif; ?>

        <div class="venda-grid">

            <!-- CARROS DISPONÍVEIS -->
            <section class="painel">
                <h3>Carros disponíveis <span class="contador"><?= count($produtos) ?></span></h3>

                <?php if (empty($produtos)): ?>
                    <p class="vazio">Nenhum carro em estoque no momento.</p>
                <?php endif; ?>

                <?php foreach ($produtos as $p): ?>
                    <?php $noCarrinho = in_array((int)$p['id_carro'], $idsNoCarrinho, true); ?>
                    <div class="carro">
                        <?php if (!empty($p['imagem'])): ?>
                            <img class="carro-foto" src="<?= $h($p['imagem']) ?>" alt="Foto do carro">
                        <?php else: ?>
                            <div class="carro-foto sem-foto">Sem foto</div>
                        <?php endif; ?>

                        <div class="carro-info">
                            <div class="carro-nome"><?= $h($p['marca']) ?> <?= $h($p['modelo']) ?></div>
                            <div class="carro-meta">
                                <?= $h($p['ano']) ?>
                                <?php if (!empty($p['cor'])): ?> • <?= $h($p['cor']) ?><?php endif; ?>
                                • <?= number_format((int)$p['km'], 0, ',', '.') ?> km
                                <?php if (!empty($p['combustivel'])): ?> • <?= $h($p['combustivel']) ?><?php endif; ?>
                            </div>
                        </div>

                        <div class="carro-acao">
                            <div class="carro-preco"><?= $brl($p['preco']) ?></div>
                            <?php if ($noCarrinho): ?>
                                <span class="no-carrinho">No carrinho</span>
                            <?php else: ?>
                                <a class="btn btn-primary btn-mini"
                                   href="index.php?controller=venda&action=adicionar&id=<?= (int)$p['id_carro'] ?>">
                                    Adicionar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>

            <!-- CARRINHO + CLIENTE -->
            <section class="painel">
                <h3>Carrinho <span class="contador"><?= count($carrinho) ?></span></h3>

                <?php if (empty($carrinho)): ?>
                    <p class="vazio">Carrinho vazio. Adicione um carro da lista ao lado.</p>
                <?php else: ?>
                    <?php foreach ($carrinho as $c): ?>
                        <div class="item">
                            <div>
                                <div class="item-nome"><?= $h($c['marca']) ?> <?= $h($c['modelo']) ?> <?= $h($c['ano']) ?></div>
                                <a class="item-remover"
                                   href="index.php?controller=venda&action=remover&id=<?= (int)$c['id_carro'] ?>">
                                    Remover
                                </a>
                            </div>
                            <div class="item-valor"><?= $brl($c['preco']) ?></div>
                        </div>
                    <?php endforeach; ?>

                    <div class="total">
                        <span>Total</span>
                        <strong><?= $brl($total) ?></strong>
                    </div>

                    <a class="limpar" href="index.php?controller=venda&action=limpar">Esvaziar carrinho</a>
                <?php endif; ?>

                <form class="form-cliente" method="post" action="index.php?controller=venda&action=finalizar">

                    <h4>Dados do cliente</h4>

                    <div class="campo">
                        <label for="cliente_nome">Nome completo</label>
                        <input type="text" id="cliente_nome" name="cliente_nome" required
                               value="<?= $h($form['cliente_nome'] ?? '') ?>">
                    </div>

                    <div class="campo">
                        <label for="cliente_cpf">CPF</label>
                        <input type="text" id="cliente_cpf" name="cliente_cpf" required
                               inputmode="numeric" maxlength="14" placeholder="000.000.000-00"
                               pattern="\d{3}\.?\d{3}\.?\d{3}-?\d{2}"
                               title="Digite os 11 números do CPF"
                               value="<?= $h($form['cliente_cpf'] ?? '') ?>">
                    </div>

                    <div class="campo">
                        <label for="forma_pagamento">Forma de pagamento</label>
                        <select id="forma_pagamento" name="forma_pagamento" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($pagamentos as $fp): ?>
                                <option value="<?= $h($fp) ?>"
                                    <?= (($form['forma_pagamento'] ?? '') === $fp) ? 'selected' : '' ?>>
                                    <?= $h($fp) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button class="btn btn-primary btn-grande" type="submit" <?= empty($carrinho) ? 'disabled' : '' ?>>
                        Finalizar venda e gerar nota
                    </button>

                </form>
            </section>

        </div>

        <!-- ÚLTIMAS VENDAS -->
        <section class="painel">
            <h3>Últimas vendas</h3>

            <?php if (empty($ultimas)): ?>
                <p class="vazio">Nenhuma venda registrada ainda.</p>
            <?php else: ?>
                <div class="tabela-wrap">
                    <table class="tabela">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Cliente</th>
                                <th>Pagamento</th>
                                <th>Data</th>
                                <th class="num">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimas as $u): ?>
                                <tr>
                                    <td><?= $h($u['numero_nota']) ?></td>
                                    <td><?= $h($u['cliente_nome']) ?></td>
                                    <td><?= $h($u['forma_pagamento']) ?></td>
                                    <td><?= $h(date('d/m/Y H:i', strtotime($u['data_emissao']))) ?></td>
                                    <td class="num"><?= $brl($u['valor_total']) ?></td>
                                    <td class="num">
                                        <a class="btn btn-mini"
                                           href="index.php?controller=venda&action=nota&id=<?= (int)$u['id_nota'] ?>">
                                            Ver nota
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <footer class="rodape">&copy; 2026 Prime Motors. Todos os direitos reservados.</footer>

    </div>

</body>

</html>
