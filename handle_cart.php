<?php
ob_start();
session_name('user_session');
session_start();
include("../eCommerce/admin/connect.php");
include("../eCommerce/admin/includes/functions/functions.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';
    $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : 0;

    $conn = connect_db();
    $stm = $conn->prepare("SELECT * FROM items WHERE item_id = ?");
    $stm->execute([$item_id]);
    $item = $stm->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $cart_item = [
            'id' => $item['item_id'],
            'name' => $item['item_name'],
            'price' => $item['item_price'],
            'quantity' => 1 // Default quantity to 1, can be adjusted as needed
        ];

        $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
        if ($action == "add") {

            if (isset($cart[$item_id])) {
                // Increment quantity if item is already in the cart
                $cart[$item_id]['quantity'] += 1;
            } else {
                // Add new item to the cart
                $cart[$item_id] = $cart_item;
            }

            // Update cookie with new cart data
            setcookie('cart', json_encode($cart), time() + (86400 * 30 * 12), "/"); // Cookie expires in 1 year

            $_SESSION['message'] = 'Item Added to Cart Successfully';
            $_SESSION['message_type'] = 'success';
        } elseif ($action == "remove") {
            // Remove item from cart
            unset($cart[$item_id]);
            setcookie('cart', json_encode($cart), time() + (86400 * 30 * 12), "/"); // Cookie expires in in 1 years
            header("location: cart.php");
            exit;
            # code...
        } elseif ($action = 'update') {
            // Update quantity of an item in the cart
            $quantity = $_POST['quantity'];
            if ($quantity > 0) {
                $cart[$item_id]['quantity'] = $quantity;
                setcookie('cart', json_encode($cart), time() + (86400 * 30 * 12), "/"); // Cookie expires in 1 year
                header("location: cart.php");
                exit;
            }else {
                header("location: cart.php");
                exit;
            }
        }
    } else {
        // Handle case when the item is not found
        $_SESSION['message'] = 'Item not found or you do not have permission to modify this item.';
        $_SESSION['message_type'] = 'error';
    }

    $_SESSION['redirect_item_id'] = $item_id;
    header("location: item_details.php");
    exit;
}

ob_end_flush();
