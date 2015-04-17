<?php
include 'config/config.php';
include 'config/header.php';

if ($_SESSION["state"] == 0) {

    echo '<div id="damagereport">';
    echo '<form action="form.php" method="post">';
    echo '            <select name="type" size="1">';
//echo '                <option>---</option>';
    echo '                <option>Schadenmeldung</option>';
//echo '               <option>Message</option>';
    echo '            </select>';

    echo '<br>';

/* BIKE */

    //$bikes = query("SELECT serial_number FROM bike WHERE bike.client_id = " . $_SESSION['user']['client_id']);

    echo '            <label for="bike">Bike:</label> ';
    echo '            <select name="bike" size="1">';
    echo '                <option>Bike ausw&auml;hlen</option>';

    foreach ($bikes as $bike) {
        echo '<option>' . $bike . '</option>';
    }

    echo '            </select>';

    echo '<br>';

    echo '            <label for="serial">S/N:</label> ';
    echo '            <input type="text" id="serial" size="10" maxlength="10" pattern="[0-9]{10}">';

    echo '            <input type="submit" value="send">';
    echo '        </form>';
    echo '</div>';

}

include 'config/footer.php';
?>