<?php
	require 'auth.php';

	if ($usercheck && $passcheck) {
		$zip = new ZipArchive;
		$file = explode(':',$_GET['file']);
		$errors = '';
		if (file_exists('../../' . $file[1])) {
			$res = $zip->open('../../' . $file[1]);
			if ($res === TRUE) {
			  $zip->extractTo('../../' . $_GET['loc']);
			  $zip->close();
			}
		} else {
			$errors .= "file does not exist. ";
		}
		header('location: explorer.php?loc=' . $_GET['loc'] . '&errors=' . $errors);
	}
?>