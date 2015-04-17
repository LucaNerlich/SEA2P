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

function query($query,$key=null)
{
	global $DB;
	if ($DB == null)
	{
		die("Es trat ein Fehler auf, keine Verbindung vorhanden!");
	}
	$query = trim($query);
	//echo "<p style='border:1px solid black;'>".$query."</p>";
	// Abfrage mit === weil auch Typabfrage wichtig ist, da Strpos false (0) zurück liefert, wenn der String nicht gefunden wurde
	// Also würde die Abfrage == 0 fälschlicherweise auch true zurückgeben, wenn der String eigentlich nicht gefunden wurde.
	if (stripos($query, "INSERT") === 0)
	{
		// 		echo "INSERT";
		if (mysqli_query($DB,$query))
		{
			//echo ">>>>>>" . mysqli_insert_id($DB) . "<<<<<<";
			return mysqli_insert_id($DB);
		}
		else
		{
			die("Es trat ein SQL-Fehler auf: " . mysqli_error($DB) . "<br>$query");
		}
	}
	else if (stripos($query, "UPDATE") === 0 || stripos($query, "DELETE") === 0)
	{
		//echo "DELETE,UPDATE";
		if (mysqli_query($DB,$query))
		{
			//echo "true";
			return mysqli_affected_rows($DB);
		}
		else
		{
			die("Es trat ein SQL-Fehler auf: " . mysqli_error($DB) . "<br>$query");
		}
	}
	else if (stripos($query, "SELECT") === 0)
	{
		// 		echo "SELECT";

		$result = mysqli_query($DB,$query);
		if ($result)
		{
			// 			echo "RESULT";
			$data = array();
			while($ar = mysqli_fetch_assoc($result))
			{
				// 				echo "DATA";
				if ($key == null)
				{
					array_push($data,$ar);
				}
				else
				{
					$data[$ar[$key]] = $ar;
				}
			}
			// 			print_r($data);
			// 			die();
			//echo sizeof($data) . count($data) . empty($data) . "<pre>";print_r($data);echo "</pre>";
			//return sizeof($data) == 0?false:$data;
			return $data;
		}
		else
		{
			//throw new Exception("Es trat ein SQL-Fehler auf: " . mysqli_error($DB) . "<br>$query");
			die("Es trat ein SQL-Fehler auf: " . mysqli_error($DB) . "<br>$query");
		}
	}
	else {
		return mysqli_query($DB,$query);
		//die("Unbekannte Funktion im SQL-Statement: " . $query);
	}
	return false;
}

function escape($str)
{
	global $DB;
	return mysqli_real_escape_string($DB,$str);
}

?>