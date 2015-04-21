<?
//error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

//error_reporting(0);
include(dirname(__FILE__)."/../conf/main.conf.php");
include(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include(dirname(__FILE__)."/../www/lib/imap.inc.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi.php");
include(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php");
include(dirname(__FILE__)."/../www/lib/class.remote.php");
include(dirname(__FILE__)."/../www/lib/class.httpclient.php");
include(dirname(__FILE__)."/../www/lib/class.aes.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");

class app_t {
  var $DB;
  var $user;
  var $mail;
  var $erp;
}

$app = new app_t();

$DEBUG = 1;

$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass);


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


$erp = new erpAPI($app);

$task = $app->DB->SelectArr("SELECT * from prozessstarter WHERE aktiv='1' ORDER by id DESC");

print_r($task);

for($task_index=0;$task_index<count($task);$task_index++)
{
  $run = 0;
  
  //if($DEBUG)
  echo "Task: {$task[$task_index]['bezeichnung']}\n";
 
  if($task[$task_index]['art']=="periodisch")
  {
		echo "Periodisch\r\n";
    if($task[$task_index]['letzteausfuerhung']=="0000-00-00 00:00:00")
    {
      $run = 1;
    }
    else
    {
      $run = $app->DB->Select("SELECT IF(DATE_SUB(NOW(),INTERVAL {$task[$task_index]['periode']} MINUTE)>'{$task[$task_index]['letzteausfuerhung']}','1','0')");
    }
  }

  if($task[$task_index]['art']=="uhrzeit")
  {
//		echo "Uhrzeit\r\n";

		$time = strtotime($task[$task_index]['startzeit']);
		$time_letzte = strtotime($task[$task_index]['letzteausfuerhung']);

		echo "CHECK h db: ".date('h', $time)."\r\n";
		echo "CHECK i db: ".date('i', $time)."\r\n";

		echo "CHECK h live: ".date('h')."\r\n";
		echo "CHECK i live: ".date('i')."\r\n";

		echo "CHECK h db: ".date('h', $time_letzte)."\r\n";
		echo "CHECK i db: ".date('i', $time_letzte)."\r\n";

		//pro minute maximal	
		if(date('h', $time) == date('h') && date('i', $time) == date('i'))// && (date('i',$time_letzte) != date('i')))
		{
      $run = 1;
		}
		else
      $run = 0;

  }

  if($run)
  {
   echo "- start\n";
    //update letzte ausfuerhung
    $app->DB->Update("UPDATE prozessstarter SET letzteausfuerhung=NOW() WHERE id='{$task[$task_index]['id']}' LIMIT 1");
    //start
    // wenn das skript laeuft hier abbrechen
    $mutexcounter = $app->DB->Select("SELECT mutexcounter FROM prozessstarter WHERE parameter='".$task[$task_index]['parameter']."' LIMIT 1");
    
    if($mutexcounter>5)
    {
      $app->DB->Update("UPDATE prozessstarter SET mutexcounter=0,mutex=0 WHERE parameter='".$task[$task_index]['parameter']."' LIMIT 1");
    }
        
    if($task[$task_index]['typ']=="cronjob")
    {
      	echo "- gestartet\n";
echo dirname(__FILE__)."/".$task[$task_index]['parameter'].".php";
    	if($DEBUG)
      include(dirname(__FILE__)."/".$task[$task_index]['parameter'].".php");
    }

    if($task[$task_index]['typ']=="url")
    {

    }

  } else {
    //if($DEBUG)
      echo "- nicht gestartet\n";

  }


}



?>
