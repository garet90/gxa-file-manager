<?php
	require '../config.php';
	if ($_COOKIE['user'] == $adminname && $_COOKIE['password'] == $adminpassword) {
		
	} else {
		echo "<script type='text/javascript'>top.location.href='../';</script>";
		exit(0);
	}
?>