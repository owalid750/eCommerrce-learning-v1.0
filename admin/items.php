<?php
ob_start();
session_name('admin_session'); // Use a distinct name for admin sessions
session_start();
$page_title = "Items";
if (isset($_SESSION['admin_user_name'])) {
    include("./init.php");
    $action = isset($_GET['action']) ? $_GET['action'] : 'manage';
    if ($action == "manage") {
        $conn = connect_db();
        $category_names = getItems($conn, "categories", null, null, null, 100);
        $users = getItems($conn, "users", null, null, null, 1000);
        $count_items = 0;
        if (isset($_POST['category_id'])) {
            $cat_id = $_POST['category_id'];
            $current_items = getItems($conn, "items", "item_id", "item_id", null, 1000, [
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
            ], $cat_id, "cat_id");
            $count_items = count($current_items);
        } else {
            $current_items = getItems($conn, "items", "item_id", null, null, 1000, [
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
            $count_items = count($current_items);
        }
?>

        <title><?php getTitle(); ?></title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f0f2f5;
                margin: 0;
                padding: 0;
            }

            .container {
                width: 90%;
                max-width: 1200px;
                margin: 20px auto;
                padding: 20px;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            header {
                text-align: center;
                margin-bottom: 20px;
            }

            h1 {
                color: #333;
            }

            .table-container {
                margin-bottom: 40px;
                overflow-x: auto;
            }

            .item-table {
                width: 100%;
                border-collapse: collapse;
            }

            .item-table th,
            .item-table td {
                padding: 12px;
                border: 1px solid #ddd;
                text-align: center;
                min-width: 100px;
            }

            .item-table th {
                background-color: #007BFF;
                color: #fff;
                font-weight: bold;
            }

            .item-table tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .btn {
                padding: 8px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
                color: #fff;
            }

            .edit-btn {
                background-color: #28a745;
                margin-right: 5px;
            }

            .delete-btn {
                background-color: #dc3545;
            }

            .item-form {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }

            .item-form h2 {
                text-align: center;
                color: #333;
            }

            .form-group {
                display: flex;
                flex-direction: column;
            }

            .form-group label {
                margin-bottom: 5px;
                font-weight: bold;
            }

            .form-group input,
            .form-group textarea,
            .form-group select {
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 4px;
                font-size: 16px;
                width: 100%;
            }

            .submit-btn {
                background-color: #007BFF;
                width: fit-content;
                align-self: center;
            }

            .message {
                padding: 10px;
                margin: 10px 0;
                border-radius: 4px;
            }

            .message.success {
                background-color: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }

            .message.error {
                background-color: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
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

            .comments-form {
                display: inline-block;
                position: relative;
            }

            .comments-link {
                text-decoration: none;
                color: #007bff;
                font-weight: bold;
                padding: 5px 10px;
                border: 1px solid #007bff;
                border-radius: 4px;
                transition: background-color 0.3s, color 0.3s;
                display: inline-flex;
                align-items: center;
                position: relative;
            }

            .comments-link:hover {
                background-color: #007bff;
                color: #fff;
            }

            .comments-link i {
                margin-right: 5px;
            }

            .tooltip {
                display: none;
                position: absolute;
                top: -35px;
                /* Adjusted position */
                left: 50%;
                transform: translateX(-50%);
                background-color: #333;
                color: #fff;
                padding: 5px 8px;
                border-radius: 4px;
                font-size: 12px;
                white-space: nowrap;
                z-index: 1000;
                opacity: 0;
                transition: opacity 0.3s;
            }

            .comments-link:hover .tooltip {
                display: block;
                opacity: 1;
            }

            .items-count-container {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
            }

            .items-count-icon {
                font-size: 24px;
                color: #007bff;
                margin-right: 10px;
            }

            .items-count-text {
                font-size: 18px;
                color: #333;
            }

            @media (max-width: 768px) {
                .item-form {
                    padding: 0 10px;
                }

                .item-table th,
                .item-table td {
                    font-size: 14px;
                }

                .form-group input,
                .form-group textarea,
                .form-group select {
                    font-size: 14px;
                }
            }
        </style>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

        <div class="container">
            <header>
                <h1>Item Management</h1>
            </header>

            <main>
                <div class="items-count-container">
                    <i class="fas fa-boxes items-count-icon"></i>
                    <span class="items-count-text">Number of items: <?php echo htmlspecialchars($count_items); ?></span>
                </div> <!-- Item List Table -->
                <div class="table-container">
                    <table class="item-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Date Added</th>
                                <th>Country Made</th>
                                <th>Status</th>
                                <th>Rating</th>
                                <th>Category</th>
                                <th>User</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Sample item row -->
                            <?php if (empty($current_items)) : ?>
                                <tr>
                                    <td colspan="10">NO Items Created Yet.</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($current_items as $item) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['item_id']) ?></td>
                                        <td><?php echo htmlspecialchars($item['item_name']) ?></td>
                                        <td><?php echo htmlspecialchars($item['item_desc']) ?></td>
                                        <td>$<?php echo htmlspecialchars($item['item_price']) ?></td>
                                        <td><?php echo htmlspecialchars($item['item_add_date']) ?></td>
                                        <td><?php echo htmlspecialchars($item['item_country_made']) ?></td>
                                        <td><?php echo htmlspecialchars($item['item_status']) ?></td>
                                        <td><?php echo htmlspecialchars($item['item_rating']) ?></td>
                                        <td><?php echo htmlspecialchars($item['cat_name']) ?></td>
                                        <td><?php echo htmlspecialchars($item['user_name']) ?></td>


                                        <td>
                                            <!-- Toggle Status Button -->
                                            <form action="items.php?action=toggle_status" method="post" style="display:inline;">

                                                <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
                                                <button type="submit" class="btn <?php echo $item['is_item_approved'] == 1 ? 'btn-inactive' : 'btn-active'; ?>">
                                                    <?php echo $item['is_item_approved'] == 1 ? 'DeApprove' : 'Approve'; ?>
                                                </button>
                                            </form>
                                            <!-- Edit button -->
                                            <button class="btn edit-btn" onclick="openModal('<?php echo htmlspecialchars($item['item_id']); ?>')"><i class="fas fa-edit"></i></button>
                                            <!-- Edit Modal -->
                                            <div id="editModal<?php echo htmlspecialchars($item['item_id']); ?>" class="modal">
                                                <div class="modal-content">
                                                    <span class="close" onclick="closeModal('<?php echo htmlspecialchars($item['item_id']); ?>')">&times;</span>
                                                    <form class="item-form" method="post" action="items.php?action=edit" id="editForm<?php echo htmlspecialchars($item['item_id']); ?>">
                                                        <h2>Edit Item</h2>
                                                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
                                                        <!-- item_name -->
                                                        <div class="form-group">
                                                            <label for="edit_item_name<?php echo htmlspecialchars($item['item_id']); ?>">Name:</label>
                                                            <input type="text" id="edit_item_name<?php echo htmlspecialchars($item['item_id']); ?>" name="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>" required>
                                                        </div>
                                                        <!-- item_desc -->
                                                        <div class="form-group">
                                                            <label for="edit_item_desc<?php echo htmlspecialchars($item['item_id']); ?>">Description:</label>
                                                            <textarea id="edit_item_desc<?php echo htmlspecialchars($item['item_id']); ?>" name="item_desc"><?php echo htmlspecialchars($item['item_desc']); ?></textarea>
                                                        </div>
                                                        <!-- item_price -->
                                                        <div class="form-group">
                                                            <label for="edit_item_price<?php echo htmlspecialchars($item['item_id']); ?>">Price in dollar:</label>
                                                            <input type="number" id="edit_item_price<?php echo htmlspecialchars($item['item_id']); ?>" name="item_price" step="0.01" value="<?php echo htmlspecialchars($item['item_price']); ?>" required>
                                                        </div>
                                                        <!-- item_add_date -->
                                                        <div class="form-group">
                                                            <label for="edit_item_add_date<?php echo htmlspecialchars($item['item_id']); ?>">Date Added:</label>
                                                            <input type="date" id="edit_item_add_date<?php echo htmlspecialchars($item['item_id']); ?>" name="item_add_date" value="<?php echo htmlspecialchars($item['item_add_date']); ?>" required>
                                                        </div>
                                                        <!-- item_country_made -->
                                                        <div class="form-group">
                                                            <label for="edit_item_country_made<?php echo htmlspecialchars($item['item_id']); ?>">Country Made:</label>
                                                            <input type="text" id="edit_item_country_made<?php echo htmlspecialchars($item['item_id']); ?>" name="item_country_made" value="<?php echo htmlspecialchars($item['item_country_made']); ?>" required>
                                                        </div>
                                                        <!-- item_status -->
                                                        <div class="form-group">
                                                            <label for="edit_item_status<?php echo htmlspecialchars($item['item_id']); ?>">Status:</label>
                                                            <select id="edit_item_status<?php echo htmlspecialchars($item['item_id']); ?>" name="item_status" required>
                                                                <option value="new" <?php echo ($item['item_status'] == 'new') ? 'selected' : ''; ?>>New</option>
                                                                <option value="used" <?php echo ($item['item_status'] == 'used') ? 'selected' : ''; ?>>Used</option>
                                                                <option value="refurbished" <?php echo ($item['item_status'] == 'refurbished') ? 'selected' : ''; ?>>Refurbished</option>
                                                            </select>
                                                        </div>
                                                        <!--item_rating-->
                                                        <div class="form-group">
                                                            <label for="edit_item_rating<?php echo htmlspecialchars($item['item_id']); ?>">Rating:</label>
                                                            <input type="number" id="edit_item_rating<?php echo htmlspecialchars($item['item_id']); ?>" name="item_rating" min="1" max="5" value="<?php echo htmlspecialchars($item['item_rating']); ?>" required>
                                                        </div>
                                                        <!-- item_cat -->
                                                        <div class="form-group">
                                                            <label for="edit_item_category<?php echo htmlspecialchars($item['item_id']); ?>">Category:</label>
                                                            <select id="edit_item_category<?php echo htmlspecialchars($item['item_id']); ?>" name="item_category" required>
                                                                <?php if (empty($category_names)) : ?>
                                                                    <option value="">No categories available</option>
                                                                <?php else : ?>
                                                                    <?php foreach ($category_names as $category) : ?>
                                                                        <option value="<?php echo htmlspecialchars($category['category_id']); ?>" <?php echo ($item['cat_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($category['cat_name']); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </select>
                                                        </div>
                                                        <!-- item_user -->
                                                        <div class="form-group">
                                                            <label for="edit_item_user<?php echo htmlspecialchars($item['item_id']); ?>">User:</label>
                                                            <select id="edit_item_user<?php echo htmlspecialchars($item['item_id']); ?>" name="item_user_id" required>
                                                                <?php if (empty($users)) : ?>
                                                                    <option value="">No Users available</option>
                                                                <?php else : ?>
                                                                    <?php foreach ($users as $user) : ?>
                                                                        <option value="<?php echo htmlspecialchars($user['user_id']); ?>" <?php echo ($item['user_id'] == $user['user_id']) ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($user['user_name']); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
                                                            </select>
                                                        </div>

                                                        <button type="submit" class="btn submit-btn">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <!-- Delete button -->
                                            <form id="deleteForm<?php echo htmlspecialchars($item['item_id']); ?>" action="items.php?action=delete" method="post" style="display:inline;">
                                                <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
                                                <button type="button" class="btn delete-btn" onclick="return confirmDelete(<?php echo htmlspecialchars($item['item_id']); ?>);"><i class="fas fa-trash"></i></button>
                                            </form>
                                            <!-- show comments -->
                                            <form action="comments.php?action=manage" method="post" class="comments-form">
                                                <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
                                                <a href="#" onclick="this.closest('form').submit(); return false;" class="comments-link">
                                                    <i class="fas fa-comments"></i>
                                                    <span class="tooltip">Show comments for this item</span>
                                                </a>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Add/Edit Item Form -->
                <div class="form-container">
                    <?php
                    if (isset($_SESSION['message'])) {
                        $message = $_SESSION['message'];
                        $message_type = $_SESSION['message_type'];

                        echo '<div class="message ' . htmlspecialchars($message_type) . '">';
                        echo htmlspecialchars($message);
                        echo '</div>';

                        // Clear message from session
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                    }
                    ?>
                    <form class="item-form" method="post" action="items.php?action=insert" id="insertForm">
                        <h2>Add/Edit Item</h2>
                        <div class="form-group">
                            <label for="item_name">Name:</label>
                            <input type="text" id="item_name" name="item_name" required>
                        </div>
                        <div class="form-group">
                            <label for="item_desc">Description:</label>
                            <textarea id="item_desc" name="item_desc"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="item_price">Price in dollar:</label>
                            <input type="number" id="item_price" name="item_price" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="item_add_date">Date Added:</label>
                            <input type="date" id="item_add_date" name="item_add_date" required>
                        </div>
                        <div class="form-group">
                            <label for="item_country_made">Country Made:</label>
                            <input type="text" id="item_country_made" name="item_country_made" required>
                        </div>
                        <div class="form-group">
                            <label for="item_status">Status:</label>
                            <select id="item_status" name="item_status" required>
                                <option value="new">New</option>
                                <option value="used">Used</option>
                                <option value="refurbished">Refurbished</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="item_rating">Rating:</label>
                            <input type="number" id="item_rating" name="item_rating" min="1" max="5" required>
                        </div>
                        <div class="form-group">
                            <label for="item_category">Category:</label>
                            <select id="item_category" name="item_category" required>
                                <?php if (empty($category_names)) : ?>
                                    <option value="">No categories available</option>
                                <?php else : ?>
                                    <?php foreach ($category_names as $category) : ?>
                                        <option value="<?php echo htmlspecialchars($category['category_id']) ?>">
                                            <?php echo htmlspecialchars($category['cat_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="item_user_id">User:</label>
                            <select id="item_user_id" name="item_user_id" required>
                                <?php if (empty($users)) : ?>
                                    <option value="">No users available</option>
                                <?php else : ?>
                                    <?php foreach ($users as $user) : ?>
                                        <option value="<?php echo htmlspecialchars($user['user_id']) ?>">
                                            <?php echo htmlspecialchars($user['user_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn submit-btn">Submit</button>
                    </form>

                </div>
            </main>
        </div>



<?php
    } elseif ($action == "insert") {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // Sanitize and retrieve form data
            $item_name = htmlspecialchars(trim($_POST['item_name']));
            $item_desc = htmlspecialchars(trim($_POST['item_desc']));
            $item_price = htmlspecialchars(trim($_POST['item_price']));
            $item_add_date = htmlspecialchars(trim($_POST['item_add_date']));
            $item_country_made = htmlspecialchars(trim($_POST['item_country_made']));
            $item_status = htmlspecialchars(trim($_POST['item_status']));
            $item_rating = htmlspecialchars(trim($_POST['item_rating']));
            $item_category = htmlspecialchars(trim($_POST['item_category'])); // Assuming this is the category ID
            $item_user_id = htmlspecialchars(trim($_POST['item_user_id'])); // Assuming this is the user ID

            // Validate form data (example validation)
            if (empty($item_name) || empty($item_price) || empty($item_category) || empty($item_user_id) || empty($item_add_date)) {
                $_SESSION['message'] = 'Please fill in all required fields.';
                $_SESSION['message_type'] = "error";
                header("Location: items.php?action=manage");
                exit;
            }
            try {
                // Prepare SQL query to insert data into the database
                $pdo = connect_db();
                $sql = "INSERT INTO items (item_name, item_desc, item_price, item_add_date, item_country_made, item_status, item_rating, cat_id, user_id) VALUES (:item_name, :item_desc, :item_price, :item_add_date, :item_country_made, :item_status, :item_rating, :cat_id, :user_id)";
                $stmt = $pdo->prepare($sql);

                // Bind parameters
                $stmt->bindParam(':item_name', $item_name);
                $stmt->bindParam(':item_desc', $item_desc);
                $stmt->bindParam(':item_price', $item_price);
                $stmt->bindParam(':item_add_date', $item_add_date);
                $stmt->bindParam(':item_country_made', $item_country_made);
                $stmt->bindParam(':item_status', $item_status);
                $stmt->bindParam(':item_rating', $item_rating);
                $stmt->bindParam(':cat_id', $item_category, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $item_user_id, PDO::PARAM_INT);


                // Execute the statement
                if ($stmt->execute()) {
                    $_SESSION['message'] = 'Item added successfully!';
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Failed to add item.';
                    $_SESSION['message_type'] = "error";
                }
                header("Location: items.php?action=manage");
                exit;
            } catch (PDOException $e) {
                $_SESSION['message'] = 'Failed to add item: ' . $e->getMessage();
                $_SESSION['message_type'] = "error";
                header("Location: items.php?action=manage");
                exit;
            }
        } else {
            header("location: items.php?action=manage");
            exit;
        }
    } elseif ($action == "edit") {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // Retrieve and sanitize inputs
            $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
            $item_name = htmlspecialchars(trim($_POST['item_name']));
            $item_desc = htmlspecialchars(trim($_POST['item_desc']));
            $item_price = htmlspecialchars(trim($_POST['item_price']));
            $item_add_date = htmlspecialchars(trim($_POST['item_add_date']));
            $item_country_made = htmlspecialchars(trim($_POST['item_country_made']));
            $item_status = htmlspecialchars(trim($_POST['item_status']));
            $item_rating = htmlspecialchars(trim($_POST['item_rating']));
            $item_category = isset($_POST['item_category']) ? intval($_POST['item_category']) : 0;
            $item_user_id = isset($_POST['item_user_id']) ? intval($_POST['item_user_id']) : 0;


            // Check if required fields are not empty
            if (empty($item_name) || empty($item_price) || empty($item_add_date) || empty($item_country_made) || empty($item_status) || empty($item_rating) || empty($item_category)) {
                $_SESSION['message'] = "Item name, price, date added, country made, status, rating, and category are required.";
                $_SESSION['message_type'] = "error";
                header("Location: items.php?action=manage");
                exit;
            }

            $pdo = connect_db();

            try {
                // Prepare and execute UPDATE statement
                $stmt = $pdo->prepare("
                    UPDATE items 
                    SET item_name = :item_name, 
                        item_desc = :item_desc, 
                        item_price = :item_price, 
                        item_add_date = :item_add_date, 
                        item_country_made = :item_country_made, 
                        item_status = :item_status, 
                        item_rating = :item_rating, 
                        cat_id = :item_category,
                        user_id= :user_id
                    WHERE item_id = :item_id
                ");
                $stmt->bindParam(':item_name', $item_name);
                $stmt->bindParam(':item_desc', $item_desc);
                $stmt->bindParam(':item_price', $item_price, PDO::PARAM_STR);
                $stmt->bindParam(':item_add_date', $item_add_date);
                $stmt->bindParam(':item_country_made', $item_country_made);
                $stmt->bindParam(':item_status', $item_status);
                $stmt->bindParam(':item_rating', $item_rating, PDO::PARAM_INT);
                $stmt->bindParam(':item_category', $item_category, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $item_user_id, PDO::PARAM_INT);
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);

                // Execute the query
                $stmt->execute();

                // Check if any rows were affected
                if ($stmt->rowCount() > 0) {
                    $_SESSION['message'] = "Item updated successfully.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "No changes were made or item not found.";
                    $_SESSION['message_type'] = "error";
                }
            } catch (PDOException $e) {
                // Set error message
                $_SESSION['message'] = "Error: " . $e->getMessage();
                $_SESSION['message_type'] = "error";
            }

            // Redirect after processing
            header("Location: items.php?action=manage");
            exit;
        } else {
            $_SESSION['message'] = "Invalid request method.";
            $_SESSION['message_type'] = "error";
            header("location: items.php?action=manage");
            exit;
        }
    } elseif ($action == "delete") {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // Get item ID
            $item_id = $_POST['item_id'] ?? null;
            if (empty($item_id)) {
                $_SESSION['message'] = "Item ID is missing.";
                $_SESSION['message_type'] = "error";
                header("location: items.php?action=manage");
                exit;
            }

            $conn = connect_db();

            // Prepare and execute the DELETE statement
            $stmt = $conn->prepare("DELETE FROM items WHERE item_id = ?");
            $stmt->execute([$item_id]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                $_SESSION['message'] = "Item with ID $item_id has been deleted.";
                $_SESSION['message_type'] = "success";
                header("location: items.php?action=manage");
                exit();
            } else {
                $_SESSION['message'] = "Item deletion failed or item with ID $item_id does not exist.";
                $_SESSION['message_type'] = "warning";
                header("location: items.php?action=manage");
                exit();
            }
        } else {
            // Invalid request method
            $_SESSION['message'] = "Invalid request method. Expected POST.";
            $_SESSION['message_type'] = "error";
            header("location: items.php?action=manage");
            exit;
        }
    } elseif ($action == "toggle_status") {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $item_id = $_POST['item_id'];

            $conn = connect_db();
            // Prepare and execute the query to get the current status
            $stmt = $conn->prepare("SELECT is_item_approved FROM items WHERE item_id = :item_id");
            $stmt->execute([':item_id' => $item_id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($item) {
                $new_status = $item['is_item_approved'] == 1 ? 0 : 1;

                // Update user status
                $stmt = $conn->prepare("UPDATE items SET is_item_approved = :new_status WHERE item_id = :item_id");
                $stmt->execute([
                    ':new_status' => $new_status,
                    ':item_id' => $item_id
                ]);

                // Set success message
                $_SESSION['message'] = $new_status == 1 ? 'Item Approved successfully.' : 'Item DeApproved successfully.';
                $_SESSION['message_type'] = 'success';
            } else {
                // Set error message if user not found
                $_SESSION['message'] = 'Item not found.';
                $_SESSION['message_type'] = 'error';
            }

            // Redirect to avoid resubmission
            header('Location: items.php?action=manage');
            exit();
        } else {
            $_SESSION['message'] = 'NOT Applicable';
            $_SESSION['message_type'] = 'error';
            header('Location: items.php');
            exit;
        }
    }
} else {
    header("location:index.php");
    exit;
}
ob_end_flush();
?>




<script>
    $(document).ready(function() {

        // Function to add asterisks to required fields
        function addAsterisks() {
            $('form#insertForm').find('[required]').each(function() {
                var label = $(this).prev('label');
                label.append(' <span style="color: red;">*</span>');
            });
        }

        // Call the function when the document is ready
        addAsterisks();
    });
</script>

<script>
    function openModal(id) {
        document.getElementById('editModal' + id).style.display = 'block';
    }

    function closeModal(id) {
        document.getElementById('editModal' + id).style.display = 'none';
    }

    function confirmDelete(item_id) {
        if (confirm("Are you sure you want to delete this Item?")) {
            // Proceed with form submission
            document.getElementById('deleteForm' + item_id).submit();
        } else {
            // Prevent form submission
            return false;
        }
    }

    // Close the modal when clicking outside of it
    /*    window.onclick = function(event) {
           if (event.target.className === 'modal') {
               closeModal(event.target.id.replace('editModal', ''));
           }
       } */
</script>