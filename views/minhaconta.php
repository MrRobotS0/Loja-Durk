<?php
include '../includes/verifica_login.php';
include '../backend/db.php';

$id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_endereco'])) {
    $resEndereco = $conn->query("SELECT id FROM enderecos WHERE user_id = $id LIMIT 1");
    if ($resEndereco && $resEndereco->num_rows > 0) {
        $enderecoId = $resEndereco->fetch_assoc()['id'];

        $conn->query("UPDATE pedidos SET endereco_id = NULL WHERE endereco_id = $enderecoId");

        $conn->query("DELETE FROM enderecos WHERE id = $enderecoId");
    }

    header("Location: minhaconta.php");
    exit();
}

$usuario = $conn->query("SELECT nome, email, telefone FROM users WHERE id = $id")->fetch_assoc();
$endereco = $conn->query("SELECT * FROM enderecos WHERE user_id = $id LIMIT 1")->fetch_assoc();
$pedidos = $conn->query("SELECT * FROM pedidos WHERE user_id = $id ORDER BY data_pedido DESC");

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Minha Conta - Loja Durk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@animxyz/core" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body class="bg-gray-100 font-sans">
    <main class="max-w-6xl mx-auto py-12 px-4 animxyz-in" xyz="fade small duration-10">
        <div class="bg-white shadow-xl rounded-2xl p-6 flex flex-col md:flex-row gap-6 items-start">

            <aside class="w-full md:w-1/4">
                <ul class="space-y-3">
                    <li><button onclick="mostrarAba('conta')" class="w-full text-left px-4 py-2 bg-purple-100 hover:bg-purple-200 text-purple-700 font-semibold rounded-lg transition flex items-center gap-2"><i class="fa-solid fa-user"></i> Minha Conta</button></li>
                    <li><button onclick="mostrarAba('endereco')" class="w-full text-left px-4 py-2 bg-purple-100 hover:bg-purple-200 text-purple-700 font-semibold rounded-lg transition flex items-center gap-2"><i class="fa-solid fa-house"></i> Endereços</button></li>
                    <li><button onclick="mostrarAba('pedidos')" class="w-full text-left px-4 py-2 bg-purple-100 hover:bg-purple-200 text-purple-700 font-semibold rounded-lg transition flex items-center gap-2"><i class="fa-solid fa-box"></i> Meus Pedidos</button></li>
                </ul>
            </aside>

            <section class="flex-1 space-y-10">
                <div id="aba-conta" class="tab">
                    <h2 class="text-3xl font-bold mb-6 text-gray-800 border-b pb-2">👤 Meus Dados</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-gray-700">
                        <div>
                            <p class="text-sm text-gray-500">Nome:</p>
                            <p class="text-lg font-semibold"><?= htmlspecialchars($usuario['nome']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email:</p>
                            <p class="text-lg font-semibold"><?= htmlspecialchars($usuario['email']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Telefone:</p>
                            <p class="text-lg font-semibold"><?= htmlspecialchars($usuario['telefone']) ?></p>
                        </div>
                    </div>
                </div>

                <div id="aba-endereco" class="tab hidden">
                    <h2 class="text-3xl font-bold mb-6 mt-0 text-gray-800 border-b pb-2">🏠 Meu Endereço</h2>
                    <?php if ($endereco): ?>
                        <div class="text-gray-700 space-y-2">
                            <p><strong>Rua:</strong> <?= htmlspecialchars($endereco['rua']) ?></p>
                            <p><strong>Número:</strong> <?= htmlspecialchars($endereco['numero']) ?></p>
                            <p><strong>Bairro:</strong> <?= htmlspecialchars($endereco['bairro']) ?></p>
                            <p><strong>Cidade:</strong> <?= htmlspecialchars($endereco['cidade']) ?></p>
                            <p><strong>Estado:</strong> <?= htmlspecialchars($endereco['estado']) ?></p>
                            <p><strong>CEP:</strong> <?= htmlspecialchars($endereco['cep']) ?></p>
                        </div>
                        <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este endereço?')">
                            <button type="submit" name="excluir_endereco" class="mt-6 bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg shadow">🗑️ Excluir Endereço</button>
                        </form>
                    <?php else: ?>
                        <p class="text-gray-600 mb-4">Você ainda não cadastrou um endereço.</p>
                        <a href="endereco_adicionar.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">➕ Adicionar Endereço</a>
                    <?php endif; ?>
                </div>

                <div id="aba-pedidos" class="tab hidden">
                    <h2 class="text-3xl font-bold mb-6 mt-0 text-gray-800 border-b pb-2">📦 Meus Pedidos</h2>
                    <?php if ($pedidos->num_rows > 0): ?>
                        <ul class="space-y-4">
                            <?php while ($p = $pedidos->fetch_assoc()): ?>
                                <li class="p-4 bg-white border rounded-lg shadow-sm hover:shadow-md transition">
                                    <p><strong>ID Pedido:</strong> #<?= $p['id'] ?></p>
                                    <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($p['data_pedido'])) ?></p>
                                    <p><strong>Status:</strong> <?= htmlspecialchars($p['status']) ?></p>
                                    <p><a href="pedido_detalhes.php?id=<?= $p['id'] ?>" class="text-blue-600 hover:underline">🔎 Ver Detalhes</a></p>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-gray-600">Você ainda não realizou nenhum pedido.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <script>
        function mostrarAba(id) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.add('hidden'));
            document.getElementById('aba-' + id).classList.remove('hidden');
        }
    </script>

    <?php include '../includes/footer.php'; ?>
</body>

</html>