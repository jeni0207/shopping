<?php
require_once "includes/auth.php";
require_once "config/db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $msgBody = trim($_POST["message"]);

    if ($name === "" || $email === "" || $msgBody === "") {
        $message = "Name, email, and message are required.";
        $messageType = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $msgBody]);
        $message = "Thank you! Your message has been sent. We'll get back to you soon.";
        $messageType = "success";
    }
}
?>
<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<section class="container" style="padding: 60px 0; max-width: 900px;">

    <p class="eyebrow">GET IN TOUCH</p>
    <h1 style="margin-bottom: 30px;">Contact Us</h1>

    <div style="display:grid; grid-template-columns: 1fr 1.3fr; gap:30px;">

        <div style="background:#fff; border:1px solid #eee; border-radius:16px; padding:25px;">
            <h3 style="margin-bottom:15px;">Contact Information</h3>

            <p style="margin-bottom:15px; font-size:14px; color:#555;">
                <i class="fa-solid fa-location-dot" style="color:#ff5a3c; margin-right:8px;"></i>
                123 Market Street, Surat, Gujarat, India
            </p>

            <p style="margin-bottom:15px; font-size:14px; color:#555;">
                <i class="fa-solid fa-phone" style="color:#ff5a3c; margin-right:8px;"></i>
                +91 98765 43210
            </p>

            <p style="margin-bottom:15px; font-size:14px; color:#555;">
                <i class="fa-solid fa-envelope" style="color:#ff5a3c; margin-right:8px;"></i>
                support@shopease.com
            </p>

            <p style="font-size:14px; color:#555;">
                <i class="fa-solid fa-clock" style="color:#ff5a3c; margin-right:8px;"></i>
                Mon–Sat: 9:00 AM – 7:00 PM
            </p>
        </div>

        <div style="background:#fff; border:1px solid #eee; border-radius:16px; padding:25px;">

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="POST" action="contact.php">

                <div class="field">
                    <label>Your Name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="field">
                    <label>Subject</label>
                    <input type="text" name="subject">
                </div>

                <div class="field">
                    <label>Message</label>
                    <textarea name="message" rows="5" required></textarea>
                </div>

                <button type="submit" class="primary-btn full">Send Message</button>

            </form>
        </div>

    </div>

</section>

<?php include "includes/footer.php"; ?>