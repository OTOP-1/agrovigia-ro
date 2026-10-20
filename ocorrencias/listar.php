<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$pdo = conectarBanco();
$talhaoId = isset($_GET['talhao_id']) ? (int)$_GET['talhao_id'] : null;

if ($talhaoId) {
    // ocorrências de um talhão específico (confirma posse via join)
    $stmt = $pdo->prepare(
        'SELECT o.*, t.nome AS talhao_nome
         FROM ocorrencias o
         JOIN talhoes t ON t.id = o.talhao_id
         JOIN propriedades p ON p.id = t.propriedade_id
         WHERE o.talhao_id = :talhao_id AND p.produtor_id = :produtor_id
         ORDER BY o.criado_em DESC'
    );
    $stmt->execute(['talhao_id' => $talhaoId, 'produtor_id' => $_SESSION['produtor_id']]);
} else {
    // todas as ocorrências de todos os talhões do produtor
    $stmt = $pdo->prepare(
        'SELECT o.*, t.nome AS talhao_nome
         FROM ocorrencias o
         JOIN talhoes t ON t.id = o.talhao_id
         JOIN propriedades p ON p.id = t.propriedade_id
         WHERE p.produtor_id = :produtor_id
         ORDER BY o.criado_em DESC'
    );
    $stmt->execute(['produtor_id' => $_SESSION['produtor_id']]);
}

$ocorrencias = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<h1>Ocorrências</h1>
<?php if ($talhaoId): ?>
    <a class="botao" href="<?= BASE_URL ?>/ocorrencias/criar.php?talhao_id=<?= $talhaoId ?>">+ Nova ocorrência</a>
<?php endif; ?>

<div class="lista-ocorrencias">
    <?php foreach ($ocorrencias as $o): ?>
        <div class="ocorrencia-card">
            <?php if ($o['foto']): ?>
                <img src="<?= UPLOAD_URL . htmlspecialchars($o['foto']) ?>" alt="Foto da ocorrência">
            <?php endif; ?>
            <div class="ocorrencia-info">
                <strong><?= htmlspecialchars($o['talhao_nome']) ?></strong>
                <span class="status status-<?= htmlspecialchars($o['status']) ?>"><?= htmlspecialchars($o['status']) ?></span>
                <p><?= nl2br(htmlspecialchars($o['sintoma_descricao'])) ?></p>
                <small><?= htmlspecialchars($o['criado_em']) ?></small>
                <div class="acoes">
                    <a href="<?= BASE_URL ?>/ocorrencias/editar.php?id=<?= $o['id'] ?>">editar</a>
                    |
                    <a href="<?= BASE_URL ?>/ocorrencias/excluir.php?id=<?= $o['id'] ?>"
                       onclick="return confirm('Excluir esta ocorrência?')">excluir</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (empty($ocorrencias)): ?>
        <p>Nenhuma ocorrência registrada ainda.</p>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
