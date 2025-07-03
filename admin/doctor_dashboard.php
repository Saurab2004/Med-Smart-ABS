<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION["doctor_logged_in"])) {
    header("Location: doctor_login.php");
    exit();
}
?>

<!-- Admin dashboard HTML content goes here -->
<!DOCTYPE html>
<html>
<head>
  <title>Doctor Dashboard</title>
  <!-- You can reuse the same HTML/CSS as before -->
</head>
<body>
  <h1>Welcome to the Doctor Dashboard</h1>
  <p>Only visible after login.</p>
  <a href="logout.php">Logout</a>
</body>
</html>
