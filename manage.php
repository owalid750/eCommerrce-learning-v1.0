<?php
ob_start();
session_name('user_session');
session_start();
include "./init.php";
// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Use session-based user ID

$conn = connect_db();

$items = getItems($conn, "items", "item_id", null, null, 10000, [
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
], null, null, [
    "user_id" => $user_id
]);
$comments = getItems($conn, "comments", "comment_id", null, null, 10000, [
    [
        'table' => 'items',
        'condition' => 'items.item_id = comments.item_id',
        'attribute' => 'item_name',
    ],
    // [
    //     'table' => 'categories',
    //     'condition' => 'items.cat_id = categories.category_id',
    //     'attribute' => 'category_id'
    // ],
], null, null, [
    "user_id" => $user_id
]);
$category_names = getItems($conn, "categories", "category_id", null, null, 20, [], null, null, [
    "allow_ads" => 1
]);
/* task how to stoure session cat name and id reelated with current item */
// Store category name and ID related to each item in the session
// foreach ($items as $item) {
//     $_SESSION['categories'][$item['item_id']] = [
//         'cat_name' => $item['cat_name'],
//         'category_id' => $item['cat_id']
//     ];
// }


?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage</title>
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        .card-header {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
        }

        .card-body {
            background-color: white;
            padding: 20px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #007bff;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .btn-edit,
        .btn-delete {
            margin-left: 5px;
        }

        .btn-close {
            border: none;
            background: transparent;
            cursor: pointer;
        }

        .custom-alert {
            padding: 15px;
            border-radius: 5px;
            margin: 20px auto;
            width: 90%;
            max-width: 600px;
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .custom-alert-close {
            position: absolute;
            top: 10px;
            right: 10px;
            border: none;
            background: transparent;
            font-size: 18px;
            cursor: pointer;
        }

        .custom-alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .custom-alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .custom-alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .custom-alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .confirmation-dialog {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .confirmation-dialog-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 90%;
            width: 400px;
        }

        .confirmation-dialog-buttons {
            margin-top: 20px;
        }

        .confirmation-dialog button {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }

        .confirmation-dialog button:hover {
            background: #0056b3;
        }

        .confirmation-dialog button.cancel {
            background: #6c757d;
        }

        .confirmation-dialog button.cancel:hover {
            background: #5a6268;
        }

        .account-status-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            /* Full viewport height */
            background-color: #f8f9fa;
            /* Optional: background color for better visibility */
        }

        .account-status {
            padding: 20px;
            border-radius: 8px;
            background-color: #f8d7da;
            color: #721c24;
            font-size: 1.25rem;
            font-weight: bold;
            border: 1px solid #f5c6cb;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 90%;
            /* Adjust to your needs */
            text-align: center;
        }

        .account-status::before {
            content: '⚠️';
            font-size: 1.5rem;
            margin-right: 10px;
        }

        @media (max-width: 768px) {
            .account-status {
                font-size: 1rem;
                padding: 15px;
            }
        }

        /* Media Queries for Responsiveness */

        @media (max-width: 1200px) {
            .card-body {
                padding: 15px;
            }

            .confirmation-dialog-content {
                max-width: 80%;
            }
        }

        @media (max-width: 992px) {
            .list-group-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-edit,
            .btn-delete {
                margin-left: 0;
                margin-top: 5px;
            }
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 10px;
            }

            .custom-alert {
                width: 95%;
                padding: 10px;
            }

            .confirmation-dialog-content {
                width: 90%;
                padding: 15px;
            }

            .confirmation-dialog button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }

        @media (max-width: 576px) {

            .card-header,
            .card-body {
                padding: 10px;
            }

            .custom-alert {
                width: 100%;
                padding: 8px;
            }

            .confirmation-dialog-content {
                width: 100%;
                padding: 10px;
            }

            .confirmation-dialog button {
                width: 100%;
                padding: 12px;
                font-size: 16px;
            }
        }
    </style>
</head>
<!-- check user activated or not  -->
<?php if (checkUserStatus($_SESSION["user_name"]) == 0) : ?>

    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <?php if (isset($_SESSION['message'])) : ?>
                        <?php
                        $message_type = isset($_SESSION['message_type']) ? htmlspecialchars($_SESSION['message_type']) : 'info';
                        ?>
                        <div class="custom-alert custom-alert-<?php echo $message_type; ?>">
                            <?php echo htmlspecialchars($_SESSION['message']); ?>
                            <button class="custom-alert-close" onclick="this.parentElement.style.display='none';">×</button>
                        </div>
                        <?php
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                    <?php endif; ?>

                    <div class="card">

                        <div class="card-header">

                            <h3 class="mb-0 text-center">Manage</h3>

                        </div>
                        <div class="card-body">
                            <button type="button" class="btn btn-success mb-3" data-toggle="modal" data-target="#addItemModal">Add New Item</button>
                            <h4>My Items</h4>
                            <?php if (!empty($items)) { ?>
                                <ul class="list-group">
                                    <?php foreach ($items as $item) { ?>

                                        <li class="list-group-item">
                                            <div>
                                                <strong><?php echo htmlspecialchars($item['item_name']); ?></strong>
                                                <p><?php echo htmlspecialchars($item['item_desc']); ?></p>
                                                <span>Price: $<?php echo htmlspecialchars($item['item_price']); ?> || </span>
                                                <span>Date Added: <?php echo htmlspecialchars($item['item_add_date']); ?> || </span>
                                                <span>Category: <?php echo htmlspecialchars($item['cat_name']); ?></span>
                                                <br>
                                                <span>Total Comments: <?php echo calcNumberOfItems($conn, "comment_id", "comments", "item_id", $item['item_id']) ?> </span>

                                                <?php if ($item['is_item_approved'] == 0) echo '<div class="alert alert-warning mt-2" role="alert">This item is not approved yet and cannot be edited.</div>'; ?>
                                            </div>
                                            <div>
                                                <?php if ($item['is_item_approved'] == 1) { ?>
                                                    <button type="button" class="btn btn-primary btn-edit" data-toggle="modal" data-target="#editItemModal" data-id="<?php echo htmlspecialchars($item['item_id']); ?>" data-name="<?php echo htmlspecialchars($item['item_name']); ?>" data-description="<?php echo htmlspecialchars($item['item_desc']); ?>" data-price="<?php echo htmlspecialchars($item['item_price']); ?>">Edit</button>

                                                <?php } else { ?>
                                                    <button type="button" class="btn btn-primary btn-edit disabled" aria-disabled="true">Edit</button>
                                                <?php } ?>
                                                <form action="handel_manage.php?action=delete_item" method="post" style="display:inline;" id="deleteForm">
                                                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
                                                    <button type="button" class="btn btn-danger btn-delete" onclick="confirmDeletion()">Delete</button>
                                                </form>

                                                <!-- New Form for Showing Item Details -->
                                                <form action="item_details.php" method="post" style="display:inline;">

                                                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
                                                    <input type="hidden" name="cat_id" value="<?php echo htmlspecialchars($item['cat_id']); ?>">
                                                    <input type="hidden" name="cat_name" value="<?php echo htmlspecialchars($item['cat_name']); ?>">
                                                    <button type="submit" class="btn btn-info">Show Item</button>
                                                </form>
                                                <!-- Confirmation Dialog HTML -->
                                                <div id="confirmationDialog" class="confirmation-dialog">
                                                    <div class="confirmation-dialog-content">
                                                        <p>Are you sure you want to delete this item?
                                                            <br> note: this will delete all comment related to this item
                                                        </p>
                                                        <div class="confirmation-dialog-buttons">
                                                            <button onclick="confirmDelete()">Yes</button>
                                                            <button onclick="cancelDelete()">No</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } else { ?>
                                <p>No items found.</p>
                            <?php } ?>

                            <!-- Add New Item Modal -->
                            <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addItemModalLabel">Add New Item</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="handel_manage.php?action=add_item" method="post" id="addItemForm">
                                                <input type="hidden" name="item_user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                                                <div class="form-group">
                                                    <label for="item_name">Item Name</label>
                                                    <input type="text" class="form-control" id="item_name" name="item_name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="item_desc">Description</label>
                                                    <textarea class="form-control" id="item_desc" name="item_desc" required></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="item_price">Price in dollar</label>
                                                    <input type="number" step="0.01" class="form-control" id="item_price" name="item_price" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="item_add_date">Date Added:</label>
                                                    <input class="form-control" type="date" id="item_add_date" name="item_add_date" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="item_country_made">Country Made:</label>
                                                    <input type="text" id="item_country_made" class="form-control" name="item_country_made" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="item_status">Status:</label>
                                                    <select class="form-control" id="item_status" name="item_status" required>
                                                        <option value="new">New</option>
                                                        <option value="used">Used</option>
                                                        <option value="refurbished">Refurbished</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="item_rating">Rating:</label>
                                                    <input class="form-control" type="number" id="item_rating" name="item_rating" min="1" max="5" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="item_category">Category:</label>
                                                    <select class="form-control" id="item_category" name="item_category" required>
                                                        <?php if (empty($category_names)) : ?>
                                                            <option value="">No categories available</option>
                                                        <?php else : ?>
                                                            <?php foreach ($category_names as $category) : ?>
                                                                <option value="<?php echo htmlspecialchars($category['category_id']) ?>">
                                                                    <?php echo htmlspecialchars($category['cat_name']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Add Item</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h4>My Comments</h4>
                            <p>total: <?php echo htmlspecialchars(count($comments)) ?></p>
                            <?php if (!empty($comments)) { ?>
                                <ul class="list-group">
                                    <?php foreach ($comments as $comment) { ?>


                                        <li class="list-group-item">
                                            <div>
                                                <p><?php echo htmlspecialchars($comment['comment_content']); ?></p>
                                                <span>Date: <?php echo htmlspecialchars($comment['comment_date']); ?> || </span>
                                                <span>Commented On Item: <?php echo htmlspecialchars($comment['item_name']); ?></span>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-primary btn-edit" data-toggle="modal" data-target="#editCommentModal" data-id="<?php echo htmlspecialchars($comment['comment_id']); ?>" data-comment="<?php echo htmlspecialchars($comment['comment_content']); ?>">Edit</button>
                                                <form action="handel_manage.php?action=delete_comment" method="post" style="display:inline;">
                                                    <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['comment_id']); ?>">
                                                    <button type="submit" class="btn btn-danger btn-delete">Delete</button>
                                                </form>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } else { ?>
                                <p>No comments found.</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Item Modal -->
        <div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-labelledby="editItemModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="handel_manage.php?action=update_item" method="post">
                        <div class="modal-body">
                            <input type="hidden" name="item_id" id="item_id">
                            <div class="form-group">
                                <label for="item_name">Name</label>
                                <input type="text" class="form-control" id="item_name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="item_description">Description</label>
                                <textarea class="form-control" id="item_description" name="description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="item_price">Price</label>
                                <input type="number" class="form-control" id="item_price" name="price" step="0.01" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Comment Modal -->
        <div class="modal fade" id="editCommentModal" tabindex="-1" role="dialog" aria-labelledby="editCommentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCommentModalLabel">Edit Comment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="handel_manage.php?action=update_comment" method="post">
                        <div class="modal-body">
                            <input type="hidden" name="comment_id" id="comment_id">
                            <div class="form-group">
                                <label for="comment_text">Comment</label>
                                <textarea class="form-control" id="comment_text" name="comment" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            $('#editItemModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var item_id = button.data('id');
                var item_name = button.data('name');
                var item_description = button.data('description');
                var item_price = button.data('price');

                var modal = $(this);
                modal.find('#item_id').val(item_id);
                modal.find('#item_name').val(item_name);
                modal.find('#item_description').val(item_description);
                modal.find('#item_price').val(item_price);
            });

            $('#editCommentModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var comment_id = button.data('id');
                var comment_text = button.data('comment');

                var modal = $(this);
                modal.find('#comment_id').val(comment_id);
                modal.find('#comment_text').val(comment_text);
            });
        </script>
        <script>
            let formToSubmit;

            function confirmDeletion() {
                formToSubmit = document.getElementById('deleteForm');
                document.getElementById('confirmationDialog').style.display = 'flex';
            }

            function confirmDelete() {
                document.getElementById('confirmationDialog').style.display = 'none';
                formToSubmit.submit();
            }

            function cancelDelete() {
                document.getElementById('confirmationDialog').style.display = 'none';
            }
        </script>


    </body>
<?php else : ?>
    <div class="account-status-container">
        <p class="account-status">YOUR ACCOUNT IS NOT ACTIVE YET! <br><a style="text-decoration: none;" href="profile.php">GO to active your account</a></p>


    </div>

<?php endif; ?>

</html>

<?php ob_end_flush(); ?>