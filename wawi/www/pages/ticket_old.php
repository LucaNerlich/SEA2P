<?php
include ("_gen/ticket.php");

class Ticket extends GenTicket {
  var $app;
  
  function Ticket($app) {
    //parent::GenTicket($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","TicketCreate");
    $this->app->ActionHandler("edit","TicketEdit");
    $this->app->ActionHandler("assistent","TicketAssistent");
    $this->app->ActionHandler("list","TicketList");
    $this->app->ActionHandler("freigabe","TicketFreigabe");
    $this->app->ActionHandler("beantwortet","TicketBeantwortet");
    $this->app->ActionHandler("antwort","TicketAntwort");
    $this->app->ActionHandler("delete","TicketDelete");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }


  function TicketBeantwortet()
  {
    $id = $this->app->Secure->GetGET("id");
    $ticket_id = $this->app->DB->Select("SELECT t.id FROM ticket t, ticket_nachricht tn WHERE tn.id='$id' AND tn.ticket=t.schluessel LIMIT 1");
    $this->app->DB->Update("UPDATE ticket_nachricht SET status='beantwortet' WHERE id='$id' LIMIT 1");
    $this->app->DB->Update("UPDATE ticket SET inbearbeitung=0 WHERE id='$ticket_id' LIMIT 1");
    //$this->TicketList();
    header("Location: index.php?module=ticket&action=list&cmd=zugewiesene");
    exit;
  }

  function TicketFreigabe()
  {
    $id = $this->app->Secure->GetGET("id");
    $ticket_id = $this->app->DB->Select("SELECT t.id FROM ticket t, ticket_nachricht tn WHERE tn.id='$id' AND tn.ticket=t.schluessel LIMIT 1");
    $this->app->DB->Update("UPDATE ticket SET inbearbeitung=0 WHERE id='$ticket_id' LIMIT 1");
    $this->TicketList();
  }

  function TicketCreate()
  {
    $this->app->Tpl->Add(TABS,
      "<a class=\"tab\" href=\"index.php?module=ticket&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a>");
 
    $warteschlange= $this->app->Secure->GetPOST("warteschlange");
    $betreff = $this->app->Secure->GetPOST("betreff");
    $quelle = $this->app->Secure->GetPOST("quelle");
    $kunde = $this->app->Secure->GetPOST("kunde");
    $mailadresse = $this->app->Secure->GetPOST("kontakt");
    $text = $this->app->Secure->GetPOST("text");
    $medium = "intern";
    $antwort = $this->app->Secure->GetPOST("antwort");
    $prio = $this->app->Secure->GetPOST("prio");

    if($prio=="")$prio=3;
    $this->app->Tpl->Set(PRIO,$this->app->erp->GetPrioTicketSelect($prio));
   
    if($warteschlange=="") $warteschlange="verwaltung";
    $this->app->Tpl->Set(WARTESCHLANGE,$this->app->erp->GetWarteschlangeTicketSelect($warteschlange));
    
    if($quelle=="") $quelle="mail";
    $this->app->Tpl->Set(QUELLE,$this->app->erp->GetQuelleTicketSelect($quelle));
  
    if($antwort=="") $antwort="sofort";
    $this->app->Tpl->Set(($antwort=="sofort")?AWRADSOFORT:AWRADZUORDNEN," checked=\"checked\"");

    if($betreff!="")
    {
      $error = array();
      if($kunde=="") array_push($error, "Kein Kunde angegeben.");
      if($mailadresse=="") array_push($error, "Keine Kontaktadresse angegeben.");
      if($text=="") array_push($error, "Kein Text angegben.");
      if(count($error)>0)
      {
	$this->app->Tpl->Set(MELDUNG,"<div class=\"warning\">".implode("<br />",$error)."</div>");
	$this->app->Tpl->Set(BETREFF,$betreff);
	$this->app->Tpl->Set(NAME,$kunde);
	$this->app->Tpl->Set(EMAIL,$mailadresse);
	$this->app->Tpl->Set(TEXT,$text);
	//$this->app->Tpl->Add(TEXT,"<center><textarea name=\"ticket_nachricht\" rows=\"20\" cols=\"102\" >$text</textarea></center>");

      }
      else
      {
	$id = $this->app->erp->createTicket($projekt,$quelle,$kunde,$mailadresse,$betreff,$text,$medium);      
	header("Location: index.php?module=ticket&action=edit&id=$id"); 
        return;
      }
    }
    $this->app->Tpl->Parse(PAGE,"ticket_neu.tpl");
    //parent::TicketCreate();
  }

  function TicketAntwort()
  {
    $message = $this->app->Secure->GetGET("message");

    $this->app->Tpl->Set(ID, $message);
    
    if(!is_numeric($message))
      return;

    /* hole Ticket-ID */
    $ticketid = $this->app->DB->Select("SELECT ticket FROM ticket_nachricht WHERE id='$message'");   

    // Projekt-ID passend zum ticket holen
    $projekt = $this->app->DB->Select("SELECT t1.projekt FROM ticket AS t1, ticket_nachricht AS t2 WHERE t1.schluessel = t2.ticket AND t2.id = '$message'");
    $adresse = $this->app->DB->Select("SELECT t1.adresse FROM ticket AS t1, ticket_nachricht AS t2 WHERE t1.schluessel = t2.ticket AND t2.id = '$message'");

    $this->app->Tpl->Set(ADRESSE, $adresse);

    $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' LIMIT 1");
//echo "Huhu".$name;

    // passende Vorlagen holen
    $result = $this->app->DB->SelectArr("SELECT id, projekt, vorlagenname, vorlage FROM ticket_vorlage WHERE projekt='$projekt' AND sichtbar=0");

    $vorlagen = "<ol>";
    foreach($result as $vorlage){

      $vorlage['vorlage'] = str_replace("[NAME]",$name,$vorlage['vorlage']);
      $vorlagen =  $vorlagen."<li><div onClick=\"einfuegenticket('".base64_encode($vorlage['vorlage'])."')\">".$vorlage['vorlagenname']."</div></li>";
    }
    $vorlagen = $vorlagen."</ol>";

    

    $senden = $this->app->Secure->GetPOST("senden");
    if($senden != "")
    {	
    	$eingabetext = $this->app->Secure->GetPOST("eingabetext");
	
	$bearbname = $this->app->User->GetName();
	$this->app->DB->Update("UPDATE ticket_nachricht SET 
	    textausgang='$eingabetext', zeitausgang=NOW(), bearbeiter='$bearbname' WHERE ticket='$ticketid' AND id='$message'");

	//senden
	if($this->app->erp->TicketMail($message)){
	  $this->app->DB->Update("UPDATE ticket SET inbearbeitung='0' WHERE schluessel='$ticketid'");
	  //redirect
	  //$this->app->Tpl->Set(PAGE,"<script>window.opener.location.href='index.php?module=ticket&action=list&tab=2'; self.close();</script>");
	  $this->app->Tpl->Set(PAGE,"<script>self.close();</script>");
	}
    }

    $text = $this->app->DB->Select("SELECT text FROM ticket_nachricht WHERE id=$message LIMIT 1");
    $betreff = $this->app->DB->Select("SELECT betreff FROM ticket_nachricht  WHERE id=$message LIMIT 1");
    $verfasser = $this->app->DB->Select("SELECT verfasser FROM ticket_nachricht  WHERE id=$message LIMIT 1");
    $mail = $this->app->DB->Select("SELECT mail FROM ticket_nachricht  WHERE id=$message LIMIT 1");
    $zeit = $this->app->DB->Select("SELECT zeit FROM ticket_nachricht  WHERE id=$message LIMIT 1");
    $status= $this->app->DB->Select("SELECT status FROM ticket_nachricht  WHERE id=$message LIMIT 1");
    $versendet= $this->app->DB->Select("SELECT versendet FROM ticket_nachricht  WHERE id=$message LIMIT 1");
    $schluessel = $this->app->DB->Select("SELECT ticket FROM ticket_nachricht  WHERE id=$message LIMIT 1");
      
    $this->app->Tpl->Add(NAME,$verfasser);
    $this->app->Tpl->Set(VORLAGEN, $vorlagen);
    // Vorlagen einfügen  
    if($this->app->Secure->GetPOST("vorlage")!=""){
      foreach($result as $vorlage){
	if($vorlage['vorlagenname'] == $this->app->Secure->GetPOST("vorlage"))
	 $text = $vorlage["vorlage"].$text; 
      }     
    }

    //$this->app->Tpl->Add(TEXT,$text);
    $this->app->Tpl->Set(TEXTVORLAGE,"\r\n\r\n\r\nFür Rückfragen stehe ich Ihnen gerne zur Verfügung.\r\n\r\nMit freundlichen Gr&uuml;ßen\r\nIhr embedded projects Team\r\n\r\n");
    //$this->app->Tpl->Add(TEXT,"<center><textarea name=\"ticket_nachricht\" rows=\"20\" cols=\"102\" >$text</textarea></center>");

/*
    $text = $text."
Mit freundlichen Grüßen
Ihr embedded projects Team";
*/
    $text = nl2br($text);
    $this->app->Tpl->Add(TEXT,"<center><textarea name=\"ticket_nachricht\" rows=\"20\" cols=\"102\" >$text</textarea></center>");


    $this->app->Tpl->Add(BETREFF,$betreff." Ticket #".$schluessel);
    $this->app->Tpl->Add(VERFASSER,$verfasser." ($mail)");


    // anhaenge anzeigen
    $anhaenge = $this->app->DB->SelectArr("SELECT datei FROM datei_stichwoerter WHERE subjekt='Anhang' AND objekt='Ticket' AND parameter='$message'");
    if(count($anhaenge))
    {
      foreach($anhaenge as $key=>$value)
      {
	$this->app->Tpl->Add(ANHAENGE,"<a href=\"index.php?module=dateien&action=send&id={$value['datei']}\" target=\"_blank\">".$this->app->erp->GetDateiName($value['datei'])."</a>&nbsp;");
      }
    } else $this->app->Tpl->Add(ANHAENGE,"keine Anh&auml;nge");
    
    $this->app->Tpl->Parse(PAGE,"ticket_popup.tpl");
    $this->app->BuildNavigation=false;
  }



  function TicketAssistent()
  {
    $id = $this->app->Secure->GetGET("id");
    $prio = $this->app->Secure->GetPOST("prio");
    $warteschlange = $this->app->Secure->GetPOST("warteschlange");
    $projekt = $this->app->Secure->GetPOST("projekt");
    $adresse = $this->app->Secure->GetPOST("adresse");
    $tmp = split(" ",$adresse); 
    $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='{$tmp[0]}' LIMIT 1");
    
    $ticket_id = $this->app->DB->Select("SELECT t.id FROM ticket t, ticket_nachricht tn WHERE tn.id='$id' AND tn.ticket=t.schluessel LIMIT 1");

    if($projekt==""){
      $projekt = $this->app->DB->Select("SELECT t.projekt FROM ticket_nachricht AS tn, ticket AS t 
	WHERE t.schluessel=tn.ticket AND tn.id=".$id);
    } 

    //$projekt; 
    $options = $this->app->erp->GetProjektSelect($projekt,&$color_selected);
    $this->app->Tpl->Set(SELECT_PROJEKT,"<select name=\"projekt\" 
      style=\"background-color:$color_selected;\"
      onChange=\"this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor\">$options</select>");

    $this->app->Tpl->Add(TABS, "<li><h2>Ticket Assistent</h2></li>");
    $this->app->Tpl->Add(TABS, "<li><a href=\"index.php?module=ticket&action=freigabe&id=$id\">zur&uuml;ck zur &Uuml;bersicht</a></li>");
   
    /* ticket lock auf aktuelle ansicht (fuer alle anderen sperren)*/ 
    $this->app->DB->Update("UPDATE ticket SET inbearbeitung=1,inbearbeitung_user='".$this->app->User->GetName()."' WHERE id='$ticket_id' LIMIT 1");

    /* hole Ticket-ID */
    $ticketid = $this->app->DB->Select("SELECT schluessel FROM ticket WHERE id=$ticket_id");
    
    /* meldung anzeigen fuer manuelle freigabe*/ 
    $inbearbeitung = $this->app->DB->Select("SELECT inbearbeitung FROM ticket WHERE id='$ticket_id' LIMIT 1");
    if($inbearbeitung=="1")
      $this->app->Tpl->Set(MELDUNG,"<div class=\"error\">Ticket f&uuml;r alle anderen gesperrt. 
	Wenn Sie nicht antworten wollen klicken Sie <input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=ticket&action=freigabe&id=$id'\">.</div>");

    //wenn bearbeiter leer, dann fuellen
    $bearbeiter = $this->app->DB->Select("SELECT bearbeiter FROM ticket WHERE id='$ticket_id' LIMIT 1");
    if($bearbeiter=="")
    {
      $bearbeiter = $this->app->User->GetDescription();
      $this->app->DB->Update("UPDATE ticket SET bearbeiter='$bearbeiter' WHERE id='$ticket_id' LIMIT 1");
    }
	  //header("Location: index.php?module=ticket&action=list");
	  $javascriptcode = '<script type="text/javascript">
	  function popup(radioObj){
	  if(radioObj[0].checked){
	  tmp = window.open("index.php?module=ticket&action=antwort&mode=new&message='.$id.'","popup",
	  "location=no,menubar=no,toolbar=no,status=no,resizable=yes,scrollbars=yes,width=1000,height=800");
	  tmp.focus();
	}			  
	}
	  </script>';
	$this->app->Tpl->Set(JAVASCRIPTCODE, $javascriptcode);
    // wenn abschicken gedrueck worden ist
    if($this->app->Secure->GetPOST("abschicken")!="")
    {
	if($projekt=="")
	  $sql = "UPDATE ticket SET prio='$prio', warteschlange='$warteschlange', adresse='$adresse', zugewiesen=1 WHERE id='$ticket_id' LIMIT 1";
	else
	  $sql = "UPDATE ticket SET prio='$prio', warteschlange='$warteschlange', adresse='$adresse', projekt='$projekt',zugewiesen=1 WHERE id='$ticket_id' LIMIT 1";

	$this->app->DB->Update($sql);

	if($this->app->Secure->GetPOST("antwort")=="sofort")
	{
	  //TODO muss noch implementiert werden
	  $redirect_kunde = 1;
	  //exit;
	} 
	else if($this->app->Secure->GetPOST("antwort")=="spam")
	{
	  $redirect_kunde = 0;
	  $this->app->DB->Update("UPDATE ticket SET inbearbeitung=0 WHERE id='$ticket_id' LIMIT 1");
	  $this->app->DB->Update("UPDATE ticket_nachricht SET status='spam' WHERE id='$id' LIMIT 1");
	  header("Location: index.php?module=ticket&action=list");
	  exit;
	}
	else
	{
	  // ticket wird zugeordnet und bearbeitung wieder freigegeben
	  $this->app->DB->Update("UPDATE ticket SET inbearbeitung=0 WHERE id='$ticket_id' LIMIT 1");
	  header("Location: index.php?module=ticket&action=list");
	  exit;
	}
    }

    $this->app->Tpl->Set(HEADING,"Ticket zuordnen");
 
    $schluessel = $this->app->DB->Select("SELECT schluessel FROM ticket WHERE id='$ticket_id' LIMIT 1");
    $quelle= $this->app->DB->Select("SELECT quelle FROM ticket WHERE id='$ticket_id' LIMIT 1");
    $kunde= $this->app->DB->Select("SELECT kunde FROM ticket WHERE id='$ticket_id' LIMIT 1");
    $mailadresse= $this->app->DB->Select("SELECT mailadresse FROM ticket WHERE id='$ticket_id' LIMIT 1");
    $status= $this->app->DB->Select("SELECT status FROM ticket WHERE id='$ticket_id' LIMIT 1");

    $text = $this->app->DB->Select("SELECT text FROM ticket_nachricht WHERE ticket='$schluessel' LIMIT 1");
    $betreff = $this->app->DB->Select("SELECT betreff FROM ticket_nachricht WHERE ticket='$schluessel' LIMIT 1");
    $zeit = $this->app->DB->Select("SELECT zeit FROM ticket_nachricht WHERE ticket='$schluessel' LIMIT 1");

    if($prio=="")$prio=3;
    $this->app->Tpl->Set(PRIO,$this->app->erp->GetPrioTicketSelect($prio));
    if($warteschlange=="") {
    	    $tmp_warteschlange= $this->app->DB->Select("SELECT warteschlange FROM ticket WHERE id='$ticket_id' LIMIT 1");                                                                                            
    	    if($tmp_warteschlange!="") $warteschlange = $tmp_warteschlange; 
    	    else
    		$warteschlange="verwaltung";
    }
    $this->app->Tpl->Set(WARTESCHLANGE,$this->app->erp->GetWarteschlangeTicketSelect($warteschlange));

    $wartezeit = $this->app->erp->GetWartezeitTicket($zeit);

    $this->app->Tpl->Set(SCHLUESSEL,$schluessel);
    $this->app->Tpl->Set(ID,$id);
    $this->app->Tpl->Set(NAME,$kunde);
    $this->app->Tpl->Set(EMAIL,$mailadresse);
    $this->app->Tpl->Set(ZEIT,$zeit);
    $this->app->Tpl->Set(WARTEZEIT,$wartezeit);
    $this->app->Tpl->Set(QUELLE,$quelle);
    $this->app->Tpl->Set(BETREFF,$betreff);
    //$this->app->Tpl->Set(TEXT,$text);

    if($text == strip_tags($text))
      $text = nl2br($text);

    $text = str_replace('\r\n',"\n",$text);   
      $text = nl2br($text);

 
    $this->app->Tpl->Set(TEXT,"<center><textarea name=\"ticket_nachricht\" rows=\"20\" cols=\"102\" >$text</textarea></center>");


   // anhaenge anzeigen
    $anhaenge = $this->app->DB->SelectArr("SELECT datei FROM datei_stichwoerter WHERE subjekt='Anhang' AND objekt='Ticket' AND parameter='$id'");
    if(count($anhaenge))
    { 
      foreach($anhaenge as $key=>$value)
      {
        $this->app->Tpl->Add(ANHAENGE,"<a href=\"index.php?module=dateien&action=send&id={$value['datei']}\" target=\"_blank\">".$this->app->erp->GetDateiName($value['datei'])."</a>&nbsp;");
      }
    } else $this->app->Tpl->Add(ANHAENGE,"keine Anh&auml;nge");

    $this->app->Tpl->Set(VORSCHLAG,$this->TicketVorschlagAdresse($kunde,$mailadresse,$text,$betreff,$projekt));

    $this->app->YUI->AutoComplete(KUNDEAUTO,"adresse",array('kundennummer','name','email'),"CONCAT(kundennummer,' ',name)","kunde");

    $this->app->Tpl->Parse(PAGE,"ticket_assistent.tpl");


    if($redirect_kunde)
    {
      //header("Location: index.php?module=adresse&action=edit&id=$adresse");
      header("Location: index.php?module=ticket&action=list");
      exit;
    }

  }

  function TicketVorschlagAdresse($name,$email,$text,$betreff,$projekt)
  {

    $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE email='$email' AND kundennummer!='' and geloescht=0 LIMIT 1");

    if($adresse > 0)
    {
      // rueckgabe 1111 name
      return $this->app->DB->Select("SELECT CONCAT(kundennummer,' ',name) FROM adresse WHERE id='$adresse'");
    }

  }


  function TicketList()
  { 
    $this->app->Tpl->Add(KURZUEBERSCHRIFT, "Tickets");
    //$this->app->Tpl->Add(TABS, "<li><a href=\"index.php?module=ticket&action=list\">&Uuml;bersicht</a></li>");
  
    //$this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=ticket&action=alle\">Alle Tickets</a>");
    //$this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=ticket&action=create\">Neues Ticket anlegen</a>");


   $type = $this->app->Secure->GetGET("cmd");

    if($type=="") $type="offene";

    switch($type)
    {
      case "offene":

      $this->app->Tpl->Set(AKTIV_TAB1,"selected");

    /* offene Tickets */
    $this->app->Tpl->Set(HEADING,"Tickets");
    $this->app->Tpl->Set(SUBHEADING,"offene Tickets");
    
    $table = new EasyTable($this->app);

    $table->Query("SELECT '<input type=checkbox>' as muell, DATE_FORMAT(tn.zeit,'%d.%m.%Y %H:%i') as empfang, t.quelle,CONCAT(LEFT(tn.betreff,30),'...') as betreff,t.kunde, 
      CONCAT('<font color=\"red\"><b>',TIMEDIFF(NOW(),tn.zeit),'</b></font>') as wartezeit, 
      tn.id FROM ticket t LEFT JOIN ticket_nachricht tn ON tn.ticket=t.schluessel WHERE tn.status!='beantwortet' AND t.zugewiesen=0 AND t.inbearbeitung=0 order by tn.zeit DESC ");

    $table->DisplayNew(INHALT,"<a href=\"index.php?module=ticket&action=assistent&id=%value%\"><img src=\"./themes/[THEME]/images/arrow.png\" width=\"20\" border=\"0\"></a>");

    $this->app->Tpl->Parse(TAB1,"rahmen100.tpl");
    $this->app->Tpl->Set(INHALT,"");
    break;

    case "zugewiesene":
      $this->app->Tpl->Set(AKTIV_TAB2,"selected");

    /* zugewiesene Tickets */
    $this->app->Tpl->Set(HEADING,"Tickets");

    $warteschlangen = $this->app->erp->GetWarteschlangeTicket();
    
    foreach($warteschlangen as $key=>$value)
    {
      if($this->app->User->GetType()=="admin" || $this->app->User->GetType()=="verwaltung" || $this->app->User->GetUsername()==$key)
      {
	$this->app->Tpl->Set(SUBHEADING,"zugewiesene Tickets: $value");
	$table = new EasyTable($this->app);
	/* $table->Query("SELECT DATE_FORMAT(t.zeit,'%d.%m.%Y') as zeit, t.prio, t.betreff, t.kunde, 
	  CONCAT('<font color=\"red\"><b>',TIMEDIFF(NOW(),t.zeit),'</b></font>') as wartezeit, 
	  t.id FROM ticket as t, ticket_nachricht as tn WHERE t.schluessel=tn.ticket AND 
	  tn.status!='beantwortet' AND t.zugewiesen=1 AND t.warteschlange='$key'
	  AND inbearbeitung!='1'
	  ORDER by t.prio, tn.zeit");
	*/
/*
	$table->Query("SELECT DATE_FORMAT(tn.zeit,'%d.%m.%Y') as zeit, t.prio, CONCAT(LEFT(tn.betreff,30),'...') as betreff, tn.verfasser, 
	  CONCAT('<font color=\"red\"><b>',TIMEDIFF(NOW(),tn.zeit),'</b></font>') as wartezeit, (SELECT COUNT(tn.id) FROM ticket_nachricht as tn WHERE tn.ticket=t.schluessel)-1 as mails,
	  tn.id FROM ticket as t, ticket_nachricht as tn WHERE t.schluessel=tn.ticket AND 
	  tn.status!='beantwortet' AND tn.status!='spam' AND t.zugewiesen=1 AND t.warteschlange='$key'
	  AND t.inbearbeitung!='1'
	  ORDER by t.prio, tn.zeit ");
*/

	$table->Query("SELECT DATE_FORMAT(tn.zeit,'%d.%m.%Y') as zeit, t.prio, CONCAT(LEFT(tn.betreff,30),'...') as betreff, tn.verfasser, 
	  CONCAT('<font color=\"red\"><b>',TIMEDIFF(NOW(),tn.zeit),'</b></font>') as wartezeit, 
	  tn.id FROM ticket as t, ticket_nachricht as tn WHERE t.schluessel=tn.ticket AND 
	  tn.status!='beantwortet' AND tn.status!='spam' AND t.zugewiesen=1 AND t.warteschlange='$key'
	  AND t.inbearbeitung!='1'
	  ORDER by t.prio, tn.zeit ");

	$table->DisplayNew(INHALT,"<a href=\"index.php?module=ticket&action=edit&id=%value%\">Antworten</a>");
	$this->app->Tpl->Parse(TAB1,"rahmen100.tpl");
	$this->app->Tpl->Set(INHALT,"");
      }
    }
    break;

    case "bearbeitung":
      $this->app->Tpl->Set(AKTIV_TAB3,"selected");
    
    /* in Bearbeitung */

    $this->app->Tpl->Set(SUBHEADING,"In Bearbeitung");
    $table = new EasyTable($this->app);
    $table->Query("SELECT DISTINCT DATE_FORMAT(tn.zeit,'%d.%m.%Y') as zeit, CONCAT(LEFT(tn.betreff,30),'...') as betreff, t.kunde, t.bearbeiter,
      tn.id FROM ticket as t, ticket_nachricht tn WHERE t.schluessel=tn.ticket AND
      t.inbearbeitung='1'
      ORDER by t.prio, tn.zeit DESC");


    if($this->app->User->GetType()=="admin" || $this->app->User->GetType()=="sauterbe" || $this->app->User->GetType()=="sautercl" || $this->app->User->GetType()=="verwaltung")
      $freigabe = "&nbsp;<a href=\"index.php?module=ticket&action=freigabe&id=%value%\">Freigabe</a>";

    $table->DisplayNew(INHALT,"<a href=\"index.php?module=ticket&action=edit&id=%value%&lesen=1\">Lesen</a>$freigabe");
    $this->app->Tpl->Parse(TAB1,"rahmen100.tpl");
    $this->app->Tpl->Set(INHALT,"");

    break;
    case "archiv":
      $this->app->Tpl->Set(AKTIV_TAB4,"selected");
    /* Archiv */

    $this->app->Tpl->Set(SUBHEADING,"Archiv");
    $table = new EasyTable($this->app);

    $sql = $this->app->erp->TicketArchivSuche(TAB1);
    $table = new EasyTable($this->app);

    $limit = $this->app->Secure->GetPOST("limit");
    if($limit=="" || $limit==0) $limit=10;

    $table->Query($sql,$limit);


/*
    $table->Query("SELECT DATE_FORMAT(tn.zeit,'%d.%m.%Y %H:%i') as zeit,CONCAT(LEFT(tn.betreff,30),'...') as betreff, t.kunde, 
      tn.id FROM ticket as t, ticket_nachricht as tn WHERE t.schluessel=tn.ticket AND tn.status='beantwortet' AND t.zugewiesen=1 
      AND inbearbeitung!='1'
      ORDER by tn.zeitausgang DESC LIMIT 5");
*/
    $table->DisplayOwn(INHALT,"<a href=\"index.php?module=ticket&action=edit&id=%value%&lesen=1\">Lesen</a>");
    $this->app->Tpl->Parse(TAB1,"rahmen100.tpl");
    $this->app->Tpl->Set(INHALT,"");
    break;
    case "spam":
      $this->app->Tpl->Set(AKTIV_TAB5,"selected");
    /* Archiv */

    $this->app->Tpl->Set(SUBHEADING,"SPAM");
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(tn.zeit,'%d.%m.%Y') as zeit,CONCAT(LEFT(tn.betreff,30),'...') as betreff, t.kunde, 
      tn.id FROM ticket as t, ticket_nachricht as tn WHERE t.schluessel=tn.ticket AND tn.status='spam'
      ORDER by t.prio, tn.zeit DESC LIMIT 5");

    $table->DisplayNew(INHALT,"<a href=\"index.php?module=ticket&action=edit&id=%value%&lesen=1\">Lesen</a>");
    $this->app->Tpl->Parse(TAB1,"rahmen100.tpl");
    $this->app->Tpl->Set(INHALT,"");

    break;
    default:;
    }

    $tab = $this->app->Secure->GetGET("tab");

/*
    if($this->app->User->GetType()=="admin" ||  $this->app->User->GetType()=="verwaltung")
    {
      if(!is_numeric($tab))$tab=1;
      $this->app->Tpl->Set(AKTIV_TAB.$tab,"selected");
      $this->app->Tpl->Parse(PAGE,"ticketuebersicht.tpl");
    }
    else
    {
      if(!is_numeric($tab))$tab=2;
      $this->app->Tpl->Set(AKTIV_TAB.$tab,"selected");
      $this->app->Tpl->Parse(PAGE,"ticketuebersicht_small.tpl");
    }
*/
      $this->app->Tpl->Parse(PAGE,"ticketuebersicht.tpl");
  }

  
  function TicketMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(HEADING,"Ticket Bearbeiten");

    //$this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=ticket&action=edit&id=$id\">Korrespondenz</a>&nbsp;");
    //$this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=ticket&action=edit&id=$id\">Ticket</a>&nbsp;");
    //$this->app->Tpl->Add(TABS,"<a href=\"index.php?module=ticket&action=kosten&id=$id\">Gesamtkalkulation</a>&nbsp;");
    //$this->app->Tpl->Add(TABS,"<a class=\"tab\" href=\"index.php?module=ticket&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a>&nbsp;");
  }


  function TicketEdit()
  {
    $zurueck = $this->app->Secure->GetPOST("zurueck");
    $lesen = $this->app->Secure->GetGET("lesen");

    $id = $this->app->Secure->GetGET("id"); // ist immer nachricht!


    if($zurueck!=""){
      header("Location: index.php?module=ticket&action=list");
      exit;
    }

    $this->app->Tpl->Add(KURZUEBERSCHRIFT, "Tickets");
    if($lesen=="")
      $this->app->erp->MenuEintrag("index.php?module=ticket&action=freigabe&id=$id","zur&uuml;ck zur &Uuml;bersicht");
    else
      $this->app->erp->MenuEintrag("index.php?module=ticket&action=list","zur&uuml;ck zur &Uuml;bersicht");
   
    $ticket_id = $this->app->DB->Select("SELECT t.id FROM ticket t, ticket_nachricht tn WHERE tn.id='$id' AND tn.ticket=t.schluessel LIMIT 1");

    $prio = $this->app->Secure->GetPOST("prio");
    $warteschlange= $this->app->Secure->GetPOST("warteschlange");
    $projekt= $this->app->Secure->GetPOST("projekt");
    //$message = $this->app->Secure->GetGET("message");
    $message = $id;
  
    $adresse = $this->app->Secure->GetPOST("adresse");
    $tmp = split(" ",$adresse); 
    if(strlen($tmp[0])>=5)
      $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='{$tmp[0]}' LIMIT 1");
    else $adresse="0";
   

    if($lesen!="1")
      $this->app->DB->Update("UPDATE ticket SET inbearbeitung=1,inbearbeitung_user='".$this->app->User->GetName()."' WHERE id='$ticket_id' LIMIT 1");
    if($prio!="")
      $this->app->DB->Update("UPDATE ticket SET prio='{$prio}' WHERE id='{$ticket_id}' LIMIT 1");
    if($warteschlange!="")
      $this->app->DB->Update("UPDATE ticket SET warteschlange='{$warteschlange}' WHERE id='{$ticket_id}' LIMIT 1");
    if($projekt!="")
      $this->app->DB->Update("UPDATE ticket SET projekt='{$projekt}' WHERE id='{$ticket_id}' LIMIT 1");
    if($adresse!="" && $adresse!=0)
    {
      $this->app->DB->Update("UPDATE ticket SET adresse='{$adresse}' WHERE id='{$ticket_id}' LIMIT 1");

      // anhaenge gehoeren adresse
      $datarr = $this->app->DB->SelectArr("SELECT * FROM datei_stichwoerter WHERE objekt='Ticket' AND parameter='{$message}'");
      if(count($datarr)>0)
      {
	foreach($datarr as $key=>$value)
	{
	  $this->app->DB->Delete("DELETE FROM datei_stichwoerter WHERE datei='{$value[datei]}' AND objekt!='Ticket' AND parameter!='{$message}'");
	  $this->app->DB->Insert("INSERT INTO datei_stichwoerter (id,datei,subjekt,objekt,parameter,logdatei) VALUES ('','{$value[datei]}','Anhang von Ticketnachricht {$message}','Adressen','$adresse',NOW())");
	}
      } 

    }

    if($projekt!=""||$warteschlange!=""||$prio!="")
    {
      //$this->app->DB->Update("UPDATE ticket SET inbearbeitung=0 WHERE id='$ticket_id' LIMIT 1");
      //header("Location: index.php?module=ticket&action=list");
      //exit;
    }


    $schluessel = $this->app->DB->Select("SELECT schluessel FROM ticket WHERE id='$ticket_id' LIMIT 1");
    $quelle= $this->app->DB->Select("SELECT quelle FROM ticket WHERE id='$ticket_id' LIMIT 1");
    $kunde= $this->app->DB->Select("SELECT adresse FROM ticket WHERE id='$ticket_id' LIMIT 1");
    $status= $this->app->DB->Select("SELECT status FROM ticket WHERE id='$ticket_id' LIMIT 1");
    $prio= $this->app->DB->Select("SELECT prio FROM ticket WHERE id='$ticket_id' LIMIT 1");
    $warteschlange= $this->app->DB->Select("SELECT warteschlange FROM ticket WHERE id='$ticket_id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM ticket WHERE id='$ticket_id' LIMIT 1");
    $adressid  = $this->app->DB->Select("SELECT adresse FROM ticket WHERE id='$ticket_id' LIMIT 1");
    $kunde= $this->app->DB->Select("SELECT CONCAT(kundennummer,' ',name) FROM adresse WHERE id='$adressid' LIMIT 1");

    $bearbeiter = $this->app->DB->Select("SELECT bearbeiter FROM ticket WHERE id='{$ticket_id}' LIMIT 1");
    if($bearbeiter==""){
      $bearbeiter = $this->app->User->GetDescription();
      $this->app->DB->Update("UPDATE ticket SET bearbeiter='$bearbeiter' WHERE id='{$ticket_id}' LIMIT 1");
    }

    $inbearbeitung = $this->app->DB->Select("SELECT inbearbeitung FROM ticket WHERE id='$ticket_id' LIMIT 1");
    if($inbearbeitung=="1" && $lesen!=1)
      $this->app->Tpl->Set(MELDUNG,"<div class=\"error\">Ticket f&uuml;r alle anderen gesperrt. 
	Wenn Sie nicht antworten wollen klicken Sie <input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=ticket&action=freigabe&id=$id'\">.</div>");


    if($lesen=="1"){
      $this->app->Tpl->Set(MELDUNG,"<div class=\"info\">Ticket wurde bereits beantwortet. Sie befinden sich im Lesemodus. <input type=\"button\" value=\"Ticket jetzt freigeben\" onclick=\"window.location.href='index.php?module=ticket&action=freigabe&id=$id'\"></div>");
      $this->app->Tpl->Set(LESENSTART,"<!--");
      $this->app->Tpl->Set(LESENENDE,"-->");
    }

    $this->app->Tpl->Set(PRIO,"<select name=\"prio\">".$this->app->erp->GetPrioTicketSelect($prio)."</select>");
    $this->app->Tpl->Set(WARTESCHLANGE,"<select name=\"warteschlange\">".$this->app->erp->GetWarteschlangeTicketSelect($warteschlange)."</select>");
    $this->app->Tpl->Set(PROJEKT,"<select name=\"projekt\">".$this->app->erp->GetProjektSelect($projekt)."</select>");

    $this->app->Tpl->Set(KUNDE,'<input type="text" size="40" name="adresse" id="adresse" value="'.$kunde.'">');
    
    $this->app->Tpl->Set(ADRESSE, $adresse);
    $this->app->Tpl->Set(SCHLUESSEL, $schluessel);
    $this->app->Tpl->Set(TICKET,$schluessel);
    $this->app->Tpl->Set(BEARBEITER,$bearbeiter);
    $this->app->Tpl->Set(QUELLE,$quelle);
    
   // anhaenge anzeigen
    $anhaenge = $this->app->DB->SelectArr("SELECT datei FROM datei_stichwoerter WHERE subjekt='Anhang' AND objekt='Ticket' AND parameter='$id'");
    if(count($anhaenge))
    { 
      foreach($anhaenge as $key=>$value)
      {
        $this->app->Tpl->Add(ANHAENGE,"<a href=\"index.php?module=dateien&action=send&id={$value['datei']}\" target=\"_blank\">".$this->app->erp->GetDateiName($value['datei'])."</a>&nbsp;");
      }
    } else $this->app->Tpl->Add(ANHAENGE,"keine Anh&auml;nge");


    
    $this->TicketMenu();
    $table = new EasyTable($this->app);
    $table->Query("SELECT 
      IF(tn.id='$message', CONCAT('<b>',DATE_FORMAT(tn.zeit, '%d.%m.%Y, %H:%i'),'</b>'), DATE_FORMAT(tn.zeit, '%d.%m.%Y, %H:%i')) AS 'Eingang',
      if(tn.zeitausgang!='0000-00-00 00:00:00',DATE_FORMAT(tn.zeitausgang, '%d.%m'),'') AS 'Ausgang', 
      tn.bearbeiter,
      tn.status,
      tn.id FROM ticket_nachricht AS tn, ticket AS t WHERE tn.ticket='$schluessel' AND t.schluessel='$schluessel' order by tn.zeit ASC");
    
    $table->DisplayNew(TABLE,"<a href=\"index.php?module=ticket&action=edit&id=%value%&lesen=$lesen\">Eingangsmail</a>
      <a href=\"index.php?module=ticket&action=edit&mode=ausgang&id=%value%&lesen=$lesen\">Ausgangsmail</a>");
    
    /* ab hier nur noch ticket_nachricht */
    if(!is_numeric($message)){
      $message = $this->app->DB->Select("SELECT MAX(id) FROM ticket_nachricht WHERE ticket='$schluessel'");
    }

    $eingangstext = $this->app->DB->Select("SELECT text FROM ticket_nachricht WHERE ticket='$schluessel' AND id=$message LIMIT 1");
    $ausgangstext = $this->app->DB->Select("SELECT textausgang FROM ticket_nachricht WHERE ticket='$schluessel' AND id=$message LIMIT 1");
    $betreff = $this->app->DB->Select("SELECT betreff FROM ticket  WHERE schluessel='$schluessel' LIMIT 1");
    $zeit = $this->app->DB->Select("SELECT DATE_FORMAT(zeit,'%d.%m.%Y, %H:%i') FROM ticket_nachricht  WHERE ticket='$schluessel' AND id=$message LIMIT 1");
    $zeitausgang = $this->app->DB->Select("SELECT DATE_FORMAT(zeitausgang,'%d.%m.%Y') FROM 
      ticket_nachricht  WHERE ticket='$schluessel' AND id=$message LIMIT 1");
    $status= $this->app->DB->Select("SELECT status FROM ticket_nachricht  WHERE ticket='$schluessel' AND id=$message LIMIT 1");
    $versendet= $this->app->DB->Select("SELECT versendet FROM ticket_nachricht  WHERE ticket='$schluessel' AND id=$message LIMIT 1");
    $verfasser= $this->app->DB->Select("SELECT verfasser FROM ticket_nachricht  WHERE ticket='$schluessel' AND id=$message LIMIT 1");
    $email = $this->app->DB->Select("SELECT mail FROM ticket_nachricht  WHERE ticket='$schluessel' AND id=$message LIMIT 1");
    $lastmessage = $this->app->DB->Select("SELECT MAX(id) FROM ticket_nachricht WHERE ticket='$schluessel' LIMIT 1");    
  
    if($this->app->Secure->GetGET("mode") == "ausgang"){
      $text = $ausgangstext;
      $zeit = $this->app->DB->Select("SELECT zeitausgang FROM ticket_nachricht  WHERE ticket='$schluessel' AND id=$message LIMIT 1");
    }
    else
      $text = $eingangstext; 
    
    $this->app->Tpl->Set(NAME,$verfasser);
    $this->app->Tpl->Set(EMAIL, $email);
    $text = nl2br($text);
    $this->app->Tpl->Add(TEXT,"<center><textarea name=\"ticket_nachricht\" rows=\"20\" cols=\"102\" >$text</textarea></center>");
    $this->app->Tpl->Add(BETREFF,$betreff);
    $this->app->Tpl->Add(ZEIT,$zeit);
    $this->app->Tpl->Add(STATUS,$status);
    $this->app->Tpl->Add(VERSENDET,$versendet);

    if($lesen=="")
      $this->app->Tpl->Set(ZURUECK,"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=ticket&action=freigabe&id=$id'\">");
    
    if($status == "beantwortet")
      $this->app->Tpl->Set(WARTEZEIT,"beantwortet");
    else{
      //TODO beo 0000-00-00 kommt falsche ausgabe
      $red = '<font color="red">'.$this->app->erp->GetWartezeitTicket($zeit).'</font>';	
      $this->app->Tpl->Set(WARTEZEIT, $red);
    }

    $this->app->Tpl->Add(ID,$ticket_id); //?????
    $this->app->Tpl->Add(TICKETNACHRICHTID,$message);
    $this->app->Tpl->Add(MESSAGE,$message);
    $this->app->Tpl->Set(LASTMESSAGE, $lastmessage);

    parent::TicketEdit();
  }



}

?>
