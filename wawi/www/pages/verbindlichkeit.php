<?php
include ("_gen/verbindlichkeit.php");

class Verbindlichkeit extends GenVerbindlichkeit {
  var $app;
  
  function Verbindlichkeit($app) {
    //parent::GenVerbindlichkeit($app);
    $this->app=&$app;

    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id))
      $this->app->Tpl->Set(SUBHEADING,": ".
        $this->app->DB->Select("SELECT nummer FROM artikel WHERE id=$id LIMIT 1"));

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","VerbindlichkeitCreate");
    $this->app->ActionHandler("createbestellung","VerbindlichkeitCreateBestellung");
    $this->app->ActionHandler("editreadonly","VerbindlichkeitEditReadonly");
    $this->app->ActionHandler("edit","VerbindlichkeitEdit");
    $this->app->ActionHandler("list","VerbindlichkeitList");
    $this->app->ActionHandler("delete","VerbindlichkeitDelete");
    $this->app->ActionHandler("bezahlt","VerbindlichkeitBezahlt");
    $this->app->ActionHandler("offen","VerbindlichkeitOffen");
    $this->app->ActionHandler("kostenstelle","VerbindlichkeitKostenstelle");


    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }

	function VerbindlichkeitCreateBestellung()
	{
		$id=$this->app->Secure->GetGET("id");//Bestellung

		$adresse = $this->app->DB->Select("SELECT adresse FROM bestellung WHERE id='$id' LIMIT 1");
		$betrag = $this->app->DB->Select("SELECT gesamtsumme FROM bestellung WHERE id='$id' LIMIT 1");

		$summe_verbindlichkeiten = $this->app->DB->Select("SELECT SUM(betrag) FROM verbindlichkeit WHERE bestellung='$id' AND bestellung > 0");
		$summe_verbindlichkeiten_normal = $this->app->DB->Select("SELECT SUM(summenormal) FROM verbindlichkeit WHERE bestellung='$id' AND bestellung > 0");
		$summe_verbindlichkeiten_ermaessigt = $this->app->DB->Select("SELECT SUM(summeermaessigt) FROM verbindlichkeit WHERE bestellung='$id' AND bestellung > 0");
		
		$verwendungszweck = $this->app->DB->Select("SELECT bezeichnunglieferant FROM bestellung_position WHERE bestellung='$id' ORDER by sort LIMIT 1");

		$skonto = $this->app->DB->Select("SELECT zahlungszielskontolieferant FROM adresse WHERE id='$adresse' LIMIT 1");
		$skonto_tage = $this->app->DB->Select("SELECT zahlungszieltageskontolieferant FROM adresse WHERE id='$adresse' LIMIT 1");
		$zahlungszieltage = $this->app->DB->Select("SELECT zahlungszieltagelieferant FROM adresse WHERE id='$adresse' LIMIT 1");

		$zahlbarbis =$this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD(NOW(), INTERVAL $zahlungszieltage day),'%Y-%m-%d')");
		$skontobis=$this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD(NOW(), INTERVAL $skonto_tage day),'%Y-%m-%d')");
		$rechnungsdatum=date('Y-m-d');

		$summenormal=$this->app->DB->Select("SELECT SUM(preis*menge) FROM bestellung_position WHERE bestellung='$id' AND umsatzsteuer!='ermaessigt'");
		$summeermaessigt=$this->app->DB->Select("SELECT SUM(preis*menge) FROM bestellung_position WHERE bestellung='$id' AND umsatzsteuer='ermaessigt'");

		$summenormal = $summenormal*$this->app->erp->GetSteuersatzNormal(true,$id,"bestellung")-$summenormal;
		$summeermaessigt = $summeermaessigt*$this->app->erp->GetSteuersatzErmaessigt(true,$id,"bestellung")-$summeermaessigt;

		$betrag = $betrag - $summe_verbindlichkeiten;
		$summenormal = $summenormal - $summe_verbindlichkeiten_normal;
		$summeermaessigt = $summeermaessigt - $summe_verbindlichkeiten_ermaessigt;

		//standard felder laden
		// INSERT INTO verbindlichkeit 
		$this->app->DB->Insert("INSERT INTO `verbindlichkeit` 
				(`id`, `rechnung`, `zahlbarbis`, `betrag`, `umsatzsteuer`, `summenormal`, `summeermaessigt`, 
				`skonto`, `skontobis`, `freigabe`, `freigabemitarbeiter`, `bestellung`, `adresse`, `status`, 
				`bezahlt`, `kontoauszuege`, `firma`, `logdatei`, `rechnungsdatum`, `kostenstelle`, 
					`beschreibung`, `verwendungszweck`, `art`, `dta_datei`, `waehrung`) VALUES 
					(NULL, '', '$zahlbarbis', '$betrag', '', '$summenormal', '$summeermaessigt', '$skonto', '$skontobis', '0', '', '$id', '$adresse', 'offen', 
					'', '', '1', '0000-00-00 00:00:00', '$rechnungsdatum', '$kostenstelle', 
					'$beschreibung', '$verwendungszweck', 'lieferant', '0', 'EUR')");


		// redirect zu edit	
		$id = $this->app->DB->GetInsertID();
		header("Location: index.php?module=verbindlichkeit&action=edit&id=$id");
		exit;

	}


  function VerbindlichkeitCreate()
  {
    $this->VerbindlichkeitMenu();
    parent::VerbindlichkeitCreate();
  }


  function VerbindlichkeitDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->DB->Update("DELETE FROM verbindlichkeit WHERE id='$id' LIMIT 1");
    $this->VerbindlichkeitList();
  }


  function VerbindlichkeitKostenstelle()
  {
    $id = $this->app->Secure->GetGET("id");
		$bezahlt = $this->app->DB->Select("SELECT bezahlt FROM verbindlichkeit WHERE id='$id' LIMIT 1");

		if($bezahlt=="1")
    	$this->app->DB->Update("UPDATE verbindlichkeit SET bezahlt='0' WHERE id='$id' LIMIT 1");
		else
    	$this->app->DB->Update("UPDATE verbindlichkeit SET bezahlt='1' WHERE id='$id' LIMIT 1");
		//$this->VerbindlichkeitList();
		if($this->app->Secure->GetGET("cmd")=="tabs-1")
		header("Location: index.php?module=verbindlichkeit&action=list#tabs-1");
		else
		header("Location: index.php?module=verbindlichkeit&action=list#tabs-2");
		exit;
  }



  function VerbindlichkeitOffen()
  {
    $id = $this->app->Secure->GetGET("id");
    $cmd = $this->app->Secure->GetGET("cmd");
		if($cmd=="ueberweisung")
		{
			$verbindlichkeit = $this->app->DB->Select("SELECT verbindlichkeit FROM dta WHERE id='$id' LIMIT 1");
    	$this->app->DB->Update("UPDATE verbindlichkeit SET status='offen' WHERE id='$verbindlichkeit' LIMIT 1");
			$this->app->DB->Delete("DELETE FROM dta WHERE id='$id' LIMIT 1");
			header("Location: index.php?module=zahlungsverkehr&action=ueberweisung");
			exit;
		} else {
    	$this->app->DB->Update("UPDATE verbindlichkeit SET status='offen' WHERE id='$id' LIMIT 1");
		}

    $this->VerbindlichkeitList();
  }



  function VerbindlichkeitBezahlt()
  {
    $id = $this->app->Secure->GetGET("id");
    $cmd = $this->app->Secure->GetGET("cmd");
		if($cmd=="ueberweisung")
		{
			$verbindlichkeit = $this->app->DB->Select("SELECT verbindlichkeit FROM dta WHERE id='$id' LIMIT 1");
    	$this->app->DB->Update("UPDATE verbindlichkeit SET status='bezahlt' WHERE id='$verbindlichkeit' LIMIT 1");
			$this->app->DB->Delete("DELETE FROM dta WHERE id='$id' LIMIT 1");
			header("Location: index.php?module=zahlungsverkehr&action=ueberweisung");
			exit;
		} 
		if($cmd=="sammelueberweisung")
		{
    	$this->app->DB->Update("UPDATE verbindlichkeit SET status='bezahlt' WHERE id='$id' LIMIT 1");
			header("Location: index.php?module=zahlungsverkehr&action=ueberweisung#tabs-3");
			exit;
		} 



		else {
    	$this->app->DB->Update("UPDATE verbindlichkeit SET status='bezahlt' WHERE id='$id' LIMIT 1");
		}

    $this->VerbindlichkeitList();
  }

  function VerbindlichkeitList()
  {
    $this->VerbindlichkeitMenu();
    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=artikel&action=search\">Verbindlichkeit suchen</a></li>");


		$submit = $this->app->Secure->GetPOST("submit");
		if($submit!="")
		{
			$verbindlichkeit = $this->app->Secure->GetPOST("verbindlichkeit");
			$konto = $this->app->Secure->GetPOST("konto");

			$bic = $this->app->DB->Select("SELECT swift FROM konten WHERE id='$konto' LIMIT 1");
			$iban = $this->app->DB->Select("SELECT iban FROM konten WHERE id='$konto' LIMIT 1");
			$inhaber = $this->app->DB->Select("SELECT inhaber FROM konten WHERE id='$konto' LIMIT 1");

			// ausgabe + jedes als bezahlt markieren
			include_once("./plugins/sepa/Sepa_credit_XML_Transfer_initation.class.php");
			
			header ("Content-Type:text/xml");
			// Il sera nommé downloaded.pdf
			header('Content-Disposition: attachment; filename="sepa.xml"');

			$test = new Sepa_credit_XML_Transfer_initation(date('Ymd')); // batch name
			$test->setOrganizationName($inhaber); // your accountname
			$test->setOrganizationIBAN($iban);	// your IBAN
			$test->setOrganizationBIC($bic);	// your BIC

			for($i=0;$i<count($verbindlichkeit);$i++)
			{ 
				$vid = $verbindlichkeit[$i];
				$adr_id = $this->app->DB->Select("SELECT adresse FROM verbindlichkeit WHERE id='$vid' LIMIT 1");			
				$rechnung = $this->app->DB->Select("SELECT rechnung FROM verbindlichkeit WHERE id='$vid' LIMIT 1");			
				$verwendungszweck = $this->app->DB->Select("SELECT verwendungszweck FROM verbindlichkeit WHERE id='$vid' LIMIT 1");			
				$betrag = $this->app->DB->Select("SELECT betrag FROM verbindlichkeit WHERE id='$vid' LIMIT 1");			
				$skonto = $this->app->DB->Select("SELECT skonto FROM verbindlichkeit WHERE id='$vid' LIMIT 1");			
				$adr_bic = $this->app->DB->Select("SELECT swift FROM adresse WHERE id='$adr_id' LIMIT 1");				
				$adr_iban = $this->app->DB->Select("SELECT iban FROM adresse WHERE id='$adr_id' LIMIT 1");				
				$adr_inhaber = $this->app->DB->Select("SELECT inhaber FROM adresse WHERE id='$adr_id' LIMIT 1");				
				$lieferantennummer = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='$adr_id' LIMIT 1");				
				if($adr_inhaber=="") $adr_inhaber = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adr_id' LIMIT 1");

				$skontocheck = $this->app->DB->Select("SELECT if(skontobis >=NOW(),'1','0') FROM verbindlichkeit WHERE id='$vid' LIMIT 1");		

				if($skontocheck=="1")
					$betrag = round($betrag*(1-($skonto/100)),2);
				
				//$betrag = str_replace('.','',$betrag);
				$betrag = str_replace(',','',$betrag);

				// add 3 test transactions
				$test_transaction	= new Sepa_credit_XML_Transfer_initation_Transaction($adr_inhaber,$betrag,$adr_iban,$adr_bic,"NR $vid RE $rechnung $verwendungszweck");

				// add the first to payment group 'a', second and third to 'b'
				$test->addTransaction($test_transaction,'001');
			}

			$test->build();
			//echo $test->getXML(); 

			$this->app->DB->Insert("INSERT INTO dta_datei (id,bezeichnung,bearbeiter,inhalt,datum,status,art,firma) VALUES ('','SEPA Sammeluebeweisung',
					".$this->app->User->GetID().",'".base64_encode($test->getXML())."','sammelueberweisung',1)");

			$dtadatei = $this->app->DB->GetInsertID();

			for($j=0;$j<count($verbindlichkeit);$j++)
			{ 
				$tmp_vid = $verbindlichkeit[$j];
    		$this->app->DB->Update("UPDATE verbindlichkeit SET status='bezahlt',dta_datei='$dtadatei' WHERE id='$tmp_vid' LIMIT 1");
			}

			header("Location: index.php?module=verbindlichkeit&action=list");
			exit;
		}

    //parent::VerbindlichkeitList();

		  $table = new EasyTable($this->app);
/*
    $table->Query("SELECT a.name, verbindlichkeit.betrag, verbindlichkeit.rechnung, DATE_FORMAT(verbindlichkeit.skontobis,'%d.%m.%Y') as bis,verbindlichkeit.id FROM verbindlichkeit, adresse a WHERE verbindlichkeit.adresse = a.id AND verbindlichkeit.bezahlt!=1 AND verbindlichkeit.skontobis <= NOW() AND verbindlichkeit.status!='bezahlt' AND verbindlichkeit.skonto > 0 order by verbindlichkeit.skontobis");
*/

    $this->app->Tpl->Set(TAB1,"<form action=\"\" method=\"post\">");
		$this->app->YUI->TableSearch(TAB1,"verbindlichkeiten");
/*
    $this->app->Tpl->Add(TAB1,"<br><center>Auswahl Konto:&nbsp;
        <select name=\"konto\">".$this->app->erp->GetSelectBICKonto()."</select>&nbsp;<input type=\"submit\" name=\"submit\" value=\"Sammel&uuml;berweisung anlegen und Zahlungen als bezahlt markieren\"></center></form>");
*/
/*
   $this->app->Tpl->Add(TAB1,"<br><center>
        <input type=\"submit\" name=\"submit\" value=\"Verbindlichkeiten an Zahlungstransfer &uuml;bergeben\"></center></form>");
*/
		$this->app->YUI->TableSearch(TAB2,"verbindlichkeitenarchiv");
    $this->app->Tpl->Parse(PAGE,"verbindlichkeituebersicht.tpl");
  }
  function VerbindlichkeitMenu()
  {
    $id = $this->app->Secure->GetGET("id");



		$this->app->Tpl->Set(KURZUEBERSCHRIFT,"Verbindlichkeiten");

		$action = $this->app->Secure->GetGET("action");
    $this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=create","Neue Verbindlichkeit anlegen");
		if($action!="list")
    	$this->app->erp->MenuEintrag("index.php?module=verbindlichkeit&action=list","Zur&uuml;ck zur &Uuml;bersicht");



//    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=verbindlichkeit&action=searc\">Rechnung Suchen</a></li>");
//    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=verbindlichkeit&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a></li>");



    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=artikel&action=search\">Verbindlichkeit suchen</a></li>");


  }


  function VerbindlichkeitEditReadonly()
  {
    $this->VerbindlichkeitMenu();
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(ID,$id);

    $this->app->erp->CommonReadonly();

		if($id > 0)
			$this->app->Tpl->Set(MELDUNG,"Bitte Nummer gut lesbar auf Rechnung notieren: ");

 		$summe = $this->app->DB->Select("SELECT CONCAT(betrag,' ',waehrung) FROM verbindlichkeit WHERE id='$id' LIMIT 1");
      $this->app->Tpl->Set(SUMME,$summe);


    $freigabe = $this->app->DB->Select("SELECT freigabe FROM verbindlichkeit WHERE id='$id' LIMIT 1");
    $freigabemitarbeiter= $this->app->DB->Select("SELECT freigabemitarbeiter FROM verbindlichkeit WHERE id='$id' LIMIT 1");
    if($freigabemitarbeiter=="" && $freigabe=="1") {
      $this->app->DB->Update("UPDATE verbindlichkeit SET freigabemitarbeiter = '".$this->app->User->GetName()."'  WHERE id='$id' LIMIT 1");
      $freigabemitarbeiter= $this->app->DB->Select("SELECT freigabemitarbeiter FROM verbindlichkeit WHERE id='$id' LIMIT 1");
    }

    $this->app->Tpl->Set(FREIGABEMITARBEITER,"<input type=\"text\" value=\"".$freigabemitarbeiter."\" readonly>");

    $bezahlt= $this->app->DB->Select("SELECT bezahlt FROM verbindlichkeit WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set(BEZAHLT,"<input type=\"text\" value=\"".$bezahlt."\" readonly>");

    $this->app->Tpl->Set(TABLE_ADRESSE_KONTAKTHISTORIE,"TDB");
    $this->app->Tpl->Set(TABLE_ADRESSE_ROLLEN,"TDB");

    $this->app->Tpl->Set(TABLE_ADRESSE_USTID,"TDB");

		

    parent::VerbindlichkeitEdit();
  }


  function VerbindlichkeitEdit()
  {
    $this->VerbindlichkeitMenu();
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(ID,$id);

		if($id > 0)
			$this->app->Tpl->Set(MELDUNG,"Bitte Nummer gut lesbar auf Rechnung notieren: ");


    $freigabe = $this->app->DB->Select("SELECT freigabe FROM verbindlichkeit WHERE id='$id' LIMIT 1");
    $freigabemitarbeiter= $this->app->DB->Select("SELECT freigabemitarbeiter FROM verbindlichkeit WHERE id='$id' LIMIT 1");
    if($freigabemitarbeiter=="" && $freigabe=="1") {
      $this->app->DB->Update("UPDATE verbindlichkeit SET freigabemitarbeiter = '".$this->app->User->GetName()."'  WHERE id='$id' LIMIT 1");
      $freigabemitarbeiter= $this->app->DB->Select("SELECT freigabemitarbeiter FROM verbindlichkeit WHERE id='$id' LIMIT 1");
    }

	  
		$alleverbindlichkeiten = $this->app->erp->VerbindlichkeitErweiterteBestellung($id);	
		if(count($alleverbindlichkeiten) > 0)
		{
			$summe = $this->app->DB->Select("SELECT CONCAT(betrag,' ',waehrung) FROM verbindlichkeit WHERE id='$id' LIMIT 1");
			$this->app->Tpl->Set(SUMME,$summe);
			$this->app->Tpl->Set(DISABLESTART,"<!--");
			$this->app->Tpl->Set(DISABLEENDE,"-->siehe Tab: Zuordnung Bestellungen");
			$this->app->DB->Update("UPDATE verbindlichkeit SET bestellung=0 WHERE id='$id' LIMIT 1");
		}


    $this->app->Tpl->Set(FREIGABEMITARBEITER,"<input type=\"text\" value=\"".$freigabemitarbeiter."\" readonly>");

    $bezahlt= $this->app->DB->Select("SELECT bezahlt FROM verbindlichkeit WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set(BEZAHLT,"<input type=\"text\" value=\"".$bezahlt."\" readonly>");

    $this->app->Tpl->Set(TABLE_ADRESSE_KONTAKTHISTORIE,"TDB");
    $this->app->Tpl->Set(TABLE_ADRESSE_ROLLEN,"TDB");

    $this->app->Tpl->Set(TABLE_ADRESSE_USTID,"TDB");

    parent::VerbindlichkeitEdit();
  }





}

?>
