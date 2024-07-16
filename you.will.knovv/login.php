<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "labeldata";

//Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

//check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Prepare and bind
    $sql = "SELECT * FROM Admin WHERE username = ?";
    $stmt = $conn->prepare($sql);

    //Check if statement prepared
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

    //verify password
    if (password_verify($inputPassword, $user["password"])) {
        //starting session and set variables
        $_SESSION["username"] = $user["username"];

        //Redirect to admin dashboard
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Invalid username or password.";
    } 
    } else {
        echo "Invalid username or password.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Admin Login</title>
        <link rel="stylesheet" href="./styles.css">
    </head>

    <body>
        <div class="login-container">
            <h1>Admin Login</h1>
            <form action="login.php" method="POST">
                
                <input type="text" id="username" name="username" placeholder="Username" required>
                <br><br>
                
                <input type="password" id="password" name="password" placeholder="Password" required>
                <br><br>

                <input type="submit" value="Login">
            </form>
        </div>
    </body>
</html>