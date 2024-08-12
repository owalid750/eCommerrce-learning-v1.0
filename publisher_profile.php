<?php
session_name('user_session');
session_start();
include("./init.php");
$page_title = "publisher profile";
$conn = connect_db();
$items = getItems($conn, "items", "item_id", null, null, 100000, [
    [
        'table' => 'categories',
        'condition' => 'items.cat_id = categories.category_id',
        'attribute' => 'cat_name'
    ],
], null, null, [
    "user_id" => $_SESSION['item_user_id']
]);

// print_r($items);
// echo count($items);
// echo "welcome to publisher profile";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php getTitle() ?></title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .item {
            background-color: #f2f2f2;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .item h2 {
            margin-top: 0;
        }

        .item p {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1><?php echo "Items Published By " . ((isset($_SESSION["user_name"]) ? $_SESSION["user_name"] : "") === $_SESSION['item_user_name'] ? '<a style="text-decoration:none" href="manage.php">You,manage</a>' : $_SESSION['item_user_name']) ?></h1>
        <?php foreach ($items as $item) : ?>
            <div class="item">
                <h2><?php echo htmlspecialchars($item['item_name']) ?></h2>
                <p><?php echo "Price: $" . htmlspecialchars($item['item_price']) ?></p>
                <p><?php echo "Category: " . htmlspecialchars($item['cat_name']) ?></p>
                <p><?php echo "Description: " . htmlspecialchars($item['item_desc']) ?></p>
                <?php if ($item['is_item_approved'] == 0) : ?>
                    <p style="color:red">This item is not approved yet.</p>
                <?php else : ?>
                    <form action="item_details.php" method="post" style="display:inline;">
                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
                        <input type="hidden" name="cat_id" value="<?php echo htmlspecialchars($item['cat_id']); ?>">
                        <input type="hidden" name="cat_name" value="<?php echo htmlspecialchars($item['cat_name']); ?>">
                        <button type="submit" class="btn btn-primary" <?php echo ($item['is_item_approved'] == 0) ? 'disabled' : ''; ?>>More About This Item</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>


</html>