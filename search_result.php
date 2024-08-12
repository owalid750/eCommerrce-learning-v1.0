<?php
session_name('user_session');
session_start();
include("./init.php");
$search_query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : "";

$conn = connect_db();
$stm = $conn->prepare("SELECT items.*, categories.cat_name 
                       FROM items 
                       INNER JOIN categories ON items.cat_id = categories.category_id 
                       WHERE (items.item_name LIKE ? OR items.item_desc LIKE ?) 
                       AND items.is_item_approved = 1
                       ORDER BY CASE
                           WHEN items.item_name LIKE ? THEN 1
                           ELSE 2
                       END, items.item_name");
$stm->execute(["%$search_query%", "%$search_query%", "$search_query%"]);
$current_items = $stm->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <style>
        /* body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        } */
        /* .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        } */
        h1 {
            text-align: center;
            color: #333;
        }

        .search-results {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 20px;
        }

        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 15px;
            width: 23%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid #ddd;
        }

        .card-body {
            padding: 15px;
        }

        .card-title {
            font-size: 1.25rem;
            color: #333;
        }

        .card-text {
            font-size: 1rem;
            color: #666;
            margin: 10px 0;
        }

        .price {
            font-size: 1.5rem;
            color: #28a745;
            margin-bottom: 10px;
        }

        /* .btn {
            display: inline-block;
            padding: 10px 15px;
            text-align: center;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 3px;
            text-decoration: none;
            cursor: pointer;
            margin-top: auto;
        } */
        .alert {
            width: 100%;
            padding: 15px;
            background-color: #ffcc00;
            color: #333;
            text-align: center;
            border-radius: 5px;
            margin-top: 20px;
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
    </style>
</head>

<body>
    <div class="container">
        <h1>Search Results for "<?php echo $search_query; ?>"</h1>
        <div class="search-results">
            <?php if (empty($current_items)) : ?>
                <div class="alert">
                    No items found for your search query.
                </div>
            <?php else : ?>
                <?php foreach ($current_items as $item) : ?>
                    <div class="card">
                        <div class="card-img-placeholder">
                            <?php if ($item['is_item_approved'] == 0) : ?>
                                <div class="not-approved-overlay">
                                    <p>This item is not approved yet.</p>
                                    <br>
                                </div>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($item['item_name']); ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $item['item_name']; ?></h5>
                            <p class="card-text"><?php echo substr($item['item_desc'], 0, 100); ?>...</p>
                            <p class="price">$<?php echo number_format($item['item_price'], 2); ?></p>
                            <form action="item_details.php" method="post">
                                <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                <input type="hidden" name="cat_id" value="<?php echo $item['cat_id']; ?>">
                                <input type="hidden" name="cat_name" value="<?php echo $item['cat_name']; ?>">
                                <button type="submit" class="btn btn-primary">More About This Item</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>