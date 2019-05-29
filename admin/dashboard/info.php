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
				color: #1C1C1C;
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
			.icon {
				font-size: 40px;
				text-align: center;
				margin-bottom: 5px;
			}
			.name {
				display: inline-block;
				background-color: rgba(235,235,235,.75);
				padding: 5px;
				border-radius: 5px;
				font-family: Sans-serif;
				font-size: 10pt;
				left: 50%;
				position: relative;
				transform: translateX(-50%);
				margin-bottom: 10px;
			}
		</style>
		<link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.css">
	</head>
	<body>
		<div class="icon"><i class="fa fa-info" aria-hidden="true"></i></div>
		<div class="name"><?php echo $_SERVER['HTTP_HOST']; ?></div>
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
		<script type="text/javascript">
			function getAbsolutePosition(e) {
			  var posx = 0;
			  var posy = 0;
			
			  if (!e) var e = window.event;
			
			  if (e.pageX || e.pageY) {
			    posx = e.pageX - document.body.scrollLeft - 
			                       document.documentElement.scrollLeft;
			    posy = e.pageY - document.body.scrollTop - 
			                       document.documentElement.scrollTop;
			  } else if (e.clientX || e.clientY) {
			    posx = e.clientX;
			    posy = e.clientY;
			  }
			
			  return {
			    x: posx,
			    y: posy
			  }
			}
			document.addEventListener( "contextmenu", function(e) {
				e.preventDefault();
				top.openContextMenu(getAbsolutePosition(e), window.frameElement);
			} );
			document.addEventListener( "keydown", function(e) {
				if (e.keyCode == 27 || e.which == 27) {
					top.closeContextMenu();
				}
			} );
			document.addEventListener( "click", function(e) {
				top.closeContextMenu();
			} );
		</script>
	</body>
</html>