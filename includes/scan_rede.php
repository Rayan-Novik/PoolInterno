<?php
include 'funcoes_rede.php';
header('Content-Type: application/json');

$acao = $_GET['acao'] ?? '';

switch ($acao) {
    case 'ip':
        echo json_encode(['ip' => buscarIpPublico()]);
        break;

    case 'maquinas':
        echo json_encode(listarMaquinasRede());
        break;

    default:
        echo json_encode(['erro' => 'Ação inválida']);
}
