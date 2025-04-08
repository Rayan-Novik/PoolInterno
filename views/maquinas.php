<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../config/config.php';  

// Verifica se o usuário tem permissão
if (!isset($_SESSION["user_role"]) || !in_array($_SESSION["user_role"], ["admin", "ti"])) {
    header("Location: index.php");
    exit;
}

// Adicionar
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["adicionar_anydesk"])) {
    $stmt = $pdo->prepare("INSERT INTO anydesks 
        (nome_pc, processador, memoria_ram, memoria_rom, ip, usuario, senha, anydesk_id, loja, setor, observacao)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $executado = $stmt->execute([
        $_POST["nome_pc"], $_POST["processador"], $_POST["memoria_ram"], $_POST["memoria_rom"],
        $_POST["ip"], $_POST["usuario"], $_POST["senha"], $_POST["anydesk_id"],
        $_POST["loja"], $_POST["setor"], $_POST["observacao"]
    ]);

    header("Location: index.php?page=maquinas&" . ($executado ? "sucesso=adicionado" : "erro=nao_adicionado"));
    exit;
}

// Editar
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["editar_anydesk"])) {
    $stmt = $pdo->prepare("UPDATE anydesks SET 
        nome_pc=?, processador=?, memoria_ram=?, memoria_rom=?, ip=?, usuario=?, senha=?, anydesk_id=?, loja=?, setor=?, observacao=? 
        WHERE id=?");

    $executado = $stmt->execute([
        $_POST["nome_pc"], $_POST["processador"], $_POST["memoria_ram"], $_POST["memoria_rom"],
        $_POST["ip"], $_POST["usuario"], $_POST["senha"], $_POST["anydesk_id"],
        $_POST["loja"], $_POST["setor"], $_POST["observacao"], $_POST["id"]
    ]);

    header("Location: index.php?page=maquinas&" . ($executado ? "sucesso=editado" : "erro=nao_editado"));
    exit;
}

// Remover
if (isset($_GET["remover"])) {
    $stmt = $pdo->prepare("DELETE FROM anydesks WHERE id = ?");
    $executado = $stmt->execute([$_GET["remover"]]);

    header("Location: index.php?page=maquinas&" . ($executado ? "sucesso=removido" : "erro=nao_removido"));
    exit;
}

// Buscar dados
$result = $pdo->query("SELECT * FROM anydesks ORDER BY loja ASC");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<div class="container mt-4">
    <h2><i class="bi bi-pc-display"></i> Gerenciador de Máquinas</h2>

    <!-- Mensagens -->
    <?php if (isset($_GET['sucesso']) || isset($_GET['erro'])): ?>
        <div class="alert <?= isset($_GET['sucesso']) ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
            <?= isset($_GET['sucesso']) ? 'Operação realizada com sucesso!' : 'Ocorreu um erro na operação.' ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Botão de Adicionar -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAdicionar">
        <i class="bi bi-plus-circle"></i> Adicionar Máquina
    </button>

    <!-- Tabela -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Loja</th>
                    <th>Setor</th>
                    <th>Nome do PC</th>
                    <th>IP</th>
                    <th>Usuário</th>
                    <th>Anydesk ID</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['loja']) ?></td>
                        <td><?= htmlspecialchars($row['setor']) ?></td>
                        <td><?= htmlspecialchars($row['nome_pc']) ?></td>
                        <td><?= htmlspecialchars($row['ip']) ?></td>
                        <td><?= htmlspecialchars($row['usuario']) ?></td>
                        <td><?= htmlspecialchars($row['anydesk_id']) ?></td>
                        <td>
                            <!-- Botões de ação -->
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $row['id'] ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="index.php?page=maquinas&remover=<?= $row['id'] ?>" onclick="return confirm('Deseja realmente remover?')" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Modal de Edição -->
                    <div class="modal fade" id="modalEditar<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <form method="post">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="editar_anydesk" value="1">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Máquina</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body row g-3">
                                        <div class="col-md-6">
                                            <label>Loja</label>
                                            <input type="text" class="form-control" name="loja" value="<?= $row['loja'] ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Setor</label>
                                            <input type="text" class="form-control" name="setor" value="<?= $row['setor'] ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Nome do PC</label>
                                            <input type="text" class="form-control" name="nome_pc" value="<?= $row['nome_pc'] ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label>IP</label>
                                            <input type="text" class="form-control" name="ip" value="<?= $row['ip'] ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Usuário</label>
                                            <input type="text" class="form-control" name="usuario" value="<?= $row['usuario'] ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Senha</label>
                                            <input type="text" class="form-control" name="senha" value="<?= $row['senha'] ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label>ID do Anydesk</label>
                                            <input type="text" class="form-control" name="anydesk_id" value="<?= $row['anydesk_id'] ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Processador</label>
                                            <input type="text" class="form-control" name="processador" value="<?= $row['processador'] ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label>RAM</label>
                                            <input type="text" class="form-control" name="memoria_ram" value="<?= $row['memoria_ram'] ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label>ROM</label>
                                            <input type="text" class="form-control" name="memoria_rom" value="<?= $row['memoria_rom'] ?>">
                                        </div>
                                        <div class="col-12">
                                            <label>Observação</label>
                                            <textarea class="form-control" name="observacao"><?= $row['observacao'] ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Salvar</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Adicionar -->
<div class="modal fade" id="modalAdicionar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post">
            <input type="hidden" name="adicionar_anydesk" value="1">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Máquina</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <!-- Mesmos campos do modal de edição -->
                    <div class="col-md-6">
                        <label>Loja</label>
                        <input type="text" class="form-control" name="loja" required>
                    </div>
                    <div class="col-md-6">
                        <label>Setor</label>
                        <input type="text" class="form-control" name="setor" required>
                    </div>
                    <div class="col-md-6">
                        <label>Nome do PC</label>
                        <input type="text" class="form-control" name="nome_pc" required>
                    </div>
                    <div class="col-md-6">
                        <label>IP</label>
                        <input type="text" class="form-control" name="ip" required>
                    </div>
                    <div class="col-md-6">
                        <label>Usuário</label>
                        <input type="text" class="form-control" name="usuario">
                    </div>
                    <div class="col-md-6">
                        <label>Senha</label>
                        <input type="text" class="form-control" name="senha">
                    </div>
                    <div class="col-md-6">
                        <label>ID do Anydesk</label>
                        <input type="text" class="form-control" name="anydesk_id" required>
                    </div>
                    <div class="col-md-6">
                        <label>Processador</label>
                        <input type="text" class="form-control" name="processador">
                    </div>
                    <div class="col-md-6">
                        <label>RAM</label>
                        <input type="text" class="form-control" name="memoria_ram">
                    </div>
                    <div class="col-md-6">
                        <label>ROM</label>
                        <input type="text" class="form-control" name="memoria_rom">
                    </div>
                    <div class="col-12">
                        <label>Observação</label>
                        <textarea class="form-control" name="observacao"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Adicionar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- JS Bootstrap + Popover -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Popover
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.forEach(function (popoverTriggerEl) {
        new bootstrap.Popover(popoverTriggerEl);
    });

    // Modal edição
    const modalEditar = document.getElementById('modalEditar');
    modalEditar.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const fields = ["id", "nome_pc", "processador", "memoria_ram", "memoria_rom", "ip", "usuario", "senha", "anydesk_id", "loja", "setor", "observacao"];
        fields.forEach(field => {
            const input = document.getElementById(`edit-${field}`);
            if (input) input.value = button.getAttribute(`data-${field}`);
        });
    });
});
</script>

<?php include 'footer.php'; ?>
