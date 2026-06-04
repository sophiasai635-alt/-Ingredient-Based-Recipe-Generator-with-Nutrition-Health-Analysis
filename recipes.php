<?php
require "api/session.php";
require "api/db.php";

requireLogin();

$category = $_GET['category'] ?? null;

if ($category) {
    $stmt = $pdo->prepare("
        SELECT r.id, r.title, r.cook_time, n.calories
        FROM recipes r
        LEFT JOIN nutrition n ON r.id = n.recipe_id
        WHERE r.category = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$category]);
} else {
    $stmt = $pdo->query("
        SELECT r.id, r.title, r.cook_time, n.calories
        FROM recipes r
        LEFT JOIN nutrition n ON r.id = n.recipe_id
        ORDER BY r.created_at DESC
    ");
}

$recipes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Recipes</title>
    <link rel="stylesheet" href="assets/dashboard.css">
</head>
<body>

<?php include "partials/navbar.php"; ?>

<div class="container">
    <h2>
        <?= $category ? ucfirst($category) . " Recipes" : "All Recipes" ?>
    </h2>

    <div class="recipe-grid">
        <?php if ($recipes): ?>
            <?php foreach ($recipes as $recipe): ?>
                <div class="recipe-card">
                    <h4><?= htmlspecialchars($recipe['title']) ?></h4>
                    <p>⏱ <?= (int)$recipe['cook_time'] ?> mins</p>
                    <p>🔥 <?= $recipe['calories'] ?? 'N/A' ?> kcal</p>

                    <a href="recipe_detail.php?id=<?= $recipe['id'] ?>" class="link-btn">
                        View Recipe
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No recipes found.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
