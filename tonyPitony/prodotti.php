<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <title>Magazzino Prodotti</title>
    <style>body { font-family: sans-serif; padding: 20px; } table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #ccc; padding: 10px; }</style>
</head>
<body>
    <h1>Gestione Magazzino</h1>
    <form method="POST">
        <h3>Aggiungi Prodotto</h3>
        <input type="text" name="nome" placeholder="Nome Prodotto" required>
        <input type="number" step="0.01" name="prezzo" placeholder="Prezzo" required>
        <input type="number" name="qta" placeholder="Quantità iniziale" required>
        <button type="submit" name="add_prod">Inserisci</button>
    </form>

    <?php
    if(isset($_POST['add_prod'])){
        $n = $_POST['nome']; $p = $_POST['prezzo']; $q = $_POST['qta'];
        $conn->query("INSERT INTO Prodotti (nome, prezzo_unitario, quantita_disponibile) VALUES ('$n', '$p', '$q')");
        echo "Prodotto inserito!";
    }
    ?>

    <h2>Prodotti in Vendita</h2>
    <table>
        <tr><th>ID</th><th>Nome</th><th>Prezzo</th><th>Disponibilità [cite: 5]</th></tr>
        <?php
        $res = $conn->query("SELECT * FROM Prodotti");
        while($r = $res->fetch_assoc()) {
            echo "<tr><td>{$r['id_prodotto']}</td><td>{$r['nome']}</td><td>{$r['prezzo_unitario']}€</td><td>{$r['quantita_disponibile']}</td></tr>";
        }
        ?>
    </table>
</body>
</html>