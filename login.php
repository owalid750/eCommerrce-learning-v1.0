<?php
ob_start();
session_name('user_session');
session_start();
include("./init.php");
$errors = [];
// Check if there are errors stored in the session
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']); // Clear the errors from the session
}

if (!isset($_SESSION['user_name'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
        $password = isset($_POST['password']) ? htmlspecialchars(trim($_POST['password'])) : '';
        if (empty($email) || empty($password)) {
            $errors[] =  'Email or password must not be empty.';
        }
        // Example of database connection
        try {
            $pdo = connect_db();
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION["user_name"] = $user['user_name'];
                header('Location: index.php');
                exit;
            } else {
                $errors[] = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
        // Store errors in session
        $_SESSION['errors'] = $errors;

        // Redirect to the same page to show the errors
        header('Location: login.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-commerce App</title>
    <style>
        body {
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            font-family: Arial, sans-serif;
        }

        .login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .card {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            box-sizing: border-box;
        }

        .card-title {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1em;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }

        .text-center {
            text-align: center;
            margin-top: 20px;
        }

        .text-center a {
            color: #007bff;
            text-decoration: none;
        }

        .text-center a:hover {
            color: #0056b3;
        }

        .success-message {
            background-color: #d4edda;
            /* Light green background */
            color: #155724;
            /* Dark green text */
            border: 1px solid #c3e6cb;
            /* Green border */
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            font-size: 1rem;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
    </style>
</head>

<body>
    <div class="login-page">
        <div class="card">
            <?php if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']); // Clear the message after displaying
            } ?>
            <h3 class="card-title">Login</h3>
            <?php if (!empty($errors)) : ?>
                <?php foreach ($errors as $error) : ?>
                    <div class="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-primary">Login</button>
            </form>
            <div class="text-center">
                <a href="#">Forgot password?</a> | <a href="register.php">Create an account</a>
            </div>
        </div>
    </div>
</body>

</html>

<?php ob_end_flush(); ?>