<?php
require_once __DIR__ . '/../config/config.php';
exigirLogin();

$pdo = conectarBanco();

// pontos para o mapa (ocorrências com coordenadas, do produtor logado)
$stmtPontos = $pdo->prepare(
    'SELECT o.id, o.sintoma_descricao, o.latitude, o.longitude, o.criado_em, t.nome AS talhao_nome
     FROM ocorrencias o
     JOIN talhoes t ON t.id = o.talhao_id
     JOIN propriedades p ON p.id = t.propriedade_id
     WHERE p.produtor_id = :produtor_id AND o.latitude IS NOT NULL AND o.longitude IS NOT NULL'
);
$stmtPontos->execute(['produtor_id' => $_SESSION['produtor_id']]);
$pontos = $stmtPontos->fetchAll();

// ocorrências por dia (últimos 30 dias) para o gráfico
$stmtGrafico = $pdo->prepare(
    "SELECT DATE(o.criado_em) AS dia, COUNT(*) AS total
     FROM ocorrencias o
     JOIN talhoes t ON t.id = o.talhao_id
     JOIN propriedades p ON p.id = t.propriedade_id
     WHERE p.produtor_id = :produtor_id
       AND o.criado_em >= (NOW() - INTERVAL 30 DAY)
     GROUP BY DATE(o.criado_em)
     ORDER BY dia"
);
$stmtGrafico->execute(['produtor_id' => $_SESSION['produtor_id']]);
$grafico = $stmtGrafico->fetchAll();

// alertas recentes disparados para a região do produtor
$stmtAlertas = $pdo->prepare(
    'SELECT DISTINCT a.municipio, a.quantidade_ocorrencias, a.enviado_em
     FROM alertas a
     JOIN produtores pr ON pr.id = :produtor_id
     WHERE a.municipio = pr.municipio
     ORDER BY a.enviado_em DESC
     LIMIT 10'
);
$stmtAlertas->execute(['produtor_id' => $_SESSION['produtor_id']]);
$alertas = $stmtAlertas->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<h1>Painel</h1>

<h2>Mapa de ocorrências</h2>
<div id="mapa" style="height:400px; border-radius:8px;"></div>

<h2>Ocorrências nos últimos 30 dias</h2>
<canvas id="graficoOcorrencias" height="100"></canvas>

<h2>Alertas recentes na sua região</h2>
<p class="subtitulo">Alertas demonstrativos registrados no banco. O sistema não envia mensagens reais por WhatsApp ou SMS.</p>
<table class="tabela">
    <thead>
        <tr><th>Município</th><th>Ocorrências que geraram o alerta</th><th>Data</th></tr>
    </thead>
    <tbody>
    <?php foreach ($alertas as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a['municipio']) ?></td>
            <td><?= (int)$a['quantidade_ocorrencias'] ?></td>
            <td><?= htmlspecialchars($a['enviado_em']) ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($alertas)): ?>
        <tr><td colspan="3">Nenhum alerta disparado na sua região ainda.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

<script>
const pontos = <?= json_encode($pontos, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
const dadosGrafico = <?= json_encode($grafico, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

// O conteúdo das ocorrências é fornecido pelos usuários e não deve virar HTML.
function limparHtml(texto) {
    return String(texto ?? '').replace(/[&<>\"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',"'":'&#39;'}[c]));
}
// ---- Mapa ----
const mapa = L.map('mapa').setView([-10.9472, -62.8182], 7); // centro aproximado de Rondônia
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(mapa);

pontos.forEach(p => {
    L.marker([parseFloat(p.latitude), parseFloat(p.longitude)])
        .addTo(mapa)
        .bindPopup(`<strong>${limparHtml(p.talhao_nome)}</strong><br>${limparHtml(p.sintoma_descricao)}<br><small>${limparHtml(p.criado_em)}</small>`);
});

// ---- Gráfico ----
new Chart(document.getElementById('graficoOcorrencias'), {
    type: 'bar',
    data: {
        labels: dadosGrafico.map(d => d.dia),
        datasets: [{
            label: 'Ocorrências registradas',
            data: dadosGrafico.map(d => d.total),
            backgroundColor: '#3a7d44'
        }]
    },
    options: {
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
