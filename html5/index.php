<?php
session_start();
require 'config/database.php';
define('APP_RUNNING', true);

$page = $_GET['page'] ?? 'home';

$allowed = [
    'home', 'dashboard', 'contact',
    'authentication', 'logout',
    'adauga_angajat', 'adauga_comanda', 'adauga_contract',
];

$public = ['authentication','home','contact'];

if (!in_array($page, $allowed)) {
    $page = '404';
}

if (!in_array($page, $public) && (!isset($_SESSION['logat']) || $_SESSION['logat'] !== true)) {
    header("Location: index.php?page=authentication");
    exit;
}

ob_start();
include 'pages/' . $page . '.php';
$content = ob_get_clean();

include 'includes/header.php';
include 'includes/navbar.php';
echo $content;
include 'includes/footer.php';



