<?php
include 'includes/auth.php';
include '../backend/db.php';

$pedidos = $conn->query("SELECT p.*, u.nome AS nome_usuario FROM pedidos p JOIN users u ON p.user_id = u.id ORDER BY p.data_pedido DESC");

include 'includes/header.php';
?>

<main class="bg-gray-100 min-h-screen p-8 font-sans">
  <div class="max-w-7xl mx-auto space-y-10">
    <h1 class="text-3xl font-bold text-gray-800 text-center">📦 Gerenciamento de Pedidos</h1>

    <div class="bg-white rounded-xl shadow overflow-x-auto">
      <table class="min-w-full text-sm text-gray-700">
        <thead class="bg-gray-200 text-xs uppercase">
          <tr>
            <th class="px-4 py-3 text-left">#ID</th>
            <th class="px-4 py-3 text-left">Cliente</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Data</th>
            <th class="px-4 py-3 text-left">Valor Total</th>
            <th class="px-4 py-3 text-left">Pagamento</th>
            <th class="px-4 py-3 text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php while($pedido = $pedidos->fetch_assoc()): ?>
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 font-medium text-gray-900">#<?= $pedido['id'] ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($pedido['nome_usuario']) ?></td>
            <td class="px-4 py-3 capitalize"><?= htmlspecialchars($pedido['status']) ?></td>
            <td class="px-4 py-3"><?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></td>
            <td class="px-4 py-3">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($pedido['metodo_pagamento']) ?></td>
            <td class="px-4 py-3 text-center">
              <a href="pedido_detalhes.php?id=<?= $pedido['id'] ?>" class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">Ver</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
