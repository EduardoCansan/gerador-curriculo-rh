<?php

define('APP_ROOT', dirname(__DIR__));

// Carrega configurações
require_once APP_ROOT . '/config/config.php';
require_once APP_ROOT . '/config/database.php';

// Carrega classes core
require_once APP_ROOT . '/core/Model.php';
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Router.php';
require_once APP_ROOT . '/core/Auth.php';
require_once APP_ROOT . '/core/Validator.php';

// Carrega Models sempre disponíveis
require_once APP_ROOT . '/app/Models/User.php';
require_once APP_ROOT . '/app/Models/Curriculo.php';

// Inicia sessão
Session::start();
