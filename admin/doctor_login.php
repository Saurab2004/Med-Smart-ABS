<?php
session_start();

// Example credentials
$valid_user = "doctor";
$valid_pass = "medsmart123";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($username === $valid_user && $password === $valid_pass) {
        $_SESSION["doctor_logged_in"] = true;
        header("Location: doctor_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Doctor Login</title>
    
</head>
<body>
    <h2>Doctor Login</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
