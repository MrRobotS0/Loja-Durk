<?php
include 'includes/auth.php';
include '../backend/db.php';

if (isset($_GET['del_cat'])) {
    $stmt = $conn->prepare("DELETE FROM categorias WHERE id = ?");
    $id = (int) $_GET['del_cat'];
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: filtros.php");
    exit();
}

if (isset($_GET['del_marca'])) {
    $stmt = $conn->prepare("DELETE FROM marcas WHERE id = ?");
    $id = (int) $_GET['del_marca'];
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: filtros.php");
    exit();
}

if (isset($_GET['del_tam'])) {
    $stmt = $conn->prepare("DELETE FROM tamanhos WHERE id = ?");
    $id = (int) $_GET['del_tam'];
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: filtros.php");
    exit();
}

$categorias = $conn->query("SELECT * FROM categorias ORDER BY id DESC");
$marcas = $conn->query("SELECT * FROM marcas ORDER BY id DESC");
$tamanhos = $conn->query("SELECT * FROM tamanhos ORDER BY id DESC");

include 'includes/header.php';
?>

<main class="bg-zinc-950/40 min-h-screen p-4 sm:p-6 lg:p-10">
  <div class="max-w-6xl mx-auto space-y-8">

    <div>
      <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Atributos</span>
      <h1 class="font-display text-5xl text-white mt-2">FILTROS</h1>
      <p class="text-zinc-400 mt-1 text-sm">Gerencie categorias, marcas e tamanhos.</p>
    </div>

    <section class="bg-zinc-950 border border-zinc-900 rounded-2xl overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-900">
        <h2 class="font-display text-xl text-white flex items-center gap-2"><i class="fa-solid fa-tags text-yellow-400"></i> CATEGORIAS</h2>
        <button onclick="document.getElementById('popupCategoria').classList.remove('hidden')" class="bg-yellow-400 hover:bg-yellow-300 text-black text-xs font-bold uppercase tracking-wider px-4 py-2 rounded-lg transition inline-flex items-center gap-2">
          <i class="fa-solid fa-plus"></i> Adicionar
        </button>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-black text-yellow-400 uppercase text-[10px] tracking-widest font-bold">
          <tr>
            <th class="px-6 py-3 text-left">ID</th>
            <th class="px-6 py-3 text-left">Nome</th>
            <th class="px-6 py-3 text-left">Slug</th>
            <th class="px-6 py-3 text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-900">
          <?php if ($categorias->num_rows > 0):
            while ($c = $categorias->fetch_assoc()): ?>
            <tr class="hover:bg-black transition">
              <td class="px-6 py-3 font-bold text-zinc-500">#<?= $c['id'] ?></td>
              <td class="px-6 py-3 text-white font-bold"><?= htmlspecialchars($c['nome']) ?></td>
              <td class="px-6 py-3 text-zinc-400"><?= htmlspecialchars($c['slug']) ?></td>
              <td class="px-6 py-3 text-center">
                <a href="filtros.php?del_cat=<?= $c['id'] ?>" onclick="return confirm('Excluir esta categoria?');" class="w-8 h-8 inline-flex items-center justify-center bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg transition">
                  <i class="fa-solid fa-trash text-xs"></i>
                </a>
              </td>
            </tr>
          <?php endwhile;
          else: ?>
            <tr><td colspan="4" class="text-center py-10 text-zinc-500">Nenhuma categoria cadastrada.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>

    <section class="bg-zinc-950 border border-zinc-900 rounded-2xl overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-900">
        <h2 class="font-display text-xl text-white flex items-center gap-2"><i class="fa-solid fa-tag text-yellow-400"></i> MARCAS</h2>
        <button onclick="document.getElementById('popupMarca').classList.remove('hidden')" class="bg-yellow-400 hover:bg-yellow-300 text-black text-xs font-bold uppercase tracking-wider px-4 py-2 rounded-lg transition inline-flex items-center gap-2">
          <i class="fa-solid fa-plus"></i> Adicionar
        </button>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-black text-yellow-400 uppercase text-[10px] tracking-widest font-bold">
          <tr>
            <th class="px-6 py-3 text-left">ID</th>
            <th class="px-6 py-3 text-left">Nome</th>
            <th class="px-6 py-3 text-left">Descrição</th>
            <th class="px-6 py-3 text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-900">
          <?php if ($marcas->num_rows > 0):
            while ($m = $marcas->fetch_assoc()): ?>
            <tr class="hover:bg-black transition">
              <td class="px-6 py-3 font-bold text-zinc-500">#<?= $m['id'] ?></td>
              <td class="px-6 py-3 text-white font-bold"><?= htmlspecialchars($m['nome']) ?></td>
              <td class="px-6 py-3 text-zinc-400"><?= htmlspecialchars($m['descricao']) ?></td>
              <td class="px-6 py-3 text-center">
                <a href="filtros.php?del_marca=<?= $m['id'] ?>" onclick="return confirm('Excluir esta marca?');" class="w-8 h-8 inline-flex items-center justify-center bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg transition">
                  <i class="fa-solid fa-trash text-xs"></i>
                </a>
              </td>
            </tr>
          <?php endwhile;
          else: ?>
            <tr><td colspan="4" class="text-center py-10 text-zinc-500">Nenhuma marca cadastrada.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>

    <section class="bg-zinc-950 border border-zinc-900 rounded-2xl overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-900">
        <h2 class="font-display text-xl text-white flex items-center gap-2"><i class="fa-solid fa-ruler text-yellow-400"></i> TAMANHOS</h2>
        <button onclick="document.getElementById('popupTamanho').classList.remove('hidden')" class="bg-yellow-400 hover:bg-yellow-300 text-black text-xs font-bold uppercase tracking-wider px-4 py-2 rounded-lg transition inline-flex items-center gap-2">
          <i class="fa-solid fa-plus"></i> Adicionar
        </button>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-black text-yellow-400 uppercase text-[10px] tracking-widest font-bold">
          <tr>
            <th class="px-6 py-3 text-left">ID</th>
            <th class="px-6 py-3 text-left">Descrição</th>
            <th class="px-6 py-3 text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-900">
          <?php if ($tamanhos->num_rows > 0):
            while ($t = $tamanhos->fetch_assoc()): ?>
            <tr class="hover:bg-black transition">
              <td class="px-6 py-3 font-bold text-zinc-500">#<?= $t['id'] ?></td>
              <td class="px-6 py-3 text-white font-bold"><?= htmlspecialchars($t['descricao']) ?></td>
              <td class="px-6 py-3 text-center">
                <a href="filtros.php?del_tam=<?= $t['id'] ?>" onclick="return confirm('Excluir este tamanho?');" class="w-8 h-8 inline-flex items-center justify-center bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg transition">
                  <i class="fa-solid fa-trash text-xs"></i>
                </a>
              </td>
            </tr>
          <?php endwhile;
          else: ?>
            <tr><td colspan="3" class="text-center py-10 text-zinc-500">Nenhum tamanho cadastrado.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </div>
</main>

<div id="popupCategoria" class="fixed inset-0 bg-black/80 backdrop-blur z-50 hidden items-center justify-center px-4 flex">
  <div class="bg-zinc-950 border border-yellow-400/30 rounded-2xl w-full max-w-md p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="font-display text-2xl text-white">NOVA CATEGORIA</h3>
      <button onclick="document.getElementById('popupCategoria').classList.add('hidden')" class="text-zinc-500 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
    </div>
    <form method="POST" action="filtros_adicionar.php?tipo=categoria" class="space-y-3">
      <input type="text" name="nome" placeholder="Nome" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
      <input type="text" name="slug" placeholder="Slug (ex: camisetas)" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
      <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-300 text-black font-bold uppercase tracking-wider py-3 rounded-lg transition">Salvar</button>
    </form>
  </div>
</div>

<div id="popupMarca" class="fixed inset-0 bg-black/80 backdrop-blur z-50 hidden items-center justify-center px-4 flex">
  <div class="bg-zinc-950 border border-yellow-400/30 rounded-2xl w-full max-w-md p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="font-display text-2xl text-white">NOVA MARCA</h3>
      <button onclick="document.getElementById('popupMarca').classList.add('hidden')" class="text-zinc-500 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
    </div>
    <form method="POST" action="filtros_adicionar.php?tipo=marca" class="space-y-3">
      <input type="text" name="nome" placeholder="Nome" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
      <textarea name="descricao" rows="3" placeholder="Descrição" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition resize-none"></textarea>
      <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-300 text-black font-bold uppercase tracking-wider py-3 rounded-lg transition">Salvar</button>
    </form>
  </div>
</div>

<div id="popupTamanho" class="fixed inset-0 bg-black/80 backdrop-blur z-50 hidden items-center justify-center px-4 flex">
  <div class="bg-zinc-950 border border-yellow-400/30 rounded-2xl w-full max-w-md p-6">
    <div class="flex items-center justify-between mb-5">
      <h3 class="font-display text-2xl text-white">NOVO TAMANHO</h3>
      <button onclick="document.getElementById('popupTamanho').classList.add('hidden')" class="text-zinc-500 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
    </div>
    <form method="POST" action="filtros_adicionar.php?tipo=tamanho" class="space-y-3">
      <input type="text" name="descricao" placeholder="Ex: P, M, G, GG, 38, 40..." required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
      <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-300 text-black font-bold uppercase tracking-wider py-3 rounded-lg transition">Salvar</button>
    </form>
  </div>
</div>

<style>
  #popupCategoria, #popupMarca, #popupTamanho { display: none; }
  #popupCategoria:not(.hidden), #popupMarca:not(.hidden), #popupTamanho:not(.hidden) { display: flex; }
</style>

<?php include 'includes/footer.php'; ?>
