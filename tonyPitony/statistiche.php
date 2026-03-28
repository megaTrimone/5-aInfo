<?php include 'db.php'; ?>
<h1>Report Aziendale</h1>

<h3>Totale da pagare per ogni ordine [cite: 18]</h3>
<ul>
<?php
$q1 = $conn->query("SELECT id_ordine, SUM(quantita * prezzo_acquisto) as tot FROM Dettagli_Ordine GROUP BY id_ordine");
while($r = $q1->fetch_assoc()) echo "<li>Ordine #{$r['id_ordine']}: Totale {$r['tot']}€</li>";
?>
</ul>

<h3>Prodotto più venduto [cite: 20]</h3>
<?php
$q2 = $conn->query("SELECT p.nome, SUM(d.quantita) as v FROM Prodotti p JOIN Dettagli_Ordine d ON p.id_prodotto = d.id_prodotto GROUP BY p.id_prodotto ORDER BY v DESC LIMIT 1");
$best = $q2->fetch_assoc();
echo "Il prodotto più venduto è: <b>" . ($best['nome'] ?? 'Nessuno') . "</b>";
?>

<h3>Clienti "Top" (Spesa > 1000€) [cite: 21]</h3>
<ul>
<?php
$q3 = $conn->query("SELECT c.nome, SUM(d.quantita * d.prezzo_acquisto) as spesa FROM Clienti c JOIN Ordini o ON c.id_cliente = o.id_cliente JOIN Dettagli_Ordine d ON o.id_ordine = d.id_ordine GROUP BY c.id_cliente HAVING spesa > 1000");
while($r = $q3->fetch_assoc()) echo "<li>{$r['nome']} ha speso {$r['spesa']}€</li>";
?>
</ul>