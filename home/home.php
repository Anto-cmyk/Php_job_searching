<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}


 
// Foreign key email from the users table

//Calling the file containing the database connection code
require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';
$user_email = $_SESSION["email"];
// Handle new post submission
if (isset($_POST['post_submit']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = mysqli_real_escape_string($conn, $_POST['post_content']);
    if (!empty($content)) {
        $insert_sql = "INSERT INTO posts (user_email, content) VALUES ('$user_email' , '$content')";
        mysqli_query($conn, $insert_sql);
        header("Location: mainPage.php"); // refresh page
        exit();
    }
}

// Fetch all posts (latest first)
$sql = "SELECT posts.content, posts.date_created, users.name, users.email
        FROM posts
        JOIN users ON posts.user_email = users.email
        ORDER BY posts.date_created DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home - JobFinder</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <div class="max-w-3xl mx-auto mt-10">

    <!-- Add Post Section -->
    <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-800 mb-3">Ready for your next opportunity?</h2>
      <form method="POST" action="home.php">
        <textarea name="post_content" class="w-full border border-gray-300 rounded p-2 mb-3" rows="3" placeholder="Write something..."></textarea>
        <button type="submit" name="post_submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
          Post
        </button>
      </form>
    </div>

    <!-- Posts Feed -->
    <div class="space-y-4">
      <?php
      if (mysqli_num_rows($result) > 0) {
 
        while ($row = mysqli_fetch_assoc($result)) {

          $email_ret = htmlspecialchars($row['email']);
          $content_ret = htmlspecialchars($row['content']);
          $name_ret = htmlspecialchars($row['name']);

              echo "
              <div class='bg-white shadow rounded-lg p-4'>
                <div class='flex items-center space-x-3 mb-2'>
                  <div class='w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center font-bold text-blue-700'>" . strtoupper(htmlspecialchars($row['name'][0])) . "</div>
                  <div>
                    <p class='font-semibold'>{$name_ret}</p>
                    <p class='text-xs text-gray-500'>{$email_ret} • " . date("F j, Y, g:i a", strtotime($row['date_created'])) . "</p>
                  </div>
                </div>
                <p class='text-gray-700'>{$content_ret}</p>
              </div>
              ";
          }
      } else {
          echo "<p class='text-gray-500'>No posts yet. Be the first to post!</p>";
      }

      mysqli_close($conn);
      ?>
    </div>

  </div>

</body>
</html>
