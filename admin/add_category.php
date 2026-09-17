<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $image = trim($_POST["image"]);
    $description = trim($_POST["description"]);
    $status = $_POST["status"];

    if ($name === "" || $image === "") {
        $message = "Name and image URL are required.";
    } else {
        $check = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $check->execute([$name]);

        if ($check->fetch()) {
            $message = "A category with this name already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (name, image, description, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $image, $description, $status]);
            header("Location: categories.php?msg=Category added successfully");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Category</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container nav">
        <a href="dashboard.php" class="logo"><span>Shop</span>Ease Admin</a>
        <div class="nav-actions">
            <a href="categories.php" class="nav-btn"><i class="fa-solid fa-tags"></i> Categories</a>
            <a href="logout.php" class="nav-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
</header>

<section class="container" style="padding:40px 0; max-width:600px;">
    <h2 style="margin-bottom:20px;">Add New Category</h2>

    <?php if ($message): ?>
        <div class="message error"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="add_category.php">

        <div class="field">
            <label>Category Name</label>
            <input type="text" name="name" required>
        </div>

        <div class="field">
            <label>Category Image URL</label>
            <input type="url" name="image" placeholder="https://example.com/image.jpg" required>
        </div>

        <div class="field">
            <label>Description</label>
            <textarea name="description" rows="3"></textarea>
        </div>

        <div class="field">
            <label>Status</label>
            <select name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <button type="submit" class="primary-btn full">Add Category</button>

    </form>
</section>

</body>
</html>