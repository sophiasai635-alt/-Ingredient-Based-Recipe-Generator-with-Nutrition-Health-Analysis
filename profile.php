<?php
require "api/session.php";
require "api/db.php";

requireLogin();

$userId = $_SESSION['user_id'];

/* Fetch user details */
$stmt = $pdo->prepare("SELECT name, email, profile_pic, preferences, created_at, bio FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

/* Fetch saved recipes */
$stmt = $pdo->prepare("
    SELECT r.id, r.title, r.cook_time, n.calories
    FROM saved_recipes s
    JOIN recipes r ON s.recipe_id = r.id
    LEFT JOIN nutrition n ON r.id = n.recipe_id
    WHERE s.user_id = ?
    ORDER BY s.saved_at DESC
");
$stmt->execute([$userId]);
$savedRecipes = $stmt->fetchAll();

$savedCount = count($savedRecipes);

/* Preferences count */
$prefCount = !empty($user['preferences']) ? count(explode(",", $user['preferences'])) : 0;

/* Avatar initial */
$nameInitial = strtoupper(mb_substr(trim($user['name']), 0, 1));

/* Member since */
$memberSince = date("F Y", strtotime($user['created_at']));

/* Average calories */
$totalCals = 0; $calCount = 0;
foreach ($savedRecipes as $r) { if ($r['calories']) { $totalCals += $r['calories']; $calCount++; } }
$avgCals = $calCount > 0 ? round($totalCals / $calCount) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['name']) ?> — Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard.css">
    <link rel="stylesheet" href="assets/profile.css">
</head>
<body>

<?php include "partials/navbar.php"; ?>

<!-- ══ PROFILE HERO ══════════════════════════════════════ -->
<div class="pf-hero">
    <div class="pf-hero-bg">
        <div class="pf-hero-orb pf-hero-orb-1"></div>
        <div class="pf-hero-orb pf-hero-orb-2"></div>
        <div class="pf-hero-grid"></div>
    </div>

    <div class="pf-hero-inner">

        <!-- Avatar -->
        <div class="pf-avatar-wrap">
            <?php if (!empty($user['profile_pic']) && $user['profile_pic'] !== 'default.png'): ?>
                <img src="uploads/<?= htmlspecialchars($user['profile_pic']) ?>"
                     alt="<?= htmlspecialchars($user['name']) ?>"
                     class="pf-avatar-img">
            <?php else: ?>
                <div class="pf-avatar-initial"><?= $nameInitial ?></div>
            <?php endif; ?>
            <div class="pf-avatar-ring"></div>
        </div>

        <!-- Identity -->
        <div class="pf-identity">
            <div class="pf-member-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                Member since <?= $memberSince ?>
            </div>
            <h1 class="pf-name"><?= htmlspecialchars($user['name']) ?></h1>
            <p class="pf-email"><?= htmlspecialchars($user['email']) ?></p>

            <?php if (!empty($user['bio'])): ?>
                <p class="pf-bio"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
            <?php endif; ?>

            <a href="profile_edit.php" class="pf-edit-btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit Profile
            </a>
        </div>

        <!-- Stats panel -->
        <div class="pf-stats-panel">
            <div class="pf-stat-item">
                <span class="pf-stat-num"><?= $savedCount ?></span>
                <span class="pf-stat-lbl">Saved Recipes</span>
            </div>
            <div class="pf-stat-divider"></div>
            <div class="pf-stat-item">
                <span class="pf-stat-num"><?= $prefCount ?></span>
                <span class="pf-stat-lbl">Preferences</span>
            </div>
            <div class="pf-stat-divider"></div>
            <div class="pf-stat-item">
                <span class="pf-stat-num"><?= $avgCals ?></span>
                <span class="pf-stat-lbl">Avg kcal</span>
            </div>
        </div>

    </div>

    <div class="pf-hero-wave">
        <svg viewBox="0 0 1440 56" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,28 C480,56 960,0 1440,28 L1440,56 L0,56 Z" fill="var(--teal-bg)"/>
        </svg>
    </div>
</div>

<!-- ══ MAIN CONTENT ══════════════════════════════════════ -->
<div class="pf-container">

    <div class="pf-layout">

        <!-- LEFT SIDEBAR -->
        <aside class="pf-sidebar">

            <!-- About card -->
            <div class="pf-card pf-card-about">
                <div class="pf-card-header">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                    About
                </div>
                <div class="pf-card-body">
                    <?php if (!empty($user['bio'])): ?>
                        <p class="pf-about-text"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                    <?php else: ?>
                        <p class="pf-empty-text">You haven't written anything about yourself yet.</p>
                        <a href="profile_edit.php" class="pf-inline-link">Add a bio →</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Preferences card -->
            <?php if (!empty($user['preferences'])): ?>
            <div class="pf-card">
                <div class="pf-card-header">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                    Dietary Preferences
                </div>
                <div class="pf-card-body">
                    <div class="pf-pref-tags">
                        <?php foreach (explode(",", $user['preferences']) as $pref): ?>
                            <?php $p = trim($pref); if ($p): ?>
                            <span class="pf-pref-tag"><?= htmlspecialchars($p) ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Quick links -->
            <div class="pf-card">
                <div class="pf-card-header">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    Quick Links
                </div>
                <div class="pf-card-body">
                    <nav class="pf-quick-links">
                        <a href="saved_recipes.php" class="pf-quick-link">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            Saved Recipes
                            <span class="pf-quick-badge"><?= $savedCount ?></span>
                        </a>
                        <a href="index.php" class="pf-quick-link">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            Discover Recipes
                        </a>
                        <a href="profile_edit.php" class="pf-quick-link">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit Profile
                        </a>
                    </nav>
                </div>
            </div>

        </aside>

        <!-- MAIN PANEL -->
        <main class="pf-main">

            <!-- Saved Recipes -->
            <div class="pf-section">
                <div class="pf-section-header">
                    <div>
                        <h2 class="pf-section-title">Saved Recipes</h2>
                        <p class="pf-section-sub"><?= $savedCount ?> recipe<?= $savedCount !== 1 ? 's' : '' ?> in your cookbook</p>
                    </div>
                    <?php if ($savedCount > 0): ?>
                    <a href="saved_recipes.php" class="pf-see-all">See all →</a>
                    <?php endif; ?>
                </div>

                <?php if ($savedRecipes): ?>
                <div class="pf-recipe-grid">
                    <?php foreach ($savedRecipes as $i => $recipe): ?>
                    <div class="pf-recipe-card" style="animation-delay: <?= $i * 0.07 ?>s">
                        <div class="pf-recipe-card-inner">
                            <div class="pf-recipe-top">
                                <div class="pf-recipe-icon">🍽️</div>
                            </div>
                            <h4 class="pf-recipe-title"><?= htmlspecialchars($recipe['title']) ?></h4>
                            <div class="pf-recipe-meta">
                                <span>
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                    <?= (int)$recipe['cook_time'] ?> min
                                </span>
                                <span>
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C7 7 4 11 4 15a8 8 0 0016 0c0-4-3-8-8-13z"/></svg>
                                    <?= $recipe['calories'] ?? 'N/A' ?> kcal
                                </span>
                            </div>
                            <a href="recipe_detail.php?id=<?= $recipe['id'] ?>" class="pf-recipe-link">
                                View Recipe
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php else: ?>
                <div class="pf-empty-state">
                    <div class="pf-empty-icon">🍳</div>
                    <h3>No saved recipes yet</h3>
                    <p>Start exploring and save the recipes you love</p>
                    <a href="index.php" class="primary-btn">Discover Recipes</a>
                </div>
                <?php endif; ?>
            </div>

        </main>
    </div>
</div>

</body>
</html>