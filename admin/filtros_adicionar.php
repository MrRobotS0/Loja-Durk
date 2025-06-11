<?php
include 'includes/auth.php';
include '../backend/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['tipo'])) {
    $tipo = $_GET['tipo'];

    switch ($tipo) {
        case 'categoria':
            $nome = trim($_POST['nome']);
            $slug = trim($_POST['slug']);

            if (!empty($nome) && !empty($slug)) {
                $stmt = $conn->prepare("INSERT INTO categorias (nome, slug) VALUES (?, ?)");
                $stmt->bind_param("ss", $nome, $slug);
                $stmt->execute();
                $stmt->close();
            }
            break;

        case 'marca':
            $nome = trim($_POST['nome']);
            $descricao = trim($_POST['descricao']);

            if (!empty($nome) && !empty($descricao)) {
                $stmt = $conn->prepare("INSERT INTO marcas (nome, descricao) VALUES (?, ?)");
                $stmt->bind_param("ss", $nome, $descricao);
                $stmt->execute();
                $stmt->close();
            }
            break;

        case 'tamanho':
            $descricao = trim($_POST['descricao']);

            if (!empty($descricao)) {
                $stmt = $conn->prepare("INSERT INTO tamanhos (descricao) VALUES (?)");
                $stmt->bind_param("s", $descricao);
                $stmt->execute();
                $stmt->close();
            }
            break;
    }
}

$conn->close();
header("Location: filtros.php");
exit();
