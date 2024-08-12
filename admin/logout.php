<?php
// admin/start_session.php
session_name('admin_session'); // Use a distinct name for admin sessions
session_start();
// If the request method is POST, display the logout confirmation page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_unset();
    session_destroy();

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Logout</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f8f9fa;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }

            .logout-container {
                text-align: center;
                background-color: #fff;
                padding: 40px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                width: 80%;
                max-width: 500px;
            }

            .logout-container h1 {
                color: #333;
                margin-bottom: 20px;
            }

            .logout-container p {
                color: #666;
                margin-bottom: 30px;
            }

            .logout-container a {
                display: inline-block;
                padding: 10px 20px;
                color: #fff;
                background-color: #007bff;
                text-decoration: none;
                border-radius: 4px;
                margin-top: 10px;
            }

            .logout-container a:hover {
                background-color: #0056b3;
            }

            .timer {
                font-weight: bold;
                color: #007bff;
            }

            @media (max-width: 768px) {
                .logout-container {
                    padding: 20px 10px;
                    width: 90%;
                }

                .logout-container h1 {
                    font-size: 1.5em;
                    margin-bottom: 15px;
                }

                .logout-container p {
                    font-size: 1em;
                    margin-bottom: 20px;
                }

                .logout-container a {
                    padding: 8px 16px;
                    font-size: 0.9em;
                }
            }
        </style>
        <script>
            // Set the timer duration (in seconds)
            let timerDuration = 5;

            function startCountdown() {
                const timerElement = document.getElementById('timer');
                const interval = setInterval(() => {
                    timerElement.textContent = timerDuration;
                    if (timerDuration <= 0) {
                        clearInterval(interval);
                        window.location.href = 'index.php';
                    } else {
                        timerDuration--;
                    }
                }, 1000);
            }

            // Start the countdown on page load
            window.onload = startCountdown;
        </script>
    </head>

    <body>
        <div class="logout-container">
            <h1>Logged Out</h1>
            <p>You have been successfully logged out. You will be redirected to the login page in <span id="timer" class="timer">5</span> seconds.</p>
            <p>If you are not redirected, click the button below:</p>
            <a href="index.php">Go to Login Page</a>
        </div>
    </body>

    </html>
<?php
} else {
    // Redirect to home or another appropriate page if accessed via GET
    header("Location: index.php");
    exit();
}
?>