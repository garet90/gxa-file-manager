<?php
	require 'auth.php';
	$files = explode(',',$_GET['files']);
	$errors = '';
	foreach ($files as $file) {
		$fileinfo = explode(':',$file);
		$filepath = explode('/',$fileinfo[1]);
		$filename = end($filepath);
		if (file_exists('../..' . $_GET['moveto'] . $filename)) {
			$errors = $errors . 'Item "' . $_GET['moveto'] . $filename . '" already exists. "' . $filename . '" was not moved.<br />';
		} else if (strpos($_GET['moveto'], $fileinfo[1]) !== false) {
			$errors = $errors . 'Directory "' . $filename . '" cannot be moved inside itself, so it was not moved.<br />';
		} else {
			rename('../..' . $fileinfo[1],'../..' . $_GET['moveto'] . $filename);
		}
	}
	if ($errors == '') {
		header('location: explorer.php?loc=' . $_GET['loc']);
	} else {
		header('location: explorer.php?loc=' . $_GET['loc'] . '&errors=' . $errors);
	}
?>