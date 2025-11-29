<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['email']) ){
    header("Location:../login.php");
    exit();
}
// Determine which page to show
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
 

include('header.php');



if($page == "home"){
    include("home.php");
}
elseif($page == "jobs"){
    include("components/jobs/main_jobsPage.php");
}
elseif($page == "notification"){
    include("components/notification/notification.php");
}
elseif($page == "menu"){
        include("components/menu/main_menu_page.php");
    }

else{
    include('home.php');
    
}



include('footer.php');
?>
