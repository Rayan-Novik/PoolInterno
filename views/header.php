<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Sistema Web</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --transition-speed: 0.3s;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f1f3f5;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background-color: #212529;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            transition: width var(--transition-speed);
            z-index: 1040;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            padding: 15px 20px;
            border-bottom: 1px solid #343a40;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #adb5bd;
            text-decoration: none;
            transition: background var(--transition-speed);
            border-radius: 4px;
        }

        .sidebar a:hover {
            background-color: #343a40;
            color: #fff;
        }

        .sidebar i {
            font-size: 1.3rem;
            margin-right: 16px;
            min-width: 24px;
            text-align: center;
        }

        .sidebar.collapsed a span {
            display: none;
        }

        .content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin-left var(--transition-speed);
        }

        .content.collapsed {
            margin-left: var(--sidebar-collapsed-width);
        }

        .toggle-btn {
            position: fixed;
            top: 10px;
            left: var(--sidebar-width);
            z-index: 1050;
            background-color: #212529;
            border: none;
            color: #fff;
            border-radius: 0 5px 5px 0;
            padding: 10px 12px;
            transition: left var(--transition-speed);
        }

        .collapsed + .toggle-btn {
            left: var(--sidebar-collapsed-width);
        }

        .header {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -100%;
                transition: left var(--transition-speed);
            }

            .sidebar.show {
                left: 0;
            }

            .toggle-btn {
                left: 10px;
            }

            .content,
            .content.collapsed {
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <strong class="fs-5">RSC</strong>
    </div>
    <a href="dashboard.php"><i class="bi bi-house-door-fill"></i><span>Início</span></a>
    <a href="pagamentos.php"><i class="bi bi-credit-card-2-front-fill"></i><span>Pagamentos</span></a>
    <a href="acessos.php"><i class="bi bi-key-fill"></i><span>Acessos</span></a>
    <a href="dominios.php"><i class="bi bi-globe2"></i><span>Domínios</span></a>
    <a href="ddns.php"><i class="bi bi-hdd-network-fill"></i><span>DDNS</span></a>
    <a href="senhasti.php"><i class="bi bi-shield-lock-fill"></i><span>Senhas</span></a>
    <a href="../auth/logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i><span>Sair</span></a>
</div>

<!-- Botão de Colapso -->
<button class="toggle-btn" id="collapseBtn"><i class="bi bi-chevron-left" id="collapseIcon"></i></button>

<!-- Conteúdo Principal -->
<div class="content" id="mainContent">
    <!-- Header -->
    <div class="header shadow-sm px-4 py-3 rounded bg-white d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-outline-dark d-md-none" id="mobileToggle">
                <i class="bi bi-list"></i>
            </button>
            <h4 class="m-0 text-primary">
                <i class="bi bi-diagram-3-fill me-2"></i>Ambiente Administrativo
            </h4>
        </div>
        <div class="d-flex align-items-center gap-4">
            <div class="text-end d-none d-md-block">
                <div class="fw-semibold">Bem-vindo, <strong><?php echo $_SESSION["user_name"]; ?></strong></div>
                <div id="clock" class="text-muted small fw-medium"></div>
            </div>
            <i class="bi bi-person-circle fs-3 text-secondary d-none d-md-block"></i>
        </div>
    </div>
</div>

<!-- Script para Data e Hora -->
<script>
    function updateClock() {
        const clock = document.getElementById('clock');
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        const time = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const date = now.toLocaleDateString('pt-BR', options);
        clock.innerHTML = `${date} - ${time}`;
    }

    setInterval(updateClock, 1000);
    updateClock();
</script>

<!-- Script de Colapso -->
<script>
    const collapseBtn = document.getElementById('collapseBtn');
    const collapseIcon = document.getElementById('collapseIcon');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('mainContent');

    collapseBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');

        if (sidebar.classList.contains('collapsed')) {
            collapseIcon.classList.remove('bi-chevron-left');
            collapseIcon.classList.add('bi-chevron-right');
            collapseBtn.style.left = '70px';
        } else {
            collapseIcon.classList.remove('bi-chevron-right');
            collapseIcon.classList.add('bi-chevron-left');
            collapseBtn.style.left = '250px';
        }
    });
</script>

</body>
</html>
