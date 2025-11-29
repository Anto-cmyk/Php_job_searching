<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

// Generate CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";


if (isset($_POST['register'])) {

  // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "<p class='text-red-600 font-semibold'>Invalid CSRF token. Please try again.</p>";
    } 
    // Sanitize input
    $name =  $_POST['name'];
    $email = $_POST['email'];
    $pass =  $_POST['password'];
    $terms = isset($_POST['terms']);

     

    if (!$terms) {
      $message = "<p class='text-red-600 font-semibold'>Please agree to the terms and conditions.</p>";
        
    }
   
    // check if email exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
      $message = "<p class='text-red-600 font-semibold'>Email already exists!</p>";
           
    }

    $hash = password_hash($pass, PASSWORD_BCRYPT);
    $code = rand(100000, 999999);
     
    $qry = "INSERT INTO users(name, email, password, verification_code) 
                                   VALUES(?,?,?,?)";
    $stmt = mysqli_prepare($conn,$qry);
    mysqli_stmt_bind_param($stmt,"ssss",$name, $email, $hash, $code);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    
    /* $insert = mysqli_query($conn, "INSERT INTO users(name, email, password, verification_code) 
                                   VALUES('$name', '$email', '$hash', '$code')";);

     if ($insert) {
        $subject = "Email Verification Code";
        $message = "Hi $name,\n\nYour verification code is: $code\n\nThank you for registering!";
        $headers = "From: no-reply@yourdomain.com";
        mail($email, $subject, $message, $headers); 

        echo "Verification code sent to your email!"; */
        header("Location: index.php"); 
        exit();
     if(!$insert) {
      $message = "<p class='text-yellow-600 font-semibold'>Registration failed.Please try again </p>";
     }
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
<link rel="stylesheet" href="../public/css/style.css">
<style>
  body {
    background-color:gray;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    height: 100vh;
  }
</style>
</head>
<body>

<!-- Navbar -->
  <nav class="bg-white shadow-md py-4 fixed w-full">
    <div class="container mx-auto flex justify-between items-center px-6">
      <div class="text-2xl font-bold text-blue-600">JobFinder</div>
      <div>
        <a href="Employer/login.php" class="text-purple-800 font-bold hover:text-blue-600 mr-1">Employer Account</a>
      </div>
    </div>
  </nav> <br> <br> <br>

  <div class="backdrop-blur-lg bg-purple-900/20 border border-purple-400/30 p-8 rounded-2xl shadow-xl sm:w-[500px] w-[98%] flex flex-col items-center">
    <h2 class="text-3xl font-semibold text-white mb-6">User Registration</h2>

              <?php echo $message; ?>


    <form method="POST" class="w-full flex flex-col space-y-4">

    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">


      <input type="text" name="name" placeholder="Full Name" required
        class="w-full px-4 py-2 rounded-lg bg-white/20 text-white placeholder-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-400 backdrop-blur-sm">

      <input type="email" name="email" placeholder="Email Address" required
        class="w-full px-4 py-2 rounded-lg bg-white/20 text-white placeholder-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-400 backdrop-blur-sm">

      <input type="password" name="password" placeholder="Password" required
        class="w-full px-4 py-2 rounded-lg bg-white/20 text-white placeholder-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-400 backdrop-blur-sm">

      <label class="text-white text-sm">
        <input type="checkbox" name="terms" class="mr-2"> I agree to 
        <a href="terms.php" target="_blank" class="text-blue-400 hover:underline">Terms & Conditions</a>
      </label>

      <button type="submit" name="register"
        class="w-full py-2 bg-purple-500 hover:bg-purple-600 text-white font-semibold rounded-lg transition duration-200">
        Register
      </button>
    </form>

    <p class="text-white mt-4 text-sm">
      Already have an account? <a href="../index.php" class="text-blue-400 hover:underline">Login</a>
    </p>
  </div>

</body>
</html>
