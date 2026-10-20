<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$propriedadeId = (int)($_GET['propriedade_id'] ?? $_POST['propriedade_id'] ?? 0);
$pdo = conectarBanco();

$stmtProp = $pdo->prepare('SELECT * FROM propriedades WHERE id = :id AND produtor_id = :produtor_id');
$stmtProp->execute(['id' => $propriedadeId, 'produtor_id' => $_SESSION['produtor_id']]);
$propriedade = $stmtProp->fetch();

if (!$propriedade) {
    mensagemFlash('Propriedade não encontrada.', 'erro');
    redirecionar('propriedades/listar.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cultura = trim($_POST['cultura'] ?? '');
    $area = $_POST['area_hectares'] !== '' ? $_POST['area_hectares'] : null;

    if ($nome === '' || $cultura === '') {
        mensagemFlash('Preencha nome e cultura do talhão.', 'erro');
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO talhoes (propriedade_id, nome, cultura, area_hectares)
             VALUES (:propriedade_id, :nome, :cultura, :area)'
        );
        $stmt->execute([
            'propriedade_id' => $propriedadeId,
            'nome' => $nome,
            'cultura' => $cultura,
            'area' => $area,
        ]);
        mensagemFlash('Talhão cadastrado com sucesso.');
    }
    redirecionar('talhoes/listar.php?propriedade_id=' . $propriedadeId);
}

require __DIR__ . '/../includes/header.php';
?>
<h1>Novo talhão em <?= htmlspecialchars($propriedade['nome']) ?></h1>
<form method="post" class="formulario">
    <input type="hidden" name="propriedade_id" value="<?= $propriedadeId ?>">
    <label>Nome do talhão
        <input type="text" name="nome" placeholder="Talhão 1" required>
    </label>
    <label>Cultura
        <input type="text" name="cultura" placeholder="cacau, café, pastagem..." required>
    </label>
    <label>Área (hectares, opcional)
        <input type="text" name="area_hectares">
    </label>
    <button type="submit">Salvar</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
