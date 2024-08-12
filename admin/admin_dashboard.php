<?php
session_name('admin_session'); // Use a distinct name for admin sessions
session_start();
if (isset($_SESSION['admin_user_name'])) {
    // echo "welcome " . $_SESSION['user_name'];
    $page_title = "Dashboard";

    include "./init.php";
    $conn = connect_db();
    //numbers members with admins
    $totalMembers = calcNumberOfItems($conn, "user_id", "users");
    $totalItems = calcNumberOfItems($conn, "item_id", "items");
    $totalComments = calcNumberOfItems($conn, "comment_id", "comments");


    $pendingMembers = calcNumberOfItems($conn, "user_id", "users", "reg_status", 0);
    $limit = 5;
    $count = 1;
    $count_request = 1;
    $count_item = 1;
    $latestMembers = getItems($conn, "users", "reg_date", "month", null, $limit);
    $latestRequests = getItems($conn, "users", "last_request_time", null, null, 200000, [], null, null, ["reg_status" => 0]);
    $num_of_latest_requests = count($latestRequests);

    $latestItems = getItems($conn, "items", "item_id", null, null, $limit, [
        [
            'table' => 'categories',
            'condition' => 'items.cat_id = categories.category_id',
            'attribute' => 'cat_name'
        ],
        [
            'table' => 'users',
            'condition' => 'items.user_id = users.user_id',
            'attribute' => 'user_name'
        ]
    ]);
    $orders = getItems($conn, "orders", "order_id", null, null, 10000000, [

        [
            'table' => "users",
            "condition" => "orders.user_id=users.user_id",
            "attribute" => "user_name"
        ],


    ], null, null,);
    // print_r($orders);
    // echo count($orders);
    // echo $_SESSION['order_message'];
?>
    <style>
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

        .order-message {
            color: red;
            font-weight: bold;
            text-align: center;

        }

        .content {
            padding: 20px;
            background-color: #f4f6f9;
            flex: 1;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            flex: 1;
            min-width: 200px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .card-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .card-description {
            font-size: 14px;
            color: #777;
        }

        .primary {
            background-color: #007bff;
            color: white;
        }

        .success {
            background-color: #28a745;
            color: white;
        }

        .warning {
            background-color: #ffc107;
            color: black;
        }

        .danger {
            background-color: #dc3545;
            color: white;
        }

        .panel-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .panel {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1;
            min-width: 300px;
        }

        .panel h3 {
            margin-top: 0;
        }

        .panel-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .panel-table th,
        .panel-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .panel-table th {
            background-color: #f8f9fa;
        }

        /* Active Button */
        .btn-active {
            background: linear-gradient(135deg, #28a745, #218838);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .btn-active:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
        }

        /* Inactive Button */
        .btn-inactive {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .btn-inactive:hover {
            background: linear-gradient(135deg, #e0a800, #d39e00);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);

        }

        .table-wrapper {
            overflow-x: auto;
            /* Allows horizontal scrolling for the table */
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
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title><?php
            getTitle();
            ?></title>
    <!-- Content -->
    <div class="content">



        <h2>Dashboard Overview</h2>
        <?php if (isset($_SESSION['order_message'])) : ?>
            <p class="order-message"><?php echo $_SESSION['order_message']; ?></p>
        <?php endif;
        unset($_SESSION['order_message']);
        ?>
        <!-- Summary Cards -->
        <div class="card-container">
            <div class="card primary">
                <div class="card-title">Total Members</div>
                <div class="card-value"><?php echo htmlspecialchars($totalMembers) ?></div>
                <div class="card-description">Total Members this month</div>
            </div>
            <div class="card warning">
                <div class="card-title">Pending Members</div>
                <div class="card-value"><?php echo htmlspecialchars($pendingMembers) ?></div>
                <div class="card-description">Pending Members this month</div>
            </div>
            <div class="card success">
                <div class="card-title">Total Items</div>
                <div class="card-value"><?php echo htmlspecialchars($totalItems) ?></div>
                <div class="card-description">Total Items this month</div>
            </div>
            <div class="card danger">
                <div class="card-title">Total Comments</div>
                <div class="card-value"><?php echo htmlspecialchars($totalComments) ?></div>
                <div class="card-description">Total Comments</div>
            </div>
        </div>

        <!-- Panels -->
        <div class="panel-container">
            <!-- Latest Registered Members Panel -->
            <div class="panel">
                <h3>
                    <button id="toggle-members" class="icon-button" title="Toggle Members">
                        <i class="fas fa-users"></i>
                    </button>
                    Latest <?php echo htmlspecialchars($limit) ?> Registered Members
                </h3>
                <div id="members-container">
                    <div class="table-wrapper">
                        <table id="members-table" class="panel-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Member ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Date Registered</th>
                                    <th>User Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($latestMembers)) : ?>
                                    <tr>
                                        <td colspan="6">No members found for the specified criteria.</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($latestMembers as $member) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($count); ?></td>
                                            <td><?php echo htmlspecialchars($member['user_id']); ?></td>
                                            <td><?php echo htmlspecialchars($member['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($member['email']); ?></td>
                                            <td><?php echo htmlspecialchars($member['reg_date']); ?></td>
                                            <td>
                                                <?php
                                                if (empty($_SESSION['csrf_token'])) {
                                                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                                }
                                                ?>
                                                <!-- Toggle Status Button -->
                                                <form action="members.php?action=toggle_status" method="post" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($member['user_id']); ?>">
                                                    <button type="submit" class="btn <?php echo $member['reg_status'] == 1 ? 'btn-inactive' : 'btn-active'; ?>">
                                                        <?php echo $member['reg_status'] == 1 ? 'Deactivate' : 'Activate'; ?>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php $count++;
                                    endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <!-- Latest Items Panel -->
            <div class="panel">
                <h3>
                    <button id="toggle-items" class="icon-button" title="Toggle Items">
                        <i class="fas fa-box"></i>
                    </button>
                    Latest <?php echo htmlspecialchars($limit) ?> Added Items
                </h3>
                <div id="items-container">
                    <div class="table-wrapper">
                        <table id="items-table" class="panel-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Date Added</th>
                                    <th>Item Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($latestItems)) : ?>
                                    <tr>
                                        <td colspan="6">No items found for the specified criteria.</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($latestItems as $item) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($count_item); ?></td>
                                            <td><?php echo htmlspecialchars($item['item_id']); ?></td>
                                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                            <td><?php echo htmlspecialchars($item['cat_name']); ?></td>
                                            <td><?php echo htmlspecialchars($item['item_add_date']); ?></td>
                                            <td>
                                                <!-- Toggle Status Button -->
                                                <form action="items.php?action=toggle_status" method="post" style="display:inline;">
                                                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
                                                    <button type="submit" class="btn <?php echo $item['is_item_approved'] == 1 ? 'btn-inactive' : 'btn-active'; ?>">
                                                        <?php echo $item['is_item_approved'] == 1 ? 'DeApprove' : 'Approve'; ?>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php $count_item++;
                                    endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <!-- Latest Requested Members to active Panel -->
        <div class="panel">
            <h3>
                <button id="toggle-requests" class="icon-button" title="toggle-requests">
                    <i class="fas fa-users"></i>
                </button>
                Latest <?php echo htmlspecialchars($num_of_latest_requests) ?> Requested Members to active
            </h3>
            <div id="request-container">
                <div class="table-wrapper">
                    <table id="members-table" class="panel-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Member ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Date Registered</th>
                                <th>Date Request</th>
                                <th>User Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($latestRequests)) : ?>
                                <tr>
                                    <td colspan="6">No members found for the specified criteria.</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($latestRequests as $request) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($count_request); ?></td>
                                        <td><?php echo htmlspecialchars($request['user_id']); ?></td>
                                        <td><?php echo htmlspecialchars($request['user_name']); ?></td>
                                        <td><?php echo htmlspecialchars($request['email']); ?></td>
                                        <td><?php echo htmlspecialchars($request['reg_date']); ?></td>
                                        <td><?php echo htmlspecialchars($request['last_request_time']); ?></td>

                                        <td>
                                            <?php
                                            if (empty($_SESSION['csrf_token'])) {
                                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                            }
                                            ?>
                                            <!-- Toggle Status Button -->
                                            <form action="members.php?action=toggle_status" method="post" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($request['user_id']); ?>">
                                                <button type="submit" class="btn <?php echo $request['reg_status'] == 1 ? 'btn-inactive' : 'btn-active'; ?>">
                                                    <?php echo $request['reg_status'] == 1 ? 'Deactivate' : 'Activate'; ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php $count_request++;
                                endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- Latest Orders  -->
        <div class="panel">
            <h3>
                <button id="toggle-orders" class="icon-button" title="toggle-orders">
                    <i class="fas fa-box"></i>
                </button>
                Latest Orders
            </h3>
            <div id="orders-container">
                <div class="table-wrapper">
                    <table id="members-table" class="panel-table">
                        <thead>
                            <tr>
                                <!-- <th>#</th> -->
                                <th>Order ID</th>
                                <th>User Name</th>

                                <th>Date </th>
                                <th>Total Amount </th>
                                <th>Shipping Address </th>
                                <th>Billing Address </th>
                                <th>Payment Method </th>
                                <th>Status </th>
                                <th>Action </th>


                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)) : ?>
                                <tr>
                                    <td colspan="8">No Orders found for the specified criteria.</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($orders as $order) : ?>
                                    <tr>

                                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>

                                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                        <td><?php echo htmlspecialchars($order['total_amount']); ?></td>
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
                                            <!-- show order details -->
                                            <form action="handle_order.php?action=show" method="post">
                                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                                                <button type="submit" class="btn btn-primary">Show</button>
                                            </form>

                                            <?php if ($order["status"] !== "Delivered" && $order["status"] !== "Canceled") : ?>
                                                <!-- change status -->
                                                <button type="button" onclick="openDialog('<?php echo htmlspecialchars($order['order_id']); ?>', '<?php echo htmlspecialchars($order['status']); ?>')">Change Status</button>
                                            <?php endif; ?>


                                            <!-- dialog -->
                                            <div id="dialog" class="modal">
                                                <form action="handle_order.php?action=update" method="post">
                                                    <input type="hidden" name="order_id" id="order_id">
                                                    <input type="hidden" name="status" id="status">
                                                    <div class="modal-content">
                                                        <h2>Change Status</h2>
                                                        <label for="new_status">New Status:</label>
                                                        <select id="new_status" name="new_status">
                                                            <option value="Pending">Pending</option>
                                                            <option value="Shipped">Shipped</option>
                                                            <option value="Delivered">Delivered</option>
                                                            <option value="Canceled">Canceled</option>
                                                        </select>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" onclick="closeDialog()">Cancel</button>
                                                        <button type="submit">Save</button>
                                                    </div>
                                                </form>
                                            </div>


                                        </td>

                                    </tr>
                                <?php
                                endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    </div>


<?php
} else {
    header("location: index.php");
    exit;
}


?>

<script>
    document.getElementById('toggle-members').addEventListener('click', function() {
        const membersContainer = document.getElementById('members-container');
        // Toggle the visibility of the members container
        if (membersContainer.style.display === 'none') {
            membersContainer.style.display = 'block';
        } else {
            membersContainer.style.display = 'none';
        }
    });

    document.getElementById('toggle-items').addEventListener('click', function() {
        const itemsContainer = document.getElementById('items-container');
        // Toggle the visibility of the items container
        if (itemsContainer.style.display === 'none') {
            itemsContainer.style.display = 'block';
        } else {
            itemsContainer.style.display = 'none';
        }
    });

    document.getElementById('toggle-requests').addEventListener('click', function() {
        const itemsContainer = document.getElementById('request-container');
        // Toggle the visibility of the items container
        if (itemsContainer.style.display === 'none') {
            itemsContainer.style.display = 'block';
        } else {
            itemsContainer.style.display = 'none';
        }
    });
    document.getElementById('toggle-orders').addEventListener('click', function() {
        const itemsContainer = document.getElementById('orders-container');
        // Toggle the visibility of the items container
        if (itemsContainer.style.display === 'none') {
            itemsContainer.style.display = 'block';
        } else {
            itemsContainer.style.display = 'none';
        }
    });
</script>
<script>
    function openDialog(orderId, currentStatus) {
        // Set the order ID and status in hidden inputs
        document.getElementById('order_id').value = orderId;
        document.getElementById('status').value = currentStatus;

        // Set the correct status in the dropdown
        var newStatusDropdown = document.getElementById('new_status');
        newStatusDropdown.value = currentStatus;

        // Open the dialog
        document.getElementById('dialog').style.display = 'block';
    }

    function closeDialog() {
        // Close the dialog
        document.getElementById('dialog').style.display = 'none';
    }
</script>

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