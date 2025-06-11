<?php
include '../includes/verifica_login.php';
include '../backend/db.php';
include '../includes/header.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['usuario_id'];
    $rua = trim($_POST['rua']);
    $numero = trim($_POST['numero']);
    $complemento = trim($_POST['complemento']);
    $bairro = trim($_POST['bairro']);
    $cidade = trim($_POST['cidade']);
    $estado = trim($_POST['estado']);
    $cep = trim($_POST['cep']);
    $pais = trim($_POST['pais']);

    $complemento = !empty($_POST['complemento']) ? trim($_POST['complemento']) : null;

    $stmt = $conn->prepare("INSERT INTO enderecos 
    (user_id, rua, numero, complemento, bairro, cidade, estado, cep, pais) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("issssssss", $userId, $rua, $numero, $complemento, $bairro, $cidade, $estado, $cep, $pais);
        $stmt->execute();
        $stmt->close();
        header("Location: minhaconta.php");
        exit();
    } else {
        die("Erro na preparação da query: " . $conn->error);
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Adicionar Endereço</title>
  <link rel="icon" type="image/png" href="imagens/logo.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@animxyz/core" />
</head>

<body class="bg-gray-100">
  <main class="max-w-xl mx-auto py-12 px-6 animxyz-in" xyz="fade small duration-10">
    <div class="bg-white shadow-xl rounded-xl p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">🏠 Adicionar Endereço</h1>

      <?php if (isset($erro)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <?= htmlspecialchars($erro) ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <input type="text" name="rua" placeholder="Rua" required class="input-style">
        <input type="text" name="numero" placeholder="Número" required class="input-style">
        <input type="text" name="complemento" placeholder="Complemento" class="input-style">
        <input type="text" name="bairro" placeholder="Bairro" required class="input-style">
        <input type="text" name="cidade" placeholder="Cidade" required class="input-style">
        <input type="text" name="estado" placeholder="Estado" required class="input-style">
        <input type="text" name="cep" placeholder="CEP" required class="input-style">
        <input type="text" name="pais" placeholder="País" required class="input-style">

        <div class="sm:col-span-2 flex justify-between mt-4">
          <a href="minhaconta.php" class="px-4 py-2 text-sm rounded bg-gray-200 hover:bg-gray-300 transition">← Voltar</a>
          <button type="submit" class="px-6 py-2 text-white bg-green-600 hover:bg-green-700 rounded transition">
            Salvar Endereço
          </button>
        </div>
      </form>
    </div>
  </main>

  <style>
    .input-style {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      width: 100%;
      font-size: 0.95rem;
    }
    body {
      font-family: Arial, sans-serif;
    }
  </style>

  <?php include '../includes/footer.php'; ?>
</body>
</html>
