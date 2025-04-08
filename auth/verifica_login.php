<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Se o usuário não estiver logado, redireciona para a tela de login
    header("Location: /sistema_empresa/auth/login.php");
    exit;
}
