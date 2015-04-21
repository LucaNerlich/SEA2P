<?

include("../conf/main.conf.php");
include("../phpwf/plugins/class.db.php");

$conf = new Config();

$db = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass);


$arr = $db->SelectArr("SELECT * FROM artikel WHERE nummer LIKE '5%'");


$start = "500000";
foreach($arr as $key=>$value)
{
  $db->UpdateWithoutLog("UPDATE artikel SET nummer='$start' WHERE id='{$value[id]}' LIMIT 1");
  $start++;
}

echo "ready!\r\n";

?>
