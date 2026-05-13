<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    exit('Não autorizado.');
}

$user_id = (int) $_SESSION['usuario_id'];
$rua = trim($_POST['rua'] ?? '');
$numero = trim($_POST['numero'] ?? '');
$complemento = !empty($_POST['complemento']) ? trim($_POST['complemento']) : null;
$bairro = trim($_POST['bairro'] ?? '');
$cidade = trim($_POST['cidade'] ?? '');
$estado = trim($_POST['estado'] ?? '');
$cep = trim($_POST['cep'] ?? '');
$pais = trim($_POST['pais'] ?? 'Brasil');

$check = $conn->prepare("SELECT id FROM enderecos WHERE user_id = ?");
$check->bind_param("i", $user_id);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    $check->close();
    exit('Já existe um endereço cadastrado.');
}
$check->close();

$stmt = $conn->prepare("INSERT INTO enderecos (user_id, rua, numero, complemento, bairro, cidade, estado, cep, pais)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssssss", $user_id, $rua, $numero, $complemento, $bairro, $cidade, $estado, $cep, $pais);

if ($stmt->execute()) {
    echo 'Endereço cadastrado com sucesso';
} else {
    echo 'Erro ao cadastrar.';
}
$stmt->close();
