<?php
	require 'auth.php';

	if ($usercheck && $passcheck) {
		$zip = new ZipArchive;
		$file = explode(':',$_GET['file']);
		$res = $zip->open('../../' . $file[1]);
		if ($res === TRUE) {
		  $zip->extractTo('../../' . $_GET['loc']);
		  $zip->close();
		}
		header('location: explorer.php?loc=' . $_GET['loc']);
	}
?>