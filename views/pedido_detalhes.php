<?php
include '../includes/verifica_login.php';
include '../includes/header.php';
include '../backend/db.php';

$usuario_id = (int) $_SESSION['usuario_id'];
$pedido_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $conn->prepare("SELECT p.*, e.rua, e.numero, e.complemento, e.bairro, e.cidade, e.estado, e.cep, e.pais
                        FROM pedidos p
                        LEFT JOIN enderecos e ON p.endereco_id = e.id
                        WHERE p.id = ? AND p.user_id = ?");
$stmt->bind_param("ii", $pedido_id, $usuario_id);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();
$stmt->close();

$itens = null;
if ($pedido) {
    $stmt = $conn->prepare("SELECT ip.*, pr.nome AS produto, t.descricao AS tamanho,
                                   (SELECT MIN(im.url_imagem) FROM imagens_produto im WHERE im.produto_id = pr.id) AS imagem
                            FROM itens_pedido ip
                            JOIN produtos pr ON ip.produto_id = pr.id
                            LEFT JOIN tamanhos t ON ip.tamanho_id = t.id
                            WHERE ip.pedido_id = ?");
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    $itens = $stmt->get_result();
}

$statusClass = $pedido ? (match (strtolower($pedido['status'] ?? '')) {
  'pendente'  => 'text-yellow-400 bg-yellow-400/10 border-yellow-400/30',
  'pago'      => 'text-blue-400 bg-blue-400/10 border-blue-400/30',
  'enviado'   => 'text-purple-400 bg-purple-400/10 border-purple-400/30',
  'entregue'  => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/30',
  'cancelado' => 'text-red-400 bg-red-400/10 border-red-400/30',
  default     => 'text-zinc-400 bg-zinc-800 border-zinc-700',
}) : '';
?>

<title>Pedido #<?= $pedido_id ?> · DURK</title>

<main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-10 py-10">

<?php if ($pedido): ?>
  <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
    <div>
      <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Pedido</span>
      <h1 class="font-display text-5xl md:text-6xl text-white mt-2">#<?= $pedido['id'] ?></h1>
    </div>
    <span class="px-4 py-2 text-xs uppercase tracking-widest font-bold rounded-full border <?= $statusClass ?>">
      <?= htmlspecialchars($pedido['status']) ?>
    </span>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6">
      <h2 class="font-display text-xl text-white mb-4 flex items-center gap-2"><i class="fa-solid fa-receipt text-yellow-400"></i> RESUMO</h2>
      <div class="space-y-2 text-sm">
        <div class="flex justify-between text-zinc-400"><span>Data</span><span class="text-white"><?= date('d/m/Y · H:i', strtotime($pedido['data_pedido'])) ?></span></div>
        <div class="flex justify-between text-zinc-400"><span>Pagamento</span><span class="text-white capitalize"><?= htmlspecialchars($pedido['metodo_pagamento']) ?></span></div>
        <div class="flex justify-between text-zinc-400 border-t border-zinc-900 pt-3 mt-3 text-base"><span class="uppercase tracking-widest text-xs">Total</span><span class="text-yellow-400 font-bold text-xl">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></span></div>
      </div>
    </div>

    <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6">
      <h2 class="font-display text-xl text-white mb-4 flex items-center gap-2"><i class="fa-solid fa-location-dot text-yellow-400"></i> ENTREGA</h2>
      <?php if (!empty($pedido['rua'])): ?>
      <p class="text-zinc-300 leading-relaxed text-sm">
        <?= htmlspecialchars($pedido['rua']) . ', Nº ' . htmlspecialchars($pedido['numero']) ?>
        <?php if (!empty($pedido['complemento'])): ?> — <?= htmlspecialchars($pedido['complemento']) ?><?php endif; ?>
        <br>
        <?= htmlspecialchars($pedido['bairro']) . ' · ' . htmlspecialchars($pedido['cidade']) . ' - ' . htmlspecialchars($pedido['estado']) ?>
        <br>
        <span class="text-zinc-500 text-xs uppercase tracking-widest">CEP <?= htmlspecialchars($pedido['cep']) ?></span>
      </p>
      <?php else: ?>
        <p class="text-zinc-500 text-sm">Endereço não informado.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 md:p-8">
    <h2 class="font-display text-xl text-white mb-5 flex items-center gap-2"><i class="fa-solid fa-box text-yellow-400"></i> ITENS</h2>
    <ul class="divide-y divide-zinc-900">
      <?php while ($itens && $item = $itens->fetch_assoc()): ?>
        <li class="flex items-center gap-4 py-4">
          <div class="w-16 h-16 bg-zinc-900 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
            <?php if (!empty($item['imagem'])): ?>
              <img src="../<?= htmlspecialchars($item['imagem']) ?>" class="w-full h-full object-contain p-2">
            <?php else: ?>
              <i class="fa-solid fa-image text-zinc-700 text-2xl"></i>
            <?php endif; ?>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-white font-bold line-clamp-2"><?= htmlspecialchars($item['produto']) ?></p>
            <div class="flex flex-wrap items-center gap-x-3 mt-1 text-[10px] uppercase tracking-widest text-zinc-500 font-bold">
              <?php if (!empty($item['tamanho'])): ?>
                <span>Tam: <span class="text-yellow-400"><?= htmlspecialchars($item['tamanho']) ?></span></span>
              <?php endif; ?>
              <span>Qtd: <span class="text-yellow-400"><?= $item['quantidade'] ?></span></span>
            </div>
          </div>
          <p class="text-yellow-400 font-bold text-right whitespace-nowrap">R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?></p>
        </li>
      <?php endwhile; ?>
    </ul>
  </div>

  <div class="mt-8 text-center">
    <a href="minhaconta.php" class="inline-flex items-center gap-2 bg-transparent border-2 border-white hover:border-yellow-400 hover:text-yellow-400 text-white px-6 py-3 rounded-lg font-bold uppercase tracking-wider text-sm transition">
      <i class="fa-solid fa-arrow-left"></i> Voltar para minha conta
    </a>
  </div>

<?php else: ?>
  <div class="text-center py-20">
    <i class="fa-solid fa-circle-question text-6xl text-zinc-800 mb-4"></i>
    <h1 class="font-display text-4xl text-white mb-4">PEDIDO NÃO ENCONTRADO</h1>
    <p class="text-zinc-500 mb-8">Esse pedido não existe ou não é seu.</p>
    <a href="minhaconta.php" class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-300 text-black px-6 py-3 rounded-lg font-bold uppercase tracking-wider text-sm transition">Voltar</a>
  </div>
<?php endif; ?>

</main>

<?php include '../includes/footer.php'; ?>
