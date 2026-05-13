<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo "Usuário não logado.";
    exit;
}

$userId = (int) $_SESSION['usuario_id'];
$produtoId = isset($_POST['produto_id']) ? (int) $_POST['produto_id'] : 0;
$tamanhoId = !empty($_POST['tamanho_id']) ? (int) $_POST['tamanho_id'] : null;

if ($produtoId <= 0) {
    http_response_code(400);
    echo "Produto inválido.";
    exit;
}

$stmt = $conn->prepare("SELECT id FROM carrinhos WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
$carrinho = $res->fetch_assoc();
$stmt->close();

if (!$carrinho) {
    $stmt = $conn->prepare("INSERT INTO carrinhos (user_id, data_criacao) VALUES (?, NOW())");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $carrinhoId = $conn->insert_id;
    $stmt->close();
} else {
    $carrinhoId = (int) $carrinho['id'];
}

if ($tamanhoId === null) {
    $stmt = $conn->prepare("SELECT id, quantidade FROM itens_carrinho WHERE carrinho_id = ? AND produto_id = ? AND tamanho_id IS NULL");
    $stmt->bind_param("ii", $carrinhoId, $produtoId);
} else {
    $stmt = $conn->prepare("SELECT id, quantidade FROM itens_carrinho WHERE carrinho_id = ? AND produto_id = ? AND tamanho_id = ?");
    $stmt->bind_param("iii", $carrinhoId, $produtoId, $tamanhoId);
}
$stmt->execute();
$result = $stmt->get_result();

if ($item = $result->fetch_assoc()) {
    $novaQtd = (int) $item['quantidade'] + 1;
    $upd = $conn->prepare("UPDATE itens_carrinho SET quantidade = ? WHERE id = ?");
    $upd->bind_param("ii", $novaQtd, $item['id']);
    $upd->execute();
    $upd->close();
} else {
    $ins = $conn->prepare("INSERT INTO itens_carrinho (carrinho_id, produto_id, tamanho_id, quantidade) VALUES (?, ?, ?, 1)");
    $ins->bind_param("iii", $carrinhoId, $produtoId, $tamanhoId);
    $ins->execute();
    $ins->close();
}
$stmt->close();

echo "adicionado";
