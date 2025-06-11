<?php include '../includes/header.php'; ?>
<?php include '../backend/db.php'; ?>
<?php
if (!isset($_GET['id'])) {
  echo "<p>Produto não especificado.</p>";
  exit;
}
$id = intval($_GET['id']);
$query = "
  SELECT p.*, c.nome AS categoria, m.nome AS marca
  FROM produtos p
  JOIN categorias c ON p.categoria_id = c.id
  JOIN marcas m ON p.marca_id = m.id
  WHERE p.id = $id
";
$result = mysqli_query($conn, $query);
$produto = mysqli_fetch_assoc($result);
if (!$produto) {
  echo "<p>Produto não encontrado.</p>";
  exit;
}
$imagens = mysqli_query($conn, "SELECT url_imagem FROM imagens_produto WHERE produto_id = $id");
?>
<?php
include '../includes/verifica_login.php';

$isFavoritado = false;
if (isset($_SESSION['usuario_id'])) {
  $userId = $_SESSION['usuario_id'];
  $check = $conn->prepare("SELECT 1 FROM favoritos WHERE user_id = ? AND produto_id = ?");
  $check->bind_param("ii", $userId, $id);
  $check->execute();
  $resultCheck = $check->get_result();
  $isFavoritado = $resultCheck->num_rows > 0;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($produto['nome']); ?> | Loja Durk</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    .scrollbar-hide::-webkit-scrollbar {
      display: none;
    }
    .scrollbar-hide {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
  </style>
</head>

<body class="bg-gray-50">
  <main class="max-w-7xl mx-auto px-6 py-10 animate__animated animate__fadeIn">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
      <div class="space-y-4" data-aos="fade-right">
        <?php if ($img = mysqli_fetch_assoc($imagens)) {
          echo '<img src="../' . htmlspecialchars($img['url_imagem']) . '" class="rounded-xl w-full max-h-[480px] object-contain bg-white shadow" alt="Imagem do produto">';
        }
        echo '<div class="flex gap-3 overflow-x-auto scrollbar-hide">';
        while ($img = mysqli_fetch_assoc($imagens)) {
          echo '<img src="../' . htmlspecialchars($img['url_imagem']) . '" class="w-20 h-20 object-contain bg-white rounded-lg shadow cursor-pointer">';
        }
        echo '</div>'; ?>
      </div>

      <div class="space-y-6" data-aos="fade-left">
        <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($produto['nome']); ?></h1>
        <p class="text-sm text-gray-500">
          Categoria: <strong><?php echo htmlspecialchars($produto['categoria']); ?></strong> | Marca: <strong><?php echo htmlspecialchars($produto['marca']); ?></strong>
        </p>
        <p class="text-4xl font-extrabold text-green-600">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
        <p class="text-gray-700 leading-relaxed">
          <?php echo nl2br(htmlspecialchars($produto['descricao'] ?? 'Produto sem descrição detalhada.')); ?>
        </p>

        <?php
$tamanhos = mysqli_query($conn, "
  SELECT t.id, t.descricao 
  FROM produto_tamanhos pt
  JOIN tamanhos t ON pt.tamanho_id = t.id
  WHERE pt.produto_id = $id
");
?>

<div class="flex flex-col gap-4 pt-4">
  <div>
    <label for="tamanho" class="block text-sm font-medium text-gray-700 mb-1">Selecione o tamanho</label>
    <select id="tamanho" class="w-full border border-gray-300 rounded px-3 py-2" required>
      <option value="">-- Escolha um tamanho --</option>
      <?php while ($t = mysqli_fetch_assoc($tamanhos)): ?>
        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['descricao']) ?></option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="flex gap-4 flex-wrap">
    <button id="btn-adicionar-carrinho"
      class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg text-lg font-semibold transition shadow">
      <i class="fa-solid fa-cart-shopping mr-2"></i> Adicionar ao carrinho
    </button>

    <button
      type="button"
      class="toggle-fav-detalhe flex items-center justify-center w-14 h-14 rounded-lg border transition text-xl 
      <?= $isFavoritado ? 'border-red-500 text-red-500 hover:text-gray-400 hover:border-gray-400' : 'border-gray-300 text-gray-400 hover:text-red-500 hover:border-red-500' ?>"
      data-id="<?= $produto['id'] ?>"
      data-acao="<?= $isFavoritado ? 'remover' : 'adicionar' ?>"
      title="Favoritar">
      <i class="fa-solid fa-heart"></i>
    </button>

    <button id="btn-comprar-agora" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg text-lg font-semibold transition shadow">
      <i class="fa-solid fa-bolt mr-2"></i> Comprar agora
    </button>
  </div>
</div>
      </div>
    </div>
  </main>
  <script>
    AOS.init();
  </script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
  $('.toggle-fav-detalhe').click(function() {
    const btn = $(this);
    const produtoId = btn.data('id');
    const acaoAtual = btn.data('acao');

    $.post('../backend/favoritar.php', {
      produto_id: produtoId,
      acao: acaoAtual
    }, function() {
      if (acaoAtual === 'adicionar') {
        btn
          .removeClass('border-gray-300 text-gray-400')
          .addClass('border-red-500 text-red-500')
          .data('acao', 'remover');
      } else {
        btn
          .removeClass('border-red-500 text-red-500')
          .addClass('border-gray-300 text-gray-400')
          .data('acao', 'adicionar');
      }
      atualizarBadges();
    }).fail(function() {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Erro ao processar favorito. Faça login novamente.',
      });
    });
  });

  $('#btn-adicionar-carrinho').click(function () {
    const tamanhoId = $('#tamanho').val();
    const produtoId = <?= $produto['id'] ?>;

    if (!tamanhoId) {
      Swal.fire({
        icon: 'warning',
        title: 'Selecione o tamanho',
        text: 'Você precisa escolher um tamanho antes de adicionar ao carrinho.'
      });
      return;
    }

    $.post('../backend/adicionar_ao_carrinho.php', {
      produto_id: produtoId,
      tamanho_id: tamanhoId
    }, function () {
      Swal.fire({
        icon: 'success',
        title: 'Adicionado ao carrinho!',
        text: 'Seu produto foi adicionado com sucesso.',
        toast: true,
        position: 'top-end',
        timer: 2000,
        showConfirmButton: false
      });
      atualizarBadges();
    }).fail(function () {
      Swal.fire({
        icon: 'error',
        title: 'Erro',
        text: 'Não foi possível adicionar ao carrinho. Faça login novamente.'
      });
    });
  });
  $(document).ready(function () {
  $('#btn-comprar-agora').click(function () {
    const tamanhoId = $('#tamanho').val();
    const produtoId = <?= $produto['id'] ?>;

    if (!tamanhoId) {
      Swal.fire({
        icon: 'warning',
        title: 'Selecione o tamanho',
        text: 'Você precisa escolher um tamanho antes de comprar.'
      });
      return;
    }

    window.location.href = `finalizar_pedido.php?produto_id=${produtoId}&tamanho_id=${tamanhoId}`;
  });
});

});
  </script>

  <?php include '../includes/footer.php'; ?>
</body>

</html>