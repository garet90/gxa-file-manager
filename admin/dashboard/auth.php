<?php
	require '../config.php';
	$usercheck = false;
	$passcheck = false;
	if ($usemysql) {
		if (isset($_COOKIE['user']) && isset($_COOKIE['password'])) { } else {
			$errors = 'You are not logged in (cookies not set)';
			header('Location: ../index.php?errors=' . $errors);
			die();
		}
		$sqlilink = mysqli_connect($mysqlip, $mysqluser, $mysqlpassword, $mysqldatabase);
		$usernamecheck = mysqli_query($sqlilink,"SELECT value FROM `settings` WHERE name='username';");
		while($row = mysqli_fetch_array($usernamecheck, MYSQL_ASSOC)) {
			if ($row['value'] == $_COOKIE['user']) {
				$usercheck = true;
			} else {
				$errors = 'Your username or password is incorrect';
				header('Location: ../index.php?errors=' . $errors);
				mysqli_close($sqlilink);
				die();
			}
		}
		$passwordcheck = mysqli_query($sqlilink,"SELECT value FROM `settings` WHERE name='password';");
		while($row = mysqli_fetch_array($passwordcheck, MYSQL_ASSOC)) {
			if (password_verify($_COOKIE['password'],$row['value'])) {
				$passcheck = true;
			} else {
				$errors = 'Your username or password is incorrect';
				header('Location: ../index.php?errors=' . $errors);
				mysqli_close($sqlilink);
				die();
			}
		}
		if (mysqli_connect_errno()) {
			$errors = 'There was an error communicating with the SQL database: ' . mysqli_error();
			header('Location: ../index.php?errors=' . $errors);
			mysqli_close($sqlilink);
			die();
		}
		mysqli_close($sqlilink);
	} else {
		if (isset($_COOKIE['user']) && isset($_COOKIE['password'])) {
			if ($_COOKIE['user'] == $adminname && $_COOKIE['password'] == $adminpassword) {
				$usercheck = true;
				$passcheck = true;
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
	}
?>