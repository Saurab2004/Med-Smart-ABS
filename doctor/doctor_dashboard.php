<?php
session_start();
if (!isset($_SESSION['doctor_logged_in']) || $_SESSION['doctor_logged_in'] !== true) header("Location: doctor_login.php");

$conn = new mysqli("localhost", "root", "", "medsmart");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$doctor_id = $_SESSION['doctor_id'];
$stmt = $conn->prepare("SELECT * FROM appointments WHERE doctor_id=? ORDER BY appointment_date ASC, appointment_time ASC");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctor Dashboard - MedSmart</title>
<style>
body { font-family:'Segoe UI',sans-serif; background:#f7f9fb; margin:0;}
.container { display:flex; }
.sidebar { width:220px; background:#007bff; color:white; height:100vh; padding-top:30px; position:fixed;}
.sidebar h2 { text-align:center; }
.sidebar a { display:block; padding:15px 20px; color:white; text-decoration:none; }
.sidebar a:hover { background:#0056b3; }
.main-content { margin-left:220px; padding:40px; width:calc(100% - 220px); }
table { width:100%; border-collapse:collapse; background:white; border-radius:8px; overflow:hidden; box-shadow:0 0 10px rgba(0,0,0,0.05);}
th, td { padding:12px 15px; border-bottom:1px solid #eee; }
th { background:#007bff; color:white; text-align:left; }
h1 { margin-top:0; }
</style>
</head>
<body>
<div class="container">
<div class="sidebar">
<h2>MedSmart</h2>
<a href="doctor_dashboard.php">Dashboard</a>
<a href="doctor_logout.php">Logout</a>
</div>

<div class="main-content">
<h1>Welcome, <?= htmlspecialchars($_SESSION['doctor_name']) ?></h1>
<h2>Your Appointments</h2>
<table>
<thead>
<tr>
<th>ID</th><th>Patient Name</th><th>Age</th><th>Gender</th><th>Department</th><th>Contact</th><th>Blood Group</th><th>Date</th><th>Time</th><th>Status</th>
</tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['patient_name']) ?></td>
<td><?= htmlspecialchars($row['patient_age']) ?></td>
<td><?= htmlspecialchars($row['gender']) ?></td>
<td><?= htmlspecialchars($row['department']) ?></td>
<td><?= htmlspecialchars($row['contact_number']) ?></td>
<td><?= htmlspecialchars($row['blood_group']) ?></td>
<td><?= htmlspecialchars($row['appointment_date']) ?></td>
<td><?= htmlspecialchars($row['appointment_time']) ?></td>
<td><?= htmlspecialchars($row['status']) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>
</body>
</html>
