<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$pdo = conectarBanco();

$stmt = $pdo->prepare(
    'SELECT * FROM propriedades WHERE id = :id AND produtor_id = :produtor_id'
);
$stmt->execute(['id' => $id, 'produtor_id' => $_SESSION['produtor_id']]);
$propriedade = $stmt->fetch();

if (!$propriedade) {
    mensagemFlash('Propriedade não encontrada.', 'erro');
    redirecionar('propriedades/listar.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();
    $nome = trim($_POST['nome'] ?? '');
    $municipio = trim($_POST['municipio'] ?? '');
    $latTexto = trim((string)($_POST['latitude'] ?? ''));
    $lonTexto = trim((string)($_POST['longitude'] ?? ''));
    $latitude = $latTexto !== '' ? $latTexto : null;
    $longitude = $lonTexto !== '' ? $lonTexto : null;
    $coordenadasOk = ($latitude === null || (is_numeric($latitude) && is_finite((float)$latitude) && (float)$latitude >= -90 && (float)$latitude <= 90))
        && ($longitude === null || (is_numeric($longitude) && is_finite((float)$longitude) && (float)$longitude >= -180 && (float)$longitude <= 180));

    if ($nome === '' || $municipio === '' || mb_strlen($nome) > 120 || mb_strlen($municipio) > 100 || !$coordenadasOk) {
        mensagemFlash('Verifique nome, município e coordenadas.', 'erro');
        redirecionar('propriedades/editar.php?id=' . $id);
    }

    $stmtUp = $pdo->prepare(
        'UPDATE propriedades
         SET nome = :nome, municipio = :municipio, latitude = :latitude, longitude = :longitude
         WHERE id = :id AND produtor_id = :produtor_id'
    );
    $stmtUp->execute([
        'nome' => $nome,
        'municipio' => $municipio,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'id' => $id,
        'produtor_id' => $_SESSION['produtor_id'],
    ]);
    mensagemFlash('Propriedade atualizada.');
    redirecionar('propriedades/listar.php');
}

require __DIR__ . '/../includes/header.php';
?>
<h1>Editar propriedade</h1>
<form method="post" class="formulario">
    <?= csrfCampo() ?>
    <input type="hidden" name="id" value="<?= $propriedade['id'] ?>">
    <label>Nome da propriedade
        <input type="text" name="nome" value="<?= htmlspecialchars($propriedade['nome']) ?>" required>
    </label>
    <label>Município
        <input type="text" name="municipio" value="<?= htmlspecialchars($propriedade['municipio']) ?>" required>
    </label>
    <label>Latitude
        <input type="text" name="latitude" value="<?= htmlspecialchars($propriedade['latitude'] ?? '') ?>">
    </label>
    <label>Longitude
        <input type="text" name="longitude" value="<?= htmlspecialchars($propriedade['longitude'] ?? '') ?>">
    </label>
    <button type="submit">Salvar alterações</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
