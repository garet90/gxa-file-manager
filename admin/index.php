<?php
	require 'config.php';
	if (isset($_COOKIE['user']) && isset($_COOKIE['password'])) {
		if ($_COOKIE['user'] == $adminname && $_COOKIE['password'] == $adminpassword) {
			header('location: dashboard/');
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>GXa Server Manager</title>
		<style>
			.center {
				padding: 2px;
				background-color: #CDCDCD;
				margin-bottom: 10px;
				word-break: break-all;
				width: 400px;
				position: absolute;
				left: 50%;
				top: 50%;
				margin-left: -200px;
				margin-top: -150px;
			}
			.boxinner {
				background-color: white;
				border: 1px solid white;
				font-size: 8pt;
				padding: 8px 4px 8px 4px;
				font-family: Sans-serif;
				color: #3D3D3D;
			}
			.boxinner.first {
				font-weight: bold;
				background-color: #e6EEEE;
				color: black;
				height: 8pt;
				margin-bottom: 2px;
			}
			.smol {
				font-size: 10px;
				font-style: italic;
			}
			.boxinner p {
				margin: 0;
				padding: 0;
				text-align: center;
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
				border: 0;
				color: #3D3D3D;
				margin-bottom: 15px;
				left: 50%;
				width: 100px;
				margin-left: -62px;
				text-align: center;
			}
			.errorbutton:hover {
				background-color: #e6EEEE;
			}
			.errortext {
				margin-bottom: 50px !important;
			}
		</style>
	</head>
	
	<body>
		<div class="center">
			<div class="boxinner first">
				<p>GXa File Manager</p>
			</div>
			<div class="boxinner">
				<form method="post" action="login.php">
					<label for="username">User</label><br />
					<input id="username" type="text" name="user" /><br /><br />
					<label for="password">Password</label><br />
					<input id="password" type="password" name="password" /><br /><br />
					<input type="submit" />
				</form><br />
				<span class="smol">GXa File Manager v0.1 BETA by Garet Halliday. <a href="https://github.com/garet90/gxa-file-manager" target="_new">github</a></span>
			</div>
		</div>
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
        <script type="text/javascript">
        function inIframe () {
            try {
                return window.self !== window.top;
            } catch (e) {
                return true;
            }
        }
        if (inIframe()) {
            top.window.location = window.location;
        }
        </script>
	</body>
</html>