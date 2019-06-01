<?php
	require 'auth.php';
	if ($usercheck && $passcheck) { } else {
		die();
	}
	function formatBytes($size, $precision = 2)
	{
		$base = log($size, 1024);
		$suffixes = array('B', 'KB', 'MB', 'GB', 'TB');   
	
		return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
	}
	function strToInt($str, $precision = 4) {
		$result = '';
		$index = array(' '=>'00','a'=>'01','b'=>'02','c'=>'03','d'=>'04','e'=>'05','f'=>'06','g'=>'07','h'=>'08','i'=>'09','j'=>'10','k'=>'11','l'=>'12','m'=>'13','n'=>'14','o'=>'15','p'=>'16','q'=>'17','r'=>'18','s'=>'19','t'=>'20','u'=>'21','v'=>'22','w'=>'23','x'=>'24','y'=>'25','z'=>'26','.'=>'27');
		$i = -1;
		while ($i++ < $precision) {
			if ($i < strlen($str)) {
				if (array_key_exists($str[$i],$index)) {
					$result .= $index[$str[$i]];
				} else {
					$result .= '00';
				}
			} else {
				$result .= '00';
			}
		}
		return $result;
	}
?>
<!DOCTYPE html>
<html ondrop="drop(event)" ondragover="allowDrop(event)">
	<head>
		<title><div class="windowicon"><i class="fa fa-folder-open-o" aria-hidden="true"></i></div><?php echo $_GET['loc']; ?> - File Explorer</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.css">
		<style>
			body, html {
				margin: 0;
				padding: 0;
				min-height: 100%;
				user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				-webkit-user-select: none;
			}
			footer {
				position: fixed;
				left: 0;
				right: 0;
				bottom: 0;
				height: 15px;
				line-height: 15px;
				background-color: #E0E6F8;
				padding: 6px 10px;
				z-index: 1;
				font-family: Sans-serif;
				font-size: 10px;
				color: #666;
				border-top: 1px solid white;
				user-select: none;
			}
			#files {
				display: flex;
				flex-direction: row;
				flex-wrap: wrap;
				justify-content: center;
				margin: 0 0 27px 0;
				font-family: Sans-serif;
				font-size: 9pt;
				line-height: calc(100vh - 28px);
			}
			.file {
				width: 75px;
				height: 90px;
				display: inline-block;
				line-height: 60px;
				text-align: center;
				position: relative;
				margin: 5px;
				border: 1px solid transparent;
				box-sizing: border-box;
				user-select: none;
				cursor: default;
				color: #1C1C1C;
			}
			.file:hover {
				border-color: #E0E6F8;
				background-color: #EFF2FB;
			}
			.file.selected {
				background-color: #E0E6F8;
				border-color: #CED8F6;
			}
			.file.faded {
				border: 1px dashed #CED8F6;
			}
			.file__icon {
				font-size: 40px;
				color: #424242;
			}
			.file__name {
				position: absolute;
				background-color: rgba(235,235,235,.75);
				line-height: 10pt;
				top: 65px;
				left: 50%;
				transform: translateY(-50%) translateX(-50%);
				font-family: Sans-serif;
				font-size: 8pt;
				padding: 5px;
				border-radius: 5px;
				max-width: 100%;
				box-sizing: border-box;
				overflow-wrap: break-word;
			}
			#loc {
				float: left;
			}
			#sortby {
				float: right;
				font-size: inherit;
				height: 100%;
			}
			#selector {
				position: absolute;
				z-index: 1;
				background-color: rgba(100,130,255,.25);
				border: 1px solid rgba(100,130,255,.5);
				box-sizing: border-box;
				display: none;
			}
			#action-frame, #download-frame, #folder-editable {
				display: none;
			}
			#status {
				float: right;
				display: inline-block;
			}
		</style>
	</head>
	<body>
		<iframe src="about:blank" id="action-frame" data-idle="true"></iframe>
		<iframe src="about:blank" id="download-frame"></iframe>
		<form method="post" action="upload.php" enctype="multipart/form-data">
			<input type="hidden" name="loc" value="<?php echo $_GET['loc']; ?>"></input>
			<input type="file" name="file" style="display: none;" id="file-upload" onchange="this.parentElement.submit();"></input>
		</form>
		<div id="files">
			<?php
				$path = '../../' . $_GET['loc'];
				$continue = true;
				$folderEditable = true;
				$filecount = 0;
				$foldercount = 0;
				if ($hasPermission == false) {
					echo "You do not have permission to view this folder.";
					$continue = false;
					$folderEditable = false;
				}
				if (!file_exists($path) && $continue) {
					echo "Folder doesn't exist.";
					$continue = false;
					$folderEditable = false;
				}
				if ($continue) {
					$files = scandir($path);
					$totalsize = 0;
					foreach ($files as $key => $file) {
						if ($file == "." || ($_GET['loc'] == '/' && $file == '..')) { } else {
							$filetype = '';
							$fileicon = '<i class="fa fa-file-o" aria-hidden="true"></i>';
							$filetitle = '';
							$finalname = $file;
							$linkTo = '';
							if (is_dir ($path . $file)) {
								$filetype = 'dir';
								$fileicon = '<i class="fa fa-folder-o" aria-hidden="true"></i>';
								$filetitle = $file . '
Modified: ' . date ("m/d/Y, H:i:s", filemtime($path . $file));
								if ($file !== "..") {
									$foldercount = $foldercount + 1;
								}
							} else {
								$expfilename = explode('.',$file);
								$extension = end($expfilename);
								$filetype = '.' . $extension;
								if (count($expfilename) == 1) {
									$fileicon = '<i class="fa fa-file-o" aria-hidden="true"></i>';
									$filetype = "file";
								} else if ($extension == "png" || $extension == "jpg" || $extension == "gif" || $extension == "bmp" || $extension == "ico") {
									$fileicon = '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
								} else if ($extension == "txt") {
									$fileicon = '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
								} else if ($extension == "php" || $extension == "html" || $extension == "xml" || $extension == "js" || $extension == "css") {
									$fileicon = '<i class="fa fa-file-code-o" aria-hidden="true"></i>';
								} else if ($extension == "wav" || $extension == "mp3") {
									$fileicon = '<i class="fa fa-file-audio-o" aria-hidden="true"></i>';
								} else if ($extension == "xlsx" || $extension == "xlsm") {
									$fileicon = '<i class="fa fa-file-excel-o" aria-hidden="true"></i>';
								} else if ($extension == "pdf") {
									$fileicon = '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>';
								} else if ($extension == "docx" || $extension == "docm") {
									$fileicon = '<i class="fa fa-file-word-o" aria-hidden="true"></i>';
								} else if ($extension == "tar" || $extension == "zip" || $extension == "gz" || $extension == "7z" || $extension == "s7z" || $extension == "jar" || $extension == "rar" || $extension == "tgz") {
									$fileicon = '<i class="fa fa-file-archive-o" aria-hidden="true"></i>';
								} else if ($extension == "pptx" || $extension == "pptm") {
									$fileicon = '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>';
								} else if ($extension == "mov" || $extension == "qt" || $extension == "mp4" || $extension == "m4p" || $extension == "m4v") {
									$fileicon = '<i class="fa fa-file-video-o" aria-hidden="true"></i>';
								} else if ($extension == "gxl") {
									$linkData = explode(':',file_get_contents($path . $file));
									$fileicon = '<i class="fa ' . $linkData[0] . '" aria-hidden="true"></i>';
									$linkTo = $linkData[1];
									$finalname = substr($file,0,-4);
								} else {
									$fileicon = '<i class="fa fa-file-o" aria-hidden="true"></i>';
								}
								$filetitle = $file . '
Size: ' . formatBytes(filesize($path . $file)) . '
Modified: ' . date ("m/d/Y, H:i:s", filemtime($path . $file));
								$filecount = $filecount + 1;
								$totalsize += filesize($path . $file);
							}
							$fileorder = "0";
							if ($filetype == "dir") {
								$fileorder = "-1";
							}
							echo '<div data-naming="false" data-selected="false" data-name="' . $file . '" data-filetype="' . $filetype . '" ondragstart="drag(event)" data-continue="false" draggable="true" class="file" title="' . $filetitle . '" style="order: ' . $fileorder . '" onmousedown="selectfile(this,false)" onclick="selectfile(this,true)" data-linkto="' . $linkTo . '"><div class="file__icon">' . $fileicon . '</div><div class="file__name" contenteditable="false">' . $finalname . '</div></div>';
						}
					}
				}
			?>
		</div>
		<div id="folder-editable"><?php
			if ($folderEditable) {
				echo "true";
			} else {
				echo "false";
			}
		?></div>
		<div id="selector"></div>
		<footer>
			<span id="loc"><?php echo $_GET['loc']; ?></span><span style="padding-left: 4px;" id="folderInfo" data-foc="<?php echo $foldercount; ?>" data-fic="<?php echo $filecount; ?>">- <?php echo $foldercount; ?> folders and <?php echo $filecount; ?> files</span>
			<div id="status"></div>
		</footer>
		<script type="text/javascript">
			function allowDrop(ev) {
				ev.preventDefault();
			}
			
			function drag(ev) {
				var loc = document.getElementById("loc").innerHTML,
					selectedFiles = document.getElementsByClassName("selected"),
					selectedFileNames = "";
				
				for (i = 0; i < selectedFiles.length; i++) {
					if (selectedFiles[i].getAttribute("data-name") !== "..") {
						if (selectedFiles[i].getAttribute("data-filetype") == "dir") {
							var filetype = "dir";
						} else {
							var filetype = "file";
						}
						selectedFileNames += filetype + ":" + loc + selectedFiles[i].getAttribute("data-name") + "|";
					}
				}
				
				selectedFileNames = selectedFileNames.slice(0,-1);
				
				ev.dataTransfer.setData("files", selectedFileNames);
				ev.dataTransfer.setData("sender", window.frameElement.id);
				ev.dataTransfer.setData("loc", loc);
			}
			
			function drop(ev) {
				ev.preventDefault();
				var loc = document.getElementById("loc").innerHTML;
				if (ev.dataTransfer.getData("loc") !== loc) {
					frameAction("move.php?files=" + ev.dataTransfer.getData("files") + "&loc=" + loc, "Moving...");
					var filesMoved = ev.dataTransfer.getData("files").split('|');
					filesMoved.forEach(function(file) {
						if (file !== "") {
							var fileSplit = file.split(':'),
								filePath = fileSplit[1].split('/'),
								fileName = filePath[filePath.length-1];
							newLocalFile(fileName,fileSplit[0]);
						}
					});
					top.document.getElementById(ev.dataTransfer.getData("sender")).contentWindow.removeFilesByString(ev.dataTransfer.getData("files"));
				}
			}
		</script>
		<script type="text/javascript">
			if (window.frameElement.id == "desktop-explorer" && document.getElementById("folder-editable").innerHTML == "true") {
				document.getElementsByTagName("footer")[0].style.display = "none";
				top.document.getElementById("cm-files-pd").style.display = "none";
				removeFilesByString("file:..");
				document.getElementById("files").style.flexDirection = "column";
				document.getElementById("files").style.justifyContent = "left";
				document.getElementById("files").style.maxHeight = "100vh";
				document.getElementById("files").style.margin = "0";
				document.getElementById("files").style.width = "0";
			} else {
				top.document.getElementById("cm-files-pd").style.display = "block";
			}
			function clickInsideElement( e, className ) {
				var el = e.srcElement || e.target;
			
				if ( el.classList.contains(className) ) {
					return el;
				} else {
					while ( el = el.parentNode ) {
						if ( el.classList && el.classList.contains(className) ) {
							return el;
						}
					}
				}
			
				return false;
			}
			function selectfile(elmnt,hardclick) {
				var selectedFiles = document.getElementsByClassName("selected"),
					selectedLength = selectedFiles.length,
					loc = document.getElementById("loc").innerHTML;
				if (elmnt && elmnt.classList.contains("selected")) {
					if (selectedLength == 1) {
						if (hardclick && !keys[17]) {
							if (elmnt.getAttribute("data-continue") == "false") {
								elmnt.setAttribute("data-continue","true");
							} else {
								var filetype = elmnt.getAttribute("data-filetype"),
									filename = elmnt.getAttribute("data-name");
								if (filetype == "dir") {
									if (filename == ".") {
										window.location = "?loc=" + loc;
									} else if (filename == "..") {
										if (loc == "/") {
											window.location = "?loc=/";
										} else {
											var parentdirectory = loc.split('/').slice(0, -2).join('/');
											if (parentdirectory == "") {
												if (window.frameElement.id !== "desktop-explorer") {
													window.location = "?loc=/";
												} else {
													top.createWindow('explorer.php?loc=/');
												}
											} else {
												if (window.frameElement.id !== "desktop-explorer") {
													window.location = "?loc=" + parentdirectory + "/";
												} else {
													top.createWindow('explorer.php?loc=' + parentdirectory + '/');
												}
											}
										}
									} else {
										if (window.frameElement.id !== "desktop-explorer") {
											window.location = "?loc=" + loc + filename + "/";
										} else {
											top.createWindow('explorer.php?loc=' + loc + filename + '/');
										}
									}
								} else {
									if (filetype == ".gxl") {
										top.createWindow(elmnt.getAttribute("data-linkto"));
									}
								}
								elmnt.setAttribute("data-continue","false");
							}
						}
					} else {
						if (keys[17] && !hardclick) {
							elmnt.classList.remove("selected");
							elmnt.setAttribute("data-selected","false");
							elmnt.setAttribute("data-continue","false");
						}
					}
				} else if (!hardclick) {
					if (keys[16]) {
						if (elmnt) {
							var selectedFiles = document.getElementsByClassName("selected"),
								firstselection = undefined;
							if (selectedFiles.length == 0) {
								firstselection = document.getElementsByClassName("file")[0];
								firstselection.classList.add("selected");
								firstselection.setAttribute("data-selected","true");
							} else {
								for (i = 0; i < selectedFiles.length; i++) {
									if (selectedFiles[i].style.order == "-1" && firstselection == undefined) {
										firstselection = selectedFiles[i];
									}
								}
								if (firstselection == undefined) {
									firstselection = selectedFiles[0];
								}
							}
							if (+firstselection.style.order < +elmnt.style.order) {
								selectOriginUntil(elmnt);
								selectToEnd(firstselection);
							} else if (+firstselection.style.order > +elmnt.style.order) {
								selectOriginUntil(firstselection);
								selectToEnd(elmnt);
							} else {
								var allFiles = document.getElementsByClassName("file"),
									selecting = false;
								for (i = 0; i < allFiles.length; i++) {
									if (allFiles[i] == elmnt || allFiles[i] == firstselection) {
										selecting = !selecting;
									} else if (selecting && allFiles[i].style.order == elmnt.style.order) {
										allFiles[i].classList.add("selected");
										allFiles[i].setAttribute("data-selected","true");
									}
								}
							}
						}
					} else if (keys[17] !== true) {
						for (i = 0; i < selectedLength; i++) {
							selectedFiles[0].setAttribute("data-continue","false");
							selectedFiles[0].setAttribute("data-selected","false");
							selectedFiles[0].classList.remove("selected");
						}
					}
					if (elmnt) {
						elmnt.classList.add("selected");
						elmnt.setAttribute("data-selected","true");
					}
				}
			}
			function selectOriginUntil(elmnt) {
				var allFiles = document.getElementsByClassName("file"),
					selecting = true;
				for (i = 0; i < allFiles.length; i++) {
					if (allFiles[i] == elmnt) {
						selecting = false;
					} else if (allFiles[i].style.order == elmnt.style.order && selecting) {
						allFiles[i].classList.add("selected");
						allFiles[i].setAttribute("data-selected","true");
					}
				}
			}
			function selectToEnd(elmnt) {
				var allFiles = document.getElementsByClassName("file"),
					selecting = false;
				for (i = 0; i < allFiles.length; i++) {
					if (allFiles[i] == elmnt) {
						selecting = true;
					} else if (allFiles[i].style.order == elmnt.style.order && selecting) {
						allFiles[i].classList.add("selected");
						allFiles[i].setAttribute("data-selected","true");
					}
				}
			}
			var keys = [];
			document.onkeydown = function(e) {
				var key = e.keyCode || e.which;
				keys[key] = true;
				var selectedFiles = document.getElementsByClassName("selected");
				if (keys[17] && keys[82]) {
					e.preventDefault();
					location.reload();
				}
				if (selectedFiles.length == 1 && selectedFiles[0].getAttribute("data-naming") == "true") { } else {
					if (keys[17] && keys[65]) {
						e.preventDefault();
						var files = document.getElementsByClassName("file");
						for (i = 0; i < files.length; i++) {
							files[i].classList.add("selected");
						}
					}
					if (keys[17] && keys[67]) {
						e.preventDefault();
						cmAction("cm-files-co");
					}
					if (keys[17] && keys[88]) {
						e.preventDefault();
						cmAction("cm-files-cu");
					}
					if (keys[17] && keys[86]) {
						e.preventDefault();
						cmAction("cm-files-pa");
					}
					if (keys[46]) {
						e.preventDefault();
						cmAction("cm-files-de");
					}
					if (keys[13] && selectedFiles.length == 1) {
						e.preventDefault();
						selectedFiles[0].click();
						selectedFiles[0].click();
					}
				}
			}
			document.onkeyup = function(e) {
				var key = e.keyCode || e.which;
				keys[key] = false;
			}
			var dragselecting = false,
				dragselectstart = [];
			document.addEventListener( "mousedown", function(e) {
				if (clickInsideElement( e, "file" ) == false) {
					selectfile();
				}
				top.closeContextMenu();
				if (clickInsideElement( e, "file" ) == false) {
					var selector = document.getElementById("selector"),
						position = getPosition(e),
						body = document.body,
						html = document.documentElement,
						pageHeight = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight ),
						pageWidth = Math.max( body.scrollWidth, body.offsetWidth, html.clientWidth, html.scrollWidth, html.offsetWidth );
					if (position.x <= pageWidth) {
						selector.style.display = "block";
						selector.style.top = position.y + "px";
						selector.style.left = position.x + "px";
						selector.style.height = 0;
						selector.style.width = 0;
						dragselecting = true;
						dragselectstart = position;
					}
				}
			});
			document.addEventListener( "mousemove", function(e) {
				if (dragselecting) {
					var selector = document.getElementById("selector"),
						position = getPosition(e),
						body = document.body,
						html = document.documentElement,
						pageHeight = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight ),
						pageWidth = Math.max( body.scrollWidth, body.offsetWidth, html.clientWidth, html.scrollWidth, html.offsetWidth );
					if (position.y > pageHeight) {
						position.y = pageHeight;
					}
					if (position.x > pageWidth) {
						position.x = pageWidth;
					}
					if (position.y < 0) {
						position.y = 0;
					}
					if (position.x < 0) {
						position.x = 0;
					}
					if (position.y > dragselectstart.y) {
						selector.style.height = (position.y - dragselectstart.y) + "px";
						selector.style.top = dragselectstart.y + "px";
					} else {
						selector.style.top = position.y + "px";
						selector.style.height = (dragselectstart.y - position.y) + "px";
					}
					if (position.x > dragselectstart.x) {
						selector.style.width = (position.x - dragselectstart.x) + "px";
						selector.style.left = dragselectstart.x + "px";
					} else {
						selector.style.left = position.x + "px";
						selector.style.width = (dragselectstart.x - position.x) + "px";
					}
					var files = document.getElementsByClassName("file");
					for (i = 0; i < files.length; i++) {
						if (((files[i].offsetTop >= Math.min(dragselectstart.y, position.y) && files[i].offsetTop <= Math.max(dragselectstart.y, position.y)) || (files[i].offsetTop + files[i].offsetHeight >= Math.min(dragselectstart.y, position.y) && files[i].offsetTop + files[i].offsetHeight <= Math.max(dragselectstart.y, position.y)) || (files[i].offsetTop <= Math.min(dragselectstart.y, position.y) && files[i].offsetTop + files[i].offsetHeight >= Math.max(dragselectstart.y, position.y))) && ((files[i].offsetLeft >= Math.min(dragselectstart.x, position.x) && files[i].offsetLeft <= Math.max(dragselectstart.x, position.x)) || (files[i].offsetLeft + files[i].offsetWidth >= Math.min(dragselectstart.x, position.x) && files[i].offsetLeft + files[i].offsetWidth <= Math.max(dragselectstart.x, position.x)) || (files[i].offsetLeft <= Math.min(dragselectstart.x, position.x) && files[i].offsetLeft + files[i].offsetWidth >= Math.max(dragselectstart.x, position.x)))) {
							if (files[i].getAttribute("data-selected") == "true") {
								files[i].classList.remove("selected");
							} else {
								files[i].classList.add("selected");
							}
						} else {
							if (files[i].getAttribute("data-selected") == "true") {
								files[i].classList.add("selected");
							} else {
								files[i].classList.remove("selected");
							}
						}
					}
				}
			});
			document.addEventListener( "mouseup", function(e) {
				var selector = document.getElementById("selector");
				selector.style.display = "none";
				dragselecting = false;
				var files = document.getElementsByClassName("file");
				for (i = 0; i < files.length; i++) {
					if (files[i].classList.contains("selected")) {
						files[i].setAttribute("data-selected","true");
					} else {
						files[i].setAttribute("data-selected","false");
					}
				}
			});
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
			function getPosition(e) {
			  var posx = 0;
			  var posy = 0;
			
			  if (!e) var e = window.event;
			
			  if (e.pageX || e.pageY) {
			    posx = e.pageX;
			    posy = e.pageY;
			  } else if (e.clientX || e.clientY) {
			    posx = e.clientX + document.body.scrollLeft - 
			                       document.documentElement.scrollLeft;
			    posy = e.clientY + document.body.scrollTop - 
			                       document.documentElement.scrollTop;
			  }
			
			  return {
			    x: posx,
			    y: posy
			  }
			}
			document.addEventListener( "contextmenu", function(e) {
				var folderEditable = document.getElementById("folder-editable").innerHTML;
				e.preventDefault();
				var selectedFiles = document.getElementsByClassName("selected");
				top.document.getElementById("cm-files-ne").style.display = "block";
				top.document.getElementById("cm-files-pa").style.display = "block";
				top.document.getElementById("cm-files-re").style.display = "block";
				if (selectedFiles.length == 0) {
					top.document.getElementById("cm-files-cu").style.display = "none";
					top.document.getElementById("cm-files-de").style.display = "none";
					top.document.getElementById("cm-files-ed").style.display = "none";
					top.document.getElementById("cm-files-rn").style.display = "none";
					top.document.getElementById("cm-files-co").style.display = "none";
					top.document.getElementById("cm-files-filter-uz").style.display = "none";
					top.document.getElementById("cm-files-op").style.display = "none";
					top.document.getElementById("cm-files-on").style.display = "none";
					top.document.getElementById("cm-files-do").style.display = "block";
				} else if (selectedFiles.length == 1) {
					if (selectedFiles[0].getAttribute("data-name") == '..') {
						top.document.getElementById("cm-files-de").style.display = "none";
						top.document.getElementById("cm-files-do").style.display = "none";
						top.document.getElementById("cm-files-rn").style.display = "none";
						top.document.getElementById("cm-files-co").style.display = "none";
						top.document.getElementById("cm-files-ed").style.display = "none";
						top.document.getElementById("cm-files-cu").style.display = "none";
						top.document.getElementById("cm-files-op").style.display = "block";
						top.document.getElementById("cm-files-on").style.display = "block";
						top.document.getElementById("cm-files-filter-uz").style.display = "none";
					} else {
						if (selectedFiles[0].getAttribute("data-filetype") !== "dir" && selectedFiles[0].getAttribute("data-filetype") !== ".gxl") {
							top.document.getElementById("cm-files-ed").style.display = "block";
							top.document.getElementById("cm-files-op").style.display = "none";
							top.document.getElementById("cm-files-on").style.display = "none";
						} else if (selectedFiles[0].getAttribute("data-filetype") == ".gxl") {
							top.document.getElementById("cm-files-ed").style.display = "none";
							top.document.getElementById("cm-files-op").style.display = "block";
							top.document.getElementById("cm-files-on").style.display = "none";
						} else {
							top.document.getElementById("cm-files-ed").style.display = "none";
							top.document.getElementById("cm-files-op").style.display = "block";
							top.document.getElementById("cm-files-on").style.display = "block";
						}
						if (selectedFiles[0].getAttribute("data-filetype") == ".zip") {
							top.document.getElementById("cm-files-filter-uz").style.display = "block";
						} else {
							top.document.getElementById("cm-files-filter-uz").style.display = "none";
						}
						top.document.getElementById("cm-files-cu").style.display = "block";
						top.document.getElementById("cm-files-rn").style.display = "block";
						top.document.getElementById("cm-files-de").style.display = "block";
						top.document.getElementById("cm-files-co").style.display = "block";
						top.document.getElementById("cm-files-do").style.display = "block";
					}
				} else {
					top.document.getElementById("cm-files-cu").style.display = "block";
					top.document.getElementById("cm-files-de").style.display = "block";
					top.document.getElementById("cm-files-co").style.display = "block";
					top.document.getElementById("cm-files-do").style.display = "block";
					top.document.getElementById("cm-files-ed").style.display = "none";
					top.document.getElementById("cm-files-rn").style.display = "none";
					top.document.getElementById("cm-files-filter-uz").style.display = "none";
					top.document.getElementById("cm-files-op").style.display = "none";
					top.document.getElementById("cm-files-on").style.display = "none";
				}
				if (document.getElementById("loc").innerHTML == "/") {
					top.document.getElementById("cm-files-pd").style.display = "none";
				} else {
					top.document.getElementById("cm-files-pd").style.display = "block";
				}
				if (window.frameElement.id == "desktop-explorer") {
					top.document.getElementById("cm-files-on").style.display = "none";
					top.document.getElementById("cm-files-pd").style.display = "none";
				}
				if (folderEditable == "true") {
					top.openContextMenu(getAbsolutePosition(e), window.frameElement, false, true);
				} else {
					top.openContextMenu(getAbsolutePosition(e), window.frameElement, false, false);
				}
				top.setElementFunctionInFrame("cm-files-re", window.frameElement);
				top.setElementFunctionInFrame("cm-files-pd", window.frameElement);
				top.setElementFunctionInFrame("cm-files-ed", window.frameElement);
				top.setElementFunctionInFrame("cm-files-do", window.frameElement);
				top.setElementFunctionInFrame("cm-files-de", window.frameElement);
				top.setElementFunctionInFrame("cm-files-rn", window.frameElement);
				top.setElementFunctionInFrame("cm-files-co", window.frameElement);
				top.setElementFunctionInFrame("cm-files-pa", window.frameElement);
				top.setElementFunctionInFrame("cm-files-pr", window.frameElement);
				top.setElementFunctionInFrame("cm-files-uz", window.frameElement);
				top.setElementFunctionInFrame("cm-files-op", window.frameElement);
				top.setElementFunctionInFrame("cm-files-on", window.frameElement);
				top.setElementFunctionInFrame("cm-files-cu", window.frameElement);
				top.setElementFunctionInFrame("cm-files-ne-fo", window.frameElement);
				top.setElementFunctionInFrame("cm-files-ne-fi", window.frameElement);
				top.setElementFunctionInFrame("cm-files-ne-up", window.frameElement);
			});
			
			function cmAction(action, menuX, menuY) {
				var loc = document.getElementById("loc").innerHTML,
					selectedFiles = document.getElementsByClassName("selected"),
					selectedFileNames = "";
				
				for (i = 0; i < selectedFiles.length; i++) {
					if (selectedFiles[i].getAttribute("data-name") !== "..") {
						if (selectedFiles[i].getAttribute("data-filetype") == "dir") {
							var filetype = "dir";
						} else {
							var filetype = "file";
						}
						selectedFileNames += filetype + ":" + loc + selectedFiles[i].getAttribute("data-name") + "|";
					}
				}
				
				selectedFileNames = selectedFileNames.slice(0,-1);
				
				if (action == "cm-files-re") {
					location.reload();
				} else if (action == "cm-files-pd") {
					if (loc == "/") {
						window.location = "explorer.php?loc=/";
					} else {
						var parentdirectory = loc.split('/').slice(0, -2).join('/');
						if (parentdirectory == "") {
							window.location = "explorer.php?loc=/";
						} else {
							window.location = "explorer.php?loc=" + parentdirectory + "/";
						}
					}
				} else if (action == "cm-files-ed") {
					if (selectedFiles.length == 1) {
						if (selectedFiles[0].getAttribute("data-filetype") !== "dir") {
							top.createWindow("edit.php?loc=" + loc + "&file=" + selectedFiles[0].getAttribute("data-name"),undefined,undefined,+menuX-300,+menuY);
						}
					}
				} else if (action == "cm-files-do") {
					var downloadFrame = document.getElementById("download-frame");
					if (selectedFiles.length > 0) {
						downloadFrame.src = "download.php?loc=" + loc + "&files=" + selectedFileNames;
					} else {
						downloadFrame.src = "download.php?loc=" + loc;
					}
				} else if (action == "cm-files-de") {
					if (selectedFiles.length > 0) {
						var filesData = selectedFileNames.split('|');
						top.createWindow("confirm.php?title=Delete " + selectedFiles.length + " items%3F&action=delete.php?loc=" + loc + "%26files=" + selectedFileNames + "%26frameToReload=" + window.frameElement.id,325,200,'center','center',true);
					}
				} else if (action == "cm-files-rn") {
					if (selectedFiles.length == 1) {
						var selectedName = selectedFiles[0].getElementsByClassName("file__name")[0],
							preName = selectedName.innerHTML;
						if (selectedFiles[0].getAttribute("data-name") !== selectedName.innerHTML) {
							selectedName.innerHTML = selectedFiles[0].getAttribute("data-name");
						}
						var oldName = selectedName.innerHTML;
						if (selectedName.innerHTML !== "..") {
							selectedFiles[0].setAttribute("data-naming","true");
							selectedName.contentEditable = "true";
							selectedName.focus();
							selectedName.spellcheck = false;
							document.execCommand('selectAll',false,null);
							selectedName.addEventListener("keydown", function(e) {
								if (e.keyCode == 13 || e.which == 13) {
									e.preventDefault();
									selectedName.blur();
								}
							});
							selectedName.onblur = function() {
								selectedName.contentEditable = "false";
								if (selectedName.innerHTML !== oldName) {
									selectedName.innerHTML = selectedName.innerHTML.replace(/[\/\\?%*:|"<>]|\.+$|(>)|(<)/g, '');
									if (getFileByName(selectedName.innerHTML) || selectedName.innerHTML == "") {
										selectedName.innerHTML = oldName;
									} else {
										frameAction("rename.php?loc=" + loc + "&file=" + selectedFileNames + "&newname=" + selectedName.innerHTML, "Renaming...");
										window.setTimeout(function() {
											selectedName.parentElement.setAttribute("data-naming","false");
										},500);
										selectedName.parentElement.setAttribute("data-name", selectedName.innerHTML);
										var today = new Date();
										var filetime = pad_with_zeroes((today.getMonth()+1),2) + "/" + pad_with_zeroes(today.getDate(),2) + "/" + pad_with_zeroes(today.getFullYear(),4) + ", " + pad_with_zeroes(today.getHours(),2) + ":" + pad_with_zeroes(today.getMinutes(),2) + ":" + pad_with_zeroes(today.getSeconds(),2);
										selectedName.parentElement.title = selectedName.innerHTML + "\r\nModified: " + filetime;
										if (selectedName.parentElement.getAttribute("data-filetype") !== "dir") {
											var selectedIcon = selectedName.parentElement.getElementsByClassName("file__icon")[0];
											var dotSplit = selectedName.innerHTML.split('.');
											if (dotSplit.length == 1) {
												selectedIcon.innerHTML = '<i class="fa fa-file-o" aria-hidden="true"></i>';
												selectedName.parentElement.setAttribute("data-filetype", "file");
											} else {
												var extension = dotSplit[dotSplit.length-1];
												if (extension == "png" || extension == "jpg" || extension == "gif" || extension == "bmp" || extension == "ico") {
													selectedIcon.innerHTML = '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
												} else if (extension == "txt") {
													selectedIcon.innerHTML = '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
												} else if (extension == "php" || extension == "html" || extension == "xml" || extension == "js" || extension == "css") {
													selectedIcon.innerHTML = '<i class="fa fa-file-code-o" aria-hidden="true"></i>';
												} else if (extension == "wav" || extension == "mp3") {
													selectedIcon.innerHTML = '<i class="fa fa-file-audio-o" aria-hidden="true"></i>';
												} else if (extension == "xlsx" || extension == "xlsm") {
													selectedIcon.innerHTML = '<i class="fa fa-file-excel-o" aria-hidden="true"></i>';
												} else if (extension == "pdf") {
													selectedIcon.innerHTML = '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>';
												} else if (extension == "docx" || extension == "docm") {
													selectedIcon.innerHTML = '<i class="fa fa-file-word-o" aria-hidden="true"></i>';
												} else if (extension == "tar" || extension == "zip" || extension == "gz" || extension == "7z" || extension == "s7z" || extension == "jar" || extension == "rar" || extension == "tgz") {
													selectedIcon.innerHTML = '<i class="fa fa-file-archive-o" aria-hidden="true"></i>';
												} else if (extension == "pptx" || extension == "pptm") {
													selectedIcon.innerHTML = '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>';
												} else if (extension == "mov" || extension == "qt" || extension == "mp4" || extension == "m4p" || extension == "m4v") {
													selectedIcon.innerHTML = '<i class="fa fa-file-video-o" aria-hidden="true"></i>';
												} else if (extension == "gxl") {
													selectedIcon.innerHTML = '<i class="fa fa-question" aria-hidden="true"></i>';
												} else {
													selectedIcon.innerHTML = '<i class="fa fa-file-o" aria-hidden="true"></i>';
												}
												selectedName.parentElement.setAttribute("data-filetype", "." + extension);
											}
										}
									}
								} else {
									selectedName.innerHTML = preName;
								}
							}
						}
					}
				} else if (action == "cm-files-ne-fo") {
					var filename = newLocalFile("new folder","dir");
					for (i = 0; i < selectedFiles.length; i++) {
						selectedFiles[0].classList.remove("selected");
					}
					getFileByName(filename).classList.add("selected");
					cmAction("cm-files-rn");
					frameAction("create.php?loc=" + loc + "&filetype=dir&name=" + filename, "Creating...");
				} else if (action == "cm-files-ne-fi") {
					var filename = newLocalFile("new file","file");
					for (i = 0; i < selectedFiles.length; i++) {
						selectedFiles[0].classList.remove("selected");
					}
					getFileByName(filename).classList.add("selected");
					cmAction("cm-files-rn");
					frameAction("create.php?loc=" + loc + "&filetype=file&name=" + filename, "Creating...");
				} else if (action == "cm-files-ne-up") {
					document.getElementById("file-upload").click();
				} else if (action == "cm-files-co") {
					top.document.getElementById("clipboard").innerHTML = selectedFileNames;
					top.document.getElementById("clipboard").setAttribute("data-action","copy");
				} else if (action == "cm-files-cu") {
					top.document.getElementById("clipboard").innerHTML = selectedFileNames;
					top.document.getElementById("clipboard").setAttribute("data-action","cut");
					var allFiles = document.getElementsByClassName("file");
					for (i = 0; i < allFiles.length; i++) {
						if (allFiles[i].classList.contains("selected")) {
							allFiles[i].classList.add("faded");
						} else {
							allFiles[i].classList.remove("faded");
						}
					}
				} else if (action == "cm-files-pa") {
					var clipboard = top.document.getElementById("clipboard");
					if (clipboard.innerHTML !== "") {
						var filesToCreate = clipboard.innerHTML.split("|"),
							continueToPaste = true;
						filesToCreate.forEach(function(file){
							var fileSplit = file.split(":"),
								locSplit = fileSplit[1].split("/"),
								fileName = locSplit[locSplit.length-1];
							if (getFileByName(fileName)) {
								continueToPaste = false;
							}
						});
						if (continueToPaste) {
							filesToCreate.forEach(function(file){
								var fileSplit = file.split(":"),
									locSplit = fileSplit[1].split("/"),
									fileName = locSplit[locSplit.length-1];
								newLocalFile(fileName,fileSplit[0]);
							});
							if (clipboard.getAttribute("data-action") == "cut") {
								frameAction("move.php?files=" + top.document.getElementById("clipboard").innerHTML + "&loc=" + loc,"Pasting...");
								clipboard.innerHTML = "";
							} else {
								frameAction("copy.php?loc=" + loc + "&files=" + top.document.getElementById("clipboard").innerHTML,"Pasting...");
							}
						}
					}
				} else if (action == "cm-files-uz") {
					if (selectedFiles.length == 1) {
						window.location = "unzip.php?loc=" + loc + "&file=" + selectedFileNames;
					}
				} else if (action == "cm-files-pr") {
					top.createWindow("properties.php?loc=" + loc + "&files=" + selectedFileNames,275,350,+menuX-137.5,+menuY);
				} else if (action == "cm-files-op") {
					if (selectedFiles.length == 1) {
						selectedFiles[0].click();
						selectedFiles[0].click();
					}
				} else if (action == "cm-files-on") {
					if (selectedFiles.length == 1) {
						var filename = selectedFiles[0].getAttribute("data-name");
						if (filename == '..') {
							if (loc == '/') {
								top.createWindow('explorer.php?loc=/');
							} else {
								var parentdirectory = loc.split('/').slice(0, -2).join('/');
								if (parentdirectory == "") {
									top.createWindow('explorer.php?loc=/',undefined,undefined,+menuX-300,+menuY);
								} else {
									top.createWindow('explorer.php?loc=' + parentdirectory + '/',undefined,undefined,+menuX-300,+menuY);
								}
							}
						} else if (filename == '.') {
							top.createWindow('explorer.php?loc=' + loc,undefined,undefined,+menuX-300,+menuY);
						} else {
							top.createWindow('explorer.php?loc=' + loc + filename + '/',undefined,undefined,+menuX-300,+menuY);
						}
					}
				}
			}
			document.addEventListener( "keydown", function(e) {
				if (e.keyCode == 27 || e.which == 27) {
					top.closeContextMenu();
				}
			} );
			function getFileByName(filename) {
				var allFiles = document.getElementsByClassName("file");
				for (i = 0; i < allFiles.length; i++) {
					if (allFiles[i].getAttribute("data-name") == filename) {
						return allFiles[i];
					}
				}
				return false;
			}
			function newLocalFile(filename,filetype) {
				var today = new Date();
				var filetime = pad_with_zeroes((today.getMonth()+1),2) + "/" + pad_with_zeroes(today.getDate(),2) + "/" + pad_with_zeroes(today.getFullYear(),4) + ", " + pad_with_zeroes(today.getHours(),2) + ":" + pad_with_zeroes(today.getMinutes(),2) + ":" + pad_with_zeroes(today.getSeconds(),2);
				if (getFileByName(filename)) {
					var filecounter = 1;
					while (getFileByName(filename + " (" + filecounter + ")")) {
						filecounter++;
					}
					filename = filename + " (" + filecounter + ")";
				}
				var fileBox = document.getElementById("files"),
					newFile = document.createElement('div'),
					fileIcon = '<i class="fa fa-file-o" aria-hidden="true"></i>',
					folderInfo = document.getElementById("folderInfo");
				newFile.setAttribute("data-selected","false");
				newFile.setAttribute("data-name",filename);
				newFile.ondragstart = function(event){ drag(event); };
				newFile.setAttribute("data-continue","false");
				newFile.setAttribute("draggable","true");
				newFile.setAttribute("class","file");
				newFile.setAttribute("title",filename + "\r\nModified: " + filetime);
				newFile.onmousedown = function(){ selectfile(newFile,false); };
				newFile.onclick = function(){ selectfile(newFile,true); };
				newFile.setAttribute("data-linkto","");
				var dotSplit = filename.split('.');
				if (filetype == "dir") {
					fileIcon = '<i class="fa fa-folder-o" aria-hidden="true"></i>';
					newFile.style.order = "-1";
					folderInfo.setAttribute("data-foc",+folderInfo.getAttribute("data-foc")+1);
					newFile.setAttribute("data-filetype","dir");
				} else if (dotSplit.length == 1) {
					fileIcon = '<i class="fa fa-file-o" aria-hidden="true"></i>';
					folderInfo.setAttribute("data-fic",+folderInfo.getAttribute("data-fic")+1);
					newFile.setAttribute("data-filetype","file");
				} else {
					var extension = dotSplit[dotSplit.length-1];
					if (extension == "png" || extension == "jpg" || extension == "gif" || extension == "bmp" || extension == "ico") {
						fileIcon = '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
					} else if (extension == "txt") {
						fileIcon = '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
					} else if (extension == "php" || extension == "html" || extension == "xml" || extension == "js" || extension == "css") {
						fileIcon = '<i class="fa fa-file-code-o" aria-hidden="true"></i>';
					} else if (extension == "wav" || extension == "mp3") {
						fileIcon = '<i class="fa fa-file-audio-o" aria-hidden="true"></i>';
					} else if (extension == "xlsx" || extension == "xlsm") {
						fileIcon = '<i class="fa fa-file-excel-o" aria-hidden="true"></i>';
					} else if (extension == "pdf") {
						fileIcon = '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>';
					} else if (extension == "docx" || extension == "docm") {
						fileIcon = '<i class="fa fa-file-word-o" aria-hidden="true"></i>';
					} else if (extension == "tar" || extension == "zip" || extension == "gz" || extension == "7z" || extension == "s7z" || extension == "jar" || extension == "rar" || extension == "tgz") {
						fileIcon = '<i class="fa fa-file-archive-o" aria-hidden="true"></i>';
					} else if (extension == "pptx" || extension == "pptm") {
						fileIcon = '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>';
					} else if (extension == "mov" || extension == "qt" || extension == "mp4" || extension == "m4p" || extension == "m4v") {
						fileIcon = '<i class="fa fa-file-video-o" aria-hidden="true"></i>';
					} else if (extension == "gxl") {
						fileIcon = '<i class="fa fa-question" aria-hidden="true"></i>';
					} else {
						fileIcon = '<i class="fa fa-file-o" aria-hidden="true"></i>';
					}
					folderInfo.setAttribute("data-fic",+folderInfo.getAttribute("data-fic")+1);
					newFile.setAttribute("data-filetype","." + extension);
				}
				folderInfo.innerHTML = "- " + folderInfo.getAttribute("data-foc") + " folders and " + folderInfo.getAttribute("data-fic") + " files";
				newFile.innerHTML = '<div class="file__icon">' + fileIcon + '</div><div class="file__name" contenteditable="false">' + filename + '</div>';
				fileBox.appendChild(newFile);
				return filename;
			}
			function removeFilesByString(filestring) {
				var files = filestring.split('|'),
					folderInfo = document.getElementById("folderInfo");
				files.forEach(function(file) {
					var filesplit = file.split(':');
					var filepath = filesplit[1].split('/');
					var filename = filepath[filepath.length-1];
					if (getFileByName(filename) !== false) {
						getFileByName(filename).remove();
						if (filesplit[0] == "dir") {
							folderInfo.setAttribute("data-foc",+folderInfo.getAttribute("data-foc")-1);
						} else {
							folderInfo.setAttribute("data-fic",+folderInfo.getAttribute("data-fic")-1);
						}
					}
				});
				folderInfo.innerHTML = "- " + folderInfo.getAttribute("data-foc") + " folders and " + folderInfo.getAttribute("data-fic") + " files";
			}
			var actionQueue = [];
			function frameAction(action, infoText) {
				var actionFrame = document.getElementById("action-frame"),
					statusText = document.getElementById("status");
				actionQueue.push(action + ";" + infoText);
				if (actionFrame.getAttribute("data-idle") == "true") {
					actionFrame.setAttribute("data-idle","false");
					var nextAction = actionQueue[0].split(';');
					actionFrame.src = nextAction[0];
					statusText.innerHTML = nextAction[1];
					actionQueue.shift();
					actionFrame.onload = function() {
						statusText.innerHTML = "";
						var frameContent = (this.contentWindow.document || this.contentDocument);
						if (frameContent.body.innerHTML !== '') {
							console.error(frameContent.body.innerHTML);
							statusText.innerHTML = "An error occurred.";
							location.reload();
						}
						if (actionQueue.length > 0) {
							var nextAction = actionQueue[0].split(':');
							this.src = nextAction[0];
							statusText.innerHTML = nextAction[1];
							actionQueue.shift();
						} else {
							this.setAttribute("data-idle","true");
						}
					};
				}
			}
			function pad_with_zeroes(number, length) {
			
			    var my_string = '' + number;
			    while (my_string.length < length) {
			        my_string = '0' + my_string;
			    }
			
			    return my_string;
			
			}
			<?php
				if (isset($_GET['errors'])) {
					if ($_GET['errors'] !== '') {
						echo 'console.error("' . $_GET['errors'] . '");';
					}
				}
			?>
		</script>
	</body>
</html>