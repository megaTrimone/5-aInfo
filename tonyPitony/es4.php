<?php
include "database.php";

$query ="SELECT * FROM orders";
$result = $conne->query($query); #eseguoquery
print("numero di righe ritornate: " . $resut->num_rows . "<br>");
foreach($result as $row){
    $id = $row['orderNumber'];
    $status = $row['status'];
    print("$orderNumber : $status <br>");
}


?>