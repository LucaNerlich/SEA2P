<?php


class AnfragePDF extends Briefpapier {
  public $doctype;
  
  function AnfragePDF($app,$projekt="")
  {
    $this->app=&$app;
    //parent::Briefpapier();
    $this->doctype="anfrage";
    $this->doctypeOrig="Anfrage";
    parent::Briefpapier($this->app,$projekt);
  } 


  function GetAnfrage($id,$info="",$extrafreitext="")
  {
      $adresse = $this->app->DB->Select("SELECT adresse FROM anfrage WHERE id='$id' LIMIT 1");

      // das muss vom anfrage sein!!!!
      $this->setRecipientLieferadresse($id,"anfrage");


      // OfferNo, customerId, OfferDate
      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
      $auftrag = $this->app->DB->Select("SELECT auftragid FROM anfrage WHERE id='$id' LIMIT 1");
      $auftrag = $this->app->DB->Select("SELECT belegnr FROM auftrag WHERE id='$auftrag' LIMIT 1");
      $bearbeiter = $this->app->DB->Select("SELECT bearbeiter FROM anfrage WHERE id='$id' LIMIT 1");
      $bestellbestaetigung = $this->app->DB->Select("SELECT bestellbestaetigung FROM anfrage WHERE id='$id' LIMIT 1");
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM anfrage WHERE id='$id' LIMIT 1");
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM anfrage WHERE id='$id' LIMIT 1");
      $freitext = $this->app->DB->Select("SELECT freitext FROM anfrage WHERE id='$id' LIMIT 1");
      $this->projekt = $this->app->DB->Select("SELECT projekt FROM anfrage WHERE id='$id' LIMIT 1");

			$ohne_briefpapier = $this->app->DB->Select("SELECT ohne_briefpapier FROM anfrage WHERE id='$id' LIMIT 1");
			$ihrebestellnummer = $this->app->DB->Select("SELECT ihrebestellnummer FROM anfrage WHERE id='$id' LIMIT 1");
      $ihrebestellnummer = $this->app->erp->ReadyForPDF($ihrebestellnummer);


      if($ohne_briefpapier=="1")
      {
        $this->logofile = "";
        $this->briefpapier="";
      }

      $this->doctype="deliveryreceipt";

      if($belegnr<=0) $belegnr = "- Entwurf";


      if($info=="")
				$this->doctypeOrig="Anfrage $belegnr";
      else
				$this->doctypeOrig="Anfrage$info $belegnr";

      if($anfrage=="") $anfrage = "-";
      if($kundennummer=="") $kundennummer= "-";

      //$this->setCorrDetails(array("Auftrag"=>$auftrag,"Ihre Kunden-Nr."=>$kundennummer,"Versand"=>$datum,"Versand"=>$bearbeiter));
      $this->setCorrDetails(array("Auftrag"=>$auftrag,$this->app->erp->Firmendaten("bezeichnungkundennummer")=>$kundennummer,"Ihre Bestellnummer"=>$ihrebestellnummer,"Datum"=>$datum));



		 $body=$this->app->erp->Firmendaten("anfrage_header");
      $body = $this->app->erp->ParseUserVars("anfrage",$id,$body);

      $this->setTextDetails(array(
	  		"body"=>$body,
	  		"footer"=>"$freitext\r\n$extrafreitext\r\n".$this->app->erp->ParseUserVars("anfrage",$id,$this->app->erp->Firmendaten("anfrage_footer"))));
      
      $artikel = $this->app->DB->SelectArr("SELECT * FROM anfrage_position WHERE anfrage='$id' ORDER By sort");

      //$waehrung = $this->app->DB->Select("SELECT waehrung FROM anfrage_position WHERE anfrage='$id' LIMIT 1");
      foreach($artikel as $key=>$value)
      {

      if($value[seriennummer]!="")
      {
        if( $value[beschreibung]!="")  $value[beschreibung] =  $value[beschreibung]."\n";
	  		$value[beschreibung] = "SN: ".$value[seriennummer]."\n\n";
      }

   			$value['herstellernummer'] = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='".$value[artikel]."' LIMIT 1");
        $value['hersteller'] = $this->app->DB->Select("SELECT hersteller FROM artikel WHERE id='".$value[artikel]."' LIMIT 1");

				$this->addItem(array('amount'=>$value[menge],
						'itemno'=>$value[nummer],
						'desc'=>ltrim($value[beschreibung]),
          	'unit'=>$value[einheit],
 						'hersteller'=>$value[hersteller],
          	'herstellernummer'=>trim($value[herstellernummer]),
	  				"name"=>$value[bezeichnung]));
      }
      

      /* Dateiname */
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y%m%d') FROM anfrage WHERE id='$id' LIMIT 1");
      $belegnr= $this->app->DB->Select("SELECT belegnr FROM anfrage WHERE id='$id' LIMIT 1");
      $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
      $tmp_name = str_replace('.','',$tmp_name);

      $this->filename = $datum."_AF".$belegnr.".pdf";
      $this->setBarcode($belegnr);
  }


}
?>
