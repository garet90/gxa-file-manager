<?php require 'auth.php' ?>
<!DOCTYPE html>
<html>
	<head>
		<title>GXa Server Manager</title>
		<style>
			.leftbox {
				width: 30%;
				float: left;
				padding: 2px;
				background-color: #CDCDCD;
				margin-bottom: 10px;
				word-break: break-all;
			}
			.leftbox.last {
				clear: both;
			}
			.rightbox {
				width: auto;
				float: left;
				top: 0;
				bottom: 0;
				position: absolute;
				left: 30%;
				margin-left: 15px;
				right: 0;
			}
			body, html {
				margin: 0;
				padding: 0;
			}
			body {
				padding: 10px;
				overflow-y: hidden;
			}
			iframe.explorer {
				width: 100%;
				height: 100%;
				border: 0;
				margin: 0;
				padding: 0;
				overflow-y: scroll;
				overflow-x: hidden;
			}
			.leftboxinner {
				background-color: white;
				border: 1px solid white;
				font-size: 8pt;
				padding: 8px 4px 8px 4px;
				font-family: Sans-serif;
				color: #3D3D3D;
			}
			.leftboxinner.first {
				font-weight: bold;
				background-color: #e6EEEE;
				color: black;
			}
		</style>
	</head>
	
	<body>
		<div class="leftbox">
			<div class="leftboxinner first">
				<?php
				echo 'Logged in. Welcome back, ' . $_COOKIE['user'] . '. (Not ' . $_COOKIE['user'] . '? <a href="logout.php">Logout</a>)';
				?>
			</div>
		</div>
		<div class="rightbox">
			<iframe src="explorer.php?loc=/" class="explorer"></iframe>
		</div>
		<div class="leftbox last">
			<div class="leftboxinner">
				<?php
				date_default_timezone_set('UTC');
				
				echo 'Server Status: Online<br />';
				echo 'Server Time: ' . date('d/m/Y, H:i:s') . '<br />';
				echo 'PHP Version: ' . phpversion() . '<br />';
				echo '<br />https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				?>
			</div>
		</div>
	</body>
</html>