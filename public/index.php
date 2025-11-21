<?php
session_start();
require __DIR__ . '/../db.php';

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'login': require __DIR__ . '/../views/auth/login.php'; break;
    case 'register': require __DIR__ . '/../views/auth/register.php'; break;
    case 'logout':
        session_destroy(); header('Location: ?page=login'); exit;
    case 'buses': require __DIR__ . '/../views/customer/buses.php'; break;
    case 'reserve_api':
    require __DIR__ . '/../actions/reserve.php'; break;
    // add more routes...
    default:
        echo "<a href='?page=login'>Login</a> | <a href='?page=register'>Register</a>";
}
