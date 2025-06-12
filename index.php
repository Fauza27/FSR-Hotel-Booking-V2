<?php

// Load konfigurasi
require_once 'config/config.php';

// Load database
require_once 'config/database.php';

// Load semua model
foreach (glob(MODEL_PATH . '*.php') as $model) {
    require_once $model;
}

// Load semua controller
foreach (glob(CONTROLLER_PATH . '*.php') as $controller) {
    require_once $controller;
}

// Load router
require_once 'routes.php';

// Inisialisasi database
$database = new Database();

// Parse URL
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Route ke controller yang sesuai
$router = new Router();
$router->route($url);