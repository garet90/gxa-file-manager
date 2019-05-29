<?php
	require 'auth.php';
	
	if ($usercheck && $passcheck) {
		$errors = '';
		$okrun = true;
		if ($_GET['filetype'] == "file") {
			if(file_exists('../../' . $_GET['loc'] . '/' . $_GET['name'])){
				$okrun = false;
				$errors = $errors . "File already exists.<br />";
			}
		} else if ($_GET['filetype'] == "dir") {
			if(file_exists('../../' . $_GET['loc'] . '/' . $_GET['name'])){
				$okrun = false;
				$errors = $errors . "Directory already exists.<br />";
			}
		} else {
			$errors = $errors . "You must choose a file type!<br />";
			$okrun = false;
		}
		if ($okrun == true) {
			if ($_GET["filetype"] == "file") {
				$file = fopen('../../' . $_GET['loc'] . '/' . $_GET['name'], 'w') or die("can't open file");
				fclose($file);
			} else if ($_GET["filetype"] == "dir") {
				mkdir('../../' . $_GET['loc'] . '/' . $_GET['name']);
			}
		}
		header('location: about:blank');
	}
?>