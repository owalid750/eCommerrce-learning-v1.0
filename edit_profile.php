<?php
session_name('user_session');
session_start();
include("../eCommerce/admin/connect.php");
include("../eCommerce/admin/includes/functions/functions.php");

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $conn = connect_db();
    $user_id = $_SESSION['user_id']; // Use session-based user ID

    if (isset($_POST['request_activation'])) {
        // Handle activation request
        handleActivationRequest($conn, $user_id);
    } else {
        // Handle profile update
        handleProfileUpdate($conn, $user_id);
    }
} else {
    header("Location: index.php");
    exit;
}

function handleActivationRequest($conn, $user_id)
{
    // Get current time in 'YYYY-MM-DD HH:MM:SS' format
    $current_time = date('Y-m-d H:i:s');

    // Update last request time with the current datetime
    $stmt = $conn->prepare("UPDATE users SET last_request_time = :current_time WHERE user_id = :user_id");
    $stmt->execute(['current_time' => $current_time, 'user_id' => $user_id]);

    // Send activation request logic here
    $_SESSION['message'] = 'Your profile will be activated in 24 hours.';
    $_SESSION['message_type'] = 'success';

    // Redirect to avoid re-submission
    header('Location: profile.php');
    exit();
}

/* function handleProfileUpdate($conn, $user_id)
{
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    // Process form data and update the database
    $username = htmlspecialchars(strip_tags(preg_replace('/\s+/', '', trim($_POST['user_name']))), ENT_QUOTES, 'UTF-8');
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $full_name = htmlspecialchars(strip_tags(trim($_POST['full_name'])), ENT_QUOTES, 'UTF-8');

    $errors = [];

    // Validate inputs
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
    if (empty($full_name)) $errors[] = "Full name is required.";

    if (count($errors) > 0) {
        $_SESSION['message'] = implode("\n", $errors);
        $_SESSION['message_type'] = "danger";
        header("Location: profile.php");
        exit;
    }

    // Check for existing username/email
    $checkResult = checkExistingUserEmail($conn, $username, $email, $user_id);

    if ($checkResult['error']) {
        $_SESSION['message'] = "Database Error: " . $checkResult['message'];
        $_SESSION['message_type'] = "danger";
    } else {
        if ($checkResult['username_exists'] && $checkResult['existing_username'] !== $username) {
            $_SESSION['message'] = "Username '$username' already exists.";
            $_SESSION['message_type'] = "danger";
            header("Location: profile.php");
            exit;
        }
        if ($checkResult['email_exists'] && $checkResult['existing_email'] !== $email) {
            $_SESSION['message'] = "Email '$email' already exists.";
            $_SESSION['message_type'] = "danger";
            header("Location: profile.php");
            exit;
        }
    }

    // Update user details
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

    // Redirect to the profile page
    header("Location: profile.php");
    exit;
} */


/* function handleProfileUpdate($conn, $user_id)
{
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    // Process form data and update the database
    $username = htmlspecialchars(strip_tags(preg_replace('/\s+/', '', trim($_POST['user_name']))), ENT_QUOTES, 'UTF-8');
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $full_name = htmlspecialchars(strip_tags(trim($_POST['full_name'])), ENT_QUOTES, 'UTF-8');

    $errors = [];

    // Validate inputs
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
    if (empty($full_name)) $errors[] = "Full name is required.";

    if (count($errors) > 0) {
        $_SESSION['message'] = implode("\n", $errors);
        $_SESSION['message_type'] = "danger";
        header("Location: profile.php");
        exit;
    }

    // Check for existing username/email
    $checkResult = checkExistingUserEmail($conn, $username, $email, $user_id);

    if ($checkResult['error']) {
        $_SESSION['message'] = "Database Error: " . $checkResult['message'];
        $_SESSION['message_type'] = "danger";
    } else {
        if ($checkResult['username_exists'] && $checkResult['existing_username'] !== $username) {
            $_SESSION['message'] = "Username '$username' already exists.";
            $_SESSION['message_type'] = "danger";
            header("Location: profile.php");
            exit;
        }
        if ($checkResult['email_exists'] && $checkResult['existing_email'] !== $email) {
            $_SESSION['message'] = "Email '$email' already exists.";
            $_SESSION['message_type'] = "danger";
            header("Location: profile.php");
            exit;
        }
    }

    // Handle file upload
    $user_image = null;
    if (!empty($_FILES['user_image']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["user_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a valid image
        $check = getimagesize($_FILES["user_image"]["tmp_name"]);
        if ($check !== false) {
            // Check file size (limit to 2MB)
            if ($_FILES["user_image"]["size"] > 2000000) {
                $_SESSION['message'] = "Sorry, your file is too large.";
                $_SESSION['message_type'] = "danger";
                header('Location: profile.php');
                exit;
            }

            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $_SESSION['message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $_SESSION['message_type'] = "danger";
                header('Location: profile.php');
                exit;
            }

            // Move file to target directory
            if (move_uploaded_file($_FILES["user_image"]["tmp_name"], $target_file)) {
                $user_image = $target_file;
            } else {
                $_SESSION['message'] = "Sorry, there was an error uploading your file.";
                $_SESSION['message_type'] = "danger";
                header('Location: profile.php');
                exit;
            }
        } else {
            $_SESSION['message'] = "File is not an image.";
            $_SESSION['message_type'] = "danger";
            header('Location: profile.php');
            exit;
        }
    } else {
        // Keep the existing image or set to NULL if no image was uploaded
        $stmt = $conn->prepare('SELECT user_image FROM users WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_image = $user['user_image'] ?? './avatar.png';
    }

    // Update user details
    $sql = 'UPDATE users SET user_name = :username, email = :email, full_name = :full_name';
    $params = [
        'username' => $username,
        'email' => $email,
        'full_name' => $full_name,
        'user_id' => $user_id,
    ];

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $sql .= ', password_hash = :password_hash';
        $params['password_hash'] = $password_hash;
    }

    if ($user_image) {
        $sql .= ', user_image = :user_image';
        $params['user_image'] = $user_image;
    }

    $sql .= ' WHERE user_id = :user_id';
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

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

    // Redirect to the profile page
    header("Location: profile.php");
    exit;
} */

function handleProfileUpdate($conn, $user_id)
{
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    // Process form data and update the database
    $username = htmlspecialchars(strip_tags(preg_replace('/\s+/', '', trim($_POST['user_name']))), ENT_QUOTES, 'UTF-8');
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $full_name = htmlspecialchars(strip_tags(trim($_POST['full_name'])), ENT_QUOTES, 'UTF-8');

    $errors = [];

    // Validate inputs
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
    if (empty($full_name)) $errors[] = "Full name is required.";

    if (count($errors) > 0) {
        $_SESSION['message'] = implode("\n", $errors);
        $_SESSION['message_type'] = "danger";
        header("Location: profile.php");
        exit;
    }

    // Check for existing username/email
    $checkResult = checkExistingUserEmail($conn, $username, $email, $user_id);

    if ($checkResult['error']) {
        $_SESSION['message'] = "Database Error: " . $checkResult['message'];
        $_SESSION['message_type'] = "danger";
    } else {
        if ($checkResult['username_exists'] && $checkResult['existing_username'] !== $username) {
            $_SESSION['message'] = "Username '$username' already exists.";
            $_SESSION['message_type'] = "danger";
            header("Location: profile.php");
            exit;
        }
        if ($checkResult['email_exists'] && $checkResult['existing_email'] !== $email) {
            $_SESSION['message'] = "Email '$email' already exists.";
            $_SESSION['message_type'] = "danger";
            header("Location: profile.php");
            exit;
        }
    }

    // Handle file upload
    $user_image = null;
    if (!empty($_FILES['user_image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $target_file = $target_dir . basename($_FILES["user_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a valid image
        $check = getimagesize($_FILES["user_image"]["tmp_name"]);
        if ($check !== false) {
            // Check file size (limit to 2MB)
            if ($_FILES["user_image"]["size"] > 2000000) {
                $_SESSION['message'] = "Sorry, your file is too large.";
                $_SESSION['message_type'] = "danger";
                header('Location: profile.php');
                exit;
            }

            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $_SESSION['message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $_SESSION['message_type'] = "danger";
                header('Location: profile.php');
                exit;
            }

            // Ensure the target directory is writable
            if (!is_writable($target_dir)) {
                $_SESSION['message'] = "Sorry, the target directory is not writable.";
                $_SESSION['message_type'] = "danger";
                header('Location: profile.php');
                exit;
            }

            // Move file to target directory
            if (move_uploaded_file($_FILES["user_image"]["tmp_name"], $target_file)) {
                $user_image = $target_file;
            } else {
                $_SESSION['message'] = "Sorry, there was an error uploading your file.";
                $_SESSION['message_type'] = "danger";
                header('Location: profile.php');
                exit;
            }
        } else {
            $_SESSION['message'] = "File is not an image.";
            $_SESSION['message_type'] = "danger";
            header('Location: profile.php');
            exit;
        }
    } else {
        // Keep the existing image or set to NULL if no image was uploaded
        $stmt = $conn->prepare('SELECT user_image FROM users WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_image = $user['user_image'] ?? 'avatar.png';
    }

    // Update user details
    $sql = 'UPDATE users SET user_name = :username, email = :email, full_name = :full_name';
    $params = [
        'username' => $username,
        'email' => $email,
        'full_name' => $full_name,
        'user_id' => $user_id,
    ];

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $sql .= ', password_hash = :password_hash';
        $params['password_hash'] = $password_hash;
    }

    if ($user_image) {
        $sql .= ', user_image = :user_image';
        $params['user_image'] = $user_image;
    }

    $sql .= ' WHERE user_id = :user_id';
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

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

    // Redirect to the profile page
    header("Location: profile.php");
    exit;
}
