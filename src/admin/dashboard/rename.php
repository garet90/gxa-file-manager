<?php
	require 'auth.php';
	$continue = true;
	if ($_GET['newname'] == "") {
		echo 'File name cannot be empty!<br />';
		$continue = false;
	}
	if ($continue == false) {
		echo 'Your file was not renamed. <a href="explorer.php?loc=' . $_GET['loc'] . '">Go back</a>';
	} else {
		$filesp = explode(':', $_GET['file']);
		$newfile = '../../' . $_GET['loc'] . '/' . $_GET['newname'];
		$fromfile = '../../' . $filesp[1];
		rename($fromfile, $newfile);
	}
	header('location: explorer.php?loc=' . $_GET['loc']);
?>