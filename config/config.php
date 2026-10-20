<?php
/**
 * Configurações gerais do sistema
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '/agrovigia-ro');
define('UPLOAD_DIR', __DIR__ . '/../uploads/ocorrencias/');
define('UPLOAD_URL', BASE_URL . '/uploads/ocorrencias/');

// Regra de negócio: nº mínimo de ocorrências parecidas na mesma região
// e janela de tempo (dias) para considerar "concentração" e disparar alerta.
define('ALERTA_QTD_MINIMA', 3);
define('ALERTA_JANELA_DIAS', 7);

require_once __DIR__ . '/database.php';

function ehAdmin(): bool
{
    return ($_SESSION['produtor_perfil'] ?? '') === 'admin';
}

/**
 * Exige login e revalida a conta no banco a cada requisição:
 * se o admin desativar, excluir ou rebaixar o usuário, o efeito é imediato,
 * mesmo que ele ainda esteja com a sessão aberta.
 */
function exigirLogin(): void
{
    if (!isset($_SESSION['produtor_id'])) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }

    $pdo = conectarBanco();
    $stmt = $pdo->prepare('SELECT nome, perfil, ativo FROM produtores WHERE id = :id');
    $stmt->execute(['id' => $_SESSION['produtor_id']]);
    $usuario = $stmt->fetch();

    if (!$usuario || !(int)$usuario['ativo']) {
        session_unset();
        session_regenerate_id(true);
        mensagemFlash('Sua conta foi desativada ou não existe mais.', 'erro');
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }

    $_SESSION['produtor_nome'] = $usuario['nome'];
    $_SESSION['produtor_perfil'] = $usuario['perfil'];
}

function exigirAdmin(): void
{
    exigirLogin();
    if (!ehAdmin()) {
        http_response_code(403);
        mensagemFlash('Acesso restrito a administradores.', 'erro');
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

// ---- Proteção CSRF (usada nos formulários de perfil e de administração) ----
function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfCampo(): string
{
    return '<input type="hidden" name="csrf" value="' . csrfToken() . '">';
}

function csrfValidar(): void
{
    $enviado = $_POST['csrf'] ?? '';
    if (!is_string($enviado) || !hash_equals($_SESSION['csrf'] ?? '', $enviado)) {
        http_response_code(400);
        die('Requisição inválida (token CSRF). Volte, recarregue a página e tente de novo.');
    }
}

function redirecionar(string $caminho): void
{
    header('Location: ' . BASE_URL . '/' . ltrim($caminho, '/'));
    exit;
}

function mensagemFlash(string $texto, string $tipo = 'sucesso'): void
{
    $_SESSION['flash'] = ['texto' => $texto, 'tipo' => $tipo];
}
