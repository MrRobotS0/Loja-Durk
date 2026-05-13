<?php
include 'includes/auth.php';
include '../backend/db.php';

$totalUsuarios = (int) $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$totalProdutos = (int) $conn->query("SELECT COUNT(*) AS total FROM produtos")->fetch_assoc()['total'];
$totalPedidos  = (int) $conn->query("SELECT COUNT(*) AS total FROM pedidos")->fetch_assoc()['total'];
$faturamento   = (float) ($conn->query("SELECT COALESCE(SUM(valor_total), 0) AS total FROM pedidos")->fetch_assoc()['total'] ?? 0);

$pedidosRecentes = $conn->query("
  SELECT p.id, p.status, p.data_pedido, p.valor_total, u.nome
  FROM pedidos p
  JOIN users u ON p.user_id = u.id
  ORDER BY p.data_pedido DESC
  LIMIT 5
");

$produtosRecentes = $conn->query("
  SELECT p.id, p.nome, p.preco, p.estoque,
         (SELECT MIN(ip.url_imagem) FROM imagens_produto ip WHERE ip.produto_id = p.id) AS imagem
  FROM produtos p
  ORDER BY p.id DESC
  LIMIT 5
");

include 'includes/header.php';
?>

<main class="bg-zinc-950/40 min-h-screen p-4 sm:p-6 lg:p-10">
  <div class="max-w-7xl mx-auto space-y-8">

    <div class="flex flex-wrap items-end justify-between gap-4">
      <div>
        <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Painel</span>
        <h1 class="font-display text-5xl text-white mt-2">DASHBOARD</h1>
        <p class="text-zinc-400 mt-1 text-sm">Visão geral da operação.</p>
      </div>
      <p class="text-zinc-500 text-sm"><?= date('d/m/Y · H:i') ?></p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 hover:border-yellow-400 transition">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 bg-blue-500/10 text-blue-400 rounded-lg flex items-center justify-center"><i class="fa-solid fa-users"></i></div>
          <span class="text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Total</span>
        </div>
        <p class="font-display text-4xl text-white"><?= $totalUsuarios ?></p>
        <p class="text-zinc-400 text-xs uppercase tracking-widest mt-1">Usuários</p>
      </div>
      <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 hover:border-yellow-400 transition">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 bg-emerald-500/10 text-emerald-400 rounded-lg flex items-center justify-center"><i class="fa-solid fa-shirt"></i></div>
          <span class="text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Catálogo</span>
        </div>
        <p class="font-display text-4xl text-white"><?= $totalProdutos ?></p>
        <p class="text-zinc-400 text-xs uppercase tracking-widest mt-1">Produtos</p>
      </div>
      <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 hover:border-yellow-400 transition">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 bg-purple-500/10 text-purple-400 rounded-lg flex items-center justify-center"><i class="fa-solid fa-box"></i></div>
          <span class="text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Total</span>
        </div>
        <p class="font-display text-4xl text-white"><?= $totalPedidos ?></p>
        <p class="text-zinc-400 text-xs uppercase tracking-widest mt-1">Pedidos</p>
      </div>
      <div class="bg-yellow-400 rounded-2xl p-6 text-black">
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 bg-black/10 text-black rounded-lg flex items-center justify-center"><i class="fa-solid fa-dollar-sign"></i></div>
          <span class="text-[10px] text-black/60 uppercase tracking-widest font-bold">Bruto</span>
        </div>
        <p class="font-display text-4xl">R$ <?= number_format($faturamento, 2, ',', '.') ?></p>
        <p class="text-black/70 text-xs uppercase tracking-widest mt-1">Faturamento</p>
      </div>
    </div>

    <div>
      <h2 class="font-display text-2xl text-white mb-4 uppercase tracking-wider">Atalhos</h2>
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        <a href="produto_adicionar.php" class="group bg-zinc-950 border border-zinc-900 hover:border-yellow-400 rounded-2xl p-5 transition">
          <i class="fa-solid fa-plus text-yellow-400 text-xl mb-3 group-hover:rotate-90 transition duration-300 inline-block"></i>
          <p class="text-white font-bold text-sm uppercase tracking-wider">Novo Produto</p>
        </a>
        <a href="produtos.php" class="group bg-zinc-950 border border-zinc-900 hover:border-yellow-400 rounded-2xl p-5 transition">
          <i class="fa-solid fa-shirt text-yellow-400 text-xl mb-3"></i>
          <p class="text-white font-bold text-sm uppercase tracking-wider">Produtos</p>
        </a>
        <a href="pedidos.php" class="group bg-zinc-950 border border-zinc-900 hover:border-yellow-400 rounded-2xl p-5 transition">
          <i class="fa-solid fa-box text-yellow-400 text-xl mb-3"></i>
          <p class="text-white font-bold text-sm uppercase tracking-wider">Pedidos</p>
        </a>
        <a href="users.php" class="group bg-zinc-950 border border-zinc-900 hover:border-yellow-400 rounded-2xl p-5 transition">
          <i class="fa-solid fa-users text-yellow-400 text-xl mb-3"></i>
          <p class="text-white font-bold text-sm uppercase tracking-wider">Usuários</p>
        </a>
        <a href="filtros.php" class="group bg-zinc-950 border border-zinc-900 hover:border-yellow-400 rounded-2xl p-5 transition">
          <i class="fa-solid fa-tags text-yellow-400 text-xl mb-3"></i>
          <p class="text-white font-bold text-sm uppercase tracking-wider">Filtros</p>
        </a>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
          <h2 class="font-display text-2xl text-white uppercase tracking-wider">Pedidos Recentes</h2>
          <a href="pedidos.php" class="text-yellow-400 hover:text-yellow-300 text-xs uppercase tracking-widest font-bold">Ver todos →</a>
        </div>
        <ul class="space-y-2">
          <?php if ($pedidosRecentes && $pedidosRecentes->num_rows > 0):
            while ($p = $pedidosRecentes->fetch_assoc()):
              $statusClass = match (strtolower($p['status'] ?? '')) {
                'pendente'  => 'text-yellow-400 bg-yellow-400/10',
                'pago'      => 'text-blue-400 bg-blue-400/10',
                'enviado'   => 'text-purple-400 bg-purple-400/10',
                'entregue'  => 'text-emerald-400 bg-emerald-400/10',
                'cancelado' => 'text-red-400 bg-red-400/10',
                default     => 'text-zinc-400 bg-zinc-800',
              }; ?>
              <li>
                <a href="pedido_detalhes.php?id=<?= $p['id'] ?>" class="flex items-center justify-between gap-3 bg-black border border-zinc-900 hover:border-yellow-400 rounded-lg p-3 transition">
                  <div class="min-w-0 flex-1">
                    <p class="text-white text-sm font-bold">#<?= $p['id'] ?> · <?= htmlspecialchars($p['nome']) ?></p>
                    <p class="text-zinc-500 text-xs"><?= date('d/m/Y H:i', strtotime($p['data_pedido'])) ?></p>
                  </div>
                  <span class="px-2 py-0.5 text-[10px] uppercase tracking-widest font-bold rounded <?= $statusClass ?> whitespace-nowrap"><?= htmlspecialchars($p['status']) ?></span>
                  <span class="text-yellow-400 font-bold text-sm whitespace-nowrap">R$ <?= number_format($p['valor_total'], 2, ',', '.') ?></span>
                </a>
              </li>
            <?php endwhile;
          else: ?>
            <li class="text-zinc-500 text-sm text-center py-6">Nenhum pedido ainda.</li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
          <h2 class="font-display text-2xl text-white uppercase tracking-wider">Produtos Recentes</h2>
          <a href="produtos.php" class="text-yellow-400 hover:text-yellow-300 text-xs uppercase tracking-widest font-bold">Ver todos →</a>
        </div>
        <ul class="space-y-2">
          <?php if ($produtosRecentes && $produtosRecentes->num_rows > 0):
            while ($p = $produtosRecentes->fetch_assoc()): ?>
              <li>
                <a href="produto_editar.php?id=<?= $p['id'] ?>" class="flex items-center gap-3 bg-black border border-zinc-900 hover:border-yellow-400 rounded-lg p-3 transition">
                  <div class="w-12 h-12 bg-zinc-900 rounded-lg overflow-hidden flex items-center justify-center flex-shrink-0">
                    <?php if (!empty($p['imagem'])): ?>
                      <img src="../<?= htmlspecialchars($p['imagem']) ?>" class="w-full h-full object-contain p-1">
                    <?php else: ?>
                      <i class="fa-solid fa-image text-zinc-700"></i>
                    <?php endif; ?>
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-white text-sm font-bold line-clamp-1"><?= htmlspecialchars($p['nome']) ?></p>
                    <p class="text-zinc-500 text-xs">Estoque: <?= (int) $p['estoque'] ?></p>
                  </div>
                  <span class="text-yellow-400 font-bold text-sm whitespace-nowrap">R$ <?= number_format($p['preco'], 2, ',', '.') ?></span>
                </a>
              </li>
            <?php endwhile;
          else: ?>
            <li class="text-zinc-500 text-sm text-center py-6">Nenhum produto cadastrado.</li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
