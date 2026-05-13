<?php include '../includes/header.php'; ?>

<title>Fale Conosco · DURK</title>

<main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-10 py-12">

  <div class="text-center mb-12">
    <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Atendimento</span>
    <h1 class="font-display text-5xl md:text-6xl text-white mt-3">FALE CONOSCO</h1>
    <p class="text-zinc-400 mt-3 max-w-xl mx-auto">Tá com dúvida ou quer trocar uma ideia? Manda mensagem que a gente responde.</p>
  </div>

  <div class="bg-zinc-950 border border-zinc-900 rounded-3xl p-6 md:p-10 grid grid-cols-1 md:grid-cols-2 gap-10">
    <div>
      <h2 class="font-display text-3xl text-white mb-5">CONTATO DIRETO</h2>
      <p class="text-zinc-400 mb-6 leading-relaxed">Nossa equipe está disponível para tirar dúvidas e ajudar no que for. Respondemos em até 48h úteis.</p>
      <ul class="space-y-4">
        <li class="flex items-start gap-4 bg-black border border-zinc-900 rounded-lg p-4">
          <div class="w-10 h-10 bg-yellow-400 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fa-regular fa-envelope text-black"></i>
          </div>
          <div>
            <p class="text-[10px] uppercase tracking-widest text-yellow-400 font-bold">Email</p>
            <a href="mailto:suporte@lojadurk.com" class="text-white font-bold hover:text-yellow-400 transition">suporte@lojadurk.com</a>
          </div>
        </li>
        <li class="flex items-start gap-4 bg-black border border-zinc-900 rounded-lg p-4">
          <div class="w-10 h-10 bg-yellow-400 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-phone text-black"></i>
          </div>
          <div>
            <p class="text-[10px] uppercase tracking-widest text-yellow-400 font-bold">Telefone</p>
            <p class="text-white font-bold">(11) 99999-9999</p>
          </div>
        </li>
        <li class="flex items-start gap-4 bg-black border border-zinc-900 rounded-lg p-4">
          <div class="w-10 h-10 bg-yellow-400 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-clock text-black"></i>
          </div>
          <div>
            <p class="text-[10px] uppercase tracking-widest text-yellow-400 font-bold">Horário</p>
            <p class="text-white font-bold">Seg a Sex · 9h às 18h</p>
          </div>
        </li>
      </ul>

      <div class="mt-6 flex gap-3">
        <a href="#" class="w-11 h-11 flex items-center justify-center bg-black border border-zinc-900 hover:border-yellow-400 hover:text-yellow-400 text-white rounded-full transition">
          <i class="fab fa-instagram"></i>
        </a>
        <a href="#" class="w-11 h-11 flex items-center justify-center bg-black border border-zinc-900 hover:border-yellow-400 hover:text-yellow-400 text-white rounded-full transition">
          <i class="fab fa-tiktok"></i>
        </a>
        <a href="#" class="w-11 h-11 flex items-center justify-center bg-black border border-zinc-900 hover:border-yellow-400 hover:text-yellow-400 text-white rounded-full transition">
          <i class="fab fa-whatsapp"></i>
        </a>
      </div>
    </div>

    <form class="space-y-4" onsubmit="event.preventDefault(); this.querySelector('button').innerHTML='<i class=\'fa-solid fa-check\'></i> Enviado!';">
      <h2 class="font-display text-3xl text-white mb-2">DEIXA TUA MENSAGEM</h2>
      <div>
        <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Nome</label>
        <input type="text" required placeholder="Seu nome" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition placeholder-zinc-600">
      </div>
      <div>
        <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Email</label>
        <input type="email" required placeholder="voce@email.com" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition placeholder-zinc-600">
      </div>
      <div>
        <label class="block text-[10px] font-bold text-yellow-400 uppercase tracking-widest mb-2">Mensagem</label>
        <textarea rows="5" required placeholder="No que a gente pode te ajudar?" class="w-full px-4 py-3 bg-black border border-zinc-800 text-white rounded-lg focus:outline-none focus:border-yellow-400 transition placeholder-zinc-600 resize-none"></textarea>
      </div>
      <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-300 text-black font-bold uppercase tracking-wider py-3.5 rounded-lg transition shadow-lg shadow-yellow-400/10">
        Enviar Mensagem
      </button>
    </form>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
