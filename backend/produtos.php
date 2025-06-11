<?php
include 'db.php';
?>

<style>
.produtos-container {
    text-align: center;
    padding: 40px 20px;
}

.produtos-container h2 {
    font-size: 2rem;
    margin-bottom: 20px;
}

.produtos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.produto-card {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 15px;
    background-color: #fff;
    transition: transform 0.3s;
}

.produto-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.produto-card img {
    width: 100%;
    height: auto;
    border-radius: 5px;
    margin-bottom: 10px;
}
</style>

<?php
$query = "SELECT * FROM produtos LIMIT 4";
$result = mysqli_query($conn, $query);

echo '<section class="produtos-container">';
echo '<h2>Novidades</h2>';
echo '<div class="produtos-grid">';

if (mysqli_num_rows($result) > 0) {
    while ($produto = mysqli_fetch_assoc($result)) {
        echo '<div class="produto-card">';
        echo '<img src="imagens/sem-imagem.png" alt="Produto" />';
        echo '<h3>' . htmlspecialchars($produto['nome']) . '</h3>';
        echo '<p>' . htmlspecialchars($produto['descricao']) . '</p>';
        echo '<p><strong>R$ ' . number_format($produto['preco'], 2, ',', '.') . '</strong></p>';
        echo '</div>';
    }
} else {
    echo '<p>Nenhum produto encontrado.</p>';
}

echo '</div>';
echo '</section>';
?>
