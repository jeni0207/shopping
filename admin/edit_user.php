<?php
require_once "../includes/auth.php";
require_once "../config/db.php";
requireAdmin();

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: users.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) { header("Location: users.php"); exit; }

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);

    $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $check->execute([$email, $id]);

    if ($check->fetch()) {
        $message = "That email is already used by another account.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, address=? WHERE id=?");
        $stmt->execute([$name, $email, $phone, $address, $id]);
        header("Location: users.php?msg=User updated successfully");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit User</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container nav">
        <a href="dashboard.php" class="logo"><span>Shop</span>Ease Admin</a>
        <div class="nav-actions">
            <a href="users.php" class="nav-btn"><i class="fa-solid fa-user"></i> Users</a>
            <a href="logout.php" class="nav-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
</header>

<section class="container" style="padding:40px 0; max-width:600px;">
    <h2 style="margin-bottom:20px;">Edit User</h2>

    <?php if ($message): ?>
        <div class="message error"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_user.php?id=<?php echo $user['id']; ?>">

        <div class="field">
            <label>Full Name</label>
            <input type="text" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>">
        </div>

        <div class="field">
            <label>Email</label>
            <input type="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
        </div>

        <div class="field">
            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
        </div>

        <div class="field">
            <label>Address</label>
            <textarea name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="primary-btn full">Update User</button>

    </form>
</section>

</body>
</html>