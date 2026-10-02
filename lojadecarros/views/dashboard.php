<?php
$nome = $_SESSION['nome'] ?? 'Usuário';
$perfil = $_SESSION['perfil'] ?? 'vendedor';

// Números dos cards. Se algo falhar, mostra 0 e o dashboard continua abrindo.
$resumo = ['vendas_mes' => 0, 'produtos' => 0];
$modeloVenda = __DIR__ . '/../models/venda.php';

if (is_file($modeloVenda)) {
    try {
        require_once $modeloVenda;
        $resumo = (new Venda())->resumoDashboard();
    } catch (Throwable $e) {
        error_log('Dashboard: ' . $e->getMessage());
    }
}
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Dashboard - Prime Motors</title>
    <link rel="icon" type="image/png" sizes="512x512" href="public/assets/css/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="public/assets/css/dashboard1.css">
</head>

<body>

    <!-- Fundo com imagem de carro (definido no CSS) -->
    <div class="fundo" aria-hidden="true"></div>

    <div class="container">

        <header class="topbar">
            <div class="brand">
                <img src="public/assets/css/logoprimemotors-removebg-preview.png"
                     alt="Prime Motors"
                     class="logo">
                <div class="brand-text">
                    <h1>Dashboard</h1>
                    <small>Painel do sistema</small>
                </div>
            </div>

            <div class="pill">
                <span class="pill-icon" aria-hidden="true">
                    <svg class="ico" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path></svg>
                </span>
                <span class="pill-texto">
                    Logado como <strong><?php echo htmlspecialchars($nome); ?></strong>
                    (<?php echo htmlspecialchars($perfil); ?>)
                </span>
                <span class="pill-sep">•</span>
                <a href="/lojadecarros/index.php?controller=auth&action=logout">Sair</a>
            </div>
        </header>

        <main class="card">

            <section class="boas-vindas">
                <h2>Bem-vindo(a), <?php echo htmlspecialchars($nome); ?>!</h2>
                <p>Escolha um módulo para continuar.</p>
            </section>

            <nav class="nav" aria-label="Módulos do sistema">

                <a href="/lojadecarros/index.php?controller=produto&action=index">
                    <span class="nav-icon" aria-hidden="true">
                        <svg class="ico" viewBox="0 0 24 24"><path d="M21 8 12 3 3 8v8l9 5 9-5V8Z"></path><path d="m3 8 9 5 9-5"></path><path d="M12 13v8"></path></svg>
                    </span>
                    <span class="nav-info">
                        <span class="nav-titulo">Produtos / Categorias</span>
                        <span class="nav-desc">Cadastro e organização do catálogo</span>
                    </span>
                    <svg class="ico nav-seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>
                </a>

                <a href="/lojadecarros/index.php?controller=entrada&action=index">
                    <span class="nav-icon" aria-hidden="true">
                        <svg class="ico" viewBox="0 0 24 24"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M4 20h16"></path></svg>
                    </span>
                    <span class="nav-info">
                        <span class="nav-titulo">Entradas</span>
                        <span class="nav-desc">Reposição e entrada de estoque</span>
                    </span>
                    <svg class="ico nav-seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>
                </a>

                <a href="/lojadecarros/index.php?controller=venda&action=index">
                    <span class="nav-icon" aria-hidden="true">
                        <svg class="ico" viewBox="0 0 24 24"><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle><path d="M3 3h2l2.6 12.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.5L21 8H6"></path></svg>
                    </span>
                    <span class="nav-info">
                        <span class="nav-titulo">Vendas</span>
                        <span class="nav-desc">Registro e acompanhamento</span>
                    </span>
                    <svg class="ico nav-seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>
                </a>

                <a href="/lojadecarros/index.php?controller=relatorio&action=index">
                    <span class="nav-icon" aria-hidden="true">
                        <svg class="ico" viewBox="0 0 24 24"><path d="M5 20v-9"></path><path d="M12 20V4"></path><path d="M19 20v-6"></path><path d="M3 20h18"></path></svg>
                    </span>
                    <span class="nav-info">
                        <span class="nav-titulo">Relatórios</span>
                        <span class="nav-desc">Indicadores e históricos</span>
                    </span>
                    <svg class="ico nav-seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>
                </a>

            </nav>

            <div class="kpis">

                <div class="kpi">
                    <span class="kpi-icon" aria-hidden="true">
                        <svg class="ico" viewBox="0 0 24 24"><path d="m3 17 6-6 4 4 8-8"></path><path d="M15 7h6v6"></path></svg>
                    </span>
                    <div class="kpi-dados">
                        <div class="label">Vendas (mês)</div>
                        <div class="value"><?php echo (int)$resumo['vendas_mes']; ?></div>
                    </div>
                </div>

                <div class="kpi">
                    <span class="kpi-icon" aria-hidden="true">
                        <svg class="ico" viewBox="0 0 24 24"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M4 20h16"></path></svg>
                    </span>
                    <div class="kpi-dados">
                        <div class="label">Entradas (mês)</div>
                        <div class="value">0</div>
                    </div>
                </div>

                <div class="kpi">
                    <span class="kpi-icon" aria-hidden="true">
                        <svg class="ico" viewBox="0 0 24 24"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>
                    </span>
                    <div class="kpi-dados">
                        <div class="label">Estoque baixo</div>
                        <div class="value">0</div>
                    </div>
                </div>

                <div class="kpi">
                    <span class="kpi-icon" aria-hidden="true">
                        <svg class="ico" viewBox="0 0 24 24"><path d="M21 8 12 3 3 8v8l9 5 9-5V8Z"></path><path d="m3 8 9 5 9-5"></path><path d="M12 13v8"></path></svg>
                    </span>
                    <div class="kpi-dados">
                        <div class="label">Produtos</div>
                        <div class="value"><?php echo (int)$resumo['produtos']; ?></div>
                    </div>
                </div>

            </div>

            <p class="observacao">
                <svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                <span>
                    Vendas (mês) e Produtos são atualizados automaticamente a cada venda registrada.
                    Entradas e Estoque baixo serão alimentados quando implementarmos esses módulos.
                </span>
            </p>

        </main>

        <footer class="rodape">&copy; 2026 Prime Motors. Todos os direitos reservados.</footer>

    </div>
</body>

</html>
