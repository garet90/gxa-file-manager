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
		<title><div class="windowicon"><i class="fa fa-info" aria-hidden="true"></i></div>Info</title>
		<meta charset="UTF-8">
		<style>
			body,html {
				font-family: Sans-serif;
				font-size: 10pt;
			}
			.title, .subheader {
				text-align: center;
				color: #555;
			}
			.subheader {
				margin: 10px 0 0 0;
			}
			.info_name, .info_value {
				display: inline-block;
				width: 50%;
				box-sizing: border-box;
				padding: 2px 5px;
				border-bottom: 1px solid #eee;
			}
			.info_name {
				text-align: right;
				font-weight: bold;
			}
			.info_value {
				text-align: left;
			}
		</style>
	</head>
	<body>
		<h1 class="title"><?php echo $_SERVER['HTTP_HOST']; ?></h1>
		<h2 class="subheader">General</h2>
		<?php
		echo '<div class="info_name">Server Status:</div><div class="info_value">Online</div>';
		echo '<div class="info_name">Server Time:</div><div class="info_value">' . date('d/m/Y, H:i:s') . ' (' . date_default_timezone_get() . ')</div>';
		echo '<div class="info_name">Server IP Address:</div><div class="info_value">' . $_SERVER['SERVER_ADDR'] . '</div>';
		echo '<div class="info_name">Server Port:</div><div class="info_value">' . $_SERVER["SERVER_PORT"] . '</div>';
		?>
		<h2 class="subheader">Software</h2>
		<?php
		echo '<div class="info_name">PHP Version:</div><div class="info_value">' . phpversion() . '</div>';
		if ($usemysql) {
			echo '<div class="info_name">Server MySQL Info:</div><div class="info_value">' . mysqli_get_server_info($sqlilink) . '</div>';
		}
		echo '<div class="info_name">Server Software:</div><div class="info_value">' . $_SERVER["SERVER_SOFTWARE"] . '</div>';
		echo '<div class="info_name">GXa File Manager Version:</div><div class="info_value">v0.2.1 (DEVELOPMENT BUILD)</div>';
		?>
		<h2 class="subheader">Disk</h2>
		<?php
		echo '<div class="info_name">Drive Label:</div><div class="info_value">' . GetEnv("SystemDrive") . '</div>';
		echo '<div class="info_name">Document Root:</div><div class="info_value">' . $_SERVER['DOCUMENT_ROOT'] . '</div>';
		echo '<div class="info_name">Total Space:</div><div class="info_value">' . disk_total_space('/') . ' (' . formatBytes(disk_total_space('/')) . ')</div>';
		echo '<div class="info_name">Free Space:</div><div class="info_value">' . disk_free_space('/') . ' (' . formatBytes(disk_free_space('/')) . ')</div>';
		echo '<div class="info_name">Disk Free Percentage:</div><div class="info_value">' . round((disk_free_space('/') / disk_total_space('/')),4)*100 . '%</div>';
		?>
	</body>
</html>