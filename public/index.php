<?php

// Carrega o bootstrap (configs, classes core, sessão)
require_once dirname(__DIR__) . '/core/bootstrap.php';

// Instancia o router
$router = new Router();

// Carrega as rotas
require_once APP_ROOT . '/routes/web.php';

// Despacha a requisição
$router->dispatch();
