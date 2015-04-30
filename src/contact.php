<?php
include '../config/config.php';
include '../config/header.php';

echo '<div id="damagereport" class="jumbotron">';
echo "<h1>Kontakt mit Hersteller aufnehmen</h1>";

if ($_SESSION["state"] == 1) {

    if (!isset($_POST['step'])) {

        echo '<form action="contact.php" method="post">';

        echo '<table>';
        echo '<tr><td></td>';
        echo '<td><select name="type" size="1">';
        echo '<option value="0">-- bitte auswählen --</option>';
        echo '<option value="1">Schadenmeldung</option>';
        echo '<option value="2">Nachricht senden</option>';
        echo '</select></td>';

        echo '<tr><td><input type="hidden" name="step" value="1"></td>';
        echo '<td><input class="submit" type="submit" value="next"></td></tr>';
        echo '</form>';

    } elseif ($_POST['step'] == 1 && $_POST['type'] == 1) {

        echo '<form action="contact.php" method="post">';

        echo '<table>';
        echo '<tr><td></td>';

        /* BIKE */
        $bikes = query("SELECT * FROM bike WHERE bike.client_id = '" . $_SESSION['user']['client_id'] . "'");

        echo '<tr><td><label for="bike">bike:</label></td>';
        echo '<td><select name="bike" size="1">';
        echo '<option>Bike ausw&auml;hlen</option>';

        foreach ($bikes as $bike) {
            echo '<option value="' . $bike["bike_id"] . '">' . $bike["name"] . '</option>';
        }

        echo '</select></td></tr>';

        echo '<tr><td><input type="hidden" name="step" value="2"></td>';
        echo '<td><input class="submit" type="submit" value="next"></td></tr>';

        echo '</form>';

    } elseif ($_POST['step'] == 2) {

        /* ID */
        echo '<form action="contact.php" method="post">';

        echo '<table style="width: 90%">';
        echo '<tr><td><label for="bike">id:</label></td>';
        echo '<td>' . $_POST['bike'] . '</td></tr>';

        /* NAME */
        echo '<tr><td><label for="name">bike:</label></td>';
        $serial = query("SELECT name FROM bike WHERE bike.bike_id = '" . $_POST['bike'] . "'");
        echo '<td>' . $serial[0]['name'] . '</td></tr>';

        /* S/N */
        echo '<tr><td><label for="serial_number">s/n:</label></td>';
        $serial = query("SELECT serial_number FROM bike WHERE bike.bike_id = '" . $_POST['bike'] . "'");
        echo '<td>' . $serial[0]['serial_number'] . '</td></tr>';

        /* ARTICLES */
        $articles = query("SELECT article.article_id, article.name FROM bike_article, article WHERE bike_article.bike_id = '" . $_POST['bike'] .
            "' AND bike_article.article_id = article.article_id");

        echo '<tr><td><label for="article">article:</label></td>';
        echo '<td><select name="article" size="1">';
        echo '<option>Artikel ausw&auml;hlen</option>';

        foreach ($articles as $article) {
            echo '<option value="' . $article["article_id"] . '">' . $article["name"] . '</option>';
        }

        echo '</select></td></tr>';

        /* TOPIC */
        echo '<tr><td><label for="topic">topic:</label></td>';
        echo '<td><input type="text" name="topic" size="30" maxlength="30"></td></tr>';

        /* MESSAGE */
        echo '<td><td><textarea name="message" maxlength="500" rows="10" style="width: 90%"></textarea></td></tr>';

        /* BUTTON */
        echo '<tr><td><input type="hidden" name="step" value="3"></td>';
        echo '<td><input class="submit" type="submit" value="send"></td></tr>';
        echo '</table>';
        echo '</form>';

    } elseif ($_POST['step'] == 3) {

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

} else {
    echo 'ausgeloggt!';
}

echo '</div>';

include '../config/footer.php';
?>