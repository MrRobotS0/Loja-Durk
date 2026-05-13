<?php
session_start();
include '../backend/db.php';

$erroLogin = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario['senha_hash'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

            if ($usuario['tipo_usuario'] === 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../views/index.php");
            }
            exit();
        } else {
            $erroLogin = 'Senha incorreta.';
        }
    } else {
        $erroLogin = 'Email não encontrado.';
    }

    $stmt->close();
}
?>

<?php include '../includes/header.php'; ?>

<title>Entrar · DURK</title>

<main class="min-h-screen flex items-center justify-center px-4 py-16 relative overflow-hidden">
  <div class="absolute inset-0 pointer-events-none opacity-[0.03]">
    <p class="font-display text-[20rem] leading-none text-white text-center whitespace-nowrap mt-20">DURK</p>
  </div>

  <div class="w-full max-w-md relative">
    <div class="bg-zinc-950 border border-zinc-900 rounded-2xl p-8 shadow-2xl">
      <div class="text-center mb-8">
        <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Acesso</span>
        <h2 class="font-display text-4xl text-white mt-2">ENTRAR</h2>
        <p class="text-zinc-500 text-sm mt-2">Bem-vindo de volta à família.</p>
      </div>

      <form method="POST" action="" class="space-y-4">
        <?php if (!empty($erroLogin)): ?>
          <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-lg px-4 py-3 flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span><?= htmlspecialchars($erroLogin) ?></span>
          </div>
        <?php endif; ?>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Email</label>
          <div class="relative">
            <i class="fa-regular fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600"></i>
            <input type="email" name="email" placeholder="voce@email.com" required
              class="w-full pl-11 pr-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition placeholder-zinc-600" />
          </div>
        </div>

        <div>
          <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Senha</label>
          <div class="relative">
            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600"></i>
            <input type="password" name="senha" id="loginSenha" placeholder="••••••••" required
              class="w-full pl-11 pr-11 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition placeholder-zinc-600" />
            <button type="button" onclick="toggleLoginPwd()" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-600 hover:text-yellow-400 transition">
              <i id="iconLoginPwd" class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="w-full py-3.5 bg-yellow-400 hover:bg-yellow-300 text-black rounded-lg font-bold uppercase tracking-wider transition shadow-lg shadow-yellow-400/10">
          Entrar
        </button>

        <p class="text-sm text-zinc-500 text-center pt-2">
          Ainda não tem cadastro?
          <a href="cadastro.php" class="text-yellow-400 hover:text-yellow-300 font-bold ml-1">Criar conta</a>
        </p>
      </form>
    </div>
  </div>
</main>

<script>
  function toggleLoginPwd() {
    const i = document.getElementById('loginSenha');
    const ic = document.getElementById('iconLoginPwd');
    if (i.type === 'password') { i.type = 'text'; ic.className = 'fa-regular fa-eye-slash'; }
    else { i.type = 'password'; ic.className = 'fa-regular fa-eye'; }
  }
</script>

<?php include '../includes/footer.php'; ?>
