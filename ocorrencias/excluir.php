<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405); exit('Use o formulário de exclusão.');
}
csrfValidar();

$id = (int)($_POST['id'] ?? 0);
$pdo = conectarBanco();

$stmt = $pdo->prepare(
    'SELECT o.talhao_id, o.foto FROM ocorrencias o
     JOIN talhoes t ON t.id = o.talhao_id
     JOIN propriedades p ON p.id = t.propriedade_id
     WHERE o.id = :id AND p.produtor_id = :produtor_id'
);
$stmt->execute(['id' => $id, 'produtor_id' => $_SESSION['produtor_id']]);
$ocorrencia = $stmt->fetch();

if ($ocorrencia) {
    $stmtDel = $pdo->prepare('DELETE FROM ocorrencias WHERE id = :id');
    $stmtDel->execute(['id' => $id]);
    $foto = UPLOAD_DIR . basename((string)($ocorrencia['foto'] ?? ''));
    if ($stmtDel->rowCount() && !empty($ocorrencia['foto']) && is_file($foto)) {
        @unlink($foto);
    }
    mensagemFlash('Ocorrência excluída.');
    redirecionar('ocorrencias/listar.php?talhao_id=' . $ocorrencia['talhao_id']);
}

redirecionar('ocorrencias/listar.php');
