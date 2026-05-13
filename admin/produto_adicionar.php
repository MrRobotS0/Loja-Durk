<?php
include 'includes/auth.php';
include '../backend/db.php';

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
    $tamanhosSelecionados = $_POST['tamanhos'] ?? [];

    $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, categoria_id, marca_id, genero, slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiiiss", $nome, $descricao, $preco, $estoque, $categoria_id, $marca_id, $genero, $slug);

    if ($stmt->execute()) {
        $produto_id = $stmt->insert_id;
        $stmt->close();

        if (!empty($tamanhosSelecionados)) {
            $stmtT = $conn->prepare("INSERT INTO produto_tamanhos (produto_id, tamanho_id) VALUES (?, ?)");
            foreach ($tamanhosSelecionados as $tid) {
                $tidInt = (int) $tid;
                $stmtT->bind_param("ii", $produto_id, $tidInt);
                $stmtT->execute();
            }
            $stmtT->close();
        }

        if (!empty($_FILES['imagens']['name'][0])) {
            $stmtI = $conn->prepare("INSERT INTO imagens_produto (produto_id, url_imagem, ordem) VALUES (?, ?, ?)");
            foreach ($_FILES['imagens']['tmp_name'] as $i => $tmpPath) {
                $nomeImg = uniqid() . "_" . basename($_FILES['imagens']['name'][$i]);
                $destinoFisico = "../views/imagens/$nomeImg";
                $urlImagem = "views/imagens/$nomeImg";
                if (move_uploaded_file($tmpPath, $destinoFisico)) {
                    $stmtI->bind_param("isi", $produto_id, $urlImagem, $i);
                    $stmtI->execute();
                }
            }
            $stmtI->close();
        }

        header("Location: produtos.php");
        exit();
    } else {
        $mensagem = 'Erro ao cadastrar produto: ' . $conn->error;
        $stmt->close();
    }
}

$categorias = $conn->query("SELECT id, nome FROM categorias ORDER BY nome");
$marcas = $conn->query("SELECT id, nome FROM marcas ORDER BY nome");
$tamanhos = $conn->query("SELECT id, descricao FROM tamanhos ORDER BY id");

include 'includes/header.php';
?>

<main class="bg-zinc-950/40 min-h-screen p-4 sm:p-6 lg:p-10">
  <div class="max-w-4xl mx-auto">
    <div class="mb-8">
      <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Novo</span>
      <h1 class="font-display text-5xl text-white mt-2">ADICIONAR PRODUTO</h1>
    </div>

    <?php if (!empty($mensagem)): ?>
      <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-lg px-4 py-3 mb-5">
        <?= htmlspecialchars($mensagem) ?>
      </div>
    <?php endif; ?>

    <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 md:p-8">
      <form method="POST" enctype="multipart/form-data" class="space-y-6">

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Nome</label>
          <input type="text" name="nome" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
        </div>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Descrição</label>
          <textarea name="descricao" rows="4" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition resize-none"></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Preço</label>
            <input type="text" name="preco" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" placeholder="R$ 0,00" />
          </div>
          <div>
            <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Estoque</label>
            <input type="number" min="0" name="estoque" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
          </div>
          <div>
            <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Gênero</label>
            <select name="genero" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
              <option value="masculino">Masculino</option>
              <option value="feminino">Feminino</option>
              <option value="unissex">Unissex</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Categoria</label>
            <select name="categoria_id" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
              <?php while ($c = $categorias->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div>
            <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Marca</label>
            <select name="marca_id" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
              <?php while ($m = $marcas->fetch_assoc()): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nome']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Slug</label>
          <input type="text" name="slug" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" placeholder="ex: camiseta-preta-algodao" />
        </div>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-3">Tamanhos disponíveis</label>
          <div class="flex flex-wrap gap-2">
            <?php while ($t = $tamanhos->fetch_assoc()): ?>
              <label class="cursor-pointer">
                <input type="checkbox" name="tamanhos[]" value="<?= $t['id'] ?>" class="hidden peer" />
                <span class="inline-block px-4 py-2 bg-black border border-zinc-800 text-zinc-400 rounded-lg text-sm font-bold peer-checked:bg-yellow-400 peer-checked:text-black peer-checked:border-yellow-400 transition">
                  <?= htmlspecialchars($t['descricao']) ?>
                </span>
              </label>
            <?php endwhile; ?>
          </div>
        </div>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Imagens</label>
          <input type="file" name="imagens[]" multiple accept="image/*" class="w-full text-zinc-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-yellow-400 file:text-black file:font-bold file:text-sm file:uppercase file:tracking-wider file:cursor-pointer hover:file:bg-yellow-300 cursor-pointer" />
          <p class="text-xs text-zinc-500 mt-2">Você pode selecionar várias imagens.</p>
        </div>

        <div class="flex flex-col sm:flex-row justify-between gap-3 pt-6 border-t border-zinc-900">
          <a href="produtos.php" class="px-5 py-3 text-sm uppercase tracking-widest font-bold rounded-lg bg-zinc-900 hover:bg-zinc-800 text-zinc-300 transition text-center">← Voltar</a>
          <button type="submit" class="px-7 py-3 text-sm uppercase tracking-widest font-bold rounded-lg bg-yellow-400 hover:bg-yellow-300 text-black transition shadow-lg shadow-yellow-400/10 inline-flex items-center justify-center gap-2">
            <i class="fa-solid fa-check"></i> Cadastrar Produto
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
  precoInput.addEventListener('focus', function () { if (!this.value) this.value = 'R$ 0,00'; });
  precoInput.addEventListener('blur', function () { if (this.value === 'R$ 0,00') this.value = ''; });
</script>

<?php include 'includes/footer.php'; ?>
