<?php
// Iniciar sessão antes de qualquer outra coisa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include $_SERVER['DOCUMENT_ROOT'] . '/var/www/html/config/config.php';

// Verificar se o usuário tem permissão (admin ou TI)
if (!isset($_SESSION["user_role"]) || !in_array($_SESSION["user_role"], ["admin", "ti"])) {
    header("Location: index.php");
    exit;
}

// Adicionar acesso
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["adicionar_acesso"])) {
    $site = $_POST["site"];
    $usuario = $_POST["usuario"];
    $senha = $_POST["senha"];

    $stmt = $pdo->prepare("INSERT INTO acessos_sites (site, usuario, senha) VALUES (?, ?, ?)");
    if ($stmt->execute([$site, $usuario, $senha])) {
        header("Location: index.php?page=acessosweb&sucesso=adicionado");
        exit;
    } else {
        header("Location: index.php?page=acessosweb&erro=nao_adicionado");
        exit;
    }
}

// Remover acesso
if (isset($_GET["remover"])) {
    $id = $_GET["remover"];
    $stmt = $pdo->prepare("DELETE FROM acessos_sites WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: index.php?page=acessosweb&sucesso=removido");
        exit;
    } else {
        header("Location: index.php?page=acessosweb&erro=nao_removido");
        exit;
    }
}


// Buscar acessos
$result = $pdo->query("SELECT * FROM acessos_sites ORDER BY site ASC");

// Agora podemos incluir o header
include 'header.php';
?>

<!-- Link para Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="container mt-4">
    <h2><i class="bi bi-shield-lock"></i> Gerenciador de Acessos de Sites</h2>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success">
            <?= ($_GET['sucesso'] == 'adicionado') ? "<i class='bi bi-check-circle-fill'></i> Acesso salvo com sucesso!" : "" ?>
            <?= ($_GET['sucesso'] == 'removido') ? "<i class='bi bi-check-circle-fill'></i> Acesso removido com sucesso!" : "" ?>
        </div>
    <?php elseif (isset($_GET['erro'])): ?>
        <div class="alert alert-danger">
            <?= ($_GET['erro'] == 'nao_adicionado') ? "<i class='bi bi-exclamation-circle-fill'></i> Erro ao salvar acesso!" : "" ?>
            <?= ($_GET['erro'] == 'nao_removido') ? "<i class='bi bi-exclamation-circle-fill'></i> Erro ao remover acesso!" : "" ?>
        </div>
    <?php endif; ?>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAdicionarAcesso">
        <i class="bi bi-plus-circle"></i> Adicionar Acesso
    </button>

    <div class="card">
        <div class="card-header bg-dark text-white">
            <i class="bi bi-list"></i> Lista de Acessos
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th><i class="bi bi-globe"></i> Site</th>
                        <th><i class="bi bi-person"></i> Usuário</th>
                        <th><i class="bi bi-lock"></i> Senha</th>
                        <th><i class="bi bi-gear"></i> Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                        <tr>
                            <td>
                                <a href="<?= htmlspecialchars($row["site"]); ?>" target="_blank">
                                    <?= htmlspecialchars($row["site"]); ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($row["usuario"]); ?></td>
                            <td><?= htmlspecialchars($row["senha"]); ?></td>
                            <td>
                                <a href="index.php?page=acessosweb&remover=<?= $row["id"]; ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Deseja realmente remover este acesso?')">
                                    <i class="bi bi-trash"></i> Remover
                                </a>

                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal para adicionar acesso -->
<div class="modal fade" id="modalAdicionarAcesso" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="index.php?page=acessosweb" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalLabel"><i class="bi bi-plus-circle"></i> Novo Acesso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="site" class="form-label"><i class="bi bi-globe"></i> Site</label>
                    <input type="url" class="form-control" name="site" id="site" required
                        placeholder="https://exemplo.com">
                </div>
                <div class="mb-3">
                    <label for="usuario" class="form-label"><i class="bi bi-person"></i> Usuário</label>
                    <input type="text" class="form-control" name="usuario" id="usuario" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label"><i class="bi bi-lock"></i> Senha</label>
                    <input type="text" class="form-control" name="senha" id="senha" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="adicionar_acesso" class="btn btn-success">
                    <i class="bi bi-check-lg"></i> Salvar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>