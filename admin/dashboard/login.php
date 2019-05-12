<?php
	if (isset($_POST['user']) && isset($_POST['password'])) {
		require '../config.php';
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
					header('Location: login.php?errors=' . $errors);
					mysqli_close($sqlilink);
					die();
				}
			}
			$passwordcheck = mysqli_query($sqlilink,"SELECT value FROM `settings` WHERE name='password';");
			while($row = mysqli_fetch_array($passwordcheck, MYSQL_ASSOC)) {
				if (password_verify($_POST['password'],$row['value'])) { } else {
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
		   	header('Location: explorer.php?loc=/');
			mysqli_close($sqlilink);
			die();
		} else {
			if ($_POST["user"] == $adminname && $_POST["password"] == $adminpassword) {
				echo "Login successful. Redirecting...";
				setcookie("user", $_POST["user"], 0, "/", $_SERVER['HTTP_HOST'], 1);
				setcookie("password", $_POST["password"], 0, "/", $_SERVER['HTTP_HOST'], 1);
				header('Location: explorer.php?loc=/');
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
			<input type="text" name="user" id="username" /><br />
			<label for="password">Password</label><br />
			<input type="password" name="password" id="password" /><br />
			<input type="submit" /><br />
			<?php
				if (isset($_GET['errors'])) {
					echo '<p class="errors">' . $_GET['errors'] . '</p>';
				}
			?>
		</form>
		<span>GXa File Manager v0.2.1 DEVELOPMENT BUILD by Garet Halliday. <a href="https://github.com/garet90/gxa-file-manager" target="_new">github</a></span>
	</body>
</html>