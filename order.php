<?php
require_once 'config/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'];
    $notes = $_POST['notes'];
    
    $result = createOrder($customer_name, $notes);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit();
}
?>