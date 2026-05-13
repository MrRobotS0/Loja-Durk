<?php
include '../includes/verifica_login.php';
include '../includes/header.php';
?>

<title>Pedido Confirmado · DURK</title>

<main class="min-h-[70vh] flex flex-col items-center justify-center px-4 py-16 text-center relative overflow-hidden">
  <div class="absolute inset-0 pointer-events-none opacity-[0.04]">
    <p class="font-display text-[20rem] leading-none text-yellow-400 text-center whitespace-nowrap">VALEU</p>
  </div>

  <div class="relative max-w-xl">
    <div class="w-24 h-24 rounded-full bg-emerald-500/10 border-2 border-emerald-500 flex items-center justify-center mx-auto mb-8">
      <i class="fa-solid fa-check text-5xl text-emerald-400"></i>
    </div>
    <span class="text-yellow-400 text-xs uppercase tracking-[0.4em] font-bold">/ Pedido Confirmado</span>
    <h1 class="font-display text-5xl md:text-6xl text-white mt-3 mb-6">VALEU PELA CONFIANÇA!</h1>
    <p class="text-lg text-zinc-400 mb-10 leading-relaxed">
      Seu pedido foi registrado com sucesso. Tu vai receber uma confirmação no seu email e em breve a gente manda os updates do envio. Bora estilizar! <span class="text-yellow-400">★</span>
    </p>
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
      <a href="vestuario.php" class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-300 text-black px-6 py-3 rounded-lg font-bold uppercase tracking-wider text-sm transition">
        Continuar Comprando <i class="fa-solid fa-arrow-right"></i>
      </a>
      <a href="minhaconta.php" class="inline-flex items-center gap-2 bg-transparent border-2 border-white hover:border-yellow-400 hover:text-yellow-400 text-white px-6 py-3 rounded-lg font-bold uppercase tracking-wider text-sm transition">
        <i class="fa-solid fa-box"></i> Ver meus pedidos
      </a>
    </div>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
