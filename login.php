<?php
include 'database.php';
session_start();

$u = $_POST['username'] ?? '';
$p = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT password FROM utenti WHERE username = ?");
$stmt->bind_param("s", $u);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()) {
    if(password_verify($p, $row['password'])) { 
        $token = bin2hex(random_bytes(16)); 
        $scadenza = date('Y-m-d H:i:s', time() + 3600); // 1 ora di validità

        $stmt_t = $conn->prepare("UPDATE utenti SET token=?, scadenza_token=? WHERE username=?");
        $stmt_t->bind_param("sss", $token, $scadenza, $u);
        $stmt_t->execute();

        $_SESSION["username"] = $u; 
        $_SESSION["token"] = $token;
        header("Location: home.php");
        exit();
    }
}
header("Location: error.html");
?>