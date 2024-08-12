<?php
session_name('user_session');
session_start();
include "./init.php";
$page_title = "home";

?>

<style>
    body,
    html {
        margin: 0;
        padding: 0;
    }

    .hero {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        height: 100vh;
        /* Full viewport height */
        background: url('your-hero-image.jpg') no-repeat center center;
        background-size: cover;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        /* Dark overlay for text readability */
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        padding: 20px;
        max-width: 600px;
        /* Adjust as needed */
    }

    .hero h1 {
        font-size: 3rem;
        margin-bottom: 20px;
        font-weight: bold;
    }

    .hero p {
        font-size: 1.25rem;
        margin-bottom: 30px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        font-size: 1.25rem;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s, border-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    @media (max-width: 768px) {
        .hero h1 {
            font-size: 2rem;
        }

        .hero p {
            font-size: 1rem;
        }
    }
</style>

<title><?php getTitle(); ?></title>

<!-- Hero Section -->
<div class="hero">
    <div class="hero-content">
        <h1>Welcome to Our Shop</h1>
        <p>Discover the best products at unbeatable prices</p>
        <a href="categories.php" class="btn btn-primary btn-lg">Shop Now</a>
    </div>
</div>