<html>
<head>
</head>
<body>

<?php
include ("spielwiese/config.php");

// $var = mysqli_query($DB, "SELECT * FROM client");
// //var_dump($var);
// while ($zeile = mysqli_fetch_assoc($var))
// {
// 	echo $zeile["surname"] . "<br>";
// }
$new_id = query("INSERT INTO client (surname, name, email,password) values ('Person','Nachname','test@mail.de','asd')");
echo $new_id . "<br>";
$clients = query("SELECT * FROM client");

foreach ($clients as $client)
{
	echo $client["surname"] . "<br>";
}

?>
</body>
</html>