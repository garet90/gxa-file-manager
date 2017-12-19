<?php
	require 'auth.php';
	if ($_POST["filetype"] == "file") {
		$file = fopen('../../' . $_POST['loc'] . '/' . $_POST['filename'], 'w') or die("can't open file");
		fclose($file);
	} else if ($_POST["filetype"] == "directory") {
		mkdir('../../' . $_POST['loc'] . '/' . $_POST['filename']);
	}
	header('location: explorer.php?loc=' . $_POST['loc']);
?>