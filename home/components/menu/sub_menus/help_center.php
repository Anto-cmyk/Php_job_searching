<?php
// help_center.php
require_once $_SERVER["DOCUMENT_ROOT"].'/db.php';
$user = $_SESSION['email'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($subject === '' || $message === '') $errors[] = 'All fields required.';
    if (empty($errors)) {
        $sql = "INSERT INTO help_requests (user_email, subject, message) VALUES (?, ?, ?)";
        $st = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($st, 'sss', $user, $subject, $message);
        mysqli_stmt_execute($st);
        header('Location: ../main_menu_page.php');
        exit();
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Help Center</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
  <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-lg font-bold mb-4">Help Center</h1>
    <?php if ($errors) foreach($errors as $err) echo '<div class="text-red-600">'.e($err).'</div>';?>
    <form method="post">
      <label class="block mb-2">Subject</label>
      <input type="text" name="subject" class="w-full p-2 border rounded mb-2" required>
      <label class="block mb-2">Message</label>
      <textarea name="message" class="w-full p-2 border rounded mb-2" required></textarea>
      <div class="mt-4"><button class="px-4 py-2 bg-blue-600 text-white rounded">Send Request</button></div>
    </form>
  </div>
</body>
</html>
