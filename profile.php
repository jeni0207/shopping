<?php
require_once "includes/auth.php";
require_once "config/db.php";
requireLogin();
checkIfBlocked($conn);

$userId = currentUserId();

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $newPassword = $_POST["new_password"] ?? '';

    if ($name === "" || $email === "") {
        $message = "Name and email are required.";
        $messageType = "error";
    } else {
        // Check if email is taken by another user
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->execute([$email, $userId]);

        if ($check->fetch()) {
            $message = "That email is already in use by another account.";
            $messageType = "error";
        } else {
            if ($newPassword !== '') {
                if (strlen($newPassword) < 6) {
                    $message = "New password must be at least 6 characters.";
                    $messageType = "error";
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, address=?, password=? WHERE id=?");
                    $stmt->execute([$name, $email, $phone, $address, $hashedPassword, $userId]);
                }
            } else {
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, address=? WHERE id=?");
                $stmt->execute([$name, $email, $phone, $address, $userId]);
            }

            if ($message === "") {
                $_SESSION['user_name'] = $name;
                $message = "Profile updated successfully.";
                $messageType = "success";

                // Refresh user data
                $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch();
            }
        }
    }
}
?>
<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<section class="container" style="padding: 50px 0; max-width: 550px;">

    <h1 style="margin-bottom: 25px;">My Profile</h1>

    <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="profile.php" style="background:#fff; padding:30px; border-radius:16px; border:1px solid #eee;">

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
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Optional">
        </div>

        <div class="field">
            <label>Address</label>
            <textarea name="address" rows="3" placeholder="Optional"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
        </div>

        <div class="field">
            <label>New Password</label>
            <input type="password" name="new_password" placeholder="Leave blank to keep current password" minlength="6">
        </div>

        <button type="submit" class="primary-btn full">Save Changes</button>

    </form>

</section>

<?php include "includes/footer.php"; ?>