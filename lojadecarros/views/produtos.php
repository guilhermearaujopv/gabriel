<?php
$editar = $editar ?? null;
$produtos = $produtos ?? [];
?>

<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Carros</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/png" sizes="512x512" href="public/assets/css/favicon.png">

<link rel="stylesheet" href="public/assets/css/produtos.css">
</head>

<body>

<!-- Fundo com imagem de carro (endereço definido no CSS) -->
<div class="fundo" aria-hidden="true"></div>

<div class="header">
<div class="container header-inner">
<div class="brand">
<img src="public/assets/css/logoprimemotors-removebg-preview.png"
     alt="Prime Motors"
     class="logo-header">
<span class="badge">Carros</span>
</div>

<div class="user">
Olá, <strong><?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário') ?></strong>
<a class="btn btn-ghost" href="index.php?controller=auth&action=logout">Sair</a>
</div>
</div>
</div>

<!-- ATALHOS PARA OUTRAS PÁGINAS -->
<div class="container nav-wrap">
<nav class="card nav-card" aria-label="Ir para outras páginas">
<h2>Ir para</h2>

<div class="nav-links">

<a href="index.php?controller=auth&action=dashboard">
<span class="nav-icon" aria-hidden="true">
<svg class="ico" viewBox="0 0 24 24"><path d="M3 11 12 3l9 8"></path><path d="M5 10v10h14V10"></path></svg>
</span>
<span class="nav-titulo">Dashboard</span>
<svg class="ico nav-seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>
</a>

<a href="index.php?controller=entrada&action=index">
<span class="nav-icon" aria-hidden="true">
<svg class="ico" viewBox="0 0 24 24"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M4 20h16"></path></svg>
</span>
<span class="nav-titulo">Entradas</span>
<svg class="ico nav-seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>
</a>

<a href="index.php?controller=venda&action=index">
<span class="nav-icon" aria-hidden="true">
<svg class="ico" viewBox="0 0 24 24"><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle><path d="M3 3h2l2.6 12.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.5L21 8H6"></path></svg>
</span>
<span class="nav-titulo">Vendas</span>
<svg class="ico nav-seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>
</a>

<a href="index.php?controller=relatorio&action=index">
<span class="nav-icon" aria-hidden="true">
<svg class="ico" viewBox="0 0 24 24"><path d="M5 20v-9"></path><path d="M12 20V4"></path><path d="M19 20v-6"></path><path d="M3 20h18"></path></svg>
</span>
<span class="nav-titulo">Relatórios</span>
<svg class="ico nav-seta" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>
</a>

</div>
</nav>
</div>

<div class="container grid">

<!-- FORM -->
<div class="card">
<h2><?= $editar ? "Editar Carro #".$editar['id_carro'] : "Cadastrar Carro" ?></h2>

<form method="post" action="index.php?controller=produto&action=salvar" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $editar['id_carro'] ?? 0 ?>">

<div class="form-row">
<div class="form-group">
<label>Marca</label>
<input class="input" type="text" name="marca" required
value="<?= $editar['marca'] ?? '' ?>">
</div>

<div class="form-group">
<label>Modelo</label>
<input class="input" type="text" name="modelo" required
value="<?= $editar['modelo'] ?? '' ?>">
</div>
</div>

<div class="form-row">
<div class="form-group">
<label>Ano</label>
<input class="input" type="number" name="ano" required
value="<?= $editar['ano'] ?? '' ?>">
</div>

<div class="form-group">
<label>Cor</label>
<input class="input" type="text" name="cor"
value="<?= $editar['cor'] ?? '' ?>">
</div>
</div>

<div class="form-row">
<div class="form-group">
<label>Combustível</label>
<input class="input" type="text" name="combustivel"
value="<?= $editar['combustivel'] ?? '' ?>">
</div>

<div class="form-group">
<label>KM</label>
<input class="input" type="number" name="km"
value="<?= $editar['km'] ?? '' ?>">
</div>
</div>

<div class="form-group">
<label>Nº Chassi</label>
<input class="input" type="text" name="n_chassi"
value="<?= $editar['n_chassi'] ?? '' ?>">
</div>

<div class="form-group">
  <label>Imagem do Carro</label>
  <input class="input" type="file" name="imagem" id="imagem" accept="image/*">
</div>

<img id="previewImagem" class="preview" alt="Prévia da imagem">

<!-- NOVO CAMPO -->
<div class="form-group">
<label>Preço (R$)</label>
<input
    class="input"
    type="number"
    name="preco"
    step="0.01"
    min="0"
    required
    value="<?= $editar['preco'] ?? '' ?>">
</div>

<div class="actions">
<button class="btn btn-primary" type="submit">Salvar</button>
<a class="btn" href="index.php?controller=produto&action=index">Limpar</a>
</div>

</form>
</div>

<!-- LISTA -->
<div class="card">
<h2>Lista de Carros</h2>

<div class="table-wrap">
<table class="table">
<thead>
<tr>
<th>ID</th>
<th>Marca</th>
<th>Modelo</th>
<th>Ano</th>
<th>Cor</th>
<th class="num">KM</th>
<th class="num">Preço</th>
<th>Ações</th>
<th>Imagem</th>
</tr>
</thead>

<tbody>
<?php foreach ($produtos as $p): ?>
<tr>
<td class="id"><?= $p['id_carro'] ?></td>
<td><?= htmlspecialchars($p['marca']) ?></td>
<td><?= htmlspecialchars($p['modelo']) ?></td>
<td><?= $p['ano'] ?></td>
<td><?= htmlspecialchars($p['cor']) ?></td>
<td class="num"><?= number_format($p['km'], 0, ',', '.') ?></td>
<td class="num preco">R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
<td>
<div class="acoes">
  <a class="btn btn-vender"
  href="index.php?controller=venda&action=adicionar&id=<?= $p['id_carro'] ?>">
  Vender
  </a>

  <a class="btn"
  href="index.php?controller=produto&action=index&id=<?= $p['id_carro'] ?>">
  Editar
  </a>

  <a class="btn btn-danger"
  href="index.php?controller=produto&action=deletar&id=<?= $p['id_carro'] ?>"
  onclick="return confirm('Deletar este carro?')">
  Excluir
  </a>
</div>
</td>

<td>
  <?php if (!empty($p['imagem'])): ?>
    <img class="thumb" src="<?= $p['imagem'] ?>" width="80" alt="Foto do carro">
  <?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>

</table>
</div>
</div>

</div>

<script>
document.getElementById("imagem").addEventListener("change", function(e) {
  const file = e.target.files[0];

  if (file) {
    const reader = new FileReader();

    reader.onload = function(ev) {
      const img = document.getElementById("previewImagem");
      img.src = ev.target.result;
      img.style.display = "block";
    };

    reader.readAsDataURL(file);
  }
});
</script>

</body>
</html>