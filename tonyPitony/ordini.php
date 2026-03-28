<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head><title>Gestione Ordini</title></head>
<body>
    <h1>Nuovo Ordine</h1>
    <form method="POST">
        ID Cliente: <input type="number" name="id_c" required>
        ID Prodotto: <input type="number" name="id_p" required>
        Quantità: <input type="number" name="qta" required>
        <button type="submit" name="compra">Conferma Acquisto</button>
    </form>

    <?php
    if(isset($_POST['compra'])){
        $idc = $_POST['id_c']; $idp = $_POST['id_p']; $qta = $_POST['qta'];
        $data = date("Y-m-d");

        // 1. Crea l'ordine [cite: 8]
        $conn->query("INSERT INTO Ordini (id_cliente, data_ordine, stato) VALUES ('$idc', '$data', 'in elaborazione')");
        $id_o = $conn->insert_id;

        // 2. Prendi il prezzo attuale del prodotto
        $prod = $conn->query("SELECT prezzo_unitario FROM Prodotti WHERE id_prodotto = $idp")->fetch_assoc();
        $prezzo = $prod['prezzo_unitario'];

        // 3. Inserisci il dettaglio [cite: 8]
        $conn->query("INSERT INTO Dettagli_Ordine (id_ordine, id_prodotto, quantita, prezzo_acquisto) VALUES ('$id_o', '$idp', '$qta', '$prezzo')");

        // 4. AGGIORNAMENTO AUTOMATICO MAGAZZINO (Logica PHP) 
        $conn->query("UPDATE Prodotti SET quantita_disponibile = quantita_disponibile - $qta WHERE id_prodotto = $idp");

        echo "Ordine effettuato e magazzino aggiornato!";
    }
    ?>
</body>
</html>