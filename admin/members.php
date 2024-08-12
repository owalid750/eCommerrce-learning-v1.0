<?php

ob_start(); // Start output buffering
session_name('admin_session'); // Use a distinct name for admin sessions
session_start();


if (isset($_SESSION['admin_user_name'])) {
    include("./init.php");
    $action = isset($_GET['action']) ? $_GET['action'] : "manage";

    if ($action == "manage") { ?>
        <style>
            /* Card Styling */
            .card {
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                transition: background-color 0.3s, box-shadow 0.3s;
                cursor: pointer;
                text-align: center;
                background-color: #ffffff;
                color: #333;
            }

            .card-title {
                font-size: 1.5em;
                font-weight: bold;
                margin-bottom: 10px;
            }

            .card-value {
                font-size: 2em;
                font-weight: bold;
                margin-bottom: 10px;
            }

            .card-description {
                font-size: 1em;
                color: #666;
            }

            .card:hover {
                background-color: #f8f9fa;
                /* Light grey */
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            }

            .card.primary {
                background-color: #007bff;
                color: #ffffff;
            }

            .card.primary:hover {
                background-color: #0056b3;
            }

            .card.secondary {
                background-color: #6c757d;
                color: #ffffff;
            }

            .card.secondary:hover {
                background-color: #5a6268;
            }

            /* Message Container */
            .message-container {
                margin: 20px auto;
                padding: 20px;
                width: 90%;
                max-width: 600px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                font-family: Arial, sans-serif;
                text-align: center;
                position: relative;
            }

            .message-container h2 {
                margin: 0;
                font-size: 1.4em;
                font-weight: bold;
            }

            .message-container.success {
                background-color: #d4edda;
                /* Light green */
                color: #155724;
                /* Dark green */
                border: 1px solid #c3e6cb;
            }

            .message-container.error {
                background-color: #f8d7da;
                /* Light red */
                color: #721c24;
                /* Dark red */
                border: 1px solid #f5c6cb;
            }

            .message-container.info {
                background-color: #d1ecf1;
                /* Light blue */
                color: #0c5460;
                /* Dark blue */
                border: 1px solid #bee5eb;
            }

            .message-container.warning {
                background-color: #fff3cd;
                /* Light yellow */
                color: #856404;
                /* Dark yellow */
                border: 1px solid #ffeeba;
            }

            .message-container::before {
                content: '';
                position: absolute;
                top: 10px;
                left: 15px;
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background-color: currentColor;
            }

            .message-container.success::before {
                background-color: #28a745;
                /* Green for success */
            }

            .message-container.error::before {
                background-color: #dc3545;
                /* Red for error */
            }

            .message-container.info::before {
                background-color: #17a2b8;
                /* Blue for info */
            }

            .message-container.warning::before {
                background-color: #ffc107;
                /* Yellow for warning */
            }


            /* Row color styles */


            /* Existing styles */
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0, 0, 0, 0.4);
            }

            .modal-content {
                background-color: #fefefe;
                margin: 15% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
                max-width: 600px;
            }

            .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }

            .close:hover,
            .close:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }

            body {
                background-color: #f8f9fa;
                font-family: Arial, sans-serif;
            }

            .container {
                max-width: 900px;
                margin: 50px auto;
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                padding: 30px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 12px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
                font-weight: bold;
            }

            tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            tr:hover {
                background-color: #f1f1f1;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                font-weight: bold;
            }

            .form-control {
                width: 100%;
                padding: 10px;
                font-size: 16px;
                border: 1px solid #ccc;
                border-radius: 4px;
                transition: border-color 0.2s;
            }

            .form-control:focus {
                outline: none;
                border-color: #007bff;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            }

            /* General Button Styling */
            .btn {
                display: inline-block;
                padding: 10px 20px;
                font-size: 16px;
                font-weight: bold;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                text-align: center;
                text-decoration: none;
                transition: all 0.3s ease;
                color: #fff;
                /* Default text color */
                text-transform: uppercase;
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

            /* Danger Button */
            .btn-danger {
                background: linear-gradient(135deg, #dc3545, #c82333);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            }

            .btn-danger:hover {
                background: linear-gradient(135deg, #c82333, #a71d2a);
                box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
            }

            /* Primary Button */
            .btn-primary {
                background: linear-gradient(135deg, #007bff, #0056b3);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            }

            .btn-primary:hover {
                background: linear-gradient(135deg, #0056b3, #003d7a);
                box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
            }

            /* Disabled Button */
            .btn:disabled {
                background: #ccc;
                color: #666;
                cursor: not-allowed;
                box-shadow: none;
                opacity: 0.7;
            }

            .modal {
                display: none;
                position: fixed;
                z-index: 1;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0, 0, 0, 0.4);
            }

            .modal-content {
                background-color: #fefefe;
                margin: 15% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
                max-width: 500px;
            }

            .modal .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }

            .modal .close:hover,
            .modal .close:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }

            @media (max-width: 768px) {
                .container {
                    padding: 15px;
                }

                table {
                    font-size: 14px;
                }

                th,
                td {
                    padding: 8px;
                }
            }
        </style>
        <?php $page_title = "Members" ?>
        <title><?php
                getTitle();
                ?></title>

        <!-- Get all users -->
        <?php $conn = connect_db();
        // Initialize filter variables with default empty values
        $usernameFilter = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : '';
        $emailFilter = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
        $statusFilter = isset($_POST['status']) ? htmlspecialchars(trim($_POST['status'])) : ''; // For active/inactive filter
        $startDateFilter = isset($_POST['start_date']) ? htmlspecialchars(trim($_POST['start_date'])) : '';


        // Prepare SQL query with filters
        $sql = "SELECT user_id, user_name, email, full_name, group_id, reg_date, reg_status 
        FROM users 
        WHERE group_id = 0";

        if ($usernameFilter) {
            $sql .= " AND user_name = :username";
        }

        if ($emailFilter) {
            $sql .= " AND email = :email";
        }

        if ($statusFilter !== '') {
            $sql .= " AND reg_status = :status";
        }
        if ($startDateFilter) {
            // Format the date correctly for filtering
            $startDateFormatted = date('Y-m-d', strtotime($startDateFilter));
            // Filter rows where the date part of reg_date is exactly the same as start_date
            $sql .= " AND DATE(reg_date) = :start_date";
        }

        $sql .= " ORDER BY reg_date DESC";

        $stmt = $conn->prepare($sql);

        // Bind parameters if necessary
        if ($usernameFilter) {
            $stmt->bindValue(':username',   $usernameFilter );
        }

        if ($emailFilter) {
            $stmt->bindValue(':email',   $emailFilter );
        }

        if ($statusFilter !== '') {
            $stmt->bindValue(':status', $statusFilter);
        }
        if ($startDateFilter) {
            $stmt->bindValue(':start_date', $startDateFormatted);
        }
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="container">
            <h2 class="form-heading">Add New Member</h2>
            <form method="post" action="?action=insert" id="addForm">
                <?php if (isset($_SESSION['message'])) : ?>
                    <div class="message-container <?php echo htmlspecialchars($_SESSION['message_type']); ?>">
                        <h2><?php echo htmlspecialchars($_SESSION['message']); ?></h2>
                    </div>
                <?php
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                endif; ?>
                <div class="form-group">
                    <label for="username">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" placeholder="Enter username" name="user_name" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label for="password">Password <span class="text-danger">*</span></label>
                    <div>
                        <input type="password" class="form-control" id="password" placeholder="Enter password" name="password" autocomplete="new-password" required>

                        <input type="checkbox" id="togglePassword" onclick="togglePasswordVisibility()">

                    </div>
                </div>


                <div class="form-group">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="fullname">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="fullname" placeholder="Enter full name" name="full_name" required>
                </div>
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary mr-2">Add</button>
                    <a href="#" class="btn btn-secondary">Cancel</a>
                </div>
            </form>

            <h2 class="form-heading">Current Members</h2>

            <!-- Filter Form -->
            <form method="post" action="members.php?action=manage" class="form-inline mb-4">
                <div class="form-group mr-2">
                    <label for="filterUsername" class="mr-2">Username:</label>
                    <input type="text" id="filterUsername" name="username" class="form-control" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
                <div class="form-group mr-2">
                    <label for="filterEmail" class="mr-2">Email:</label>
                    <input type="text" id="filterEmail" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="form-group mr-2">
                    <label for="filterStatus" class="mr-2">Status:</label>
                    <select id="filterStatus" name="status" class="form-control">
                        <option value="">All</option>
                        <option value="1" <?php echo (isset($_POST['status']) && $_POST['status'] === '1') ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo (isset($_POST['status']) && $_POST['status'] === '0') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="form-group mr-2">
                    <label for="filterStartDate" class="mr-2">Date:</label>
                    <input type="date" id="filterStartDate" name="start_date" class="form-control" value="<?php echo htmlspecialchars($_POST['start_date'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
            <div>
                <div class="table-responsive">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Full Name</th>
                                <th>Control</th>
                                <th>Registration Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)) : ?>
                                <tr>
                                    <td colspan="7">No users found.</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($users as $user) : ?>
                                    <tr class="<?php echo $user['reg_status'] == 1 ? 'row-active' : 'row-inactive'; ?>">
                                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                        <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['group_id'] == 1 ? "admin" : "user"); ?></td>
                                        <td><?php echo htmlspecialchars($user['reg_date']); ?></td>

                                        <td>
                                            <?php
                                            if (empty($_SESSION['csrf_token'])) {
                                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                            }
                                            ?>
                                            <form id="deleteForm<?php echo htmlspecialchars($user['user_id']); ?>" action="members.php?action=delete" method="post" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                                <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo htmlspecialchars($user['user_id']); ?>)">Delete</button>
                                            </form>

                                            <!-- Edit button -->
                                            <button type="button" class="btn btn-primary mr-2" onclick="openModal('<?php echo htmlspecialchars($user['user_id']); ?>')">Edit</button>

                                            <!-- Edit Modal -->
                                            <div id="editModal<?php echo htmlspecialchars($user['user_id']); ?>" class="modal">
                                                <div class="modal-content">
                                                    <span class="close" onclick="closeModal('<?php echo htmlspecialchars($user['user_id']); ?>')">&times;</span>
                                                    <form action="members.php?action=edit_in_current_users" method="post">
                                                        <h2>Edit User Details</h2>

                                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                                        <!-- Username -->
                                                        <div class="form-group">
                                                            <label for="editUsername<?php echo htmlspecialchars($user['user_id']); ?>">Username</label>
                                                            <input type="text" class="form-control" id="editUsername<?php echo htmlspecialchars($user['user_id']); ?>" name="username" value="<?php echo htmlspecialchars($user['user_name']); ?>" required>
                                                        </div>
                                                        <!-- Email -->
                                                        <div class="form-group">
                                                            <label for="editEmail<?php echo htmlspecialchars($user['user_id']); ?>">Email address</label>
                                                            <input type="email" class="form-control" id="editEmail<?php echo htmlspecialchars($user['user_id']); ?>" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                                        </div>
                                                        <!-- Full Name -->
                                                        <div class="form-group">
                                                            <label for="editFullName<?php echo htmlspecialchars($user['user_id']); ?>">Full Name</label>
                                                            <input type="text" class="form-control" id="editFullName<?php echo htmlspecialchars($user['user_id']); ?>" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                                        </div>
                                                        <!-- Submit Button -->
                                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Toggle Status Button -->
                                            <form action="members.php?action=toggle_status" method="post" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                                                <button type="submit" class="btn <?php echo $user['reg_status'] == 1 ? 'btn-inactive' : 'btn-active'; ?>">
                                                    <?php echo $user['reg_status'] == 1 ? 'Deactivate' : 'Activate'; ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>


            </div>


        </div>
        <?php
    } elseif ($action == "edit_in_current_users") {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // Validate and sanitize incoming data
            $user_id = $_POST['user_id'];
            $username = htmlspecialchars(trim($_POST['username']));
            $email = htmlspecialchars(trim($_POST['email']));
            $full_name = htmlspecialchars(trim($_POST['full_name']));

            $update_form_errors = [];

            // Validate inputs...
            if (empty($username)) {
                $update_form_errors[] = "Update : Username is required.";
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $update_form_errors[] = "Update : A valid email is required.";
            }
            if (empty($full_name)) {
                $update_form_errors[] = "Update : Full name is required.";
            }
            // Check for validation update_form_errors$update_form_errors
            if (count($update_form_errors) > 0) {
                $_SESSION['message'] = implode("\n", $update_form_errors);
                $_SESSION['message_type'] = "danger";
                header("Location: members.php?action=manage");
                exit;
            }
            $conn = connect_db();
            $checkResult = checkExistingUserEmail($conn, $username, $email, $user_id);

            // Handle the result from the function
            if ($checkResult['error']) {
                // Handle database error
                $_SESSION['message'] = "Database Error: " . $checkResult['message'];
                $_SESSION['message_type'] = "danger";
            } else {
                // Check username and email existence
                if ($checkResult['username_exists'] && $checkResult['existing_username'] !== $username) {
                    $_SESSION['message'] = "Username '$username' already exists.";
                    $_SESSION['message_type'] = "danger";
                    header("Location: members.php?action=manage");
                    exit;
                }
                if ($checkResult['email_exists'] && $checkResult['existing_email'] !== $email) {
                    $_SESSION['message'] = "Email '$email' already exists.";
                    $_SESSION['message_type'] = "danger";
                    header("Location: members.php?action=manage");
                    exit;
                }
            }
            // Prepare the update query based on user input
            $query = "UPDATE users SET full_name=?, user_name=?, email=? WHERE user_id=?";
            $params = [$full_name, $username, $email, $user_id];

            // Execute the update query
            $stmt = $conn->prepare($query);
            $stmt->execute($params);

            // Set success or error message
            if ($stmt->rowCount() > 0) {
                $_SESSION['message'] = "User $username updated successfully.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Update: No changes were made.";
                $_SESSION['message_type'] = "warning";
            }

            header("Location: members.php?action=manage");
            exit;
        } else {
            // Handle invalid request method (optional)
            // Redirect or display an error message
            echo "Invalid request method.";
        }
    } elseif ($action == "insert") {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Sanitize and validate inputs
            $username = htmlspecialchars(trim($_POST['user_name']), ENT_QUOTES, 'UTF-8');
            $password = trim($_POST['password']);
            $email = trim($_POST['email']);
            $full_name = htmlspecialchars(trim($_POST['full_name']), ENT_QUOTES, 'UTF-8');
            $errors = [];

            // Validate inputs...
            if (empty($username)) {
                $errors[] = "Username is required.";
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "A valid email is required.";
            }
            if (empty($full_name)) {
                $errors[] = "Full name is required.";
            }
            // Example: Additional validation like password length
            if (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters.";
            }

            // Check for validation errors
            if (count($errors) > 0) {
                $_SESSION['message'] = implode("\n", $errors);
                $_SESSION['message_type'] = "danger";
                header("Location: members.php?action=manage");
                exit;
            }

            // Hash the password
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            // Connect to the database
            $conn = connect_db();

            try {
                // Check if username or email already exists
                $stmt = $conn->prepare('SELECT COUNT(*) AS count_username FROM users WHERE user_name = :username');
                $stmt->execute(['username' => $username]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row['count_username'] > 0) {
                    // Username already exists
                    $_SESSION['message'] = "Username '$username' already exists.";
                    $_SESSION['message_type'] = "danger";
                    header("Location: members.php?action=manage");
                    exit;
                }

                // Check if email already exists
                $stmt = $conn->prepare('SELECT COUNT(*) AS count_email FROM users WHERE email = :email');
                $stmt->execute(['email' => $email]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row['count_email'] > 0) {
                    // Email already exists
                    $_SESSION['message'] = "Email '$email' already exists.";
                    $_SESSION['message_type'] = "danger";
                    header("Location: members.php?action=manage");
                    exit;
                }
                // Prepare SQL statement
                $stmt = $conn->prepare('INSERT INTO users (user_name, password_hash, email, full_name,reg_status) VALUES (:username, :password_hash, :email, :full_name,1)');

                // Execute the statement with sanitized inputs
                $stmt->execute([
                    'username' => $username,
                    'password_hash' => $password_hash,
                    'email' => $email,
                    'full_name' => $full_name
                ]);

                // Set success or error message
                if ($stmt->rowCount() > 0) {
                    $_SESSION['message'] = "User inserted successfully.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "No changes were made.";
                    $_SESSION['message_type'] = "warning";
                }
                header("Location: members.php?action=manage");
                exit;
            } catch (PDOException $e) {
                // Handle database errors
                $_SESSION['message'] = "Database error: " . $e->getMessage();
                $_SESSION['message_type'] = "danger";
            }
        } else {
            // Handle other request methods (e.g., GET)
            // header("HTTP/1.1 405 Method Not Allowed");
            // echo "Method Not Allowed";
            // exit;
            homeRedirectfun("Method Not Allowed. You will be redirected shortly.", 5);
        }
    } elseif ($action == "edit") {
        
        $user_id = (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) ? intval($_GET['user_id']) : 0;
        $page_title = "Edit Profile";

        // Connect to the database
        $conn = connect_db();
        // Prepare and execute query securely
        $stmt = $conn->prepare('SELECT * FROM users WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch();
        if ($user) {
        ?>

            <?php $page_title = "Edit Profile" ?>
            <title><?php
                    getTitle();
                    ?></title>

            <style>
                body {
                    background-color: #f8f9fa;
                }

                .card {
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    border: none;
                }

                .card-header {
                    background-color: #2c3e50;
                }

                .form-control:focus {
                    box-shadow: none;
                    border-color: #007bff;
                }

                .btn-primary {
                    background-color: #007bff;
                    border-color: #007bff;
                }

                .btn-primary:hover {
                    background-color: #0056b3;
                    border-color: #0056b3;
                }

                /* Add this CSS to your stylesheet */
                .message-container {
                    margin: 20px auto;
                    padding: 15px;
                    width: 80%;
                    border-radius: 5px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    font-family: Arial, sans-serif;
                    text-align: center;
                }

                .message-container h2 {
                    margin: 0;
                    font-size: 1.5em;
                }

                .message-container.success {
                    background-color: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }

                .message-container.error {
                    background-color: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }

                .message-container.warning {
                    background-color: #fff3cd;
                    color: #856404;
                    border: 1px solid #ffeeba;
                }

                .message-container.info {
                    background-color: #d1ecf1;
                    color: #0c5460;
                    border: 1px solid #bee5eb;
                }
            </style>



            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h3 class="mb-0 text-center">Edit Profile</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                if (empty($_SESSION['csrf_token'])) {
                                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                }
                                if (isset($_SESSION['message'])) : ?>
                                    <div class="message-container <?php echo htmlspecialchars($_SESSION['message_type']); ?>">
                                        <h2><?php echo htmlspecialchars($_SESSION['message']); ?></h2>
                                    </div>
                                <?php
                                    unset($_SESSION['message']);
                                    unset($_SESSION['message_type']);
                                endif; ?>

                                <form method="post" action="?action=update" id="editForm">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" class="form-control" id="username" placeholder="Enter username" value="<?php echo $user["user_name"] ?>" name="user_name" autocomplete="off" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" id="password" placeholder="Leave blank if you Don't want to change password" name="password" autocomplete="new-password">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" placeholder="Enter email" value="<?php echo $user["email"] ?>" name="email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="fullname">Full Name</label>
                                        <input type="text" class="form-control" id="fullname" placeholder="Enter full name" value="<?php echo $user["full_name"] ?>" name="full_name" required>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                        <a href="#" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



<?php
        } else {
            // echo "This ID was not found in the database.";
            homeRedirectfun("This ID was not found in the database.. You will be redirected shortly.", .5);
        }
    } elseif ($action == "delete") {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('TRY HARDER NEXT TIME');
            }
            $user_id = $_POST['user_id'];
            if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
                $user_id = $_POST['user_id'];

                // Verify if the user is logged in and matches the user_id to be deleted
                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
                    $_SESSION['message'] = "You cannot delete yourself.";
                    $_SESSION['message_type'] = "error";
                    header("location: members.php?action=manage");
                    exit();
                }
            }
            // Assuming connect_db() function returns a PDO connection
            $conn = connect_db();

            // Prepare and execute the DELETE statement
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                $_SESSION['message'] = "User with ID $user_id has been deleted.";
                $_SESSION['message_type'] = "success";
                header("location: members.php?action=manage");
                exit();
            } else {
                $_SESSION['message'] = "User deletion failed or user with ID $user_id does not exist.";
                $_SESSION['message_type'] = "warning";
                header("location: members.php?action=manage");
                exit();
            }
        } else {
            // echo "Invalid request method. Expected POST.";
            homeRedirectfun("Invalid request method. Expected POST.. You will be redirected shortly.", 5);
        }
    } elseif ($action == "update") {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                // die('CSRF token validation failed');
                die('TRY HARDER NEXT TIME :)');
            }
            // Process form data and update the database
            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
            $username = htmlspecialchars(strip_tags(preg_replace('/\s+/', '', trim($_POST['user_name']))), ENT_QUOTES, 'UTF-8');
            $password = trim($_POST['password']);
            $email = trim($_POST['email']);
            $full_name = htmlspecialchars(strip_tags(trim($_POST['full_name'])), ENT_QUOTES, 'UTF-8');

            $errors = [];

            // Validate username
            if (empty($username)) {
                $errors[] = "Username is required.";
            }

            // Validate email
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "A valid email is required.";
            }

            // Validate full name
            if (empty($full_name)) {
                $errors[] = "Full name is required.";
            }

            // Check if there are validation errors
            if (count($errors) > 0) {
                $_SESSION['message'] = implode("\n", $errors);
                $_SESSION['message_type'] = "danger";
                header("Location: members.php?action=edit&user_id=$user_id");
                exit;
            }

            // Connect to the database
            $conn = connect_db();
            $checkResult = checkExistingUserEmail($conn, $username, $email, $user_id);

            // Handle the result from the function
            if ($checkResult['error']) {
                // Handle database error
                $_SESSION['message'] = "Database Error: " . $checkResult['message'];
                $_SESSION['message_type'] = "danger";
            } else {
                // Check username and email existence
                if ($checkResult['username_exists'] && $checkResult['existing_username'] !== $username) {
                    $_SESSION['message'] = "Username '$username' already exists.";
                    $_SESSION['message_type'] = "danger";
                    header("Location: members.php?action=edit&user_id=$user_id");
                    exit;
                }
                if ($checkResult['email_exists'] && $checkResult['existing_email'] !== $email) {
                    $_SESSION['message'] = "Email '$email' already exists.";
                    $_SESSION['message_type'] = "danger";
                    header("Location: members.php?action=edit&user_id=$user_id");
                    exit;
                }
            }
            // Check if password is not empty, then hash it, otherwise keep the old password
            if (!empty($password)) {

                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare('UPDATE users SET user_name = :username, password_hash = :password_hash, email = :email, full_name = :full_name WHERE user_id = :user_id');
                $stmt->execute(['username' => $username, 'password_hash' => $password_hash, 'email' => $email, 'full_name' => $full_name, 'user_id' => $user_id]);
            } else {

                $stmt = $conn->prepare('UPDATE users SET user_name = :username, email = :email, full_name = :full_name WHERE user_id = :user_id');
                $stmt->execute(['username' => $username, 'email' => $email, 'full_name' => $full_name, 'user_id' => $user_id]);
            }

            // Update session variable if the username has changed
            if ($_SESSION['user_id'] == $user_id) {
                $_SESSION['user_name'] = $username;
            }

            // Set success or error message
            if ($stmt->rowCount() > 0) {
                $_SESSION['message'] = "User information updated successfully.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "No changes were made.";
                $_SESSION['message_type'] = "warning";
            }

            // Redirect to the edit page to show the message
            header("Location: members.php?action=edit&user_id=$user_id");
            exit;
        } else {
            homeRedirectfun("An error occurred. You will be redirected shortly.", 5);
        }
    } elseif ($action == "toggle_status") {

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'toggle_status') {
            $user_id = $_POST['user_id'];
            $csrf_token = $_POST['csrf_token'];

            // Validate CSRF token
            if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
                $_SESSION['message'] = 'Invalid CSRF token.';
                $_SESSION['message_type'] = 'error';
                header('Location: members.php');
                exit();
            }
            $conn = connect_db();
            // Prepare and execute the query to get the current status
            $stmt = $conn->prepare("SELECT reg_status FROM users WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $new_status = $user['reg_status'] == 1 ? 0 : 1;

                // Update user status
                $stmt = $conn->prepare("UPDATE users SET reg_status = :new_status WHERE user_id = :user_id");
                $stmt->execute([
                    ':new_status' => $new_status,
                    ':user_id' => $user_id
                ]);

                // Set success message
                $_SESSION['message'] = $new_status == 1 ? 'User activated successfully.' : 'User deactivated successfully.';
                $_SESSION['message_type'] = 'success';
            } else {
                // Set error message if user not found
                $_SESSION['message'] = 'User not found.';
                $_SESSION['message_type'] = 'error';
            }

            // Redirect to avoid resubmission
            header('Location: members.php?action=manage');
            exit();
        } else {
            $_SESSION['message'] = 'NOT Applicable';
            $_SESSION['message_type'] = 'error';
            header('Location: members.php');
            exit;
        }
    }
} else {
    header("location: index.php");
    exit;
}
ob_end_flush();
?>


<script>
    function togglePasswordVisibility() {
        var passwordField = document.getElementById('password');
        var checkbox = document.getElementById('togglePassword');

        if (checkbox.checked) {
            passwordField.type = 'text';
        } else {
            passwordField.type = 'password';
        }
    }
    // Function to open modal
    function openModal(userId) {
        var modal = document.getElementById('editModal' + userId);
        modal.style.display = 'block';
    }

    // Function to close modal
    function closeModal(userId) {
        var modal = document.getElementById('editModal' + userId);
        modal.style.display = 'none';
    }

    // delete confirmation alert
    function confirmDelete(userId) {
        if (confirm("Are you sure you want to delete this user?")) {
            // Proceed with form submission
            document.getElementById('deleteForm' + userId).submit();
        } else {
            // Do nothing or handle cancel action
        }
    }
</script>
<script>
    $(document).ready(function() {

        // Focus and blur events for input fields
        $('input').focus(function() {
            $(this).data('placeholder', $(this).attr('placeholder')).attr('placeholder', '');
        }).blur(function() {
            $(this).attr('placeholder', $(this).data('placeholder'));
        });

        // Function to add asterisks to required fields
        function addAsterisks() {
            $('form#editForm').find('[required]').each(function() {
                var label = $(this).prev('label');
                label.append(' <span style="color: red;">*</span>');
            });
        }

        // Call the function when the document is ready
        addAsterisks();
    });
</script>