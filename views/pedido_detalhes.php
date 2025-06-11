<?php
include '../includes/verifica_login.php';
include '../includes/header.php';
include '../backend/db.php';

$usuario_id = $_SESSION['usuario_id'];
$pedido_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql_pedido = "SELECT p.*, e.rua, e.numero, e.complemento, e.bairro, e.cidade, e.estado, e.cep, e.pais
              FROM pedidos p
              JOIN enderecos e ON p.endereco_id = e.id
              WHERE p.id = $pedido_id AND p.user_id = $usuario_id";
$res_pedido = mysqli_query($conn, $sql_pedido);
$pedido = mysqli_fetch_assoc($res_pedido);

$sql_itens = "SELECT ip.*, pr.nome AS produto, t.descricao AS tamanho
              FROM itens_pedido ip
              JOIN produtos pr ON ip.produto_id = pr.id
              LEFT JOIN tamanhos t ON ip.tamanho_id = t.id
              WHERE ip.pedido_id = $pedido_id";
$res_itens = mysqli_query($conn, $sql_itens);
?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<head>
    <title>Detalhes do Pedido - Loja Durk</title>
</head>
<main class="max-w-5xl mx-auto px-6 py-12" data-aos="fade-up">
    <h1 class="text-4xl font-extrabold text-center text-gray-800 mb-6">Detalhes do Pedido</h1>

    <?php if ($pedido): ?>
    <div class="bg-white shadow-lg rounded-lg p-6 mb-10">
        <h2 class="text-2xl font-semibold text-gray-700 mb-2">Informações do Pedido</h2>
        <p class="text-gray-600"><strong>Status:</strong> <?= $pedido['status'] ?></p>
        <p class="text-gray-600"><strong>Data do Pedido:</strong> <?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></p>
        <p class="text-gray-600"><strong>Método de Pagamento:</strong> <?= ucfirst($pedido['metodo_pagamento']) ?></p>
        <p class="text-gray-600"><strong>Total:</strong> R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></p>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6 mb-10">
        <h2 class="text-2xl font-semibold text-gray-700 mb-2">Endereço de Entrega</h2>
        <p class="text-gray-600">
            <?= $pedido['rua'] . ', Nº ' . $pedido['numero']; ?>
            <?= $pedido['complemento'] ? ' - ' . $pedido['complemento'] : ''; ?>,<br>
            <?= $pedido['bairro'] . ', ' . $pedido['cidade'] . ' - ' . $pedido['estado']; ?><br>
            <?= $pedido['cep'] . ' - ' . $pedido['pais']; ?>
        </p>
    </div>

    <div class="bg-white shadow-lg rounded-lg p-6 mb-10">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Itens do Pedido</h2>
        <ul class="divide-y divide-gray-200">
            <?php while ($item = mysqli_fetch_assoc($res_itens)): ?>
            <li class="flex justify-between py-4">
                <div>
                    <p class="text-gray-800 font-medium"><?= htmlspecialchars($item['produto']) ?></p>
                    <?php if ($item['tamanho']): ?>
                        <p class="text-sm text-gray-500">Tamanho: <?= htmlspecialchars($item['tamanho']) ?></p>
                    <?php endif; ?>
                    <p class="text-sm text-gray-500">Quantidade: <?= $item['quantidade'] ?></p>
                </div>
                <div class="text-right text-green-600 font-semibold">
                    R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?>
                </div>
            </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <div class="text-center mt-8">
        <a href="minhaconta.php" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-all duration-300">Voltar para a Minha Conta</a>
    </div>

    <?php else: ?>
        <p class="text-red-600 text-center text-lg font-semibold">Pedido não encontrado ou não pertence a você.</p>
    <?php endif; ?>
</main>

<script>AOS.init();</script>

<?php include '../includes/footer.php'; ?>
