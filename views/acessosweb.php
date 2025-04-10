<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/../config/config.php';

if (!isset($_SESSION["user_role"]) || !in_array($_SESSION["user_role"], ["admin", "ti"])) {
    header("Location: index.php");
    exit;
}

// Adicionar acesso
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["adicionar_acesso"])) {
    $stmt = $pdo->prepare("INSERT INTO acessos_sites (nome_site, site, usuario, senha) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$_POST["nome_site"], $_POST["site"], $_POST["usuario"], $_POST["senha"]])) {
        header("Location: index.php?page=acessosweb&sucesso=adicionado");
        exit;
    } else {
        header("Location: index.php?page=acessosweb&erro=nao_adicionado");
        exit;
    }
}

// Editar acesso
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_acesso"])) {
    $stmt = $pdo->prepare("UPDATE acessos_sites SET nome_site = ?, site = ?, usuario = ?, senha = ? WHERE id = ?");
    if ($stmt->execute([$_POST["nome_site"], $_POST["site"], $_POST["usuario"], $_POST["senha"], $_POST["id"]])) {
        header("Location: index.php?page=acessosweb&sucesso=atualizado");
        exit;
    } else {
        header("Location: index.php?page=acessosweb&erro=nao_atualizado");
        exit;
    }
}

// Remover acesso
if (isset($_GET["remover"])) {
    $stmt = $pdo->prepare("DELETE FROM acessos_sites WHERE id = ?");
    if ($stmt->execute([$_GET["remover"]])) {
        header("Location: index.php?page=acessosweb&sucesso=removido");
        exit;
    } else {
        header("Location: index.php?page=acessosweb&erro=nao_removido");
        exit;
    }
}

$result = $pdo->query("SELECT * FROM acessos_sites ORDER BY nome_site ASC");
include 'header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">ACESSOS WEB'S</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAdicionarAcesso">
            <i class="bi bi-plus-lg"></i>
        </button>
    </div>

    <?php if (isset($_GET['sucesso']) || isset($_GET['erro'])): ?>
        <div class="alert <?= isset($_GET['sucesso']) ? 'alert-success' : 'alert-danger' ?>">
            <?php
            $msgs = [
                "adicionado" => "Acesso adicionado com sucesso!",
                "removido" => "Acesso removido com sucesso!",
                "atualizado" => "Acesso atualizado com sucesso!",
                "nao_adicionado" => "Erro ao adicionar o acesso!",
                "nao_removido" => "Erro ao remover o acesso!",
                "nao_atualizado" => "Erro ao atualizar o acesso!"
            ];
            $chave = $_GET['sucesso'] ?? $_GET['erro'];
            echo "<i class='bi bi-info-circle-fill'></i> " . ($msgs[$chave] ?? 'Erro inesperado');
            ?>
        </div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
        <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
            <div class="col d-flex">
                <div class="card shadow-sm flex-fill">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="fw-bold"><i class="bi bi-bookmark"></i> <?= htmlspecialchars($row["nome_site"]) ?></h5>
                            <h6 class="card-title mb-2">
                                <i class="bi bi-globe"></i>
                                <a href="<?= htmlspecialchars($row["site"]) ?>" target="_blank">
                                    <?= htmlspecialchars($row["site"]) ?>
                                </a>
                            </h6>
                            <p class="mb-1"><i class="bi bi-person"></i> <?= htmlspecialchars($row["usuario"]) ?></p>
                            <p class="mb-3"><i class="bi bi-lock"></i> <?= htmlspecialchars($row["senha"]) ?></p>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-primary btn-sm" title="Editar"
                                onclick='preencherFormulario(<?= json_encode($row) ?>)'>
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="index.php?page=acessosweb&remover=<?= $row["id"] ?>" class="btn btn-danger btn-sm" title="Remover"
                               onclick="return confirm('Deseja realmente remover este acesso?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalAdicionarAcesso" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="index.php?page=acessosweb" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalLabel"><i class="bi bi-key-fill"></i> Acesso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="form-id">
                <div class="mb-3">
                    <label for="nome_site" class="form-label"><i class="bi bi-bookmark"></i> Nome do Site</label>
                    <input type="text" class="form-control" name="nome_site" id="form-nome_site" required>
                </div>
                <div class="mb-3">
                    <label for="site" class="form-label"><i class="bi bi-globe"></i> URL do Site</label>
                    <input type="url" class="form-control" name="site" id="form-site" required placeholder="https://exemplo.com">
                </div>
                <div class="mb-3">
                    <label for="usuario" class="form-label"><i class="bi bi-person"></i> Usuário</label>
                    <input type="text" class="form-control" name="usuario" id="form-usuario" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label"><i class="bi bi-lock"></i> Senha</label>
                    <input type="text" class="form-control" name="senha" id="form-senha" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="adicionar_acesso" id="btn-salvar" class="btn btn-success">
                    <i class="bi bi-check-lg"></i>
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function preencherFormulario(data) {
    document.getElementById('form-id').value = data.id;
    document.getElementById('form-nome_site').value = data.nome_site;
    document.getElementById('form-site').value = data.site;
    document.getElementById('form-usuario').value = data.usuario;
    document.getElementById('form-senha').value = data.senha;
    document.querySelector('[name="adicionar_acesso"]').setAttribute('name', 'editar_acesso');
    document.getElementById('btn-salvar').classList.remove('btn-success');
    document.getElementById('btn-salvar').classList.add('btn-primary');
    const modal = new bootstrap.Modal(document.getElementById('modalAdicionarAcesso'));
    modal.show();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>
