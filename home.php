<?php
include 'database.php';
session_start();

// Punto 6: Controllo accesso e Punto 4: Sessione
if(!isset($_SESSION["username"]) || !isset($_SESSION["token"])) {
    header("Location: error.html");
    exit();
}

$u = $_SESSION["username"];
$t = $_SESSION["token"];

// Punto 5: Verifica Token e Scadenza nel DB
$stmt = $conn->prepare("SELECT scadenza_token FROM utenti WHERE username=? AND token=?");
$stmt->bind_param("ss", $u, $t);
$stmt->execute();
$res = $stmt->get_result();

if($row = $res->fetch_assoc()) {
    if(strtotime($row['scadenza_token']) < time()) {
        header("Location: logout.php"); // Token scaduto
        exit();
    }
} else {
    header("Location: error.html"); // Token non valido
    exit();
}

// Logica dolci originale
$c = $_GET['categoria'] ?? 'Torte';
$stmt_d = $conn->prepare("SELECT * FROM dolce WHERE categoria = ?");
$stmt_d->bind_param("s", $c);
$stmt_d->execute();
$res_d = $stmt_d->get_result();
?>
<html>
<body>
    <h1>Benvenuto <?php echo htmlspecialchars($u); ?>!</h1>
    <form action='' method='GET'>
        Categoria: <select name='categoria'>
            <option value='Torte'>Torte</option>
            <option value='Gelato'>Gelato</option>
        </select>
        <input type='submit' value='Cerca'>
    </form>
    <h3>Risultati:</h3>
    <?php while($d = $res_d->fetch_assoc()) { echo implode(" ", $d) . "<br>"; } ?>
    <hr>
    <a href="logout.php">Logout</a>
</body>
</html>