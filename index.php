<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit;
}

$page = $_GET['page'] ?? 'dashboard';
$viewPath = __DIR__ . "/views/{$page}.php";
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Painel - Sistema Web</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --transition-speed: 0.3s;
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(160deg, #212529, #343a40);
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            transition: width var(--transition-speed), left var(--transition-speed);
            z-index: 1040;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            padding: 18px 20px;
            font-weight: bold;
            font-size: 1.3rem;
            border-bottom: 1px solid #495057;
            text-align: center;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #adb5bd;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            transition: background var(--transition-speed), color 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #495057;
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

        /* CONTEÚDO */
        .content {
            margin-left: var(--sidebar-width);
            padding: 0;
            transition: margin-left var(--transition-speed);
        }

        .content.collapsed {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* BOTÃO DE COLAPSAR */
        .toggle-btn {
            position: absolute;
            top: 15px;
            right: -18px;
            background-color: #343a40;
            border: none;
            color: #fff;
            border-radius: 50%;
            padding: 6px 9px;
            cursor: pointer;
            z-index: 1050;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 768px) {
            .toggle-btn {
                display: none;
            }
        }

        /* HEADER MODERNO */
        .header {
            background: linear-gradient(145deg, #212529, #343a40);
            padding: 15px 25px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
            color: #fff;
        }

        .notification-icon {
            position: relative;
            cursor: pointer;
            font-size: 1.4rem;
            color: #fff;
        }

        .notification-popup {
            position: absolute;
            top: 120%;
            right: 0;
            width: 280px;
            background-color: #fff;
            color: #212529;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            padding: 15px;
            display: none;
            z-index: 2000;
        }

        .notification-popup.show {
            display: block;
        }

        .notification-popup h6 {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .notification-popup ul {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }

        .notification-popup li {
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
            font-size: 0.95rem;
        }

        .notification-popup li:last-child {
            border-bottom: none;
        }

        /* RESPONSIVO */
        @media (max-width: 768px) {
            .sidebar {
                left: -100%;
                width: var(--sidebar-width);
            }

            .sidebar.show {
                left: 0;
            }

            .content,
            .content.collapsed {
                margin-left: 0 !important;
            }

            .sidebar.collapsed {
                width: var(--sidebar-width);
            }

            .header {
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">RSC</div>
    <a href="?page=dashboard" class="<?= $page === 'dashboard' ? 'active' : '' ?>"><i class="bi bi-house-door-fill"></i><span>Início</span></a>
    <a href="?page=pagamentos" class="<?= $page === 'pagamentos' ? 'active' : '' ?>"><i class="bi bi-credit-card-2-front-fill"></i><span>Pagamentos</span></a>
    <a href="?page=acessos" class="<?= $page === 'acessos' ? 'active' : '' ?>"><i class="bi bi-key-fill"></i><span>Acessos</span></a>
    <a href="?page=dominios" class="<?= $page === 'dominios' ? 'active' : '' ?>"><i class="bi bi-globe2"></i><span>Domínios</span></a>
    <a href="?page=ramais" class="<?= $page === 'ramais' ? 'active' : '' ?>"><i class="bi bi-telephone-fill"></i><span>Ramais</span></a>
    <a href="?page=maquinas" class="<?= $page === 'maquinas' ? 'active' : '' ?>"><i class="bi bi-pc-display-horizontal"></i><span>Máquinas</span></a>
    <a href="?page=acessosweb" class="<?= $page === 'acessosweb' ? 'active' : '' ?>"><i class="bi bi-shield-lock-fill"></i><span>Acessos Web</span></a>
    <a href="?page=trafegorede" class="<?= $page === 'trafegorede' ? 'active' : '' ?>"><i class="bi bi-activity"></i><span>Tráfego</span></a>
    <a href="auth/logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i><span>Sair</span></a>

    <button class="toggle-btn" id="collapseBtn" aria-label="Alternar menu lateral">
        <i class="bi bi-chevron-left" id="collapseIcon"></i>
    </button>
</div>

<!-- CONTEÚDO -->
<div class="content" id="mainContent">
    <!-- HEADER -->
    <div class="header">
        <button class="btn btn-outline-light d-md-none" id="mobileToggle" aria-label="Abrir menu">
            <i class="bi bi-list"></i>
        </button>

        <div class="d-flex align-items-center gap-3 position-relative">
            <i class="bi bi-bell-fill notification-icon" id="notifIcon"></i>
            <div class="notification-popup" id="notifPopup">
                <h6><i class="bi bi-exclamation-circle text-warning me-1"></i> Pendências</h6>
                <ul>
                    <li>Pagamento do servidor vence amanhã</li>
                    <li>Domínio expira em 3 dias</li>
                    <li>2 novas solicitações de acesso</li>
                </ul>
            </div>

            <div class="text-end d-none d-md-block">
                <div class="fw-semibold">Bem-vindo, <strong><?= $_SESSION["user_name"] ?? 'Usuário'; ?></strong></div>
                <div id="clock" class="text-light small fw-medium"></div>
            </div>

            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION["user_name"] ?? 'U') ?>&background=0D6EFD&color=fff" class="rounded-circle" width="40" height="40" alt="Avatar do usuário">
        </div>
    </div>

    <div class="mt-4 p-4">
        <?php
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<div class='alert alert-warning'><i class='bi bi-exclamation-triangle-fill me-2'></i> Página não encontrada.</div>";
        }
        ?>
    </div>
</div>

<!-- SCRIPT -->
<script>
    function updateClock() {
        const clock = document.getElementById('clock');
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const time = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        clock.innerHTML = `${now.toLocaleDateString('pt-BR', options)} - ${time}`;
    }

    setInterval(updateClock, 1000);
    updateClock();

    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('mainContent');
    const collapseBtn = document.getElementById('collapseBtn');
    const collapseIcon = document.getElementById('collapseIcon');
    const mobileToggle = document.getElementById('mobileToggle');

    collapseBtn?.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
        collapseIcon.classList.toggle('bi-chevron-left');
        collapseIcon.classList.toggle('bi-chevron-right');
    });

    mobileToggle?.addEventListener('click', () => {
        sidebar.classList.toggle('show');
    });

    document.addEventListener('click', (event) => {
        if (window.innerWidth <= 768 && !sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
            sidebar.classList.remove('show');
        }
    });

    const notifIcon = document.getElementById('notifIcon');
    const notifPopup = document.getElementById('notifPopup');

    notifIcon?.addEventListener('click', () => {
        notifPopup.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
        if (!notifPopup.contains(e.target) && !notifIcon.contains(e.target)) {
            notifPopup.classList.remove('show');
        }
    });
</script>

</body>
</html>
