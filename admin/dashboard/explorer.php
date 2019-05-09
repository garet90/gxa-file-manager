<?php
require 'auth.php';
function formatBytes($size, $precision = 2)
{
	$base = log($size, 1024);
	$suffixes = array('B', 'KB', 'MB', 'GB', 'TB');   

	return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>File Explorer</title>
		<script src="jquery-3.2.1.min.js"></script>
		<script src="jquery.tablesorter.min.js"></script>
		<style>
			input[type=button] {
				appearance: none;
				-webkit-appearance: none;
				-moz-appearance: none;
			}
			.center {
				padding: 2px;
				background-color: #CDCDCD;
				margin-bottom: 10px;
				word-break: break-all;
				width: 300px;
				position: absolute;
				left: 50%;
				top: 50%;
				margin-left: -150px;
				margin-top: -150px;
				display: none;
				position: fixed;
				-webkit-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
				-moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
				box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
			}
			.boxinner {
				background-color: white;
				border: 1px solid white;
				font-size: 8pt;
				padding: 8px 12px;
				font-family: Sans-serif;
				color: #3D3D3D;
				min-height: 35px;
			}
			.boxinner.first {
				font-weight: bold;
				background-color: #e6EEEE;
				color: black;
				height: 8pt;
				margin-bottom: 2px;
				min-height: 0;
			}
			.boxinner p {
				margin: 0;
				padding: 0;
				text-align: center;
			}
			.deleteTitle {
				width: 100%;
				font-weight: bold;
				font-size: 10pt;
				text-align: center;
				font-family: Sans-serif;
				margin-bottom: 0;
			}
			.delbut {
				clear: both;
				padding: 10px;
				cursor: pointer;
				height: 10px;
				line-height: 10px;
				width: auto;
				margin: 0px 15px 15px 15px;
				position: absolute;
				bottom: 0;
				font-family: Sans-serif;
				font-size: 8pt;
				font-weight: bold;
				background-color: white;
				color: #3D3D3D;
				border: 1px solid #CDCDCD;
			}
			.delbut:hover {
				background-color: #e6EEEE;
			}
			.delbut.no {
				left: 0;
			}
			.delbut.yes {
				right: 0;
			}
			input.delbut {
				padding-bottom: 15px;
				padding-top: 15px;
				line-height: 0;
			}
			.sortIcon {
				float: right;
				padding-right: 5px;
			}
			/* tables */
			table.tablesorter {
				font-family:arial;
				background-color: #CDCDCD;
				margin:10px 0pt 15px;
				font-size: 8pt;
				width: 100%;
				text-align: left;
			}
			table.tablesorter thead tr th, table.tablesorter tfoot tr th {
				background-color: #e6EEEE;
				border: 1px solid #FFF;
				font-size: 8pt;
				padding: 4px;
			}
			table.tablesorter thead tr .header {
				background-image: url(bg.gif);
				background-repeat: no-repeat;
				background-position: center right;
				cursor: pointer;
			}
			table.tablesorter tbody td {
				color: #3D3D3D;
				padding: 4px;
				background-color: #FFF;
				vertical-align: top;
			}
			table.tablesorter tbody tr.odd td {
				background-color:#F0F0F6;
			}
			table.tablesorter thead tr .headerSortUp {
				background-image: url(asc.gif);
			}
			table.tablesorter thead tr .headerSortDown {
				background-image: url(desc.gif);
			}
			table.tablesorter thead tr .headerSortDown, table.tablesorter thead tr .headerSortUp {
				background-color: #8dbdd8;
			}
			.sortable {
				cursor: pointer;
			}
			.botText {
				margin-right: 10px;
				display: inline-block;
				font-size: 8pt;
			}
			#filetable {
				margin-bottom: 0;
			}
			.locview {
				font-size: 8pt;
				color: #3D3D3D;
				margin: 10px 0;
			}
			.textinput {
				margin-bottom: 50px;
			}
			#fileBox {
				margin: auto;
				margin-bottom: 50px;
				width: 288px;
			}
			.filesn {
				display: none;
			}
			.noselect {
				cursor: default;
				color: #A4A4A4;
			}
			#newFileName {
				margin-bottom: 50px;
			}
			.littleIndent {
				padding-left: 3px;
			}
			#verifyJS {
				display: none;
			}
			#errorbox {
				position: fixed;
				display: <?php
					if (isset($_GET['errors'])) {
						echo "block";
					} else {
						echo "none";
					}
				?>;
				-webkit-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
				-moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
				box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
			}
			.errorbutton {
				clear: both;
				padding: 10px;
				cursor: pointer;
				height: 10px;
				line-height: 10px;
				position: absolute;
				bottom: 0;
				font-family: Sans-serif;
				font-size: 8pt;
				font-weight: bold;
				background-color: white;
				color: #3D3D3D;
				margin-bottom: 15px;
				left: 50%;
				width: 100px;
				margin-left: -62px;
				text-align: center;
				border: 1px solid #CDCDCD;
			}
			.errorbutton:hover {
				background-color: #e6EEEE;
			}
			.errortext {
				margin-bottom: 50px !important;
			}
			#moveFrame {
				border: 0;
				width: 100%;
				height: 100px;
				padding-bottom: 50px;
			}
		</style>
		<link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.css">
	</head>
	<body onLoad="top.inload('stop')">
		<span id="verifyJS">false</span>
		<div class="center" id="errorbox">
			<div class="boxinner first">
				<p>An error occurred</p>
			</div>
			<div class="boxinner">
				<p class="errortext"><?php
					if (isset($_GET['errors'])) {
						echo $_GET['errors'];
					}
				?></p>
				<div class="errorbutton" onclick="this.parentElement.parentElement.style.display = 'none';">OK</div>
			</div>
		</div>
		<div class="center" id="confirmDelete">
			<div class="boxinner first">
				<p>Delete <span id="itemdel">file</span>?</p>
			</div>
			<div class="boxinner">
				<p class="errortext">Are you sure? This item will be gone forever.</p>
				<div class="delbut no" onClick="this.parentElement.parentElement.style.display = 'none'">Cancel</div>
				<div class="delbut yes" onClick="deleteConfirmed();">Delete</div>
			</div>
		</div>
		<div class="center" id="uploadItem">
			<div class="boxinner first">
				<p>Upload file</p>
			</div>
			<div class="boxinner">
				<form method="post" action="upload.php" enctype="multipart/form-data">
					<input type="hidden" name="loc" value="<?php echo $_GET['loc'] ?>" />
					<input type="file" id="fileBox" name="file" />
					<div class="delbut no" onClick="this.parentElement.parentElement.parentElement.style.display = 'none'; window.uploadToggle = 0;">Cancel</div>
					<input type="submit" onClick="top.inload('start')" value="Upload" class="delbut yes" />
				</form>
			</div>
		</div>
		<div class="center" id="copyItem">
			<div class="boxinner first">
				<p>Copy <span id="itemcopy">file</span>?</p>
			</div>
			<div class="boxinner">
				<p class="errortext">Copies will be named "copy.[original file name]".</p>
				<div class="delbut no" onClick="this.parentElement.parentElement.style.display = 'none'; window.copyToggle = 0;">Cancel</div>
				<div class="delbut yes" onClick="copyConfirmed();">Copy</div>
			</div>
		</div>
		<div class="center" id="renameItem">
			<div class="boxinner first">
				<p>Rename file</p>
			</div>
			<div class="boxinner">
				<input type="hidden" name="loc" value="<?php echo $_GET['loc'] ?>" />
				<label for="newFileName">New file name</label><br />
				<input type="text" id="newFileName" />
				<div class="delbut no" onClick="this.parentElement.parentElement.style.display = 'none'; window.renameToggle = 0;">Cancel</div>
				<div class="delbut yes" onClick="confirmRename()">Rename</div>
			</div>
		</div>
		<div class="center" id="moveItem">
			<div class="boxinner first">
				<p>Move file</p>
			</div>
			<div class="boxinner">
				<input type="hidden" name="loc" value="<?php echo $_GET['loc'] ?>" />
				<iframe src="about:blank" id="moveFrame"></iframe>
				<div class="delbut no" onClick="this.parentElement.parentElement.style.display = 'none'; window.renameToggle = 0;">Cancel</div>
				<div class="delbut yes" onClick="confirmMove()">Move Here</div>
			</div>
		</div>
		<table id="filetable" class="tablesorter">
			<thead>
				<tr>
					<th data-sorter="false"><input type="checkbox" onClick="toggleAll(this)" id="toggleAllCheck" /></th>
					<th onclick="sortChange();" class="sortable"><i class="fa fa-file-o" aria-hidden="true"></i><div class="sortIcon" id="s1"><i class="fa fa-sort" aria-hidden="true"></i></div></th>
					<th onclick="sortChange();" class="sortable">Name<div class="sortIcon" id="s2"><i class="fa fa-sort" aria-hidden="true"></i></div></th>
					<th onclick="sortChange();" class="sortable">Size<div class="sortIcon" id="s3"><i class="fa fa-sort" aria-hidden="true"></i></div></th>
					<th onclick="sortChange();" class="sortable">Modified<div class="sortIcon" id="s4"><i class="fa fa-sort" aria-hidden="true"></i></div></th>
				</tr>
			</thead>
			<tbody>
				<?php
					$fileCount = 0;
					$folderCount = 0;
					$folderStorage = 0;
					$btext = '';
					$files = scandir('../../' . $_GET['loc']);
					if ($_GET['loc'] == '/') {
						unset($files[1]);
					}
					foreach ($files as $file) {
						if (is_dir ('../../' . $_GET['loc'] . '/' . $file)) {
							if ($file == '.') {
								$filename = '';
								$btext = '<div class="botText"><a onclick="' . "top.inload('start')" . '" href="?loc=' . $_GET['loc'] . '"><i class="fa fa-refresh" aria-hidden="true"></i><span class="littleIndent">Refresh</span></a></div>
								' . $btext;
							} else if ($file == '..') {
								$newUrl = preg_replace('/(\/+)/','/',$_GET['loc']);
								$splitLoc = explode('/', $newUrl);
								$removed = array_pop($splitLoc);
								if ($removed == "") {
									$remove2 = array_pop($splitLoc);
								}
								$finLoc = join('/', $splitLoc);
								$filename = '';
								$btext = '<div class="botText"><a onclick="' . "top.inload('start')" . '" href="?loc=' . $finLoc . '/' . '"><i class="fa fa-folder-open-o" aria-hidden="true"></i><span class="littleIndent">Parent Directory</span></a></div>
								' . $btext;
							} else {
								$dirbef = $_GET['loc'] . '/' . $file;
								$diraft = preg_replace('/(\/+)/','/',$dirbef);
								$filePath = $_GET['loc'] . '/' . $file;
								$filePath = preg_replace('/(\/+)/','/',$filePath);
								$filename = '<tr><td><input class="fileInput" onClick="' . "selectFile(this,'dir:" . $filePath . "')" . '" type="checkbox" /></td><td><span class="filesn">1|</span><i class="fa fa-folder-o" aria-hidden="true"></i></td><td><a onclick="' . "top.inload('start')" . '" href="?loc=' . $diraft . '">' . $file . '/</a></td><td></td><td></td></tr>';
								$folderCount = $folderCount + 1;
							}
							echo $filename;
						} else {
							$filesizeSort = filesize('../../' . $_GET['loc'] . '/' . $file);
							$filesize = formatBytes($filesizeSort);
							$filedate = date ("m/d/Y, H:i:s", filemtime('../../' . $_GET['loc'] . '/' . $file));
							$filesplit = explode('.', $file);
							if (count ($filesplit) > 1) {
								$extension = array_pop($filesplit);
								if ($extension == "png" || $extension == "jpg" || $extension == "gif" || $extension == "bmp" || $extension == "ico") {
									$fileicon = '<span class="filesn">2|</span><i class="fa fa-file-image-o" aria-hidden="true"></i>';
								} else if ($extension == "txt") {
									$fileicon = '<span class="filesn">3|</span><i class="fa fa-file-text-o" aria-hidden="true"></i>';
								} else if ($extension == "php" || $extension == "html" || $extension == "xml" || $extension == "js" || $extension == "css") {
									$fileicon = '<span class="filesn">4|</span><i class="fa fa-file-code-o" aria-hidden="true"></i>';
								} else if ($extension == "wav" || $extension == "mp3") {
									$fileicon = '<span class="filesn">5|</span><i class="fa fa-file-audio-o" aria-hidden="true"></i>';
								} else if ($extension == "xlsx" || $extension == "xlsm") {
									$fileicon = '<span class="filesn">6|</span><i class="fa fa-file-excel-o" aria-hidden="true"></i>';
								} else if ($extension == "pdf") {
									$fileicon = '<span class="filesn">7|</span><i class="fa fa-file-pdf-o" aria-hidden="true"></i>';
								} else if ($extension == "docx" || $extension == "docm") {
									$fileicon = '<span class="filesn">8|</span><i class="fa fa-file-word-o" aria-hidden="true"></i>';
								} else if ($extension == "tar" || $extension == "zip" || $extension == "gz" || $extension == "7z" || $extension == "s7z" || $extension == "jar" || $extension == "rar" || $extension == "tgz") {
									$fileicon = '<span class="filesn">9|</span><i class="fa fa-file-archive-o" aria-hidden="true"></i>';
								} else if ($extension == "pptx" || $extension == "pptm") {
									$fileicon = '<span class="filesn">10|</span><i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>';
								} else if ($extension == "mov" || $extension == "qt" || $extension == "mp4" || $extension == "m4p" || $extension == "m4v") {
									$fileicon = '<span class="filesn">11|</span><i class="fa fa-file-video-o" aria-hidden="true"></i>';
								} else {
									$fileicon = '<span class="filesn">12|</span><i class="fa fa-file-o" aria-hidden="true"></i>';
								}
							} else {
								$fileicon = '<i class="fa fa-file-o" aria-hidden="true"></i>';
							}
							$filePath = $_GET['loc'] . '/' . $file;
							$filePath = preg_replace('/(\/+)/','/',$filePath);
							echo '<tr><td><input type="checkbox" onClick="' . "selectFile(this,'file:" . $filePath . "')" . '" class="fileInput" /></td><td>' . $fileicon . '</td><td>' . $file . '</td><td><span class="filesn">' . $filesizeSort . '|</span>' . $filesize . '</td><td>' . $filedate . '</td></tr>';
							$fileCount = $fileCount + 1;
							$folderStorage = $folderStorage + $filesizeSort;
						}
					}
				?>
			</tbody>
		</table>
		<?php
		echo $btext;
		?><div class="botText">
			<a href="javascript:createItem();"><i class="fa fa-file-o" aria-hidden="true"></i><span class="littleIndent">Create</span></a>
		</div>
		<div class="botText">
			<a href="javascript:uploadItem();"><i class="fa fa-upload" aria-hidden="true"></i><span class="littleIndent">Upload</span></a>
		</div>
		<div class="botText">
			<a href="javascript:deleteConf();" id="deleteLink" class="noselect"><i class="fa fa-trash-o" aria-hidden="true"></i><span class="littleIndent">Delete</span></a>
		</div>
		<div class="botText">
			<a href="javascript:editFile();" id="editLink" class="noselect"><i class="fa fa-pencil-square-o" aria-hidden="true"></i><span class="littleIndent">Edit</span></a>
		</div>
		<div class="botText">
			<a href="javascript:renameFile();" id="renameLink" class="noselect"><i class="fa fa-pencil" aria-hidden="true"></i><span class="littleIndent">Rename</span></a>
		</div>
		<div class="botText">
			<a class="noselect" href="javascript:copyFile();" id="copyLink"><i class="fa fa-files-o" aria-hidden="true"></i><span class="littleIndent">Copy</span></a>
		</div>
		<div class="botText">
			<a class="noselect" href="javascript:moveFile();" id="moveLink"><i class="fa fa-arrows" aria-hidden="true"></i><span class="littleIndent">Move</span></a>
		</div>
		<?php
		$folderStorage = formatBytes($folderStorage);
		
		$explodedURL = explode('/', $_GET['loc']);
		$prevURLstring = "/";
		foreach ($explodedURL as $key=>$URL) {
			$explodedURL[$key] = "<a href='explorer.php?loc=" . $prevURLstring . $URL . "' onclick=" . '"' . "top.inload('start')" . '">' . $URL . "</a>";
			$prevURLstring = $prevURLstring . $URL . '/';
			$prevURLstring = preg_replace('/(\/+)/','/',$prevURLstring);
		}
		$joinedURL = join('/', $explodedURL);
		
		echo '<div class="locview"><a href="explorer.php?loc=/" onclick="' . "top.inload('start')" . '">root</a>' . $joinedURL . ' - ' . $fileCount . ' files, ' . $folderCount . ' folders, taking up ' . $folderStorage . '.</div>';
		?>
		<div class="center" id="createMenu">
			<div class="boxinner first">
				<p>Create file</p>
			</div>
			<div class="boxinner">
				<form method="post" action="create.php">
					<input type="hidden" value="<?php echo $_GET['loc'] ?>" name="loc" />
					<label>File Type</label><br />
					<input type="radio" name="filetype" value="file" id="fileType1" /><label for="fileType1">File</label><br />
					<input type="radio" name="filetype" value="directory" id="fileType2" /><label for="fileType2">Directory</label></br /><br />
					<label for="fileName">File Name</label><br /><input type="text" id="fileName" name="filename" class="textinput" />
					<input class="delbut yes" onclick="top.inload('start')" type="submit" value="Create" />
					<div class="delbut no" onClick="this.parentElement.parentElement.parentElement.style.display = 'none'; window.create = false;">Cancel</div>
				</form>
			</div>
		</div>
		<script type="text/javascript">
			//verifyJS
			document.getElementById("verifyJS").innerHTML = "true";
			//code
			var create = false;
			function createItem() {
				var createMenu = document.getElementById("createMenu");
				if (create) {
					createMenu.style.display = "none";
					create = false;
				} else {
					createMenu.style.display = "block";
					create = true;
				}
			}
			var delLoc = "";
			function deleteConf() {
				var confirmDelete = document.getElementById("confirmDelete"),
					itemdel = document.getElementById('itemdel'),
					delYes = document.getElementById('delYes');
				if (selectedFiles.length == 1) {
					var filesplit = selectedFiles[0].split('/'),
						filesp = selectedFiles[0].split(':'),
						filedir = filesp[1].split('/'),
						popped = filedir.pop(),
						filedirectory = filedir.join('/') + '/',
						filename = filesplit[filesplit.length-1];
					confirmDelete.style.display = "block";
					itemdel.innerHTML = filename;
					
					delLoc = filedirectory;
				} else if (selectedFiles.length > 1) {
					delLoc = "<?php echo $_GET['loc'] ?>";
					confirmDelete.style.display = "block";
					itemdel.innerHTML = selectedFiles.length + " items";
				}
			}
			function deleteConfirmed() {
				top.inload('start');
				if (selectedFiles.length > 0) {
					window.location = "delete.php?loc=" + delLoc + "&files=" + selectedFiles.join(',');
				} else {
					window.location = "explorer.php?loc=<?php echo $_GET['loc']; ?>&errors=You can't delete nothing!";
				}
			}
			function toggleAll(accord) {
				var array = document.getElementsByClassName("fileInput");
				
				if (accord.checked) {
					for(var ii = 0; ii < array.length; ii++)
					{

					   if(array[ii].type == "checkbox")
					   {
						  if(array[ii].className == "fileInput")
						   {
							array[ii].checked = true;
							array[ii].onclick.apply(array[ii]);
						   }


					   }
					}
				} else {
					for(var ii = 0; ii < array.length; ii++)
					{

					   if(array[ii].type == "checkbox")
					   {
						  if(array[ii].className == "fileInput")
						   {
							array[ii].checked = false;
							array[ii].onclick.apply(array[ii]);
						   }


					   }
					}
				}
			}
			function sortChange() {
				var sortObjects = 4,
					sortList = {
						'ascending': '<i class="fa fa-sort-asc" aria-hidden="true"></i>',
						'descending': '<i class="fa fa-sort-desc" aria-hidden="true"></i>',
						'none': '<i class="fa fa-sort" aria-hidden="true"></i>'
					};
				
				window.setTimeout(function(){
					sortObjects++;
					for (i = 1; i < sortObjects; i++) { 
						document.getElementById('s' + i).innerHTML = sortList[document.getElementById('s' + i).parentElement.parentElement.getAttribute('aria-sort')];
					}
				},1);
			}
			uploadToggle = 0;
			function uploadItem() {
				var uploadScreen = document.getElementById('uploadItem');
				
				if (uploadToggle == 0) {
					uploadScreen.style.display = "block";
					uploadToggle = 1;
				} else {
					uploadScreen.style.display = "none";
					uploadToggle = 0;
				}
			}
			var selectedFiles = [];
			function selectFile(x,y) {
				changeSelectStatus();
				if (x.checked || x == true) {
					if (searcharray(y,selectedFiles)) {
					} else {
						selectedFiles.push(y);
					}
				} else {
					var arrayItem = selectedFiles.indexOf(y);
					if (arrayItem > -1) {
						selectedFiles.splice(arrayItem, 1);
					}
				}
				var deleteLink = document.getElementById("deleteLink"),
					renameLink = document.getElementById("renameLink"),
					editLink = document.getElementById("editLink"),
					copyLink = document.getElementById("copyLink"),
					moveLink = document.getElementById("moveLink");
				if (selectedFiles.length > 0) {
					deleteLink.classList.remove("noselect");
					copyLink.classList.remove("noselect");
					moveLink.classList.remove("noselect");
				} else {
					deleteLink.classList.add("noselect");
					copyLink.classList.add("noselect");
					moveLink.classList.add("noselect");
				}
				if (selectedFiles.length == 1) {
					var testSplit = selectedFiles[0].split(':');
					renameLink.classList.remove("noselect");
					if (testSplit[0] == "file") {
						editLink.classList.remove("noselect");
					}
				} else {
					renameLink.classList.add("noselect");
					editLink.classList.add("noselect");
				}
			}
			function searcharray(search_for_string, array_to_search) 
			{
			    for (var i=0; i<array_to_search.length; i++) 
				{
			        if (array_to_search[i].match(search_for_string))
					{ 
						return true;
					}
			    }
			 
			    return false;
			}
			function editFile() {
				if (selectedFiles.length == 1) {
					var testSplit = selectedFiles[0].split(':');
					if (testSplit[0] == "file") {
						var fileSplit = selectedFiles[0].split('/'),
							fileName = fileSplit.pop();
						window.location = "edit.php?loc=<?php echo $_GET['loc']; ?>&file=" + fileName;
						top.inload('start');
					}
				}
			}
			renameToggle = 0;
			function renameFile() {
				if (selectedFiles.length == 1) {
					var renameItem = document.getElementById("renameItem");
					if (renameToggle == 0) {
						renameToggle = 1;
						renameItem.style.display = "block";
					} else {
						renameToggle = 0;
						renameItem.style.display = "none";
					}
				}
			}
			function confirmRename() {
				top.inload('start');
				if (selectedFiles.length == 1) {
					var newFileName = document.getElementById("newFileName").value;
					window.location = "rename.php?file=" + selectedFiles[0] + "&loc=<?php echo $_GET['loc'] ?>&newname=" + newFileName;
				} else {
					window.location = "explorer.php?loc=<?php echo $_GET['loc'] ?>&errors=You can't rename " + selectedFiles.length + " files!";
				}
			}
			var copyToggle = 0;
			function copyFile() {
				if (selectedFiles.length > 0) {
					var copyItem = document.getElementById("copyItem");
					if (copyToggle == 0) {
						copyItem.style.display = "block";
						copyToggle = 1;
						var itemCopy = document.getElementById("itemcopy");
						if (selectedFiles.length == 1) {
							var filesplit = selectedFiles[0].split('/'),
								filename = filesplit[filesplit.length-1];
							itemCopy.innerHTML = filename;
						} else if (selectedFiles.length > 1) {
							itemCopy.innerHTML = selectedFiles.length + " items";
						}
					} else {
						copyItem.style.display = "none";
						copyToggle = 0;
					}
				}
			}
			function copyConfirmed() {
				top.inload('start');
				if (selectedFiles.length > 0) {
					var selectedString = selectedFiles.join(",");
					window.location = "copy.php?loc=<?php echo $_GET['loc'] ?>&files=" + selectedString;
				} else {
					window.location = "explorer.php?loc=<?php echo $_GET['loc'] ?>&errors=You can't copy nothing!";
				}
			}
			function moveFile() {
				if (selectedFiles.length > 0) {
					var moveItem = document.getElementById("moveItem"),
						moveFrame = document.getElementById("moveFrame");
					moveItem.style.display = "block";
					moveFrame.src = "directoryexplorer.php?loc=/";
					top.inload('start');
				}
			}
			function confirmMove() {
				top.inload('start');
				var moveFrame = document.getElementById("moveFrame"),
					selectedString = selectedFiles.join(",");
				if (selectedFiles.length > 0) {
					window.location = "move.php?loc=<?php echo $_GET['loc'] ?>&moveto=" + moveFrame.contentWindow.document.getElementById("location").innerHTML + "&files=" + selectedString;
				} else {
					window.location = "explorer.php?loc=<?php echo $_GET['loc'] ?>&errors=You can't move nothing!";
				}
			}
			function changeSelectStatus() {
				var fileInput = document.getElementsByClassName("fileInput"),
					arrchecked = false,
					arrunchecked = false,
					toggleAllCheck = document.getElementById("toggleAllCheck");
				for(var ii = 0; ii < fileInput.length; ii++) {
					if (fileInput[ii].checked == true) {
						arrchecked = true;
					} else if (fileInput[ii].checked == false) {
						arrunchecked = true;
					}
				}
				if (arrchecked == true && arrunchecked == true) {
					toggleAllCheck.indeterminate = true;
					toggleAllCheck.checked = true;
				} else if (arrchecked == true && arrunchecked == false) {
					toggleAllCheck.indeterminate = false;
					toggleAllCheck.checked = true;
				} else if (arrchecked == false && arrunchecked == true) {
					toggleAllCheck.indeterminate = false;
					toggleAllCheck.checked = false;
				}
				return arrchecked + ":" + arrunchecked;
			}
		</script>
		<script type="text/javascript">
		$(function(){
			$("#filetable").tablesorter({
				sortList : [[1,0],[2,0],[3,0],[4,0]]
			});
		});
		</script>
		<script type="text/javascript">
			var verifyJS = document.getElementById("verifyJS");
			if (verifyJS.innerHTML == "false" || verifyJS.innerHTML == false) {
				top.inload('start');
				location.reload();
			}
		</script>
	</body>
</html>