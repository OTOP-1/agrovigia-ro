<?php
/**
 * Conexão com o banco de dados (PDO)
 * AgroVigia RO
 *
 * Nenhuma credencial fica no código: tudo vem do arquivo .env
 * (veja .env.example e a seção "Instalação e execução" do spec.md).
 */

require_once __DIR__ . '/env.php';

function conectarBanco(): PDO
{
    $host    = env('DB_HOST');
    $nome    = env('DB_NAME');
    $usuario = env('DB_USER');
    $senha   = env('DB_PASS', '');
    $porta   = env('DB_PORT', '3306');

    if (!$host || !$nome || !$usuario || !$porta || !ctype_digit($porta) || (int)$porta < 1 || (int)$porta > 65535) {
        http_response_code(500);
        die('Configuração do banco ausente. Copie o arquivo .env.example para .env e preencha '
          . 'DB_HOST, DB_NAME, DB_USER e DB_PASS (veja a seção "Instalação e execução" do spec.md).');
    }

    try {
        $dsn = 'mysql:host=' . $host . ';port=' . $porta . ';dbname=' . $nome . ';charset=utf8mb4';
        $opcoes = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        return new PDO($dsn, $usuario, $senha, $opcoes);
    } catch (PDOException $e) {
        error_log('AgroVigia RO - falha na conexão com o banco: ' . $e->getMessage());
        http_response_code(500);
        if (defined('APP_DEBUG') && APP_DEBUG) {
            die('Erro na conexão com o banco de dados: ' . $e->getMessage());
        }
        die('Não foi possível conectar ao banco de dados. Verifique as configurações do arquivo .env.');
    }
}
