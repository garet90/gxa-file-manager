<?php
	require 'auth.php';
	if ($usercheck && $passcheck) { } else {
		die();
	}
	if ($usemysql) {
		$sqlilink = mysqli_connect($mysqlip, $mysqluser, $mysqlpassword, $mysqldatabase);
	}
	function formatBytes($size, $precision = 2)
	{
		$base = log($size, 1024);
		$suffixes = array('B', 'KB', 'MB', 'GB', 'TB');   
	
		return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
	} 
?>
<!DOCTYPE html>
<html>
	<head>
		<title>GXa File Manager</title>
		<style>
			.leftbox {
				width: 30%;
				float: left;
				padding: 2px;
				background-color: #CDCDCD;
				margin-bottom: 10px;
				word-break: break-all;
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
			#explorer {
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
				margin-top: 2px;
			}
			.leftboxinner.first {
				font-weight: bold;
				background-color: #e6EEEE;
				color: black;
				margin-top: 0;
			}
			#loadbox {
				width: 25px;
				height: 25px;
				position: absolute;
				z-index: 100;
				top: 50%;
				left: 50%;
				margin-top: -12.5px;
				margin-left: -12.5px;
				-webkit-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
				-moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
				box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
			}
			.leftboxinner.load {
				line-height: 23px;
				padding: 0;
				height: 23px;
				text-align: center;
				font-size: 10pt;
				margin-top: 0;
			}
		</style>
		<link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.css">
	</head>
	
	<body>
		<div class="leftbox">
			<div class="leftboxinner first">
				<?php
				echo 'Logged in. Welcome back, ' . $_COOKIE['user'] . '. (Not ' . $_COOKIE['user'] . '? <a href="logout.php">Logout</a>)';
				?>
			</div>
			<div class="leftboxinner">
				<?php
				date_default_timezone_set('UTC');
				
				echo 'Server Status: Online<br />';
				echo 'Server Time: ' . date('d/m/Y, H:i:s') . '<br />';
				echo 'PHP Version: ' . phpversion() . '<br />';
				if ($usemysql) {
					echo 'Server MySQL Info: ' . mysqli_get_server_info($sqlilink) . '<br />';
				}
				echo 'Server IP Address: ' . $_SERVER["REMOTE_ADDR"] . '<br />';
				echo 'Server Port: ' . $_SERVER["SERVER_PORT"] . '<br />';
				echo 'Server Software: ' . $_SERVER["SERVER_SOFTWARE"] . '<br />';
				echo 'HTTPS: ' . $_SERVER["HTTPS"] . '<br />';
				echo '<br /><progress value="' . disk_free_space('/') . '" max="' .  disk_total_space('/') . '"></progress><br />';
				echo formatBytes(disk_free_space('/')) . ' of ' . formatBytes(disk_total_space('/')) . ' (' . round((disk_free_space('/') / disk_total_space('/')),4)*100 . '%) available on ' . GetEnv("SystemDrive") . '<br />';
				echo '<br />https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				?>
			</div>
		</div>
		<div class="rightbox">
			<iframe src="about:blank" id="explorer"></iframe>
			<div id="loadbox" class="leftbox">
				<div class="leftboxinner load">
					<i class="fa fa-spinner fa-pulse fa-fw"></i>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			var loadBox = document.getElementById("loadbox"),
				explorer = document.getElementById("explorer");
			function inload(x) {
				if (x == "stop") {
					loadBox.style.display = "none";
				}
				if (x == "start") {
					loadBox.style.display = "block";
				}
			}
			explorer.src = "explorer.php?loc=/";
		</script>
	</body>
</html>