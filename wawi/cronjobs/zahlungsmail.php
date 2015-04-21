<?
/*
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.db.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");




class app_t {
  var $DB;
  var $user;
}
// ende debug
*/
$app = new app_t();

$DEBUG = 0;


$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass);
$erp = new erpAPI($app);

$firmendatenid = $app->DB->Select("SELECT MAX(id) FROM firmendaten LIMIT 1");

$benutzername = $app->DB->Select("SELECT benutzername FROM firmendaten WHERE id='".$firmendatenid."' LIMIT 1");
$passwort = $app->DB->Select("SELECT passwort FROM firmendaten WHERE id='".$firmendatenid."' LIMIT 1");
$host = $app->DB->Select("SELECT host FROM firmendaten WHERE id='".$firmendatenid."' LIMIT 1");
$port = $app->DB->Select("SELECT port FROM firmendaten WHERE id='".$firmendatenid."' LIMIT 1");
$mailssl = $app->DB->Select("SELECT mailssl FROM firmendaten WHERE id='".$firmendatenid."' LIMIT 1");


$app->mail = new PHPMailer();
//$app->mail->PluginDir="plugins/phpmailer/";
$app->mail->IsSMTP();
$app->mail->SMTPAuth   = true;                  // enable SMTP authentication
if($mailssl)
$app->mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
$app->mail->Host       = $host;
$app->mail->Port       = $port;                   // set the SMTP port for the GMAIL server

$app->mail->Username   = $benutzername;
$app->mail->Password   = $passwort;


$meineauftraege = $app->DB->SelectArr("SELECT id FROM auftrag WHERE status='freigegeben' AND vorkasse_ok!='1' AND zahlungsweise='vorkasse' AND zahlungsweise!='nachnahme' AND (zahlungsmailcounter<3 OR zahlungsmailcounter IS NULL)");
for($i=0;$i<count($meineauftraege);$i++)
{
  //$this->AuftragEinzelnBerechnen($meineauftraege[$i][id]);
  $erp->AuftragZahlungsmail($meineauftraege[$i][id]);
}


?>
