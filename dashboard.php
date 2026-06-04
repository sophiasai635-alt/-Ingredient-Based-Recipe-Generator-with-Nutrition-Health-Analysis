<?php
require "api/session.php";
require "api/db.php";

requireLogin();

/* 🔒 Ensure preferences are selected */
if (empty($_SESSION['preferences'])) {
    header("Location: preferences.php");
    exit;
}

$userName    = strtoupper($_SESSION['name']);
$preferences = explode(",", $_SESSION['preferences']);

/* Prepare placeholders for IN clause */
$placeholders = implode(",", array_fill(0, count($preferences), "?"));

/* Fetch recommended recipes based on preferences */
$stmt = $pdo->prepare("
    SELECT r.id, r.title, r.cook_time, r.difficulty, r.category, n.calories
    FROM recipes r
    LEFT JOIN nutrition n ON r.id = n.recipe_id
    WHERE r.category IN ($placeholders)
    ORDER BY r.created_at DESC
    LIMIT 6
");
$stmt->execute($preferences);
$recipes = $stmt->fetchAll();

/* ── NEW: Quick stats ──────────────────────────────────── */
$totalSaved = $pdo->prepare("SELECT COUNT(*) FROM saved_recipes WHERE user_id = ?");
$totalSaved->execute([$_SESSION['user_id']]);
$savedCount = (int) $totalSaved->fetchColumn();

$totalSearches = $pdo->prepare("SELECT COUNT(*) FROM ingredient_search_logs WHERE user_id = ?");
$totalSearches->execute([$_SESSION['user_id']]);
$searchCount = (int) $totalSearches->fetchColumn();

$totalRecipes = (int) $pdo->query("SELECT COUNT(*) FROM recipes")->fetchColumn();

/* ── NEW: Recently saved recipes (sidebar feel) ────────── */
$recentSaved = $pdo->prepare("
    SELECT r.id, r.title, r.cook_time, r.category
    FROM saved_recipes sr
    JOIN recipes r ON sr.recipe_id = r.id
    WHERE sr.user_id = ?
    ORDER BY sr.id DESC
    LIMIT 4
");
$recentSaved->execute([$_SESSION['user_id']]);
$recentSavedRecipes = $recentSaved->fetchAll();

/* ── NEW: Most recent search ───────────────────────────── */
$lastSearch = $pdo->prepare("
    SELECT ingredients FROM ingredient_search_logs
    WHERE user_id = ?
    ORDER BY searched_at DESC
    LIMIT 1
");
$lastSearch->execute([$_SESSION['user_id']]);
$lastSearchRow = $lastSearch->fetch();
$lastIngredients = $lastSearchRow ? htmlspecialchars($lastSearchRow['ingredients']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Recipe Generator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard.css">
</head>
<body>

<!-- ── NAVBAR ───────────────────────────────────────────── -->

<?php include "partials/navbar.php"; ?>

    <!-- Quick search bar (toggled) -->
    <div class="quick-search-bar" id="quickSearchBar">
        <form action="ingredients.php" method="GET" class="quick-search-form">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="ingredients" placeholder="Type ingredients to search… e.g. egg, tomato, garlic" autocomplete="off">
            <button type="submit">Search</button>
        </form>
    </div>
</nav>

<!-- ── MAIN ──────────────────────────────────────────────── -->
<main class="container">

    <!-- ── Welcome banner ────────────────────────────────── -->
    <section class="welcome-card">
        <div class="welcome-left">
            <p class="welcome-eyebrow">Good <?= (date('H') < 12 ? 'morning' : (date('H') < 17 ? 'afternoon' : 'evening')) ?></p>
            <h2>Welcome back, <?= htmlspecialchars($userName) ?> 👋</h2>
            <p>Ready to cook something great? Search by ingredients or explore what's waiting for you.</p>
            <div class="welcome-actions">
                <a href="ingredients.php" class="primary-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Find by Ingredients
                </a>
                <?php if ($lastIngredients): ?>
                    <a href="ingredients.php?ingredients=<?= urlencode($lastIngredients) ?>" class="secondary-btn">
                        🔁 Repeat last search
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="welcome-right">
            <div class="welcome-art">🥘</div>
        </div>
    </section>

    <!-- ── Quick stats ────────────────────────────────────── -->
    <section class="stats-row">
        <div class="stat-card">
            <div class="stat-icon stat-icon-teal">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
            </div>
            <div class="stat-info">
                <span class="stat-number" data-target="<?= $savedCount ?>"><?= $savedCount ?></span>
                <span class="stat-label">Saved Recipes</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            <div class="stat-info">
                <span class="stat-number" data-target="<?= $searchCount ?>"><?= $searchCount ?></span>
                <span class="stat-label">Ingredient Searches</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </div>
            <div class="stat-info">
                <span class="stat-number" data-target="<?= $totalRecipes ?>"><?= $totalRecipes ?></span>
                <span class="stat-label">Recipes Available</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-rose">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
            </div>
            <div class="stat-info">
                <span class="stat-number"><?= count($preferences) ?></span>
                <span class="stat-label">Preferences Set</span>
            </div>
        </div>
    </section>

    <!-- ── Main content grid ─────────────────────────────── -->
    <div class="content-grid">

        <!-- LEFT: Categories + Recommended -->
        <div class="content-main">

            <!-- Categories -->
            <section class="section-block">
                <div class="section-header">
                    <h3 class="section-title">Browse Categories</h3>
                </div>
                <div class="categories">
                    <a href="recipes.php?category=Italian" class="category-card">
                        <span class="cat-emoji">🍝</span>
                        <span class="cat-label">Italian</span>
                    </a>
                    <a href="recipes.php?category=Asian" class="category-card">
                        <span class="cat-emoji">🍜</span>
                        <span class="cat-label">Asian</span>
                    </a>
                    <a href="recipes.php?category=Indian" class="category-card">
                        <span class="cat-emoji">🍛</span>
                        <span class="cat-label">Indian</span>
                    </a>
                    <a href="recipes.php?category=healthy" class="category-card">
                        <span class="cat-emoji">🥗</span>
                        <span class="cat-label">Healthy</span>
                    </a>
                    <a href="recipes.php?category=quick" class="category-card">
                        <span class="cat-emoji">⚡</span>
                        <span class="cat-label">Quick &amp; Easy</span>
                    </a>
                    <a href="recipes.php?category=comfort" class="category-card">
                        <span class="cat-emoji">🍔</span>
                        <span class="cat-label">Comfort Food</span>
                    </a>
                </div>
            </section>

            <!-- Recommended feed -->
            <section class="section-block">
                <div class="section-header">
                    <h3 class="section-title">Recommended for You</h3>
                    <span class="section-subtitle">Based on your preferences: <?= implode(', ', array_map('ucfirst', $preferences)) ?></span>
                </div>

                <div class="recipe-grid">
                    <?php if ($recipes): ?>
                        <?php foreach ($recipes as $i => $recipe): ?>
                            <div class="recipe-card" style="--card-i:<?= $i ?>">
                                <div class="recipe-card-top">
                                    <span class="recipe-category-pill"><?= htmlspecialchars(ucfirst($recipe['category'])) ?></span>
                                    <?php if (!empty($recipe['difficulty'])): ?>
                                        <span class="recipe-diff-pill diff-<?= strtolower($recipe['difficulty']) ?>"><?= ucfirst($recipe['difficulty']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <h4><?= htmlspecialchars($recipe['title']) ?></h4>
                                <div class="recipe-meta">
                                    <span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <?= (int)$recipe['cook_time'] ?> mins
                                    </span>
                                    <span>🔥 <?= $recipe['calories'] ?? 'N/A' ?> kcal</span>
                                </div>
                                <a href="recipe_detail.php?id=<?= $recipe['id'] ?>" class="link-btn">
                                    View Recipe
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-recipes">
                            <span>🍽</span>
                            <p>No recipes found for your preferences yet.<br>Try <a href="preferences.php">updating your preferences</a>.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        </div>

        <!-- RIGHT: Sidebar -->
        <aside class="content-sidebar">

            <!-- Quick ingredient search widget -->
            <div class="sidebar-card">
                <h4 class="sidebar-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Quick Search
                </h4>
                <form action="ingredients.php" method="GET" class="sidebar-search-form">
                    <input type="text" name="ingredients" placeholder="egg, tomato, garlic…" autocomplete="off">
                    <button type="submit">Go</button>
                </form>
                <p class="sidebar-hint">Enter comma-separated ingredients to find matching recipes.</p>
            </div>

            <!-- Recently saved -->
            <div class="sidebar-card">
                <h4 class="sidebar-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
                    Recently Saved
                </h4>
                <?php if (!empty($recentSavedRecipes)): ?>
                    <ul class="saved-list">
                        <?php foreach ($recentSavedRecipes as $sr): ?>
                            <li class="saved-item">
                                <a href="recipe_detail.php?id=<?= $sr['id'] ?>">
                                    <span class="saved-item-title"><?= htmlspecialchars($sr['title']) ?></span>
                                    <span class="saved-item-meta">
                                        <?= htmlspecialchars(ucfirst($sr['category'])) ?>
                                        · <?= (int)$sr['cook_time'] ?> min
                                    </span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="saved_recipes.php" class="sidebar-link">View all saved →</a>
                <?php else: ?>
                    <p class="sidebar-empty">No saved recipes yet. Start exploring and save your favourites!</p>
                <?php endif; ?>
            </div>

            <!-- Your preferences -->
            <div class="sidebar-card">
                <h4 class="sidebar-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    Your Preferences
                </h4>
                <div class="pref-tags">
                    <?php foreach ($preferences as $pref): ?>
                        <span class="pref-tag"><?= htmlspecialchars(ucfirst(trim($pref))) ?></span>
                    <?php endforeach; ?>
                </div>
                <a href="preferences.php" class="sidebar-link">Update preferences →</a>
            </div>

        </aside>
    </div>

</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Profile click → profile page (original logic)
    const profile = document.querySelector(".nav-profile");
    if (profile) {
        profile.style.cursor = "pointer";
    }

    // Close quick search bar when clicking outside
    document.addEventListener("click", (e) => {
        const bar = document.getElementById("quickSearchBar");
        const btn = document.querySelector(".nav-search-btn");
        if (bar && !bar.contains(e.target) && btn && !btn.contains(e.target)) {
            bar.classList.remove("open");
        }
    });

    // Staggered card entrance
    document.querySelectorAll(".recipe-card").forEach((card, i) => {
        card.style.animationDelay = (i * 0.07) + "s";
        card.classList.add("card-animate");
    });

    // Stat number count-up animation
    document.querySelectorAll(".stat-number[data-target]").forEach(el => {
        const target = parseInt(el.getAttribute("data-target"), 10);
        if (target === 0) return;
        let current = 0;
        const step  = Math.ceil(target / 30);
        const timer = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current;
            if (current >= target) clearInterval(timer);
        }, 30);
    });
});
</script>
</body>
</html>