<?php
	require 'config.php';
	if ($_POST["user"] == $adminname && $_POST["password"] == $adminpassword) {
		echo "Login successful. Redirecting...";
		setcookie("user", $_POST["user"], 0, "/", $_SERVER['HTTP_HOST'], 1);
		setcookie("password", $_POST["password"], 0, "/", $_SERVER['HTTP_HOST'], 1);
		header('Location: dashboard/');
	} else {
		$errors = 'Your username or password is incorrect';
		header('Location: index.php?errors=' . $errors);
	}
?>