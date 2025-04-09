<?php
include __DIR__ . '/../../includes/funcoes_rede.php';
header('Content-Type: application/json');

echo json_encode(listarMaquinasRede());
