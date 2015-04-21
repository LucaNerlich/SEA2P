<?php


class BestellungPDF extends Briefpapier {
  public $doctype;
  
  function BestellungPDF($app,$projekt="")
  {
    $this->app=&$app;
    //parent::Briefpapier();
    $this->doctype="bestellung";
    $this->doctypeOrig="Bestellung";
		$this->bestellungohnepreis=0;
    parent::Briefpapier($this->app,$projekt);
  } 


  function GetBestellung($id)
  {
      $adresse = $this->app->DB->Select("SELECT adresse FROM bestellung WHERE id='$id' LIMIT 1");
      //$this->setRecipientDB($adresse);
      $this->setRecipientLieferadresse($id,"bestellung");
            
      // OfferNo, customerId, OfferDate

      $kundennummer = $this->app->DB->Select("SELECT kundennummerlieferant FROM adresse WHERE id='$adresse' LIMIT 1");
			$kundennummer = $this->app->erp->ReadyForPDF($kundennummer);

      $angebot = $this->app->DB->Select("SELECT angebot FROM bestellung WHERE id='$id' LIMIT 1");
			$angebot = $this->app->erp->ReadyForPDF($angebot);

      $keineartikelnummern = $this->app->DB->Select("SELECT keineartikelnummern FROM bestellung WHERE id='$id' LIMIT 1");
      $artikelnummerninfotext = $this->app->DB->Select("SELECT artikelnummerninfotext FROM bestellung WHERE id='$id' LIMIT 1");
      $einkaeufer = $this->app->DB->Select("SELECT einkaeufer FROM bestellung WHERE id='$id' LIMIT 1");
			$einkaeufer = $this->app->erp->ReadyForPDF($einkaeufer);

      $ustid = $this->app->DB->Select("SELECT ustid FROM bestellung WHERE id='$id' LIMIT 1");
      $bestellbestaetigung = $this->app->DB->Select("SELECT bestellbestaetigung FROM bestellung WHERE id='$id' LIMIT 1");
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM bestellung WHERE id='$id' LIMIT 1");
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");
      $freitext = $this->app->DB->Select("SELECT freitext FROM bestellung WHERE id='$id' LIMIT 1");

      $ohne_briefpapier = $this->app->DB->Select("SELECT ohne_briefpapier FROM bestellung WHERE id='$id' LIMIT 1");
      $this->bestellungohnepreis = $this->app->DB->Select("SELECT bestellungohnepreis FROM bestellung WHERE id='$id' LIMIT 1");

			if($this->bestellungohnepreis)
				$this->nichtsichtbar_summe=1;

      if($ohne_briefpapier=="1")
      {
        $this->logofile = "";
        $this->briefpapier="";
        $this->briefpapier2="";
      }

      if($belegnr<=0) $belegnr = "- Entwurf";

      $this->doctypeOrig="Bestellung $belegnr";

      if($angebot=="") $angebot = "-";
      if($kundennummer=="") $kundennummer= "-";

	  if(!$this->app->erp->BestellungMitUmsatzeuer($id)){
        $this->ust_befreit=true;
      }

      $this->setCorrDetails(array("Angebot-Nr."=>$angebot,"Unsere Kunden-Nr."=>$kundennummer,"Bestelldatum"=>$datum,"Einkauf"=>$einkaeufer));
     if(!$this->app->erp->BestellungMitUmsatzeuer($id) && $ustid!="" )
      {
  //$steuer = "\nSteuerfreie innergemeinschaftliche Lieferung. Ihre USt-IdNr. $ustid Land: $land";
        $this->ust_befreit=true;
  		if($keinsteuersatz!="1")
     		$steuer = $this->app->erp->Firmendaten("eu_lieferung_vermerk");
        $steuer = str_replace('{USTID}',$ustid,$steuer);
        $steuer = str_replace('{LAND}',$land,$steuer);
      }

	 		$body=$this->app->erp->Firmendaten("bestellung_header");
      $body = $this->app->erp->ParseUserVars("bestellung",$id,$body);


      if($bestellbestaetigung)
      {
						$this->setTextDetails(array(
	  			"body"=>$body,
	  			"footer"=>"Die Bestellung ist erst nach Eingang einer Bestätigung Ihrerseits gültig. Wird die Bestellung nicht innerhalb einer Woche bestätigt verfällt diese.\n\n$freitext"));
      } else 
      {
	$this->setTextDetails(array(
	  "body"=>$body,
	  "footer"=>$this->app->erp->ParseUserVars("bestellung",$id,$this->app->erp->Firmendaten("bestellung_footer")."\n\n$freitext")));
      }
      $artikel = $this->app->DB->SelectArr("SELECT * FROM bestellung_position WHERE bestellung='$id' ORDER By sort");

      //$waehrung = $this->app->DB->Select("SELECT waehrung FROM bestellung_position WHERE bestellung='$id' LIMIT 1");
      foreach($artikel as $key=>$value)
      {

	$lieferdatum = $this->app->String->Convert($value[lieferdatum],"%1-%2-%3","%3.%2.%1");

	if($lieferdatum=="00.00.0000") $lieferdatum ="sofort";

  if($value[umsatzsteuer] != "ermaessigt") $value[umsatzsteuer] = "normal";

//	if(!$this->app->erp->BestellungMitUmsatzeuer($id)) $value[umsatzsteuer] = ""; 

	if($keineartikelnummern==1)
	  $value[bestellnummer]="siehe Artikel";

  $value[artikelnummer]=$this->app->DB->Select("SELECT nummer FROM artikel WHERE id='".$value[artikel]."' LIMIT 1");
  if($artikelnummerninfotext)
  {
    $value[beschreibung]= $value[beschreibung]."\nBest.-Nr.: ".$value[bestellnummer];
    $value[bestellnummer]=$value[artikelnummer];


  } else {
    if($value[artikelnummer]!="")
      $value[beschreibung]= $value[beschreibung]."\nUnsere Art.-Nr.: ".$value[artikelnummer];
   }

	if($value[vpe] > 1 && is_numeric($value[vpe])) {
				$value[beschreibung] = $value[beschreibung]."\nMenge in VPE: ".$value[vpe];
				$value[preis] = $value[preis]*$value[menge]/($value[menge] / $value[vpe]);
				$value[menge] = round($value[menge] / $value[vpe],2);
	}
	if($value[beschreibung]!="") $newline="\n";

	if($this->bestellungohnepreis) $value[preis] = "-";

	$this->addItem(array('currency'=>$value[waehrung],'amount'=>$value[menge],'price'=>$value[preis],
		'tax'=>$value[umsatzsteuer],
		'vpe'=>$value[vpe],
    'unit'=>$value[einheit],
		'itemno'=>$value[bestellnummer],
		'desc'=>$value[beschreibung].$newline."Lieferdatum: ".$lieferdatum,
	  "name"=>$value[bezeichnunglieferant]));
      }
      $summe = $this->app->DB->Select("SELECT SUM(menge*preis)/(if(vpe > 0,vpe,1)) FROM bestellung_position WHERE bestellung='$id'");

      $summeV = $this->app->DB->Select("SELECT SUM(menge*preis)/(if(vpe > 0,vpe,1)) FROM bestellung_position WHERE bestellung='$id' AND (umsatzsteuer='normal' || umsatzsteuer='') ")/100 * $this->app->erp->GetSteuersatzNormal(false,$id,"bestellung");
      $summeR = $this->app->DB->Select("SELECT SUM(menge*preis)/(if(vpe > 0,vpe,1)) FROM bestellung_position WHERE bestellung='$id' AND umsatzsteuer='ermaessigt'")/100 * $this->app->erp->GetSteuersatzErmaessigt(false,$id,"bestellung");
      

		if($this->bestellungohnepreis!=1)
		{
      if($this->app->erp->BestellungMitUmsatzeuer($id))
      {
				$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $summeV + $summeR,"totalTaxV"=>$summeV,"totalTaxR"=>$summeR));
      } else
      	$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe));
		}

      /* Dateiname */
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y%m%d') FROM bestellung WHERE id='$id' LIMIT 1");
      $belegnr= $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$id' LIMIT 1");
      $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
      $tmp_name = str_replace('.','',$tmp_name);

      $this->filename = $datum."_BE".$belegnr.".pdf";
      $this->setBarcode($belegnr);
  }


}
?>
