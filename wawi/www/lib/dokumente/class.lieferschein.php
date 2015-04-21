<?php


class LieferscheinPDF extends Briefpapier {
  public $doctype;
  
  function LieferscheinPDF($app,$projekt="")
  {
    $this->app=&$app;
    //parent::Briefpapier();
    $this->doctype="lieferschein";
    $this->doctypeOrig="Lieferschein";
    parent::Briefpapier($this->app,$projekt);
  } 


  function GetLieferschein($id,$info="",$extrafreitext="")
  {
      $adresse = $this->app->DB->Select("SELECT adresse FROM lieferschein WHERE id='$id' LIMIT 1");

      // das muss vom lieferschein sein!!!!
      $this->setRecipientLieferadresse($id,"lieferschein");


      // OfferNo, customerId, OfferDate
      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
      $auftrag = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id='$id' LIMIT 1");
      $auftrag = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftrag' LIMIT 1");
      $bearbeiter = $this->app->DB->Select("SELECT bearbeiter FROM lieferschein WHERE id='$id' LIMIT 1");
      $bearbeiter = $this->app->erp->ReadyForPDF($bearbeiter);
      $vertrieb = $this->app->DB->Select("SELECT vertrieb FROM lieferschein WHERE id='$id' LIMIT 1");
      $vertrieb = $this->app->erp->ReadyForPDF($vertrieb);
      $bestellbestaetigung = $this->app->DB->Select("SELECT bestellbestaetigung FROM lieferschein WHERE id='$id' LIMIT 1");
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM lieferschein WHERE id='$id' LIMIT 1");
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$id' LIMIT 1");
      $versandart = $this->app->DB->Select("SELECT versandart FROM lieferschein WHERE id='$id' LIMIT 1");
      $freitext = $this->app->DB->Select("SELECT freitext FROM lieferschein WHERE id='$id' LIMIT 1");
      $this->projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein WHERE id='$id' LIMIT 1");

			$ohne_briefpapier = $this->app->DB->Select("SELECT ohne_briefpapier FROM lieferschein WHERE id='$id' LIMIT 1");
			$ihrebestellnummer = $this->app->DB->Select("SELECT ihrebestellnummer FROM lieferschein WHERE id='$id' LIMIT 1");
      $ihrebestellnummer = $this->app->erp->ReadyForPDF($ihrebestellnummer);


      if($ohne_briefpapier=="1")
      {
        $this->logofile = "";
        $this->briefpapier="";
        $this->briefpapier2="";
      }

      $this->doctype="deliveryreceipt";

      if($belegnr<=0) $belegnr = "- Entwurf";

			$this->zusatzfooter = " (LS$belegnr)";

      if($info=="")
				$this->doctypeOrig="Lieferschein $belegnr";
      else
				$this->doctypeOrig="Lieferschein$info $belegnr";

      if($lieferschein=="") $lieferschein = "-";
      if($kundennummer=="") $kundennummer= "-";

			if($bearbeiter==$vertrieb) $vertrieb="";

      //$this->setCorrDetails(array("Auftrag"=>$auftrag,"Ihre Kunden-Nr."=>$kundennummer,"Versand"=>$datum,"Versand"=>$bearbeiter));
      $this->setCorrDetails(array("Auftrag"=>$auftrag,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer,$this->app->erp->Firmendaten("auftrag_bezeichnung_bestellnummer")=>$ihrebestellnummer,"Lieferdatum"=>$datum,$this->app->erp->Firmendaten("auftrag_bezeichnung_bearbeiter")=>$bearbeiter,$this->app->erp->Firmendaten("auftrag_bezeichnung_vertrieb")=>$vertrieb));




 			$body=$this->app->erp->Firmendaten("lieferschein_header");
      $body = $this->app->erp->ParseUserVars("lieferschein",$id,$body);
 
      $this->setTextDetails(array(
	  		"body"=>$body,
	  		"footer"=>"$freitext\r\n$extrafreitext\r\n".$this->app->erp->ParseUserVars("lieferschein",$id,$this->app->erp->Firmendaten("lieferschein_footer"))));
      
      $artikel = $this->app->DB->SelectArr("SELECT * FROM lieferschein_position WHERE lieferschein='$id' ORDER By sort");

      //$waehrung = $this->app->DB->Select("SELECT waehrung FROM lieferschein_position WHERE lieferschein='$id' LIMIT 1");
      foreach($artikel as $key=>$value)
      {

      if($value[seriennummer]!="")
      {
        if( $value[beschreibung]!="")  $value[beschreibung] =  $value[beschreibung]."\n";
	  		$value[beschreibung] = "SN: ".$value[seriennummer]."\n\n";
      }

   			$value['herstellernummer'] = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value[artikel]."' LIMIT 1");
        $value['hersteller'] = $this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value[artikel]."' LIMIT 1");

   			if($value[explodiert_parent_artikel] > 0)
        {
          $check_ausblenden = $this->app->DB->Select("SELECT keineeinzelartikelanzeigen FROM artikel WHERE id='".$value[explodiert_parent_artikel]."' LIMIT 1");
        } else $check_ausblenden=0;

        if($check_ausblenden!=1)
        {
					$this->addItem(array('amount'=>$value[menge],
						'itemno'=>$value[nummer],
						'desc'=>ltrim($value[beschreibung]),
          	'unit'=>$value[einheit],
 						'hersteller'=>$value[hersteller],
          	'herstellernummer'=>trim($value[herstellernummer]),
	  				"name"=>$value[bezeichnung]));
				}
      }
      

      /* Dateiname */
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y%m%d') FROM lieferschein WHERE id='$id' LIMIT 1");
      $belegnr= $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$id' LIMIT 1");
      $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
      $tmp_name = str_replace('.','',$tmp_name);

      $this->filename = $datum."_LS".$belegnr.".pdf";
      $this->setBarcode($belegnr);
  }


}
?>
