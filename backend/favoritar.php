<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo "Usuário não logado";
    exit;
}

$userId = $_SESSION['usuario_id'];

if (!isset($_POST['produto_id']) || !isset($_POST['acao'])) {
    http_response_code(400);
    echo "Dados inválidos";
    exit;
}

$produtoId = intval($_POST['produto_id']);
$acao = $_POST['acao'];

if ($acao === 'adicionar') {
    $stmt = $conn->prepare("INSERT IGNORE INTO favoritos (user_id, produto_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $produtoId);
    $stmt->execute();
    $resposta = "adicionado";
} elseif ($acao === 'remover') {
    $stmt = $conn->prepare("DELETE FROM favoritos WHERE user_id = ? AND produto_id = ?");
    $stmt->bind_param("ii", $userId, $produtoId);
    $stmt->execute();
    $resposta = "removido";
} else {
    http_response_code(400);
    echo "Ação inválida";
    exit;
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($isAjax) {
    echo $resposta;
} else {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
