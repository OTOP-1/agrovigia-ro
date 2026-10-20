<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/alertas.php';
exigirLogin();

$talhaoId = (int)($_GET['talhao_id'] ?? $_POST['talhao_id'] ?? 0);
$pdo = conectarBanco();

// confirma que o talhão pertence a uma propriedade do produtor logado
$stmt = $pdo->prepare(
    'SELECT t.*, p.municipio, p.produtor_id FROM talhoes t
     JOIN propriedades p ON p.id = t.propriedade_id
     WHERE t.id = :id AND p.produtor_id = :produtor_id'
);
$stmt->execute(['id' => $talhaoId, 'produtor_id' => $_SESSION['produtor_id']]);
$talhao = $stmt->fetch();

if (!$talhao) {
    mensagemFlash('Talhão não encontrado.', 'erro');
    redirecionar('propriedades/listar.php');
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = trim($_POST['sintoma_descricao'] ?? '');
    $latitude = $_POST['latitude'] !== '' ? $_POST['latitude'] : null;
    $longitude = $_POST['longitude'] !== '' ? $_POST['longitude'] : null;
    $nomeArquivo = null;

    if ($descricao === '') {
        $erro = 'Descreva o sintoma observado.';
    } else {
        // upload de foto (opcional, mas recomendado no formulário original)
        if (!empty($_FILES['foto']['name'])) {
            $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
            $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

            if (!in_array($extensao, $permitidas, true)) {
                $erro = 'Formato de imagem não permitido (use JPG, PNG ou WEBP).';
            } elseif ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
                $erro = 'A foto deve ter no máximo 5MB.';
            } else {
                $nomeArquivo = uniqid('ocorrencia_', true) . '.' . $extensao;
                if (!is_dir(UPLOAD_DIR)) {
                    mkdir(UPLOAD_DIR, 0755, true);
                }
                move_uploaded_file($_FILES['foto']['tmp_name'], UPLOAD_DIR . $nomeArquivo);
            }
        }

        if ($erro === '') {
            $stmtIns = $pdo->prepare(
                'INSERT INTO ocorrencias (talhao_id, sintoma_descricao, foto, latitude, longitude)
                 VALUES (:talhao_id, :descricao, :foto, :latitude, :longitude)'
            );
            $stmtIns->execute([
                'talhao_id' => $talhaoId,
                'descricao' => $descricao,
                'foto' => $nomeArquivo,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

            $ocorrenciaId = (int)$pdo->lastInsertId();

            // ---- parte inovadora: verifica concentração e dispara alerta ----
            verificarConcentracaoEDispararAlerta($pdo, $ocorrenciaId);

            mensagemFlash('Ocorrência registrada com sucesso.');
            redirecionar('ocorrencias/listar.php?talhao_id=' . $talhaoId);
        }
    }
}

require __DIR__ . '/../includes/header.php';
?>
<h1>Nova ocorrência em <?= htmlspecialchars($talhao['nome']) ?></h1>
<?php if ($erro): ?>
    <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" class="formulario">
    <input type="hidden" name="talhao_id" value="<?= $talhaoId ?>">
    <label>O que você observou?
        <textarea name="sintoma_descricao" rows="4" required></textarea>
    </label>
    <label>Foto (opcional)
        <input type="file" name="foto" accept="image/*">
    </label>
    <label>Latitude (opcional)
        <input type="text" name="latitude">
    </label>
    <label>Longitude (opcional)
        <input type="text" name="longitude">
    </label>
    <button type="submit">Registrar ocorrência</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
