<?php
session_start();
$conn = new mysqli("localhost", "root", "", "medsmart");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, password_hash, name, age, gender, contact_number, blood_group 
            FROM patients WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            // Store user info in session
            $_SESSION['patient_id'] = $user['id'];
            $_SESSION['patient_name'] = $user['name'];
            $_SESSION['patient_email'] = $email;
            $_SESSION['patient_age'] = $user['age'];
            $_SESSION['patient_gender'] = $user['gender'];
            $_SESSION['patient_contact'] = $user['contact_number'];
            $_SESSION['patient_blood'] = $user['blood_group'];

            // Redirect to home page (index.php)
            header("Location: index.php");
            exit;
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Login</title>
<style>
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f4f9ff;
  margin: 0; padding: 0;
}
.form-container {
  width: 90%; max-width: 400px;
  margin: 60px auto; padding: 30px;
  background: #ffffff; border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
.form-container h2 {
  text-align: center; color: #2c3e50; margin-bottom: 25px;
}
.form-container label { display: block; margin-bottom: 8px; color: #555; font-weight: 500; }
.form-container input {
  width: 100%; padding: 10px 12px; margin-bottom: 20px;
  border: 1px solid #ccc; border-radius: 6px; font-size: 14px;
}
.form-container button {
  width: 100%; padding: 12px; border: none; border-radius: 8px;
  background-color: #1976d2; color: #fff; font-size: 16px; font-weight: 600;
  cursor: pointer; transition: all 0.3s ease;
}
.form-container button:hover { background-color: #1565c0; transform: translateY(-2px); }
.form-container .switch { text-align: center; margin-top: 15px; font-size: 14px; }
.form-container .switch a { color: #1976d2; text-decoration: none; font-weight: 600; }
.form-container .switch a:hover { text-decoration: underline; }
p { color: red; text-align: center; }
</style>
</head>
<body>

<div class="form-container">
  <h2>Patient Login</h2>
  <?php if($message) echo "<p>$message</p>"; ?>
  <form method="POST" action="">
    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>
  </form>
  <div class="switch">Don't have an account? <a href="signup.php">Signup</a></div>
</div>

</body>
</html>
