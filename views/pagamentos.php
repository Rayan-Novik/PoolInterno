<?php
include 'header.php';
include __DIR__ . '/../config/config.php';

// Criar pagamento
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["adicionar_pagamento"])) {
    $empresa = $_POST["empresa"];
    $descricao = $_POST["descricao"];
    $valor = $_POST["valor"];
    $data_vencimento = $_POST["data_vencimento"];

    if (criarPagamento($pdo, $empresa, $descricao, $valor, $data_vencimento)) {
        header("Location: index.php?page=pagamentos&sucesso=adicionado");
        exit;
    } else {
        header("Location: index.php?page=pagamentos&erro=nao_adicionado");
        exit;
    }
}

// Remover pagamento
if (isset($_GET["remover"])) {
    $id_pagamento = $_GET["remover"];

    if (removerPagamento($pdo, $id_pagamento)) {
        header("Location: index.php?page=pagamentos&sucesso=removido");
        exit;
    } else {
        header("Location: index.php?page=pagamentos&erro=nao_removido");
        exit;
    }
}

// Confirmar pagamento solicitado
if (isset($_GET["confirmar"])) {
    $id_pagamento = $_GET["confirmar"];

    if (confirmarPagamento($pdo, $id_pagamento)) {
        header("Location: index.php?page=pagamentos&sucesso=confirmado");
        exit;
    } else {
        header("Location: index.php?page=pagamentos&erro=nao_confirmado");
        exit;
    }
}

// Atualizar vencimentos manualmente
if (isset($_GET["atualizar_vencimentos"])) {
    atualizarVencimentos($pdo);
    header("Location: index.php?page=pagamentos&sucesso=vencimentos_atualizados");
    exit;
}

// Buscar pagamentos
$sql = "SELECT * FROM pagamentos ORDER BY data_vencimento ASC";
$result = $pdo->query($sql);
?>

<div class="container mt-4">
    <h2 class="mb-4 fw-bold">CONTROLE DE PAGAMENTOS</h2>

    <!-- Mensagens -->
    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success">
            <?php
            if ($_GET['sucesso'] == 'adicionado') echo "Pagamento adicionado com sucesso!";
            if ($_GET['sucesso'] == 'removido') echo "Pagamento removido com sucesso!";
            if ($_GET['sucesso'] == 'confirmado') echo "Pagamento confirmado!";
            if ($_GET['sucesso'] == 'vencimentos_atualizados') echo "Vencimentos atualizados!";
            ?>
        </div>
    <?php elseif (isset($_GET['erro'])): ?>
        <div class="alert alert-danger">
            <?php
            if ($_GET['erro'] == 'nao_adicionado') echo "Erro ao adicionar pagamento!";
            if ($_GET['erro'] == 'nao_removido') echo "Erro ao remover pagamento!";
            if ($_GET['erro'] == 'nao_confirmado') echo "Erro ao confirmar pagamento!";
            ?>
        </div>
    <?php endif; ?>

    <!-- Botões -->
    <div class="mb-3 d-flex gap-2">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAdicionar" title="Adicionar Pagamento">
            <i class="bi bi-plus-lg"></i>
        </button>
        <a href="?atualizar_vencimentos=1" class="btn btn-primary" title="Atualizar Vencimentos">
            <i class="bi bi-arrow-repeat"></i>
        </a>
    </div>

    <!-- Modal Adicionar -->
    <div class="modal fade" id="modalAdicionar" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Adicionar Pagamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="adicionar_pagamento" value="1">
                        <div class="mb-3">
                            <label class="form-label">Empresa</label>
                            <input type="text" name="empresa" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" name="descricao" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor (R$)</label>
                            <input type="number" name="valor" class="form-control" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data de Vencimento</label>
                            <input type="date" name="data_vencimento" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Adicionar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela responsiva -->
    <div class="row">
        <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row["empresa"]); ?></h5>
                        <p class="card-text mb-1"><strong>Descrição:</strong> <?php echo htmlspecialchars($row["descricao"]); ?></p>
                        <p class="card-text mb-1"><strong>Valor:</strong> R$ <?php echo number_format($row["valor"], 2, ',', '.'); ?></p>
                        <p class="card-text mb-1"><strong>Vencimento:</strong> <?php echo date("d/m/Y", strtotime($row["data_vencimento"])); ?></p>
                        <span class="badge 
                            <?php echo ($row["status"] == 'pendente') ? 'bg-danger' : 
                                         (($row["status"] == 'solicitado') ? 'bg-warning' : 
                                         'bg-success'); ?>">
                            <?php echo ucfirst($row["status"] ?? 'desconhecido'); ?>
                        </span>
                        <div class="mt-3 d-flex gap-2">
                            <a href="index.php?page=pagamentos&remover=<?php echo $row['id']; ?>"
                               class="btn btn-danger btn-sm" title="Remover"
                               onclick="return confirm('Tem certeza que deseja remover este pagamento?')">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php if ($row["status"] == "pendente") { ?>
                                <a href="index.php?page=pagamentos&confirmar=<?php echo $row["id"]; ?>"
                                   class="btn btn-warning btn-sm" title="Confirmar Pagamento">
                                    <i class="bi bi-check-circle"></i>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php include 'footer.php'; ?>
