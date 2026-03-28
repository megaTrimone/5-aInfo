<?php
include 'database.php';

#if inline che controlla se è stato settato il valore di color
$orderNumber = isset($_GET['id']) ? $_GET['id'] : '';

$sql = "SELECT * FROM orderdetails WHERE orderNumber='" . $orderNumber ."'";

$result = $conn->query($sql); //Eseguo la query

//Printo il numero di righe
print("Numero di righe ritornate: " . $result->num_rows . "<br>"); 

print("<table><tr><th>NumeroOrdine</th><th>NumeroProdotto</th><th>Quantità</th><th>Prezzo</th> </tr>");
foreach($result as $row) {
  print("<tr><td>".$row["orderNumber"]. "</td><td>".$row["productCode"] . "</td><td>" . $row["quantityOrdered"] . "</td><td>" 
  . $row["priceEach"] . "</td></tr>");
}
print("</table>");

?>