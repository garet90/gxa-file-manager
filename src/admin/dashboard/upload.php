<?php
require 'auth.php';
$target_dir = '../../' . $_POST['loc'] . '/';
$target_file = $target_dir . basename($_FILES["file"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.<br />";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["file"]["size"] > 8388608) {
    echo "Sorry, your file is too large.<br />";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded. <a href='explorer.php?loc=" . $_POST['loc'] . "'>Go back</a>";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
		header('location: explorer.php?loc=' . $_POST['loc']);
    } else {
        echo "Sorry, there was an error uploading your file. <a href='explorer.php?loc=" . $_POST['loc'] . "'>Go back</a>";
    }
}
?>