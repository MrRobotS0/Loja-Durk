<?php
include 'includes/auth.php';
include '../backend/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

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
    header('Location: produtos.php');
    exit();
}

$imgStmt = $conn->prepare("SELECT url_imagem FROM imagens_produto WHERE produto_id = ? ORDER BY ordem");
$imgStmt->bind_param("i", $id);
$imgStmt->execute();
$imagens = $imgStmt->get_result();

$tamStmt = $conn->prepare("
    SELECT t.descricao FROM produto_tamanhos pt
    JOIN tamanhos t ON pt.tamanho_id = t.id
    WHERE pt.produto_id = ?
");
$tamStmt->bind_param("i", $id);
$tamStmt->execute();
$tamanhos = $tamStmt->get_result();

include 'includes/header.php';
?>

<main class="bg-zinc-950/40 min-h-screen p-4 sm:p-6 lg:p-10">
  <div class="max-w-5xl mx-auto">
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
      <div>
        <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Produto #<?= $id ?></span>
        <h1 class="font-display text-4xl text-white mt-2"><?= htmlspecialchars($produto['nome']) ?></h1>
      </div>
      <a href="produto_editar.php?id=<?= $id ?>" class="bg-yellow-400 hover:bg-yellow-300 text-black px-5 py-2.5 rounded-lg text-sm font-bold uppercase tracking-wider transition inline-flex items-center gap-2">
        <i class="fa-solid fa-pen"></i> Editar
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6">
        <h2 class="font-display text-xl text-white mb-5 flex items-center gap-2"><i class="fa-solid fa-circle-info text-yellow-400"></i> INFORMAÇÕES</h2>
        <dl class="space-y-3 text-sm">
          <div class="grid grid-cols-3 gap-2 py-2 border-b border-zinc-900">
            <dt class="text-yellow-400 uppercase tracking-widest text-[10px] font-bold">Preço</dt>
            <dd class="col-span-2 text-white font-bold">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></dd>
          </div>
          <div class="grid grid-cols-3 gap-2 py-2 border-b border-zinc-900">
            <dt class="text-yellow-400 uppercase tracking-widest text-[10px] font-bold">Estoque</dt>
            <dd class="col-span-2 text-white"><?= (int) $produto['estoque'] ?></dd>
          </div>
          <div class="grid grid-cols-3 gap-2 py-2 border-b border-zinc-900">
            <dt class="text-yellow-400 uppercase tracking-widest text-[10px] font-bold">Gênero</dt>
            <dd class="col-span-2 text-white capitalize"><?= htmlspecialchars($produto['genero']) ?></dd>
          </div>
          <div class="grid grid-cols-3 gap-2 py-2 border-b border-zinc-900">
            <dt class="text-yellow-400 uppercase tracking-widest text-[10px] font-bold">Categoria</dt>
            <dd class="col-span-2 text-white"><?= htmlspecialchars($produto['categoria'] ?? '—') ?></dd>
          </div>
          <div class="grid grid-cols-3 gap-2 py-2 border-b border-zinc-900">
            <dt class="text-yellow-400 uppercase tracking-widest text-[10px] font-bold">Marca</dt>
            <dd class="col-span-2 text-white"><?= htmlspecialchars($produto['marca'] ?? '—') ?></dd>
          </div>
          <div class="grid grid-cols-3 gap-2 py-2 border-b border-zinc-900">
            <dt class="text-yellow-400 uppercase tracking-widest text-[10px] font-bold">Slug</dt>
            <dd class="col-span-2 text-zinc-400 break-all"><?= htmlspecialchars($produto['slug']) ?></dd>
          </div>
          <div class="grid grid-cols-3 gap-2 py-2">
            <dt class="text-yellow-400 uppercase tracking-widest text-[10px] font-bold">Cadastro</dt>
            <dd class="col-span-2 text-zinc-400"><?= !empty($produto['data_cadastro']) ? date('d/m/Y H:i', strtotime($produto['data_cadastro'])) : '—' ?></dd>
          </div>
        </dl>

        <div class="mt-5 pt-5 border-t border-zinc-900">
          <p class="text-yellow-400 uppercase tracking-widest text-[10px] font-bold mb-2">Descrição</p>
          <p class="text-zinc-300 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
        </div>
      </div>

      <div class="space-y-6">
        <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6">
          <h2 class="font-display text-xl text-white mb-4 flex items-center gap-2"><i class="fa-solid fa-images text-yellow-400"></i> IMAGENS</h2>
          <?php if ($imagens && $imagens->num_rows > 0): ?>
            <div class="grid grid-cols-3 gap-3">
              <?php while ($img = $imagens->fetch_assoc()): ?>
                <div class="aspect-square bg-black border border-zinc-800 rounded-lg overflow-hidden p-1">
                  <img src="../<?= htmlspecialchars($img['url_imagem']) ?>" class="w-full h-full object-contain">
                </div>
              <?php endwhile; ?>
            </div>
          <?php else: ?>
            <p class="text-zinc-500 text-sm">Nenhuma imagem cadastrada.</p>
          <?php endif; ?>
        </div>

        <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6">
          <h2 class="font-display text-xl text-white mb-4 flex items-center gap-2"><i class="fa-solid fa-ruler text-yellow-400"></i> TAMANHOS</h2>
          <?php if ($tamanhos && $tamanhos->num_rows > 0): ?>
            <div class="flex flex-wrap gap-2">
              <?php while ($t = $tamanhos->fetch_assoc()): ?>
                <span class="px-3 py-1.5 bg-yellow-400 text-black rounded-lg text-sm font-bold"><?= htmlspecialchars($t['descricao']) ?></span>
              <?php endwhile; ?>
            </div>
          <?php else: ?>
            <p class="text-zinc-500 text-sm">Nenhum tamanho vinculado.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="mt-8">
      <a href="produtos.php" class="inline-flex items-center gap-2 text-zinc-400 hover:text-yellow-400 text-sm uppercase tracking-widest font-bold transition">
        <i class="fa-solid fa-arrow-left"></i> Voltar para lista
      </a>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
