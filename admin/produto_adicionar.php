<?php
include 'includes/auth.php';
include '../backend/db.php';

$categorias = $conn->query("SELECT id, nome FROM categorias ORDER BY nome");
$marcas = $conn->query("SELECT id, nome FROM marcas ORDER BY nome");
$tamanhos = $conn->query("SELECT id, descricao FROM tamanhos ORDER BY descricao");

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = str_replace(['R$', '.', ','], ['', '', '.'], $_POST['preco']);
    $preco = floatval($preco);
    $estoque = intval($_POST['estoque']);
    $genero = $_POST['genero'];
    $categoria_id = intval($_POST['categoria_id']);
    $marca_id = intval($_POST['marca_id']);
    $slug = trim($_POST['slug']);
    $tamanhosSelecionados = $_POST['tamanhos'] ?? [];

    $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, categoria_id, marca_id, genero, slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiiiss", $nome, $descricao, $preco, $estoque, $categoria_id, $marca_id, $genero, $slug);

    if ($stmt->execute()) {
        $produto_id = $stmt->insert_id;

        foreach ($tamanhosSelecionados as $tid) {
            $conn->query("INSERT INTO produto_tamanhos (produto_id, tamanho_id) VALUES ($produto_id, " . intval($tid) . ")");
        }

        if (!empty($_FILES['imagens']['name'][0])) {
            foreach ($_FILES['imagens']['tmp_name'] as $i => $tmpPath) {
                $nomeImg = uniqid() . "_" . basename($_FILES['imagens']['name'][$i]);
                $destinoFisico = "../views/imagens/$nomeImg";
                $urlImagem = "views/imagens/$nomeImg";
                if (move_uploaded_file($tmpPath, $destinoFisico)) {
                    $conn->query("INSERT INTO imagens_produto (produto_id, url_imagem, ordem) VALUES ($produto_id, '$urlImagem', $i)");
                }
            }
        }

        header("Location: produtos.php");
        exit();
    } else {
        $mensagem = '<p class="text-red-500">Erro ao cadastrar produto: ' . $stmt->error . '</p>';
    }

    $stmt->close();
}
?>

<?php include 'includes/header.php'; ?>

<div class="wrapper">
<main class="bg-gray-100 min-h-screen p-8">
  <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">➕ Adicionar Produto</h1>
    <?= $mensagem ?>
    <form method="POST" enctype="multipart/form-data" class="space-y-5">

      <div>
        <label class="block font-medium text-sm mb-1">Nome</label>
        <input type="text" name="nome" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Descrição</label>
        <textarea name="descricao" rows="4" required class="w-full px-4 py-2 border rounded-lg"></textarea>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block font-medium text-sm mb-1">Preço</label>
          <input type="text" name="preco" required class="w-full px-4 py-2 border rounded-lg" placeholder="R$ 0,00" />
        </div>
        <div>
          <label class="block font-medium text-sm mb-1">Estoque</label>
          <input type="number" name="estoque" required class="w-full px-4 py-2 border rounded-lg" />
        </div>
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Gênero</label>
        <select name="genero" class="w-full px-4 py-2 border rounded-lg">
          <option value="masculino">Masculino</option>
          <option value="feminino">Feminino</option>
          <option value="unissex">Unissex</option>
        </select>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block font-medium text-sm mb-1">Categoria</label>
          <select name="categoria_id" class="w-full px-4 py-2 border rounded-lg">
            <?php while ($c = $categorias->fetch_assoc()): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div>
          <label class="block font-medium text-sm mb-1">Marca</label>
          <select name="marca_id" class="w-full px-4 py-2 border rounded-lg">
            <?php while ($m = $marcas->fetch_assoc()): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nome']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Slug</label>
        <input type="text" name="slug" class="w-full px-4 py-2 border rounded-lg" placeholder="ex: camiseta-preta-algodao" />
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Tamanhos disponíveis</label>
        <div class="grid grid-cols-3 gap-2">
          <?php while ($t = $tamanhos->fetch_assoc()): ?>
            <label class="flex items-center space-x-2">
              <input type="checkbox" name="tamanhos[]" value="<?= $t['id'] ?>" />
              <span><?= htmlspecialchars($t['descricao']) ?></span>
            </label>
          <?php endwhile; ?>
        </div>
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Imagens</label>
        <input type="file" name="imagens[]" multiple accept="image/*" class="w-full" />
        <p class="text-xs text-gray-500">Você pode selecionar várias imagens.</p>
      </div>

      <div class="pt-4">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
          Cadastrar Produto
        </button>
        <a href="produtos.php" class="ml-4 text-sm text-gray-600 hover:underline">← Voltar</a>
      </div>
    </form>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
</div>

<script>
  const precoInput = document.querySelector('input[name="preco"]');

  precoInput.addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, '');
    value = (parseInt(value) / 100).toFixed(2);
    e.target.value = 'R$ ' + value.replace('.', ',');
  });

  precoInput.addEventListener('focus', function () {
    if (!this.value) this.value = 'R$ 0,00';
  });

  precoInput.addEventListener('blur', function () {
    if (this.value === 'R$ 0,00') this.value = '';
  });
</script>
