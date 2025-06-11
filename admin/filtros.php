<?php
include 'includes/auth.php';
include '../backend/db.php';

if (isset($_GET['del_cat'])) {
    $id = intval($_GET['del_cat']);
    $conn->query("DELETE FROM categorias WHERE id = $id");
    header("Location: filtros.php");
    exit();
}

if (isset($_GET['del_marca'])) {
    $id = intval($_GET['del_marca']);
    $conn->query("DELETE FROM marcas WHERE id = $id");
    header("Location: filtros.php");
    exit();
}

if (isset($_GET['del_tam'])) {
    $id = intval($_GET['del_tam']);
    $conn->query("DELETE FROM tamanhos WHERE id = $id");
    header("Location: filtros.php");
    exit();
}

include 'includes/header.php';

$categorias = $conn->query("SELECT * FROM categorias ORDER BY id DESC");
$marcas = $conn->query("SELECT * FROM marcas ORDER BY id DESC");
$tamanhos = $conn->query("SELECT * FROM tamanhos ORDER BY id DESC");
?>

<main class="bg-gray-100 min-h-screen p-8 font-sans">
  <div class="max-w-6xl mx-auto space-y-10">

    <section class="bg-white p-6 rounded-xl shadow-md">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">📂 Categorias</h2>
        <button onclick="document.getElementById('popupCategoria').showModal()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
          ➕ Adicionar
        </button>
      </div>
      <table class="w-full text-sm text-left">
        <thead class="bg-gray-100 text-gray-700 uppercase tracking-wide">
          <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Nome</th>
            <th class="px-4 py-2">Slug</th>
            <th class="px-4 py-2 text-center">Excluir</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <?php while($c = $categorias->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 font-medium text-gray-800"><?= $c['id'] ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($c['nome']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($c['slug']) ?></td>
              <td class="px-4 py-2 text-center">
                <a href="filtros.php?del_cat=<?= $c['id'] ?>" onclick="return confirm('Excluir esta categoria?');" class="text-red-600 hover:underline">🗑️</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>

    <section class="bg-white p-6 rounded-xl shadow-md">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">🏷️ Marcas</h2>
        <button onclick="document.getElementById('popupMarca').showModal()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
          Adicionar
        </button>
      </div>
      <table class="w-full text-sm text-left">
        <thead class="bg-gray-100 text-gray-700 uppercase tracking-wide">
          <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Nome</th>
            <th class="px-4 py-2">Descrição</th>
            <th class="px-4 py-2 text-center">Excluir</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <?php while($m = $marcas->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 font-medium text-gray-800"><?= $m['id'] ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($m['nome']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($m['descricao']) ?></td>
              <td class="px-4 py-2 text-center">
                <a href="filtros.php?del_marca=<?= $m['id'] ?>" onclick="return confirm('Excluir esta marca?');" class="text-red-600 hover:underline">🗑️</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>

    <section class="bg-white p-6 rounded-xl shadow-md">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">📏 Tamanhos</h2>
        <button onclick="document.getElementById('popupTamanho').showModal()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
          Adicionar
        </button>
      </div>
      <table class="w-full text-sm text-left">
        <thead class="bg-gray-100 text-gray-700 uppercase tracking-wide">
          <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Descrição</th>
            <th class="px-4 py-2 text-center">Excluir</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <?php while($t = $tamanhos->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 font-medium text-gray-800"><?= $t['id'] ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($t['descricao']) ?></td>
              <td class="px-4 py-2 text-center">
                <a href="filtros.php?del_tam=<?= $t['id'] ?>" onclick="return confirm('Excluir este tamanho?');" class="text-red-600 hover:underline">🗑️</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
