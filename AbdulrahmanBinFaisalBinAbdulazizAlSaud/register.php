<?php
include 'database.php';
$u = $_POST['username'] ?? '';
$e = $_POST['email'] ?? '';
$p = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT); 

$sql = "INSERT INTO utenti (username, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $u, $e, $p);

if($stmt->execute()) {
    echo "Registrazione andata a fuon fine! <a href='login.html'>Vai al Login</a>";
} else {
    header("Location: error.html");
}
?>