<?php
	require '../config.php';
	if (isset($_COOKIE['user']) && isset($_COOKIE['password'])) {
		if ($_COOKIE['user'] == $adminname && $_COOKIE['password'] == $adminpassword) {
			
		} else {
			$errors = 'Your username or password is incorrect';
			header('Location: ../index.php?errors=' . $errors);
			die();
		}
	} else {
		$errors = 'You are not logged in (cookies not set)';
		header('Location: ../index.php?errors=' . $errors);
		die();
	}
?>