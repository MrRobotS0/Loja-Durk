<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo "Usuário não logado.";
    exit;
}

$userId = $_SESSION['usuario_id'];
$produtoId = intval($_POST['produto_id']);
$tamanhoId = null; 

$stmt = $conn->prepare("SELECT id FROM carrinhos WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
$carrinho = $res->fetch_assoc();

if (!$carrinho) {
    $stmt = $conn->prepare("INSERT INTO carrinhos (user_id, data_criacao) VALUES (?, NOW())");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $carrinhoId = $conn->insert_id;
} else {
    $carrinhoId = $carrinho['id'];
}

$stmt = $conn->prepare("SELECT id, quantidade FROM itens_carrinho WHERE carrinho_id = ? AND produto_id = ?");
$stmt->bind_param("ii", $carrinhoId, $produtoId);
$stmt->execute();
$result = $stmt->get_result();

if ($item = $result->fetch_assoc()) {
    $novaQtd = $item['quantidade'] + 1;
    $stmt = $conn->prepare("UPDATE itens_carrinho SET quantidade = ? WHERE id = ?");
    $stmt->bind_param("ii", $novaQtd, $item['id']);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("INSERT INTO itens_carrinho (carrinho_id, produto_id, quantidade) VALUES (?, ?, 1)");
    $stmt->bind_param("ii", $carrinhoId, $produtoId);
    $stmt->execute();
}

echo "adicionado";
?>
