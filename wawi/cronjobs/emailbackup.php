<?php
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");



class app_t {
  var $DB;
  var $user;
}
*/

// ENDE Kommentar
//$erp = new erpAPI(&$app);
$app = new app_t();

$DEBUG = 0;


$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass);
$db = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass);
$erp = new erpAPI($app);


// wenn das skript laeuft hier abbrechen
$mutex = $app->DB->Select("SELECT mutex FROM prozessstarter WHERE parameter='emailbackup' AND mutex > 0 LIMIT 1");

if($mutex==1)
{
  $app->DB->Update("UPDATE prozessstarter SET mutexcounter=mutexcounter+1 WHERE parameter='emailbackup' LIMIT 1");
  exit;
}

$app->DB->Select("UPDATE prozessstarter SET mutex='1' WHERE parameter='emailbackup' LIMIT 1");

// TODO wenn das skript noch laueft hier abbrechen


$test = new IMAP();
$accounts = $app->DB->SelectArr("SELECT * from emailbackup WHERE emailbackup=1");

for($i=0;$i<count($accounts);$i++)
{
if($accounts[$i]['emailbackup']==1)
{
  echo "E-Mail Account Backup: ".$accounts[$i]['benutzername']."\r\n";
  $mailbox = $test->imap_connect($accounts[$i]['server'],"993","INBOX",$accounts[$i]['benutzername'],$accounts[$i]['passwort'],3);
  $test->imap_import($mailbox,0,$accounts[$i]['loeschtage'],$accounts[$i]['id'],$conf->WFuserdata."emailbackup/".$conf->WFdbname);
}
}

//echo "ready!\r\n";
$app->DB->Update("UPDATE prozessstarter SET mutex='0' WHERE parameter='emailbackup' LIMIT 1");
$app->DB->Update("UPDATE prozessstarter SET mutexcounter='0' WHERE parameter='emailbackup' LIMIT 1");


?>
