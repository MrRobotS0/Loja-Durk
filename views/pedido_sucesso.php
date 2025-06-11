<?php
include '../includes/verifica_login.php';
include '../includes/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<main class="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-4 py-16 text-center" data-aos="fade-in">
    <div class="max-w-xl">
        <div class="text-green-600 text-6xl mb-6">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1 class="text-4xl font-extrabold text-gray-800 mb-4">Pedido Confirmado!</h1>
        <p class="text-lg text-gray-600 mb-6">
            Seu pedido foi realizado com sucesso. Você receberá uma confirmação por e-mail em breve. Obrigado por comprar conosco!
        </p>
        <a href="vestuario.php" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow transition duration-300">
            Continuar Comprando
        </a>
    </div>
</main>

<script>
AOS.init();
</script>

<?php include '../includes/footer.php'; ?>
