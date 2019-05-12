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
				user-select: none;
				height: 100vh;
				width: 100vw;
			}
			.window {
				background-color: #A9BCF5;
				padding: 2px;
				position: absolute;
				display: flex;
				flex-flow: column;
				min-height: 250px;
				min-width: 400px;
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
				flex: 0 1 auto;
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
				border-radius: 5px;
			}
			.windowicon {
				display: inline-block;
				width: 20px;
				text-align: center;
			}
			.windowtitle {
				float: left;
			}
			.windowclose {
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
			#context-menu {
				position: absolute;
				z-index: 1000000;
				display: none;
				background-color: white;
				box-shadow: rgba(0, 0, 0, 0.5) 4px 4px 2px -2px;
				border: 1px solid #CDCDCD;
				width: 200px;
			}
			#context-menu.active {
				display: block;
			}
			.context-menu__items {
				list-style: none;
				padding: 0;
				margin-block-start: 3px;
				margin-block-end: 3px;
			}
			.context-menu__item {
				padding: 4px 20px;
				font-size: 9pt;
				text-decoration: none;
				font-family: Sans-serif;
			}
			.context-menu__item:hover {
				background-color: #eeeeee;
			}
			.context-menu__break {
				border-bottom: 1px solid #eeeeee;
				margin: 3px 0;
			}
		</style>
	</head>
	<body>
		<div id="bgtext">GXa File Manager</div>
		<script type="text/javascript">
			var windowCount = 0;
			function createWindow(target,wWidth,wHeight,wX,wY) {
				windowCount++;
				var newWindow = document.createElement('div');
				newWindow.id = "dw-" + windowCount;
				newWindow.classList.add('window');
				newWindow.onmousedown = function() { moveToTop(this); }
				newWindow.style.zIndex = topZIndex;
				if (wWidth == undefined) {
					wWidth = 600;
				}
				newWindow.style.width = wWidth + "px";
				if (wHeight == undefined) {
					wHeight = 400;
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
				newWindow.innerHTML = '<div class="windowheader" id="dw-' + windowCount + '-hd"><div class="windowtitle" id="dw-' + windowCount + '-ti"><div class="windowicon"><i class="fa fa-spinner fa-fw"></i></div>Loading</div><div class="windowclose" onclick="this.parentElement.parentElement.remove();"><i class="fa fa-times"></i></div></div><div class="windowcontent"><div class="interactionshield" id="dw-' + windowCount + '-is"></div><iframe src="' + target + '" class="windowframe" id="dw-' + windowCount + '-cw"></iframe><div class="loadicon" id="dw-' + windowCount + '-li"><i class="fa fa-spinner fa-pulse fa-fw"></i></div></div>';
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
				}
			
				function closeDragElement() {
					// stop moving when mouse button is released:
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
				var contextMenu = document.getElementById("context-menu"),
					cmSettings = document.getElementById("cm-settings"),
					cmSettingsCl = document.getElementById("cm-settings-cl"),
					cmtWH = clickInsideElement(e, 'windowheader');
				e.preventDefault();
				var position = getPosition(e);
				contextMenu.classList.add("active");
				changeISStatus(true);
				contextMenu.style.top = position.y + "px";
				contextMenu.style.left = position.x + "px";
				if (cmtWH !== false) {
					cmSettings.style.display = "block";
					cmSettingsCl.onclick = function() {
						cmtWH.parentElement.remove();
						closeContextMenu();
					}
				} else {
					cmSettings.style.display = "none";
				}
			} );
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
				var contextMenu = document.getElementById("context-menu");
				contextMenu.classList.remove("active");
				changeISStatus(false);
			}
			
			<?php
				if (isset($_COOKIE['user']) && isset($_COOKIE['password'])) {
					if ($_COOKIE['user'] !== '' && $_COOKIE['password'] !== '') {
						echo "createWindow('explorer.php?loc=/',600,400,'center','center');";
					} else {
						echo "createWindow('login.php',400,250,'center','center');";
					}
				} else {
					echo "createWindow('login.php',400,250,'center','center');";
				}
			?>
		</script>
		<nav id="context-menu">
			<ul class="context-menu__items">
				<div id="cm-settings" class="context-menu__filter">
					<li class="context-menu__item" id="cm-settings-cl">
						<span class="context-menu__link">
							Close Window
						</span>
					</li>
					<div class="context-menu__break"></div>
				</div>
				<li class="context-menu__item" onclick="createWindow('explorer.php?loc=/',600,400,+this.parentElement.parentElement.style.left.slice(0, -2)-300,+this.parentElement.parentElement.style.top.slice(0, -2)-200); closeContextMenu();">
					<span class="context-menu__link">
						New Window
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
	</body>
</html>