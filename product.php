<?php
session_start();
require '../connect.php';

// Kiểm tra kết nối database
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Xử lý thêm & cập nhật sản phẩm
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_product'])) {
    $product_id = $_POST['product_id'] ?? ''; // Nếu có ID, nghĩa là cập nhật
    $category_id = $_POST['category_id'];
    $product_name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);

    // Xử lý ảnh sản phẩm
    $image_url = "";
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image_url = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_url);
    }

    // Kiểm tra sản phẩm đã tồn tại hay chưa
    $check_stmt = $conn->prepare("SELECT product_id, stock_quantity FROM products WHERE product_name = ? AND category_id = ?");
    $check_stmt->bind_param("si", $product_name, $category_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Sản phẩm đã tồn tại → cập nhật số lượng
        $row = $result->fetch_assoc();
        $new_quantity = $row['stock_quantity'] + $stock_quantity;
        $product_id = $row['product_id'];

        // Cập nhật sản phẩm
        if ($image_url) {
            $update_stmt = $conn->prepare("UPDATE products SET stock_quantity = ?, description = ?, price = ?, image_url = ? WHERE product_id = ?");
            $update_stmt->bind_param("isdsi", $new_quantity, $description, $price, $image_url, $product_id);
        } else {
            $update_stmt = $conn->prepare("UPDATE products SET stock_quantity = ?, description = ?, price = ? WHERE product_id = ?");
            $update_stmt->bind_param("isdi", $new_quantity, $description, $price, $product_id);
        }

        if ($update_stmt->execute()) {
            echo "<script>alert('Product update successful!'); window.location='product.php';</script>";
        } else {
            echo "Update error: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        // Sản phẩm chưa tồn tại → thêm mới
        $insert_stmt = $conn->prepare("INSERT INTO products (category_id, product_name, description, price, image_url, stock_quantity) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("issdsi", $category_id, $product_name, $description, $price, $image_url, $stock_quantity);

        if ($insert_stmt->execute()) {
            echo "<script>alert('New product added successfully!'); window.location='product.php';</script>";
        } else {
            echo "Error adding product:" . $insert_stmt->error;
        }
        $insert_stmt->close();
    }

    $check_stmt->close();
}

// Xóa sản phẩm
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM products WHERE product_id=?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('Product deleted successfully!'); window.location='product.php';</script>";
    } else {
        echo "Error while deleting:" . $stmt->error;
    }
    $stmt->close();
}

// Lấy danh sách sản phẩm
$result = $conn->query("SELECT product_id, category_id, product_name, description, price, image_url, stock_quantity FROM products");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="product.css">
</head>

<body>

    <div class="container">
        <h2>Product Management</h2>

        <!-- Add / Edit Product Form -->
        <div class="Add-data">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" id="product_id">
                <input type="text" name="product_name" id="product_name" placeholder="Product Name" required>
                <input type="text" name="category_id" id="category_id" placeholder="Category ID" required>
                <textarea name="description" id="description" placeholder="Product Description"></textarea>
                <input type="number" name="price" id="price" placeholder="Price" step="0.01" required>
                <label for="customFileInput" class="custom-file-label" placeholder="Choose File">Choose File</label>
                <input type="file" id="customFileInput" style="display: none;">
                <input type="number" name="stock_quantity" id="stock_quantity" placeholder="Stock Quantity" required>
                <button type="submit" name="save_product" class="btn">Save</button>
            </form>
        </div>
        <!-- =============================== -->
        <script>
            document.getElementById("customFileInput").addEventListener("change", function() {
                let fileName = this.files.length > 0 ? this.files[0].name : "No file chosen";
                document.querySelector(".custom-file-label").textContent = fileName;
            });
        </script>
        <!-- =============================== -->
        <!-- Product List Table -->
        <table>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Image</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['product_id'] ?></td>
                    <td><?= $row['category_id'] ?></td>
                    <td><?= $row['product_name'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td><?= $row['price'] ?> $</td>
                    <td>
                        <?php if ($row['image_url']): ?>
                            <img src="<?= $row['image_url'] ?>" width="50">
                        <?php endif; ?>
                    </td>
                    <td><?= $row['stock_quantity'] ?></td>
                    <td>
                        <button class="btn-edit" onclick="editProduct('<?= $row['product_id'] ?>', '<?= $row['category_id'] ?>', '<?= $row['product_name'] ?>', '<?= $row['description'] ?>', '<?= $row['price'] ?>', '<?= $row['stock_quantity'] ?>')">✏ Edit</button>
                        <a href="?delete=<?= $row['product_id'] ?>" class="btn-delete" onclick="return confirm('Confirm delete?');">🗑 Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <div class="buttons">
            <a href="../Main/main.php">⬅ Back</a>
        </div>

    </div>

    <script>
        function editProduct(id, category_id, name, desc, price, stock) {
            document.getElementById("product_id").value = id;
            document.getElementById("category_id").value = category_id;
            document.getElementById("product_name").value = name;
            document.getElementById("description").value = desc;
            document.getElementById("price").value = price;
            document.getElementById("stock_quantity").value = stock;
        }
    </script>

</body>

</html>


<?php $conn->close(); ?>