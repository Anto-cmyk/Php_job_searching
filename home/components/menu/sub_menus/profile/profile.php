<?php
if (!isset($_SESSION)) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';

// CSRF Token
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Ensure logged in
if (!isset($_SESSION['email'])) {
    header('Location: ../../../login.php'); exit();
}

$email = $_SESSION['email'];
$message = '';

// Fetch user data
$stmt = $conn->prepare("SELECT u.name,u.email,f.profile_picture, f.resume 
                        FROM users u 
                        LEFT JOIN files f ON u.email=f.user_email 
                        WHERE u.email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch user description
$stmt = $conn->prepare("SELECT bio FROM user_bio WHERE user_email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($bio);
$stmt->fetch();
$stmt->close();

// Fetch skills
$stmt = $conn->prepare("SELECT skill FROM user_skills WHERE user_email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($skill);
$stmt->fetch();
$stmt->close();

// Fetch education
$stmt = $conn->prepare("SELECT school_name,degree,start_year,end_year FROM user_schools WHERE user_email=?");
$stmt->bind_param("s",$email);
$stmt->execute();
$stmt->bind_result($school_name,$degree,$start_year,$end_year);
$stmt->fetch();
$stmt->close();

// Fetch user's posts
$stmt = $conn->prepare("SELECT id, content, date_created FROM posts WHERE user_email=? ORDER BY date_created DESC");
$stmt->bind_param("s", $email);
$stmt->execute();
$posts_result = $stmt->get_result();
$posts = $posts_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF validation failed!");

    // Update Description
    if (isset($_POST['update_intro'])) {
        $intro = trim($_POST['introduction']);
        $stmt = $conn->prepare("INSERT INTO user_bio(user_email,bio) VALUES (?,?) 
                                ON DUPLICATE KEY UPDATE bio=VALUES(bio)");
        $stmt->bind_param("ss", $email,$intro);
        $stmt->execute();
        $stmt->close();
        $bio = $intro;
        $message = "Description updated!";
    }

    // Update Skills
    if (isset($_POST['update_skills'])) {
        $skills = trim($_POST['skills']);
        $stmt = $conn->prepare("INSERT INTO user_skills(user_email,skill) VALUES (?,?) 
                                ON DUPLICATE KEY UPDATE skill=VALUES(skill)");
        $stmt->bind_param("ss", $email,$skills);
        $stmt->execute();
        $stmt->close();
        $skill = $skills;
        $message = "Skills updated!";
    }

    // Update Education
    if (isset($_POST['update_education'])) {
        $education = trim($_POST['education']);
        $stmt = $conn->prepare("INSERT INTO user_schools(user_email,school_name) VALUES (?,?) 
                                ON DUPLICATE KEY UPDATE school_name=VALUES(school_name)");
        $stmt->bind_param("ss", $email,$education);
        $stmt->execute();
        $stmt->close();
        $school_name = $education;
        $message = "Education updated!";
    }

    // Upload Profile Picture
    if (isset($_POST['upload_picture']) && isset($_FILES['profile_picture'])) {
        if ($_FILES['profile_picture']['error'] === 0) {
            $file_data = file_get_contents($_FILES['profile_picture']['tmp_name']);
            $stmt = $conn->prepare("INSERT INTO files(user_email,profile_picture) VALUES(?,?) 
                                    ON DUPLICATE KEY UPDATE profile_picture=VALUES(profile_picture)");
            $stmt->bind_param("sb", $email, $null);
            $stmt->send_long_data(1, $file_data);
            $stmt->execute();
            $stmt->close();
            $_SESSION['profile_picture'] = $file_data;
            $message = "Profile picture updated!";
        }
    }

    // Upload Resume
    if (isset($_POST['upload_resume']) && isset($_FILES['resume'])) {
        if ($_FILES['resume']['error'] === 0) {
            $file_data = file_get_contents($_FILES['resume']['tmp_name']);
            $stmt = $conn->prepare("INSERT INTO files(user_email,resume) VALUES(?,?) 
                                    ON DUPLICATE KEY UPDATE resume=VALUES(resume)");
            $stmt->bind_param("sb", $email, $null);
            $stmt->send_long_data(1, $file_data);
            $stmt->execute();
            $stmt->close();
            $message = "Resume updated!";
        }
    }

    // Delete Post
    if (isset($_POST['delete_post'])) {
        $post_id = (int)$_POST['post_id'];
        $stmt = $conn->prepare("DELETE FROM posts WHERE id=? AND user_email=?");
        $stmt->bind_param("is", $post_id, $email);
        $stmt->execute();
        $stmt->close();
        $message = "Post deleted!";
        // Remove from posts array immediately
        foreach($posts as $key=>$p) if($p['id']==$post_id) unset($posts[$key]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
    // Auto hide message after 5 seconds
    function hideMessage() {
        const msg = document.getElementById('flash-message');
        if(msg) setTimeout(()=>{msg.style.display='none'},5000);
    }
    window.onload = hideMessage;
</script>
</head>
<body class="bg-gray-100 text-gray-800">

<!-- Fixed header -->
<div class="fixed top-0 left-0 right-0 bg-white shadow p-6 flex items-center space-x-6 z-50">
    <div class="relative w-16 h-16">
        <?php if ($_SESSION['profile_picture']??false): ?>
            <img src="data:image/*;base64,<?= base64_encode($_SESSION['profile_picture']); ?>" class="w-full h-full rounded-full object-cover border shadow-sm">
        <?php else: ?>
            <div class='w-full h-full bg-blue-200 rounded-full flex items-center justify-center font-bold text-blue-700 text-2xl'>
                <?= strtoupper(htmlspecialchars($user['name'][0]??'')) ?>
            </div>
        <?php endif; ?>
        <!-- Camera Overlay -->
        <form method="POST" enctype="multipart/form-data" class="absolute bottom-0 right-0">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <label for="profile_picture" class="cursor-pointer bg-white border border-gray-300 p-2 rounded-full shadow hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h2l1-2h12l1 2h2a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1z"/>
                    <circle cx="12" cy="13" r="3"/>
                </svg>
            </label>
            <input type="file" id="profile_picture" name="profile_picture" class="hidden" onchange="this.form.submit()">
            <input type="hidden" name="upload_picture" value="1">
        </form>
    </div>
    <div>
        <h2 class="text-xl font-semibold"><?= htmlspecialchars($user['name']??'') ?></h2>
        <p class="text-gray-600"><?= htmlspecialchars($user['email']??'') ?></p>
    </div>
</div>

<!-- Relative content -->
<div class="mt-36 max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-xl relative">

<?php if($message): ?>
<p id="flash-message" class="bg-green-100 text-green-700 p-3 rounded mb-3 transition-all duration-500"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<!-- Description -->
<form method="POST" class="mb-4">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
    <label class="block font-medium">Description</label>
    <textarea name="introduction" rows="3" class="w-full border p-2 rounded"><?= htmlspecialchars($bio??'') ?></textarea>
    <button name="update_intro" class="bg-indigo-600 text-white px-4 py-2 rounded mt-2 hover:bg-indigo-700">Update Description</button>
</form>

<!-- Skills -->
<form method="POST" class="mb-4">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
    <label class="block font-medium">Skills (comma separated)</label>
    <input type="text" name="skills" value="<?= htmlspecialchars($skill??'') ?>" class="w-full border p-2 rounded">
    <button name="update_skills" class="bg-green-600 text-white px-4 py-2 rounded mt-2 hover:bg-green-700">Update Skills</button>
</form>

<!-- Education -->
<form method="POST" class="mb-4">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
    <label class="block font-medium">Level of Education</label>
    <input type="text" name="education" value="<?= htmlspecialchars($school_name??'') ?>" class="w-full border p-2 rounded">
    <button name="update_education" class="bg-purple-600 text-white px-4 py-2 rounded mt-2 hover:bg-purple-700">Update Education</button>
</form>

<!-- Resume -->
<form method="POST" enctype="multipart/form-data" class="mb-4">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
    <label class="block font-medium">Resume</label>
    <input type="file" name="resume" class="border p-1 mb-2">
    <button name="upload_resume" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">Upload Resume</button>
</form>

<!-- Posts -->
<h3 class="text-xl font-semibold mt-6 mb-3">My Posts</h3>
<?php if(count($posts) > 0): ?>
<div class="space-y-4">
<?php foreach($posts as $post): ?>
<div class="border p-3 rounded bg-gray-50 flex justify-between items-start">
    <div>
        <p><?= htmlspecialchars($post['content']); ?></p>
        <small class="text-gray-500"><?= $post['date_created']; ?></small>
    </div>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
        <button name="delete_post" class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">Delete</button>
    </form>
</div>
<?php endforeach; ?>
</div>
<?php else: ?>
<p class="text-gray-500">No posts yet.</p>
<?php endif; ?>

</div>
</body>
</html>
