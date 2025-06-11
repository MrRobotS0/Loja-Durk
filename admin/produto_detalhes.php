<?php
include 'includes/auth.php';
include '../backend/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("
    SELECT p.*, c.nome AS categoria, m.nome AS marca
    FROM produtos p
    LEFT JOIN categorias c ON p.categoria_id = c.id
    LEFT JOIN marcas m ON p.marca_id = m.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$produto = $resultado->fetch_assoc();
$stmt->close();

if (!$produto) {
    header('Location: produtos.php');
    exit();
}

$imagens = $conn->query("SELECT url_imagem FROM imagens_produto WHERE produto_id = $id");

$tamanhos = $conn->query("
    SELECT t.descricao FROM produto_tamanhos pt
    JOIN tamanhos t ON pt.tamanho_id = t.id
    WHERE pt.produto_id = $id
");
?>

<?php include 'includes/header.php'; ?>

<main class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">🧾 Detalhes do Produto</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="font-semibold text-lg text-gray-700 mb-2">📌 Informações principais</h2>
                <p><strong>Nome:</strong> <?= htmlspecialchars($produto['nome']) ?></p>
                <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
                <p><strong>Preço:</strong> R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                <p><strong>Estoque:</strong> <?= $produto['estoque'] ?></p>
                <p><strong>Gênero:</strong> <?= htmlspecialchars($produto['genero']) ?></p>
                <p><strong>Categoria:</strong> <?= htmlspecialchars($produto['categoria']) ?></p>
                <p><strong>Marca:</strong> <?= htmlspecialchars($produto['marca']) ?></p>
                <p><strong>Slug:</strong> <?= htmlspecialchars($produto['slug']) ?></p>
                <p><strong>Data de Cadastro:</strong> <?= date('d/m/Y H:i', strtotime($produto['data_cadastro'])) ?></p>
            </div>

            <div>
                <h2 class="font-semibold text-lg text-gray-700 mb-2">📷 Imagens do Produto</h2>
                <?php if ($imagens && $imagens->num_rows > 0): ?>
                    <div class="flex flex-wrap gap-4">
                        <?php while ($img = $imagens->fetch_assoc()): ?>
                            <img src="../<?= $img['url_imagem'] ?>" alt="Imagem do Produto"
                                class="w-32 h-32 object-cover rounded shadow" />
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">Nenhuma imagem cadastrada.</p>
                <?php endif; ?>

                <h2 class="font-semibold text-lg text-gray-700 mt-6 mb-2">📏 Tamanhos disponíveis</h2>
                <?php if ($tamanhos && $tamanhos->num_rows > 0): ?>
                    <ul class="list-disc list-inside text-sm text-gray-700">
                        <?php while ($t = $tamanhos->fetch_assoc()): ?>
                            <li><?= htmlspecialchars($t['descricao']) ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-500">Nenhum tamanho vinculado.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-6">
            <a href="produtos.php"
                class="text-sm text-gray-600 hover:underline">← Voltar para lista de produtos</a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>