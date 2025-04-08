<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include $_SERVER['DOCUMENT_ROOT'] . '/var/www/html/config/config.php';

if (!isset($_SESSION["user_role"]) || !in_array($_SESSION["user_role"], ["admin", "ti"])) {
    header("Location: index.php");
    exit;
}

// Inserir domínio
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["adicionar_dominio"])) {
    $nome = $_POST["nome"];
    $valor = $_POST["valor"];
    $vencimento = $_POST["vencimento"];
    $descricao = $_POST["descricao"];

    $stmt = $pdo->prepare("INSERT INTO dominios (nome, valor, vencimento, descricao) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$nome, $valor, $vencimento, $descricao])) {
        header("Location: index.php?page=dominios&sucesso=adicionado");
        exit;
    } else {
        header("Location: index.php?page=dominios&erro=nao_adicionado");
        exit;
    }
}

// Editar domínio
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["editar_dominio"])) {
    $id = $_POST["id"];
    $nome = $_POST["nome"];
    $valor = $_POST["valor"];
    $vencimento = $_POST["vencimento"];
    $descricao = $_POST["descricao"];

    $stmt = $pdo->prepare("UPDATE dominios SET nome = ?, valor = ?, vencimento = ?, descricao = ? WHERE id = ?");
    if ($stmt->execute([$nome, $valor, $vencimento, $descricao, $id])) {
        header("Location: index.php?page=dominios&sucesso=editado");
        exit;
    } else {
        header("Location: index.php?page=dominios&erro=nao_editado");
        exit;
    }
}

// Remover domínio
if (isset($_GET["remover"])) {
    $id = $_GET["remover"];
    $stmt = $pdo->prepare("DELETE FROM dominios WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: index.php?page=dominios&sucesso=removido");
        exit;
    } else {
        header("Location: index.php?page=dominios&erro=nao_removido");
        exit;
    }
}

// Buscar dados
$result = $pdo->query("SELECT * FROM dominios ORDER BY vencimento ASC");

include 'header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="container mt-4">
    <h2><i class="bi bi-globe2"></i> Gerenciador de Domínios</h2>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success">
            <?= ($_GET['sucesso'] == 'adicionado') ? "<i class='bi bi-check-circle-fill'></i> Domínio adicionado com sucesso!" : "" ?>
            <?= ($_GET['sucesso'] == 'removido') ? "<i class='bi bi-check-circle-fill'></i> Domínio removido com sucesso!" : "" ?>
            <?= ($_GET['sucesso'] == 'editado') ? "<i class='bi bi-check-circle-fill'></i> Domínio editado com sucesso!" : "" ?>
        </div>
    <?php elseif (isset($_GET['erro'])): ?>
        <div class="alert alert-danger">
            <?= ($_GET['erro'] == 'nao_adicionado') ? "<i class='bi bi-exclamation-circle-fill'></i> Erro ao adicionar domínio!" : "" ?>
            <?= ($_GET['erro'] == 'nao_removido') ? "<i class='bi bi-exclamation-circle-fill'></i> Erro ao remover domínio!" : "" ?>
            <?= ($_GET['erro'] == 'nao_editado') ? "<i class='bi bi-exclamation-circle-fill'></i> Erro ao editar domínio!" : "" ?>
        </div>
    <?php endif; ?>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAdicionarDominio">
        <i class="bi bi-plus-circle"></i> Adicionar Domínio
    </button>

    <div class="card">
        <div class="card-header bg-dark text-white">
            <i class="bi bi-list-ul"></i> Lista de Domínios
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Domínio</th>
                        <th>Valor (R$)</th>
                        <th>Vencimento</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row["nome"]) ?></td>
                            <td>R$ <?= number_format($row["valor"], 2, ',', '.') ?></td>
                            <td><?= date('d/m/Y', strtotime($row["vencimento"])) ?></td>
                            <td><?= htmlspecialchars($row["descricao"]) ?></td>
                            <td>
                                <button 
                                    class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditarDominio"
                                    data-id="<?= $row["id"] ?>"
                                    data-nome="<?= htmlspecialchars($row["nome"], ENT_QUOTES) ?>"
                                    data-valor="<?= $row["valor"] ?>"
                                    data-vencimento="<?= $row["vencimento"] ?>"
                                    data-descricao="<?= htmlspecialchars($row["descricao"], ENT_QUOTES) ?>"
                                >
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                                <a href="index.php?page=dominios&remover=<?= $row["id"]; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Deseja remover este domínio?')">
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

<!-- Modal Adicionar -->
<div class="modal fade" id="modalAdicionarDominio" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Novo Domínio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Domínio</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Valor (R$)</label>
                    <input type="number" step="0.01" name="valor" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Data de Vencimento</label>
                    <input type="date" name="vencimento" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button name="adicionar_dominio" class="btn btn-success"><i class="bi bi-check-lg"></i> Salvar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditarDominio" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <input type="hidden" name="id" id="edit-id">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Domínio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Domínio</label>
                    <input type="text" name="nome" id="edit-nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Valor (R$)</label>
                    <input type="number" step="0.01" name="valor" id="edit-valor" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Data de Vencimento</label>
                    <input type="date" name="vencimento" id="edit-vencimento" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" id="edit-descricao" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button name="editar_dominio" class="btn btn-success"><i class="bi bi-check-lg"></i> Atualizar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modalEditar = document.getElementById('modalEditarDominio');
    modalEditar.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit-id').value = button.getAttribute('data-id');
        document.getElementById('edit-nome').value = button.getAttribute('data-nome');
        document.getElementById('edit-valor').value = button.getAttribute('data-valor');
        document.getElementById('edit-vencimento').value = button.getAttribute('data-vencimento');
        document.getElementById('edit-descricao').value = button.getAttribute('data-descricao');
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>
