<?php include '../includes/header.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<main class="max-w-5xl mx-auto px-6 py-12" data-aos="fade-up">
  <div class="text-center mb-12">
    <h1 class="text-4xl font-extrabold text-gray-900">Fale Conosco</h1>
    <p class="mt-2 text-gray-600">Precisa de ajuda ou quer nos enviar uma mensagem? Preencha o formulário abaixo.</p>
  </div>

  <div class="bg-white shadow-xl rounded-xl p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
    <div>
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">Entre em contato</h2>
      <p class="text-gray-600 mb-4">Nossa equipe está disponível para tirar dúvidas e ajudar no que for necessário. Respondemos em até 48h úteis.</p>
      <ul class="space-y-3 text-gray-700">
        <li><strong>Email:</strong> suporte@lojadurk.com</li>
        <li><strong>Telefone:</strong> (11) 99999-9999</li>
        <li><strong>Horário:</strong> Seg a Sex, das 9h às 18h</li>
      </ul>
    </div>

    <form class="space-y-6">
      <div>
        <label class="block text-sm font-medium text-gray-700">Nome</label>
        <input type="text" placeholder="Seu nome completo" class="mt-1 w-full border rounded-md p-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" placeholder="seuemail@exemplo.com" class="mt-1 w-full border rounded-md p-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Mensagem</label>
        <textarea rows="4" placeholder="Digite sua mensagem aqui" class="mt-1 w-full border rounded-md p-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
      </div>

      <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition duration-300">Enviar Mensagem</button>
    </form>
  </div>
</main>

<script>AOS.init();</script>

<?php include '../includes/footer.php'; ?>
