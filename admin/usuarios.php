<?php
require_once __DIR__ . '/../config/config.php';
exigirAdmin();

$pdo = conectarBanco();
$meuId = (int)$_SESSION['produtor_id'];

// ---- filtros (GET) ----
$busca  = trim($_GET['q'] ?? '');
$perfil = $_GET['perfil'] ?? '';
$status = $_GET['status'] ?? '';

$where = [];
$params = [];

if ($busca !== '') {
    $where[] = '(pr.nome LIKE :q1 OR pr.email LIKE :q2 OR pr.municipio LIKE :q3)';
    $like = '%' . $busca . '%';
    $params['q1'] = $params['q2'] = $params['q3'] = $like;
}
if (in_array($perfil, ['produtor', 'admin'], true)) {
    $where[] = 'pr.perfil = :perfil';
    $params['perfil'] = $perfil;
}
if ($status === 'ativo' || $status === 'inativo') {
    $where[] = 'pr.ativo = :ativo';
    $params['ativo'] = $status === 'ativo' ? 1 : 0;
}

$sql = "SELECT pr.id, pr.nome, pr.email, pr.telefone, pr.municipio, pr.perfil, pr.ativo, pr.criado_em,
               (SELECT COUNT(*) FROM propriedades p WHERE p.produtor_id = pr.id) AS qtd_propriedades,
               (SELECT COUNT(*)
                  FROM ocorrencias o
                  JOIN talhoes t ON t.id = o.talhao_id
                  JOIN propriedades p ON p.id = t.propriedade_id
                 WHERE p.produtor_id = pr.id) AS qtd_ocorrencias
        FROM produtores pr"
     . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
     . ' ORDER BY pr.nome';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<h1>Gerenciar perfis</h1>
<p>
    <a class="botao" href="<?= BASE_URL ?>/admin/usuario_criar.php">+ Novo perfil</a>
    <a class="botao botao-secundario" href="<?= BASE_URL ?>/admin/index.php">&larr; Voltar à administração</a>
</p>

<form method="get" class="filtros">
    <input type="text" name="q" placeholder="Buscar por nome, e-mail ou município" value="<?= htmlspecialchars($busca) ?>">
    <select name="perfil">
        <option value="">Todos os tipos</option>
        <option value="produtor" <?= $perfil === 'produtor' ? 'selected' : '' ?>>Produtores</option>
        <option value="admin" <?= $perfil === 'admin' ? 'selected' : '' ?>>Administradores</option>
    </select>
    <select name="status">
        <option value="">Qualquer status</option>
        <option value="ativo" <?= $status === 'ativo' ? 'selected' : '' ?>>Ativos</option>
        <option value="inativo" <?= $status === 'inativo' ? 'selected' : '' ?>>Desativados</option>
    </select>
    <button type="submit">Filtrar</button>
    <a href="<?= BASE_URL ?>/admin/usuarios.php">limpar</a>
</form>

<p><?= count($usuarios) ?> perfil(is) encontrado(s).</p>

<div class="tabela-scroll">
<table class="tabela">
    <thead>
        <tr>
            <th>Nome</th>
            <th>E-mail / telefone</th>
            <th>Município</th>
            <th>Tipo</th>
            <th>Status</th>
            <th title="propriedades / ocorrências">Prop. / Ocorr.</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($usuarios as $u): ?>
        <?php $souEu = (int)$u['id'] === $meuId; ?>
        <tr class="<?= $u['ativo'] ? '' : 'linha-inativa' ?>">
            <td>
                <?= htmlspecialchars($u['nome']) ?>
                <?php if ($souEu): ?><small>(você)</small><?php endif; ?>
            </td>
            <td>
                <?= htmlspecialchars($u['email']) ?><br>
                <small><?= htmlspecialchars($u['telefone']) ?></small>
            </td>
            <td><?= htmlspecialchars($u['municipio']) ?></td>
            <td><span class="badge badge-<?= htmlspecialchars($u['perfil']) ?>"><?= $u['perfil'] === 'admin' ? 'Admin' : 'Produtor' ?></span></td>
            <td><span class="badge <?= $u['ativo'] ? 'badge-ativo' : 'badge-inativo' ?>"><?= $u['ativo'] ? 'Ativo' : 'Desativado' ?></span></td>
            <td><?= (int)$u['qtd_propriedades'] ?> / <?= (int)$u['qtd_ocorrencias'] ?></td>
            <td class="acoes-admin">
                <a href="<?= BASE_URL ?>/admin/usuario_editar.php?id=<?= (int)$u['id'] ?>">editar</a>
                <?php if (!$souEu): ?>
                    <form method="post" action="<?= BASE_URL ?>/admin/usuario_alternar.php" class="inline-form">
                        <?= csrfCampo() ?>
                        <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                        <button type="submit" class="link-botao"><?= $u['ativo'] ? 'desativar' : 'reativar' ?></button>
                    </form>
                    <form method="post" action="<?= BASE_URL ?>/admin/usuario_excluir.php" class="inline-form"
                          onsubmit="return confirm('Excluir DEFINITIVAMENTE esta conta e todos os dados associados?')">
                        <?= csrfCampo() ?>
                        <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                        <button type="submit" class="link-botao link-perigo">excluir</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($usuarios)): ?>
        <tr><td colspan="7">Nenhum perfil encontrado com esses filtros.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
