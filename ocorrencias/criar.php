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
    csrfValidar();
    $descricao = trim((string)($_POST['sintoma_descricao'] ?? ''));
    $latTexto = trim((string)($_POST['latitude'] ?? ''));
    $lonTexto = trim((string)($_POST['longitude'] ?? ''));
    $latitude = $latTexto === '' ? null : $latTexto;
    $longitude = $lonTexto === '' ? null : $lonTexto;
    $coordenadasOk = ($latitude === null || (is_numeric($latitude) && is_finite((float)$latitude) && (float)$latitude >= -90 && (float)$latitude <= 90))
        && ($longitude === null || (is_numeric($longitude) && is_finite((float)$longitude) && (float)$longitude >= -180 && (float)$longitude <= 180));
    $nomeArquivo = null;

    if ($descricao === '' || mb_strlen($descricao) > 5000) {
        $erro = 'Descreva o sintoma observado (até 5.000 caracteres).';
    } elseif (!$coordenadasOk) {
        $erro = 'Informe latitude entre -90 e 90 e longitude entre -180 e 180.';
    }

    if ($erro === '' && isset($_FILES['foto']) && ($_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $arquivo = $_FILES['foto'];
        $maximo = 2 * 1024 * 1024;  // limites conservadores para hospedagens gratuitas
        if (($arquivo['error'] ?? -1) !== UPLOAD_ERR_OK || !isset($arquivo['tmp_name'])
            || !is_uploaded_file($arquivo['tmp_name']) || ($arquivo['size'] ?? 0) > $maximo) {
            $erro = 'Não foi possível enviar a foto. Use uma imagem de até 2 MB.';
        } else {
            $info = @getimagesize($arquivo['tmp_name']);
            $tipos = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
            $tipo = $info[2] ?? 0;
            if (!isset($tipos[$tipo]) || $info[0] > 7000 || $info[1] > 7000) {
                $erro = 'Arquivo inválido. Envie uma imagem JPG, PNG ou WEBP.';
            } else {
                $nomeArquivo = 'ocorrencia_' . bin2hex(random_bytes(16)) . '.' . $tipos[$tipo];
                if (!is_dir(UPLOAD_DIR) && !@mkdir(UPLOAD_DIR, 0755, true) && !is_dir(UPLOAD_DIR)) {
                    $erro = 'A pasta de fotos não está disponível na hospedagem.';
                    $nomeArquivo = null;
                } elseif (!move_uploaded_file($arquivo['tmp_name'], UPLOAD_DIR . $nomeArquivo)) {
                    $erro = 'Não foi possível salvar a foto na hospedagem.';
                    $nomeArquivo = null;
                }
            }
        }
    }

    if ($erro === '') {
        try {
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
        } catch (PDOException $e) {
            if ($nomeArquivo && is_file(UPLOAD_DIR . $nomeArquivo)) @unlink(UPLOAD_DIR . $nomeArquivo);
            error_log('[AgroVigia] Falha ao cadastrar ocorrência: ' . $e->getMessage());
            $erro = 'Não foi possível registrar a ocorrência no banco.';
        }
        if ($erro === '') {
            try {
                // Alertas são simulados no próprio banco, sem enviar WhatsApp real.
                verificarConcentracaoEDispararAlerta($pdo, $ocorrenciaId);
            } catch (Throwable $e) {
                error_log('[AgroVigia] Erro ao registrar alertas: ' . $e->getMessage());
            }
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
    <?= csrfCampo() ?>
    <input type="hidden" name="talhao_id" value="<?= $talhaoId ?>">
    <label>O que você observou?
        <textarea name="sintoma_descricao" rows="4" maxlength="5000" required></textarea>
    </label>
    <label>Foto (opcional)
        <input type="file" name="foto" accept="image/jpeg,image/png,image/webp">
        <small>Apenas JPG, PNG ou WEBP, até 2 MB.</small>
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
