<?php
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/lib/class.remote.php");
include(dirname(__FILE__)."/../www/lib/class.httpclient.php");
include(dirname(__FILE__)."/../www/lib/class.aes.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");



class app_t {
  var $DB;
  var $erp;
  var $user;
  var $remote;
}
*/
//ENDE

echo "start 1 umsatzstatistik\r\n";
$app = new app_t();


$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass);
$erp = new erpAPI($app);
$app->erp = $erp;

//$firmendatenid = $app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");
$all = array('rechnung','gutschrift');
foreach($all as $key)
{
	$result = $app->DB->SelectArr("SELECT id FROM $key WHERE provision_summe=0");
	for($i=0;$i<count($result);$i++)
	{
		echo "$key Wert ".$result[$i]['id']."\r\n";
		$app->erp->BerechneProvision($result[$i]['id'],$key);
	}
}
echo "done\r\n";

?>
