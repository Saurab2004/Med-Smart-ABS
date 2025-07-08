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

// Handle payment status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'], $_POST['payment_status'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $payment_status = $_POST['payment_status'] === 'Verified' ? 'Verified' : 'Pending';

    $stmt = $conn->prepare("UPDATE appointments SET payment_status = ? WHERE id = ?");
    $stmt->bind_param("si", $payment_status, $appointment_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all appointments
$result = $conn->query("SELECT * FROM appointments ORDER BY appointment_date DESC, appointment_time DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard - Med Smart</title>
<style>
  body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 20px; }
  h1 { text-align: center; margin-bottom: 30px; }
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
  form {
    margin: 0;
  }
  select {
    padding: 5px;
    border-radius: 5px;
  }
  input[type="submit"] {
    padding: 5px 10px;
    margin-left: 5px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  input[type="submit"]:hover {
    background-color: #218838;
  }
  .logout-btn {
    display: block;
    margin: 20px auto 40px auto;
    width: 100px;
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
</style>
</head>
<body>

<h1>Admin Dashboard - Med Smart</h1>
<a href="admin_logout.php" class="logout-btn">Logout</a>

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
      <th>Transaction Code</th>
      <th>Payment Status</th>
      <th>Verify Payment</th>
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
        <td><?= htmlspecialchars($row['transaction_code']) ?></td>
        <td><?= $row['payment_status'] ?></td>
        <td>
          <form method="POST" action="">
            <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>" />
            <select name="payment_status">
              <option value="Pending" <?= $row['payment_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
              <option value="Verified" <?= $row['payment_status'] == 'Verified' ? 'selected' : '' ?>>Verified</option>
            </select>
            <input type="submit" value="Update" />
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>

<?php
$conn->close();
?>
