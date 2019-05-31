<!DOCTYPE html>
<html>
	<head>
		<title>GXa File Manager</title>
		<link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.css">
		<meta charset="UTF-8">
		<style>
			body, html {
				margin: 0;
				padding: 0;
				overflow: hidden;
				height: 100vh;
				width: 100vw;
				user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				-webkit-user-select: none;
			}
			.window {
				background-color: #A9BCF5;
				border: 2px solid #A9BCF5;
				position: absolute;
				display: flex;
				flex-flow: column;
				min-height: 250px;
				min-width: 400px;
				box-shadow: 4px 4px 2px -2px rgba(0,0,0,.5);
				box-sizing: border-box;
			}
			.windowheader, .windowcontent {
				border: 1px solid white;
				position: relative;
			}
			.windowheader {
				background-color: #E0E6F8;
				color: black;
				font-size: 8pt;
				padding: 8px;
				font-family: Sans-serif;
				margin-bottom: 2px;
				flex: 0 0 auto;
				cursor: move;
				font-weight: bold;
			}
			.windowcontent {
				background-color: white;
				flex: 1 1 auto;
			}
			.windowframe {
				width: 100%;
				height: 100%;
				border: 0;
				box-sizing: border-box;
			}
			.interactionshield {
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				display: none;
			}
			.resizer, .nresizer, .eresizer {
				position: absolute;
				background-color: transparent;
			}
			.resizer {
				width: 10px;
				height: 10px;
				bottom: -5px;
				right: -5px;
				cursor: se-resize;
			}
			.nresizer {
				height: 10px;
				bottom: -5px;
				left: 0;
				right: 5px;
				cursor: n-resize;
			}
			.eresizer {
				width: 10px;
				top: 0;
				bottom: 5px;
				right: -5px;
				cursor: e-resize;
			}
			.loadicon {
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translateY(-50%) translateX(-50%);
				text-align: center;
				line-height: 21px;
				width: 25px;
				height: 25px;
				padding: 0 2px;
				background-color: white;
				color: #3D3D3D;
				border: 2px solid #A9BCF5;
				box-sizing: border-box;
				font-size: 10pt;
				box-shadow: 4px 4px 2px -1px rgba(0,0,0,.5);
			}
			.windowicon {
				display: inline-block;
				width: 20px;
				text-align: center;
			}
			.windowtitle {
				float: left;
			}
			.windowclose, .windowfs {
				float: right;
				width: 20px;
				text-align: center;
				cursor: pointer;
			}
			#bgtext {
				width: 100vw;
				height: 100vh;
				line-height: 100vh;
				text-align: center;
				font-size: 75pt;
				color: #eeeeee;
				font-family: "Arial Black", Gadget, sans-serif;
			}
			#context-menu, .sub-context-menu {
				position: absolute;
				z-index: 1000000;
				display: none;
				background-color: white;
				box-shadow: rgba(0, 0, 0, 0.5) 4px 4px 2px -2px;
				border: 1px solid #CDCDCD;
				width: 200px;
			}
			.sub-context-menu {
				z-index: 1000001;
			}
			#context-menu.active, .sub-context-menu.active {
				display: block;
			}
			.context-menu__items {
				list-style: none;
				padding: 0;
				margin: 3px 0;
			}
			.context-menu__item {
				padding: 4px 20px;
				font-size: 9pt;
				text-decoration: none;
				font-family: Sans-serif;
				cursor: default;
			}
			.context-menu__item:hover {
				background-color: #eeeeee;
			}
			.context-menu__shortcut {
				color: #BDBDBD;
				float: right;
				font-size: 7pt;
				line-height: 11pt;
			}
			.context-menu__break {
				border-bottom: 1px solid #eeeeee;
				margin: 3px 0;
			}
			#background-darken {
				position: absolute;
				top: 0;
				left: 0;
				bottom: 0;
				right: 0;
				background-color: rgba(0,0,0,.5);
				display: none;
			}
			#clipboard {
				display: none;
			}
			#desktop-explorer {
				position: absolute;
				display: none;
				z-index: 0;
				top: 0;
				left: 0;
				width: 100vw;
				height: 100vh;
				border: 0;
			}
		</style>
	</head>
	<body>
		<iframe src="about:blank" id="desktop-explorer"></iframe>
		<div class="interactionshield"></div>
		<span id="clipboard" data-action=""></span>
		<div id="bgtext">GXa File Manager</div>
		<div id="background-darken"></div>
		<script type="text/javascript">
			function FSToggle(elmnt,direction) {
				wIcon = elmnt.getElementsByClassName("windowfs")[0];
				if (elmnt.getAttribute("data-fullscreen") == "false") {
					elmnt.setAttribute("data-fullscreen",elmnt.style.top + ":" + elmnt.style.left + ":" + elmnt.style.height + ":" + elmnt.style.width + ":");
					if (direction) {
						if (direction == "left") {
							elmnt.style.top = "0";
							elmnt.style.right = "";
							elmnt.style.left = "0";
							elmnt.style.height = "100vh";
							elmnt.style.width = "50vw";
						} else if (direction == "right") {
							elmnt.style.top = "0";
							elmnt.style.right = "0";
							elmnt.style.left = "";
							elmnt.style.height = "100vh";
							elmnt.style.width = "50vw";
						}
					} else {
						elmnt.style.top = "0";
						elmnt.style.right = "";
						elmnt.style.left = "0";
						elmnt.style.height = "100vh";
						elmnt.style.width = "100vw";
					}
					wIcon.innerHTML = '<i class="fa fa-window-restore"></i>';
					elmnt.getElementsByClassName("resizer")[0].style.display = "none";
					elmnt.getElementsByClassName("nresizer")[0].style.display = "none";
					elmnt.getElementsByClassName("eresizer")[0].style.display = "none";
				} else {
					var datasplit = elmnt.getAttribute("data-fullscreen").split(":");
					elmnt.style.top = datasplit[0];
					elmnt.style.left = datasplit[1];
					elmnt.style.right = "";
					elmnt.style.height = datasplit[2];
					elmnt.style.width = datasplit[3];
					elmnt.setAttribute("data-fullscreen","false");
					wIcon.innerHTML = '<i class="fa fa-window-maximize"></i>';
					elmnt.getElementsByClassName("resizer")[0].style.display = "block";
					elmnt.getElementsByClassName("nresizer")[0].style.display = "block";
					elmnt.getElementsByClassName("eresizer")[0].style.display = "block";
				}
			}
			function resolveDarkened(elmnt,frameToReload,filesToRemove) {
				var backgroundDarken = document.getElementById("background-darken");
				backgroundDarken.style.display = "none";
				elmnt.remove();
				if (frameToReload) {
					document.getElementById(frameToReload).contentWindow.removeFilesByString(filesToRemove);
				}
			}
			var windowCount = 0;
			function createWindow(target,wWidth,wHeight,wX,wY,imptWindow) {
				windowCount++;
				var newWindow = document.createElement('div'),
					backgroundDarken = document.getElementById("background-darken");
				newWindow.id = "dw-" + windowCount;
				newWindow.classList.add('window');
				newWindow.onmousedown = function() { moveToTop(this); }
				topZIndex++;
				newWindow.style.zIndex = topZIndex;
				newWindow.setAttribute("data-fullscreen","false");
				if (imptWindow) {
					backgroundDarken.style.display = "block";
					backgroundDarken.style.zIndex = topZIndex;
				}
				if (wWidth == undefined) {
					wWidth = 600;
				} else if (wWidth < 600) {
					newWindow.style.minWidth = wWidth + "px";
				}
				newWindow.style.width = wWidth + "px";
				if (wHeight == undefined) {
					wHeight = 400;
				} else if (wHeight < 400) {
					newWindow.style.minHeight = wHeight + "px";
				}
				newWindow.style.height = wHeight + "px";
				if (wX !== undefined) {
					if (wX == "center") {
						newWindow.style.left = (.5 * +window.innerWidth) - (.5 * +wWidth) + "px";
					} else {
						if (wX < 0) {
							wX = 0;
						}
						if (wX > (+window.innerWidth - +wWidth)) {
							wX = +window.innerWidth - +wWidth;
						}
						newWindow.style.left = wX + "px";
					}
				} else {
					newWindow.style.left = "10px";
				}
				if (wY !== undefined) {
					if (wY == "center") {
						newWindow.style.top = (.5 * +window.innerHeight) - (.5 * +wHeight) + "px";
					} else {
						if (wY < 0) {
							wY = 0;
						}
						if (wY > (+window.innerHeight - +wHeight)) {
							wY = +window.innerHeight - +wHeight;
						}
						newWindow.style.top = wY + "px";
					}
				} else {
					newWindow.style.top = "10px";
				}
				if (imptWindow) {
					newWindow.innerHTML = '<div class="windowheader" id="dw-' + windowCount + '-hd"><div class="windowtitle" id="dw-' + windowCount + '-ti"><div class="windowicon"><i class="fa fa-spinner fa-fw"></i></div>Loading</div><div class="windowclose" onclick="resolveDarkened(this.parentElement.parentElement);"><i class="fa fa-times"></i></div></div><div class="windowcontent"><div class="interactionshield" id="dw-' + windowCount + '-is"></div><iframe src="' + target + '" class="windowframe" id="dw-' + windowCount + '-cw"></iframe><div class="loadicon" id="dw-' + windowCount + '-li"><i class="fa fa-spinner fa-pulse fa-fw"></i></div></div>';
				} else {
					newWindow.innerHTML = '<div class="windowheader" id="dw-' + windowCount + '-hd"><div class="windowtitle" id="dw-' + windowCount + '-ti"><div class="windowicon"><i class="fa fa-spinner fa-fw"></i></div>Loading</div><div class="windowclose" onclick="this.parentElement.parentElement.remove();"><i class="fa fa-times"></i></div><div class="windowfs" onclick="FSToggle(this.parentElement.parentElement);"><i class="fa fa-window-maximize"></i></div></div><div class="windowcontent"><div class="interactionshield" id="dw-' + windowCount + '-is"></div><iframe src="' + target + '" class="windowframe" id="dw-' + windowCount + '-cw"></iframe><div class="loadicon" id="dw-' + windowCount + '-li"><i class="fa fa-spinner fa-pulse fa-fw"></i></div></div>';
				}
				document.body.appendChild(newWindow);
				initiateWindow(document.getElementById('dw-' + windowCount));
				return document.getElementById('dw-' + windowCount);
			}
			var topZIndex = 0;
			function moveToTop(elmnt) {
				topZIndex++;
				elmnt.style.zIndex = topZIndex;
			}
			function initiateWindow(elmnt) {
				var whd = document.getElementById(elmnt.id + '-hd'),
					wis = document.getElementById(elmnt.id + '-is'),
					wcw = document.getElementById(elmnt.id + '-cw'),
					wli = document.getElementById(elmnt.id + '-li'),
					wti = document.getElementById(elmnt.id + '-ti');
				dragElement(elmnt);
				resizeElement(elmnt);
				wcw.onload = function() {
					wti.innerHTML = wcw.contentDocument.title;
					wcw.contentWindow.onbeforeunload = function() {
						wti.innerHTML = '<div class="windowicon"><i class="fa fa-spinner fa-fw"></i></div>Loading...';
						wli.style.display = 'block';
					}
					wli.style.display = 'none';
				}
			}
			function dragElement(elmnt) {
			 	var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
				if (document.getElementById(elmnt.id + "-hd")) {
					// if present, the header is where you move the DIV from:
					document.getElementById(elmnt.id + "-hd").onmousedown = dragMouseDown;
				} else {
					// otherwise, move the DIV from anywhere inside the DIV: 
					elmnt.onmousedown = dragMouseDown;
				}
			
				function dragMouseDown(e) {
					e = e || window.event;
					e.preventDefault();
					changeISStatus(true);
					// get the mouse cursor position at startup:
					pos3 = e.clientX;
					pos4 = e.clientY;
					document.onmouseup = closeDragElement;
					// call a function whenever the cursor moves:
					document.onmousemove = elementDrag;
				}
			
				function elementDrag(e) {
					e = e || window.event;
					e.preventDefault();
					changeISStatus(true);
					// calculate the new cursor position:
					pos1 = pos3 - e.clientX;
					pos2 = pos4 - e.clientY;
					pos3 = e.clientX;
					pos4 = e.clientY;
					// set the element's new position:
					elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
					elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
					if (elmnt.getAttribute("data-fullscreen") !== "false") {
						var elmntFSSplit = elmnt.getAttribute("data-fullscreen").split(":");
						elmnt.setAttribute("data-fullscreen",(pos4-17) + "px:" + (pos3-(.5*+elmntFSSplit[3].slice(0,-2))) + "px:" + elmntFSSplit[2] + ":" + elmntFSSplit[3]);
						FSToggle(elmnt);
					}
				}
			
				function closeDragElement() {
					// stop moving when mouse button is released:
					if (pos4 <= 0) {
						FSToggle(elmnt);
					} else if (pos3 <= 0) {
						FSToggle(elmnt,"left");
					} else if (pos3 >= (+window.innerWidth-1)) {
						FSToggle(elmnt,"right");
					}
					changeISStatus(false);
					document.onmouseup = null;
					document.onmousemove = null;
				}
			}
			
			function resizeElement(elmnt) {
				//create box in bottom-left
				var resizer = document.createElement('div'),
				nresizer = document.createElement('div'),
				eresizer = document.createElement('div');
				resizer.classList.add('resizer');
				nresizer.classList.add('nresizer');
				eresizer.classList.add('eresizer');
				//Append Child to Element
				elmnt.appendChild(resizer);
				elmnt.appendChild(nresizer);
				elmnt.appendChild(eresizer);
				//box function onmousemove
				resizer.addEventListener('mousedown', initResize, false);
				eresizer.addEventListener('mousedown', initResizeX, false);
				nresizer.addEventListener('mousedown', initResizeY, false);
				
				//Window funtion mousemove & mouseup
				function initResize(e) {
				   window.addEventListener('mousemove', Resize, false);
				   window.addEventListener('mouseup', stopResize, false);
				}
				function initResizeX(e) {
				   window.addEventListener('mousemove', ResizeX, false);
				   window.addEventListener('mouseup', stopResizeX, false);
				}
				function initResizeY(e) {
				   window.addEventListener('mousemove', ResizeY, false);
				   window.addEventListener('mouseup', stopResizeY, false);
				}
				//resize the element
				function Resize(e) {
				   elmnt.style.width = (e.clientX - elmnt.offsetLeft) + 'px';
				   elmnt.style.height = (e.clientY - elmnt.offsetTop) + 'px';
					changeISStatus(true);
				}
				function ResizeX(e) {
				   elmnt.style.width = (e.clientX - elmnt.offsetLeft) + 'px';
					changeISStatus(true);
				}
				function ResizeY(e) {
				   elmnt.style.height = (e.clientY - elmnt.offsetTop) + 'px';
					changeISStatus(true);
				}
				//on mouseup remove windows functions mousemove & mouseup
				function stopResize(e) {
				    window.removeEventListener('mousemove', Resize, false);
				    window.removeEventListener('mouseup', stopResize, false);
					changeISStatus(false);
				}
				function stopResizeX(e) {
				    window.removeEventListener('mousemove', ResizeX, false);
				    window.removeEventListener('mouseup', stopResizeX, false);
					changeISStatus(false);
				}
				function stopResizeY(e) {
				    window.removeEventListener('mousemove', ResizeY, false);
				    window.removeEventListener('mouseup', stopResizeY, false);
					changeISStatus(false);
				}
			}
			
			function changeISStatus(showIS) {
				var ISs = document.getElementsByClassName("interactionshield");
				if (showIS) {
					for (i=0; i<ISs.length; i++) {
						ISs[i].style.display = "block";
					}
				} else {
					for (i=0; i<ISs.length; i++) {
						ISs[i].style.display = "none";
					}
				}
			}
			
			function getPosition(e) {
			  var posx = 0;
			  var posy = 0;
			
			  if (!e) var e = window.event;
			
			  if (e.pageX || e.pageY) {
			    posx = e.pageX;
			    posy = e.pageY;
			  } else if (e.clientX || e.clientY) {
			    posx = e.clientX + document.body.scrollLeft + 
			                       document.documentElement.scrollLeft;
			    posy = e.clientY + document.body.scrollTop + 
			                       document.documentElement.scrollTop;
			  }
			
			  return {
			    x: posx,
			    y: posy
			  }
			}
			function clickInsideElement( e, className ) {
				var el = e.srcElement || e.target;
			
				if ( el.classList.contains(className) ) {
					return el;
				} else {
					while ( el = el.parentNode ) {
						if ( el.classList && el.classList.contains(className) ) {
							return el;
						}
					}
				}
			
				return false;
			}
			
			document.addEventListener( "contextmenu", function(e) {
				var cmSettingsCl = document.getElementById("cm-settings-cl"),
					cmSettingsRe = document.getElementById("cm-settings-re"),
					cmtWH = clickInsideElement(e, 'window');
				e.preventDefault();
				if (cmtWH !== false) {
					openContextMenu(getPosition(e),undefined,true);
					cmSettingsCl.onclick = function() {
						cmtWH.remove();
						closeContextMenu();
					}
					var selectedFrame = cmtWH.getElementsByClassName("windowcontent")[0].getElementsByClassName("windowframe")[0];
					cmSettingsRe.onclick = function() {
						selectedFrame.src = selectedFrame.contentWindow.location.href;
						closeContextMenu();
					}
				} else {
					openContextMenu(getPosition(e),undefined,false);
				}
			} );
			function openContextMenu(position, frame, showSettings, showFiles, showEdit) {
				var contextMenu = document.getElementById("context-menu"),
					cmSettings = document.getElementById("cm-settings"),
					cmFiles = document.getElementById("cm-files"),
					cmEdit = document.getElementById("cm-edit"),
					positionTop = 0;
					positionLeft = 0;
				
				if (showSettings) {
					cmSettings.style.display = "block";
				} else {
					cmSettings.style.display = "none";
				}
				if (showFiles) {
					cmFiles.style.display = "block";
				} else {
					cmFiles.style.display = "none";
				}
				if (showEdit) {
					cmEdit.style.display = "block";
				} else {
					cmEdit.style.display = "none";
				}
				
				if (document.getElementById("background-darken").style.display !== "block") {
					contextMenu.classList.add("active");
				}
				if (frame !== undefined) {
					if (frame.id == "desktop-explorer") {
						positionTop = position.y;
						positionLeft = position.x;
					} else {
						positionTop = position.y + +frame.parentElement.parentElement.offsetTop + 4 + +frame.parentElement.parentElement.getElementsByClassName("windowheader")[0].offsetHeight;
						positionLeft = position.x + +frame.parentElement.parentElement.offsetLeft + 2;
					}
				} else {
					positionTop = position.y;
					positionLeft = position.x;
				}
				
				if (positionTop + contextMenu.offsetHeight > window.innerHeight) {
					if (positionTop - contextMenu.offsetHeight > 0) {
						positionTop = positionTop - contextMenu.offsetHeight;
					} else {
						positionTop = window.innerHeight - contextMenu.offsetHeight;
					}
				}
				if (positionLeft + contextMenu.offsetWidth > window.innerWidth) {
					positionLeft = window.innerWidth - contextMenu.offsetWidth;
				}
				
				contextMenu.style.top = positionTop + "px";
				contextMenu.style.left = positionLeft + "px";
			}
			function openSubContextMenu(menuId, elmnt) {
				window.setTimeout(function() {
					if (elmnt.getAttribute("data-mouseover") == "true") {
						var menu = document.getElementById(menuId);
						menu.classList.add("active");
						menu.style.top = +elmnt.parentElement.parentElement.parentElement.style.top.slice(0,-2) + elmnt.offsetTop + "px";
						if (+elmnt.parentElement.parentElement.parentElement.style.left.slice(0,-2) + +elmnt.parentElement.parentElement.parentElement.offsetWidth + menu.offsetWidth > window.innerWidth) {
							menu.style.left = +elmnt.parentElement.parentElement.parentElement.style.left.slice(0,-2) - +menu.offsetWidth + "px";
						} else {
							menu.style.left = +elmnt.parentElement.parentElement.parentElement.style.left.slice(0,-2) + +elmnt.parentElement.parentElement.parentElement.offsetWidth + "px";
						}
					}
				}, 250);
			}
			document.addEventListener( "mousedown", function(e) {
				if (clickInsideElement( e, "context-menu__item" ) == false) {
					closeContextMenu();
				}
			} );
			document.addEventListener( "keydown", function(e) {
				if (e.keyCode == 27 || e.which == 27) {
					closeContextMenu();
				}
			} );
			function closeContextMenu() {
				var contextMenu = document.getElementById("context-menu"),
					subContextMenus = document.getElementsByClassName("sub-context-menu");
				contextMenu.classList.remove("active");
				for (i = 0; i < subContextMenus.length; i++) {
					subContextMenus[i].classList.remove("active");
				}
			}
			function setElementFunctionInFrame(targetID, frame) {
				var contextMenu = document.getElementById("context-menu");
				document.getElementById(targetID).onclick = function() {
					frame.contentWindow.cmAction(targetID,contextMenu.style.left.slice(0,-2),contextMenu.style.top.slice(0,-2));
					closeContextMenu();
				}
			}
			
			<?php
				if (isset($_COOKIE['user']) && isset($_COOKIE['password'])) {
					if ($_COOKIE['user'] == '' && $_COOKIE['password'] == '') {
						echo "createWindow('login.php',400,250,'center','center');";
					} else {
						echo "loggedIn();";
					}
				} else {
					echo "createWindow('login.php',400,250,'center','center');";
				}
			?>
			function loggedIn() {
				var desktopExplorer = document.getElementById("desktop-explorer");
				desktopExplorer.src = "explorer.php?loc=/admin/users/" + getCookie("user") + "/desktop/";
				desktopExplorer.style.display = "block";
			}
			function getCookie(cname) {
			  var name = cname + "=";
			  var decodedCookie = decodeURIComponent(document.cookie);
			  var ca = decodedCookie.split(';');
			  for(var i = 0; i <ca.length; i++) {
			    var c = ca[i];
			    while (c.charAt(0) == ' ') {
			      c = c.substring(1);
			    }
			    if (c.indexOf(name) == 0) {
			      return c.substring(name.length, c.length);
			    }
			  }
			  return "";
			}
		</script>
		<nav id="context-menu">
			<ul class="context-menu__items">
				<div id="cm-files" class="context-menu__filter">
					<li class="context-menu__item" id="cm-files-op">
						<span class="context-menu__link">
							Open<span class="context-menu__shortcut">Enter</span>
						</span>
					</li>
					<li class="context-menu__item" id="cm-files-on">
						<span class="context-menu__link">
							Open In New Window
						</span>
					</li>
					<li class="context-menu__item" id="cm-files-re">
						<span class="context-menu__link">
							Refresh<span class="context-menu__shortcut">CTRL + R</span>
						</span>
					</li>
					<li class="context-menu__item" id="cm-files-pd">
						<span class="context-menu__link">
							Parent Directory
						</span>
					</li>
					<div class="context-menu__break"></div>
					<li class="context-menu__item" id="cm-files-ne" data-mouseover="false" onmouseover="this.setAttribute('data-mouseover','true'); openSubContextMenu('context-menu-new',this);" onmouseout="this.setAttribute('data-mouseover','false');">
						<span class="context-menu__link">
							New
						</span>
					</li>
					<li class="context-menu__item" id="cm-files-de">
						<span class="context-menu__link">
							Delete<span class="context-menu__shortcut">DEL</span>
						</span>
					</li>
					<li class="context-menu__item" id="cm-files-ed">
						<span class="context-menu__link">
							Edit
						</span>
					</li>
					<li class="context-menu__item" id="cm-files-do">
						<span class="context-menu__link">
							Download
						</span>
					</li>
					<li class="context-menu__item" id="cm-files-rn">
						<span class="context-menu__link">
							Rename
						</span>
					</li>
					<li class="context-menu__item" id="cm-files-cu">
						<span class="context-menu__link">
							Cut<span class="context-menu__shortcut">CTRL + X</span>
						</span>
					</li>
					<li class="context-menu__item" id="cm-files-co">
						<span class="context-menu__link">
							Copy<span class="context-menu__shortcut">CTRL + C</span>
						</span>
					</li>
					<li class="context-menu__item" id="cm-files-pa">
						<span class="context-menu__link">
							Paste<span class="context-menu__shortcut">CTRL + V</span>
						</span>
					</li>
					<div class="context-menu__filter" id="cm-files-filter-uz">
						<div class="context-menu__break"></div>
						<li class="context-menu__item" id="cm-files-uz">
							<span class="context-menu__link">
								Extract
							</span>
						</li>
					</div>
					<div class="context-menu__break"></div>
					<li class="context-menu__item" id="cm-files-pr">
						<span class="context-menu__link">
							Properties
						</span>
					</li>
					<div class="context-menu__break"></div>
				</div>
				<div id="cm-edit" class="context-menu__filter">
					<li class="context-menu__item" id="cm-edit-sa">
						<span class="context-menu__link">
							Save<span class="context-menu__shortcut">CTRL + S</span>
						</span>
					</li>
					<div class="context-menu__break"></div>
					<li class="context-menu__item" id="cm-edit-un">
						<span class="context-menu__link">
							Undo<span class="context-menu__shortcut">CTRL + Z</span>
						</span>
					</li>
					<li class="context-menu__item" id="cm-edit-re">
						<span class="context-menu__link">
							Redo<span class="context-menu__shortcut">CTRL + Y</span>
						</span>
					</li>
					<div class="context-menu__break"></div>
					<li class="context-menu__item" id="cm-edit-cu">
						<span class="context-menu__link">
							Cut<span class="context-menu__shortcut">CTRL + X</span>
						</span>
					</li>
					<li class="context-menu__item" id="cm-edit-co">
						<span class="context-menu__link">
							Copy<span class="context-menu__shortcut">CTRL + C</span>
						</span>
					</li>
					<li class="context-menu__item" id="cm-edit-pa">
						<span class="context-menu__link">
							Paste<span class="context-menu__shortcut">CTRL + V</span>
						</span>
					</li>
					<li class="context-menu__item" id="cm-edit-se">
						<span class="context-menu__link">
							Select All<span class="context-menu__shortcut">CTRL + A</span>
						</span>
					</li>
					<div class="context-menu__break"></div>
				</div>
				<div id="cm-settings" class="context-menu__filter">
					<li class="context-menu__item" id="cm-settings-cl">
						<span class="context-menu__link">
							Close Window
						</span>
					</li>
					<li class="context-menu__item" id="cm-settings-re">
						<span class="context-menu__link">
							Reload
						</span>
					</li>
					<div class="context-menu__break"></div>
				</div>
				<li class="context-menu__item" onclick="createWindow('explorer.php?loc=/',600,400,+this.parentElement.parentElement.style.left.slice(0, -2)-300,+this.parentElement.parentElement.style.top.slice(0, -2)-200); closeContextMenu();">
					<span class="context-menu__link">
						File Explorer
					</span>
				</li>
				<li class="context-menu__item" onclick="createWindow('info.php',600,400,+this.parentElement.parentElement.style.left.slice(0, -2)-300,+this.parentElement.parentElement.style.top.slice(0, -2)-200); closeContextMenu();">
					<span class="context-menu__link">
						Info
					</span>
				</li>
				<li class="context-menu__item" onclick="createWindow('settings.php',600,400,+this.parentElement.parentElement.style.left.slice(0, -2)-300,+this.parentElement.parentElement.style.top.slice(0, -2)-200); closeContextMenu();">
					<span class="context-menu__link">
						Settings
					</span>
				</li>
				<li class="context-menu__item" onclick="window.location = 'logout.php';">
					<span class="context-menu__link">
						Logout
					</span>
				</li>
			</ul>
		</nav>
		<nav id="context-menu-new" class="sub-context-menu">
			<ul class="context-menu__items">
				<li class="context-menu__item" id="cm-files-ne-fo">
					<span class="context-menu__link">
						Folder
					</span>
				</li>
				<li class="context-menu__item" id="cm-files-ne-fi">
					<span class="context-menu__link">
						File
					</span>
				</li>
				<li class="context-menu__item" id="cm-files-ne-up">
					<span class="context-menu__link">
						Upload
					</span>
				</li>
			</ul>
		</nav>
	</body>
</html>