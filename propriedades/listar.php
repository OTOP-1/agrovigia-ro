<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$pdo = conectarBanco();
$stmt = $pdo->prepare(
    'SELECT id, nome, municipio, latitude, longitude
     FROM propriedades
     WHERE produtor_id = :produtor_id
     ORDER BY nome'
);
$stmt->execute(['produtor_id' => $_SESSION['produtor_id']]);
$propriedades = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<h1>Minhas propriedades</h1>
<a class="botao" href="<?= BASE_URL ?>/propriedades/criar.php">+ Nova propriedade</a>

<table class="tabela">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Município</th>
            <th>Talhões</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($propriedades as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['nome']) ?></td>
            <td><?= htmlspecialchars($p['municipio']) ?></td>
            <td><a href="<?= BASE_URL ?>/talhoes/listar.php?propriedade_id=<?= $p['id'] ?>">ver talhões</a></td>
            <td>
                <a href="<?= BASE_URL ?>/propriedades/editar.php?id=<?= $p['id'] ?>">editar</a>
                |
                <a href="<?= BASE_URL ?>/propriedades/excluir.php?id=<?= $p['id'] ?>"
                   onclick="return confirm('Excluir esta propriedade e todos os talhões/ocorrências dela?')">excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($propriedades)): ?>
        <tr><td colspan="4">Nenhuma propriedade cadastrada ainda.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>
