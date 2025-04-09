<?php

function buscarIpPublico(): string {
    $ip = @file_get_contents("https://api.ipify.org");
    return $ip ?: 'Não disponível';
}

function listarMaquinasRede(): array {
    $baseIp = "1.1.1.";
    $ipsAtivos = [];

    // Cria array com IPs a testar
    $ips = [];
    for ($i = 1; $i <= 254; $i++) {
        $ips[] = $baseIp . $i;
    }

    // Cria múltiplos processos de ping em paralelo (até 30 simultâneos)
    $processos = [];
    foreach ($ips as $ip) {
        $processos[$ip] = popen("ping -n 1 -w 100 $ip", 'r');
        usleep(10000); // evita overload
    }

    // Coleta as respostas
    foreach ($processos as $ip => $proc) {
        $saida = stream_get_contents($proc);
        pclose($proc);
        if (strpos($saida, 'TTL=') !== false) {
            $ipsAtivos[] = $ip;
        }
    }

    return $ipsAtivos;
}