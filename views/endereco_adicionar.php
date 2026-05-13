<?php
include '../includes/verifica_login.php';
include '../backend/db.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) $_SESSION['usuario_id'];
    $rua = trim($_POST['rua']);
    $numero = trim($_POST['numero']);
    $complemento = !empty($_POST['complemento']) ? trim($_POST['complemento']) : null;
    $bairro = trim($_POST['bairro']);
    $cidade = trim($_POST['cidade']);
    $estado = trim($_POST['estado']);
    $cep = trim($_POST['cep']);
    $pais = trim($_POST['pais']) ?: 'Brasil';

    $stmt = $conn->prepare("INSERT INTO enderecos
    (user_id, rua, numero, complemento, bairro, cidade, estado, cep, pais)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssss", $userId, $rua, $numero, $complemento, $bairro, $cidade, $estado, $cep, $pais);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: minhaconta.php");
        exit();
    } else {
        $erro = 'Erro ao salvar o endereço. Tente novamente.';
        $stmt->close();
    }
}

include '../includes/header.php';
?>

<title>Adicionar Endereço · DURK</title>

<main class="max-w-3xl mx-auto py-12 px-4 sm:px-6 lg:px-10">

  <div class="mb-10">
    <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Cadastro</span>
    <h1 class="font-display text-5xl text-white mt-2">NOVO ENDEREÇO</h1>
    <p class="text-zinc-400 mt-2">Pra gente saber pra onde mandar seus drops.</p>
  </div>

  <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 md:p-8">
    <?php if (!empty($erro)): ?>
      <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-lg px-4 py-3 mb-5 flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($erro) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <div class="sm:col-span-2">
        <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Rua</label>
        <input type="text" name="rua" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
      </div>
      <div>
        <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Número</label>
        <input type="text" name="numero" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
      </div>
      <div>
        <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Complemento</label>
        <input type="text" name="complemento" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" placeholder="opcional" />
      </div>
      <div>
        <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Bairro</label>
        <input type="text" name="bairro" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
      </div>
      <div>
        <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">CEP</label>
        <input type="text" name="cep" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" placeholder="00000-000" />
      </div>
      <div>
        <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Cidade</label>
        <input type="text" name="cidade" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
      </div>
      <div>
        <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Estado</label>
        <input type="text" name="estado" required maxlength="2" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition uppercase" placeholder="SP" />
      </div>
      <div>
        <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">País</label>
        <input type="text" name="pais" value="Brasil" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
      </div>

      <div class="sm:col-span-2 flex flex-col sm:flex-row justify-between gap-3 pt-4">
        <a href="minhaconta.php" class="px-5 py-3 text-sm uppercase tracking-widest font-bold rounded-lg bg-zinc-900 hover:bg-zinc-800 text-zinc-300 transition text-center">← Voltar</a>
        <button type="submit" class="px-7 py-3 text-sm uppercase tracking-widest font-bold rounded-lg bg-yellow-400 hover:bg-yellow-300 text-black transition shadow-lg shadow-yellow-400/10">
          Salvar Endereço
        </button>
      </div>
    </form>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
