<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: products.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) { header("Location: products.php"); exit; }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $category = trim($_POST["category"]);
    $price = $_POST["price"];
    $discountPrice = trim($_POST["discount_price"]) !== '' ? $_POST["discount_price"] : null;
    $stock = $_POST["stock"];
    $description = trim($_POST["description"]);
    $specs = trim($_POST["specs"]);
    $imageName = trim($_POST["image"]);

    $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, discount_price=?, image=?, description=?, specs=?, stock=? WHERE id=?");
    $stmt->execute([$name, $category, $price, $discountPrice, $imageName, $description, $specs, $stock, $id]);

    header("Location: products.php?msg=Product updated successfully");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Product</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container nav">
        <a href="dashboard.php" class="logo"><span>Shop</span>Ease Admin</a>
        <div class="nav-actions">
            <a href="products.php" class="nav-btn"><i class="fa-solid fa-box"></i> Products</a>
            <a href="logout.php" class="nav-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
</header>

<section class="container" style="padding:40px 0; max-width:600px;">
    <h2 style="margin-bottom:20px;">Edit Product</h2>

    <form method="POST" action="edit_product.php?id=<?php echo $product['id']; ?>">

        <div class="field">
            <label>Product Name</label>
            <input type="text" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <div class="field">
            <label>Category</label>
            <input type="text" name="category" required value="<?php echo htmlspecialchars($product['category']); ?>">
        </div>

        <div class="field">
            <label>Price (₹)</label>
            <input type="number" step="0.01" name="price" required value="<?php echo $product['price']; ?>">
        </div>

        <div class="field">
            <label>Discount Price (₹) — optional</label>
            <input type="number" step="0.01" name="discount_price" value="<?php echo htmlspecialchars($product['discount_price'] ?? ''); ?>">
        </div>

        <div class="field">
            <label>Stock Quantity</label>
            <input type="number" name="stock" required value="<?php echo $product['stock']; ?>">
        </div>

        <div class="field">
            <label>Description</label>
            <textarea name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="field">
            <label>Specifications — optional</label>
            <textarea name="specs" rows="3"><?php echo htmlspecialchars($product['specs'] ?? ''); ?></textarea>
        </div>

        <div class="field">
            <label>Current Image Preview</label><br>
            <img src="<?php echo htmlspecialchars($product['image']); ?>" width="80" style="border-radius:8px; margin-bottom:10px;">
        </div>

        <div class="field">
            <label>Product Image URL</label>
            <input type="url" name="image" required value="<?php echo htmlspecialchars($product['image']); ?>">
        </div>

        <button type="submit" class="primary-btn full">Update Product</button>

    </form>
</section>

</body>
</html>