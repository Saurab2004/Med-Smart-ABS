<?php
// Usage: Run once to create admin user then delete this file

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medsmart";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$admin_username = "medsmart";
$admin_password_plain = "medsmart@123"; // Change to a secure password

$password_hash = password_hash($admin_password_plain, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
$stmt->bind_param("ss", $admin_username, $password_hash);
if ($stmt->execute()) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
