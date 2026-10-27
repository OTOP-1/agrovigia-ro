<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/usuarios.php';
exigirLogin();

$pdo = conectarBanco();
$meuId = (int)$_SESSION['produtor_id'];

$stmt = $pdo->prepare(
    'SELECT id, nome, email, telefone, municipio, perfil, criado_em FROM produtores WHERE id = :id'
);
$stmt->execute(['id' => $meuId]);
$usuario = $stmt->fetch();

$errosDados = [];
$errosSenha = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'dados') {
        // O usuário NUNCA altera o próprio perfil (produtor/admin) por aqui:
        // só nome, e-mail, telefone e município.
        $dados = lerDadosUsuarioDoPost();
        $errosDados = validarDadosUsuario($dados);

        if (!$errosDados && emailEmUso($pdo, $dados['email'], $meuId)) {
            $errosDados[] = 'Este e-mail já está sendo usado por outra conta.';
        }

        if (!$errosDados) {
            $up = $pdo->prepare(
                'UPDATE produtores
                 SET nome = :nome, email = :email, telefone = :telefone, municipio = :municipio
                 WHERE id = :id'
            );
            $up->execute($dados + ['id' => $meuId]);
            $_SESSION['produtor_nome'] = $dados['nome'];
            mensagemFlash('Seus dados foram atualizados.');
            redirecionar('perfil/editar.php');
        }

        // mantém o que a pessoa digitou para ela corrigir
        $usuario = array_merge($usuario, $dados);

    } elseif ($acao === 'senha') {
        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmacao = $_POST['confirmacao'] ?? '';

        $stmtHash = $pdo->prepare('SELECT senha_hash FROM produtores WHERE id = :id');
        $stmtHash->execute(['id' => $meuId]);
        $hashAtual = $stmtHash->fetchColumn();

        if (!password_verify($senhaAtual, $hashAtual)) {
            $errosSenha[] = 'A senha atual está incorreta.';
        }
        if (strlen($novaSenha) < SENHA_TAMANHO_MINIMO || strlen($novaSenha) > 72) {
            $errosSenha[] = 'A nova senha deve ter de ' . SENHA_TAMANHO_MINIMO . ' a 72 caracteres.';
        }
        if ($novaSenha !== $confirmacao) {
            $errosSenha[] = 'A confirmação não é igual à nova senha.';
        }

        if (!$errosSenha) {
            $up = $pdo->prepare('UPDATE produtores SET senha_hash = :hash WHERE id = :id');
            $up->execute(['hash' => password_hash($novaSenha, PASSWORD_DEFAULT), 'id' => $meuId]);
            session_regenerate_id(true);
            mensagemFlash('Senha alterada com sucesso.');
            redirecionar('perfil/editar.php');
        }
    }
}

require __DIR__ . '/../includes/header.php';
?>
<h1>Meu perfil</h1>
<p class="subtitulo">
    Tipo de conta:
    <span class="badge badge-<?= htmlspecialchars($usuario['perfil']) ?>"><?= $usuario['perfil'] === 'admin' ? 'Administrador' : 'Produtor' ?></span>
    &middot; Cadastrado em <?= htmlspecialchars(date('d/m/Y', strtotime($usuario['criado_em']))) ?>
</p>

<h2>Dados pessoais</h2>
<?php foreach ($errosDados as $e): ?>
    <div class="alerta alerta-erro"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>
<form method="post" class="formulario">
    <?= csrfCampo() ?>
    <input type="hidden" name="acao" value="dados">
    <label>Nome completo
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
    </label>
    <label>E-mail
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
    </label>
    <label>Telefone (WhatsApp, com DDD)
        <input type="text" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>" required>
    </label>
    <label>Município
        <input type="text" name="municipio" value="<?= htmlspecialchars($usuario['municipio']) ?>" required>
    </label>
    <button type="submit">Salvar dados</button>
</form>

<h2>Alterar senha</h2>
<?php foreach ($errosSenha as $e): ?>
    <div class="alerta alerta-erro"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>
<form method="post" class="formulario">
    <?= csrfCampo() ?>
    <input type="hidden" name="acao" value="senha">
    <label>Senha atual
        <input type="password" name="senha_atual" required autocomplete="current-password">
    </label>
    <label>Nova senha (mínimo <?= SENHA_TAMANHO_MINIMO ?> caracteres)
        <input type="password" name="nova_senha" required autocomplete="new-password">
    </label>
    <label>Confirmar nova senha
        <input type="password" name="confirmacao" required autocomplete="new-password">
    </label>
    <button type="submit">Alterar senha</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
