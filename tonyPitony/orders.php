<h2>Filtri </h2>
<form action='' method='GET'>
<select name='stato'>
  <option value='Shipped'>Shipped</option>
  <option value='Resolved'>Resolved</option>
  <option value='Cancelled'>Cancelled</option>
  <option value='On Hold'>On Hold</option>
  <option value='Disputed'>Disputed</option>
  <option value='In Process'>In Process</option>
  <option value=''>Tutti</option>
</select>
<input name='orderNumber'>
<input type='date' name='orderDate'>
<input type='submit'>
</form>
<h2>Risultati </h2>


<?php
include 'database.php';

$stato = isset($_GET['stato']) ? $_GET['stato'] : '';
$orderNumber = isset($_GET['orderNumber']) ? $_GET['orderNumber'] : '';
$orderDate = isset($_GET['orderDate']) ? $_GET['orderDate'] : '';

$sql = "SELECT * FROM orders WHERE 1=1 ";
if($stato && !empty($stato)) {
  $sql .= " AND status = '". $stato ."'";
}
if($orderNumber && !empty($orderNumber)) {
  $sql .= " AND orderNumber = '". $orderNumber ."'";
}
if($orderDate && !empty($orderDate)) {
  $sql .= " AND orderDate = '". $orderDate ."'";
}

$result = $conn->query($sql); //Eseguo la query

//Printo il numero di righe
print("Numero di righe ritornate: " . $result->num_rows . "<br>"); 

#action='' fa si che il redirect torni sulla stessa pagina
#anche chiamato self-redirect



print("<table><tr><th>NumeroOrdine</th><th>Stato</th><th>Data</th><th>Commento</th> <th>Azioni</th> </tr>");
foreach($result as $row) {
  print("<tr><td> <a href ='orderDetails.php?id=".$row["orderNumber"]."'> " .$row["orderNumber"] . "</a></td><td>" . $row["status"] . "</td><td>" 
  . $row["orderDate"] . "</td><td>" . $row["comments"] . "</td> <td><a href='deleteOrder.php?id=". $row["orderNumber"] ."'>Cancella</a></td></tr>");
}
print("</table>");

?>