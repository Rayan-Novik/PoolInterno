<?php include 'header.php'; ?>

<style>
    .dashboard-card {
        border: none;
        border-radius: 12px;
        color: white;
        transition: transform 0.2s ease-in-out;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        min-height: 300px; /* altura mínima uniforme */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
    }

    .bg-dominios { background: linear-gradient(135deg, #007bff, #00bfff); }
    .bg-ramais { background: linear-gradient(135deg, #28a745, #00c97b); }
    .bg-acessos { background: linear-gradient(135deg, #dc3545, #ff6b6b); }
    .bg-almoco { background: linear-gradient(135deg, #f39c12, #f1c40f); }
    .bg-emails { background: linear-gradient(135deg, #8e44ad, #9b59b6); }
    .bg-remote { background: linear-gradient(135deg, #e74c3c, #ff7675); }
    .bg-monitoramento { background: linear-gradient(135deg, #1abc9c, #48c9b0); }
    .bg-trafego { background: linear-gradient(135deg, #2c3e50, #34495e); }

    .dashboard-icon {
        font-size: 3rem;
        opacity: 0.9;
    }

    .dashboard-title {
        font-size: 1.3rem;
        font-weight: bold;
        margin-top: 10px;
    }

    .dashboard-text {
        font-size: 0.9rem;
        margin-top: 5px;
        opacity: 0.85;
    }

    .dashboard-btn {
        margin-top: 10px;
        background-color: rgba(255, 255, 255, 0.15);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .dashboard-btn:hover {
        background-color: rgba(255, 255, 255, 0.25);
        color: #fff;
    }
</style>

<div class="container mt-4">
<h2 class="mb-4 fw-bold"></i> PAINEL DE CONTROLE</h2>


    <div class="row g-4">
        <!-- Domínios -->
        <div class="col-md-6 col-lg-3">
            <div class="card dashboard-card bg-dominios text-center p-4">
                <div>
                    <div class="dashboard-icon"><i class="bi bi-globe2"></i></div>
                    <div class="dashboard-title">Domínios</div>
                    <div class="dashboard-text">Gerencie e monitore os domínios da empresa.</div>
                </div>
                <a href="index.php?page=dominios" class="btn dashboard-btn btn-sm">Acessar</a>
            </div>
        </div>

        <!-- E-mails -->
        <div class="col-md-6 col-lg-3">
            <div class="card dashboard-card bg-emails text-center p-4">
                <div>
                    <div class="dashboard-icon"><i class="bi bi-envelope-fill"></i></div>
                    <div class="dashboard-title">E-mails</div>
                    <div class="dashboard-text">Gerencie as contas de e-mail corporativas.</div>
                </div>
                <a href="index.php?page=emails" class="btn dashboard-btn btn-sm">Gerenciar</a>
            </div>
        </div>

        <!-- Ramais -->
        <div class="col-md-6 col-lg-3">
            <div class="card dashboard-card bg-ramais text-center p-4">
                <div>
                    <div class="dashboard-icon"><i class="bi bi-telephone-fill"></i></div>
                    <div class="dashboard-title">Ramais</div>
                    <div class="dashboard-text">Lista e status dos ramais da empresa.</div>
                </div>
                <a href="index.php?page=ramais" class="btn dashboard-btn btn-sm">Visualizar</a>
            </div>
        </div>

        <!-- Acessos -->
        <div class="col-md-6 col-lg-3">
            <div class="card dashboard-card bg-acessos text-center p-4">
                <div>
                    <div class="dashboard-icon"><i class="bi bi-shield-lock-fill"></i></div>
                    <div class="dashboard-title">Acessos</div>
                    <div class="dashboard-text">Controle de senhas e permissões.</div>
                </div>
                <a href="index.php?page=acessos" class="btn dashboard-btn btn-sm">Gerenciar</a>
            </div>
        </div>

        <!-- Acesso Remoto -->
        <div class="col-md-6 col-lg-3">
            <div class="card dashboard-card bg-remote text-center p-4">
                <div>
                    <div class="dashboard-icon"><i class="bi bi-pc-display-horizontal"></i></div>
                    <div class="dashboard-title">Acesso Remoto</div>
                    <div class="dashboard-text">Controle de máquinas com AnyDesk.</div>
                </div>
                <a href="index.php?page=anydesk" class="btn dashboard-btn btn-sm">Acessar</a>
            </div>
        </div>

        <!-- Tráfego -->
        <div class="col-md-6 col-lg-3">
            <div class="card dashboard-card bg-trafego text-center p-4">
                <div>
                    <div class="dashboard-icon"><i class="bi bi-graph-up-arrow"></i></div>
                    <div class="dashboard-title">Tráfego</div>
                    <div class="dashboard-text">Monitoramento de rede e DDNS.</div>
                </div>
                <a href="index.php?page=trafegorede" class="btn dashboard-btn btn-sm">Analisar</a>
            </div>
        </div>

        <!-- Monitoramento -->
        <div class="col-md-6 col-lg-3">
            <div class="card dashboard-card bg-monitoramento text-center p-4">
                <div>
                    <div class="dashboard-icon"><i class="bi bi-server"></i></div>
                    <div class="dashboard-title">Monitoramento</div>
                    <div class="dashboard-text">Status de servidores, links e serviços.</div>
                </div>
                <a href="index.php?page=monitoramento" class="btn dashboard-btn btn-sm">Verificar</a>
            </div>
        </div>

        <!-- Almoço -->
        <div class="col-md-6 col-lg-3">
            <div class="card dashboard-card bg-almoco text-center p-4">
                <div>
                    <div class="dashboard-icon"><i class="bi bi-egg-fried"></i></div>
                    <div class="dashboard-title">Almoço</div>
                    <div class="dashboard-text">Registro e controle de assinaturas de almoço.</div>
                </div>
                <a href="index.php?page=almoco" class="btn dashboard-btn btn-sm">Assinar</a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
