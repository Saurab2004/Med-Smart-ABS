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

// Set image folder relative to this PHP script
$target_dir = "doctorsimages/";

// Add Doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_doctor'])) {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $contact = $_POST['contact'];
    $available = $_POST['available'];
    $username = $_POST['username'];
    $image_path = '';

    if (!empty($_FILES['image']['name'])) {
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $image_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $image_path = $target_dir . $image_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            die("Error uploading image.");
        }
    }

    $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, contact_number, image_path, available, username) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $specialization, $contact, $image_path, $available, $username);
    $stmt->execute();
    $stmt->close();
    header("Location: doctors.php");
    exit;
}

// Delete Doctor
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Optional: delete old image file
    $res = $conn->query("SELECT image_path FROM doctors WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['image_path']) && file_exists($row['image_path'])) {
            unlink($row['image_path']);
        }
    }
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
    $username = $_POST['username'];
    $image_path = $_POST['current_image'];

    if (!empty($_FILES['image']['name'])) {
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Delete old image if exists
        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path);
        }

        $image_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $image_path = $target_dir . $image_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            die("Error uploading image.");
        }
    }

    $stmt = $conn->prepare("UPDATE doctors SET name=?, specialization=?, contact_number=?, available=?, image_path=?, username=? WHERE id=?");
    $stmt->bind_param("ssssssi", $name, $specialization, $contact, $available, $image_path, $username, $id);
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
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f7f9fb;
    }
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
      transition: 0.3s;
    }
    .sidebar a:hover { background: #0056b3; }
    .main-content {
      margin-left: 220px;
      padding: 40px;
      width: calc(100% - 220px);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      border-radius: 8px;
      overflow: hidden;
    }
    th, td {
      padding: 12px 15px;
      border-bottom: 1px solid #eee;
    }
    th {
      background-color: #007bff;
      color: white;
      text-align: left;
    }
    img {
      width: 60px;
      height: auto;
      border-radius: 6px;
    }
    h1, h2 {
      margin-top: 0;
    }
    form {
      margin-top: 30px;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    .form-group {
      margin-bottom: 15px;
    }
    label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
    }
    input[type="text"], select, input[type="file"] {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      outline: none;
      transition: 0.2s;
    }
    input[type="text"]:focus, select:focus {
      border-color: #007bff;
    }
    input[type="submit"] {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: 600;
    }
    .delete-link, .edit-link {
      text-decoration: none;
      margin-right: 10px;
      font-weight: 600;
    }
    .delete-link {
      color: red;
    }
    .edit-link {
      color: #007bff;
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
          <th>Username</th>
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
          <td><?= htmlspecialchars($row['username']) ?></td>
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

        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" value="<?= htmlspecialchars($edit['name']) ?>" required>
        </div>

        <div class="form-group">
          <label>Specialization</label>
          <input type="text" name="specialization" value="<?= htmlspecialchars($edit['specialization']) ?>" required>
        </div>

        <div class="form-group">
          <label>Contact Number</label>
          <input type="text" name="contact" value="<?= htmlspecialchars($edit['contact_number']) ?>">
        </div>

        <div class="form-group">
          <label>Doctor ID (Username)</label>
          <input type="text" name="username" value="<?= htmlspecialchars($edit['username']) ?>" required>
        </div>

        <input type="hidden" name="current_image" value="<?= $edit['image_path'] ?>">

        <div class="form-group">
          <label>Image</label>
          <input type="file" name="image">
        </div>

        <div class="form-group">
          <label>Availability</label>
          <select name="available">
            <option value="Yes" <?= $edit['available'] === 'Yes' ? 'selected' : '' ?>>Available</option>
            <option value="No" <?= $edit['available'] === 'No' ? 'selected' : '' ?>>Not Available</option>
          </select>
        </div>

        <input type="submit" name="edit_doctor" value="Update Doctor">
      </form>
    <?php else: ?>
      <form method="POST" enctype="multipart/form-data">

        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" placeholder="Doctor's Name" required>
        </div>

        <div class="form-group">
          <label>Specialization</label>
          <input type="text" name="specialization" placeholder="Specialization" required>
        </div>

        <div class="form-group">
          <label>Contact Number</label>
          <input type="text" name="contact" placeholder="Phone number">
        </div>

        <div class="form-group">
          <label>Doctor ID (Username)</label>
          <input type="text" name="username" placeholder="Unique doctor ID" required>
        </div>

        <div class="form-group">
          <label>Image</label>
          <input type="file" name="image">
        </div>

        <div class="form-group">
          <label>Availability</label>
          <select name="available">
            <option value="Yes">Available</option>
            <option value="No">Not Available</option>
          </select>
        </div>

        <input type="submit" name="add_doctor" value="Add Doctor">
      </form>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
