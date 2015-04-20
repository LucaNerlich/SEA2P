<?php
include 'config/config.php';
include 'config/header.php';

/*
if (isset($_GET["page"]) && file ^ _exists($_GET["page"])) {
    include $_GET["page"];
} else {
    echo '<div class="jumbotron">
  <h1>HAW Bike Tracking</h1>
  <p>Software Entwicklung 2</p>
  <p><a class="btn btn-primary btn-lg" href="src/learnMore.html" role="button">Unser Konzept</a></p>
</div>';
}
*/
if ($_SESSION["state"] == 0) {
    ?>
    <div align="center">
        <div class="jumbotron">
            <h1>HAW Bike Tracking</h1>

            <p>Bitte logge dich zun&auml;chst ein!</p>
        </div>
    </div>

<?php
} else {
    echo "<div class='jumbotron'>";
    echo "<h1>Dein Dashboard</h1>";
    echo "</div>";
    echo "<div class='panel panel-default'>";
    /*
     * Aktuelle Kilometer der Fahrrad Teile
     */
    echo "<div class='panel-heading'><h2>Deine Bikes</h2></div>";
    echo "<div class='panel-body'>";
    echo "<table class='table table-striped'>";
    echo "<tr><th>Name</th><th>Serial Number</th><th>Kilometers</th></tr>";
    $bikes = query("SELECT bike.*, sum(dh.kilometers) as current_km FROM bike LEFT JOIN drive_history dh ON bike.bike_id = dh.bike_id WHERE bike.client_id = " . $_SESSION["user"]["client_id"] . " GROUP BY dh.bike_id");
    foreach ($bikes as $bike) {
        $parts = query("SELECT ba.*, a.* FROM bike_article ba LEFT JOIN article a ON ba.article_id = a.article_id WHERE ba.bike_id = " . $bike["bike_id"]);
        echo "<tr><td>" . $bike["name"] . "</td><td>" . $bike["serial_number"] . "</td><td>" . $bike["current_km"] . " km</td></tr>";
        if (sizeof($parts) > 0) {
            echo "<tr><td colspan='3'>";
            echo "<table class='table table-striped'>";
            echo "<tr><th>Name</th><th>Wear Out</th><th>Current KM</th><th>Percentage</th></tr>";
            foreach ($parts as $part) {
                if ($part["wearout"] != null) {
                    $p_zahl = round(($part["current_km"] / $part["wearout"]) * 100);
                    $percentage = $p_zahl . " %";
                    if ($p_zahl < 50) {
                        $percentage = message($percentage, 1, false);
                    } elseif ($p_zahl < 90) {
                        $percentage = message($percentage, 2, false);
                    } else {
                        $percentage = message($percentage, 3, false);
                    }
                } else {
                    $percentage = "<i>not available</i>";
                }
                echo "<tr><td>" . $part["name"] . "</td><td>" . ($part["wearout"] == null ? "-" : $part["wearout"]) . "</td><td>" . $part["current_km"] . "</td><td>$percentage</td></tr>";
            }
            echo "</table>";
            echo "</td></tr>";
        }
    }
    echo "</table>";
    echo "</div>";
    echo "</div>";

    /*
     * Fitnesstatistik
     */

    echo "<div class='panel panel-default'>";
    echo "<div class='panel-heading'><h2>Deine Fitness</h2></div>";
    echo "<div class='panel-body'>";
    echo '<div id="phone">';
    echo "<table class='table table-striped'>";

    $dates = query("SELECT SUM(kilometers) as summe, MONTH(created_on) as monat, year(created_on) as jahr FROM drive_history WHERE client_id = " . $_SESSION["user"]["client_id"] . " AND created_on > DATE_SUB(CURDATE(),INTERVAL 1 YEAR) GROUP BY MONTH(created_on) ORDER BY year(created_on), month(created_on) ASC");
    echo "<tr>";
    foreach ($dates as $date) {
        $dates_c[$date["jahr"] . $date["monat"]] = $date["summe"];
    }

    //$dates_conf
    for ($i = 12; $i >= 0; $i--) {
        echo "<th>" . date("M\nY", mktime(0, 0, 0, date("m") - $i, date("d"), date("Y"))) . "</th>";
        //echo $i . "--";
        //echo date("Y-m", mktime(0, 0, 0, date("m")-$i, date("d"),   date("Y")));
    }
    echo "</tr><tr>";
    $statData = array();
    for ($i = 12; $i >= 0; $i--) {
        $c_str = date("Yn", mktime(0, 0, 0, date("m") - $i, date("d"), date("Y")));
        $statData[$c_str] = isset($dates_c[$c_str]) ? $dates_c[$c_str] : 0;

        echo "<td style='text-align:center;'>" . (isset($dates_c[$c_str]) ? $dates_c[$c_str] : "0") . "</td>";
        //echo $i . "--";
        //echo date("Y-m", mktime(0, 0, 0, date("m")-$i, date("d"),   date("Y")));
    }
    echo "</tr>";

    //print_r($dates_c);
// 	foreach ($dates as $date)
// 	{
// 		echo "<tr><td>" . $bike["name"] . "</td><td>" . $bike["serial_number"] . "</td><td>" . $bike["current_km"] . " km</td></tr>";
// 		//for ($i = 0; $i )
// 	}
    echo "</table>";

    echo '<canvas style="border: 1px solid #bbb; width: 96%; height: 180px;" id="fitnessStat"></canvas>';
    echo '<script type="text/javascript">';
    echo 'var statData = new Array ();' . "\n";
    foreach ($statData as $key => $data) {
        echo 'statData.push("' . $data . '");' . "\n";
        //echo 'statData["' . $key . '"] = "' . $data . '";' . "\n";
    }
    echo '</script>';
    echo "<script src='js/fitness_stat.js'></script>";
    echo '</div>';
    echo "</div>";
    echo "</div>";
    /*
     * Aktuelle und vergangene Tickets
     */
    echo "<div class='panel panel-default'>";
    echo "<div class='panel-heading'><h2>Deine Tickets</h2></div>";
    echo "<div class='panel-body'>";
    echo "<table class='table table-striped'>";
    echo "<tr><th>Subject</th><th>Type</th><th>Created On</th><th></th></tr>";
    $topics = query("SELECT  * from topic WHERE client_id = " . $_SESSION["user"]["client_id"] . " ORDER BY active DESC, created_on desc");
    if (sizeof($topics) == 0) {
        echo "<tr><td colspan='4' style='text-align:center;'><i>Keine offenen oder geschlossenen Tickets bisher.</i></td></tr>";
    } else {
        foreach ($topics as $topic) {
            echo "<tr><td>" . $topic["subject"] . "</td><td>" . ($topic["type"] == 1 ? "Schaden" : "Kontakt") . "</td><td>" . $topic["created_on"] . " km</td><td>" . ($topic["active"] == 1 ? "<img src='graphic/y.png'>" : "") . "</td></tr>";

        }
    }

    echo "</table>";
    echo "</div>";
    echo "</div>";

}

include 'config/footer.php';
?>
