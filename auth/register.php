<?php
include '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $cargo = trim($_POST["cargo"]);
    $email = trim($_POST["email"]);
    $setor = $_POST["setor"];

    // Inserir solicitação no banco
    $sql = "INSERT INTO solicitacoes (nome, cargo, email, setor) VALUES (:nome, :cargo, :email, :setor)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':cargo' => $cargo,
        ':email' => $email,
        ':setor' => $setor
    ]);

    $mensagem = "Solicitação enviada com sucesso! Aguarde a aprovação do administrador.";
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Acesso</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container {
            max-width: 400px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2 class="text-center">Solicitar Acesso</h2>
        <?php if (isset($mensagem)) : ?>
            <div class="alert alert-success"><?php echo $mensagem; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="mb-3">
                <label for="cargo" class="form-label">Cargo</label>
                <input type="text" class="form-control" id="cargo" name="cargo" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail Corporativo</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="setor" class="form-label">Setor</label>
                <select class="form-select" id="setor" name="setor" required>
                    <option value="ti">TI</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Enviar Solicitação</button>
        </form>
        <div class="text-center mt-3">
            <a href="login.php">Já tem uma conta? Faça login</a>
        </div>
    </div>
</body>
</html>
