<?php
	function formatBytes($size, $precision = 2)
	{
		$base = log($size, 1024);
		$suffixes = array('B', 'KB', 'MB', 'GB', 'TB');   
	
		return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
	}
	function GetDirectorySize($path){
	    $bytestotal = 0;
	    $path = realpath($path);
	    if($path!==false && $path!='' && file_exists($path)){
	        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
	            $bytestotal += $object->getSize();
	        }
	    }
	    return $bytestotal;
	}
	function GetContentsFileFolder($path) {
		$files = glob($path . "*");
		$dircount = 0;
		$filecount = 0;
		if ($files){
			foreach ($files as $file) {
				if (is_dir($file) && $file !== "") {
					$dircount += 1;
					$dircount += GetContentsFileFolder($file . '/')[0];
					$filecount += GetContentsFileFolder($file . '/')[1];
				} else {
					$filecount += 1;
				}
			}
		}
		return array($dircount, $filecount);
	}
	$totalsize = 0;
	$files = explode(',',$_GET['files']);
	$filemtimemin = 0;
	$filemtimemax = 0;
	$filectimemin = 0;
	$filectimemax = 0;
	$filecount = 0;
	$foldercount = 0;
	$containsfiles = 0;
	$containsfolders = 0;
	$filetypes = array();
	if (count($files) > 1) {
		$icon = '<i class="fa fa-copy" aria-hidden="true" style="transform: rotateY(180deg);"></i>';
		$title = count($files) . ' items';
		foreach ($files as $file) {
			$fileinfo = explode(':',$file);
			$filepath = explode('/',$fileinfo[1]);
			$filename = end($filepath);
			$filemtime = filemtime('../../' . $_GET['loc'] . $filename);
			if ($filemtime < $filemtimemin || $filemtimemin == 0) {
				$filemtimemin = $filemtime;
			}
			if ($filemtime > $filemtimemax) {
				$filemtimemax = $filemtime;
			}
			$filectime = filectime('../../' . $_GET['loc'] . $filename);
			if ($filectime < $filectimemin || $filectimemin == 0) {
				$filectimemin = $filectime;
			}
			if ($filectime > $filectimemax) {
				$filectimemax = $filectime;
			}
			if ($fileinfo[0] == "dir") {
				if (!in_array("folder",$filetypes)) {
					array_push($filetypes,"folder");
				}
				$totalsize += GetDirectorySize('../../' . $_GET['loc'] . $filename);
				$foldercount += 1;
				$containsfolders += GetContentsFileFolder('../../' . $_GET['loc'] . $filename . '/')[0];
				$containsfiles += GetContentsFileFolder('../../' . $_GET['loc'] . $filename . '/')[1];
			} else {
				$totalsize += filesize('../../' . $_GET['loc'] . $filename);
				$extfind = explode('.',$fileinfo[1]);
				$extension = end($extfind);
				if (!in_array($extension,$filetypes)) {
					array_push($filetypes,$extension);
				}
				$filecount += 1;
			}
		}
	} else if ($files[0] == "") {
		$filepath = explode('/',$_GET['loc']);
		$title = $filepath[count($filepath)-2];
		$filemtimemin = filemtime('../../' . $_GET['loc']);
		$filemtimemax = $filemtimemin;
		$filectimemin = filectime('../../' . $_GET['loc']);
		$filectimemax = $filectimemin;
		$totalsize += GetDirectorySize('../../' . $_GET['loc']);
		array_push($filetypes,"folder");
		$icon = '<i class="fa fa-folder-o" aria-hidden="true"></i>';
		$foldercount += 1;
		$containsfolders += GetContentsFileFolder('../../' . $_GET['loc'])[0];
		$containsfiles += GetContentsFileFolder('../../' . $_GET['loc'])[1];
	} else {
		$fileinfo = explode(':',$files[0]);
		$filepath = explode('/',$fileinfo[1]);
		$title = end($filepath);
		$filemtimemin = filemtime('../../' . $_GET['loc'] . end($filepath));
		$filemtimemax = $filemtimemin;
		$filectimemin = filectime('../../' . $_GET['loc'] . end($filepath));
		$filectimemax = $filectimemin;
		if ($fileinfo[0] == "dir") {
			$icon = '<i class="fa fa-folder-o" aria-hidden="true"></i>';
			array_push($filetypes,"folder");
			$totalsize += GetDirectorySize('../../' . $_GET['loc'] . end($filepath));
			$containsfolders += GetContentsFileFolder('../../' . $_GET['loc'] . end($filepath) . '/')[0];
			$containsfiles += GetContentsFileFolder('../../' . $_GET['loc'] . end($filepath) . '/')[1];
			$foldercount += 1;
		} else {
			$filecount += 1;
			$totalsize += filesize('../../' . $_GET['loc'] . end($filepath));
			$extfind = explode('.',$fileinfo[1]);
			$extension = end($extfind);
			if (count($extfind) == 1) {
				$icon = '<i class="fa fa-file-o" aria-hidden="true"></i>';
			} else if ($extension == "png" || $extension == "jpg" || $extension == "gif" || $extension == "bmp" || $extension == "ico") {
				$icon = '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
			} else if ($extension == "txt") {
				$icon = '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
			} else if ($extension == "php" || $extension == "html" || $extension == "xml" || $extension == "js" || $extension == "css") {
				$icon = '<i class="fa fa-file-code-o" aria-hidden="true"></i>';
			} else if ($extension == "wav" || $extension == "mp3") {
				$icon = '<i class="fa fa-file-audio-o" aria-hidden="true"></i>';
			} else if ($extension == "xlsx" || $extension == "xlsm") {
				$icon = '<i class="fa fa-file-excel-o" aria-hidden="true"></i>';
			} else if ($extension == "pdf") {
				$icon = '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>';
			} else if ($extension == "docx" || $extension == "docm") {
				$icon = '<i class="fa fa-file-word-o" aria-hidden="true"></i>';
			} else if ($extension == "tar" || $extension == "zip" || $extension == "gz" || $extension == "7z" || $extension == "s7z" || $extension == "jar" || $extension == "rar" || $extension == "tgz") {
				$icon = '<i class="fa fa-file-archive-o" aria-hidden="true"></i>';
			} else if ($extension == "pptx" || $extension == "pptm") {
				$icon = '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>';
			} else if ($extension == "mov" || $extension == "qt" || $extension == "mp4" || $extension == "m4p" || $extension == "m4v") {
				$icon = '<i class="fa fa-file-video-o" aria-hidden="true"></i>';
			} else if ($extension == "gxl") {
				$linkData = explode(':',file_get_contents('../../' . $fileinfo[1]));
				$icon = '<i class="fa ' . $linkData[0] . '" aria-hidden="true"></i>';
			} else {
				$icon = '<i class="fa fa-file-o" aria-hidden="true"></i>';
			}
			array_push($filetypes,$extension);
		}
	}
	if ($title == "") {
		$title = "Document Root";
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><div class="windowicon"><i class="fa fa-info-circle" aria-hidden="true"></i></div><?php echo $title; ?> - Properties</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.css">
		<style>
			body {
				color: #1C1C1C;
				font-family: Sans-serif;
			}
			table {
				word-break: break-all;
				width: 100%;
				border-collapse: collapse;
				font-size: 9pt;
			}
			td, th {
				border: 1px solid #dddddd;
				text-align: left;
				padding: 8px;
			}
			tr:nth-child(even) {
				background-color: #eeeeee;
			}
			.icon {
				font-size: 40px;
				text-align: center;
				margin-bottom: 5px;
			}
			.name {
				display: inline-block;
				background-color: rgba(235,235,235,.75);
				padding: 5px;
				border-radius: 5px;
				font-family: Sans-serif;
				font-size: 10pt;
				left: 50%;
				position: relative;
				transform: translateX(-50%);
				margin-bottom: 10px;
			}
		</style>
	</head>
	<body>
		<div class="icon"><?php echo $icon; ?></div>
		<div class="name"><?php echo $title; ?></div>
		<table>
			<tr>
				<th>Property</th>
				<th>Value</th>
			</tr>
			<tr>
				<td>Selecting</td>
				<td><?php echo $foldercount; ?> folders and <?php echo $filecount; ?> files</td>
			</tr>
			<?php
				if ($foldercount > 0) {
					echo '<tr><td>Contains</td><td>' . $containsfolders . ' folders and ' . $containsfiles . ' files</td></tr>';
				}
			?>
			<tr>
				<td>Location</td>
				<td><?php echo $_GET['loc']; ?></td>
			</tr>
			<tr>
				<td>Location on drive</td>
				<td><?php echo realpath('../../' . $_GET['loc']); ?></td>
			</tr>
			<tr>
				<td>Type</td>
				<td><?php echo implode(', ',$filetypes); ?></td>
			</tr>
			<tr>
				<td>Size</td>
				<td><?php echo number_format($totalsize); ?> (<?php echo formatBytes($totalsize); ?>)</td>
			</tr>
			<tr>
				<td>Modified</td>
				<td><?php
					echo date ("m/d/Y, H:i:s", $filemtimemin);
					if ($filemtimemin !== $filemtimemax) {
						echo ' - ' . date ("m/d/Y, H:i:s", $filemtimemax);
					}
				?></td>
			</tr>
			<tr>
				<td>Created</td>
				<td><?php
					echo date ("m/d/Y, H:i:s", $filectimemin);
					if ($filectimemin !== $filectimemax) {
						echo ' - ' . date ("m/d/Y, H:i:s", $filectimemax);
					}
				?></td>
			</tr>
		</table>
		<script type="text/javascript">
			function getAbsolutePosition(e) {
			  var posx = 0;
			  var posy = 0;
			
			  if (!e) var e = window.event;
			
			  if (e.pageX || e.pageY) {
			    posx = e.pageX - document.body.scrollLeft - 
			                       document.documentElement.scrollLeft;
			    posy = e.pageY - document.body.scrollTop - 
			                       document.documentElement.scrollTop;
			  } else if (e.clientX || e.clientY) {
			    posx = e.clientX;
			    posy = e.clientY;
			  }
			
			  return {
			    x: posx,
			    y: posy
			  }
			}
			document.addEventListener( "contextmenu", function(e) {
				e.preventDefault();
				top.openContextMenu(getAbsolutePosition(e), window.frameElement);
			} );
			document.addEventListener( "keydown", function(e) {
				if (e.keyCode == 27 || e.which == 27) {
					top.closeContextMenu();
				}
			} );
			document.addEventListener( "click", function(e) {
				top.closeContextMenu();
			} );
		</script>
	</body>
</html>