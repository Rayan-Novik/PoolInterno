<?php
$host = "localhost";
$dbname = "sistema_web";
$user = "root"; 
$pass = "pool2025";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// ✅ Buscar pagamentos pendentes
function getPagamentosPendentes($pdo) {
    $sql = "SELECT * FROM pagamentos WHERE status = 'pendente' ORDER BY data_vencimento ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ✅ Buscar um pagamento pelo ID
function getPagamentoById($pdo, $id) {
    $sql = "SELECT * FROM pagamentos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ✅ Buscar acessos AnyDesk
function getAcessosAnyDesk($pdo) {
    $sql = "SELECT * FROM acessos_anydesk ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ✅ Buscar logs de e-mails
function getLogsEmails($pdo) {
    $sql = "SELECT * FROM logs_emails ORDER BY data_envio DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ✅ Buscar usuários cadastrados
function getUsuarios($pdo) {
    $sql = "SELECT * FROM usuarios ORDER BY nome ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ✅ Criar usuário com criptografia de senha
function criarUsuario($pdo, $nome, $email, $senha, $cargo) {
    try {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT); // Criptografar a senha
        $sql = "INSERT INTO usuarios (nome, email, senha, cargo) VALUES (:nome, :email, :senha, :cargo)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senha_hash,
            ':cargo' => $cargo
        ]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// ✅ Editar usuário (sem alterar a senha, a menos que fornecida)
function editarUsuario($pdo, $id, $nome, $email, $cargo, $nova_senha = null) {
    try {
        if ($nova_senha) {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT); // Criptografar a nova senha
            $sql = "UPDATE usuarios SET nome = :nome, email = :email, cargo = :cargo, senha = :senha WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':nome' => $nome,
                ':email' => $email,
                ':cargo' => $cargo,
                ':senha' => $senha_hash
            ]);
        } else {
            $sql = "UPDATE usuarios SET nome = :nome, email = :email, cargo = :cargo WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':nome' => $nome,
                ':email' => $email,
                ':cargo' => $cargo
            ]);
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// ✅ Remover usuário
function removerUsuario($pdo, $id) {
    try {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// ✅ Criar pagamento
function criarPagamento($pdo, $empresa, $descricao, $valor, $data_vencimento) {
    $sql = "INSERT INTO pagamentos (empresa, descricao, valor, data_vencimento, status) VALUES (?, ?, ?, ?, 'pendente')";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$empresa, $descricao, $valor, $data_vencimento]);
}

// ✅ Editar pagamento (verifica se existe antes de editar)
function editarPagamento($pdo, $id, $empresa, $descricao, $valor, $data_vencimento) {
    if (getPagamentoById($pdo, $id)) {
        $sql = "UPDATE pagamentos SET empresa=?, descricao=?, valor=?, data_vencimento=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$empresa, $descricao, $valor, $data_vencimento, $id]);
    }
    return false;
}

// ✅ Remover pagamento (verifica se existe antes de remover)
function removerPagamento($pdo, $id) {
    if (getPagamentoById($pdo, $id)) {
        $sql = "DELETE FROM pagamentos WHERE id=?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
    return false;
}

// ✅ Confirmar pagamento (atualiza status para "pago" se existir)
function confirmarPagamento($pdo, $id) {
    if (getPagamentoById($pdo, $id)) {
        $sql = "UPDATE pagamentos SET status = 'pago' WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
    return false;
}

// ✅ Atualizar vencimentos automaticamente todo mês (apenas para pendentes)
function atualizarVencimentos($pdo) {
    $sql = "UPDATE pagamentos SET data_vencimento = DATE_ADD(data_vencimento, INTERVAL 1 MONTH) WHERE status = 'pendente'";
    return $pdo->exec($sql);
}


?>
