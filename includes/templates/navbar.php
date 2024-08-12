<?php
$conn = connect_db();
$categories = getItems($conn, "categories", "category_id", null, null, 2000, [], null, null, ["visibility" => 1]);
?>

<style>
    /* Basic reset */
    body,
    ul,
    li,
    a,
    input,
    button {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    /* Scoped Navbar Styles */
    .custom-navbar {
        background-color: #333;
        color: #fff;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .custom-navbar .container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }

    .custom-navbar .navbar-brand {
        font-size: 1.75rem;
        font-weight: bold;
        text-decoration: none;
        color: #fff;
    }

    .custom-navbar .navbar-nav {
        list-style: none;
        display: flex;
        align-items: center;
        margin: 0;
        padding: 0;
    }

    .custom-navbar .navbar-nav .nav-item {
        position: relative;
    }


    .custom-navbar .navbar-nav .nav-link {
        color: #fff;
        text-decoration: none;
        padding: 10px 15px;
        display: block;
        transition: background-color 0.3s, color 0.3s;
    }

    .custom-navbar .navbar-nav .nav-link:hover {
        background-color: #555;
        border-radius: 5px;
    }

    .custom-navbar .search-form {
        display: flex;
        align-items: center;
    }

    .custom-navbar .search-form input {
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-right: 5px;
        color: black
    }

    .custom-navbar .search-form button {
        background-color: #444;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .custom-navbar .search-form button:hover {
        background-color: #555;

    }

    .custom-navbar .navbar-actions {
        display: flex;
        align-items: center;
    }

    .custom-navbar .navbar-actions .nav-link {
        color: #fff;
        margin-left: 15px;
        font-size: 1.25rem;
        text-decoration: none;
        position: relative;
    }

    .custom-navbar .navbar-actions .cart .badge {
        background-color: #e74c3c;
        color: #fff;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 0.75rem;
        position: absolute;
        top: -5px;
        right: -10px;
    }

    .custom-navbar .navbar-toggler {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #fff;
        cursor: pointer;
    }

    .custom-navbar .navbar-nav.show {
        display: flex;
        flex-direction: column;
    }

    /* Updated Dropdown Styles */
    .custom-navbar .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
        list-style: none;
        padding: 10px 0;
        margin: 0;
        min-width: 200px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .custom-navbar .dropdown-menu .dropdown-item {
        color: #333;
        text-decoration: none;
        padding: 10px 20px;
        display: block;
        transition: background-color 0.3s, color 0.3s;
    }

    .custom-navbar .dropdown-menu .dropdown-item:hover {
        background-color: #f1f1f1;
        color: #007bff;
    }

    .custom-navbar .nav-item:hover .dropdown-menu {
        display: block;
    }

    .dropdown-item-custom {
        display: block;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #fff;
        color: #333;
        text-decoration: none;
        font-size: 16px;
        font-weight: 500;
        transition: background-color 0.3s, color 0.3s, border-color 0.3s, transform 0.3s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .dropdown-item-custom:hover,
    .dropdown-item-custom:focus {
        background-color: #f8f9fa;
        color: #007bff;
        border-color: #007bff;
        outline: none;
        transform: translateX(5px);
    }

    .category-name {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
        color: #333;
    }

    .category-description {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .dropdown-menu.show {
        animation: fadeIn 0.3s ease-out;
    }

    .hover-effect {
        display: inline;
        color: #fff;
        text-decoration: none;
        cursor: pointer;
    }

    .hover-effect:hover {
        background-color: #555;
        border-radius: 5px;
    }

    /* Styling for the search suggestions container */
    .search-form {
        position: relative;
        display: flex;
        align-items: center;
    }

    .suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: #fff;
        color: black;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        /* Hidden by default */
    }

    .suggestion-item {
        padding: 10px 15px;
        border-bottom: 1px solid #ddd;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .suggestion-item:last-child {
        border-bottom: none;
    }

    .suggestion-item:hover {
        background-color: #f1f1f1;
    }

    .suggestion-item strong {
        font-weight: bold;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .custom-navbar .navbar-nav {
            display: none;
            flex-direction: column;
            width: 100%;
            background-color: #333;
            padding: 10px;
        }

        .custom-navbar .navbar-nav.show {
            display: flex;
        }

        .custom-navbar .navbar-toggler {
            display: block;
        }

        .custom-navbar .search-form {
            margin-top: 10px;
        }

        .custom-navbar .navbar-actions {
            margin-top: 10px;
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<nav class="custom-navbar">
    <div class="container">
        <button class="navbar-toggler" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <a href="index.php" class="navbar-brand">YourLogo</a>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="index.php" class="nav-link">Home</a>
            </li>
            <li class="nav-item dropdown">
                <a href="categories.php" class="nav-link dropdown-toggle">Categories</a>
                <ul class="dropdown-menu">
                    <?php foreach ($categories as $category) : ?>
                        <li>
                            <form action="categories_items.php" method="post" style="display: inline; border: none; padding: 0; margin: 0;">
                                <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category['category_id']); ?>">
                                <input type="hidden" name="cat_name" value="<?php echo htmlspecialchars($category['cat_name']); ?>">
                                <button type="submit" class="dropdown-item-custom">
                                    <div class="category-name"><?php echo htmlspecialchars($category['cat_name']); ?></div>
                                </button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li class="nav-item">
                <a href="categories.php" class="nav-link">Shop</a>
            </li>
            <li>
                <div class="navbar-actions">
                    <?php if (!isset($_SESSION['user_name'])) : ?>
                        <a href="login.php" class="nav-link login"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="register.php" class="nav-link signup"><i class="fas fa-user-plus"></i> Signup</a>

                    <?php else : ?>
                        <form action="profile.php" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                            <button type="submit" class="btn btn-link nav-link" style="display:inline;">
                                <i class="fas fa-user"></i> Account
                            </button>
                        </form>
                        <form action="manage.php" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                            <button type="submit" class="btn btn-link nav-link" style="display:inline;">
                                <i class="fas fa-cogs"></i> Manage
                            </button>
                        </form>
                        <!-- implemt featuse add to cart -->

                    <?php endif; ?>
                </div>
            </li>
        </ul>
        <!-- search feature -->
        <?php $cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
        ?>
        <a href="cart.php" class="nav-link cart" style="text-decoration: none;">
            <i class="fas fa-shopping-cart"></i>
            Cart <span class="badge"><?php echo count($cart); ?></span>
        </a>
        <div class="search-form">
            <form action="search_result.php" method="get">
                <input type="text" name="query" id="search-input" placeholder="Search products..." autocomplete="off">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <div class="suggestions" id="suggestions"></div>
        </div>


        <?php if (isset($_SESSION['user_name'])) : ?>
            <form action="logout.php" method="POST" style="display:inline;" onsubmit="confirmLogout(event);">
                <button type="submit" class="btn btn-link nav-link logout hover-effect" style="display:inline; color: #fff;
        text-decoration: none;cursor:pointer;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        <?php endif; ?>
    </div>
</nav>


<script>
    document.querySelector('.navbar-toggler').addEventListener('click', function() {
        document.querySelector('.navbar-nav').classList.toggle('show');
    });

    function confirmLogout(event) {
        if (!confirm('Are you sure you want to logout?')) {
            event.preventDefault();
        }
    }
</script>
<!-- handle search -->
<script>
    document.getElementById('search-input').addEventListener('input', function() {
        const query = this.value;
        const suggestionsDiv = document.getElementById('suggestions');

        if (query.length > 1) {
            fetch('suggestions.php?query=' + encodeURIComponent(query))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    suggestionsDiv.innerHTML = ''; // Clear previous suggestions
                    if (data.length > 0) {
                        suggestionsDiv.style.display = 'block'; // Show suggestions
                        data.forEach(item => {
                            const suggestionItem = document.createElement('div');
                            suggestionItem.className = 'suggestion-item';
                            suggestionItem.textContent = item.item_name;
                            suggestionItem.dataset.query = item.item_name; // Store the query for redirection
                            suggestionItem.addEventListener('click', function() {
                                const searchQuery = encodeURIComponent(this.dataset.query);
                                window.location.href = `search_result.php?query=${searchQuery}`;
                            });
                            suggestionsDiv.appendChild(suggestionItem);
                        });
                    } else {
                        suggestionsDiv.style.display = 'none'; // Hide suggestions if no data
                    }
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        } else {
            suggestionsDiv.style.display = 'none'; // Hide suggestions if query is too short
        }
    });
</script>