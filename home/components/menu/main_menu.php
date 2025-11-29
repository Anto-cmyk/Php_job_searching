<?php
if (!isset($_SESSION)) {
    session_start();
}

/*  <?php endif; ?>  

//<?php if(isset($_SESSION["name"])): ?>
   <img src="data:image/*;base64,//<?php echo base64_encode($user['profile_picture']); ?>" class="w-14 h-14 rounded-full border-2 border-blue-500" alt="Profile">
   <?php else: ?> */

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JobFinder Menu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 text-gray-800">

  
     <section class="w-full bg-white shadow-lg flex flex-col">
      <!-- Profile Section -->
<a href="/home/components/menu/main_menu_page.php?page=profile" class="text-gray-500 hover:text-gray-700">
 <div class="p-5 border-b border-gray-200 flex items-center justify-between bg-gray-500">
     <div class="flex items-center space-x-4">
    
    <div class='w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center font-bold text-blue-700'> <?= strtoupper(htmlspecialchars($_SESSION['name'][0])) ?></div>

        <div>
            <h2 class="font-semibold text-gray-800">
                <?php echo htmlspecialchars($_SESSION["name"]); ?>
            </h2>
            <p class="text-sm text-green-500">job seeker</p>
        </div>
    </div>

    <!-- Dropdown Icon -->
         <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 transform transition-transform duration-200 hover:rotate-180 flex justify-self-center text-red-800 " fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    
</div>
   </a>



      <!-- Scrollable Menu -->
      <div class="flex-1 overflow-y-auto">
        <ul class="p-4 space-y-6 text-sm">

          

          <!-- PROFILE -->
          <li>
            <h3 class="text-gray-500 uppercase font-semibold text-xs mb-2">Profile</h3>
            <ul class="space-y-2">
              <li><a href="./components/menu/main_menu_page.php?page=profile" class="flex items-center gap-2 hover:text-blue-600"><i data-lucide="user"></i> My Profile</a></li>
              <li><a href="./components/menu/main_menu_page.php?page=profile" class="flex items-center gap-2 hover:text-blue-600"><i data-lucide="edit"></i> Edit Profile</a></li>
              <li><a href="./components/menu/main_menu_page.php?page=profile" class="flex items-center gap-2 hover:text-blue-600"><i data-lucide="upload"></i> Upload Resume</a></li>
               <li><a href="/home/components/menu/main_menu_page.php?page=logout" class="flex items-center gap-2 text-red-600 hover:text-red-700"><i data-lucide="log-out"></i> Logout</a></li>
            </ul>
          </li>

          

          <!-- COMMUNITY -->
          <li>
            <h3 class="text-gray-500 uppercase font-semibold text-xs mb-2">Community</h3>
            <ul class="space-y-2">
               <li><a href="/home/components/menu/main_menu_page.php?page=community" class="flex items-center gap-2 hover:text-blue-600"><i data-lucide="users"></i> Communities</a></li>
              <li><a href="/home/components/menu/main_menu_page.php?page=about_us" class="flex items-center gap-2 hover:text-blue-600"><i data-lucide="bell-ring"></i> About Us</a></li>
            </ul>
          </li>

          <!-- RESOURCES -->
          <li>
            <h3 class="text-gray-500 uppercase font-semibold text-xs mb-2">Tips</h3>
            <ul class="space-y-2">
               <li><a href="/home/components/menu/main_menu_page.php?page=fraud" class="flex items-center gap-2 hover:text-blue-600"><i data-lucide="book-open"></i> Report fraud</a></li>
              <li><a href="/home/components/menu/main_menu_page.php?page=help" class="flex items-center gap-2 hover:text-blue-600"><i data-lucide="help-circle"></i> Help Center</a></li>
            </ul>
          </li>

        </ul>
      </div>
</section>

     

 
  <script>
    lucide.createIcons();
  </script>

</body>
</html>
