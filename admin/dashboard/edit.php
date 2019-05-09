<?php
	require 'auth.php';
	$filecontents = file_get_contents ('../../' . $_GET['loc'] . '/' . $_GET['file']);
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
		<title>Document Editor</title>
		<style>
		body,html {
			padding: 1px;
		}
		.editorarea {
			resize: none;
			height: 80vh;
			width: 100%;
			margin-bottom: -2px;
			margin-top: -1px;
			white-space: pre;
			background: url(img/linenumbers.png);
			background-attachment: scroll;
			background-repeat: no-repeat;
			background-position: left top;
			padding-left: 35px;
			padding-top: 10px;
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			-webkit-box-sizing: border-box;
			border: none;
			overflow: auto;
			outline: none;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none;
			tab-size: 4;
			-moz-tab-size: 4;
			-o-tab-size: 4;
			font-size: 12px;
			line-height: 16px;
			font-family: monospace;
			text-size-adjust: none;
		}
		.wrapper {
			padding: 2px;
			background-color: #CDCDCD;
		}
		.inner {
			background-color: white;
			border: 1px solid white;
			font-size: 8pt;
			font-family: Sans-serif;
			color: #3D3D3D;
			overflow: hidden;
		}
		.inner.top {
			padding: 8px;
			background-color: #e6EEEE;
			color: black;
			margin-bottom: 2px;
			height: 10px;
			position: relative;
		}
		.button {
			cursor: pointer;
			font-size: 10px;
			font-weight: normal;
			margin-top: -1px;
		}
		div.button {
			float: left;
			text-align: left;
		}
		input.button {
			background: none;
			border: none;
			float: right;
			text-align: right;
			padding: 0;
		}
		#botText {
			font-size: 8pt;
			margin: 5px 0px;
			color: #3D3D3D;
		}
		.title {
			position: absolute;
			top: 0;
			margin: 0;
			padding: 8px 0;
			left: 50%;
			text-align: center;
			width: 200px;
			margin-left: -100px;
			font-weight: bold;
			font-size: 10px;
			margin-top: -1px;
		}
		.locview {
			font-size: 8pt;
			color: #3D3D3D;
			margin: 10px 0;
		}
		</style>
	</head>
	
	<body onload="top.inload('stop')">
		<div class="wrapper">
			<div class="inner top">
				<div class="button" onclick='window.location = "explorer.php?loc=<?php echo $_GET['loc'] ?>"; top.inload("start");'>Back</div>
				<p class="title"><?php echo $_GET['file']; ?></p>
				<form method="post" action="save.php">
					<input type="submit" class="button" value="Save" onclick="top.inload('start')" />
			</div>
			<div class="inner">
				<input type="hidden" name="loc" value="<?php echo $_GET['loc'] ?>" />
				<input type="hidden" name="file" value="<?php echo $_GET['file'] ?>" />
				<div id="writeArea">
					<textarea class="editorarea" id="data" name="data" onkeyup="setIndexIndicator(this);" onmouseup="this.onkeyup();" onscroll="changeNumberCol(this);" spellcheck="false"><?php
						echo str_replace("<","&" . "lt;",str_replace(">","&" . "gt;",$filecontents));
					?></textarea>
				</div>
				</form>
			</div>
		</div>
		<div id="botText">Line #, Column #</div>
		<?php
		$explodedURL = explode('/', substr($_GET['loc'],1));
		$prevURLstring = "/";
		foreach ($explodedURL as $key=>$URL) {
			$explodedURL[$key] = "<a href='explorer.php?loc=" . $prevURLstring . $URL . "' onclick=" . '"' . "top.inload('start')" . '">' . $URL . "</a>";
			$prevURLstring = $prevURLstring . $URL . '/';
			$prevURLstring = preg_replace('/(\/+)/','/',$prevURLstring);
		}
		$joinedURL = join('/', $explodedURL);
		?>
		<div class="locview"><a href="explorer.php?loc=/" onclick="top.inload('start')">root</a>/<?php echo $joinedURL . $_GET['file'] . ' - ' . number_format(strlen($filecontents)) . ' characters, ' . number_format(substr_count( $filecontents, "\n" )+1) . ' lines, taking up ' . formatBytes(strlen($filecontents)); ?></div>
		<script type="text/javascript">
			function getLineNumberAndColumnIndex(textarea,selectLocation){
				var textLines = textarea.value.substr(0, selectLocation).split("\n");
				var currentLineNumber = textLines.length;
				var currentColumnIndex = textLines[textLines.length-1].length;
				return [currentLineNumber,currentColumnIndex];
			}
			function setIndexIndicator(textarea){
				var s = getLineNumberAndColumnIndex(textarea, textarea.selectionStart),
				se = getLineNumberAndColumnIndex(textarea, textarea.selectionEnd);
				if (s[0] !== se[0] && s[1] !== se[1]) {
					document.getElementById("botText").innerHTML = "Line " + s[0] + ", Column " + s[1] + " to Line " + se[0] + ", Column " + se[1];
				} else {
					document.getElementById("botText").innerHTML = "Line " + s[0] + ", Column " + s[1];
				}
			}
			var textareas = document.getElementsByTagName('textarea');
			var count = textareas.length;
			var shifted = false;
			document.onkeydown = function(e) {
				if(e.keyCode==16 || e.which==16) { shifted = true; }
			}
			document.onkeyup = function(e) {
				if(e.keyCode==16 || e.which==16) { shifted = false; }
			}
			for(var i=0;i<count;i++){
				textareas[i].onkeydown = function(e){
					if(e.keyCode==9 || e.which==9){
						e.preventDefault();
						var selectStartIndex = getLineNumberAndColumnIndex(this,this.selectionStart),
						selectEndIndex = getLineNumberAndColumnIndex(this,this.selectionEnd);
						if (selectStartIndex[0] == selectEndIndex[0]) {
							var s = this.selectionStart;
							this.value = this.value.substring(0,this.selectionStart) + "\t" + this.value.substring(this.selectionEnd);
							this.selectionEnd = s+1;
						} else {
							var lines = this.value.split("\n"),
							s = this.selectionStart,
							se = this.selectionEnd;
							sef = 0;
							if (shifted) {
								var td = 0;
								for (i = selectStartIndex[0]-1; i < selectEndIndex[0]; i++) {
									if (lines[i][0] == "\t") {
										lines[i] = lines[i].substr(1,lines[i].length);
										td = td + 1;
									}
								}
								sef = se - td;
							} else {
								for (i = selectStartIndex[0]-1; i < selectEndIndex[0]; i++) {
									lines[i] = "\t" + lines[i];
								}
								sef = (selectEndIndex[0] - selectStartIndex[0]) + se + 1;
							}
							this.value = lines.join("\n");
							this.selectionStart = s;
							this.selectionEnd = sef;
						}
					}
					if(e.keyCode==13 || e.which==13){
						if (<?php echo $useautotab; ?>) {
							e.preventDefault();
							var locIndex = getLineNumberAndColumnIndex(this, this.selectionStart),
							tabCount = this.value.split("\n")[locIndex[0]-1].split("\t").length-1,
							s = this.selectionStart,
							tabStr = "";
							for (i = 0; i < tabCount; i++) {
								tabStr = tabStr + "\t";
							}
							this.value = this.value.substring(0,this.selectionStart) + "\n" + tabStr + this.value.substring(this.selectionEnd);
							this.selectionEnd = s+1+tabCount; 
						}
					}
				}
			}
			function changeNumberCol(textarea) {
				textarea.style.backgroundPositionY = "-" + textarea.scrollTop + "px";
				if (checkBrowser() !== "Firefox") {
					textarea.style.backgroundPositionX = "-" + textarea.scrollLeft + "px";
				}
			}
			function checkBrowser(){
				c = navigator.userAgent.search("Chrome");
				f = navigator.userAgent.search("Firefox");
				m8 = navigator.userAgent.search("MSIE 8.0");
				m9 = navigator.userAgent.search("MSIE 9.0");
				if (c > -1) {
					browser = "Chrome";
				} else if (f > -1) {
					browser = "Firefox";
				} else if (m9 > -1) {
					browser ="MSIE 9.0";
				} else if (m8 > -1) {
					browser ="MSIE 8.0";
				}
				return browser;
			}
		</script>
	</body>
</html>