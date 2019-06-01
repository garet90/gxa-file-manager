<?php
	require '../config.php';
	function copy_directory($src,$dst) {
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					copy_directory($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}
	if (isset($_POST['user']) && isset($_POST['password'])) {
		if ($usemysql) {
			$sqlilink = mysqli_connect($mysqlip, $mysqluser, $mysqlpassword, $mysqldatabase);
			$tablecheck = mysqli_query($sqlilink,"SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = N'settings'");
			if (mysqli_num_rows($tablecheck) == 0) {
				mysqli_query($sqlilink,"CREATE TABLE `gxa-panel`.`settings` ( `ID` INT NOT NULL AUTO_INCREMENT , `name` TEXT NOT NULL , `value` TEXT NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB; ");
				mysqli_query($sqlilink,"CREATE TABLE `gxa-panel`.`users` ( `ID` INT NOT NULL AUTO_INCREMENT , `name` TEXT NOT NULL , `password` TEXT NOT NULL , `permissions` TEXT NOT NULL , PRIMARY KEY (`ID`)) ENGINE = InnoDB; ");
				mysqli_query($sqlilink,"INSERT INTO `users` (`ID`, `name`, `password`, `permissions`) VALUES (NULL, '" . $_POST["user"] . "', '" . password_hash($_POST["password"], PASSWORD_DEFAULT) . "', '*'); ");
				copy_directory("../../admin/users/default/","../../admin/users/" . $_POST['user'] ."/");
			}
			$user = mysqli_query($sqlilink,"SELECT * FROM `users` WHERE name='" . $_POST['user'] . "';");
			if (mysqli_num_rows($user) != 1) {
				$errors = 'Your username or password is incorrect';
				header('Location: login.php?errors=' . $errors);
				mysqli_close($sqlilink);
				die();
			} 
			while($row = mysqli_fetch_array($user, MYSQL_ASSOC)) {
				if (password_verify($_POST['password'],$row['password'])) { } else {
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
			echo "Login successful. Redirecting...";
			setcookie("user", $_POST["user"], 0, "/", $_SERVER['HTTP_HOST'], 1);
			setcookie("password", $_POST["password"], 0, "/", $_SERVER['HTTP_HOST'], 1);
		   	echo '<script>top.loggedIn(); window.frameElement.parentElement.parentElement.remove();</script>';
			mysqli_close($sqlilink);
			die();
		} else {
			if ($_POST["user"] == $adminname && $_POST["password"] == $adminpassword) {
				echo "Login successful. Redirecting...";
				if (!file_exists('../../admin/users/' . $adminname . '/')) {
					copy_directory("../../admin/users/default/","../../admin/users/" . $adminname ."/");
				}
				setcookie("user", $_POST["user"], 0, "/", $_SERVER['HTTP_HOST'], 1);
				setcookie("password", $_POST["password"], 0, "/", $_SERVER['HTTP_HOST'], 1);
		   		echo '<script>top.loggedIn(); window.frameElement.parentElement.parentElement.remove();</script>';
				die();
			} else {
				$errors = 'Your username or password is incorrect';
				header('Location: login.php?errors=' . $errors);
				die();
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><div class="windowicon"><i class="fa fa-user-o" aria-hidden="true"></i></div>Login Portal</title>
		<meta charset="UTF-8">
		<style>
			body, html {
				font-family: Sans-serif;
				font-size: 10pt;
			}
			input {
				margin-bottom: 10px;
			}
			.errors {
				color: red;
				margin: 0 0 10px 0;
			}
		</style>
	</head>
	<body>
		<form action="login.php" method="post">
			<label for="username">Username</label><br />
			<input type="text" name="user" id="username" autocomplete="username" /><br />
			<label for="password">Password</label><br />
			<input type="password" name="password" id="password" autocomplete="current-password" /><br />
			<input type="submit" /><br />
			<?php
				if (isset($_GET['errors'])) {
					echo '<p class="errors">' . $_GET['errors'] . '</p>';
				}
			?>
		</form>
		<span>GXa File Manager <?php echo $gxaversion; ?> by Garet Halliday. <a href="https://github.com/garet90/gxa-file-manager" target="_new">github</a></span>
	</body>
</html>