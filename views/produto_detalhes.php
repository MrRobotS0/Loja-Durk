<?php
include '../includes/header.php';
include '../backend/db.php';

if (!isset($_GET['id'])) {
  echo "<main class='max-w-4xl mx-auto px-6 py-20 text-center text-zinc-400'><h1 class='font-display text-5xl text-white mb-4'>PRODUTO NÃO ESPECIFICADO</h1></main>";
  include '../includes/footer.php';
  exit;
}
$id = (int) $_GET['id'];

$stmt = $conn->prepare("
  SELECT p.*, c.nome AS categoria, m.nome AS marca
  FROM produtos p
  LEFT JOIN categorias c ON p.categoria_id = c.id
  LEFT JOIN marcas m ON p.marca_id = m.id
  WHERE p.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$produto) {
  echo "<main class='max-w-4xl mx-auto px-6 py-20 text-center text-zinc-400'><h1 class='font-display text-5xl text-white mb-4'>PRODUTO NÃO ENCONTRADO</h1><a href='vestuario.php' class='text-yellow-400 hover:underline'>Voltar ao catálogo</a></main>";
  include '../includes/footer.php';
  exit;
}

$stmtImg = $conn->prepare("SELECT url_imagem FROM imagens_produto WHERE produto_id = ? ORDER BY ordem");
$stmtImg->bind_param("i", $id);
$stmtImg->execute();
$imagensRes = $stmtImg->get_result();
$imagens = [];
while ($row = $imagensRes->fetch_assoc()) $imagens[] = $row['url_imagem'];
$stmtImg->close();

$stmtTam = $conn->prepare("
  SELECT t.id, t.descricao
  FROM produto_tamanhos pt
  JOIN tamanhos t ON pt.tamanho_id = t.id
  WHERE pt.produto_id = ?
  ORDER BY t.id
");
$stmtTam->bind_param("i", $id);
$stmtTam->execute();
$tamanhosRes = $stmtTam->get_result();
$tamanhos = [];
while ($row = $tamanhosRes->fetch_assoc()) $tamanhos[] = $row;
$stmtTam->close();

$isFavoritado = false;
if (isset($_SESSION['usuario_id'])) {
  $userId = (int) $_SESSION['usuario_id'];
  $check = $conn->prepare("SELECT 1 FROM favoritos WHERE user_id = ? AND produto_id = ?");
  $check->bind_param("ii", $userId, $id);
  $check->execute();
  $isFavoritado = $check->get_result()->num_rows > 0;
  $check->close();
}
?>

<title><?= htmlspecialchars($produto['nome']) ?> · DURK</title>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-10">
  <nav class="text-xs text-zinc-500 uppercase tracking-widest mb-8 flex items-center gap-2">
    <a href="index.php" class="hover:text-yellow-400">Home</a>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <a href="vestuario.php" class="hover:text-yellow-400">Vestuário</a>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-yellow-400 truncate"><?= htmlspecialchars($produto['nome']) ?></span>
  </nav>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

    <div class="space-y-4">
      <div class="aspect-square bg-zinc-950 border border-zinc-900 rounded-2xl overflow-hidden flex items-center justify-center">
        <?php if (!empty($imagens)): ?>
          <img id="imgPrincipal" src="../<?= htmlspecialchars($imagens[0]) ?>" class="w-full h-full object-contain p-6" alt="<?= htmlspecialchars($produto['nome']) ?>">
        <?php else: ?>
          <div class="text-zinc-700"><i class="fa-solid fa-image text-8xl"></i></div>
        <?php endif; ?>
      </div>
      <?php if (count($imagens) > 1): ?>
      <div class="grid grid-cols-5 gap-3">
        <?php foreach ($imagens as $img): ?>
          <button onclick="document.getElementById('imgPrincipal').src='../<?= htmlspecialchars($img) ?>'"
            class="aspect-square bg-zinc-950 border border-zinc-900 hover:border-yellow-400 rounded-lg overflow-hidden p-2 transition">
            <img src="../<?= htmlspecialchars($img) ?>" class="w-full h-full object-contain">
          </button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <div class="space-y-6">
      <div>
        <div class="flex items-center gap-2 mb-3">
          <span class="text-yellow-400 text-[10px] uppercase tracking-[0.3em] font-bold"><?= htmlspecialchars($produto['categoria'] ?? '') ?></span>
          <?php if (!empty($produto['marca'])): ?>
            <span class="text-zinc-600">·</span>
            <span class="text-zinc-400 text-[10px] uppercase tracking-[0.3em] font-bold"><?= htmlspecialchars($produto['marca']) ?></span>
          <?php endif; ?>
        </div>
        <h1 class="font-display text-4xl md:text-5xl text-white leading-tight"><?= htmlspecialchars($produto['nome']) ?></h1>
      </div>

      <div class="border-y border-zinc-900 py-5">
        <p class="text-zinc-500 text-xs uppercase tracking-widest mb-1">Preço</p>
        <p class="text-4xl md:text-5xl font-bold text-yellow-400">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
        <p class="text-zinc-500 text-xs mt-1">ou 3x sem juros</p>
      </div>

      <div>
        <p class="text-zinc-500 text-xs uppercase tracking-widest mb-2">Descrição</p>
        <p class="text-zinc-300 leading-relaxed">
          <?= nl2br(htmlspecialchars($produto['descricao'] ?? 'Peça streetwear autêntica, com a essência da marca em cada detalhe.')) ?>
        </p>
      </div>

      <?php if (!empty($tamanhos)): ?>
      <div>
        <div class="flex items-center justify-between mb-3">
          <label class="text-zinc-500 text-xs uppercase tracking-widest">Tamanho</label>
          <a href="#" class="text-zinc-500 text-xs hover:text-yellow-400 underline">Tabela de medidas</a>
        </div>
        <div class="flex flex-wrap gap-2" id="seletor-tamanhos">
          <?php foreach ($tamanhos as $t): ?>
            <button type="button" data-tam="<?= $t['id'] ?>"
              class="opt-tam min-w-[3rem] px-4 py-3 border border-zinc-800 hover:border-yellow-400 text-white font-bold rounded-lg transition">
              <?= htmlspecialchars($t['descricao']) ?>
            </button>
          <?php endforeach; ?>
        </div>
        <input type="hidden" id="tamanho" value="">
      </div>
      <?php endif; ?>

      <div class="flex flex-col sm:flex-row gap-3 pt-4">
        <button id="btn-adicionar-carrinho"
          class="flex-1 bg-yellow-400 hover:bg-yellow-300 text-black py-4 rounded-lg font-bold uppercase tracking-wider transition shadow-lg shadow-yellow-400/10 inline-flex items-center justify-center gap-2">
          <i class="fa-solid fa-bag-shopping"></i> Adicionar ao Carrinho
        </button>

        <button type="button" id="btn-favoritar"
          class="w-14 h-14 sm:w-auto sm:px-5 flex items-center justify-center rounded-lg border transition
          <?= $isFavoritado ? 'border-pink-500 text-pink-500 bg-pink-500/10' : 'border-zinc-800 text-zinc-400 hover:border-pink-500 hover:text-pink-500' ?>"
          data-id="<?= $produto['id'] ?>"
          data-acao="<?= $isFavoritado ? 'remover' : 'adicionar' ?>"
          title="Favoritar">
          <i class="fa-solid fa-heart text-lg"></i>
        </button>
      </div>

      <button id="btn-comprar-agora"
        class="w-full bg-black border-2 border-white hover:border-yellow-400 hover:text-yellow-400 text-white py-4 rounded-lg font-bold uppercase tracking-wider transition inline-flex items-center justify-center gap-2">
        <i class="fa-solid fa-bolt"></i> Comprar Agora
      </button>

      <div class="grid grid-cols-3 gap-3 pt-6">
        <div class="text-center p-4 bg-zinc-950 border border-zinc-900 rounded-lg">
          <i class="fa-solid fa-truck-fast text-yellow-400 text-xl mb-1"></i>
          <p class="text-[10px] text-zinc-400 uppercase tracking-wider mt-1">Frete rápido</p>
        </div>
        <div class="text-center p-4 bg-zinc-950 border border-zinc-900 rounded-lg">
          <i class="fa-solid fa-rotate-left text-yellow-400 text-xl mb-1"></i>
          <p class="text-[10px] text-zinc-400 uppercase tracking-wider mt-1">Troca fácil</p>
        </div>
        <div class="text-center p-4 bg-zinc-950 border border-zinc-900 rounded-lg">
          <i class="fa-solid fa-shield-halved text-yellow-400 text-xl mb-1"></i>
          <p class="text-[10px] text-zinc-400 uppercase tracking-wider mt-1">Compra segura</p>
        </div>
      </div>
    </div>
  </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const swalDark = { background: '#18181b', color: '#fff', confirmButtonColor: '#facc15' };

$(document).ready(function () {
  $('.opt-tam').click(function () {
    $('.opt-tam').removeClass('bg-yellow-400 text-black border-yellow-400').addClass('border-zinc-800 text-white');
    $(this).removeClass('border-zinc-800 text-white').addClass('bg-yellow-400 text-black border-yellow-400');
    $('#tamanho').val($(this).data('tam'));
  });

  $('#btn-favoritar').click(function () {
    const btn = $(this);
    const acao = btn.data('acao');
    $.post('../backend/favoritar.php', { produto_id: btn.data('id'), acao }, function () {
      if (acao === 'adicionar') {
        btn.removeClass('border-zinc-800 text-zinc-400 hover:border-pink-500 hover:text-pink-500')
           .addClass('border-pink-500 text-pink-500 bg-pink-500/10').data('acao', 'remover');
      } else {
        btn.removeClass('border-pink-500 text-pink-500 bg-pink-500/10')
           .addClass('border-zinc-800 text-zinc-400 hover:border-pink-500 hover:text-pink-500').data('acao', 'adicionar');
      }
      atualizarBadges();
    }).fail(function () {
      Swal.fire({ icon: 'error', title: 'Faça login', text: 'Você precisa estar logado para favoritar.', ...swalDark });
    });
  });

  $('#btn-adicionar-carrinho').click(function () {
    const tamanhoId = $('#tamanho').val();
    <?php if (!empty($tamanhos)): ?>
    if (!tamanhoId) {
      Swal.fire({ icon: 'warning', title: 'Escolha um tamanho', text: 'Selecione um tamanho antes de continuar.', ...swalDark });
      return;
    }
    <?php endif; ?>
    $.post('../backend/adicionar_ao_carrinho.php', { produto_id: <?= $produto['id'] ?>, tamanho_id: tamanhoId }, function () {
      Swal.fire({ icon: 'success', title: 'Adicionado ao carrinho!', toast: true, position: 'top-end', timer: 1800, showConfirmButton: false, ...swalDark });
      atualizarBadges();
    }).fail(function () {
      Swal.fire({ icon: 'error', title: 'Erro', text: 'Faça login para comprar.', ...swalDark });
    });
  });

  $('#btn-comprar-agora').click(function () {
    const tamanhoId = $('#tamanho').val();
    <?php if (!empty($tamanhos)): ?>
    if (!tamanhoId) {
      Swal.fire({ icon: 'warning', title: 'Escolha um tamanho', text: 'Selecione um tamanho antes de continuar.', ...swalDark });
      return;
    }
    <?php endif; ?>
    window.location.href = `finalizar_pedido.php?produto_id=<?= $produto['id'] ?>${tamanhoId ? '&tamanho_id=' + tamanhoId : ''}`;
  });
});
</script>

<?php include '../includes/footer.php'; ?>
