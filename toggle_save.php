<?php
require "api/session.php";
require "api/db.php";

requireLogin();

$userId = $_SESSION['user_id'];

if (!isset($_POST['recipe_id'])) {
    header("Location: dashboard.php");
    exit;
}

$recipeId = (int) $_POST['recipe_id'];

/* Check if already saved */
$stmt = $pdo->prepare("SELECT id FROM saved_recipes WHERE user_id = ? AND recipe_id = ?");
$stmt->execute([$userId, $recipeId]);

if ($stmt->fetch()) {
    // Unsave
    $delete = $pdo->prepare("DELETE FROM saved_recipes WHERE user_id = ? AND recipe_id = ?");
    $delete->execute([$userId, $recipeId]);
} else {
    // Save
    $insert = $pdo->prepare("INSERT INTO saved_recipes (user_id, recipe_id) VALUES (?, ?)");
    $insert->execute([$userId, $recipeId]);
}

/* Redirect back */
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
