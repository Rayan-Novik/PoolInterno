<?php
ob_start(); // Adicionado aqui para permitir redirecionamento com header()

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit;
}

$page = $_GET['page'] ?? 'dashboard'; // Página padrão é o dashboard
$viewPath = __DIR__ . "/views/{$page}.php"; // Caminho do conteúdo
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
            position: absolute;
            top: 15px;
            right: -20px;
            background-color: #212529;
            border: none;
            color: #fff;
            border-radius: 5px;
            padding: 8px;
            cursor: pointer;
            z-index: 1050;
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

            .sidebar.collapsed {
                width: 0 !important;
            }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <strong class="fs-5">RSC</strong>
    </div>
    <a href="?page=dashboard"><i class="bi bi-house-door-fill"></i><span>Início</span></a>
    <a href="?page=pagamentos"><i class="bi bi-credit-card-2-front-fill"></i><span>Pagamentos</span></a>
    <a href="?page=acessos"><i class="bi bi-key-fill"></i><span>Acessos</span></a>
    <a href="?page=dominios"><i class="bi bi-globe2"></i><span>Domínios</span></a>
    <a href="?page=ramais"><i class="bi bi-telephone-fill"></i><span>Ramais</span></a>
    <a href="?page=maquinas"><i class="bi bi-pc-display-horizontal"></i><span>Máquinas</span></a>
    <a href="?page=acessosweb"><i class="bi bi-shield-lock-fill"></i><span>Acessos Web</span></a>
    <a href="?page=trafegorede"><i class="bi bi-activity"></i><span>Tráfego</span></a>
    <a href="auth/logout.php" class="text-danger"><i class="bi bi-box-arrow-right"></i><span>Sair</span></a>

    <!-- Botão de Colapso Dentro do Sidebar -->
    <button class="toggle-btn" id="collapseBtn"><i class="bi bi-chevron-left" id="collapseIcon"></i></button>
</div>

<!-- Conteúdo Principal -->
<div class="content" id="mainContent">
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
                <div class="fw-semibold">Bem-vindo, <strong><?php echo $_SESSION["user_name"] ?? 'Usuário'; ?></strong></div>
                <div id="clock" class="text-muted small fw-medium"></div>
            </div>
            <i class="bi bi-person-circle fs-3 text-secondary d-none d-md-block"></i>
        </div>
    </div>

    <div class="mt-4">
        <?php
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<div class='alert alert-warning'><i class='bi bi-exclamation-triangle-fill me-2'></i> Página não encontrada.</div>";
        }
        ?>
    </div>
</div>

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

    const collapseBtn = document.getElementById('collapseBtn');
    const collapseIcon = document.getElementById('collapseIcon');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('mainContent');
    const mobileToggle = document.getElementById('mobileToggle');

    // Colapsa o sidebar no modo desktop
    collapseBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');

        if (sidebar.classList.contains('collapsed')) {
            collapseIcon.classList.replace('bi-chevron-left', 'bi-chevron-right');
        } else {
            collapseIcon.classList.replace('bi-chevron-right', 'bi-chevron-left');
        }
    });

    // Colapsa/expande o sidebar no modo mobile
    mobileToggle.addEventListener('click', () => {
        sidebar.classList.toggle('show');
    });
</script>

</body>
</html>
