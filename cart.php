<?php
session_name('user_session');
session_start();
include("./init.php");
$cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
// print_r($cart);
// echo count(json_decode($_COOKIE['cart'], true));
$conn = connect_db();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$orders = getItems($conn, "orders", "order_id", null, null, 1000, [], null, null, ["user_id" => $user_id]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <style>
        /* General Styles
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        } */

        header {
            background-color: #f8f9fa;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        header h1 {
            margin: 0;
            text-align: center;
            color: #007bff;
        }

        main {
            padding: 20px 0;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .cart-table thead {
            background-color: #007bff;
            color: #fff;
        }

        .cart-table th,
        .cart-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .cart-table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .cart-table .total-label {
            font-weight: bold;
            font-size: large;
            text-align: center;
        }

        .cart-table .total-amount {
            font-weight: bold;
            color: #007bff;
        }

        .empty-cart {
            text-align: center;
            font-size: 1.2em;
            color: #888;
        }

        footer {
            background-color: #f8f9fa;
            padding: 10px 0;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #333;
        }

        footer p {
            margin: 0;
        }

        h1,
        h2 {
            text-align: center;
        }

        .order-status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
        }

        .order-status.pending {
            background-color: #ffc107;
            /* Yellow */
            color: #fff;
        }

        .order-status.shipped {
            background-color: #17a2b8;
            /* Blue */
            color: #fff;
        }

        .order-status.delivered {
            background-color: #28a745;
            /* Green */
            color: #fff;
        }

        /* Basic styles for the modal */
        #order-details-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 600px;
        }

        #close-modal {
            background: #f44336;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        .order-message {
            color: red;
            font-weight: bold;
            text-align: center;

        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <h1>Your Cart</h1>
        </div>
    </header>
    <main>
        <div class="container">
            <?php if (!empty($cart)) : ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $item) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td>$<?php echo htmlspecialchars($item['price']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?>

                                    <form action="handle_cart.php?action=update" method="post">
                                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                        <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>">
                                        <button type="submit" onclick="this.form.submit();this.form.reset();return false;" class="btn btn-primary" >Update</button>
                                      
                                    </form>
                                </td>



                                <td>$<?php echo htmlspecialchars($item['price'] * $item['quantity']); ?></td>
                                <td>
                                    <form action="handle_cart.php?action=remove" method="post">
                                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                        <button type="submit" class="btn btn-danger">Remove</button>
                                    </form>
                                </td>
                            </tr>


                        <?php endforeach; ?>
                        <tr>
                            <td colspan="4" class="total-label">Total</td>
                            <td class="total-amount">$
                                <?php
                                $total = 0;
                                foreach ($cart as $item) {
                                    $total += $item['price'] * $item['quantity'];
                                }
                                echo htmlspecialchars($total);
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php if (isset($_SESSION["user_id"])) : ?>
                    <form action="checkout.php" method="post">
                        <button type="submit" class="btn">Checkout</button>
                    </form>

                <?php else : ?>
                    <p>You need to <a href="login.php">login</a> first to checkout.</p>
                <?php endif; ?>
            <?php else : ?>
                <p class="empty-cart">Your cart is empty.</p>
            <?php endif; ?>

            <!-- Display order history -->
            <?php if (isset($_SESSION['order-message'])) : ?>
                <p class="order-message"><?php echo $_SESSION['order-message']; ?></p>
            <?php endif;
            unset($_SESSION['order-message']); ?>

            <h2>Your Order History</h2>
            <?php if (!empty($orders)) : ?>
                <table class="order-history cart-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Shipping Address</th>
                            <th>Billing Address</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                <td>$<?php echo htmlspecialchars($order['total_amount']); ?></td>

                                <td><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                                <td><?php echo htmlspecialchars($order['billing_address']); ?></td>
                                <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                <td>
                                    <span class="order-status <?php echo strtolower($order['status']); ?>">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Modal Structure -->
                                    <div id="order-details-modal" style="display: none;">
                                        <div class="modal-content">
                                            <h2>Order Details</h2>
                                            <div id="order-details">
                                                <!-- Order details will be dynamically loaded here -->
                                            </div>
                                            <button id="close-modal">Close</button>
                                        </div>
                                    </div>

                                    <form action="order_details.php" method="post">
                                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                                        <button type="submit" class="btn btn-primary">Show</button>
                                    </form>

                                    <br>
                                    <?php if ($order["status"] !== "Delivered" && $order['status']!=="Canceled") : ?>
                                        <form id="cancel-order-form" method="post" action="cancel_order.php">
                                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                                            <button type="submit" onclick="return confirm('Are you sure you want to cancel this Order?')" class="btn btn-danger">Cancel</button>
                                        </form>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>You have no previous orders.</p>
                <?php if (!isset($_SESSION["user_id"])) : ?>
                    <p>or you need to <a href="login.php">login</a> first.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    <script>
        // JavaScript to handle modal display
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.btn-primary').forEach(button => {
                button.addEventListener('click', async (event) => {
                    event.preventDefault(); // Prevent the form from submitting

                    const form = button.closest('form');
                    const formData = new FormData(form);

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();

                        // Populate the modal with order details
                        const orderDetails = document.getElementById('order-details');
                        let html = `<h3>Order ID: ${data.order.order_id}</h3>
                            <p>Status: ${data.order.status}</p>
                            <p>Total Amount: $${data.order.total_amount}</p>
                            <p>Shipping Address: ${data.order.shipping_address}</p>
                            <p>Billing Address: ${data.order.billing_address}</p>
                            <h4>Items:</h4>
                            <ul>`;
                        data.items.forEach(item => {
                            html += `<li> ID: ${item.item_id} || Name: ${item.item_name} 
                            || PRICE: $${item.price} || Quantity:  ${item.quantity}</li>`;
                        });
                        html += `</ul>`;
                        orderDetails.innerHTML = html;

                        // Show the modal
                        document.getElementById('order-details-modal').style.display = 'flex';
                    } catch (error) {
                        console.error('Error:', error);
                    }
                });
            });

            document.getElementById('close-modal').addEventListener('click', () => {
                document.getElementById('order-details-modal').style.display = 'none';
            });
        });
    </script>


</body>

</html>