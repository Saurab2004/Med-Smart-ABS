<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "medsmart");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all patients
$result = $conn->query("SELECT * FROM patients ORDER BY name ASC");

// If a specific patient is selected
$selected_patient = null;
if (isset($_GET['id'])) {
    $pid = intval($_GET['id']);
    $patient_result = $conn->query("SELECT * FROM patients WHERE id = $pid");
    if ($patient_result->num_rows > 0) {
        $selected_patient = $patient_result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Patients - Med Smart</title>
<style>
  body { margin: 0; font-family: Arial, sans-serif; }
  .container { display: flex; }
  .sidebar {
    width: 220px;
    background-color: #007bff;
    color: white;
    height: 100vh;
    padding-top: 30px;
    position: fixed;
  }
  .sidebar h2 { text-align: center; }
  .sidebar a {
    display: block;
    padding: 15px 20px;
    color: white;
    text-decoration: none;
  }
  .sidebar a:hover { background: #0056b3; }
  .main-content {
    margin-left: 220px;
    padding: 20px;
    width: calc(100% - 220px);
  }
  table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 0 10px #ccc;
    margin-bottom: 30px;
  }
  th, td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: left;
  }
  th {
    background-color: #007bff;
    color: white;
  }
  tr:hover {
    background-color: #f1f1f1;
  }
</style>
</head>
<body>

<div class="container">
  <div class="sidebar">
    <h2>MedSmart</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="doctors.php">Doctors</a>
    <a href="patients.php">Patients</a>
    <a href="admin_logout.php">Logout</a>
  </div>

  <div class="main-content">
    <h1>Patients List</h1>

    <?php if ($selected_patient): ?>
      <h2>Patient Details</h2>
      <table>
        <tr><th>ID</th><td><?= $selected_patient['id'] ?></td></tr>
        <tr><th>Name</th><td><?= htmlspecialchars($selected_patient['name']) ?></td></tr>
        <tr><th>Age</th><td><?= htmlspecialchars($selected_patient['age']) ?></td></tr>
        <tr><th>Gender</th><td><?= htmlspecialchars($selected_patient['gender']) ?></td></tr>
        <tr><th>Contact</th><td><?= htmlspecialchars($selected_patient['contact_number']) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($selected_patient['email']) ?></td></tr>
        <tr><th>Blood Group</th><td><?= htmlspecialchars($selected_patient['blood_group']) ?></td></tr>
      </table>
      <hr>
    <?php endif; ?>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Age</th>
          <th>Contact</th>
          <th>Blood Group</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><a href="patients.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></a></td>
          <td><?= htmlspecialchars($row['age']) ?></td>
          <td><?= htmlspecialchars($row['contact_number']) ?></td>
          <td><?= htmlspecialchars($row['blood_group']) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
