<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario_id'])) exit;

$userId = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['item_id']) && isset($_POST['quantidade'])) {
        $itemId = intval($_POST['item_id']);
        $quantidade = max(1, intval($_POST['quantidade']));

        $stmt = $conn->prepare("
            UPDATE itens_carrinho ic
            JOIN carrinhos c ON ic.carrinho_id = c.id
            SET ic.quantidade = ?
            WHERE ic.id = ? AND c.user_id = ?
        ");
        $stmt->bind_param("iii", $quantidade, $itemId, $userId);
        $stmt->execute();
        exit('atualizado');
    }

    if (isset($_POST['item_id']) && isset($_POST['remover'])) {
        $itemId = intval($_POST['item_id']);

        $stmt = $conn->prepare("
            DELETE ic FROM itens_carrinho ic
            JOIN carrinhos c ON ic.carrinho_id = c.id
            WHERE ic.id = ? AND c.user_id = ?
        ");
        $stmt->bind_param("ii", $itemId, $userId);
        $stmt->execute();
        exit('removido');
    }
}
?>
