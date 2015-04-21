<?php

/*
* IMAP include file, contains all email importing functions
* STILL EXPERIMENTAL - USE WITH CARE! you've been warned :)
*/

class IMAP {


  function IMAP()
  {
    // supported protocols
    $this->IMAP_IMAP	  = 1;
    $this->IMAP_POP3	  = 2;
    $this->IMAP_IMAP_SSL  = 3;
    $this->IMAP_POP3_SSL  = 4;
  }

  /* decode mime format strings */
  function imap_decode($text)
  {
    $elements=imap_mime_header_decode($text);
    for($i=0;$i<count($elements);$i++) 
      return htmlspecialchars($elements[$i]->text);
  }

  /* get mime type */
  function imap_get_mime_type(&$structure)
  {
    $primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
    if($structure->subtype)
      return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype;

    return "TEXT/PLAIN";
  }

  /* get part of body by mime type */
  function imap_get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false)
  {
    if(!$structure)
      $structure = @imap_fetchstructure($stream, $msg_number);
	
    if($structure)
    {
      if($mime_type == $this->imap_get_mime_type($structure))
      { 
	if(!$part_number)
	  $part_number = "1";
			
	$text = imap_fetchbody($stream, $msg_number, $part_number);
		
	if($structure->encoding == 3)
	  return imap_base64($text);
	else if($structure->encoding == 4)
	  return imap_qprint($text);
	else
	  return $text;
      }
	
      if($structure->type == 1) /* multipart */
      {
	while(list($index, $sub_structure) = each($structure->parts))
	{
	  if($part_number)
	    $prefix = $part_number . '.';

	  $data = $this->imap_get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));
	  if($data)
	    return $data;
	}
      }
    }
    return false;
  }

  /* connect to server and fetch an $mailbox object */
  function imap_connect($server,$port,$folder,$username,$password,$type)
  {
    //determine protocol type and fix the server connect string
    switch($type)
    {
      case $this->IMAP_IMAP: 	 $server_path = '{'.$server.':'.$port.'}'.$folder; 		break;
      case $this->IMAP_POP3: 	 $server_path = '{'.$server.':'.$port.'/pop3}'.$folder; 	break;
      case $this->IMAP_IMAP_SSL: $server_path = '{'.$server.':'.$port.'/imap/ssl/novalidate-cert}'.$folder; 	break;
      case $this->IMAP_POP3_SSL: $server_path = '{'.$server.':'.$port.'/pop3/ssl}'.$folder; 	break;
      default: 	$server_path = '{'.$server.':'.$port.'}'.$folder; 			break;
    }
    return imap_open($server_path, $username, $password);
  }

  /* return number of messages in current mailbox */
  function imap_message_count($mailbox)
  {
    if ($header = imap_check($mailbox)) 
      return $header->Nmsgs;
    else
      return 0;
  }


function flattenParts($messageParts, $flattenedParts = array(), $prefix = '', $index = 1, $fullPrefix = true) {

  foreach($messageParts as $part) {
    $flattenedParts[$prefix.$index] = $part;
    if(isset($part->parts)) {
      if($part->type == 2) {
        $flattenedParts = $this->flattenParts($part->parts, $flattenedParts, $prefix.$index.'.', 0, false);
      }
      elseif($fullPrefix) {
        $flattenedParts = $this->flattenParts($part->parts, $flattenedParts, $prefix.$index.'.');
      }
      else {
        $flattenedParts = $this->flattenParts($part->parts, $flattenedParts, $prefix);
      }
      unset($flattenedParts[$prefix.$index]->parts);
    }
    $index++;
  }

  return $flattenedParts;
      
}

  /* close server connection gracefully */
  function imap_disconnect($mailbox)
  {
    return imap_close($mailbox);
  }

  function encodeToUtf8($string) {
     return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
  }

  /* import IMAP messages from mailbox */
  function imap_import($mailbox,$delete_msg=0,$daysold=0,$emailbackup_id=0,$path="")
  {
    global $db,$erp;
    $num_messages = $this->imap_message_count($mailbox);



	
      for($i=1; $num_messages >= $i; $i++)
      {
echo "Start Message $i\r\n";
	$result_flags = imap_fetch_overview($mailbox,$i,0);
echo "Result Message $i\r\n";

	if($result_flags[0]->seen == "1") $flag_cache = "read"; else $flag_cache = "unread";


	$msg			= imap_header($mailbox,$i);
	$subject 		= $this->encodeToUtf8(mysql_escape_string($this->imap_decode($msg->subject)));
	$from 			= $this->encodeToUtf8($this->imap_decode($msg->fromaddress));
	$action			= $this->encodeToUtf8(mysql_escape_string($this->imap_get_part($mailbox, $i, "TEXT/PLAIN")));
	$action_html		= $this->encodeToUtf8(mysql_escape_string($this->imap_get_part($mailbox, $i, "TEXT/HTML")));
	//$action		= get_part($mbox, $i, "TEXT/HTML");



	//pruefe ob email in datenbank bereits enthalten ist
	$timestamp =  strtotime($msg->MailDate);
	$frommd5		= md5($from.$subject.$timestamp);
	$empfang = date('Y-m-d H:i:s',$timestamp);
	$sql = "SELECT COUNT(id) FROM emailbackup_mails WHERE 
	  checksum='$frommd5'";// AND empfang='$empfang'";

	if($db->Select($sql)==0)
	{
	  echo "insert $i md5 hash ".$frommd5."\r\n";
	  //pruefe ob anhaene vorhanden sind
	  $attachments = $this->extract_attachments2($mailbox,$i);
	  $anhang=0;
	  for($j=0;$j<count($attachments);$j++)
	  {
	    if($attachments[$j]['is_attachment']==1)
	    {
	      $anhang = 1; break;
	    }
	  }

	  $serial = base64_encode(serialize($msg));
/*
          $structure = imap_fetchstructure($mailbox, $i);
          $flattenedParts = $this->flattenParts($structure->parts);
          $flattenedParts = base64_encode(serialize($flattenedParts));
          $attachments_db = base64_encode(serialize($attachments));
*/
/*

          $tmpfname = tempnam();
          $handle = fopen($tmpfname, "w");

          fclose($handle);

          unlink($tmpfname);

 */   
        

	  //fuege gegenenfalls ein
	  $sql = "INSERT INTO emailbackup_mails (id,webmail,subject,sender,action,action_html,empfang,anhang,checksum,phpobj,flattenedparts,attachment) 
	    VALUES ('',$emailbackup_id,'$subject','$from','$action','$action_html','$empfang','$anhang','$frommd5','$serial','$flattenedParts','$attachments_db')";
	  $db->InsertWithoutLog($sql);

	  //speichere anhang als datei 
	  $id = $db->GetInsertID();
 echo "GetInsertId $id\r\n";

	//$path = $path."emailbackup/";
	
    	if(!is_dir($path))                                                                      
        {                                                                                                                   
              $path_b = $path;                                  
              if (substr(trim($path), -1) == DIRECTORY_SEPARATOR) {
                $path = substr(trim($path), 0, -1);
              }   
              system("chmod 777 ".$path);                                                                                       
              $path = $path_b;
              system("mkdir ".$path);                                                                
              system("chmod 777 ".$path);                                                            
        }
	  if($anhang==1)
	  {
            // Prüfen ob Ordner vorhanden ansonsten anlegen
            //$ordner = "/var/data/userdata/emailbackup/$id";
            $ordner = $path."/".$id;
            if(!is_dir($ordner))
              if($DEBUG)
                echo "mkdir $ordner\n";
              else
                mkdir($ordner);

	    for($j=0;$j<count($attachments);$j++)
	    {


if($attachments[$j]['filename']=="" && $attachments[$j]['name']!="") //UPDATE ERROR MAILS
$attachments[$j]['filename'] = $attachments[$j]['name'];


	      if($attachments[$j]['is_attachment']==1 && $attachments[$j]['filename']!="")
	      {
		$handle = fopen ($ordner."/".$attachments[$j]['filename'], "wb");
		fwrite($handle, $attachments[$j]['attachment']);
		fclose($handle);
	      }

	        //  function CreateDatei($name,$titel,$beschreibung,$nummer,$datei,$ersteller,$without_log=false)
                $datei = $attachments[$j]['attachment'];
                if($DEBUG)
                  echo "CreateDatei({$attachments[$j]['filename']},{$attachments[$j]['filename']},\"\",\"\",$datei,\"Backup Mail\",true,$ordner)\n";
                else
                {
               	$dms = str_replace('emailbackup','dms',$path);
                  $tmpid = $erp->CreateDatei($attachments[$j]['filename'],$attachments[$j]['filename'],"","",$datei,"Backup Mail",true,$dms);
                 }

                //Hole Datei-ID
                //$datei_id = $app->DB->Select("SELECT id FROM datei WHERE beschreibung=$mailid");

              if($DEBUG)
                echo "AddDateiStichwort $tmpid,'Anhang','E-Mail',$id,true)\n";
              else
                $erp->AddDateiStichwort($tmpid,'Anhang','E-Mail',$id,true);

	    }
	  }
	}	
	//wenn oldday !=0 pruefe ob email geloescht werden soll
	if(((time() - $timestamp) > $daysold*60*60*24) && $daysold!=0)
	{
	    //echo "delete $i $from $empfang\r\n";
	    imap_delete($mailbox,$i);
	} else {
	    //echo "not delete $i $from $empfang\r\n";

	    if($flag_cache =="unread")
	    {
	   		//   $status = imap_setflag_full($mailbox, $i, "\\Seen");  
	    	imap_clearflag_full($mailbox, $i, "\\Seen");
	    } else {

			//gelesenen loeschen
	    //imap_delete($mailbox,$i);

			}
			
	    //else
	      //$status = imap_setflag_full($mailbox, $i, "\\Recent ");  
	      //$status = imap_setflag_full($mailbox, $i, "\\Recent \\Flagged");  


	}


	/*
	echo "<br>";
	echo date('r');
	echo "<br>";
	$timestamp =  strtotime('Sun, 20 Jul 2008 23:34:07 +0200'); 
	$datum = date("d.m.Y - H:i", $timestamp);
	echo $datum;
	*/

	/*Mon, 29 Jun 2009 23:48:57 -0700
	2009-08-14 20:20:24
*/
	//insert ticket
	//**print "from '$from', subject: '".substr($subject,0,50)."', body contains <B>".strlen($action)."</B> characters<br>";


/*Array
(
[0] => Array
(
[is_attachment] => 
[filename] => 
[name] => 
[attachment] => 
)

[1] => Array
(
[is_attachment] => 1
[filename] => 20090622BDR_EAC-BOX_RP001_V100_C.pdf
[name] => 20090622BDR_EAC-BOX_RP001_V100_C.pdf
[attachment] => 'inhalt der datei'
*/

/*
add_ticket($subject,$from,'','NOW()','NOW()',$GLOBALS[STATUS_OPEN],$GLOBALS[SEVERITY_NORMAL],$_SESSION[user_id]);
//$query = "INSERT INTO $GLOBALS[mysql_prefix]ticket (affected,scope,owner,description,problemstart,problemend,status,date,severity) VALUES('$from','',$_SESSION[user_id],'$subject','2002-03-05 18:30:00','2002-03-05 18:30:00',$GLOBALS[STATUS_OPEN],NOW(),$GLOBALS[SEVERITY_NORMAL])";
//mysql_query($query) or do_error("imap_import($delete_msg)::mysql_query()", 'mysql query failed', mysql_error());

//insert action (i.e. the body of the message)
//$action 	= strip_html($action); //fix formatting, custom tags etc.
$ticket_id 	= mysql_insert_id();
		
if ($action) //is $action empty?
{
$query 		= "INSERT INTO $GLOBALS[mysql_prefix]action (description,ticket_id,date,user,action_type) VALUES('$action','$ticket_id',NOW(),$_SESSION[user_id],$GLOBALS[ACTION_COMMENT])";
mysql_query($query) or do_error("imap_import($delete_msg)::mysql_query()", 'mysql query failed', mysql_error());
}
		
if ($action_html)
{
$query 		= "INSERT INTO $GLOBALS[mysql_prefix]action (description,ticket_id,date,user,action_type) VALUES('$action_html','$ticket_id',NOW(),$_SESSION[user_id],$GLOBALS[ACTION_COMMENT])";
mysql_query($query) or do_error("imap_import($delete_msg)::mysql_query()", 'mysql query failed', mysql_error());
}	
		
if ($delete_msg) imap_delete($mailbox,$i);
}
*/	
	
    //get rid of deleted messages if deletetion is on
    // if ($delete_msg) imap_expunge($mailbox);
    }
    //imap_expunge($mailbox);
    imap_close($mailbox,CL_EXPUNGE);
    print "fetched and inserted $num_messages emails into database\r\n";
  }
  
  function extract_attachments($connection, $message_number) {
   
    $attachments = array();
    $structure = imap_fetchstructure($connection, $message_number);
   
    if(isset($structure->parts) && count($structure->parts)) {
   
        for($i = 0; $i < count($structure->parts); $i++) {
   
            $attachments[$i] = array(
                'is_attachment' => false,
                'filename' => '',
                'name' => '',
                'attachment' => ''
            );
           
            if($structure->parts[$i]->ifdparameters) {
                foreach($structure->parts[$i]->dparameters as $object) {
                    if(strtolower($object->attribute) == 'filename') {
                        $attachments[$i]['is_attachment'] = true;
                        $attachments[$i]['filename'] = $object->value;
                    }
                }
            }
           
            if($structure->parts[$i]->ifparameters) {
                foreach($structure->parts[$i]->parameters as $object) {
                    if(strtolower($object->attribute) == 'name') {
                        $attachments[$i]['is_attachment'] = true;
                        $attachments[$i]['name'] = $object->value;
                    }
                }
            }
           
            if($attachments[$i]['is_attachment']) {
                $attachments[$i]['attachment'] = imap_fetchbody($connection, $message_number, $i+1);
                if($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                    $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                }
                elseif($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                    $attachments[$i]['attachment'] = 
quoted_printable_decode($attachments[$i]['attachment']);
                }
            }
           
        }
       
    }
   
    return $attachments;
  }

  function extract_attachments2($mbox, $mid) {

    $this->htmlmsg="";
    $this->plainmsg="";
    $this->charset="";
    unset($this->attachments);

    // BODY
    $s = imap_fetchstructure($mbox,$mid);
    if (!$s->parts)  // simple
        $this->getpart($mbox,$mid,$s,0);  // pass 0 as part-number
    else {  // multipart: cycle through each part
        foreach ($s->parts as $partno0=>$p)
            $this->getpart($mbox,$mid,$p,$partno0+1);
    }

    unset($attachments);
    for($i=0;$i<count($this->attachments);$i++)
    {
      $attachments[$i]['attachment'] = $this->attachments[$i][1];
      $attachments[$i]['is_attachment'] = true;
      $attachments[$i]['filename'] = $this->attachments[$i][0];
      $attachments[$i]['name'] = $this->attachments[$i][0];
    }

    return $attachments;
  }



  function getpart($mbox,$mid,$p,$partno) {
    // $partno = '1', '2', '2.1', '2.1.3', etc for multipart, 0 if simple
    // DECODE DATA
    $data = ($partno)?
        imap_fetchbody($mbox,$mid,$partno):  // multipart
        imap_body($mbox,$mid);  // simple
    // Any part may be encoded, even plain text messages, so check everything.
    if ($p->encoding==4)
        $data = quoted_printable_decode($data);
    elseif ($p->encoding==3)
        $data = base64_decode($data);
    // PARAMETERS
    // get all parameters, like charset, filenames of attachments, etc.
    $params = array();
    if ($p->parameters)
        foreach ($p->parameters as $x)
            $params[strtolower($x->attribute)] = $x->value;
    if ($p->dparameters)
        foreach ($p->dparameters as $x)
            $params[strtolower($x->attribute)] = $x->value;

    // ATTACHMENT
    // Any part with a filename is an attachment,
    // so an attached text file (type 0) is not mistaken as the message.
    if ($params['filename'] || $params['name']) {
        // filename may be given as 'Filename' or 'Name' or both
        $filename = ($params['filename'])? $params['filename'] : $params['name'];
        // filename may be encoded, so see imap_mime_header_decode()
        $this->attachments[] = array($filename,$data);  // this is a problem if two files have same name
    }

/*
    // TEXT
    if ($p->type==0 && $data) {
        // Messages may be split in different parts because of inline attachments,
        // so append parts together with blank row.
        if (strtolower($p->subtype)=='plain')
            $this->plainmsg. = trim($data) ."\n\n";
        else
            $this->htmlmsg. = $data ."<br><br>";
        $this->charset = $params['charset'];  // assume all parts are same charset
    }

    // EMBEDDED MESSAGE
    // Many bounce notifications embed the original message as type 2,
    // but AOL uses type 1 (multipart), which is not handled here.
    // There are no PHP functions to parse embedded messages,
    // so this just appends the raw source to the main message.
    elseif ($p->type==2 && $data) {
        $this->plainmsg. = $data."\n\n";
    }
*/
    // SUBPART RECURSION
    if ($p->parts) {
        foreach ($p->parts as $partno0=>$p2)
            $this->getpart($mbox,$mid,$p2,$partno.'.'.($partno0+1));  // 1.2, 1.2.1, etc.
    }
  }


}
?>
