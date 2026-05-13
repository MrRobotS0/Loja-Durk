<?php
include 'includes/auth.php';
include '../backend/db.php';

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $senha = $_POST['senha'];
    $confirmar = $_POST['confirmar'];
    $tipo = $_POST['tipo_usuario'];

    if ($senha !== $confirmar) {
        $mensagem = 'As senhas não coincidem.';
        $tipoMensagem = 'erro';
    } elseif (strlen($senha) < 6) {
        $mensagem = 'A senha deve ter no mínimo 6 caracteres.';
        $tipoMensagem = 'erro';
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $mensagem = 'Email já cadastrado.';
            $tipoMensagem = 'erro';
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (nome, email, senha_hash, telefone, tipo_usuario) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nome, $email, $senha_hash, $telefone, $tipo);

            if ($stmt->execute()) {
                header('Location: users.php');
                exit();
            } else {
                $mensagem = 'Erro ao cadastrar.';
                $tipoMensagem = 'erro';
            }
            $stmt->close();
        }
        $check->close();
    }
}

include 'includes/header.php';
?>

<main class="bg-zinc-950/40 min-h-screen p-4 sm:p-6 lg:p-10">
  <div class="max-w-xl mx-auto">
    <div class="mb-8">
      <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Novo</span>
      <h1 class="font-display text-5xl text-white mt-2">ADICIONAR USUÁRIO</h1>
    </div>

    <?php if (!empty($mensagem)): ?>
      <div class="<?= $tipoMensagem === 'erro' ? 'bg-red-500/10 border-red-500/30 text-red-400' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' ?> border text-sm rounded-lg px-4 py-3 mb-5">
        <?= htmlspecialchars($mensagem) ?>
      </div>
    <?php endif; ?>

    <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-6 md:p-8">
      <form method="POST" class="space-y-4">
        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Nome</label>
          <input type="text" name="nome" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
        </div>
        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Email</label>
          <input type="email" name="email" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
        </div>
        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Telefone</label>
          <input type="tel" name="telefone" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" placeholder="(11) 99999-0000" />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Senha</label>
            <input type="password" name="senha" required minlength="6" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
          </div>
          <div>
            <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Confirmar</label>
            <input type="password" name="confirmar" required class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition" />
          </div>
        </div>
        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Tipo de Usuário</label>
          <select name="tipo_usuario" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition">
            <option value="usuario">Usuário</option>
            <option value="admin">Administrador</option>
          </select>
        </div>
        <div class="flex flex-col sm:flex-row justify-between gap-3 pt-6 border-t border-zinc-900">
          <a href="users.php" class="px-5 py-3 text-sm uppercase tracking-widest font-bold rounded-lg bg-zinc-900 hover:bg-zinc-800 text-zinc-300 transition text-center">← Voltar</a>
          <button type="submit" class="px-7 py-3 text-sm uppercase tracking-widest font-bold rounded-lg bg-yellow-400 hover:bg-yellow-300 text-black transition shadow-lg shadow-yellow-400/10 inline-flex items-center justify-center gap-2">
            <i class="fa-solid fa-check"></i> Cadastrar
          </button>
        </div>
      </form>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
