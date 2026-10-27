<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$id = (int)($_GET['id'] ?? 0);
$pdo = conectarBanco();

$stmt = $pdo->prepare('DELETE FROM propriedades WHERE id = :id AND produtor_id = :produtor_id');
$stmt->execute(['id' => $id, 'produtor_id' => $_SESSION['produtor_id']]);

mensagemFlash('Propriedade excluída.');
redirecionar('propriedades/listar.php');
