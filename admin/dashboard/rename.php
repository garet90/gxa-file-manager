<?php
	require 'auth.php';
	
	if ($usercheck && $passcheck) {
		$continue = true;
		$errors = '';
		if (file_exists('../../' . $_GET['loc'] . '/' . $_GET['newname'])) {
			$errors = $errors . 'File already exists! ';
			$continue = false;
		}
		if ($_GET['newname'] == "") {
			$errors = $errors . 'File name cannot be empty! ';
			$continue = false;
		}
		if (strpos($_GET['newname'], '/') == true) {
			$continue = false;
			$errors = $errors . 'Invalid file name. ';
		}
		if ($continue == true) {
			$filesp = explode(':', $_GET['file']);
			$newfile = '../../' . $_GET['loc'] . '/' . $_GET['newname'];
			$fromfile = '../../' . $filesp[1];
			rename($fromfile, $newfile);
		}
		echo $errors;
	}
?>