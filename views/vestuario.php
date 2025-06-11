<?php
session_start();
include '../includes/header.php';
include '../backend/db.php';

$userId = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

$favoritados = [];
if ($userId) {
    $favQuery = "SELECT produto_id FROM favoritos WHERE user_id = $userId";
    $favResult = mysqli_query($conn, $favQuery);
    while ($row = mysqli_fetch_assoc($favResult)) {
        $favoritados[] = $row['produto_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vestuário - Loja Durk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .group:hover .fav-btn {
            opacity: 1;
            transform: translateY(0);
        }
        .heart-red { color: #ef4444; } 
        .heart-gray { color: #9ca3af; } 
    </style>
</head>
<body class="bg-gradient-to-b from-blue-50 to-white min-h-screen">

<main class="max-w-[1400px] mx-auto px-6 py-10">
  <h1 class="text-5xl font-extrabold text-center text-gray-900 mb-2 animate__animated animate__fadeInDown">🛍️ Vestuário</h1>
  <p class="text-center text-gray-600 text-lg mb-10 animate__animated animate__fadeInUp">Explore os produtos que vão transformar seu estilo</p>
  
  <form method="GET" class="bg-white rounded-2xl shadow-lg p-6 mb-12 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 animate__animated animate__fadeIn">
    <?php
      function filtroSelect($label, $name, $query, $valueField, $textField) {
        global $conn;
        echo "<div><label class='block text-sm font-semibold text-gray-700 mb-1'>$label</label><select name='$name' class='w-full border border-gray-300 rounded px-3 py-2'>";
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
      <label class="block text-sm font-semibold text-gray-700 mb-1">Gênero</label>
      <select name="genero" class="w-full border border-gray-300 rounded px-3 py-2">
        <option value="">Todos</option>
        <option value="masculino" <?= isset($_GET['genero']) && $_GET['genero'] === 'masculino' ? 'selected' : '' ?>>Masculino</option>
        <option value="feminino" <?= isset($_GET['genero']) && $_GET['genero'] === 'feminino' ? 'selected' : '' ?>>Feminino</option>
        <option value="unissex" <?= isset($_GET['genero']) && $_GET['genero'] === 'unissex' ? 'selected' : '' ?>>Unissex</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-semibold text-gray-700 mb-1">Preço até</label>
      <input type="text" name="preco" placeholder="R$" value="<?= isset($_GET['preco']) ? htmlspecialchars($_GET['preco']) : '' ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
    </div>
    <div class="flex items-end">
      <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition duration-300">Filtrar</button>
    </div>
  </form>

  <section class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-8 animate__animated animate__fadeIn">
    <?php
    $filtros = [];
    if (!empty($_GET['categoria'])) $filtros[] = "p.categoria_id = " . intval($_GET['categoria']);
    if (!empty($_GET['marca'])) $filtros[] = "p.marca_id = " . intval($_GET['marca']);
    if (!empty($_GET['genero'])) $filtros[] = "p.genero = '" . $conn->real_escape_string($_GET['genero']) . "'";
    if (!empty($_GET['preco'])) $filtros[] = "p.preco <= " . floatval(str_replace(',', '.', str_replace('R$', '', $_GET['preco'])));
    if (!empty($_GET['tamanho'])) {
      $filtros[] = "EXISTS (SELECT 1 FROM produto_tamanhos pt WHERE pt.produto_id = p.id AND pt.tamanho_id = " . intval($_GET['tamanho']) . ")";
    }

    $where = count($filtros) ? "WHERE " . implode(" AND ", $filtros) : "";

    $query = "
      SELECT p.id, p.nome, p.preco, c.nome AS categoria, MIN(ip.url_imagem) AS imagem
      FROM produtos p
      JOIN categorias c ON p.categoria_id = c.id
      LEFT JOIN imagens_produto ip ON p.id = ip.produto_id
      $where
      GROUP BY p.id
      ORDER BY p.nome ASC;
    ";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
      while ($produto = mysqli_fetch_assoc($result)) {
        $isFavoritado = in_array($produto['id'], $favoritados);
        $heartClass = $isFavoritado ? 'heart-red' : 'heart-gray';
        $acao = $isFavoritado ? 'remover' : 'adicionar';
        
        echo '<div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1 hover:scale-105 duration-300 group flex flex-col relative overflow-hidden" data-aos="zoom-in">';
        echo '<div class="absolute top-3 right-3 fav-btn opacity-0 transform -translate-y-3 transition duration-300">';
        
        if ($userId) {
          echo '<button type="button" class="text-xl toggle-fav ' . $heartClass . '" data-id="' . $produto['id'] . '" data-acao="' . $acao . '" title="Favoritar">';
          echo '<i class="fa-solid fa-heart"></i>';
          echo '</button>';
        }
        echo '</div>';
        echo '<img src="../' . htmlspecialchars($produto['imagem']) . '" alt="' . htmlspecialchars($produto['nome']) . '" class="w-full aspect-[3/4] h-64 object-contain rounded-t-xl">';
        echo '<div class="p-4 flex flex-col flex-grow">';
        echo '<h3 class="text-base font-semibold text-gray-800 line-clamp-2">' . htmlspecialchars($produto['nome']) . '</h3>';
        echo '<p class="text-sm text-gray-500">' . htmlspecialchars($produto['categoria']) . '</p>';
        echo '<p class="text-green-600 font-bold text-lg mt-1">R$ ' . number_format($produto['preco'], 2, ',', '.') . '</p>';
        echo '<div class="mt-auto flex justify-between items-center pt-4">';
        echo '<a href="produto_detalhes.php?id=' . $produto['id'] . '" class="ml-auto px-4 py-2 rounded-lg text-white font-semibold bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 transition text-sm">Ver produto</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
      }
    } else {
      echo '<p class="col-span-full text-center text-gray-600">Nenhum produto encontrado com os filtros selecionados.</p>';
    }
    mysqli_close($conn);
    ?>
  </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function() {
    $('.toggle-fav').click(function() {
      const btn = $(this);
      const produtoId = btn.data('id');
      const acaoAtual = btn.data('acao');

      $.post('../backend/favoritar.php', {
        produto_id: produtoId,
        acao: acaoAtual
      }, function() {
        if (acaoAtual === 'adicionar') {
          btn.removeClass('heart-gray').addClass('heart-red').data('acao', 'remover');
          Swal.fire({
            toast: true,
            icon: 'success',
            title: 'Produto adicionado aos favoritos!',
            position: 'top-end',
            timer: 1800,
            showConfirmButton: false
          });
        } else {
          btn.removeClass('heart-red').addClass('heart-gray').data('acao', 'adicionar');
          Swal.fire({
            toast: true,
            icon: 'info',
            title: 'Produto removido dos favoritos',
            position: 'top-end',
            timer: 1800,
            showConfirmButton: false
          });
        }
        atualizarBadges();
      }).fail(function() {
        Swal.fire({
          icon: 'error',
          title: 'Erro ao favoritar',
          text: 'Verifique sua conexão ou faça login novamente.'
        });
      });
    });
  });
</script>


<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init();</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
