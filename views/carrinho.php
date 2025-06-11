<?php
session_start();
include '../includes/verifica_login.php';
include '../includes/header.php';
include '../backend/db.php';

$userId = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['item_id']) && isset($_POST['quantidade'])) {
        $itemId = intval($_POST['item_id']);
        $qtd = max(1, intval($_POST['quantidade']));
        $stmt = $conn->prepare("UPDATE itens_carrinho SET quantidade = ? WHERE id = ?");
        $stmt->bind_param("ii", $qtd, $itemId);
        $stmt->execute();
        exit;
    }

    if (isset($_POST['item_id']) && isset($_POST['remover'])) {
        $itemId = intval($_POST['item_id']);
        $stmt = $conn->prepare("DELETE FROM itens_carrinho WHERE id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        exit;
    }
}

$carrinhoId = null;
$stmt = $conn->prepare("SELECT id FROM carrinhos WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
if ($carrinho = $res->fetch_assoc()) {
    $carrinhoId = $carrinho['id'];
} else {
    echo "<main class='max-w-6xl mx-auto px-6 py-10'><p class='text-gray-500'>Seu carrinho está vazio.</p></main>";
    include '../includes/footer.php';
    exit;
}

$stmt = $conn->prepare("
  SELECT ic.id AS item_id, p.nome, p.preco, ic.quantidade, 
         ip.url_imagem, t.descricao AS tamanho
  FROM itens_carrinho ic
  JOIN produtos p ON ic.produto_id = p.id
  LEFT JOIN imagens_produto ip ON ip.produto_id = p.id
  LEFT JOIN tamanhos t ON ic.tamanho_id = t.id
  WHERE ic.carrinho_id = ?
  GROUP BY ic.id
");
$stmt->bind_param("i", $carrinhoId);
$stmt->execute();
$itens = $stmt->get_result();
?>

<main class="max-w-7xl mx-auto px-6 py-12">
    <h1 class="text-4xl font-bold text-center mb-10">🛒 Meu Carrinho</h1>

    <?php if ($itens->num_rows === 0): ?>
        <p class="text-gray-500">Seu carrinho está vazio.</p>
    <?php else: ?>
        <form action="finalizar_pedido.php" method="post">
            <div class="space-y-6" id="lista-itens">
                <?php $total = 0; ?>
                <?php while ($item = $itens->fetch_assoc()):
                    $subtotal = $item['preco'] * $item['quantidade'];
                    $total += $subtotal; ?>
                    <div class="flex items-center justify-between bg-white rounded-xl shadow-lg p-5 relative group transition-all duration-300" data-item-id="<?= $item['item_id'] ?>" data-preco="<?= $item['preco'] ?>">
                        <label class="absolute left-4 top-4">
                            <input type="checkbox" name="selecionados[]" value="<?= $item['item_id'] ?>" class="peer hidden">
                            <span class="w-5 h-5 border-2 border-gray-400 rounded-md block peer-checked:bg-green-600 peer-checked:border-green-600 transition duration-150"></span>
                        </label>
                        <div class="flex items-center gap-5">
                            <img src="../<?= htmlspecialchars($item['url_imagem']) ?>" class="w-28 h-28 object-contain bg-gray-100 rounded-lg">
                            <div>
                                <h2 class="text-lg font-semibold"><?= htmlspecialchars($item['nome']) ?></h2>
                                <?php if ($item['tamanho']): ?>
                                    <p class="text-sm text-gray-500">Tamanho: <?= htmlspecialchars($item['tamanho']) ?></p>
                                <?php endif; ?>
                                <p class="text-sm text-gray-500">Preço unitário: R$ <?= number_format($item['preco'], 2, ',', '.') ?></p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <div class="flex items-center gap-2">
                                <button class="btn-menos px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded-full"><i class="fa-solid fa-minus"></i></button>
                                <span class="qtd font-semibold text-lg"><?= $item['quantidade'] ?></span>
                                <button class="btn-mais px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded-full"><i class="fa-solid fa-plus"></i></button>
                            </div>
                            <span class="subtotal text-lg font-bold text-green-600">R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                            <button class="btn-remover text-red-500 text-sm hover:underline">Remover</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="text-right mt-10">
                <p class="text-2xl font-bold">Total: <span id="valor-total">R$ <?= number_format($total, 2, ',', '.') ?></span></p>
                <button type="submit" class="inline-block mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold shadow-md transition">
                    Finalizar Pedido com Selecionados
                </button>
            </div>
        </form>
    <?php endif; ?>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        function atualizarTotais() {
            let total = 0;
            $('#lista-itens > div[data-item-id]').each(function() {
                const preco = parseFloat($(this).data('preco'));
                const qtd = parseInt($(this).find('.qtd').text());
                const subtotal = preco * qtd;
                $(this).find('.subtotal').text('R$ ' + subtotal.toFixed(2).replace('.', ','));
                total += subtotal;
            });
            $('#valor-total').text('R$ ' + total.toFixed(2).replace('.', ','));
        }

        $('.btn-mais, .btn-menos').click(function() {
            const container = $(this).closest('[data-item-id]');
            const itemId = container.data('item-id');
            const qtdSpan = container.find('.qtd');
            let quantidade = parseInt(qtdSpan.text());

            if ($(this).hasClass('btn-mais')) {
                quantidade++;
            } else {
                if (quantidade > 1) quantidade--;
            }

            $.post('../backend/carrinho_ajax.php', {
                item_id: itemId,
                quantidade: quantidade
            }, function() {
                qtdSpan.text(quantidade);
                atualizarTotais();
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: 'Quantidade atualizada',
                    timer: 1200,
                    position: 'top-end',
                    showConfirmButton: false
                });
            });

        });

        $('.btn-remover').click(function() {
            const container = $(this).closest('[data-item-id]');
            const itemId = container.data('item-id');

            $.post('../backend/carrinho_ajax.php', {
                item_id: itemId,
                remover: true
            }, function() {
                container.fadeOut(300, function() {
                    $(this).remove();
                    atualizarTotais();
                    Swal.fire({
                        toast: true,
                        icon: 'info',
                        title: 'Item removido',
                        timer: 1200,
                        position: 'top-end',
                        showConfirmButton: false
                    });
                });
            });
        });
    });
</script>

<?php include '../includes/footer.php'; ?>