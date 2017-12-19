<?php
	require 'auth.php';
 function rrmdir($dir) {
  if (is_dir($dir)) {
    $objects = scandir($dir);
    foreach ($objects as $object) {
      if ($object != "." && $object != "..") {
        if (filetype($dir."/".$object) == "dir") 
           rrmdir($dir."/".$object); 
        else unlink   ($dir."/".$object);
      }
    }
    reset($objects);
    rmdir($dir);
  }
 }
	$files = explode(',', $_GET['files']);
	foreach ($files as $file) {
		$splitpath = explode(":", $file);
		if ($splitpath[0] == "dir") {
			rrmdir('../../' . $splitpath[1]);
		}
		if ($splitpath[0] == "file") {
			unlink('../../' . $splitpath[1]);
		}
	}
	header('location: explorer.php?loc=' . $_GET['loc']);
?>