<?php
include 'db.php';

// Attiviamo la visualizzazione degli errori per non impazzire
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- 1. GESTIONE INSERIMENTO NUOVO CLIENTE ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_cliente'])) {
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $cf = $_POST['cf'];
    $email = $_POST['email'];
    
    $sql = "INSERT INTO clienti (nome, cognome, cf, email) VALUES ('$nome', '$cognome', '$cf', '$email')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php"); 
        exit();
    } else {
        die("<h2 style='color:red;'>ERRORE MYSQL (Cliente): " . $conn->error . "</h2>");
    }
}

// --- 2. GESTIONE INSERIMENTO NUOVO PRODOTTO ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_prodotto'])) {
    $nome_prod = $_POST['nome_prodotto'];
    $prezzo = $_POST['prezzo_unitario'];
    $qta_disp = $_POST['quantita_disponibile'];
    
    $sql = "INSERT INTO prodotti (nome, prezzo_unitario, quantita_disponibile) VALUES ('$nome_prod', '$prezzo', '$qta_disp')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        die("<h2 style='color:red;'>ERRORE MYSQL (Prodotto): " . $conn->error . "</h2>");
    }
}

// --- 3. GESTIONE INSERIMENTO NUOVO ORDINE E SCALO QUANTITA' ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_ordine'])) {
    $id_cliente = $_POST['id_cliente'];
    $id_prodotto = $_POST['id_prodotto'];
    $qta_acquistata = $_POST['quantita_acquistata'];
    
    $check_qta = $conn->query("SELECT quantita_disponibile FROM prodotti WHERE id_prodotto = $id_prodotto");
    if ($check_qta && $check_qta->num_rows > 0) {
        $row = $check_qta->fetch_assoc();
        if ($row['quantita_disponibile'] >= $qta_acquistata) {
            
            $sql_ord = "INSERT INTO ordini (id_cliente, id_prodotto, quantita_acquistata, data_ordine, stato) 
                        VALUES ('$id_cliente', '$id_prodotto', '$qta_acquistata', CURDATE(), 'in elaborazione')";
            
            if ($conn->query($sql_ord) === TRUE) {
                // Scala la quantità dal magazzino
                $sql_upd = "UPDATE prodotti SET quantita_disponibile = quantita_disponibile - $qta_acquistata WHERE id_prodotto = $id_prodotto";
                $conn->query($sql_upd);
                header("Location: index.php");
                exit();
            } else {
                die("<h2 style='color:red;'>ERRORE MYSQL (Ordine): " . $conn->error . "</h2>");
            }
        } else {
            die("<h2 style='color:orange;'>ERRORE: Quantità non sufficiente in magazzino!</h2><a href='index.php'>Torna indietro</a>");
        }
    } else {
        die("<h2 style='color:red;'>ERRORE: Prodotto non trovato! Hai inserito l'ID giusto?</h2><a href='index.php'>Torna indietro</a>");
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Negozio</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #333; }
        .form-group { margin-bottom: 15px; }
        input[type="text"], input[type="number"], input[type="email"] { padding: 8px; width: 100%; max-width: 300px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
        input[type="submit"] { padding: 10px 15px; background: #28a745; color: white; border: none; cursor: pointer; border-radius: 4px; font-weight: bold; }
        input[type="submit"]:hover { background: #218838; }
        .flex-container { display: flex; gap: 20px; flex-wrap: wrap; }
        .form-box { background: #e9ecef; padding: 15px; border-radius: 5px; flex: 1; min-width: 300px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 40px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        a.btn-report { display: inline-block; padding: 15px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-bottom: 20px; }
        a.btn-report:hover { background: #0056b3; }
    </style>
</head>
<body>

<div class="container">
    <a href="report.php" class="btn-report">Vedi Statistiche e Report SQL &rarr;</a>
    <h1>Pannello di Gestione Negozio</h1>

    <div class="flex-container">
        <div class="form-box">
            <h3>+ Nuovo Cliente</h3>
            <form method="POST" action="">
                <div class="form-group"><input type="text" name="nome" placeholder="Nome" required></div>
                <div class="form-group"><input type="text" name="cognome" placeholder="Cognome" required></div>
                <div class="form-group"><input type="text" name="cf" placeholder="Codice Fiscale" required></div>
                <div class="form-group"><input type="email" name="email" placeholder="Email" required></div>
                <input type="submit" name="add_cliente" value="Aggiungi Cliente">
            </form>
        </div>

        <div class="form-box">
            <h3>+ Nuovo Prodotto</h3>
            <form method="POST" action="">
                <div class="form-group"><input type="text" name="nome_prodotto" placeholder="Nome Prodotto" required></div>
                <div class="form-group"><input type="number" step="0.01" name="prezzo_unitario" placeholder="Prezzo (€)" required></div>
                <div class="form-group"><input type="number" name="quantita_disponibile" placeholder="Quantità Iniziale" required></div>
                <input type="submit" name="add_prodotto" value="Aggiungi Prodotto">
            </form>
        </div>

        <div class="form-box">
            <h3>+ Nuovo Ordine</h3>
            <form method="POST" action="">
                <div class="form-group"><input type="number" name="id_cliente" placeholder="ID Cliente" required></div>
                <div class="form-group"><input type="number" name="id_prodotto" placeholder="ID Prodotto" required></div>
                <div class="form-group"><input type="number" name="quantita_acquistata" placeholder="Quantità da Acquistare" required></div>
                <input type="submit" name="add_ordine" value="Conferma Ordine">
            </form>
        </div>
    </div>

    <hr>

    <h2>Elenco Prodotti (Magazzino)</h2>
    <table>
        <tr><th>ID Prodotto</th><th>Nome</th><th>Prezzo (€)</th><th>Disponibilità</th></tr>
        <?php
        $res = $conn->query("SELECT * FROM prodotti");
        if($res && $res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                echo "<tr><td>{$row['id_prodotto']}</td><td>{$row['nome']}</td><td>{$row['prezzo_unitario']}</td><td><b>{$row['quantita_disponibile']}</b></td></tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Nessun prodotto.</td></tr>";
        }
        ?>
    </table>

    <h2>Elenco Clienti</h2>
    <table>
        <tr><th>ID Cliente</th><th>Nome</th><th>Cognome</th><th>Codice Fiscale</th><th>Email</th></tr>
        <?php
        $res = $conn->query("SELECT * FROM clienti");
        if($res && $res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                echo "<tr><td>{$row['id_cliente']}</td><td>{$row['nome']}</td><td>{$row['cognome']}</td><td>{$row['cf']}</td><td>{$row['email']}</td></tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Nessun cliente.</td></tr>";
        }
        ?>
    </table>

    <h2>Elenco Ordini</h2>
    <table>
        <tr><th>ID Ordine</th><th>ID Cliente</th><th>ID Prodotto</th><th>Quantità</th><th>Data</th><th>Stato</th></tr>
        <?php
        $res = $conn->query("SELECT * FROM ordini ORDER BY id_ordine DESC");
        if($res && $res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                echo "<tr><td>{$row['id_ordine']}</td><td>{$row['id_cliente']}</td><td>{$row['id_prodotto']}</td><td>{$row['quantita_acquistata']}</td><td>{$row['data_ordine']}</td><td>{$row['stato']}</td></tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Nessun ordine.</td></tr>";
        }
        ?>
    </table>

</div>
</body>
</html>