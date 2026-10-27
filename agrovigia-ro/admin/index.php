<?php
require_once __DIR__ . '/../config/config.php';
exigirAdmin();

$pdo = conectarBanco();
$totais = $pdo->query(
    "SELECT
        (SELECT COUNT(*) FROM produtores)                       AS usuarios,
        (SELECT COUNT(*) FROM produtores WHERE perfil = 'admin') AS admins,
        (SELECT COUNT(*) FROM produtores WHERE ativo = 0)        AS inativos,
        (SELECT COUNT(*) FROM propriedades)                      AS propriedades,
        (SELECT COUNT(*) FROM talhoes)                           AS talhoes,
        (SELECT COUNT(*) FROM ocorrencias)                       AS ocorrencias,
        (SELECT COUNT(*) FROM alertas)                           AS alertas"
)->fetch();

$recentes = $pdo->query(
    'SELECT id, nome, email, municipio, perfil, ativo, criado_em
     FROM produtores ORDER BY criado_em DESC, id DESC LIMIT 5'
)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<h1>🛡️ Administração</h1>
<p class="subtitulo">Visão geral do sistema e gerenciamento de todos os perfis cadastrados.</p>

<div class="stats">
    <div class="stat"><strong><?= (int)$totais['usuarios'] ?></strong><span>perfis cadastrados</span></div>
    <div class="stat"><strong><?= (int)$totais['admins'] ?></strong><span>administradores</span></div>
    <div class="stat"><strong><?= (int)$totais['inativos'] ?></strong><span>contas desativadas</span></div>
    <div class="stat"><strong><?= (int)$totais['propriedades'] ?></strong><span>propriedades</span></div>
    <div class="stat"><strong><?= (int)$totais['talhoes'] ?></strong><span>talhões</span></div>
    <div class="stat"><strong><?= (int)$totais['ocorrencias'] ?></strong><span>ocorrências</span></div>
    <div class="stat"><strong><?= (int)$totais['alertas'] ?></strong><span>alertas disparados</span></div>
</div>

<p>
    <a class="botao" href="<?= BASE_URL ?>/admin/usuarios.php">Gerenciar perfis</a>
    <a class="botao botao-secundario" href="<?= BASE_URL ?>/admin/usuario_criar.php">+ Novo perfil</a>
</p>

<h2>Últimos cadastros</h2>
<table class="tabela">
    <thead>
        <tr><th>Nome</th><th>E-mail</th><th>Município</th><th>Tipo</th><th>Status</th><th>Cadastro</th></tr>
    </thead>
    <tbody>
    <?php foreach ($recentes as $u): ?>
        <tr>
            <td><a href="<?= BASE_URL ?>/admin/usuario_editar.php?id=<?= (int)$u['id'] ?>"><?= htmlspecialchars($u['nome']) ?></a></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['municipio']) ?></td>
            <td><span class="badge badge-<?= htmlspecialchars($u['perfil']) ?>"><?= $u['perfil'] === 'admin' ? 'Admin' : 'Produtor' ?></span></td>
            <td><span class="badge <?= $u['ativo'] ? 'badge-ativo' : 'badge-inativo' ?>"><?= $u['ativo'] ? 'Ativo' : 'Desativado' ?></span></td>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($u['criado_em']))) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>
