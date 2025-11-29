<?php
// report_fraud.php
require_once $_SERVER["DOCUMENT_ROOT"].'/db.php';
$user = $_SESSION['email'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entity = trim($_POST['reported_entity'] ?? '');
    $details = trim($_POST['details'] ?? '');
    if ($entity === '' || $details === '') $errors[] = 'All fields required.';

    $evidence_path = null;
    if (!empty($_FILES['evidence']) && $_FILES['evidence']['error'] === UPLOAD_ERR_OK) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['evidence']['tmp_name']);
        $allowed = ['image/jpeg','image/png','application/pdf'];
        if (!in_array($mime, $allowed)) $errors[] = 'Evidence must be PNG/JPG/PDF.';
        elseif ($_FILES['evidence']['size'] > 5*1024*1024) $errors[] = 'Evidence too large (max 5MB).';
        else {
            $upload_dir = __DIR__ . '/uploads/evidence/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $fn = bin2hex(random_bytes(8)) . '-' . preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($_FILES['evidence']['name']));
            if (move_uploaded_file($_FILES['evidence']['tmp_name'], $upload_dir.$fn)) {
                $evidence_path = 'uploads/evidence/'.$fn;
            } else {
                $errors[] = 'Failed to store evidence.';
            }
        }
    }

    if (empty($errors)) {
        $sql = "INSERT INTO fraud_reports (reporter_email, reported_entity, details, evidence_path) VALUES (?, ?, ?, ?)";
        $st = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($st, 'ssss', $user, $entity, $details, $evidence_path);
        mysqli_stmt_execute($st);
        header('Location: myprofile.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Report Fraud</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
  <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-lg font-bold mb-4">Report Fraud</h1>
    <?php if ($errors) foreach($errors as $err) echo '<div class="text-red-600">'.e($err).'</div>';?>
    <form method="post" enctype="multipart/form-data">
      <label class="block mb-2">Reported entity (company or person)</label>
      <input type="text" name="reported_entity" class="w-full p-2 border rounded mb-2" required>
      <label class="block mb-2">Details</label>
      <textarea name="details" class="w-full p-2 border rounded mb-2" required></textarea>
      <label class="block mb-2">Evidence (optional)</label>
      <input type="file" name="evidence" accept="image/*,application/pdf">
      <div class="mt-4"><button class="px-4 py-2 bg-red-600 text-white rounded">Submit Report</button></div>
    </form>
  </div>
</body>
</html>
