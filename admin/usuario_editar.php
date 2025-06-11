<?php
include 'includes/auth.php';
include '../backend/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mensagem = '';

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    header('Location: usuarios.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $tipo = $_POST['tipo_usuario'];
    $novaSenha = $_POST['nova_senha'];

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
        $mensagem = '<p class="text-red-500">Erro ao atualizar: ' . $stmt->error . '</p>';
    }

    $stmt->close();
}
?>

<?php include 'includes/header.php'; ?>

<main class="bg-gray-100 min-h-screen p-8">
  <div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">✏️ Editar Usuário</h1>

    <?= $mensagem ?>

    <form method="POST" class="space-y-4">

      <div>
        <label class="block font-medium text-sm mb-1">Nome</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800" />
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800" />
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Telefone</label>
        <input type="tel" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800"
               placeholder="(11) 99999-0000" />
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Tipo de Usuário</label>
        <select name="tipo_usuario" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800">
          <option value="usuario" <?= $usuario['tipo_usuario'] === 'usuario' ? 'selected' : '' ?>>Usuário</option>
          <option value="admin" <?= $usuario['tipo_usuario'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
      </div>

      <div>
        <label class="block font-medium text-sm mb-1">Nova Senha <span class="text-gray-500 text-xs">(deixe em branco para não alterar)</span></label>
        <input type="password" name="nova_senha"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800" />
      </div>

      <div class="pt-4">
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition">
          Salvar Alterações
        </button>
        <a href="usuarios.php"
           class="ml-4 text-sm text-gray-600 hover:underline">← Voltar para a lista</a>
      </div>
    </form>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
