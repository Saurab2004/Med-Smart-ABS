<!-- /doctor/login.php -->
<?php
session_start();
$conn = new mysqli("localhost", "root", "", "medsmart");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_POST['doctor_id'];
    $specialization = $_POST['specialization'];

    $sql = "SELECT * FROM doctors WHERE id=? AND specialization=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $doctor_id, $specialization);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['doctor_id'] = $doctor_id;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid ID or Specialization";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Login</title>
</head>
<body>
<h2>Doctor Login</h2>
<form method="post">
    Doctor ID: <input type="number" name="doctor_id" required><br>
    Specialization: <input type="text" name="specialization" required><br>
    <button type="submit">Login</button>
</form>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
</body>
</html>
