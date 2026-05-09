<?php
session_start();

if (isset($_SESSION['logat']) && $_SESSION['logat'] === true) {
    header("Location: dashboard.php");
    exit;
}

require_once 'dbUtils.php';

$mesaj_eroare = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $parola_introdusa = $_POST['parola'];

    if (!empty($username) && !empty($parola_introdusa)) {
        $stmt = $conn->prepare("SELECT id, parola FROM utilizatori WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($parola_introdusa, $user['parola'])) {
                $_SESSION['logat'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;

                header("Location: dashboard.php");
                exit;
            } else {
                $mesaj_eroare = "Parolă incorectă!";
            }
        } else {
            $mesaj_eroare = "Utilizatorul nu există!";
        }
        $stmt->close();
    } else {
        $mesaj_eroare = "Te rugăm să completezi ambele câmpuri.";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Autentificare</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; margin-top: 100px; }
        .login-box { border: 1px solid #ccc; padding: 20px; border-radius: 5px; width: 300px; }
        .eroare { color: red; margin-bottom: 10px; }
        input[type="text"], input[type="password"] { width: 100%; margin-bottom: 15px; padding: 8px; box-sizing: border-box;}
        button { width: 100%; padding: 10px; background: #007BFF; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login Aplicație</h2>

    <?php if ($mesaj_eroare != ""): ?>
        <div class="eroare"><?php echo htmlspecialchars($mesaj_eroare); ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="username">Utilizator:</label>
        <input type="text" id="username" name="username" required>

        <label for="parola">Parolă:</label>
        <input type="password" id="parola" name="parola" required>

        <button type="submit">Autentificare</button>
    </form>
</div>

</body>
</html>