<?php
session_start();
include 'db.php';

$usuario_id = $_SESSION['usuario_id'] ?? null;
$metodo = $_POST['pagamento'] ?? 'pix';

if (!$usuario_id || empty($_POST['selecionados'])) {
    die("Dados incompletos para processar o pedido.");
}

$sql_endereco = "SELECT id FROM enderecos WHERE user_id = $usuario_id LIMIT 1";
$res = mysqli_query($conn, $sql_endereco);
$endereco = mysqli_fetch_assoc($res);
$endereco_id = $endereco['id'] ?? null;

if (!$endereco_id) {
    die("Endereço não encontrado.");
}

$ids = implode(',', array_map('intval', $_POST['selecionados']));
$sql = "SELECT ic.*, p.preco, ic.produto_id, ic.tamanho_id 
        FROM itens_carrinho ic
        JOIN produtos p ON ic.produto_id = p.id
        WHERE ic.id IN ($ids)";
$res = mysqli_query($conn, $sql);

$produtos = [];
$valor_total = 0;

while ($row = mysqli_fetch_assoc($res)) {
    $subtotal = $row['preco'] * $row['quantidade'];
    $valor_total += $subtotal;
    $produtos[] = $row;
}

if (empty($produtos)) {
    die("Nenhum produto encontrado.");
}

$stmt = $conn->prepare("INSERT INTO pedidos (user_id, endereco_id, status, data_pedido, valor_total, metodo_pagamento) VALUES (?, ?, 'Pendente', NOW(), ?, ?)");
if (!$stmt) {
    die("Erro na preparação do pedido: " . $conn->error);
}
$stmt->bind_param("iids", $usuario_id, $endereco_id, $valor_total, $metodo);
if (!$stmt->execute()) {
    die("Erro ao inserir pedido: " . $stmt->error);
}
$pedido_id = $stmt->insert_id;

$stmt_item = $conn->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, tamanho_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?, ?)");
if (!$stmt_item) {
    die("Erro na preparação dos itens: " . $conn->error);
}

foreach ($produtos as $p) {
    $pid = $p['produto_id'];
    $tid = $p['tamanho_id'];
    $qtd = $p['quantidade'];
    $preco = $p['preco'];
    $stmt_item->bind_param("iiiid", $pedido_id, $pid, $tid, $qtd, $preco);
    if (!$stmt_item->execute()) {
        die("Erro ao inserir item do pedido: " . $stmt_item->error);
    }
}

$limpar = "DELETE FROM itens_carrinho WHERE id IN ($ids)";
if (!mysqli_query($conn, $limpar)) {
    die("Erro ao limpar carrinho: " . mysqli_error($conn));
}

header("Location: ../views/pedido_sucesso.php");
exit;
?>
