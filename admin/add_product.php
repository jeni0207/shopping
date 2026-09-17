<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $category = trim($_POST["category"]);
    $price = $_POST["price"];
    $discountPrice = trim($_POST["discount_price"]) !== '' ? $_POST["discount_price"] : null;
    $stock = $_POST["stock"];
    $description = trim($_POST["description"]);
    $specs = trim($_POST["specs"]);
    $imageName = trim($_POST["image"]);
    
    if ($name === "" || $price === "" || $imageName === "") {
        $message = "Name, price, and image URL are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, discount_price, image, description, specs, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $price, $discountPrice, $imageName, $description, $specs, $stock]);
        header("Location: products.php?msg=Product added successfully");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Product</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container nav">
        <a href="dashboard.php" class="logo"><span>Shop</span>Ease Admin</a>
        <div class="nav-actions">
            <a href="products.php" class="nav-btn">📦 Products</a>
            <a href="logout.php" class="nav-btn">🚪 Logout</a>
        </div>
    </div>
</header>

<section class="container" style="padding:40px 0; max-width:600px;">
    <h2 style="margin-bottom:20px;">Add New Product</h2>

    <?php if ($message): ?>
        <div class="message error"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="add_product.php">

        <div class="field">
            <label>Product Name</label>
            <input type="text" name="name" required>
        </div>

        <div class="field">
            <label>Category</label>
            <input type="text" name="category" required>
        </div>

        <div class="field">
    <label>Price (₹)</label>
    <input type="number" step="0.01" name="price" required>
</div>
<div class="field">
    <label>Discount Price (₹) — optional</label>
    <input type="number" step="0.01" name="discount_price" placeholder="Leave blank if no discount">
</div>

        <div class="field">
            <label>Stock Quantity</label>
            <input type="number" name="stock" required>
        </div>

        <div class="field">
            <label>Description</label>
            <textarea name="description" rows="4"></textarea>
        </div>
        <div class="field">
    <label>Specifications — optional</label>
    <textarea name="specs" rows="3" placeholder="e.g. Color: Black, Material: Cotton, Weight: 200g"></textarea>
</div>

        <div class="field">
            <label>Product Image URL</label>
            <input type="url" name="image" placeholder="https://example.com/image.jpg" required>
        </div>

        <button type="submit" class="primary-btn full">Add Product</button>

    </form>
</section>

</body>
</html>