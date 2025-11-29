<?php
if (!isset($_SESSION)) {
    session_start();
}


// Get page from URL
$page = isset($_GET['page']) ? $_GET['page'] : 'main_menu';
 

    if ($page == 'main_menu') {
        include 'main_menu.php';
    } 
    elseif ($page == 'profile') {
        include 'sub_menus/profile/profile.php';
    } 
    elseif ($page == 'about_us') {
        include 'sub_menus/about_us/about_us.php';
    } 
    elseif ($page == 'community') {
        include 'sub_menus/community/community.php';
    } 
    elseif ($page == 'fraud') {
        include 'sub_menus/report_fraud.php';
    }
    elseif ($page == 'help') {
        include 'sub_menus/help_center.php';
    } 
    elseif ($page == 'logout') {
        include 'sub_menus/logout.php';
    }
    elseif ($page == 'resume') {
        include 'pages/settings.php';
    }
    else {
        include 'main_menu.php';
    }

    ?>
 
