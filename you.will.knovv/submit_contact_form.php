<?php
// Database connection parameters
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

// Initialize variables and error messages
$nameErr = $emailErr = $subjectErr = $messageErr = "";
$sender_name = $sender_email = $sender_subject = $message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    if (empty($_POST["sender_name"])) {
        $nameErr = "Your Name is required";
    } else {
        $sender_name = $_POST["sender_name"];
    }

    if (empty($_POST["sender_email"])) {
        $emailErr = "Your Email is required";
    } else {
        $sender_email = $_POST["sender_email"];
    }

    if (empty($_POST["sender_subject"])) {
        $subjectErr = "Subject is required";
    } else {
        $sender_subject = $_POST["sender_subject"];
    }

    if (empty($_POST["message"])) {
        $messageErr = "Your Message is required";
    } else {
        $message = $_POST["message"];
    }

    // If all fields are valid, insert into the database
    if (empty($nameErr) && empty($emailErr) && empty($subjectErr) && empty($messageErr)) {
        $sql = "INSERT INTO contactform (sender_name, sender_email, sender_subject, message) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $sender_name, $sender_email, $sender_subject, $message);

        if ($stmt->execute()) {
            echo "<script>alert('Your form has been send succesfully')</script>";
            echo "<script>window.open('contacts-index.html','_self')</script>";
        } else {
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    } else {
        // Display validation errors
        echo "Please correct the following errors:<br>";
        echo $nameErr . "<br>";
        echo $emailErr . "<br>";
        echo $subjectErr . "<br>";
        echo $messageErr . "<br>";
    }
}

// Close the connection
$conn->close();
?>