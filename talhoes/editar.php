<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$pdo = conectarBanco();

// join garante que o talhão pertence a uma propriedade do produtor logado
$stmt = $pdo->prepare(
    'SELECT t.* FROM talhoes t
     JOIN propriedades p ON p.id = t.propriedade_id
     WHERE t.id = :id AND p.produtor_id = :produtor_id'
);
$stmt->execute(['id' => $id, 'produtor_id' => $_SESSION['produtor_id']]);
$talhao = $stmt->fetch();

if (!$talhao) {
    mensagemFlash('Talhão não encontrado.', 'erro');
    redirecionar('propriedades/listar.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();
    $nome = trim($_POST['nome'] ?? '');
    $cultura = trim($_POST['cultura'] ?? '');
    $areaTexto = trim((string)($_POST['area_hectares'] ?? ''));
    $area = $areaTexto !== '' ? $areaTexto : null;
    $areaOk = $area === null || (is_numeric($area) && is_finite((float)$area) && (float)$area > 0 && (float)$area <= 999999);

    if ($nome === '' || $cultura === '' || mb_strlen($nome) > 80 || mb_strlen($cultura) > 80 || !$areaOk) {
        mensagemFlash('Verifique nome, cultura e área em hectares.', 'erro');
        redirecionar('talhoes/editar.php?id=' . $id);
    }
    $stmtUp = $pdo->prepare(
        'UPDATE talhoes SET nome = :nome, cultura = :cultura, area_hectares = :area WHERE id = :id'
    );
    $stmtUp->execute(['nome' => $nome, 'cultura' => $cultura, 'area' => $area, 'id' => $id]);
    mensagemFlash('Talhão atualizado.');
    redirecionar('talhoes/listar.php?propriedade_id=' . $talhao['propriedade_id']);
}

require __DIR__ . '/../includes/header.php';
?>
<h1>Editar talhão</h1>
<form method="post" class="formulario">
    <?= csrfCampo() ?>
    <input type="hidden" name="id" value="<?= $talhao['id'] ?>">
    <label>Nome do talhão
        <input type="text" name="nome" value="<?= htmlspecialchars($talhao['nome']) ?>" required>
    </label>
    <label>Cultura
        <input type="text" name="cultura" value="<?= htmlspecialchars($talhao['cultura']) ?>" required>
    </label>
    <label>Área (hectares)
        <input type="text" name="area_hectares" value="<?= htmlspecialchars($talhao['area_hectares'] ?? '') ?>">
    </label>
    <button type="submit">Salvar alterações</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
