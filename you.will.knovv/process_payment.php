<?php
session_start();

// Check if session is not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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

if (isset($_POST['submit_payment'])) {
    // Retrieve payment information from form
    $cardholder_name = $_POST['cardholder_name'];
    $card_number = $_POST['card_number'];
    $expiry_month = $_POST['expiry_month'];
    $expiry_year = $_POST['expiry_year'];
    $cvv = $_POST['cvv'];

    // Perform validation and processing (dummy example)
    // In a real scenario, you would use a payment gateway SDK and handle security properly

    // Update database with payment status
    $sql = "INSERT INTO payments (cardholder_name, card_number, expiry_month, expiry_year, cvv, payment_status)
            VALUES ('$cardholder_name', '$card_number', '$expiry_month', '$expiry_year', '$cvv', 'complete')";

    if ($conn->query($sql) === TRUE) {
        // Payment successful
        $_SESSION['payment_successful'] = true; // Set a session variable upon successful payment

        // Redirect to thank you page
        header('Location: thank_you.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>