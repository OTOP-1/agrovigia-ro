<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$pdo = conectarBanco();

$stmt = $pdo->prepare(
    'SELECT o.* FROM ocorrencias o
     JOIN talhoes t ON t.id = o.talhao_id
     JOIN propriedades p ON p.id = t.propriedade_id
     WHERE o.id = :id AND p.produtor_id = :produtor_id'
);
$stmt->execute(['id' => $id, 'produtor_id' => $_SESSION['produtor_id']]);
$ocorrencia = $stmt->fetch();

if (!$ocorrencia) {
    mensagemFlash('Ocorrência não encontrada.', 'erro');
    redirecionar('ocorrencias/listar.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();
    $descricao = trim($_POST['sintoma_descricao'] ?? '');
    $status = (string)($_POST['status'] ?? 'nova');
    if ($descricao === '' || mb_strlen($descricao) > 5000 || !in_array($status, ['nova','em_analise','confirmada','resolvida'], true)) {
        mensagemFlash('Descrição ou status inválido.', 'erro');
        redirecionar('ocorrencias/editar.php?id=' . $id);
    }

    $stmtUp = $pdo->prepare(
        'UPDATE ocorrencias SET sintoma_descricao = :descricao, status = :status WHERE id = :id'
    );
    $stmtUp->execute(['descricao' => $descricao, 'status' => $status, 'id' => $id]);
    mensagemFlash('Ocorrência atualizada.');
    redirecionar('ocorrencias/listar.php?talhao_id=' . $ocorrencia['talhao_id']);
}

require __DIR__ . '/../includes/header.php';
?>
<h1>Editar ocorrência</h1>
<form method="post" class="formulario">
    <?= csrfCampo() ?>
    <input type="hidden" name="id" value="<?= $ocorrencia['id'] ?>">
    <label>Descrição do sintoma
        <textarea name="sintoma_descricao" rows="4" maxlength="5000" required><?= htmlspecialchars($ocorrencia['sintoma_descricao']) ?></textarea>
    </label>
    <label>Status
        <select name="status">
            <?php foreach (['nova', 'em_analise', 'confirmada', 'resolvida'] as $s): ?>
                <option value="<?= $s ?>" <?= $ocorrencia['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <button type="submit">Salvar alterações</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
