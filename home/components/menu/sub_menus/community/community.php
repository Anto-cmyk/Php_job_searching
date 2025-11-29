<?php
if (!session_id()) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// 🔒 Require login
if (!isset($_SESSION['email'])) {
    header('Location: /login.php');
    exit;
}

$user = $_SESSION['email'];
$is_admin = ($user === 'admin@example.com'); // change this if needed
$msg = "";

// ---------------------------
// 📌 CREATE COMMUNITY
// ---------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $is_private = isset($_POST['is_private']) ? 1 : 0;

    if ($name !== '') {
        $sql = "INSERT INTO communities (name, description, owner_email, is_private) VALUES (?, ?, ?, ?)";
        $st = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($st, 'sssi', $name, $desc, $user, $is_private);
        mysqli_stmt_execute($st);
        $msg = "✅ Community created successfully.";
    }
}

// ---------------------------
// 👥 JOIN COMMUNITY
// ---------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join'])) {
    $cid = intval($_POST['community_id']);
    $check = "SELECT id FROM community_members WHERE community_id=? AND user_email=?";
    $st = mysqli_prepare($conn, $check);
    mysqli_stmt_bind_param($st, 'is', $cid, $user);
    mysqli_stmt_execute($st);
    $r = mysqli_stmt_get_result($st);

    if (mysqli_num_rows($r) === 0) {
        $ins = "INSERT INTO community_members (community_id, user_email) VALUES (?, ?)";
        $st2 = mysqli_prepare($conn, $ins);
        mysqli_stmt_bind_param($st2, 'is', $cid, $user);
        mysqli_stmt_execute($st2);
        $msg = "✅ Joined community.";
    }
}

// ---------------------------
// 🚪 LEAVE COMMUNITY
// ---------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave'])) {
    $cid = intval($_POST['community_id']);
    $del = "DELETE FROM community_members WHERE community_id=? AND user_email=?";
    $st = mysqli_prepare($conn, $del);
    mysqli_stmt_bind_param($st, 'is', $cid, $user);
    mysqli_stmt_execute($st);
    $msg = "❌ Left community.";
}

// ---------------------------
// 🗑️ ADMIN DELETE COMMUNITY
// ---------------------------
if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_community'])) {
    $cid = intval($_POST['community_id']);
    mysqli_query($conn, "DELETE FROM community_members WHERE community_id=$cid");
    mysqli_query($conn, "DELETE FROM communities WHERE id=$cid");
    $msg = "🗑️ Community deleted successfully.";
}

// ---------------------------
// --- SEARCH + PAGINATION ---
$search = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

// Base parameters (user visibility)
$params = [$user, $user];
$paramTypes = 'ss';
$searchCondition = "";

// ✅ Build search condition BEFORE building SQL
if ($search !== '') {
    $searchCondition = " AND (c.name LIKE ? OR c.description LIKE ?)";
    $searchLike = "%$search%";
    $params[] = $searchLike;
    $params[] = $searchLike;
    $paramTypes .= "ss";
}

// ---------------- COUNT QUERY ----------------
$sqlCount = "
    SELECT COUNT(*) AS total
    FROM communities c
    WHERE ((c.is_private=0) 
          OR (c.owner_email=?) 
          OR (EXISTS(SELECT 1 FROM community_members m2 WHERE m2.community_id=c.id AND m2.user_email=?)))
          $searchCondition
";

$stc = mysqli_prepare($conn, $sqlCount);
mysqli_stmt_bind_param($stc, $paramTypes, ...$params);
mysqli_stmt_execute($stc);
$countRes = mysqli_stmt_get_result($stc);
$total = mysqli_fetch_assoc($countRes)['total'];
$totalPages = max(1, ceil($total / $limit));

// ---------------- MAIN QUERY ----------------
$params = [$user, $user, $user];
$paramTypes = "sss";

if ($search !== '') {
    $params[] = "%$search%";
    $params[] = "%$search%";
    $paramTypes .= "ss";
    $searchCondition = " AND (c.name LIKE ? OR c.description LIKE ?)";
}

$sql = "
    SELECT c.*, EXISTS(
        SELECT 1 FROM community_members m WHERE m.community_id=c.id AND m.user_email=?
    ) AS is_member
    FROM communities c
    WHERE ((c.is_private=0) OR (c.owner_email=?) 
          OR (EXISTS(SELECT 1 FROM community_members m2 WHERE m2.community_id=c.id AND m2.user_email=?)))
          $searchCondition
    ORDER BY c.created_at DESC
    LIMIT ? OFFSET ?
";

$params[] = $limit;
$params[] = $offset;
$paramTypes .= "ii";

$st = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($st, $paramTypes, ...$params);
mysqli_stmt_execute($st);
$communities = mysqli_stmt_get_result($st);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Communities</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4">
<div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow-md">
  <h1 class="text-2xl font-bold text-blue-700 mb-4">🌍 Communities</h1>

  <?php if ($msg): ?>
    <div class="p-3 mb-4 rounded <?php echo str_contains($msg, '⚠️') ? 'bg-yellow-100 text-yellow-700' : (str_contains($msg, '🗑️') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-800'); ?>">
      <?= e($msg) ?>
    </div>
  <?php endif; ?>

  <!-- 🔍 Search -->
  <form method="get" class="mb-6 flex gap-2">
    <input type="text" name="search" value="<?= e($search) ?>" placeholder="Search communities..." class="flex-grow border p-2 rounded focus:ring focus:ring-blue-200">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Search</button>
  </form>

  <!-- ➕ Create -->
  <div class="bg-gray-50 border p-4 rounded mb-6">
    <h2 class="font-semibold text-gray-700 mb-2">Create Community</h2>
    <form method="post">
      <input type="text" name="name" placeholder="Community name" class="w-full p-2 border rounded mb-2 focus:ring focus:ring-blue-200" required>
      <textarea name="description" placeholder="Short description" class="w-full p-2 border rounded mb-2"></textarea>
      <label class="inline-flex items-center"><input type="checkbox" name="is_private" class="mr-2"> Private</label>
      <div class="mt-2"><button name="create" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create</button></div>
    </form>
  </div>

  <!-- 📋 Communities -->
  <?php if (mysqli_num_rows($communities) > 0): ?>
    <div class="space-y-4">
      <?php while ($c = mysqli_fetch_assoc($communities)): ?>
        <div class="bg-gray-50 border rounded-lg p-4 shadow-sm">
          <div class="flex justify-between items-center">
            <div>
              <h3 class="font-semibold text-lg"><?= e($c['name']) ?> <?php if($c['is_private']) echo '<span class="text-xs text-gray-500">(Private)</span>'; ?></h3>
              <p class="text-sm text-gray-600 mt-1"><?= e($c['description']) ?></p>
            </div>
            <div class="flex gap-2">
              <?php if ($c['is_member']): ?>
                <form method="post"><input type="hidden" name="community_id" value="<?= e($c['id']) ?>"><button name="leave" class="px-3 py-1 border rounded text-sm hover:bg-gray-100">Leave</button></form>
              <?php else: ?>
                <form method="post"><input type="hidden" name="community_id" value="<?= e($c['id']) ?>"><button name="join" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Join</button></form>
              <?php endif; ?>

              <?php if ($is_admin): ?>
                <form method="post" onsubmit="return confirm('Delete this community?')">
                  <input type="hidden" name="community_id" value="<?= e($c['id']) ?>">
                  <button name="delete_community" class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">Delete</button>
                </form>
                <a href="members.php?cid=<?= e($c['id']) ?>" class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">Members</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p class="text-gray-600 italic text-center mt-6">No communities found.</p>
  <?php endif; ?>

  <!-- 📄 Pagination -->
  <?php if ($totalPages > 1): ?>
    <div class="flex justify-center mt-6 gap-2">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
           class="px-3 py-1 border rounded <?= $i === $page ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-100' ?>">
           <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>
</body>
</html>




 