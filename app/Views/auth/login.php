<div class="auth-logo">
    <span class="logo-icon">📄</span>
    <h1><?= APP_NAME ?></h1>
    <p>Padronização inteligente de currículos</p>
</div>
<div class="auth-container">
<<<<<<< HEAD
    <div class="auth-card"> 
=======
    <div class="auth-logo">
        <div class="logo-icon">
            <i class="fa-solid fa-file-zipper"></i>
        </div>
        <h1>Resume Flow</h1>
        <p>Optimize your talent pipeline</p>
    </div>
    <div class="auth-card">
>>>>>>> ae6629a7d06f29157c44e1f0f14b2f15057295af

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= APP_URL ?>/login" class="auth-form">
            <div class="form-group">
                <label for="email" class="email">Email Address</label>
                <div class="input-icon">
                    <i class="fa-solid fa-envelope left-icon"></i>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="name@company.com"
                        required
                        autocomplete="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    >
                </div>
            </div>
            <div class="label-row">
                <label for="password" class="password">Password</label>
                <a href="#" class="forgot-link">Forgot?</a>
            </div>
            <div class="input-icon">
                <i class="fa-solid fa-lock left-icon"></i>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
                <button type="button" class="toggle-password" onclick="togglePassword()">
                    <i class="fa-regular fa-eye" id="eye-icon"></i>
                </button>
            </div>
            <button type="submit" class="sign-in-button">
                Sign In <i class="fa-solid fa-arrow-right"></i>
            </button>

        </form>
    </div>
</div>