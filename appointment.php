<?php
// appointment.php

// DB connection config (adjust if needed)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medsmart";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get and sanitize inputs
$name = htmlspecialchars(trim($_POST['name']));
$age = intval($_POST['age']);
$gender = $_POST['gender'];
$department = htmlspecialchars(trim($_POST['department']));
$contact = htmlspecialchars(trim($_POST['contact_number']));
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$blood_group = htmlspecialchars(trim($_POST['blood_group']));
$appointment_date = $_POST['appointment_date'];
$appointment_time = $_POST['appointment_time'];

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format");
}

// Prepare statement
$sql = "INSERT INTO appointments (name, age, gender, department, contact_number, email, blood_group, appointment_date, appointment_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sisssssss", $name, $age, $gender, $department, $contact, $email, $blood_group, $appointment_date, $appointment_time);

// Execute
if ($stmt->execute()) {
    echo "<h3>Appointment booked successfully!</h3>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
