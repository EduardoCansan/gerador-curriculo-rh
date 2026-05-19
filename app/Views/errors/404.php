<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>404 — Página não encontrada</title>
    <style>
        body { font-family: sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #f3f4f6; margin: 0; }
        .box { text-align: center; background: white; padding: 48px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        h1 { font-size: 4rem; color: #1a3a5c; margin: 0; }
        p { color: #6b7280; margin: 12px 0 24px; }
        a { background: #1a3a5c; color: white; text-decoration: none; padding: 10px 24px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>404</h1>
        <p>Página não encontrada.</p>
        <a href="<?= defined('APP_URL') ? APP_URL : '/' ?>/dashboard">← Voltar ao início</a>
    </div>
</body>
</html>
