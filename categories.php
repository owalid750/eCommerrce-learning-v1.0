<?php
session_name('user_session');
session_start();
include "./init.php";
$conn = connect_db();
$categories = getItems($conn, "categories", "category_id", null, null, 20, [], null, null, ["visibility" => 1]);
// print_r($categories);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        } */

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 40px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .col-md-4 {
            flex: 1 1 calc(33.333% - 20px);
            margin: 10px;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-img-placeholder {
            height: 200px;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            font-size: 1.5rem;
            margin: 0;
            color: #333;
        }

        .card-text {
            font-size: 1rem;
            color: #666;
            margin: 10px 0;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #f9f9f9;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.875rem;
            color: #fff;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-secondary {
            background-color: #6c757d;
        }

        .text-muted {
            color: #6c757d;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .col-md-4 {
                flex: 1 1 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Categories</h2>
        <div class="row">
            <?php foreach ($categories as $category) : ?>
                <div class="col-md-4">
                    <form action="categories_items.php" method="post" style="display: inline; border: none; padding: 0; margin: 0;">
                        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category['category_id']); ?>">
                        <input type="hidden" name="cat_name" value="<?php echo htmlspecialchars($category['cat_name']); ?>">

                        <div class="card" onclick="this.closest('form').submit();">
                            <div class="card-img-placeholder">
                                <?php echo htmlspecialchars($category['cat_name']); ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($category['cat_name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($category['description']); ?></p>
                            </div>
                            <div class="card-footer">
                                <div>
                                    <span class="badge badge-<?php echo $category['visibility'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $category['visibility'] ? 'Visible' : 'Hidden'; ?>
                                    </span>
                                    <span class="badge badge-<?php echo $category['allow_comment'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $category['allow_comment'] ? 'Comments Allowed' : 'Comments Disabled'; ?>
                                    </span>
                                    <span class="badge badge-<?php echo $category['allow_ads'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $category['allow_ads'] ? 'Ads Allowed' : 'Ads Disabled'; ?>
                                    </span>
                                </div>
                                <small class="text-muted">Order: <?php echo htmlspecialchars($category['ordering']); ?></small>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>