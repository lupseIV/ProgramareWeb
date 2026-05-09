<?php
if (!defined('APP_RUNNING')) {
    header('Location: /?page=home');
    exit;
}

$_SESSION = [];
session_destroy();

header('Location: ?page=home');
exit;
