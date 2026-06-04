<?php
require "api/session.php";
require "api/db.php";

requireLogin();

/* 🔒 If preferences already set → skip this page */
$stmt = $pdo->prepare("SELECT preferences FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!empty($user['preferences'])) {
    $_SESSION['preferences'] = $user['preferences'];
    header("Location: dashboard.php");
    exit;
}

$error = "";

/* Handle form submission */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $preferences = $_POST['preferences'] ?? [];

    if (empty($preferences)) {
        $error = "Please select at least one preference.";
    } else {
        $prefString = implode(",", $preferences);

        $stmt = $pdo->prepare(
            "UPDATE users SET preferences = ? WHERE id = ?"
        );
        $stmt->execute([$prefString, $_SESSION['user_id']]);

        /* ✅ Save in session for quick access */
        $_SESSION['preferences'] = $prefString;

        header("Location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Preferences | Recipe Generator</title>
    <link rel="stylesheet" href="assets/dashboard.css">
</head>
<body>

<div class="container">
    <h2>Select Your Recipe Preferences 🍽️</h2>
    <p>Choose what kind of recipes you enjoy (you can select multiple)</p>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>
            <input type="checkbox" name="preferences[]" value="healthy">
            🥗 Healthy Recipes
        </label><br>

        <label>
            <input type="checkbox" name="preferences[]" value="quick">
            ⚡ Quick & Easy
        </label><br>

        <label>
            <input type="checkbox" name="preferences[]" value="protein">
            💪 High Protein
        </label><br>

        <label>
            <input type="checkbox" name="preferences[]" value="comfort">
            🍔 Comfort Food
        </label><br>

        <label>
            <input type="checkbox" name="preferences[]" value="low_calorie">
            🔥 Low Calorie
        </label><br><br>

        <button type="submit" class="primary-btn">
            Save Preferences
        </button>
    </form>
</div>

</body>
</html>
