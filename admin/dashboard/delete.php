<?php
	require 'auth.php';
	
	if ($usercheck && $passcheck) {
		function rrmdir($dir) {
			if (is_dir($dir)) {
				$objects = scandir($dir);
				foreach ($objects as $object) {
					if ($object != "." && $object != "..") {
						if (filetype($dir."/".$object) == "dir") {
							rrmdir($dir."/".$object); 
						} else {
							unlink($dir."/".$object);
						}
					}
				}
				reset($objects);
				rmdir($dir);
			}
		}
		$files = explode(',', $_GET['files']);
		$errors = '';
		$okrun = true;
		foreach ($files as $file) {
			$splitpath = explode(":", $file);
			if ($splitpath[0] == "dir") {
				if (file_exists('../../' . $splitpath[1])) {
					rrmdir('../../' . $splitpath[1]);
				} else {
					$errors = $errors . 'directory ' . $splitpath[1] . ' does not exist.<br />';
					$okrun = false;
				}
			}
			if ($splitpath[0] == "file") {
				if (file_exists('../../' . $splitpath[1])) {
					unlink('../../' . $splitpath[1]);
				} else {
					$errors = $errors . 'file ' . $splitpath[1] . ' does not exist.<br />';
					$okrun = false;
				}
			}
		}
		if ($okrun == true) {
			header('location: explorer.php?loc=' . $_GET['loc']);
		} else {
			header('location: explorer.php?loc=' . $_GET['loc'] . '&errors=' . $errors);
		}
	}
?>