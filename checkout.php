<?php
ob_start();
session_name('user_session');
session_start();
include("./init.php");
$cartItems = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .checkout-container {
            width: 100%;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .progress-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .progress-indicator .step {
            width: 32%;
            padding: 10px;
            background-color: #f4f4f4;
            text-align: center;
            border-radius: 4px;
            font-weight: bold;
        }

        .progress-indicator .step.active {
            background-color: #007bff;
            color: #fff;
        }

        .checkout-content {
            display: flex;
            flex-wrap: wrap;
        }

        .cart-summary,
        .checkout-form-container,
        .payment-section,
        .review-section {
            width: 100%;
            max-width: 48%;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 10px;
            background-color: #f9f9f9;
        }

        .cart-summary h2,
        .checkout-form-container h2,
        .payment-section h2,
        .review-section h2 {
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .total {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .submit-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .submit-btn:disabled {
            background-color: #ddd;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .checkout-content {
                flex-direction: column;
            }

            .cart-summary,
            .checkout-form-container,
            .payment-section {
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="checkout-container">
        <div class="progress-indicator" id="progress-indicator">
            <div class="step active" data-step="1">1. Shipping</div>
            <div class="step" data-step="2">2. Payment</div>
            <div class="step" data-step="3">3. Review</div>
        </div>
        <div class="checkout-content">
            <div class="cart-summary">
                <h2>Your Cart</h2>
                <!-- Sample cart items -->
                <?php foreach ($cartItems as $item) : ?>


                    <div class="cart-item">
                        <p>Item Name: <?php echo $item['name']; ?></p>
                        <p>Quantity: x<?php echo $item['quantity']; ?></p>
                        <p>Price: $<?php echo $item['price']; ?></p>
                    </div>
                <?php endforeach; ?>
                <div class="total">
                    <p>Total Price</p>
                    <span id="total-amount">
                        <?php
                        $total = 0;
                        foreach ($cartItems as $item) {
                            $total += $item['price'] * $item['quantity'];
                        }
                        echo "$" . htmlspecialchars($total);
                        ?>
                    </span>
                </div>

            </div>
            <div class="checkout-form-container" id="checkout-form-container">
                <h2>Checkout</h2>
                <form class="checkout-form" id="checkout-form">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Shipping Address</label>
                        <input type="text" id="shipping_address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Billing Address
                        </label>
                        <input type="text" id="billing_address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    <div class="form-group">
                        <label for="zip">Zip Code</label>
                        <input type="text" id="zip" name="zip" required>
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" required>
                    </div>
                    <!-- <div class="form-group">
                        <label for="total-amount">Total Amount</label>
                        <input type="number" id="total-amount" name="total_amount" step="1" required>

                    </div> -->

                    <button type="submit" class="submit-btn" disabled>Place Order</button>
                </form>
            </div>
            <div class="payment-section" id="payment-section" style="display:none;">
                <h2>Payment Details</h2>
                <form class="payment-form" id="payment-form">
                    <div class="form-group">
                        <label for="payment-method">Payment Method</label>
                        <select id="payment-method" name="payment-method" required>
                            <option value="credit-card">Credit Card</option>
                            <option value="paypal">PayPal</option>
                            <option value="bank-transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="card-number">Card Number</label>
                        <input type="text" id="card-number" name="card-number" required>
                    </div>
                    <div class="form-group">
                        <label for="expiry-date">Expiry Date</label>
                        <input type="text" id="expiry-date" name="expiry-date" required>
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" required>
                    </div>
                    <button type="submit" class="submit-btn">Pay Now</button>
                </form>
            </div>
            <div class="review-section" id="review-section" style="display:none;">
                <h2>Review Your Order</h2>
                <div class="review-details">
                    <p><strong>Name:</strong> <span id="review-name"></span></p>
                    <p><strong>Email:</strong> <span id="review-email"></span></p>
                    <p><strong>Shipping Address:</strong> <span id="review-shipping_address"></span></p>
                    <p><strong>Billing Address:</strong> <span id="review-billing_address"></span></p>
                    <p><strong>City:</strong> <span id="review-city"></span></p>
                    <p><strong>Zip Code:</strong> <span id="review-zip"></span></p>
                    <p><strong>Country:</strong> <span id="review-country"></span></p>
                    <p><strong>Payment Method:</strong> <span id="review-payment-method"></span></p>
                    <p><strong>Card Number:</strong> <span id="review-card-number"></span></p>
                    <button type="button" class="submit-btn" id="confirm-order">Confirm Order</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        const form = document.getElementById('checkout-form');
        const paymentForm = document.getElementById('payment-form');
        const submitBtn = document.querySelector('.submit-btn');
        const checkoutFormContainer = document.getElementById('checkout-form-container');
        const paymentSection = document.getElementById('payment-section');
        const reviewSection = document.getElementById('review-section');
        const progressIndicator = document.getElementById('progress-indicator');
        const steps = progressIndicator.querySelectorAll('.step');

        let shippingCompleted = false;
        let paymentCompleted = false;

        form.addEventListener('input', () => {
            const allFilled = [...form.querySelectorAll('input')].every(input => input.value.trim() !== '');
            submitBtn.disabled = !allFilled;
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            // Hide the checkout form and show the payment section
            checkoutFormContainer.style.display = 'none';
            paymentSection.style.display = 'block';
            // Update the progress indicator
            progressIndicator.querySelector('.step.active').classList.remove('active');
            progressIndicator.querySelector('.step:nth-child(2)').classList.add('active');
            shippingCompleted = true;
        });

        paymentForm.addEventListener('submit', (event) => {
            event.preventDefault();
            // Hide the payment section and show the review section
            paymentSection.style.display = 'none';
            reviewSection.style.display = 'block';
            // Update the progress indicator
            progressIndicator.querySelector('.step.active').classList.remove('active');
            progressIndicator.querySelector('.step:nth-child(3)').classList.add('active');
            paymentCompleted = true;

            // Fill the review section with the provided information
            document.getElementById('review-name').textContent = document.getElementById('name').value;
            document.getElementById('review-email').textContent = document.getElementById('email').value;
            document.getElementById('review-shipping_address').textContent = document.getElementById('shipping_address').value;
            document.getElementById('review-billing_address').textContent = document.getElementById('billing_address').value;
            document.getElementById('review-city').textContent = document.getElementById('city').value;
            document.getElementById('review-zip').textContent = document.getElementById('zip').value;
            document.getElementById('review-country').textContent = document.getElementById('country').value;
            document.getElementById('review-payment-method').textContent = document.getElementById('payment-method').value;
            document.getElementById('review-card-number').textContent = document.getElementById('card-number').value;
        });

        steps.forEach(step => {
            step.addEventListener('click', () => {
                const stepNumber = parseInt(step.dataset.step);
                if ((stepNumber === 1) ||
                    (stepNumber === 2 && shippingCompleted) ||
                    (stepNumber === 3 && shippingCompleted && paymentCompleted)) {
                    // Hide all sections
                    checkoutFormContainer.style.display = 'none';
                    paymentSection.style.display = 'none';
                    reviewSection.style.display = 'none';

                    // Show the relevant section
                    if (stepNumber === 1) {
                        checkoutFormContainer.style.display = 'block';
                    } else if (stepNumber === 2) {
                        paymentSection.style.display = 'block';
                    } else if (stepNumber === 3) {
                        reviewSection.style.display = 'block';
                    }

                    // Update the progress indicator
                    progressIndicator.querySelector('.step.active').classList.remove('active');
                    step.classList.add('active');
                }
            });
        });

        document.getElementById('confirm-order').addEventListener('click', async () => {
            // Collect order data
            const shippingAddress = document.getElementById('shipping_address').value;
            const billingAddress = document.getElementById('billing_address').value;
            const totalAmountText = document.getElementById('total-amount').textContent;
            const totalAmount = parseFloat(totalAmountText.replace('$', ''));
            console.log(totalAmount);
            const paymentMethod = document.getElementById('payment-method').value;

            // Validate data
            if (!shippingAddress || !billingAddress || isNaN(totalAmount) || totalAmount <= 0 || !paymentMethod) {
                alert('Please fill out all fields correctly.');
                return;
            }

            // Prepare data for sending
            const orderData = {
                shipping_address: shippingAddress,
                billing_address: billingAddress,
                total_amount: totalAmount,
                payment_method: paymentMethod
            };

            try {
                // Send data to server
                const response = await fetch('process_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });

                // Check if response is okay
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                // Get response data
                const result = await response.json();

                // Provide feedback based on response
                if (result.status === 'success') {
                    alert('Order confirmed successfully!');
                    // Redirect to cart page after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'cart.php';
                    }, 2000); // Adjust time as needed
                } else {
                    alert('Order confirmation failed: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while processing your order.');
            }
        });
    </script>
</body>

</html>

<?php ob_end_flush(); ?>