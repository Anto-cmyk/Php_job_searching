<?php
session_start();
include 'includes/config.php';

// ✅ CSRF Token
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ✅ Ensure logged in
if (!isset($_SESSION['user_email'])) {
  header('Location: login.php');
  exit();
}

$email = $_SESSION['user_email'];
$message = '';

// ✅ Fetch current user data
$stmt = $conn->prepare("SELECT name, email, introduction, resume FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ✅ Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF validation failed!");
  }

  // 🔹 Update Name Only
  if (isset($_POST['update_name'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
      $stmt = $conn->prepare("UPDATE users SET name=? WHERE email=?");
      $stmt->bind_param("ss", $name, $email);
      $stmt->execute();
      $stmt->close();
      $message = "Name updated successfully!";
    } else {
      $message = "Name cannot be empty.";
    }
  }

  // 🔹 Update Introduction Only
  if (isset($_POST['update_intro'])) {
    $intro = trim($_POST['introduction']);
    $stmt = $conn->prepare("UPDATE users SET introduction=? WHERE email=?");
    $stmt->bind_param("ss", $intro, $email);
    $stmt->execute();
    $stmt->close();
    $message = "Introduction updated successfully!";
  }

  // 🔹 Change Password
  if (isset($_POST['change_password'])) {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (password_verify($old, $res['password'])) {
      $hashed = password_hash($new, PASSWORD_DEFAULT);
      $stmt2 = $conn->prepare("UPDATE users SET password=? WHERE email=?");
      $stmt2->bind_param("ss", $hashed, $email);
      $stmt2->execute();
      $stmt2->close();
      $message = "Password changed successfully!";
    } else {
      $message = "Old password is incorrect!";
    }
  }

  // 🔹 Upload Resume
  if (isset($_POST['upload_resume']) && isset($_FILES['resume'])) {
    if ($_FILES['resume']['error'] === 0) {
      $allowed = ['pdf', 'doc', 'docx'];
      $ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);

      if (in_array(strtolower($ext), $allowed)) {
        $file_name = uniqid('resume_', true) . '.' . $ext;
        $target = 'uploads/resumes/' . $file_name;
        if (move_uploaded_file($_FILES['resume']['tmp_name'], $target)) {
          $stmt = $conn->prepare("UPDATE users SET resume=? WHERE email=?");
          $stmt->bind_param("ss", $target, $email);
          $stmt->execute();
          $stmt->close();
          $message = "Resume uploaded successfully!";
        } else {
          $message = "Failed to upload resume.";
        }
      } else {
        $message = "Invalid file format.";
      }
    }
  }

  // 🔹 Delete Account
  if (isset($_POST['delete_account'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();
    session_destroy();
    header("Location: register.php");
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="max-w-3xl mx-auto mt-10 bg-white p-6 shadow-lg rounded-xl">
  <h2 class="text-2xl font-bold mb-4">Edit Profile</h2>

  <?php if ($message): ?>
    <p class="bg-green-100 text-green-700 p-3 rounded mb-3"><?php echo htmlspecialchars($message); ?></p>
  <?php endif; ?>

  <!-- 🔹 Update Name -->
  <form method="POST" class="space-y-3">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <div>
      <label class="block text-sm font-medium">Name</label>
      <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="w-full border rounded p-2">
    </div>
    <button name="update_name" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Name</button>
  </form>

  <hr class="my-6">

  <!-- 🔹 Update Introduction -->
  <form method="POST" class="space-y-3">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <div>
      <label class="block text-sm font-medium">Introduction / Summary</label>
      <textarea name="introduction" rows="3" class="w-full border rounded p-2"><?php echo htmlspecialchars($user['introduction'] ?? ''); ?></textarea>
    </div>
    <button name="update_intro" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Update Summary</button>
  </form>

  <hr class="my-6">

  <!-- 🔹 Change Password -->
  <form method="POST" class="space-y-3">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <h3 class="text-lg font-medium">Change Password</h3>
    <input type="password" name="old_password" placeholder="Old Password" required class="w-full border rounded p-2">
    <input type="password" name="new_password" placeholder="New Password" required class="w-full border rounded p-2">
    <button name="change_password" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Change Password</button>
  </form>

  <hr class="my-6">

  <!-- 🔹 Upload Resume -->
  <form method="POST" enctype="multipart/form-data" class="space-y-3">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <h3 class="text-lg font-medium">Upload Resume</h3>
    <input type="file" name="resume" accept=".pdf,.doc,.docx" required class="block w-full border p-2 rounded">
    <button name="upload_resume" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Upload Resume</button>
  </form>

  <hr class="my-6">

  <!-- 🔹 Delete Account -->
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <button name="delete_account" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700" onclick="return confirm('Are you sure you want to delete your account?');">Delete Account</button>
  </form>
</div>
</body>
</html>
