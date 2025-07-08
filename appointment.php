<?php
// Simple required fields check
$fields = ['name', 'age', 'gender', 'department', 'contact_number', 'email', 'blood_group', 'appointment_date', 'appointment_time', 'transaction_code'];
foreach ($fields as $f) {
    if (empty($_POST[$f])) {
        die("Error: Missing $f. Please go back and fill all fields.");
    }
}

// Sanitize inputs to prevent XSS
function clean($str) {
    return htmlspecialchars(trim($str));
}

$name = clean($_POST['name']);
$age = clean($_POST['age']);
$gender = clean($_POST['gender']);
$department = clean($_POST['department']);
$contact_number = clean($_POST['contact_number']);
$email = clean($_POST['email']);
$blood_group = clean($_POST['blood_group']);
$appointment_date = clean($_POST['appointment_date']);
$appointment_time = clean($_POST['appointment_time']);
$transaction_code = clean($_POST['transaction_code']);

// Format date and time
$formatted_date = date("F j, Y", strtotime($appointment_date));
$formatted_time = date("h:i A", strtotime($appointment_time));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Appointment Confirmation - Med Smart</title>
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
  }
  .container {
    max-width: 800px;
    margin: 0 auto;
    border: 1px solid #333;
    padding: 30px 40px;
    box-sizing: border-box;
  }
  h1, h2 {
    text-align: center;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
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
</style>
</head>
<body>
  <div class="container">
    <h1>Med Smart Hospital</h1>
    <h2>Appointment Confirmation</h2>
    <hr />

    <div class="field"><span class="field-label">Name:</span> <?= $name ?></div>
    <div class="field"><span class="field-label">Contact Number:</span> <?= $contact_number ?></div>
    <div class="field"><span class="field-label">Blood Group:</span> <?= $blood_group ?></div>

    <hr />

    <div class="field"><span class="field-label">Age:</span> <?= $age ?></div>
    <div class="field"><span class="field-label">Gender:</span> <?= $gender ?></div>
    <div class="field"><span class="field-label">Department:</span> <?= $department ?></div>
    <div class="field"><span class="field-label">Email:</span> <?= $email ?></div>
    <div class="field"><span class="field-label">Appointment Date:</span> <?= $formatted_date ?></div>
    <div class="field"><span class="field-label">Appointment Time:</span> <?= $formatted_time ?></div>
    <div class="field"><span class="field-label">Transaction Code:</span> <?= $transaction_code ?></div>

    <hr />

    <div class="footer-note">
      Please arrive at least 10 minutes before your appointment time.<br />
      Appointment fee Rs. 500 is non-refundable.<br />
      Thank you for choosing Med Smart Hospital.
    </div>

    <button class="print-btn" onclick="window.print()">Print Confirmation</button>
  </div>
</body>
</html>
