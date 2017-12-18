<?php
	require 'config.php';
	if (isset($_COOKIE['user']) && isset($_COOKIE['password'])) {
		if ($_COOKIE['user'] == $adminname && $_COOKIE['password'] == $adminpassword) {
			header('location: dashboard/');
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>GXa Server Manager</title>
		<style>
			.center {
				width: 400px;
				height: 200px;
				position: fixed;
				left: 50%;
				top: 50%;
				margin-top: -111px;
				margin-left: -211px;
				border: 1px solid black;
				border-radius: 2px;
				padding: 10px;
			}
			#title {
				width: 600px;
				text-align: center;
				line-height: 30px;
				font-size: 30px;
				position: fixed;
				top: 50%;
				left: 50%;
				margin-left: -300px;
				margin-top: -160px;
			}
			.smol {
				font-size: 10px;
				font-style: italic;
			}
		</style>
	</head>
	
	<body>
		<div class="center">
			<p id="title">GXa Server Management Console</p>
			<form method="post" action="login.php">
				<label for="username">User</label><br />
				<input id="username" type="text" name="user" /><br /><br />
				<label for="password">Password</label><br />
				<input id="password" type="password" name="password" /><br /><br />
				<input type="submit" />
			</form><br />
			<span class="smol">GXa Server Management Console v0.1 BETA by Garet Halliday</span>
		</div>
	</body>
</html>