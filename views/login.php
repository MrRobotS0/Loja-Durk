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
        }        
    } else {
        $erroLogin = 'Email não encontrado.';
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="icon" type="image/png" href="imagens/logo.png" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@animxyz/core@0.4.0/dist/animxyz.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100">

    <?php include '../includes/header.php'; ?>

    <main class="min-h-screen flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg animxyz-in" xyz="fade down duration-10 stagger ease-in-out">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6 xyz-nested" xyz="fade small stagger">Login</h2>

            <form method="POST" action="" class="space-y-5 xyz-nested" xyz="fade up stagger">
                <?php if (!empty($erroLogin)): ?>
                    <p class="text-red-500 text-center text-sm"><?= $erroLogin ?></p>
                <?php endif; ?>

                <input type="email" name="email" placeholder="Email" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-800 transition" />

                <input type="password" name="senha" placeholder="Senha" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-800 transition" />

                <p class="text-sm text-gray-600 text-center">
                    Não tem cadastro?
                    <a href="../views/cadastro.php" class="text-blue-600 hover:underline font-medium">Clique aqui</a>
                </p>

                <button type="submit"
                    class="w-full py-3 bg-gray-800 text-white rounded-xl hover:bg-gray-900 transition-all duration-300 transform hover:scale-[1.02] shadow-md">
                    Entrar
                </button>

            </form>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>

</html>