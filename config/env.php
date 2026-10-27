<?php
/**
 * Leitor simples de variáveis de ambiente (arquivo .env), SEM dependências
 * (não precisa de Composer). As credenciais do banco e as configurações
 * que mudam de ambiente para ambiente ficam no .env, que NÃO vai para o Git.
 * O arquivo .env.example (que vai para o Git) mostra quais variáveis existem.
 *
 * Regras do .env:
 *  - uma variável por linha:  CHAVE=valor
 *  - linhas que começam com # são comentários (comentário só em linha própria)
 *  - valor pode vir entre aspas simples ou duplas
 *  - variável de ambiente REAL do servidor, se existir, vale mais que o .env
 */

function envArquivo(): array
{
    static $valores = null;
    if ($valores !== null) {
        return $valores;
    }
    $valores = [];

    $caminho = dirname(__DIR__) . '/.env';
    if (!is_file($caminho) || !is_readable($caminho)) {
        return $valores;
    }

    $conteudo = file_get_contents($caminho);
    $conteudo = preg_replace('/^\xEF\xBB\xBF/', '', $conteudo); // remove BOM (Bloco de Notas)

    foreach (preg_split('/\R/', $conteudo) as $linha) {
        $linha = trim($linha);
        if ($linha === '' || $linha[0] === '#' || strpos($linha, '=') === false) {
            continue;
        }
        [$chave, $valor] = explode('=', $linha, 2);
        $chave = trim($chave);
        $valor = trim($valor);

        $tamanho = strlen($valor);
        if ($tamanho >= 2 && ($valor[0] === '"' || $valor[0] === "'") && $valor[$tamanho - 1] === $valor[0]) {
            $valor = substr($valor, 1, -1);
        }
        if ($chave !== '') {
            $valores[$chave] = $valor;
        }
    }
    return $valores;
}

/** Lê uma variável de ambiente; devolve $padrao se ela não existir. */
function env(string $chave, ?string $padrao = null): ?string
{
    $real = getenv($chave);
    if ($real !== false) {
        return $real;
    }
    $arquivo = envArquivo();
    return array_key_exists($chave, $arquivo) ? $arquivo[$chave] : $padrao;
}
