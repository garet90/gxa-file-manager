<?php
	require "auth.php";
	if ($usercheck && $passcheck) { } else {
		die();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Directory Explorer</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.css">
		<style>
			body, html {
				margin: 0;
				padding: 0;
				font-size: 12px;
				font-family: Sans-serif;
			}
		</style>
	</head>
	<body onLoad="top.inload('stop')">
	<?php
		$directories = glob('../../' . $_GET['loc'] . '*', GLOB_ONLYDIR);
		if ($_GET['loc'] !== '/') {
			$currentarray = explode('/',$_GET['loc']);
			array_pop($currentarray);
			array_pop($currentarray);
			$parentdirectory = implode('/',$currentarray) . '/';
			echo "<a href='?loc=" . $parentdirectory . "' onclick='top.inload(\"start\")'><i class='fa fa-folder-open-o' aria-hidden='true'></i> Parent Directory</a><br /><br />";
		}
		foreach ($directories as $directory) {
			$subdir = substr($directory, 6);
			$dirpath = explode('/', $subdir);
			echo "<a href='?loc=" . $subdir . "/' onclick='top.inload(\"start\")'><i class='fa fa-folder-o' aria-hidden='true'></i> " . end($dirpath) . "</a><br />";
		}
		$explodedURL = explode('/', substr($_GET['loc'],1));
		$prevURLstring = "/";
		foreach ($explodedURL as $key=>$URL) {
			$explodedURL[$key] = "<a href='directoryexplorer.php?loc=" . $prevURLstring . $URL . "/' onclick=" . '"' . "top.inload('start')" . '">' . $URL . "</a>";
			$prevURLstring = $prevURLstring . $URL . '/';
			$prevURLstring = preg_replace('/(\/+)/','/',$prevURLstring);
		}
		$joinedURL = join('/', $explodedURL);
		echo "<br /><a href='?loc=/' onclick='top.inload(\"start\")'>root</a>/" . $joinedURL . " - " . count($directories) . " directories";
		echo "<p id='location' style='display:none;'>" . $_GET['loc'] . "</p>";
	?>
	</body>
</html>