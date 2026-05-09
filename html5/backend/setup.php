<?php
require_once "dbUtils.php";

try {
    $hashedPassword = password_hash("password", PASSWORD_DEFAULT);
    $username = "user";

    $stmt = $conn->prepare("INSERT INTO utilizatoir (username, parola) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    $stmt->execute();

    echo "User created successfully!";
} catch (mysqli_sql_exception $e) {
    echo "Database error: " . $e->getMessage();
} finally {
    $stmt->close();
    $conn->close();
}