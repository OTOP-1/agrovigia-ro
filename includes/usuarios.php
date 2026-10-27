<?php
/**
 * Funções compartilhadas entre "Meu perfil" (usuário edita a si mesmo)
 * e a área de administração (admin gerencia todos os perfis).
 */

require_once __DIR__ . '/../config/config.php';

const SENHA_TAMANHO_MINIMO = 10;

/** Deixa só os dígitos do telefone (o sistema de alertas usa esse formato). */
function normalizarTelefone(string $telefone): string
{
    return preg_replace('/\D+/', '', $telefone);
}

/**
 * Validação no SERVIDOR (RNF02). Retorna uma lista de mensagens de erro;
 * lista vazia = dados válidos.
 *
 * $dados: nome, email, telefone (já normalizado), municipio
 * $senha: nova senha em texto (ou '' se não está sendo alterada)
 */
function validarDadosUsuario(array $dados, string $senha = '', bool $senhaObrigatoria = false): array
{
    $erros = [];

    $nome = trim($dados['nome'] ?? '');
    if (mb_strlen($nome) < 3 || mb_strlen($nome) > 120) {
        $erros[] = 'Informe o nome completo (entre 3 e 120 caracteres).';
    }

    $email = trim($dados['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
        $erros[] = 'Informe um e-mail válido.';
    }

    $telefone = $dados['telefone'] ?? '';
    if (strlen($telefone) < 10 || strlen($telefone) > 13) {
        $erros[] = 'Informe um telefone com DDD (10 a 13 dígitos).';
    }

    $municipio = trim($dados['municipio'] ?? '');
    if ($municipio === '' || mb_strlen($municipio) > 100) {
        $erros[] = 'Informe o município (até 100 caracteres).';
    }

    if ($senhaObrigatoria || $senha !== '') {
        if (strlen($senha) < SENHA_TAMANHO_MINIMO || strlen($senha) > 72) {
            $erros[] = 'A senha deve ter entre ' . SENHA_TAMANHO_MINIMO . ' e 72 caracteres.';
        }
    }

    return $erros;
}

/** Verifica se o e-mail já pertence a OUTRA conta (ignora a conta $ignorarId). */
function emailEmUso(PDO $pdo, string $email, int $ignorarId = 0): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM produtores WHERE email = :email AND id <> :id');
    $stmt->execute(['email' => $email, 'id' => $ignorarId]);
    return (int)$stmt->fetchColumn() > 0;
}

/** Lê os campos de texto de um formulário de usuário (sem a senha). */
function lerDadosUsuarioDoPost(): array
{
    return [
        'nome'      => trim($_POST['nome'] ?? ''),
        'email'     => trim($_POST['email'] ?? ''),
        'telefone'  => normalizarTelefone($_POST['telefone'] ?? ''),
        'municipio' => trim($_POST['municipio'] ?? ''),
    ];
}

/**
 * Exclui o usuário e, em cascata (FK ON DELETE CASCADE), suas propriedades,
 * talhões, ocorrências e alertas. Antes, apaga do disco as fotos das
 * ocorrências dele para não deixar arquivos órfãos em uploads/.
 */
function excluirUsuarioComFotos(PDO $pdo, int $usuarioId): void
{
    $stmt = $pdo->prepare(
        'SELECT o.foto
         FROM ocorrencias o
         JOIN talhoes t ON t.id = o.talhao_id
         JOIN propriedades p ON p.id = t.propriedade_id
         WHERE p.produtor_id = :id AND o.foto IS NOT NULL'
    );
    $stmt->execute(['id' => $usuarioId]);
    $fotos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $del = $pdo->prepare('DELETE FROM produtores WHERE id = :id');
    $del->execute(['id' => $usuarioId]);

    foreach ($fotos as $foto) {
        $caminho = UPLOAD_DIR . basename($foto);
        if (is_file($caminho)) {
            unlink($caminho);
        }
    }
}
