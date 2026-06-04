<?php
require "../api/db.php";
require "../api/session.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        $error = "Invalid email or password";
    } else {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];

        header("Location: ../dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | Recipe Generator</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

<div class="auth-box">
    <h2>Welcome Back 👋</h2>
    <p class="subtitle">Login to generate recipes</p>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Gmail Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button>Login</button>
    </form>

    <p class="switch">
        New user?
        <a href="register.php">Create account</a>
    </p>
</div>

</body>
</html>
