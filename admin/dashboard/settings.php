<?php
	require 'auth.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<style>
			/* tables */
			table.tablesorter {
				font-family:arial;
				background-color: #CDCDCD;
				margin:10px 0pt 15px;
				font-size: 8pt;
				width: 100%;
				text-align: left;
			}
			table.tablesorter thead tr th, table.tablesorter tfoot tr th {
				background-color: #e6EEEE;
				border: 1px solid #FFF;
				font-size: 8pt;
				padding: 4px;
			}
			table.tablesorter thead tr .header {
				background-image: url(bg.gif);
				background-repeat: no-repeat;
				background-position: center right;
				cursor: pointer;
			}
			table.tablesorter tbody td {
				color: #3D3D3D;
				padding: 4px;
				background-color: #FFF;
				vertical-align: top;
			}
			table.tablesorter tbody tr.odd td {
				background-color:#F0F0F6;
			}
			table.tablesorter thead tr .headerSortUp {
				background-image: url(asc.gif);
			}
			table.tablesorter thead tr .headerSortDown {
				background-image: url(desc.gif);
			}
			table.tablesorter thead tr .headerSortDown, table.tablesorter thead tr .headerSortUp {
			background-color: #8dbdd8;
			}
			.sortable {
				cursor: pointer;
			}
			.sortIcon {
				float: right;
				padding-right: 5px;
			}
		</style>
		<link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.css">
		<script src="jquery-3.2.1.min.js"></script>
		<script src="jquery.tablesorter.min.js"></script>
	</head>
	<body onLoad="top.inload('stop')">
		<table class="tablesorter" id="plugintable">
			<thead>
				<tr>
					<th data-sorter="false"><input type="checkbox" onClick="toggleAll(this)" id="toggleAllCheck" /></th>
					<th onclick="sortChange();" class="sortable">Name<div class="sortIcon" id="s1"><i class="fa fa-sort" aria-hidden="true"></i></div></th>
					<th onclick="sortChange();" class="sortable">Status<div class="sortIcon" id="s2"><i class="fa fa-sort" aria-hidden="true"></i></div></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="checkbox" class="settingInput" onClick="toggleSelected(this,'authenticator.php')" /></td>
					<td>Authenticator</td>
					<td>Active</td>
				</tr>
				<tr>
					<td><input type="checkbox" class="settingInput" onClick="toggleSelected(this,'filemanager.php')" /></td>
					<td>File Manager</td>
					<td>Active</td>
				</tr>
			</tbody>
		</table>
		<script>
		
		</script>
		<script type="text/javascript">
		$(function(){
			$("#plugintable").tablesorter();
		});
		</script>
		<script type="text/javascript">
		function sortChange() {
			var sortObjects = 2,
				sortList = {
					'ascending': '<i class="fa fa-sort-asc" aria-hidden="true"></i>',
					'descending': '<i class="fa fa-sort-desc" aria-hidden="true"></i>',
					'none': '<i class="fa fa-sort" aria-hidden="true"></i>'
				};
			
			window.setTimeout(function(){
				sortObjects++;
				for (i = 1; i < sortObjects; i++) { 
					document.getElementById('s' + i).innerHTML = sortList[document.getElementById('s' + i).parentElement.parentElement.getAttribute('aria-sort')];
				}
			},1);
		}
		function toggleAll(x) {
			var settingInput = document.getElementsByClassName("settingInput");
			for (var i = 0; i < settingInput.length; i++) {
				settingInput[i].checked = x.checked;
			}
		}
		var selectedPlugins = [];
		function toggleSelected(x,y) {
			if (x.checked || x == true) {
				if (searcharray(y,selectedPlugins)) { } else {
					selectedPlugins.push(y);
				}
			} else {
				var arrayItem = selectedPlugins.indexOf(y);
				if (arrayItem > -1) {
					selectedPlugins.splice(arrayItem, 1);
				}
			}
			changeSelectStatus();
		}
		function changeSelectStatus() {
			var fileInput = document.getElementsByClassName("settingInput"),
				arrchecked = false,
				arrunchecked = false,
				toggleAllCheck = document.getElementById("toggleAllCheck");
			for(var ii = 0; ii < fileInput.length; ii++) {
				if (fileInput[ii].checked == true) {
					arrchecked = true;
				} else if (fileInput[ii].checked == false) {
					arrunchecked = true;
				}
			}
			if (arrchecked == true && arrunchecked == true) {
				toggleAllCheck.indeterminate = true;
				toggleAllCheck.checked = true;
			} else if (arrchecked == true && arrunchecked == false) {
				toggleAllCheck.indeterminate = false;
				toggleAllCheck.checked = true;
			} else if (arrchecked == false && arrunchecked == true) {
				toggleAllCheck.indeterminate = false;
				toggleAllCheck.checked = false;
			}
			return arrchecked + ":" + arrunchecked;
		}
		function searcharray(search_for_string, array_to_search) 
		{
		    for (var i=0; i<array_to_search.length; i++) 
			{
		        if (array_to_search[i].match(search_for_string))
				{ 
					return true;
				}
		    }
		 
		    return false;
		}
		</script>
	</body>
</html>