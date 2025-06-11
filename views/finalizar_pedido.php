<?php
include '../includes/verifica_login.php';
include '../includes/header.php';
include '../backend/db.php';

$usuario_id = $_SESSION['usuario_id'];

$sql_endereco = "SELECT * FROM enderecos WHERE user_id = $usuario_id LIMIT 1";
$res_end = mysqli_query($conn, $sql_endereco);
$endereco = ($res_end && mysqli_num_rows($res_end) > 0) ? mysqli_fetch_assoc($res_end) : null;

$produtos = [];

if (isset($_GET['produto_id'])) {
    $produto_id = $_GET['produto_id'];
    $quantidade = $_GET['quantidade'] ?? 1;
    $sql_produto = "SELECT id, nome, preco FROM produtos WHERE id = $produto_id";
    $res = mysqli_query($conn, $sql_produto);
    if ($res && mysqli_num_rows($res) > 0) {
        $produto = mysqli_fetch_assoc($res);
        $produto['quantidade'] = $quantidade;
        $produtos[] = $produto;
    }
} elseif (isset($_POST['selecionados']) && is_array($_POST['selecionados'])) {
    $ids = implode(',', array_map('intval', $_POST['selecionados']));
    $sql = "SELECT ic.id, p.nome, p.preco, ic.quantidade FROM itens_carrinho ic JOIN produtos p ON ic.produto_id = p.id WHERE ic.id IN ($ids)";
    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            $produtos[] = $row;
        }
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" data-aos="fade-up">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-gray-800">Finalizar Pedido</h1>
        <p class="text-gray-500 mt-2">Revise as informações antes de confirmar.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Resumo dos Produtos</h2>
            <ul>
                <?php if (!empty($produtos)): ?>
                    <?php foreach ($produtos as $item): ?>
                        <li class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($item['nome']); ?> (x<?php echo $item['quantidade']; ?>)</span>
                            <span class="text-green-600 font-semibold">R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="text-red-600">Nenhum produto selecionado.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div>
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Endereço de Entrega</h2>
                <?php if ($endereco): ?>
                    <p class="text-gray-700">
                        <?php echo $endereco['rua'] . ', Nº ' . $endereco['numero']; ?>
                        <?php if (!empty($endereco['complemento'])) echo ' - ' . $endereco['complemento']; ?>,
                        <?php echo $endereco['bairro'] . ', ' . $endereco['cidade'] . ' - ' . $endereco['estado'] . ', ' . $endereco['cep'] . ', ' . $endereco['pais']; ?>
                    </p>
                <?php else: ?>
                    <p class="text-yellow-600">Nenhum endereço encontrado.</p>
                    <button class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded" onclick="document.getElementById('modalEndereco').classList.remove('hidden')">Cadastrar Endereço</button>
                <?php endif; ?>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Forma de Pagamento</h2>
                <form method="POST" action="../backend/processar_pedido.php" class="space-y-4">
                    <?php foreach ($produtos as $item): ?>
                        <input type="hidden" name="selecionados[]" value="<?= $item['id'] ?>">
                    <?php endforeach; ?>

                    <label class="block">
                        <input type="radio" name="pagamento" value="pix" class="mr-2" onclick="toggleCartao(false)" checked> Pix
                    </label>
                    <label class="block">
                        <input type="radio" name="pagamento" value="cartao" class="mr-2" onclick="toggleCartao(true)"> Cartão de Crédito
                    </label>
                    <label class="block">
                        <input type="radio" name="pagamento" value="boleto" class="mr-2" onclick="toggleCartao(false)"> Boleto
                    </label>

                    <div id="dados-cartao" class="hidden space-y-3">
                        <input type="text" name="numero_cartao" placeholder="Número do Cartão" class="w-full border p-2 rounded">
                        <input type="text" name="nome_cartao" placeholder="Nome no Cartão" class="w-full border p-2 rounded">
                        <div class="flex gap-4">
                            <input type="text" name="validade" placeholder="Validade (MM/AA)" class="flex-1 border p-2 rounded">
                            <input type="text" name="cvv" placeholder="CVV" class="flex-1 border p-2 rounded">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-4">Confirmar Pedido</button>
                </form>
            </div>
        </div>
    </div>
</main>

<div id="modalEndereco" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-xl">
        <h2 class="text-2xl font-bold mb-4">Cadastrar Endereço</h2>
        <form id="formEndereco">
            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="rua" placeholder="Rua" class="border p-2 rounded" required>
                <input type="text" name="numero" placeholder="Número" class="border p-2 rounded" required>
                <input type="text" name="complemento" placeholder="Complemento" class="border p-2 rounded">
                <input type="text" name="bairro" placeholder="Bairro" class="border p-2 rounded" required>
                <input type="text" name="cidade" placeholder="Cidade" class="border p-2 rounded" required>
                <input type="text" name="estado" placeholder="Estado" class="border p-2 rounded" required>
                <input type="text" name="cep" placeholder="CEP" class="border p-2 rounded" required>
                <input type="text" name="pais" placeholder="País" class="border p-2 rounded" required>
            </div>
            <input type="hidden" name="user_id" value="<?= $usuario_id ?>">
            <div class="flex justify-end mt-4">
                <button type="button" onclick="document.getElementById('modalEndereco').classList.add('hidden')" class="mr-4 text-gray-600">Cancelar</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
AOS.init();

function toggleCartao(ativo) {
    document.getElementById('dados-cartao').classList.toggle('hidden', !ativo);
}

document.getElementById('formEndereco').addEventListener('submit', function (e) {
    e.preventDefault();
    const form = e.target;
    const data = new FormData(form);
    fetch('../backend/cadastrar_endereco.php', {
        method: 'POST',
        body: data
    }).then(res => res.text())
      .then(() => location.reload());
});
</script>

<?php include '../includes/footer.php'; ?>
