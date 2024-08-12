<?php
session_name('user_session');
session_start();
include "./init.php";
$conn = connect_db();
$category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
$cat_name = isset($_POST["cat_name"]) ? $_POST['cat_name'] : "";
$page_title = $cat_name;
// $search_query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : "";
$items = getItems($conn, "items", "item_id", "item_id", null, 1000, [
    [
        'table' => 'categories',
        'condition' => 'items.cat_id = categories.category_id',
        'attribute' => 'cat_name'
    ],
    [
        'table' => 'users',
        'condition' => 'items.user_id = users.user_id',
        'attribute' => 'user_name'
    ]
], $category_id, "cat_id");


$_SESSION["category_id"] = $category_id;
$_SESSION["cat_name"] = $cat_name;

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php getTitle() ?></title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css">
    <style>
        .item-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            transition: transform 0.2s ease;
        }

        .item-card:hover {
            transform: scale(1.02);
        }

        .item-img {
            height: 200px;
            object-fit: cover;
        }

        .item-title {
            font-size: 1.25rem;
            margin-bottom: 10px;
        }

        .item-description {
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .item-price {
            font-size: 1.1rem;
            color: #28a745;
            font-weight: bold;
        }

        .card-img-placeholder {
            height: 200px;
            background-color: #cccccc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .item-card {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            transition: transform 0.2s ease;
        }

        .item-card.disabled-card {
            opacity: 0.5;
            pointer-events: none;
            background-color: #f8f9fa;
        }

        .card-img-placeholder {
            position: relative;
            height: 200px;
            background-color: #e9ecef;
        }

        .not-approved-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #dc3545;
            font-weight: bold;
            font-size: 1.25rem;
        }

        .item-title {
            font-size: 1.25rem;
            margin-bottom: 10px;
        }

        .item-price {
            font-size: 1.1rem;
            color: #28a745;
            font-weight: bold;
        }

        .item-description {
            font-size: 1rem;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="my-4">Items in <?php echo htmlspecialchars($cat_name) ?> Category</h1>
        <div class="row">
            <?php if (!empty($items)) : ?>
                <?php foreach ($items as $item) : ?>
                    <div class="col-md-4">
                        <div class="item-card card <?php echo $item['is_item_approved'] == 0 ? 'disabled-card' : ''; ?>">
                            <div class="card-img-placeholder">
                                <?php if ($item['is_item_approved'] == 0) : ?>
                                    <div class="not-approved-overlay">
                                        <p>This item is not approved yet.</p>
                                    </div>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($item['item_name']); ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title item-title"><?php echo htmlspecialchars($item['item_name']); ?></h5>
                                <p class="card-text item-description"><?php echo htmlspecialchars($item['item_desc']); ?></p>
                                <p class="item-price">$<?php echo htmlspecialchars($item['item_price']); ?></p>
                                <?php if ($item['is_item_approved'] == 1) : ?>
                                    <form action="item_details.php" method="post" style="display: inline;">
                                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
                                        <button type="submit" class="btn btn-primary">View Details</button>
                                    </form>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else : ?>
                <p>No items found in this category.</p>
            <?php endif; ?>
        </div>
    </div>
    <script src="path/to/bootstrap.min.js"></script>
</body>

</html>