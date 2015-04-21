<?php


class ZahlungsavisPDF extends Briefpapier {
  public $doctype;
  public $doctypeid;
  
  function ZahlungsavisPDF($app,$projekt="")
  {
    $this->app=&$app;
    //parent::Briefpapier();
    $this->doctype="zahlungsavis";
    $this->doctypeOrig="Zahlungsavis";
    parent::Briefpapier($this->app,$projekt);
  } 


  function GetZahlungsavis($id,$als="",$doppel=0)
  {
      $this->doctypeid=$id;
      $adresse = $this->app->DB->Select("SELECT adresse FROM zahlungsavis WHERE id='$id' LIMIT 1");
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y%m%d') FROM zahlungsavis WHERE id='$id' LIMIT 1");
      $iban = $this->app->DB->Select("SELECT iban FROM zahlungsavis WHERE id='$id' LIMIT 1");
      $bic = $this->app->DB->Select("SELECT iban FROM zahlungsavis WHERE id='$id' LIMIT 1");
      $dta_datei = $this->app->DB->Select("SELECT dta_datei FROM zahlungsavis WHERE id='$id' LIMIT 1");
      $konto = $this->app->DB->Select("SELECT konto FROM dta_datei WHERE id='$dta_datei' LIMIT 1");
    	$glaeubiger = trim($this->app->DB->Select("SELECT glaeubiger FROM konten WHERE id='$konto' LIMIT 1"));
			
      $this->setRecipientLieferadresse($adresse,"adresse");

      $this->doctypeOrig="Zahlungsavis vom $datum/$id";

      // OfferNo, customerId, OfferDate

      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
      $mandatsreferenz = preg_replace('/[^A-Za-z0-9]+/', '', $kundennummer);

			$bearbeiter = $this->app->erp->ReadyForPDF($bearbeiter);
			$vertrieb = $this->app->erp->ReadyForPDF($vertrieb);

//      $body = $this->app->erp->Firmendaten("rechnung_header");
//			$body = $this->app->erp->ParseUserVars("rechnung",$id,$body);

//			$footer = "Der Betrag wird innerhalb 7-10 Tagen von dem Konto $iban abgebucht.";

			//r.soll*(1.0-(r.zahlungszielskonto/100))

      $rechnungen = $this->app->DB->SelectArr("SELECT r.belegnr, r.soll,r.zahlungszielskonto, r.gruppe, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, r.zahlungszielskonto FROM zahlungsavis_rechnung za LEFT JOIN rechnung r ON r.id=za.rechnung WHERE za.zahlungsavis='$id' ORDER by r.datum");
      foreach($rechnungen as $key=>$value)
      {
				$sonderrabatt_skonto = 0;
				if($value[gruppe] > 0)
				{
				 	$sonderrabatt_skonto = $this->app->DB->Select("SELECT sonderrabatt_skonto FROM gruppen WHERE id='".$value[gruppe]."' LIMIT 1");
				}
				if($value[zahlungszielskonto] > 0) $value[beschreibung] = " mit Skonto ".$value[zahlungszielskonto]."%";
				else $value[beschreibung] = "";

				if($sonderrabatt_skonto > 0) $value[beschreibung] .= " mit Sonderrabatt ".$sonderrabatt_skonto."%";

	
        $this->addItem(array('currency'=>$value[waehrung],
          'amount'=>1,
          'price'=>$value[soll]*(1.0-(($value[zahlungszielskonto]+$sonderrabatt_skonto)/100)),
          'tax'=>$value[umsatzsteuer],
          'itemno'=>$value[belegnr],
          'unit'=>$value[einheit],
          'desc'=>$value[beschreibung],
 					'hersteller'=>$value[hersteller],
          'herstellernummer'=>trim($value[herstellernummer]),
          "name"=>ltrim("Rechnung vom ".$value[vom]),
          "rabatt"=>$value[rabatt]));

          $netto_gesamt = $value[soll]*(1.0-(($value[zahlungszielskonto]+$sonderrabatt_skonto)/100));
          $summe = $summe + $netto_gesamt;
			}
 $gutschriften = $this->app->DB->SelectArr("SELECT r.belegnr, r.soll, r.zahlungszielskonto, r.gruppe, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom,r.zahlungszielskonto FROM zahlungsavis_gutschrift za LEFT JOIN gutschrift r ON r.id=za.gutschrift WHERE za.zahlungsavis='$id' ORDER by r.datum");
      foreach($gutschriften as $key=>$value)
      {
				$sonderrabatt_skonto = 0;
				if($value[gruppe] > 0)
				{
				 	$sonderrabatt_skonto = $this->app->DB->Select("SELECT sonderrabatt_skonto FROM gruppen WHERE id='".$value[gruppe]."' LIMIT 1");
				}

				if($value[zahlungszielskonto] > 0) $value[beschreibung] = " mit Skonto ".$value[zahlungszielskonto]."%";
				else $value[beschreibung] = "";
				if($sonderrabatt_skonto > 0) $value[beschreibung] .= " mit Sonderrabatt ".$sonderrabatt_skonto."%";

        $this->addItem(array('currency'=>$value[waehrung],
          'amount'=>1,
          'price'=>$value[soll]*(1.0-(($value[zahlungszielskonto]+$sonderrabatt_skonto)/100))*-1,
          'tax'=>$value[umsatzsteuer],
          'itemno'=>$value[belegnr],
          'unit'=>$value[einheit],
          'desc'=>$value[beschreibung],
 					'hersteller'=>$value[hersteller],
          'herstellernummer'=>trim($value[herstellernummer]),
          "name"=>ltrim("Gutschrift vom ".$value[vom]),
          "rabatt"=>$value[rabatt]));

          $netto_gesamt = $value[soll]*(1.0-(($value[zahlungszielskonto]+$sonderrabatt_skonto)/100));
          $summe = $summe - $netto_gesamt;
			}
				$body = "Sehr geehrter Kunde,

mittels SEPA Lastschrift ziehen wir zum SEPA Mandat $kundennummer mit der SEPA Gläubiger Identifikationsnummer $glaeubiger Forderungen in Höhe von ".number_format($summe,2,',','.')." EUR in 7-10 Tagen zu Lasten ihrer Bankverbindung mit der IBAN Kontonummer $iban (BIC: $bic) ein und bitten rechtzeitig für eine entsprechende Kontodeckung zu sorgen.

";
	
			$this->setTextDetails(array(
	  		"body"=>$body,
	  		"footer"=>$footer));

      	$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe));

      /* Dateiname */
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y%m%d') FROM rechnung WHERE id='$id' LIMIT 1");
      $belegnr= $this->app->DB->Select("SELECT belegnr FROM rechnung WHERE id='$id' LIMIT 1");
      $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
      $tmp_name = str_replace('.','',$tmp_name);

      $this->filename = $datum."_ZA".$belegnr.".pdf";

      $this->setBarcode($belegnr);
  }


}
?>
