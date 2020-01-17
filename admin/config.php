<?php

	$gxaversion = 'v0.3.0-alpha';
	
	$adminfoldername = 'admin';

	// ------------------------------------ //
	//       ACCOUNT / LOGIN SETTINGS       //
	// ------------------------------------ //
	
	$usemysql = true;
	// Turning this on will allow the use of a MySQL database to hold login information, settings, etc. It will also enable multiple usually disabled features.
	// This may become required in the future.

	$mysqluser = 'root';

	$mysqlpassword = 'password';

	$mysqldatabase = 'gxa';
	// Create this database and accompanying MySQL user before launching GXa File Manager.

	$mysqlip = 'localhost';

	// THE USERNAME AND PASSWORD OF THE ADMINISTRATOR ACCOUNT WILL BE SET TO THE VALUES OF THE FIRST LOGIN TO THE SERVER IF USING SQL

	// Only edit below this line if you are NOT using MySQL (ALTHOUGH MYSQL ISN'T REQUIRED, IT IS HIGHLY RECOMMENDED. SOME FUNCTIONS MAY BE UNUSABLE OTHERWISE!)
	// NOTE: THESE WILL ONLY BE USED WHEN NOT USING MYSQL

	$adminname = 'admin';

	$adminpassword = 'password';
	
	// ------------------------------------ //
	//         CODE EDITOR SETTINGS         //
	// ------------------------------------ //
	
	$useautotab = true;
	// Automagically tab to orginize your code better

?>
