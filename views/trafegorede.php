<?php
include 'header.php';
?>

<link rel="stylesheet" href="assets/css/estilo_rede.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .card-custom {
        border: none;
        border-radius: 10px;
        min-height: 250px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .bg-gradient-blue {
        background: linear-gradient(135deg, #0d6efd, #66b2ff);
        color: white;
    }

    .bg-gradient-green {
        background: linear-gradient(135deg, #28a745, #a8e063);
        color: white;
    }

    .bg-gradient-dark {
        background: linear-gradient(135deg, #343a40, #6c757d);
        color: white;
    }

    .card h4, .card h5 {
        font-weight: 600;
    }

    .badge {
        font-size: 1rem;
        padding: 0.5em 1em;
    }

    .list-group-item {
        border: none;
        border-bottom: 1px solid #ddd;
        background: transparent;
    }

    @media (max-width: 768px) {
        canvas {
            width: 100% !important;
            height: auto !important;
        }

        .card-custom {
            padding: 1rem !important;
        }
    }
</style>

<div class="container mt-4">
    <h2 class="mb-4 text-center text-md-start"><i class="bi bi-graph-up-arrow"></i> Monitoramento de Tráfego de Rede</h2>

    <div class="row g-4">
        <!-- IP Público -->
        <div class="col-md-6">
            <div class="card card-custom bg-gradient-blue p-4 text-center">
                <h4><i class="bi bi-wifi"></i> IP Público</h4>
                <p class="fs-4 fw-bold mb-0" id="ipPublico">Carregando...</p>
            </div>
        </div>

        <!-- Gráfico -->
        <div class="col-md-6">
            <div class="card card-custom bg-gradient-green p-4">
                <h5><i class="bi bi-speedometer2"></i> Consumo de Rede (em tempo real)</h5>
                <canvas id="graficoRede" height="120"></canvas>
                <div class="mt-3 text-center">
                    <span class="badge bg-primary me-2">Upload: <span id="uploadTexto">0 KB/s</span></span>
                    <span class="badge bg-success">Download: <span id="downloadTexto">0 KB/s</span></span>
                </div>
            </div>
        </div>

        <!-- Máquinas na Rede -->
        <div class="col-12">
            <div class="card card-custom bg-gradient-dark p-4">
                <h5><i class="bi bi-pc-display-horizontal"></i> Máquinas Ativas na Rede</h5>
                <ul class="list-group list-group-flush mt-3" id="listaMaquinas">
                    <li class="list-group-item text-light">Escaneando rede...</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/grafico_rede.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // IP público
        fetch('includes/scan_rede.php?acao=ip')
            .then(res => res.json())
            .then(data => {
                document.getElementById("ipPublico").innerText = data.ip || "Não encontrado";
            });

        // Máquinas na rede
        fetch('includes/scan_rede.php?acao=maquinas')
            .then(res => res.json())
            .then(data => {
                const lista = document.getElementById("listaMaquinas");
                lista.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(ip => {
                        lista.innerHTML += `<li class="list-group-item text-light"><i class="bi bi-check-circle-fill text-success me-2"></i> ${ip}</li>`;
                    });
                } else {
                    lista.innerHTML = '<li class="list-group-item text-light">Nenhuma máquina ativa encontrada.</li>';
                }
            });
    });
</script>

<?php include 'footer.php'; ?>
