<?php
include 'includes/auth.php';
include '../backend/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $conn->prepare("SELECT p.*, u.nome AS nome_usuario, u.email, u.telefone,
                               e.rua, e.numero, e.complemento, e.bairro, e.cidade, e.estado, e.cep
                        FROM pedidos p
                        JOIN users u ON p.user_id = u.id
                        LEFT JOIN enderecos e ON p.endereco_id = e.id
                        WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pedido) {
  header("Location: pedidos.php");
  exit();
}

$itensStmt = $conn->prepare("SELECT i.*, pr.nome, t.descricao AS tamanho
                             FROM itens_pedido i
                             JOIN produtos pr ON i.produto_id = pr.id
                             LEFT JOIN tamanhos t ON i.tamanho_id = t.id
                             WHERE i.pedido_id = ?");
$itensStmt->bind_param("i", $id);
$itensStmt->execute();
$itens = $itensStmt->get_result();

$statusClass = match (strtolower($pedido['status'] ?? '')) {
  'pendente'  => 'text-yellow-400 bg-yellow-400/10 border-yellow-400/30',
  'pago'      => 'text-blue-400 bg-blue-400/10 border-blue-400/30',
  'enviado'   => 'text-purple-400 bg-purple-400/10 border-purple-400/30',
  'entregue'  => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/30',
  'cancelado' => 'text-red-400 bg-red-400/10 border-red-400/30',
  default     => 'text-zinc-400 bg-zinc-800 border-zinc-700',
};

include 'includes/header.php';
?>

<main class="bg-zinc-950/40 min-h-screen p-4 sm:p-6 lg:p-10">
  <div class="max-w-5xl mx-auto space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
      <div>
        <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Pedido</span>
        <h1 class="font-display text-5xl text-white mt-2">#<?= $pedido['id'] ?></h1>
      </div>
      <span class="px-4 py-2 text-xs uppercase tracking-widest font-bold rounded-full border <?= $statusClass ?>"><?= htmlspecialchars($pedido['status']) ?></span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6">
        <h2 class="font-display text-xl text-white mb-4 flex items-center gap-2"><i class="fa-solid fa-receipt text-yellow-400"></i> PEDIDO</h2>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between text-zinc-400"><span>Data</span><span class="text-white"><?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></span></div>
          <div class="flex justify-between text-zinc-400"><span>Pagamento</span><span class="text-white capitalize"><?= htmlspecialchars($pedido['metodo_pagamento']) ?></span></div>
          <div class="flex justify-between text-zinc-400 border-t border-zinc-900 pt-3 mt-3"><span class="uppercase tracking-widest text-xs">Total</span><span class="text-yellow-400 font-bold text-xl">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></span></div>
        </div>
      </div>

      <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6">
        <h2 class="font-display text-xl text-white mb-4 flex items-center gap-2"><i class="fa-solid fa-user text-yellow-400"></i> CLIENTE</h2>
        <p class="text-white font-bold"><?= htmlspecialchars($pedido['nome_usuario']) ?></p>
        <p class="text-zinc-400 text-sm"><?= htmlspecialchars($pedido['email'] ?? '') ?></p>
        <p class="text-zinc-400 text-sm"><?= htmlspecialchars($pedido['telefone'] ?? '') ?></p>
        <?php if (!empty($pedido['rua'])): ?>
          <p class="text-yellow-400 uppercase tracking-widest text-[10px] font-bold mt-3 mb-1">Endereço</p>
          <p class="text-zinc-300 text-sm leading-relaxed">
            <?= htmlspecialchars($pedido['rua']) ?>, <?= htmlspecialchars($pedido['numero']) ?>
            <?php if (!empty($pedido['complemento'])): ?> — <?= htmlspecialchars($pedido['complemento']) ?><?php endif; ?><br>
            <?= htmlspecialchars($pedido['bairro']) ?> · <?= htmlspecialchars($pedido['cidade']) ?> - <?= htmlspecialchars($pedido['estado']) ?><br>
            <span class="text-zinc-500 text-xs">CEP <?= htmlspecialchars($pedido['cep']) ?></span>
          </p>
        <?php endif; ?>
      </div>
    </div>

    <div class="bg-zinc-950 border border-zinc-900 rounded-2xl overflow-hidden">
      <div class="px-6 py-4 border-b border-zinc-900">
        <h2 class="font-display text-xl text-white flex items-center gap-2"><i class="fa-solid fa-box text-yellow-400"></i> ITENS</h2>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-black text-yellow-400 uppercase text-[10px] tracking-widest font-bold">
          <tr>
            <th class="px-6 py-3 text-left">Produto</th>
            <th class="px-6 py-3 text-left">Tamanho</th>
            <th class="px-6 py-3 text-left">Qtd</th>
            <th class="px-6 py-3 text-left">Unitário</th>
            <th class="px-6 py-3 text-left">Subtotal</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-900">
          <?php while ($item = $itens->fetch_assoc()): ?>
            <tr>
              <td class="px-6 py-3 text-white font-bold"><?= htmlspecialchars($item['nome']) ?></td>
              <td class="px-6 py-3 text-zinc-300"><?= htmlspecialchars($item['tamanho'] ?? '—') ?></td>
              <td class="px-6 py-3 text-zinc-300"><?= $item['quantidade'] ?></td>
              <td class="px-6 py-3 text-zinc-300">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
              <td class="px-6 py-3 text-yellow-400 font-bold">R$ <?= number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div>
      <a href="pedidos.php" class="inline-flex items-center gap-2 text-zinc-400 hover:text-yellow-400 text-sm uppercase tracking-widest font-bold transition">
        <i class="fa-solid fa-arrow-left"></i> Voltar para lista
      </a>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
