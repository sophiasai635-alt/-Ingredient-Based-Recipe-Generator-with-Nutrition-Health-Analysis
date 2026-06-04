<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = isset($_SESSION['name']) ? strtoupper($_SESSION['name']) : 'USER';
$navUserInitial = strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1));

/* Saved count badge + profile pic — only query if $pdo is available */
$navSavedCount = 0;
$navProfilePic = '';
if (isset($pdo) && !empty($_SESSION['user_id'])) {
    try {
        $navSavedStmt = $pdo->prepare("SELECT COUNT(*) FROM saved_recipes WHERE user_id = ?");
        $navSavedStmt->execute([$_SESSION['user_id']]);
        $navSavedCount = (int) $navSavedStmt->fetchColumn();
    } catch (Exception $e) { /* silently ignore */ }

    try {
        $navPicStmt = $pdo->prepare("SELECT profile_pic FROM users WHERE id = ?");
        $navPicStmt->execute([$_SESSION['user_id']]);
        $navProfilePic = (string) $navPicStmt->fetchColumn();
    } catch (Exception $e) { /* silently ignore */ }
}

$navCurrent = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
    <div class="nav-inner">

        <!-- Logo -->
        <div class="nav-left">
            <a href="dashboard.php" class="logo">
                <span class="logo-icon">🍽</span>
                <span class="logo-text">RecipeGen</span>
            </a>
        </div>

        <!-- Center links -->
        <div class="nav-center">
            <a href="dashboard.php" class="nav-link <?= $navCurrent === 'dashboard.php' ? 'active' : '' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="ingredients.php" class="nav-link <?= $navCurrent === 'ingredients.php' ? 'active' : '' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 002-2V2"/><path d="M7 2v20"/><path d="M21 15V2"/><path d="M18 9h-4a2 2 0 00-2 2v2a2 2 0 002 2h4"/></svg>
                Find by Ingredients
            </a>
            <a href="dishes.php" class="nav-link <?= $navCurrent === 'dishes.php' ? 'active' : '' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.06 22.99h1.66c.84 0 1.53-.64 1.63-1.46L23 5.05h-5V1h-1.97v4.05h-4.97l.3 2.34c1.71.47 3.31 1.32 4.27 2.26 1.44 1.42 2.43 2.89 2.43 5.29v8.05zM1 21.99V21h15.03v.99c0 .55-.45 1-1.01 1H2.01c-.56 0-1.01-.45-1.01-1zm15.03-7c0-4.5-6.29-5-7.52-5-1.23 0-7.51.5-7.51 5h15.03zM1.02 17h15.03v2H1.02z"/></svg>
                Find by Dishes
            </a>
            <a href="saved_recipes.php" class="nav-link <?= $navCurrent === 'saved_recipes.php' ? 'active' : '' ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
                Saved
                <?php if ($navSavedCount > 0): ?>
                    <span class="nav-badge"><?= $navSavedCount ?></span>
                <?php endif; ?>
            </a>
        </div>

        <!-- Right side -->
        <div class="nav-right">
            <button class="nav-search-btn" onclick="document.getElementById('quickSearchBar').classList.toggle('open')" title="Quick search">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
            <div class="nav-divider"></div>

            <!-- Profile: shows uploaded photo or falls back to initial -->
            <a href="profile.php" class="nav-profile" title="View profile">
                <?php if (!empty($navProfilePic) && $navProfilePic !== 'default.png'): ?>
                    <img src="uploads/<?= htmlspecialchars($navProfilePic) ?>"
                         alt="<?= htmlspecialchars($userName) ?>"
                         class="nav-avatar nav-avatar-img">
                <?php else: ?>
                    <span class="nav-avatar"><?= $navUserInitial ?></span>
                <?php endif; ?>
                <span class="nav-username"><?= htmlspecialchars($userName) ?></span>
            </a>

            <a href="auth/logout.php" class="nav-logout" title="Logout">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </a>
        </div>

    </div>

    <!-- Quick search dropdown -->
    <div class="quick-search-bar" id="quickSearchBar">
        <form action="ingredients.php" method="GET" class="quick-search-form">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="ingredients" placeholder="Type ingredients to search… e.g. egg, tomato, garlic" autocomplete="off">
            <button type="submit">Search</button>
        </form>
    </div>
</nav>

<style>
/* Profile image in navbar */
.nav-avatar-img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
    display: block;
    flex-shrink: 0;
}
</style>

<script>
/* Close quick-search bar when clicking outside — safe to run multiple times */
(function() {
    document.addEventListener("click", function(e) {
        var bar = document.getElementById("quickSearchBar");
        var btn = document.querySelector(".nav-search-btn");
        if (bar && !bar.contains(e.target) && btn && !btn.contains(e.target)) {
            bar.classList.remove("open");
        }
    });
})();
</script>