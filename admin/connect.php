
<?php

function connect_db()
{
    try {
        $conn = new PDO("mysql:host=localhost;dbname=shop", "root", "");
        // Set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // echo "Connected successfully TO DATABASE";
        return $conn;
    } catch (PDOException $e) {
        echo "Connection failed to Database: " . $e->getMessage();
    }
}

// Test fun
// var_dump(connect_db());
?>