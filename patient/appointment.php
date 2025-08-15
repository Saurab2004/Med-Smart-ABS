<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medsmart";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Database connection failed: " . $conn->connect_error);

// Sanitize inputs
$name = htmlspecialchars(trim($_POST['name']));
$age = intval($_POST['age']);
$gender = $_POST['gender'];
$contact = htmlspecialchars(trim($_POST['contact_number']));
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$blood_group = htmlspecialchars(trim($_POST['blood_group']));
$appointment_date = $_POST['appointment_date'];
$appointment_time = $_POST['appointment_time'];
$doctor_id = intval($_POST['doctor_id']);
$doctor_name = htmlspecialchars(trim($_POST['doctor_name']));
$department = htmlspecialchars(trim($_POST['department']));

// Validate contact
if (!preg_match("/^\+977[0-9]{10}$/", $contact)) die("Invalid contact number.");

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) die("Invalid email.");

// Validate date & time
if ($appointment_date < date("Y-m-d")) die("Cannot select past date.");
if ($appointment_time < "08:00" || $appointment_time > "20:00") die("Invalid time.");

// Handle file upload
$report_path = NULL;
if (!empty($_FILES['patient_image']['name'])) {
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    $filename = time() . "_" . basename($_FILES['patient_image']['name']);
    $target_file = $upload_dir . $filename;
    if (move_uploaded_file($_FILES['patient_image']['tmp_name'], $target_file)) {
        $report_path = $target_file;
    }
}

// Insert appointment
$stmt = $conn->prepare("INSERT INTO appointments 
(patient_name, patient_age, gender, department, contact_number, email, blood_group, appointment_date, appointment_time, transaction_code, doctor_id, status, report_path)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
$txn_code = uniqid("TXN");
$stmt->bind_param("sisssssssis", $name, $age, $gender, $department, $contact, $email, $blood_group, $appointment_date, $appointment_time, $txn_code, $doctor_id, $report_path);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: appointment_confirmation.php?txn=" . $txn_code);
exit();
