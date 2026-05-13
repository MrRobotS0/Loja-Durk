<?php
include '../includes/verifica_login.php';
include '../includes/header.php';
include '../backend/db.php';

$usuario_id = (int) $_SESSION['usuario_id'];

$stmtEnd = $conn->prepare("SELECT * FROM enderecos WHERE user_id = ? LIMIT 1");
$stmtEnd->bind_param("i", $usuario_id);
$stmtEnd->execute();
$endereco = $stmtEnd->get_result()->fetch_assoc();
$stmtEnd->close();

$produtos = [];
$totalGeral = 0;

if (isset($_GET['produto_id'])) {
    $produto_id = (int) $_GET['produto_id'];
    $quantidade = max(1, (int) ($_GET['quantidade'] ?? 1));
    $stmt = $conn->prepare("SELECT id, nome, preco FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($prod = $res->fetch_assoc()) {
        $prod['quantidade'] = $quantidade;
        $produtos[] = $prod;
        $totalGeral += $prod['preco'] * $prod['quantidade'];
    }
    $stmt->close();
} elseif (isset($_POST['selecionados']) && is_array($_POST['selecionados'])) {
    $ids = array_map('intval', $_POST['selecionados']);
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $sql = "SELECT ic.id, p.nome, p.preco, ic.quantidade
                FROM itens_carrinho ic
                JOIN produtos p ON ic.produto_id = p.id
                WHERE ic.id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $produtos[] = $row;
            $totalGeral += $row['preco'] * $row['quantidade'];
        }
        $stmt->close();
    }
}
?>

<title>Finalizar Pedido · DURK</title>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-10">

  <div class="mb-10">
    <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Checkout</span>
    <h1 class="font-display text-5xl md:text-6xl text-white mt-2">FINALIZAR PEDIDO</h1>
    <p class="text-zinc-400 mt-2">Confira tudo antes de fechar o drop.</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-2 space-y-6">
      <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 md:p-8">
        <h2 class="font-display text-2xl text-white mb-5 flex items-center gap-3"><i class="fa-solid fa-box text-yellow-400"></i> RESUMO</h2>
        <?php if (!empty($produtos)): ?>
          <ul class="divide-y divide-zinc-900">
            <?php foreach ($produtos as $item): ?>
              <li class="flex justify-between items-center py-4">
                <div>
                  <p class="text-white font-bold"><?= htmlspecialchars($item['nome']) ?></p>
                  <p class="text-zinc-500 text-xs uppercase tracking-wider mt-1">Quantidade: <?= $item['quantidade'] ?></p>
                </div>
                <p class="text-yellow-400 font-bold">R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></p>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="text-red-400">Nenhum produto selecionado.</p>
        <?php endif; ?>
      </div>

      <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 md:p-8">
        <h2 class="font-display text-2xl text-white mb-5 flex items-center gap-3"><i class="fa-solid fa-location-dot text-yellow-400"></i> ENTREGA</h2>
        <?php if ($endereco): ?>
          <div class="bg-black border border-zinc-900 rounded-lg p-4 text-zinc-300 leading-relaxed">
            <?= htmlspecialchars($endereco['rua']) . ', Nº ' . htmlspecialchars($endereco['numero']) ?>
            <?php if (!empty($endereco['complemento'])): ?> — <?= htmlspecialchars($endereco['complemento']) ?><?php endif; ?>
            <br>
            <?= htmlspecialchars($endereco['bairro']) . ' · ' . htmlspecialchars($endereco['cidade']) . ' - ' . htmlspecialchars($endereco['estado']) ?>
            <br>
            <span class="text-zinc-500 text-xs uppercase tracking-wider">CEP <?= htmlspecialchars($endereco['cep']) ?> · <?= htmlspecialchars($endereco['pais']) ?></span>
          </div>
        <?php else: ?>
          <div class="text-center py-6 border border-dashed border-yellow-400/30 bg-yellow-400/5 rounded-lg">
            <i class="fa-solid fa-triangle-exclamation text-yellow-400 text-3xl mb-2"></i>
            <p class="text-yellow-400">Nenhum endereço cadastrado.</p>
            <button type="button" class="mt-4 bg-yellow-400 hover:bg-yellow-300 text-black px-5 py-2.5 rounded-lg font-bold uppercase tracking-wider text-sm transition"
              onclick="document.getElementById('modalEndereco').classList.remove('hidden')">
              <i class="fa-solid fa-plus"></i> Cadastrar Endereço
            </button>
          </div>
        <?php endif; ?>
      </div>

      <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 md:p-8">
        <h2 class="font-display text-2xl text-white mb-5 flex items-center gap-3"><i class="fa-solid fa-credit-card text-yellow-400"></i> PAGAMENTO</h2>
        <form method="POST" action="../backend/processar_pedido.php" class="space-y-4" id="formPagamento">
          <?php foreach ($produtos as $item): ?>
            <input type="hidden" name="selecionados[]" value="<?= $item['id'] ?>">
          <?php endforeach; ?>
          <?php if (isset($_GET['produto_id'])): ?>
            <input type="hidden" name="produto_direto" value="<?= (int) $_GET['produto_id'] ?>">
            <input type="hidden" name="quantidade_direto" value="<?= max(1, (int) ($_GET['quantidade'] ?? 1)) ?>">
            <?php if (isset($_GET['tamanho_id'])): ?>
              <input type="hidden" name="tamanho_direto" value="<?= (int) $_GET['tamanho_id'] ?>">
            <?php endif; ?>
          <?php endif; ?>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <label class="metodo-pgto cursor-pointer">
              <input type="radio" name="pagamento" value="pix" class="hidden peer" onclick="toggleCartao(false)" checked>
              <div class="border-2 border-zinc-800 peer-checked:border-yellow-400 peer-checked:bg-yellow-400/5 rounded-lg p-4 text-center transition">
                <i class="fa-brands fa-pix text-2xl text-yellow-400"></i>
                <p class="text-white font-bold mt-2 text-sm">Pix</p>
                <p class="text-[10px] text-zinc-500 uppercase tracking-wider">Aprovação imediata</p>
              </div>
            </label>
            <label class="metodo-pgto cursor-pointer">
              <input type="radio" name="pagamento" value="cartao" class="hidden peer" onclick="toggleCartao(true)">
              <div class="border-2 border-zinc-800 peer-checked:border-yellow-400 peer-checked:bg-yellow-400/5 rounded-lg p-4 text-center transition">
                <i class="fa-regular fa-credit-card text-2xl text-yellow-400"></i>
                <p class="text-white font-bold mt-2 text-sm">Cartão</p>
                <p class="text-[10px] text-zinc-500 uppercase tracking-wider">Até 3x s/ juros</p>
              </div>
            </label>
            <label class="metodo-pgto cursor-pointer">
              <input type="radio" name="pagamento" value="boleto" class="hidden peer" onclick="toggleCartao(false)">
              <div class="border-2 border-zinc-800 peer-checked:border-yellow-400 peer-checked:bg-yellow-400/5 rounded-lg p-4 text-center transition">
                <i class="fa-solid fa-barcode text-2xl text-yellow-400"></i>
                <p class="text-white font-bold mt-2 text-sm">Boleto</p>
                <p class="text-[10px] text-zinc-500 uppercase tracking-wider">Compensa em 1-3 dias</p>
              </div>
            </label>
          </div>

          <div id="dados-cartao" class="hidden space-y-3 pt-4 border-t border-zinc-900 mt-4">
            <input type="text" name="numero_cartao" placeholder="Número do cartão" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
            <input type="text" name="nome_cartao" placeholder="Nome impresso no cartão" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
            <div class="grid grid-cols-2 gap-3">
              <input type="text" name="validade" placeholder="MM/AA" class="px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
              <input type="text" name="cvv" placeholder="CVV" class="px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
            </div>
          </div>
        </form>
      </div>
    </div>

    <aside class="lg:col-span-1">
      <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 sticky top-28">
        <h2 class="font-display text-2xl text-white mb-6 pb-4 border-b border-zinc-900">TOTAL</h2>
        <div class="space-y-3 text-sm">
          <div class="flex justify-between text-zinc-400">
            <span>Subtotal</span><span class="text-white font-bold">R$ <?= number_format($totalGeral, 2, ',', '.') ?></span>
          </div>
          <div class="flex justify-between text-zinc-400">
            <span>Frete</span><span class="text-emerald-400 font-bold">Grátis</span>
          </div>
        </div>
        <div class="border-t border-zinc-900 mt-5 pt-5 flex justify-between items-baseline">
          <span class="text-zinc-400 uppercase tracking-widest text-xs">Total a pagar</span>
          <span class="font-display text-3xl text-yellow-400">R$ <?= number_format($totalGeral, 2, ',', '.') ?></span>
        </div>
        <button type="submit" form="formPagamento" <?= !$endereco || empty($produtos) ? 'disabled' : '' ?>
          class="w-full mt-6 bg-yellow-400 hover:bg-yellow-300 text-black px-6 py-4 rounded-lg font-bold uppercase tracking-wider transition shadow-lg shadow-yellow-400/10 inline-flex items-center justify-center gap-2 disabled:bg-zinc-700 disabled:cursor-not-allowed disabled:shadow-none">
          Confirmar Pedido <i class="fa-solid fa-check"></i>
        </button>
        <p class="text-zinc-500 text-[10px] uppercase tracking-widest mt-4 text-center"><i class="fa-solid fa-lock"></i> Compra 100% segura</p>
      </div>
    </aside>
  </div>
</main>

<div id="modalEndereco" class="fixed inset-0 bg-black/80 backdrop-blur flex items-center justify-center hidden z-50 px-4">
  <div class="bg-zinc-950 border border-yellow-400/30 p-6 md:p-8 rounded-2xl w-full max-w-xl">
    <div class="flex items-center justify-between mb-5">
      <h2 class="font-display text-2xl text-white">CADASTRAR ENDEREÇO</h2>
      <button type="button" onclick="document.getElementById('modalEndereco').classList.add('hidden')" class="text-zinc-500 hover:text-white text-xl">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <form id="formEndereco" class="space-y-3">
      <div class="grid grid-cols-2 gap-3">
        <input type="text" name="rua" placeholder="Rua" class="col-span-2 px-4 py-2.5 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition text-sm" required>
        <input type="text" name="numero" placeholder="Número" class="px-4 py-2.5 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition text-sm" required>
        <input type="text" name="complemento" placeholder="Complemento" class="px-4 py-2.5 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition text-sm">
        <input type="text" name="bairro" placeholder="Bairro" class="col-span-2 px-4 py-2.5 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition text-sm" required>
        <input type="text" name="cidade" placeholder="Cidade" class="px-4 py-2.5 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition text-sm" required>
        <input type="text" name="estado" placeholder="UF" maxlength="2" class="px-4 py-2.5 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition text-sm uppercase" required>
        <input type="text" name="cep" placeholder="CEP" class="px-4 py-2.5 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition text-sm" required>
        <input type="text" name="pais" value="Brasil" placeholder="País" class="px-4 py-2.5 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition text-sm" required>
      </div>
      <input type="hidden" name="user_id" value="<?= $usuario_id ?>">
      <div class="flex justify-end gap-3 pt-3">
        <button type="button" onclick="document.getElementById('modalEndereco').classList.add('hidden')" class="px-5 py-2.5 text-sm uppercase tracking-widest font-bold rounded-lg bg-zinc-900 hover:bg-zinc-800 text-zinc-300 transition">Cancelar</button>
        <button type="submit" class="px-5 py-2.5 text-sm uppercase tracking-widest font-bold rounded-lg bg-yellow-400 hover:bg-yellow-300 text-black transition">Salvar</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleCartao(ativo) {
  document.getElementById('dados-cartao').classList.toggle('hidden', !ativo);
}

document.getElementById('formEndereco')?.addEventListener('submit', function (e) {
  e.preventDefault();
  fetch('../backend/cadastrar_endereco.php', { method: 'POST', body: new FormData(e.target) })
    .then(r => r.text()).then(() => location.reload());
});
</script>

<?php include '../includes/footer.php'; ?>
