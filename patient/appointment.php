<?php
// appointment.php

// DB config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medsmart";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize inputs
$name = htmlspecialchars(trim($_POST['name']));
$age = intval($_POST['age']);
$gender = $_POST['gender'];
$department = htmlspecialchars(trim($_POST['department']));
$doctor = htmlspecialchars(trim($_POST['doctor']));
$contact = htmlspecialchars(trim($_POST['contact_number']));

// Validate contact number (+977 followed by exactly 10 digits)
if (!preg_match("/^\+977[0-9]{10}$/", $contact)) {
    die("<h3 style='color:red;'>Invalid contact number. It must start with +977 and be followed by exactly 10 digits.</h3>");
}

// Validate and sanitize email
$email_raw = trim($_POST['email']);
$email = filter_var($email_raw, FILTER_SANITIZE_EMAIL);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("<h3 style='color:red;'>Invalid email format</h3>");
}

$blood_group = htmlspecialchars(trim($_POST['blood_group']));
$appointment_date = $_POST['appointment_date'];
$appointment_time = $_POST['appointment_time'];

// Validate date and time
$today = date("Y-m-d");
if ($appointment_date < $today) {
    die("<h3 style='color:red;'>Error: Cannot select a past date.</h3>");
}
if ($appointment_time < "08:00" || $appointment_time > "20:00") {
    die("<h3 style='color:red;'>Error: Appointment time must be between 08:00 and 20:00.</h3>");
}

// Save to DB
$sql = "INSERT INTO appointments 
(name, age, gender, department, doctor, contact_number, email, blood_group, appointment_date, appointment_time) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sissssssss", $name, $age, $gender, $department, $doctor, $contact, $email, $blood_group, $appointment_date, $appointment_time);
$stmt->execute();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointment Confirmation - MedSmart</title>
    <style>
      @page {
  size: A4;
  margin: 20mm;
}

body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 20mm;
  color: #000;
  background: #f2f2f2;
}

.container {
  max-width: 800px;
  margin: 0 auto;
  border: 1px solid #333;
  padding: 30px 40px;
  background: white;
  border-radius: 10px;
  box-sizing: border-box;
}

.medsmart-logo {
  display: flex;
  align-items: center;
  justify-content: center;
  gap:0px;
  margin-bottom: 20px;
}

.circle-letter {
 font-size: 28px;
  font-weight: 600;
  color: #333;
}


.logo-text {
  font-size: 28px;
  font-weight: 600;
  color: #333;
}


 h2 {
  text-align: center;
  margin-bottom: 10px;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color:rgb(0, 0, 0);
}
hr {
  border: none;
  border-top: 2px solid #333;
  margin: 20px 0;
}

.field-label {
  font-weight: bold;
  width: 180px;
  display: inline-block;
}

.field {
  margin: 10px 0;
  font-size: 16px;
}

.footer-note {
  margin-top: 40px;
  font-size: 14px;
  color: #555;
  text-align: center;
  font-style: italic;
}

button.print-btn {
  display: block;
  margin: 30px auto 0;
  padding: 12px 40px;
  font-size: 16px;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

button.print-btn:hover {
  background-color: #218838;
}

@media print {
  button.print-btn {
    display: none;
  }

  body {
    background: white;
    margin: 0;
    padding: 0;
  }

  .container {
    border: none;
    box-shadow: none;
    border-radius: 0;
  }
}

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
  <div class="field"><span class="field-label">Doctor:</span> <?= htmlspecialchars($doctor) ?></div>
  <div class="field"><span class="field-label">Blood Group:</span> <?= htmlspecialchars($blood_group) ?></div>
  <div class="field"><span class="field-label">Contact:</span> <?= htmlspecialchars($contact) ?></div>
  <div class="field"><span class="field-label">Email:</span> <?= htmlspecialchars($email) ?></div>
  <div class="field"><span class="field-label">Appointment Date:</span> <?= date("Y-m-d", strtotime($appointment_date)) ?></div>
  <div class="field"><span class="field-label">Appointment Time:</span> <?= $appointment_time ?></div>

  <div class="footer-note">
    Please arrive 15 minutes early with all necessary documents.
  </div>
</div>

<button class="print-btn" onclick="window.print()">🖨️ Print Appointment Slip</button>

</body>
</html>

