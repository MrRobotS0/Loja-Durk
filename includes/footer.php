<footer class="bg-black border-t border-zinc-800 mt-24 relative overflow-hidden">
  <div class="absolute -top-12 left-0 right-0 pointer-events-none select-none opacity-[0.04]">
    <p class="font-display text-[20rem] leading-none text-white text-center whitespace-nowrap">DURK</p>
  </div>

  <div class="relative max-w-7xl mx-auto px-6 py-16 grid grid-cols-2 md:grid-cols-4 gap-10 text-sm text-zinc-400">
    <div class="col-span-2 md:col-span-1">
      <div class="flex items-center gap-3 mb-4">
        <img src="../views/imagens/logo.png" alt="Logo" class="h-10 w-auto" />
        <span class="font-display text-2xl text-white tracking-wider">DURK<span class="text-yellow-400">.</span></span>
      </div>
      <p class="text-zinc-500 text-xs leading-relaxed">
        Streetwear underground. Do asfalto pra rua, da rua pro mundo. Atitude em cada peça.
      </p>
      <div class="flex space-x-3 mt-5">
        <a href="#" class="w-9 h-9 flex items-center justify-center bg-zinc-900 hover:bg-yellow-400 hover:text-black rounded-full transition border border-zinc-800">
          <i class="fab fa-instagram"></i>
        </a>
        <a href="#" class="w-9 h-9 flex items-center justify-center bg-zinc-900 hover:bg-yellow-400 hover:text-black rounded-full transition border border-zinc-800">
          <i class="fab fa-tiktok"></i>
        </a>
        <a href="#" class="w-9 h-9 flex items-center justify-center bg-zinc-900 hover:bg-yellow-400 hover:text-black rounded-full transition border border-zinc-800">
          <i class="fab fa-spotify"></i>
        </a>
        <a href="#" class="w-9 h-9 flex items-center justify-center bg-zinc-900 hover:bg-yellow-400 hover:text-black rounded-full transition border border-zinc-800">
          <i class="fab fa-whatsapp"></i>
        </a>
      </div>
    </div>

    <div>
      <h4 class="font-display text-white mb-4 uppercase tracking-widest text-base">A Marca</h4>
      <ul class="space-y-2.5">
        <li><a href="../views/sobre.php" class="hover:text-yellow-400 transition">Quem Somos</a></li>
        <li><a href="../views/vestuario.php" class="hover:text-yellow-400 transition">Catálogo</a></li>
        <li><a href="#" class="hover:text-yellow-400 transition">FAQ</a></li>
      </ul>
    </div>

    <div>
      <h4 class="font-display text-white mb-4 uppercase tracking-widest text-base">Sua Conta</h4>
      <ul class="space-y-2.5">
        <li><a href="../views/minhaconta.php" class="hover:text-yellow-400 transition">Minha Conta</a></li>
        <li><a href="../views/minhaconta.php" class="hover:text-yellow-400 transition">Meus Pedidos</a></li>
        <li><a href="../views/favoritos.php" class="hover:text-yellow-400 transition">Favoritos</a></li>
        <li><a href="../views/carrinho.php" class="hover:text-yellow-400 transition">Carrinho</a></li>
      </ul>
    </div>

    <div>
      <h4 class="font-display text-white mb-4 uppercase tracking-widest text-base">Contato</h4>
      <ul class="space-y-2.5">
        <li><a href="../views/suporte.php" class="hover:text-yellow-400 transition">Fale Conosco</a></li>
        <li><a href="mailto:durk@fatec.com.br" class="hover:text-yellow-400 transition break-all">durk@fatec.com.br</a></li>
        <li class="text-zinc-500">Seg a Sex · 9h às 18h</li>
      </ul>
    </div>
  </div>

  <div class="relative border-t border-zinc-900">
    <div class="max-w-7xl mx-auto px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-zinc-500">
      <p>&copy; <?= date('Y') ?> <span class="text-yellow-400 font-semibold">DURK</span>. Todos os direitos reservados.</p>
      <p class="uppercase tracking-widest">Made on the streets · Worn worldwide</p>
    </div>
  </div>
</footer>
</body>
</html>
