<?php

if (!defined('APP_RUNNING')) session_start();
require 'config/database.php';
require 'includes/functions.php';
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

// Remember me — auto-login if no session but valid cookie exists
if (!isset($_SESSION['logat']) && isset($_COOKIE['remember_token'])) {
    $cookieToken = $_COOKIE['remember_token'];
    $tokenHash   = hash('sha256', $cookieToken);

    $stmt = $pdo->prepare("SELECT id, username, role FROM utilizatori
                           WHERE remember_token = :token
                           AND remember_expiry > NOW()
                           LIMIT 1");
    $stmt->execute([':token' => $tokenHash]);
    $user = $stmt->fetch();

    if ($user) {
        // Valid token — restore session
        session_regenerate_id(true);
        $_SESSION['logat']    = true;
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        // Roll the token — generate a new one on each auto-login (prevents token reuse)
        $newToken     = bin2hex(random_bytes(32));
        $newTokenHash = hash('sha256', $newToken);
        $newExpiry    = date('Y-m-d H:i:s', time() + 30 * 24 * 60 * 60);

        $pdo->prepare("UPDATE utilizatori SET remember_token = :token, remember_expiry = :expiry WHERE id = :id")
            ->execute([':token' => $newTokenHash, ':expiry' => $newExpiry, ':id' => $user['id']]);

        setcookie('remember_token', $newToken, [
            'expires'  => time() + 30 * 24 * 60 * 60,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        $pdo->prepare("UPDATE utilizatori SET logged_in = 1, logged_at = NOW() WHERE id = :id")
            ->execute([':id' => $user['id']]);
    } else {
        // Invalid or expired token — delete the cookie
        setcookie('remember_token', '', ['expires' => time() - 1, 'path' => '/']);
    }
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



