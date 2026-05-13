<?php
include '../includes/header.php';
include '../backend/db.php';

$termo = isset($_GET['q']) ? trim($_GET['q']) : '';
?>

<title>Busca: <?= htmlspecialchars($termo) ?> · DURK</title>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-10 min-h-[60vh]">

  <div class="mb-10">
    <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Busca</span>
    <h1 class="font-display text-4xl md:text-5xl text-white mt-2">
      RESULTADOS PARA <span class="text-yellow-400">"<?= htmlspecialchars($termo) ?>"</span>
    </h1>
  </div>

  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
    <?php
    if ($termo) {
      $stmt = $conn->prepare("
        SELECT p.id, p.nome, p.preco, c.nome AS categoria,
               (SELECT MIN(ip.url_imagem) FROM imagens_produto ip WHERE ip.produto_id = p.id) AS imagem
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE p.nome LIKE ? OR p.descricao LIKE ?
        ORDER BY p.id DESC
      ");
      $like = "%" . $termo . "%";
      $stmt->bind_param("ss", $like, $like);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        while ($p = $result->fetch_assoc()) {
          ?>
          <a href="produto_detalhes.php?id=<?= $p['id'] ?>" class="group block bg-zinc-950 border border-zinc-900 rounded-2xl overflow-hidden hover:border-yellow-400 transition-all duration-500">
            <div class="relative aspect-[4/5] bg-zinc-900 overflow-hidden">
              <?php if (!empty($p['imagem'])): ?>
                <img src="../<?= htmlspecialchars($p['imagem']) ?>" class="w-full h-full object-contain p-3 transition-transform duration-700 group-hover:scale-110">
              <?php else: ?>
                <div class="w-full h-full flex items-center justify-center text-zinc-700"><i class="fa-solid fa-image text-5xl"></i></div>
              <?php endif; ?>
            </div>
            <div class="p-4">
              <p class="text-yellow-400 text-[10px] uppercase tracking-widest font-bold mb-1"><?= htmlspecialchars($p['categoria'] ?? 'Streetwear') ?></p>
              <h3 class="text-sm font-bold text-white line-clamp-2 mb-2 min-h-[2.5rem]"><?= htmlspecialchars($p['nome']) ?></h3>
              <p class="text-lg font-bold text-white">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
            </div>
          </a>
          <?php
        }
      } else {
        echo '<div class="col-span-full text-center py-20"><i class="fa-solid fa-magnifying-glass text-6xl text-zinc-800 mb-4"></i><p class="text-zinc-500 text-lg">Nenhum produto encontrado.</p><a href="vestuario.php" class="inline-block mt-6 text-yellow-400 hover:underline uppercase tracking-widest text-sm font-bold">Ver catálogo completo</a></div>';
      }
      $stmt->close();
    } else {
      echo '<div class="col-span-full text-center py-20"><p class="text-zinc-500">Digite algo na busca para encontrar peças.</p></div>';
    }
    $conn->close();
    ?>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
