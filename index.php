<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RecipeGen — Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/index.css">
</head>
<body>

<div class="page-wrap">

    <!-- ══ LEFT PANEL ══════════════════════════════════ -->
    <div class="left-panel">

        <!-- Mesh gradient layers -->
        <div class="mesh-1"></div>
        <div class="mesh-2"></div>
        <div class="mesh-3"></div>

        <!-- Noise texture overlay -->
        <div class="noise"></div>

        <!-- Content -->
        <div class="left-inner">

            <!-- Logo -->
            <a href="#" class="logo">
                <div class="logo-mark">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M18.06 22.99h1.66c.84 0 1.53-.64 1.63-1.46L23 5.05h-5V1h-1.97v4.05h-4.97l.3 2.34c1.71.47 3.31 1.32 4.27 2.26 1.44 1.42 2.43 2.89 2.43 5.29v8.05zM1 21.99V21h15.03v.99c0 .55-.45 1-1.01 1H2.01c-.56 0-1.01-.45-1.01-1zm15.03-7c0-4.5-6.29-5-7.52-5-1.23 0-7.51.5-7.51 5h15.03zM1.02 17h15.03v2H1.02z" fill="currentColor"/></svg>
                </div>
                <span>RecipeGen</span>
            </a>

            <!-- Hero text -->
            <div class="hero-text">
                <p class="hero-eyebrow">Your personal kitchen</p>
                <h1 class="hero-heading">
                    Cook with<br>
                    <em>intention,</em><br>
                    eat with joy.
                </h1>
                <p class="hero-body">
                    Discover hundreds of recipes tailored to your ingredients, preferences, and lifestyle.
                </p>
            </div>

            <!-- Floating cuisine tags -->
            <div class="cuisine-tags">
                <span class="ctag ctag--1">🌿 Healthy</span>
                <span class="ctag ctag--2">🍝 Italian</span>
                <span class="ctag ctag--3">🌶 Spicy</span>
                <span class="ctag ctag--4">⚡ Quick</span>
                <span class="ctag ctag--5">🥗 Vegan</span>
                <span class="ctag ctag--6">🍱 Asian</span>
            </div>

            <!-- Decorative rings -->
            <div class="deco-ring ring-lg"></div>
            <div class="deco-ring ring-sm"></div>

        </div>
    </div>

    <!-- ══ RIGHT PANEL ═════════════════════════════════ -->
    <div class="right-panel">

        <!-- Subtle background pattern -->
        <div class="right-bg"></div>

        <div class="right-inner">

            <!-- ── LOGIN CARD ─────────────────────────── -->
            <div class="auth-card" id="cardLogin">

                <div class="card-top">
                    <div class="card-icon">🍽️</div>
                    <h2 class="card-title">Welcome back</h2>
                    <p class="card-sub">Sign in to your culinary world</p>
                </div>

                <form method="POST" action="auth/login.php" class="auth-form">

                    <div class="field">
                        <label for="loginEmail">Email address</label>
                        <div class="input-wrap">
                            <svg class="inp-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <input type="email" id="loginEmail" name="email" placeholder="you@gmail.com" required autocomplete="email">
                        </div>
                    </div>

                    <div class="field">
                        <label for="loginPwd">
                            Password
                            <button type="button" class="fp-trigger" onclick="switchCard('cardForgot')">Forgot password?</button>
                        </label>
                        <div class="input-wrap">
                            <svg class="inp-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            <input type="password" id="loginPwd" name="password" placeholder="••••••••" required autocomplete="current-password">
                            <button type="button" class="eye-btn" onclick="toggleEye('loginPwd',this)" tabindex="-1">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="cta-btn">
                        Sign In
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>

                </form>

                <div class="divider"><span>or</span></div>

                <p class="switch-row">
                    New to RecipeGen?
                    <a href="auth/register.php" class="switch-link">Create account →</a>
                </p>

            </div>

            <!-- ── FORGOT PASSWORD CARD ────────────────── -->
            <div class="auth-card auth-card--hidden" id="cardForgot">

                <div class="card-top">
                    <div class="card-icon card-icon--reset">🔑</div>
                    <h2 class="card-title">Reset password</h2>
                    <p class="card-sub">Enter your email and we'll send a reset link</p>
                </div>

                <form method="POST" action="auth/forgot_password.php" class="auth-form">

                    <div class="field">
                        <label for="forgotEmail">Email address</label>
                        <div class="input-wrap">
                            <svg class="inp-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <input type="email" id="forgotEmail" name="email" placeholder="you@gmail.com" required autocomplete="email">
                        </div>
                    </div>

                    <button type="submit" class="cta-btn cta-btn--outline">
                        Send Reset Link
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                    </button>

                </form>

                <p class="switch-row" style="margin-top:18px">
                    <button type="button" class="back-btn" onclick="switchCard('cardLogin')">
                        ← Back to sign in
                    </button>
                </p>

            </div>

        </div>
    </div>

</div>

<script>
function switchCard(id) {
    document.querySelectorAll('.auth-card').forEach(c => c.classList.add('auth-card--hidden'));
    const target = document.getElementById(id);
    target.classList.remove('auth-card--hidden');
    /* re-trigger animation */
    target.style.animation = 'none';
    target.offsetHeight;
    target.style.animation = '';
}

function toggleEye(id, btn) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.classList.toggle('eye-btn--on');
}
</script>

</body>
</html>