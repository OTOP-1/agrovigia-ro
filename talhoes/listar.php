<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$propriedadeId = (int)($_GET['propriedade_id'] ?? 0);
$pdo = conectarBanco();

// garante que a propriedade pertence ao produtor logado
$stmtProp = $pdo->prepare('SELECT * FROM propriedades WHERE id = :id AND produtor_id = :produtor_id');
$stmtProp->execute(['id' => $propriedadeId, 'produtor_id' => $_SESSION['produtor_id']]);
$propriedade = $stmtProp->fetch();

if (!$propriedade) {
    mensagemFlash('Propriedade não encontrada.', 'erro');
    redirecionar('propriedades/listar.php');
}

$stmt = $pdo->prepare('SELECT * FROM talhoes WHERE propriedade_id = :propriedade_id ORDER BY nome');
$stmt->execute(['propriedade_id' => $propriedadeId]);
$talhoes = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<h1>Talhões de <?= htmlspecialchars($propriedade['nome']) ?></h1>
<a class="botao" href="<?= BASE_URL ?>/talhoes/criar.php?propriedade_id=<?= $propriedadeId ?>">+ Novo talhão</a>

<table class="tabela">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Cultura</th>
            <th>Área (ha)</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($talhoes as $t): ?>
        <tr>
            <td><?= htmlspecialchars($t['nome']) ?></td>
            <td><?= htmlspecialchars($t['cultura']) ?></td>
            <td><?= htmlspecialchars($t['area_hectares'] ?? '-') ?></td>
            <td>
                <a href="<?= BASE_URL ?>/ocorrencias/listar.php?talhao_id=<?= $t['id'] ?>">ocorrências</a>
                |
                <a href="<?= BASE_URL ?>/talhoes/editar.php?id=<?= $t['id'] ?>">editar</a>
                |
                <form method="post" action="<?= BASE_URL ?>/talhoes/excluir.php" class="inline-form" onsubmit="return confirm('Excluir este talhão e suas ocorrências?')">
                    <?= csrfCampo() ?><input type="hidden" name="id" value="<?= (int)$t['id'] ?>"><input type="hidden" name="propriedade_id" value="<?= (int)$propriedadeId ?>"><button type="submit" class="link-botao link-perigo">excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($talhoes)): ?>
        <tr><td colspan="4">Nenhum talhão cadastrado ainda.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<p><a href="<?= BASE_URL ?>/propriedades/listar.php">&larr; Voltar para propriedades</a></p>
<?php require __DIR__ . '/../includes/footer.php'; ?>
