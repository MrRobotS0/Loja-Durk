<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Durk</title>
  <link rel="icon" type="image/png" href="imagens/logo.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>

    .glow {
      text-shadow: 0 0 10px rgba(168, 85, 247, 0.6), 0 0 20px rgba(168, 85, 247, 0.4);
    }
  </style>
</head>

<body class="bg-white-950 text-black">
  <?php include '../includes/header.php'; ?>

  <div class="relative w-full max-h-[500px] overflow-hidden" data-aos="zoom-in">
    <img src="imagens/banner.png" alt="Banner Durk" class="w-full h-[500px] object-cover object-center rounded-b-2xl shadow-xl">
  </div>

  <main class="max-w-7xl mx-auto px-6 py-16">
    <section class="text-center mb-16" data-aos="fade-up">
      <h2 class="text-4xl md:text-5xl text-purple-500 mb-4 glow">Novos Lançamentos</h2>
      <p class="text-black-300 text-lg max-w-2xl mx-auto">Chega de mais do mesmo. Aqui você encontra atitude, cultura e estilo em cada peça.</p>
    </section>

    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
      <div class="bg-white rounded-xl shadow-lg p-4 transform hover:scale-105 transition-all duration-300" data-aos="fade-up">
        <div class="aspect-[3/4] rounded-lg overflow-hidden mb-4 bg-black">
          <img src="imagens/techfleece.png" alt="Tech Fleece" class="object-cover w-full h-full">
        </div>
        <h3 class="text-xl font-bold text-black">Jaqueta Tech Fleece Dk X Syna</h3>
        <p class="text-purple-400 text-sm">Moletom</p>
        <p class="text-green-500 text-lg font-semibold mt-2">R$ 99,90</p>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-4 transform hover:scale-105 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
        <div class="aspect-[3/4] rounded-lg overflow-hidden mb-4 bg-black">
          <img src="imagens/oversizeddk.png" alt="Oversized" class="object-cover w-full h-full">
        </div>
        <h3 class="text-xl font-bold text-black">Camiseta Oversized Durk</h3>
        <p class="text-purple-400 text-sm">Camisetas</p>
        <p class="text-green-500 text-lg font-semibold mt-2">R$ 89,90</p>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-4 transform hover:scale-105 transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
        <div class="aspect-[3/4] rounded-lg overflow-hidden mb-4 bg-black">
          <img src="imagens/cintodk.png" alt="Cinto" class="object-cover w-full h-full">
        </div>
        <h3 class="text-xl font-bold text-black">Cinto Masculino Durk</h3>
        <p class="text-purple-400 text-sm">Acessórios</p>
        <p class="text-green-500 text-lg font-semibold mt-2">R$ 69,90</p>
      </div>
    </section>
  </main>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>

  <?php include '../includes/footer.php'; ?>
</body>

</html>
