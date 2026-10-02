<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="public/assets/css/login2.css">

    <title>Prime Motors</title>

    <link
        rel="icon"
        type="image/png"
        sizes="512x512"
        href="public/assets/css/favicon.png"
    >
</head>

<body>

    <main class="login-page">

        <section class="container">

            <div class="brand">
                <img
                    src="public/assets/css/logoprimemotors-removebg-preview.png"
                    alt="Prime Motors"
                    class="logo"
                >

                <span class="slogan">
                    EXCELÊNCIA EM MOVIMENTO
                </span>
            </div>

            <form
                method="post"
                action="/lojadecarros/index.php?controller=auth&action=login"
                class="login-form"
            >

                <div class="input-box">

                    <label for="email">
                        E-mail
                    </label>

                    <div class="input-wrapper email-icon">

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Digite seu e-mail"
                            required
                        >

                    </div>

                </div>


                <div class="input-box">

                    <label for="senha">
                        Senha
                    </label>

                    <div class="input-wrapper password-icon">

                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            placeholder="Digite sua senha"
                            required
                        >

                    </div>

                </div>


                <button type="submit" class="btn-login">
                    <span class="login-symbol">↪</span>
                    Entrar
                </button>

            </form>


            <div class="divider">
                <span>ou</span>
            </div>


            <a
                href="index.php?controller=usuario&action=create"
                class="btn-register"
            >
                <span class="user-symbol">♙</span>
                Cadastrar
            </a>

        </section>


        <footer>
            © 2026 Prime Motors. Todos os direitos reservados.
        </footer>

    </main>

</body>

</html>