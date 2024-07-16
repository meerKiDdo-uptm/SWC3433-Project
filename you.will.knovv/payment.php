<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "labeldata";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure cart items are accessible
if (empty($_SESSION['cart'])) {
    // Handle case where cart is empty or not properly initialized
    header('Location: cart.php'); // Redirect back to cart or handle error
    exit();
}

// Assuming payment is successful, clear the cart session and database entries
if (isset($_POST['payment_status']) && $_POST['payment_status'] == 'success') {
    // Clear session cart
    $_SESSION['cart'] = [];

    // Clear database cart entries
    $sql = "TRUNCATE TABLE cart"; // Use TRUNCATE TABLE for a more efficient deletion
    if ($conn->query($sql) === TRUE) {
        echo "Cart cleared successfully.";
    } else {
        echo "Error clearing cart: " . $conn->error;
    }
} else {
    echo "Payment was not successful. Please try again.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Information</title>
    <style>
        .container {
            margin: 20px;
        }
        form {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            float: right;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Enter Payment Information</h2>
        <form method="post" action="process_payment.php">
            <label for="cardholder_name">Cardholder Name:</label>
            <input type="text" id="cardholder_name" name="cardholder_name" required><br>

            <label for="card_number">Card Number:</label>
            <input type="text" id="card_number" name="card_number" required><br>

            <label for="expiry_month">Expiration Month:</label>
            <input type="number" id="expiry_month" name="expiry_month" min="1" max="12" required><br>

            <label for="expiry_year">Expiration Year:</label>
            <input type="number" id="expiry_year" name="expiry_year" min="<?php echo date('Y'); ?>" required><br>

            <label for="cvv">CVV:</label>
            <input type="number" id="cvv" name="cvv" required><br>

            <input type="submit" name="submit_payment" value="Buy">
        </form>
    </div>
</body>
</html>