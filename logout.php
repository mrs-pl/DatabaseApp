<?php

session_start();

//check if user is logged in, if so ends the session and logs out
if(isset($_SESSION['username'])){
	session_destroy();
	
	$_SESSION = array();
	
}

//redirect to the home page
    echo "<script> window.location.assign('login.php'); </script>";

?>