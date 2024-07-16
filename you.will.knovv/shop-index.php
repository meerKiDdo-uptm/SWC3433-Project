<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop Page</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>  
    <div class="topnav">
        <div class="logo">
            <a href="youwillknovvindex.html"><img src="Images/labellogo.png" alt="Logo"></a>
        </div>
        <div class="breadcrumbs">
            <a href="./about-index.html" class="breadcrumb-link">About</a>
            <a href="./shop-index.php" class="breadcrumb-link">Shop</a>
            <a href="./news-index.html" class="breadcrumb-link">News</a>
            <a href="./contacts-index.html" class="breadcrumb-link">Contacts</a>
        </div>

        <a href="cart.php" class="nav-link">
                <i class="fas fa-shopping-cart cart-icon"></i>
        </a>
    </div>

    <div class="container">
        <h1>Shop</h1>
        <div class="product-grid">
            <?php
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            // Database connection
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

            // Fetch products from database
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);

            // Display products
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product">';
                    echo '<div class="product-image">';
                    echo '<img src="Images/' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '<span class="tooltip">' . htmlspecialchars($row['description']) . '</span>'; // Description as tooltip
                    echo '</div>';
                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<p>RM ' . number_format($row['price'], 2) . '</p>';
                    echo '<form method="post" action="cart.php">';
                    echo '<input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">';
                    echo '<label for="quantity">Quantity:</label>';
                    echo '<input type="number" name="quantity" value="1" min="1">';
                    echo '<input type="submit" name="add_to_cart" value="Add to Cart">';
                    echo '</form>';
                    echo '</div>';
                }
            } else {
                echo "No products found.";
            }

            // Close connection
            $conn->close();
            ?>
        </div>
    </div>

    <footer>
            <div class="footer-content">
                <div class="section">
                    <p class="imagination-text">Imagination Boost</p>
                    <p class="imagination-text">Supplements Brain-Stimulation Stuff</p>
                </div>
                <div class="section">
                    <p class="copyright-text">&copy; 2020 YOU.WILL.KNOVV. All rights reserved.</p>
                </div>
                <div class="section">
                    <div class="social-icons">
                        <a href="https://www.youtube.com/@youwillknovv" target="_blank" class="social-icon"><i class="fab fa-youtube"></i></a>
                        <a href="https://www.instagram.com/you.will.knovv/" target="_blank" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.facebook.com/you.will.knovv" target="_blank" class="social-icon"><i class="fab fa-facebook"></i></a>
                        <a href="https://x.com/youwillknovv" target="_blank" class="social-icon"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="section">
                    <div class="additional-content">
                        <p>Corporate name (Trade name) you.will.knovv Co., Ltd. Representative: Kwon Hyuk</p>
                        <p>Business Registration Number: 392-87-00763 Mail Order Business Report: No. 2020-Seoul Seodaemun-1108</p>
                        <p>Address: #501, Yuseong Building, 3 Yeonhui-ro 37-gil, Seodaemun-gu, Seoul (Hongeun-dong)</p>
                        <p><strong>E-MAIL: info@youwillknovv.com Phone: 02-6083-1637 Fax: 02-6083-1638</strong></p>
                        <p><strong>Personal Information Manager: Hong Hee-soon</strong></p>
                        <div class="section">
                            <a href="login.php" target="_blank" class="admin-login">you.will.knovv admin</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
</body>
</html>