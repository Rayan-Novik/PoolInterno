<?php
function obterTaxaRede($interface) {
    $saida = shell_exec("netstat -e -n | findstr \"$interface\"");
    preg_match_all('/\d+/', $saida, $valores);
    return [
        'upload' => rand(10, 300),    // Simulado
        'download' => rand(10, 300)   // Simulado
    ];
}

header('Content-Type: application/json');
echo json_encode([
    'lan' => obterTaxaRede('Ethernet'),
    'wlan' => obterTaxaRede('Wi-Fi')
]);
