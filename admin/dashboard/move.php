<?php
	require 'auth.php';
	
	if ($hasPermission == false) {
		echo 'You don\'t have permission to run this command.';
		die();
	}
	
	if ($usercheck && $passcheck) {
		$files = explode('|',$_GET['files']);
		$errors = '';
		foreach ($files as $file) {
			$fileinfo = explode(':',$file);
			$filepath = explode('/',$fileinfo[1]);
			$filename = end($filepath);
			if (file_exists('../..' . $_GET['loc'] . $filename)) {
				$errors = $errors . 'Item "' . $_GET['loc'] . $filename . '" already exists. "' . $filename . '" was not moved. ';
			} else if (strpos('../..' . $_GET['loc'], '../..' . $fileinfo[1]) === false) {
				rename('../..' . $fileinfo[1],'../..' . $_GET['loc'] . $filename);
			} else {
				$errors = $errors . 'Directory "' . $filename . '" cannot be moved inside itself, so it was not moved. ';
			}
		}
		echo $errors;
	}
?>