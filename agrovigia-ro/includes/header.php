<?php require_once __DIR__ . '/../config/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroVigia RO</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<header class="topbar">
    <a class="logo" href="<?= BASE_URL ?>/index.php">🌱 AgroVigia RO</a>
    <?php if (isset($_SESSION['produtor_id'])): ?>
        <nav>
            <a href="<?= BASE_URL ?>/propriedades/listar.php">Propriedades</a>
            <a href="<?= BASE_URL ?>/ocorrencias/listar.php">Ocorrências</a>
            <a href="<?= BASE_URL ?>/dashboard/index.php">Painel</a>
            <?php if (ehAdmin()): ?>
                <a href="<?= BASE_URL ?>/admin/index.php" class="link-admin">Administração</a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/perfil/editar.php" class="usuario">Meu perfil (<?= htmlspecialchars($_SESSION['produtor_nome']) ?>)</a>
            <a href="<?= BASE_URL ?>/logout.php">Sair</a>
        </nav>
    <?php endif; ?>
</header>
<main class="container">
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alerta alerta-<?= htmlspecialchars($_SESSION['flash']['tipo']) ?>">
            <?= htmlspecialchars($_SESSION['flash']['texto']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
