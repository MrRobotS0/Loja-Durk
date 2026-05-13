<?php
include 'includes/auth.php';
include '../backend/db.php';

$pedidos = $conn->query("
  SELECT p.*, u.nome AS nome_usuario
  FROM pedidos p
  JOIN users u ON p.user_id = u.id
  ORDER BY p.data_pedido DESC
");

include 'includes/header.php';
?>

<main class="bg-zinc-950/40 min-h-screen p-4 sm:p-6 lg:p-10">
  <div class="max-w-7xl mx-auto space-y-6">

    <div>
      <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Gestão</span>
      <h1 class="font-display text-5xl text-white mt-2">PEDIDOS</h1>
    </div>

    <div class="overflow-x-auto bg-zinc-950 border border-zinc-900 rounded-2xl">
      <table class="min-w-full text-sm">
        <thead class="bg-black text-yellow-400 uppercase text-[10px] tracking-widest font-bold">
          <tr>
            <th class="px-6 py-4 text-left">#ID</th>
            <th class="px-6 py-4 text-left">Cliente</th>
            <th class="px-6 py-4 text-left">Status</th>
            <th class="px-6 py-4 text-left">Data</th>
            <th class="px-6 py-4 text-left">Valor</th>
            <th class="px-6 py-4 text-left">Pagamento</th>
            <th class="px-6 py-4 text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-900">
          <?php if ($pedidos && $pedidos->num_rows > 0):
            while ($pedido = $pedidos->fetch_assoc()):
              $statusClass = match (strtolower($pedido['status'] ?? '')) {
                'pendente'  => 'text-yellow-400 bg-yellow-400/10',
                'pago'      => 'text-blue-400 bg-blue-400/10',
                'enviado'   => 'text-purple-400 bg-purple-400/10',
                'entregue'  => 'text-emerald-400 bg-emerald-400/10',
                'cancelado' => 'text-red-400 bg-red-400/10',
                default     => 'text-zinc-400 bg-zinc-800',
              }; ?>
            <tr class="hover:bg-black transition">
              <td class="px-6 py-4 font-bold text-zinc-500">#<?= $pedido['id'] ?></td>
              <td class="px-6 py-4 text-white font-bold"><?= htmlspecialchars($pedido['nome_usuario']) ?></td>
              <td class="px-6 py-4">
                <span class="px-3 py-1 text-[10px] uppercase tracking-widest font-bold rounded-full <?= $statusClass ?>">
                  <?= htmlspecialchars($pedido['status']) ?>
                </span>
              </td>
              <td class="px-6 py-4 text-zinc-300"><?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></td>
              <td class="px-6 py-4 text-yellow-400 font-bold">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></td>
              <td class="px-6 py-4 capitalize text-zinc-300"><?= htmlspecialchars($pedido['metodo_pagamento']) ?></td>
              <td class="px-6 py-4 text-center">
                <a href="pedido_detalhes.php?id=<?= $pedido['id'] ?>" class="inline-flex items-center gap-1.5 bg-zinc-900 hover:bg-yellow-400 hover:text-black text-white px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider transition">
                  <i class="fa-solid fa-eye"></i> Ver
                </a>
              </td>
            </tr>
          <?php endwhile;
          else: ?>
            <tr><td colspan="7" class="text-center py-16 text-zinc-500">
              <i class="fa-solid fa-box-open text-5xl text-zinc-800 mb-3 block"></i>
              Nenhum pedido registrado.
            </td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
