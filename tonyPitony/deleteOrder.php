<?php
include 'database.php';
$orderNumber = isset($_GET['id']) ? $_GET['id'] : '';
$sql = "DELETE FROM orders WHERE orderNumber=$orderNumber";
$result = $conn->query($sql);
if($result == 1) {
    print("Ordine cancellato!");
    header('Refresh: 10; URL=orders.php');
} else {
    print("C'è stato un problema!");
}
?>