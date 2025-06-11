<?php
include '../includes/verifica_login.php';
include '../includes/header.php';
include '../backend/db.php';

$userId = $_SESSION['usuario_id'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meus Favoritos - Loja Durk</title>
  <script src="https://cdn.tailwindcss.com"></script>
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
  </style>
</head>
<body class="bg-gradient-to-b from-blue-50 to-white min-h-screen">

<main class="max-w-[1400px] mx-auto px-6 py-10">
  <h1 class="text-5xl font-extrabold text-center text-gray-900 mb-2 animate__animated animate__fadeInDown">💙 Meus Favoritos</h1>
  <p class="text-center text-gray-600 text-lg mb-10 animate__animated animate__fadeInUp">Seus produtos queridinhos estão aqui!</p>

  <section class="grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-8 animate__animated animate__fadeIn">
    <?php
    $sql = "
      SELECT p.id, p.nome, p.preco, c.nome AS categoria, MIN(ip.url_imagem) AS imagem
      FROM favoritos f
      INNER JOIN produtos p ON f.produto_id = p.id
      LEFT JOIN categorias c ON p.categoria_id = c.id
      LEFT JOIN imagens_produto ip ON p.id = ip.produto_id
      WHERE f.user_id = $userId
      GROUP BY p.id
      ORDER BY p.nome ASC
    ";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
      while ($produto = mysqli_fetch_assoc($result)) {
        echo '<div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1 hover:scale-105 duration-300 group flex flex-col relative overflow-hidden" data-aos="zoom-in">';
        echo '<form action="../backend/favoritar.php" method="POST" class="absolute top-3 right-3 fav-btn opacity-0 transform -translate-y-3 transition duration-300">';
        echo '<input type="hidden" name="produto_id" value="' . $produto['id'] . '">';
        echo '<button type="submit" name="acao" value="remover" title="Remover dos Favoritos" class="text-red-500 hover:text-gray-400 text-xl"><i class="fa-solid fa-heart"></i></button>';
        echo '</form>';
        echo '<img src="../' . htmlspecialchars($produto['imagem']) . '" alt="' . htmlspecialchars($produto['nome']) . '" class="w-full aspect-[3/4] h-64 object-contain rounded-t-xl">';
        echo '<div class="p-4 flex flex-col flex-grow">';
        echo '<h3 class="text-base font-semibold text-gray-800 line-clamp-2">' . htmlspecialchars($produto['nome']) . '</h3>';
        echo '<p class="text-sm text-gray-500">' . htmlspecialchars($produto['categoria']) . '</p>';
        echo '<p class="text-green-600 font-bold text-lg mt-1">R$ ' . number_format($produto['preco'], 2, ',', '.') . '</p>';
        echo '<div class="mt-auto flex justify-between items-center pt-4">';
        echo '<button title="Adicionar ao carrinho" class="text-gray-600 hover:text-gray-900 text-xl transition"><i class="fa-solid fa-cart-shopping"></i></button>';
        echo '<a href="produto_detalhes.php?id=' . $produto['id'] . '" class="ml-auto px-4 py-2 rounded-lg text-white font-semibold bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 transition text-sm">Ver produto</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
      }
    } else {
      echo '<p class="col-span-full text-center text-gray-600">Você ainda não favoritou nenhum produto.</p>';
    }

    mysqli_close($conn);
    ?>
  </section>
</main>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init();</script>
<?php include '../includes/footer.php'; ?>
</body>
</html>