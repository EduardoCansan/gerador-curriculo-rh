<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-brand">
        <span class="brand-icon">📄</span>
        <?= APP_NAME ?>
    </div>
    <div class="navbar-user">
        <span class="user-badge <?= Auth::isAdmin() ? 'badge-admin' : 'badge-recruiter' ?>">
            <?= Auth::isAdmin() ? '👑 Admin' : '🔎 Recrutador' ?>
        </span>
        <span class="user-name"><?= htmlspecialchars(Auth::user()['name'] ?? '') ?></span>
        <a href="<?= APP_URL ?>/logout" class="btn-logout">Sair</a>
    </div>
</nav>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li>
                <a href="<?= APP_URL ?>/dashboard" class="<?= str_contains($_SERVER['REQUEST_URI'], 'dashboard') || $_SERVER['REQUEST_URI'] === parse_url(APP_URL, PHP_URL_PATH) . '/' ? 'active' : '' ?>">
                    📊 Dashboard
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/curriculos" class="<?= str_contains($_SERVER['REQUEST_URI'], 'curriculos') ? 'active' : '' ?>">
                    📁 Currículos
                </a>
            </li>
            <li>
                <a href="<?= APP_URL ?>/curriculos/novo">
                    ➕ Novo Currículo
                </a>
            </li>
            <?php if (Auth::isAdmin()): ?>
            <li class="sidebar-divider"></li>
            <li class="sidebar-label">Administração</li>
            <li>
                <a href="<?= APP_URL ?>/admin/usuarios" class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin') ? 'active' : '' ?>">
                    👥 Usuários
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="main-content">
        <?= $content ?>
    </main>

</div>

<script src="<?= APP_URL ?>/js/app.js"></script>
</body>
</html>
