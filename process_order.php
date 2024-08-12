<?php
session_name('user_session');
session_start();
include("../eCommerce/admin/connect.php");
include("../eCommerce/admin/includes/functions/functions.php");

// Get the raw POST data from the request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate and sanitize input
if (isset($data['shipping_address'], $data['billing_address'], $data['total_amount'], $data['payment_method'])) {
    $user_id = $_SESSION['user_id'];
    $shipping_address = htmlspecialchars(strip_tags($data['shipping_address']));
    $billing_address = htmlspecialchars(strip_tags($data['billing_address']));
    $total_amount = floatval($data['total_amount']);
    $payment_method = htmlspecialchars(strip_tags($data['payment_method']));
    $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];

    if ($total_amount <= 0) {
        // Invalid total amount
        echo json_encode(['status' => 'error', 'message' => 'Invalid total amount']);
        exit;
    }

    try {
        // Connect to the database using PDO
        $pdo = connect_db();
        $pdo->beginTransaction();

        // Insert into orders table
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, payment_method, shipping_address, billing_address, status, order_date) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$user_id, $total_amount, $payment_method, $shipping_address, $billing_address]);

        $order_id = $pdo->lastInsertId();

        // Insert into order_details table
        $stmt = $pdo->prepare("INSERT INTO order_details (order_id, item_id, quantity, price, name) VALUES (?, ?, ?, ?,?)");

        foreach ($cart as $item) {
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price'], $item['name']]);
        }

        $pdo->commit();

        // Clear cart
        setcookie('cart', '', time() - 3600, '/');

        // Success
        echo json_encode(['status' => 'success', 'message' => 'Order confirmed successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        // Handle database connection errors
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // Missing fields
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
}
