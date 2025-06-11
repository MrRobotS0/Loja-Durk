<?php include '../includes/header.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<main class="max-w-7xl mx-auto px-6 py-12 bg-white text-black" style="font-family: 'Bebas Neue', cursive;" data-aos="fade-up">
    <section class="text-center mb-16">
        <h1 class="text-6xl font-extrabold text-purple-600 drop-shadow-lg">DURK</h1>
        <p class="mt-4 text-gray-700 text-xl max-w-2xl mx-auto">Roupas com alma, cultura urbana na pele e nas ideias. Bem-vindo ao mundo onde o estilo fala mais alto que palavras.</p>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-20">
        <div class="bg-gradient-to-br from-purple-600 to-purple-800 text-white p-6 rounded-xl shadow-2xl border border-purple-800 transform hover:scale-105 transition-all" data-aos="zoom-in-up">
            <h2 class="text-4xl font-bold mb-4">Do Asfalto pro Mundo</h2>
            <p class="text-lg">Criada no coração da rua, a Durk traduz a resistência, a liberdade e o grito de quem faz do concreto seu palco. A gente vive a quebrada e veste quem representa.</p>
        </div>

        <div class="rounded-xl shadow-xl border-4 border-purple-700 overflow-hidden h-64 md:h-auto" data-aos="zoom-in-down">
            <img src="imagens/synadunk.png" alt="Logo Synadunk" class="w-full h-full object-cover">
        </div>
    </section>

    <section class="text-center mb-20">
        <h2 class="text-4xl font-bold text-purple-500 mb-10">Valores de Rua</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-gray-100 text-black rounded-xl p-6 shadow-md hover:shadow-2xl transition-all border border-purple-600" data-aos="flip-left">
                <i class="fas fa-star text-purple-500 text-3xl mb-3"></i>
                <h3 class="text-2xl mb-2">Autenticidade</h3>
                <p class="text-gray-600 text-sm">Sem rótulos, sem padrão. Só atitude real.</p>
            </div>
            <div class="bg-gray-100 text-black rounded-xl p-6 shadow-md hover:shadow-2xl transition-all border border-purple-600" data-aos="flip-left" data-aos-delay="100">
                <i class="fas fa-fist-raised text-purple-500 text-3xl mb-3"></i>
                <h3 class="text-2xl mb-2">Resistência</h3>
                <p class="text-gray-600 text-sm">Superamos o corre com estilo e honra.</p>
            </div>
            <div class="bg-gray-100 text-black rounded-xl p-6 shadow-md hover:shadow-2xl transition-all border border-purple-600" data-aos="flip-left" data-aos-delay="200">
                <i class="fas fa-paint-brush text-purple-500 text-3xl mb-3"></i>
                <h3 class="text-2xl mb-2">Criatividade</h3>
                <p class="text-gray-600 text-sm">Cada peça é arte, cada look é expressão.</p>
            </div>
            <div class="bg-gray-100 text-black rounded-xl p-6 shadow-md hover:shadow-2xl transition-all border border-purple-600" data-aos="flip-left" data-aos-delay="300">
                <i class="fas fa-headphones text-purple-500 text-3xl mb-3"></i>
                <h3 class="text-2xl mb-2">Cultura</h3>
                <p class="text-gray-600 text-sm">Hip hop, skate, grafite e alma de quebrada. Essa é a gente.</p>
            </div>
        </div>
    </section>

    <section class="mt-20 text-center" data-aos="fade-up">
        <h2 class="text-4xl font-bold text-purple-500 mb-4">Muito além da moda. É atitude.</h2>
        <p class="text-gray-700 max-w-2xl mx-auto">Se a rua te ensinou a ser forte, a Durk veste sua essência. Estilo pra quem vive, cria e não abaixa a cabeça. Vem com a gente.</p>
        <a href="fale_conosco.php" class="mt-6 inline-block bg-purple-600 hover:bg-purple-700 text-white font-bold px-6 py-3 rounded-lg shadow-lg transition-all">Fale Conosco</a>
    </section>
</main>

<script>
    AOS.init();
</script>

<?php include '../includes/footer.php'; ?>