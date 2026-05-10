<?php
require_once "dbUtils.php";

try {
    $hashedPassword = password_hash("password", PASSWORD_DEFAULT);
    $username = "user";
    $stmt = $conn->prepare("INSERT INTO utilizatori (username, parola) VALUES (?, ?)");
    $stmt->bind_param("ss", $username,  $hashedPassword);
    $stmt->execute();

    $admin = "admin";
    $hashedAdminPass = password_hash("password", PASSWORD_DEFAULT);
    $role = "ADMIN";
    $stmt = $conn->prepare("INSERT INTO utilizatori (username, parola, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $admin, $hashedAdminPass, $role);
    $stmt->execute();


    $manager = "manager";
    $hashedMangerPass = password_hash("password", PASSWORD_DEFAULT);
    $role = "MANAGER";
    $stmt = $conn->prepare("INSERT INTO utilizatori (username, parola, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $manager, $hashedMangerPass, $role);
    $stmt->execute();

    echo "User created successfully!";
} catch (mysqli_sql_exception $e) {
    echo "Database error: " . $e->getMessage();
} finally {
    $stmt->close();
    $conn->close();
}