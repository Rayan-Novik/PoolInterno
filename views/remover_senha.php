<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Remover senha da tabela
    $sql = "DELETE FROM senhas_sites WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        header("Location: senhasti.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Erro ao excluir a senha.</div>";
    }
}
?>
