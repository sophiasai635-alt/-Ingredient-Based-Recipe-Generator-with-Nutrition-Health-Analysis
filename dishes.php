<?php
require "api/session.php";
require "api/db.php";
requireLogin();

$userId = $_SESSION['user_id'];

/* ── Search & Filter params ───────────────────────────── */
$search   = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$sort     = trim($_GET['sort'] ?? 'newest');
$diff     = trim($_GET['difficulty'] ?? '');

/* ══════════════════════════════════════════════════════
   AJAX: single recipe detail for modal
══════════════════════════════════════════════════════ */
if (isset($_GET['recipe_id']) && is_numeric($_GET['recipe_id'])) {
    $rStmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
    $rStmt->execute([(int)$_GET['recipe_id']]);
    $detail = $rStmt->fetch(PDO::FETCH_ASSOC);

    if ($detail) {
        /* Related: same category, exclude current */
        $relStmt = $pdo->prepare("
            SELECT id, title, image, cook_time, difficulty
            FROM recipes
            WHERE category = ? AND id != ?
            ORDER BY created_at DESC
            LIMIT 5
        ");
        $relStmt->execute([$detail['category'], $detail['id']]);
        $related = $relStmt->fetchAll(PDO::FETCH_ASSOC);

        /* Is this already saved by the user? */
        $svStmt = $pdo->prepare("SELECT 1 FROM saved_recipes WHERE user_id = ? AND recipe_id = ?");
        $svStmt->execute([$userId, $detail['id']]);
        $isSaved = (bool)$svStmt->fetchColumn();

        header('Content-Type: application/json');
        echo json_encode(['recipe' => $detail, 'related' => $related, 'is_saved' => $isSaved]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
    }
    exit;
}

/* ══════════════════════════════════════════════════════
   Main page query — recipes table
══════════════════════════════════════════════════════ */
$where  = [];
$params = [];

if ($search !== '') {
    $like     = "%$search%";
    $where[]  = "(r.title LIKE ? OR r.description LIKE ? OR r.category LIKE ?)";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}
if ($category !== '') {
    $where[]  = "r.category = ?";
    $params[] = $category;
}
if ($diff !== '') {
    $where[]  = "r.difficulty = ?";
    $params[] = $diff;
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

$orderSQL = match($sort) {
    'az'       => "ORDER BY r.title ASC",
    'za'       => "ORDER BY r.title DESC",
    'time-asc' => "ORDER BY r.cook_time ASC",
    'oldest'   => "ORDER BY r.created_at ASC",
    default    => "ORDER BY r.created_at DESC",
};

$stmt = $pdo->prepare("SELECT r.* FROM recipes r $whereSQL $orderSQL");
$stmt->execute($params);
$recipes    = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalCount = count($recipes);

/* All categories */
$catStmt = $pdo->query("SELECT DISTINCT category FROM recipes WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
$allCategories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

/* All difficulty values */
$diffStmt = $pdo->query("SELECT DISTINCT difficulty FROM recipes WHERE difficulty IS NOT NULL AND difficulty != '' ORDER BY FIELD(difficulty,'Easy','Medium','Hard')");
$allDifficulties = $diffStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find by Dishes — RecipeGen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard.css">
    <link rel="stylesheet" href="assets/dishes.css">
</head>
<body>

<?php include "partials/navbar.php"; ?>

<!-- ══ HERO ════════════════════════════════════════════ -->
<div class="dsh-hero">
    <div class="dsh-hero-bg">
        <div class="dsh-orb dsh-orb-1"></div>
        <div class="dsh-orb dsh-orb-2"></div>
        <div class="dsh-orb dsh-orb-3"></div>
        <div class="dsh-grid-texture"></div>
    </div>

    <div class="dsh-hero-inner">
        <span class="dsh-eyebrow">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M18.06 22.99h1.66c.84 0 1.53-.64 1.63-1.46L23 5.05h-5V1h-1.97v4.05h-4.97l.3 2.34c1.71.47 3.31 1.32 4.27 2.26 1.44 1.42 2.43 2.89 2.43 5.29v8.05zM1 21.99V21h15.03v.99c0 .55-.45 1-1.01 1H2.01c-.56 0-1.01-.45-1.01-1zm15.03-7c0-4.5-6.29-5-7.52-5-1.23 0-7.51.5-7.51 5h15.03zM1.02 17h15.03v2H1.02z"/></svg>
            Dish Explorer
        </span>

        <h1 class="dsh-hero-title">
            Find <em>Any Dish</em><br>You're Craving
        </h1>

        <p class="dsh-hero-sub">
            Browse all <?= $totalCount ?> dishes across <?= count($allCategories) ?> cuisines — search by name, filter by category or difficulty, and see full cooking steps.
        </p>

        <!-- Search bar -->
        <form method="GET" action="dishes.php" class="dsh-search-form" id="dishSearchForm">
            <input type="hidden" name="category"  value="<?= htmlspecialchars($category) ?>">
            <input type="hidden" name="sort"       value="<?= htmlspecialchars($sort) ?>">
            <input type="hidden" name="difficulty" value="<?= htmlspecialchars($diff) ?>">
            <div class="dsh-search-box">
                <svg class="dsh-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input
                    type="text"
                    name="search"
                    id="dishSearch"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search dishes… e.g. Biryani, Pasta, Curry"
                    autocomplete="off">
                <?php if ($search): ?>
                <a href="dishes.php?category=<?= urlencode($category) ?>&sort=<?= urlencode($sort) ?>&difficulty=<?= urlencode($diff) ?>" class="dsh-search-clear" title="Clear">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </a>
                <?php endif; ?>
                <button type="submit" class="dsh-search-btn">Search</button>
            </div>
        </form>

        <!-- Category browse chips — top 8 from DB -->
        <div class="dsh-suggestions">
            <span class="dsh-suggest-label">Browse:</span>
            <?php foreach (array_slice($allCategories, 0, 8) as $cat): ?>
            <a href="dishes.php?category=<?= urlencode($cat) ?>"
               class="dsh-suggest-chip <?= $category === $cat ? 'active' : '' ?>">
                <?= htmlspecialchars($cat) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="dsh-hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="var(--teal-bg)"/>
        </svg>
    </div>
</div>

<!-- ══ MAIN CONTENT ════════════════════════════════════ -->
<div class="container">

    <!-- ── Filters bar ─────────────────────────────────── -->
    <div class="dsh-filters-bar">

        <!-- Category pills (scrollable) -->
        <div class="dsh-filters-left">
            <div class="dsh-cat-pills">
                <a href="dishes.php?search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>&difficulty=<?= urlencode($diff) ?>"
                   class="dsh-cat-pill <?= $category === '' ? 'active' : '' ?>">All</a>
                <?php foreach ($allCategories as $cat): ?>
                <a href="dishes.php?search=<?= urlencode($search) ?>&category=<?= urlencode($cat) ?>&sort=<?= urlencode($sort) ?>&difficulty=<?= urlencode($diff) ?>"
                   class="dsh-cat-pill <?= $category === $cat ? 'active' : '' ?>">
                    <?= htmlspecialchars($cat) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="dsh-filters-right">

            <!-- Difficulty dropdown -->
            <?php if ($allDifficulties): ?>
            <div class="dsh-diff-wrap">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 20h.01M7 20v-4m5 4v-8m5 8V4"/></svg>
                <select onchange="applyDifficulty(this.value)">
                    <option value="">All Levels</option>
                    <?php foreach ($allDifficulties as $d): ?>
                    <option value="<?= htmlspecialchars($d) ?>" <?= $diff === $d ? 'selected' : '' ?>>
                        <?= htmlspecialchars(ucfirst($d)) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- Result count -->
            <span class="dsh-result-count">
                <strong><?= $totalCount ?></strong> <?= $totalCount === 1 ? 'dish' : 'dishes' ?>
                <?php if ($search): ?>for "<strong><?= htmlspecialchars($search) ?></strong>"<?php endif; ?>
            </span>

            <!-- Sort -->
            <div class="dsh-sort-wrap">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M7 12h10M11 18h2"/></svg>
                <select id="sortSelect" onchange="applySort(this.value)">
                    <option value="newest"   <?= $sort === 'newest'   ? 'selected' : '' ?>>Newest</option>
                    <option value="oldest"   <?= $sort === 'oldest'   ? 'selected' : '' ?>>Oldest</option>
                    <option value="az"       <?= $sort === 'az'       ? 'selected' : '' ?>>A → Z</option>
                    <option value="za"       <?= $sort === 'za'       ? 'selected' : '' ?>>Z → A</option>
                    <option value="time-asc" <?= $sort === 'time-asc' ? 'selected' : '' ?>>Quickest First</option>
                </select>
            </div>

            <!-- View toggle -->
            <div class="dsh-view-toggle">
                <button class="dsh-view-btn active" id="btnGrid" title="Grid" onclick="setView('grid')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h7v7H3zm0 11h7v7H3zm11-11h7v7h-7zm0 11h7v7h-7z"/></svg>
                </button>
                <button class="dsh-view-btn" id="btnList" title="List" onclick="setView('list')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18v2H3zm0 6h18v2H3zm0 6h18v2H3z"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ── Dish Grid ────────────────────────────────────── -->
    <?php if ($recipes): ?>
    <div class="dsh-grid" id="dishGrid">
        <?php foreach ($recipes as $i => $recipe):
            $imgPath   = !empty($recipe['image']) ? "uploads/recipes/" . $recipe['image'] : null;
            $snippet   = mb_strimwidth($recipe['description'] ?? '', 0, 88, '…');
            $diffClass = !empty($recipe['difficulty']) ? 'diff-' . strtolower($recipe['difficulty']) : '';
            $initials  = strtoupper(substr($recipe['title'], 0, 2));
        ?>
        <div class="dsh-card"
             style="animation-delay:<?= min($i * 0.05, 0.9) ?>s"
             onclick="openModal(<?= (int)$recipe['id'] ?>)"
             role="button"
             tabindex="0"
             onkeydown="if(event.key==='Enter')openModal(<?= (int)$recipe['id'] ?>)">

            <!-- Image area -->
            <div class="dsh-card-img-wrap">
                <?php if ($imgPath): ?>
                    <img src="<?= htmlspecialchars($imgPath) ?>"
                         alt="<?= htmlspecialchars($recipe['title']) ?>"
                         class="dsh-card-img"
                         loading="lazy"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="dsh-card-no-img" style="display:none;"><?= $initials ?></div>
                <?php else: ?>
                    <div class="dsh-card-no-img"><?= $initials ?></div>
                <?php endif; ?>

                <div class="dsh-card-overlay"></div>

                <!-- Category badge top-left -->
                <?php if (!empty($recipe['category'])): ?>
                <span class="dsh-card-cat-badge"><?= htmlspecialchars($recipe['category']) ?></span>
                <?php endif; ?>

                <!-- Difficulty badge top-right -->
                <?php if (!empty($recipe['difficulty'])): ?>
                <span class="dsh-card-diff-badge <?= $diffClass ?>"><?= htmlspecialchars(ucfirst($recipe['difficulty'])) ?></span>
                <?php endif; ?>

                <!-- Cook time bottom-left -->
                <?php if (!empty($recipe['cook_time'])): ?>
                <span class="dsh-card-time-badge">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    <?= (int)$recipe['cook_time'] ?> min
                </span>
                <?php endif; ?>

                <!-- Hover CTA -->
                <div class="dsh-card-cta">
                    <span>View Details</span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </div>
            </div>

            <!-- Body -->
            <div class="dsh-card-body">
                <h3 class="dsh-card-name"><?= htmlspecialchars($recipe['title']) ?></h3>
                <?php if ($snippet): ?>
                <p class="dsh-card-desc"><?= htmlspecialchars($snippet) ?></p>
                <?php endif; ?>
                <div class="dsh-card-footer">
                    <span class="dsh-card-added">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        <?= (int)$recipe['cook_time'] ?> mins cook time
                    </span>
                    <span class="dsh-card-open-link">Details →</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php else: ?>
    <!-- Empty state -->
    <div class="dsh-empty">
        <div class="dsh-empty-art">
            <div class="dsh-empty-circle"></div>
            <span class="dsh-empty-emoji">🍽️</span>
        </div>
        <h2 class="dsh-empty-title">
            <?= $search ? 'No dishes found for "' . htmlspecialchars($search) . '"' : 'No dishes found' ?>
        </h2>
        <p class="dsh-empty-sub">
            <?= ($search || $category || $diff) ? 'Try adjusting your filters or search term.' : 'No recipes have been added yet.' ?>
        </p>
        <?php if ($search || $category || $diff): ?>
        <a href="dishes.php" class="primary-btn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Clear all filters
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>

<!-- ══ MODAL ════════════════════════════════════════════ -->
<div class="dsh-modal-backdrop" id="modalBackdrop" onclick="closeModal()"></div>

<div class="dsh-modal" id="dishModal" role="dialog" aria-modal="true">

    <button class="dsh-modal-close" onclick="closeModal()" title="Close">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
    </button>

    <!-- Loading state -->
    <div class="dsh-modal-loading" id="modalLoading">
        <div class="dsh-modal-spinner"></div>
        <p>Loading dish…</p>
    </div>

    <!-- Populated by JS -->
    <div class="dsh-modal-content" id="modalContent" style="display:none;">

        <div class="dsh-modal-img-wrap" id="modalImgWrap"></div>

        <div class="dsh-modal-body">

            <div class="dsh-modal-header">
                <div class="dsh-modal-badges" id="modalBadges"></div>
                <h2 class="dsh-modal-title" id="modalTitle"></h2>
                <p class="dsh-modal-desc"  id="modalDesc"></p>
                <div class="dsh-modal-stats" id="modalStats"></div>
            </div>

            <div class="dsh-modal-section">
                <div class="dsh-modal-col-header">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Cooking Steps
                </div>
                <ol class="dsh-steps-list" id="modalSteps"></ol>
            </div>

            <div class="dsh-modal-related" id="modalRelated" style="display:none;">
                <div class="dsh-modal-col-header">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    More in this Category
                </div>
                <div class="dsh-related-grid" id="relatedGrid"></div>
            </div>

            <div class="dsh-modal-actions" id="modalActions"></div>

        </div>
    </div>
</div>

<script>
/* ── URL helpers ────────────────────────────────────── */
function applySort(val) {
    const u = new URL(window.location.href);
    u.searchParams.set('sort', val);
    window.location.href = u.toString();
}
function applyDifficulty(val) {
    const u = new URL(window.location.href);
    val ? u.searchParams.set('difficulty', val) : u.searchParams.delete('difficulty');
    window.location.href = u.toString();
}

/* ── View toggle ────────────────────────────────────── */
function setView(mode) {
    const grid = document.getElementById('dishGrid');
    const bG   = document.getElementById('btnGrid');
    const bL   = document.getElementById('btnList');
    if (!grid) return;
    grid.classList.toggle('dsh-grid--list', mode === 'list');
    bG.classList.toggle('active', mode !== 'list');
    bL.classList.toggle('active', mode === 'list');
    localStorage.setItem('dishView', mode);
}
(function(){ if (localStorage.getItem('dishView') === 'list') setView('list'); })();

/* ── Debounced search submit ────────────────────────── */
let sTimer;
const sInput = document.getElementById('dishSearch');
if (sInput) {
    sInput.addEventListener('input', () => {
        clearTimeout(sTimer);
        sTimer = setTimeout(() => document.getElementById('dishSearchForm').submit(), 500);
    });
}

/* ── Modal ──────────────────────────────────────────── */
function openModal(id) {
    document.getElementById('modalBackdrop').classList.add('open');
    document.getElementById('dishModal').classList.add('open');
    document.body.style.overflow = 'hidden';
    document.getElementById('modalLoading').style.display = 'flex';
    document.getElementById('modalContent').style.display = 'none';

    fetch(`dishes.php?recipe_id=${id}`)
        .then(r => r.json())
        .then(data => {
            renderModal(data.recipe, data.related || [], data.is_saved);
            document.getElementById('modalLoading').style.display = 'none';
            document.getElementById('modalContent').style.display = 'block';
        })
        .catch(() => {
            document.getElementById('modalLoading').innerHTML =
                '<p style="color:#E11D48;padding:24px;text-align:center">Failed to load. Please try again.</p>';
        });
}

function closeModal() {
    document.getElementById('modalBackdrop').classList.remove('open');
    document.getElementById('dishModal').classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

/* ── Render modal ───────────────────────────────────── */
function renderModal(recipe, related, isSaved) {

    /* Hero image */
    const imgWrap = document.getElementById('modalImgWrap');
    if (recipe.image) {
        imgWrap.innerHTML =
            `<img src="uploads/recipes/${esc(recipe.image)}"
                  alt="${esc(recipe.title)}"
                  class="dsh-modal-img"
                  onerror="this.parentElement.innerHTML='<div class=dsh-modal-no-img>${esc(recipe.title.substring(0,2).toUpperCase())}</div>'">
             <div class="dsh-modal-img-gradient"></div>`;
    } else {
        imgWrap.innerHTML = `<div class="dsh-modal-no-img">${esc(recipe.title.substring(0,2).toUpperCase())}</div>`;
    }
    imgWrap.style.display = 'block';

    /* Badges */
    let badges = '';
    if (recipe.category)   badges += `<span class="dsh-modal-cat-pill">${esc(recipe.category)}</span>`;
    if (recipe.difficulty) badges += `<span class="recipe-diff-pill diff-${recipe.difficulty.toLowerCase()}">${esc(recipe.difficulty)}</span>`;
    document.getElementById('modalBadges').innerHTML = badges;

    /* Title + desc */
    document.getElementById('modalTitle').textContent = recipe.title;
    document.getElementById('modalDesc').textContent  = recipe.description || '';

    /* Stats chips */
    let statsHTML = '';
    if (recipe.cook_time) {
        statsHTML += `<div class="dsh-stat-chip">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            ${esc(String(recipe.cook_time))} min cook time
        </div>`;
    }
    if (recipe.difficulty) {
        statsHTML += `<div class="dsh-stat-chip">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 20h.01M7 20v-4m5 4v-8m5 8V4"/></svg>
            ${esc(recipe.difficulty)} difficulty
        </div>`;
    }
    if (recipe.created_at) {
        const formatted = new Date(recipe.created_at).toLocaleDateString('en-US', {year:'numeric',month:'short',day:'numeric'});
        statsHTML += `<div class="dsh-stat-chip">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Added ${formatted}
        </div>`;
    }
    document.getElementById('modalStats').innerHTML = statsHTML;

    /* Steps — support numbered, newline, comma/semicolon formats */
    const raw = (recipe.steps || '').trim();
    let steps = [];
    if (/^\s*\d+[\.\)]\s/.test(raw)) {
        steps = raw.split(/\n?\s*\d+[\.\)]\s+/).map(s => s.trim()).filter(Boolean);
    } else if (raw.includes('\n')) {
        steps = raw.split('\n').map(s => s.trim()).filter(Boolean);
    } else {
        steps = raw.split(/[;]/).map(s => s.trim()).filter(Boolean);
        if (steps.length === 1) steps = raw.split(',').map(s => s.trim()).filter(Boolean);
    }

    document.getElementById('modalSteps').innerHTML = steps.length
        ? steps.map((s, i) =>
            `<li class="dsh-step-item">
                <span class="dsh-step-num">${i + 1}</span>
                <span class="dsh-step-text">${esc(s)}</span>
             </li>`).join('')
        : '<li class="dsh-step-empty">No steps available for this recipe.</li>';

    /* Related recipes — clicking opens another modal */
    const relSection = document.getElementById('modalRelated');
    const relGrid    = document.getElementById('relatedGrid');
    if (related.length > 0) {
        relGrid.innerHTML = related.map(r => {
            const img = r.image
                ? `<img src="uploads/recipes/${esc(r.image)}" alt="${esc(r.title)}"
                        onerror="this.parentElement.innerHTML='<div class=dsh-related-no-img>${esc(r.title.substring(0,2).toUpperCase())}</div>'">`
                : `<div class="dsh-related-no-img">${esc(r.title.substring(0,2).toUpperCase())}</div>`;
            const dc = r.difficulty ? 'diff-' + r.difficulty.toLowerCase() : '';
            return `<div class="dsh-related-card" onclick="event.stopPropagation();openModal(${r.id})">
                        <div class="dsh-related-img-wrap">${img}</div>
                        <div class="dsh-related-info">
                            <span class="dsh-related-title">${esc(r.title)}</span>
                            <div class="dsh-related-meta">
                                ${r.cook_time ? `<span>⏱ ${r.cook_time} min</span>` : ''}
                                ${r.difficulty ? `<span class="recipe-diff-pill ${dc}">${esc(r.difficulty)}</span>` : ''}
                            </div>
                        </div>
                    </div>`;
        }).join('');
        relSection.style.display = 'block';
    } else {
        relSection.style.display = 'none';
    }

    /* Footer actions */
    const savedLabel = isSaved ? '❤️ Saved' : '🤍 Save Recipe';
    const savedClass = isSaved ? 'dsh-btn-saved' : '';
    document.getElementById('modalActions').innerHTML =
        `<a href="recipe_detail.php?id=${recipe.id}" class="dsh-action-primary">
            View Full Recipe
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
         </a>
         <form method="POST" action="toggle_save.php" onsubmit="return handleSave(event,${recipe.id})">
             <input type="hidden" name="recipe_id" value="${recipe.id}">
             <button type="submit" class="dsh-action-save ${savedClass}" id="saveBtn_${recipe.id}">${savedLabel}</button>
         </form>`;
}

/* Save with optimistic UI */
function handleSave(e, id) {
    e.preventDefault();
    const btn     = document.getElementById('saveBtn_' + id);
    const wasSaved = btn.classList.contains('dsh-btn-saved');
    btn.classList.toggle('dsh-btn-saved');
    btn.textContent = wasSaved ? '🤍 Save Recipe' : '❤️ Saved';

    fetch('toggle_save.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'recipe_id=' + id
    }).catch(() => {
        btn.classList.toggle('dsh-btn-saved');
        btn.textContent = wasSaved ? '❤️ Saved' : '🤍 Save Recipe';
    });
    return false;
}

function esc(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(String(str || '')));
    return d.innerHTML;
}
</script>

</body>
</html>