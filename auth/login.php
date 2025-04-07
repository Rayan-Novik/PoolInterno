<?php
include '../config/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"]);
    $senha = trim($_POST["senha"]);

    $sql = "SELECT * FROM usuarios WHERE nome = :nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':nome' => $nome]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o usuário foi encontrado e a senha é válida
    if ($usuario && isset($usuario['senha_hash']) && password_verify($senha, $usuario['senha_hash'])) {
        $_SESSION["user_id"] = $usuario["id"];
        $_SESSION["user_name"] = $usuario["nome"];
        $_SESSION["user_role"] = $usuario["setor"];
        header("Location: ../views/dashboard.php");
        exit;
    } else {
        $erro = "Nome de usuário ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            max-width: 400px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center mb-4">Login</h2>
        <?php if (isset($erro)) : ?>
            <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome de Usuário</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
        <div class="text-center mt-3">
            <a href="register.php">Solicitar Acesso</a>
        </div>
    </div>
</body>
</html>
