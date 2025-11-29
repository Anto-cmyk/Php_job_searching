<?php
//session_start();

 require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';
// Assume the user is logged in
$user_email = $_SESSION['email'];

// --- Mark Notification as Read ---
if (isset($_GET['read_id'])) {
    $notif_id = intval($_GET['read_id']);
    $update_sql = "UPDATE notifications 
                   SET status='read' 
                   WHERE id='$notif_id' AND user_email='$user_email'";
    mysqli_query($conn, $update_sql);
    header("Location: notifications.php");
    exit();
}



// --- Fetch Notifications ---
$sql = "SELECT id, title, message, date_created, status 
        FROM notifications 
        WHERE user_email='$user_email' 
        ORDER BY date_created DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <div class="max-w-3xl mx-auto mt-10 bg-white shadow-lg rounded-lg p-6">
    <h1 class="text-2xl font-bold text-blue-700 mb-4">🔔 Notifications</h1>

    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $notif_id = $row['id'];
            $is_unread = ($row['status'] === 'unread');
            $border_color = $is_unread ? 'border-blue-400 bg-blue-50' : 'border-gray-300 bg-gray-50';
            $text_bold = $is_unread ? 'font-semibold' : 'font-normal';

            echo "
            <div class='border-l-4 $border_color p-4 mb-3 rounded'>
              <h2 class='$text_bold text-gray-900'>{$row['title']}</h2>
              <p class='text-gray-600 text-sm mt-1'>{$row['message']}</p>
              <p class='text-xs text-gray-500 mt-2'>" . date('F j, Y, g:i a', strtotime($row['date_created'])) . "</p>";

            if ($is_unread) {
                echo "<a href='?read_id=$notif_id' class='inline-block mt-2 text-sm text-white bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded'>
                        Mark as Read
                      </a>";
            } else {
                echo "<span class='inline-block mt-2 text-xs text-green-700 bg-green-100 px-2 py-1 rounded'>Read</span>";
            }

            echo "</div>";
        }
    } else {
        echo "<p class='text-gray-500'>No notifications found.</p>";
    }

    mysqli_close($conn);
    ?>
  </div>

</body>
</html>
