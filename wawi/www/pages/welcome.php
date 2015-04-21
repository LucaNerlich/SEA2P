<?php
class Welcome 
{

  function Welcome(&$app)
  {
    $this->app=&$app; 

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("login","WelcomeLogin");
    $this->app->ActionHandler("main","WelcomeMain");
    $this->app->ActionHandler("poll","WelcomePoll");
    $this->app->ActionHandler("list","TermineList");
    $this->app->ActionHandler("cronjob","WelcomeCronjob");
    $this->app->ActionHandler("adapterbox","WelcomeAdapterbox");
    $this->app->ActionHandler("help","WelcomeHelp");
    $this->app->ActionHandler("info","WelcomeInfo");
    $this->app->ActionHandler("icons","WelcomeIcons");
    $this->app->ActionHandler("vorgang","VorgangAnlegen");
    $this->app->ActionHandler("removevorgang","VorgangEntfernen");
    $this->app->ActionHandler("editvorgang","VorgangEdit");
    $this->app->ActionHandler("logout","WelcomeLogout");
    $this->app->ActionHandler("start","WelcomeStart");
    $this->app->ActionHandler("settings","WelcomeSettings");
    $this->app->ActionHandler("upgrade","WelcomeUpgrade");
    $this->app->ActionHandler("startseite","WelcomeStartseite");
    $this->app->ActionHandler("addnote","WelcomeAddNote");
    $this->app->ActionHandler("movenote","WelcomeMoveNote");
    $this->app->ActionHandler("oknote","WelcomeOkNote");
    $this->app->ActionHandler("delnote","WelcomeDelNote");
    $this->app->ActionHandler("pinwand","WelcomePinwand");
    $this->app->ActionHandler("css","WelcomeCss");
    $this->app->ActionHandler("logo","WelcomeLogo");
    $this->app->ActionHandler("unlock","WelcomeUnlock");
    $this->app->ActionHandler("direktzugriff","WelcomeDirektzugriff");

    $this->app->DefaultActionHandler("login");

    $this->app->ActionHandlerListen($app);
  }

  function WelcomePoll()
  {
    $smodule = $this->app->Secure->GetGET("smodule");
    $saction = $this->app->Secure->GetGET("saction");
    $sid = $this->app->Secure->GetGET("sid");
    $user = $this->app->Secure->GetGET("user");

    $this->app->erp->TimeoutUseredit($smodule,$sid,$user);

    //uodate nur erlauben wenn time stamp in 		
    //echo "red";
    exit;
  }	


  function WelcomeDirektzugriff()
  {
    $direktzugriff = $this->app->Secure->GetPOST("direktzugriff");


    switch($direktzugriff)
    {
      case "1": $link="index.php?module=adresse&action=list"; break;
      case "11": $link="index.php?module=adresse&action=list"; break;
      case "12": $link="index.php?module=artikel&action=list"; break;
      case "13": $link="index.php?module=projekt&action=list"; break;

      case "2": $link="index.php?module=angebot&action=list"; break;
      case "21": $link="index.php?module=anfrage&action=list"; break;
      case "22": $link="index.php?module=angebot&action=list"; break;
      case "23": $link="index.php?module=auftrag&action=list"; break;

      case "3": $link="index.php?module=bestellung&action=list"; break;
      case "31": $link="index.php?module=bestellung&action=list"; break;
      case "32": $link="index.php?module=lager&action=ausgehend"; break;
      case "33": $link="index.php?module=produktion&action=list"; break;

      case "5": $link="index.php?module=rechnung&action=list"; break;

      case "8": $link="index.php?module=lieferschein&action=list"; break;
      case "81": $link="index.php?module=lieferschein&action=list"; break;
      case "82": $link="index.php?module=lager&action=list"; break;
      case "84": $link="index.php?module=versanderzeugen&action=offene"; break;
      default: $link="index.php";
    }

    header("Location: $link");
    exit;
  }

  function WelcomeAdapterbox()
  {
    $ip = $this->app->Secure->GetGET("ip");
    $serial = $this->app->Secure->GetGET("serial");
    $device = $this->app->Secure->GetGET("device");
    if(is_numeric($ip))
      $ip = long2ip($ip);
    else $ip="";

    echo "OK";
    $this->app->DB->Delete("DELETE FROM adapterbox_log WHERE ip='$ip'");
    $this->app->DB->Insert("INSERT INTO adapterbox_log (id,datum,ip,meldung,seriennummer,device)
        VALUES ('',NOW(),'$ip','Adapterbox connected ($device)','$serial','device')");


    // check if there is an adapterbox
    $anzahldrucker = $this->app->DB->Select("SELECT COUNT(id) FROM drucker WHERE art=2 AND anbindung='adapterbox'");

    if($anzahldrucker <= 0 && $device=="zebra")
    {
      $this->app->DB->Insert("INSERT INTO drucker (id,art,anbindung,adapterboxseriennummer,bezeichnung,name,aktiv,firma)
          VALUES ('','2','adapterbox','$serial','Zebra','Etikettendrucker',1,1)");
      $tmpid = $this->app->DB->GetInsertID();

      $this->app->erp->FirmendatenSet("standardetikettendrucker",$tmpid);
    }

    $xml ='
      <label>
      <line x="3" y="3" size="4">Step 2 of 2</line>
      <line x="3" y="8" size="4">Connection establish</line>
      <line x="3" y="13" size="4">Server: '.$_SERVER['SERVER_ADDR'].'</line>
      </label>
      ';

    if($this->app->erp->Firmendaten("deviceenable")!="1")
    {
      HttpClient::quickPost("http://".$ip."/labelprinter.php?amount=1",array('label'=>$xml,'amount'=>1));
      //$this->app->erp->EtikettenDrucker("xml",1,"","","",$xml);
    } else {
      $job = base64_encode(json_encode(array('label'=>base64_encode($xml),'amount'=>$anzahl)));//."<amount>".$anzahl."</amount>");
      $this->app->DB->Insert("INSERT INTO device_jobs (id,zeitstempel,deviceidsource,deviceiddest,job,art) VALUES ('',NOW(),'000000000','$serial','$job','labelprinter')");
    }	


    // update ip
    if($ip!="")
      $this->app->DB->Update("UPDATE drucker SET adapterboxip='$ip' WHERE adapterboxseriennummer='$serial' LIMIT 1");

    //uodate nur erlauben wenn time stamp in 		
    //echo "red";
    exit;
  }	




  function WelcomeCronjob()
  {
    system("php5 ../cronjobs/starter.php");
    exit;
  }	

  function WelcomeStart()
  {
    if($this->app->erp->UserDevice()=="smartphone")
    {
      $this->WelcomeStartSmartphone();
    } else {
      $this->WelcomeStartDesktop();
    }
  }

  function WelcomeStartSmartphone()
  {
    header("Location: index.php?module=mobile&action=list");
    exit;
  }

  function WelcomeStartDesktop()
  {
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Ihre Startseite");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT2,"[BENUTZER]");
    $this->app->erp->StartseiteMenu();

    $this->app->Tpl->Set(TABTEXT,"Ihre Startseite");

    $module = $this->app->Secure->GetGET("module");


    //fenster rechts offene vorgaenge ***
    $this->app->Tpl->Set(SUBSUBHEADING,"Vorg&auml;nge");
    $arrVorgaenge = $this->app->DB->SelectArr("SELECT * FROM offenevorgaenge WHERE adresse='{$this->app->User->GetAdresse()}' ORDER by id DESC");
    $this->app->Tpl->Set(INHALT,"");
    if(count($arrVorgaenge) > 0)
    {
      for($i=0;$i<count($arrVorgaenge);$i++)
      {

        $this->app->Tpl->Add(VORGAENGE,"<tr><td>".substr(ucfirst($arrVorgaenge[$i]['titel']),0,100)."</td><td align=\"right\"><img src=\"./themes/[THEME]/images/1x1t.gif\" width=\"7\" border=\"0\" align=\"right\">
            <a href=\"index.php?".$arrVorgaenge[$i]['href']."\"><img src=\"./themes/[THEME]/images/right.png\" border=\"0\" align=\"right\" title=\"Erledigen\"></a>&nbsp;
            <a href=\"index.php?module=welcome&action=removevorgang&vorgang={$arrVorgaenge[$i]['id']}\"><img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\" align=\"right\" title=\"Erledigt\"></a>&nbsp;
            <img src=\"./themes/[THEME]/images/1x1t.gif\" width=\"3\" border=\"0\" align=\"right\">
            <a href=\"javascript: var ergebnistext=prompt('Offenen Vorgang umbenennen:','".ucfirst($arrVorgaenge[$i]['titel'])."'); if(ergebnistext!='' && ergebnistext!=null) window.location.href='index.php?module=welcome&action=editvorgang&vorgang={$arrVorgaenge[$i]['id']}&titel='+ergebnistext;\"><img src=\"./themes/[THEME]/images/edit.png\" alt=\"Bearbeiten\" title=\"Bearbeiten\" border=\"0\" align=\"right\"></a></td></tr>");

      }
    }

    $this->app->erp->KalenderList(KALENDER);

    $this->app->Tpl->Parse(STARTSEITE,"lesezeichen.tpl");

    if($this->app->User->GetType()=="admin")
    {
      $tmpprojects = $this->app->DB->SelectArr("SELECT id,abkuerzung FROM projekt WHERE geloescht='0' ORDER by abkuerzung");

      $montag = $this->app->erp->getFirstDayOfWeek(date('Y'), date('W'));

      for($i=0;$i<count($tmpprojects);$i++)
      {
        $projektid = $tmpprojects[$i][id];
        $abkuerzung = $tmpprojects[$i][abkuerzung];
        $summe_auftraege = $this->app->DB->Select("SELECT SUM(ap.preis*ap.menge) FROM auftrag a LEFT JOIN
            auftrag_position ap ON ap.auftrag=a.id LEFT JOIN artikel art ON art.id=ap.artikel WHERE 
            art.projekt='$projektid' AND a.datum >='$montag' ");

        $gesamtsumme += $summe_auftraege;

        setlocale(LC_MONETARY, 'de_DE');

        if($summe_auftraege > 0)
        {
          $tpl .='<tr><td>'.$abkuerzung.'</td><td align="right"> '.money_format('%= ^-14#8.2i',$summe_auftraege).' EUR</td></tr>';
        }
      }
      /*
         $sql = "SELECT FLOOR(SUM(FORMAT(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600,2)))
         FROM zeiterfassung z WHERE z.abrechnen=1 AND z.ist_abgerechnet!=1 AND z.adresse_abrechnung > 0 AND 
         DATE_FORMAT(z.von,'%Y-%m-%d') >='$montag'";

         $gesamt_stunden = $this->app->DB->Select($sql);
         $tpl .='<tr><td>Gesamt (netto)</td><td align="right">'.money_format('%= ^-14#8.2i',$gesamtsumme).' EUR</td></tr>';
         $tpl .='<tr><td>Gebuchte Stunden</td><td align="right">'.money_format('%= ^-14#8.2i',$gesamt_stunden).'</td></tr>';
         $tpl .='<tr><td>mit Ausgaben (netto)</td><td align="right"><b>'.money_format('%= ^-14#8.2i',$gesamtsumme/100*20).' EUR</b></td></tr>';
         $tpl .='<tr><td>ca. Umsatz Stunden</td><td align="right"><b>'.money_format('%= ^-14#8.2i',$gesamt_stunden*65).' EUR</b></td></tr>';
       */			
      $this->app->Tpl->Set(UMSATZ,'<h1 onmouseover="document.getElementById(\'umsatzwoche\').style.display=\'block\';" onmouseout="document.getElementById(\'umsatzwoche\').style.display=\'none\';">Umsatz ab Montag</h1>
          <div style="margin:5px;display:none" id="umsatzwoche"><table width="90%">
          '.$tpl.'
          </table>
          </div>
          <br>');
    }

    if($this->app->Conf->WFdbType=="postgre")
      $this->app->Tpl->Set('TERMINE', $this->Termine($this->app->DB->Select("SELECT CAST(now() AS date);")));
    else
      $this->app->Tpl->Set('TERMINE', $this->Termine($this->app->DB->Select("SELECT CURDATE();")));


    if($this->app->Conf->WFdbType=="postgre")
      $this->app->Tpl->Set('TERMINEMORGEN', $this->Termine($this->app->DB->Select("SELECT CAST(now() AS date) + INTERVAL '1 DAY'")));
    else	
      $this->app->Tpl->Set('TERMINEMORGEN', $this->Termine($this->app->DB->Select("SELECT DATE_ADD(CURDATE(), INTERVAL 1 DAY);")));

    if($this->app->Conf->WFdbType=="postgre") {
      $summestunden = $this->app->DB->Select("SELECT SUM((extract(epoch from z.bis)-extract(epoch from z.von))/3600.0) as stunden
          FROM zeiterfassung z WHERE z.abrechnen='1' AND z.ist_abgerechnet IS NULL OR z.ist_abgerechnet='0'");
    } else {
      $summestunden = $this->app->DB->Select("SELECT SUM((UNIX_TIMESTAMP(z.bis)-UNIX_TIMESTAMP(z.von))/3600.0) as stunden
          FROM zeiterfassung z WHERE z.abrechnen='1' AND z.ist_abgerechnet IS NULL OR z.ist_abgerechnet='0' AND z.adresse_abrechnung > 0");
    }

    if($summestunden > 0)
      $this->app->Tpl->Add('DRINGEND','<li>'.number_format($summestunden,2,',','.').' Stunden nicht abgerechnet (<a href="index.php?module=zeiterfassung&action=abrechnenpdf">PDF</a>)</li>');

    // reservierungen ohne auftraege
    /*
       $tmp = $this->app->DB->SelectArr("SELECT a.id,a.nummer, a.name_de, (SELECT SUM(ap.menge-ap.geliefert) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND auf.status='freigegeben') as auftrag,
       (SELECT SUM(l.menge) FROM lager_reserviert l WHERE l.artikel=a.id) as reserviert FROM artikel a WHERE (SELECT SUM(l.menge) FROM lager_reserviert l WHERE l.artikel=a.id) > (SELECT SUM(ap.menge-ap.geliefert) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND auf.status='freigegeben')");
    //(SELECT SUM(ap.menge-ap.geliefert) FROM auftrag_position ap WHERE ap.artikel=a.id)");

    if(count($tmp)>0)
    $this->app->Tpl->Add('DRINGEND','<li><b>Sonderreservierungen</b></li><ul style="list-style-position:outside;">');
    for($i=0;$i<count($tmp);$i++)
    {
    $this->app->Tpl->Add('DRINGEND','<li>'.$tmp[$i]['name_de'].'&nbsp;<a href="index.php?module=artikel&action=lager&id='.$tmp[$i]['id'].'"><img src="./themes/new/images/edit.png"></a></li>');

    }
    if(count($tmp)>0)
    $this->app->Tpl->Add('DRINGEND','</ul>');
     */


    // Wiki-Einträge
    //$data = $this->app->DB->SelectArr("SELECT * FROM accordion ORDER BY position");


    $this->app->Tpl->Set(USERNAME,$this->app->User->GetName());

    $tmp = $this->app->DB->SelectArr("SELECT * FROM aufgabe WHERE adresse='".$this->app->User->GetAdresse()."' AND startseite='1' AND status='offen' ORDER by prio DESC");
    //TODOFORUSER

    for($i=0;$i<count($tmp);$i++)
    {
      $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='".$tmp[$i]['initiator']."' LIMIT 1");
      $high="";
      if($tmp[$i]['initiator']!=$tmp[$i]['adresse']) $additional = "<br><font style=\"font-size:8pt\">von ".$name."</font>"; else $additional="";


      if($tmp[$i]['prio']=="1") { $class="noteit_highprio"; $high="&nbsp;(Prio)"; }
      else $class="noteit";

      $this->app->Tpl->Add(TODOFORUSER,"<div class=\"$class\">".$tmp[$i]['aufgabe'].$additional."$high<br>
          <span style=\"cursor:pointer\" onclick=\"if(!confirm('Wirklich ".$tmp[$i]['aufgabe']." bearbeiten?')) return false; else window.location.href='index.php?module=aufgaben&action=edit&id=".$tmp[$i]['id']."&referrer=1#tabs-3';\"><img src=\"./themes/new/images/edit.png\"></span>
          <span style=\"cursor:pointer\" onclick=\"if(!confirm('Wirklich ".$tmp[$i]['aufgabe']." abschließen?')) return false; else window.location.href='index.php?module=aufgaben&action=abschluss&id=".$tmp[$i]['id']."&referrer=1';\"><img src=\"./themes/new/images/versand.png\"></span></div>
          ");
    }

    if($i<=0)
      $this->app->Tpl->Add(TODOFORUSER,"Keine Aufgaben f&uuml;r Startseite");

    $tmp = $this->app->DB->SelectArr("SELECT * FROM aufgabe WHERE initiator='".$this->app->User->GetAdresse()."' AND adresse!='".$this->app->User->GetAdresse()."' AND startseite='1' AND status='offen' ORDER by prio DESC");

    for($i=0;$i<count($tmp);$i++)
    {
      $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='".$tmp[$i]['adresse']."' LIMIT 1");
      $high="";
      if($tmp[$i]['prio']=="1") { $class="noteit_highprio"; $high="&nbsp;(Prio)"; }
      else $class="noteit";


      $this->app->Tpl->Add(TODOFORMITARBEITER,"<div class=\"$class\">".$tmp[$i]['aufgabe']."$high<br><font style=\"font-size:8pt\">f&uuml;r&nbsp;".$name."</font></div>");
    }
    if($i<=0)
      $this->app->Tpl->Add(TODOFORMITARBEITER,"Keine Aufgaben f&uuml;r Startseite");


    $this->app->Tpl->Set('ACCORDION', $this->Accordion());

    $this->WaWisionUpgradeFeed();

    if($this->app->erp->Version()=="oss")
    {
      $this->app->Tpl->Set('INFO',"<br><h1>Info</h1>Sie verwenden die Open-Source Version. Wir bieten f&uuml;r diese folgende Leistungen an:
          <br><br><ul><li><a href=\"http://shop.wawision.de/sonstige/1-jahr-zugang-updateserver-open-source-version.html?c=164\" target=\"_blank\">Update-Zugang</a> f&uuml;r 39,90 EUR im Jahr</li><li>Zubeh&ouml;r und extra Module im <a href=\"http://shop.wawision.de\" target=\"_blank\">Shop</a></li></ul>");
    }



    $this->app->Tpl->Parse(PAGE,"startseite.tpl");
    //    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }


  function WelcomeIcons()
  {
    $type = $this->app->Secure->GetGET("type");
    header("Content-type: image/svg+xml");

    switch($type)
    {
      case "artikelgruppe.svg":
        $xml = file_get_contents("./images/icons/artikelgruppe.svg");
        break;
    }
    //style="fill:#3fb9cd; hintergrund"
    //style="fill:#e43f25;
    //style="fill:#a6e0be;
    //style="fill:#449cbe;

    $farbe1 = $this->app->erp->Firmendaten("firmenfarbeganzdunkel");

    if($farbe1 =="")
      $farbe1 = "rgb(7, 134, 153)";

    $farbe2 = "#e43f25"; // rot im artikel
    $farbe3 = "#a6e0be"; // hell tyrkis im artikel kreis
    $farbe4 = "#449cbe"; // dunkelblau im artikel rechteck 

    $xml = str_replace('#3fb9cd',$farbe1,$xml);
    $xml = str_replace('#e43f25',$farbe2,$xml);
    $xml = str_replace('#a6e0be',$farbe3,$xml);
    $xml = str_replace('#449cbe',$farbe4,$xml);

    echo $xml;
    exit;
  }

  function WelcomeLogo()
  {
    if($this->app->erp->Firmendaten("firmenlogo")!="")
    {
      header("Content-Type: image/png");
      echo base64_decode($this->app->erp->Firmendaten("firmenlogo"));
      exit;
    }

  }


  function WelcomeCss()
  {
    $file = $this->app->Secure->GetGET("file");

    if ($this->app->erp->UserDevice()=="smartphone") {

    } else { 

      if($file=="style.css")
        $tmp = file_get_contents("./themes/new/css/style.css");


      if($file=="popup.css")
        $tmp = file_get_contents("./themes/new/css/popup.css");


      if($file=="grid.css")
        $tmp = file_get_contents("./themes/new/css/grid.css");
    }	

    $firmenfarbehell = $this->app->erp->Firmendaten("firmenfarbehell");
    if($firmenfarbehell =="")
      $firmenfarbehell = "#c2e3ea";//rgb(67, 187, 209)"; //ALT

    $firmenfarbedunkel = $this->app->erp->Firmendaten("firmenfarbedunkel");
    if($firmenfarbedunkel =="")
      $firmenfarbedunkel = "#53bed0";//rgb(2, 125, 141)"; //ALT

    $firmenfarbeganzdunkel = $this->app->erp->Firmendaten("firmenfarbeganzdunkel");
    if($firmenfarbeganzdunkel =="")
      $firmenfarbeganzdunkel = "#018fa3";

    $navigationfarbe = $this->app->erp->Firmendaten("navigationfarbe"); //ALT
    if($navigationfarbe =="")
      $navigationfarbe = "#48494b";

    $navigationfarbeschrift = $this->app->erp->Firmendaten("navigationfarbeschrift");
    if($navigationfarbeschrift =="")
      $navigationfarbeschrift = "#c9c9cb";

    $unternavigationfarbe = $this->app->erp->Firmendaten("unternavigationfarbe");
    if($unternavigationfarbe =="")
      $unternavigationfarbe = "#d5ecf2";

    $unternavigationfarbeschrift = $this->app->erp->Firmendaten("unternavigationfarbeschrift");
    if($unternavigationfarbeschrift =="")
      $unternavigationfarbeschrift = "#027d8d";


    $firmenfarbe = $this->app->erp->Firmendaten("firmenfarbe");
    if($firmenfarbe =="")
      $firmenfarbe = "#48494b";	

    $tmp = str_replace("[TPLSYSTEMBASE]",$firmenfarbe,$tmp);

    $tmp = str_replace("[TPLFIRMENFARBEHELL]",$firmenfarbehell,$tmp);
    $tmp = str_replace("[TPLFIRMENFARBEDUNKEL]",$firmenfarbedunkel,$tmp);
    $tmp = str_replace("[TPLFIRMENFARBEGANZDUNKEL]",$firmenfarbeganzdunkel,$tmp);
    $tmp = str_replace("[TPLNAVIGATIONFARBE]",$navigationfarbe,$tmp);
    $tmp = str_replace("[TPLNAVIGATIONFARBESCHRIFT]",$navigationfarbeschrift,$tmp);

    $tmp = str_replace("[TPLUNTERNAVIGATIONFARBE]",$unternavigationfarbe,$tmp);
    $tmp = str_replace("[TPLUNTERNAVIGATIONFARBESCHRIFT]",$unternavigationfarbeschrift,$tmp);


    $subaction = $this->app->Secure->GetGET("subaction");
    $submodule = $this->app->Secure->GetGET("submodule");
    if($subaction=="pinwand" || $subaction=="start" || $submodule=="kalender")
      $tmp = str_replace("[JSDMMZINDEX]","10000",$tmp);
    else
      $tmp = str_replace("[JSDMMZINDEX]","10",$tmp);


    if($this->app->erp->Firmendaten("standardaufloesung")=="1"){
      $tmp = str_replace("[CSSSMALL1]","1000",$tmp);
      $tmp = str_replace("[CSSSMALL2]","1000",$tmp);
      $tmp = str_replace("[CSSMARGIN]","margin-left: auto; margin-right: auto;",$tmp);
    } else {
      $tmp = str_replace("[CSSSMALL1]","1200",$tmp);
      $tmp = str_replace("[CSSSMALL2]","1200",$tmp);
      $tmp = str_replace("[CSSMARGIN]","margin-left: auto; margin-right: auto;",$tmp);
    }



    header("Content-type: text/css");
    echo $tmp;

    exit;

  }

  function WaWisionUpgradeFeed($max=3)
  {
    if(!$this->app->Conf->WFoffline)
    {
      $branch = $this->app->erp->Branch();
      $BLOGURL = 'http://update.embedded-projects.net/wawision.php?branch='.$branch; 
        $NUMITEMS = 2; $TIMEFORMAT = "j F Y, g:ia"; 
      $CACHEFILE = $this->app->erp->GetTMP().md5($BLOGURL); 
      $CACHETIME = 4; # hours


        if(!file_exists($CACHEFILE) || ((time() - filemtime($CACHEFILE)) > 3600 * $CACHETIME)) { 
          if($feed_contents = @file_get_contents($BLOGURL)) { 
# write feed contents to cache file 
            $fp = fopen($CACHEFILE, 'w'); 
            fwrite($fp, $feed_contents); 
            fclose($fp); 
          } 
        }

      $feed_contents = file_get_contents($CACHEFILE);
      $xml = simplexml_load_string($feed_contents);
      $json = json_encode($xml);
      $array = json_decode($json,TRUE);

      for($i=0;$i<count($array['channel']['item']);$i++)
      {
        $this->app->Tpl->Add(WAIWISONFEEDS,"<tr><td><b>".$array['channel']['item'][$i]['title']
            ."</b></td></tr><tr><td  style=\"font-size:7pt\">".$array['channel']['item'][$i]['description']."</td></tr>");
      }
      $this->app->Tpl->Parse(WELCOMENEWS,"welcome_news.tpl");
    }
  }


  function WelcomeAddNote()
  {

    if($this->app->Secure->GetPOST("note-body")!="")
    {
      $color = $this->app->Secure->GetPOST("color");
      $aufgabe = $this->app->Secure->GetPOST("note-body");

      $aufgabe =  str_replace('\r\n',' ',$aufgabe);

      $beschreibung = $this->app->Secure->GetPOST("note-body");
      $max_z = $this->app->DB->Select("SELECT MAX(note_z) FROM aufgabe WHERE adresse='".$this->app->User->GetAdresse()."' ");
      $id = $this->app->erp->CreateAufgabe($this->app->User->GetAdresse(),$aufgabe);
      $this->app->DB->Update("UPDATE aufgabe SET pinwand='1',note_color='$color', note_z='$max_z',beschreibung='$beschreibung' WHERE id='$id' LIMIT 1");


      $this->app->Tpl->Set(PAGE, "<script>
          parent.location.href = './index.php?module=welcome&action=pinwand';
          </script>");
    }
    else 
      $this->app->Tpl->Parse(PAGE,"welcome_pinwand_addnote.tpl");

    $this->app->BuildNavigation=false;
  }



  function WelcomeDelNote()
  {
    $id = $this->app->Secure->GetGET("id");

    $tmp = rand(8888,999999999);

    $this->app->DB->Update("DELETE FROM aufgabe WHERE id='$id' LIMIT 1");
    header("Location: index.php?module=welcome&action=pinwand");
    exit;
  }



  function WelcomeOkNote()
  {
    $id = $this->app->Secure->GetGET("id");


    $this->app->DB->Update("UPDATE aufgabe SET status='abgeschlossen' WHERE id='$id' LIMIT 1");
    header("Location: index.php?module=welcome&action=pinwand");

    exit;
  }




  function WelcomeMoveNote()
  {
    $x = $this->app->Secure->GetGET("x");
    $y = $this->app->Secure->GetGET("y");
    $z = $this->app->Secure->GetGET("z");
    $id = $this->app->Secure->GetGET("id");

    $this->app->DB->Update("UPDATE aufgabe SET note_x='$x',note_y='$y',note_z='$z' WHERE id='$id' LIMIT 1");
    exit;
  }


  function WelcomePinwand()
  {
    $this->app->erp->StartseiteMenu();

    $tmp = $this->app->DB->SelectArr("SELECT * FROM aufgabe WHERE adresse='".$this->app->User->GetAdresse()."' AND pinwand='1' AND status='offen'");

    for($i=0;$i<count($tmp);$i++)
    {
      $left = $tmp[$i][note_x];
      $color = $tmp[$i][note_color];
      if($color=="")$color="yellow";
      $top = $tmp[$i][note_y];
      $zindex = $tmp[$i][note_z];
      $text = nl2br($this->app->erp->ReadyForPDF($tmp[$i][beschreibung]));
      $id = $tmp[$i][id];
      $projekt = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$tmp[$i][projekt]."' LIMIT 1");


      $result = ' <div class="note '.$color.'" style="left:'.$left.'px;top:'.$top.'px;  z-index:'.$zindex.'">
        '.$text.'  
        <div class="author">'.$projekt.'&nbsp;
      <a href="index.php?module=aufgaben&action=edit&id='.$id.'#tabs-3" target="_blank"><img src="./themes/[THEME]/images/edit.png"></a>&nbsp;
      <a href="index.php?module=welcome&action=delnote&id='.$id.'"><img src="./themes/[THEME]/images/delete.gif"></a>
        <a href="index.php?module=welcome&action=oknote&id='.$id.'"><img src="./themes/[THEME]/images/ok.png"></a>
        </div>
        <span class="data">'.$id.'</span>
        </div>';

      $this->app->Tpl->Add(NOTES,$result);
    }

    $this->app->Tpl->Set(POPUPWIDTH,"400");	
    $this->app->Tpl->Set(POPUPHEIGHT,"400");	

    $this->app->Tpl->Parse(PAGE,"welcome_pinwand.tpl");	
  }

  function Accordion()
  {
    // check if accordion is empty

    $data = $this->app->DB->SelectArr("SELECT * FROM accordion ORDER BY position");

    if(count($data)<=0)
    {
      $this->app->DB->Insert("INSERT INTO accordion (name,target,position) VALUES ('Startseite','StartseiteWiki','1')");

      $check_startseite = $this->app->DB->Select("SELECT name FROM wiki WHERE name='StartseiteWiki' LIMIT 1");
      if($check_startseite == "")
      {
        $wikifirstpage='<h1>waWision</h1>
<p>Herzlich Willkommen in Ihrem waWision,<br><br>wir freuen uns Sie als waWision Benutzer begrüßen zu dürfen. Mit waWision organisieren Sie Ihre Firma schnell und einfach. Sie haben alle wichtigen Zahlen und Vorgänge im Überblick.<br><br>Für Einsteiger sind die folgenden Thema wichtig:<br><br></p>
<ul>
<li> <a href="index.php?module=firmendaten&amp;action=edit" target="_blank"> Firmendaten</a> (dort richten Sie Ihr Briefpapier ein)</li>
<li> <a href="index.php?module=adresse&amp;action=list" target="_blank"> Stammdaten / Adressen</a> (Kunden und Lieferanten angelen)</li>
<li> <a href="index.php?module=artikel&amp;action=list" target="_blank"> Artikel anlegen</a> (Ihr Artikelstamm)</li>
<li> <a href="index.php?module=angebot&amp;action=list" target="_blank"> Angebot</a> / <a href="index.php?module=auftrag&amp;action=list" target="_blank"> Auftrag</a> (Alle Dokumente für Ihr Geschäft)</li>
<li> <a href="index.php?module=rechnung&amp;action=list" target="_blank"> Rechnung</a> / <a href="index.php?module=gutschrift&amp;action=list" target="_blank"> Gutschrift</a></li>
<li> <a href="index.php?module=lieferschein&amp;action=list" target="_blank"> Lieferschein</a></li>
</ul>
<p><br><br>Kennen Sie unsere Zusatzmodule die Struktur und Organisation in das tägliche Geschäft bringen?<br><br></p>
<ul>
<li> <a href="index.php?module=kalender&amp;action=list" target="_blank"> Kalender</a></li>
<li> <a href="index.php?module=wiki&amp;action=list" target="_blank"> Wiki</a></li>
</ul>';

        $this->app->DB->Insert("INSERT INTO wiki (name,content) VALUES ('StartseiteWiki','".$wikifirstpage."')");
      }
      $data = $this->app->DB->SelectArr("SELECT * FROM accordion ORDER BY position");
    }


    $out = '';
    for($i=0;$i<count($data);$i++) 
    {
      $entry = '';
      $edit = '';
      if($data[$i]['target']!='') {
        $edit = "<a id=\"wiki_startseite_edit\" href=\"index.php?module=wiki&action=edit&name={$data[$i]['target']}\">Seite editieren</a>";

        $wikipage_exists = $this->app->DB->Select("SELECT '1' FROM wiki WHERE name='{$data[$i]['target']}' LIMIT 1");
        if($wikipage_exists!='1')
          $this->app->DB->Insert("INSERT INTO wiki (name) VALUES ('{$data[$i]['target']}')");
        $wikipage_content = $this->app->DB->Select("SELECT content FROM wiki WHERE name='{$data[$i]['target']}' LIMIT 1");

        $wikiparser = new WikiParser();
        $content = $wikiparser->parse($wikipage_content);

        $this->app->Tpl->Set('ACCORDIONENTRY'.$i, $content);
        $entry = "[ACCORDIONENTRY$i]";
      }	
      $out .= "<!--<h3><a href=\"#\">{$data[$i]['name']}</a></h3>-->
        <div><div class=\"wiki\"><!--$edit<br/>-->$entry<br>$edit</div></div>";
    }
    return $out;
  }

  function WelcomeUpgrade()
  {
    $this->app->erp->MenuEintrag("index.php?module=welcome&action=start","zur&uuml;ck zur Startseite");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Update f&uuml;r WaWision");

    $lizenz = $this->app->erp->Firmendaten("lizenz");
    $schluessel = $this->app->erp->Firmendaten("schluessel");
    if($lizenz=="" || $schluessel=="")
    {
      if(is_file("../wawision.inc.php"))
      {
        include_once("../wawision.inc.php");
        $this->app->erp->FirmendatenSet("lizenz",$WAWISION['serial']);	
        $this->app->erp->FirmendatenSet("schluessel",$WAWISION['authkey']);	
      }
    }

    $this->app->Tpl->Set(TABTEXT,"Upgrade");
    $this->WaWisionUpgradeFeed(5);

    if($this->app->Secure->GetPOST("upgrade"))
    {
      ob_start();
      // dringend nacheinander, sonst wird das alte upgrade nur ausgefuehrt
      if($this->app->erp->IsWindows())
      {
        system("cd .. && c:\\xampp\\php\\php.exe upgradesystemclient2.php && c:\\xampp\\php\\php.exe upgradedbonly.php");
      } else {
        system("cd ../ && php5 upgradesystemclient2.php && php5 upgradedbonly.php");
      }	
      $result = ob_get_contents();
      ob_end_clean();
      include("../version.php");
      $result .="\r\nIhre Version: $version_revision\r\n";
    }

    if($this->app->Secure->GetPOST("upgradedb"))
    {
      ob_start();
      //      include("upgradesystemclient.php");
      if($this->app->erp->IsWindows())
        system("cd .. && c:\\xampp\\php\\php.exe upgradedbonly.php");
      else
        $result = ob_get_contents();
      ob_end_clean();
    }

    if($this->app->erp->Firmendaten("version")=="")
      $this->app->erp->FirmendatenSet("version",$this->app->erp->RevisionPlain());

    $this->app->Tpl->Add(TAB1,"<table width=\"100%\"><tr valign=\"top\"><td width=\"70%\"><form action=\"\" method=\"post\">
        <textarea rows=\"15\" cols=\"90\">$result</textarea>
        <br><input type=\"submit\" value=\"Update starten\" name=\"upgrade\">&nbsp;
        <input type=\"submit\" value=\"Reparieren\" name=\"upgradedb\">&nbsp;
        </form></td><td>[WELCOMENEWS]</td></tr></table>");

        $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }


  function Termine($date)
  {
    $userid = $this->app->User->GetID();

    if(is_numeric($userid)) {
      $termine = $this->app->DB->SelectArr("SELECT DISTINCT color,von,bis,bezeichnung FROM kalender_user AS ka
          RIGHT JOIN kalender_event AS ke ON ka.event=ke.id
          WHERE (ka.userid='$userid' OR ke.public='1') AND DATE(von)='$date'
          ORDER BY von");
      $out = '';
      foreach($termine AS $t) {
        $von = date('G:i', strtotime($t['von']));
        $bis = date('G:i', strtotime($t['bis']));

        if($t['allDay']=='1') {
          $von = 'Ganztags';
          $bis = '';
        }else {
          if($von==$bis)
            $bis = '';
          else 
            $bis = '- '.$bis;
        }

        $color = (($t['color']!='') ? "style='background-color: {$t['color']};border-color: {$t['color']};'" : '');

        $out .= "<li $color><span class=\"description\">{$t['bezeichnung']}</span><span style=\"float:right; margin-top:-15px;\">$von $bis&nbsp;&nbsp;</span></li>";
      }

      if(count($termine)==0) $out = '<center><i>Keine Termine vorhanden</i></center>';

      return $out;
    }
  }



  function Aufgaben($parse)
  {
    $userid = $this->app->User->GetAdresse();

    if(is_numeric($userid))
    {


    }

  }

  function WelcomeHelp()
  {
  }

  function WelcomeSettings()
  {
    $submit_password = $this->app->Secure->GetPOST("submit_password");

    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Pers&ouml;nliche Einstellungen");


    $submit_startseite = $this->app->Secure->GetPOST("submit_startseite");
    $startseite = $this->app->Secure->GetPOST("startseite");

    if($submit_startseite!=""){
      $this->app->DB->Update("UPDATE user SET startseite='$startseite' WHERE id='".$this->app->User->GetID()."' LIMIT 1");
    }

    if($submit_password!="")
    {
      $password = $this->app->Secure->GetPOST("password");
      $repassword = $this->app->Secure->GetPOST("passwordre");

      if($password!="" && $password==$repassword)
      {
        $this->app->Tpl->Set(MESSAGE,"<div class=\"error2\">Passwort wurde ge&auml;ndert!</div>");
        $passwordmd5 = md5($password);
        $this->app->DB->Update("UPDATE user SET passwordmd5='$passwordmd5' WHERE id='".$this->app->User->GetID()."' LIMIT 1");
      } else if($password!=""){
        $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Passworteingabe falsch! Bitte zwei mal das gleiche Passwort eingeben!</div>");
      }
    }		

    $startseite = $this->app->DB->Select("SELECT startseite FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");

    $this->app->Tpl->Set(STARTSEITE,$startseite);

    $this->app->Tpl->Parse(PAGE,"welcome_settings.tpl");

  }



  function WelcomeInfo()
  {

    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Informationen zur Software");

    $this->app->Tpl->Set(TABTEXT,"Informationen zur Software");

    if($this->app->erp->Version()!="oss")
    {
      $this->app->Tpl->Set(TAB1,"Sie benutzen die kommerzielle Version von waWision. Alle Rechte vorbehalten. Beachten Sie die Nutzungsbedinungen.<br><br>&copy; Copyright by embedded projects GmbH Augsburg");
    }
    else {
      $this->app->Tpl->Set(TAB1,"Sie benutzen die Open-Source Version von waWision. Die Software steht unter der GNU/AGPL.<br><br><div class=\"info\">Das Logo und der Link zur Homepage <a href=\"http://www.wawision.de\" target=\"_blank\">http://www.wawision.de</a> d&uuml;rfen
          nicht entfernt werden.</div><br>&copy; Copyright by embedded projects GmbH Augsburg");
    }
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");

  }


  function WelcomeMenu()
  {

    /*	
        $this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\" style=\"background-color: [FARBE1]\">Stammdaten</h2></li>");
        $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=adresse&action=list\">Adresse</a></li>");
        $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=artikel&action=list\">Artikel</a></li>");


        $this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\" style=\"background-color: [FARBE2]\">Verkauf & Einkauf</h2></li>");
        $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=angebot&action=list\">Angebote</a></li>");
        $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=auftrag&action=list\">Auftr&auml;ge</a></li>");
        $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=lieferschein&action=list\">Lieferscheine</a></li>");
        $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=rechnung&action=list\">Rechnungen</a></li>");
        $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=gutschrift&action=list\">Gutschrift</a></li>");
        $this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\" style=\"background-color: [FARBE3]\">Versand & Logistik</h2></li>");
        $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=ticket&action=list\">Tickets</a></li>");
        $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=versanderzeugen&action=offene\">Versand starten</a></li>");
        $this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\" style=\"background-color: [FARBE4]\">Buchhaltung</h2></li>");
        $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=zahlungseingang&action=list\">Zahlungseingang</a></li>");
        $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=rechnung&action=mahnwesen\">Mahnwesen</a></li>");
        $this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\" style=\"background-color: [FARBE5]\">Einstellungen</h2></li>");
        $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=einstellungen&action=list\">Allgemein</a></li>");
        $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=einstellungen&action=firma\">Firma</a></li>");

        $this->app->erp->MenuEintrag("index.php?module=lager&action=list","zur&uuml;ck zur &Uuml;bersicht");

     */
    $this->app->Tpl->Add(KURZUEBERSCHRIFT,"<h2>Startseite</h2>");

  }


  function WelcomeMain()
  {

    $this->app->Tpl->Set(UEBERSCHRIFT,"Herzlich Willkommen ".$this->app->User->GetDescription()."!");
    $this->WelcomeMenu();

    // muss jeder sehen
    $this->app->erp->LagerAusgehend(ARTIKEL);

    if($this->app->User->GetType()=="admin")
    {
      /*
         $table = new EasyTable($this->app);
         $table->Query("SELECT a.name, betrag, rechnung, DATE_FORMAT(zahlbarbis,'%d.%m.%Y') as bis,skonto, DATE_FORMAT(skontobis,'%d.%m.%Y') as skonbis, verbindlichkeit.id FROM verbindlichkeit, adresse a WHERE verbindlichkeit.adresse = a.id AND verbindlichkeit.bezahlt!=1 
         AND zahlbarbis <= NOW() AND freigabe=1 AND status!='bezahlt' OR (verbindlichkeit.skontobis!=0 AND verbindlichkeit.skonto > 0 AND verbindlichkeit.adresse = a.id AND status!='bezahlt')  order by zahlbarbis");
         $table->Display(VERBINDLICHKEITEN);
       */
      /*
      //TICKETS
      $table->Query("SELECT DATE_FORMAT(tn.zeit,'%d.%m.%Y %H:%i') as empfang, CONCAT(LEFT(tn.betreff,30),'...') as betreff,t.warteschlange, 
      CONCAT('<font color=\"red\"><b>',TIMEDIFF(NOW(),tn.zeit),'</b></font>') as wartezeit, 
      tn.id FROM ticket t LEFT JOIN ticket_nachricht tn ON tn.ticket=t.schluessel WHERE tn.status!='beantwortet' AND tn.status!='spam' AND t.inbearbeitung=0 order by tn.zeit ASC");

      $table->DisplayNew(TICKETS,"<a href=\"index.php?module=ticket&action=assistent&id=%value%\"><img src=\"./themes/[THEME]/images/arrow.png\" width=\"20\" border=\"0\"></a>");
       */
      /*
         $table = new EasyTable($this->app);
         $table->Query("SELECT a.name, betrag, rechnung, DATE_FORMAT(zahlbarbis,'%d.%m.%Y') as bis,skonto, DATE_FORMAT(skontobis,'%d.%m.%Y') as skonbis, verbindlichkeit.id FROM verbindlichkeit, adresse a WHERE verbindlichkeit.adresse = a.id AND verbindlichkeit.bezahlt!=1 
         AND zahlbarbis <= NOW() AND freigabe=1 AND status!='bezahlt' OR (verbindlichkeit.skontobis!=0 AND verbindlichkeit.skonto > 0 AND verbindlichkeit.adresse = a.id AND status!='bezahlt')  order by zahlbarbis");
         $table->Display(VERBINDLICHKEITEN);


         $table->Query("SELECT DATE_FORMAT(tn.zeit,'%d.%m.%Y %H:%i') as empfang, CONCAT(LEFT(tn.betreff,30),'...') as betreff,t.warteschlange, 
         CONCAT('<font color=\"red\"><b>',TIMEDIFF(NOW(),tn.zeit),'</b></font>') as wartezeit, 
         tn.id FROM ticket t LEFT JOIN ticket_nachricht tn ON tn.ticket=t.schluessel WHERE tn.status!='beantwortet' AND tn.status!='spam' AND t.inbearbeitung=0 order by tn.zeit ASC");
       */
      // $table->DisplayNew(TICKETS,"<a href=\"index.php?module=ticket&action=assistent&id=%value%\"><img src=\"./themes/[THEME]/images/arrow.png\" width=\"20\" border=\"0\"></a>");



      /*
         $this->app->Tpl->Set(SUBHEADING,"Termine Heute");
      //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
      $table = new EasyTable($this->app);
      $table->Query("SELECT '23.11.2009' as datum, '15:00' as zeit, aufgabe,id FROM aufgabe LIMIT 3,3");
      $table->DisplayNew(INHALT,"<a href=\"index.php?module=ticket&action=assistent&id=%value%\">Lesen</a>");
      $this->app->Tpl->Parse(TERMINE,"rahmen.tpl");
      $this->app->Tpl->Set(INHALT,"");

      $this->app->Tpl->Set(SUBHEADING,"Aufgaben Heute");
      //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
      $table = new EasyTable($this->app);
      $table->Query("SELECT '23.11.2009' as datum, '15:00' as zeit, aufgabe,id FROM aufgabe LIMIT 3");
      $table->DisplayNew(INHALT,"<a href=\"index.php?module=ticket&action=assistent&id=%value%\">Lesen</a>");
      $this->app->Tpl->Parse(AUFGABEN,"rahmen.tpl");
      $this->app->Tpl->Set(INHALT,"");

      $this->app->Tpl->Set(SUBHEADING,"Wichtige Tickets zum Beantworten");
      //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
      $table = new EasyTable($this->app);

      $key = 'technik';
      $table->Query("SELECT DATE_FORMAT(t.zeit,'%d.%m.%Y') as zeit, t.prio, t.betreff, t.kunde, 
      CONCAT('<font color=\"red\"><b>',TIMEDIFF(NOW(),t.zeit),'</b></font>') as wartezeit, 
      t.id FROM ticket as t, ticket_nachricht as tn WHERE t.schluessel=tn.ticket AND tn.status!='beantwortet' AND t.zugewiesen=1 
      AND (t.warteschlange='$key')
      AND inbearbeitung!='1'
      ORDER by t.prio, tn.zeit");

      $table->DisplayNew(INHALT,"<a href=\"index.php?module=ticket&action=assistent&id=%value%\">Lesen</a>");
      $this->app->Tpl->Parse(TICKETS,"rahmen.tpl");
      $this->app->Tpl->Set(INHALT,"");

      $this->app->Tpl->Set(SUBHEADING,"offene Arbeitspakete");
      //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
      $table = new EasyTable($this->app);
      $table->Query("SELECT '23.11.2009' as datum, '15:00' as zeit, ticket,id FROM ticket LIMIT 3");
      $table->DisplayNew(INHALT,"<a href=\"index.php?module=ticket&action=assistent&id=%value%\">Lesen</a>");
      $this->app->Tpl->Parse(ARBEITSPAKETE,"rahmen.tpl");
      $this->app->Tpl->Set(INHALT,"");

      $this->app->Tpl->Set(SUBHEADING,"Bitte freigeben durch ".$this->app->User->GetDescription());
      //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
      $table = new EasyTable($this->app);
      $table->Query("SELECT '23.11.2009' as datum, '15:00' as zeit, ticket,id FROM ticket LIMIT 3");
      $table->DisplayNew(INHALT,"<a href=\"index.php?module=ticket&action=assistent&id=%value%\">Lesen</a>");
      $this->app->Tpl->Parse(FREIGABEN,"rahmen.tpl");
      $this->app->Tpl->Set(INHALT,"");


      $this->app->Tpl->Set(SUBHEADING,"offene Ausgaben");
      $this->app->Tpl->Set(INHALT,"Tabelle ");
      $this->app->Tpl->Parse(AUSGABEN,"rahmen.tpl");

      $this->app->Tpl->Set(SUBHEADING,"");

       */
      //$this->app->Tpl->Parse(STAT,"welcome_stat.tpl");
      //   $this->app->YUI->ChartAdd("#4040FF",array(5, 10, 20, 10, 40, 52, 68, 70, 30, 20));
      //   $this->app->YUI->ChartAdd("red",array(12, 20, 10, 60, 70, 82, 28, 70, 30, 20));
      //   $this->app->YUI->Chart(STAT,array('ab', 'cd', 'wed', 'thu', 'fri', 'sat', 'sun', 'mon', 'tue', 'wed'));
    }
    $this->app->Tpl->Parse(PAGE,"welcome_main.tpl");
    //$this->app->BuildNavigation=false;
    //$this->app->Tpl->Parse(PAGE,"welcome_stat.tpl");
  }


  function WelcomeStartseite()
  {
    $this->app->erp->Startseite();
  }

  function WelcomeLogin()
  {
    if($this->app->User->GetID()!="")
    {

      // alle cookies SpryMedia loeschen

      // Setzen des Verfalls-Zeitpunktes auf 1 Stunde in der Vergangenheit
      $this->app->erp->ClearCookies();
      $this->app->erp->Startseite();
    }
    else
    {
      $this->app->erp->InitialSetup();
      $this->app->Tpl->Set(UEBERSCHRIFT,"wawision &middot; Enterprise Warehouse Management");
      $this->app->acl->Login();
    }
  }

  function WelcomeLogout()
  {
    $this->app->acl->Logout();
    $this->app->erp->ClearCookies();
    //$this->app->WF->ReBuildPageFrame();
    //$this->WelcomeMain();
  }

  function WelcomeUnlock()
  {
    $gui = $this->app->Secure->GetGET("gui");
    $id =  $this->app->Secure->GetGET("id");

    // sperre entfernen bzw umschreiben
    if($gui=="angebot" || $gui=="auftrag" || $gui=="rechnung" || $gui=="bestellung" || $gui=="gutschrift" || $gui=="lieferschein" || $gui=="adresse" || $gui=="artikel")
    {
      $this->app->DB->Update("UPDATE $gui SET usereditid='".$this->app->User->GetID()."'  WHERE id='$id' LIMIT 1");
      header("Location: index.php?module=$gui&action=edit&id=$id");
      exit;
    }
  }


  function VorgangAnlegen()
  {
    //print_r($_SERVER['HTTP_REFERER']);
    $titel = $this->app->Secure->GetGET("titel");

    $url = parse_url($_SERVER['HTTP_REFERER']);
    //$url = parse_url("http://dev.eproo.de/~sauterbe/eprooSystem-2009-11-21/webroot/index.php?module=ticket&action=edit&id=1");

    //module=ticket&action=edit&id=1
    //$url['query']
    $params = split("&",$url['query']);
    foreach($params as $value){
      $attribut = split("=",$value);
      $arrPara[$attribut[0]] = $attribut[1];
    }

    $adresse = $this->app->User->GetAdresse();
    if($titel=="")
      $titel = ucfirst($arrPara['module'])." ".$arrPara['id'];
    $href = $url['query'];
    $this->app->erp->AddOffenenVorgang($adresse, $titel, $href);

    header("Location: ".$_SERVER['HTTP_REFERER']);
  }


  function VorgangEdit()
  {
    $vorgang = $this->app->Secure->GetGET("vorgang");
    $titel = $this->app->Secure->GetGET("titel");
    $this->app->erp->RenameOffenenVorgangID($vorgang,$titel);
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
  } 

  function VorgangEntfernen()
  {
    $vorgang = $this->app->Secure->GetGET("vorgang");
    $this->app->erp->RemoveOffenenVorgangID($vorgang);
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
  } 


}
?>
