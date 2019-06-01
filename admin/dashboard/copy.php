<?php
	require 'auth.php';
	
	if ($hasPermission == false) {
		echo 'You don\'t have permission to run this command.';
		die();
	}
	
	if ($usercheck && $passcheck) {
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
	
		$files = explode("|", $_GET['files']);
		$errors = '';
		foreach ($files as $file) {
			$splitpath = explode(":", $file);
			$splitdir = explode("/", $splitpath[1]);
			$filename = array_pop($splitdir);
			$continue = true;
			if (strpos('../..' . $_GET['loc'], '../..' . $splitpath[1]) !== false) {
				$errors .= "Folders cannot be copied inside of themselves. (../.." . $_GET['loc'] . ' => ../..' . $splitpath[1] . ')';
				$continue = false;
			}
			$endpath = $_GET['loc'];
			if ($continue) {
				if ($splitpath[0] == "file") {
					if (file_exists('../../' . $endpath . '/' . $filename)) {
						$errors = $errors . 'file "' . $endpath . '/' . $filename . '" already exists! ';
					} else {
						copy ('../../' . $splitpath[1], '../../' . $endpath . '/' . $filename);
					}
				} else {
					if (file_exists('../../' . $endpath . '/' . $filename . '/')) {
						$errors = $errors . 'directory "' . $endpath . '/' . $filename . '/" already exists! ';
					} else {
						copy_directory('../../' . $splitpath[1], '../../' . $endpath . '/' . $filename);
					}
				}
			}
		}
		echo $errors;
	}
?>