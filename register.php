<?php
require_once "includes/auth.php";
require_once "config/db.php";

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$message = "";
$messageType = "";
$name = "";
$email = "";
$phone = "";
$address = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm_password"];

    if ($name === "" || $email === "" || $password === "") {
        $message = "All fields are required.";
        $messageType = "error";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
        $messageType = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $messageType = "error";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $message = "Email already registered.";
            $messageType = "error";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashedPassword, $phone, $address]);
            $message = "Registration successful! You can now login.";
            $messageType = "success";
        }
    }
}
?>
<?php include "includes/header.php"; ?>

<div class="register-container">
    <div class="register-card">
        <div class="register-title-line"></div>
        <h1>Create Your Account</h1>
        <p class="register-subtitle">Join us and start shopping today</p>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="field">
                <label>Full Name</label>
                <input type="text" name="name" required value="<?php echo htmlspecialchars($name); ?>">
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="field">
                <label>Mobile Number</label>
                <input type="text" name="phone" required pattern="[0-9]{10}" maxlength="10" placeholder="e.g. 9876543210" value="<?php echo htmlspecialchars($phone); ?>">
            </div>
            <div class="field">
                <label>Address</label>
                <textarea name="address" rows="3" required placeholder="House no, street, area"><?php echo htmlspecialchars($address); ?></textarea>
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <div class="field">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required minlength="6">
            </div>
            <button type="submit" class="register-btn">Create Account</button>
        </form>

        <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>

<?php include "includes/footer.php"; ?>