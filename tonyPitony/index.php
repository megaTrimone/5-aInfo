<?php
include 'db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Crea la cartella uploads se non esiste
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

// --- 1. GESTIONE INSERIMENTO NUOVO CLIENTE ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_cliente'])) {
    $nome = $conn->real_escape_string($_POST['nome']);
    $cognome = $conn->real_escape_string($_POST['cognome']);
    $cf = $conn->real_escape_string($_POST['cf']);
    $email = $conn->real_escape_string($_POST['email']);
    $data_nascita = $conn->real_escape_string($_POST['data_nascita']);
    $indirizzo = $conn->real_escape_string($_POST['indirizzo']);
    $citta = $conn->real_escape_string($_POST['citta']);
    $provincia = $conn->real_escape_string($_POST['provincia']);
    
    // Gestione upload foto
    $foto_path = "";
    if (isset($_FILES['foto_ci']) && $_FILES['foto_ci']['error'] == 0) {
        $foto_path = 'uploads/' . basename($_FILES['foto_ci']['name']);
        move_uploaded_file($_FILES['foto_ci']['tmp_name'], $foto_path);
    }
    
    $sql = "INSERT INTO clienti (nome, cognome, cf, email, data_nascita, indirizzo, citta, provincia, foto_carta_identita) 
            VALUES ('$nome', '$cognome', '$cf', '$email', '$data_nascita', '$indirizzo', '$citta', '$provincia', '$foto_path')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php"); 
        exit();
    } else {
        die("<h2 style='color:red;'>ERRORE MYSQL (Cliente): " . $conn->error . "</h2>");
    }
}

// --- 2. GESTIONE INSERIMENTO NUOVO PRODOTTO ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_prodotto'])) {
    $nome_prod = $conn->real_escape_string($_POST['nome_prodotto']);
    $prezzo = $_POST['prezzo_unitario'];
    $qta_disp = $_POST['quantita_disponibile'];
    $id_cat = $_POST['id_categoria'];
    
    $sql = "INSERT INTO prodotti (nome_prodotto, prezzo_unitario, quantita_disponibile, id_categoria) 
            VALUES ('$nome_prod', '$prezzo', '$qta_disp', '$id_cat')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        die("<h2 style='color:red;'>ERRORE MYSQL (Prodotto): " . $conn->error . "</h2>");
    }
}

// --- 3. GESTIONE INSERIMENTO NUOVO ORDINE ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_ordine'])) {
    $id_cliente = $_POST['id_cliente'];
    $id_prodotto = $_POST['id_prodotto'];
    $qta_acquistata = $_POST['quantita_ordinata'];
    
    $check_qta = $conn->query("SELECT quantita_disponibile FROM prodotti WHERE id_prodotto = $id_prodotto");
    if ($check_qta && $check_qta->num_rows > 0) {
        $row = $check_qta->fetch_assoc();
        if ($row['quantita_disponibile'] >= $qta_acquistata) {
            
            // A. Creiamo prima l'intestazione dell'ordine
            $sql_ord = "INSERT INTO ordini (id_cliente, data_ordine, stato) VALUES ('$id_cliente', CURDATE(), 'in elaborazione')";
            if ($conn->query($sql_ord) === TRUE) {
                
                // Recuperiamo l'ID dell'ordine appena creato
                $id_ordine_nuovo = $conn->insert_id;
                
                // B. Inseriamo il prodotto nei dettagli_ordine
                $sql_dettaglio = "INSERT INTO dettagli_ordine (id_ordine, id_prodotto, quantita_ordinata) 
                                  VALUES ('$id_ordine_nuovo', '$id_prodotto', '$qta_acquistata')";
                $conn->query($sql_dettaglio);

                // C. Scaliamo la quantità dal magazzino
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
        die("<h2 style='color:red;'>ERRORE: Prodotto non trovato!</h2><a href='index.php'>Torna indietro</a>");
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione E-commerce</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1, h2, h3 { color: #333; }
        .form-group { margin-bottom: 10px; }
        input[type="text"], input[type="number"], input[type="email"], input[type="date"], select, input[type="file"] { padding: 8px; width: 100%; max-width: 300px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="submit"] { padding: 10px 15px; background: #28a745; color: white; border: none; cursor: pointer; border-radius: 4px; font-weight: bold; width: 100%; max-width: 300px; margin-top: 10px;}
        input[type="submit"]:hover { background: #218838; }
        .flex-container { display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-start;}
        .form-box { background: #e9ecef; padding: 15px; border-radius: 5px; flex: 1; min-width: 320px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 40px; font-size: 14px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #007bff; color: white; }
        a.btn-report { display: inline-block; padding: 15px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-bottom: 20px; }
        a.btn-report:hover { background: #0056b3; }
    </style>
</head>
<body>

<div class="container">
    <a href="report.php" class="btn-report">Vedi Statistiche e Report SQL &rarr;</a>
    <h1>Gestione E-commerce</h1>

    <div class="flex-container">
        <div class="form-box">
            <h3>+ Nuovo Cliente</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group"><input type="text" name="nome" placeholder="Nome" required></div>
                <div class="form-group"><input type="text" name="cognome" placeholder="Cognome" required></div>
                <div class="form-group"><input type="text" name="cf" placeholder="Codice Fiscale" required></div>
                <div class="form-group"><input type="email" name="email" placeholder="Email" required></div>
                <div class="form-group"><input type="date" name="data_nascita" required></div>
                <div class="form-group"><input type="text" name="indirizzo" placeholder="Indirizzo" required></div>
                <div class="form-group"><input type="text" name="citta" placeholder="Città" required></div>
                <div class="form-group"><input type="text" name="provincia" placeholder="Provincia (es. MI)" maxlength="2" required></div>
                <div class="form-group">
                    <label style="font-size: 12px;">Foto Carta d'Identità:</label><br>
                    <input type="file" name="foto_ci" accept="image/*">
                </div>
                <input type="submit" name="add_cliente" value="Aggiungi Cliente">
            </form>
        </div>

        <div class="form-box">
            <h3>+ Nuovo Prodotto</h3>
            <form method="POST" action="">
                <div class="form-group"><input type="text" name="nome_prodotto" placeholder="Nome Prodotto" required></div>
                <div class="form-group"><input type="number" step="0.01" name="prezzo_unitario" placeholder="Prezzo (€)" required></div>
                <div class="form-group"><input type="number" name="quantita_disponibile" placeholder="Quantità Magazzino" required></div>
                <div class="form-group">
                    <select name="id_categoria" required>
                        <option value="">-- Seleziona Categoria --</option>
                        <?php
                        $cat_res = $conn->query("SELECT * FROM categorie");
                        while($c = $cat_res->fetch_assoc()) {
                            echo "<option value='{$c['id_categoria']}'>{$c['nome_categoria']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <input type="submit" name="add_prodotto" value="Aggiungi Prodotto">
            </form>
        </div>

        <div class="form-box">
            <h3>+ Nuovo Ordine Rapido</h3>
            <form method="POST" action="">
                <div class="form-group"><input type="number" name="id_cliente" placeholder="ID Cliente" required></div>
                <div class="form-group"><input type="number" name="id_prodotto" placeholder="ID Prodotto" required></div>
                <div class="form-group"><input type="number" name="quantita_ordinata" placeholder="Quantità" required></div>
                <input type="submit" name="add_ordine" value="Crea Ordine">
            </form>
        </div>
    </div>

    <hr>

    <h2>Magazzino Prodotti</h2>
    <table>
        <tr><th>ID</th><th>Nome</th><th>Categoria</th><th>Prezzo (€)</th><th>Disponibilità</th></tr>
        <?php
        $res = $conn->query("SELECT p.*, c.nome_categoria FROM prodotti p LEFT JOIN categorie c ON p.id_categoria = c.id_categoria");
        if($res && $res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                echo "<tr><td>{$row['id_prodotto']}</td><td>{$row['nome_prodotto']}</td><td>{$row['nome_categoria']}</td><td>{$row['prezzo_unitario']}</td><td><b>{$row['quantita_disponibile']}</b></td></tr>";
            }
        } else { echo "<tr><td colspan='5'>Nessun prodotto.</td></tr>"; }
        ?>
    </table>

    <h2>Clienti Registrati</h2>
    <table>
        <tr><th>ID</th><th>Nome</th><th>Città</th><th>Email</th><th>CF</th><th>Foto Documento</th></tr>
        <?php
        $res = $conn->query("SELECT * FROM clienti");
        if($res && $res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                $foto = $row['foto_carta_identita'] ? "Caricata" : "Mancante";
                echo "<tr><td>{$row['id_cliente']}</td><td>{$row['nome']} {$row['cognome']}</td><td>{$row['citta']} ({$row['provincia']})</td><td>{$row['email']}</td><td>{$row['cf']}</td><td>$foto</td></tr>";
            }
        } else { echo "<tr><td colspan='6'>Nessun cliente.</td></tr>"; }
        ?>
    </table>

    <h2>Dettaglio Ordini</h2>
    <table>
        <tr><th>ID Ordine</th><th>Data</th><th>Cliente</th><th>Prodotto</th><th>Q.tà</th><th>Stato</th></tr>
        <?php
        $sql_ordini = "SELECT o.id_ordine, o.data_ordine, o.stato, c.nome, c.cognome, p.nome_prodotto, d.quantita_ordinata 
                       FROM ordini o 
                       JOIN clienti c ON o.id_cliente = c.id_cliente
                       JOIN dettagli_ordine d ON o.id_ordine = d.id_ordine
                       JOIN prodotti p ON d.id_prodotto = p.id_prodotto
                       ORDER BY o.id_ordine DESC";
        $res = $conn->query($sql_ordini);
        if($res && $res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                echo "<tr><td>{$row['id_ordine']}</td><td>{$row['data_ordine']}</td><td>{$row['nome']} {$row['cognome']}</td><td>{$row['nome_prodotto']}</td><td>{$row['quantita_ordinata']}</td><td>{$row['stato']}</td></tr>";
            }
        } else { echo "<tr><td colspan='6'>Nessun ordine.</td></tr>"; }
        ?>
    </table>

</div>
</body>
</html>