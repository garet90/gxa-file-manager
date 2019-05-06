<?php
require 'auth.php';
$target_dir = '../../' . $_POST['loc'] . '/';
$target_file = $target_dir . basename($_FILES["file"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$errors = '';
// Check if file already exists
if (file_exists($target_file)) {
	$errors = $errors . "File already exists.<br />";
	$uploadOk = 0;
}
// Check file size
if ($_FILES["file"]["size"] > 8388608) {
	$errors = $errors . "Your file is too large.<br />";
	$uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
	$errors = $errors . "Your file was not uploaded.";
// if everything is ok, try to upload file
} else {
	if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
		echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
		header('location: explorer.php?loc=' . $_POST['loc']);
	} else {
		header('location: explorer.php?loc=' . $_POST['loc'] . '&errors=' . $errors);
	}
}
?>