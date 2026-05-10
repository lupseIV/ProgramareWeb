<?php
function isAdmin(): bool {
    return $_SESSION['role'] == 'ADMIN';
}

function isManager(): bool {
    return $_SESSION['role'] == 'MANAGER';
}

function isEmployee(): bool {
    return $_SESSION['role'] == 'EMPLOYEE';
}

function isLogged(): bool {
    return isset($_SESSION['logat']) && $_SESSION['logat'] === true;
}