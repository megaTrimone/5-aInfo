<h2>Inserimento</h2>
<form action='' method='GET'>
    OrderNumber: <input name='orderNumber'><br>
    Stato: <select name='stato'>
  <option value='Shipped'>Shipped</option>
  <option value='Resolved'>Resolved</option>
  <option value='Cancelled'>Cancelled</option>
  <option value='On Hold'>On Hold</option>
  <option value='Disputed'>Disputed</option>
  <option value='In Process'>In Process</option>
  <option value=''>Tutti</option>
</select><br>
Date: <input type='date' name='orderDate'><br>
RequiredDate: <input type='date' name='requiredDate'><br>
ShippedDate: <input type='date' name='shippedDate'><br>
CustomerNumber: <select name='customerNumber'>
<?php

include 'database.php';

$sql = "SELECT DISTINCT customerNumber, contactFirstName, contactLastName FROM customers";

$result = $conn->query($sql); //Eseguo la query

foreach($result as $row) {
    print("<option value='". $row["customerNumber"] ."'>".$row["customerNumber"] . " - ". $row["contactFirstName"] . $row["contactLastName"] ."</option>");
}


?>
  <input type='submit'>
</form>
<h2>Risultati </h2>


<?php

$stato = isset($_GET['stato']) ? $_GET['stato'] : '';
$orderNumber = isset($_GET['orderNumber']) ? $_GET['orderNumber'] : '';
$orderDate = isset($_GET['orderDate']) ? $_GET['orderDate'] : '';
$requiredDate = isset($_GET['requiredDate']) ? $_GET['requiredDate'] : '';
$shippedDate = isset($_GET['shippedDate']) ? $_GET['shippedDate'] : '';
$customerNumber = isset($_GET['customerNumber']) ? $_GET['customerNumber'] : '';


$sql = "INSERT INTO orders (status, orderNumber, orderDate, requiredDate, shippedDate, customerNumber) VALUES
('$stato', '$orderNumber', '$orderDate', '$requiredDate', '$shippedDate', '$customerNumber')";

$result = $conn->query($sql);
if($result == 1) {
    print("Ordine Inserito!");
    header('Refresh: 3; URL=orders.php');
} else {
    print("C'è stato un problema!");
}
?>
