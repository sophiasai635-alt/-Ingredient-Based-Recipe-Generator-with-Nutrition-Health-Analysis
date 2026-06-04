<?php
/* ══════════════════════════════════════════════════════
   auth/register.php
   Location: auth/register.php
   All relative paths go one level up (../)
══════════════════════════════════════════════════════ */
require_once "../api/session.php";
require_once "../api/db.php";

/* Redirect if already logged in */
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit;
}

$errors   = [];
$formData = ['name' => '', 'email' => ''];

/* ══════════════════════════════════════════════════════
   HANDLE POST
══════════════════════════════════════════════════════ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    /* Preserve values on error */
    $formData = ['name' => $name, 'email' => $email];

    /* ── Validation ─────────────────────────────────── */
    if ($name === '') {
        $errors['name'] = 'Full name is required.';
    } elseif (strlen($name) < 2) {
        $errors['name'] = 'Name must be at least 2 characters.';
    } elseif (strlen($name) > 80) {
        $errors['name'] = 'Name is too long (max 80 characters).';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $errors['name'] = 'Name can only contain letters and spaces.';
    }

    if ($email === '') {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } elseif (!preg_match('/@gmail\.com$/i', $email)) {
        $errors['email'] = 'Only @gmail.com emails are allowed.';
    }

    if ($password === '') {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'Password must contain both letters and numbers.';
    }

    if ($confirm === '') {
        $errors['confirm'] = 'Please confirm your password.';
    } elseif ($password !== $confirm) {
        $errors['confirm'] = 'Passwords do not match.';
    }

    /* ── Check duplicate email ───────────────────────── */
    if (empty($errors)) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors['email'] = 'An account with this email already exists.';
        }
    }

    /* ── Insert user ─────────────────────────────────── */
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$name, $email, $hash]);
        $newId = $pdo->lastInsertId();

        /* Start session immediately */
        $_SESSION['user_id'] = $newId;
        $_SESSION['name']    = $name;
        $_SESSION['email']   = $email;

        header("Location: ../dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RecipeGen — Create Account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/register.css">
</head>
<body>

<div class="page-wrap">

    <div class="left-panel">
        <div class="mesh-1"></div>
        <div class="mesh-2"></div>
        <div class="mesh-3"></div>
        <div class="noise"></div>

        <div class="left-inner">

            <a href="../index.php" class="logo">
                <div class="logo-mark">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M18.06 22.99h1.66c.84 0 1.53-.64 1.63-1.46L23 5.05h-5V1h-1.97v4.05h-4.97l.3 2.34c1.71.47 3.31 1.32 4.27 2.26 1.44 1.42 2.43 2.89 2.43 5.29v8.05zM1 21.99V21h15.03v.99c0 .55-.45 1-1.01 1H2.01c-.56 0-1.01-.45-1.01-1zm15.03-7c0-4.5-6.29-5-7.52-5-1.23 0-7.51.5-7.51 5h15.03zM1.02 17h15.03v2H1.02z" fill="currentColor"/>
                    </svg>
                </div>
                <span>RecipeGen</span>
            </a>

            <div class="hero-text">
                <p class="hero-eyebrow">Join thousands of home cooks</p>
                <h1 class="hero-heading">
                    Start your<br>
                    <em>culinary</em><br>
                    journey today.
                </h1>
                <p class="hero-body">
                    Save recipes, track nutrition, discover dishes from around the world — all in one place.
                </p>
            </div>

            <ul class="feature-list">
                <li class="feat-item feat--1">
                    <div class="feat-check">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <span>200+ curated recipes across cuisines</span>
                </li>
                <li class="feat-item feat--2">
                    <div class="feat-check">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <span>Nutritional analysis for every dish</span>
                </li>
                <li class="feat-item feat--3">
                    <div class="feat-check">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <span>Find recipes by ingredients you have</span>
                </li>
                <li class="feat-item feat--4">
                    <div class="feat-check">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <span>Save and organise your favourites</span>
                </li>
            </ul>

            <div class="deco-ring ring-lg"></div>
            <div class="deco-ring ring-sm"></div>
        </div>
    </div>

    <div class="right-panel">
        <div class="right-bg"></div>

        <div class="right-inner">
            <div class="auth-card">

                <div class="card-top">
                    <div class="card-icon">✨</div>
                    <h2 class="card-title">Create your account</h2>
                    <p class="card-sub">Free forever &nbsp;·&nbsp; No credit card needed</p>
                </div>

                <form method="POST" action="register.php" class="auth-form" id="regForm" novalidate>

                    <div class="field <?= isset($errors['name']) ? 'field--error' : '' ?>">
                        <label for="regName">Full name</label>
                        <div class="input-wrap">
                            <svg class="inp-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input
                                type="text"
                                id="regName"
                                name="name"
                                placeholder="Your full name"
                                value="<?= htmlspecialchars($formData['name']) ?>"
                                required
                                autocomplete="name">
                        </div>
                        <?php if (isset($errors['name'])): ?>
                            <span class="field-error"><?= htmlspecialchars($errors['name']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="field <?= isset($errors['email']) ? 'field--error' : '' ?>">
                        <label for="regEmail">Email address</label>
                        <div class="input-wrap">
                            <svg class="inp-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <input
                                type="email"
                                id="regEmail"
                                name="email"
                                placeholder="you@gmail.com"
                                value="<?= htmlspecialchars($formData['email']) ?>"
                                required
                                autocomplete="email">
                        </div>
                        <?php if (isset($errors['email'])): ?>
                            <span class="field-error"><?= htmlspecialchars($errors['email']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="field <?= isset($errors['password']) ? 'field--error' : '' ?>">
                        <label for="regPwd">Password</label>
                        <div class="input-wrap">
                            <svg class="inp-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            <input
                                type="password"
                                id="regPwd"
                                name="password"
                                placeholder="Min 8 chars, letters + numbers"
                                required
                                autocomplete="new-password">
                            <button type="button" class="eye-btn" onclick="toggleEye('regPwd',this)" tabindex="-1">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <span class="field-error"><?= htmlspecialchars($errors['password']) ?></span>
                        <?php endif; ?>
                        <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                        <span class="strength-label" id="strengthLabel"></span>
                    </div>

                    <div class="field <?= isset($errors['confirm']) ? 'field--error' : '' ?>">
                        <label for="regConfirm">Confirm password</label>
                        <div class="input-wrap">
                            <svg class="inp-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            <input
                                type="password"
                                id="regConfirm"
                                name="confirm"
                                placeholder="Re-enter your password"
                                required
                                autocomplete="new-password">
                            <button type="button" class="eye-btn" onclick="toggleEye('regConfirm',this)" tabindex="-1">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <?php if (isset($errors['confirm'])): ?>
                            <span class="field-error"><?= htmlspecialchars($errors['confirm']) ?></span>
                        <?php endif; ?>
                        <span class="match-label" id="matchLabel"></span>
                    </div>

                    <button type="submit" class="cta-btn">
                        Create Account
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>

                </form>

                <p class="switch-row">
                    Already have an account?
                    <a href="../index.php" class="switch-link">Sign in →</a>
                </p>

            </div>
        </div>
    </div>

</div>

<script>
/* ── Eye toggle ─────────────────────────────────────── */
function toggleEye(id, btn) {
    const inp = document.getElementById(id);
    inp.type  = inp.type === 'password' ? 'text' : 'password';
    btn.classList.toggle('eye-btn--on');
}

/* ── Password strength ──────────────────────────────── */
const pwdInput      = document.getElementById('regPwd');
const strengthFill  = document.getElementById('strengthFill');
const strengthLabel = document.getElementById('strengthLabel');

pwdInput.addEventListener('input', function () {
    const val = this.value;
    let score = 0;
    if (val.length >= 8)               score++;
    if (val.length >= 12)              score++;
    if (/[A-Z]/.test(val))             score++;
    if (/[0-9]/.test(val))             score++;
    if (/[^A-Za-z0-9]/.test(val))     score++;

    const levels = [
        { pct: '0%',    color: 'transparent', label: '' },
        { pct: '25%',   color: '#E11D48',     label: 'Weak' },
        { pct: '50%',   color: '#D97706',     label: 'Fair' },
        { pct: '75%',   color: '#2A9D8F',     label: 'Good' },
        { pct: '90%',   color: '#1B7F75',     label: 'Strong' },
        { pct: '100%',  color: '#16A34A',     label: 'Very strong ✓' },
    ];
    const lvl = levels[Math.min(score, 5)];

    strengthFill.style.width      = val.length ? lvl.pct   : '0%';
    strengthFill.style.background = val.length ? lvl.color : 'transparent';
    strengthLabel.textContent     = val.length ? lvl.label : '';
    strengthLabel.style.color     = lvl.color;
});

/* ── Confirm match ──────────────────────────────────── */
const confirmInput = document.getElementById('regConfirm');
const matchLabel   = document.getElementById('matchLabel');

function checkMatch() {
    if (!confirmInput.value) { matchLabel.textContent = ''; return; }
    const ok = confirmInput.value === pwdInput.value;
    matchLabel.textContent = ok ? '✓ Passwords match' : '✗ Passwords do not match';
    matchLabel.style.color = ok ? '#16A34A' : '#E11D48';
}
confirmInput.addEventListener('input', checkMatch);
pwdInput.addEventListener('input', checkMatch);

/* ── Client-side submit guard ───────────────────────── */
document.getElementById('regForm').addEventListener('submit', function (e) {
    /* Remove previous client errors */
    document.querySelectorAll('.field-error.js-err').forEach(el => el.remove());
    document.querySelectorAll('.field--error').forEach(el => el.classList.remove('field--error'));

    let valid = true;

    function addErr(inputId, msg) {
        const field = document.getElementById(inputId).closest('.field');
        field.classList.add('field--error');
        const span = document.createElement('span');
        span.className   = 'field-error js-err';
        span.textContent = msg;
        field.appendChild(span);
        valid = false;
    }

    const name  = document.getElementById('regName').value.trim();
    const email = document.getElementById('regEmail').value.trim();
    const pwd   = pwdInput.value;
    const conf  = confirmInput.value;

    if (!name) {          
        addErr('regName',    'Full name is required.');
    } else if (!/^[a-zA-Z\s]+$/.test(name)) {
        addErr('regName',    'Name can only contain letters and spaces.');
    }
    
    if (!email) {
        addErr('regEmail',   'Email is required.');
    } else if (!email.toLowerCase().endsWith('@gmail.com')) {
        addErr('regEmail',   'Only @gmail.com emails are allowed.');
    }

    if (pwd.length < 8) addErr('regPwd',     'Minimum 8 characters required.');
    if (pwd !== conf)   addErr('regConfirm', 'Passwords do not match.');

    if (!valid) e.preventDefault();
});
</script>

</body>
</html>