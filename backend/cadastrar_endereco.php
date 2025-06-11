<?php
include 'db.php';

$user_id = $_POST['user_id'];
$rua = $_POST['rua'];
$numero = $_POST['numero'];
$complemento = $_POST['complemento'];
$bairro = $_POST['bairro'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$cep = $_POST['cep'];
$pais = $_POST['pais'];

$check = mysqli_query($conn, "SELECT id FROM enderecos WHERE user_id = $user_id");
if ($check && mysqli_num_rows($check) > 0) {
    exit('Já existe um endereço cadastrado.');
}

$sql = "INSERT INTO enderecos (user_id, rua, numero, complemento, bairro, cidade, estado, cep, pais)
        VALUES ('$user_id', '$rua', '$numero', '$complemento', '$bairro', '$cidade', '$estado', '$cep', '$pais')";

if (mysqli_query($conn, $sql)) {
    echo 'Endereço cadastrado com sucesso';
} else {
    echo 'Erro ao cadastrar: ' . mysqli_error($conn);
}
