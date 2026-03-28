<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Report e Statistiche</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f9; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 800px; margin: auto; }
        h1, h2 { color: #333; }
        .result-box { background: #e9ecef; padding: 15px; border-left: 5px solid #007bff; margin-bottom: 20px; border-radius: 4px; }
        a.btn { display: inline-block; padding: 10px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; margin-bottom: 20px; }
        a.btn:hover { background: #5a6268; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="btn">&larr; Torna alla Gestione</a>
    <h1>Report e Statistiche SQL</h1>

    <h2>1. Ordini effettuati "Oggi"</h2>
    <div class="result-box">
        <?php
        $sql1 = "SELECT id_ordine, stato FROM ordini WHERE data_ordine = CURDATE()";
        $res1 = $conn->query($sql1);
        if ($res1 && $res1->num_rows > 0) {
            while($row = $res1->fetch_assoc()) {
                echo "Ordine #" . $row['id_ordine'] . " - Stato: " . $row['stato'] . "<br>";
            }
        } else {
            echo "Nessun ordine effettuato oggi.";
        }
        ?>
    </div>

    <h2>2. Ordini dell'ultima settimana</h2>
    <div class="result-box">
        <?php
        $sql2 = "SELECT id_ordine, data_ordine FROM ordini WHERE data_ordine >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        $res2 = $conn->query($sql2);
        if ($res2 && $res2->num_rows > 0) {
            while($row = $res2->fetch_assoc()) {
                echo "Ordine #" . $row['id_ordine'] . " del " . $row['data_ordine'] . "<br>";
            }
        } else {
            echo "Nessun ordine nell'ultima settimana.";
        }
        ?>
    </div>

    <h2>3. Totale da pagare per ogni ordine</h2>
    <div class="result-box">
        <?php
        $sql3 = "SELECT o.id_ordine, SUM(o.quantita_acquistata * p.prezzo_unitario) AS totale 
                 FROM ordini o 
                 JOIN prodotti p ON o.id_prodotto = p.id_prodotto 
                 GROUP BY o.id_ordine";
        $res3 = $conn->query($sql3);
        if ($res3 && $res3->num_rows > 0) {
            while($row = $res3->fetch_assoc()) {
                echo "Ordine #" . $row['id_ordine'] . ": <strong>" . number_format($row['totale'], 2) . "€</strong><br>";
            }
        } else {
            echo "Nessun dato disponibile.";
        }
        ?>
    </div>

    <h2>4. Somma delle quantità vendute per ogni prodotto</h2>
    <div class="result-box">
        <?php
        $sql4 = "SELECT p.nome, SUM(o.quantita_acquistata) AS totale_venduto 
                 FROM ordini o 
                 JOIN prodotti p ON o.id_prodotto = p.id_prodotto 
                 GROUP BY p.id_prodotto";
        $res4 = $conn->query($sql4);
        if ($res4 && $res4->num_rows > 0) {
            while($row = $res4->fetch_assoc()) {
                echo $row['nome'] . ": <strong>" . $row['totale_venduto'] . " unità vendute</strong><br>";
            }
        } else {
            echo "Nessun prodotto venduto finora.";
        }
        ?>
    </div>

    <h2>5. Prodotto più venduto in assoluto</h2>
    <div class="result-box">
        <?php
        $sql5 = "SELECT p.nome, SUM(o.quantita_acquistata) AS totale_venduto 
                 FROM ordini o 
                 JOIN prodotti p ON o.id_prodotto = p.id_prodotto 
                 GROUP BY p.id_prodotto 
                 ORDER BY totale_venduto DESC LIMIT 1";
        $res5 = $conn->query($sql5);
        if ($res5 && $res5->num_rows > 0) {
            $row = $res5->fetch_assoc();
            echo "Il prodotto più venduto è: <strong>" . $row['nome'] . "</strong> con " . $row['totale_venduto'] . " pezzi venduti.";
        } else {
            echo "Nessun dato sufficiente.";
        }
        ?>
    </div>

    <h2>6. Clienti che hanno speso più di 1000€</h2>
    <div class="result-box">
        <?php
        $sql6 = "SELECT c.nome, c.cognome, SUM(o.quantita_acquistata * p.prezzo_unitario) AS totale_speso 
                 FROM clienti c 
                 JOIN ordini o ON c.id_cliente = o.id_cliente 
                 JOIN prodotti p ON o.id_prodotto = p.id_prodotto 
                 GROUP BY c.id_cliente 
                 HAVING totale_speso > 1000";
        $res6 = $conn->query($sql6);
        if ($res6 && $res6->num_rows > 0) {
            while($row = $res6->fetch_assoc()) {
                echo $row['nome'] . " " . $row['cognome'] . " - Totale speso: <strong>" . number_format($row['totale_speso'], 2) . "€</strong><br>";
            }
        } else {
            echo "Nessun cliente ha superato la soglia dei 1000€.";
        }
        ?>
    </div>

</div>
</body>
</html>