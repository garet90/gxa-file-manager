<?php
	require 'auth.php';
	
	if ($hasPermission == false) {
		echo 'You don\'t have permission to run this command.';
		die();
	}

	if ($usercheck && $passcheck) {
		$target_dir = '../../' . $_POST['loc'] . '/';
		$target_file = $target_dir . basename($_FILES["file"]["name"]);
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$errors = '';
		// Check if file already exists
		if (file_exists($target_file)) {
			$errors = $errors . "File already exists. ";
		}
		// Check file size
		if ($_FILES["file"]["size"] > 8388608) {
			$errors = $errors . "Your file is too large. ";
		}
		if ($errors == "") {
			move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
		}
		header('Location: explorer.php?loc=' . $_POST['loc'] . '&errors=' . $errors);
	}
?>