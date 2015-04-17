<?php
include 'config/config.php';
include 'config/header.php';

if ($_SESSION["state"] == 1) {

    if (!isset($_POST['step'])) {

        echo '<div id="damagereport">';
        echo '<form action="form.php" method="post">';
        echo '            <select name="type" size="1">';
//echo '                <option>---</option>';
        echo '                <option>Schadenmeldung</option>';
//echo '               <option>Message</option>';
        echo '            </select>';

        echo '<br>';

        /* BIKE */

        $bikes = query("SELECT * FROM bike WHERE bike.client_id = " . $_SESSION['user']['client_id']);

        echo '            <label for="bike">bike:</label> ';
        echo '            <select name="bike" size="1">';
        echo '                <option>Bike ausw&auml;hlen</option>';

        foreach ($bikes as $bike) {
            echo '<option>' . $bike["name"] . '</option>';
        }

        echo '</select>';

        echo '<br>';

        echo '<input type="hidden" name="step" value="1">';
        echo '<input type="submit" value="next">';
        echo '</form>';
        echo '</div>';

    } elseif ($_POST['step'] == 1) {

        echo '<div id="damagereport">';
        echo '<form action="form.php" method="post">';
        echo '<label for="bike">bike:</label> ';
        echo $_POST['bike'];

        echo '<br>';

        echo '<label for="topic">topic:</label> ';
        echo '<input type="text" name="topic" size="30" maxlength="30">';

        echo '<br>';

        echo '<textarea name="message" cols="50" rows="10"></textarea>';

        echo '<br>';

        echo '<input type="hidden" name="step" value="2">';
        echo '<input type="submit" value="send">';
        echo '</form>';
        echo '</div>';

    } elseif ($_POST['step'] == 2) {

        echo 'DONE';

//        echo '            <label for="serial">S/N:</label> ';
//        if ($_POST['name'] != "Bike ausw&auml;hlen") {
//
//            $serial = query("SELECT serial_number FROM bike WHERE bike.name = '" . $_POST['name'] . "' AND bike.client_id = " . $_SESSION['user']['client_id']);
//
//            echo $serial;
//
//        }

        //echo '            <input type="text" id="serial" size="10" maxlength="10" pattern="[0-9]{10}">';

    }

}

include 'config/footer.php';
?>