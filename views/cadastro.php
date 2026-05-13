<?php
session_start();
include '../backend/db.php';

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST['nome']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirma = $_POST['confirma_senha'];

    if ($senha !== $confirma) {
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
            $mensagem = 'Este email já está cadastrado.';
            $tipoMensagem = 'erro';
        } else {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (nome, email, senha_hash, telefone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nome, $email, $senha_hash, $telefone);

            if ($stmt->execute()) {
                header("Location: login.php?cadastrado=1");
                exit();
            } else {
                $mensagem = 'Erro ao cadastrar. Tente novamente.';
                $tipoMensagem = 'erro';
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>

<?php include '../includes/header.php'; ?>

<title>Criar Conta · DURK</title>

<main class="min-h-screen flex items-center justify-center px-4 py-16 relative overflow-hidden">
  <div class="absolute inset-0 pointer-events-none opacity-[0.03]">
    <p class="font-display text-[20rem] leading-none text-white text-center whitespace-nowrap mt-20">DURK</p>
  </div>

  <div class="w-full max-w-md relative">
    <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-8 shadow-2xl">
      <div class="text-center mb-8">
        <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Novo na crew</span>
        <h2 class="font-display text-4xl text-white mt-2">CRIAR CONTA</h2>
        <p class="text-zinc-500 text-sm mt-2">Cria sua conta e bora pros drops.</p>
      </div>

      <form method="POST" action="" class="space-y-4">
        <?php if (!empty($mensagem)): ?>
          <div class="<?= $tipoMensagem === 'erro' ? 'bg-red-500/10 border-red-500/30 text-red-400' : 'bg-green-500/10 border-green-500/30 text-green-400' ?> border text-sm rounded-lg px-4 py-3 flex items-center gap-2">
            <i class="fa-solid fa-circle-<?= $tipoMensagem === 'erro' ? 'exclamation' : 'check' ?>"></i>
            <span><?= htmlspecialchars($mensagem) ?></span>
          </div>
        <?php endif; ?>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Nome Completo</label>
          <div class="relative">
            <i class="fa-regular fa-user absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600"></i>
            <input type="text" name="nome" required class="w-full pl-11 pr-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition placeholder-zinc-600" placeholder="Seu nome" />
          </div>
        </div>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Telefone</label>
          <div class="relative">
            <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600"></i>
            <input type="tel" name="telefone" id="telefone" required class="w-full pl-11 pr-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition placeholder-zinc-600" placeholder="(11) 99999-9999" />
          </div>
        </div>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Email</label>
          <div class="relative">
            <i class="fa-regular fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600"></i>
            <input type="email" name="email" required class="w-full pl-11 pr-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition placeholder-zinc-600" placeholder="voce@email.com" />
          </div>
        </div>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Senha</label>
          <div class="relative">
            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600"></i>
            <input type="password" name="senha" id="senha" required minlength="6" class="w-full pl-11 pr-11 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition placeholder-zinc-600" placeholder="Mínimo 6 caracteres" />
            <button type="button" onclick="togglePwd('senha', 'iconSenha')" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-600 hover:text-yellow-400 transition">
              <i id="iconSenha" class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Confirmar Senha</label>
          <div class="relative">
            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600"></i>
            <input type="password" name="confirma_senha" id="confSenha" required class="w-full pl-11 pr-11 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition placeholder-zinc-600" placeholder="Repita a senha" />
            <button type="button" onclick="togglePwd('confSenha', 'iconConf')" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-600 hover:text-yellow-400 transition">
              <i id="iconConf" class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="w-full py-3.5 bg-yellow-400 hover:bg-yellow-300 text-black rounded-lg font-bold uppercase tracking-wider transition shadow-lg shadow-yellow-400/10 mt-2">
          Criar Conta
        </button>

        <p class="text-sm text-zinc-500 text-center pt-2">
          Já tem cadastro?
          <a href="login.php" class="text-yellow-400 hover:text-yellow-300 font-bold ml-1">Entrar</a>
        </p>
      </form>
    </div>
  </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/inputmask.min.js"></script>
<script>
  Inputmask({ mask: "(99) 99999-9999" }).mask(document.getElementById("telefone"));

  function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === "password") { input.type = "text"; icon.className = 'fa-regular fa-eye-slash'; }
    else { input.type = "password"; icon.className = 'fa-regular fa-eye'; }
  }
</script>

<?php include '../includes/footer.php'; ?>
