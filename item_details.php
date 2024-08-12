<!-- 
task 1 create link to go to cat items page 
task 2 create public profile publisher page
task 3 create link to go to public profile for publisher  page  

-->
<?php
session_name('user_session');
session_start();
include "./init.php";
$conn = connect_db();
// Check if item_id is in POST data or session variable
if (isset($_POST['item_id'])) {
    $item_id = intval($_POST['item_id']);
} elseif (isset($_SESSION['redirect_item_id'])) {
    $item_id = intval($_SESSION['redirect_item_id']);
    // unset($_SESSION['redirect_item_id']);
} else {
    $item_id = 0; // Default value if item_id is not found
}

$session_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "";
$item_details = getItems($conn, "items", "item_id", "item_id", null, 1000, [
    [
        'table' => 'categories',
        'condition' => 'items.cat_id = categories.category_id',
        'attribute' => 'cat_name'
    ],
    [
        'table' => 'users',
        'condition' => 'items.user_id = users.user_id',
        'attribute' => 'user_name'
    ],

], $item_id, "item_id");

$comments = getItems($conn, "comments", "comment_id", null, null, 10000, [
    [
        'table' => 'users',
        'condition' => 'comments.user_id = users.user_id',
        'attribute' => 'user_name,user_image'
    ],

], null, null, [
    "item_id" => $item_id
]);
$_SESSION['item_user_id'] = $item_details[0]["user_id"];
$_SESSION['item_user_name'] = $item_details[0]["user_name"];


// Prioritize POST data over session data for category name and ID
$category_id = isset($_POST['cat_id']) ? $_POST['cat_id'] : (isset($_SESSION['category_id']) ? $_SESSION['category_id'] : null);
$cat_name = isset($_POST['cat_name']) ? $_POST['cat_name'] : (isset($_SESSION['cat_name']) ? $_SESSION['cat_name'] : null);

// debug
// echo "<br>";
// echo $cat_name;
// echo "<br>";
// echo $category_id;
// echo "<br>";

// print_r($_SESSION);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Details</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .item-details {
            margin: 20px auto;
            max-width: 800px;
        }

        .item-img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .item-title {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .item-price {
            font-size: 1.5rem;
            color: #28a745;
            font-weight: bold;
        }

        .item-description {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .item-meta {
            margin-bottom: 20px;
        }

        .item-meta p {
            margin: 5px 0;
        }

        .item-meta span {
            font-weight: bold;
        }

        .comments-section {
            background-color: #f8f9fa;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }

        .comment {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            height: fit-content;
            padding: 15px;
            margin-bottom: 20px;
        }

        .comment-user {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .comment-text {
            margin-bottom: 10px;
        }

        .comment-date {
            font-size: 0.9em;
            color: #6c757d;
        }

        .add-comment-form {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-top: 30px;
        }


        .btn-edit,
        .btn-delete {
            background-color: #f8f9fa;
            color: #007bff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-edit:hover,
        .btn-delete:hover {
            background-color: #e9ecef;
        }

        .btn-edit {
            margin-right: 10px;
        }

        .btn-delete {
            color: #dc3545;
        }

        .btn-delete:hover {
            color: #a52121;
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

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
            vertical-align: middle;
        }

        .comment-user {
            font-weight: bold;
        }

        /* Basic styles for dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-toggle {
            background: none;
            /* Remove background */
            border: none;
            /* Remove border */
            padding: 0;
            /* Remove padding */
            cursor: pointer;
            /* Pointer cursor */
            font-size: 24px;
            /* Font size for three dots */
            color: #007bff;
            /* Color of the dots */
            text-align: center;
            /* Center text */
        }

        .dropdown-menu {
            display: none;
            /* Hide dropdown menu by default */
            position: absolute;
            /* Position menu relative to dropdown */
            background-color: #ffffff;
            /* Background color of menu */
            border: 1px solid #ddd;
            /* Border around menu */
            border-radius: 4px;
            /* Rounded corners */
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            /* Shadow for dropdown */
            z-index: 1000;
            /* Ensure menu appears above other content */
            right: 0;
            /* Align menu to the right of the button */
            top: 30px;
            /* Adjust position of menu */
        }

        .dropdown-item {
            display: block;
            /* Display items as block elements */
            padding: 8px 16px;
            /* Padding inside menu items */
            color: #007bff;
            /* Text color */
            text-decoration: none;
            /* Remove underline from links */
        }

        .dropdown-item:hover {
            background-color: #f1f1f1;
            /* Background color on hover */
        }

        .dropdown form {
            margin: 0;
            /* Remove margin from form */
        }

        .dropdown-menu.show {
            display: block;
            /* Show menu when active */
        }
    </style>
</head>

<body>
    <div class="container item-details">
        <?php if ($item_details) : ?>
            <?php foreach ($item_details as $item) : ?>
                <div class="card">
                    <?php $itemname = $item['item_name'] ?>
                    <img src="<?php echo htmlspecialchars($item['item_image'] ?? 'https://placehold.co/600x400/grey/white?font=oswald&text=') . "$itemname"; ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" class="item-img">
                    <div class="card-body">
                        <h1 class="item-title"><?php echo htmlspecialchars($item['item_name']); ?></h1>
                        <p class="item-price">$<?php echo htmlspecialchars(number_format($item['item_price'], 2)); ?></p>
                        <p class="item-description"><?php echo htmlspecialchars($item['item_desc']); ?></p>
                        <div class="item-meta">
                            <p><span>Date Added:</span> <?php echo htmlspecialchars(date('F j, Y', strtotime($item['item_add_date']))); ?></p>
                            <p><span>Country Made:</span> <?php echo htmlspecialchars($item['item_country_made']); ?></p>
                            <p><span>Status:</span> <?php echo htmlspecialchars($item['item_status']); ?></p>
                            <p><span>Rating:</span>
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= htmlspecialchars($item['item_rating'])) {
                                        echo '<i class="fas fa-star"  style="color: yellow;"></i>'; // Full star
                                    } else {
                                        echo '<i class="far fa-star"  style="color: black;"></i>'; // Empty star
                                    }
                                }
                                ?>
                            </p>
                            <p>
                                <span>Category:</span>
                            <form action="categories_items.php" method="post" style="display:inline;">
                                <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category_id); ?>">
                                <input type="hidden" name="cat_name" value="<?php echo htmlspecialchars($cat_name); ?>">
                                <button type="submit" style="background:none; border:none; padding:0; margin:0; text-decoration:none; color:inherit; cursor:pointer;">
                                    <?php echo htmlspecialchars($item['cat_name']); ?>
                                </button>
                            </form>
                            </p>

                            <p><span>Published BY:</span> <a href="publisher_profile.php" style="text-decoration: none;"><?php echo $item['user_id'] == $session_id ? "YOU" : htmlspecialchars($item['user_name']); ?></a></p>
                            <?php
                            // Fetch the cart from cookies
                            $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
                            ?>
                            <?php if (!isset($cart[$item['item_id']])) : ?>
                                <div>
                                    <form action="handle_cart.php?action=add" method="post">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                        <input type="hidden" name="redirect" value="item_page.php"> <!-- Redirect after add -->
                                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                                    </form>
                                </div>
                            <?php else : ?>
                                <div>
                                    <form action="cart.php" method="post">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                        <input type="hidden" name="redirect" value="cart.php"> <!-- Redirect to cart -->
                                        <p>Item added to cart</p>
                                        <button type="submit" class="btn btn-primary">Go to Cart</button>
                                    </form>
                                </div>
                            <?php endif; ?>


                        </div>
                        <a href="categories.php" class="btn btn-primary">Back to Home</a>
                    </div>
                </div>
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

                <!-- Comment Section -->
                <div class="comments-section">
                    <h2>Latest <?php echo htmlspecialchars(count($comments) > 0 ? count($comments) : "")  ?> Comments</h2>
                    <?php if ($comments) : ?>
                        <?php foreach ($comments as $comment) : ?>
                            <div class="comment">

                                <p class="comment-user">
                                    <?php if (!empty($comment['user_image'])) : ?>
                                        <img src="<?php echo htmlspecialchars($comment['user_image']); ?>" alt="User Image" class="user-avatar">
                                    <?php else : ?>
                                        <img src="./avatar.png" alt="Default Avatar" class="user-avatar">
                                    <?php endif; ?>
                                    <?php echo $comment['user_id'] == $session_id ? "You" : htmlspecialchars($comment['user_name']); ?> says:
                                </p>


                                <p class="comment-text"><?php echo htmlspecialchars($comment['comment_content']); ?></p>
                                <p class="comment-date"><?php echo htmlspecialchars(date('F j, Y', strtotime($comment['comment_date']))); ?></p>
                                <?php if ($comment['user_id'] == $session_id || $session_id == $item['user_id']) : ?>
                                    <div class="comment-actions">
                                        <div class="dropdown">
                                            <button class="dropdown-toggle">...</button>
                                            <div class="dropdown-menu">
                                                <button type="button" class="btn-edit" data-toggle="modal" data-target="#editCommentModal" data-id="<?php echo htmlspecialchars($comment['comment_id']); ?>" data-comment="<?php echo htmlspecialchars($comment['comment_content'], ENT_QUOTES, 'UTF-8'); ?>">Edit</button>


                                                <form action="handel_comment.php?action=delete" method="post" class="d-inline">
                                                    <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                                                    <input type="hidden" name="item_id" value="<?php echo $comment['item_id']; ?>">
                                                    <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this Comment?')">Delete</button>
                                                </form>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>No comments yet. Be the first to comment!</p>
                    <?php endif; ?>
                </div>

                <!-- Add Comment Form -->
                <div class="add-comment-form">
                    <h2>Add a Comment</h2>
                    <form action="handel_comment.php?action=add" method="post">
                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['item_id']); ?>">
                        <div class="form-group">
                            <label for="comment">Comment:</label>
                            <textarea id="comment" name="comment" class="form-control" required></textarea>
                        </div>
                        <?php if (isset($_SESSION['user_name'])) : ?>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        <?php else : ?>
                            <p>You need to <a href="login.php">login</a> first to add a comment.</p>
                        <?php endif; ?>
                    </form>
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
                            <form action="handel_comment.php?action=edit" method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="comment_id" id="comment_id">
                                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
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

                <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> -->
                <script>
                    $(document).ready(function() {
                        // Handle the edit comment modal
                        $('#editCommentModal').on('show.bs.modal', function(event) {
                            console.log('Modal show event triggered');

                            var button = $(event.relatedTarget); // Button that triggered the modal
                            var comment_id = button.data('id');
                            var comment_text = button.data('comment');

                            console.log('Comment ID:', comment_id);
                            console.log('Comment Text:', comment_text);

                            var modal = $(this);
                            modal.find('#comment_id').val(comment_id);
                            modal.find('#comment_text').val(comment_text);
                        });
                        // Handle dropdown menu visibility
                        $('.dropdown-toggle').click(function(e) {
                            e.stopPropagation(); // Prevent the click event from propagating to the document

                            var $dropdown = $(this).next('.dropdown-menu');
                            $('.dropdown-menu').not($dropdown).removeClass('show'); // Hide other open dropdowns
                            $dropdown.toggleClass('show'); // Toggle the clicked dropdown
                        });

                        // Hide dropdown menus if clicked outside
                        $(document).mouseup(function(e) {
                            var $dropdowns = $('.dropdown-menu');
                            if (!$dropdowns.is(e.target) && $dropdowns.has(e.target).length === 0) {
                                $dropdowns.removeClass('show');
                            }
                        });
                    });
                </script>



            <?php endforeach; ?>
        <?php else : ?>
            <p>Item not found.</p>
        <?php endif; ?>
    </div>
    <!-- <script src="./admin/includes/libraries/bootstrap.min.js"></script> -->


</body>


</html>