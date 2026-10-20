<?php
require_once __DIR__ . '/config/config.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $municipio = trim($_POST['municipio'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || $email === '' || $telefone === '' || $municipio === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
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
                'telefone' => $telefone,
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
        <input type="password" name="senha" required>
    </label>
    <button type="submit">Cadastrar</button>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
