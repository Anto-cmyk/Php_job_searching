<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';
if (!session_id()) session_start();

// Validate job ID
if (!isset($_GET['id'])) {
    header("Location: ../mainPage.php");
    exit();
}

$id = (int) $_GET['id'];
$query = "SELECT * FROM jobs WHERE id = $id LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<p class='text-center text-red-600 mt-10'>Job not found.</p>";
    exit;
}

$job = mysqli_fetch_assoc($result);

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token mismatch!");
    }

    // Sanitize user input (basic)
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Prepared statement
    $sql = "INSERT INTO applicants (job_id, applicant_name, applicant_email, message)
            VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters: i = integer, s = string
        mysqli_stmt_bind_param($stmt, "isss", $id, $name, $email, $message);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('✅ Application submitted successfully!');</script>";
        } else {
            echo "<script>alert('❌ Error submitting your application.');</script>";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('❌ Failed to prepare statement.');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($job['title']) ?> - Job Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
  html {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    width: 100%;
  }
</style>

</head>
<body class="bg-gray-100 text-gray-800 w-full min-h-screen m-0 p-0">

<!-- Full width wrapper -->
<div class="w-full px-1 m-0 py-3 sm:py-10">

    <!-- Inner card -->
    <div class="bg-white shadow-lg w-full sm:max-w-3xl sm:mx-auto p-4 sm:p-6 rounded-none sm:rounded-lg">
        <h1 class="text-2xl sm:text-3xl font-bold text-blue-700 mb-3">
            <?= htmlspecialchars($job['title']) ?>
        </h1>

        <p class="text-gray-700 mb-2"><strong>Company:</strong> <?= htmlspecialchars($job['company']) ?></p>
        <p class="text-gray-700 mb-2"><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
        <p class="text-gray-700 mb-2">
            <strong>Status:</strong> 
            <span class="<?= $job['status'] === 'closed' ? 'text-red-600' : 'text-green-600' ?>">
                <?= ucfirst($job['status']) ?>
            </span>
        </p>

        <hr class="my-4">

        <p class="text-gray-800 leading-relaxed mb-6">
            <?= nl2br(htmlspecialchars($job['description'])) ?>
        </p>

        <?php if ($job['status'] === 'open'): ?>
        <h2 class="text-lg sm:text-xl font-semibold text-blue-600 mb-3">
            Apply for this job
        </h2>

        <form method="POST" class="space-y-4 bg-gray-50 p-4 rounded-md shadow-inner w-full">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <input type="text" name="name" placeholder="Your Full Name" required
                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            <input type="email" name="email" placeholder="Your Email Address" required
                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            <textarea name="message" rows="4" placeholder="Write your message or cover letter..." required
                class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>

            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 w-full sm:w-auto">
                Submit Application
            </button>
        </form>
        <?php else: ?>
            <p class="text-red-600 font-semibold">
                This job is no longer accepting applications.
            </p>
        <?php endif; ?>
    </div>
</div>

</body>


</html>
