<?php
session_name('user_session');
session_start();
include("../eCommerce/admin/connect.php");
include("../eCommerce/admin/includes/functions/functions.php");


// Check if order_id is set
if (isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    // Connect to the database using PDO
    $pdo = connect_db();

    // Fetch order details
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch order items
    $stmt = $pdo->prepare("
    SELECT 
        od.item_id, 
        i.item_name, 
        od.quantity, 
        od.price 
    FROM 
        order_details od 
    INNER JOIN 
        items i 
    ON 
        od.item_id = i.item_id 
    WHERE 
        od.order_id = ?
");
    $stmt->execute([$order_id]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Close connection
    $pdo = null;

    // Prepare order details for JSON response
    $response = [
        'order' => $order,
        'items' => $order_items
    ];

    echo json_encode($response);
    exit;
}
