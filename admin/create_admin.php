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

// Default credentials
$admin_username = "admin";
$admin_password_plain = "admin@123"; // Change this later in production

$password_hash = password_hash($admin_password_plain, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
$stmt->bind_param("ss", $admin_username, $password_hash);
if ($stmt->execute()) {
    echo " Admin user created successfully.<br>";
    echo "Username: <b>admin</b><br>";
    echo "Password: <b>admin@123</b><br>";
    echo " For security, please delete or rename this file after running it.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
