<?php
require "api/session.php";
require "api/db.php";

requireLogin();

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT name, email, profile_pic, bio FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $bio  = trim($_POST['bio']);
    $photoName = $user['profile_pic'];

    /* Handle Image Upload */
    if (!empty($_FILES['profile_pic']['name'])) {
        $targetDir = "uploads/";
        $extension = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);
        $fileName  = "user_" . $userId . "_" . time() . "." . $extension;
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFile);
        $photoName = $fileName;
    }

    $stmt = $pdo->prepare("UPDATE users SET name = ?, bio = ?, profile_pic = ? WHERE id = ?");
    $stmt->execute([$name, $bio, $photoName, $userId]);

    $_SESSION['name'] = $name;

    header("Location: profile.php");
    exit;
}

/* Avatar initial for JS default */
$nameInitial = strtoupper(mb_substr(trim($user['name']), 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile — Recipe Generator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/dashboard.css">
    <link rel="stylesheet" href="assets/profile.css">
</head>
<body class="edit-page">

<?php include "partials/navbar.php"; ?>

<div class="pe-wrapper">

    <!-- Page heading -->
    <div class="pe-page-header">
        <a href="profile.php" class="pe-back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to profile
        </a>
        <h1 class="pe-page-title">Edit Profile</h1>
        <p class="pe-page-sub">Update your personal information and photo</p>
    </div>

    <!-- Edit Card -->
    <div class="pe-card">
        <form method="POST" enctype="multipart/form-data" class="pe-form" id="editForm">

            <!-- ── PHOTO SECTION ── -->
            <div class="pe-photo-col">
                <div class="pe-photo-wrap" id="photoWrap">
                    <?php if (!empty($user['profile_pic']) && $user['profile_pic'] !== 'default.png'): ?>
                        <img id="previewImage"
                             src="uploads/<?= htmlspecialchars($user['profile_pic']) ?>"
                             alt="Profile photo"
                             class="pe-photo-img">
                    <?php else: ?>
                        <!-- Initial avatar (shown when no photo) -->
                        <div class="pe-photo-initial" id="photoInitial"><?= $nameInitial ?></div>
                        <img id="previewImage" src="" alt="" class="pe-photo-img" style="display:none;">
                    <?php endif; ?>

                    <!-- Hover overlay -->
                    <label for="profile_pic" class="pe-photo-overlay">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        <span>Change Photo</span>
                    </label>
                </div>

                <input type="file" name="profile_pic" id="profile_pic" accept="image/*" hidden>

                <p class="pe-photo-hint">JPG, PNG or GIF · Max 5MB</p>

                <label for="profile_pic" class="pe-change-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    Upload Photo
                </label>
            </div>

            <!-- ── FIELDS SECTION ── -->
            <div class="pe-fields-col">

                <div class="pe-field-group">
                    <label class="pe-label" for="name">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Full Name
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="pe-input"
                        value="<?= htmlspecialchars($user['name']) ?>"
                        placeholder="Your full name"
                        required>
                </div>

                <div class="pe-field-group">
                    <label class="pe-label pe-label-disabled">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        Email Address
                        <span class="pe-label-note">Cannot be changed</span>
                    </label>
                    <input
                        type="email"
                        class="pe-input pe-input-disabled"
                        value="<?= htmlspecialchars($user['email']) ?>"
                        disabled>
                </div>

                <div class="pe-field-group pe-field-grow">
                    <label class="pe-label" for="bio">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        About You
                    </label>
                    <textarea
                        id="bio"
                        name="bio"
                        class="pe-textarea"
                        rows="5"
                        placeholder="Tell us a little about yourself, your cooking style, favourite cuisines…"><?= htmlspecialchars($user['bio']) ?></textarea>
                    <span class="pe-char-count" id="charCount">0 / 300</span>
                </div>

                <!-- Actions -->
                <div class="pe-actions">
                    <button type="submit" class="pe-save-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><polyline points="20 6 9 17 4 12"/></svg>
                        Save Changes
                    </button>
                    <a href="profile.php" class="pe-cancel-btn">Cancel</a>
                </div>

            </div>
        </form>
    </div>

</div>

<script>
(function () {
    /* ── Image preview ─────────────────────────────────── */
    const fileInput   = document.getElementById("profile_pic");
    const previewImg  = document.getElementById("previewImage");
    const initDiv     = document.getElementById("photoInitial");

    fileInput.addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function () {
            previewImg.src = reader.result;
            previewImg.style.display = "block";
            if (initDiv) initDiv.style.display = "none";
        };
        reader.readAsDataURL(file);
    });

    /* ── Bio char counter ──────────────────────────────── */
    const bioArea   = document.getElementById("bio");
    const charCount = document.getElementById("charCount");
    const MAX = 300;

    function updateCount() {
        const len = bioArea.value.length;
        charCount.textContent = len + " / " + MAX;
        charCount.classList.toggle("pe-char-warn", len > MAX * 0.85);
        charCount.classList.toggle("pe-char-over", len > MAX);
    }

    bioArea.addEventListener("input", updateCount);
    updateCount();

    /* ── Name → initial live update ────────────────────── */
    const nameInput = document.getElementById("name");
    nameInput.addEventListener("input", function () {
        if (initDiv && previewImg.style.display === "none") {
            initDiv.textContent = (nameInput.value.trim()[0] || "?").toUpperCase();
        }
    });
})();
</script>

</body>
</html>