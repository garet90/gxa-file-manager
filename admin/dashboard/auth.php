<?php
	require '../config.php';
	$usercheck = false;
	$passcheck = false;
	$permissions = array();
	$hasPermission = true;
	if ($usemysql) {
		if (isset($_COOKIE['user']) && isset($_COOKIE['password'])) { } else {
			$errors = 'You are not logged in';
			header('Location: login.php?errors=' . $errors);
			die();
		}
		$sqlilink = mysqli_connect($mysqlip, $mysqluser, $mysqlpassword, $mysqldatabase);
		$user = mysqli_query($sqlilink,"SELECT * FROM `users` WHERE name='" . $_COOKIE['user'] . "';");
		if (mysqli_num_rows($user) == 1) {
			$usercheck = true;
		} else {
			$errors = 'Your username or password is incorrect';
			header('Location: login.php?errors=' . $errors);
			mysqli_close($sqlilink);
			die();
		} 
		while($row = mysqli_fetch_array($user, MYSQL_ASSOC)) {
			if (password_verify($_COOKIE['password'],$row['password'])) {
				$passcheck = true;
				$permissions = explode(',',$row['permissions']);
			} else {
				$errors = 'Your username or password is incorrect';
				header('Location: login.php?errors=' . $errors);
				mysqli_close($sqlilink);
				die();
			}
		}
		if (mysqli_connect_errno()) {
			$errors = 'There was an error communicating with the SQL database: ' . mysqli_error();
			header('Location: login.php?errors=' . $errors);
			mysqli_close($sqlilink);
			die();
		}
		mysqli_close($sqlilink);
	} else {
		if (isset($_COOKIE['user']) && isset($_COOKIE['password'])) {
			if ($_COOKIE['user'] == $adminname && $_COOKIE['password'] == $adminpassword) {
				$usercheck = true;
				$passcheck = true;
				$permissions = array('*');
			} else {
				$errors = 'Your username or password is incorrect';
				header('Location: login.php?errors=' . $errors);
				die();
			}
		} else {
			$errors = 'You are not logged in';
			header('Location: login.php?errors=' . $errors);
			die();
		}
	}
	if (isset($_GET['loc'])) {
		foreach ($permissions as $permission) {
			$psplit = explode(':',$permission);
			if ($psplit[0] == "DISALLOW") {
				if (strpos('../../' . $_GET['loc'], '../../' . $psplit[1]) !== false) {
					$hasPermission = false;
				}
			} else if ($psplit[0] == "ALLOW") {
				if (strpos('../../' . $_GET['loc'], '../../' . $psplit[1]) !== false) {
					$hasPermission = true;
				}
			}
		}
		if ($hasPermission) {
			foreach ($permissions as $permission) {
				$psplit = explode(':',$permission);
				if (isset($_GET['files'])) {
					$files = explode(",",$_GET['files']);
					foreach ($files as $file) {
						$filesplit = explode(":",$file);
						if ($psplit[0] == "DISALLOW") {
							if (strpos('../../' . $filesplit[1] . '/', '../../' . $psplit[1]) !== false) {
								$hasPermission = false;
							}
						}
					}
				}
				if (isset($_GET['file'])) {
					$filesplit = explode(":",$_GET['file']);
					if (count($filesplit) == 1) {
						$fileloc = $_GET['loc'] . $_GET['file'];
					} else {
						$fileloc = $filesplit[1];
					}
					if ($psplit[0] == "DISALLOW") {
						if (strpos('../../' . $fileloc . '/', '../../' . $psplit[1]) !== false) {
							$hasPermission = false;
						}
					}
				}
			}
		}
	}
?>