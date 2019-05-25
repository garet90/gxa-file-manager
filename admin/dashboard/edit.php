<?php
	require 'auth.php';
	if ($usercheck && $passcheck) { } else {
		die();
	}
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
		<title><div class="windowicon"><i class="fa fa-file-o" aria-hidden="true"></i></div><?php echo $_GET['file']; ?> - Document Editor</title>
		<style>
		body,html {
			padding: 0;
			margin: 0;
		}
		.editorarea {
			resize: none;
			height: 100%;
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
		#writeArea {
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			bottom: 25px;
		}
		footer {
			position: fixed;
			left: 0;
			right: 0;
			font-size: 10px;
			color: #666;
			user-select: none;
			z-index: 1;
			font-family: Sans-serif;
			overflow: hidden;
			box-sizing: border-box;
			bottom: 0;
			border-top: 1px solid white;
			background-color: #E0E6F8;
			height: 28px;
			padding: 6px 10px;
			line-height: 15px;
		}
		#action-frame {
			display: none;
		}
		#botText, #infoText {
			display: inline-block;
		}
		#botText {
			float: left;
		}
		#infoText {
			float: right;
		}
		</style>
	</head>
	<body>
		<iframe src="about:blank" id="action-frame" name="action-frame"></iframe>
		<form id="edit-form" method="post" action="save.php" target="action-frame">
			<input type="hidden" name="loc" value="<?php echo $_GET['loc'] ?>" />
			<input type="hidden" name="file" value="<?php echo $_GET['file'] ?>" />
			<div id="writeArea">
				<textarea class="editorarea" id="data" name="data" onkeyup="setIndexIndicator(this);" onmouseup="this.onkeyup();" onscroll="changeNumberCol(this);" spellcheck="false"><?php
					echo str_replace("<","&" . "lt;",str_replace(">","&" . "gt;",$filecontents));
				?></textarea>
			</div>
		</form>
		<footer>
			<div id="botText">Line #, Column #</div>
			<div id="infoText"></div>
		</footer>
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
			var keys = [];
			document.onkeydown = function(e) {
				var key = e.keyCode || e.which;
				keys[key] = true;
				if (keys[17] && keys[83]) {
					e.preventDefault();
					cmAction("cm-edit-sa");
				}
			}
			document.onkeyup = function(e) {
				var key = e.keyCode || e.which;
				keys[key] = false;
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
							if (keys[16]) {
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
				top.openContextMenu(getAbsolutePosition(e), window.frameElement, false, false, true);
				top.setElementFunctionInFrame("cm-edit-sa", window.frameElement);
				top.setElementFunctionInFrame("cm-edit-se", window.frameElement);
				top.setElementFunctionInFrame("cm-edit-un", window.frameElement);
				top.setElementFunctionInFrame("cm-edit-re", window.frameElement);
				top.setElementFunctionInFrame("cm-edit-cu", window.frameElement);
				top.setElementFunctionInFrame("cm-edit-co", window.frameElement);
				top.setElementFunctionInFrame("cm-edit-pa", window.frameElement);
			});
			
			function cmAction(action, menuX, menuY) {
				var dataEditor = document.getElementById("data");
				if (action == "cm-edit-sa") {
					document.getElementById("edit-form").submit();
					document.getElementById("infoText").innerHTML = "Saving...";
					document.getElementById("action-frame").onload = function() {
						document.getElementById("infoText").innerHTML = "Saved";
						window.setTimeout(function(){
							document.getElementById("infoText").innerHTML = "";
						},1000);
					};
				} else if (action == "cm-edit-se") {
					dataEditor.focus();
					dataEditor.setSelectionRange(0, dataEditor.value.length);
					dataEditor.onkeyup();
				} else if (action == "cm-edit-un") {
					dataEditor.focus();
					document.execCommand("undo");
				} else if (action == "cm-edit-re") {
					dataEditor.focus();
					document.execCommand("redo");
				} else if (action == "cm-edit-cu") {
					dataEditor.focus();
					document.execCommand("cut");
				} else if (action == "cm-edit-co") {
					dataEditor.focus();
					document.execCommand("copy");
				} else if (action == "cm-edit-pa") {
					dataEditor.focus();
					document.execCommand("paste");
				}
			}
			
			document.addEventListener( "keydown", function(e) {
				if (e.keyCode == 27 || e.which == 27) {
					top.closeContextMenu();
				}
			});
			
			document.addEventListener( "click", function(e) {
				top.closeContextMenu();
			});
		</script>
	</body>
</html>