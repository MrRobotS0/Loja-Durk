<?php
session_start();
include 'db.php';

$usuario_id = isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
$metodo = $_POST['pagamento'] ?? 'pix';

if (!$usuario_id) {
    die("Acesso negado.");
}

$stmtEnd = $conn->prepare("SELECT id FROM enderecos WHERE user_id = ? LIMIT 1");
$stmtEnd->bind_param("i", $usuario_id);
$stmtEnd->execute();
$endereco = $stmtEnd->get_result()->fetch_assoc();
$endereco_id = $endereco['id'] ?? null;
$stmtEnd->close();

if (!$endereco_id) {
    die("Endereço não encontrado.");
}

$produtos = [];
$valor_total = 0;
$itensIds = [];

if (!empty($_POST['produto_direto'])) {
    $pid = (int) $_POST['produto_direto'];
    $qtd = max(1, (int) ($_POST['quantidade_direto'] ?? 1));
    $tid = !empty($_POST['tamanho_direto']) ? (int) $_POST['tamanho_direto'] : null;

    $stmt = $conn->prepare("SELECT id, preco FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    if ($row = $stmt->get_result()->fetch_assoc()) {
        $produtos[] = [
            'produto_id' => (int) $row['id'],
            'tamanho_id' => $tid,
            'quantidade' => $qtd,
            'preco'      => (float) $row['preco'],
        ];
        $valor_total += $row['preco'] * $qtd;
    }
    $stmt->close();
} elseif (!empty($_POST['selecionados']) && is_array($_POST['selecionados'])) {
    $ids = array_map('intval', $_POST['selecionados']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $conn->prepare("SELECT ic.id AS item_id, ic.produto_id, ic.tamanho_id, ic.quantidade, p.preco
                            FROM itens_carrinho ic
                            JOIN produtos p ON ic.produto_id = p.id
                            JOIN carrinhos c ON ic.carrinho_id = c.id
                            WHERE ic.id IN ($placeholders) AND c.user_id = ?");
    $params = array_merge($ids, [$usuario_id]);
    $stmt->bind_param($types . 'i', ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $produtos[] = [
            'produto_id' => (int) $row['produto_id'],
            'tamanho_id' => $row['tamanho_id'] !== null ? (int) $row['tamanho_id'] : null,
            'quantidade' => (int) $row['quantidade'],
            'preco'      => (float) $row['preco'],
        ];
        $valor_total += $row['preco'] * $row['quantidade'];
        $itensIds[] = (int) $row['item_id'];
    }
    $stmt->close();
} else {
    die("Dados incompletos.");
}

if (empty($produtos)) {
    die("Nenhum produto encontrado.");
}

$conn->begin_transaction();
try {
    $stmtPed = $conn->prepare("INSERT INTO pedidos (user_id, endereco_id, status, data_pedido, valor_total, metodo_pagamento)
                               VALUES (?, ?, 'Pendente', NOW(), ?, ?)");
    $stmtPed->bind_param("iids", $usuario_id, $endereco_id, $valor_total, $metodo);
    $stmtPed->execute();
    $pedido_id = $stmtPed->insert_id;
    $stmtPed->close();

    $stmtItem = $conn->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, tamanho_id, quantidade, preco_unitario)
                                VALUES (?, ?, ?, ?, ?)");
    foreach ($produtos as $p) {
        $stmtItem->bind_param("iiiid", $pedido_id, $p['produto_id'], $p['tamanho_id'], $p['quantidade'], $p['preco']);
        $stmtItem->execute();
    }
    $stmtItem->close();

    if (!empty($itensIds)) {
        $placeholders = implode(',', array_fill(0, count($itensIds), '?'));
        $types = str_repeat('i', count($itensIds));
        $del = $conn->prepare("DELETE FROM itens_carrinho WHERE id IN ($placeholders)");
        $del->bind_param($types, ...$itensIds);
        $del->execute();
        $del->close();
    }

    $conn->commit();
    header("Location: ../views/pedido_sucesso.php");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    die("Erro ao processar pedido. Tente novamente.");
}
