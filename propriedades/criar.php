<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $municipio = trim($_POST['municipio'] ?? '');
    $latitude = $_POST['latitude'] !== '' ? $_POST['latitude'] : null;
    $longitude = $_POST['longitude'] !== '' ? $_POST['longitude'] : null;

    if ($nome === '' || $municipio === '') {
        mensagemFlash('Preencha nome e município da propriedade.', 'erro');
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
