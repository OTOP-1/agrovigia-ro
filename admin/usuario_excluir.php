<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/usuarios.php';
exigirAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('admin/usuarios.php');
}
csrfValidar();

$id = (int)($_POST['id'] ?? 0);

if ($id === (int)$_SESSION['produtor_id']) {
    mensagemFlash('Você não pode excluir a sua própria conta.', 'erro');
    redirecionar('admin/usuarios.php');
}

$pdo = conectarBanco();
$stmt = $pdo->prepare('SELECT nome FROM produtores WHERE id = :id');
$stmt->execute(['id' => $id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    mensagemFlash('Perfil não encontrado.', 'erro');
    redirecionar('admin/usuarios.php');
}

excluirUsuarioComFotos($pdo, $id);

mensagemFlash('Perfil de ' . $usuario['nome'] . ' excluído, junto com seus dados.');
redirecionar('admin/usuarios.php');
