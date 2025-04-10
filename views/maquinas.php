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

// Processa as ações de INSERT, UPDATE e DELETE

// Adicionar
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["adicionar_anydesk"])) {
    $stmt = $pdo->prepare("INSERT INTO anydesks 
        (nome_pc, processador, memoria_ram, memoria_rom, ip, usuario, senha, anydesk_id, loja, setor, categoria, observacao)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $executado = $stmt->execute([
        $_POST["nome_pc"],
        $_POST["processador"],
        $_POST["memoria_ram"],
        $_POST["memoria_rom"],
        $_POST["ip"],
        $_POST["usuario"],
        $_POST["senha"],
        $_POST["anydesk_id"],
        $_POST["loja"],
        $_POST["setor"],
        $_POST["categoria"],
        $_POST["observacao"]
    ]);

    header("Location: index.php?page=maquinas&" . ($executado ? "sucesso=adicionado" : "erro=nao_adicionado"));
    exit;
}

// Editar
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["editar_anydesk"])) {
    $stmt = $pdo->prepare("UPDATE anydesks SET 
        nome_pc=?, processador=?, memoria_ram=?, memoria_rom=?, ip=?, usuario=?, senha=?, anydesk_id=?, loja=?, setor=?, categoria=?, observacao=? 
        WHERE id=?");

    $executado = $stmt->execute([
        $_POST["nome_pc"],
        $_POST["processador"],
        $_POST["memoria_ram"],
        $_POST["memoria_rom"],
        $_POST["ip"],
        $_POST["usuario"],
        $_POST["senha"],
        $_POST["anydesk_id"],
        $_POST["loja"],
        $_POST["setor"],
        $_POST["categoria"],
        $_POST["observacao"],
        $_POST["id"]
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

// Filtragem por categoria: Se um filtro for passado via GET, usa-o; senão, exibe todos agrupados
$filterCategoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

if ($filterCategoria && in_array($filterCategoria, ['Máquinas', 'Servidores', 'Impressoras', 'DVRs'])) {
    $stmt = $pdo->prepare("SELECT * FROM anydesks WHERE categoria = ? ORDER BY loja ASC");
    $stmt->execute([$filterCategoria]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $agrupados = false;  // indica que não haverá agrupamento por categoria
} else {
    $stmt = $pdo->query("SELECT * FROM anydesks ORDER BY categoria, loja ASC");
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Agrupa os resultados por categoria
    $agrupados = [];
    foreach ($resultados as $row) {
        $agrupados[$row['categoria']][] = $row;
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<div class="container mt-4">
    <h2 class="mb-4 fw-bold"></i>MAQUINAS</h2>

    <!-- Filtro por Categoria -->
    <form method="get" action="index.php" class="mb-3">
        <!-- Mantém a página 'maquinas' -->
        <input type="hidden" name="page" value="maquinas">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="categoria" class="col-form-label">Filtrar por Categoria:</label>
            </div>
            <div class="col-auto">
                <select id="categoria" name="categoria" class="form-control">
                    <option value="" <?= ($filterCategoria === '') ? 'selected' : '' ?>>Todos</option>
                    <option value="Máquinas" <?= ($filterCategoria === 'Máquinas') ? 'selected' : '' ?>>Máquinas</option>
                    <option value="Servidores" <?= ($filterCategoria === 'Servidores') ? 'selected' : '' ?>>Servidores
                    </option>
                    <option value="Impressoras" <?= ($filterCategoria === 'Impressoras') ? 'selected' : '' ?>>Impressoras
                    </option>
                    <option value="DVRs" <?= ($filterCategoria === 'DVRs') ? 'selected' : '' ?>>DVRs</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Mensagens -->
    <?php if (isset($_GET['sucesso']) || isset($_GET['erro'])): ?>
        <div class="alert <?= isset($_GET['sucesso']) ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show"
            role="alert">
            <?= isset($_GET['sucesso']) ? 'Operação realizada com sucesso!' : 'Ocorreu um erro na operação.' ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Botão de Adicionar -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalAdicionar">
        <i class="bi bi-plus-circle"></i> Adicionar Máquina
    </button>

    <!-- Exibição dos Registros -->
    <?php if ($agrupados === false): ?>
        <!-- Exibe os registros filtrados (única categoria) -->
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
                        <th>Categoria</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['loja']) ?></td>
                            <td><?= htmlspecialchars($row['setor']) ?></td>
                            <td><?= htmlspecialchars($row['nome_pc']) ?></td>
                            <td><?= htmlspecialchars($row['ip']) ?></td>
                            <td><?= htmlspecialchars($row['usuario']) ?></td>
                            <td><?= htmlspecialchars($row['anydesk_id']) ?></td>
                            <td><?= htmlspecialchars($row['categoria']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalEditar<?= $row['id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="index.php?page=maquinas&remover=<?= $row['id'] ?>"
                                    onclick="return confirm('Deseja realmente remover?')" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Modal de Edição (para cada linha) -->
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
                                                <input type="text" class="form-control" name="loja" value="<?= $row['loja'] ?>"
                                                    required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Setor</label>
                                                <input type="text" class="form-control" name="setor"
                                                    value="<?= $row['setor'] ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Nome do PC</label>
                                                <input type="text" class="form-control" name="nome_pc"
                                                    value="<?= $row['nome_pc'] ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>IP</label>
                                                <input type="text" class="form-control" name="ip" value="<?= $row['ip'] ?>"
                                                    required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Usuário</label>
                                                <input type="text" class="form-control" name="usuario"
                                                    value="<?= $row['usuario'] ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label>Senha</label>
                                                <input type="text" class="form-control" name="senha"
                                                    value="<?= $row['senha'] ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label>ID do Anydesk</label>
                                                <input type="text" class="form-control" name="anydesk_id"
                                                    value="<?= $row['anydesk_id'] ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Processador</label>
                                                <input type="text" class="form-control" name="processador"
                                                    value="<?= $row['processador'] ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label>RAM</label>
                                                <input type="text" class="form-control" name="memoria_ram"
                                                    value="<?= $row['memoria_ram'] ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label>ROM</label>
                                                <input type="text" class="form-control" name="memoria_rom"
                                                    value="<?= $row['memoria_rom'] ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label>Categoria</label>
                                                <select class="form-control" name="categoria" required>
                                                    <option value="Máquinas" <?= $row['categoria'] === 'Máquinas' ? 'selected' : '' ?>>Máquinas</option>
                                                    <option value="Servidores" <?= $row['categoria'] === 'Servidores' ? 'selected' : '' ?>>Servidores</option>
                                                    <option value="Impressoras" <?= $row['categoria'] === 'Impressoras' ? 'selected' : '' ?>>Impressoras</option>
                                                    <option value="DVRs" <?= $row['categoria'] === 'DVRs' ? 'selected' : '' ?>>DVRs
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label>Observação</label>
                                                <textarea class="form-control"
                                                    name="observacao"><?= $row['observacao'] ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Salvar</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <!-- Exibe os registros agrupados por categoria -->
        <?php foreach ($agrupados as $categoria => $rows): ?>
            <h3 class="mt-4"><?= htmlspecialchars($categoria) ?></h3>
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
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['loja']) ?></td>
                                <td><?= htmlspecialchars($row['setor']) ?></td>
                                <td><?= htmlspecialchars($row['nome_pc']) ?></td>
                                <td><?= htmlspecialchars($row['ip']) ?></td>
                                <td><?= htmlspecialchars($row['usuario']) ?></td>
                                <td><?= htmlspecialchars($row['anydesk_id']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalEditar<?= $row['id'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="index.php?page=maquinas&remover=<?= $row['id'] ?>"
                                        onclick="return confirm('Deseja realmente remover?')" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Modal de Edição (para cada linha) -->
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
                                                    <input type="text" class="form-control" name="loja" value="<?= $row['loja'] ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Setor</label>
                                                    <input type="text" class="form-control" name="setor"
                                                        value="<?= $row['setor'] ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Nome do PC</label>
                                                    <input type="text" class="form-control" name="nome_pc"
                                                        value="<?= $row['nome_pc'] ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>IP</label>
                                                    <input type="text" class="form-control" name="ip" value="<?= $row['ip'] ?>"
                                                        required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Usuário</label>
                                                    <input type="text" class="form-control" name="usuario"
                                                        value="<?= $row['usuario'] ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Senha</label>
                                                    <input type="text" class="form-control" name="senha"
                                                        value="<?= $row['senha'] ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>ID do Anydesk</label>
                                                    <input type="text" class="form-control" name="anydesk_id"
                                                        value="<?= $row['anydesk_id'] ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Processador</label>
                                                    <input type="text" class="form-control" name="processador"
                                                        value="<?= $row['processador'] ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>RAM</label>
                                                    <input type="text" class="form-control" name="memoria_ram"
                                                        value="<?= $row['memoria_ram'] ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>ROM</label>
                                                    <input type="text" class="form-control" name="memoria_rom"
                                                        value="<?= $row['memoria_rom'] ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Categoria</label>
                                                    <select class="form-control" name="categoria" required>
                                                        <option value="Máquinas" <?= $row['categoria'] === 'Máquinas' ? 'selected' : '' ?>>Máquinas</option>
                                                        <option value="Servidores" <?= $row['categoria'] === 'Servidores' ? 'selected' : '' ?>>Servidores</option>
                                                        <option value="Impressoras" <?= $row['categoria'] === 'Impressoras' ? 'selected' : '' ?>>Impressoras</option>
                                                        <option value="DVRs" <?= $row['categoria'] === 'DVRs' ? 'selected' : '' ?>>DVRs
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label>Observação</label>
                                                    <textarea class="form-control"
                                                        name="observacao"><?= $row['observacao'] ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Salvar</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
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
                    <div class="col-md-6">
                        <label>Categoria</label>
                        <select class="form-control" name="categoria" required>
                            <option value="Máquinas">Máquinas</option>
                            <option value="Servidores">Servidores</option>
                            <option value="Impressoras">Impressoras</option>
                            <option value="DVRs">DVRs</option>
                        </select>
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
        // Inicializa Popovers, se houver
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.forEach(function (popoverTriggerEl) {
            new bootstrap.Popover(popoverTriggerEl);
        });
    });
</script>

<?php include 'footer.php'; ?>