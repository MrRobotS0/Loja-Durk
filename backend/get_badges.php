<?php
session_start();
require_once 'db.php';

$response = ['favoritos' => 0, 'carrinho' => 0];

if (isset($_SESSION['usuario_id'])) {
    $userId = (int) $_SESSION['usuario_id'];

    $stmtFav = $conn->prepare("SELECT COUNT(*) AS total FROM favoritos WHERE user_id = ?");
    $stmtFav->bind_param("i", $userId);
    $stmtFav->execute();
    $response['favoritos'] = (int) ($stmtFav->get_result()->fetch_assoc()['total'] ?? 0);
    $stmtFav->close();

    $stmtCar = $conn->prepare("SELECT COALESCE(SUM(ic.quantidade), 0) AS total
                               FROM itens_carrinho ic
                               JOIN carrinhos c ON ic.carrinho_id = c.id
                               WHERE c.user_id = ?");
    $stmtCar->bind_param("i", $userId);
    $stmtCar->execute();
    $response['carrinho'] = (int) ($stmtCar->get_result()->fetch_assoc()['total'] ?? 0);
    $stmtCar->close();
}

header('Content-Type: application/json');
echo json_encode($response);
