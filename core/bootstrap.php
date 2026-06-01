<?php

define('APP_ROOT', dirname(__DIR__));

// 1. Load Env FIRST — before anything else
require_once APP_ROOT . '/core/Env.php';
Env::load(APP_ROOT . '/.env');

// 2. Now load configs (they can use Env::get safely)
require_once APP_ROOT . '/config/config.php';
require_once APP_ROOT . '/config/database.php';

// 3. Load core classes
require_once APP_ROOT . '/core/Model.php';
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Router.php';
require_once APP_ROOT . '/core/Auth.php';
require_once APP_ROOT . '/core/Validator.php';
require_once APP_ROOT . '/core/FileReader.php';

// 4. Load models
require_once APP_ROOT . '/app/Models/User.php';
require_once APP_ROOT . '/app/Models/Curriculo.php';

// 5. Start session
Session::start();
