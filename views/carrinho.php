<?php
include '../includes/verifica_login.php';
include '../includes/header.php';
include '../backend/db.php';

$userId = (int) $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT id FROM carrinhos WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
$carrinho = $res->fetch_assoc();
$stmt->close();

$itens = null;
if ($carrinho) {
  $stmt = $conn->prepare("
    SELECT ic.id AS item_id, p.id AS produto_id, p.nome, p.preco, ic.quantidade,
           (SELECT MIN(ip.url_imagem) FROM imagens_produto ip WHERE ip.produto_id = p.id) AS url_imagem,
           t.descricao AS tamanho
    FROM itens_carrinho ic
    JOIN produtos p ON ic.produto_id = p.id
    LEFT JOIN tamanhos t ON ic.tamanho_id = t.id
    WHERE ic.carrinho_id = ?
    ORDER BY ic.id DESC
  ");
  $stmt->bind_param("i", $carrinho['id']);
  $stmt->execute();
  $itens = $stmt->get_result();
}
?>

<title>Carrinho · DURK</title>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-10">
  <div class="mb-10">
    <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Checkout</span>
    <h1 class="font-display text-5xl md:text-6xl text-white mt-2">SEU CARRINHO</h1>
  </div>

  <?php if (!$itens || $itens->num_rows === 0): ?>
    <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-16 text-center">
      <i class="fa-solid fa-bag-shopping text-7xl text-zinc-800 mb-6"></i>
      <h2 class="font-display text-3xl text-white mb-3">CARRINHO VAZIO</h2>
      <p class="text-zinc-500 mb-8">Que tal dar uma olhada nos drops?</p>
      <a href="vestuario.php" class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-300 text-black px-6 py-3 rounded-lg font-bold uppercase tracking-wider transition">
        Ver Catálogo <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>
  <?php else: ?>
    <form action="finalizar_pedido.php" method="post" class="grid grid-cols-1 lg:grid-cols-3 gap-8" id="formCarrinho">
      <div class="lg:col-span-2 space-y-4" id="lista-itens">
        <?php $total = 0; ?>
        <?php while ($item = $itens->fetch_assoc()):
          $subtotal = $item['preco'] * $item['quantidade'];
          $total += $subtotal; ?>
          <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-5 flex flex-col sm:flex-row items-stretch sm:items-center gap-5 relative transition hover:border-zinc-800"
            data-item-id="<?= $item['item_id'] ?>" data-preco="<?= $item['preco'] ?>">

            <label class="absolute top-3 left-3 cursor-pointer">
              <input type="checkbox" name="selecionados[]" value="<?= $item['item_id'] ?>" class="peer hidden" checked>
              <span class="w-5 h-5 border-2 border-zinc-700 rounded block peer-checked:bg-yellow-400 peer-checked:border-yellow-400 transition flex items-center justify-center">
                <i class="fa-solid fa-check text-black text-xs opacity-0 peer-checked:opacity-100"></i>
              </span>
            </label>

            <div class="w-24 h-24 sm:w-28 sm:h-28 bg-zinc-900 rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden ml-7">
              <?php if (!empty($item['url_imagem'])): ?>
                <img src="../<?= htmlspecialchars($item['url_imagem']) ?>" class="w-full h-full object-contain p-2">
              <?php else: ?>
                <i class="fa-solid fa-image text-zinc-700 text-3xl"></i>
              <?php endif; ?>
            </div>

            <div class="flex-1 min-w-0">
              <h2 class="text-base font-bold text-white line-clamp-2"><?= htmlspecialchars($item['nome']) ?></h2>
              <?php if ($item['tamanho']): ?>
                <p class="text-xs text-zinc-500 uppercase tracking-wider mt-1">Tamanho: <span class="text-yellow-400 font-bold"><?= htmlspecialchars($item['tamanho']) ?></span></p>
              <?php endif; ?>
              <p class="text-xs text-zinc-500 mt-1">R$ <?= number_format($item['preco'], 2, ',', '.') ?> un.</p>
            </div>

            <div class="flex flex-row sm:flex-col items-center sm:items-end justify-between gap-3">
              <div class="flex items-center bg-black border border-zinc-800 rounded-lg overflow-hidden">
                <button type="button" class="btn-menos w-9 h-9 hover:bg-zinc-900 text-zinc-400 hover:text-white transition"><i class="fa-solid fa-minus text-xs"></i></button>
                <span class="qtd w-10 text-center font-bold text-white"><?= $item['quantidade'] ?></span>
                <button type="button" class="btn-mais w-9 h-9 hover:bg-zinc-900 text-zinc-400 hover:text-white transition"><i class="fa-solid fa-plus text-xs"></i></button>
              </div>
              <span class="subtotal text-lg font-bold text-yellow-400">R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
              <button type="button" class="btn-remover text-zinc-500 hover:text-red-500 text-xs uppercase tracking-widest font-bold transition">
                <i class="fa-solid fa-trash"></i> Remover
              </button>
            </div>
          </div>
        <?php endwhile; ?>
      </div>

      <aside class="lg:col-span-1">
        <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 sticky top-28">
          <h2 class="font-display text-2xl text-white mb-6 pb-4 border-b border-zinc-900">RESUMO DO PEDIDO</h2>
          <div class="space-y-3 text-sm">
            <div class="flex justify-between text-zinc-400">
              <span>Subtotal</span>
              <span id="resumo-subtotal" class="text-white font-bold">R$ <?= number_format($total, 2, ',', '.') ?></span>
            </div>
            <div class="flex justify-between text-zinc-400">
              <span>Frete</span>
              <span class="text-yellow-400 font-bold">A calcular</span>
            </div>
          </div>
          <div class="border-t border-zinc-900 mt-5 pt-5 flex justify-between items-baseline">
            <span class="text-zinc-400 uppercase tracking-widest text-xs">Total</span>
            <span id="valor-total" class="font-display text-3xl text-yellow-400">R$ <?= number_format($total, 2, ',', '.') ?></span>
          </div>
          <button type="submit" class="w-full mt-6 bg-yellow-400 hover:bg-yellow-300 text-black px-6 py-4 rounded-lg font-bold uppercase tracking-wider transition shadow-lg shadow-yellow-400/10 inline-flex items-center justify-center gap-2">
            Finalizar Pedido <i class="fa-solid fa-arrow-right"></i>
          </button>
          <a href="vestuario.php" class="block text-center mt-4 text-zinc-500 hover:text-yellow-400 text-xs uppercase tracking-widest font-bold transition">
            ← Continuar comprando
          </a>
        </div>
      </aside>
    </form>
  <?php endif; ?>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const swalDark = { background: '#18181b', color: '#fff', confirmButtonColor: '#facc15' };

$(document).ready(function () {
  function atualizarTotais() {
    let total = 0;
    $('#lista-itens > div[data-item-id]').each(function () {
      const preco = parseFloat($(this).data('preco'));
      const qtd = parseInt($(this).find('.qtd').text());
      const subtotal = preco * qtd;
      $(this).find('.subtotal').text('R$ ' + subtotal.toFixed(2).replace('.', ','));
      total += subtotal;
    });
    const formatado = 'R$ ' + total.toFixed(2).replace('.', ',');
    $('#valor-total').text(formatado);
    $('#resumo-subtotal').text(formatado);
  }

  $(document).on('click', '.btn-mais, .btn-menos', function () {
    const container = $(this).closest('[data-item-id]');
    const qtdSpan = container.find('.qtd');
    let qtd = parseInt(qtdSpan.text());
    if ($(this).hasClass('btn-mais')) qtd++;
    else if (qtd > 1) qtd--;

    $.post('../backend/carrinho_ajax.php', { item_id: container.data('item-id'), quantidade: qtd }, function () {
      qtdSpan.text(qtd);
      atualizarTotais();
    });
  });

  $(document).on('click', '.btn-remover', function () {
    const container = $(this).closest('[data-item-id]');
    $.post('../backend/carrinho_ajax.php', { item_id: container.data('item-id'), remover: true }, function () {
      container.fadeOut(250, function () {
        $(this).remove();
        atualizarTotais();
        atualizarBadges();
        if ($('#lista-itens > div[data-item-id]').length === 0) location.reload();
      });
      Swal.fire({ toast: true, icon: 'info', title: 'Item removido', timer: 1200, position: 'top-end', showConfirmButton: false, ...swalDark });
    });
  });
});
</script>

<?php include '../includes/footer.php'; ?>
