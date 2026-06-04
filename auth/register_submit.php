<?php
require "../api/db.php";
require "../api/session.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request");
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$name || !$email || !$password) {
    die("All fields are required");
}

/* Allow only @gmail.com */
if (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
    die("Only @gmail.com emails are allowed");
}

/* Check if email already exists */
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    die("Email already registered");
}

/* Insert user */
$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO users (name, email, password)
    VALUES (?, ?, ?)
");

$stmt->execute([$name, $email, $hashed]);

header("Location: ../index.php");
exit;
