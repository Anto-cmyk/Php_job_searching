<?php
if (!isset($_SESSION)) {
    session_start();
}
include 'db.php';

// Generate CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";


if (isset($_POST['login'])) {
// Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = "<p class='text-red-600 font-semibold'>Invalid CSRF token. Please try again.</p>";
    } 

    // Sanitize input
        $email =  trim($_POST['email']);
        $pass =  $_POST['password'];


    /* $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($query) == 1) {
        $user = mysqli_fetch_assoc($query);



        if ($user['is_verified'] == 0) {
            echo "Please verify your email first.";
            exit;
        }  

        if (password_verify($pass, $user['password'])) {
            
             $_SESSION['name'] = htmlspecialchars($user['name']);
             $_SESSION['email'] = htmlspecialchars($user['email']);

            header("Location: home/mainPage.php");
            exit(); */

     // Prepare the SQL statement
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);

// Execute
$stmt->execute();

// Get result
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($pass, $user['password']))
       {

        // Store session data securely
        $_SESSION['name'] = htmlspecialchars($user['name']);
        $_SESSION['email'] = htmlspecialchars($user['email']);
        

        header("Location: home/mainPage.php");
        exit();
     

// Close statement and connection


 

        
       } 
        else {
                    $message = "<p class='text-red-600 font-semibold'>Incorrect password.</p>";

               } }

    else {
               $message = "<p class='text-red-600 font-semibold'>Email not registered.</p>";

    } 
     $stmt->close();
     $conn->close();
  
  } 
  







  


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Form</title>
<link rel="stylesheet" href="../public/css/style.css">

  <style>
  body {
    background-color:black;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    height: 100vh;
    align-items:center;
    
    
  }
</style>
</head>
<body >

<!-- Navbar -->
  <nav class="bg-white shadow-md py-4 fixed w-full">
    <div class="container mx-auto flex justify-between items-center px-6">
      <div class="text-2xl font-bold text-blue-600">JobFinder</div>
      <div>
        <a href="Employer/login.php" class="text-purple-800 font-bold hover:text-blue-600 mr-1">Employer Account</a>
      </div>
    </div>
  </nav> <br> <br> <br>

  <div class="backdrop-blur-lg bg-white/10 p-8 rounded-2xl shadow-4xl w-full max-w-sm border border-white/20 items-center text-white">
          <h2 class="text-2xl font-semibold text-purple text-center mb-6">User login</h2>

           <?php echo $message; ?>


    <form  method="POST" class="space-y-5" >
      <!-- Email -->
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

      <div>
        <label for="email" class="block text-sm font-medium text-black mb-1">Email</label>
        <input type="email" id="email" name="email" required
          class="w-full px-4 py-2 bg-black/20 text-black placeholder-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 backdrop-blur-lg">
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-black mb-1">Password</label>
        <input type="password" id="password" name="password" required
          class="w-full px-4 py-2 bg-white/20 text-black placeholder-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 backdrop-blur-sm">
      </div>

      <!-- Login Button -->
      <button type="submit" id="login" name="login"
        class="w-full py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition duration-200">
        Login
      </button>

      <!-- Extra Links -->
      <p class="text-center text-sm text-blue-500 mt-4">
        Don’t have an account?
        <a href="register.php" class="text-red-900 hover:underline">Sign up</a>
      </p>
    </form>
  </div>

</body>
</html>
