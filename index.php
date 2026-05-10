<?php
session_name('kpi_session');
session_start();

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Route map
$routes = [
    'login'     => ['controller' => 'AuthController',      'actions' => ['index','login','logout']],
    'dashboard' => ['controller' => 'DashboardController', 'actions' => ['index']],
    'spd'       => ['controller' => 'SpdController',       'actions' => ['index','detail','save','delete']],
    'nkl'       => ['controller' => 'NklController',       'actions' => ['index','save']],
    'nbr'       => ['controller' => 'NbrController',       'actions' => ['index','save','delete']],
    'std'       => ['controller' => 'StdController',       'actions' => ['index','save','deleteHarian']],
    'turnover'  => ['controller' => 'TurnoverController',  'actions' => ['index','save']],
    'summary'   => ['controller' => 'SummaryController',   'actions' => ['index','whatsapp']],
    'admin'     => ['controller' => 'AdminController',     'actions' => ['index','toko','users','saveToko','saveUser','deleteToko','deleteUser']],
    'otp'       => ['controller' => 'OtpController',       'actions' => ['index','verify','manage','update']],
];

if (!isset($routes[$page])) {
    $page = 'dashboard';
}

$controllerName = $routes[$page]['controller'];
$allowedActions = $routes[$page]['actions'];

if (!in_array($action, $allowedActions)) {
    $action = 'index';
}

require_once __DIR__ . '/controllers/' . $controllerName . '.php';

$controller = new $controllerName();
$controller->$action();
