<?php
include 'includes/auth.php';
include '../backend/db.php';

if (isset($_GET['excluir']) && is_numeric($_GET['excluir'])) {
    $idExcluir = (int) $_GET['excluir'];

    $del1 = $conn->prepare("DELETE FROM imagens_produto WHERE produto_id = ?");
    $del1->bind_param("i", $idExcluir); $del1->execute(); $del1->close();

    $del2 = $conn->prepare("DELETE FROM produto_tamanhos WHERE produto_id = ?");
    $del2->bind_param("i", $idExcluir); $del2->execute(); $del2->close();

    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $idExcluir);
    $stmt->execute();
    $stmt->close();

    header("Location: produtos.php");
    exit();
}

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

if (!empty($busca)) {
    $stmt = $conn->prepare("
        SELECT p.*, c.nome AS categoria, m.nome AS marca
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        LEFT JOIN marcas m ON p.marca_id = m.id
        WHERE p.nome LIKE ?
        ORDER BY p.id DESC
    ");
    $like = '%' . $busca . '%';
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $produtos = $stmt->get_result();
} else {
    $produtos = $conn->query("
        SELECT p.*, c.nome AS categoria, m.nome AS marca
        FROM produtos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        LEFT JOIN marcas m ON p.marca_id = m.id
        ORDER BY p.id DESC
    ");
}

include 'includes/header.php';
?>

<main class="bg-zinc-950/40 min-h-screen p-4 sm:p-6 lg:p-10">
  <div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4">
      <div>
        <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Catálogo</span>
        <h1 class="font-display text-5xl text-white mt-2">PRODUTOS</h1>
      </div>
      <div class="flex flex-col sm:flex-row gap-2">
        <form method="GET" class="flex items-center gap-2">
          <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Buscar por nome..."
            class="px-4 py-2.5 bg-black border border-zinc-800 text-white text-sm rounded-lg focus:outline-none focus:border-yellow-400 transition w-full sm:w-64" />
          <button type="submit" class="bg-zinc-900 hover:bg-zinc-800 text-white px-4 py-2.5 rounded-lg text-sm font-bold uppercase tracking-wider transition">
            <i class="fa-solid fa-magnifying-glass"></i>
          </button>
        </form>
        <a href="produto_adicionar.php"
          class="bg-yellow-400 hover:bg-yellow-300 text-black text-sm font-bold uppercase tracking-wider px-5 py-2.5 rounded-lg transition inline-flex items-center justify-center gap-2">
          <i class="fa-solid fa-plus"></i> Novo
        </a>
      </div>
    </div>

    <div class="overflow-x-auto bg-zinc-950 border border-zinc-900 rounded-2xl">
      <table class="min-w-full text-sm">
        <thead class="bg-black text-yellow-400 uppercase text-[10px] tracking-widest font-bold">
          <tr>
            <th class="px-6 py-4 text-left">ID</th>
            <th class="px-6 py-4 text-left">Produto</th>
            <th class="px-6 py-4 text-left">Preço</th>
            <th class="px-6 py-4 text-left">Estoque</th>
            <th class="px-6 py-4 text-left">Gênero</th>
            <th class="px-6 py-4 text-left">Categoria</th>
            <th class="px-6 py-4 text-left">Marca</th>
            <th class="px-6 py-4 text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-900">
          <?php if ($produtos && $produtos->num_rows > 0):
            while ($p = $produtos->fetch_assoc()): ?>
            <tr class="hover:bg-black transition">
              <td class="px-6 py-4 font-bold text-zinc-500">#<?= $p['id'] ?></td>
              <td class="px-6 py-4 text-white font-bold"><?= htmlspecialchars($p['nome']) ?></td>
              <td class="px-6 py-4 text-yellow-400 font-bold">R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
              <td class="px-6 py-4">
                <?php $estoque = (int) $p['estoque']; ?>
                <span class="<?= $estoque > 10 ? 'text-emerald-400' : ($estoque > 0 ? 'text-yellow-400' : 'text-red-400') ?> font-bold"><?= $estoque ?></span>
              </td>
              <td class="px-6 py-4 capitalize text-zinc-300"><?= htmlspecialchars($p['genero']) ?></td>
              <td class="px-6 py-4 text-zinc-300"><?= htmlspecialchars($p['categoria'] ?? '—') ?></td>
              <td class="px-6 py-4 text-zinc-300"><?= htmlspecialchars($p['marca'] ?? '—') ?></td>
              <td class="px-6 py-4">
                <div class="flex justify-center gap-1.5">
                  <a href="produto_detalhes.php?id=<?= $p['id'] ?>" title="Detalhes"
                    class="w-8 h-8 flex items-center justify-center bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-white rounded-lg transition">
                    <i class="fa-solid fa-eye text-xs"></i>
                  </a>
                  <a href="produto_editar.php?id=<?= $p['id'] ?>" title="Editar"
                    class="w-8 h-8 flex items-center justify-center bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 rounded-lg transition">
                    <i class="fa-solid fa-pen text-xs"></i>
                  </a>
                  <a href="produtos.php?excluir=<?= $p['id'] ?>" title="Excluir"
                    onclick="return confirm('Excluir este produto definitivamente?');"
                    class="w-8 h-8 flex items-center justify-center bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg transition">
                    <i class="fa-solid fa-trash text-xs"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endwhile;
          else: ?>
            <tr><td colspan="8" class="text-center py-16 text-zinc-500">
              <i class="fa-solid fa-box-open text-5xl text-zinc-800 mb-3 block"></i>
              Nenhum produto encontrado.
            </td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
