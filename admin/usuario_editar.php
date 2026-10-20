<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/usuarios.php';
exigirAdmin();

$pdo = conectarBanco();
$meuId = (int)$_SESSION['produtor_id'];
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM produtores WHERE id = :id');
$stmt->execute(['id' => $id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    mensagemFlash('Perfil não encontrado.', 'erro');
    redirecionar('admin/usuarios.php');
}

$souEu = $id === $meuId;
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfValidar();

    $dados = lerDadosUsuarioDoPost();
    $novaSenha = $_POST['nova_senha'] ?? '';

    // Trava de segurança: o admin não pode rebaixar nem desativar a PRÓPRIA conta
    // (senão poderia ficar sem nenhum administrador ativo no sistema).
    if ($souEu) {
        $perfil = $usuario['perfil'];
        $ativo = (int)$usuario['ativo'];
    } else {
        $perfil = ($_POST['perfil'] ?? 'produtor') === 'admin' ? 'admin' : 'produtor';
        $ativo = isset($_POST['ativo']) ? 1 : 0;
    }

    $erros = validarDadosUsuario($dados, $novaSenha, false);
    if (!$erros && emailEmUso($pdo, $dados['email'], $id)) {
        $erros[] = 'Já existe outra conta com esse e-mail.';
    }

    if (!$erros) {
        $campos = 'nome = :nome, email = :email, telefone = :telefone, municipio = :municipio,
                   perfil = :perfil, ativo = :ativo';
        $params = $dados + ['perfil' => $perfil, 'ativo' => $ativo, 'id' => $id];

        if ($novaSenha !== '') {
            $campos .= ', senha_hash = :senha_hash';
            $params['senha_hash'] = password_hash($novaSenha, PASSWORD_DEFAULT);
        }

        $up = $pdo->prepare("UPDATE produtores SET $campos WHERE id = :id");
        $up->execute($params);

        mensagemFlash('Perfil atualizado.');
        redirecionar('admin/usuarios.php');
    }

    $usuario = array_merge($usuario, $dados, ['perfil' => $perfil, 'ativo' => $ativo]);
}

// resumo do que esse usuário tem no sistema
$stmtRes = $pdo->prepare(
    'SELECT
        (SELECT COUNT(*) FROM propriedades WHERE produtor_id = :id1) AS propriedades,
        (SELECT COUNT(*) FROM talhoes t JOIN propriedades p ON p.id = t.propriedade_id WHERE p.produtor_id = :id2) AS talhoes,
        (SELECT COUNT(*) FROM ocorrencias o
            JOIN talhoes t ON t.id = o.talhao_id
            JOIN propriedades p ON p.id = t.propriedade_id
          WHERE p.produtor_id = :id3) AS ocorrencias'
);
$stmtRes->execute(['id1' => $id, 'id2' => $id, 'id3' => $id]);
$resumo = $stmtRes->fetch();

require __DIR__ . '/../includes/header.php';
?>
<h1>Editar perfil</h1>
<p class="subtitulo">
    <?= (int)$resumo['propriedades'] ?> propriedade(s),
    <?= (int)$resumo['talhoes'] ?> talhão(ões),
    <?= (int)$resumo['ocorrencias'] ?> ocorrência(s) &middot;
    cadastrado em <?= htmlspecialchars(date('d/m/Y', strtotime($usuario['criado_em']))) ?>
</p>

<?php foreach ($erros as $e): ?>
    <div class="alerta alerta-erro"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<form method="post" class="formulario">
    <?= csrfCampo() ?>
    <input type="hidden" name="id" value="<?= (int)$usuario['id'] ?>">
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

    <?php if ($souEu): ?>
        <p class="aviso">Esta é a sua própria conta: por segurança, você não pode alterar seu tipo de conta
        nem desativá-la por aqui.</p>
    <?php else: ?>
        <label>Tipo de conta
            <select name="perfil">
                <option value="produtor" <?= $usuario['perfil'] === 'produtor' ? 'selected' : '' ?>>Produtor</option>
                <option value="admin" <?= $usuario['perfil'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </label>
        <label class="checkbox">
            <input type="checkbox" name="ativo" value="1" <?= $usuario['ativo'] ? 'checked' : '' ?>>
            Conta ativa (pode fazer login)
        </label>
    <?php endif; ?>

    <label>Redefinir senha (deixe em branco para manter a atual)
        <input type="password" name="nova_senha" autocomplete="new-password">
    </label>
    <button type="submit">Salvar alterações</button>
</form>
<p><a href="<?= BASE_URL ?>/admin/usuarios.php">&larr; Voltar à lista</a></p>
<?php require __DIR__ . '/../includes/footer.php'; ?>
