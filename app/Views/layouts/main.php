<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> — <?= APP_NAME ?></title>
=======
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> - <?= APP_NAME ?></title>
>>>>>>> ae6629a7d06f29157c44e1f0f14b2f15057295af
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.css">
</head>
<body>

<div class="layout">

    <aside class="sidebar">
        <div class="workspace-card">
            <div class="workspace-icon">
                <i class="fa-solid fa-laptop"></i>
            </div>
            <div>
                <strong>Recruiter Pro</strong>
                <span>Enterprise Plan</span>
            </div>
        </div>

        <nav class="sidebar-nav" aria-label="Principal">
            <ul class="sidebar-menu">
                <li>
                    <a href="<?= APP_URL ?>/dashboard" class="<?= str_contains($_SERVER['REQUEST_URI'], 'dashboard') || $_SERVER['REQUEST_URI'] === parse_url(APP_URL, PHP_URL_PATH) . '/' ? 'active' : '' ?>">
                        <i class="fa-regular fa-file-lines"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= APP_URL ?>/curriculos" class="<?= str_contains($_SERVER['REQUEST_URI'], 'curriculos') && !str_contains($_SERVER['REQUEST_URI'], 'curriculos/novo') ? 'active' : '' ?>">
                        <i class="fa-regular fa-folder"></i>
                        <span>Curriculos</span>
                    </a>
                </li>
                <li>
                    <a href="<?= APP_URL ?>/curriculos/novo" class="<?= str_contains($_SERVER['REQUEST_URI'], 'curriculos/novo') ? 'active' : '' ?>">
                        <i class="fa-solid fa-plus"></i>
                        <span>Novo Curriculo</span>
                    </a>
                </li>
            </ul>

            <?php if (Auth::isAdmin()): ?>
                <div class="sidebar-label">Administracao</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="<?= APP_URL ?>/admin/usuarios" class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin') ? 'active' : '' ?>">
                            <i class="fa-solid fa-users"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <a href="#">
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>
            <a href="#">
                <i class="fa-regular fa-circle-question"></i>
                <span>Support</span>
            </a>
        </div>
    </aside>

    <div class="content-shell">
        <header class="topbar">
            
            <div class="logo-icon">
                <i class="fa-solid fa-file-zipper"></i>
            </div>
            <a href="<?= APP_URL ?>/dashboard" class="topbar-brand">Resume Flow</a>

            <form class="topbar-search" action="<?= APP_URL ?>/curriculos" method="GET">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" name="q" placeholder="Search resumes..." aria-label="Search resumes">
            </form>

            <div class="topbar-actions">
                <button type="button" class="icon-button" aria-label="Notifications" style="margin-left: 20px">
                    <i class="fa-regular fa-bell"></i>
                </button>
                <button type="button" class="icon-button" aria-label="Help" style="margin: 0 15px 0 10px">
                    <i class="fa-regular fa-circle-question"></i>
                </button>
                <a href="<?= APP_URL ?>/logout" class="profile-button" aria-label="Logout">
                    <span><?= htmlspecialchars(substr(Auth::user()['name'] ?? 'U', 0, 1)) ?></span>
                </a>
            </div>
        </header>

        <main class="main-content">
            <?= $content ?>
        </main>
    </div>

</div>

<script src="<?= APP_URL ?>/js/app.js"></script>
</body>
</html>
