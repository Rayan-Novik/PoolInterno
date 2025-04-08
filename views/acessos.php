<?php
include $_SERVER['DOCUMENT_ROOT'] . '/var/www/html/config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["user_role"]) || !in_array($_SESSION["user_role"], ["admin", "ti"])) {
    header("Location: index.php?page=home");
    exit;
}

// Criar usuário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["adicionar_usuario"])) {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);
    $cargo = $_POST["cargo"];
    $setor = $_POST["setor"];
    $token = bin2hex(random_bytes(32)); // Gerar token

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, cargo, setor, token) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$nome, $email, $senha, $cargo, $setor, $token])) {
        header("Location: index.php?page=acessos&sucesso=adicionado");
        exit;
    } else {
        header("Location: index.php?page=acessos&erro=nao_adicionado");
        exit;
    }
}

// Editar usuário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_usuario"])) {
    $id_usuario = $_POST["id"];
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $cargo = $_POST["cargo"];
    $setor = $_POST["setor"];

    $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, cargo = ?, setor = ? WHERE id = ?");
    if ($stmt->execute([$nome, $email, $cargo, $setor, $id_usuario])) {
        header("Location: index.php?page=acessos&sucesso=editado");
        exit;
    } else {
        header("Location: index.php?page=acessos&erro=nao_editado");
        exit;
    }
}

// Remover usuário
if (isset($_GET["remover"])) {
    $id_usuario = $_GET["remover"];
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    if ($stmt->execute([$id_usuario])) {
        header("Location: index.php?page=acessos&sucesso=removido");
        exit;
    } else {
        header("Location: index.php?page=acessos&erro=nao_removido");
        exit;
    }
}

// Buscar usuários
$sql = "SELECT * FROM usuarios ORDER BY nome ASC";
$result = $pdo->query($sql);
?>

<!-- Biblioteca de ícones -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<div class="container mt-4">
    <h2>Gerenciamento de Usuários</h2>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success">
            <?= ($_GET['sucesso'] == 'adicionado') ? "Usuário adicionado com sucesso!" : "" ?>
            <?= ($_GET['sucesso'] == 'editado') ? "Usuário editado com sucesso!" : "" ?>
            <?= ($_GET['sucesso'] == 'removido') ? "Usuário removido com sucesso!" : "" ?>
        </div>
    <?php elseif (isset($_GET['erro'])): ?>
        <div class="alert alert-danger">
            <?= ($_GET['erro'] == 'nao_adicionado') ? "Erro ao adicionar usuário!" : "" ?>
            <?= ($_GET['erro'] == 'nao_editado') ? "Erro ao editar usuário!" : "" ?>
            <?= ($_GET['erro'] == 'nao_removido') ? "Erro ao remover usuário!" : "" ?>
        </div>
    <?php endif; ?>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAdicionar">
        <i class="bi bi-person-plus-fill me-1"></i> Adicionar Usuário
    </button>

    <div class="card">
        <div class="card-header bg-secondary text-white">Lista de Usuários</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Cargo</th>
                        <th>Setor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row["nome"]); ?></td>
                            <td><?= htmlspecialchars($row["email"]); ?></td>
                            <td><?= ucfirst($row["cargo"]); ?></td>
                            <td><?= ucfirst($row["setor"]); ?></td>
                            <td>
                                <button class="btn btn-info btn-sm btnEditar"
                                    data-id="<?= $row["id"]; ?>"
                                    data-nome="<?= htmlspecialchars($row["nome"]); ?>"
                                    data-email="<?= htmlspecialchars($row["email"]); ?>"
                                    data-cargo="<?= $row["cargo"]; ?>"
                                    data-setor="<?= $row["setor"]; ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalEditar">
                                    <i class="bi bi-pencil-square me-1"></i> Editar
                                </button> 
                                <a href="index.php?page=acessos&remover=<?= $row["id"]; ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Tem certeza que deseja remover este usuário?')">
                                    <i class="bi bi-trash me-1"></i> Remover
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Adicionar Usuário -->
<div class="modal fade" id="modalAdicionar" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Adicionar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="adicionar_usuario" value="1">
                <div class="mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>E-mail</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Senha</label>
                    <input type="password" name="senha" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Cargo</label>
                    <input type="text" name="cargo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Setor</label>
                    <input type="text" name="setor" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i> Salvar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Usuário -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="editar_usuario" value="1">
                <input type="hidden" name="id" id="editarId">
                <div class="mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" id="editarNome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>E-mail</label>
                    <input type="email" name="email" id="editarEmail" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Cargo</label>
                    <input type="text" name="cargo" id="editarCargo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Setor</label>
                    <input type="text" name="setor" id="editarSetor" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Atualizar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap e Script para preencher modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.btnEditar').forEach(button => {
    button.addEventListener('click', function () {
        document.getElementById('editarId').value = this.dataset.id;
        document.getElementById('editarNome').value = this.dataset.nome;
        document.getElementById('editarEmail').value = this.dataset.email;
        document.getElementById('editarCargo').value = this.dataset.cargo;
        document.getElementById('editarSetor').value = this.dataset.setor;
    });
});
</script>

<?php include 'footer.php'; ?>
