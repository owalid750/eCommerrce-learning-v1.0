
<?php

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'home':
            echo "<h1>Welcome to the Home Page</h1>";
            break;
        case 'edit':
            echo "<h1>Welcome to the Edit Page</h1>";
            break;
        case 'insert':
            echo "<h1>Welcome to the Insert Page</h1>";
            break;
        default:
            echo "<h1>Page not found</h1>";
    }
} else {
    echo "<h1>Welcome to the Home Page</h1>";
}
?>