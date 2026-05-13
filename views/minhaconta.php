<?php
include '../includes/verifica_login.php';
include '../backend/db.php';

$id = (int) $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_endereco'])) {
    $stmt = $conn->prepare("SELECT id FROM enderecos WHERE user_id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resEndereco = $stmt->get_result();
    if ($resEndereco->num_rows > 0) {
        $enderecoId = (int) $resEndereco->fetch_assoc()['id'];
        $stmt->close();

        $upd = $conn->prepare("UPDATE pedidos SET endereco_id = NULL WHERE endereco_id = ?");
        $upd->bind_param("i", $enderecoId);
        $upd->execute();
        $upd->close();

        $del = $conn->prepare("DELETE FROM enderecos WHERE id = ?");
        $del->bind_param("i", $enderecoId);
        $del->execute();
        $del->close();
    }
    header("Location: minhaconta.php");
    exit();
}

$stmt = $conn->prepare("SELECT nome, email, telefone FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM enderecos WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$endereco = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM pedidos WHERE user_id = ? ORDER BY data_pedido DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$pedidos = $stmt->get_result();

include '../includes/header.php';
?>

<title>Minha Conta · DURK</title>

<main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-10">

  <div class="mb-10">
    <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Perfil</span>
    <h1 class="font-display text-5xl md:text-6xl text-white mt-2">MINHA CONTA</h1>
    <p class="text-zinc-400 mt-2">Olá, <span class="text-yellow-400 font-bold"><?= htmlspecialchars($usuario['nome']) ?></span>.</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

    <aside class="md:col-span-1">
      <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-4 space-y-2 sticky top-28">
        <button onclick="mostrarAba('conta')" id="btn-conta" class="aba-btn w-full text-left px-4 py-3 rounded-lg transition flex items-center gap-3 font-bold uppercase tracking-wider text-sm">
          <i class="fa-solid fa-user"></i> Meus Dados
        </button>
        <button onclick="mostrarAba('endereco')" id="btn-endereco" class="aba-btn w-full text-left px-4 py-3 rounded-lg transition flex items-center gap-3 font-bold uppercase tracking-wider text-sm">
          <i class="fa-solid fa-location-dot"></i> Endereço
        </button>
        <button onclick="mostrarAba('pedidos')" id="btn-pedidos" class="aba-btn w-full text-left px-4 py-3 rounded-lg transition flex items-center gap-3 font-bold uppercase tracking-wider text-sm">
          <i class="fa-solid fa-box"></i> Pedidos
        </button>
        <a href="../backend/logout.php" class="w-full text-left px-4 py-3 rounded-lg transition flex items-center gap-3 font-bold uppercase tracking-wider text-sm text-red-400 hover:bg-red-500/10">
          <i class="fa-solid fa-arrow-right-from-bracket"></i> Sair
        </a>
      </div>
    </aside>

    <section class="md:col-span-3">
      <div id="aba-conta" class="tab bg-zinc-950 border border-zinc-900 rounded-2xl p-8">
        <h2 class="font-display text-3xl text-white mb-6 pb-4 border-b border-zinc-900 flex items-center gap-3">
          <i class="fa-solid fa-user text-yellow-400"></i> MEUS DADOS
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div class="bg-black border border-zinc-900 rounded-lg p-4">
            <p class="text-[10px] uppercase tracking-widest text-yellow-400 font-bold">Nome</p>
            <p class="text-white font-bold mt-1"><?= htmlspecialchars($usuario['nome']) ?></p>
          </div>
          <div class="bg-black border border-zinc-900 rounded-lg p-4">
            <p class="text-[10px] uppercase tracking-widest text-yellow-400 font-bold">Email</p>
            <p class="text-white font-bold mt-1 break-all"><?= htmlspecialchars($usuario['email']) ?></p>
          </div>
          <div class="bg-black border border-zinc-900 rounded-lg p-4">
            <p class="text-[10px] uppercase tracking-widest text-yellow-400 font-bold">Telefone</p>
            <p class="text-white font-bold mt-1"><?= htmlspecialchars($usuario['telefone'] ?? '—') ?></p>
          </div>
        </div>
      </div>

      <div id="aba-endereco" class="tab hidden bg-zinc-950 border border-zinc-900 rounded-2xl p-8">
        <h2 class="font-display text-3xl text-white mb-6 pb-4 border-b border-zinc-900 flex items-center gap-3">
          <i class="fa-solid fa-location-dot text-yellow-400"></i> ENDEREÇO
        </h2>
        <?php if ($endereco): ?>
          <div class="bg-black border border-zinc-900 rounded-lg p-6 space-y-3">
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div><p class="text-[10px] uppercase tracking-widest text-yellow-400 font-bold">Rua</p><p class="text-white"><?= htmlspecialchars($endereco['rua']) ?></p></div>
              <div><p class="text-[10px] uppercase tracking-widest text-yellow-400 font-bold">Número</p><p class="text-white"><?= htmlspecialchars($endereco['numero']) ?></p></div>
              <div><p class="text-[10px] uppercase tracking-widest text-yellow-400 font-bold">Bairro</p><p class="text-white"><?= htmlspecialchars($endereco['bairro']) ?></p></div>
              <div><p class="text-[10px] uppercase tracking-widest text-yellow-400 font-bold">Cidade</p><p class="text-white"><?= htmlspecialchars($endereco['cidade']) ?></p></div>
              <div><p class="text-[10px] uppercase tracking-widest text-yellow-400 font-bold">Estado</p><p class="text-white"><?= htmlspecialchars($endereco['estado']) ?></p></div>
              <div><p class="text-[10px] uppercase tracking-widest text-yellow-400 font-bold">CEP</p><p class="text-white"><?= htmlspecialchars($endereco['cep']) ?></p></div>
            </div>
          </div>
          <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este endereço?')" class="mt-6">
            <button type="submit" name="excluir_endereco" class="bg-red-500/10 border border-red-500/30 hover:bg-red-500/20 text-red-400 px-5 py-2.5 rounded-lg font-bold uppercase tracking-wider text-sm transition inline-flex items-center gap-2">
              <i class="fa-solid fa-trash"></i> Excluir Endereço
            </button>
          </form>
        <?php else: ?>
          <div class="text-center py-10">
            <i class="fa-solid fa-map-location-dot text-6xl text-zinc-800 mb-4"></i>
            <p class="text-zinc-500 mb-6">Você ainda não cadastrou um endereço.</p>
            <a href="endereco_adicionar.php" class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-300 text-black px-5 py-3 rounded-lg font-bold uppercase tracking-wider text-sm transition">
              <i class="fa-solid fa-plus"></i> Adicionar Endereço
            </a>
          </div>
        <?php endif; ?>
      </div>

      <div id="aba-pedidos" class="tab hidden bg-zinc-950 border border-zinc-900 rounded-2xl p-8">
        <h2 class="font-display text-3xl text-white mb-6 pb-4 border-b border-zinc-900 flex items-center gap-3">
          <i class="fa-solid fa-box text-yellow-400"></i> MEUS PEDIDOS
        </h2>
        <?php if ($pedidos->num_rows > 0): ?>
          <div class="space-y-3">
            <?php while ($p = $pedidos->fetch_assoc()):
              $statusClass = match (strtolower($p['status'] ?? '')) {
                'pendente'  => 'text-yellow-400 bg-yellow-400/10 border-yellow-400/30',
                'pago'      => 'text-blue-400 bg-blue-400/10 border-blue-400/30',
                'enviado'   => 'text-purple-400 bg-purple-400/10 border-purple-400/30',
                'entregue'  => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/30',
                'cancelado' => 'text-red-400 bg-red-400/10 border-red-400/30',
                default     => 'text-zinc-400 bg-zinc-800 border-zinc-700',
              };
              ?>
              <a href="pedido_detalhes.php?id=<?= $p['id'] ?>" class="block bg-black border border-zinc-900 hover:border-yellow-400 rounded-lg p-5 transition group">
                <div class="flex items-center justify-between flex-wrap gap-3">
                  <div>
                    <p class="text-[10px] uppercase tracking-widest text-yellow-400 font-bold">Pedido #<?= $p['id'] ?></p>
                    <p class="text-zinc-300 text-sm mt-1"><?= date('d/m/Y · H:i', strtotime($p['data_pedido'])) ?></p>
                  </div>
                  <div class="flex items-center gap-4">
                    <span class="px-3 py-1 text-[10px] uppercase tracking-widest font-bold rounded-full border <?= $statusClass ?>">
                      <?= htmlspecialchars($p['status']) ?>
                    </span>
                    <span class="text-white font-bold">R$ <?= number_format($p['valor_total'], 2, ',', '.') ?></span>
                    <i class="fa-solid fa-arrow-right text-zinc-600 group-hover:text-yellow-400 group-hover:translate-x-1 transition"></i>
                  </div>
                </div>
              </a>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-10">
            <i class="fa-solid fa-box-open text-6xl text-zinc-800 mb-4"></i>
            <p class="text-zinc-500">Você ainda não fez nenhum pedido.</p>
            <a href="vestuario.php" class="inline-block mt-6 text-yellow-400 hover:underline uppercase tracking-widest text-sm font-bold">Ver catálogo</a>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </div>
</main>

<style>
  .aba-btn { color: #a1a1aa; }
  .aba-btn:hover { background: #18181b; color: #fafafa; }
  .aba-btn.ativa { background: rgba(250, 204, 21, 0.15); color: #facc15; }
</style>

<script>
  function mostrarAba(id) {
    document.querySelectorAll('.tab').forEach(tab => tab.classList.add('hidden'));
    document.getElementById('aba-' + id).classList.remove('hidden');
    document.querySelectorAll('.aba-btn').forEach(b => b.classList.remove('ativa'));
    document.getElementById('btn-' + id).classList.add('ativa');
  }
  mostrarAba('conta');
</script>

<?php include '../includes/footer.php'; ?>
