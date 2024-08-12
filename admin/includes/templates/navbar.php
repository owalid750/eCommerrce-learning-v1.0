<style>
    /* Navbar container */
    .navbar {
        background-color: #2c3e50;
        border: none;
        border-radius: 0;
        margin-bottom: 0;
        min-height: 60px;
    }

    /* Navbar header */
    .navbar-header {
        padding: 10px 15px;
    }

    /* Navbar brand */
    .navbar-brand {
        color: #ecf0f1;
        font-size: 24px;
        font-weight: bold;
        text-transform: uppercase;
    }

    /* Navbar links */
    .navbar-nav>li>a {
        color: #ecf0f1;
        font-size: 16px;
        padding: 20px 15px;
    }

    .navbar-nav>li>a:hover,
    .navbar-nav>li>a:focus {
        background-color: #34495e;
    }

    /* Toggle button */
    .navbar-toggle {
        background-color: #34495e;
        border: none;
    }

    .navbar-toggle .icon-bar {
        background-color: #ecf0f1;
    }

    /* Dropdown menu */
    .dropdown-menu {
        background-color: #2c3e50;
        border: none;
    }

    .dropdown-menu>li>a {
        color: #ecf0f1;
        padding: 10px 20px;
    }

    .dropdown-menu>li>a:hover,
    .dropdown-menu>li>a:focus {
        background-color: #34495e;
    }

    /* Dropdown toggle */
    .dropdown-toggle {
        color: #ecf0f1;
        padding: 15px 20px;
    }

    /* Caret icon */
    .caret {
        border-top-color: #ecf0f1;
        border-bottom-color: #ecf0f1;
    }
</style>

<nav class="navbar navbar-inverse">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app_nav" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="admin_dashboard.php">
                <?php echo lang("ADMIN-HOME") ?>
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app_nav">
            <ul class="nav navbar-nav">
                <li><a href="categories.php"><?php echo lang("CATEGORIES") ?></a></li>
                <li><a href="items.php"><?php echo lang("ITEMS") ?></a></li>
                <li><a href="members.php?action=manage"><?php echo lang("MEMBERS") ?></a></li>
                <li><a href="comments.php"><?php echo lang("COMMENTS") ?></a></li>
                <li><a href="#"><?php echo lang("STATISTICS") ?></a></li>
                <li><a href="#"><?php echo lang("LOGS") ?></a></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <?php echo $_SESSION['admin_user_name'] ?> <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="../index.php" target="_blank">Visit Shop</a></li>
                        <li><a href="members.php?action=edit&user_id=<?php echo $_SESSION['admin_user_id'] ?>"><?php echo lang("EDIT-PROFILE") ?></a></li>
                        <li><a href="#"><?php echo lang("SETTING") ?></a></li>
                        <li>
                            <a href="#" id="logoutButton" class="btn btn-link" style="color: #000; text-decoration: none;"><?php echo lang("LOG-OUT") ?></a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- *** task make the confermation loug out aret resposive  -->
<!-- Custom Confirmation Dialog -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Confirm Logout</h2>
        <p>Are you sure you want to log out?</p>
        <button id="confirmLogout" class="btn btn-danger">Yes, Logout</button>
        <button id="cancelLogout" class="btn btn-secondary">Cancel</button>
    </div>
</div>

<!-- Hidden Logout Form -->
<form id="logoutForm" action="logout.php" method="post" style="display: none;">
    <input type="hidden" name="logout" value="1">
</form>

<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 400px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .close-button {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close-button:hover,
    .close-button:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        margin: 10px 5px;
        text-align: center;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        font-size: 16px;
        cursor: pointer;
    }

    .btn-danger {
        background-color: #d9534f;
        color: #fff;
    }

    .btn-danger:hover {
        background-color: #c9302c;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: #fff;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    @media (max-width: 768px) {
        .modal-content {
            width: 90%;
            margin: 20% auto;
            padding: 15px;
        }

        .btn {
            width: 100%;
            margin: 5px 0;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const logoutButton = document.getElementById('logoutButton');
        const logoutModal = document.getElementById('logoutModal');
        const closeButton = document.querySelector('.close-button');
        const confirmLogout = document.getElementById('confirmLogout');
        const cancelLogout = document.getElementById('cancelLogout');
        const logoutForm = document.getElementById('logoutForm');

        logoutButton.addEventListener('click', () => {
            logoutModal.style.display = 'block';
        });

        closeButton.addEventListener('click', () => {
            logoutModal.style.display = 'none';
        });

        cancelLogout.addEventListener('click', () => {
            logoutModal.style.display = 'none';
        });

        confirmLogout.addEventListener('click', () => {
            logoutForm.submit();
        });

        window.addEventListener('click', (event) => {
            if (event.target == logoutModal) {
                logoutModal.style.display = 'none';
            }
        });
    });
</script>