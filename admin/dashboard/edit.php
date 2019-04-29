<?php
	require 'auth.php';
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
			white-space: nowrap;
			overflow: auto;
			background: url(img/linenumbers.png);
			background-attachment: local;
			background-repeat: no-repeat;
			padding-left: 35px;
			padding-top: 10px;
			line-height: 16px;
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			-webkit-box-sizing: border-box;
			border: none;
			overflow: auto;
			outline: none;
			-webkit-box-shadow: none;
			-moz-box-shadow: none;
			box-shadow: none;
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
			padding: 8px 4px 8px 4px;
			font-weight: bold;
			background-color: #e6EEEE;
			color: black;
			margin-bottom: 2px;
			height: 8pt;
		}
		.button {
			cursor: pointer;
			font-size: 8pt;
			font-weight: bold;
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
		#locIndicator {
			font-size: 12px;
			margin: 5px 0px;
		}
		</style>
	</head>
	
	<body onload="top.inload('stop')">
		<div class="wrapper">
			<div class="inner top">
				<div class="button" onclick='window.location = "explorer.php?loc=<?php echo $_GET['loc'] ?>"; top.inload("start");'>Back</div>
				<form method="post" action="save.php">
					<input type="submit" class="button" value="Save" onclick="top.inload('start')" />
			</div>
			<div class="inner">
				<input type="hidden" name="loc" value="<?php echo $_GET['loc'] ?>" />
				<input type="hidden" name="file" value="<?php echo $_GET['file'] ?>" />
				<div id="writeArea">
					<textarea class="editorarea" name="data" onkeyup="getLineNumberAndColumnIndex(this);" onmouseup="this.onkeyup();"><?php
						echo str_replace(">","&gt;",str_replace("<","&lt;",file_get_contents ('../../' . $_GET['loc'] . '/' . $_GET['file'])));
					?></textarea>
				</div>
				</form>
			</div>
		</div>
		<p id="locIndicator">Line #, Column #</p>
		<script type="text/javascript">
			function getLineNumberAndColumnIndex(textarea){
				var textLines = textarea.value.substr(0, textarea.selectionStart).split("\n");
				var currentLineNumber = textLines.length;
				var currentColumnIndex = textLines[textLines.length-1].length;
				document.getElementById("locIndicator").innerHTML = "Line " + currentLineNumber + ", Column " + currentColumnIndex;
			}
		</script>
	</body>
</html>