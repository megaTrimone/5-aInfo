<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "scuola_test";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) { die("Errore: " . $conn->connect_error); }
?>