<?php require 'auth.php' ?>
<!DOCTYPE html>
<html>
	<head>
		<title>GXa File Manager</title>
		<style>
			.leftbox {
				width: 30%;
				float: left;
				padding: 2px;
				background-color: #CDCDCD;
				margin-bottom: 10px;
				word-break: break-all;
			}
			.leftbox.last {
				clear: both;
			}
			.rightbox {
				width: auto;
				float: left;
				top: 0;
				bottom: 0;
				position: absolute;
				left: 30%;
				margin-left: 15px;
				right: 0;
			}
			body, html {
				margin: 0;
				padding: 0;
			}
			body {
				padding: 10px;
				overflow-y: hidden;
			}
			iframe.explorer {
				width: 100%;
				height: 100%;
				border: 0;
				margin: 0;
				padding: 0;
				overflow-y: scroll;
				overflow-x: hidden;
			}
			.leftboxinner {
				background-color: white;
				border: 1px solid white;
				font-size: 8pt;
				padding: 8px 4px 8px 4px;
				font-family: Sans-serif;
				color: #3D3D3D;
			}
			.leftboxinner.first {
				font-weight: bold;
				background-color: #e6EEEE;
				color: black;
			}
			#loadbox {
				width: 25px;
				height: 25px;
				position: absolute;
				z-index: 100;
				top: 50%;
				left: 50%;
				margin-top: -12.5px;
				margin-left: -12.5px;
				-webkit-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
				-moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
				box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
			}
			.leftboxinner.load {
				line-height: 23px;
				padding: 0;
				height: 23px;
				text-align: center;
				font-size: 10pt;
			}
			.leftboxinner.nonfirst {
				margin-top: 2px;
			}
			.leftnavtext {
				margin-left: 5px;
			}
			.leftnavbutton {
				padding: 8px;
				margin: 0;
				font-size: 8pt;
				cursor: pointer;
			}
			.leftnavbutton:hover {
				color: black;
			}
			.leftnavbutton.activebutton {
				color: black;
				font-weight: bold;
				background-color: #e6EEEE;
			}
			.leftboxinner.button {
				padding: 0;
			}
		</style>
		<link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.css">
	</head>
	
	<body>
		<div class="leftbox">
			<div class="leftboxinner first">
				<?php
				echo 'Logged in. Welcome back, ' . $_COOKIE['user'] . '. (Not ' . $_COOKIE['user'] . '? <a href="logout.php">Logout</a>)';
				?>
			</div>
		</div>
		<div class="rightbox">
			<iframe src="explorer.php?loc=/" class="explorer"></iframe>
			<div id="loadbox" class="leftbox">
				<div class="leftboxinner load">
					<i class="fa fa-spinner fa-pulse fa-fw"></i>
				</div>
			</div>
		</div>
		<div class="leftbox last">
			<div class="leftboxinner button">
				<p class="leftnavbutton activebutton" onClick="changeScreen(this,'explorer.php?loc=/')"><i class="fa fa-folder-open-o" aria-hidden="true"></i><span class="leftnavtext">File Manager</span></p>
			</div>
			<div class="leftboxinner nonfirst button">
				<p class="leftnavbutton" onClick="changeScreen(this,'settings.php')"><i class="fa fa-cog" aria-hidden="true"></i><span class="leftnavtext">Settings</span></p>
			</div>
		</div>
		<div class="leftbox last">
			<div class="leftboxinner">
				<?php
				date_default_timezone_set('UTC');
				
				echo 'Server Status: Online<br />';
				echo 'Server Time: ' . date('d/m/Y, H:i:s') . '<br />';
				echo 'PHP Version: ' . phpversion() . '<br />';
				echo '<br />https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				?>
			</div>
		</div>
		<script type="text/javascript">
			var loadBox = document.getElementById("loadbox");
			function inload(x) {
				if (x == "stop") {
					loadBox.style.display = "none";
				}
				if (x == "start") {
					loadBox.style.display = "block";
				}
			}
			function changeScreen(x,y) {
				inload("start");
				document.getElementsByClassName('explorer')[0].src = y;
				document.getElementsByClassName('activebutton')[0].classList.remove('activebutton');
				x.classList.add('activebutton');
			}
		</script>
	</body>
</html>