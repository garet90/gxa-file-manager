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
			border: 0;
			resize: none;
			height: 70vh;
			width: 99.75%;
			margin-left: -1px;
			margin-top: -1px;
			margin-bottom: -3px;
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
		</style>
	</head>
	
	<body>
		<div class="wrapper">
			<div class="inner top">
				<div class="button" onclick='window.location = "explorer.php?loc=<?php echo $_GET['loc'] ?>"'>Back</div>
				<form method="post" action="save.php">
					<input type="submit" class="button" value="Save" />
			</div>
			<div class="inner">
					<input type="hidden" name="loc" value="<?php echo $_GET['loc'] ?>" />
					<input type="hidden" name="file" value="<?php echo $_GET['file'] ?>" />
					<div id="writeArea">
						<textarea class="editorarea" name="data"><?php
							echo file_get_contents ('../../' . $_GET['loc'] . '/' . $_GET['file']);
						?></textarea>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>