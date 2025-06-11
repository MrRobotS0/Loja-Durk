<?php
include 'includes/auth.php';
include '../backend/db.php';

if (isset($_GET['excluir']) && is_numeric($_GET['excluir'])) {
    $idExcluir = intval($_GET['excluir']);

    $conn->query("DELETE FROM imagens_produto WHERE produto_id = $idExcluir");
    $conn->query("DELETE FROM produto_tamanhos WHERE produto_id = $idExcluir");

    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $idExcluir);
    $stmt->execute();
    $stmt->close();

    header("Location: produtos.php");
    exit();
}

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$sql = "
    SELECT p.*, c.nome AS categoria, m.nome AS marca
    FROM produtos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    LEFT JOIN marcas m ON p.marca_id = m.id
";
if (!empty($busca)) {
    $buscaEsc = $conn->real_escape_string($busca);
    $sql .= " WHERE p.nome LIKE '%$buscaEsc%'";
}
$sql .= " ORDER BY p.id DESC";
$produtos = $conn->query($sql);

include 'includes/header.php';
?>

<main class="bg-gradient-to-b from-gray-100 to-white min-h-screen p-8 font-sans">
  <div class="max-w-7xl mx-auto space-y-8">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
      <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-2">🧥 Gerenciar Produtos</h1>
      <div class="flex flex-col sm:flex-row gap-2 items-center">
        <form method="GET" class="flex items-center gap-2">
          <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Buscar por nome..."
                 class="px-3 py-1.5 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring focus:outline-none" />
          <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-lg text-sm transition flex items-center gap-1">
            🔍 <span>Buscar</span>
          </button>
        </form>
        <a href="produto_adicionar.php"
          class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-1.5 rounded-full transition flex items-center gap-1">
          ➕ <span>Novo Produto</span>
        </a>
      </div>
    </div>

    <div class="overflow-x-auto bg-white rounded-2xl shadow-lg">
      <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
          <tr>
            <th class="px-6 py-4 border-b">ID</th>
            <th class="px-6 py-4 border-b">Nome</th>
            <th class="px-6 py-4 border-b">Preço</th>
            <th class="px-6 py-4 border-b">Estoque</th>
            <th class="px-6 py-4 border-b">Gênero</th>
            <th class="px-6 py-4 border-b">Categoria</th>
            <th class="px-6 py-4 border-b">Marca</th>
            <th class="px-6 py-4 border-b text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php while ($p = $produtos->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 font-medium text-gray-800"><?= $p['id'] ?></td>
              <td class="px-6 py-4"><?= htmlspecialchars($p['nome']) ?></td>
              <td class="px-6 py-4">R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
              <td class="px-6 py-4"><?= $p['estoque'] ?></td>
              <td class="px-6 py-4 capitalize"><?= htmlspecialchars($p['genero']) ?></td>
              <td class="px-6 py-4"><?= htmlspecialchars($p['categoria'] ?? '-') ?></td>
              <td class="px-6 py-4"><?= htmlspecialchars($p['marca'] ?? '-') ?></td>
              <td class="px-6 py-4 text-center flex justify-center gap-2">
                <a href="produto_detalhes.php?id=<?= $p['id'] ?>"
                  class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded-md text-xs transition shadow-sm flex items-center gap-1">
                  🔍 <span>Detalhes</span>
                </a>
                <a href="produto_editar.php?id=<?= $p['id'] ?>"
                  class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs transition shadow-sm flex items-center gap-1">
                  ✏️ <span>Editar</span>
                </a>
                <a href="produtos.php?excluir=<?= $p['id'] ?>"
                  onclick="return confirm('Deseja realmente excluir este produto?');"
                  class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-xs transition shadow-sm flex items-center gap-1">
                  🗑️ <span>Excluir</span>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
