<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include $_SERVER['DOCUMENT_ROOT'] . '/var/www/html/config/config.php';

if (!isset($_SESSION["user_role"]) || !in_array($_SESSION["user_role"], ["admin", "ti"])) {
    header("Location: index.php");
    exit;
}

// Atualizar ramal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $numero = $_POST['numero'];
    $status = $_POST['status'];

    $update = $pdo->prepare("UPDATE ramais SET numero = ?, status = ? WHERE id = ?");
    $update->execute([$numero, $status, $id]);

    header("Location: index.php?page=ramais");
    exit;
}

// Buscar os 24 ramais
$stmt = $pdo->query("SELECT * FROM ramais ORDER BY id ASC LIMIT 24");
$ramais = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .ramal-card {
        height: 100px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: bold;
        border-radius: 10px;
        cursor: pointer;
        transition: transform 0.2s;
        padding: 6px;
        text-align: center;
    }

    .ramal-card:hover {
        transform: scale(1.03);
    }

    .ramal-porta {
        font-size: 0.8rem;
        font-weight: normal;
        margin-bottom: 2px;
    }

    .ramal-numero {
        font-size: 1.1rem;
        font-weight: bold;
    }

    .ramal-status {
        font-size: 0.75rem;
        margin-top: 6px;
    }
</style>

<div class="container mt-4">
    <h2><i class="bi bi-telephone"></i> Painel de Ramais</h2>
    <div class="row mt-4">
        <?php foreach ($ramais as $index => $ramal): ?>
            <?php
                $bgClass = match($ramal['status']) {
                    'ativo' => 'bg-success text-white',
                    'atencao' => 'bg-warning text-dark',
                    'inativo' => 'bg-danger text-white',
                    default => 'bg-secondary text-white'
                };
                $portaNumero = $index + 1;
            ?>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                <div class="ramal-card <?= $bgClass ?>" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $ramal['id'] ?>">
                    <div class="ramal-porta">Porta <?= $portaNumero ?></div>
                    <div class="ramal-numero"><?= htmlspecialchars($ramal['numero']) ?></div>
                    <div class="ramal-status">Status: <?= ucfirst($ramal['status']) ?></div>
                </div>
            </div>

            <!-- Modal editar ramal -->
            <div class="modal fade" id="modalEditar<?= $ramal['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar Ramal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?= $ramal['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Número do Ramal</label>
                                <input type="text" name="numero" class="form-control" value="<?= htmlspecialchars($ramal['numero']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="ativo" <?= $ramal['status'] == 'ativo' ? 'selected' : '' ?>>Ativo (Verde)</option>
                                    <option value="atencao" <?= $ramal['status'] == 'atencao' ? 'selected' : '' ?>>Atenção (Amarelo)</option>
                                    <option value="inativo" <?= $ramal['status'] == 'inativo' ? 'selected' : '' ?>>Inativo (Vermelho)</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success"><i class="bi bi-check-lg"></i> Salvar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'footer.php'; ?>
