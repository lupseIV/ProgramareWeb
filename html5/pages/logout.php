<?php
if (!defined('APP_RUNNING')) {
    header('Location: /?page=home');
    exit;
}

$userId = $_SESSION['user_id'] ?? null;

$_SESSION = [];
session_destroy();

if ($userId) {
    $pdo->prepare("UPDATE utilizatori SET logged_in = 0, remember_token = NULL, remember_expiry = NULL WHERE id = :id")
        ->execute([':id' => $userId]);
}

if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', ['expires' => time() - 1, 'path' => '/']);
}

header('Location: ?page=home');
exit;
