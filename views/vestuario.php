<?php
include '../includes/header.php';
include '../backend/db.php';

$userId = isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;

$favoritados = [];
if ($userId) {
    $stmt = $conn->prepare("SELECT produto_id FROM favoritos WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $favoritados[] = (int) $row['produto_id'];
    }
    $stmt->close();
}
?>

<title>Catálogo · DURK</title>

<main class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-10 py-10">

  <div class="mb-10">
    <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Catálogo</span>
    <h1 class="font-display text-6xl md:text-7xl text-white mt-2">VESTUÁRIO</h1>
    <p class="text-zinc-400 mt-3 max-w-xl">Drops autorais, peças com atitude. Filtra do teu jeito.</p>
  </div>

  <form method="GET" class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 mb-12 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
    <?php
      function filtroSelect($label, $name, $query, $valueField, $textField) {
        global $conn;
        echo "<div><label class='block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2'>$label</label>";
        echo "<select name='$name' class='w-full bg-black border border-zinc-800 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-yellow-400 transition'>";
        echo "<option value=''>Todas</option>";
        $res = $conn->query($query);
        while ($row = $res->fetch_assoc()) {
          $selected = isset($_GET[$name]) && $_GET[$name] == $row[$valueField] ? 'selected' : '';
          echo "<option value='{$row[$valueField]}' $selected>" . htmlspecialchars($row[$textField]) . "</option>";
        }
        echo "</select></div>";
      }

      filtroSelect('Categoria', 'categoria', "SELECT id, nome FROM categorias ORDER BY nome", 'id', 'nome');
      filtroSelect('Marca', 'marca', "SELECT id, nome FROM marcas ORDER BY nome", 'id', 'nome');
      filtroSelect('Tamanho', 'tamanho', "SELECT id, descricao FROM tamanhos ORDER BY descricao", 'id', 'descricao');
    ?>
    <div>
      <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Gênero</label>
      <select name="genero" class="w-full bg-black border border-zinc-800 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-yellow-400 transition">
        <option value="">Todos</option>
        <option value="masculino" <?= isset($_GET['genero']) && $_GET['genero'] === 'masculino' ? 'selected' : '' ?>>Masculino</option>
        <option value="feminino" <?= isset($_GET['genero']) && $_GET['genero'] === 'feminino' ? 'selected' : '' ?>>Feminino</option>
        <option value="unissex" <?= isset($_GET['genero']) && $_GET['genero'] === 'unissex' ? 'selected' : '' ?>>Unissex</option>
      </select>
    </div>
    <div>
      <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Preço até</label>
      <input type="text" name="preco" placeholder="R$ 0,00" value="<?= isset($_GET['preco']) ? htmlspecialchars($_GET['preco']) : '' ?>" class="w-full bg-black border border-zinc-800 text-white rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-yellow-400 transition" />
    </div>
    <div class="flex items-end gap-2 col-span-2 md:col-span-1 lg:col-span-1">
      <button type="submit" class="flex-1 bg-yellow-400 hover:bg-yellow-300 text-black font-bold uppercase tracking-wider py-2.5 rounded-lg transition text-sm">Filtrar</button>
      <a href="vestuario.php" class="px-3 py-2.5 border border-zinc-800 hover:border-yellow-400 text-zinc-400 hover:text-yellow-400 rounded-lg transition text-sm" title="Limpar filtros">
        <i class="fa-solid fa-xmark"></i>
      </a>
    </div>
  </form>

  <section class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
    <?php
    $filtros = [];
    $params = [];
    $types = '';

    if (!empty($_GET['categoria'])) {
      $filtros[] = "p.categoria_id = ?";
      $params[] = (int) $_GET['categoria'];
      $types .= 'i';
    }
    if (!empty($_GET['marca'])) {
      $filtros[] = "p.marca_id = ?";
      $params[] = (int) $_GET['marca'];
      $types .= 'i';
    }
    if (!empty($_GET['genero'])) {
      $filtros[] = "p.genero = ?";
      $params[] = $_GET['genero'];
      $types .= 's';
    }
    if (!empty($_GET['preco'])) {
      $valor = (float) str_replace(',', '.', str_replace(['R$', '.'], '', $_GET['preco']));
      $filtros[] = "p.preco <= ?";
      $params[] = $valor;
      $types .= 'd';
    }
    if (!empty($_GET['tamanho'])) {
      $filtros[] = "EXISTS (SELECT 1 FROM produto_tamanhos pt WHERE pt.produto_id = p.id AND pt.tamanho_id = ?)";
      $params[] = (int) $_GET['tamanho'];
      $types .= 'i';
    }

    $where = count($filtros) ? "WHERE " . implode(" AND ", $filtros) : "";

    $sql = "SELECT p.id, p.nome, p.preco, c.nome AS categoria, MIN(ip.url_imagem) AS imagem
            FROM produtos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            LEFT JOIN imagens_produto ip ON p.id = ip.produto_id
            $where
            GROUP BY p.id
            ORDER BY p.id DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
      $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
      while ($produto = $result->fetch_assoc()) {
        $isFav = in_array((int) $produto['id'], $favoritados, true);
        $acao = $isFav ? 'remover' : 'adicionar';
        $heartClass = $isFav ? 'text-pink-500' : 'text-zinc-500 hover:text-pink-500';
        ?>
        <div class="group bg-zinc-950 border border-zinc-900 rounded-2xl overflow-hidden hover:border-yellow-400 transition-all duration-500 flex flex-col">
          <?php if ($userId): ?>
          <button type="button" class="toggle-fav absolute top-3 right-3 z-10 w-9 h-9 bg-black/70 backdrop-blur rounded-full flex items-center justify-center <?= $heartClass ?> transition opacity-0 group-hover:opacity-100"
            data-id="<?= $produto['id'] ?>" data-acao="<?= $acao ?>" title="Favoritar">
            <i class="fa-solid fa-heart text-sm"></i>
          </button>
          <?php endif; ?>
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
        <?php
      }
    } else {
      echo '<div class="col-span-full text-center py-20"><i class="fa-solid fa-ghost text-6xl text-zinc-800 mb-4"></i><p class="text-zinc-500">Nenhum produto encontrado com esses filtros.</p></div>';
    }
    $stmt->close();
    mysqli_close($conn);
    ?>
  </section>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
  $('.toggle-fav').click(function () {
    const btn = $(this);
    const acao = btn.data('acao');
    $.post('../backend/favoritar.php', { produto_id: btn.data('id'), acao }, function () {
      if (acao === 'adicionar') {
        btn.removeClass('text-zinc-500 hover:text-pink-500').addClass('text-pink-500').data('acao', 'remover');
        Swal.fire({ toast: true, icon: 'success', title: 'Adicionado aos favoritos', position: 'top-end', timer: 1500, showConfirmButton: false, background: '#18181b', color: '#fff' });
      } else {
        btn.removeClass('text-pink-500').addClass('text-zinc-500 hover:text-pink-500').data('acao', 'adicionar');
        Swal.fire({ toast: true, icon: 'info', title: 'Removido dos favoritos', position: 'top-end', timer: 1500, showConfirmButton: false, background: '#18181b', color: '#fff' });
      }
      atualizarBadges();
    }).fail(function () {
      Swal.fire({ icon: 'error', title: 'Erro ao favoritar', text: 'Faça login para favoritar produtos.', background: '#18181b', color: '#fff', confirmButtonColor: '#facc15' });
    });
  });
});
</script>

<?php include '../includes/footer.php'; ?>
