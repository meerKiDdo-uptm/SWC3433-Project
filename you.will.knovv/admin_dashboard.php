<?php
session_start();

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

//check if user logged in
if (!isset($_SESSION["username"])) {
    //if not, redirect to login page
    header("Location: login.php");
    exit();
}

//initialize variable for validation
$nameErr = $descErr = $priceErr = $imageErr = "";
$name = $description = $price = $image = "";

//Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //validate input
    if (empty($_POST["name"])) {
        $nameErr = "Product name is required";
    } else {
        $name = $_POST["name"];
    }

    if (empty($_POST["description"])) {
        $descErr = "Description is required";
    } else {
        $description = $_POST["description"];
    }

    if (empty($_POST["price"])) {
        $priceErr = "Price is required";
    } else {
        $price = $_POST["price"];
    }

    //file upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $targetDir = "Images/";
        $imageName = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

        //checking image (real or fake)
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $imageErr = "File is not an image.";
        }

        // check file size
        if ($_FILES["image"]["size"] > 500000) {
            $imageErr = "Sorry, your file is too large.";
        }

        //allow certain file format
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
                $imageErr = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        //if non, move upload file
        if (empty($imageErr)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $image = $targetFile;
            } else {
                $imageErr = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        $imageErr = "Please uploaad an image.";
    }

    //If all fields valid, insert into database
    if (empty($nameErr) && empty($descErr) && empty($priceErr) && empty($imageErr)) {
        // Insert into products table
        $sql = "INSERT INTO products (name, description, price, image_path) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssds", $name, $description, $price, $imageName);

        if ($stmt->execute()) {
            echo '<div style="color: green;">New product added successfully.</div>';
            // Clear form fields after successful insertion
            $name = $description = $price = $image = "";
        } else {
            echo '<div style="color: red;">Error: ' . $conn->error . '</div>';
        }

        $stmt->close();

        // Retrieve updated list of products
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);
    }

        // Handle product deletion
        if (isset($_POST['delete_product']) && isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];

        // Prepare and execute SQL statement to delete product
        $sql_delete = "DELETE FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql_delete);
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
            echo '<div style="color: green;">Product deleted successfully.</div>';
        } else {
            echo '<div style="color: red;">Error deleting product: ' . $conn->error . '</div>';
        }

        $stmt->close();
    }
}

// Fetch products from database
$sql_select = "SELECT * FROM products";
$result = $conn->query($sql_select);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Admin Dashboard</title>
        <link rel="stylesheet" href="styles.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>

    <body>
        <h1>Welcome to the Admin Dashboard</h1>
        <p>Hello, <?php echo htmlspecialchars($_SESSION["username"]); ?>! You are logged in.</p>

        <h2>Manage Products</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["description"] . "</td>";
                echo "<td>RM " . number_format($row["price"], 2) . "</td>";
                echo '<td><img src="' . $row["image_path"] . '" alt="' . $row["name"] . '" style="width: 100px; height: auto;"></td>';
                echo '<td>
                        <form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                            <input type="hidden" name="product_id" value="' . $row["id"] . '">
                            <input type="submit" name="delete_product" value="Delete">
                        </form>
                    </td>';
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No products found.</td></tr>";
        }
        ?>
        </tbody>
        </table>

    <!-- Button for adding a new product -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">
        Add Product
    </button>

    <!-- Modal for adding product (Bootstrap example) -->
    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form for adding a new product -->
                    <form action="admin_dashboard.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Product Name:</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                            <?php echo "<span class='text-danger'>$nameErr</span>";?>
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description" rows="4" cols="50" class="form-control"></textarea>
                            <?php echo "<span class='text-danger'>$descErr</span>";?>
                        </div>
                        <div class="form-group">
                            <label for="price">Price:</label>
                            <input type="text" id="price" name="price" class="form-control" required>
                            <?php echo "<span class='text-danger'>$priceErr</span>";?>
                        </div>
                        <div class="form-group">
                            <label for="image">Product Image:</label>
                            <input type="file" id="image" name="image" accept="image/*" class="form-control" required>
                            <?php echo "<span class='text-danger'>$imageErr</span>";?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <input type="submit" name="add_product" value="Add Product" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        
    </div>

    <!--Logout Link-->
    <br><br>
    <a href="logout.php" class="btn btn-danger">Logout</a>

        <!-- Optionally include Bootstrap JS for modal functionality -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
</html>

<?php
// Close connection at the end of the script
$conn->close();
?>