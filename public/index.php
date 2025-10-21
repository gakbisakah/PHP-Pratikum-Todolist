<?php
session_start();
// aktfkan error selama dev
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . '/../controllers/TodoController.php');

$page = $_GET['page'] ?? 'index';
$controller = new TodoController();

// routing
switch ($page) {
    case 'index':
        $controller->index();
        break;
    case 'create':
        $controller->create();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'detail':
        $controller->detail();
        break;
    case 'sort':
        // dipanggil AJAX POST JSON
        $controller->sort();
        break;
    default:
        $controller->index();
        break;
}
