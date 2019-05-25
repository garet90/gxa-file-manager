<?php
	require 'auth.php';
	
	if ($usercheck && $passcheck) {
		$continue = true;
		$errors = '';
		if (file_exists('../../' . $_GET['loc'] . '/' . $_GET['newname'])) {
			$errors = $errors . 'File already exists!<br />';
			$continue = false;
		}
		if ($_GET['newname'] == "") {
			$errors = $errors . 'File name cannot be empty!<br />';
			$continue = false;
		}
		if (strpos($_GET['newname'], '/') == true) {
			$continue = false;
			$errors = $errors . 'Invalid file name.<br />';
		}
		if ($continue == false) {
			echo $errors;
		} else {
			$filesp = explode(':', $_GET['file']);
			$newfile = '../../' . $_GET['loc'] . '/' . $_GET['newname'];
			$fromfile = '../../' . $filesp[1];
			rename($fromfile, $newfile);
			header('Location: explorer.php?loc=' . $_GET['loc']);
		}
	}
?>