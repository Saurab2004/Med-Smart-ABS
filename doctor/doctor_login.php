<?php
session_start();
$conn = new mysqli("localhost", "root", "", "medsmart");
if ($conn->connect_error) die("DB connection failed: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $stmt = $conn->prepare("SELECT id, password_hash, name FROM doctors WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['doctor_logged_in'] = true;
            $_SESSION['doctor_id'] = $row['id'];
            $_SESSION['doctor_name'] = $row['name'];
            header("Location: doctor_dashboard.php");
            exit;
        } else { $error = "Invalid password"; }
    } else { $error = "Invalid username"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Login - MedSmart</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f2f5;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.login-container {
    background-color: #fff;
    padding: 40px 30px;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    width: 350px;
}

h2 {
    text-align: center;
    color: #007BFF;
    margin-bottom: 25px;
    font-size: 24px;
}

form label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

form input[type="text"],
form input[type="password"] {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 20px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: border 0.3s ease;
}

form input[type="text"]:focus,
form input[type="password"]:focus {
    border-color: #007BFF;
    outline: none;
}

form input[type="submit"] {
    width: 100%;
    padding: 12px;
    background-color: #007BFF;
    border: none;
    border-radius: 6px;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

form input[type="submit"]:hover {
    background-color: #0056b3;
}

.error-msg {
    color: red;
    text-align: center;
    margin-bottom: 15px;
    font-weight: 600;
}
</style>
</head>
<body>
<div class="login-container">
<h2>Doctor Login</h2>
<?php if(isset($error)) echo "<p class='error-msg'>$error</p>"; ?>
<form method="POST">
    <label>Username:</label>
    <input type="text" name="username" required>
    <label>Password:</label>
    <input type="password" name="password" required>
    <input type="submit" value="Login">
</form>
</div>
</body>
</html>
