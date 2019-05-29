<?php
	require 'auth.php';
	
	if ($usercheck && $passcheck) {
		$files = explode(',',$_GET['files']);
		$errors = '';
		foreach ($files as $file) {
			$fileinfo = explode(':',$file);
			$filepath = explode('/',$fileinfo[1]);
			$filename = end($filepath);
			if (file_exists('../..' . $_GET['loc'] . $filename)) {
				$errors = $errors . 'Item "' . $_GET['loc'] . $filename . '" already exists. "' . $filename . '" was not moved.<br />';
			} else if (strpos($_GET['moveto'], $fileinfo[1]) !== false) {
				$errors = $errors . 'Directory "' . $filename . '" cannot be moved inside itself, so it was not moved.<br />';
			} else {
				rename('../..' . $fileinfo[1],'../..' . $_GET['loc'] . $filename);
			}
		}
		if ($errors == '') {
			header('location: about:blank');
		} else {
			header('location: about:blank');
		}
	}
?>