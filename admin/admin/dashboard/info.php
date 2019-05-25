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
				color: #1C1C1C;
			}
			.title, .subheader {
				text-align: center;
				color: #555;
			}
			.subheader {
				margin: 10px;
			}
			table {
				word-break: break-all;
				width: 100%;
				border-collapse: collapse;
				font-family: Sans-serif;
				font-size: 9pt;
			}
			td, th {
				border: 1px solid #dddddd;
				text-align: left;
				padding: 8px;
			}
			tr:nth-child(even) {
				background-color: #eeeeee;
			}
		</style>
	</head>
	<body>
		<h1 class="title"><?php echo $_SERVER['HTTP_HOST']; ?></h1>
		<h2 class="subheader">General</h2>
		<table>
			<tr>
				<th>Property</th>
				<th>Value</th>
			</tr>
			<tr>
				<td>Server Status</td>
				<td>Online</td>
			</tr>
			<tr>
				<td>Server Time</td>
				<td><?php echo date('d/m/Y, H:i:s'); ?> (<?php echo date_default_timezone_get(); ?>)</td>
			</tr>
			<tr>
				<td>Server IP Address</td>
				<td><?php echo $_SERVER['SERVER_ADDR']; ?></td>
			</tr>
			<tr>
				<td>Server Port</td>
				<td><?php echo $_SERVER["SERVER_PORT"]; ?></td>
			</tr>
		</table>
		<h2 class="subheader">Software</h2>
		<table>
			<tr>
				<th>Property</th>
				<th>Value</th>
			</tr>
			<tr>
				<td>PHP Version</td>
				<td><?php echo phpversion(); ?></td>
			</tr>
			<?php
				if ($usemysql) {
					echo '<tr><td>MySQL Version</td><td>' . mysqli_get_server_info($sqlilink) . '</td></tr>';
				}
			?>
			<tr>
				<td>Software</td>
				<td><?php echo $_SERVER["SERVER_SOFTWARE"]; ?></td>
			</tr>
			<tr>
				<td>GXa File Manager Version</td>
				<td><?php echo $gxaversion; ?></td>
			</tr>
		</table>
		<h2 class="subheader">Disk</h2>
		<table>
			<tr>
				<th>Property</th>
				<th>Value</th>
			</tr>
			<tr>
				<td>Drive Label</td>
				<td><?php echo GetEnv("SystemDrive"); ?></td>
			</tr>
			<tr>
				<td>Document Root</td>
				<td><?php echo $_SERVER['DOCUMENT_ROOT']; ?></td>
			</tr>
			<tr>
				<td>Total Space</td>
				<td><?php echo number_format(disk_total_space('/')) . ' (' . formatBytes(disk_total_space('/')) . ')'; ?></td>
			</tr>
			<tr>
				<td>Free Space</td>
				<td><?php echo number_format(disk_free_space('/')) . ' (' . formatBytes(disk_free_space('/')) . ')'; ?></td>
			</tr>
			<tr>
				<td>Free Percentage</td>
				<td><?php echo round((disk_free_space('/') / disk_total_space('/')),4)*100; ?>%</td>
			</tr>
		</table>
	</body>
</html>