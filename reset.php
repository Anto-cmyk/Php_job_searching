<?php
include 'db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
}

if (isset($_POST['update'])) {
    $token = $_POST['token'];
    $newpass = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE reset_token='$token'");
    if (mysqli_num_rows($query) > 0) {
        mysqli_query($conn, "UPDATE users SET password='$newpass', reset_token=NULL WHERE reset_token='$token'");
        echo "Password updated successfully. <a href='login.php'>Login</a>";
    } else {
        echo "Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
</head>
<body>
<h2>Reset Password</h2>
<form method="POST">
  <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
  <input type="password" name="password" placeholder="New password" required><br><br>
  <button type="submit" name="update">Update Password</button>
</form>
</body>
</html>
