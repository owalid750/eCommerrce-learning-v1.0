<?php
session_name('user_session');
session_start();
include("./admin/connect.php");
include("./includes/functions/functions.php");

$action = isset($_GET['action']) ? $_GET['action'] : "";

if ($action == "add") {
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $user_id = $_SESSION['user_id']; // Use session-based user ID
        $item_id = $_POST['item_id'];
        $comment_content = htmlspecialchars(trim($_POST['comment']));

        if (empty($comment_content)) {
            $_SESSION["message"] = "Comment cannot be empty";
            $_SESSION["message_type"] = "error";
        } else {
            $conn = connect_db();
            $sql = "INSERT INTO comments (user_id, item_id, comment_content) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$user_id, $item_id, $comment_content]);

            if ($stmt->rowCount() > 0) {
                $_SESSION["message"] = "Comment added successfully";
                $_SESSION["message_type"] = "success";
            } else {
                $_SESSION["message"] = "Failed to add comment";
                $_SESSION["message_type"] = "error";
            }
        }
        $_SESSION['redirect_item_id'] = $item_id;
        header("location: item_details.php");
        exit;
    }
} elseif ($action == "delete") {
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $comment_id = $_POST['comment_id'];
        $item_id = $_POST['item_id'];
        $conn = connect_db();
        $sql = "DELETE FROM comments WHERE comment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$comment_id]);
        if ($stmt->rowCount() > 0) {
            $_SESSION["message"] = "Comment deleted successfully";
            $_SESSION["message_type"] = "success";
        } else {
            $_SESSION["message"] = "Failed to delete comment";
            $_SESSION["message_type"] = "error";
        }
        $_SESSION['redirect_item_id'] = $item_id;
        header("location: item_details.php");
        exit;
    }
} elseif ($action == "edit") {
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $comment_id = $_POST['comment_id'];
        $item_id = $_POST['item_id'];
        $comment_content = htmlspecialchars(trim($_POST['comment']));
        if (empty($comment_content)) {
            $_SESSION["message"] = "Comment cannot be empty";
            $_SESSION["message_type"] = "error";
        } else {
            $conn = connect_db();
            $sql = "UPDATE comments SET comment_content = ? WHERE comment_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$comment_content, $comment_id]);
            if ($stmt->rowCount() > 0) {
                $_SESSION["message"] = "Comment updated successfully";
                $_SESSION["message_type"] = "success";
            } else {
                $_SESSION["message"] = "Failed to update comment";
                $_SESSION["message_type"] = "error";
            }
        }
        $_SESSION['redirect_item_id'] = $item_id;
        header("location: item_details.php");
        exit;
    }
}
