<?php
session_start();
require_once 'db.php';

$response = ['favoritos' => 0, 'carrinho' => 0];

if (isset($_SESSION['usuario_id'])) {
    $userId = $_SESSION['usuario_id'];

    $sqlFav = "SELECT COUNT(*) AS total FROM favoritos WHERE user_id = $userId";
    $resFav = mysqli_query($conn, $sqlFav);
    $response['favoritos'] = mysqli_fetch_assoc($resFav)['total'] ?? 0;

    $sqlCarrinho = "SELECT SUM(quantidade) AS total FROM itens_carrinho ic
                    JOIN carrinhos c ON ic.carrinho_id = c.id
                    WHERE c.user_id = $userId";
    $resCarrinho = mysqli_query($conn, $sqlCarrinho);
    $response['carrinho'] = mysqli_fetch_assoc($resCarrinho)['total'] ?? 0;
}

header('Content-Type: application/json');
echo json_encode($response);
