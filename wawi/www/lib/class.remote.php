<?php

class Remote
{

  function Remote(&$app)
  {
    $this->app=$app;
  }

  function RemoteConnection($id)
  {
    return $this->RemoteCommand($id,"auth");
  }

  function RemoteGetUpdateArticleList($id)
  {
    return $this->RemoteCommand($id,"getlist");
  }


  function RemoteGetFileList($id)
  {
    return $this->RemoteCommand($id,"getfilelist");
  }

  function RemoteGetFileListArticle($id,$artikel_id)
  {
    $data[artikel] = $artikel_id;

    return $this->RemoteCommand($id,"getfilelistarticle",$data);
  }

  function RemoteGetAuftraegeAnzahl($id)
  {
    return $this->RemoteCommand($id,"getauftraegeanzahl");
  }

  function RemoteGetAuftrag($id)
  {
    return $this->RemoteCommand($id,"getauftrag");
  }

  function RemoteSendExportlink($id)
  {
    //$data[0] = array('aasas','asddd');


    // passwort erzeugen , daten verschluesseln, wenn passwort neu link an kunden senden
    // alternativ artikel umfrage
    //    $all = $this->app->DB->SelectArr("SELECT * FROM artikelgruppen WHERE shop='$id'");

    // alle artikelid = 38 die in einem auftrag sind


    // usb90key
    $artikelid= 38;

    $all = $this->app->DB->SelectArr("SELECT a.id as auftrag, a.adresse as adresse  FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag WHERE ap.artikel='$artikelid' AND ap.geliefert_menge < ap.menge AND a.status!='storniert'
    AND a.status!='abgeschlossen'");
    // mail mit url senden
 
    $loop = 0;
 
    for($i=0;$i<count($all);$i++)
    {
      $auftragid = $all[$i][auftrag];
      $adresse = $all[$i][adresse];

      $grund = "artikel";

      $check = $this->app->DB->Select("SELECT reg FROM exportlink_sent WHERE adresse='$adresse' AND objekt='$artikelid' AND ident='$auftragid' LIMIT 1");

      if($check=="")
      {

      $checkreg = 1;
      while($checkreg!="")
      {
	  $token1 = md5(uniqid(rand(), true));
	  $token2 = md5(uniqid(rand(), true));

	$token3 = md5(uniqid(rand(), true));
	  $token4 = md5(uniqid(rand(), true));

	  $zufall = $token1.”-”.$token2;
	  $zufall = md5($zufall);

	  $zufall2 = $token3.”-”.$token4;
	  $zufall2 = md5($zufall2);

	  $reg = md5($zufall2.$zufall);


	$checkreg = $this->app->DB->Select("SELECT reg FROM exportlink_sent WHERE reg='$reg' LIMIT 1");
      }

      // pruefen ob es zahl schon gibt sonst nochmal

      $data[$loop][reg] = $reg;
      $data[$loop][grund] = $grund;
      $data[$loop][objekt] = $artikelid; // artikel id
      $data[$loop][ident] = $auftragid;//
      $loop++;

      $this->app->DB->Insert("INSERT INTO exportlink_sent (id,reg,grund,objekt,ident,adresse,datum) VALUES ('','$reg','$grund','$artikelid','$auftragid','$adresse',NOW())");
      }

    }
    return $this->RemoteCommand($id,"exportlink",$data);
  }



 function RemoteSendNavigation($id)
  {
    //$data[0] = array('aasas','asddd');
    
    $all = $this->app->DB->SelectArr("SELECT * FROM shopnavigation WHERE shop='$id'");
    for($i=0;$i<count($all);$i++)
    {

      $data[$i][id] = $all[$i][id];
      $data[$i][bezeichnung] = $all[$i][bezeichnung];
      $data[$i][position] = $all[$i][position];
      $data[$i][parent] = $all[$i][parent];
      $data[$i][bezeichnung_en] = $all[$i][bezeichnung_en];
      $data[$i][plugin] = $all[$i][plugin];
      $data[$i][pluginparameter] =  $all[$i][pluginparameter];
      $data[$i][target] =  $all[$i][target];
    }
    return $this->RemoteCommand($id,"navigation",$data);
  }

  function RemoteSendArtikelgruppen($id)
  {
    //$data[0] = array('aasas','asddd');
    
    $all = $this->app->DB->SelectArr("SELECT * FROM artikelgruppen WHERE shop='$id'");
    for($i=0;$i<count($all);$i++)
    {

      $data[$i][id] = $all[$i][id];
      $data[$i][bezeichnung] = $all[$i][bezeichnung];
      $data[$i][bezeichnung_en] = $all[$i][bezeichnung_en];
      $data[$i][beschreibung_de] = $all[$i][beschreibung_de];
      $data[$i][beschreibung_en] = $all[$i][beschreibung_en];
    }
    return $this->RemoteCommand($id,"artikelgruppen",$data);
  }


  function RemoteSendInhalt($id)
  {
    //$data[0] = array('aasas','asddd');
    
    $all = $this->app->DB->SelectArr("SELECT * FROM inhalt WHERE shop='$id' AND aktiv=1");
    for($i=0;$i<count($all);$i++)
    {

//      $data[$i][id] = $all[$i][id];
      $data[$i][sprache] = $all[$i][sprache];
      $data[$i][inhalt] = $all[$i][inhalt];
      $data[$i][kurztext] = $all[$i][kurztext];
      $data[$i][html] = $all[$i][html];
      $data[$i][title] = $all[$i][title];
      $data[$i][description] = $all[$i][description];
      $data[$i][keywords] = $all[$i][keywords];
      $data[$i][inhaltstyp] = $all[$i][inhaltstyp];
      $data[$i][template] = $all[$i][template];
      $data[$i][finalparse] = $all[$i][finalparse];
      $data[$i][navigation] = $all[$i][navigation];
      $data[$i][sichtbarbis] = $all[$i][sichtbarbis];
      $data[$i][datum] = $all[$i][datum];
      $data[$i][aktiv] = $all[$i][aktiv];
    }
    return $this->RemoteCommand($id,"inhalt",$data);
  }

  function RemoteSendArtikelArtikelgruppen($id)
  {
    //$data[0] = array('aasas','asddd');
    
    $all = $this->app->DB->SelectArr("SELECT * FROM artikel_artikelgruppe");
    for($i=0;$i<count($all);$i++)
    {
      $data[$i][id] = $all[$i][id];
      $data[$i][artikel] = $all[$i][artikel];
      $data[$i][artikelgruppe] = $all[$i][artikelgruppe];
      $data[$i][position] = $all[$i][position];
    }
    return $this->RemoteCommand($id,"artikelartikelgruppen",$data);
  }




 function RemoteSendArticleList($id,$artikel_arr)
  {
    //$data[0] = array('aasas','asddd');
		if(is_file("objectapi/mysql/_gen/object.gen.artikel.php"))
    	include_once("objectapi/mysql/_gen/object.gen.artikel.php");

		if(is_file("/var/www/www/objectapi/mysql/_gen/object.gen.artikel.php"))
    	include_once("/var/www/www/objectapi/mysql/_gen/object.gen.artikel.php");

    $tmp = new ObjGenArtikel($this->app);

    for($i=0;$i<count($artikel_arr);$i++)
    {
      $artikel = $artikel_arr[$i];
      $tmp->Select($artikel);

      $data[$i][artikel] = $artikel;
      $data[$i][nummer] = $tmp->GetNummer();

      $data[$i][inaktiv] = $tmp->GetInaktiv();

			if($tmp->GetIntern_Gesperrt()=="1")
					$data[$i][inaktiv]=1;

      $data[$i][name_de] = $tmp->GetName_De();
      $data[$i][name_en] = $tmp->GetName_En();
      $data[$i][hersteller] = $tmp->GetHersteller();
      $data[$i][kurztext_de] = $tmp->GetKurztext_De();
      $data[$i][kurztext_en] = $tmp->GetKurztext_En();
      $data[$i][beschreibung_de] = $tmp->GetBeschreibung_De();
      $data[$i][beschreibung_en] = $tmp->GetBeschreibung_En();
      $data[$i][uebersicht_de] = $tmp->GetUebersicht_De();
      $data[$i][uebersicht_en] = $tmp->GetUebersicht_En();
      $data[$i][links_de] = $tmp->GetLinks_De();
      $data[$i][links_en] = $tmp->GetLinks_En();
      $data[$i][startseite_de] = $tmp->GetStartseite_De();
      $data[$i][startseite_en] = $tmp->GetStartseite_En();
      $data[$i][neu] = $tmp->GetNeu();
      $data[$i][restmenge] = $tmp->GetRestmenge();
      $data[$i][topseller] = $tmp->GetTopseller();
      $data[$i][startseite] = $tmp->GetStartseite();
      $data[$i][standardbild] = $tmp->GetStandardbild();
      $data[$i][herstellerlink] = $tmp->GetHerstellerlink();
      $data[$i][lieferzeit] = $tmp->GetLieferzeit();
      $data[$i][lieferzeitmanuell] = $tmp->GetLieferzeitmanuell();
      $data[$i][gewicht] = $tmp->GetGewicht();
      $data[$i][wichtig] = $tmp->GetWichtig();
      $data[$i][porto] = $tmp->GetPorto();
//      $data[$i][lagerartikel] = $tmp->GetLagerartikel();
      $data[$i][gesperrt] = $tmp->GetGesperrt();
      $data[$i][sperrgrund] = $tmp->GetSperrgrund();
      $data[$i][gueltigbis] = $tmp->GetGueltigbis();
      $data[$i][umsatzsteuer] = $tmp->GetUmsatzsteuer();
      if($data[$i][umsatzsteuer]!="ermaessigt") $data[$i][umsatzsteuer]="normal";
      $data[$i][ausverkauft] = $tmp->GetAusverkauft();
      $data[$i][variante] = $tmp->GetVariante();
      $data[$i][variante_von] = $tmp->GetVariante_Von();
      $data[$i][pseudopreis] = $tmp->GetPseudopreis();
 //     $data[$i][lieferant] = $tmp->GetLieferant();


			$data[$i][anzahl_bilder] = $this->app->DB->Select("SELECT COUNT(datei) FROM  datei_stichwoerter WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='$artikel'");

			if($tmp->GetJuststueckliste())
      	$lagernd = $this->app->erp->ArtikelAnzahlLagerStueckliste($artikel);
			else
      	$lagernd = $this->app->erp->ArtikelAnzahlLager($artikel);

 			$reserviert = $this->app->erp->ArtikelAnzahlReserviert($artikel);

    	$offen = $this->app->erp->ArtikelAnzahlOffene($artikel);

    	if($offen > $reserviert) $reserviert = $offen;

			$data[$i][anzahl_lager] = $lagernd - $reserviert;
			if($data[$i][anzahl_lager]<0) $data[$i][anzahl_lager] = 0;

      $projekt = $this->app->DB->Select("SELECT projekt FROM shopexport WHERE id='$id' LIMIT 1");

      $preis = $this->app->DB->Select("SELECT MAX(preis) FROM verkaufspreise WHERE 
	artikel='$artikel' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND ab_menge = 1 AND (objekt='Standard' OR objekt='') AND (adresse='0' OR adresse='') AND geloescht=0");



      $data[$i][preis] = $preis;

      $data[$i][checksum] = $tmp->GetChecksum();
      if($preis == "")
			$data[$i][artikel]="ignore" ;
    }


		$lagerexport = $this->app->DB->Select("SELECT lagerexport FROM shopexport WHERE id='$id' LIMIT 1");
		$artikelexport = $this->app->DB->Select("SELECT artikelexport FROM shopexport WHERE id='$id' LIMIT 1");

		if($lagerexport=="1")
    	$result =  $this->RemoteCommand($id,"sendlistlager",$data);

		if($artikelexport=="1")
    	$result = $this->RemoteCommand($id,"sendlist",$data);

		return $result;
  }

  function RemoteUpdateAuftrag($id,$auftrag)
  {
		$status = $this->app->DB->Select("SELECT status FROM auftrag WHERE id='$auftrag' LIMIT 1");
		$shopextid = $this->app->DB->Select("SELECT shopextid FROM auftrag WHERE id='$auftrag' LIMIT 1");
		$shopextstatus = $this->app->DB->Select("SELECT shopextstatus FROM auftrag WHERE id='$auftrag' LIMIT 1");

		$lieferscheinid = $this->app->DB->Select("SELECT id FROM lieferschein WHERE auftragid='$auftrag' LIMIT 1");
		$tracking = $this->app->DB->Select("SELECT tracking FROM versand WHERE lieferschein='$lieferscheinid' LIMIT 1");

		if($shopextstatus=="abgeschlossen")
			return;

    $data[auftrag] = $shopextid;

		//versand
		if($status=="abgeschlossen")
		{
			$data[versand]="1";
			//tracking
			if($tracking!="")
			$data[tracking]=$tracking;
			$shopextstatus="versendet";
		}

		//zahlung
		if($this->app->erp->AuftragSaldo($auftrag)==0)
		{
			$data[zahlung]="1";
			$shopextstatus="bezahlt";
		}

		if($data[versand]=="1" || $data[zahlung]=="1")
    	$this->RemoteCommand($id,"updateauftrag",$data);

		if($data[zahlung]=="1" && $data[versand]=="1")
			$shopextstatus="abgeschlossen";

		$this->app->DB->Update("UPDATE auftrag SET shopextstatus='$shopextstatus' WHERE id='$auftrag' LIMIT 1");
  }


  function RemoteDeleteAuftrag($id,$auftrag)
  {

    $data[auftrag] = $auftrag;
    $this->RemoteCommand($id,"deleteauftrag",$data);
  }

  function RemoteDeleteFile($id,$fid)
  {

    $inhalt = $this->app->erp->GetDatei($fid);
    $titel = $this->app->DB->Select("SELECT titel FROM datei WHERE id='$fid' LIMIT 1");
    $beschreibung = $this->app->DB->Select("SELECT beschreibung FROM datei WHERE id='$fid' LIMIT 1");

    $data[datei] = $fid;
    $data[checksum] = md5($inhalt);
    $data[checksum] = md5($inhalt.$titel.$beschreibung);
    $this->RemoteCommand($id,"deletefile",$data);
  }


	// artikel id, shop id
  function RemoteUpdateFilesArtikel($artikel_id,$id)
  {
      $dateien = $this->app->DB->SelectArr("SELECT DISTINCT ds.datei FROM datei_stichwoerter ds, datei d, artikel a WHERE d.id=ds.datei AND (ds.subjekt='Shopbild' OR ds.subjekt='Gruppenbild') AND ((ds.objekt='Artikel' AND ds.parameter=a.id)  OR (ds.objekt='Kampangen' AND ds.parameter='$id')) AND d.firma='".$this->app->User->GetFirma()."' AND a.shop='$id' AND a.id='$artikel_id'");

      $tmp = $this->app->remote->RemoteGetFileListArticle($id,$artikel_id);

      foreach($tmp as $row)
        $checkarray[$row[datei]] = $row[checksum];

      $datei_updates = 0;
      for($i=0;$i<count($dateien);$i++)
      {
  			$fid = $dateien[$i][datei];
  			$geloescht = $this->app->DB->Select("SELECT geloescht FROM datei WHERE id='$fid' LIMIT 1");

					if(($checkarray[$fid]!=md5($this->app->erp->GetDatei($fid))) && $geloescht==0)
					{
						$datei_updates++;
						$this->app->remote->RemoteSendFile($id,$fid);
						$this->app->remote->RemoteAddFileSubject($id,$fid);
						$checkarray[$fid]="update";
					} else
					{   
						if($geloescht)
						{ 
							$this->app->remote->RemoteDeleteFile($id,$fid);
							$checkarray[$fid]="delete";
						}
					}
					$checkarray[$fid]="mark";
      }
      if(count($checkarray)>0)
      {
					$delete=0;
					foreach($checkarray as $key=>$value)
					{   
						if($checkarray[$key]!="mark" && $checkarray[$key]!="delete" && $checkarray[$key]!="update")
						{
							//echo "loesche $key<br>";
							$this->app->remote->RemoteDeleteFile($id,$key);
							$delete++;
						}
					}   
      }


  }



  function RemoteSendFile($id,$fid)
  {
    // sende stichwoerter
    $geloescht = $this->app->DB->Select("SELECT geloescht FROM datei WHERE id='$fid' LIMIT 1");
    $titel = $this->app->DB->Select("SELECT titel FROM datei WHERE id='$fid' LIMIT 1");
    $beschreibung = $this->app->DB->Select("SELECT beschreibung FROM datei WHERE id='$fid' LIMIT 1");

    $inhalt = $this->app->erp->GetDatei($fid);
    $data[datei] = $fid;
    $data[titel] = $titel;
    $data[beschreibung] =$beschreibung; 
    $data[inhalt] = base64_encode($inhalt);
    $data[checksum] = md5($inhalt.$titel.$beschreibung);
    if(!$geloescht)
      $this->RemoteCommand($id,"sendfile",$data);
  }

  function RemoteAddFileSubject($id,$fid)
  {
    // sende stichwoerter
    $woerter = $this->app->DB->SelectArr("SELECT subjekt,parameter FROM datei_stichwoerter WHERE (objekt='Artikel' OR objekt='Kampangen') AND datei='$fid'");
    for($i=0;$i<count($woerter);$i++)
    {
      $geloescht = $this->app->DB->Select("SELECT geloescht FROM datei WHERE id='$fid' LIMIT 1");
      $stichwort[subjekt] = $woerter[$i][subjekt];
      $stichwort[artikel] = $woerter[$i][parameter];
      $stichwort[datei] =  $fid;
      if(!$geloescht)
				$this->RemoteCommand($id,"addfilesubjekt",$stichwort);
    }
  }


  function RemoteDeleteArticle($id,$artikel)
  {
    return $this->RemoteCommand($id,"deletearticle",$artikel);
  }


  function RemoteCommand($id,$action,$data="")
  {
    $token = $this->app->DB->Select("SELECT token FROM shopexport WHERE id='$id' LIMIT 1");
    $url = $this->app->DB->Select("SELECT url FROM shopexport WHERE id='$id' LIMIT 1");
    $z = $this->app->DB->Select("SELECT passwort FROM shopexport WHERE id='$id' LIMIT 1");
    $tmp = parse_url($url);

    $aes = new AES($z);
    $token = base64_encode($aes->encrypt(serialize($token)));
    $client = new HttpClient($tmp[host]);
    $geturl = $tmp[path].'index.php?module=import&action='.$action.'&challenge='.$challenge;
    $post_data['token'] = $token;
    //$post_data['data'] = base64_encode($aes->encrypt(serialize($data)));
    $post_data['data'] = base64_encode(serialize($data));

    if(!$client->post($geturl,$post_data))
    {
      die('An error occurred: '.$client->getError());
    }

    return unserialize(base64_decode($client->getContent()));
  }



  function RemoteCommandAES($id,$action,$data="")
  {
    $token = $this->app->DB->Select("SELECT token FROM shopexport WHERE id='$id' LIMIT 1");
    $url = $this->app->DB->Select("SELECT url FROM shopexport WHERE id='$id' LIMIT 1");
    $z = $this->app->DB->Select("SELECT passwort FROM shopexport WHERE id='$id' LIMIT 1");

    $tmp = parse_url($url);

    $aes = new AES($z);
    $token = base64_encode($aes->encrypt(serialize($token)));

    $client = new HttpClient($tmp[host]);
    $geturl = $tmp[path].'index.php?module=import&action='.$action.'&challenge='.$challenge;

    $post_data['token'] = $token;
    $post_data['data'] = base64_encode($aes->encrypt(serialize($data)));

    if(!$client->post($geturl,$post_data))
    {
      die('An error occurred: '.$client->getError());
    }
    return unserialize($aes->decrypt(base64_decode($client->getContent())));
  }



}
?>
