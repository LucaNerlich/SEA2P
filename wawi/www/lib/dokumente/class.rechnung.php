<?php


class RechnungPDF extends Briefpapier {
  public $doctype;
  public $doctypeid;
  
  function RechnungPDF($app,$projekt="")
  {
    $this->app=&$app;
    //parent::Briefpapier();
    $this->doctype="rechnung";
    $this->doctypeOrig="Rechnung";
    parent::Briefpapier($this->app,$projekt);
  } 


  function GetRechnung($id,$als="",$doppel=0)
  {
      $this->doctypeid=$id;
      $adresse = $this->app->DB->Select("SELECT adresse FROM rechnung WHERE id='$id' LIMIT 1");
//      $this->setRecipientDB($adresse);
      $this->setRecipientLieferadresse($id,"rechnung");

      // OfferNo, customerId, OfferDate

      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
      $auftrag= $this->app->DB->Select("SELECT auftrag FROM rechnung WHERE id='$id' LIMIT 1");
      $buchhaltung= $this->app->DB->Select("SELECT buchhaltung FROM rechnung WHERE id='$id' LIMIT 1");
      $bearbeiter= $this->app->DB->Select("SELECT bearbeiter FROM rechnung WHERE id='$id' LIMIT 1");
			$bearbeiter = $this->app->erp->ReadyForPDF($bearbeiter);
      $vertrieb= $this->app->DB->Select("SELECT vertrieb FROM rechnung WHERE id='$id' LIMIT 1");
			$vertrieb = $this->app->erp->ReadyForPDF($vertrieb);
      $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM rechnung WHERE id='$id' LIMIT 1");
      $lieferscheinid = $lieferschein;
      $this->projekt = $this->app->DB->Select("SELECT projekt FROM rechnung WHERE id='$id' LIMIT 1");
      $lieferschein = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
      $bestellbestaetigung = $this->app->DB->Select("SELECT bestellbestaetigung FROM rechnung WHERE id='$id' LIMIT 1");
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM rechnung WHERE id='$id' LIMIT 1");
      $mahnwesen_datum = $this->app->DB->Select("SELECT DATE_FORMAT(mahnwesen_datum,'%d.%m.%Y') FROM rechnung WHERE id='$id' LIMIT 1");
      $lieferdatum = $this->app->DB->Select("SELECT DATE_FORMAT(lieferdatum,'%d.%m.%Y') FROM rechnung WHERE id='$id' LIMIT 1");
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");

			if($doppel!=1)
      	$doppel = $this->app->DB->Select("SELECT doppel FROM rechnung WHERE id='$id' LIMIT 1");

      $freitext = $this->app->DB->Select("SELECT freitext FROM rechnung WHERE id='$id' LIMIT 1");
      $ustid = $this->app->DB->Select("SELECT ustid FROM rechnung WHERE id='$id' LIMIT 1");
      $this->anrede = $this->app->DB->Select("SELECT typ FROM rechnung WHERE id='$id' LIMIT 1");
      $keinsteuersatz = $this->app->DB->Select("SELECT keinsteuersatz FROM rechnung WHERE id='$id' LIMIT 1");
      $soll = $this->app->DB->Select("SELECT soll FROM rechnung WHERE id='$id' LIMIT 1");
      $ist = $this->app->DB->Select("SELECT ist FROM rechnung WHERE id='$id' LIMIT 1");
      $land = $this->app->DB->Select("SELECT land FROM rechnung WHERE id='$id' LIMIT 1");
      $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM rechnung WHERE id='$id' LIMIT 1");
      $zahlungsstatus = $this->app->DB->Select("SELECT zahlungsstatus FROM rechnung WHERE id='$id' LIMIT 1");
      $zahlungszieltage = $this->app->DB->Select("SELECT zahlungszieltage FROM rechnung WHERE id='$id' LIMIT 1");
      $zahlungszieltageskonto = $this->app->DB->Select("SELECT zahlungszieltageskonto FROM rechnung WHERE id='$id' LIMIT 1");
      $zahlungszielskonto = $this->app->DB->Select("SELECT zahlungszielskonto FROM rechnung WHERE id='$id' LIMIT 1");
      $ohne_briefpapier = $this->app->DB->Select("SELECT ohne_briefpapier FROM rechnung WHERE id='$id' LIMIT 1");
      $ihrebestellnummer = $this->app->DB->Select("SELECT ihrebestellnummer FROM rechnung WHERE id='$id' LIMIT 1");
			$ihrebestellnummer = $this->app->erp->ReadyForPDF($ihrebestellnummer);

			if($ohne_briefpapier=="1")
			{
			 	$this->logofile = "";
      	$this->briefpapier="";
      	$this->briefpapier2="";
			}

      $zahlungdatum = $this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD(datum, INTERVAL $zahlungszieltage DAY),'%d.%m.%Y') FROM rechnung WHERE id='$id' LIMIT 1");
      $zahlungszielskontodatum = $this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD(datum, INTERVAL $zahlungszieltageskonto DAY),'%d.%m.%Y') FROM rechnung WHERE id='$id' LIMIT 1");

      if(!$this->app->erp->RechnungMitUmsatzeuer($id)){
        $this->ust_befreit=true;
      }


      $zahlungsweise = strtolower($zahlungsweise);
      //if($zahlungsweise=="rechnung"&&$zahlungsstatus!="bezahlt")
      if($zahlungsweise=="rechnung" || $zahlungsweise=="einzugsermaechtigung" || $zahlungsweise=="lastschrift")
      {

				if($zahlungsweise=="rechnung")
				{
					if($zahlungszieltage==0){
				 		$zahlungsweisetext = $this->app->erp->Firmendaten("zahlung_rechnung_sofort_de");
						if($zahlungsweisetext=="") $zahlungsweisetext ="Rechnung zahlbar sofort. ";
					}
					else {
						$zahlungsweisetext = $this->app->erp->Firmendaten("zahlung_rechnung_de");
						if($zahlungsweisetext=="") $zahlungsweisetext ="Rechnung zahlbar innerhalb von {ZAHLUNGSZIELTAGE} Tagen bis zum {ZAHLUNGBISDATUM}. ";
						$zahlungsweisetext = str_replace("{ZAHLUNGSZIELTAGE}",$zahlungszieltage,$zahlungsweisetext);
						$zahlungsweisetext = str_replace("{ZAHLUNGBISDATUM}",$zahlungdatum,$zahlungsweisetext);
//					$zahlungsweisetext = "Rechnung zahlbar innerhalb $zahlungszieltage Tage bis zum $zahlungdatum netto.";
					}

				if($zahlungszielskonto!=0)
	  			$zahlungsweisetext .=" (Skonto $zahlungszielskonto % innerhalb $zahlungszieltageskonto Tage bis zum $zahlungszielskontodatum)";	

				} else {
					//lastschrift
					$zahlungsweisetext = $this->app->erp->Firmendaten("zahlung_".$zahlungsweise."_de");
					if($zahlungsweisetext=="") $zahlungsweisetext ="Der Betrag wird von Ihrem Konto abgebucht.";
					if($zahlungszielskonto!=0)
	  				$zahlungsweisetext .="\r\nSkonto $zahlungszielskonto % aus Zahlungskonditionen";	
				}
					
      } 
			else {
					$zahlungsweisetext = $this->app->erp->Firmendaten("zahlung_".$zahlungsweise."_de");
          if($zahlungsweisetext=="" || $zahlungsweise=="vorkasse")
	  				$zahlungsweisetext = "Bezahlung per ".ucfirst($zahlungsweise);
      }


      if($belegnr<=0) $belegnr = "- Proforma";
			else {
				if($doppel==1 || $als=="doppel")
					$belegnr .= " (Kopie)";
			}
 
      if($als=="zahlungserinnerung") 
      $this->doctypeOrig="Zahlungserinnerung vom $mahnwesen_datum";
      else if($als=="mahnung1") 
      $this->doctypeOrig="1. Mahnung vom $mahnwesen_datum";
      else if($als=="mahnung2") 
      $this->doctypeOrig="2. Mahnung vom $mahnwesen_datum";
      else if($als=="mahnung3") 
      $this->doctypeOrig="3. Mahnung vom $mahnwesen_datum";
      else if($als=="inkasso") 
      $this->doctypeOrig="Inkasso-Mahnung vom $mahnwesen_datum";
      else
      $this->doctypeOrig="Rechnung $belegnr";

      $this->zusatzfooter = " (RE$belegnr)";

      if($rechnung=="") $rechnung = "-";
      if($kundennummer=="") $kundennummer= "-";

      if($auftrag==0) $auftrag = "-";
      if($lieferschein==0) $lieferschein= "-";

      $datumlieferschein = $this->app->DB->Select("SELECT DATE_FORMAT(datum, '%d.%m.%Y') 
				FROM lieferschein WHERE id='$lieferscheinid' LIMIT 1");

			if($datumlieferschein=="00.00.0000") $datumlieferschein = $datum;
			if($lieferdatum=="00.00.0000") $lieferdatum = $datum;
			if($mahnwesen_datum=="00.00.0000") $mahnwesen_datum = "";

        if($vertrieb!=$bearbeiter)
        {
//* start
			if($lieferschein!='-')
			{
				if($auftrag!="-")
				$this->setCorrDetails(array("Auftrag"=>$auftrag,"Rechnungsdatum"=>$datum,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Firmendaten("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
						"Lieferschein"=>$lieferschein,"Lieferdatum"=>$datumlieferschein,
$this->app->erp->Firmendaten("auftrag_bezeichnung_bearbeiter")=>$bearbeiter,$this->app->erp->Firmendaten("auftrag_bezeichnung_vertrieb")=>$vertrieb
					));
				else
				$this->setCorrDetails(array("Rechnungsdatum"=>$datum,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Firmendaten("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
					"Lieferschein"=>$lieferschein,"Lieferdatum"=>$datumlieferschein,
$this->app->erp->Firmendaten("auftrag_bezeichnung_bearbeiter")=>$bearbeiter,$this->app->erp->Firmendaten("auftrag_bezeichnung_vertrieb")=>$vertrieb
					));
			}
			else {
				if($auftrag!="-")
					$this->setCorrDetails(array("Auftrag"=>$auftrag,"Datum"=>$datum,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Firmendaten("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
			"Lieferung"=>$lieferdatum,
		$this->app->erp->Firmendaten("auftrag_bezeichnung_bearbeiter")=>$bearbeiter,$this->app->erp->Firmendaten("auftrag_bezeichnung_vertrieb")=>$vertrieb
				));
				else
					$this->setCorrDetails(array("Datum"=>$datum,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Firmendaten("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
					"Lieferung"=>$lieferdatum,
					$this->app->erp->Firmendaten("auftrag_bezeichnung_bearbeiter")=>$bearbeiter,$this->app->erp->Firmendaten("auftrag_bezeichnung_vertrieb")=>$vertrieb
						));
			}
//*ende hack
} else {
//start hack
			if($lieferschein!='-')
			{
				if($auftrag!="-")
				$this->setCorrDetails(array("Auftrag"=>$auftrag,"Rechnungsdatum"=>$datum,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Firmendaten("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
						"Lieferschein"=>$lieferschein,"Lieferdatum"=>$datumlieferschein,
$this->app->erp->Firmendaten("auftrag_bezeichnung_bearbeiter")=>$bearbeiter
					));
				else
				$this->setCorrDetails(array("Rechnungsdatum"=>$datum,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Firmendaten("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
					"Lieferschein"=>$lieferschein,"Lieferdatum"=>$datumlieferschein,
$this->app->erp->Firmendaten("auftrag_bezeichnung_bearbeiter")=>$bearbeiter
					));
			}
			else {
				if($auftrag!="-")
					$this->setCorrDetails(array("Auftrag"=>$auftrag,"Datum"=>$datum,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Firmendaten("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
			"Lieferung"=>$lieferdatum,
		$this->app->erp->Firmendaten("auftrag_bezeichnung_bearbeiter")=>$bearbeiter
				));
				else
					$this->setCorrDetails(array("Datum"=>$datum,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Firmendaten("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,
					"Lieferung"=>$lieferdatum,
					"Ansprechpartner"=>$buchhaltung
						));
			}
//ende hack

}
      //if(!$this->app->erp->RechnungMitUmsatzeuer($id) && $ustid!="" )
      if(!$this->app->erp->RechnungMitUmsatzeuer($id) && $keinsteuersatz!="1")
      {
        $this->ust_befreit=true;
				if($keinsteuersatz!="1"){
					if($this->app->erp->Export($land))
						$steuer = $this->app->erp->Firmendaten("export_lieferung_vermerk");
					else
						$steuer = $this->app->erp->Firmendaten("eu_lieferung_vermerk");
					$steuer = str_replace('{USTID}',$ustid,$steuer);
					$steuer = str_replace('{LAND}',$land,$steuer);
				}
      }

      if($als!="")
      {
        $body = $this->app->erp->MahnwesenBody($id,$als);
				$footer = $this->app->erp->Firmendaten("rechnung_footer");
      }
      else {
        $body = $this->app->erp->Firmendaten("rechnung_header");
				$body = $this->app->erp->ParseUserVars("rechnung",$id,$body);
        $footer = "$freitext"."\r\n".$this->app->erp->ParseUserVars("rechnung",$id,$this->app->erp->Firmendaten("rechnung_footer")."\r\n$steuer\r\n$zahlungsweisetext");
      }

			$this->setTextDetails(array(
	  		"body"=>$body,
	  		"footer"=>$footer));

      $artikel = $this->app->DB->SelectArr("SELECT * FROM rechnung_position WHERE rechnung='$id' ORDER By sort");
      $summe_rabatt = $this->app->DB->Select("SELECT SUM(rabatt) FROM rechnung_position WHERE rechnung='$id'");
      if($summe_rabatt > 0) $this->rabatt=1;

      if($this->app->erp->Firmendaten("modul_verband")=="1") $this->rabatt=1; 

      foreach($artikel as $key=>$value)
      {
				if($value[umsatzsteuer] != "ermaessigt") $value[umsatzsteuer] = "normal";

				$limit = 60;	
				$summary= $value[bezeichnung];
				if (strlen($summary) > $limit)
				{
	  			$value[desc]= $value[bezeichnung];
	  			$value[bezeichnung] = substr($summary, 0, strrpos(substr($summary, 0, $limit), ' ')) . '...';
				}

       $value['herstellernummer'] = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value[artikel]."' LIMIT 1");
        $value['hersteller'] = $this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value[artikel]."' LIMIT 1");


  			if($value[explodiert_parent_artikel] > 0)
        {
          $check_ausblenden = $this->app->DB->Select("SELECT keineeinzelartikelanzeigen FROM artikel WHERE id='".$value[explodiert_parent_artikel]."' LIMIT 1");
        } else $check_ausblenden=0;

        if($check_ausblenden!=1)
        {
        $this->addItem(array('currency'=>$value[waehrung],
          'amount'=>$value[menge],
          'price'=>$value[preis],
          'tax'=>$value[umsatzsteuer],
          'itemno'=>$value[nummer],
          'unit'=>$value[einheit],
          'desc'=>$value[beschreibung],
 					'hersteller'=>$value[hersteller],
          'herstellernummer'=>trim($value[herstellernummer]),
          'grundrabatt'=>$value[grundrabatt],
          'rabatt1'=>$value[rabatt1],
          'rabatt2'=>$value[rabatt2],
          'rabatt3'=>$value[rabatt3],
          'rabatt4'=>$value[rabatt4],
          'rabatt5'=>$value[rabatt5],
          "name"=>ltrim($value[bezeichnung]),
          "rabatt"=>$value[rabatt]));
					}

          $netto_gesamt = $value[menge]*($value[preis]-($value[preis]/100*$value[rabatt]));
          $summe = $summe + $netto_gesamt;

          if($value[umsatzsteuer]=="" || $value[umsatzsteuer]=="normal")
          {
            $summeV = $summeV + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzNormal(false,$id,"rechnung"));
          }
          else {
            $summeR = $summeR + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzErmaessigt(false,$id,"rechnung"));
          }

      
			}
/*
      $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM rechnung_position WHERE rechnung='$id'");
      $summeV = $this->app->DB->Select("SELECT SUM(menge*preis) FROM rechnung_position WHERE rechnung='$id' AND (umsatzsteuer!='ermaessigt')")/100 * 19;
      $summeR = $this->app->DB->Select("SELECT SUM(menge*preis) FROM rechnung_position WHERE rechnung='$id' AND umsatzsteuer='ermaessigt'")/100 * 7;
*/     
      if($this->app->erp->RechnungMitUmsatzeuer($id))
      {
				$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $summeV + $summeR,"totalTaxV"=>$summeV,"totalTaxR"=>$summeR));
      } else
			{
      	$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe));
			}

      /* Dateiname */
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y%m%d') FROM rechnung WHERE id='$id' LIMIT 1");
      $belegnr= $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");
      $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
      $tmp_name = str_replace('.','',$tmp_name);

      if($als=="")
      $this->filename = $datum."_RE".$belegnr.".pdf";
      else
      $this->filename = $datum."_MA".$belegnr.".pdf";

      $this->setBarcode($belegnr);
  }


}
?>
