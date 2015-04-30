<?php
include '../config/config.php';
include '../config/header.php';

echo '<div id="damagereport" class="jumbotron">';
echo "<h1>Kontakt mit Hersteller aufnehmen</h1>";

if ($_SESSION["state"] == 1) {

    /* variables */
    $error = -1;
    $donothing = 0;
    $senddamage = 1;
    $sendmessage = 2;


    /* choose contact option */
    if (!isset($_POST['step'])) {

        echo '<form action="contact.php" method="post">';

        echo '<table>';
        echo '<tr><td></td>';
        echo '<td><select name="type" size="1">';
        echo '<option value="' . $donothing . '">-- bitte ausw&auml;hlen --</option>';
        echo '<option value="' . $senddamage . '">Schadenmeldung</option>';
        echo '<option value="' . $sendmessage . '">Nachricht senden</option>';
        echo '</select></td>';

        echo '<tr><td><input type="hidden" name="step" value="1"></td>';
        echo '<td><input class="submit" type="submit" value="next"></td></tr>';
        echo '</form>';

    }

/* send damage */
    elseif ($_POST['type'] == $senddamage) {

        if ($_POST['step'] == 1) {

            echo '<form action="contact.php" method="post">';

            echo '<table>';
            echo '<tr><td></td>';

            /* BIKE */
            $bikes = query("SELECT * FROM bike WHERE bike.client_id = '" . $_SESSION['user']['client_id'] . "'");

            echo '<tr><td><label for="bike">bike:</label></td>';
            echo '<td><select name="bike" size="1">';
            echo '<option value="' . $donothing . '">-- bitte ausw&auml;hlen --</option>';

            foreach ($bikes as $bike) {
                echo '<option value="' . $bike["bike_id"] . '">' . $bike["name"] . '</option>';
            }

            echo '</select></td></tr>';

            echo '<tr><td><input type="hidden" name="type" value="' . $senddamage . '"></td>';
            echo '<tr><td><input type="hidden" name="step" value="2"></td>';
            echo '<td><input class="submit" type="submit" value="next"></td></tr>';

            echo '</form>';

        } elseif ($_POST['step'] == 2 && $_POST['bike'] != $donothing) {

            /* ID */
            echo '<form action="contact.php" method="post">';

            echo '<table style="width: 90%">';
            echo '<tr><td><label for="bike">id:</label></td>';
            echo '<td>' . $_POST['bike'] . '</td></tr>';

            /* NAME */
            echo '<tr><td><label for="name">bike:</label></td>';
            $name = query("SELECT name FROM bike WHERE bike.bike_id = '" . $_POST['bike'] . "'");
            echo '<td>' . $name[0]['name'] . '</td></tr>';

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
            echo '<tr><td><input type="hidden" name="type" value="' . $senddamage . '"></td>';
            echo '<tr><td><input type="hidden" name="step" value="3"></td>';
            echo '<td><input class="submit" type="submit" value="send"></td></tr>';
            echo '</table>';
            echo '</form>';

        } elseif ($_POST['step'] == 2 && $_POST['bike'] == $donothing) {

            echo '<form action="contact.php" method="post">';

            echo '<table>';
            echo '<tr><td>BITTE EIN G&UumlLTIGES FAHRRAD AUSW&Auml;HLEN</td></tr>';
            echo '<tr><td><input type="hidden" name="type" value="' . $senddamage . '"></td>';
            echo '<tr><td><input type="hidden" name="bike" value="' . $error . '"></td>';
            echo '<tr><td><input type="hidden" name="step" value="1"></td>';
            echo '<td><input class="submit" type="submit" value="try again"></td></tr>';
            echo '</form>';

        }
        elseif ($_POST['step'] == 3) {

            echo 'DONE';

            echo ' ' . $_POST['topic'] . '';
            echo ' ' . $_POST['message'] . '';

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
    } elseif ($_POST['type'] == $sendmessage) {

        if ($_POST['step'] == 1) {

            /* ID */
            echo '<form action="contact.php" method="post">';

            echo '<table style="width: 90%">';
            echo '<tr><td><label for="userid">user id:</label></td>';
            echo '<td>' . $_SESSION['user']['client_id'] . '</td></tr>';

            /* NAME */
            echo '<tr><td><label for="name">name:</label></td>';
            echo '<td>' . $_SESSION['user']['name'] . ", " . $_SESSION['user']['surname'] . '</td></tr>';

            /* TOPIC */
            echo '<tr><td><label for="topic">topic:</label></td>';
            echo '<td><input type="text" name="topic" size="30" maxlength="30"></td></tr>';

            /* MESSAGE */
            echo '<td><td><textarea name="message" maxlength="500" rows="10" style="width: 90%"></textarea></td></tr>';

            /* BUTTON */
            echo '<tr><td><input type="hidden" name="type" value="' . $sendmessage . '"></td>';
            echo '<tr><td><input type="hidden" name="step" value="2"></td>';
            echo '<td><input class="submit" type="submit" value="send"></td></tr>';
            echo '</table>';
            echo '</form>';
        }

        elseif ($_POST['step'] == 2) {

            echo 'DONE2';

            echo ' ' . $_POST['topic'] . '';
            echo ' ' . $_POST['message'] . '';

        }
    }
} else {
    echo 'ausgeloggt!';
}

echo '</div>';

include '../config/footer.php';
?>