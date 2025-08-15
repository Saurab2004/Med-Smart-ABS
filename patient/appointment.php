<?php
// appointment.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medsmart";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("<h3 style='color:red;'>Database connection failed: " . $conn->connect_error . "</h3>");

// Sanitize inputs
$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
$age = isset($_POST['age']) ? intval($_POST['age']) : 0;
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
$contact = isset($_POST['contact_number']) ? htmlspecialchars(trim($_POST['contact_number'])) : '';
$email_raw = isset($_POST['email']) ? trim($_POST['email']) : '';
$email = filter_var($email_raw, FILTER_SANITIZE_EMAIL);
$blood_group = isset($_POST['blood_group']) ? htmlspecialchars(trim($_POST['blood_group'])) : '';
$appointment_date = isset($_POST['appointment_date']) ? $_POST['appointment_date'] : '';
$appointment_time = isset($_POST['appointment_time']) ? $_POST['appointment_time'] : '';

// Doctor info
$doctor_id = isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : 0;
$doctor_name = isset($_POST['doctor_name']) ? htmlspecialchars(trim($_POST['doctor_name'])) : 'Not Assigned';
$department = isset($_POST['department']) ? htmlspecialchars(trim($_POST['department'])) : 'Not Assigned';

// Validate contact number
if (!preg_match("/^\+977[0-9]{10}$/", $contact)) die("<h3 style='color:red;'>Invalid contact number. Must start with +977 and 10 digits.</h3>");

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) die("<h3 style='color:red;'>Invalid email format</h3>");

// Validate date & time
$today = date("Y-m-d");
if ($appointment_date < $today) die("<h3 style='color:red;'>Cannot select a past date.</h3>");
if ($appointment_time < "08:00" || $appointment_time > "20:00") die("<h3 style='color:red;'>Time must be 08:00-20:00</h3>");

// Transaction code & status
$transaction_code = uniqid("TXN");
$status = 'pending';

// Insert appointment
$sql = "INSERT INTO appointments 
(patient_name, patient_age, gender, department, contact_number, email, blood_group, appointment_date, appointment_time, transaction_code, doctor_id, status)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) die("<h3 style='color:red;'>Prepare failed: " . $conn->error . "</h3>");

$stmt->bind_param(
    "sissssssssis",
    $name, $age, $gender, $department, $contact, $email, $blood_group, $appointment_date, $appointment_time, $transaction_code, $doctor_id, $status
);

if (!$stmt->execute()) die("<h3 style='color:red;'>Database error: " . $stmt->error . "</h3>");
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<title>Appointment Confirmation - MedSmart</title>
<style>
@page { size: A4; margin: 20mm; }
body { font-family: Arial, sans-serif; margin: 0; padding: 20mm; color: #000; background: #f2f2f2; }
.container { max-width: 800px; margin: 0 auto; border: 1px solid #333; padding: 30px 40px; background: white; border-radius: 10px; box-sizing: border-box; }
.medsmart-logo { display: flex; align-items: center; justify-content: center; gap:0px; margin-bottom: 20px; }
.circle-letter { font-size: 28px; font-weight: 600; color: #333; }
.logo-text { font-size: 28px; font-weight: 600; color: #333; }
h2 { text-align: center; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1.5px; color:rgb(0,0,0); }
hr { border: none; border-top: 2px solid #333; margin: 20px 0; }
.field-label { font-weight: bold; width: 180px; display: inline-block; }
.field { margin: 10px 0; font-size: 16px; }
.footer-note { margin-top: 40px; font-size: 14px; color: #555; text-align: center; font-style: italic; }
button.print-btn { display: block; margin: 30px auto 0; padding: 12px 40px; font-size: 16px; background-color: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; }
button.print-btn:hover { background-color: #218838; }
@media print { button.print-btn { display: none; } body { background: white; margin:0; padding:0; } .container { border:none; box-shadow:none; border-radius:0; } }
</style>
</head>
<body>
<div class="container" id="confirmation">
<div class="medsmart-logo">
  <div class="circle-letter">Med-</div>
  <div class="logo-text">Smart</div>
</div>

<h2>Appointment Confirmation</h2>
<hr>

<div class="field"><span class="field-label">Patient Name:</span> <?= htmlspecialchars($name) ?></div>
<div class="field"><span class="field-label">Age:</span> <?= $age ?></div>
<div class="field"><span class="field-label">Gender:</span> <?= htmlspecialchars($gender) ?></div>
<div class="field"><span class="field-label">Department:</span> <?= htmlspecialchars($department) ?></div>
<div class="field"><span class="field-label">Doctor:</span> <?= htmlspecialchars($doctor_name) ?></div>
<div class="field"><span class="field-label">Blood Group:</span> <?= htmlspecialchars($blood_group) ?></div>
<div class="field"><span class="field-label">Contact:</span> <?= htmlspecialchars($contact) ?></div>
<div class="field"><span class="field-label">Email:</span> <?= htmlspecialchars($email) ?></div>
<div class="field"><span class="field-label">Appointment Date:</span> <?= date("Y-m-d", strtotime($appointment_date)) ?></div>
<div class="field"><span class="field-label">Appointment Time:</span> <?= $appointment_time ?></div>

<div class="footer-note">Please arrive 15 minutes early with all necessary documents.</div>
</div>

<button class="print-btn" onclick="window.print()">🖨️ Print Appointment Slip</button>
</body>
</html>
