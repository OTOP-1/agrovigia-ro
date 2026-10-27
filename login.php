<?php
require_once __DIR__ . '/config/config.php';

if (isset($_SESSION['produtor_id'])) {
    redirecionar('index.php');
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $pdo = conectarBanco();
    $stmt = $pdo->prepare(
        'SELECT id, nome, senha_hash, perfil, ativo FROM produtores WHERE email = :email'
    );
    $stmt->execute(['email' => $email]);
    $produtor = $stmt->fetch();

    if (!$produtor || !password_verify($senha, $produtor['senha_hash'])) {
        $erro = 'E-mail ou senha inválidos.';
    } elseif (!(int)$produtor['ativo']) {
        // só revela que a conta está desativada a quem acertou a senha
        $erro = 'Sua conta está desativada. Fale com o administrador do sistema.';
    } else {
        session_regenerate_id(true); // evita fixação de sessão
        $_SESSION['produtor_id'] = $produtor['id'];
        $_SESSION['produtor_nome'] = $produtor['nome'];
        $_SESSION['produtor_perfil'] = $produtor['perfil'];

        if ($produtor['perfil'] === 'admin') {
            redirecionar('admin/index.php');
        }
        redirecionar('index.php');
    }
}

require __DIR__ . '/includes/header.php';
?>
<h1>Entrar</h1>
<?php if ($erro): ?>
    <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>
<form method="post" class="formulario">
    <?= csrfCampo() ?>
    <label>E-mail
        <input type="email" name="email" required>
    </label>
    <label>Senha
        <input type="password" name="senha" required>
    </label>
    <button type="submit">Entrar</button>
</form>
<p>Ainda não tem conta? <a href="<?= BASE_URL ?>/cadastro.php">Cadastre-se</a></p>
<?php require __DIR__ . '/includes/footer.php'; ?>
