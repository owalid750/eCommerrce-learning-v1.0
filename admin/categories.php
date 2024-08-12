<?php
ob_start();
session_name('admin_session'); // Use a distinct name for admin sessions
session_start();

$page_title = "Categories";
if (isset($_SESSION['admin_user_name'])) {
    include("./init.php");
    $action = isset($_GET["action"]) ? $_GET["action"] : "manage";
    if ($action == "manage") {
        $conn = connect_db();
        $categories = getItems($conn, "categories", null, null, null, 100);
?>
        <title><?php getTitle() ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f5f5f5;
                margin: 0;
                padding: 0;
            }

            .admin-categories {
                max-width: 1200px;
                margin: 20px auto;
                padding: 20px;
                background-color: #fff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
            }

            h2 {
                font-size: 1.5em;
                margin-bottom: 20px;
            }

            .category-form {
                margin-bottom: 40px;
            }

            .category-form form {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .form-group {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            label {
                font-weight: bold;
                color: #333;
            }

            input[type="text"],
            input[type="number"],
            textarea {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 1em;
            }

            textarea {
                resize: vertical;
            }

            input[type="checkbox"] {
                width: auto;
                margin-top: 7px;
            }

            .submit-btn {
                padding: 10px 15px;
                background-color: #28a745;
                color: #fff;
                border: none;
                border-radius: 4px;
                font-size: 1em;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .submit-btn:hover {
                background-color: #218838;
            }

            .category-cards {
                margin-top: 40px;
            }

            .cards-container {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 20px;
            }

            .card {
                background-color: #fff;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                display: flex;
                flex-direction: column;
            }

            .card-header {
                background-color: #f7f7f7;
                padding: 10px 15px;
                border-bottom: 1px solid #ddd;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .card-header h3 {
                margin: 0;
                font-size: 1.2em;
            }

            .card-body {
                padding: 15px;
                flex-grow: 1;
            }

            .card-body p {
                margin: 10px 0;
            }

            .card-footer {
                padding: 10px 15px;
                background-color: #f7f7f7;
                border-top: 1px solid #ddd;
                display: flex;
                justify-content: flex-end;
                gap: 10px;
            }

            .card button {
                padding: 5px 10px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .edit-btn {
                background-color: #ffc107;
                color: #fff;
            }

            .edit-btn:hover {
                background-color: #e0a800;
            }

            .delete-btn {
                background-color: #dc3545;
                color: #fff;
            }

            .delete-btn:hover {
                background-color: #c82333;
            }

            .message {
                padding: 10px;
                margin: 10px 0;
                border-radius: 4px;
            }

            .message.success {
                background-color: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }

            .message.error {
                background-color: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }

            .items-form {
                display: inline-block;
                position: relative;
            }

            .items-link {
                text-decoration: none;
                color: #007bff;
                font-weight: bold;
                padding: 5px 10px;
                border: 1px solid #007bff;
                border-radius: 4px;
                transition: background-color 0.3s, color 0.3s;
                display: inline-flex;
                align-items: center;
                position: relative;
            }

            .items-link:hover {
                background-color: #007bff;
                color: #fff;
            }

            .items-link i {
                margin-right: 5px;
            }

            .tooltip {
                display: none;
                position: absolute;
                top: -45px;
                /* Adjusted position */
                left: 50%;
                transform: translateX(-50%);
                background-color: #333;
                color: #fff;
                padding: 5px 8px;
                border-radius: 4px;
                font-size: 12px;
                white-space: normal;
                /* Allow text to wrap */
                max-width: 200px;
                /* Adjust as needed */
                z-index: 1000;
                opacity: 0;
                transition: opacity 0.3s;
            }

            .items-link:hover .tooltip {
                display: block;
                opacity: 1;
            }

            @media (max-width: 1024px) {
                .cards-container {
                    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                }
            }

            @media (max-width: 768px) {
                .cards-container {
                    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                }
            }

            @media (max-width: 480px) {
                .cards-container {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        <div class="admin-categories">
            <?php
            if (isset($_SESSION['message'])) {
                $message = $_SESSION['message'];
                $message_type = $_SESSION['message_type'];

                echo '<div class="message ' . htmlspecialchars($message_type) . '">';
                echo htmlspecialchars($message);
                echo '</div>';

                // Clear message from session
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
            ?>

            <section class="category-form">
                <h2>Add/Edit Category</h2>
                <form method="post" action="categories.php?action=insert">
                    <div class="form-group">
                        <label for="cat_name">Category Name:</label>
                        <input type="text" id="cat_name" name="cat_name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="ordering">Ordering:</label>
                        <input type="number" id="ordering" name="ordering" required>
                    </div>
                    <div class="form-group">
                        <label for="visibility">Visibility:</label>
                        <input type="checkbox" id="visibility" name="visibility">
                    </div>
                    <div class="form-group">
                        <label for="allow_comment">Allow Comments:</label>
                        <input type="checkbox" id="allow_comment" name="allow_comment">
                    </div>
                    <div class="form-group">
                        <label for="allow_ads">Allow Ads:</label>
                        <input type="checkbox" id="allow_ads" name="allow_ads">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="submit-btn">Add</button>
                    </div>
                </form>
            </section>
            <section class="category-cards">
                <h2>Category List</h2>

                <div class="cards-container">
                    <!-- Example card -->
                    <?php if (empty($categories)) : ?>
                        <tr>
                            <td colspan="4">No Category Created Yet.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($categories as $cat) : ?>
                            <div class="card">
                                <div class="card-header">
                                    <h3><?php echo htmlspecialchars($cat['cat_name']); ?></h3>
                                    <span>ID: <?php echo htmlspecialchars($cat['category_id']); ?></span>
                                </div>
                                <div class="card-body">
                                    <p>Description: <?php echo htmlspecialchars($cat['description']); ?> </p>
                                    <p>Ordering: <?php echo htmlspecialchars($cat['ordering']); ?></p>
                                    <p>Visibility: <?php echo htmlspecialchars($cat['visibility']) == 1 ? "YES" : "NO"; ?></p>
                                    <p>Allow Comments: <?php echo htmlspecialchars($cat['allow_comment']) == 1 ? "YES" : "NO"; ?></p>
                                    <p>Allow Ads: <?php echo htmlspecialchars($cat['allow_ads']) == 1 ? "YES" : "NO"; ?></p>
                                </div>
                                <div class="card-footer">
                                    <!-- Edit button -->
                                    <button type="button" class="btn btn-primary mr-2" onclick="openModal('<?php echo htmlspecialchars($cat['category_id']); ?>')">Edit</button>
                                    <!-- Edit Modal -->
                                    <div id="editModal<?php echo htmlspecialchars($cat['category_id']); ?>" class="modal">
                                        <div class="modal-content">
                                            <span class="close" onclick="closeModal('<?php echo htmlspecialchars($cat['category_id']); ?>')">&times;</span>
                                            <form action="categories.php?action=edit" method="post">
                                                <h2>Edit Category Details</h2>
                                                <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($cat['category_id']); ?>">
                                                <!-- cat_name -->
                                                <div class="form-group">
                                                    <label for="editCategoryName<?php echo htmlspecialchars($cat['category_id']); ?>">Category Name</label>
                                                    <input type="text" class="form-control" id="editCategoryName<?php echo htmlspecialchars($cat['category_id']); ?>" name="cat_name" value="<?php echo htmlspecialchars($cat['cat_name']); ?>" required>
                                                </div>
                                                <!-- description -->
                                                <div class="form-group">
                                                    <label for="editCategoryDescription<?php echo htmlspecialchars($cat['category_id']); ?>">Description</label>
                                                    <textarea class="form-control" id="editCategoryDescription<?php echo htmlspecialchars($cat['category_id']); ?>" name="description" required><?php echo htmlspecialchars($cat['description']); ?> </textarea>
                                                </div>
                                                <!-- ordering -->
                                                <div class="form-group">
                                                    <label for="editCategoryOrdering<?php echo htmlspecialchars($cat['category_id']); ?>">Ordering</label>
                                                    <input type="number" class="form-control" id="editCategoryOrdering<?php echo htmlspecialchars($cat['category_id']); ?>" name="ordering" value="<?php echo htmlspecialchars($cat['ordering']); ?>" required>
                                                </div>
                                                <!-- visibility -->
                                                <div class="form-group">
                                                    <label for="editCategoryVisibility<?php echo htmlspecialchars($cat['category_id']); ?>">Visibility</label>
                                                    <input type="checkbox" id="editCategoryVisibility<?php echo htmlspecialchars($cat['category_id']); ?>" name="visibility" <?php echo ($cat['visibility'] ? 'checked' : ''); ?>>
                                                </div>
                                                <!-- allow_comment -->
                                                <div class="form-group">
                                                    <label for="editCategoryAllowComment<?php echo htmlspecialchars($cat['category_id']); ?>">Allow Comments</label>
                                                    <input type="checkbox" id="editCategoryAllowComment<?php echo htmlspecialchars($cat['category_id']); ?>" name="allow_comment" <?php echo ($cat['allow_comment'] ? 'checked' : ''); ?>>
                                                </div>
                                                <!-- allow_ads -->
                                                <div class="form-group">
                                                    <label for="editCategoryAllowAds<?php echo htmlspecialchars($cat['category_id']); ?>">Allow Ads</label>
                                                    <input type="checkbox" id="editCategoryAllowAds<?php echo htmlspecialchars($cat['category_id']); ?>" name="allow_ads" <?php echo ($cat['allow_ads'] ? 'checked' : ''); ?>>
                                                </div>
                                                <!-- Submit Button -->
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </form>
                                        </div>
                                    </div>
                                    <?php
                                    if (empty($_SESSION['csrf_token'])) {
                                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                    }
                                    ?>
                                    <!-- Delete button -->
                                    <form id="deleteForm<?php echo htmlspecialchars($cat['category_id']); ?>" action="categories.php?action=delete" method="post" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($cat['category_id']); ?>">
                                        <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo htmlspecialchars($cat['category_id']); ?>)">Delete</button>
                                    </form>
                                    <!-- show items -->
                                    <form action="items.php?action=manage" method="post" class="items-form">
                                        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($cat['category_id']); ?>">
                                        <a href="#" onclick="this.closest('form').submit(); return false;" class="items-link">
                                            <i class="fas fa-box-open"></i>
                                            <span class="tooltip">Show items for this category</span>
                                        </a>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <!-- Additional cards will be added here -->
                </div>

            </section>
        </div>

<?php
    } elseif ($action == "insert") {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // Sanitize and validate inputs
            $cat_name = htmlspecialchars(trim($_POST['cat_name']));
            $cat_desc = htmlspecialchars(trim($_POST['description']));
            $cat_ordering = htmlspecialchars(trim($_POST['ordering']));
            $visibility = isset($_POST['visibility']) ? 1 : 0; // Checkbox values are either 1 (checked) or 0 (unchecked)
            $allow_comment = isset($_POST['allow_comment']) ? 1 : 0;
            $allow_ads = isset($_POST['allow_ads']) ? 1 : 0;
            // Check if required fields are not empty
            if (empty($cat_name) || empty($cat_ordering) || empty($cat_desc)) {
                $_SESSION['message'] = "Category name , Category Desc and  ordering are required.";
                $_SESSION['message_type'] = "error";
                header("Location: categories.php?action=manage");
                exit;
            }
            try {
                $pdo = connect_db();
                $result = checkItem($pdo, "cat_name", "categories", $cat_name);
                if ($result['item_exist'] == false) {
                    $stmt = $pdo->prepare("
                INSERT INTO categories (cat_name, description, ordering, visibility, allow_comment, allow_ads) 
                VALUES (:cat_name, :description, :ordering, :visibility, :allow_comment, :allow_ads)
            ");
                    $stmt->bindParam(':cat_name', $cat_name);
                    $stmt->bindParam(':description', $cat_desc);
                    $stmt->bindParam(':ordering', $cat_ordering, PDO::PARAM_INT);
                    $stmt->bindParam(':visibility', $visibility, PDO::PARAM_INT);
                    $stmt->bindParam(':allow_comment', $allow_comment, PDO::PARAM_INT);
                    $stmt->bindParam(':allow_ads', $allow_ads, PDO::PARAM_INT);

                    // Execute the query
                    $stmt->execute();

                    // Set success message
                    $_SESSION['message'] = "Category added successfully.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "THE CategoryName: $cat_name Already Exist";
                    $_SESSION['message_type'] = "error";
                    header("location: categories.php?action=manage");
                    exit;
                }
            } catch (PDOException $e) {
                // Set error message
                $_SESSION['message'] = "Error: " . $e->getMessage();
                $_SESSION['message_type'] = "error";
            }
            // Redirect after processing
            header("Location: categories.php?action=manage");
            exit;
        } else {
            $_SESSION['message'] = "TRY HARDER NEXT TIME :)";
            $_SESSION['message_type'] = "error";
            header("location: categories.php?action=manage");
            exit;
        }
    } elseif ($action == "edit") {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // Retrieve and sanitize inputs
            $cat_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
            $cat_name = htmlspecialchars(trim($_POST['cat_name']));
            $cat_desc = htmlspecialchars(trim($_POST['description']));
            $cat_ordering = htmlspecialchars(trim($_POST['ordering']));
            $visibility = isset($_POST['visibility']) ? 1 : 0;
            $allow_comment = isset($_POST['allow_comment']) ? 1 : 0;
            $allow_ads = isset($_POST['allow_ads']) ? 1 : 0;

            // Check if required fields are not empty
            if (empty($cat_name) || empty($cat_desc) || intval($cat_ordering) == 0) {
                $_SESSION['message'] = "Category description, ordering  and name are required.";
                $_SESSION['message_type'] = "error";
                header("Location: categories.php?action=manage");
                exit;
            }
            $pdo = connect_db();
            // Prepare and execute UPDATE statement
            $result = checkItem($pdo, "cat_name", "categories", $cat_name, $cat_id,"category_id");
            try {
                if ($result['item_exist'] == false) {
                    $stmt = $pdo->prepare("
                    UPDATE categories 
                    SET cat_name = :cat_name, 
                        description = :description, 
                        ordering = :ordering, 
                        visibility = :visibility, 
                        allow_comment = :allow_comment, 
                        allow_ads = :allow_ads
                    WHERE category_id = :category_id
                ");
                    $stmt->bindParam(':cat_name', $cat_name);
                    $stmt->bindParam(':description', $cat_desc);
                    $stmt->bindParam(':ordering', $cat_ordering, PDO::PARAM_INT);
                    $stmt->bindParam(':visibility', $visibility, PDO::PARAM_INT);
                    $stmt->bindParam(':allow_comment', $allow_comment, PDO::PARAM_INT);
                    $stmt->bindParam(':allow_ads', $allow_ads, PDO::PARAM_INT);
                    $stmt->bindParam(':category_id', $cat_id, PDO::PARAM_INT);

                    // Execute the query
                    $stmt->execute();

                    // Check if any rows were affected
                    if ($stmt->rowCount() > 0) {
                        $_SESSION['message'] = "Category updated successfully.";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $_SESSION['message'] = "No changes were made or category not found.";
                        $_SESSION['message_type'] = "error";
                    }
                } else {
                    $_SESSION['message'] = "THE CategoryName: $cat_name Already Exist";
                    $_SESSION['message_type'] = "error";
                    header("location: categories.php?action=manage");
                    exit;
                }
            } catch (PDOException $e) {
                // Set error message
                $_SESSION['message'] = "Error: " . $e->getMessage();
                $_SESSION['message_type'] = "error";
            }

            // Redirect after processing
            header("Location: categories.php?action=manage");
            exit;
        } else {
            $_SESSION['message'] = "TRY HARDER NEXT TIME :)";
            $_SESSION['message_type'] = "error";
            header("location: categories.php?action=manage");
            exit;
        }
    } elseif ($action == "delete") {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['message'] = "TRY HARDER NEXT TIME :)";
                $_SESSION['message_type'] = "error";
                header("location: categories.php?action=manage");
                exit;
            }
            // Get category ID
            $category_id = $_POST['category_id'] ?? null;
            if (empty($category_id)) {
                $_SESSION['message'] = "Category ID is missing.";
                $_SESSION['message_type'] = "error";
                header("location: categories.php?action=manage");
                exit;
            }
            $conn = connect_db();

            // Prepare and execute the DELETE statement
            $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
            $stmt->execute([$category_id]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                $_SESSION['message'] = "Category with ID $category_id has been deleted.";
                $_SESSION['message_type'] = "success";
                header("location: categories.php?action=manage");
                exit();
            } else {
                $_SESSION['message'] = "Category deletion failed or category with ID $Category does not exist.";
                $_SESSION['message_type'] = "warning";
                header("location: categories.php?action=manage");
                exit();
            }
        } else {
            // echo "Invalid request method. Expected POST.";
            $_SESSION['message'] = "TRY HARDER NEXT TIME :)";
            $_SESSION['message_type'] = "error";
            header("location: categories.php?action=manage");
            exit;
        }
    }
} else {
    header("location: index.php");
    exit;
}
ob_end_flush();


?>


<script>
    function confirmDelete(category_id) {
        if (confirm("Are you sure you want to delete this Category?")) {
            // Proceed with form submission
            document.getElementById('deleteForm' + category_id).submit();
        } else {
            // Do nothing or handle cancel action
        }
    }

    function openModal(id) {
        document.getElementById('editModal' + id).style.display = 'block';
    }

    function closeModal(id) {
        document.getElementById('editModal' + id).style.display = 'none';
    }

    // Close the modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            closeModal(event.target.id.replace('editModal', ''));
        }
    }
</script>