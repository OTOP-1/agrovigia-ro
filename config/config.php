<?php
/**
 * Configurações gerais do sistema
 */

require_once __DIR__ . '/env.php';

// Erros detalhados só em desenvolvimento (APP_DEBUG=1 no .env). Online: sempre 0.
define('APP_DEBUG', in_array(strtolower((string)env('APP_DEBUG', '0')), ['1', 'true', 'on', 'yes'], true));
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
if (!APP_DEBUG) error_reporting(E_ALL);
if (APP_DEBUG) {
    error_reporting(E_ALL);
}

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'httponly' => true,                       // JavaScript não lê o cookie de sessão
        'samesite' => 'Lax',                      // não é enviado em requisições de outros sites
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                      || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'),  // só por HTTPS quando o site usa HTTPS
    ]);
    session_start();
}

// Caminho da aplicação na URL. XAMPP (htdocs/agrovigia-ro): /agrovigia-ro
// Hospedagem com o site na raiz do domínio: APP_BASE_URL= (vazio) no .env.
define('BASE_URL', rtrim((string)env('APP_BASE_URL', '/agrovigia-ro'), '/'));
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
    return '<input type="hidden" name="csrf" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

function csrfValidar(): void
{
    $enviado = $_POST['csrf'] ?? '';
    if (!is_string($enviado) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $enviado)) {
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

/** Verifica coordenadas decimais sem aceitar valores fora do planeta. */
function coordenadaOpcional(?string $valor, bool $latitude): ?string
{
    $valor = trim((string)$valor);
    if ($valor === '') return null;
    $n = filter_var($valor, FILTER_VALIDATE_FLOAT);
    $limite = $latitude ? 90 : 180;
    if ($n === false || $n < -$limite || $n > $limite) {
        throw new InvalidArgumentException($latitude ? 'Latitude inválida.' : 'Longitude inválida.');
    }
    return (string)$n;
}

function areaOpcional(?string $valor): ?string
{
    $valor=trim((string)$valor);
    if ($valor === '') return null;
    if (!is_numeric($valor) || !is_finite((float)$valor) || (float)$valor <= 0 || (float)$valor > 999999) {
        throw new InvalidArgumentException('Área inválida: informe um número de hectares positivo.');
    }
    return (string)(float)$valor;
}
