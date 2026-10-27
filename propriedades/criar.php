<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

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
        mensagemFlash('Verifique nome, município e coordenadas (latitude -90 a 90; longitude -180 a 180).', 'erro');
    } else {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare(
            'INSERT INTO propriedades (produtor_id, nome, municipio, latitude, longitude)
             VALUES (:produtor_id, :nome, :municipio, :latitude, :longitude)'
        );
        $stmt->execute([
            'produtor_id' => $_SESSION['produtor_id'],
            'nome' => $nome,
            'municipio' => $municipio,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
        mensagemFlash('Propriedade cadastrada com sucesso.');
    }
    redirecionar('propriedades/listar.php');
}

require __DIR__ . '/../includes/header.php';
?>
<h1>Nova propriedade</h1>
<form method="post" class="formulario">
    <?= csrfCampo() ?>
    <label>Nome da propriedade
        <input type="text" name="nome" required>
    </label>
    <label>Município
        <input type="text" name="municipio" required>
    </label>
    <label>Latitude (opcional)
        <input type="text" name="latitude" placeholder="-10.7500">
    </label>
    <label>Longitude (opcional)
        <input type="text" name="longitude" placeholder="-62.2167">
    </label>
    <button type="submit">Salvar</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
