<?php
require "api/session.php";
require "api/db.php";
requireLogin();

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT r.id, r.title, r.image, r.cook_time, n.calories
    FROM saved_recipes s
    JOIN recipes r ON s.recipe_id = r.id
    LEFT JOIN nutrition n ON r.id = n.recipe_id
    WHERE s.user_id = ?
    ORDER BY s.saved_at DESC
");
$stmt->execute([$userId]);
$recipes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Recipes — Recipe Generator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard.css">
    <link rel="stylesheet" href="assets/saved_recipes.css">
</head>
<body>

<?php include "partials/navbar.php"; ?>

<!-- ── PAGE HERO ─────────────────────────────────────── -->
<div class="sr-hero">
    <div class="sr-hero-inner">
        <div class="sr-hero-left">
            <span class="sr-hero-eyebrow">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                Your Collection
            </span>
            <h1 class="sr-hero-title">Saved Recipes</h1>
            <p class="sr-hero-sub">
                <?php $count = count($recipes); ?>
                <?= $count ?> <?= $count === 1 ? 'recipe' : 'recipes' ?> saved to your personal cookbook
            </p>
        </div>
        <div class="sr-hero-right">
            <div class="sr-stats-pills">
                <div class="sr-stat-pill">
                    <span class="sr-stat-pill-num"><?= $count ?></span>
                    <span class="sr-stat-pill-lbl">Saved</span>
                </div>
                <?php
                    $totalCals = array_sum(array_column($recipes, 'calories'));
                    $avgCals = $count > 0 ? round($totalCals / $count) : 0;
                ?>
                <div class="sr-stat-pill">
                    <span class="sr-stat-pill-num"><?= $avgCals ?></span>
                    <span class="sr-stat-pill-lbl">Avg kcal</span>
                </div>
                <?php
                    $totalTime = array_sum(array_column($recipes, 'cook_time'));
                ?>
                <div class="sr-stat-pill">
                    <span class="sr-stat-pill-num"><?= $totalTime ?></span>
                    <span class="sr-stat-pill-lbl">Total mins</span>
                </div>
            </div>
        </div>
    </div>
    <div class="sr-hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="var(--teal-bg)"/>
        </svg>
    </div>
</div>

<!-- ── MAIN CONTENT ───────────────────────────────────── -->
<div class="container">

    <!-- Toolbar -->
    <div class="sr-toolbar">
        <div class="sr-toolbar-left">
            <div class="sr-search-wrap">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" id="srSearch" placeholder="Search your saved recipes…" autocomplete="off">
            </div>
        </div>
        <div class="sr-toolbar-right">
            <div class="sr-sort-wrap">
                <label for="srSort">Sort by</label>
                <select id="srSort">
                    <option value="default">Recently Saved</option>
                    <option value="title">Title A–Z</option>
                    <option value="time-asc">Quickest First</option>
                    <option value="calories-asc">Lowest Calories</option>
                    <option value="calories-desc">Highest Calories</option>
                </select>
            </div>
            <div class="sr-view-toggle">
                <button class="sr-view-btn active" id="viewGrid" title="Grid view">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h7v7H3zm0 11h7v7H3zm11-11h7v7h-7zm0 11h7v7h-7z"/></svg>
                </button>
                <button class="sr-view-btn" id="viewList" title="List view">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18v2H3zm0 6h18v2H3zm0 6h18v2H3z"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Recipe Grid -->
    <?php if ($recipes): ?>
    <div class="sr-grid" id="srGrid">
        <?php foreach ($recipes as $i => $recipe): ?>
            <?php
            $imagePath = !empty($recipe['image'])
                ? "uploads/recipes/" . $recipe['image']
                : "assets/no-image.png";
            $hasImage = !empty($recipe['image']);
            $cals = $recipe['calories'] ?? null;
            $time = (int)$recipe['cook_time'];

            // Calorie badge color
            $calClass = 'cal-neutral';
            if ($cals !== null) {
                if ($cals < 300) $calClass = 'cal-low';
                elseif ($cals < 600) $calClass = 'cal-mid';
                else $calClass = 'cal-high';
            }
            ?>
            <div class="sr-card" 
                 style="animation-delay: <?= $i * 0.06 ?>s"
                 data-title="<?= htmlspecialchars(strtolower($recipe['title'])) ?>"
                 data-time="<?= $time ?>"
                 data-calories="<?= $cals ?? 0 ?>">

                <!-- Image area -->
                <div class="sr-card-img-wrap">
                    <img 
                        src="<?= htmlspecialchars($imagePath) ?>" 
                        alt="<?= htmlspecialchars($recipe['title']) ?>"
                        class="sr-card-img"
                        onerror="this.src='assets/no-image.png'">
                    <div class="sr-card-img-overlay"></div>

                    <!-- Floating badges -->
                    <div class="sr-card-badges">
                        <span class="sr-badge sr-badge-time">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            <?= $time ?> min
                        </span>
                        <?php if ($cals !== null): ?>
                        <span class="sr-badge sr-badge-cal <?= $calClass ?>">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C7 7 4 11 4 15a8 8 0 0016 0c0-4-3-8-8-13z"/></svg>
                            <?= $cals ?> kcal
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Remove btn on hover -->
                    <form method="POST" action="toggle_save.php" class="sr-remove-form">
                        <input type="hidden" name="recipe_id" value="<?= $recipe['id'] ?>">
                        <button type="submit" class="sr-remove-btn" title="Remove from saved">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        </button>
                    </form>
                </div>

                <!-- Card body -->
                <div class="sr-card-body">
                    <h3 class="sr-card-title"><?= htmlspecialchars($recipe['title']) ?></h3>

                    <div class="sr-card-meta">
                        <span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            <?= $time ?> mins cook time
                        </span>
                        <?php if ($cals !== null): ?>
                        <span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C7 7 4 11 4 15a8 8 0 0016 0c0-4-3-8-8-13z"/></svg>
                            <?= $cals ?> kcal
                        </span>
                        <?php endif; ?>
                    </div>

                    <div class="sr-card-footer">
                        <a href="recipe_detail.php?id=<?= $recipe['id'] ?>" class="sr-view-btn-link">
                            View Recipe
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                        <form method="POST" action="toggle_save.php" class="sr-remove-form-inline">
                            <input type="hidden" name="recipe_id" value="<?= $recipe['id'] ?>">
                            <button type="submit" class="sr-remove-inline-btn" title="Remove">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                Saved
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- No results (hidden by default, shown by JS) -->
    <div class="sr-no-results" id="srNoResults" style="display:none;">
        <div class="sr-no-results-icon">🔍</div>
        <h3>No recipes match your search</h3>
        <p>Try a different keyword</p>
    </div>

    <?php else: ?>
    <!-- Empty state -->
    <div class="sr-empty">
        <div class="sr-empty-art">
            <div class="sr-empty-circle"></div>
            <span class="sr-empty-emoji">🍽️</span>
        </div>
        <h2 class="sr-empty-title">Your cookbook is empty</h2>
        <p class="sr-empty-sub">Start exploring recipes and save the ones you love — they'll appear here for easy access anytime.</p>
        <a href="index.php" class="primary-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            Discover Recipes
        </a>
    </div>
    <?php endif; ?>

</div>

<script>
(function () {
    const grid = document.getElementById('srGrid');
    const searchInput = document.getElementById('srSearch');
    const sortSelect = document.getElementById('srSort');
    const noResults = document.getElementById('srNoResults');
    const viewGridBtn = document.getElementById('viewGrid');
    const viewListBtn = document.getElementById('viewList');

    if (!grid) return;

    function getCards() {
        return Array.from(grid.querySelectorAll('.sr-card'));
    }

    // ── Search ─────────────────────────────────────────
    function filterCards() {
        const q = searchInput.value.toLowerCase().trim();
        let visible = 0;
        getCards().forEach(card => {
            const title = card.dataset.title || '';
            const match = !q || title.includes(q);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        noResults.style.display = visible === 0 ? 'flex' : 'none';
    }

    searchInput && searchInput.addEventListener('input', filterCards);

    // ── Sort ───────────────────────────────────────────
    sortSelect && sortSelect.addEventListener('change', () => {
        const val = sortSelect.value;
        const cards = getCards();
        cards.sort((a, b) => {
            if (val === 'title') return a.dataset.title.localeCompare(b.dataset.title);
            if (val === 'time-asc') return parseInt(a.dataset.time) - parseInt(b.dataset.time);
            if (val === 'calories-asc') return parseInt(a.dataset.calories) - parseInt(b.dataset.calories);
            if (val === 'calories-desc') return parseInt(b.dataset.calories) - parseInt(a.dataset.calories);
            return 0; // default — keep DOM order
        });
        cards.forEach(c => grid.appendChild(c));
    });

    // ── View toggle ────────────────────────────────────
    viewGridBtn && viewGridBtn.addEventListener('click', () => {
        grid.classList.remove('sr-grid--list');
        viewGridBtn.classList.add('active');
        viewListBtn.classList.remove('active');
    });

    viewListBtn && viewListBtn.addEventListener('click', () => {
        grid.classList.add('sr-grid--list');
        viewListBtn.classList.add('active');
        viewGridBtn.classList.remove('active');
    });
})();
</script>

</body>
</html>