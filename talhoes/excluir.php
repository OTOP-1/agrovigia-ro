<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405); exit('Use o formulário de exclusão.');
}
csrfValidar();

$id = (int)($_POST['id'] ?? 0);
$propriedadeId = (int)($_POST['propriedade_id'] ?? 0);
$pdo = conectarBanco();

// garante posse via join antes de excluir
$fotoStmt = $pdo->prepare('SELECT o.foto FROM ocorrencias o JOIN talhoes t ON t.id=o.talhao_id JOIN propriedades p ON p.id=t.propriedade_id WHERE t.id=:id AND p.produtor_id=:produtor_id AND o.foto IS NOT NULL');
$fotoStmt->execute(['id'=>$id, 'produtor_id'=>$_SESSION['produtor_id']]);
$fotos = $fotoStmt->fetchAll(PDO::FETCH_COLUMN);
$stmt = $pdo->prepare(
    'DELETE t FROM talhoes t
     JOIN propriedades p ON p.id = t.propriedade_id
     WHERE t.id = :id AND p.produtor_id = :produtor_id'
);
$stmt->execute(['id' => $id, 'produtor_id' => $_SESSION['produtor_id']]);
if ($stmt->rowCount()) {
    foreach ($fotos as $foto) {
        $arquivo = UPLOAD_DIR . basename((string)$foto);
        if (is_file($arquivo)) @unlink($arquivo);
    }
}

mensagemFlash('Talhão excluído.');
redirecionar('talhoes/listar.php?propriedade_id=' . $propriedadeId);
