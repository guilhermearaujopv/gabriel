<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
      <link rel="stylesheet" href="public/assets/css/cadastro.css">
    <title>Cadastrar Vendedor</title>

<link rel="icon" type="image/png" sizes="512x512" href="public/assets/css/favicon.png">

</head>

    <body>

    <div class="container">

        <img src="public/assets/css/logoprimemotors-removebg-preview.png"
             alt="Prime Motors"
             class="logo">

        <h2>Cadastro de Vendedor</h2>

        <form action="index.php?controller=usuario&action=store" method="POST">

            <div>
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div>
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>

            <button type="submit">Cadastrar</button>

        </form>

    </div>

</body>

</html>
