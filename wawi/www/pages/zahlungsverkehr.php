<?php
//include ("_gen/zahlungsverkehr.php");
//require_once("Payment/DTA.php"); //PEAR

class Zahlungsverkehr //extends GenZahlungsverkehr
{

  function Zahlungsverkehr(&$app)
  {
    $this->app=$app; 

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("lastschrift","ZahlungsverkehrLastschrift");
    $this->app->ActionHandler("ueberweisung","ZahlungsverkehrUeberweisung");
    $this->app->ActionHandler("deletedtaueberweisung","ZahlungsverkehrDeleteDTAUeberweisung");
    $this->app->ActionHandler("deletedtalastschrift","ZahlungsverkehrDeleteDTALastschrift");
    $this->app->ActionHandler("downloaddta","ZahlungsverkehrDownloadDTA");
    $this->app->ActionHandler("downloaddtastatus","ZahlungsverkehrDownloadDTAStatus");
    $this->app->ActionHandler("einstellungen","ZahlungsverkehrEinstellungen");
    $this->app->ActionHandler("downloadavis","ZahlungsverkehrDownloadAvis");
    $this->app->ActionHandler("undodta","ZahlungsverkehrUndoDTA");
    $this->app->ActionHandler("downloaddtalastschrift","ZahlungsverkehrDownloadDTALastschrift");
    $this->app->ActionHandler("verbindlichkeitenladen","ZahlungsverkehrVerbindlichkeitenLaden");
    $this->app->ActionHandler("dtaedit","ZahlungsverkehrDTAEdit");
    $this->app->ActionHandler("minidetail","ZahlungsverkehrMiniDetail");

    $this->app->DefaultActionHandler("ueberweisung");
    $this->app->ActionHandlerListen($app);
  }

  function ZahlungsverkehrMiniDetail()
  {
    /*
       if(strpos($_SERVER["HTTP_REFERER"] ,"action=list")!==false)
       $this->MultilevelMiniDetailPositionen();
       else
       $this->MultilevelMiniDetailAbrechnung();
     */
    $id = $this->app->Secure->GetGET("id");
    $summe = $this->app->DB->Select("SELECT SUM(betrag) FROM dta WHERE datei='$id'");
    $table = new EasyTable($this->app);
    $table->Query("SELECT name, vz1, betrag FROM dta WHERE datei='$id'");
    $table->DisplayNew(BUCHUNGEN,"Betrag","noAction");
    $this->app->Tpl->Add(BUCHUNGEN,"<table width=100%><tr><td align=right>Pr&uuml;fsumme: $summe</td></tr></table>");
    $this->app->Tpl->Output("zahlungsverkehr_minidetail.tpl");
    exit;
  }

  function ZahlungsverkehrDTAEdit()
  {
    $id = $this->app->Secure->GetGET("id");
    $verbindlichkeit = $this->app->DB->Select("SELECT verbindlichkeit FROM dta WHERE id='$id' LIMIT 1");
    header("Location: index.php?module=verbindlichkeit&action=edit&id=".$verbindlichkeit);
    exit;
  }


  function ZahlungsverkehrDownloadAvis()
  {
    $id = $this->app->Secure->GetGET("id");
    $Brief = new ZahlungsavisPDF($this->app,$projekt);
    $Brief->GetZahlungsavis($id);
    $Brief->displayDocument();
    exit;
  }	


  function ZahlungsverkehrDeleteDTALastschrift()
  {
    $id = $this->app->Secure->GetGET("id");

    if($id > 0)
    {
      $this->app->DB->Delete("DELETE FROM dta_datei WHERE id='$id' LIMIT 1");
      $rechnung = $this->app->DB->SelectArr("SELECT id FROM rechnung WHERE dta_datei='".$id."'");

      for($i=0;$i<count($rechnung);$i++)
      {
        if($rechnung[$i][id] > 0)
          $this->app->DB->Update("UPDATE rechnung SET zahlungsstatus='offen',dta_datei=0 WHERE id='".$rechnung[$i][id]."' LIMIT 1");
      }
      $gutschrift = $this->app->DB->SelectArr("SELECT id FROM gutschrift WHERE dta_datei='".$id."'");

      for($i=0;$i<count($gutschrift);$i++)
      {
        if($gutschrift[$i][id] > 0)
        {
          $this->app->DB->Update("UPDATE gutschrift SET zahlungsstatus='',dta_datei=0,manuell_vorabbezahlt='',
              manuell_vorabbezahlt_hinweis='' WHERE id='".$gutschrift[$i][id]."' LIMIT 1");
        }
      }

      $zahlungsavis = $this->app->DB->SelectArr("SELECT id FROM zahlungsavis WHERE dta_datei='".$id."'");
      for($i=0;$i<count($zahlungsavis);$i++)
      {
        if($zahlungsavis[$i][id] > 0)
        {
          $this->app->DB->Delete("DELETE FROM zahlungsavis_rechnung WHERE zahlungsavis='".$zahlungsavis[$i][id]."'");
          $this->app->DB->Delete("DELETE FROM zahlungsavis_gutschrift WHERE zahlungsavis='".$zahlungsavis[$i][id]."'");
        }
      }
      $this->app->DB->Delete("DELETE FROM zahlungsavis WHERE dta_datei='".$id."'");

      $this->app->DB->Update("DELETE FROM dta WHERE datei='$id'");
    }
    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Sammellastschrift wurde gel&ouml;scht!</div>  ");
    header("Location: index.php?module=zahlungsverkehr&action=lastschrift&msg=$msg#tabs-3");
    exit;	
  }



  function ZahlungsverkehrUndoDTA()
  {
    $id = $this->app->Secure->GetGET("id");

    if($id > 0)
    {
      $verbindlichkeit = $this->app->DB->Select("SELECT verbindlichkeit FROM dta WHERE id='$id' LIMIT 1");
      if($verbindlichkeit > 0)
        $this->app->DB->Update("UPDATE verbindlichkeit SET status='offen' WHERE id='".$verbindlichkeit."' LIMIT 1");

      $this->app->DB->Update("UPDATE dta SET datei=0 WHERE id='$id'");
    }
    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die &Uuml;berweisung wurde als nicht bezahlt markiert!</div>  ");
    header("Location: index.php?module=zahlungsverkehr&action=ueberweisung&msg=$msg#tabs-1");
    exit;	
  }




  function ZahlungsverkehrDeleteDTAUeberweisung()
  {
    $id = $this->app->Secure->GetGET("id");

    if($id > 0)
    {
      $this->app->DB->Delete("DELETE FROM dta_datei WHERE id='$id' LIMIT 1");
      $verbindlichkeit = $this->app->DB->SelectArr("SELECT verbindlichkeit FROM dta WHERE datei='".$id."'");

      for($i=0;$i<count($verbindlichkeit);$i++)
      {
        if($verbindlichkeit[$i][verbindlichkeit] > 0)
          $this->app->DB->Update("UPDATE verbindlichkeit SET status='offen' WHERE id='".$verbindlichkeit[$i][verbindlichkeit]."' LIMIT 1");
      }

      $this->app->DB->Update("UPDATE dta SET datei=0 WHERE datei='$id'");
    }
    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Sammel&uuml;berweisung wurde gel&ouml;scht!</div>  ");
    header("Location: index.php?module=zahlungsverkehr&action=ueberweisung&msg=$msg#tabs-3");
    exit;	
  }


  function ZahlungsverkehrVerbindlichkeitenLaden()
  {

    $this->ZahlungsverkehrAutoabgleichVerbindlichkeiten();
    $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Verbindlichkeiten wurden geladen!</div>  ");
    header("Location: index.php?module=zahlungsverkehr&modul=ueberweisung&msg=$msg#tabs-1");
    exit;	
  }




  function ZahlungsverkehrDownloadDTALastschrift()
  {
    $id = $this->app->Secure->GetGET("id");

    $this->app->DB->Update("UPDATE dta_datei SET status='abgeschlossen' WHERE id='$id' LIMIT 1");

    $konto = $this->app->DB->Select("SELECT konto FROM dta_datei WHERE id='".$id."' LIMIT 1");
    $bic = trim($this->app->DB->Select("SELECT swift FROM konten WHERE id='$konto' LIMIT 1"));
    $iban = trim($this->app->DB->Select("SELECT iban FROM konten WHERE id='$konto' LIMIT 1"));
    $inhaber = trim($this->app->DB->Select("SELECT inhaber FROM konten WHERE id='$konto' LIMIT 1"));
    $glaeubiger = trim($this->app->DB->Select("SELECT glaeubiger FROM konten WHERE id='$konto' LIMIT 1"));

    require_once("./plugins/sepa/sepa.php");

    // Erzeugen einer neuen Instanz
        $config = array("name" => $inhaber,
                "IBAN" => $iban,
                "BIC" => $bic,
                "batch" => true,
                "creditor_id" => "00000",
                "currency" => "EUR"
                );
     $SEPASDD = new SEPASDD($config); 

    $dta = $this->app->DB->SelectArr("SELECT * FROM dta WHERE datei='$id'");

    for($i=0;$i<count($dta);$i++)
    {
      $verwendungszweck = $this->app->erp->UmlauteEntfernen($dta[$i][vz1]);

      $betrag = $dta[$i][betrag];

      $adr_id = $dta[$i][adresse];
      $adr_bic = trim($dta[$i][blz]); 
      $adr_iban = trim($dta[$i][konto]);
      $mandatsreferenzaenderung = $dta[$i][mandatsreferenzaenderung];

      $mandatsreferenz = trim($this->app->DB->Select("SELECT mandatsreferenz FROM adresse WHERE id='".$adr_id."' LIMIT 1"));
      $mandatsreferenzdatum = $this->app->DB->Select("SELECT mandatsreferenzdatum FROM adresse WHERE id='".$adr_id."' LIMIT 1");

      if($mandatsreferenzaenderung==1) $mandatsreferenzaenderung=true; else $mandatsreferenzaenderung=false;

      if($mandatsreferenzdatum=="0000-00-00" || $mandatsreferenzdatum=="") {
				$mandatsreferenzdatum=date('Y-m-d');
				$this->app->DB->Update("UPDATE adresse SET mandatsreferenzdatum='$mandatsreferenzdatum' WHERE id='".$adr_id."' LIMIT 1");
			}	

      if($mandatsreferenz=="")
      {
        $mandatsreferenz = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='".$adr_id."' LIMIT 1");
        $mandatsreferenz = preg_replace('/[^A-Za-z0-9]+/', '', $mandatsreferenz);
				$this->app->DB->Update("UPDATE adresse SET mandatsreferenz='$mandatsreferenz' WHERE id='".$adr_id."' LIMIT 1");
      }
      if($mandatsreferenzaenderung==1) $mandatsreferenzaenderung=true; else $mandatsreferenzaenderung=false;

      $adr_inhaber = $this->app->erp->UmlauteEntfernen($dta[$i][name]);

      $betrag = str_replace('.','',$betrag);
      $betrag = str_replace(',','',$betrag);

			// pruefe ob es schon einen einzug gabe
			$checkid = $this->app->DB->Select("SELECT COUNT(id) FROM dta WHERE konto='$adr_iban' AND blz='$adr_bic' AND lastschrift > 0 AND datei > 0");
			if($checkid >= 2)
			{
				// wiederholung
				$type = "RCUR";
				$collection_date = date('Y-m-d', strtotime('+6 days')); //normal 3 bank AT
			} else {
				$type = "FRST";
				$collection_date = date('Y-m-d', strtotime('+10 days')); // normal 6 bank AT
			}

      $payment = array("name" => $adr_inhaber,
                 "IBAN" => $adr_iban,
                 "BIC" => $adr_bic,
                 "amount" => $betrag,
                 "type" => $type,
                 "collection_date" => $collection_date,
                 "mandate_id" => $mandatsreferenz,
                 "mandate_date" => $mandatsreferenzdatum,
                 "description" => $verwendungszweck
                );        

      $SEPASDD->addPayment($payment);

    }
    //$sepaxml = $creator->generateBasislastschriftXml();
    $sepaxml = $SEPASDD->save();
    header('Content-Type: application/xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="sepa_lastschrift.xml"');
    // Nun kann die XML-Datei über den Aufruf der entsprechenden Methode generiert werden
    echo trim($sepaxml);
    exit;
  }




  function ZahlungsverkehrDownloadDTALastschrift_alt()
  {
    $id = $this->app->Secure->GetGET("id");

    $this->app->DB->Update("UPDATE dta_datei SET status='abgeschlossen' WHERE id='$id' LIMIT 1");

    $konto = $this->app->DB->Select("SELECT konto FROM dta_datei WHERE id='".$id."' LIMIT 1");
    $bic = trim($this->app->DB->Select("SELECT swift FROM konten WHERE id='$konto' LIMIT 1"));
    $iban = trim($this->app->DB->Select("SELECT iban FROM konten WHERE id='$konto' LIMIT 1"));
    $inhaber = trim($this->app->DB->Select("SELECT inhaber FROM konten WHERE id='$konto' LIMIT 1"));
    $glaeubiger = trim($this->app->DB->Select("SELECT glaeubiger FROM konten WHERE id='$konto' LIMIT 1"));

    require_once("./plugins/sepa/SepaXmlCreator.php");

    // Erzeugen einer neuen Instanz
    $creator = new SepaXmlCreator();

    /*
     * Mit den Account-Werten wird das eigene Konto beschrieben
     * erster Parameter = Name
     * zweiter Parameter = IBAN
     * dritter Paramenter = BIC
     */
    $creator->setAccountValues($inhaber, $iban, $bic);

    /*
     * Setzen Sie von der Bundesbank übermittelte Gläubiger-ID
     */

    $creator->setGlaeubigerId($glaeubiger);

    // pro Auftrag wenn dann
    //$creator->setAusfuehrungOffset(7);

    $dta = $this->app->DB->SelectArr("SELECT * FROM dta WHERE datei='$id'");

    for($i=0;$i<count($dta);$i++)
    {
      $verwendungszweck = $this->app->erp->UmlauteEntfernen($dta[$i][vz1]);

      $betrag = $dta[$i][betrag];

      $adr_id = $dta[$i][adresse];
      $adr_bic = trim($dta[$i][blz]); 
      $adr_iban = trim($dta[$i][konto]);
      $mandatsreferenzaenderung = $dta[$i][mandatsreferenzaenderung];

      $mandatsreferenz = trim($this->app->DB->Select("SELECT mandatsreferenz FROM adresse WHERE id='".$adr_id."' LIMIT 1"));
      $mandatsreferenzdatum = $this->app->DB->Select("SELECT mandatsreferenzdatum FROM adresse WHERE id='".$adr_id."' LIMIT 1");

      if($mandatsreferenzaenderung==1) $mandatsreferenzaenderung=true; else $mandatsreferenzaenderung=false;

      if($mandatsreferenzdatum=="0000-00-00" || $mandatsreferenzdatum=="") $mandatsreferenzdatum=date('Y')."-01-01";

      if($mandatsreferenz=="")
      {
        $mandatsreferenz = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='".$adr_id."' LIMIT 1");
        $mandatsreferenz = preg_replace('/[^A-Za-z0-9]+/', '', $mandatsreferenz);
      }
      if($mandatsreferenzaenderung==1) $mandatsreferenzaenderung=true; else $mandatsreferenzaenderung=false;

      $adr_inhaber = $this->app->erp->UmlauteEntfernen($dta[$i][name]);

      //$betrag = str_replace('.','',$betrag);
      $betrag = str_replace(',','',$betrag);

      // Erzeugung einer neuen Buchungssatz
      $buchung = new SepaBuchung();
      // gewünschter Einzugsbetrag
      $buchung->setBetrag($betrag);
      // gewünschte End2End Referenz (OPTIONAL)
      //$buchung->setEnd2End('ID-00002');
      // BIC des Zahlungspflichtigen Institutes
      $buchung->setBic($adr_bic);
      // Name des Zahlungspflichtigen
      $buchung->setName($adr_inhaber);//'Mustermann, Max');
      // IBAN des Zahlungspflichtigen
      $buchung->setIban($adr_iban);
      // gewünschter Verwendungszweck (OPTIONAL)
      $buchung->setVerwendungszweck($verwendungszweck);
      // Referenz auf das vom Kunden erteilte Lastschriftmandat
      // ID = MANDAT0001
      // Erteilung durch Kunden am 20. Mai 2013
      // False = seit letzter Lastschrift wurde am Mandat nichts geändert
      $buchung->setMandat($mandatsreferenz, $mandatsreferenzdatum, $mandatsreferenzaenderung);

      
      $creator->setIsFolgelastschrift();
      // Buchung zur Liste hinzufügen
      $creator->addBuchung($buchung); 

    }
    $sepaxml = $creator->generateBasislastschriftXml();
    header('Content-Type: application/xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="sepa_lastschrift.xml"');
    // Nun kann die XML-Datei über den Aufruf der entsprechenden Methode generiert werden
    echo trim($sepaxml);
    exit;
  }



  function ZahlungsverkehrDownloadDTAStatus()
  {
    $id = $this->app->Secure->GetGET("id");

    if($id < 0) return;

    $status = $this->app->DB->Select("SELECT status FROM dta_datei WHERE id='$id' LIMIT 1");
    if($status=='abgeschlossen') $status='offen'; else $status='abgeschlossen';

    $this->app->DB->Update("UPDATE dta_datei SET status='$status' WHERE id='$id' LIMIT 1");

    header("Location: index.php?module=zahlungsverkehr&action=ueberweisung#tabs-3");
    exit;		
  }


  function ZahlungsverkehrDownloadDTA()
  {
    $id = $this->app->Secure->GetGET("id");

    if($id < 0) return;

    //$this->app->DB->Update("UPDATE dta_datei SET status='abgeschlossen' WHERE id='$id' LIMIT 1");

    $konto = $this->app->DB->Select("SELECT konto FROM dta_datei WHERE id='".$id."' LIMIT 1");

    $bic = $this->app->DB->Select("SELECT swift FROM konten WHERE id='$konto' LIMIT 1");
    $iban = $this->app->DB->Select("SELECT iban FROM konten WHERE id='$konto' LIMIT 1");
    $inhaber = $this->app->DB->Select("SELECT inhaber FROM konten WHERE id='$konto' LIMIT 1");

    include_once("./plugins/sepa/Sepa_credit_XML_Transfer_initation.class.php");

    header ("Content-Type:text/xml");
    header('Content-Disposition: attachment; filename="sepa.xml"');

    $test = new Sepa_credit_XML_Transfer_initation(date('Ymd')); // batch name
    $test->setOrganizationName($inhaber); // your accountname
    $test->setOrganizationIBAN($iban);  // your IBAN
    $test->setOrganizationBIC($bic);  // your BIC

    $dta = $this->app->DB->SelectArr("SELECT * FROM dta WHERE datei='$id'");

    for($i=0;$i<count($dta);$i++)
    {

      $verwendungszweck = $this->app->erp->UmlauteEntfernen($dta[$i][vz1]);

      $betrag = $dta[$i][betrag];

      $adr_id = $dta[$i][adresse];
      $adr_bic = $dta[$i][blz]; 
      $adr_iban = $dta[$i][konto];
      $adr_inhaber = $this->app->erp->UmlauteEntfernen($dta[$i][name]);

      //$betrag = str_replace('.','',$betrag);
      $betrag = str_replace(',','',$betrag);

      // add 3 test transactions
      $test_transaction = new Sepa_credit_XML_Transfer_initation_Transaction($adr_inhaber,$betrag,$adr_iban,$adr_bic,$verwendungszweck);

      // add the first to payment group 'a', second and third to 'b'
      $test->addTransaction($test_transaction,'001');
    }
    $test->build();
    echo $test->getXML(); 

    exit;
  }


  function ZahlungsverkehrAutoabgleichVerbindlichkeiten()
  {
    $this->app->DB->Delete("DELETE FROM dta WHERE verbindlichkeit > 0 AND datei <=0");

    $skontoversatz = $this->app->erp->Firmendaten("skonto_ueberweisung_ueberziehen");
    if($skontoversatz <= 0) $skontoversatz=0;

    for($j=0;$j<2;$j++)
    {
      if($j==0)
      {
        $arr_verbindlichkeit = $this->app->DB->SelectArr("SELECT * FROM verbindlichkeit v WHERE 
            (v.skontobis!='0000-00-00' AND v.skontobis >=DATE_FORMAT(DATE_SUB(NOW(), INTERVAL $skontoversatz DAY),'%Y-%m-%d') ) AND (v.status='offen' OR v.status='') AND v.freigabe='1' AND v.rechnungsfreigabe=1 AND v.betrag >=0");	
      }
      else
      {
        $arr_verbindlichkeit = $this->app->DB->SelectArr("SELECT * FROM verbindlichkeit v WHERE 
            (v.skontobis<DATE_FORMAT(DATE_SUB(NOW(), INTERVAL $skontoversatz DAY),'%Y-%m-%d') OR v.skontobis='0000-00-00') AND (v.status='offen' OR v.status='') AND v.freigabe='1' AND v.rechnungsfreigabe=1 AND v.betrag >=0");	
      }

      for($i=0;$i<count($arr_verbindlichkeit);$i++)
      {
        $verbindlichkeit = $arr_verbindlichkeit[$i]['id'];
        $adresse = $arr_verbindlichkeit[$i]['adresse'];
        $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' LIMIT 1");

        if($j==0)
          $betrag = round($arr_verbindlichkeit[$i]['betrag']*(1-($arr_verbindlichkeit[$i]['skonto']/100)),2);
        else
          $betrag = $arr_verbindlichkeit[$i]['betrag'];

        $betrag = str_replace(',','',$betrag);

        $vz1 = "NR ".$arr_verbindlichkeit[$i]['id']." RE ".$arr_verbindlichkeit[$i]['rechnung']." ".$arr_verbindlichkeit[$i]['verwendungszweck'];

        $blz = $this->app->DB->Select("SELECT swift FROM adresse WHERE id='$adresse' LIMIT 1");
        $konto = $this->app->DB->Select("SELECT iban FROM adresse WHERE id='$adresse' LIMIT 1");

        $lastschrift = 0; $gutschrift = 0; 
        $waehrung = $arr_verbindlichkeit[$i]['waehrung'];

        if($arr_verbindlichkeit[$i]['skonto'] > 0)
        {
          $datum = $arr_verbindlichkeit[$i]['skontobis'];

          $sparen = $arr_verbindlichkeit[$i]['betrag'] - round($arr_verbindlichkeit[$i]['betrag']*(1-($arr_verbindlichkeit[$i]['skonto']/100)),2);
          $gesamt = $arr_verbindlichkeit[$i]['betrag'];

          $vz1 = $vz1."<!--ENDE--><br><font color=red>Skonto ".$arr_verbindlichkeit[$i]['skonto']."% (".$sparen." ".$arr_verbindlichkeit[$i]['waehrung']." von $gesamt ".$arr_verbindlichkeit[$i]['waehrung'].") bis: ".$arr_verbindlichkeit[$i]['skontobis']."</font>";		
        }
        else
          $datum = $arr_verbindlichkeit[$i]['zahlbarbis'];

        if($datum=='0000-00-00') $datum=date('Y-m-d');

        $this->app->DB->Insert("INSERT INTO dta (id,adresse,datum,name,konto,blz,betrag,vz1,vz2,vz3,lastschrift,gutschrift,waehrung,firma,verbindlichkeit)
            VALUES ('','$adresse','$datum','$name','$konto','$blz','$betrag','$vz1','$vz2','$vz3','$lastschrift','$gutschrift','$waehrung','1','$verbindlichkeit')");
      }
    }


  }

  function ZahlungsverkehrUeberweisung()
  {
    // 		$this->app->Tpl->Set(UEBERSCHRIFT,"Lastschrift&nbsp;/&nbsp;Sammel&uuml;berweisung");
    $this->ZahlungsverkehrMenu();
    $erzeugen = $this->app->Secure->GetPOST("erzeugen"); 
    $lastschrift= $this->app->Secure->GetPOST("lastschrift"); 
    $konto=$this->app->Secure->GetPOST("konto");


    if($erzeugen!="")
    {
      //erzeugen
      $dta = $this->app->Secure->GetPOST("dta"); 

      if(count($dta)>0)
      {
        $this->app->DB->Insert("INSERT INTO dta_datei (id,bezeichnung,bearbeiter,inhalt,datum,konto,status,art,firma) VALUES 
            ('','Sammelueberweisung','".$this->app->User->GetName()."','',NOW(),'$konto','offen','sammelueberweisung','1')");

        $dta_datei = $this->app->DB->GetInsertID();
      }

      for($i=0;$i<count($dta);$i++)
      {
        if($dta_datei > 0 && $dta[$i] > 0)
        {
          $vz1 = $this->app->DB->Select("SELECT vz1 FROM dta WHERE id='".$dta[$i]."' LIMIT 1");
          $needle = strpos($vz1, '<!-'); // Position von '<a onMouseOut' von (HTML-Site) ermitteln
          if($needle > 0)
            $vz1 =  substr($vz1, 0,$needle); // gibt den Teil von string zurück, der durch die Parameter 

          $this->app->DB->Update("UPDATE dta SET datei='$dta_datei', vz1='$vz1' WHERE id='".$dta[$i]."' LIMIT 1");
          $verbindlichkeit = $this->app->DB->Select("SELECT verbindlichkeit FROM dta WHERE id='".$dta[$i]."' LIMIT 1");

          if($verbindlichkeit > 0)
            $this->app->DB->Update("UPDATE verbindlichkeit SET status='bezahlt' WHERE id='$verbindlichkeit' LIMIT 1");
        }
      }
    }



    // offene Zahlungsverkehren
    $summe = $this->app->DB->Select("SELECT SUM(d.betrag) FROM dta d WHERE d.datei <=0 AND d.lastschrift!=1");

    if($summe <=0) $summe = "0,00";
    $this->app->Tpl->Set(TAB1,"<form action=\"\" method=\"post\"><br><center><h1>Gesamt offen: $summe EUR</h1></center>");


    $this->app->YUI->TableSearch(TAB1,"ueberweisung");
    $this->app->Tpl->Add(TAB1,"<br><center>Auswahl Konto:&nbsp;
        <select name=\"konto\">".$this->app->erp->GetSelectBICKonto()."</select>&nbsp;<input type=\"submit\" value=\"Sammel&uuml;berweisung anlegen und Zahlungen als bezahlt markieren\" name=\"erzeugen\"></center></form>");


    $this->app->YUI->TableSearch(TAB2,"ueberweisungarchiv");


    $dateien = $this->app->DB->SelectArr("SELECT id FROM dta_datei WHERE status!='abgeschlossen'");
    for($idateien=0;$idateien<count($dateien);$idateien++)
    {
      $adressen = $this->app->DB->SelectArr("SELECT DISTINCT adresse FROM dta WHERE datei='".$dateien[$idateien]['id']."'");	

      for($iadressen=0;$iadressen<count($adressen);$iadressen++)
      {
        $verbindlichkeiten = $this->app->DB->SelectArr("SELECT * FROM verbindlichkeit WHERE status!='bezahlt' 
            AND adresse='".$adressen[$iadressen]['adresse']."' AND adresse > 0 AND betrag < 0");	

          for($iverbindluchkeiten=0;$iverbindlichkeiten<count($verbindlichkeiten);$iverbindlichkeiten++)
          {
            $find_adressen[] = $adressen[$iadressen]['adresse'];
          }
      }
    }

    $find_adressen = array_unique ( $find_adressen );

    if(count($find_adressen)>0)
    {	
      $this->app->Tpl->Set(MESSAGE,"<div style=\"border:5px solid #FA5858; padding: 8px;\"><h1>Hinweis: Es gibt zu Lieferanten offene Betr&auml;ge die abgezogen werden d&uuml;rfen!</h1>[TAB33]</div>");	

      $adressen_sql = implode(' OR v.adresse=',$find_adressen);

      $adresse_sql = substr($adresse_sql,3);

      $table = new EasyTable($this->app);
      $table->Query("SELECT v.id as 'Nr.', a.lieferantennummer as 'lieferant-Nr.', a.name as lieferant,v.verwendungszweck, v.betrag, v.id FROM verbindlichkeit v LEFT JOIN adresse a ON a.id=v.adresse WHERE v.status!='bezahlt' AND (".$adressen_sql.") AND v.betrag < 0");
      $table->DisplayNew(TAB33, "<a href=\"index.php?module=verbindlichkeit&action=edit&id=%value%\" target=\"_blank\"><img src=\"./themes/".$this->app->Conf->WFconf[defaulttheme]."/images/edit.png\" border=\"0\"></a>&nbsp;
          <a href=\"index.php?module=verbindlichkeit&action=bezahlt&id=%value%&cmd=sammelueberweisung\"><img src=\"./themes/".$this->app->Conf->WFconf[defaulttheme]."/images/ack.png\" border=\"0\"></a>&nbsp;");
    }

    $this->app->YUI->TableSearch(TAB3,"dta_datei_ueberweisung");

    $this->app->Tpl->Parse(PAGE,"zahlungsverkehr_ueberweisung.tpl");

  }


  function ZahlungsverkehrEinstellungen()
  {
    $this->ZahlungsverkehrMenu();
    $speichern = $this->app->Secure->GetPOST("speichern");
    if($speichern!="")
    {
      $skonto_ueberweisung_ueberziehen=$this->app->Secure->GetPOST("skonto_ueberweisung_ueberziehen");
      $this->app->erp->FirmendatenSet("skonto_ueberweisung_ueberziehen",$skonto_ueberweisung_ueberziehen);	
    }
    $this->app->Tpl->Set(TAGE,$this->app->erp->Firmendaten("skonto_ueberweisung_ueberziehen"));
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"SEPA Zahlungsverkehr");
    $this->app->Tpl->Parse(PAGE,"zahlungsverkehr_einstellungen.tpl");
  }


  function ZahlungsverkehrMenu()
  {
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"SEPA Zahlungsverkehr");
    $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=ueberweisung","&Uuml;berweisungen");
    $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=lastschrift","Lastschriften");
    $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=verbindlichkeitenladen","Verbindlichkeiten laden");
    $this->app->erp->MenuEintrag("index.php?module=zahlungsverkehr&action=einstellungen","Einstellungen");
  }


  function ZahlungsverkehrLastschrift()
  {
    $this->app->Tpl->Set(UEBERSCHRIFT,"Lastschrift&nbsp;/&nbsp;Sammel&uuml;berweisung");
    $this->ZahlungsverkehrMenu();
    $erzeugen = $this->app->Secure->GetPOST("erzeugen"); 
    $versandavis = $this->app->Secure->GetPOST("versandavis"); 
    $lastschrift= $this->app->Secure->GetPOST("lastschrift"); 
    $konto=$this->app->Secure->GetPOST("konto");

    if($versandavis!="")
    {

      $avis=$this->app->Secure->GetPOST("avis");
      $drucker=$this->app->Secure->GetPOST("drucker");
      //echo "Drucker $drucker";
      //print_r($avis);

      for($i=0;$i<count($avis);$i++)
      {
        $Brief = new ZahlungsavisPDF($this->app,$projekt);
        $Brief->GetZahlungsavis($avis[$i]);
        $tmpfile = $Brief->displayTMP();
        $this->app->printer->Drucken($drucker,$tmpfile);	
        unlink($tmpfile);
        $this->app->DB->Update("UPDATE zahlungsavis SET versendet_am=NOW(), versendet_per='brief', versendet=1 WHERE id='".$avis[$i]."' LIMIT 1");
      }
    }	

    if($erzeugen!="")
    {
      //erzeugen
      $rechnung= $this->app->Secure->GetPOST("rechnung"); 
      if(count($rechnung)>0)
      {
        $this->app->DB->Insert("INSERT INTO dta_datei (id,bezeichnung,bearbeiter,inhalt,datum,konto,status,art,firma) VALUES 
            ('','Lastschrift','".$this->app->User->GetName()."','',NOW(),'$konto','offen','lastschrift','1')");

        $dta_datei = $this->app->DB->GetInsertID();
      }

      for($i=0;$i<count($rechnung);$i++)
      {
        $adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='{$rechnung[$i]}' limit 1");
        $blz = $this->app->DB->Select("SELECT swift FROM adresse WHERE id='$adresse' LIMIT 1");
        $konto = $this->app->DB->Select("SELECT iban FROM adresse WHERE id='$adresse' LIMIT 1");

        $checkavis = $this->app->DB->Select("SELECT id FROM zahlungsavis WHERE adresse='$adresse' AND dta_datei='$dta_datei' AND dta_datei > 0 LIMIT 1");
        // wenn anzahl der rechnungen > 1 zahlungsavis anlegen
        if($checkavis<=0)
        {
          $this->app->DB->Insert("INSERT INTO zahlungsavis (id,datum,adresse,ersteller,projekt,dta_datei,bic,iban) VALUES 
              ('',NOW(),'$adresse','".$this->app->User->GetName()."','$projekt','$dta_datei','$blz','$konto')");
          $checkavis = $this->app->DB->GetInsertID();
        } 

        $this->app->DB->Insert("INSERT INTO zahlungsavis_rechnung (id,rechnung,zahlungsavis) VALUES ('','{$rechnung[$i]}','$checkavis')");

        //echo $rechnung[$i]."<br>";
        // dta erzeugen 
        //$auftrag = $this->app->DB->Select("SELECT auftrag FROM rechnung WHERE id='{$rechnung[$i]}' limit 1");
        $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' LIMIT 1");
        $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
        $mandatsreferenzaenderung = $this->app->DB->Select("SELECT mandatsreferenzaenderung FROM adresse WHERE id='$adresse' LIMIT 1");

        if($mandatsreferenzaenderung>=1) $this->app->DB->Update("UPDATE adresse SET mandatsreferenzaenderung=0 WHERE id='$adresse' LIMIT 1");
        else $mandatsreferenzaenderung=0;

        //$betrag = $this->app->DB->Select("SELECT soll*(1.0-(zahlungszielskonto/100)) FROM rechnung WHERE id='{$rechnung[$i]}' LIMIT 1");
        $betrag = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='{$rechnung[$i]}' LIMIT 1");
        $zahlungszielskonto = $this->app->DB->Select("SELECT zahlungszielskonto FROM rechnung WHERE id='{$rechnung[$i]}' LIMIT 1");
        $gruppe = $this->app->DB->Select("SELECT gruppe FROM rechnung WHERE id='{$rechnung[$i]}' LIMIT 1");
        $sonderrabatt = $this->app->DB->Select("SELECT sonderrabatt_skonto FROM gruppen WHERE id='".$gruppe."' LIMIT 1");
        //TODO SONDER
        $betrag = $betrag *(1.0-(($zahlungszielskonto + $sonderrabatt) /100));

        $vz1= "RE ".$this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='{$rechnung[$i]}' limit 1")." KD ".$kundennummer." ".$name;

        $lastschrift=1;

        $arr_adressen[]=$adresse;

        // TODO sind fuer einen Kunden mehrer Rechnungen vorhanden so sollte man nur eine Buchung erstelle und alle addieren
        // pruefen ob es fuer die adresse mit der dta_datei schon einen Eintrag gibt, wenn ja vz1 = kundennummer + Datum und betrag = betrag + betrag
        $checkdtatmp = $this->app->DB->Select("SELECT id FROM dta WHERE adresse='$adresse' AND datei='$dta_datei' AND adresse > 0 AND datei > 0 LIMIT 1");
        if($checkdtatmp <=0)
        {
          $this->app->DB->Insert("INSERT INTO dta (id,adresse,datum,name,konto,
            blz,betrag,vz1,lastschrift,kontointern,firma,datei,rechnung,
            mandatsreferenzaenderung) VALUES ('','$adresse',NOW(),'$name','$konto',
              '$blz','$betrag','$vz1','$lastschrift','$konto','".$this->app->User->GetFirma()."','$dta_datei','{$rechnung[$i]}',$mandatsreferenzaenderung)");
          $this->app->DB->Update("UPDATE zahlungsavis SET betrag=".$betrag." WHERE id='".$checkavis."' LIMIT 1");
        } else {
          $datum = date('Ymd');
          $this->app->DB->Update("UPDATE dta SET vz1='KD $kundennummer ZA $datum/$checkavis', betrag=betrag+$betrag WHERE id='$checkdtatmp' LIMIT 1");	
          $this->app->DB->Update("UPDATE zahlungsavis SET betrag=betrag+".$betrag." WHERE id='".$checkavis."' LIMIT 1");
        }
        //rechnung auf bezahlt markieren + soll auf ist
        $this->app->DB->Update("UPDATE rechnung SET schreibschutz=1,zahlungsstatus='abgebucht',dta_datei='$dta_datei' WHERE id='{$rechnung[$i]}' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
      }

      // alle gutschriften die noch rein passen mit aufnehmen von dem kunden
      for($arradri=0;$arradri<count($arr_adressen);$arradri++)
      {

        $betrag = $this->app->DB->SelectArr("SELECT id,betrag FROM dta WHERE adresse='".$arr_adressen[$arradri]."' AND datei='$dta_datei' LIMIT 1");
        $avis = $this->app->DB->Select("SELECT id FROM zahlungsavis WHERE dta_datei='$dta_datei' AND adresse='".$arr_adressen[$arradri]."' LIMIT 1");
        $gutschriften = $this->app->DB->SelectArr("SELECT id, soll FROM gutschrift WHERE adresse='".$arr_adressen[$arradri]."' AND dta_datei <=0 
            AND (status='freigegeben' OR status='versendet') 
            AND (manuell_vorabbezahlt='' OR manuell_vorabbezahlt='0000-00-00') AND (zahlungsweise='lastschrift' OR zahlungsweise='einzugsermaechtigung') ORDER by datum");

        $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='".$arr_adressen[$arradri]."' LIMIT 1");

        for($gi=0;$gi<count($gutschriften);$gi++)
        {
          //TODO SONDER
          $zahlungszielskonto = $this->app->DB->Select("SELECT zahlungszielskonto FROM gutschrift WHERE id='{$gutschriften[$gi][id]}' LIMIT 1");
          $gruppe = $this->app->DB->Select("SELECT gruppe FROM gutschrift WHERE id='{$gutschriften[$gi][id]}' LIMIT 1");
          $sonderrabatt = $this->app->DB->Select("SELECT sonderrabatt_skonto FROM gruppen WHERE id='".$gruppe."' LIMIT 1");


          //TODO SONDER
          $gutschriften[$gi][soll] = round($gutschriften[$gi][soll] *(1.0-(($zahlungszielskonto + $sonderrabatt) /100)),2);

          if(($betrag[0][betrag] - $gutschriften[$gi][soll]) >= 0)		
          {
            $this->app->DB->Update("UPDATE gutschrift SET schreibschutz=1,zahlungsstatus='bezahlt',dta_datei='$dta_datei',
                manuell_vorabbezahlt=NOW(),manuell_vorabbezahlt_hinweis=CONCAT('Zahlungsavis $datum/$avis ',manuell_vorabbezahlt_hinweis) 
                WHERE id='{$gutschriften[$gi][id]}' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

            $betrag[0][betrag] = $betrag[0][betrag] - $gutschriften[$gi][soll];

            $this->app->DB->Update("UPDATE dta SET vz1='KD $kundennummer ZA $datum/$avis', betrag='".$betrag[0][betrag]."' WHERE id='".$betrag[0][id]."' LIMIT 1");	
            $this->app->DB->Insert("INSERT INTO zahlungsavis_gutschrift (id,gutschrift,zahlungsavis) VALUES ('','{$gutschriften[$gi][id]}','$avis')");
            $this->app->DB->Update("UPDATE zahlungsavis SET betrag='".$betrag[0][betrag]."' WHERE id='".$avis."' LIMIT 1");
          }
        }
      }
    }

    // offene Zahlungsverkehren

    $summe = $this->app->DB->Select("SELECT FORMAT(SUM(r.soll*(1.0-(r.zahlungszielskonto/100))),2,'de_DE')
        FROM rechnung r, projekt p WHERE (r.zahlungsweise='lastschrift' OR r.zahlungsweise='einzugsermaechtigung') AND (r.zahlungsstatus!='bezahlt' AND r.dta_datei <= 0)                			AND r.belegnr!=0 AND r.belegnr!='' AND p.id=r.projekt");

    if($summe <=0) $summe = "0,00";

    $this->app->Tpl->Set(TAB1,"<form action=\"\" method=\"post\"><br><center><h1>Gesamt offen: $summe EUR (Zahlungsart Lastschrift)</h1></center>");
    $this->app->YUI->TableSearch(TAB1,"lastschriften");
    $this->app->Tpl->Add(TAB1,"<br><center>Auswahl Konto:&nbsp;
        <select name=\"konto\">".$this->app->erp->GetSelectBICKonto()."</select>&nbsp;<input type=\"submit\" name=\"erzeugen\" value=\"Sammellastschrift anlegen\"></center></form>");
    $summe = $this->app->DB->Select("SELECT FORMAT(SUM(r.soll*(1.0-(r.zahlungszielskonto/100))),2,'de_DE')
        FROM gutschrift r, projekt p WHERE 
        (r.zahlungsweise='lastschrift' OR r.zahlungsweise='einzugsermaechtigung') 
        AND (r.status='freigegeben' OR r.status='versendet') AND r.dta_datei <=0 
        AND (r.manuell_vorabbezahlt='' OR r.manuell_vorabbezahlt='0000-00-00') 
        AND r.belegnr!=0 AND p.id=r.projekt AND r.dta_datei <=0");


    if($summe <=0) $summe = "0,00";

    $this->app->Tpl->Set(TAB2,"<br><center><h1>Gesamt offen: $summe EUR (Zahlungsart Lastschrift)</h1></center>");
    $this->app->YUI->TableSearch(TAB2,"lastschriften_gutschriften");

    $this->app->YUI->TableSearch(TAB3,"lastschriftenarchiv");

    $this->app->Tpl->Set(TAB4,"<form action=\"\" method=\"post\">");
    $this->app->YUI->TableSearch(TAB4,"zahlungsavis");
    $this->app->Tpl->Add(TAB4,"<br><center>Auswahl Drucker:&nbsp;
        <select name=\"drucker\">".$this->app->erp->GetSelectDrucker()."</select>&nbsp;<input type=\"submit\" name=\"versandavis\" value=\"Offen Zahlungsavis senden\" onclick=\"this.form.action += 'tabs-4';\">&nbsp;<!--<i>Ist eine E-Mail Adresse hinterlegt wird die Avis an diesee versendet.</i>--></center></form>");
    $this->app->YUI->TableSearch(TAB5,"dta_datei_lastschrift");

    $this->app->Tpl->Parse(PAGE,"zahlungsverkehr_lastschrift.tpl");
  }

  function ZahlungsverkehrDTA()
  {
    $id = $this->app->Secure->GetGET("id");
    $inhalt = $this->app->DB->Select("SELECT inhalt FROM dta_datei WHERE id='$id' LIMIT 1");
    $datum= $this->app->DB->Select("SELECT datum FROM dta_datei WHERE id='$id' LIMIT 1");

    $this->app->DB->Update("UPDATE dta_datei SET status='verarbeitet' WHERE id='$id' LIMIT 1");


    header("Content-Disposition: attachment; filename=\"".$datum."_Lastschrift.txt\"");
    header("Content-type: text/plain");
    header("Cache-control: public");
    echo $inhalt;
    exit;

  }




}
?>
