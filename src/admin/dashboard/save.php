<?php
	require 'auth.php';
	$myfile = fopen('../../' . $_POST['loc'] . '/' . $_POST['file'], "w") or die("Unable to open file!");
	fwrite($myfile, $_POST['data']);
	fclose($myfile);
	header('location: explorer.php?loc=' . $_POST['loc']);
?>