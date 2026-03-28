<?php
$servername = "127.0.0.1"; 
$username = "root";        // L'utente predefinito di XAMPP è sempre root!
$password = "";           
$database = "5AI_LIDEO";  
$port = 3306;              

//Mi collego al database
$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
  //redirecto a una pagina di errore
  die("Connection failed: " . $conn->connect_error);
}

?>