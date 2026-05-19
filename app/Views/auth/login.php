<div class="auth-container">
    <div class="auth-card">
        <div class="auth-logo">
            <span class="logo-icon">📄</span>
            <h1><?= APP_NAME ?></h1>
            <p>Padronização inteligente de currículos</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= APP_URL ?>/login" class="auth-form">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="seu@email.com"
                    required
                    autocomplete="email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Entrar no sistema
            </button>
        </form>

        <p class="auth-footer">RH Padronizador v<?= APP_VERSION ?></p>
    </div>
</div>
