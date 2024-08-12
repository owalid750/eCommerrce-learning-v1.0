<?php
session_name('admin_session');
session_start();
include("./connect.php");
include("./includes/functions/functions.php");
$action = isset($_GET['action']) ? $_GET['action'] : "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if ($action == "show") {
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;

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
    } elseif ($action == "update") {

        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;
        $status = isset($_POST['new_status']) ? $_POST['new_status'] : '';
        $conn = connect_db();
        $stmt = $conn->prepare("UPDATE orders SET status = :ostatus WHERE order_id = :order_id");
        $stmt->execute([':ostatus' => $status, ':order_id' => $order_id]);
        if ($stmt) {
            $_SESSION['order_message'] = "Order status updated successfully";
        } else {
            $_SESSION['order_message'] = "Error updating order status";
        }

        header("Location: admin_dashboard.php");
        exit;
    }
}
