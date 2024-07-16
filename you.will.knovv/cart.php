<?php
session_start(); // Ensure session is started

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "labeldata";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    if (isset($_POST['id']) && isset($_POST['quantity'])) {
        $id = (int)$_POST['id'];
        $quantity = (int)$_POST['quantity'];

        $sql = "SELECT * FROM products WHERE id = $id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();

            // Insert into cart table
            $sql = "INSERT INTO cart (product_id, product_name, product_price, quantity) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isdi", $product['id'], $product['name'], $product['price'], $quantity);
            $stmt->execute();
            $stmt->close();

            // Add to session cart
            $_SESSION['cart'][$id] = $quantity;

            // Redirect to cart page
            header('Location: cart.php');
            exit();
        }
    } else {
        echo "Product ID and quantity are required.";
    }
}

// Handle remove from cart
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Remove from session cart
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }

    // Remove from database cart table
    $sql = "DELETE FROM cart WHERE product_id = $id";
    $conn->query($sql);

    // Redirect to cart page
    header('Location: cart.php');
    exit();
}

// Handle checkout
if (isset($_POST['checkout'])) {
    header('Location: payment.php');
    exit();
}

// Fetch cart items
$cartItems = [];
$totalPrice = 0;
$sql = "SELECT * FROM cart";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
        $totalPrice += $row['product_price'] * $row['quantity'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop Cart</title>
    <style>
        .cart-container {
            margin: 20px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .total-price {
            font-size: 1.5em;
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>
<body>
<div class="cart-container">
        <h1>Shop Cart</h1>
        <?php if (empty($_SESSION['cart']) || empty($cartItems)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <form method="post" action="payment.php">
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                        <span>Quantity: <?php echo $item['quantity']; ?></span>
                        <span>Price: RM<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?></span>
                        <a href="cart.php?action=remove&id=<?php echo $item['product_id']; ?>">Remove</a>
                    </div>
                <?php endforeach; ?>
                <div class="total-price">Total: RM<?php echo number_format($totalPrice, 2); ?></div>
                <input type="submit" name="checkout" value="Checkout">
            </form>
        <?php endif; ?>
        <a href="shop-index.php">Continue Shopping</a>
    </div>
</body>
</html>