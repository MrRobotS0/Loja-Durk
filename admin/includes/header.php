<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../views/imagens/logo.png">
  <title>Admin - Painel</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body class="bg-gray-50 font-sans">
  <header class="bg-gray-900 text-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <img src="../views/imagens/logoAba.png" alt="Durk Logo" class="h-12 w-auto">
        <h1 class="text-2xl font-bold tracking-wide">Painel Admin</h1>
      </div>

      <nav>
        <ul class="flex items-center gap-6 text-sm font-medium">
          <li><a href="../views/index.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition"><i class="fa-solid fa-house"></i> Website Loja</a></li>
          <li><a href="index.php" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition"><i class="fa-solid fa-sliders"></i> Painel</a></li>
        </ul>
      </nav>

      <div>
        <a href="../backend/logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2">
          <i class="fa-solid fa-arrow-right-from-bracket"></i> Sair
        </a>
      </div>
    </div>
  </header>
</body>

</html>
