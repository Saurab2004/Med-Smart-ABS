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

// Add Doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_doctor'])) {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $contact = $_POST['contact'];
    $available = $_POST['available'];
    $image_path = '';

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "adminimage/";
        $image_path = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, contact_number, image_path, available) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $specialization, $contact, $image_path, $available);
    $stmt->execute();
    $stmt->close();
    header("Location: doctors.php");
    exit;
}

// Delete Doctor
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM doctors WHERE id = $id");
    header("Location: doctors.php");
    exit;
}

// Update Doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_doctor'])) {
    $id = intval($_POST['doctor_id']);
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $contact = $_POST['contact'];
    $available = $_POST['available'];
    $image_path = $_POST['current_image'];

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "adminimage/";
        $image_path = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    $stmt = $conn->prepare("UPDATE doctors SET name=?, specialization=?, contact_number=?, available=?, image_path=? WHERE id=?");
    $stmt->bind_param("sssssi", $name, $specialization, $contact, $available, $image_path, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: doctors.php");
    exit;
}

// Get Doctors
$result = $conn->query("SELECT * FROM doctors ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Doctors - Med Smart</title>
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
    }
    th, td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }
    th { background-color: #007bff; color: white; }
    img { width: 60px; height: auto; }
    form { margin-top: 30px; }
    input, select {
      padding: 8px;
      margin-bottom: 10px;
      width: 100%;
    }
    input[type="submit"] {
      background-color: #28a745;
      color: white;
      border: none;
      cursor: pointer;
      width: auto;
      padding: 10px 20px;
    }
    .delete-link, .edit-link {
      color: red;
      text-decoration: none;
      margin-right: 10px;
    }
    .edit-link { color: #007bff; }
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
    <h1>Doctors List</h1>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Name</th>
          <th>Specialization</th>
          <th>Contact</th>
          <th>Available</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td>
            <?php if ($row['image_path']): ?>
              <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Doctor">
            <?php else: ?>N/A<?php endif; ?>
          </td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['specialization']) ?></td>
          <td><?= htmlspecialchars($row['contact_number']) ?></td>
          <td><?= $row['available'] ?></td>
          <td>
            <a class="edit-link" href="?edit=<?= $row['id'] ?>">Edit</a>
            <a class="delete-link" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this doctor?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <h2><?= isset($_GET['edit']) ? 'Edit Doctor' : 'Add New Doctor' ?></h2>

    <?php
    if (isset($_GET['edit'])):
      $edit_id = intval($_GET['edit']);
      $edit = $conn->query("SELECT * FROM doctors WHERE id = $edit_id")->fetch_assoc();
    ?>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="doctor_id" value="<?= $edit['id'] ?>">
        <input type="text" name="name" value="<?= htmlspecialchars($edit['name']) ?>" required>
        <input type="text" name="specialization" value="<?= htmlspecialchars($edit['specialization']) ?>" required>
        <input type="text" name="contact" value="<?= htmlspecialchars($edit['contact_number']) ?>">
        <input type="hidden" name="current_image" value="<?= $edit['image_path'] ?>">
        <input type="file" name="image">
        <select name="available">
          <option value="Yes" <?= $edit['available'] === 'Yes' ? 'selected' : '' ?>>Available</option>
          <option value="No" <?= $edit['available'] === 'No' ? 'selected' : '' ?>>Not Available</option>
        </select>
        <input type="submit" name="edit_doctor" value="Update Doctor">
      </form>
    <?php else: ?>
      <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Name" required>
        <input type="text" name="specialization" placeholder="Specialization" required>
        <input type="text" name="contact" placeholder="Contact Number">
        <input type="file" name="image">
        <select name="available">
          <option value="Yes">Available</option>
          <option value="No">Not Available</option>
        </select>
        <input type="submit" name="add_doctor" value="Add Doctor">
      </form>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
