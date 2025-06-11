<?php
include 'includes/auth.php';
include '../backend/db.php';

$totalUsuarios = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$totalProdutos = $conn->query("SELECT COUNT(*) AS total FROM produtos")->fetch_assoc()['total'];
$totalPedidos = $conn->query("SELECT COUNT(*) AS total FROM pedidos")->fetch_assoc()['total'];

include 'includes/header.php';
?>

<main class="bg-gradient-to-b from-gray-100 to-white min-h-screen p-8 font-sans">
  <div class="max-w-7xl mx-auto space-y-12">
    <h1 class="text-3xl font-bold text-gray-800 text-center">Painel Administrativo</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition">
        <h2 class="text-sm text-gray-500 uppercase">Usuários Cadastrados</h2>
        <p class="text-4xl font-bold text-blue-600 mt-2"><?= $totalUsuarios ?></p>
      </div>
      <div class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition">
        <h2 class="text-sm text-gray-500 uppercase">Produtos Ativos</h2>
        <p class="text-4xl font-bold text-green-600 mt-2"><?= $totalProdutos ?></p>
      </div>
      <div class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition">
        <h2 class="text-sm text-gray-500 uppercase">Total de Pedidos</h2>
        <p class="text-4xl font-bold text-purple-600 mt-2"><?= $totalPedidos ?></p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-2xl shadow p-6 flex flex-col items-center text-center hover:shadow-xl transition">
        <h2 class="text-lg font-bold text-gray-800 mb-2">Usuários</h2>
        <p class="text-sm text-gray-500 mb-4">Gerencie os usuários da loja.</p>
        <a href="users.php" class="bg-blue-600 text-white px-5 py-2 rounded-full hover:bg-blue-700 transition">Acessar</a>
      </div>
      <div class="bg-white rounded-2xl shadow p-6 flex flex-col items-center text-center hover:shadow-xl transition">
        <h2 class="text-lg font-bold text-gray-800 mb-2">Produtos</h2>
        <p class="text-sm text-gray-500 mb-4">Controle e cadastre produtos.</p>
        <a href="produtos.php" class="bg-green-600 text-white px-5 py-2 rounded-full hover:bg-green-700 transition">Acessar</a>
      </div>
      <div class="bg-white rounded-2xl shadow p-6 flex flex-col items-center text-center hover:shadow-xl transition">
        <h2 class="text-lg font-bold text-gray-800 mb-2">Filtros</h2>
        <p class="text-sm text-gray-500 mb-4">Gerencie marcas, tamanhos e categorias.</p>
        <a href="filtros.php" class="bg-yellow-500 text-white px-5 py-2 rounded-full hover:bg-yellow-600 transition">Acessar</a>
      </div>
      <div class="bg-white rounded-2xl shadow p-6 flex flex-col items-center text-center hover:shadow-xl transition">
        <h2 class="text-lg font-bold text-gray-800 mb-2">Pedidos</h2>
        <p class="text-sm text-gray-500 mb-4">Visualize e controle os pedidos.</p>
        <a href="pedidos.php" class="bg-gray-700 text-white px-5 py-2 rounded-full hover:bg-gray-800 transition">Acessar</a>
      </div>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
