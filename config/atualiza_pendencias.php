<?php
include 'config.php';

// Buscar pagamentos vencidos e ativos
$sql = "SELECT * FROM pagamentos WHERE vencimento < CURDATE() AND ativo = 1";
$stmt = $pdo->query($sql);
$pendencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($pendencias as $p) {
    $novo_vencimento = date('Y-m-d', strtotime($p['vencimento'] . ' +1 month'));

    // Criar nova parcela com novo vencimento
    $sql = "INSERT INTO pagamentos (empresa, descricao, valor, vencimento, ativo) 
            VALUES (:empresa, :descricao, :valor, :vencimento, 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':empresa' => $p['empresa'],
        ':descricao' => $p['descricao'],
        ':valor' => $p['valor'],
        ':vencimento' => $novo_vencimento
    ]);
}

echo "Pendências atualizadas!";
