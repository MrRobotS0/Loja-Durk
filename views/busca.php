<?php
include '../includes/header.php';
include '../backend/db.php';

$termo = isset($_GET['q']) ? trim($_GET['q']) : '';
?>

<main class="p-8 min-h-screen bg-gray-50">
  <h1 class="text-2xl font-bold mb-6">Resultado da busca por: "<?= htmlspecialchars($termo) ?>"</h1>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php
    if ($termo) {
      $stmt = $conn->prepare("SELECT * FROM produtos WHERE nome LIKE ?");
      $like = "%" . $termo . "%";
      $stmt->bind_param("s", $like);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        while ($produto = $result->fetch_assoc()) {
          echo '<div class="bg-white p-4 shadow rounded">';
          echo '<h2 class="text-lg font-semibold">' . htmlspecialchars($produto['nome']) . '</h2>';
          echo '<p class="text-gray-600">Preço: R$ ' . number_format($produto['preco'], 2, ',', '.') . '</p>';
          echo '<p class="text-sm text-gray-500 mt-1">' . htmlspecialchars($produto['descricao']) . '</p>';
          echo '</div>';
        }
      } else {
        echo '<p class="text-red-500 col-span-full">Nenhum produto encontrado.</p>';
      }

      $stmt->close();
    } else {
      echo '<p class="text-gray-500 col-span-full">Digite algo para buscar.</p>';
    }

    $conn->close();
    ?>
  </div>
</main>
<style>
    body {
        font-family: Arial, sans-serif;
    }
</style>
<?php include '../includes/footer.php'; ?>