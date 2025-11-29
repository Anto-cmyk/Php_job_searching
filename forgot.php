<?php
include 'db.php';

if (isset($_POST['reset'])) {
    $email = trim($_POST['email']);
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($query) > 0) {
        $token = bin2hex(random_bytes(16));
        mysqli_query($conn, "UPDATE users SET reset_token='$token' WHERE email='$email'");

        $reset_link = "http://localhost/auth/reset.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the link to reset your password: $reset_link";
        $headers = "From: no-reply@yourdomain.com";
        mail($email, $subject, $message, $headers);

        echo "Password reset link sent to your email.";
    } else {
        echo "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
</head>
<body>
<h2>Forgot Password</h2>
<form method="POST">
  <input type="email" name="email" placeholder="Enter your email" required><br><br>
  <button type="submit" name="reset">Send Reset Link</button>
</form>
</body>
</html>
