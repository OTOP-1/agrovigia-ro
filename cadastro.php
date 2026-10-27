<?php
require_once __DIR__ . '/config/config.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $municipio = trim($_POST['municipio'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || $email === '' || $telefone === '' || $municipio === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (mb_strlen($nome) < 3 || mb_strlen($nome) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email)>150 || mb_strlen($municipio)>100) {
        $erro = 'Confira o nome, município e endereço de e-mail.';
    } elseif (strlen(preg_replace('/\D+/', '', $telefone)) < 10 || strlen(preg_replace('/\D+/', '', $telefone)) > 13) {
        $erro = 'Informe telefone com DDD (10 a 13 dígitos).';
    } elseif (strlen($senha) < 10 || strlen($senha) > 72) {
        $erro = 'Use uma senha de 10 a 72 caracteres.';
    } else {
        $pdo = conectarBanco();
        $stmt = $pdo->prepare(
            'INSERT INTO produtores (nome, email, senha_hash, telefone, municipio)
             VALUES (:nome, :email, :senha_hash, :telefone, :municipio)'
        );
        try {
            $stmt->execute([
                'nome' => $nome,
                'email' => $email,
                'senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
                'telefone' => preg_replace('/\D+/', '', $telefone),
                'municipio' => $municipio,
            ]);
            mensagemFlash('Cadastro realizado! Faça login para continuar.');
            redirecionar('login.php');
        } catch (PDOException $e) {
            $erro = 'Não foi possível cadastrar (e-mail já usado?).';
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<h1>Criar conta</h1>
<?php if ($erro): ?>
    <div class="alerta alerta-erro"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>
<form method="post" class="formulario">
    <?= csrfCampo() ?>
    <label>Nome completo
        <input type="text" name="nome" required>
    </label>
    <label>E-mail
        <input type="email" name="email" required>
    </label>
    <label>Telefone (WhatsApp)
        <input type="text" name="telefone" placeholder="69999990000" required>
    </label>
    <label>Município
        <input type="text" name="municipio" required>
    </label>
    <label>Senha
        <input type="password" name="senha" minlength="10" maxlength="72" autocomplete="new-password" required>
    </label>
    <button type="submit">Cadastrar</button>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
