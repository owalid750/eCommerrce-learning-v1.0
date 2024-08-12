<?php
ob_start();
session_name('user_session');
session_start();
include("./init.php");

$errors = [];
if (!isset($_SESSION['user_name'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Handle form submission
        $user_name = isset($_POST['user_name']) ? htmlspecialchars(trim($_POST['user_name'])) : "";
        $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
        $password = isset($_POST['password']) ? htmlspecialchars(trim($_POST['password'])) : '';
        $full_name = isset($_POST['full_name']) ? htmlspecialchars(trim($_POST['full_name'])) : "";

        $errors = [];

        if (empty($email)) {
            $errors[] = 'Email must not be empty.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }
        if (empty($password)) {
            $errors[] = 'Password must not be empty.';
        }
        if (empty($user_name)) {
            $errors[] = 'Username must not be empty.';
        }
        if (empty($full_name)) {
            $errors[] = 'Full name must not be empty.';
        }

        if (empty($errors)) {
            try {
                $pdo = connect_db();
                $check_exist_user_name = checkItem($pdo, "user_name", "users", $user_name);
                $check_exist_email = checkItem($pdo, "email", "users", $email);

                if ($check_exist_user_name['item_exist']) {
                    $errors[] = 'Username already exists.';
                }
                if ($check_exist_email['item_exist']) {
                    $errors[] = 'Email already exists.';
                }

                if (empty($errors)) {
                    $password_hash = password_hash($password, PASSWORD_BCRYPT);

                    $stm = $pdo->prepare("INSERT INTO users (user_name, email, password_hash, full_name) 
                                              VALUES (:user_name, :email, :password_hash, :full_name)");
                    $stm->execute([
                        'user_name' => $user_name,
                        'email' => $email,
                        'password_hash' => $password_hash,
                        'full_name' => $full_name
                    ]);

                    // Add a success message to the session
                    $_SESSION['success'] = 'Registration successful! Login now';

                    // Redirect to login page or any other page
                    header('Location: login.php');
                    exit;
                }
            } catch (PDOException $e) {
                $errors[] = 'Database error: ' . $e->getMessage();
            }

            // Add errors to session
            $_SESSION['errors'] = $errors;
            header('Location: register.php');
            exit;
        }
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
    <title>Register - E-commerce App</title>
    <style>
        body {
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            font-family: Arial, sans-serif;
        }

        .error {
            color: red;
        }

        .register-page {
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
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('register_form');
            let valid = {
                user_name: false,
                email: false,
                password: false,
                full_name: false
            };

            form.addEventListener('input', function(e) {
                if (['user_name', 'Email', 'password', 'full_name'].includes(e.target.id)) {
                    validateField(e.target.id, e.target.value);
                }
            });

            function validateField(field, value) {
                if (field === 'user_name') {
                    const usernameValid = value.length >= 4;
                    if (usernameValid) {
                        checkFieldExistence(field, value);
                    } else {
                        valid[field] = false;
                        document.getElementById(field + 'Error').textContent = 'Username must be at least 4 characters long';
                    }
                } else if (field === 'Email') {
                    const emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                    if (emailValid) {
                        checkFieldExistence(field, value);
                    } else {
                        valid[field] = false;
                        document.getElementById(field + 'Error').textContent = 'Invalid email format';
                    }
                } else if (field === 'password') {
                    const passwordValid = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@$]).{8,}$/.test(value);
                    valid[field] = passwordValid;
                    document.getElementById(field + 'Error').textContent = passwordValid ? '' : 'Password must be at least 8 characters long and contain lowercase, uppercase, and special characters (!@$(,:")';
                } else if (field === 'full_name') {
                    valid[field] = value.trim() !== '';
                    document.getElementById(field + 'Error').textContent = valid[field] ? '' : 'Full name must not be empty';
                }
            }

            function checkFieldExistence(field, value) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'valid_register.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            const errorElement = document.getElementById(field + 'Error');
                            if (response.error) {
                                errorElement.textContent = response.error;
                                valid[field] = false;
                            } else {
                                errorElement.textContent = '';
                                valid[field] = true;
                            }
                        } catch (e) {
                            console.error('Error parsing JSON response:', e);
                        }
                    }
                };
                xhr.send('field=' + field + '&value=' + encodeURIComponent(value));
            }

            form.addEventListener('submit', function(e) {
                let allValid = true;
                const fields = ['user_name', 'Email', 'password', 'full_name'];

                fields.forEach(field => {
                    if (!valid[field]) {
                        allValid = false;
                        document.getElementById(field + 'Error').textContent = 'This field is required';
                    }
                });

                if (!allValid) {
                    e.preventDefault();
                }
            });
        });
    </script>


</head>

<body>
    <div class="register-page">
        <div class="card">
            <h3 class="card-title">Register</h3>
            <?php if (!empty($errors)) : ?>
                <?php foreach ($errors as $error) : ?>
                    <div class="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
            <form id="register_form" action="register.php" method="POST">
                <div class="form-group">
                    <label for="user_name">Username</label>
                    <input type="text" id="user_name" name="user_name" required>
                    <span id="user_nameError" class="error"></span>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="Email" name="email" required>
                    <span id="EmailError" class="error"></span>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <span id="passwordError" class="error"></span>

                </div>
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required>
                    <span id="full_nameError" class="error"></span>

                </div>
                <button type="submit" class="btn-primary">Register</button>
            </form>
            <div class="text-center">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </div>
    </div>
</body>

</html>

<?php ob_end_flush(); ?>