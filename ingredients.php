<?php
require "api/session.php";
require "api/db.php";
requireLogin();

$results             = [];
$notFoundIngredients = [];

$filterOrder      = $_GET['order']      ?? '';
$filterDifficulty = $_GET['difficulty'] ?? '';
$filterCategory   = $_GET['category']   ?? '';

/* ================================================================
   SMART INGREDIENT MATCHING ALGORITHM
   ----------------------------------------------------------------
   1. Parse raw input → clean tokens (trim, lowercase)
   2. Fuzzy-resolve each token: exact → prefix → partial LIKE
      so typos like "tomatoe" still match "tomato"
   3. For every recipe that uses ANY matched ingredient, compute:
        matched_count  = how many of YOUR ingredients the recipe uses
        recipe_total   = total ingredients the recipe requires
   4. Two coverage scores:
        you_coverage    = matched / you_typed        (using what you have)
        recipe_coverage = matched / recipe_total     (how complete you are)
   5. Smart Score = (you_coverage×0.4 + recipe_coverage×0.6) × 100
      Weighted toward recipe completeness so nearly-complete recipes rank higher
   6. Fetch per-card missing ingredients so user knows what to still buy
   7. Log search to ingredient_search_logs
================================================================ */

if (isset($_GET['ingredients'])) {

    // 1. Parse tokens
    $rawTokens = array_values(array_filter(
        array_map('trim', explode(',', strtolower($_GET['ingredients'])))
    ));

    if (!empty($rawTokens) && count($rawTokens) <= 10) {

        // 2. Fuzzy-resolve each token
        $resolvedIds = [];   // [ ingredient_id => ingredient_name ]

        foreach ($rawTokens as $token) {
            $stmt = $pdo->prepare(
                "SELECT id, name FROM ingredients
                 WHERE LOWER(name) = ?
                    OR LOWER(name) LIKE ?
                    OR LOWER(name) LIKE ?
                 LIMIT 1"
            );
            $stmt->execute([$token, $token . '%', '%' . $token . '%']);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $resolvedIds[$row['id']] = $row['name'];
            } else {
                $notFoundIngredients[] = $token;
            }
        }

        $matchedIngredientIds = array_keys($resolvedIds);
        $youTypedCount        = count($rawTokens);

        // 7. Log search
        try {
            $pdo->prepare(
                "INSERT INTO ingredient_search_logs (user_id, ingredients, searched_at) VALUES (?, ?, NOW())"
            )->execute([$_SESSION['user_id'], $_GET['ingredients']]);
        } catch (Exception $e) { /* silently ignore */ }

        if (!empty($matchedIngredientIds)) {

            $inList = str_repeat('?,', count($matchedIngredientIds) - 1) . '?';

            // 3. Fetch recipes + matched / total counts in one query
            $sql = "
                SELECT
                    r.id, r.title, r.image, r.cook_time, r.difficulty, r.category,
                    n.calories,
                    COUNT(DISTINCT CASE WHEN ri.ingredient_id IN ($inList)
                          THEN ri.ingredient_id END)  AS matched_count,
                    COUNT(DISTINCT ri.ingredient_id)  AS recipe_total
                FROM recipes r
                JOIN recipe_ingredients ri ON r.id = ri.recipe_id
                LEFT JOIN nutrition n      ON r.id = n.recipe_id
                WHERE ri.ingredient_id IN ($inList)
            ";

            $params = array_merge($matchedIngredientIds, $matchedIngredientIds); // CASE + WHERE

            if (!empty($filterDifficulty)) { $sql .= " AND r.difficulty = ?"; $params[] = $filterDifficulty; }
            if (!empty($filterCategory))   { $sql .= " AND r.category = ?";   $params[] = $filterCategory; }

            $sql .= " GROUP BY r.id, r.title, r.image, r.cook_time, r.difficulty, r.category, n.calories";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rawRecipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 4, 5, 6 — score + missing
            $scored = [];

            foreach ($rawRecipes as $recipe) {
                $matched     = (int) $recipe['matched_count'];
                $recipeTotal = (int) $recipe['recipe_total'];
                if ($matched === 0) continue;

                $youCoverage    = $youTypedCount > 0 ? $matched / $youTypedCount : 0;
                $recipeCoverage = $recipeTotal   > 0 ? $matched / $recipeTotal   : 0;
                $smartScore     = round(($youCoverage * 0.4 + $recipeCoverage * 0.6) * 100);

                // Fetch missing ingredient names (cap display at 6)
                $missingStmt = $pdo->prepare(
                    "SELECT i.name FROM recipe_ingredients ri
                     JOIN ingredients i ON ri.ingredient_id = i.id
                     WHERE ri.recipe_id = ? AND ri.ingredient_id NOT IN ($inList)
                     ORDER BY i.name LIMIT 6"
                );
                $missingStmt->execute(array_merge([$recipe['id']], $matchedIngredientIds));
                $missingNames = $missingStmt->fetchAll(PDO::FETCH_COLUMN);

                $scored[] = [
                    'id'              => $recipe['id'],
                    'title'           => $recipe['title'],
                    'image'           => $recipe['image'],
                    'cook_time'       => $recipe['cook_time'],
                    'calories'        => $recipe['calories'],
                    'difficulty'      => $recipe['difficulty'],
                    'category'        => $recipe['category'],
                    'matched'         => $matched,
                    'recipe_total'    => $recipeTotal,
                    'you_coverage'    => round($youCoverage    * 100),
                    'recipe_coverage' => round($recipeCoverage * 100),
                    'smart_score'     => $smartScore,
                    'missing'         => $missingNames,
                ];
            }

            // Sort
            if ($filterOrder === 'time') {
                usort($scored, fn($a, $b) => $a['cook_time'] <=> $b['cook_time']);
            } elseif ($filterOrder === 'calories') {
                usort($scored, fn($a, $b) => ($a['calories'] ?? PHP_INT_MAX) <=> ($b['calories'] ?? PHP_INT_MAX));
            } else {
                usort($scored, fn($a, $b) => $b['smart_score'] <=> $a['smart_score']);
            }

            $results = $scored;
        }
    }
}

$difficulties   = $pdo->query("SELECT DISTINCT difficulty FROM recipes ORDER BY difficulty")->fetchAll(PDO::FETCH_COLUMN);
$categories     = $pdo->query("SELECT DISTINCT category   FROM recipes ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$perfectMatches = count(array_filter($results, fn($r) => $r['smart_score'] === 100));
$highMatches    = count(array_filter($results, fn($r) => $r['smart_score'] >= 70 && $r['smart_score'] < 100));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Recipes by Ingredients</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/ingredients.css">
    <link rel="stylesheet" href="assets/dashboard.css">
</head>
<body>

<?php include "partials/navbar.php"; ?>

<!-- ── Hero ──────────────────────────────────────────── -->
<div class="page-hero">
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
    <div class="hero-orb hero-orb-3"></div>
    <div class="hero-inner">
        <span class="hero-eyebrow">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            Smart Recipe Finder
        </span>
        <h1 class="hero-title">What's in your kitchen?</h1>
        <p class="hero-subtitle">Enter your ingredients and our algorithm ranks every recipe by match score — and tells you exactly what's missing.</p>
        <div class="hero-pills">
            <span class="hero-pill">🎯 Smart scoring</span>
            <span class="hero-pill">🔤 Typo-tolerant</span>
            <span class="hero-pill">📋 Missing list</span>
        </div>
    </div>
</div>

<div class="container">

    <!-- ── Search Card ───────────────────────────────── -->
    <div class="search-card">
        <form method="GET" id="searchForm">

            <div class="input-row">
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 002-2V2"/><path d="M7 2v20"/><path d="M21 15V2"/><path d="M18 9h-4a2 2 0 00-2 2v2a2 2 0 002 2h4"/></svg>
                    </span>
                    <input
                        type="text"
                        name="ingredients"
                        id="ingredientInput"
                        placeholder="egg, milk, tomato, garlic, onion…"
                        value="<?= htmlspecialchars($_GET['ingredients'] ?? '') ?>"
                        required
                        autocomplete="off"
                        spellcheck="false"
                    >
                    <span class="input-counter" id="tagCount"></span>
                </div>
                <button type="submit" class="search-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Find Recipes
                </button>
            </div>

            <div class="tag-preview" id="tagPreview"></div>

            <div class="filters-row">
                <div class="select-group">
                    <label class="filter-label">Sort by</label>
                    <div class="select-wrapper">
                        <select name="order">
                            <option value="">Default</option>
                            <option value="time"     <?= $filterOrder==='time'     ?'selected':'' ?>>⏱ Cook Time</option>
                            <option value="calories" <?= $filterOrder==='calories' ?'selected':'' ?>>🔥 Calories</option>
                            <option value="match"    <?= $filterOrder==='match'    ?'selected':'' ?>>🎯 Smart Score</option>
                        </select>
                    </div>
                </div>
                <div class="select-group">
                    <label class="filter-label">Difficulty</label>
                    <div class="select-wrapper">
                        <select name="difficulty">
                            <option value="">Any</option>
                            <?php foreach ($difficulties as $diff): ?>
                                <option value="<?= htmlspecialchars($diff) ?>" <?= $filterDifficulty===$diff?'selected':'' ?>>
                                    <?= ucfirst(htmlspecialchars($diff)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="select-group">
                    <label class="filter-label">Category</label>
                    <div class="select-wrapper">
                        <select name="category">
                            <option value="">All</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>" <?= $filterCategory===$cat?'selected':'' ?>>
                                    <?= ucfirst(htmlspecialchars($cat)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

        </form>

        <div class="search-footer">
            <span class="form-hint">Up to 10 ingredients, comma-separated · Typos handled automatically</span>
            <?php if (!empty($notFoundIngredients)): ?>
                <div class="warn-banner">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <strong><?= implode(', ', array_map('htmlspecialchars', $notFoundIngredients)) ?></strong>
                    <?= count($notFoundIngredients) > 1 ? 'were' : 'was' ?> not recognised and skipped.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Results header ────────────────────────────── -->
    <?php if (isset($_GET['ingredients']) && !empty($results)): ?>
        <div class="results-header">
            <div class="results-left">
                <span class="results-count"><strong><?= count($results) ?></strong> recipe<?= count($results) !== 1 ? 's' : '' ?> found</span>
                <div class="tier-pills">
                    <?php if ($perfectMatches > 0): ?>
                        <span class="tier-pill tier-perfect">🎯 <?= $perfectMatches ?> perfect</span>
                    <?php endif; ?>
                    <?php if ($highMatches > 0): ?>
                        <span class="tier-pill tier-high">✅ <?= $highMatches ?> great</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="score-legend">
                <span class="legend-label">Score:</span>
                <span class="legend-item"><span class="legend-dot dot-perfect"></span>100%</span>
                <span class="legend-item"><span class="legend-dot dot-high"></span>70–99%</span>
                <span class="legend-item"><span class="legend-dot dot-mid"></span>40–69%</span>
                <span class="legend-item"><span class="legend-dot dot-low"></span>&lt;40%</span>
            </div>
        </div>
    <?php endif; ?>

    <!-- ── Cards ─────────────────────────────────────── -->
    <div class="results">

        <?php if (!empty($results)): ?>
            <?php foreach ($results as $i => $r): ?>
                <?php
                $imagePath    = !empty($r['image']) ? "uploads/recipes/" . $r['image'] : "assets/no-image.png";
                $score        = $r['smart_score'];
                $scoreClass   = $score === 100 ? 'score-perfect'
                              : ($score >= 70  ? 'score-high'
                              : ($score >= 40  ? 'score-mid' : 'score-low'));
                $missingTotal = $r['recipe_total'] - $r['matched'];
                ?>

                <article class="recipe-card <?= $scoreClass ?>" style="--card-i:<?= $i ?>">

                    <div class="card-image-wrap">
                        <img src="<?= htmlspecialchars($imagePath) ?>"
                             alt="<?= htmlspecialchars($r['title']) ?>"
                             loading="lazy">
                        <div class="card-image-overlay"></div>

                        <!-- Animated score ring -->
                        <div class="score-ring-wrap">
                            <svg class="score-ring" viewBox="0 0 44 44">
                                <circle class="ring-track" cx="22" cy="22" r="18"/>
                                <circle class="ring-fill"  cx="22" cy="22" r="18" data-pct="<?= $score ?>"/>
                            </svg>
                            <span class="score-ring-label"><?= $score ?>%</span>
                        </div>

                        <span class="card-cat-pill"><?= htmlspecialchars(ucfirst($r['category'])) ?></span>
                    </div>

                    <div class="card-body">

                        <h3 class="card-title"><?= htmlspecialchars($r['title']) ?></h3>

                        <!-- Dual coverage progress bars -->
                        <div class="coverage-block">
                            <div class="cov-row">
                                <span class="cov-label">Your ingredients used</span>
                                <span class="cov-val"><?= $r['you_coverage'] ?>%</span>
                            </div>
                            <div class="cov-track"><div class="cov-bar cov-you" style="width:<?= $r['you_coverage'] ?>%"></div></div>

                            <div class="cov-row" style="margin-top:8px">
                                <span class="cov-label">Recipe completeness</span>
                                <span class="cov-val"><?= $r['recipe_coverage'] ?>%</span>
                            </div>
                            <div class="cov-track"><div class="cov-bar cov-recipe" style="width:<?= $r['recipe_coverage'] ?>%"></div></div>
                        </div>

                        <!-- Meta chips -->
                        <div class="meta-row">
                            <span class="meta-chip">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <?= $r['cook_time'] ?> min
                            </span>
                            <span class="meta-chip">🔥 <?= $r['calories'] ?? 'N/A' ?> kcal</span>
                            <span class="meta-chip diff-chip"><?= htmlspecialchars(ucfirst($r['difficulty'])) ?></span>
                            <span class="meta-chip ingr-chip"><?= $r['matched'] ?>/<?= $r['recipe_total'] ?> ingr.</span>
                        </div>

                        <!-- Missing ingredients or all-good -->
                        <?php if (!empty($r['missing'])): ?>
                            <div class="missing-block">
                                <span class="missing-label">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Still need:
                                </span>
                                <div class="missing-tags">
                                    <?php foreach ($r['missing'] as $m): ?>
                                        <span class="missing-tag"><?= htmlspecialchars($m) ?></span>
                                    <?php endforeach; ?>
                                    <?php if ($missingTotal > 6): ?>
                                        <span class="missing-tag missing-more">+<?= $missingTotal - 6 ?> more</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="all-good">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                You have all the ingredients!
                            </div>
                        <?php endif; ?>

                        <!-- Actions -->
                        <div class="card-actions">
                            <a href="recipe_detail.php?id=<?= $r['id'] ?>" class="view-btn">
                                View Recipe
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </a>
                            <form method="POST" action="toggle_save.php">
                                <input type="hidden" name="recipe_id" value="<?= $r['id'] ?>">
                                <?php
                                $check = $pdo->prepare("SELECT id FROM saved_recipes WHERE user_id = ? AND recipe_id = ?");
                                $check->execute([$_SESSION['user_id'], $r['id']]);
                                $isSaved = $check->fetch();
                                ?>
                                <button type="submit" class="save-btn <?= $isSaved ? 'is-saved' : '' ?>"
                                        title="<?= $isSaved ? 'Saved' : 'Save recipe' ?>">
                                    <?= $isSaved ? '❤️' : '🤍' ?>
                                </button>
                            </form>
                        </div>

                    </div>
                </article>

            <?php endforeach; ?>

        <?php elseif (isset($_GET['ingredients'])): ?>
            <div class="empty-state">
                <div class="empty-icon">🔍</div>
                <h3>No recipes found</h3>
                <p>Nothing matched <strong>"<?= htmlspecialchars($_GET['ingredients']) ?>"</strong>.<br>Try fewer or different ingredients.</p>
            </div>

        <?php else: ?>
            <div class="empty-state start-state">
                <div class="empty-icon">🥗</div>
                <h3>Ready to cook something?</h3>
                <p>Enter the ingredients you have on hand — our algorithm scores every recipe and shows you exactly what's still missing.</p>
                <div class="how-it-works">
                    <div class="how-step"><span class="how-num">1</span><p>Type your ingredients</p></div>
                    <div class="how-arrow">→</div>
                    <div class="how-step"><span class="how-num">2</span><p>Algorithm scores recipes</p></div>
                    <div class="how-arrow">→</div>
                    <div class="how-step"><span class="how-num">3</span><p>See what's missing</p></div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
// ── Live tag preview ──────────────────────────────────────
const input      = document.getElementById('ingredientInput');
const tagPreview = document.getElementById('tagPreview');
const tagCount   = document.getElementById('tagCount');

function renderTags() {
    if (!input) return;
    const parts = input.value.split(',').map(s => s.trim()).filter(Boolean);

    if (tagCount) {
        tagCount.textContent   = parts.length ? parts.length + ' ingredient' + (parts.length !== 1 ? 's' : '') : '';
        tagCount.style.display = parts.length ? 'inline-flex' : 'none';
    }
    if (tagPreview) {
        tagPreview.innerHTML     = '';
        tagPreview.style.display = parts.length ? 'flex' : 'none';
        parts.forEach(p => {
            const el = document.createElement('span');
            el.className   = 'live-tag';
            el.textContent = p;
            tagPreview.appendChild(el);
        });
    }
}

input?.addEventListener('input', renderTags);
renderTags(); // run on page load (for pre-filled values)

// ── Validation ────────────────────────────────────────────
document.getElementById('searchForm')?.addEventListener('submit', e => {
    const parts = (input?.value || '').split(',').map(s => s.trim()).filter(Boolean);
    if (parts.length > 10) {
        alert('Please enter at most 10 ingredients.');
        e.preventDefault();
    }
});

// ── Staggered card entrance ───────────────────────────────
document.querySelectorAll('.recipe-card').forEach((card, i) => {
    card.style.animationDelay = (i * 0.065) + 's';
    card.classList.add('card-animate');
});

// ── Animate SVG score rings ───────────────────────────────
document.querySelectorAll('.ring-fill').forEach(ring => {
    const pct          = parseFloat(ring.getAttribute('data-pct') || 0);
    const circumference = 2 * Math.PI * 18; // r=18
    const offset       = circumference * (1 - pct / 100);

    ring.style.strokeDasharray  = circumference;
    ring.style.strokeDashoffset = circumference; // start at 0%

    // Double rAF forces the browser to register the initial state
    requestAnimationFrame(() => requestAnimationFrame(() => {
        ring.style.transition       = 'stroke-dashoffset 1s cubic-bezier(0.4, 0, 0.2, 1)';
        ring.style.strokeDashoffset = offset;
    }));
});
</script>

</body>
</html>