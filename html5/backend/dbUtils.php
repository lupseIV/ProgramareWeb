<?php
$host = "localhost";
$dbName = "programare_web";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
