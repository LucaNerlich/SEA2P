<?php
include '../config/config.php';
include '../config/header.php';

if (isset($_POST["bike_id"]))
{
	$bike_id = intval($_POST["bike_id"]);
	$km = intval($_POST["kilometer"]);
	
	if ($bike_id > 0 && $km > 0)
	{
		query("INSERT INTO drive_history (bike_id, client_id, kilometers) VALUES ($bike_id," . $_SESSION["user"]["client_id"] . ",$km)");
		query("UPDATE bike_article SET current_km = current_km + $km WHERE bike_id = $bike_id");
		echo message("Eintrag erfolgreich!",1);
	}
	else
	{
		echo message("Deine Eingaben sind fehlerhaft!",2);
	}
}

echo "<div class='jumbotron'>";
echo "<h1>Gefahrene Kilometer eintragen</h1>";
echo "<form action='trip.php' method='post'>";
$bikes = query("SELECT * FROM bike WHERE client_id = " . $_SESSION["user"]["client_id"]);



echo "<table>";
echo "<tr><td>Welches Bike bist du gefahren?</td><td>";
echo "<select name='bike_id'>";
foreach ($bikes as $bike)
{
	echo "<option value='" . $bike["bike_id"] . "'>" . $bike["name"] . " (" . $bike["serial_number"] . ")</option>";
}	
	
echo "</select></td></tr>";
echo "<tr><td>Wie viele Kilometer bist du gefahren?</td><td><input type='number' name='kilometer' min='1' step='1'></td></tr>";
echo "<tr><td></td><td><input class='submit' type='submit' value='Ich war sportlich!'></td></tr>";
echo "</table>";
echo "</form>";


echo "</div>";
include '../config/footer.php';
?>