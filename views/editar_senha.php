<?php
include $_SERVER['DOCUMENT_ROOT'] . '/var/www/html/config/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Exibir ID para depuração
    echo "ID recebido: " . $id; 

    // Buscar a senha
    $sql = "SELECT * FROM senhas_sites WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $senha = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$senha) {
        echo "<div class='alert alert-danger'>Senha não encontrada.</div>";
        exit();
    }

    // Editar a senha se o formulário for enviado
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_senha'])) {
        $nome_site = $_POST['nome_site'];
        $link = $_POST['link'];
        $usuario = $_POST['usuario'];
        $senha_nova = $_POST['senha'];

        $sql = "UPDATE senhas_sites SET nome_site = :nome_site, link = :link, usuario = :usuario, senha = :senha WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome_site', $nome_site);
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':senha', $senha_nova);
        $stmt->bindParam(':id', $id); // Verifique se o ID está correto

        if ($stmt->execute()) {
            header("Location: senhasti.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Erro ao editar a senha.</div>";
        }
    }
}
?>

<div class="container mt-5">
    <h2>Editar Senha</h2>
    <form method="POST">
        <div class="form-group">
            <label for="nome_site">Nome do Site</label>
            <input type="text" class="form-control" id="nome_site" name="nome_site" value="<?php echo $senha['nome_site']; ?>" required>
        </div>
        <div class="form-group">
            <label for="link">Link</label>
            <input type="text" class="form-control" id="link" name="link" value="<?php echo $senha['link']; ?>" required>
        </div>
        <div class="form-group">
            <label for="usuario">Usuário</label>
            <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo $senha['usuario']; ?>" required>
        </div>
        <div class="form-group">
            <label for="senha">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" value="<?php echo $senha['senha']; ?>" required>
        </div>
        <button type="submit" name="edit_senha" class="btn btn-primary">Salvar Alterações</button>
    </form>
</div>
