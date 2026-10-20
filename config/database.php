<?php
/**
 * Conexão com o banco de dados (PDO)
 * AgroVigia RO
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'agrovigia_ro');
define('DB_USER', 'root');
define('DB_PASS', 'alunoifro'); // ajuste conforme seu ambiente (XAMPP/WAMP/Laragon)

function conectarBanco(): PDO
{
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $opcoes = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $opcoes);
    } catch (PDOException $e) {
        die('Erro na conexão com o banco de dados: ' . $e->getMessage());
    }
}
