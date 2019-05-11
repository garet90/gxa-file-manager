<?php
	require 'auth.php';
	
	if ($usercheck && passcheck) {
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
	
		$files = explode(",", $_GET['files']);
		$errors = '';
		foreach ($files as $file) {
			$splitpath = explode(":", $file);
			$splitdir = explode("/", $splitpath[1]);
			$filename = array_pop($splitdir);
			$endpath = join("/", $splitdir);
			$endfname = "copy." . $filename;
			if ($splitpath[0] == "file") {
				if (file_exists('../../' . $endpath . '/' . $endfname)) {
					$errors = $errors . 'file "' . $endpath . '/' . $endfname . '" already exists<br />';
				} else {
					copy ('../../' . $splitpath[1], '../../' . $endpath . '/' . $endfname);
				}
			} else {
				if (file_exists('../../' . $endpath . '/' . $endfname . '/')) {
					$errors = $errors . 'directory "' . $endpath . '/' . $endfname . '/" already exists<br />';
				} else {
					copy_directory('../../' . $splitpath[1], '../../' . $endpath . '/' . $endfname);
				}
			}
		}
		if ($errors == '') {
			header('location: explorer.php?loc=' . $_GET['loc']);
		} else {
			header('location: explorer.php?loc=' . $_GET['loc'] . '&errors=' . $errors);
		}
	}
?>