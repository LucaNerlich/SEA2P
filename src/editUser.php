<?php
include '../config/config.php';
include '../config/header.php';

$kunden = query("SELECT * FROM client");

echo "<h1>Welchen Kunden moechtest du bearbeiten?</h1>";

echo "<br>";

echo "<select name='client'>";

foreach ($kunden as $kunde) {
    echo "<option value='" . $kunde["client_id"] . "'>" . $kunde["name"] . ", " . $kunde["surname"] . " - " .  $kunde["client_id"]  . " </option>";
}

echo "</select>";

include '../config/footer.php';
?>