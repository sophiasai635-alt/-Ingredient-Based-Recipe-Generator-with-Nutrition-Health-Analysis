<?php
require "api/session.php";
require "api/db.php";

requireLogin();

if (!isset($_GET['id'])) {
    die("Recipe not found");
}

$recipeId = (int) $_GET['id'];

/* Fetch recipe */
$stmt = $pdo->prepare("
    SELECT r.*, n.calories, n.protein, n.carbs, n.fat
    FROM recipes r
    LEFT JOIN nutrition n ON r.id = n.recipe_id
    WHERE r.id = ?
");
$stmt->execute([$recipeId]);
$recipe = $stmt->fetch();

if (!$recipe) {
    die("Recipe not found");
}

/* Fetch ingredients */
$ingStmt = $pdo->prepare("
    SELECT i.name, ri.quantity
    FROM ingredients i
    JOIN recipe_ingredients ri ON i.id = ri.ingredient_id
    WHERE ri.recipe_id = ?
");
$ingStmt->execute([$recipeId]);
$ingredients = $ingStmt->fetchAll();

/* Save/unsave check */
$check = $pdo->prepare("SELECT id FROM saved_recipes WHERE user_id = ? AND recipe_id = ?");
$check->execute([$_SESSION['user_id'], $recipeId]);
$isSaved = $check->fetch();

/* Related recipes (same category, exclude current) */
$relatedStmt = $pdo->prepare("
    SELECT r.id, r.title, r.cook_time, r.difficulty, n.calories
    FROM recipes r
    LEFT JOIN nutrition n ON r.id = n.recipe_id
    WHERE r.category = ? AND r.id != ?
    ORDER BY r.created_at DESC
    LIMIT 3
");
$relatedStmt->execute([$recipe['category'], $recipeId]);
$relatedRecipes = $relatedStmt->fetchAll();

/* Parse steps into numbered array */
$rawSteps = trim($recipe['steps'] ?? '');
$steps = [];
foreach (explode("\n", $rawSteps) as $line) {
    $line = trim($line);
    if ($line !== '') {
        // Strip leading "1." / "1)" / "Step 1:" patterns if present
        $clean = preg_replace('/^(\d+[\.\)]\s*|step\s*\d+[:\.\)]\s*)/i', '', $line);
        $steps[] = $clean ?: $line;
    }
}
if (empty($steps)) {
    $steps[] = $rawSteps;
}

$imagePath = !empty($recipe['image'])
    ? "uploads/recipes/" . $recipe['image']
    : null;

$difficultyClass = strtolower($recipe['difficulty'] ?? 'easy');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($recipe['title']) ?> | Recipe Generator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard.css">
    <link rel="stylesheet" href="assets/recipe_detail.css">
</head>
<body>

<?php include "partials/navbar.php"; ?>

<!-- ── HERO ────────────────────────────────────────────── -->
<div class="rd-hero">
    <?php if ($imagePath): ?>
        <div class="rd-hero-img" style="background-image: url('<?= htmlspecialchars($imagePath) ?>')"></div>
    <?php else: ?>
        <div class="rd-hero-img rd-hero-placeholder"></div>
    <?php endif; ?>
    <div class="rd-hero-overlay"></div>

    <div class="rd-hero-content">
        <div class="rd-breadcrumb">
            <a href="dashboard.php">Home</a>
            <span>›</span>
            <a href="recipes.php?category=<?= urlencode($recipe['category']) ?>"><?= htmlspecialchars(ucfirst($recipe['category'])) ?></a>
            <span>›</span>
            <span><?= htmlspecialchars($recipe['title']) ?></span>
        </div>

        <div class="rd-hero-meta">
            <span class="rd-cat-pill"><?= htmlspecialchars(ucfirst($recipe['category'])) ?></span>
            <span class="rd-diff-pill rd-diff-<?= $difficultyClass ?>"><?= htmlspecialchars(ucfirst($recipe['difficulty'])) ?></span>
        </div>

        <h1 class="rd-title"><?= htmlspecialchars($recipe['title']) ?></h1>

        <p class="rd-subtitle"><?= htmlspecialchars($recipe['description']) ?></p>

        <div class="rd-hero-stats">
            <div class="rd-hero-stat">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <div>
                    <span class="rd-stat-val"><?= (int)$recipe['cook_time'] ?> min</span>
                    <span class="rd-stat-key">Cook Time</span>
                </div>
            </div>
            <div class="rd-hero-stat-divider"></div>
            <div class="rd-hero-stat">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 100 20A10 10 0 0012 2z"/><path d="M12 8v4l3 3"/></svg>
                <div>
                    <span class="rd-stat-val"><?= count($ingredients) ?></span>
                    <span class="rd-stat-key">Ingredients</span>
                </div>
            </div>
            <div class="rd-hero-stat-divider"></div>
            <div class="rd-hero-stat">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                <div>
                    <span class="rd-stat-val"><?= $recipe['calories'] ?? 'N/A' ?> kcal</span>
                    <span class="rd-stat-key">Calories</span>
                </div>
            </div>
            <div class="rd-hero-stat-divider"></div>
            <div class="rd-hero-stat">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                <div>
                    <span class="rd-stat-val"><?= $isSaved ? 'Saved' : 'Unsaved' ?></span>
                    <span class="rd-stat-key">Status</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── BODY ─────────────────────────────────────────────── -->
<div class="rd-body">
    <div class="rd-main-col">

        <!-- Ingredients -->
        <section class="rd-card rd-card-ingredients" id="section-ingredients">
            <div class="rd-card-header">
                <div class="rd-card-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 002-2V2"/><path d="M7 2v20"/><path d="M21 15V2"/><path d="M18 9h-4a2 2 0 00-2 2v2a2 2 0 002 2h4"/></svg>
                </div>
                <h2 class="rd-card-title">Ingredients</h2>
                <span class="rd-card-count"><?= count($ingredients) ?> items</span>
            </div>
            <ul class="rd-ingredient-list">
                <?php foreach ($ingredients as $i => $ing): ?>
                    <li class="rd-ingredient-item" style="--item-i:<?= $i ?>">
                        <label class="rd-ingredient-check">
                            <input type="checkbox" class="ing-checkbox">
                            <span class="ing-checkmark">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            </span>
                            <span class="ing-name"><?= htmlspecialchars(ucfirst($ing['name'])) ?></span>
                            <?php if (!empty($ing['quantity'])): ?>
                                <span class="ing-qty"><?= htmlspecialchars($ing['quantity']) ?></span>
                            <?php endif; ?>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="rd-ing-hint">✓ Check off ingredients as you gather them</p>
        </section>

        <!-- Steps -->
        <section class="rd-card" id="section-steps">
            <div class="rd-card-header">
                <div class="rd-card-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                </div>
                <h2 class="rd-card-title">Steps</h2>
                <span class="rd-card-count"><?= count($steps) ?> steps</span>
            </div>
            <ol class="rd-steps-list">
                <?php foreach ($steps as $idx => $step): ?>
                    <li class="rd-step-item" style="--step-i:<?= $idx ?>">
                        <div class="rd-step-num"><?= $idx + 1 ?></div>
                        <div class="rd-step-body">
                            <p><?= htmlspecialchars($step) ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </section>

    </div>

    <!-- ── SIDEBAR ───────────────────────────────────────── -->
    <aside class="rd-sidebar">

        <!-- Save / Unsave -->
        <div class="rd-sidebar-card rd-save-card">
            <form method="POST" action="toggle_save.php">
                <input type="hidden" name="recipe_id" value="<?= $recipeId ?>">
                <button type="submit" class="rd-save-btn <?= $isSaved ? 'is-saved' : '' ?>">
                    <?php if ($isSaved): ?>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
                        Saved to Collection
                    <?php else: ?>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
                        Save Recipe
                    <?php endif; ?>
                </button>
            </form>
            <a href="dashboard.php" class="rd-back-btn">← Back to Dashboard</a>
        </div>

        <!-- Nutrition Card -->
        <div class="rd-sidebar-card rd-nutrition-card">
            <div class="rd-card-header">
                <div class="rd-card-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                </div>
                <h2 class="rd-card-title">Nutrition</h2>
            </div>
            <div class="rd-nutrition-grid">
                <div class="rd-nut-item rd-nut-calories">
                    <span class="rd-nut-val"><?= $recipe['calories'] ?? '—' ?></span>
                    <span class="rd-nut-label">Calories</span>
                    <span class="rd-nut-unit">kcal</span>
                </div>
                <div class="rd-nut-item">
                    <span class="rd-nut-val"><?= $recipe['protein'] ?? '—' ?></span>
                    <span class="rd-nut-label">Protein</span>
                    <span class="rd-nut-unit">g</span>
                </div>
                <div class="rd-nut-item">
                    <span class="rd-nut-val"><?= $recipe['carbs'] ?? '—' ?></span>
                    <span class="rd-nut-label">Carbs</span>
                    <span class="rd-nut-unit">g</span>
                </div>
                <div class="rd-nut-item">
                    <span class="rd-nut-val"><?= $recipe['fat'] ?? '—' ?></span>
                    <span class="rd-nut-label">Fat</span>
                    <span class="rd-nut-unit">g</span>
                </div>
            </div>

            <!-- Macro bar -->
            <?php
            $protein = (float)($recipe['protein'] ?? 0);
            $carbs   = (float)($recipe['carbs']   ?? 0);
            $fat     = (float)($recipe['fat']      ?? 0);
            $macroTotal = $protein + $carbs + $fat;
            $pPct = $macroTotal > 0 ? round($protein / $macroTotal * 100) : 0;
            $cPct = $macroTotal > 0 ? round($carbs   / $macroTotal * 100) : 0;
            $fPct = $macroTotal > 0 ? round($fat     / $macroTotal * 100) : 0;
            ?>
            <?php if ($macroTotal > 0): ?>
                <div class="rd-macro-bar-wrap">
                    <div class="rd-macro-bar">
                        <div class="rd-macro-seg rd-macro-protein" style="width:<?= $pPct ?>%" title="Protein <?= $pPct ?>%"></div>
                        <div class="rd-macro-seg rd-macro-carbs"   style="width:<?= $cPct ?>%" title="Carbs <?= $cPct ?>%"></div>
                        <div class="rd-macro-seg rd-macro-fat"     style="width:<?= $fPct ?>%" title="Fat <?= $fPct ?>%"></div>
                    </div>
                    <div class="rd-macro-legend">
                        <span><span class="rd-macro-dot protein-dot"></span>Protein <?= $pPct ?>%</span>
                        <span><span class="rd-macro-dot carbs-dot"></span>Carbs <?= $cPct ?>%</span>
                        <span><span class="rd-macro-dot fat-dot"></span>Fat <?= $fPct ?>%</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick info card -->
        <div class="rd-sidebar-card rd-info-card">
            <div class="rd-card-header">
                <div class="rd-card-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <h2 class="rd-card-title">Recipe Info</h2>
            </div>
            <ul class="rd-info-list">
                <li>
                    <span class="rd-info-label">Category</span>
                    <span class="rd-info-val"><?= htmlspecialchars(ucfirst($recipe['category'])) ?></span>
                </li>
                <li>
                    <span class="rd-info-label">Difficulty</span>
                    <span class="rd-info-val rd-diff-inline rd-diff-<?= $difficultyClass ?>"><?= htmlspecialchars(ucfirst($recipe['difficulty'])) ?></span>
                </li>
                <li>
                    <span class="rd-info-label">Cook Time</span>
                    <span class="rd-info-val"><?= (int)$recipe['cook_time'] ?> minutes</span>
                </li>
                <li>
                    <span class="rd-info-label">Ingredients</span>
                    <span class="rd-info-val"><?= count($ingredients) ?> items</span>
                </li>
                <li>
                    <span class="rd-info-label">Steps</span>
                    <span class="rd-info-val"><?= count($steps) ?> steps</span>
                </li>
            </ul>
        </div>

    </aside>
</div>

<!-- ── RELATED RECIPES ─────────────────────────────────── -->
<?php if (!empty($relatedRecipes)): ?>
<div class="rd-related">
    <div class="rd-related-inner">
        <h2 class="rd-related-title">More <?= htmlspecialchars(ucfirst($recipe['category'])) ?> Recipes</h2>
        <div class="rd-related-grid">
            <?php foreach ($relatedRecipes as $rel): ?>
                <a href="recipe_detail.php?id=<?= $rel['id'] ?>" class="rd-related-card">
                    <div class="rd-related-body">
                        <h4><?= htmlspecialchars($rel['title']) ?></h4>
                        <div class="rd-related-meta">
                            <span>⏱ <?= (int)$rel['cook_time'] ?> min</span>
                            <span>🔥 <?= $rel['calories'] ?? 'N/A' ?> kcal</span>
                            <span class="rd-rel-diff rd-diff-<?= strtolower($rel['difficulty']) ?>"><?= htmlspecialchars(ucfirst($rel['difficulty'])) ?></span>
                        </div>
                    </div>
                    <svg class="rd-related-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {

    // ── Ingredient checkboxes ──────────────────────────────
    document.querySelectorAll(".ing-checkbox").forEach(cb => {
        cb.addEventListener("change", () => {
            const item = cb.closest(".rd-ingredient-item");
            item.classList.toggle("checked", cb.checked);
        });
    });

    // ── Stagger ingredient items on load ───────────────────
    document.querySelectorAll(".rd-ingredient-item, .rd-step-item").forEach((el, i) => {
        el.style.animationDelay = (i * 0.055) + "s";
        el.classList.add("item-animate");
    });

    // ── Step active highlight on click ─────────────────────
    document.querySelectorAll(".rd-step-item").forEach(step => {
        step.addEventListener("click", () => {
            step.classList.toggle("step-active");
        });
    });

});
</script>

</body>
</html>