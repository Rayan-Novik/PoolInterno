<?php
include 'header.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

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
    <h2>Pagamentos</h2>

    <!-- Exibir mensagens de sucesso ou erro -->
    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success">
            <?php
            if ($_GET['sucesso'] == 'adicionado')
                echo "Pagamento adicionado com sucesso!";
            if ($_GET['sucesso'] == 'removido')
                echo "Pagamento removido com sucesso!";
            if ($_GET['sucesso'] == 'confirmado')
                echo "Pagamento confirmado!";
            if ($_GET['sucesso'] == 'vencimentos_atualizados')
                echo "Vencimentos atualizados!";
            ?>
        </div>
    <?php elseif (isset($_GET['erro'])): ?>
        <div class="alert alert-danger">
            <?php
            if ($_GET['erro'] == 'nao_adicionado')
                echo "Erro ao adicionar pagamento!";
            if ($_GET['erro'] == 'nao_removido')
                echo "Erro ao remover pagamento!";
            if ($_GET['erro'] == 'nao_confirmado')
                echo "Erro ao confirmar pagamento!";
            ?>
        </div>
    <?php endif; ?>

    <!-- Botões -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAdicionar">Adicionar
        Pagamento</button>
    <a href="?atualizar_vencimentos=1" class="btn btn-secondary mb-3">Atualizar Vencimentos</a>

    <!-- Modal para adicionar pagamento -->
    <div class="modal fade" id="modalAdicionar" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Adicionar Pagamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

    <!-- Tabela de pagamentos -->
    <div class="card">
        <div class="card-header bg-secondary text-white">Lista de Pagamentos</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>Descrição</th>
                        <th>Valor (R$)</th>
                        <th>Vencimento</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["empresa"]); ?></td>
                            <td><?php echo htmlspecialchars($row["descricao"]); ?></td>
                            <td>R$ <?php echo number_format($row["valor"], 2, ',', '.'); ?></td>
                            <td><?php echo date("d/m/Y", strtotime($row["data_vencimento"])); ?></td>
                            <td>
                                <span
                                    class="badge <?php echo ($row["status"] == 'pendente') ? 'bg-danger' : (($row["status"] == 'solicitado') ? 'bg-warning' : 'bg-success'); ?>">
                                    <?php echo ucfirst($row["status"] ?? 'desconhecido'); ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?page=pagamentos&remover=<?php echo $row['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Tem certeza que deseja remover este pagamento?')">
                                    Remover
                                </a>
                                <?php if ($row["status"] == "pendente") { ?>
                                    <a href="index.php?page=pagamentos&confirmar=<?php echo $row["id"]; ?>"
                                        class="btn btn-warning btn-sm">Confirmar</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>