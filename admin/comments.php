<?php
ob_start();
session_name('admin_session'); // Use a distinct name for admin sessions
session_start();
$page_title = "Comments";
if (isset($_SESSION['admin_user_name'])) {
    include("./init.php");
    $action = isset($_GET['action']) ? $_GET['action'] : 'manage';
    if ($action == "manage") {
        $conn = connect_db();
        if (isset($_POST["item_id"])) {
            $item_id = $_POST["item_id"];
            $comments = getItems(
                $conn,
                "comments",
                "comment_id",
                "item_id",
                null,
                10000,
                [
                    [
                        'table' => 'items',
                        'condition' => 'comments.item_id = items.item_id',
                        'attribute' => 'item_name'
                    ],
                    [
                        'table' => 'users',
                        'condition' => 'comments.user_id = users.user_id',
                        'attribute' => 'user_name'
                    ]
                ],
                $item_id,
                "item_id"
            );
        } else {
            $comments = getItems(
                $conn,
                "comments",
                "comment_id",
                null,
                null,
                10000,
                [
                    [
                        'table' => 'items',
                        'condition' => 'comments.item_id = items.item_id',
                        'attribute' => 'item_name'
                    ],
                    [
                        'table' => 'users',
                        'condition' => 'comments.user_id = users.user_id',
                        'attribute' => 'user_name'
                    ]
                ],

            );
        }



?>
        <style>
            /* Reset some basic styles */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                color: #333;
            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }

            h1 {
                text-align: center;
                margin-bottom: 20px;
            }

            .comments-table {
                width: 100%;
                border-collapse: collapse;
                background-color: #fff;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .comments-table thead {
                background-color: #007bff;
                color: #fff;
            }

            .comments-table th,
            .comments-table td {
                padding: 12px;
                text-align: left;
            }

            .comments-table th {
                font-weight: bold;
            }

            .comments-table tbody tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .comments-table tbody tr:hover {
                background-color: #e9ecef;
            }

            .comments-table .status-approved {
                color: #28a745;
            }

            .comments-table .status-pending {
                color: #ffc107;
            }

            .comments-table .status-rejected {
                color: #dc3545;
            }

            .status-approved {
                color: green;
                font-weight: bold;
            }

            .status-disapproved {
                color: red;
                font-weight: bold;
            }

            .action-btns {
                display: flex;
                gap: 10px;
            }

            .btn-delete {
                padding: 5px 10px;
                border: none;
                border-radius: 3px;
                cursor: pointer;
                color: #fff;
                background-color: #d9534f;
            }

            .btn-delete:hover {
                background-color: #c9302c;
            }

            .btn-delete:disabled {
                background-color: #e0e0e0;
                cursor: not-allowed;
            }

            .no-comments {
                text-align: center;
                color: #888;
            }

            .alert {
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 5px;
                color: #fff;
                font-weight: bold;
            }

            .alert.success {
                background-color: #5bc0de;
            }

            .alert.error {
                background-color: #d9534f;
            }

            .alert.warning {
                background-color: #f0ad4e;
            }

            .alert.info {
                background-color: #5bc0de;
            }


            /* Responsive design */
            @media (max-width: 768px) {
                .comments-table thead {
                    display: none;
                }

                .comments-table,
                .comments-table tbody,
                .comments-table tr,
                .comments-table td {
                    display: block;
                    width: 100%;
                }

                .comments-table tr {
                    margin-bottom: 10px;
                    border: 1px solid #ddd;
                }

                .comments-table td {
                    padding: 10px;
                    text-align: right;
                    position: relative;
                    border-bottom: 1px solid #ddd;
                }

                .comments-table td::before {
                    content: attr(data-label);
                    position: absolute;
                    left: 0;
                    width: 50%;
                    padding-left: 10px;
                    font-weight: bold;
                    color: #333;
                    white-space: nowrap;
                }

                .comments-table td:last-child {
                    border-bottom: 0;
                }
            }
        </style>
        <title><?php getTitle(); ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <div class="container">
            <h1>Comments</h1>
            <?php if (isset($_SESSION['message'])) : ?>
                <div class="alert <?php echo $_SESSION['message_type']; ?>">
                    <?php echo $_SESSION['message']; ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <table class="comments-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Content</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Item</th>
                        <th>User</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($comments)) : ?>
                        <?php foreach ($comments as $comment) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($comment['comment_id']); ?></td>
                                <td><?php echo htmlspecialchars($comment['comment_content']); ?></td>
                                <td class="<?php echo htmlspecialchars($comment['comment_status']) == 1 ? 'status-approved' : 'status-disapproved'; ?>">
                                    <form action="comments.php?action=toggle_status" method="post" style="display:inline;">
                                        <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['comment_id']); ?>">
                                        <a href="#" onclick="this.closest('form').submit(); return false;" style="text-decoration:none; color:inherit;">
                                            <?php echo $comment['comment_status'] == 1 ? 'DeApprove' : 'Approve'; ?>
                                        </a>
                                    </form>
                                </td>
                                <td><?php echo htmlspecialchars($comment['comment_date']); ?></td>
                                <td><?php echo htmlspecialchars($comment['item_name']); ?></td>
                                <td><?php echo htmlspecialchars($comment['user_name']); ?></td>
                                <td class="action-btns">


                                    <!-- Edit button -->
                                    <button type="button" class="btn btn-primary mr-2" onclick="openModal('<?php echo htmlspecialchars($comment['comment_id']); ?>')">Edit</button>

                                    <!-- Edit Modal -->
                                    <div id="editModal<?php echo htmlspecialchars($comment['comment_id']); ?>" class="modal">
                                        <div class="modal-content">
                                            <span class="close" onclick="closeModal('<?php echo htmlspecialchars($comment['comment_id']); ?>')">&times;</span>
                                            <form action="comments.php?action=edit" method="post">
                                                <h2>Edit Comment Details</h2>

                                                <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['comment_id']); ?>">
                                                <!-- comment content -->
                                                <div class="form-group">
                                                    <label for="editCommentContent<?php echo htmlspecialchars($comment['comment_id']); ?>">Comment_content</label>
                                                    <input type="text" class="form-control" id="editCommentContent<?php echo htmlspecialchars($comment['comment_id']); ?>" name="comment_content" value="<?php echo htmlspecialchars($comment['comment_content']); ?>" required>
                                                </div>

                                                <!-- Submit Button -->
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Delete button -->
                                    <form id="deleteForm<?php echo htmlspecialchars($comment['comment_id']); ?>" action="comments.php?action=delete" method="post" style="display:inline;">
                                        <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['comment_id']); ?>">
                                        <button type="button" class="btn delete-btn" onclick="return confirmDelete(<?php echo htmlspecialchars($comment['comment_id']); ?>);"><i class="fas fa-trash"></i></button>
                                    </form>


                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="no-comments">No comments available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>



<?php
    } elseif ($action == "delete") {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // Get item ID
            $comment_id = $_POST['comment_id'] ?? null;
            if (empty($comment_id)) {
                $_SESSION['message'] = "comment_id  is missing.";
                $_SESSION['message_type'] = "error";
                header("location: comments.php?action=manage");
                exit;
            }

            $conn = connect_db();

            // Prepare and execute the DELETE statement
            $stmt = $conn->prepare("DELETE FROM comments WHERE comment_id = ?");
            $stmt->execute([$comment_id]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                $_SESSION['message'] = "Comments with ID $comment_id has been deleted.";
                $_SESSION['message_type'] = "success";
                header("location: comments.php?action=manage");
                exit();
            } else {
                $_SESSION['message'] = "Item deletion failed or Comment with ID $comment_id does not exist.";
                $_SESSION['message_type'] = "warning";
                header("location: comments.php?action=manage");
                exit();
            }
        } else {
            // Invalid request method
            $_SESSION['message'] = "Invalid request method. Expected POST.";
            $_SESSION['message_type'] = "error";
            header("location: comments.php?action=manage");
            exit;
        }
    } elseif ($action == "edit") {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $comment_id = $_POST['comment_id'] ?? null;
            $comment_content = htmlspecialchars(trim($_POST['comment_content']));
            if (empty($comment_content)) {
                $_SESSION["message"] = "Comment content must not be empty !";
                $_SESSION["message_type"] = "error";
                header("location: comments.php?action=manage");
                exit;
            }
            $conn = connect_db();
            $stm = $conn->prepare("UPDATE comments SET comment_content= :c_content WHERE comment_id= :c_id");
            $comment = $stm->execute([
                'c_content' => $comment_content,
                'c_id' => $comment_id
            ]);
            if ($comment) {
                $_SESSION["message"] = "COMMENT updated succesfully";
                $_SESSION["message_type"] = "success";
            } else {
                $_SESSION["message"] = "Comment Not Found";
                $_SESSION["message_type"] = "error";
            }
            header("location: comments.php?action=manage");
            exit;
        } else {
            $_SESSION["message"] = "EXPECTED POST REQUEST";
            $_SESSION["message_type"] = "error";
            header("location: comments.php?action=manage");
            exit;
        }
    } elseif ($action == "toggle_status") {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $comment_id = $_POST['comment_id'] ?? null;
            $conn = connect_db();
            $stm = $conn->prepare("SELECT comment_status FROM comments WHERE comment_id= :c_id");
            $stm->execute(['c_id' => $comment_id]);
            $comment = $stm->fetch(PDO::FETCH_ASSOC);
            if ($comment) {
                $new_status = $comment['comment_status'] == 1 ? 0 : 1;
                $stm = $conn->prepare("UPDATE comments SET comment_status= :c_status WHERE comment_id= :c_id");
                $stm->execute([
                    'c_status' => $new_status,
                    'c_id' => $comment_id
                ]);
                $_SESSION['message'] = $new_status == 1 ? 'Comment Approved successfully.' : 'Comment Disapproved successfully.';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Comment not found.';
                $_SESSION['message_type'] = 'error';
            }
            header("location: comments.php?action=manage");
            exit;
        } else {
            $_SESSION["message"] = "EXPECTED POST REQUEST";
            $_SESSION["message_type"] = "error";
            header("location: comments.php?action=manage");
            exit;
        }
    } else {
        header("location: comments.php?action=manage");
        exit;
    }
} else {
    header("location:index.php");
    exit;
}
ob_end_flush();
?>




<script>
    function confirmDelete(comment_id) {
        if (confirm("Are you sure you want to delete this Comment?")) {
            // Proceed with form submission
            document.getElementById('deleteForm' + comment_id).submit();
        } else {
            // Prevent form submission
            return false;
        }
    }
    // Function to open modal
    function openModal(commentId) {
        var modal = document.getElementById('editModal' + commentId);
        modal.style.display = 'block';
    }

    // Function to close modal
    function closeModal(commentId) {
        var modal = document.getElementById('editModal' + commentId);
        modal.style.display = 'none';
    }
    // Close the modal when clicking outside of it
    /*    window.onclick = function(event) {
           if (event.target.className === 'modal') {
               closeModal(event.target.id.replace('editModal', ''));
           }
       } */
</script>