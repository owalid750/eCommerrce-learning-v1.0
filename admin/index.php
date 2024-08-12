<?php
session_name('admin_session'); // Use a distinct name for admin sessions
session_start();
$no_nav_bar = "";
$page_title = "Login";
if (isset($_SESSION['admin_user_name'])) {
    header('Location: admin_dashboard.php');
    exit;
}
include "./init.php";

$errors = []; // Initialize an array to store errors

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $user_name = htmlspecialchars(trim($_POST['user_name']), ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars(trim($_POST['password']), ENT_QUOTES, 'UTF-8');

    // Basic input validation
    if (empty($user_name)) {
        $errors['user_name'] = 'Username is required.';
    }
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    }

    // Proceed only if there are no validation errors
    if (empty($errors)) {
        // Connect to the database
        $conn = connect_db();

        // Prepare and execute query securely
        $stmt = $conn->prepare('SELECT user_id,user_name, password_hash FROM users WHERE user_name = :user_name AND group_id = 1');
        $stmt->execute(['user_name' => $user_name]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Password is correct, start a session and set session variables
            $_SESSION['admin_user_name'] = $user['user_name'];
            $_SESSION['admin_user_id'] = $user['user_id'];
            // Redirect to the admin dashboard or any other page
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $errors['login'] = 'Invalid username or password.';
        }
    }
} else {
    // If not a POST request (e.g., on initial load or refresh), clear errors
    $errors = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            /* Ensure at least full viewport height */
            font-family: Arial, sans-serif;
        }

        .page-content {
            text-align: center;
            max-width: 800px;
            /* Adjust as needed */
            width: 90%;
            /* Adjust as needed */
            padding: 20px;
            margin: auto;
            /* Center horizontally */
        }

        .login-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            text-align: left;
            /* Reset text-align for inner content */
        }

        .login-container h2 {
            margin-bottom: 1.5rem;
            font-size: 3rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .error {
            color: red;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-block {
            width: 100%;
            display: block;
        }

        @media (max-width: 768px) {
            .page-content {
                padding: 10px;
            }

            .login-container {
                padding: 1.5rem;
            }

            .login-container h2 {
                font-size: 2rem;
                margin-bottom: 1rem;
            }
        }
    </style>
    <title><?php
            getTitle();
            ?></title>
</head>

<body>

    <body>
        <div class="page-content">
            <div class="login-container">
                <h2 class="text-center">Admin Login</h2>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="user_name" id="username" placeholder="Enter username" value="<?php echo isset($_POST['user_name']) ? htmlspecialchars($_POST['user_name'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
                        <?php if (isset($errors['user_name'])) : ?>
                            <div class="error"><?php echo $errors['user_name']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
                        <?php if (isset($errors['password'])) : ?>
                            <div class="error"><?php echo $errors['password']; ?></div>
                        <?php endif; ?>
                        <?php if (isset($errors['login'])) : ?>
                            <div class="error"><?php echo $errors['login']; ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
            </div>
        </div>

        <script src="<?php echo $js; ?>jquery.min.js"></script>
        <script>
            $(document).ready(function() {


                // Focus and blur events for input fields
                $('input').focus(function() {
                    $(this).data('placeholder', $(this).attr('placeholder')).attr('placeholder', '');
                }).blur(function() {
                    $(this).attr('placeholder', $(this).data('placeholder'));
                });
            });
        </script>
    </body>

</html>

<?php
include $tpl . 'footer.php';
?>