<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../views/imagens/logo.png">
  <title>Painel Admin · DURK</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <style>
    html, body { background: #0a0a0a; color: #fafafa; font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
    body { min-height: 100vh; display: flex; flex-direction: column; }
    main { flex: 1; }
    .font-display { font-family: 'Anton', sans-serif; letter-spacing: 0.02em; }
    ::-webkit-scrollbar { width: 10px; height: 10px; }
    ::-webkit-scrollbar-track { background: #0a0a0a; }
    ::-webkit-scrollbar-thumb { background: #27272a; border-radius: 8px; }
    ::-webkit-scrollbar-thumb:hover { background: #facc15; }
  </style>
</head>

<body>
<?php $current = basename($_SERVER['PHP_SELF']); ?>
<header class="bg-black border-b border-zinc-900 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-4 flex items-center justify-between gap-4">
    <div class="flex items-center gap-6">
      <a href="index.php" class="flex items-center gap-3">
        <img src="../views/imagens/logo.png" alt="Durk" class="h-9 w-auto">
        <div class="leading-none">
          <p class="font-display text-2xl text-white tracking-wider">DURK<span class="text-yellow-400">.</span></p>
          <p class="text-[9px] text-yellow-400 uppercase tracking-[0.3em] font-bold mt-0.5">Painel Admin</p>
        </div>
      </a>
      <nav class="hidden lg:flex items-center gap-1 ml-4">
        <?php
        $links = [
          'index.php'    => ['Dashboard', 'fa-gauge-high'],
          'produtos.php' => ['Produtos',  'fa-shirt'],
          'pedidos.php'  => ['Pedidos',   'fa-box'],
          'users.php'    => ['Usuários',  'fa-users'],
          'filtros.php'  => ['Filtros',   'fa-tags'],
        ];
        foreach ($links as $url => [$label, $icon]):
          $active = (basename($url) === $current) ? 'bg-yellow-400 text-black' : 'text-zinc-300 hover:bg-zinc-900 hover:text-white';
        ?>
          <a href="<?= $url ?>" class="px-3 py-2 rounded-lg text-xs font-bold uppercase tracking-wider flex items-center gap-2 transition <?= $active ?>">
            <i class="fa-solid <?= $icon ?>"></i> <?= $label ?>
          </a>
        <?php endforeach; ?>
      </nav>
    </div>

    <div class="flex items-center gap-2">
      <a href="../views/index.php" class="hidden sm:inline-flex items-center gap-2 bg-zinc-900 hover:bg-zinc-800 text-white px-3 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Loja
      </a>
      <a href="../backend/logout.php" class="inline-flex items-center gap-2 bg-red-500/10 border border-red-500/30 hover:bg-red-500/20 text-red-400 px-3 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition">
        <i class="fa-solid fa-arrow-right-from-bracket"></i> Sair
      </a>
    </div>
  </div>

  <nav class="lg:hidden border-t border-zinc-900 bg-black overflow-x-auto">
    <div class="max-w-7xl mx-auto px-4 py-2 flex gap-1">
      <?php foreach ($links as $url => [$label, $icon]):
        $active = (basename($url) === $current) ? 'bg-yellow-400 text-black' : 'text-zinc-400 hover:text-white';
      ?>
        <a href="<?= $url ?>" class="px-3 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider flex items-center gap-1.5 whitespace-nowrap transition <?= $active ?>">
          <i class="fa-solid <?= $icon ?>"></i> <?= $label ?>
        </a>
      <?php endforeach; ?>
    </div>
  </nav>
</header>
