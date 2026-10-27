<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/usuarios.php';
exigirAdmin();

$pdo = conectarBanco();
$erros = [];
$form = ['nome' => '', 'email' => '', 'telefone' => '', 'municipio' => '', 'perfil' => 'produtor', 'ativo' => 1];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();

    $dados = lerDadosUsuarioDoPost();
    $senha = $_POST['senha'] ?? '';
    $perfil = ($_POST['perfil'] ?? 'produtor') === 'admin' ? 'admin' : 'produtor';
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    $erros = validarDadosUsuario($dados, $senha, true);
    if (!$erros && emailEmUso($pdo, $dados['email'])) {
        $erros[] = 'Já existe uma conta com esse e-mail.';
    }

    if (!$erros) {
        $ins = $pdo->prepare(
            'INSERT INTO produtores (nome, email, senha_hash, telefone, municipio, perfil, ativo)
             VALUES (:nome, :email, :senha_hash, :telefone, :municipio, :perfil, :ativo)'
        );
        $ins->execute($dados + [
            'senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
            'perfil' => $perfil,
            'ativo' => $ativo,
        ]);
        mensagemFlash('Perfil criado com sucesso.');
        redirecionar('admin/usuarios.php');
    }

    $form = $dados + ['perfil' => $perfil, 'ativo' => $ativo];
}

require __DIR__ . '/../includes/header.php';
?>
<h1>Novo perfil</h1>
<?php foreach ($erros as $e): ?>
    <div class="alerta alerta-erro"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>
<form method="post" class="formulario">
    <?= csrfCampo() ?>
    <label>Nome completo
        <input type="text" name="nome" value="<?= htmlspecialchars($form['nome']) ?>" required>
    </label>
    <label>E-mail
        <input type="email" name="email" value="<?= htmlspecialchars($form['email']) ?>" required>
    </label>
    <label>Telefone (WhatsApp, com DDD)
        <input type="text" name="telefone" value="<?= htmlspecialchars($form['telefone']) ?>" required>
    </label>
    <label>Município
        <input type="text" name="municipio" value="<?= htmlspecialchars($form['municipio']) ?>" required>
    </label>
    <label>Senha inicial (mínimo <?= SENHA_TAMANHO_MINIMO ?> caracteres)
        <input type="password" name="senha" required autocomplete="new-password">
    </label>
    <label>Tipo de conta
        <select name="perfil">
            <option value="produtor" <?= $form['perfil'] === 'produtor' ? 'selected' : '' ?>>Produtor</option>
            <option value="admin" <?= $form['perfil'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
    </label>
    <label class="checkbox">
        <input type="checkbox" name="ativo" value="1" <?= $form['ativo'] ? 'checked' : '' ?>>
        Conta ativa (pode fazer login)
    </label>
    <button type="submit">Criar perfil</button>
</form>
<p><a href="<?= BASE_URL ?>/admin/usuarios.php">&larr; Voltar</a></p>
<?php require __DIR__ . '/../includes/footer.php'; ?>
