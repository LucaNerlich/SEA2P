 <?php

 include 'config/config.php';
 include 'config/header.php';

 echo "<div class='panel panel-default'>";
    echo "<div class='panel-heading'><h2>Deine Nachrichten</h2></div>";
    echo "<div class='panel-body'>";
    echo "<table class='table table-striped'>";
    echo "<tr><th>Subject</th><th>Type</th><th>Created On</th><th></th></tr>";
    $topics = query("SELECT  * from topics WHERE client_id = " . $_SESSION["user"]["client_id"] . " ORDER BY active DESC, created_on desc");
    if (sizeof($topics) == 0) {
        echo "<tr><td colspan='4' style='text-align:center;'><i>Keine Nachrichten bisher.</i></td></tr>";
    } else {
        foreach ($topics as $topic) {
            echo "<tr><td><a href='answermessage.php?topic_id=$topic["id"]'>" . $topic["subject"] . "<a></td><td>" . ($topic["type"] == 1 ? "Schaden" : "Kontakt") . "</td><td>" . date("d.m.Y H:i", strtotime($topic["created_on"])) . "</td><td>" . ($topic["active"] == 1 ? "<img src='graphic/y.png'>" : "") . "</td></tr>";

        }
    }

    echo "</table>";
    echo "</div>";
    echo "</div>";



    echo "</div>";
    include '../config/footer.php';

}
include 'config/footer.php';
?>