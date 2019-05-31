<?php
	require 'auth.php';
	
	if ($hasPermission == false) {
		echo 'You don\'t have permission to run this command.';
		die();
	}
	
	if ($usercheck && $passcheck) {
		$myfile = fopen('../../' . $_POST['loc'] . '/' . $_POST['file'], "w") or die("Unable to open file!");
		fwrite($myfile, $_POST['data']);
		fclose($myfile);
	}
?>