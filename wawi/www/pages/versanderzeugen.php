<?php

class Versanderzeugen 
{

  function Versanderzeugen(&$app)
  {
    $this->app=&$app; 
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("main","VersanderzeugenMain");
    $this->app->ActionHandler("list","VersanderzeugenList");
    $this->app->ActionHandler("offene","VersanderzeugenOffene");
    $this->app->ActionHandler("gelesen","VersanderzeugenGelesen");
    $this->app->ActionHandler("einzel","VersanderzeugenEinzel");
    $this->app->ActionHandler("delete","VersanderzeugenDelete");
    $this->app->ActionHandler("schnelleingabe","VersanderzeugenSchnelleingabe");
    $this->app->ActionHandler("frankieren","VersanderzeugenFrankieren");
    $this->app->ActionHandler("korrektur","VersanderzeugenKorrektur");
  
    $this->app->DefaultActionHandler("list");

    
		$this->app->Tpl->Set(KURZUEBERSCHRIFT,"Versand");
    $this->app->ActionHandlerListen($app);
  }

  function VersanderzeugenGelesen()
  {
    $id = $this->app->Secure->GetGET("id");

    $this->app->DB->Update("UPDATE versand SET gelesen=1 WHERE id='$id' LIMIT 1");

    header("Location: index.php?module=versanderzeugen&action=einzel&id=$id");
    exit;
  }

  function VersanderzeugenKorrektur()
  {
    $id = $this->app->Secure->GetGET("id");

    $this->app->DB->Update("UPDATE versand SET paketmarkegedruckt=0 WHERE id='$id' LIMIT 1");

    header("Location: index.php?module=versanderzeugen&action=einzel&id=$id");
    exit;
  }



  function VersanderzeugenDelete()
  {
    $id = $this->app->Secure->GetGET("id");

		$lieferschein = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");

		if($lieferschein > 0)
			$this->app->DB->Delete("DELETE FROM lager_reserviert WHERE objekt='lieferschein' AND parameter='$lieferschein'");

    $this->app->DB->Delete("DELETE FROM versand WHERE id='$id' LIMIT 1");

    header("Location: index.php?module=versanderzeugen&action=offene");
    exit;
  }


	function VersandMenu()
	{
    $this->app->erp->MenuEintrag("index.php?module=versanderzeugen&action=offene","Zur&uuml;ck zur &Uuml;bersicht");

	}

  function VersanderzeugenEinzel()
  {
    $id = $this->app->Secure->GetGET("id");

		$this->VersandMenu();


    $name = $this->app->DB->Select("SELECT r.name FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(ADRESSE,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT r.ansprechpartner FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(ADRESSE,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT r.abteilung FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(ADRESSE,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT r.unterabteilung FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(ADRESSE,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT r.adresszusatz FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(ADRESSE,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT r.strasse FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(ADRESSE,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT CONCAT(r.land,'-',r.plz,' ',r.ort) FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(ADRESSE,$name."&nbsp;<br>");

    $name = $this->app->DB->Select("SELECT l.name FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(LIEFERUNG,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT l.ansprechpartner FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(LIEFERUNG,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT l.abteilung FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(LIEFERUNG,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT l.unterabteilung FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(LIEFERUNG,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT l.adresszusatz FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(LIEFERUNG,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT l.strasse FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(LIEFERUNG,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT CONCAT(l.land,'-',l.plz,' ',l.ort) FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(LIEFERUNG,$name."&nbsp;<br>");


    $name = $this->app->DB->Select("SELECT r.zahlungsweise FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    $versandart = $this->app->DB->Select("SELECT l.versandart FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");

    //if($name=="nachnahme") $this->app->Tpl->Add(INFORMATION,"<div class=\"error\">Nachnahme Lieferung!</div><script type=\"text/javascript\">alert('ACHTUNG NACHNAHME!');</script>");
    if($name=="nachnahme") $this->app->Tpl->Add(INFORMATION,"<div class=\"error\">Nachnahme Lieferung!</div>");
    if($name=="bar") $this->app->Tpl->Add(INFORMATION,"<div class=\"error\">Achtung Barzahlung!</div>");
    if($versandart=="selbstabholer") $this->app->Tpl->Add(INFORMATION,"<div class=\"error\">Achtung Selbstaboler!</div>");
    if($versandart=="packstation") $this->app->Tpl->Add(INFORMATION,"<div class=\"warnung\">Achtung Packstation!</div>");

    $this->app->Tpl->Parse(TAB1,"versandeinzel.tpl");

    $artikel = $this->app->Secure->GetPOST("artikel");
    $menge = $this->app->Secure->GetPOST("menge");
    $posold= $this->app->Secure->GetPOST("posold");
    $seriennummer  = $this->app->Secure->GetPOST("seriennummer");
    $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
    $rechnung = $this->app->DB->Select("SELECT rechnung FROM versand WHERE id='$id' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
    $versand_gelesen = $this->app->DB->Select("SELECT gelesen FROM versand WHERE id='$id' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
    $auftrag= $this->app->DB->Select("SELECT auftrag FROM lieferschein WHERE id='$lieferschein' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
    $projekt= $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$lieferschein' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
    $land = $this->app->DB->Select("SELECT land  FROM lieferschein WHERE id='$lieferschein' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
    $auftragid= $this->app->DB->Select("SELECT id FROM auftrag WHERE belegnr='$auftrag' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
    $auftragsart= $this->app->DB->Select("SELECT art FROM auftrag WHERE belegnr='$auftrag' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

		$freitext_lieferschein = $this->app->DB->Select("SELECT freitext FROM lieferschein WHERE id='$lieferschein' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
		$internebemerkung = $this->app->DB->Select("SELECT internebemerkung FROM lieferschein WHERE id='$lieferschein' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
//		$freitext_lieferschein = nl2br($freitext_lieferschein);
		if(($freitext_lieferschein!="" || $internebemerkung!="") && $versand_gelesen!=1 && $this->app->erp->Firmendaten("versand_gelesen"))
		$this->app->Tpl->Add(TAB1,"<div style=\"color:white;font-size:18pt; font-weight:bold; position:absolute; 
			background-color:red; top:0px;padding:10px; width:1078px; height:350px; overflow:scroll;\"><table width=100%><tr><td><u>Interne Bemerkung:</u></td><td align=right>
			<input type=\"button\" value=\"als gelesen markieren\" onclick=\"window.location.href='index.php?module=versanderzeugen&action=gelesen&id=$id'\" style=\"font-size:10pt;\"></td></tr></table><br>$freitext_lieferschein<br>$internebemerkung</div>");

		// fremde nummern erlauben wenn es im projekt aktiv ist

		if($projekt > 0)
		{
			$eanherstellerscan = $this->app->DB->Select("SELECT eanherstellerscan FROM projekt WHERE id='$projekt'");
		} else $eanherstellerscan=0;

		if($eanherstellerscan=="1")
		{
 			$artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$artikel' AND projekt='$projekt' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
			if($artikelid <=0) 
				$artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE projekt='$projekt' 
					AND ean='$artikel' AND ean!='' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
			if($artikelid <=0) 
				$artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE projekt='$projekt' 
					AND herstellernummer='$artikel' AND herstellernummer!='' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
		} 
		else
		{
    	$artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$artikel' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
			if($artikelid <=0) $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='$artikel' AND ean!='' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
		}
    $seriennummern= $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id='$artikelid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
    $lagerartikel = $this->app->DB->Select("SELECT lagerartikel FROM artikel WHERE id='$artikelid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

    //wenn seriennummer dann update und weiter mit naechsten artikel
    if($seriennummer!="")
    {
					//oder tabelle seriennummern
					$tmpseriennummer = $this->app->DB->Select("SELECT seriennummer FROM lieferschein_position WHERE lieferschein='$lieferschein' AND artikel='$artikelid' AND geliefert<=menge LIMIT 1");
					if($tmpseriennummer!="")$tmpseriennummer = $tmpseriennummer .' '.$seriennummer;	 else $tmpseriennummer=$seriennummer;
					$this->app->DB->Update("UPDATE lieferschein_position SET seriennummer='$tmpseriennummer' WHERE id='$posold' AND lieferschein='$lieferschein' AND artikel='$artikelid'");

					$adresse = $this->app->DB->Select("SELECT adresse FROM versand WHERE id='$id' LIMIT 1");
					$name_de= $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikelid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
					$this->app->DB->Insert("INSERT INTO seriennummern (id,seriennummer,adresse,artikel,beschreibung,lieferung,lieferschein,bearbeiter,logdatei)
						VALUES ('','$seriennummer','$adresse','$artikelid','$name_de',DATE_FORMAT(NOW(),'%Y-%m-%d'),'$lieferschein','".$this->app->User->GetName()."',NOW())");
    } else {
    // wenn artikel pass in lieferschein und auftrag erhoehen und aus zwischenlager nehmen
    $pos = $this->app->DB->Select("SELECT id FROM lieferschein_position WHERE lieferschein='$lieferschein' AND artikel='$artikelid' AND geliefert<menge LIMIT 1");
    $posauftrag = $this->app->DB->Select("SELECT id FROM auftrag_position WHERE auftrag='$auftragid' AND artikel='$artikelid' AND geliefert_menge<menge LIMIT 1");

		if(!is_numeric($menge))
			$menge = 1;

		$restmenge =  $this->app->DB->Select("SELECT SUM(menge-geliefert) FROM lieferschein_position WHERE lieferschein='$lieferschein' AND artikel='$artikelid' LIMIT 1");

    if($pos > 0 && $restmenge >= $menge)
    {
      // immer einzeln scannen
      //artikel kann wieder 1 - n mal bei 1 - m positionen vorkommen!!!
      $tmpgeliefert  = $this->app->DB->Select("SELECT geliefert FROM lieferschein_position WHERE lieferschein='$lieferschein' AND artikel='$artikelid' AND geliefert<menge LIMIT 1");
      $tmpgeliefert = $tmpgeliefert + $menge;
      $this->app->DB->Update("UPDATE lieferschein_position SET geliefert='$tmpgeliefert' WHERE id='$pos' AND lieferschein='$lieferschein' AND artikel='$artikelid' LIMIT 1");


      $tmpgeliefertauftrag  = $this->app->DB->Select("SELECT geliefert_menge FROM auftrag_position WHERE auftrag='$auftrag' AND artikel='$artikelid' AND geliefert_menge<menge LIMIT 1");
      $tmpgeliefertauftrag = $tmpgeliefertauftrag + $menge;
      $this->app->DB->Update("UPDATE auftrag_position SET geliefert_menge='$tmpgeliefertauftrag' WHERE id='$posauftrag' AND auftrag='$auftragid' AND artikel='$artikelid' LIMIT 1");

      $this->app->DB->Update("UPDATE auftrag_position SET geliefert='1' WHERE auftrag='$auftragid' AND artikel='$artikelid' AND geliefert_menge=menge AND geliefert!='1' LIMIT 1");
      //echo "artikel kommt in lieferung vor";

      //artikel aus zwischenlager nehmen!!!!
      $zwischenlagerid = $this->app->DB->Select("SELECT id FROM zwischenlager WHERE objekt='lieferung' AND artikel='$artikelid' AND menge > 0 LIMIT 1");
      $zwischenlagermenge = $this->app->DB->Select("SELECT menge FROM zwischenlager WHERE id='$zwischenlagerid' LIMIT 1");
      $zwischenlagermenge = $zwischenlagermenge -$menge;
      $this->app->DB->Update("UPDATE zwischenlager SET menge='$zwischenlagermenge' WHERE id='$zwischenlagerid' LIMIT 1");
      $this->app->DB->Delete("DELETE FROM zwischenlager WHERE menge<='0'");

    } 
    else { $artikelfalsch=1; }


    } 
    $summemenge = $this->app->DB->Select("SELECT SUM(lp.menge) FROM lieferschein_position lp, artikel a 
	WHERE a.id=lp.artikel AND lp.lieferschein='$lieferschein' AND (a.lagerartikel=1 OR a.porto=0) AND a.juststueckliste=0");
    $summegeliefert = $this->app->DB->Select("SELECT SUM(lp.geliefert) FROM lieferschein_position lp, artikel a 
	WHERE a.id=lp.artikel AND lp.lieferschein='$lieferschein' AND (a.lagerartikel=1 OR a.porto=0) AND a.juststueckliste=0");
    if($summegeliefert>=$summemenge)
      $komplett = 1;
 
    
    if(($seriennummern=="eigene" || $seriennummern=="vomprodukt") && $artikel!="" && $seriennummer=="")
    {
      $this->app->Tpl->Add(TAB1,"<form action=\"\" method=\"post\">
	<div class=\"warning\">Bitte Seriennummer eingeben und auf weiter klicken: <input type=\"text\" size=\"30\" name=\"seriennummer\" id=\"erstes\"><input type=\"hidden\" name=\"artikel\" value=\"$artikel\">
	  <input type=\"hidden\" name=\"posold\" value=\"$pos\">&nbsp;<input type=\"submit\" value=\"weiter\"></div></form>");
	$this->app->Tpl->Add(TAB1,"<script type=\"text/javascript\">document.getElementById(\"erstes\").focus(); </script>");
    } 
    else if($artikelfalsch && $artikel!="" && $posold=="")
    {
      $this->app->Tpl->Add(TAB1,"<div class=\"error\" align=\"center\">Artikel bzw. angegebene Menge ist in Lieferung nicht enthalten, bzw. befindet sich der Artikel bereits in der Lieferung!&nbsp;
	<input type=\"button\" onclick=\"window.location.href='index.php?module=versanderzeugen&action=einzel&id=$id';\" value=\"anderen Artikel scannen\"></div>");
    }
    else 
    {
      if($komplett)
      {
				$adresse = $this->app->DB->Select("SELECT adresse FROM versand WHERE id='$id' LIMIT 1");
				$projekt = $this->app->DB->Select("SELECT projekt FROM versand WHERE id='$id' LIMIT 1");
				$papieregedruckt = $this->app->DB->Select("SELECT papieregedruckt FROM versand WHERE id='$id' LIMIT 1");
				$weitererlieferschein = $this->app->DB->Select("SELECT weitererlieferschein FROM versand WHERE id='$id' LIMIT 1");

				$versandart = $this->app->DB->Select("SELECT l.versandart FROM versand v LEFT JOIN lieferschein l ON v.lieferschein=l.id WHERE v.id='$id' LIMIT 1");
				//  hier rechnung drucken
				//  hier lieferschein drucken

				$druckercode = $this->app->DB->Select("SELECT druckerlogistikstufe2 FROM projekt WHERE id='$projekt' LIMIT 1");

				if($druckercode <=0)
					$druckercode = $this->app->erp->Firmendaten("standardversanddrucker"); // standard = 3 // 2 buchhaltung  // 1 empfang


	// wenn papiere bereits bedruckt sofort weiter
	if($papieregedruckt >=1)
	{
		header("Location: index.php?module=versanderzeugen&action=frankieren&id=$id&land=$land");
		exit;
	}
  $this->app->DB->Update("UPDATE versand SET papieregedruckt='1' WHERE id='$id' LIMIT 1");
	if($lieferschein >0)
	{
	$Brief = new LieferscheinPDF($this->app,$projekt);
	if($weitererlieferschein=="1")
		$Brief->GetLieferschein($lieferschein,"-Kopie");
	else
		$Brief->GetLieferschein($lieferschein);

	$tmpfile = $Brief->displayTMP();

	if($this->app->erp->Projektdaten($projekt,"autodrucklieferschein")=="1")	
	{
		$mengedruck = $this->app->erp->Projektdaten($projekt,"autodrucklieferscheinmenge");
		if($mengedruck <=0) $mengedruck=1;
		for($mengedruck;$mengedruck > 0;$mengedruck--)
			$this->app->printer->Drucken($druckercode,$tmpfile);
	}

  $fileid_lieferschein = $this->app->erp->CreateDatei($Brief->filename,"lieferschein","","",$tmpfile,$this->app->User->GetName());
  $this->app->erp->AddDateiStichwort($fileid_lieferschein,"lieferschein","lieferschein",$lieferschein,$without_log=false);

	unlink($tmpfile);
	}

	if($versandart=="selbstabholer")
	{

	  if($lieferschein >0)
	  {
	  $Brief = new LieferscheinPDF($this->app,$projekt);
	  $Brief->GetLieferschein($lieferschein,"-Doppel","\r\n\r\nUnterschrift Ware erhalten:\r\n\r\n\r\n\r\n________________________________________");
	  $tmpfile = $Brief->displayTMP();

		if($this->app->erp->Projektdaten($projekt,"autodrucklieferschein")=="1")	
	  	$this->app->printer->Drucken($druckercode,$tmpfile);
	  unlink($tmpfile);

	  }
		// wenn zahlung bar dann rechnungsdoppel
		$zahlungsweiserechnung = $this->app->DB->Select("SELECT zahlungsweise FROM rechnung WHERE id='$rechnung' LIMIT 1");

	  if($rechnung >0 && ($zahlungsweiserechnung=="bar"))
	  {
	  	$Brief = new RechnungPDF($this->app,$projekt);
	  	$Brief->GetRechnung($rechnung,"",1);
	  	$tmpfile = $Brief->displayTMP();

			if($this->app->erp->Projektdaten($projekt,"autodruckrechnung")=="1" && $weitererlieferschein!="1")	
	  		$this->app->printer->Drucken($druckercode,$tmpfile);
	  	unlink($tmpfile);
	  }

		// wenn zahlung bar dann rechnungsdoppel
	}


	  if($lieferschein >0)
	  {
	// Lieferschein auf versendet stellen!!!!
		//TODO ARCHIVIEREN

	if($this->app->erp->Projektdaten($projekt,"autodrucklieferschein")=="1")	
	{
	$this->app->DB->Update("UPDATE lieferschein SET status='versendet',versendet='1',schreibschutz='1' WHERE id='$lieferschein' LIMIT 1");
	$this->app->DB->Insert("INSERT INTO dokumente_send 
	  (id,dokument,zeit,bearbeiter,adresse,parameter,art,betreff,text,projekt,ansprechpartner,dateiid) VALUES ('','lieferschein',NOW(),'".$this->app->User->GetName()."',
	  '$adresse','$lieferschein','versand','Mitgesendet bei Lieferung','','$projekt','','$fileid_lieferschein')");
      $this->app->erp->LieferscheinProtokoll($lieferschein,"Lieferschein versendet (Auto-Versand)");
	}
	  }
	//if($auftragsart=="standardauftrag" || $auftragsart==""||$auftragsart=="rma")
	if($rechnung >= 0 && is_numeric($rechnung) && $auftragsart!="lieferung")//$auftragsart=="standardauftrag" || $auftragsart==""||$auftragsart=="rma")
	{
	  //demodruck
	  if($rechnung>0)
	  {
	  $Brief = new RechnungPDF($this->app,$projekt);
	  $Brief->GetRechnung($rechnung);
	  $tmpfile = $Brief->displayTMP();

		if($this->app->erp->Projektdaten($projekt,"autodruckrechnung")=="1" && $weitererlieferschein!="1")	
		{
			$mengedruck = $this->app->erp->Projektdaten($projekt,"autodruckrechnungmenge");
			if($mengedruck <=0) $mengedruck=1;
			for($mengedruck;$mengedruck > 0;$mengedruck--)
	  		$this->app->printer->Drucken($druckercode,$tmpfile);
 
			$fileid_rechung = $this->app->erp->CreateDatei($Brief->filename,"rechung","","",$tmpfile,$this->app->User->GetName());
  		$this->app->erp->AddDateiStichwort($fileid_rechung,"rechnung","rechnung",$rechnung,$without_log=false);
 
	  	unlink($tmpfile);

			// Rechnung auf versendet stellen!!!!
			//TODO ARCHIVIEREN
	  	$this->app->DB->Insert("INSERT INTO dokumente_send 
	  		(id,dokument,zeit,bearbeiter,adresse,parameter,art,betreff,text,projekt,ansprechpartner,dateiid) VALUES ('','rechnung',NOW(),'".$this->app->User->GetName()."',
	  		'$adresse','$rechnung','versand','Mitgesendet bei Lieferung','','$projekt','','$fileid_rechung')");

	  	$this->app->DB->Update("UPDATE rechnung SET status='versendet', versendet='1',schreibschutz='1' WHERE id='$rechnung' LIMIT 1");
    	$this->app->erp->RechnungProtokoll($rechnung,"Rechnung versendet (Auto-Versand)");
		}
  }
		// Druck Auftrag Anhang wenn aktiv
		if($this->app->erp->Projektdaten($projekt,"autodruckanhang")=="1")	
		{

			// alle anhaenge drucken! wo auftrag datei anhang
			$tmpanhang = $this->app->erp->GetDateiSubjektObjekt("anhang","Auftrag",$auftragid);
			for($i=0;$i<count($tmpanhang);$i++)
			  $this->app->printer->Drucken($druckercode,$tmpanhang[$i]);

			$tmpanhang ="";
		}

		// versende rechnung, lieferschein auftrag anhang wenn es im projekt aktiv ist
		$tmp = array();
		if($this->app->erp->Projektdaten($projekt,"automailanhang")=="1")
    {
			//Anhang
			//TODO alle Auftrag Anhang als Datei nach tmp kopieren und in tmp speichern	
      $tmp = $this->app->erp->GetDateiSubjektObjektDateiname("anhang","Auftrag",$auftragid,"AUFTRAG_".$auftrag."_");
    }

		if($this->app->erp->Projektdaten($projekt,"automaillieferschein")=="1")
    {
			//Lieferschein
			$Brief = new LieferscheinPDF($this->app,$projekt);
			if($weitererlieferschein=="1")
				$Brief->GetLieferschein($lieferschein,"-Kopie");
			else
				$Brief->GetLieferschein($lieferschein);

			$tmp[] = $Brief->displayTMP();
    }

		if($this->app->erp->Projektdaten($projekt,"automailrechnung")=="1" && $weitererlieferschein!="1")
    {
			//Rechnung
  		$Brief = new RechnungPDF($this->app,$projekt);
	  	$Brief->GetRechnung($rechnung);
	  	$tmprechnung = $Brief->displayTMP();
	  	$tmp[] = $tmprechnung;
	
			$fileid_rechung = $this->app->erp->CreateDatei($Brief->filename,"rechung","","",$tmprechnung,$this->app->User->GetName());
  		$this->app->erp->AddDateiStichwort($fileid_rechung,"rechnung","rechnung",$rechnung,$without_log=false);
 
			// Rechnung auf versendet stellen!!!!
			//TODO ARCHIVIEREN
	  	$this->app->DB->Insert("INSERT INTO dokumente_send 
	  		(id,dokument,zeit,bearbeiter,adresse,parameter,art,betreff,text,projekt,ansprechpartner,dateiid) VALUES ('','rechnung',NOW(),'".$this->app->User->GetName()."',
	  		'$adresse','$rechnung','versand','Autoversand per Mail bei Versand','','$projekt','','$fileid_rechung')");

	  	$this->app->DB->Update("UPDATE rechnung SET status='versendet', versendet='1',schreibschutz='1' WHERE id='$rechnung' LIMIT 1");
    	$this->app->erp->RechnungProtokoll($rechnung,"Rechnung versendet (Auto-Versand per Mail)");
    }
		
		//TODO
		if(count($tmp) > 0)
		{
    	$this->app->DB->Update("UPDATE versand SET papieregedruckt='1' WHERE id='$id' LIMIT 1");
			//MailSenden
			$text = $this->app->erp->GetGeschaeftsBriefText('VersandMailDokumente','deusch',$projekt);
      $betreff = $this->app->erp->GetGeschaeftsBriefBetreff('VersandMailDokumente','deutsch',$projekt);

			$to = $this->app->DB->Select("SELECT email FROM auftrag WHERE id='$auftragid' LIMIT 1");
      $to_name = $this->app->DB->Select("SELECT name FROM auftrag WHERE id='$auftragid' LIMIT 1");

			$this->app->erp->MailSend($this->app->erp->GetFirmaMail(),$this->app->erp->GetFirmaName(),$to,$to_name,$betreff,$text,$tmp,$projekt);
		}

		// alle tmp anhaenge wieder loeschen
		for($i=0;$i<count($tmp);$i++)
		{
			unlink($tmp[$i]);
		}
	}

	header("Location: index.php?module=versanderzeugen&action=frankieren&id=$id&land=$land");
	exit;

      } else {

	// TODO wenn es lagerartikel ist barcode scannen
	// sonst menge bestätigen
	//if($lagerartikel==1)
	if(1)
	{
	//if($this->app->User->GetType("admin"))
	if(1)
	{
	$this->app->Tpl->Add(TAB1,"<form action=\"\" method=\"post\" name=\"erfassen\" id=\"erfassen\">
	  <div class=\"error\" align=\"center\">Bitte Artikel scannen:<br><br>
			Menge:&nbsp;<input type=\"text\" name=\"menge\" id=\"menge\" value=\"1\" size=\"5\">
			<input type=\"text\" name=\"artikel\" id=\"erstes\" size=\"30\">&nbsp;<input type=\"submit\" value=\"Artikel erfassen\" name=\"artikelerfassen\"></div></form>
	
	<script>
function BuchenJavascript(artikel,menge)
{
	document.getElementById(\"erstes\").value=artikel;
	document.getElementById(\"menge\").value=menge;
	document.getElementById(\"erfassen\").submit();
}
</script>

");
	} else {
	$this->app->Tpl->Add(TAB1,"<form action=\"\" method=\"post\">
	  <div class=\"error\" align=\"center\">Bitte Lager-Artikel scannen: <input type=\"text\" name=\"artikel\" id=\"erstes\" size=\"30\">&nbsp;<input type=\"submit\" value=\"Artikel erfassen\" name=\"artikelerfassen\"></div></form>");
	}
	} else 
	{
	$this->app->Tpl->Add(TAB1,"<form action=\"\" method=\"post\">
	  <div class=\"error\" align=\"center\">Bitte Artikel scannen: <input type=\"text\" name=\"artikel\" id=\"erstes\" size=\"30\">&nbsp;<input type=\"submit\" value=\"Artikel erfassen\" name=\"artikelerfassen\"></div></form>");
	}
	$this->app->Tpl->Add(TAB1,"<script type=\"text/javascript\">document.getElementById(\"erstes\").focus(); </script>");
      }
    }

    $namekunde = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' LIMIT 1");
    $this->app->Tpl->Set(TABTEXT,"Versand Kunde: ".$namekunde);


      //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
    $table = new EasyTable($this->app);


		if($this->app->erp->RechteVorhanden("versanderzeugen","schnelleingabe"))
		{
    $table->Query("SELECT if((lp.geliefert < lp.menge) AND a.juststueckliste!=1,CONCAT('<b>',lp.bezeichnung,'</b>'),lp.bezeichnung) as bezeichnung, 
				lp.nummer, lp.menge as soll, lp.geliefert as ist, 
			CONCAT('<a href=# onclick=BuchenJavascript(\"',a.nummer,'\",\"',(lp.menge-lp.geliefert),'\")>
						<img src=./themes/new/images/forward.png></a>') as '' 
      FROM versand v LEFT JOIN lieferschein_position lp ON  v.lieferschein=lp.lieferschein LEFT JOIN artikel a ON a.id=lp.artikel WHERE v.id='$id'");
    $table->DisplayNew(INHALT,"OK","noAction");
		} else {
 			$table->Query("SELECT if((lp.geliefert < lp.menge) AND a.juststueckliste!=1,CONCAT('<b>',lp.bezeichnung,'</b>'),lp.bezeichnung) as bezeichnung, 
				lp.nummer, lp.menge as soll, lp.geliefert as ist
      FROM versand v LEFT JOIN lieferschein_position lp ON  v.lieferschein=lp.lieferschein LEFT JOIN artikel a ON a.id=lp.artikel WHERE v.id='$id'");
    	$table->DisplayNew(INHALT,"Ist","noAction");
		}
    $this->app->Tpl->Parse(TAB1,"rahmen.tpl");
    $this->app->Tpl->Set(INHALT,"");

    $this->app->Tpl->Set(SUBHEADING,"");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");

  }



  function VersanderzeugenFrankieren()
  {
		$this->VersandMenu();
    $id = $this->app->Secure->GetGET("id");
    $land = $this->app->Secure->GetGET("land");
    $adresse = $this->app->DB->Select("SELECT adresse FROM versand WHERE id='$id' and firma='".$this->app->User->GetFirma()."'");
    $namekunde = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' LIMIT 1");

 		$versandart = $this->app->DB->Select("SELECT l.versandart FROM versand v 
			LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
 		$lieferschein = $this->app->DB->Select("SELECT l.belegnr FROM versand v 
			LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    $lieferscheinid = $this->app->DB->Select("SELECT lieferschein FROM versand WHERE id='$id' LIMIT 1");



    $submit = $this->app->Secure->GetPOST("submit");
    if($submit!="")// && $versandart != "dhl")
    {
        $versand = $this->app->Secure->GetPOST("versand");
        $tracking= $this->app->Secure->GetPOST("tracking_$versand");

        $this->app->DB->Update("UPDATE versand SET versandunternehmen='$versand', tracking='$tracking',
						versendet_am=NOW(),abgeschlossen='1',versendet_am_zeitstempel=NOW(), logdatei=NOW() WHERE id='$id' LIMIT 1");

				//TODO wenn alle Pakete erfasst

        //versand mail an kunden
        $this->app->erp->Versandmail($id); 

        if($versand=="rma")
        {


        }

      	header("Location: index.php?module=versanderzeugen&action=offene");
    }
	

    $name = $this->app->DB->Select("SELECT r.name FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(ADRESSE,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT r.ansprechpartner FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(ADRESSE,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT r.abteilung FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(ADRESSE,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT r.unterabteilung FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(ADRESSE,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT r.adresszusatz FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(ADRESSE,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT r.strasse FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(ADRESSE,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT CONCAT(r.land,'-',r.plz,' ',r.ort) FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(ADRESSE,$name."&nbsp;<br>");

    $name = $this->app->DB->Select("SELECT l.name FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(LIEFERUNG,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT l.ansprechpartner FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(LIEFERUNG,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT l.abteilung FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(LIEFERUNG,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT l.unterabteilung FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(LIEFERUNG,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT l.adresszusatz FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(LIEFERUNG,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT l.strasse FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(LIEFERUNG,$name."&nbsp;<br>");
    $name = $this->app->DB->Select("SELECT CONCAT(l.land,'-',l.plz,' ',l.ort) FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    if($name!="") $this->app->Tpl->Add(LIEFERUNG,$name."&nbsp;<br>");

    $name = $this->app->DB->Select("SELECT r.zahlungsweise FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");
    $versandart = $this->app->DB->Select("SELECT l.versandart FROM versand v LEFT JOIN rechnung r ON r.id=v.rechnung LEFT JOIN lieferschein l ON l.id=v.lieferschein WHERE v.id='$id' LIMIT 1");


    if($name=="nachnahme") $this->app->Tpl->Add(INFORMATION,"<div class=\"error\">Nachnahme Lieferung!</div>");
    if($name=="bar") $this->app->Tpl->Add(INFORMATION,"<div class=\"error\">Achtung Barzahlung!</div>");
    if($versandart=="selbstabholer") { $this->app->Tpl->Add(INFORMATION,"<div class=\"error\">Achtung Selbstabholer!</div>"); $this->app->Tpl->Set(SELBSTABHOLER,"checked"); }
    if($versandart=="packstation") $this->app->Tpl->Add(INFORMATION,"<div class=\"error\">Achtung Packstation!</div>");
    if($versandart=="DHL") { $this->app->Tpl->Add(INFORMATION,"<div class=\"info\">DHL Versand</div>"); $this->app->Tpl->Set(DHL,"checked");}
    if($versandart=="DPD" || $versandart=="express_dpd" || $versandart=="export_dpd") { 	
			$this->app->Tpl->Add(INFORMATION,"<div class=\"info\">DPD Versand</div>"); $this->app->Tpl->Set(DPD,"checked");}
    if($versandart=="dpd") { $this->app->Tpl->Add(INFORMATION,"<div class=\"info\">DPD Versand</div>"); $this->app->Tpl->Set(DPD,"checked");}
    if($versandart=="dhl") { $this->app->Tpl->Add(INFORMATION,"<div class=\"info\">DHL Versand</div>"); $this->app->Tpl->Set(DHL,"checked");}
    if($versandart=="rma") { $this->app->Tpl->Add(INFORMATION,"<div class=\"error\">Achtung RMA!</div>");  $this->app->Tpl->Set(RMA,"checked"); }
    if($versandart=="spedition") { $this->app->Tpl->Add(INFORMATION,"<div class=\"error\">Achtung Versand mit Spedition!</div>");  $this->app->Tpl->Set(SPEDITION,"checked"); }
    if($versandart=="express") { $this->app->Tpl->Add(INFORMATION,"<div class=\"error\">Achtung Versand per Epxress!</div>");  $this->app->Tpl->Set(EXPRESS,"checked"); }
    if($versandart=="expresseuropa") { $this->app->Tpl->Add(INFORMATION,"<div class=\"error\">Achtung Versand per EU-Epxress!</div>");  $this->app->Tpl->Set(EXPRESSEUROPA,"checked"); }
    if($versandart=="briefklein") { $this->app->Tpl->Add(INFORMATION,"<div class=\"error\">Versand per Brief Klein!</div>");  $this->app->Tpl->Set(BRIEFMARKEKLEIN,"checked"); }
    if($versandart=="briefgross") { $this->app->Tpl->Add(INFORMATION,"<div class=\"error\">Versand per Brief Gro&szlig;!</div>");  $this->app->Tpl->Set(BRIEFMARKEGROSS,"checked"); }

    $this->app->Tpl->Parse(TAB1,"versandeinzel.tpl");

		$this->app->Tpl->Set(TABTEXT,"Versand Kunde: ".$namekunde." LS: <b>$lieferschein</b>");

    $this->app->Tpl->Set(DHL,"checked");
		
    $this->app->Tpl->Set(VORSCHLAG,strtoupper($versandart));


    if($versandart=="packstation" || $versandart=="versandunternehmen" || $versandart=="DHL" || $versandart=="dhl") 
      $this->app->erp->PaketmarkeDHLEmbedded(TAB1,"versand");
    else if($versandart=="DPD" || $versandart=="dpd" || $versandart=="express_dpd") 
      $this->app->erp->PaketmarkeDPDEmbedded(TAB1,"versand");
    else if($versandart=="express_dpd") 
      $this->app->erp->PaketmarkeDPDEmbedded(TAB1,"versand","express");
    else if($versandart=="export_dpd") 
      $this->app->erp->PaketmarkeDPDEmbedded(TAB1,"versand","export");
    else if($versandart=="ups" || $versandart=="UPS") 
      $this->app->erp->PaketmarkeUPSEmbedded(TAB1,"versand");
    else if(strtolower($versandart)=="express_dhl")
      $this->app->erp->PaketmarkeDHLEmbedded(TAB1,"versand","express");
    else if ($versandart=="rma")
      $this->app->Tpl->Parse(TAB1,"versand_rma.tpl");
    else
      $this->app->Tpl->Parse(TAB1,"versandfrankieren.tpl");

		$freitext_lieferschein = $this->app->DB->Select("SELECT freitext FROM lieferschein WHERE id='$lieferscheinid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
		$internebemerkung = $this->app->DB->Select("SELECT internebemerkung FROM lieferschein WHERE id='$lieferscheinid' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
    $versand_gelesen = $this->app->DB->Select("SELECT gelesen FROM versand WHERE id='$id' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
//		$freitext_lieferschein = nl2br($freitext_lieferschein);
		if(($freitext_lieferschein!="" || $internebemerkung!="") && $versand_gelesen!=1 && $this->app->erp->Firmendaten("versand_gelesen"))
		$this->app->Tpl->Add(TAB1,"<div style=\"color:white;font-size:18pt; font-weight:bold; position:absolute; 
			background-color:red; top:0px;padding:10px; width:1078px; height:350px; overflow:scroll;\"><table width=100%><tr><td><u>Interne Bemerkung:</u></td><td align=right>
			<input type=\"button\" value=\"als gelesen markieren\" onclick=\"window.location.href='index.php?module=versanderzeugen&action=gelesen&id=$id'\" style=\"font-size:10pt;\"></td></tr></table><br>$freitext_lieferschein<br>$internebemerkung</div>");

    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }



  function VersanderzeugenOffene()
  {

		$lieferschein = $this->app->Secure->GetPOST("lieferschein");

		if($lieferschein !="")
		{
			$id = $this->app->DB->Select("SELECT id FROM lieferschein WHERE belegnr='$lieferschein' AND belegnr!='' LIMIT 1");
			if($id > 0)
			{
				$versand = $this->app->DB->Select("SELECT id FROM versand WHERE lieferschein='$id' AND abgeschlossen!=1 LIMIT 1");
				header("Location: index.php?module=versanderzeugen&action=einzel&id=$versand");
				exit;
			} else {
				$this->app->Tpl->Set(MESSAGE,"<div class=error>Lieferschein nicht gefunden!</div>");
			}

		}
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Verdsandzentrum");

		$this->app->erp->MenuEintrag("index.php?module=versanderzeugen&action=offene","&Uuml;bersicht");


    // suche ob es etwas im zwischenlager gibt mit lieferung
    $zaehlen = $this->app->DB->Select("SELECT COUNT(id) FROM zwischenlager WHERE objekt='lieferung'");
    $versand = $this->app->DB->Select("SELECT COUNT(id) FROM versand WHERE abgeschlossen!='1'");
   
     $projektearr = $this->app->DB->SelectArr("SELECT id FROM projekt WHERE autoversand='1' AND firma='".$this->app->User->GetFirma()."'");

    $projektearr[]=0;
		$this->app->YUI->TableSearch(TAB1,"versandoffene");
		$this->app->YUI->TableSearch(TAB2,"versandfertig");
   	
    $this->app->Tpl->Set(SUBHEADING,"");
    $this->app->Tpl->Parse(PAGE,"versanderzeugen_offene.tpl");

  }


  function VersanderzeugenLogin()
  {
    if($this->app->User->GetID()!="")
      {
      $this->VersanderzeugenMain();
      }
    else
      {
      $this->app->Tpl->Set(HEADING,"embedded projects GmbH Verwaltung ");
      $this->app->acl->Login();
      }
  }

  function VersanderzeugenLogout()
  {
    $this->app->acl->Logout();
    //$this->app->WF->ReBuildPageFrame();
    //$this->VersanderzeugenMain();
  }

  function VorgangAnlegen()
  {
    //print_r($_SERVER['HTTP_REFERER']);

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
    $titel = ucfirst($arrPara['module'])." ".$arrPara['id'];
    $href = $url['query'];
    $this->app->erp->AddOffenenVorgang($adresse, $titel, $href);

    header("Location: ".$_SERVER['HTTP_REFERER']);
  }


  function VorgangEntfernen()
  {
    $vorgang = $this->app->Secure->GetGET("vorgang");
    $this->app->erp->RemoveOffenenVorgangID($vorgang);
    header("Location: ".$_SERVER['HTTP_REFERER']);
  } 


}
?>
