<?php
session_start();

// Golește toate variabilele de sesiune
$_SESSION = array();

// Distruge complet sesiunea
session_destroy();

// Redirecționează către pagina de login
header("Location: login.php");
exit;
