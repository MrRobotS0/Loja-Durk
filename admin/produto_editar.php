<?php
include 'includes/auth.php';
include '../backend/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$produto = $resultado->fetch_assoc();
$stmt->close();

if (!$produto) {
    header("Location: produtos.php");
    exit();
}

$categorias = $conn->query("SELECT id, nome FROM categorias ORDER BY nome");
$marcas = $conn->query("SELECT id, nome FROM marcas ORDER BY nome");
$tamanhos = $conn->query("SELECT id, descricao FROM tamanhos ORDER BY descricao");

$tamanhosSelecionados = $conn->query("SELECT tamanho_id FROM produto_tamanhos WHERE produto_id = $id");
$tamanhosAtuais = array();
while ($t = $tamanhosSelecionados->fetch_assoc()) {
    $tamanhosAtuais[] = $t['tamanho_id'];
}

if (isset($_GET['remover_imagem'])) {
    $imgId = intval($_GET['remover_imagem']);
    $conn->query("DELETE FROM imagens_produto WHERE id = $imgId AND produto_id = $id");
    header("Location: produto_editar.php?id=$id");
    exit();
}

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
    $novosTamanhos = $_POST['tamanhos'] ?? [];

    $stmt = $conn->prepare("UPDATE produtos SET nome = ?, descricao = ?, preco = ?, estoque = ?, categoria_id = ?, marca_id = ?, genero = ?, slug = ? WHERE id = ?");
    $stmt->bind_param("ssdiiissi", $nome, $descricao, $preco, $estoque, $categoria_id, $marca_id, $genero, $slug, $id);

    if ($stmt->execute()) {
        $conn->query("DELETE FROM produto_tamanhos WHERE produto_id = $id");
        foreach ($novosTamanhos as $tid) {
            $conn->query("INSERT INTO produto_tamanhos (produto_id, tamanho_id) VALUES ($id, " . intval($tid) . ")");
        }

        if (!empty($_FILES['imagens']['name'][0])) {
            foreach ($_FILES['imagens']['tmp_name'] as $i => $tmpPath) {
                $nomeImg = uniqid() . "_" . basename($_FILES['imagens']['name'][$i]);
                $destino = "../views/imagens/$nomeImg";
                $urlImagem = "views/imagens/$nomeImg";
                if (move_uploaded_file($tmpPath, $destino)) {
                    $conn->query("INSERT INTO imagens_produto (produto_id, url_imagem, ordem) VALUES ($id, '$urlImagem', $i)");
                }
            }
        }

        $mensagem = '<p class="text-green-600">Produto atualizado com sucesso!</p>';
    } else {
        $mensagem = '<p class="text-red-500">Erro ao atualizar produto: ' . $stmt->error . '</p>';
    }

    $stmt->close();
}

$imagens = $conn->query("SELECT id, url_imagem FROM imagens_produto WHERE produto_id = $id ORDER BY ordem");
?>

<?php include 'includes/header.php'; ?>

<main class="bg-gray-100 min-h-screen p-8">
  <div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">✏️ Editar Produto</h1>
    <?= $mensagem ?>
    <form method="POST" enctype="multipart/form-data" class="space-y-5">

      <div>
        <label class="block font-medium text-sm mb-1">Nome</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Descrição</label>
        <textarea name="descricao" rows="4" required class="w-full px-4 py-2 border rounded-lg"><?= htmlspecialchars($produto['descricao']) ?></textarea>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block font-medium text-sm mb-1">Preço</label>
          <input type="text" name="preco" value="<?= 'R$ ' . number_format($produto['preco'], 2, ',', '.') ?>" required class="w-full px-4 py-2 border rounded-lg" />
        </div>
        <div>
          <label class="block font-medium text-sm mb-1">Estoque</label>
          <input type="number" name="estoque" value="<?= $produto['estoque'] ?>" required class="w-full px-4 py-2 border rounded-lg" />
        </div>
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Gênero</label>
        <select name="genero" class="w-full px-4 py-2 border rounded-lg">
          <option value="masculino" <?= $produto['genero'] === 'masculino' ? 'selected' : '' ?>>Masculino</option>
          <option value="feminino" <?= $produto['genero'] === 'feminino' ? 'selected' : '' ?>>Feminino</option>
          <option value="unissex" <?= $produto['genero'] === 'unissex' ? 'selected' : '' ?>>Unissex</option>
        </select>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block font-medium text-sm mb-1">Categoria</label>
          <select name="categoria_id" class="w-full px-4 py-2 border rounded-lg">
            <?php while ($c = $categorias->fetch_assoc()): ?>
              <option value="<?= $c['id'] ?>" <?= $produto['categoria_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nome']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div>
          <label class="block font-medium text-sm mb-1">Marca</label>
          <select name="marca_id" class="w-full px-4 py-2 border rounded-lg">
            <?php while ($m = $marcas->fetch_assoc()): ?>
              <option value="<?= $m['id'] ?>" <?= $produto['marca_id'] == $m['id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['nome']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Slug</label>
        <input type="text" name="slug" value="<?= htmlspecialchars($produto['slug']) ?>" class="w-full px-4 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Tamanhos Disponíveis</label>
        <div class="grid grid-cols-3 gap-2">
          <?php
          $tamanhos = $conn->query("SELECT id, descricao FROM tamanhos ORDER BY descricao");
          while ($t = $tamanhos->fetch_assoc()): ?>
            <label class="flex items-center space-x-2">
              <input type="checkbox" name="tamanhos[]" value="<?= $t['id'] ?>" <?= in_array($t['id'], $tamanhosAtuais) ? 'checked' : '' ?> />
              <span><?= htmlspecialchars($t['descricao']) ?></span>
            </label>
          <?php endwhile; ?>
        </div>
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Adicionar Novas Imagens</label>
        <input type="file" name="imagens[]" multiple accept="image/*" class="w-full" />
        <p class="text-xs text-gray-500">Você pode enviar novas imagens sem apagar as existentes.</p>
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Imagens Atuais</label>
        <div class="flex flex-wrap gap-3">
          <?php while ($img = $imagens->fetch_assoc()): ?>
            <div class="relative">
              <img src="../<?= $img['url_imagem'] ?>" class="w-24 h-24 object-cover rounded shadow" />
              <a href="?id=<?= $id ?>&remover_imagem=<?= $img['id'] ?>" onclick="return confirm('Remover esta imagem?')"
                 class="absolute top-0 right-0 text-white bg-red-600 hover:bg-red-700 rounded px-2">×</a>
            </div>
          <?php endwhile; ?>
        </div>
      </div>

      <div class="pt-4">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
          Atualizar Produto
        </button>
        <a href="produtos.php" class="ml-4 text-sm text-gray-600 hover:underline">← Voltar</a>
      </div>
    </form>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
