<?php
/**
 * Alertas internos SIMULADOS: conta ocorrências no mesmo município
 * e registra uma notificação na tabela `alertas`.
 * Não há envio real de WhatsApp/SMS ou conexão com a EMATER-RO.
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Verifica concentração de ocorrências recentes no município do talhão
 * informado e, se atingir o limite, dispara os alertas.
 */
function verificarConcentracaoEDispararAlerta(PDO $pdo, int $ocorrenciaId): void
{
    // Descobre o município da ocorrência recém-criada, via talhão -> propriedade
    $sql = "SELECT o.id, p.municipio, p.id AS propriedade_id
            FROM ocorrencias o
            JOIN talhoes t ON t.id = o.talhao_id
            JOIN propriedades p ON p.id = t.propriedade_id
            WHERE o.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $ocorrenciaId]);
    $ocorrencia = $stmt->fetch();

    if (!$ocorrencia) {
        return;
    }

    $municipio = $ocorrencia['municipio'];

    // Conta ocorrências recentes (janela de dias) no mesmo município,
    // olhando por todas as propriedades daquele município (não só a atual).
    $janelaDias = (int)ALERTA_JANELA_DIAS; // constante interna, nunca entrada do usuário
    $sqlConcentracao = "SELECT COUNT(*) AS total
            FROM ocorrencias o
            JOIN talhoes t ON t.id = o.talhao_id
            JOIN propriedades p ON p.id = t.propriedade_id
            WHERE p.municipio = :municipio
              AND o.criado_em >= (NOW() - INTERVAL {$janelaDias} DAY)";
    $stmt2 = $pdo->prepare($sqlConcentracao);
    $stmt2->bindValue(':municipio', $municipio);
    $stmt2->execute();
    $total = (int)$stmt2->fetch()['total'];

    if ($total < ALERTA_QTD_MINIMA) {
        return; // ainda não atingiu o limite para considerar "concentração"
    }

    // Produtores do mesmo município para registrar alertas internos (não há envio).
    $sqlVizinhos = "SELECT DISTINCT pr.id, pr.nome, pr.telefone
            FROM produtores pr
            JOIN propriedades p ON p.produtor_id = pr.id
            WHERE p.municipio = :municipio";
    $stmt3 = $pdo->prepare($sqlVizinhos);
    $stmt3->execute(['municipio' => $municipio]);
    $vizinhos = $stmt3->fetchAll();

    $mensagem = "⚠️ AgroVigia RO: foram registradas $total ocorrências de "
        . "possíveis pragas/doenças em lavouras de $municipio nos últimos "
        . ALERTA_JANELA_DIAS . " dias. Fique atento à sua lavoura e, se notar "
        . "algo, cadastre uma ocorrência ou procure a EMATER-RO.";

    $sqlInsereAlerta = "INSERT INTO alertas
            (ocorrencia_origem_id, municipio, quantidade_ocorrencias, canal, destinatario, mensagem)
            VALUES (:origem, :municipio, :qtd, :canal, :destinatario, :mensagem)";
    $stmtInsere = $pdo->prepare($sqlInsereAlerta);

    foreach ($vizinhos as $vizinho) {
        $stmtInsere->execute([
            'origem'       => $ocorrenciaId,
            'municipio'    => $municipio,
            'qtd'          => $total,
            'canal'        => 'whatsapp',
            'destinatario' => $vizinho['telefone'],
            'mensagem'     => $mensagem,
        ]);

        // Envio externo não configurado: registros ficam na tabela alertas.
    }
}

/**
 * Os alertas são SIMULADOS e registrados no banco. Não enviamos WhatsApp/SMS
 * nem gravamos telefones em logs do diretório público da hospedagem.
 * Integração com serviço externo exigiria consentimento e configuração adicional.
 */
