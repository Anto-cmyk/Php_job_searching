<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';

function e($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'admin@example.com') {
    die("Access denied");
}

$cid = intval($_GET['cid'] ?? 0);
if ($cid <= 0) die("Invalid community ID");

$res = mysqli_query($conn, "
    SELECT m.id AS mid, m.user_email, c.name
    FROM community_members m
    JOIN communities c ON c.id = m.community_id
    WHERE c.id=$cid
");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_member'])) {
    $mid = intval($_POST['member_id']);
    mysqli_query($conn, "DELETE FROM community_members WHERE id=$mid");
    header("Location: members.php?cid=$cid");
    exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Members</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
<h1 class="text-xl font-bold mb-4">Members of Community: <?php echo e(mysqli_fetch_assoc(mysqli_query($conn,"SELECT name FROM communities WHERE id=$cid"))['name']); ?></h1>
<table class="w-full border-collapse">
<tr class="bg-gray-200"><th class="p-2 border">Email</th><th class="p-2 border">Action</th></tr>
<?php mysqli_data_seek($res,0); while($m=mysqli_fetch_assoc($res)): ?>
<tr class="border-b">
<td class="p-2 border"><?php echo e($m['user_email']); ?></td>
<td class="p-2 border">
<form method="post"><input type="hidden" name="member_id" value="<?php echo e($m['mid']); ?>"><button name="remove_member" class="px-2 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">Remove</button></form>
</td>
</tr>
<?php endwhile; ?>
</table>
<a href="community.php" class="block mt-4 text-blue-600 hover:underline">← Back to communities</a>
</div>
</body>
</html>
