<?php
session_name('user_session');
session_start();
include("./admin/connect.php");
include("./includes/functions/functions.php");

$action = isset($_GET['action']) ? $_GET['action'] : "";

// Function to handle redirection with messages and types
function redirectToManagePage($message = '', $message_type = 'info')
{
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $message_type; // New: Set message type
    header("Location: manage.php");
    exit;
}

// Function to handle validation errors
function validateField($value, $fieldName)
{
    if (empty(htmlspecialchars(trim($value)))) {
        return "$fieldName cannot be empty.";
    }
    return '';
}

// Function to delete an item
function deleteItem($item_id)
{
    $conn = connect_db();
    $stm = $conn->prepare("DELETE FROM items WHERE item_id = ?");
    $stm->execute([$item_id]);
}

// Function to delete a comment
function deleteComment($comment_id)
{
    $conn = connect_db();
    $stm = $conn->prepare("DELETE FROM comments WHERE comment_id = ?");
    $stm->execute([$comment_id]);
}

// Function to update an item
function updateItem($item_id, $item_name, $item_desc, $item_price)
{
    $conn = connect_db();
    $stm = $conn->prepare("UPDATE items SET item_name = :item_name, item_desc = :item_desc, item_price = :item_price WHERE item_id = :item_id");
    $stm->execute([
        'item_name' => htmlspecialchars(trim($item_name)),
        'item_desc' => htmlspecialchars(trim($item_desc)),
        'item_price' => htmlspecialchars(trim($item_price)),
        'item_id' => $item_id
    ]);
}

// Function to update a comment
function updateComment($comment_id, $comment_content)
{
    $conn = connect_db();
    $stm = $conn->prepare("UPDATE comments SET comment_content = :comment_content WHERE comment_id = :comment_id");
    $stm->execute([
        'comment_content' => htmlspecialchars(trim($comment_content)),
        'comment_id' => $comment_id
    ]);
}
// function to add a item
function addItem($item_name, $item_desc, $item_price, $item_add_date, $item_country_made, $item_status, $item_rating, $item_category, $item_user_id)
{
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
    $stmt->execute();
}


try {
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        switch ($action) {
            case "add_item":
                if (isset($_POST['item_name'], $_POST['item_desc'], $_POST['item_price'], $_POST['item_add_date'], $_POST['item_country_made'], $_POST['item_status'], $_POST['item_rating'], $_POST['item_category'], $_POST['item_user_id'])) {

                    $errors = [];
                    $errors[] = validateField($_POST['item_name'], 'Item name');
                    $errors[] = validateField($_POST['item_desc'], 'Item description');
                    $errors[] = validateField($_POST['item_price'], 'Item price');
                    $errors[] = validateField($_POST['item_add_date'], 'item_add_date');
                    $errors[] = validateField($_POST['item_country_made'], 'item_country_made');
                    $errors[] = validateField($_POST['item_status'], 'item_status');
                    $errors[] = validateField($_POST['item_rating'], 'item_rating');
                    $errors[] = validateField($_POST['item_category'], 'item_category');
                    // Filter out empty error messages
                    $errors = array_filter($errors);

                    if (empty($errors)) {
                        addItem($_POST['item_name'], $_POST['item_desc'], $_POST['item_price'], $_POST['item_add_date'], $_POST['item_country_made'], $_POST['item_status'], $_POST['item_rating'], $_POST['item_category'], $_POST['item_user_id']);
                        redirectToManagePage('Item ADDED successfully.', 'success');
                    } else {
                        redirectToManagePage(implode(' ', $errors), 'error');
                    }
                }
                break;

            case "delete_item":
                if (isset($_POST['item_id'])) {
                    deleteItem($_POST['item_id']);
                    redirectToManagePage('Item deleted successfully.', 'success');
                }
                break;

            case "delete_comment":
                if (isset($_POST['comment_id'])) {
                    deleteComment($_POST['comment_id']);
                    redirectToManagePage('Comment deleted successfully.', 'success');
                }
                break;

            case "update_item":
                if (isset($_POST['item_id'], $_POST['name'], $_POST['description'], $_POST['price'])) {
                    $errors = [];
                    $errors[] = validateField($_POST['name'], 'Item name');
                    $errors[] = validateField($_POST['description'], 'Item description');
                    $errors[] = validateField($_POST['price'], 'Item price');

                    // Filter out empty error messages
                    $errors = array_filter($errors);

                    if (empty($errors)) {
                        updateItem($_POST['item_id'], $_POST['name'], $_POST['description'], $_POST['price']);
                        redirectToManagePage('Item updated successfully.', 'success');
                    } else {
                        redirectToManagePage(implode(' ', $errors), 'error');
                    }
                }
                break;

            case "update_comment":
                if (isset($_POST['comment_id'], $_POST['comment'])) {
                    $errors = [];
                    $errors[] = validateField($_POST['comment'], 'Comment');

                    // Filter out empty error messages
                    $errors = array_filter($errors);

                    if (empty($errors)) {
                        updateComment($_POST['comment_id'], $_POST['comment']);
                        redirectToManagePage('Comment updated successfully.', 'success');
                    } else {
                        redirectToManagePage(implode(' ', $errors), 'error');
                    }
                }
                break;

            default:
                redirectToManagePage('Unknown action.', 'warning');
                break;
        }
    }
} catch (Exception $e) {
    // Handle exceptions or log errors
    redirectToManagePage('An error occurred: ' . htmlspecialchars($e->getMessage()), 'error');
}
