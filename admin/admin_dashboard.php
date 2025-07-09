<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medsmart";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all appointments
$result = $conn->query("SELECT * FROM appointments ORDER BY appointment_date DESC, appointment_time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Appointments - Med Smart</title>
<style>
  * {
    box-sizing: border-box;
  }
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f7f7f7;
    display: flex;
  }
  .sidebar {
    width: 220px;
    background-color: #007bff;
    color: white;
    height: 100vh;
    position: fixed;
    padding-top: 30px;
  }
  .sidebar h2 {
    text-align: center;
    font-size: 20px;
    margin-bottom: 30px;
  }
  .sidebar a {
    display: block;
    padding: 15px 20px;
    color: white;
    text-decoration: none;
  }
  .sidebar a:hover {
    background-color: #0056b3;
  }
  .logout-btn {
    display: block;
    width: 80%;
    margin: 20px auto;
    padding: 10px;
    background: #dc3545;
    color: white;
    text-align: center;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
  }
  .logout-btn:hover {
    background: #b52b2b;
  }
  .main-content {
    margin-left: 220px;
    padding: 20px;
    width: calc(100% - 220px);
  }
  h1 {
    text-align: center;
    margin-bottom: 30px;
  }
  table {
    border-collapse: collapse;
    width: 100%;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0 10px #ccc;
  }
  th, td {
    padding: 12px 15px;
    border-bottom: 1px solid #ddd;
    text-align: left;
  }
  th {
    background: #007bff;
    color: white;
  }
  tr:hover {
    background-color: #f1f1f1;
  }
</style>
</head>
<body>

<div class="sidebar">
  <h2>Med-Smart</h2>
  <a href="admin_dashboard.php">Dashboard</a>
  <a href="doctors.php">Doctors</a>
  <a href="patients.php">Patients</a>
  <a href="admin_logout.php" class="logout-btn">Logout</a>
</div>

<div class="main-content">
  <h1>Appointments</h1>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Contact</th>
        <th>Blood Group</th>
        <th>Department</th>
        <th>Appointment Date</th>
        <th>Appointment Time</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['contact_number']) ?></td>
          <td><?= htmlspecialchars($row['blood_group']) ?></td>
          <td><?= htmlspecialchars($row['department']) ?></td>
          <td><?= htmlspecialchars($row['appointment_date']) ?></td>
          <td><?= htmlspecialchars($row['appointment_time']) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
