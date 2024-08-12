<?php
include("./admin/connect.php");
function checkItem($pdo, $field, $table, $value)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stmt->execute([$value]);
    $item_exist = $stmt->fetchColumn() > 0;

    return ['item_exist' => $item_exist];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['value'])) {
        $field = $_POST['field'];
        $value = $_POST['value'];

        try {
            $pdo = connect_db();
            $check_exist = checkItem($pdo, $field, "users", $value);

            if ($check_exist['item_exist']) {
                echo json_encode(['error' => $field . ' already exists.']);
            } else {
                echo json_encode(['error' => '']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }
}
