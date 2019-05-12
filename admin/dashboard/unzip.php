<?php
	require 'auth.php';

	if ($usercheck && $passcheck) {
		$zip = new ZipArchive;
		$res = $zip->open('../../' . $_GET['loc'] . '/' . $_GET['file']);
		if ($res === TRUE) {
		  $zip->extractTo('../../' . $_GET['loc']);
		  $zip->close();
		}
		header('location: explorer.php?loc=' . $_GET['loc']);
	}
?>