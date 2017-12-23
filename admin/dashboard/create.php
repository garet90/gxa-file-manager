<?php
	require 'auth.php';
	$errors = '';
	$okrun = true;
	if (strpos($_POST['filename'], '/') == true) {
		$okrun = false;
		$errors = $errors . 'Invalid file name.<br />';
	}
	if ($okrun == true) {
		if ($_POST['filetype'] == "file") {
			if(file_exists('../../' . $_POST['loc'] . '/' . $_POST['filename'])){
				$okrun = false;
				$errors = $errors . "File already exists.<br />";
			}
		} else if ($_POST['filetype'] == "directory") {
			if(file_exists('../../' . $_POST['loc'] . '/' . $_POST['filename'])){
				$okrun = false;
				$errors = $errors . "Directory already exists.<br />";
			}
		} else {
			$errors = $errors . "You must choose a file type!<br />";
			$okrun = false;
		}
	}
	if ($okrun == true) {
		if ($_POST["filetype"] == "file") {
			$file = fopen('../../' . $_POST['loc'] . '/' . $_POST['filename'], 'w') or die("can't open file");
			fclose($file);
		} else if ($_POST["filetype"] == "directory") {
			mkdir('../../' . $_POST['loc'] . '/' . $_POST['filename']);
		}
	}
	if ($okrun == true) {
		header('location: explorer.php?loc=' . $_POST['loc']);
	} else {
		$errors = $errors . "No files were created.";
		header('location: explorer.php?loc=' .$_POST['loc'] . '&errors=' . $errors);
	}
?>