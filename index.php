<?php
include 'config/config.php';
include 'config/header.php';

/*
if (isset($_GET["page"]) && file ^ _exists($_GET["page"])) {
    include $_GET["page"];
} else {
    echo '<div class="jumbotron">
  <h1>HAW Bike Tracking</h1>
  <p>Software Entwicklung 2</p>
  <p><a class="btn btn-primary btn-lg" href="src/learnMore.html" role="button">Unser Konzept</a></p>
</div>';
}
*/
if ($_SESSION["state"] == 0)
{
?>
<div align="center">
    <div class="jumbotron">
        <h1>HAW Bike Tracking</h1>

        <p>Bitte logge dich zun&auml;chst ein!</p>
    </div>
</div>

<?php 
}
else
{
	echo "<div class='jumbotron'>";
	echo "<h1>Dein Dashboard</h1>";
	
	/*
	 * Aktuelle Kilometer der Fahrrad Teile
	 */
	echo "<h2>Deine Bikes</h2>";
	echo "<table class='table table-striped'>";
	echo "<tr><th>Name</th><th>Serial Number</th><th>Kilometers</th></tr>";
	$bikes = query("SELECT bike.*, sum(dh.kilometers) as current_km FROM bike LEFT JOIN drive_history dh ON bike.bike_id = dh.bike_id WHERE bike.client_id = " . $_SESSION["user"]["client_id"] . " GROUP BY dh.bike_id");
	foreach ($bikes as $bike)
	{
		$parts = query("SELECT ba.*, a.* FROM bike_article ba LEFT JOIN article a ON ba.article_id = a.article_id WHERE ba.bike_id = " . $bike["bike_id"]);
		echo "<tr><td>" . $bike["name"] . "</td><td>" . $bike["serial_number"] . "</td><td>" . $bike["current_km"] . " km</td></tr>";
		if (sizeof($parts) > 0)
		{
			echo "<tr><td colspan='3'>";
			echo "<table class='table table-striped'>";
			echo "<tr><th>Name</th><th>Wear Out</th><th>Current KM</th><th>Percentage</th></tr>";
			foreach($parts as $part)
			{
				if ($part["wearout"] != null)
				{
					$p_zahl = round(($part["current_km"] / $part["wearout"]) * 100);
					$percentage = $p_zahl . " %";
					if ($p_zahl < 50)
					{
						$percentage = message($percentage,1,false);
					}
					elseif ($p_zahl < 90)
					{
						$percentage = message($percentage,2,false);
					}
					else
					{
						$percentage = message($percentage,3,false);
					}
				}
				else
				{
					$percentage = "<i>not available</i>";
				}
				echo "<tr><td>" . $part["name"] . "</td><td>" . ($part["wearout"]==null?"-":$part["wearout"]) . "</td><td>" . $part["current_km"] . "</td><td>$percentage</td></tr>";
			}
			echo "</table>";
			echo "</td></tr>";
		}
	}
	echo "</table>";
	/*
	 * Fitnesstatistik
	 */
	echo "<h2>Deine Fitness</h2>";
	echo "<table class='table table-striped'>";
	
	$dates = query("SELECT SUM(kilometers) as summe, MONTH(created_on) as monat, year(created_on) as jahr FROM drive_history WHERE client_id = " . $_SESSION["user"]["client_id"] . " AND created_on > DATE_SUB(CURDATE(),INTERVAL 1 YEAR) GROUP BY MONTH(created_on) ORDER BY year(created_on), month(created_on) ASC");
	echo "<tr>";
	foreach ($dates as $date)
	{
		$dates_c[$date["jahr"] . $date["monat"]] = $date["summe"];
	}
	
	//$dates_conf
	for ($i = 12; $i >= 0; $i --)
	{
		echo "<th>" . date("Y-m", mktime(0, 0, 0, date("m")-$i, date("d"),   date("Y"))) . "</th>";
		//echo $i . "--";
		//echo date("Y-m", mktime(0, 0, 0, date("m")-$i, date("d"),   date("Y")));
	}
	echo "</tr><tr>";
	for ($i = 12; $i >= 0; $i --)
	{
		$c_str = date("Yn", mktime(0, 0, 0, date("m")-$i, date("d"),   date("Y")));
		echo "<td>" . (isset($dates_c[$c_str])?$dates_c[$c_str]:"0") . "</td>";
	//echo $i . "--";
	//echo date("Y-m", mktime(0, 0, 0, date("m")-$i, date("d"),   date("Y")));
	}
	echo "</tr>";
	//print_r($dates_c);
// 	foreach ($dates as $date)
// 	{
// 		echo "<tr><td>" . $bike["name"] . "</td><td>" . $bike["serial_number"] . "</td><td>" . $bike["current_km"] . " km</td></tr>";
// 		//for ($i = 0; $i )
// 	}
	echo "</table>";
	
	
	/*
	 * Aktuelle und vergangene Tickets
	 */
	echo "<h2>Deine Tickets</h2>";
	echo "<table class='table table-striped'>";
	echo "<tr><th>Subject</th><th>Type</th><th>Created On</th><th></th></tr>";
	$topics = query("SELECT  * from topic WHERE client_id = " . $_SESSION["user"]["client_id"] . " ORDER BY active DESC, created_on desc");
	if (sizeof($topics) == 0)
	{
		echo "<tr><td colspan='4' style='text-align:center;'><i>Keine offnenen oder geschlossenen Tickets bisher.</i></td></tr>";
	}
	else
	{
		foreach ($topics as $topic)
		{
			echo "<tr><td>" . $topic["subject"] . "</td><td>" . ($topic["type"] == 1?"Schaden":"Kontakt") . "</td><td>" . $topic["created_on"] . " km</td><td>" . ($topic["active"] == 1?"<img src='graphic/y.png'>":"") . "</td></tr>";
		
		}
	}
	
	echo "</table>";
	
	echo "</div>";
}
?>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/js/bootstrap.min.js"></script>
</body>
</html>

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
include 'config/footer.php';
?>
