<?php
require_once __DIR__ . '/../config/config.php';
exigirAdmin();

// Só aceita POST + token CSRF (ação que altera dados nunca por link GET)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('admin/usuarios.php');
}
csrfValidar();

$id = (int)($_POST['id'] ?? 0);

if ($id === (int)$_SESSION['produtor_id']) {
    mensagemFlash('Você não pode desativar a sua própria conta.', 'erro');
    redirecionar('admin/usuarios.php');
}

$pdo = conectarBanco();
$stmt = $pdo->prepare('UPDATE produtores SET ativo = 1 - ativo WHERE id = :id');
$stmt->execute(['id' => $id]);

mensagemFlash($stmt->rowCount() ? 'Status da conta alterado.' : 'Perfil não encontrado.', $stmt->rowCount() ? 'sucesso' : 'erro');
redirecionar('admin/usuarios.php');
