<!DOCTYPE html>
<html>
	<head>
		<title><div class="windowicon"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></div><?php echo $_GET['title']; ?></title>
		<meta charset="UTF-8">
		<style>
			p {
				font-family: Sans-serif;
				font-size: 10pt;
				text-align: center;
				color: #444;
			}
			.button {
				padding: 10px;
				width: 75px;
				text-align: center;
				display: inline-block;
				border: 1px solid #E0E6F8;
				background-color: #EFF2FB;
				font-family: Sans-serif;
				font-size: 9pt;
				color: #444;
				cursor: pointer;
				user-select: none;
				white-space: pre-wrap;
				overflow: auto;
				position: absolute;
				bottom: 10px;
			}
			.button:hover {
				border-color: #CED8F6;
				background-color: #E0E6F8;
			}
		</style>
	</head>
	<body>
		<p>Are you sure?<br />Deleted files cannot be recovered!</p>
		<div class="button" style="left: 10px;" onclick="top.resolveDarkened(window.frameElement.parentElement.parentElement);">CANCEL</div>
		<div class="button" style="right: 10px;" onclick="window.location='<?php echo $_GET['action']; ?>';">OK</div>
		<script type="text/javascript">
			document.addEventListener( "contextmenu", function(e) {
				e.preventDefault();
			});
		</script>
	</body>
</html>