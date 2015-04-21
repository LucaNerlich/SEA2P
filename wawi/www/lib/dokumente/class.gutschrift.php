<?php


class GutschriftPDF extends Briefpapier {
  public $doctype;
  
  function GutschriftPDF($app,$projekt="")
  {
    $this->app=&$app;
    //parent::Briefpapier();
    $this->doctype="gutschrift";
    $this->doctypeOrig="Gutschrift";
    parent::Briefpapier($this->app,$projekt);
  } 


  function GetGutschrift($id)
  {
      $adresse = $this->app->DB->Select("SELECT adresse FROM gutschrift WHERE id='$id' LIMIT 1");
      //$this->setRecipientDB($adresse);
      $this->setRecipientLieferadresse($id,"gutschrift");

      // OfferNo, customerId, OfferDate

      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
      $rechnungid = $this->app->DB->Select("SELECT rechnungid FROM gutschrift WHERE id='$id' LIMIT 1");
      $rechnung = $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$rechnungid' LIMIT 1");
      $buchhaltung= $this->app->DB->Select("SELECT buchhaltung FROM gutschrift WHERE id='$id' LIMIT 1");
      $bearbeiter= $this->app->DB->Select("SELECT bearbeiter FROM gutschrift WHERE id='$id' LIMIT 1");
      $bearbeiter = $this->app->erp->ReadyForPDF($bearbeiter);
      $vertrieb= $this->app->DB->Select("SELECT vertrieb FROM gutschrift WHERE id='$id' LIMIT 1");
      $vertrieb = $this->app->erp->ReadyForPDF($vertrieb);
      $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM gutschrift WHERE id='$id' LIMIT 1");
      $lieferschein = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
      $bestellbestaetigung = $this->app->DB->Select("SELECT bestellbestaetigung FROM gutschrift WHERE id='$id' LIMIT 1");
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM gutschrift WHERE id='$id' LIMIT 1");
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM gutschrift WHERE id='$id' LIMIT 1");
      $freitext = $this->app->DB->Select("SELECT freitext FROM gutschrift WHERE id='$id' LIMIT 1");
      $ustid = $this->app->DB->Select("SELECT ustid FROM gutschrift WHERE id='$id' LIMIT 1");
      $stornorechnung = $this->app->DB->Select("SELECT stornorechnung FROM gutschrift WHERE id='$id' LIMIT 1");
      $keinsteuersatz = $this->app->DB->Select("SELECT keinsteuersatz FROM gutschrift WHERE id='$id' LIMIT 1");
      $land = $this->app->DB->Select("SELECT land FROM gutschrift WHERE id='$id' LIMIT 1");
      $this->anrede = $this->app->DB->Select("SELECT typ FROM gutschrift WHERE id='$id' LIMIT 1");
      $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM gutschrift WHERE id='$id' LIMIT 1");
      $zahlungsstatus = $this->app->DB->Select("SELECT zahlungsstatus FROM gutschrift WHERE id='$id' LIMIT 1");
      $zahlungszieltage = $this->app->DB->Select("SELECT zahlungszieltage FROM gutschrift WHERE id='$id' LIMIT 1");
      $zahlungszielskonto = $this->app->DB->Select("SELECT zahlungszielskonto FROM gutschrift WHERE id='$id' LIMIT 1");
      $this->projekt = $this->app->DB->Select("SELECT projekt FROM gutschrift WHERE id='$id' LIMIT 1");

      if($vertrieb==$bearbeiter) $vertrieb=""; 

      $zahlungdatum = $this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD(datum, INTERVAL $zahlungszieltage DAY),'%d.%m.%Y') FROM gutschrift WHERE id='$id' LIMIT 1");

      $ohne_briefpapier = $this->app->DB->Select("SELECT ohne_briefpapier FROM gutschrift WHERE id='$id' LIMIT 1");
      $ihrebestellnummer = $this->app->DB->Select("SELECT ihrebestellnummer FROM gutschrift WHERE id='$id' LIMIT 1");
      $ihrebestellnummer = $this->app->erp->ReadyForPDF($ihrebestellnummer);


      if($ohne_briefpapier=="1")
      {
        $this->logofile = "";
        $this->briefpapier="";
        $this->briefpapier2="";
      }

//      $zahlungsweise = strtolower($zahlungsweise);

			switch($zahlungsweise)
			{
				case "lastschrift": $zahlungsweisetext = "\nDer Betrag wird mit offenen Lastschriften verrechnet."; break;
				case "einzugsermaechtigung": $zahlungsweisetext = "\nDer Betrag wird mit offenen Lastschriften verrechnet."; break;
				//default: $zahlungsweisetext = "\nDer Betrag wird verrechnet oder überwiesen."; break;
			}	


      if($zahlungszielskonto>0) $zahlungsweisetext .= "\nSkonto $zahlungszielskonto% aus Zahlungskonditionen";

/*
      if($zahlungsweise=="gutschrift"&&$zahlungsstatus!="bezahlt")
      {
	$zahlungsweisetext = "Gutschrift zahlbar innerhalb $zahlungszieltage Tagen bis zum $zahlungdatum.";
	if($zahlungszielskonto!=0)
	  $zahlungsweisetext .=" (Skonto $zahlungszielskonto % innerhalb $zahlungszieltageskonto Tagen)";	
      } elseif($zahlungsweise=="bar")
      {
	$zahlungsweisetext = "Gutschrift: Barzahlung";
      } elseif($zahlungsweise=="nachnahme")
      {
	$zahlungsweisetext = "Gutschrift: Bezahlung per Nachnahme";
      } else {
	if($zahlungsstatus!="bezahlt")
	{
	  $zahlungsweisetext = "Die Gutschrift muss per Vorkasse bezahl werden. Zahlungsweise: ".ucfirst($zahlungsweise);
	}
	else
	{
	  $zahlungsweisetext = "Die Gutschrift wurde per Vorkasse (".ucfirst($zahlungsweise).") bezahlt";
	}
      }
 */     
      
      if($belegnr<=0) $belegnr = "- Entwurf";


			if($stornorechnung)
      	$this->doctypeOrig=$this->app->erp->Firmendaten("bezeichnungstornorechnung")." $belegnr";
			else
      	$this->doctypeOrig="Gutschrift $belegnr";

      if($gutschrift=="") $gutschrift = "-";
      if($kundennummer=="") $kundennummer= "-";

      if($auftrag==0) $auftrag = "-";
      if($lieferschein==0) $lieferschein= "-";

	//$this->setCorrDetails(array("Auftrag"=>$auftrag,"Datum"=>$datum,"Ihre Kunden-Nr."=>$kundennummer,"Lieferschein"=>$lieferschein,"Buchhaltung"=>$buchhaltung));
	if($rechnung >0)
		$this->setCorrDetails(array("Rechnung"=>$rechnung,$this->app->erp->Firmendaten("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,"Datum"=>$datum,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Firmendaten("auftrag_bezeichnung_bearbeiter")=>$bearbeiter,$this->app->erp->Firmendaten("auftrag_bezeichnung_vertrieb")=>$vertrieb));
	else
		$this->setCorrDetails(array("Datum"=>$datum,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Firmendaten("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,$this->app->erp->Firmendaten("auftrag_bezeichnung_bearbeiter")=>$bearbeiter,$this->app->erp->Firmendaten("auftrag_bezeichnung_vertrieb")=>$vertrieb));

      if(!$this->app->erp->GutschriftMitUmsatzeuer($id) && $keinsteuersatz!="1")
			{
       if($this->app->erp->Export($land))
          $steuer = $this->app->erp->Firmendaten("export_lieferung_vermerk");
        else
          $steuer = $this->app->erp->Firmendaten("eu_lieferung_vermerk");
        $steuer = str_replace('{USTID}',$ustid,$steuer);
        $steuer = str_replace('{LAND}',$land,$steuer);
			}

			$gutschrift_header=$this->app->erp->Firmendaten("gutschrift_header");
      $gutschrift_header = $this->app->erp->ParseUserVars("gutschrift",$id,$gutschrift_header);

		      if($stornorechnung)
					{
						$gutschrift_header = str_replace('{ART}',"Stronorechnung",$gutschrift_header);
     			} else {
						$gutschrift_header = str_replace('{ART}',"Gutschrift",$gutschrift_header);
					} 
     			$this->setTextDetails(array(
  					"body"=>$gutschrift_header,
  					"footer"=>"$freitext"."\r\n".$this->app->erp->ParseUserVars("gutschrift",$id,$this->app->erp->Firmendaten("gutschrift_footer"))."\r\n$zahlungsweisetext\r\n$steuer"));

      $artikel = $this->app->DB->SelectArr("SELECT * FROM gutschrift_position WHERE gutschrift='$id' ORDER By sort");

      if(!$this->app->erp->GutschriftMitUmsatzeuer($id)) $this->ust_befreit=true;

      $summe_rabatt = $this->app->DB->Select("SELECT SUM(rabatt) FROM gutschrift_position WHERE gutschrift='$id'");
      if($summe_rabatt > 0) $this->rabatt=1;

      if($this->app->erp->Firmendaten("modul_verband")=="1") $this->rabatt=1; 

      //$waehrung = $this->app->DB->Select("SELECT waehrung FROM gutschrift_position WHERE gutschrift='$id' LIMIT 1");
      foreach($artikel as $key=>$value)
      {
        if($value[umsatzsteuer] != "ermaessigt") $value[umsatzsteuer] = "normal";

				// negative Darstellung bei Stornorechnung
				if($stornorechnung) $value[preis] = $value[preis] *-1;

        $this->addItem(array('currency'=>$value[waehrung],
          'amount'=>$value[menge],
          'price'=>$value[preis],
          'tax'=>$value[umsatzsteuer],
          'itemno'=>$value[nummer],
          'unit'=>$value[einheit],
          'desc'=>$value[beschreibung],
          "name"=>ltrim($value[bezeichnung]),
          'grundrabatt'=>$value[grundrabatt],
          'rabatt1'=>$value[rabatt1],
          'rabatt2'=>$value[rabatt2],
          'rabatt3'=>$value[rabatt3],
          'rabatt4'=>$value[rabatt4],
          'rabatt5'=>$value[rabatt5],
          "rabatt"=>$value[rabatt]));

          $netto_gesamt = $value[menge]*($value[preis]-($value[preis]/100*$value[rabatt]));
          $summe = $summe + $netto_gesamt;

          if($value[umsatzsteuer]=="" || $value[umsatzsteuer]=="normal")
          {
            $summeV = $summeV + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzNormal(false,$id,"gutschrift"));
          }
          else {
            $summeR = $summeR + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzErmaessigt(false,$id,"gutschrift"));
          }

      }
/*
      $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM gutschrift_position WHERE gutschrift='$id'");

      $summeV = $this->app->DB->Select("SELECT SUM(menge*preis) FROM gutschrift_position WHERE gutschrift='$id' AND (umsatzsteuer='normal' or umsatzsteuer='')")/100 * 19;
      $summeR = $this->app->DB->Select("SELECT SUM(menge*preis) FROM gutschrift_position WHERE gutschrift='$id' AND umsatzsteuer='ermaessigt'")/100 * 7;
 */     
      if($this->app->erp->GutschriftMitUmsatzeuer($id))
      {
				$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $summeV + $summeR,"totalTaxV"=>$summeV,"totalTaxR"=>$summeR));
      } else
      	$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe));

      /* Dateiname */
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y%m%d') FROM gutschrift WHERE id='$id' LIMIT 1");
      $belegnr= trim($this->app->DB->Select("SELECT belegnr FROM gutschrift WHERE id='$id' LIMIT 1"));
      $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
      $tmp_name = str_replace('.','',$tmp_name);

			if($stornorechnung)
      	$this->filename = $datum."_STORNO_".$belegnr.".pdf";
			else
      	$this->filename = $datum."_GS".$belegnr.".pdf";

     $this->setBarcode($belegnr);
  }


}
?>
