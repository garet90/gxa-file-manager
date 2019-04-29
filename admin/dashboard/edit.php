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
            tab-size: 4;
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
			font-weight: bold;
			background-color: #e6EEEE;
			color: black;
			margin-bottom: 2px;
			height: 8pt;
            position: relative;
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
			font-size: 8pt;
			margin: 3px 0px;
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
            font-weight: normal;
            font-size: 8pt;
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
					<textarea class="editorarea" id="data" name="data" onkeyup="getLineNumberAndColumnIndex(this);" onmouseup="this.onkeyup();" spellcheck="false"><?php
						echo str_replace("<","&" . "lt;",str_replace(">","&" . "gt;",file_get_contents ('../../' . $_GET['loc'] . '/' . $_GET['file'])));
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
            var textareas = document.getElementsByTagName('textarea');
            var count = textareas.length;
            for(var i=0;i<count;i++){
                textareas[i].onkeydown = function(e){
                    if(e.keyCode==9 || e.which==9){
                        e.preventDefault();
                        var s = this.selectionStart;
                        this.value = this.value.substring(0,this.selectionStart) + "    " + this.value.substring(this.selectionEnd);
                        this.selectionEnd = s+4; 
                    }
                }
            }
		</script>
	</body>
</html>