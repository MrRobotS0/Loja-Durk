<?php
include 'includes/auth.php';
include '../backend/db.php';

if (isset($_GET['excluir']) && is_numeric($_GET['excluir'])) {
    $idExcluir = (int) $_GET['excluir'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $idExcluir);
    $stmt->execute();
    $stmt->close();

    header("Location: users.php");
    exit();
}

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

if (!empty($busca)) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE nome LIKE ? OR email LIKE ? ORDER BY id DESC");
    $like = '%' . $busca . '%';
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $usuarios = $stmt->get_result();
} else {
    $usuarios = $conn->query("SELECT * FROM users ORDER BY id DESC");
}

include 'includes/header.php';
?>

<main class="bg-zinc-950/40 min-h-screen p-4 sm:p-6 lg:p-10">
  <div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4">
      <div>
        <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Gestão</span>
        <h1 class="font-display text-5xl text-white mt-2">USUÁRIOS</h1>
      </div>
      <div class="flex flex-col sm:flex-row gap-2">
        <form method="GET" class="flex items-center gap-2">
          <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Buscar nome ou email..."
            class="px-4 py-2.5 bg-black border border-zinc-800 text-white text-sm rounded-lg focus:outline-none focus:border-yellow-400 transition w-full sm:w-64" />
          <button type="submit" class="bg-zinc-900 hover:bg-zinc-800 text-white px-4 py-2.5 rounded-lg text-sm font-bold uppercase tracking-wider transition">
            <i class="fa-solid fa-magnifying-glass"></i>
          </button>
        </form>
        <a href="usuario_adicionar.php" class="bg-yellow-400 hover:bg-yellow-300 text-black text-sm font-bold uppercase tracking-wider px-5 py-2.5 rounded-lg transition inline-flex items-center justify-center gap-2">
          <i class="fa-solid fa-plus"></i> Novo
        </a>
      </div>
    </div>

    <div class="overflow-x-auto bg-zinc-950 border border-zinc-900 rounded-2xl">
      <table class="min-w-full text-sm">
        <thead class="bg-black text-yellow-400 uppercase text-[10px] tracking-widest font-bold">
          <tr>
            <th class="px-6 py-4 text-left">ID</th>
            <th class="px-6 py-4 text-left">Nome</th>
            <th class="px-6 py-4 text-left">Email</th>
            <th class="px-6 py-4 text-left">Telefone</th>
            <th class="px-6 py-4 text-left">Tipo</th>
            <th class="px-6 py-4 text-center">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-zinc-900">
          <?php if ($usuarios && $usuarios->num_rows > 0):
            while ($u = $usuarios->fetch_assoc()): ?>
            <tr class="hover:bg-black transition">
              <td class="px-6 py-4 font-bold text-zinc-500">#<?= $u['id'] ?></td>
              <td class="px-6 py-4 text-white font-bold"><?= htmlspecialchars($u['nome']) ?></td>
              <td class="px-6 py-4 text-zinc-300"><?= htmlspecialchars($u['email']) ?></td>
              <td class="px-6 py-4 text-zinc-300"><?= htmlspecialchars($u['telefone'] ?? '—') ?></td>
              <td class="px-6 py-4">
                <?php if ($u['tipo_usuario'] === 'admin'): ?>
                  <span class="px-2.5 py-0.5 bg-yellow-400 text-black text-[10px] uppercase tracking-widest font-bold rounded">Admin</span>
                <?php else: ?>
                  <span class="px-2.5 py-0.5 bg-zinc-900 text-zinc-400 text-[10px] uppercase tracking-widest font-bold rounded">User</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4">
                <div class="flex justify-center gap-1.5">
                  <a href="usuario_editar.php?id=<?= $u['id'] ?>" title="Editar" class="w-8 h-8 flex items-center justify-center bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 rounded-lg transition">
                    <i class="fa-solid fa-pen text-xs"></i>
                  </a>
                  <a href="users.php?excluir=<?= $u['id'] ?>" title="Excluir"
                    onclick="return confirm('Excluir este usuário definitivamente?');"
                    class="w-8 h-8 flex items-center justify-center bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-lg transition">
                    <i class="fa-solid fa-trash text-xs"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endwhile;
          else: ?>
            <tr><td colspan="6" class="text-center py-16 text-zinc-500">
              <i class="fa-solid fa-users-slash text-5xl text-zinc-800 mb-3 block"></i>
              Nenhum usuário encontrado.
            </td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
