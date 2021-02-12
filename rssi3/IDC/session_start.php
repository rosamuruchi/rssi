<?php

session_start();
if(!isset($_SESSION['username'])){
$user = $_GET['username'];
$_SESSION['username'] = $user;
$list_of_users = array('david.penott@tecoar.com.ar', 'gustavo.alvarez@tecoar.com.ar', 'miguel.telecemian@tecoar.com.ar');
	if (in_array($user, $list_of_users)) {
		header('location:welcome.php');
	} else {
		header('location:login.php');
	}
}else{
  header('location:welcome.php');
}