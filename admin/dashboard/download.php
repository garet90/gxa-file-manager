<?php
	require 'auth.php';
	
	if ($hasPermission == false || $innerPermission == false) {
		echo 'You don\'t have permission to run this command.';
		die();
	}
	
	if ($usercheck && $passcheck) {
		function zipdirectory($zip,$folder,$root) { // zipdirectory($zip,'dashboard','/admin/')
			$src = '../../' . $root . '/' . $folder;
			$dir = opendir($src);
			$fc = 0;
			while(false !== ( $file = readdir($dir)) ) {
				if (( $file != '.' ) && ( $file != '..' )) {
					if ( is_dir($src . '/' . $file) ) {
						zipdirectory($zip,$folder . '/' . $file,$root);
					}
					else {
						$zip->addFile($src . '/' . $file,ltrim($folder . '/' . $file,'/'));
					}
					$fc = $fc + 1;
				}
			}
			if ($fc == 0) {
				$zip->addEmptyDir($folder);
			}
			closedir($dir);
		}
		if (isset($_GET['files'])) {
			$files = explode('|', $_GET['files']);
		} else {
			$files = array('dir:' . $_GET['loc']);
		}
		
		$zip = new ZipArchive();
		$zip_name = time().".zip";
		if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==TRUE){
			$error .=  "* Sorry ZIP creation failed at this time<br/>";
		}
		foreach($files as $file){
			$filedata = explode(':', $file);
			$filepath = '../../' . $filedata[1];
			$filepathex = explode('/', $filedata[1]);
			$filename = end($filepathex);
			if ($filedata[0] == "file") {
				$zip->addFile($filepath, ltrim($filename, '/'));
			} else {
				zipdirectory($zip,$filename,$_GET['loc']);
			}
		}
		$zip->close();
		if(file_exists($zip_name)){
			// push to download the zip
			header('Content-type: application/zip');
			header('Content-Disposition: attachment; filename="'.$zip_name.'"');
			readfile($zip_name);
			// remove zip file is exists in temp path
			unlink($zip_name);
		}
	}
?>