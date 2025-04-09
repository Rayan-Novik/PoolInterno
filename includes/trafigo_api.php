<?php
include 'funcoes_rede.php';

$data = obterTrafegoRede();

echo json_encode([
    'LAN' => $data['Ethernet'] ?? ['download' => 0, 'upload' => 0],
    'WLAN' => $data['Wi-Fi'] ?? ['download' => 0, 'upload' => 0]
]);
