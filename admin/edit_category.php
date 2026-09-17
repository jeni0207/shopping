<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: categories.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) { header("Location: categories.php"); exit; }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $image = trim($_POST["image"]);
    $description = trim($_POST["description"]);
    $status = $_POST["status"];

    $stmt = $conn->prepare("UPDATE categories SET name=?, image=?, description=?, status=? WHERE id=?");
    $stmt->execute([$name, $image, $description, $status, $id]);

    header("Location: categories.php?msg=Category updated successfully");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Category</title>
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
    <h2 style="margin-bottom:20px;">Edit Category</h2>

    <form method="POST" action="edit_category.php?id=<?php echo $category['id']; ?>">

        <div class="field">
            <label>Category Name</label>
            <input type="text" name="name" required value="<?php echo htmlspecialchars($category['name']); ?>">
        </div>

        <div class="field">
            <label>Category Image URL</label>
            <input type="url" name="image" required value="<?php echo htmlspecialchars($category['image']); ?>">
        </div>

        <div class="field">
            <label>Current Image Preview</label><br>
            <img src="<?php echo htmlspecialchars($category['image']); ?>" width="80" style="border-radius:8px; margin-bottom:10px;">
        </div>

        <div class="field">
            <label>Description</label>
            <textarea name="description" rows="3"><?php echo htmlspecialchars($category['description']); ?></textarea>
        </div>

        <div class="field">
            <label>Status</label>
            <select name="status">
                <option value="active" <?php echo $category['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $category['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <button type="submit" class="primary-btn full">Update Category</button>

    </form>
</section>

</body>
</html>