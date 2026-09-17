<?php
require_once "includes/auth.php";
require_once "config/db.php";

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$message = "";
$messageType = "";
if (isset($_GET['blocked'])) {
    $message = "Your account has been blocked. Please contact support.";
    $messageType = "error";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: index.php");
        exit;
    } else {
        $message = "Invalid email or password.";
        $messageType = "error";
    }
}
?>
<?php include "includes/header.php"; ?>

<div class="register-container">
    <div class="register-card">
        <div class="register-title-line"></div>
        <h1>Welcome Back</h1>
        <p class="register-subtitle">Login to continue shopping</p>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="register-btn">Login</button>
        </form>

        <p class="login-link">Don't have an account? <a href="register.php">Register</a></p>
    </div>
</div>

<?php include "includes/footer.php"; ?>