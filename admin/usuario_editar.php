<?php
include 'includes/auth.php';
include '../backend/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$mensagem = '';
$tipoMensagem = '';

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuario) {
    header('Location: users.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $tipo = $_POST['tipo_usuario'];
    $novaSenha = $_POST['nova_senha'];

    if (!empty($novaSenha) && strlen($novaSenha) < 6) {
        $mensagem = 'A nova senha deve ter no mínimo 6 caracteres.';
        $tipoMensagem = 'erro';
    } else {
        if (!empty($novaSenha)) {
            $senha_hash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET nome = ?, email = ?, telefone = ?, tipo_usuario = ?, senha_hash = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $nome, $email, $telefone, $tipo, $senha_hash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET nome = ?, email = ?, telefone = ?, tipo_usuario = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $nome, $email, $telefone, $tipo, $id);
        }
        if ($stmt->execute()) {
            header("Location: users.php");
            exit();
        } else {
            $mensagem = 'Erro ao atualizar.';
            $tipoMensagem = 'erro';
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<main class="bg-zinc-950/40 min-h-screen p-4 sm:p-6 lg:p-10">
  <div class="max-w-xl mx-auto">
    <div class="mb-8">
      <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Edição #<?= $id ?></span>
      <h1 class="font-display text-5xl text-white mt-2">EDITAR USUÁRIO</h1>
    </div>

    <?php if (!empty($mensagem)): ?>
      <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-lg px-4 py-3 mb-5">
        <?= htmlspecialchars($mensagem) ?>
      </div>
    <?php endif; ?>

    <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 md:p-8">
      <form method="POST" class="space-y-4">
        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Nome</label>
          <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
        </div>
        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
        </div>
        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Telefone</label>
          <input type="tel" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
        </div>
        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Tipo de Usuário</label>
          <select name="tipo_usuario" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
            <option value="usuario" <?= $usuario['tipo_usuario'] === 'usuario' ? 'selected' : '' ?>>Usuário</option>
            <option value="admin" <?= $usuario['tipo_usuario'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
          </select>
        </div>
        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Nova Senha <span class="text-zinc-600 normal-case font-normal tracking-normal">(deixe em branco para manter)</span></label>
          <input type="password" name="nova_senha" minlength="6" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
        </div>
        <div class="flex flex-col sm:flex-row justify-between gap-3 pt-6 border-t border-zinc-900">
          <a href="users.php" class="px-5 py-3 text-sm uppercase tracking-widest font-bold rounded-lg bg-zinc-900 hover:bg-zinc-800 text-zinc-300 transition text-center">← Voltar</a>
          <button type="submit" class="px-7 py-3 text-sm uppercase tracking-widest font-bold rounded-lg bg-yellow-400 hover:bg-yellow-300 text-black transition shadow-lg shadow-yellow-400/10 inline-flex items-center justify-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i> Salvar
          </button>
        </div>
      </form>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
