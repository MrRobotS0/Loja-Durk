<?php
include '../includes/verifica_login.php';
include '../includes/header.php';
include '../backend/db.php';

$userId = (int) $_SESSION['usuario_id'];

$stmt = $conn->prepare("
  SELECT p.id, p.nome, p.preco, c.nome AS categoria, MIN(ip.url_imagem) AS imagem
  FROM favoritos f
  INNER JOIN produtos p ON f.produto_id = p.id
  LEFT JOIN categorias c ON p.categoria_id = c.id
  LEFT JOIN imagens_produto ip ON p.id = ip.produto_id
  WHERE f.user_id = ?
  GROUP BY p.id
  ORDER BY p.nome ASC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<title>Favoritos · DURK</title>

<main class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10 py-10 min-h-[60vh]">

  <div class="mb-10">
    <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Lista</span>
    <h1 class="font-display text-5xl md:text-6xl text-white mt-2">FAVORITOS <i class="fa-solid fa-heart text-pink-500 text-3xl align-middle"></i></h1>
    <p class="text-zinc-400 mt-2">Os drops que tocaram seu coração.</p>
  </div>

  <section class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
    <?php if ($result->num_rows > 0):
      while ($produto = $result->fetch_assoc()): ?>
      <div class="group bg-zinc-950 border border-zinc-900 rounded-2xl overflow-hidden hover:border-yellow-400 transition-all duration-500 flex flex-col relative">
        <form action="../backend/favoritar.php" method="POST" class="absolute top-3 right-3 z-10">
          <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
          <button type="submit" name="acao" value="remover" title="Remover dos favoritos"
            class="w-9 h-9 bg-black/70 backdrop-blur rounded-full flex items-center justify-center text-pink-500 hover:text-red-500 transition">
            <i class="fa-solid fa-heart text-sm"></i>
          </button>
        </form>
        <a href="produto_detalhes.php?id=<?= $produto['id'] ?>" class="block relative aspect-[4/5] bg-zinc-900 overflow-hidden">
          <?php if (!empty($produto['imagem'])): ?>
            <img src="../<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" class="w-full h-full object-contain p-3 transition-transform duration-700 group-hover:scale-110">
          <?php else: ?>
            <div class="w-full h-full flex items-center justify-center text-zinc-700"><i class="fa-solid fa-image text-5xl"></i></div>
          <?php endif; ?>
        </a>
        <div class="p-4 flex flex-col flex-grow">
          <p class="text-yellow-400 text-[10px] uppercase tracking-widest font-bold mb-1"><?= htmlspecialchars($produto['categoria'] ?? 'Streetwear') ?></p>
          <h3 class="text-sm font-bold text-white line-clamp-2 mb-3 min-h-[2.5rem] group-hover:text-yellow-400 transition"><?= htmlspecialchars($produto['nome']) ?></h3>
          <div class="mt-auto flex items-center justify-between gap-2">
            <p class="text-lg font-bold text-white">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
            <a href="produto_detalhes.php?id=<?= $produto['id'] ?>" class="bg-zinc-900 hover:bg-yellow-400 hover:text-black text-white text-[10px] font-bold uppercase tracking-wider px-3 py-2 rounded-lg transition">
              Ver
            </a>
          </div>
        </div>
      </div>
      <?php endwhile;
    else: ?>
      <div class="col-span-full text-center py-20">
        <i class="fa-regular fa-heart text-6xl text-zinc-800 mb-4"></i>
        <p class="text-zinc-500 text-lg mb-2">Sua lista de favoritos está vazia.</p>
        <p class="text-zinc-600 text-sm mb-8">Salva os drops que você curte clicando no ❤.</p>
        <a href="vestuario.php" class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-300 text-black px-6 py-3 rounded-lg font-bold uppercase tracking-wider text-sm transition">
          <i class="fa-solid fa-arrow-right"></i> Ver catálogo
        </a>
      </div>
    <?php endif;
    $stmt->close();
    mysqli_close($conn);
    ?>
  </section>
</main>

<?php include '../includes/footer.php'; ?>
