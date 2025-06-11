<?php
include 'includes/auth.php';
include '../backend/db.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $senha = $_POST['senha'];
    $confirmar = $_POST['confirmar'];
    $tipo = $_POST['tipo_usuario'];

    if ($senha !== $confirmar) {
        $mensagem = '<p class="text-red-500">As senhas não coincidem.</p>';
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (nome, email, senha_hash, telefone, tipo_usuario) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $email, $senha_hash, $telefone, $tipo);

        if ($stmt->execute()) {
            header('Location: users.php');
            exit();
        } else {
            $mensagem = '<p class="text-red-500">Erro ao cadastrar: ' . $stmt->error . '</p>';
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<?php include 'includes/header.php'; ?>

<main class="bg-gray-100 min-h-screen p-8">
  <div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">➕ Adicionar Usuário</h1>

    <?= $mensagem ?>

    <form method="POST" class="space-y-4">

      <div>
        <label class="block font-medium text-sm mb-1">Nome</label>
        <input type="text" name="nome" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800" />
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Email</label>
        <input type="email" name="email" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800" />
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Telefone</label>
        <input type="tel" name="telefone" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800"
               placeholder="(11) 99999-0000" />
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Senha</label>
        <input type="password" name="senha" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800" />
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Confirmar Senha</label>
        <input type="password" name="confirmar" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800" />
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Tipo de Usuário</label>
        <select name="tipo_usuario" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800">
          <option value="usuario">Usuário</option>
          <option value="admin">Administrador</option>
        </select>
      </div>

      <div class="pt-4">
        <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition">
          Cadastrar
        </button>
        <a href="usuarios.php"
           class="ml-4 text-sm text-gray-600 hover:underline">← Voltar para a lista</a>
      </div>
    </form>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
