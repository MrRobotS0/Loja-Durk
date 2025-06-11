<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "durk";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}
?>
