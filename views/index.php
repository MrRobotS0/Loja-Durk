<?php
include '../includes/header.php';
include '../backend/db.php';

$destaques = $conn->query("
  SELECT p.id, p.nome, p.preco, c.nome AS categoria, MIN(ip.url_imagem) AS imagem
  FROM produtos p
  LEFT JOIN categorias c ON p.categoria_id = c.id
  LEFT JOIN imagens_produto ip ON p.id = ip.produto_id
  GROUP BY p.id
  ORDER BY p.id DESC
  LIMIT 6
");
?>

<title>DURK · Streetwear Underground</title>

<section class="relative w-full overflow-hidden bg-black">
  <div class="absolute inset-0">
    <img src="imagens/banner.png" alt="" class="w-full h-full object-cover opacity-50">
    <div class="absolute inset-0 bg-gradient-to-r from-black via-black/70 to-transparent"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
  </div>

  <div class="relative max-w-7xl mx-auto px-6 lg:px-10 py-24 lg:py-36 flex flex-col items-start">
    <span class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-400 text-black text-xs font-bold uppercase tracking-widest rounded-full mb-6 animate-pulse">
      <span class="w-2 h-2 bg-black rounded-full"></span> Novo Drop Online
    </span>
    <h1 class="font-display text-6xl sm:text-7xl lg:text-9xl text-white leading-[0.9] mb-6">
      DO ASFALTO<br>
      <span class="text-yellow-400">PRA RUA.</span>
    </h1>
    <p class="text-lg lg:text-xl text-zinc-300 max-w-xl mb-10 leading-relaxed">
      Roupas com alma de quebrada. Cultura, atitude e estilo em cada peça <span class="text-yellow-400 font-semibold">underground</span>.
    </p>
    <div class="flex flex-wrap gap-4">
      <a href="vestuario.php" class="group inline-flex items-center gap-3 bg-yellow-400 hover:bg-yellow-300 text-black px-8 py-4 rounded-lg font-bold uppercase tracking-wider transition shadow-2xl shadow-yellow-400/20">
        Ver Catálogo
        <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
      </a>
      <a href="sobre.php" class="inline-flex items-center gap-3 bg-transparent border-2 border-white hover:border-yellow-400 hover:text-yellow-400 text-white px-8 py-4 rounded-lg font-bold uppercase tracking-wider transition">
        A Marca
      </a>
    </div>
  </div>

  <div class="absolute top-1/2 -translate-y-1/2 right-6 hidden xl:flex flex-col items-center gap-4 text-zinc-500 uppercase text-xs tracking-[0.4em]" style="writing-mode: vertical-rl;">
    <span>Drop 01</span>
    <span class="w-px h-12 bg-yellow-400"></span>
    <span>SS<?= date('y') ?></span>
  </div>
</section>

<section class="bg-black border-y border-zinc-900">
  <div class="max-w-7xl mx-auto px-6 lg:px-10 py-14 grid grid-cols-2 md:grid-cols-4 gap-6">
    <div class="flex items-center gap-4">
      <i class="fa-solid fa-truck-fast text-3xl text-yellow-400"></i>
      <div>
        <p class="font-bold text-white text-sm uppercase tracking-wider">Frete Rápido</p>
        <p class="text-zinc-500 text-xs">Brasil inteiro</p>
      </div>
    </div>
    <div class="flex items-center gap-4">
      <i class="fa-solid fa-shield-halved text-3xl text-yellow-400"></i>
      <div>
        <p class="font-bold text-white text-sm uppercase tracking-wider">Pagamento</p>
        <p class="text-zinc-500 text-xs">100% seguro</p>
      </div>
    </div>
    <div class="flex items-center gap-4">
      <i class="fa-solid fa-rotate-left text-3xl text-yellow-400"></i>
      <div>
        <p class="font-bold text-white text-sm uppercase tracking-wider">Trocas</p>
        <p class="text-zinc-500 text-xs">Até 30 dias</p>
      </div>
    </div>
    <div class="flex items-center gap-4">
      <i class="fa-solid fa-fire text-3xl text-yellow-400"></i>
      <div>
        <p class="font-bold text-white text-sm uppercase tracking-wider">Drops</p>
        <p class="text-zinc-500 text-xs">Edição limitada</p>
      </div>
    </div>
  </div>
</section>

<main class="bg-black">
  <section class="max-w-7xl mx-auto px-6 lg:px-10 py-20">
    <div class="flex items-end justify-between mb-12 flex-wrap gap-4">
      <div>
        <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Coleção</span>
        <h2 class="font-display text-5xl md:text-6xl text-white mt-2">NOVOS LANÇAMENTOS</h2>
      </div>
      <a href="vestuario.php" class="text-zinc-400 hover:text-yellow-400 uppercase text-sm tracking-widest font-bold transition flex items-center gap-2">
        Ver tudo <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php if ($destaques && $destaques->num_rows > 0):
        while ($p = $destaques->fetch_assoc()): ?>
        <a href="produto_detalhes.php?id=<?= $p['id'] ?>" class="group block bg-zinc-950 border border-zinc-900 rounded-2xl overflow-hidden hover:border-yellow-400 transition-all duration-500">
          <div class="relative aspect-[4/5] bg-zinc-900 overflow-hidden">
            <?php if (!empty($p['imagem'])): ?>
              <img src="../<?= htmlspecialchars($p['imagem']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>" class="w-full h-full object-contain p-4 transition-transform duration-700 group-hover:scale-110">
            <?php else: ?>
              <div class="w-full h-full flex items-center justify-center text-zinc-700">
                <i class="fa-solid fa-image text-6xl"></i>
              </div>
            <?php endif; ?>
            <span class="absolute top-4 left-4 bg-yellow-400 text-black text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">Novo</span>
          </div>
          <div class="p-6">
            <p class="text-yellow-400 text-xs uppercase tracking-widest font-bold mb-2"><?= htmlspecialchars($p['categoria'] ?? 'Streetwear') ?></p>
            <h3 class="text-lg font-bold text-white line-clamp-2 mb-3 min-h-[3.5rem] group-hover:text-yellow-400 transition"><?= htmlspecialchars($p['nome']) ?></h3>
            <div class="flex items-center justify-between">
              <p class="text-2xl font-bold text-white">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
              <span class="w-10 h-10 bg-zinc-900 group-hover:bg-yellow-400 group-hover:text-black text-white rounded-full flex items-center justify-center transition">
                <i class="fa-solid fa-arrow-right"></i>
              </span>
            </div>
          </div>
        </a>
        <?php endwhile;
      else: ?>
        <p class="col-span-full text-center text-zinc-500 py-16">Em breve, novos drops por aqui.</p>
      <?php endif; ?>
    </div>
  </section>

  <section class="relative bg-zinc-950 border-y border-zinc-900 py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-5 pointer-events-none">
      <p class="font-display text-[18rem] leading-none text-yellow-400 whitespace-nowrap">UNDERGROUND</p>
    </div>
    <div class="relative max-w-5xl mx-auto px-6 text-center">
      <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Manifesto</span>
      <h2 class="font-display text-5xl md:text-7xl text-white mt-4 mb-6 leading-tight">
        NÃO É SÓ ROUPA.<br>
        <span class="text-stroke">É IDENTIDADE.</span>
      </h2>
      <p class="text-zinc-400 text-lg leading-relaxed max-w-2xl mx-auto mb-8">
        A gente veste quem representa. Quem vive o corre, faz arte e não baixa a cabeça.
        Cada peça é uma página da cultura urbana que pulsa nas ruas.
      </p>
      <a href="sobre.php" class="inline-flex items-center gap-3 text-yellow-400 hover:text-white border-b-2 border-yellow-400 hover:border-white pb-1 font-bold uppercase tracking-widest transition">
        Conheça a marca <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>
  </section>

  <section class="max-w-7xl mx-auto px-6 lg:px-10 py-20">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <a href="vestuario.php?genero=masculino" class="group relative aspect-[4/5] md:aspect-square overflow-hidden rounded-2xl bg-zinc-900 border border-zinc-800 hover:border-yellow-400 transition">
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/30 to-transparent z-10"></div>
        <img src="imagens/oversizeddk.png" alt="" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
        <div class="absolute bottom-0 left-0 right-0 p-6 z-20">
          <p class="text-yellow-400 text-xs uppercase tracking-widest font-bold">Categoria</p>
          <h3 class="font-display text-4xl text-white mt-1">MASCULINO</h3>
        </div>
      </a>
      <a href="vestuario.php?genero=feminino" class="group relative aspect-[4/5] md:aspect-square overflow-hidden rounded-2xl bg-zinc-900 border border-zinc-800 hover:border-yellow-400 transition">
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/30 to-transparent z-10"></div>
        <img src="imagens/techfleece.png" alt="" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
        <div class="absolute bottom-0 left-0 right-0 p-6 z-20">
          <p class="text-yellow-400 text-xs uppercase tracking-widest font-bold">Categoria</p>
          <h3 class="font-display text-4xl text-white mt-1">FEMININO</h3>
        </div>
      </a>
      <a href="vestuario.php?genero=unissex" class="group relative aspect-[4/5] md:aspect-square overflow-hidden rounded-2xl bg-zinc-900 border border-zinc-800 hover:border-yellow-400 transition">
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/30 to-transparent z-10"></div>
        <img src="imagens/cintodk.png" alt="" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
        <div class="absolute bottom-0 left-0 right-0 p-6 z-20">
          <p class="text-yellow-400 text-xs uppercase tracking-widest font-bold">Categoria</p>
          <h3 class="font-display text-4xl text-white mt-1">UNISSEX</h3>
        </div>
      </a>
    </div>
  </section>

  <section class="max-w-7xl mx-auto px-6 lg:px-10 py-20 mb-10">
    <div class="bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-3xl p-10 md:p-16 relative overflow-hidden">
      <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
        <i class="fa-solid fa-envelope-open-text text-[20rem] text-black"></i>
      </div>
      <div class="relative grid md:grid-cols-2 gap-8 items-center">
        <div>
          <h3 class="font-display text-4xl md:text-5xl text-black leading-tight">FICA POR DENTRO DOS PRÓXIMOS DROPS</h3>
          <p class="text-black/80 mt-4 text-lg">Entra na lista e ganha 10% off na primeira compra. Sem spam, só novidades.</p>
        </div>
        <form class="flex flex-col sm:flex-row gap-3" onsubmit="event.preventDefault(); this.querySelector('button').innerText='✓ Inscrito';">
          <input type="email" required placeholder="Seu melhor email" class="flex-1 px-5 py-4 rounded-lg bg-black text-white placeholder-zinc-500 border-2 border-black focus:outline-none focus:border-zinc-800">
          <button type="submit" class="bg-black text-yellow-400 hover:bg-zinc-900 px-7 py-4 rounded-lg font-bold uppercase tracking-wider transition">Inscrever</button>
        </form>
      </div>
    </div>
  </section>
</main>

<?php include '../includes/footer.php'; ?>
