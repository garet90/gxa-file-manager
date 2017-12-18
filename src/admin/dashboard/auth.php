<?php
	require '../config.php';
	if ($_COOKIE['user'] == $adminname && $_COOKIE['password'] == $adminpassword) {
		
	} else {
		header('Location: ../');
	}
?>