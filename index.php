<?php
include "spielwiese/config.php";
include "spielwiese/header.php";

if (isset($_GET["page"]) && file^_exists($_GET["page"])) {
    include $_GET["page"];
} else {
    echo '<div class="jumbotron">
  <h1>HAW Bike Tracking</h1>
  <p>Software Entwicklung 2</p>
  <p><a class="btn btn-primary btn-lg" href="src/learnMore.html" role="button">Unser Konzept</a></p>
</div>';
}

include "spielwiese/footer.php";


?>

<?php
// 	echo "<h1>Beispiel PHP-Datei</h1>";

// 	echo "<h2>Datenbankverbindung</h2>";

// 	$DB = mysqli_connect ("eu-cdbr-azure-north-c.cloudapp.net", "b9e898e7fd354b", "603aec01") or die ("Keine Verbindung moeglich!");
// 	mysqli_select_db ($DB, HAWBIKEDB1) or die ("Die Datenbank existiert nicht.");
// 	if (!mysqli_query($DB, "create table if not exists user (user_id int not null AUTO_INCREMENT, name varchar(50), email varchar(50), type int not null default 2, primary key (user_id))"))
// 	{
// 		echo "Es trat ein Fehler auf beim Anlegen: " . mysqli_error($DB);
// 	}
// 	if (!mysqli_query($DB,"INSERT INTO user (name, email) VALUES ('Fabian','fabiansim@gmx.de'),('Testperson','test@test.de');"))
// 	{
// 		echo "Es trat ein Fehler auf beim Insert: " . mysqli_error($DB);
// 	}
// 	echo "<h2>Ausgabe</h2>";
// 	$result = mysqli_query($DB,"SELECT * FROM user");
// 	while ($ar = mysqli_fetch_assoc($result))
// 	{
// 		echo "<p>#" . $ar["user_id"] . ": " . $ar["name"] . " (" . $ar["email"] . ")";
// 	}
?>
