<?php
include '../config/config.php';
include '../config/header.php';



// Funktion zum berechnen der Distanz zwischen zwei Punkten

function distance($lat1, $lon1, $lat2, $lon2, $unit = 'K') {

	$theta = $lon1 - $lon2;
	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	$dist = acos($dist);
	$dist = rad2deg($dist);
	$miles = $dist * 60 * 1.1515;
	$unit = strtoupper($unit);

	if ($unit == "K") {
		return ($miles * 1.609344);
	} else if ($unit == "N") {
		return ($miles * 0.8684);
	} else {
		return $miles;
	}
}




if (isset($_POST["bike_id"]))
{
	$bike_id = intval($_POST["bike_id"]);
	$km = intval($_POST["kilometer"]);
	//echo $_SERVER["SCRIPT_NAME"];
	if ($bike_id > 0 && $_FILES["datei"]["size"] > 0)
	{

		// Erechne die gefahrene Strecke
		$trkpts = file_get_contents ($_FILES['datei']['tmp_name']);
		preg_match_all('/<trkpt lat=\"(.*?)\" lon=\"(.*?)\"/s', $trkpts, $matches);
		//$matches = $matches[0];
		$distance = 0;
		for ($i = 1; $i < sizeof($matches[1]); $i++)
		{
			$distance += distance($matches[1][$i-1],$matches[2][$i-1],$matches[1][$i],$matches[2][$i],'K');
		}
		
		// Verschiebe die Datei ins Usertracks-Verzeichnis
		if ($distance > 0 && $distance < 1000)
		{
			move_uploaded_file($_FILES['datei']['tmp_name'], "../usertracks/" . $_SESSION["user"]["client_id"] . "__" . date("Y_m_d_H_i_s") . ".gpx");
			echo message("Datei erfolgreich hochgeladen!",1);
			$km = round($distance,2);
		}
		else
		{
			echo message("Die Datei enthielt keine g&uuml;ltigen Wegpunkte!",2);
			$km = 0;
		}
	}
	
	if ($bike_id > 0 && $km > 0)
	{
		query("INSERT INTO drive_history (bike_id, client_id, kilometers) VALUES ($bike_id," . $_SESSION["user"]["client_id"] . ",$km)");
		$parts = query("SELECT b.current_km, a.wearout, b.article_id, a.name FROM bike_article b INNER JOIN article a ON a.article_id = b.article_id WHERE b.bike_id = $bike_id");
		
		query("UPDATE bike_article SET current_km = current_km + $km WHERE bike_id = $bike_id");
		
		
		// Erstelle Nachrichten bei überschreiten eines Kilometersstandes
		$parts = query("SELECT b.current_km, a.wearout, b.article_id, a.name FROM bike_article b INNER JOIN article a ON a.article_id = b.article_id WHERE b.bike_id = $bike_id");
		foreach ($parts as $part)
		{
			if ($part["wearout"] != null && $part["current_km"] < $part["wearout"] && ($part["current_km"] + $km) >= $part["wearout"])
			{
				$topic_id = query("INSERT INTO topics (created_on, active, subject, client_id, type) VALUES (NOW(), null, '" . $part["name"] . " pruefen'," . $_SESSION["user"]["client_id"] . ",1)");
				query("INSERT INTO message (created_on, client_sent, text, topic_id) VALUES (NOW(),1,'Lieber Kunde,\nSie sollten folgendes Teil ueberpruefen: " . $part["name"] . ", da die empfohlene Laufleistung von " . $part["wearout"] . " km ueberschritten wurde.',$topic_id)");
				//echo "Überschritt bei " . $part["name"];
			}
		}
		
		echo message("Eintrag erfolgreich!",1);
	}
	else
	{
		echo message("Deine Eingaben sind fehlerhaft!",2);
	}
}

echo "<div class='jumbotron'>";
echo "<h1>Gefahrene Kilometer eintragen</h1>";
echo "<form action='trip.php' method='post' enctype='multipart/form-data'>";
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
echo "<tr><td></td><td>ODER</td></tr>";
echo "<tr><td>Lade eine getrackte Tour hoch</td><td><input type='file' name='datei'></td></tr>";
echo "<tr><td></td><td><input class='submit' type='submit' value='Ich war sportlich!'></td></tr>";
echo "</table>";
echo "</form>";


echo "</div>";

echo "<div class='panel panel-default'>";

if (isset($_GET["trip"]))
{
	$file = $_SESSION["user"]["client_id"] . "__" . $_GET["trip"] . ".gpx";
	echo "<div class='panel-heading'><h2>Dein Track</h2></div>";
	echo "<div class='panel-body'>";
	echo '<script type="text/javascript" src="../assets/GM_Utils/GPX2GM.js"></script>';
	echo '<div id="map" class="gpxview:' . $file . ':Karte" style="width:500px;height:300px"></div><p>';
	echo '<div id="map_wp" style="width:500px;height:300px"></div></p>';
	echo "</div>";
}


echo "<div class='panel-heading'><h2>Deine gespeicherten Tracks</h2></div>";
echo "<div class='panel-body'>";
$files = scandir("../usertracks");
$str = "";
foreach ($files as $file)
{
	if (strpos($file,$_SESSION["user"]["client_id"] . "__") === 0)
	{
		$file = substr($file,strpos($file,"__")+2,strpos($file,".")-3);
		$str .= "<li><a href='trip.php?trip=$file'>$file</a></li>";
		//echo $file;
	}
}
if (sizeof($str) > 0)
{
	echo "<ul>$str</ul>";
}
else
{
	echo "<i>Du hast noch keine gespeicherten Tracks</i>";
}
echo "</div>";

echo "</div>";
//echo '<script type="text/javascript" src="../assets/GM_Utils/GPX2GM.js"></script>';
//echo '<div id="map" class="gpxview:Beispiel1.gpx:Karte" style="width:500px;height:300px"></div><p>';
//echo '<div id="map_wp" style="width:600px;height:400px"></div></p>';
include '../config/footer.php';
?>