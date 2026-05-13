<?php
include 'db.php';

$query = "SELECT p.id, p.nome, p.descricao, p.preco,
                 (SELECT MIN(ip.url_imagem) FROM imagens_produto ip WHERE ip.produto_id = p.id) AS imagem
          FROM produtos p
          ORDER BY p.id DESC
          LIMIT 4";
$result = mysqli_query($conn, $query);
?>

<section class="bg-black py-16">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="font-display text-4xl text-white mb-8 text-center">NOVIDADES</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php if ($result && mysqli_num_rows($result) > 0):
        while ($p = mysqli_fetch_assoc($result)): ?>
          <a href="../views/produto_detalhes.php?id=<?= $p['id'] ?>" class="group bg-zinc-950 border border-zinc-900 hover:border-yellow-400 rounded-2xl overflow-hidden transition">
            <div class="aspect-square bg-zinc-900 p-4">
              <?php if (!empty($p['imagem'])): ?>
                <img src="../<?= htmlspecialchars($p['imagem']) ?>" class="w-full h-full object-contain transition-transform group-hover:scale-110" />
              <?php else: ?>
                <div class="w-full h-full flex items-center justify-center text-zinc-700"><i class="fa-solid fa-image text-5xl"></i></div>
              <?php endif; ?>
            </div>
            <div class="p-4">
              <h3 class="text-white font-bold text-sm line-clamp-2 mb-2"><?= htmlspecialchars($p['nome']) ?></h3>
              <p class="text-yellow-400 font-bold">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
            </div>
          </a>
        <?php endwhile;
      else: ?>
        <p class="col-span-full text-zinc-500 text-center">Nenhum produto encontrado.</p>
      <?php endif; ?>
    </div>
  </div>
</section>
