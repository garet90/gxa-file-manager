<?php
	require 'auth.php';
	if ($usercheck && $passcheck) { } else {
		die();
	}
	function copy_directory($src,$dst) {
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					copy_directory($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}
	function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") {
						rrmdir($dir."/".$object); 
					} else {
						unlink($dir."/".$object);
					}
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
	if (isset($_POST['action'])) {
		if ($_POST['action'] == "cp") {
			if ($_POST['op'] == $_COOKIE['password']) {
				if ($_POST['np'] == $_POST['cp']) {
					if ($usemysql) {
						if ($_POST['np'] !== "") {
							$sqlilink = mysqli_connect($mysqlip, $mysqluser, $mysqlpassword, $mysqldatabase);
							mysqli_query($sqlilink,"UPDATE `users` SET `password` = '" . password_hash($_POST["np"], PASSWORD_DEFAULT) . "' WHERE `users`.`name` = '" . $_COOKIE['user'] . "' ");
							mysqli_close($sqlilink);
							echo '<script type="text/javascript">top.window.location = "logout.php";</script>';
							die();
						} else {
							die('Your password cannot be blank.');
						}
					} else {
						die('You must enable MySQL to change your password. You may change your password in "/admin/config.php" otherwise.');
					}
				} else {
					die('Passwords don\'t match.');
				}
			} else {
				die('Old password is incorrect.');
			}
		} else if ($_POST['action'] == 'da') {
			if ($usemysql) {
				if ($_POST['pw'] == $_COOKIE['password']) {
					$sqlilink = mysqli_connect($mysqlip, $mysqluser, $mysqlpassword, $mysqldatabase);
					mysqli_query($sqlilink,"DELETE FROM `users` WHERE `users`.`name` = '" . $_COOKIE['user'] . "' ");
					mysqli_close($sqlilink);
					rrmdir('../../admin/users/' . $_COOKIE['user']);
					echo '<script type="text/javascript">top.window.location = "logout.php";</script>';
					die();
				} else {
					die('Your password is incorrect.');
				}
			} else {
				die('You activate MySQL to use multiple user accounts.');
			}
		} else if ($_POST['action'] == 'ca') {
			$hasPermission = false;
			foreach ($permissions as $permission) {
				if ($permission == "CREATE_ACCOUNTS" || $permission == "*") {
					$hasPermission = true;
				}
			}
			if ($hasPermission) {
				if ($usemysql) {
					if ($_POST['un'] != "") {
						if ($_POST['pw'] != "") {
							if ($_POST['pw'] == $_POST['cp']) {
								$sqlilink = mysqli_connect($mysqlip, $mysqluser, $mysqlpassword, $mysqldatabase);
								$suc = mysqli_query($sqlilink,"SELECT * FROM `users` WHERE `users`.`name` = '" . $_POST['un'] . "' ");
								if (mysqli_num_rows($suc) == 0 && $_POST['un'] != "default") {
									mysqli_query($sqlilink,"INSERT INTO `users` (`ID`, `name`, `password`, `permissions`) VALUES (NULL, '" . $_POST['un'] . "', '" . password_hash($_POST["pw"], PASSWORD_DEFAULT) . "', '" . $_POST['up'] . "') ");
									copy_directory('../../admin/users/default/','../../admin/users/' . $_POST['un'] . '/');
								} else {
									die('Username is taken!');
								}
								mysqli_close($sqlilink);
							} else {
								die('Passwords don\'t match!');
							}
						} else {
							die('Password cannot be blank!');
						}
					} else {
						die('Username cannot be blank!');
					}
				} else {
					die('You activate MySQL to use multiple user accounts.');
				}
			} else {
				die('You don\'t have permission to create user accounts!');
			}
		} else if ($_POST['action'] == 'ef') {
			if (isset($_POST['at']) && $_POST['at'] == "on") {
				setcookie("editor-at", "true", time() + (86400 * 30), "/", $_SERVER['HTTP_HOST'], 1);
			} else {
				setcookie("editor-at", "false", time() + (86400 * 30), "/", $_SERVER['HTTP_HOST'], 1);
			}
			header('Location: settings.php');
			die();
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><div class="windowicon"><i class="fa fa-cogs" aria-hidden="true"></i></div>Settings</title>
		<meta charset="UTF-8">
		<style>
			body, html {
				margin: 0;
				padding: 0;
				user-select: none;
				-moz-user-select: none;
				-webkit-user-select: none;
				font-family: Sans-serif;
			}
			.nav {
				position: fixed;
				width: 160px;
				background-color: #F0F0F0;
				top: 0;
				left: 0;
				bottom: 0;
				padding: 10px 0;
				box-sizing: border-box;
				font-size: 10pt;
				border-right: 1px solid #DEDEDE;
			}
			.nav__items {
				list-style: none;
				padding: 0;
				margin: 0;
			}
			.nav__item {
				padding: 6px 0;
				cursor: pointer;
				border-right: 1px solid transparent;
				box-sizing: border-box;
				width: 160px;
				color: #646464;
			}
			.nav__item:hover {
				background-color: #ECECEC;
				border-color: #DEDEDE;
				color: #3C3C3C;
			}
			.nav__item.active {
				background-color: #E0E6F8;
				border-color: #A9BCF5;
				color: #141414;
			}
			.nav__icon {
				width: 30px;
				text-align: center;
			}
			#menus {
				position: fixed;
				right: 0;
				left: 160px;
				top: 0;
				bottom: 0;
				padding: 10px;
				box-sizing: border-box;
				overflow-y: auto;
			}
			.menu {
				display: none;
			}
			.menu.active {
				display: block;
			}
			.menu__subheader {
				width: 100%;
				border-bottom: 1px solid #BDBDBD;
				margin: 0;
				padding: 0;
				font-weight: normal;
				font-size: 14pt;
				color: #222;
			}
			.menu__label {
				font-size: 9pt;
				color: #444;
			}
			.menu__input, .menu__textarea {
				margin-bottom: 2px;
				box-sizing: border-box;
				width: 200px;
			}
			.menu__textarea {
				min-width: 200px;
				max-width: 100%;
				min-height: 50px;
				max-height: 150px;
			}
			.menu__cb-container {
				margin: 0;
				display: flex;
				align-items: center;
			}
			.menu__description {
				margin: 0 0 5px 0;
			}
			.menu__form {
				margin: 5px 5px 10px 5px;
				font-size: 9pt;
			}
			.menu__submit {
				margin: 5px 0;
				color: #444;
			}
		</style>
		<link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.css">
	</head>
	<body>
		<nav class="nav">
			<ul class="nav__items">
				<li class="nav__item active" onmousedown="changePage('general',this)">
					<i class="fa fa-wrench nav__icon" aria-hidden="true"></i>General
				</li>
				<li class="nav__item" onmousedown="changePage('my-account',this)">
					<i class="fa fa-user nav__icon" aria-hidden="true"></i>My Account
				</li>
				<li class="nav__item" onmousedown="changePage('file-explorer',this)">
					<i class="fa fa-folder-open nav__icon" aria-hidden="true"></i>File Explorer
				</li>
				<li class="nav__item" onmousedown="changePage('file-editor',this)">
					<i class="fa fa-file nav__icon" aria-hidden="true"></i>File Editor
				</li>
				<li class="nav__item" onmousedown="changePage('user-accounts',this)">
					<i class="fa fa-users nav__icon" aria-hidden="true"></i>User Accounts
				</li>
			</ul>
		</nav>
		<div id="menus">
			<div id="menu-general" class="menu active">
				<h2 class="menu__subheader">About</h2>
				<div class="menu__form">You are currently using GXa File Manager version <?php echo $gxaversion; ?> created by Garet Halliday. You can find updates, plugins, and the wiki on the projects <a href="https://github.com/garet90/gxa-file-manager" target="_new">GitHub page</a>.</div>
			</div>
			<div id="menu-my-account" class="menu">
				<h2 class="menu__subheader">Change Password</h2>
				<form class="menu__form" method="post" action="settings.php" autocomplete="off">
					<input name="action" type="hidden" value="cp" />
					<label class="menu__label" for="cp-op">Old password</label><br />
					<input name="op" class="menu__input" id="cp-op" type="password" /><br />
					<label class="menu__label" for="cp-np">New password</label><br />
					<input name="np" class="menu__input" id="cp-np" type="password" /><br />
					<label class="menu__label" for="cp-cp">Confirm password</label><br />
					<input name="cp" class="menu__input" id="cp-cp" type="password" /><br />
					<input class="menu__submit" type="submit" value="Change Password" />
				</form>
				<h2 class="menu__subheader">Deactivate Account</h2>
				<form class="menu__form" method="post" action="settings.php" autocomplete="off">
					<input name="action" type="hidden" value="da" />
					<label class="menu__label" for="da-pw">Password</label><br />
					<input name="pw" class="menu__input" id="da-pw" type="password" /><br />
					Warning: Deactivated accounts are unrecoverable. Deactivation will delete your user folder, containing your desktop and other personal files. Continue?<br />
					<input class="menu__submit" type="submit" value="Deactivate Account" />
				</form>
			</div>
			<div id="menu-file-explorer" class="menu">
				<div class="menu__form">
					No settings are available for this app at this time.
				</div>
			</div>
			<div id="menu-file-editor" class="menu">
				<h2 class="menu__subheader">Function</h2>
				<form class="menu__form" method="post" action="settings.php" autocomplete="off">
					<input type="hidden" name="action" value="ef" />
					<p class="menu__cb-container"><input type="checkbox" class="menu__checkbox" id="ef-at" name="at" <?php
						if ((isset($_COOKIE['editor-at']) && $_COOKIE['editor-at'] == "true") || (isset($_COOKIE['editor-at']) == false && $useautotab == true)) {
							echo "checked";
						}
					?> /><label class="menu__label" for="ef-at">Use Auto Tab</label></p>
					<p class="menu__description">Auto Tab automatically inserts the same amount of tabs that were in the previous line in the beginning of each new line. This is very useful for organization when programming.</p>
					<input type="submit" value="Save Changes" />
				</form>
			</div>
			<div id="menu-user-accounts" class="menu">
				<h2 class="menu__subheader">Create Account</h2>
				<form class="menu__form" method="post" action="settings.php" autocomplete="off">
					<input name="action" type="hidden" value="ca" />
					<label class="menu__label" for="ca-un">Username</label><br />
					<input name="un" class="menu__input" id="ca-un" type="text" /><br />
					<label class="menu__label" for="ca-pw">Password</label><br />
					<input name="pw" class="menu__input" id="ca-pw" type="password" /><br />
					<label class="menu__label" for="ca-cp">Confirm password</label><br />
					<input name="cp" class="menu__input" id="ca-cp" type="password" /><br />
					<label class="menu__label" for="ca-up">User permissions</label><br />
					<textarea name="up" class="menu__textarea" id="ca-up"></textarea><br />
					Warning: User permissions are highly experimental at this time. Use at your own risk! Information on how to use permissions can be found <a href="https://github.com/garet90/gxa-file-manager/wiki/Permissions" target="_new">here</a>.<br />
					<input class="menu__submit" type="submit" value="Create Account" />
				</form>
			</div>
		</div>
		<script type="text/javascript">
			function changePage(pageId, pageTab) {
				document.getElementsByClassName("active")[0].classList.remove("active");
				document.getElementsByClassName("active")[0].classList.remove("active");
				pageTab.classList.add("active");
				document.getElementById("menu-" + pageId).classList.add("active");
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
				top.openContextMenu(getAbsolutePosition(e), window.frameElement);
			} );
			document.addEventListener( "keydown", function(e) {
				if (e.keyCode == 27 || e.which == 27) {
					top.closeContextMenu();
				}
			} );
			document.addEventListener( "click", function(e) {
				top.closeContextMenu();
			} );
		</script>
	</body>
</html>