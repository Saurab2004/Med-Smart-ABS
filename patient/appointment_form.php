<?php
// appointment_form.php
// Get doctor info from URL
$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;
$doctor_name = isset($_GET['doctor_name']) ? htmlspecialchars(trim($_GET['doctor_name'])) : '';
$department = isset($_GET['department']) ? htmlspecialchars(trim($_GET['department'])) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Appointment - MedSmart</title>
<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #e9f0f5; margin: 0; padding: 20px; }
.container { max-width: 600px; background: #fff; padding: 30px 40px; margin: 30px auto; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
h2 { text-align: center; color: #007B5E; margin-bottom: 25px; }
form label { display: block; margin: 15px 0 5px; font-weight: 600; color: #333; }
input, select { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; transition: 0.3s border ease; }
input:focus, select:focus { border-color: #007B5E; outline: none; }
input[type="submit"] { background-color: #007B5E; color: white; font-weight: bold; border: none; cursor: pointer; padding: 12px; margin-top: 25px; transition: background-color 0.3s ease; border-radius: 6px; }
input[type="submit"]:hover { background-color: #005f48; }
input[readonly] { background-color: #f1f1f1; cursor: not-allowed; }
</style>
</head>
<body>

<div class="container">
<h2>Book an Appointment</h2>
<form action="appointment.php" method="POST" enctype="multipart/form-data">
    <label for="name">Name:</label>
    <input id="name" type="text" name="name" required />

    <label for="age">Age:</label>
    <input id="age" type="number" name="age" min="0" required />

    <label for="gender">Gender:</label>
    <select id="gender" name="gender" required>
        <option value="">--Select--</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
    </select>

    <label for="contact_number">Contact Number (+977):</label>
    <input id="contact_number" type="tel" name="contact_number" placeholder="+977XXXXXXXXXX" required pattern="\+977[0-9]{10}" title="Number must start with +977 followed by 10 digits" />

    <label for="email">Email:</label>
    <input id="email" type="email" name="email" required />

    <label for="blood_group">Blood Group:</label>
    <select id="blood_group" name="blood_group" required>
        <option value="">--Select--</option>
        <option value="A+">A+</option>
        <option value="A-">A-</option>
        <option value="B+">B+</option>
        <option value="B-">B-</option>
        <option value="O+">O+</option>
        <option value="O-">O-</option>
        <option value="AB+">AB+</option>
        <option value="AB-">AB-</option>
    </select>

    <label for="appointment_date">Appointment Date:</label>
    <input id="appointment_date" type="date" name="appointment_date" required />

    <label for="appointment_time">Appointment Time:</label>
    <input id="appointment_time" type="time" name="appointment_time" required />

    <label for="patient_image">Upload Image/Report (optional):</label>
    <input id="patient_image" type="file" name="patient_image" accept="image/*" />

    <label for="doctor_name">Doctor:</label>
    <input type="text" name="doctor_name" value="<?= $doctor_name ?>" readonly />

    <label for="department">Department:</label>
    <input type="text" name="department" value="<?= $department ?>" readonly />

    <input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">

    <input type="submit" value="Book Appointment" />
</form>
</div>

<script>
const contactInput = document.getElementById('contact_number');
contactInput.addEventListener('focus', function () {
    if (!this.value.startsWith('+977')) { this.value = '+977'; }
});

const dateInput = document.getElementById("appointment_date");
const today = new Date().toISOString().split("T")[0];
dateInput.setAttribute("min", today);

const timeInput = document.getElementById("appointment_time");
timeInput.setAttribute("min", "08:00");
timeInput.setAttribute("max", "20:00");
</script>

</body>
</html>
