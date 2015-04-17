<?php
include '../config/config.php';
include '../config/header.php';

echo '<div id="damagereport" class="jumbotron">';
echo "<h1>Kontakt mit Hersteller aufnehmen</h1>";

if ($_SESSION["state"] == 1) {

    if (!isset($_POST['step'])) {

        echo '<form action="contact.php" method="post">';
        echo '<select name="type" size="1">';
//echo '                <option>---</option>';
        echo '<option>Schadenmeldung</option>';
//echo '               <option>Message</option>';
        echo '</select>';

        echo '<br>';

        /* BIKE */

        $bikes = query("SELECT * FROM bike WHERE bike.client_id = '" . $_SESSION['user']['client_id'] . "'");

        echo '            <label for="bike">bike:</label> ';
        echo '            <select name="bike" size="1">';
        echo '                <option>Bike ausw&auml;hlen</option>';

        foreach ($bikes as $bike) {
            echo '<option value="' . $bike["bike_id"] . '">' . $bike["name"] . '</option>';
        }

        echo '</select>';

        echo '<br>';

        echo '<input type="hidden" name="step" value="1">';
        echo '<input type="submit" value="next">';
        echo '</form>';

    } elseif ($_POST['step'] == 1) {

        /* ID */
        echo '<form action="contact.php" method="post">';
        echo '<label for="bike">id:</label> ';
        echo $_POST['bike'];

        echo '<br>';

        /* NAME */
        echo '<label for="name">bike:</label> ';
        $serial = query("SELECT name FROM bike WHERE bike.bike_id = '" . $_POST['bike'] . "'");
        echo $serial[0]['name'];

        echo '<br>';

        /* S/N */
        echo '<label for="serial_number">s/n:</label> ';
        $serial = query("SELECT serial_number FROM bike WHERE bike.bike_id = '" . $_POST['bike'] . "'");
        echo $serial[0]['serial_number'];

        echo '<br>';

        /* ARTICLES */
        $articles = query("SELECT article.article_id, article.name FROM bike_article, article WHERE bike_article.bike_id = '" . $_POST['bike'] .
            "' AND bike_article.article_id = article.article_id");

        echo '<label for="article">article:</label> ';
        echo '<select name="article" size="1">';
        echo '<option>Artikel ausw&auml;hlen</option>';

        foreach ($articles as $article) {
            echo '<option value="' . $article["article_id"] . '">' . $article["name"] . '</option>';
        }

        echo '</select>';

        echo '<br>';

        /* TOPIC */
        echo '<label for="topic">topic:</label> ';
        echo '<input type="text" name="topic" size="30" maxlength="30">';

        echo '<br>';

        echo '<textarea name="message" cols="50" rows="10"></textarea>';

        echo '<br>';

        echo '<input type="hidden" name="step" value="2">';
        echo '<input type="submit" value="send">';
        echo '</form>';

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

} else {
    echo 'ausgeloggt!';
}

echo '</div>';

include '../config/footer.php';
?>