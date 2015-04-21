<?
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

$app = new app_t();

*/
// ende kommentar



$DEBUG = 0;


$conf = new Config();
$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass);
//$app->Conf = new Config();

// wenn das skript laeuft hier abbrechen
$mutex = $app->DB->Select("SELECT mutex FROM prozessstarter WHERE parameter='supportmails' LIMIT 1");

if($mutex)
{
  $app->DB->Update("UPDATE prozessstarter SET mutexcounter=mutexcounter+1 WHERE parameter='supportmails' LIMIT 1");
  exit;
}
$app->DB->Select("UPDATE prozessstarter SET mutex='1' WHERE parameter='supportmails' LIMIT 1");

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
$imap = new IMAP();

$accounts = $app->DB->SelectArr("SELECT * from emailbackup WHERE ticket='1'");

for($a=0;$a<count($accounts);$a++)
{
  echo "E-Mail Account Backup: ".$accounts[$a]['benutzername']."\n";
  $mailbox = $imap->imap_connect($accounts[$a]['server'],"993","INBOX",$accounts[$a]['benutzername'],$accounts[$a]['passwort'],3);

  if($accounts[$a]['projekt']==0)
    $projekt = 1;
  else $projekt = $accounts[$a]['projekt'];
  
  //echo $test->imap_message_count($mailbox);
  //$test->imap_import($mailbox,0,$accounts[$i]['loeschtage'],$accounts[$i]['id']);
  
  $delete_msg=0;
  $daysold = $accounts[$a]['loeschtage'];
  
  $num_messages = $imap->imap_message_count($mailbox);
  echo "Anzahl E-Mails: ".$num_messages."\n";
  
  // Zähler für erfolgreich eingefügte Mails
  $inserted_mails = 0;

  mb_internal_encoding("UTF-8");

  for($i=1; $num_messages >= $i; $i++)
      {
        $msg                    = imap_header($mailbox,$i);
        $subject                = $imap->encodeToUtf8(($imap->imap_decode($msg->subject)));
        $from                   = $imap->encodeToUtf8($imap->imap_decode($msg->sender[0]->mailbox."@".$msg->sender[0]->host));
        $action                 = $imap->encodeToUtf8(($imap->imap_get_part($mailbox, $i, "TEXT/PLAIN")));
        $action_html            = $imap->encodeToUtf8(($imap->imap_get_part($mailbox, $i, "TEXT/HTML")));
        //$action               = get_part($mbox, $i, "TEXT/HTML");

	$name_sender = mb_decode_mimeheader($msg->sender[0]->personal);
	if($name_sender=="")
	 $name_sender = $from;

	if($accounts[$a]['autoresponder']==1 && $accounts[$a]['autorespondertext']!="" && $from!="info@embedded-projects.net" && $erp->AutoresponderBlacklist($from)!=1)
	{
	  //$text = $app->DB->Select("SELECT text FROM geschaeftsbrief_vorlagen WHERE id='".$accounts[$a]['geschaeftsbriefvorlage']."' LIMIT 1");
	  //$betreff = $app->DB->Select("SELECT betreff FROM geschaeftsbrief_vorlagen WHERE id='".$accounts[$a]['geschaeftsbriefvorlage']."' LIMIT 1");
	  $text = $accounts[$a]['autorespondertext'];
	  $betreff = $accounts[$a]['autoresponderbetreff'];
	  $erp->MailSend($accounts[$a]['email'],"",$from,$name_sender,$betreff,$text);
	}


				//echo $msg->sender[0]->mailbox."@".$msg->sender[0]->host;
				//echo "***** $from ****";
				//echo $msg->fromaddress;
				//print_r($msg);
				//$text = "=?ISO-8859-15?Q?Bastian_D=F6rsam?=";
				//$ret2 = mb_decode_mimeheader($text); 
				//echo $ret2;


        //pruefe ob email in datenbank bereits enthalten ist
        $timestamp =  strtotime($msg->MailDate);

        $frommd5 = md5($from.$subject.$timestamp);
        $empfang = date('Y-m-d H:i:s',$timestamp);
        $sql = "SELECT COUNT(id) FROM emailbackup_mails WHERE 
          checksum='$frommd5' AND empfang='$empfang'";
	
        if($app->DB->Select($sql)==0)
        {
          echo "insert $i md5 hash ".$frommd5."\n";
          //pruefe ob anhaene vorhanden sind
          $attachments = $imap->extract_attachments2($mailbox,$i);
          $anhang=0;
          for($j=0;$j<count($attachments);$j++)
          {
            if($attachments[$j]['is_attachment']==1)
            {
              $anhang = 1; break;
            }
          }

	  $mailacc = $accounts[$a]['email'];
	  $mailaccid = $accounts[$a]['id'];
	 
	  if(!$erp->isMailAdr($from))
	    $from = $erp->filterMailAdr($from);
 
	  //fuege gegenenfalls ein
          $sql = "INSERT INTO emailbackup_mails (webmail,subject,sender,action,action_html,empfang,anhang,checksum) 
            VALUES ('$mailaccid','$subject','$from','$action','$action_html','$empfang','$anhang','$frommd5')";

	  if($DEBUG)
	    echo $sql;
	  else { 
	    $app->DB->InsertWithoutLog($sql);
	    $inserted_mails++;

	    $id = $app->DB->GetInsertID();
	  } 
	  //Generiere Ticket wenn neu sonst fuege nur nachricht an und hole alte ticket id
	  //#201001080420
	  //$subject  = substr("#

	  if($DEBUG)
	    echo "ticket suchen oder anlegen\n";

	  if(eregi("#[0-9]{12}", $subject, $matches))
	  {
	    if($DEBUG)
	      echo "ticket nummer in betreff gefunden\n";


	    $schluessel = str_replace("#", "",$matches[0]);

	    if($action_html!="")
	      $sql = "INSERT INTO `ticket_nachricht` (`id`, `ticket`, `zeit`,`text`,`betreff`,`medium`,`verfasser`, `mail`) 
		VALUES (NULL, '$schluessel', FROM_UNIXTIME($timestamp), '$action_html','$subject','email','$name_sender', '$from');";
	    else
	      $sql = "INSERT INTO `ticket_nachricht` (`id`, `ticket`, `zeit`,`text`,`betreff`,`medium`,`verfasser`, `mail`) 
		VALUES (NULL, '$schluessel',FROM_UNIXTIME($timestamp), '$action','$subject','email','$name_sender', '$from');";

	    if(!$DEBUG)
	    {
	      $app->DB->InsertWithoutLog($sql);
	      $ticketnachricht = $app->DB->GetInsertID();
	    }
	  } else 
	  {
	    if(!$DEBUG)
	    {

	      if($action_html!="")
		$ticketnachricht = $erp->CreateTicket($projekt,$mailacc,$name_sender,$from,$subject,$action_html,$timestamp); // ACHTUNG immer Projekt eprooshop
	      else
		$ticketnachricht = $erp->CreateTicket($projekt,$mailacc,$name_sender,$from,$subject,$action,$timestamp); // ACHTUNG immer Projekt eprooshop
	    } else 
	      {
		echo "Lege neues Ticket an\n";
		//echo "CreateTicket($projekt,$mailacc,$name_sender,$from,$subject,$action,$timestamp);";
	      }
	  }    
          //speichere anhang als datei
          if($anhang==1)
          {
	    // Prüfen ob Ordner vorhanden ansonsten anlegen
	    $ordner = $conf->WFuserdata."emailbackup/".$conf->WFdbname."/$id";
	    if(!is_dir($ordner))
	      if($DEBUG)
		echo "mkdir $ordner\n";
	      else
		mkdir($ordner);
            
	    //Mail-ID holen
	    $mailid = $app->DB->Select("SELECT MAX(id) FROM emailbackup_mails");
	  
	    for($j=0;$j<count($attachments);$j++)
       {
              if($attachments[$j]['is_attachment']==1 && $attachments[$j]['filename']!="")
              {
						if($DEBUG)
		  				echo "fopen, fwrite, fclose {$attachments[$j]['filename']}";
		else {
		  $handle = fopen ($ordner."/".$attachments[$j]['filename'], "wb");
		  fwrite($handle, $attachments[$j]['attachment']);
		  fclose($handle);
		}
		//Schreibe Anhänge in Datei-Tabelle

		//  function CreateDatei($name,$titel,$beschreibung,$nummer,$datei,$ersteller,$without_log=false)
		$datei = $ordner."/".$attachments[$j]['filename'];//$attachments[$j]['attachment'];

		if($DEBUG)
		  echo "CreateDatei({$attachments[$j]['filename']},{$attachments[$j]['filename']},\"\",\"\",\"datei\",\"Support Mail\",true,".$conf->WFuserdata."dms/".$conf->WFdname.")\n"; 
		else
		  $tmpid = $erp->CreateDatei($attachments[$j]['filename'],$attachments[$j]['filename'],"","",$datei,"Support Mail",true,$conf->WFuserdata."dms/".$conf->WFdbname);
		  
		  
		//Hole Datei-ID
		//$datei_id = $app->DB->Select("SELECT id FROM datei WHERE beschreibung=$mailid");

	      if($DEBUG)
					echo "AddDateiStichwort $tmpid,'Anhang','Ticket',$ticketnachricht,true)\n";
	      else
					$erp->AddDateiStichwort($tmpid,'Anhang','Ticket',$ticketnachricht,true);

		//Erzeuge Semantik in Datei_Stichwoerter	
		//$app->DB->InsertWithoutLog("INSERT INTO datei_stichwoerter (datei, subjekt, objekt, parameter)
		//		  VALUES ($datei_id, 'Anhang', 'Ticket', '$ticketid')");

		//Erzeuge erste Datei-Version
		//$app->DB->InsertWithoutLog("INSERT INTO datei_version (datei, ersteller, datum, version, dateiname, bemerkung)
	//			  VALUES ($datei_id, '$mailaccid', DATE(NOW()), 1, '".$attachments[$j]['filename']."', 'Initiale Version')");
              }
            }
          }
	
        }

	if($DEBUG)
	  echo "delete mail\n";
	else
	  imap_delete($mailbox,$i);

        //wenn oldday !=0 pruefe ob email geloescht werden soll
				print "fetched and inserted $inserted_mails emails into database\r\n";

    }
    imap_expunge($mailbox) ;
    $imap->imap_disconnect($mailbox);
    $app->DB->Select("UPDATE prozessstarter SET mutex='0',mutexcounter='0' WHERE parameter='supportmails' LIMIT 1");
}

?>
