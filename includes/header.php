<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once '../backend/db.php';

$qtdCarrinho = 0;
if (isset($_SESSION['usuario_id'])) {
  $userId = $_SESSION['usuario_id'];
  $sqlQtd = "SELECT SUM(quantidade) AS total FROM itens_carrinho ic
             JOIN carrinhos c ON ic.carrinho_id = c.id
             WHERE c.user_id = $userId";
  $resQtd = mysqli_query($conn, $sqlQtd);
  $qtdCarrinho = mysqli_fetch_assoc($resQtd)['total'] ?? 0;
}
$qtdFavoritos = 0;
if (isset($_SESSION['usuario_id'])) {
  $sqlFav = "SELECT COUNT(*) AS total FROM favoritos WHERE user_id = $userId";
  $resFav = mysqli_query($conn, $sqlFav);
  $qtdFavoritos = mysqli_fetch_assoc($resFav)['total'] ?? 0;
}

?>

<head>
  <link rel="icon" type="image/png" href="../views/imagens/logo.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<header class="bg-white border-b border-gray-200 shadow-md sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 lg:px-10 flex items-center justify-between h-20 relative">

    <div class="flex items-center gap-10">
      <a href="../views/index.php" class="flex items-center gap-2">
        <img src="../views/imagens/logo.png" alt="Logo Durk" class="h-12 w-auto">
        <span class="text-xl font-bold text-gray-800 hidden md:inline">Durk</span>
      </a>
      <nav class="hidden sm:flex gap-8 text-base font-semibold text-gray-700">
        <a href="../views/vestuario.php" class="hover:text-purple-600 transition flex items-center gap-1"><i class="fa-solid fa-shirt"></i> Vestuário</a>
      </nav>
    </div>

    <div class="flex items-center gap-4 relative">

      <button id="btnSearchToggle" class="text-gray-600 hover:text-black text-xl transition">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>

      <form action="../views/busca.php" method="GET" id="searchPopup"
        class="hidden absolute top-14 -right-10 md:right-0 bg-white border border-gray-300 rounded-lg shadow-lg p-3 flex items-center gap-2 z-50 w-72 transition-all">
        <input type="text" name="q" placeholder="Buscar..." class="flex-1 px-3 py-2 border rounded text-sm focus:outline-none focus:ring-2 focus:ring-purple-500" required>
        <button type="submit" class="text-white bg-purple-600 hover:bg-purple-700 px-3 py-2 rounded text-sm">
          <i class="fa-solid fa-search"></i>
        </button>
      </form>

      <a href="../views/favoritos.php" title="Favoritos"
        class="text-gray-600 hover:text-pink-600 text-xl transition relative">
        <i class="fa-regular fa-heart"></i>
        <?php if ($qtdFavoritos > 0): ?>
          <span id="badge-favoritos" class="absolute -top-2 -right-2 bg-pink-600 text-white text-xs font-semibold px-1.5 py-0.5 rounded-full">
            <?= $qtdFavoritos ?>
          </span>
        <?php endif; ?>
      </a>


      <a href="<?= isset($_SESSION['usuario_id']) ? '../views/minhaconta.php' : '../views/login.php' ?>" title="Minha Conta"
        class="text-gray-600 hover:text-blue-600 text-xl transition"><i class="fa-regular fa-user"></i></a>

      <a href="../views/carrinho.php" title="Carrinho"
        class="text-gray-600 hover:text-green-600 text-xl transition relative">
        <i class="fa-solid fa-cart-shopping"></i>
        <?php if ($qtdCarrinho > 0): ?>
          <span id="badge-carrinho" class="absolute -top-2 -right-2 bg-green-600 text-white text-xs font-semibold px-1.5 py-0.5 rounded-full">
            <?= $qtdCarrinho ?>
          </span>

        <?php endif; ?>
      </a>

      <?php if (isset($_SESSION['usuario_id']) && $_SESSION['tipo_usuario'] === 'admin'): ?>
        <a href="../admin/index.php"
          class="bg-gray-900 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-gray-700 transition font-semibold">
          Painel Admin
        </a>
      <?php endif; ?>

      <?php if (isset($_SESSION['usuario_id'])): ?>
        <a href="../backend/logout.php" title="Sair"
          class="text-gray-600 hover:text-red-600 text-xl transition"><i class="fa-solid fa-arrow-right-from-bracket"></i></a>
      <?php endif; ?>
    </div>
  </div>
</header>

<script>
  const btnSearchToggle = document.getElementById('btnSearchToggle');
  const searchPopup = document.getElementById('searchPopup');

  btnSearchToggle.addEventListener('click', () => {
    searchPopup.classList.toggle('hidden');
  });

  document.addEventListener('click', (e) => {
    if (!btnSearchToggle.contains(e.target) && !searchPopup.contains(e.target)) {
      searchPopup.classList.add('hidden');
    }
  });
</script>
<script>
  function atualizarBadges() {
    fetch('../backend/get_badges.php')
      .then(response => response.json())
      .then(data => {
        const favBadge = document.getElementById('badge-favoritos');
        if (data.favoritos > 0) {
          if (!favBadge) {
            const novoBadge = document.createElement('span');
            novoBadge.id = 'badge-favoritos';
            novoBadge.className = 'absolute -top-2 -right-2 bg-pink-600 text-white text-xs font-semibold px-1.5 py-0.5 rounded-full';
            novoBadge.innerText = data.favoritos;
            document.querySelector('a[title="Favoritos"]').appendChild(novoBadge);
          } else {
            favBadge.innerText = data.favoritos;
          }
        } else if (favBadge) {
          favBadge.remove();
        }

        const cartBadge = document.getElementById('badge-carrinho');
        if (data.carrinho > 0) {
          if (!cartBadge) {
            const novoBadge = document.createElement('span');
            novoBadge.id = 'badge-carrinho';
            novoBadge.className = 'absolute -top-2 -right-2 bg-green-600 text-white text-xs font-semibold px-1.5 py-0.5 rounded-full';
            novoBadge.innerText = data.carrinho;
            document.querySelector('a[title="Carrinho"]').appendChild(novoBadge);
          } else {
            cartBadge.innerText = data.carrinho;
          }
        } else if (cartBadge) {
          cartBadge.remove();
        }
      });
  }
</script>
