<?php
session_start();
if (!isset($_SESSION['doctor_logged_in']) || $_SESSION['doctor_logged_in'] !== true) {
    header("Location: doctor_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "medsmart");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$doctor_id = $_SESSION['doctor_id'];
$update_message = "";

// Handle doctor report upload
if(isset($_POST['upload_report'])){
    $appointment_id = intval($_POST['appointment_id']);
    $status = $_POST['status'];

    if(isset($_FILES['doctor_report']) && $_FILES['doctor_report']['error'] == 0){
        $target_dir = "uploads/"; // Upload inside patient folder: doctor dashboard is in doctor/
        if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $filename = time() . "_" . basename($_FILES['doctor_report']['name']);
        $target_file = $target_dir . $filename;

        if(move_uploaded_file($_FILES['doctor_report']['tmp_name'], "../patient/" . $target_file)){
            $stmt = $conn->prepare("UPDATE appointments SET doctor_report_path=? , status=? WHERE id=?");
            $stmt->bind_param("ssi", $target_file, $status, $appointment_id);
            if($stmt->execute()){
                $update_message = "Report uploaded successfully!";
            } else {
                $update_message = "Failed to update appointment!";
            }
            $stmt->close();
        } else {
            $update_message = "Failed to upload the file!";
        }
    } else {
        $update_message = "No file selected!";
    }
}

// Fetch appointments
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
th, td { padding:12px 15px; border-bottom:1px solid #eee; text-align:left; }
th { background:#007bff; color:white; }
h1 { margin-top:0; }
a.view-report { color:#007bff; text-decoration:none; }
a.view-report:hover { text-decoration:underline; }
form { margin:0; }
input[type=file] { margin-top:5px; }
select, button { padding:5px 10px; margin-top:5px; border-radius:5px; border:1px solid #ccc; }
button { background:#007bff; color:white; cursor:pointer; }
button:hover { background:#0056b3; }
p.message { color:green; font-weight:bold; }
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
<?php if($update_message) echo "<p class='message'>$update_message</p>"; ?>
<h2>Your Appointments</h2>
<table>
<thead>
<tr>
<th>ID</th><th>Patient Name</th><th>Age</th><th>Gender</th><th>Department</th><th>Contact</th><th>Blood Group</th><th>Date</th><th>Time</th><th>Status</th><th>Patient Report</th><th>Doctor Report</th>
</tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['patient_name']) ?></td>
<td><?= $row['patient_age'] ?></td>
<td><?= htmlspecialchars($row['gender']) ?></td>
<td><?= htmlspecialchars($row['department']) ?></td>
<td><?= htmlspecialchars($row['contact_number']) ?></td>
<td><?= htmlspecialchars($row['blood_group']) ?></td>
<td><?= htmlspecialchars($row['appointment_date']) ?></td>
<td><?= htmlspecialchars($row['appointment_time']) ?></td>
<td><?= htmlspecialchars($row['status']) ?></td>

<!-- Patient uploaded report -->
<td>
<?php if(!empty($row['report_path']) && file_exists("../patient/" . $row['report_path'])): ?>
    <a href="../patient/<?= htmlspecialchars($row['report_path']) ?>" target="_blank" class="view-report">View</a>
<?php else: ?>
    No report
<?php endif; ?>
</td>

<!-- Doctor upload & view report -->
<td>
<form method="POST" enctype="multipart/form-data">
<input type="file" name="doctor_report" required>
<select name="status">
    <option value="pending" <?= $row['status']=='pending'?'selected':'' ?>>Pending</option>
    <option value="completed" <?= $row['status']=='completed'?'selected':'' ?>>Completed</option>
</select>
<input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
<button type="submit" name="upload_report">Upload</button>
</form>
<?php if(!empty($row['doctor_report_path']) && file_exists("../patient/" . $row['doctor_report_path'])): ?>
<br><a href="../patient/<?= htmlspecialchars($row['doctor_report_path']) ?>" target="_blank" class="view-report">View</a>
<?php endif; ?>
</td>

</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>

</body>
</html>
