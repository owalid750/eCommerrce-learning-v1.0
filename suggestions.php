<?php
ob_start();
/* // Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("./init.php");

$search_query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : "";

// Log to check if query is set correctly
file_put_contents('debug.log', "Query: $search_query\n", FILE_APPEND);

if ($search_query === "") {
    echo json_encode([]);
    exit;
}

try {
    $conn = connect_db();
    $stm = $conn->prepare("SELECT item_name FROM items WHERE item_name LIKE ? AND is_item_approved = 1 LIMIT 10");
    $stm->execute(["%$search_query%"]);
    $suggestions = $stm->fetchAll(PDO::FETCH_ASSOC);

    // Log to check if suggestions are fetched correctly
    file_put_contents('debug.log', "Suggestions: " . json_encode($suggestions) . "\n", FILE_APPEND);

    echo json_encode($suggestions);
} catch (Exception $e) {
    // Log error messages
    file_put_contents('debug.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['error' => $e->getMessage()]);
} */

///////////
// Enable error reporting
/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("./init.php");

$search_query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : "";

if ($search_query === "") {
    echo json_encode([]);
    exit;
}

try {
    $conn = connect_db();
    $stm = $conn->prepare("SELECT item_name FROM items WHERE item_name LIKE ? AND is_item_approved = 1 LIMIT 10");
    $stm->execute(["%$search_query%"]);
    $suggestions = $stm->fetchAll(PDO::FETCH_ASSOC);

    // Ensure only JSON is output
    header('Content-Type: application/json');
    echo json_encode($suggestions);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
} */
// // Enable error reporting
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// include("./init.php");

// $search_query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : "";

// if ($search_query === "") {
//     header('Content-Type: application/json');
//     echo json_encode([]);
//     exit;
// }

// try {
//     $conn = connect_db();
//     $stm = $conn->prepare("SELECT item_name FROM items WHERE item_name LIKE ? AND is_item_approved = 1 LIMIT 10");
//     $stm->execute(["%$search_query%"]);
//     $suggestions = $stm->fetchAll(PDO::FETCH_ASSOC);

//     header('Content-Type: application/json');
//     echo json_encode($suggestions);
// } catch (Exception $e) {
//     header('Content-Type: application/json');
//     echo json_encode(['error' => $e->getMessage()]);
// }


// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debugging output
error_log("Debug: PHP script started");

include("../eCommerce/admin/connect.php");
include("../eCommerce/admin/includes/functions/functions.php");
$search_query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : "";

if ($search_query === "") {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

try {
    $conn = connect_db();
    $search_query = "%$search_query%";
    $stm = $conn->prepare("
        SELECT item_name 
        FROM items 
        WHERE item_name LIKE ? AND is_item_approved = 1 
        ORDER BY 
            CASE 
                WHEN item_name LIKE ? THEN 0 
                ELSE 1 
            END,
            item_name 
        LIMIT 10
    ");
    $stm->execute([$search_query, $search_query]);

    $suggestions = $stm->fetchAll(PDO::FETCH_ASSOC);

    // Debugging output
    error_log("Debug: Suggestions fetched");

    header('Content-Type: application/json');
    echo json_encode($suggestions);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
ob_end_flush();
