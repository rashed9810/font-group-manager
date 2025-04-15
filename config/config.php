<?php
define('BASE_PATH', dirname(__DIR__));
define('DB_TYPE', 'sqlite');
define('DB_PATH', BASE_PATH . '/db/font_group_system.sqlite');
define('UPLOAD_DIR', BASE_PATH . '/uploads/fonts');
define('FONT_URL', 'uploads/fonts');
if (!file_exists(UPLOAD_DIR)) { mkdir(UPLOAD_DIR, 0777, true);
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
