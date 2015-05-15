<?php
include '../config/config.php';
include '../config/header.php';


echo "<div class='jumbotron'>";
echo "<h1>Nachricht senden</h1>";

  echo "</select></td></tr>";
    echo "<tr><td>Bitte trage deine Nachricht ein:</td><td><input type='text' name='nachricht'></td></tr>";
    echo "<tr><td></td><td><input class='submit' type='submit' value='Message absenden!'></td></tr>";
    echo "</table>";
    echo "</form>";


        $sql = "INSERT INTO message (created_on, text, topic_id) VALUES ('" . escape($_POST["topic"]) . "'," . $_SESSION['user']['client_id'] . "," . $_POST["message"] . ")";
            $topic_id = query($sql);

            $sql = "INSERT INTO message (created_on, text, topic_id) VALUES (....,'" . escape($_POST["message"]) . "',$topic_id)";

            if (query($sql) !== false)
            {
            	echo message("Nachricht erfolgreich versendet!",1);
            }
            else
            {
            	echo message("Es trat ein Fehler auf!",2);
            }
        }


}
include 'config/footer.php';
?>