<?php
session_start();

if (!isset($_SESSION['logat']) || $_SESSION['logat'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
<h1>Bine ai venit, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
<p>Aceasta este o zonă securizată a aplicației.</p>

<a href="logout.php">Deconectare</a>
</body>
</html>