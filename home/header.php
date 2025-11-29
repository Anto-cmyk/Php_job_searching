<?php
  if (!isset($_SESSION)) {
      session_start();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    
    <link rel="stylesheet" href="public/css/style.css"> 
    
    
    <script src="https://cdn.tailwindcss.com"></script>  


</head>
<body>
    

<!-- HEADER -->

<header class="bg-purple-400 shadow-md top-0 z-50 fixed h-[100px] text-4xl w-full">
  <div class="flex  py-4 px-6 flex-col">
    
    <!-- Logo / Site Name -->
    <div class="flex items-center space-x-2 mb-1 ml-0 justify-between">
     
      <span class="font-bold text-gray-800 text-[19px]"> JobFinder</span>
      <h4 class="text-[18px]">
        <?php 
          if (isset($_SESSION['name'])) {
              echo "Welcome, " . htmlspecialchars($_SESSION['name']) . "!";
          } else {
              echo "Welcome, Guest!";
          }
          ?>
      </h4>
    </div>

    <!-- Navigation Menu -->
    <nav class="md:flex space-x-1 flex flex-row leading-[30px]">
                    <!-- Home Icon SVG -->
      <a href="mainPage.php?page=home" class="text-yellow-700 basis-[5%] mr-[31%] hover:text-blue-500 font-medium focus:bg-purple-950">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-9 text-gray-700 hover:text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.5L12 3l9 6.5V21a1 1 0 01-1 1h-6v-6H10v6H4a1 1 0 01-1-1V9.5z"/>
            </svg>
      </a>
        
        
      <div class="flex justify-between basis-[50%]">
                    <!-- Modern Jobs / Briefcase Icon -->

      <a href="mainPage.php?page=jobs" class="focus:bg-purple-950 text-gray-700 hover:text-blue-500 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9 text-gray-700 hover:text-blue-500 transition-colors duration-200" fill="none" viewBox="0 0 24            24"            stroke="currentColor">
             <rect x="3" y="7" width="18" height="14" rx="2" ry="2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
             <path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
           </svg>

      </a>
                       <!-- Notification / Bell Icon -->

      <a href="mainPage.php?page=notification" class="focus:bg-purple-950 text-gray-700 hover:text-blue-500">
                   <svg xmlns="http://www.w3.org/2000/svg" class="focus:bg-purple-950 h-9 w-9 text-gray-700 hover:text-blue-500 transition-colors duration-200" fill="none"                          viewBox="0 0 24 24" stroke="currentColor">
         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595            1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1"/>
        </svg>
      </a>
                   <!-- Modern menu btn -->

      <a href="mainPage.php?page=menu" class="focus:bg-purple-950 text-gray-700 hover:text-blue-500 font-medium">
          
                 <svg xmlns="http://www.w3.org/2000/svg" class="focus:bg-purple-950 h-9 w-9 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
  </svg>
          </a>
      </div>
       
    </nav>

     

    

  </div>

   
    
  </div>
</header>    <br><br><br><br><br>

</body>
</html>