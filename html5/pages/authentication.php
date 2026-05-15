<?php
if (!defined('APP_RUNNING')) {
    header('Location: /?page=authentication');
    exit;
}

$pageTitle = "Autentificare";
$styles  = ["styles/auth.css"];

if (isset($_SESSION['logat']) && $_SESSION['logat'] === true) {
    header('Location: ?page=dashboard');
    exit;
}
$errors    = [];
$username  = '';

// Generate a fresh CAPTCHA question on every GET load
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require_once __DIR__ . '/../captcha.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username         = trim($_POST['username'] ?? '');
    $parola_introdusa = $_POST['parola'] ?? '';

    $captchaInput = trim($_POST['captcha'] ?? '');

    if (empty($username)) {
        $errors[] = 'Numele de utilizator este obligatoriu.';
    }
    if (empty($parola_introdusa)) {
        $errors[] = 'Parola este obligatorie.';
    }
    if ($captchaInput === '') {
        $errors[] = 'Răspunsul CAPTCHA este obligatoriu.';
    } elseif ($captchaInput !== ($_SESSION['captcha_answer'] ?? '')) {
        $errors[] = 'Răspuns CAPTCHA incorect.';
    }

    // Clear after check — prevents reuse on multiple submits
    unset($_SESSION['captcha_answer'], $_SESSION['captcha_question']);

    // Generate a fresh question for the re-rendered form
    require_once __DIR__ . '/../captcha.php';

    if (empty($errors)) {
        $sql = "SELECT id, username, parola, role FROM utilizatori WHERE username = '" . $username . "' AND parola = MD5('" . $parola_introdusa . "')";

        $stmt = $pdo->query($sql);
        $user = $stmt->fetch();

        if ($user) {
            session_regenerate_id(true);

            $_SESSION['logat']    = true;
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            $upd = $pdo->prepare("UPDATE utilizatori SET logged_in = 1, logged_at = NOW() WHERE id = :id");
            $upd->execute([':id' => $user['id']]);

            if (!empty($_POST['remember_me'])) {
                $token        = bin2hex(random_bytes(32));
                $tokenHash    = hash('sha256', $token);
                $expiry       = date('Y-m-d H:i:s', time() + 30 * 24 * 60 * 60);

                $pdo->prepare("UPDATE utilizatori SET remember_token = :token, remember_expiry = :expiry WHERE id = :id")
                    ->execute([':token' => $tokenHash, ':expiry' => $expiry, ':id' => $user['id']]);

                setcookie('remember_token', $token, [
                    'expires'  => time() + 30 * 24 * 60 * 60,
                    'path'     => '/',
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            }

            header('Location: ?page=dashboard');
            exit;
        } else {
            $errors[] = 'Utilizator sau parolă incorectă.';
        }
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-logo">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="var(--accent)">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 4c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
            </svg>
            <h1>ERP System</h1>
        </div>

        <h2>Autentificare</h2>
        <p class="auth-subtitle">Introduceți datele de acces pentru a continua.</p>

        <?php if (!empty($errors)): ?>
            <div class="auth-error">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="?page=authentication" class="auth-form">

            <div class="form-group">
                <label for="username">Utilizator</label>
                <div class="input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($username) ?>"
                        placeholder="Introduceți utilizatorul"
                        autocomplete="username"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="parola">Parolă</label>
                <div class="input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input
                        type="password"
                        id="parola"
                        name="parola"
                        placeholder="Introduceți parola"
                        autocomplete="current-password"
                        required
                    >
                </div>
            </div>
            <div class="form-group">
                <label for="captcha">Verificare CAPTCHA</label>
                <div class="captcha-question">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <span><?= htmlspecialchars($_SESSION['captcha_question'] ?? '') ?></span>
                </div>
                <div class="input-wrapper">
                    <svg class="input-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <input
                        type="number"
                        id="captcha"
                        name="captcha"
                        placeholder="Răspunsul dvs."
                        autocomplete="off"
                        min="0"
                        required
                    >
                </div>
            </div>

            <div class="form-group remember-group">
                <label class="remember-label">
                    <input type="checkbox" name="remember_me" value="1">
                    Ține-mă minte 30 de zile
                </label>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-auth">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    Autentificare
                </button>
                <button type="reset" class="btn-auth">

                    <a class=" back-btn" href="?page=home">Înapoi</a>
                </button>
            </div>

        </form>
    </div>
</div>
