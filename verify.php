<?php
include 'db.php';

if (isset($_POST['verify'])) {
    $email = trim($_POST['email']);
    $code = trim($_POST['code']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND verification_code='$code'");
    if (mysqli_num_rows($query) > 0) {
        mysqli_query($conn, "UPDATE users SET is_verified=1 WHERE email='$email'");
        echo "Email verified! You can now <a href='login.php'>login</a>.";
    } else {
        echo "Invalid code or email!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Email Verification</title>
</head>
<body>
<h2>Verify Your Email</h2>
<form method="POST">
  <input type="email" name="email" placeholder="Enter your email" required><br><br>
  <input type="text" name="code" placeholder="Enter verification code" required><br><br>
  <button type="submit" name="verify">Verify</button>
</form>
</body>
</html>
