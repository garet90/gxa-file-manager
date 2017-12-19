<?php
	require 'config.php';
	if ($_POST["user"] == $adminname && $_POST["password"] == $adminpassword) {
		echo "Login successful. Redirecting...";
		setcookie("user", $_POST["user"], 0, "/", "admin.justintuckerlaw.com", 1);
		setcookie("password", $_POST["password"], 0, "/", "admin.justintuckerlaw.com", 1);
		header('Location: dashboard/');
	} else {
		echo "Your username or password is incorrect";
	}
?>