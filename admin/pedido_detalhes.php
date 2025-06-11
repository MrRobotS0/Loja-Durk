<?php
include 'includes/auth.php';
include '../backend/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$pedido = $conn->query("SELECT p.*, u.nome AS nome_usuario, e.rua, e.numero, e.cidade, e.estado, e.cep
                        FROM pedidos p
                        JOIN users u ON p.user_id = u.id
                        JOIN enderecos e ON p.endereco_id = e.id
                        WHERE p.id = $id")->fetch_assoc();

if (!$pedido) {
  header("Location: pedidos.php");
  exit();
}

$itens = $conn->query("SELECT i.*, pr.nome, pr.preco FROM itens_pedido i JOIN produtos pr ON i.produto_id = pr.id WHERE i.pedido_id = $id");

include 'includes/header.php';
?>

<main class="bg-gray-100 min-h-screen p-8 font-sans">
  <div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📦 Detalhes do Pedido #<?= $pedido['id'] ?></h1>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <h2 class="font-semibold text-gray-700 mb-2">🧾 Informações do Pedido</h2>
        <p><strong>Status:</strong> <?= htmlspecialchars($pedido['status']) ?></p>
        <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></p>
        <p><strong>Total:</strong> R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></p>
        <p><strong>Pagamento:</strong> <?= htmlspecialchars($pedido['metodo_pagamento']) ?></p>
      </div>

      <div>
        <h2 class="font-semibold text-gray-700 mb-2">👤 Cliente</h2>
        <p><strong>Nome:</strong> <?= htmlspecialchars($pedido['nome_usuario']) ?></p>
        <p><strong>Endereço:</strong><br>
          <?= htmlspecialchars($pedido['rua']) ?>, <?= $pedido['numero'] ?><br>
          <?= $pedido['cidade'] ?> - <?= $pedido['estado'] ?><br>
          CEP: <?= $pedido['cep'] ?></p>
      </div>
    </div>

    <div class="mt-8">
      <h2 class="font-semibold text-gray-700 mb-2">🛒 Itens do Pedido</h2>
      <table class="w-full text-sm text-left border border-gray-200">
        <thead class="bg-gray-100 text-gray-700">
          <tr>
            <th class="px-4 py-2 border-b">Produto</th>
            <th class="px-4 py-2 border-b">Quantidade</th>
            <th class="px-4 py-2 border-b">Preço Unitário</th>
            <th class="px-4 py-2 border-b">Subtotal</th>
          </tr>
        </thead>
        <tbody class="bg-white">
          <?php while ($item = $itens->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2"><?= htmlspecialchars($item['nome']) ?></td>
              <td class="px-4 py-2"><?= $item['quantidade'] ?></td>
              <td class="px-4 py-2">R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
              <td class="px-4 py-2">R$ <?= number_format($item['quantidade'] * $item['preco'], 2, ',', '.') ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-6">
      <a href="pedidos.php" class="text-sm text-gray-600 hover:underline">← Voltar para a lista de pedidos</a>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
