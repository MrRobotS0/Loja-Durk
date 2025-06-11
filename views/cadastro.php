<?php
include '../backend/db.php';

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST['nome']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirma = $_POST['confirma_senha'];

    if ($senha !== $confirma) {
        $mensagem = '<p class="text-red-500 text-sm">As senhas não coincidem.</p>';
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (nome, email, senha_hash, telefone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $senha_hash, $telefone);

        if ($stmt->execute()) {
            $mensagem = '<p class="text-green-600 text-sm">Cadastro realizado com sucesso!</p>';
            header("Location: ../views/login.php");
            exit();
        } else {
            $mensagem = '<p class="text-red-500 text-sm">Erro ao cadastrar: ' . $stmt->error . '</p>';
        }
        
        $stmt->close();
        $conn->close();
    }
} ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cadastro</title>
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
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6 xyz-nested" xyz="fade small stagger">Cadastro</h2>

            <form method="POST" action="" class="space-y-5 xyz-nested" xyz="fade up stagger">
                <?= $mensagem ?>
                <input type="text" name="nome" placeholder="Nome" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-800 transition" />

                <input type="tel" name="telefone" id="telefone" placeholder="Telefone com DDD" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-800 transition" />

                <input type="email" name="email" placeholder="Email" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-800 transition" />

                <div class="relative">
                    <input type="password" name="senha" id="senha" placeholder="Senha" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-800 transition" />
                    <button type="button" onclick="togglePassword('senha', 'iconSenha')" class="absolute top-1/2 right-4 -translate-y-1/2">
                        <svg id="iconSenha" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12s3.75-6.75 9.75-6.75 9.75 6.75 9.75 6.75-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>

                <div class="relative">
                    <input type="password" name="confirma_senha" id="confSenha" placeholder="Confirme a senha" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-800 transition" />
                    <button type="button" onclick="togglePassword('confSenha', 'iconConf')" class="absolute top-1/2 right-4 -translate-y-1/2">
                        <svg id="iconConf" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12s3.75-6.75 9.75-6.75 9.75 6.75 9.75 6.75-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-gray-800 text-white rounded-xl hover:bg-gray-900 transition-all duration-300 transform hover:scale-[1.02] shadow-md">
                    Cadastrar
                </button>
            </form>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/inputmask.min.js"></script>
<script>
    Inputmask({ mask: "(99) 99999-9999" }).mask(document.getElementById("telefone"));

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        const eyeOpen = `
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M2.25 12s3.75-6.75 9.75-6.75 9.75 6.75 9.75 6.75-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;

        const eyeOff = `
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M3.98 8.223A10.477 10.477 0 001.5 12c1.875 3.75 6.375 6.75 10.5 6.75 1.758 0 3.51-.463 5.02-1.277M21 21l-18-18" />
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M9.75 9.75a3 3 0 004.5 4.5" />`;

        if (input.type === "password") {
            input.type = "text";
            icon.innerHTML = eyeOff;
        } else {
            input.type = "password";
            icon.innerHTML = eyeOpen;
        }
    }
</script>

</html>