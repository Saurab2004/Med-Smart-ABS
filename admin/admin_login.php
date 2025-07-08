<?php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $servername = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "medsmart";

    $conn = new mysqli($servername, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT password_hash FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($password_hash);
        $stmt->fetch();

        if (password_verify($password, $password_hash)) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Login - Med Smart</title>
<style>
  body { font-family: Arial, sans-serif; background: #f7f7f7; }
  .login-container {
    max-width: 400px; margin: 100px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px #ccc;
  }
  h2 { text-align: center; margin-bottom: 20px; }
  label { display: block; margin-top: 10px; font-weight: bold; }
  input[type="text"], input[type="password"] {
    width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;
  }
  input[type="submit"] {
    margin-top: 20px; background-color: #007bff; color: white; border: none; padding: 12px; width: 100%; border-radius: 5px; cursor: pointer;
  }
  input[type="submit"]:hover {
    background-color: #0056b3;
  }
  .error { color: red; margin-top: 15px; text-align: center; }
</style>
</head>
<body>
  <div class="login-container">
    <h2>Admin Login</h2>
    <form method="POST" action="">
      <label for="username">Username</label>
      <input id="username" name="username" type="text" required />
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required />
      <input type="submit" value="Login" />
      <?php if($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
