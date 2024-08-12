<?php
ob_start();
session_name('user_session');
session_start();
include "./init.php";
$page_title = "Edit Profile";

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Use session-based user ID

// Connect to the database
$conn = connect_db();
if (!$conn) {
    echo "Database connection failed.";
    exit;
}

// Prepare and execute query securely
$stmt = $conn->prepare('SELECT * FROM users WHERE user_id = :user_id');
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();
if ($user) {
    // Get current time as a UNIX timestamp
    $current_time = time();
    $can_request = false;
    $button_message = '';

    if ($user['reg_status'] == 0) {
        // Convert last_request_time to UNIX timestamp
        $last_request_time = strtotime($user['last_request_time']); // Convert DATETIME to UNIX timestamp

        // Calculate time difference in seconds
        $time_diff = $current_time - $last_request_time;

        // Set the minimum interval (NUM seconds for this example)
        $min_interval = 60 * 60 * 6; // 6 hours in seconds

        // Determine if the user can request activation again
        if ($time_diff > $min_interval) {
            $can_request = true;
        } else {
            // Calculate remaining time
            $remaining_time = $min_interval - $time_diff;

            // Convert remaining time to hours, minutes, and seconds
            $remaining_hours = floor($remaining_time / 3600);
            $remaining_minutes = floor(($remaining_time % 3600) / 60);
            $remaining_seconds = $remaining_time % 60;

            // Create the button message
            $button_message = 'You can request activation again in ' . $remaining_hours . ' hours, ' . $remaining_minutes . ' minutes, and ' . $remaining_seconds . ' seconds.';

            // Pass remaining time to JavaScript for dynamic countdown
            $remaining_time_js = $remaining_time;
        }
    } else {
        $can_request = false;
    }






?>
    <!DOCTYPE html>
    <html>

    <head>
        <title><?php echo htmlspecialchars($page_title); ?></title>
        <style>
            #countdown {
                font-weight: bold;
                color: red;
            }

            /* Your CSS styles here */
            body {
                background-color: #f8f9fa;
            }

            .card {
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                border: none;
                margin-bottom: 50px;
                width: 100%;
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

            .status-container {
                position: absolute;
                top: 10px;
                right: 10px;
                display: flex;
                gap: 10px;
            }

            .status-activated {
                color: green;
                font-weight: bold;
                padding: 5px;
                border: 1px solid green;
                border-radius: 3px;
                background-color: #e6ffe6;
            }

            .status-deactivated {
                color: red;
                font-weight: bold;
                padding: 5px;
                border: 1px solid red;
                border-radius: 3px;
                background-color: #ffe6e6;
            }

            .user-admin {
                color: blue;
                font-weight: bold;
                padding: 5px;
                border: 1px solid blue;
                border-radius: 3px;
                background-color: #e6f0ff;
            }

            .user-regular {
                color: gray;
                font-weight: bold;
                padding: 5px;
                border: 1px solid gray;
                border-radius: 3px;
                background-color: #f0f0f0;
            }
        </style>
        </script>
    </head>

    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3 class="mb-0 text-center">Edit Profile</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_SESSION['message'])) : ?>
                                <div class="message-container <?php echo htmlspecialchars($_SESSION['message_type']); ?>">
                                    <h2><?php echo htmlspecialchars($_SESSION['message']); ?></h2>
                                </div>
                            <?php
                                unset($_SESSION['message']);
                                unset($_SESSION['message_type']);
                            endif; ?>
                            <?php
                            if (empty($_SESSION['csrf_token'])) {
                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                            } ?>
                            <form method="POST" action="edit_profile.php" id="editForm" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <div class="status-container">
                                    <span class="<?php echo $user['reg_status'] == 1 ? 'status-activated' : 'status-deactivated'; ?>">
                                        <?php echo $user['reg_status'] == 1 ? 'Activated' : 'Deactivated'; ?>
                                    </span>
                                    <span class="<?php echo $user['group_id'] == 1 ? 'user-admin' : 'user-regular'; ?>">
                                        <?php echo $user['group_id'] == 1 ? 'Admin' : 'User'; ?>
                                    </span>
                                </div>

                                <div class="form-group text-center">
                                    <label for="user_image">Profile Image</label>
                                    <div>
                                        <?php if (!empty($user['user_image'])) : ?>
                                            <img src="<?php echo htmlspecialchars($user['user_image']); ?>" alt="Profile Image" class="img-thumbnail" width="150" height="150">
                                        <?php else : ?>
                                            <img src="./avatar.png" alt="Avatar" class="img-thumbnail" width="150" height="150">
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" class="form-control-file mt-3" id="user_image" name="user_image">
                                </div>

                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" placeholder="Enter username" value="<?php echo htmlspecialchars($user["user_name"]); ?>" name="user_name" autocomplete="off" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" placeholder="Leave blank if you don't want to change password" name="password" autocomplete="new-password">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" placeholder="Enter email" value="<?php echo htmlspecialchars($user["email"]); ?>" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="fullname">Full Name</label>
                                    <input type="text" class="form-control" id="fullname" placeholder="Enter full name" value="<?php echo htmlspecialchars($user["full_name"]); ?>" name="full_name" required>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button name="submit" type="submit" class="btn btn-primary">Save Changes</button>
                                    <a href="profile.php" class="btn btn-secondary">Cancel</a>
                                </div>
                                <?php if ($user['reg_status'] == 0) : ?>
                                    <div class="form-group mt-3">
                                        <button type="submit" name="request_activation" class="btn btn-warning" <?php echo $can_request ? '' : 'disabled'; ?>>
                                            <?php echo $can_request ? 'Request Activation' : 'Requested, Try again later'; ?>
                                        </button>
                                        <?php if (!$can_request && $button_message) : ?>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($button_message); ?>
                                            </small>

                                        <?php endif; ?>

                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>


    </html>
<?php
} else {
    echo "User not found.";
}
ob_end_flush();
?>