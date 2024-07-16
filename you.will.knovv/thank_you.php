<?php
session_start();

// Ensure payment was successful before displaying thank you page
if (!isset($_SESSION['payment_successful']) || $_SESSION['payment_successful'] !== true) {
    header('Location: cart.php'); // Redirect back to cart or handle error
    exit();
}

// Clear the cart after successful payment
$_SESSION['cart'] = [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You</title>
        <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 50px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        p {
            font-size: 18px;
            line-height: 1.6;
            color: #666;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
        <h1>Thank You for Your Purchase!</h1>
        <p>Your order has been successfully processed.</p>
        <p>We have received your payment and will proceed with the shipment shortly.</p>
        <p>For any inquiries regarding your order, please contact our <a href="contacts-index.html">customer support</a>.</p>
        <p>Continue shopping from our <a href="shop-index.php">shop</a>.</p>
    </div>
</body>
</html>