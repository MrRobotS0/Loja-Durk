<?php
include 'includes/auth.php';
include '../backend/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$produto) {
    header("Location: produtos.php");
    exit();
}

if (isset($_GET['remover_imagem'])) {
    $imgId = (int) $_GET['remover_imagem'];
    $stmt = $conn->prepare("DELETE FROM imagens_produto WHERE id = ? AND produto_id = ?");
    $stmt->bind_param("ii", $imgId, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: produto_editar.php?id=$id");
    exit();
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = (float) str_replace(['R$', '.', ','], ['', '', '.'], $_POST['preco']);
    $estoque = (int) $_POST['estoque'];
    $genero = $_POST['genero'];
    $categoria_id = (int) $_POST['categoria_id'];
    $marca_id = (int) $_POST['marca_id'];
    $slug = trim($_POST['slug']);
    $novosTamanhos = $_POST['tamanhos'] ?? [];

    $stmt = $conn->prepare("UPDATE produtos SET nome = ?, descricao = ?, preco = ?, estoque = ?, categoria_id = ?, marca_id = ?, genero = ?, slug = ? WHERE id = ?");
    $stmt->bind_param("ssdiiissi", $nome, $descricao, $preco, $estoque, $categoria_id, $marca_id, $genero, $slug, $id);

    if ($stmt->execute()) {
        $stmt->close();

        $del = $conn->prepare("DELETE FROM produto_tamanhos WHERE produto_id = ?");
        $del->bind_param("i", $id); $del->execute(); $del->close();

        if (!empty($novosTamanhos)) {
            $stmtT = $conn->prepare("INSERT INTO produto_tamanhos (produto_id, tamanho_id) VALUES (?, ?)");
            foreach ($novosTamanhos as $tid) {
                $tidInt = (int) $tid;
                $stmtT->bind_param("ii", $id, $tidInt);
                $stmtT->execute();
            }
            $stmtT->close();
        }

        if (!empty($_FILES['imagens']['name'][0])) {
            $stmtI = $conn->prepare("INSERT INTO imagens_produto (produto_id, url_imagem, ordem) VALUES (?, ?, ?)");
            foreach ($_FILES['imagens']['tmp_name'] as $i => $tmpPath) {
                $nomeImg = uniqid() . "_" . basename($_FILES['imagens']['name'][$i]);
                $destino = "../views/imagens/$nomeImg";
                $urlImagem = "views/imagens/$nomeImg";
                if (move_uploaded_file($tmpPath, $destino)) {
                    $stmtI->bind_param("isi", $id, $urlImagem, $i);
                    $stmtI->execute();
                }
            }
            $stmtI->close();
        }

        $produtoStmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
        $produtoStmt->bind_param("i", $id);
        $produtoStmt->execute();
        $produto = $produtoStmt->get_result()->fetch_assoc();
        $produtoStmt->close();

        $mensagem = 'ok';
    } else {
        $mensagem = 'erro';
        $stmt->close();
    }
}

$tamSel = $conn->prepare("SELECT tamanho_id FROM produto_tamanhos WHERE produto_id = ?");
$tamSel->bind_param("i", $id);
$tamSel->execute();
$tamSelRes = $tamSel->get_result();
$tamanhosAtuais = [];
while ($t = $tamSelRes->fetch_assoc()) $tamanhosAtuais[] = (int) $t['tamanho_id'];
$tamSel->close();

$categorias = $conn->query("SELECT id, nome FROM categorias ORDER BY nome");
$marcas = $conn->query("SELECT id, nome FROM marcas ORDER BY nome");
$tamanhos = $conn->query("SELECT id, descricao FROM tamanhos ORDER BY id");

$imgStmt = $conn->prepare("SELECT id, url_imagem FROM imagens_produto WHERE produto_id = ? ORDER BY ordem");
$imgStmt->bind_param("i", $id);
$imgStmt->execute();
$imagens = $imgStmt->get_result();

include 'includes/header.php';
?>

<main class="bg-zinc-950/40 min-h-screen p-4 sm:p-6 lg:p-10">
  <div class="max-w-4xl mx-auto">
    <div class="mb-8">
      <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Edição #<?= $id ?></span>
      <h1 class="font-display text-5xl text-white mt-2">EDITAR PRODUTO</h1>
    </div>

    <?php if ($mensagem === 'ok'): ?>
      <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-lg px-4 py-3 mb-5">
        <i class="fa-solid fa-check"></i> Produto atualizado com sucesso!
      </div>
    <?php elseif ($mensagem === 'erro'): ?>
      <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-lg px-4 py-3 mb-5">
        Erro ao atualizar produto.
      </div>
    <?php endif; ?>

    <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 md:p-8">
      <form method="POST" enctype="multipart/form-data" class="space-y-6">

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Nome</label>
          <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
        </div>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Descrição</label>
          <textarea name="descricao" rows="4" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition resize-none"><?= htmlspecialchars($produto['descricao']) ?></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Preço</label>
            <input type="text" name="preco" value="<?= 'R$ ' . number_format($produto['preco'], 2, ',', '.') ?>" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
          </div>
          <div>
            <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Estoque</label>
            <input type="number" min="0" name="estoque" value="<?= (int) $produto['estoque'] ?>" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
          </div>
          <div>
            <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Gênero</label>
            <select name="genero" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
              <option value="masculino" <?= $produto['genero'] === 'masculino' ? 'selected' : '' ?>>Masculino</option>
              <option value="feminino" <?= $produto['genero'] === 'feminino' ? 'selected' : '' ?>>Feminino</option>
              <option value="unissex" <?= $produto['genero'] === 'unissex' ? 'selected' : '' ?>>Unissex</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Categoria</label>
            <select name="categoria_id" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
              <?php while ($c = $categorias->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>" <?= $produto['categoria_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nome']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div>
            <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Marca</label>
            <select name="marca_id" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
              <?php while ($m = $marcas->fetch_assoc()): ?>
                <option value="<?= $m['id'] ?>" <?= $produto['marca_id'] == $m['id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['nome']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Slug</label>
          <input type="text" name="slug" value="<?= htmlspecialchars($produto['slug']) ?>" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
        </div>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-3">Tamanhos Disponíveis</label>
          <div class="flex flex-wrap gap-2">
            <?php while ($t = $tamanhos->fetch_assoc()): ?>
              <label class="cursor-pointer">
                <input type="checkbox" name="tamanhos[]" value="<?= $t['id'] ?>" <?= in_array((int) $t['id'], $tamanhosAtuais, true) ? 'checked' : '' ?> class="hidden peer" />
                <span class="inline-block px-4 py-2 bg-black border border-zinc-800 text-zinc-400 rounded-lg text-sm font-bold peer-checked:bg-yellow-400 peer-checked:text-black peer-checked:border-yellow-400 transition">
                  <?= htmlspecialchars($t['descricao']) ?>
                </span>
              </label>
            <?php endwhile; ?>
          </div>
        </div>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Adicionar Novas Imagens</label>
          <input type="file" name="imagens[]" multiple accept="image/*" class="w-full text-zinc-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-yellow-400 file:text-black file:font-bold file:text-sm file:uppercase file:tracking-wider file:cursor-pointer hover:file:bg-yellow-300 cursor-pointer" />
        </div>

        <?php if ($imagens->num_rows > 0): ?>
        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-3">Imagens Atuais</label>
          <div class="flex flex-wrap gap-3">
            <?php while ($img = $imagens->fetch_assoc()): ?>
              <div class="relative group">
                <div class="w-24 h-24 bg-black border border-zinc-800 rounded-lg overflow-hidden p-1">
                  <img src="../<?= htmlspecialchars($img['url_imagem']) ?>" class="w-full h-full object-contain" />
                </div>
                <a href="?id=<?= $id ?>&remover_imagem=<?= $img['id'] ?>" onclick="return confirm('Remover esta imagem?')"
                  class="absolute -top-2 -right-2 w-6 h-6 flex items-center justify-center bg-red-500 hover:bg-red-600 text-white text-xs rounded-full transition shadow-lg">
                  <i class="fa-solid fa-xmark"></i>
                </a>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
        <?php endif; ?>

        <div class="flex flex-col sm:flex-row justify-between gap-3 pt-6 border-t border-zinc-900">
          <a href="produtos.php" class="px-5 py-3 text-sm uppercase tracking-widest font-bold rounded-lg bg-zinc-900 hover:bg-zinc-800 text-zinc-300 transition text-center">← Voltar</a>
          <button type="submit" class="px-7 py-3 text-sm uppercase tracking-widest font-bold rounded-lg bg-yellow-400 hover:bg-yellow-300 text-black transition shadow-lg shadow-yellow-400/10 inline-flex items-center justify-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i> Salvar Alterações
          </button>
        </div>
      </form>
    </div>
  </div>
</main>

<script>
  const precoInput = document.querySelector('input[name="preco"]');
  precoInput.addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, '');
    if (!value) { e.target.value = ''; return; }
    value = (parseInt(value) / 100).toFixed(2);
    e.target.value = 'R$ ' + value.replace('.', ',');
  });
</script>

<?php include 'includes/footer.php'; ?>
