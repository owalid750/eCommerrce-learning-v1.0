<?php
session_name('user_session');
session_start();
include("../eCommerce/admin/connect.php");
include("../eCommerce/admin/includes/functions/functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    try {
        $pdo = connect_db();


        $stmt = $pdo->prepare("UPDATE orders SET status = 'Canceled' WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $order_id]);


        $_SESSION['order-message'] = 'Order has been cancelled successfully.';
    } catch (PDOException $e) {
        $_SESSION['order-message'] = 'Database error: ' . $e->getMessage();
    }

    header('Location: cart.php'); // Redirect to orders page
    exit;
} else {
    $_SESSION['order-message'] = 'Invalid request.';
    header('Location: cart.php'); // Redirect to orders page
    exit;
}
