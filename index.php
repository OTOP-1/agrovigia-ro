<?php
require_once __DIR__ . '/config/config.php';
exigirLogin();
require __DIR__ . '/includes/header.php';
?>
<h1>Bem-vindo(a), <?= htmlspecialchars($_SESSION['produtor_nome']) ?></h1>
<p>O AgroVigia RO ajuda você a monitorar suas lavouras e receber alertas
quando pragas ou doenças começam a se espalhar na sua região.</p>

<div class="cards">
    <a class="card" href="<?= BASE_URL ?>/propriedades/listar.php">
        <h2>🏡 Propriedades</h2>
        <p>Cadastre suas propriedades e talhões.</p>
    </a>
    <a class="card" href="<?= BASE_URL ?>/ocorrencias/listar.php">
        <h2>📷 Ocorrências</h2>
        <p>Registre sintomas encontrados na lavoura, com foto.</p>
    </a>
    <a class="card" href="<?= BASE_URL ?>/dashboard/index.php">
        <h2>📊 Painel</h2>
        <p>Veja o histórico e o mapa de ocorrências da sua região.</p>
    </a>
    <a class="card" href="<?= BASE_URL ?>/perfil/editar.php">
        <h2>👤 Meu perfil</h2>
        <p>Atualize seus dados de contato e sua senha.</p>
    </a>
    <?php if (ehAdmin()): ?>
        <a class="card card-admin" href="<?= BASE_URL ?>/admin/index.php">
            <h2>🛡️ Administração</h2>
            <p>Gerencie todos os perfis cadastrados no sistema.</p>
        </a>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
