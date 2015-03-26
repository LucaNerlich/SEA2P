<?php
$database["server"] = "eu-cdbr-azure-north-c.cloudapp.net";
$database["name"] = "HAWBIKEDB1";
$database["username"] = "b9e898e7fd354b";
$database["password"] = "603aec01";


$DB = mysqli_connect ($database["server"], $database["username"], $database["password"]) or die ("Keine Verbindung moeglich!");
mysqli_select_db ($DB, $database["name"]) or die ("Die Datenbank existiert nicht.");
if (!mysqli_query($DB, "create table if not exists user (user_id int not null AUTO_INCREMENT, name varchar(50), email varchar(50), type int not null default 2, primary key (user_id))"))
{
	echo "Es trat ein Fehler auf beim Anlegen: " . mysqli_error($DB);
}
?>