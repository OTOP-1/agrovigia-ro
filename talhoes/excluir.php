<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$id = (int)($_GET['id'] ?? 0);
$propriedadeId = (int)($_GET['propriedade_id'] ?? 0);
$pdo = conectarBanco();

// garante posse via join antes de excluir
$stmt = $pdo->prepare(
    'DELETE t FROM talhoes t
     JOIN propriedades p ON p.id = t.propriedade_id
     WHERE t.id = :id AND p.produtor_id = :produtor_id'
);
$stmt->execute(['id' => $id, 'produtor_id' => $_SESSION['produtor_id']]);

mensagemFlash('Talhão excluído.');
redirecionar('talhoes/listar.php?propriedade_id=' . $propriedadeId);
