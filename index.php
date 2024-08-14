<?php
session_name('user_session');
session_start();
include "./init.php";
$page_title = "Home";
$conn = connect_db();
$items = getItems($conn, "items", "item_id", null, null, 3, [
    [
        'table' => 'categories',
        'condition' => 'items.cat_id = categories.category_id',
        'attribute' => 'cat_name'
    ],
], null, null, [
    "is_item_approved" => 1,
    "item_rating" => 2
]);

$comments = getItems($conn, "comments", "comment_id", null, null, 5, [
    [
        'table' => 'users',
        'condition' => 'comments.user_id = users.user_id',
        'attribute' => 'user_name,user_image'
    ]
])
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php getTitle(); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        /* Harmonious color palette */
        :root {
            --primary-color: #4a90e2;
            /* Calm blue */
            --secondary-color: #50e3c2;
            /* Soft teal */
            --accent-color: #f5a623;
            /* Warm orange */
            --background-color: #f7f9fb;
            /* Light background */
            --text-color: #333;
            /* Dark grey for text */
            --text-light-color: #7f8c8d;
            /* Muted grey for secondary text */
            --border-color: #e0e0e0;
            /* Light border color */
        }

        .hero {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            height: 100vh;
            /* background: url('Modern Minimal E-Commerce Logo.png') no-repeat center center; */
            background-color: white;
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
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 20px;
            max-width: 700px;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            font-weight: 700;
            line-height: 1.2;
            color: white;
            /* Warm orange for the main heading */
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 30px;
            line-height: 1.5;
            color: #fff;
            /* White for readability against dark background */
        }

        .btn-primary {
            background-color: var(--primary-color);
            /* Calm blue for primary actions */
            border: none;
            color: #fff;
            padding: 15px 25px;
            font-size: 1.25rem;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s, box-shadow 0.3s;
            display: inline-block;
        }

        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 10px;
            vertical-align: middle;
        }

        .btn-primary:hover {
            background-color: #357ABD;
            /* Slightly darker blue for hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .section {
            padding: 60px 20px;
            text-align: center;
            background-color: var(--background-color);
            /* Light background for sections */
        }

        .section-header {
            display: block;
            padding: 60px 20px;
            text-align: center;
            background-color: var(--background-color);
        }

        .section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: var(--primary-color);
            /* Calm blue for section headings */
        }

        .section p {
            font-size: 1.125rem;
            color: var(--text-light-color);
            /* Muted grey for body text */
            margin-bottom: 40px;
        }

        .products,
        .testimonials {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .product,
        .testimonial {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 400px;
            flex: 1;
            opacity: 0;
            /* Start hidden for animation */
            transition: opacity 0.6s ease-out;
            border: 1px solid var(--border-color);
            /* Light border for definition */
        }

        .product img,
        .testimonial img {
            max-width: 100%;
            padding-bottom: 10px;
            width: 100%;
            border-radius: 8px;
        }

        .testimonial {
            text-align: left;
        }

        .testimonial p {
            font-style: italic;
            color: var(--text-color);
            /* Dark grey for testimonial text */
        }

        .testimonial span {
            display: block;
            font-weight: bold;
            margin-top: 10px;
            color: var(--primary-color);
            /* Calm blue for testimonial author */
        }

        footer {
            background-color: var(--primary-color);
            color: #fff;
            padding: 40px 20px;
            text-align: center;
        }

        footer h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #fff;
        }

        footer p {
            font-size: 1rem;
            color: #e0e0e0;
            /* Light grey for footer text */
            margin-bottom: 20px;
        }

        footer .social-links a {
            color: #fff;
            margin: 0 10px;
            text-decoration: none;
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        footer .social-links a:hover {
            color: #50e3c2;
            /* Soft teal for hover effect */
        }

        footer .quick-links {
            margin-top: 20px;
        }

        footer .quick-links a {
            color: #e0e0e0;
            text-decoration: none;
            margin: 0 10px;
            font-size: 1rem;
        }

        footer .quick-links a:hover {
            color: #fff;
        }

        .card-img-placeholder {
            height: 200px;
            background-color: #cccccc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: bold;
        }

        /* AOS Animations */
        .aos-animate {
            opacity: 1 !important;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .btn-primary {
                padding: 12px 20px;
                font-size: 1rem;
            }

            .products,
            .testimonials {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>

<body>

    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-content" data-aos="fade-up">
            <h1>Welcome to Our Shop</h1>
            <p>Discover the best products at unbeatable prices.</p>
            <a href="categories.php" class="btn-primary">Shop Now</a>
        </div>
    </div>

    <!-- Products Section -->
    <div class="section-header">
        <h2 data-aos="fade-up">Featured Products</h2>
        <p data-aos="fade-up">Check out our selection of the latest and greatest products.</p>
    </div>
    <section class="section products">
        <?php foreach ($items as $item): ?>
            <div class="product" data-aos="fade-up" data-aos-delay="100">
                <div class="card-img-placeholder">
                    <?php echo htmlspecialchars($item['item_name']); ?>
                </div>
                <h3><?php echo htmlspecialchars($item['item_name']) ?></h3>
                <p><?php echo htmlspecialchars($item['item_desc']) ?></p>
                <form action="item_details.php" method="post">
                    <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                    <input type="hidden" name="cat_id" value="<?php echo $item['cat_id']; ?>">
                    <input type="hidden" name="cat_name" value="<?php echo $item['cat_name']; ?>">
                    <button type="submit" class="btn-primary">View Details</button>
                </form>
            </div>
        <?php endforeach; ?>

    </section>

    <!-- Testimonials Section -->
    <div class="section-header">
        <h2 data-aos="fade-up">What Our Customers Say</h2>
        <p data-aos="fade-up">Read feedback from our satisfied customers.</p>
    </div>
    <section class="section testimonials">
        <?php foreach ($comments as $comment): ?>
            <div class="testimonial" data-aos="fade-up" data-aos-delay="100">
                <?php if (!empty($comment['user_image'])) : ?>
                    <img src="<?php echo htmlspecialchars($comment['user_image']); ?>" alt="User Image" class="user-avatar">
                <?php else : ?>
                    <img src="./avatar.png" alt="Default Avatar" class="user-avatar">
                <?php endif; ?>
                <p>"<?php echo htmlspecialchars($comment['comment_content']); ?>"</p>
                <span>- <?php echo htmlspecialchars($comment['user_name']); ?></span>
            </div>
        <?php endforeach; ?>

    </section>

    <!-- Call to Action Section -->
    <section class="section">
        <h2 data-aos="fade-up">Ready to Start Shopping?</h2>
        <p data-aos="fade-up">Don’t miss out on our amazing deals and offers. Click below to start shopping now!</p>
        <a href="categories.php" class="btn-primary" data-aos="fade-up">Shop Now</a>
    </section>

    <!-- Footer Section -->
    <!-- <footer>
        <h3>Contact Us</h3>
        <p>123 E-commerce St, Shopville, SH 12345</p>
        <p>Email: support@ourshop.com | Phone: (123) 456-7890</p>

        <div class="social-links" data-aos="fade-up">
            <a href="https://facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="https://instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        </div>

        <div class="quick-links" data-aos="fade-up" data-aos-delay="100">
            <a href="about.php">About Us</a>
            <a href="contact.php">Contact Us</a>
            <a href="privacy.php">Privacy Policy</a>
            <a href="terms.php">Terms of Service</a>
        </div>

        <p style="margin-top: 20px;">&copy; 2024 Our Shop. All Rights Reserved.</p>
    </footer> -->

    <!-- AOS Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000, // Duration of animation
            easing: 'ease-in-out', // Animation easing
            once: true // Animate only once
        });
    </script>
    <!-- Font Awesome for Social Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js" integrity="sha384-4bO2jITf4Y8U5e/Ml2Q9A/TU3mMtxQ9Q1fAdzM31C8VxtF1e5hnr3urQ0F8P2eE" crossorigin="anonymous"></script>
</body>

</html>