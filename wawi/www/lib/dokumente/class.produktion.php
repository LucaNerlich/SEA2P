<?php


class ProduktionPDF extends Briefpapier {
  public $doctype;
  
  function ProduktionPDF($app,$proforma="")
  {
    $this->app=&$app;
    //parent::Briefpapier();
    if($proforma=="")
    {
    $this->doctypeOrig="Produktion";
    $this->doctype="produktion";
    }
    else
    { 
    $this->doctypeOrig="Proformarechnung";
    $this->doctype="proforma";
    }
    parent::Briefpapier($this->app);
		$this->logofile="";
  } 


  function GetProduktion($id)
  {
      $adresse = $this->app->DB->Select("SELECT adresse FROM produktion WHERE id='$id' LIMIT 1");
      //$this->setRecipientDB($adresse);
      $this->setRecipientLieferadresse($id,"produktion");


      // OfferNo, customerId, OfferDate

      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");

      $anfrage= $this->app->DB->Select("SELECT angebot FROM produktion WHERE id='$id' LIMIT 1");
      $vertrieb= $this->app->DB->Select("SELECT vertrieb FROM produktion WHERE id='$id' LIMIT 1");
      $bestellbestaetigung = $this->app->DB->Select("SELECT bestellbestaetigung FROM produktion WHERE id='$id' LIMIT 1");
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM produktion WHERE id='$id' LIMIT 1");
      $land = $this->app->DB->Select("SELECT land FROM produktion WHERE id='$id' LIMIT 1");
      $ustid = $this->app->DB->Select("SELECT ustid FROM produktion WHERE id='$id' LIMIT 1");
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM produktion WHERE id='$id' LIMIT 1");
      $freitext = $this->app->DB->Select("SELECT freitext FROM produktion WHERE id='$id' LIMIT 1");
      $bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM produktion WHERE id='$id' LIMIT 1");
      $name = $this->app->DB->Select("SELECT name FROM produktion WHERE id='$id' LIMIT 1");

			$name = $name." (Kundennummer: ".$kundennummer.")";


			$this->nichtsichtbar_zeileabsender=true;
			$this->nichtsichtbar_rechtsoben=true;
			$this->nichtsichtbar_box=true;
			$this->nichtsichtbar_summe=true;
			$this->nichtsichtbar_empfaenger=true;
			$this->barcode_sichtbar=true;
			$this->hintergrund_sichtbar=false;
			$this->nichtsichtbar_footer=true;

			$this->abstand_betreffzeileoben = -60;
			$this->abstand_artikeltabelleoben = -60;

      $telefax= $this->app->DB->Select("SELECT telefax FROM adresse WHERE id='$adresse' LIMIT 1");

      if($belegnr<=0) $belegnr = "- Entwurf";
      if($this->doctype=="produktion")
      $this->doctypeOrig="Produktion $belegnr";
      else
      $this->doctypeOrig="Proformarechnung $belegnr";

      if($produktion=="") $produktion = "-";
      if($kundennummer=="") $kundennummer= "-";


      $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM produktion WHERE id='$id' LIMIT 1");
      $zahlungsstatus = $this->app->DB->Select("SELECT zahlungsstatus FROM produktion WHERE id='$id' LIMIT 1");
      $zahlungszieltage = $this->app->DB->Select("SELECT zahlungszieltage FROM produktion WHERE id='$id' LIMIT 1");
      $zahlungszieltageskonto = $this->app->DB->Select("SELECT zahlungszieltageskonto FROM produktion WHERE id='$id' LIMIT 1");
      $zahlungszielskonto = $this->app->DB->Select("SELECT zahlungszielskonto FROM produktion WHERE id='$id' LIMIT 1");



      
      if($telefax!="" && $telefax!=0)
	$this->setCorrDetails(array("Angebot"=>$anfrage,"Ihre Kunden-Nr."=>$kundennummer,"Produktionsdatum"=>$datum,"Vertrieb"=>$vertrieb,"Ihre Faxnummer"=>$telefax));
      else
	$this->setCorrDetails(array("Angebot"=>$anfrage,"Ihre Kunden-Nr."=>$kundennummer,"Produktionsdatum"=>$datum,"Vertrieb"=>$vertrieb));


      if($this->doctype=="produktion")
      {
	  //"body"=>"Kunde: $name\r\nBezeichnung: $bezeichnung\r\n"));
      $this->setTextDetails(array(
	  "body"=>"Kunde: $name\r\n",
	  "footer"=>"\r\n$freitext\n\n"));
      } else {

//	if(!$this->app->erp->ProduktionMitUmsatzeuer($id)) $steuerzeile = "Steuerfrei nach § 4 Nr. 1b i.V.m. § 6 a UStG. Ihre USt-IdNr. $ustid Land: $land";
/*
	  $this->setTextDetails(array(
	  "body"=>"Sehr geehrte Damen und Herren,\n\nvielen Dank für Ihren Produktion.", 
	  "footer"=>"$freitext\n\n$zahlungstext\n\n$steuerzeile"));

*/
      }

      $artikel = $this->app->DB->SelectArr("SELECT * FROM produktion_position WHERE produktion='$id' ORDER By sort");

      //$waehrung = $this->app->DB->Select("SELECT waehrung FROM produktion_position WHERE produktion='$id' LIMIT 1");
      foreach($artikel as $key=>$value)
      {

				$this->addItem(array('currency'=>$value[waehrung],'amount'=>$value[menge],'price'=>$value[preis],'tax'=>$value[umsatzsteuer],'itemno'=>$value[nummer],'desc'=>$value[beschreibung],
	  "name"=>$value[bezeichnung]));
      }
      $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM produktion_position WHERE produktion='$id'");

      $summeV = $this->app->DB->Select("SELECT SUM(menge*preis) FROM produktion_position WHERE produktion='$id' AND (umsatzsteuer='normal' or umsatzsteuer='')")/100 * 19;
      $summeR = $this->app->DB->Select("SELECT SUM(menge*preis) FROM produktion_position WHERE produktion='$id' AND umsatzsteuer='ermaessigt'")/100 * 7;
      
      /* Dateiname */
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y%m%d') FROM produktion WHERE id='$id' LIMIT 1");
      $belegnr= $this->app->DB->Select("SELECT belegnr FROM produktion WHERE id='$id' LIMIT 1");
      $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
      $tmp_name = str_replace('.','',$tmp_name);

      if($this->doctype=="produktion")
      $this->filename = $datum."_PD".$belegnr.".pdf";
      $this->setBarcode($belegnr);
  }


}
?>
