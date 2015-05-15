<?php
include '../config/config.php';
include '../config/header.php';

echo "<h1>Welchen Kunden moechtest du bearbeiten?</h1>";
echo "<form action='editUser.php' method='post'>";
$kunden = query("SELECT * FROM client");
echo "<br>";
echo "<select name='client'>";

foreach ($kunden as $kunde) {
    echo "<option value='" . $kunde["client_id"] . "'>" . $kunde["name"] . ", " . $kunde["surname"] . " - " . $kunde["client_id"] . " </option>";
}

echo "</select>";

echo "<input class='submit' type='submit' value='Kunden auswaehlen!'>";
echo "</form>";

if (isset($_POST["client"])){
    // $kunde hier nicht gesetzt. Soll der aktuell oben (ausm select) ausgewaehlte sein.
    $bike = query("SELECT * FROM bike WHERE client_id = " . $_kunde["client_id"]);
    echo "<table>";
    echo "<tr>";
    echo "<td>Kunde: </td>";
    echo "<td>" . $kunde["surname"] . "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Bike: </td>";
    echo "<td>" . $bike["name"] . "</td>";
    echo "</tr>";
    echo "</table>";

}

include '../config/footer.php';
?>