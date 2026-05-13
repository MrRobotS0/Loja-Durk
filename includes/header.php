<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../backend/db.php';

$qtdCarrinho = 0;
$qtdFavoritos = 0;

if (isset($_SESSION['usuario_id'])) {
  $userId = (int) $_SESSION['usuario_id'];

  $stmtCart = $conn->prepare("SELECT COALESCE(SUM(ic.quantidade), 0) AS total
                              FROM itens_carrinho ic
                              JOIN carrinhos c ON ic.carrinho_id = c.id
                              WHERE c.user_id = ?");
  $stmtCart->bind_param("i", $userId);
  $stmtCart->execute();
  $qtdCarrinho = (int) ($stmtCart->get_result()->fetch_assoc()['total'] ?? 0);
  $stmtCart->close();

  $stmtFav = $conn->prepare("SELECT COUNT(*) AS total FROM favoritos WHERE user_id = ?");
  $stmtFav->bind_param("i", $userId);
  $stmtFav->execute();
  $qtdFavoritos = (int) ($stmtFav->get_result()->fetch_assoc()['total'] ?? 0);
  $stmtFav->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="../views/imagens/logo.png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Bebas+Neue&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <style>
    :root {
      --durk-bg: #0a0a0a;
      --durk-surface: #18181b;
      --durk-accent: #facc15;
      --durk-accent-hover: #eab308;
    }
    html, body {
      background: var(--durk-bg);
      color: #fafafa;
      font-family: 'Inter', system-ui, sans-serif;
      -webkit-font-smoothing: antialiased;
    }
    body { min-height: 100vh; display: flex; flex-direction: column; }
    main { flex: 1; }
    .font-display { font-family: 'Anton', 'Bebas Neue', sans-serif; letter-spacing: 0.02em; }
    .font-graffiti { font-family: 'Bebas Neue', sans-serif; letter-spacing: 0.05em; }
    .durk-accent { color: var(--durk-accent); }
    .bg-durk-accent { background-color: var(--durk-accent); }
    .border-durk-accent { border-color: var(--durk-accent); }
    .text-stroke { -webkit-text-stroke: 1px var(--durk-accent); color: transparent; }
    ::-webkit-scrollbar { width: 10px; height: 10px; }
    ::-webkit-scrollbar-track { background: #0a0a0a; }
    ::-webkit-scrollbar-thumb { background: #27272a; border-radius: 8px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--durk-accent); }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .line-clamp-2 {
      display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2;
      -webkit-box-orient: vertical; overflow: hidden;
    }
    .glitch-hover:hover { animation: glitch 0.4s; }
    @keyframes glitch {
      0%, 100% { transform: translate(0); }
      20% { transform: translate(-2px, 2px); }
      40% { transform: translate(2px, -2px); }
      60% { transform: translate(-1px, 1px); }
      80% { transform: translate(1px, -1px); }
    }
    .marquee {
      display: flex; gap: 3rem; animation: marquee 30s linear infinite;
      white-space: nowrap; will-change: transform;
    }
    @keyframes marquee {
      from { transform: translateX(0); }
      to { transform: translateX(-50%); }
    }
  </style>
</head>

<body>

<div class="bg-durk-accent text-black text-xs font-bold overflow-hidden border-b border-black/20">
  <div class="marquee py-2 uppercase tracking-widest">
    <span>★ Frete grátis acima de R$ 299</span>
    <span>★ Novidades toda semana</span>
    <span>★ Streetwear autêntico do asfalto pra rua</span>
    <span>★ Drop limitado — corre que acaba</span>
    <span>★ Frete grátis acima de R$ 299</span>
    <span>★ Novidades toda semana</span>
    <span>★ Streetwear autêntico do asfalto pra rua</span>
    <span>★ Drop limitado — corre que acaba</span>
  </div>
</div>

<header class="bg-black/95 backdrop-blur border-b border-zinc-800 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 flex items-center justify-between h-20 relative">

    <div class="flex items-center gap-10">
      <a href="../views/index.php" class="flex items-center gap-3 group">
        <img src="../views/imagens/logo.png" alt="Logo Durk" class="h-11 w-auto transition group-hover:scale-110">
        <span class="font-display text-3xl text-white tracking-wider">DURK<span class="durk-accent">.</span></span>
      </a>
      <nav class="hidden md:flex gap-7 text-sm font-bold uppercase tracking-wider text-zinc-300">
        <a href="../views/index.php" class="hover:text-yellow-400 transition glitch-hover">Home</a>
        <a href="../views/vestuario.php" class="hover:text-yellow-400 transition glitch-hover flex items-center gap-1.5">
          <i class="fa-solid fa-shirt text-xs"></i> Vestuário
        </a>
        <a href="../views/sobre.php" class="hover:text-yellow-400 transition glitch-hover">Sobre</a>
        <a href="../views/suporte.php" class="hover:text-yellow-400 transition glitch-hover">Suporte</a>
      </nav>
    </div>

    <div class="flex items-center gap-3 sm:gap-4 relative">

      <button id="btnSearchToggle" class="text-zinc-300 hover:text-yellow-400 text-lg transition w-10 h-10 flex items-center justify-center rounded-full hover:bg-zinc-900">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>

      <form action="../views/busca.php" method="GET" id="searchPopup"
        class="hidden absolute top-14 right-0 bg-zinc-900 border-2 border-yellow-400 rounded-lg shadow-2xl p-3 flex items-center gap-2 z-50 w-72 md:w-80">
        <input type="text" name="q" placeholder="O que você procura?"
          class="flex-1 px-3 py-2 bg-black border border-zinc-700 rounded text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-yellow-400" required>
        <button type="submit" class="text-black bg-yellow-400 hover:bg-yellow-500 px-3 py-2 rounded text-sm font-bold transition">
          <i class="fa-solid fa-arrow-right"></i>
        </button>
      </form>

      <a href="../views/favoritos.php" title="Favoritos"
        class="text-zinc-300 hover:text-pink-500 text-lg transition relative w-10 h-10 flex items-center justify-center rounded-full hover:bg-zinc-900">
        <i class="fa-regular fa-heart"></i>
        <?php if ($qtdFavoritos > 0): ?>
          <span id="badge-favoritos" class="absolute -top-1 -right-1 bg-pink-500 text-white text-[10px] font-bold px-1.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full ring-2 ring-black">
            <?= $qtdFavoritos ?>
          </span>
        <?php endif; ?>
      </a>

      <a href="<?= isset($_SESSION['usuario_id']) ? '../views/minhaconta.php' : '../views/login.php' ?>" title="Minha Conta"
        class="text-zinc-300 hover:text-yellow-400 text-lg transition w-10 h-10 flex items-center justify-center rounded-full hover:bg-zinc-900">
        <i class="fa-regular fa-user"></i>
      </a>

      <a href="../views/carrinho.php" title="Carrinho"
        class="text-zinc-300 hover:text-yellow-400 text-lg transition relative w-10 h-10 flex items-center justify-center rounded-full hover:bg-zinc-900">
        <i class="fa-solid fa-bag-shopping"></i>
        <?php if ($qtdCarrinho > 0): ?>
          <span id="badge-carrinho" class="absolute -top-1 -right-1 bg-yellow-400 text-black text-[10px] font-bold px-1.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full ring-2 ring-black">
            <?= $qtdCarrinho ?>
          </span>
        <?php endif; ?>
      </a>

      <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['tipo_usuario'] ?? '') === 'admin'): ?>
        <a href="../admin/index.php"
          class="hidden sm:inline-flex bg-yellow-400 text-black px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-yellow-300 transition items-center gap-1.5">
          <i class="fa-solid fa-shield-halved"></i> Admin
        </a>
      <?php endif; ?>

      <?php if (isset($_SESSION['usuario_id'])): ?>
        <a href="../backend/logout.php" title="Sair"
          class="text-zinc-400 hover:text-red-500 text-lg transition w-10 h-10 flex items-center justify-center rounded-full hover:bg-zinc-900">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </a>
      <?php endif; ?>
    </div>
  </div>
</header>

<script>
  (function () {
    const btn = document.getElementById('btnSearchToggle');
    const popup = document.getElementById('searchPopup');
    if (!btn || !popup) return;
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      popup.classList.toggle('hidden');
      const input = popup.querySelector('input[name="q"]');
      if (!popup.classList.contains('hidden') && input) input.focus();
    });
    document.addEventListener('click', (e) => {
      if (!btn.contains(e.target) && !popup.contains(e.target)) {
        popup.classList.add('hidden');
      }
    });
  })();

  function atualizarBadges() {
    fetch('../backend/get_badges.php')
      .then(r => r.json())
      .then(data => {
        const updateBadge = (selector, count, bg, fg) => {
          const link = document.querySelector(selector);
          if (!link) return;
          let badge = link.querySelector('span');
          if (count > 0) {
            if (!badge) {
              badge = document.createElement('span');
              badge.className = `absolute -top-1 -right-1 ${bg} ${fg} text-[10px] font-bold px-1.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full ring-2 ring-black`;
              link.appendChild(badge);
            }
            badge.innerText = count;
          } else if (badge) {
            badge.remove();
          }
        };
        updateBadge('a[title="Favoritos"]', data.favoritos, 'bg-pink-500', 'text-white');
        updateBadge('a[title="Carrinho"]', data.carrinho, 'bg-yellow-400', 'text-black');
      })
      .catch(() => {});
  }
</script>
