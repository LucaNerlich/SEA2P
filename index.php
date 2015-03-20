<html>
	<head>
		<title>PHP-Beispiel</title>
		
	</head>
	<body>
<?php
	echo "<h1>Beispiel PHP-Datei</h1>";
	
	echo "<h2>Datenbankverbindung</h2>";

	$DB = mysqli_connect ("eu-cdbr-azure-north-c.cloudapp.net", "b9e898e7fd354b", "603aec01") or die ("Keine Verbindung moeglich!");
	mysqli_select_db ($DB, HAWBIKEDB1) or die ("Die Datenbank existiert nicht.");
?>
	</body>
</html>