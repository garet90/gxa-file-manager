<?php
	setcookie("user", "", 0, "/", $_SERVER['HTTP_HOST'], 1);
	setcookie("password", "", 0, "/", $_SERVER['HTTP_HOST'], 1);
	header('Location: ../dashboard/');
?>