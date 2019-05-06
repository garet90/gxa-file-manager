<?php
	require 'config.php';
	if ($usemysql) {
		$sqlilink = mysqli_connect($mysqlip, $mysqluser, $mysqlpassword, $mysqldatabase);
		$tablecheck = mysqli_query($sqlilink,"SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'settings'");
		if (mysqli_num_rows($tablecheck) == 0) {
			mysqli_query($sqlilink,"CREATE TABLE `gxa-panel`.`settings` ( `ID` INT NOT NULL AUTO_INCREMENT , `name` TEXT NOT NULL , `value` TEXT NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB; ");
			mysqli_query($sqlilink,"INSERT INTO `settings` (`ID`, `name`, `value`) VALUES (NULL, 'username', '" . $_POST["user"] . "'); ");
			mysqli_query($sqlilink,"INSERT INTO `settings` (`ID`, `name`, `value`) VALUES (NULL, 'password', '" . password_hash($_POST["password"], PASSWORD_DEFAULT) . "'); ");
		}
		$usernamecheck = mysqli_query($sqlilink,"SELECT value FROM `settings` WHERE name='username';");
		while($row = mysqli_fetch_array($usernamecheck, MYSQL_ASSOC)) {
			if ($row['value'] !== $_POST['user']) {
				$errors = 'Your username or password is incorrect';
				header('Location: index.php?errors=' . $errors);
				mysqli_close($sqlilink);
				die();
			}
		}
		$passwordcheck = mysqli_query($sqlilink,"SELECT value FROM `settings` WHERE name='password';");
		while($row = mysqli_fetch_array($passwordcheck, MYSQL_ASSOC)) {
			if (password_verify($_POST['password'],$row['value'])) { } else {
				$errors = 'Your username or password is incorrect';
				header('Location: index.php?errors=' . $errors);
				mysqli_close($sqlilink);
				die();
			}
		}
		if (mysqli_connect_errno()) {
			$errors = 'There was an error communicating with the SQL database: ' . mysqli_error();
			header('Location: index.php?errors=' . $errors);
			mysqli_close($sqlilink);
			die();
		}
		echo "Login successful. Redirecting...";
		setcookie("user", $_POST["user"], 0, "/", $_SERVER['HTTP_HOST'], 1);
		setcookie("password", $_POST["password"], 0, "/", $_SERVER['HTTP_HOST'], 1);
	   	header('Location: dashboard/');
		mysqli_close($sqlilink);
		die();
	} else {
		if ($_POST["user"] == $adminname && $_POST["password"] == $adminpassword) {
			echo "Login successful. Redirecting...";
			setcookie("user", $_POST["user"], 0, "/", $_SERVER['HTTP_HOST'], 1);
			setcookie("password", $_POST["password"], 0, "/", $_SERVER['HTTP_HOST'], 1);
			header('Location: dashboard/');
			die();
		} else {
			$errors = 'Your username or password is incorrect';
			header('Location: index.php?errors=' . $errors);
			die();
		}
	}
?>