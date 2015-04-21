<?php


class InventurPDF extends Briefpapier {
  public $doctype;
  
  function InventurPDF($app)
  {
    $this->app=&$app;
    //parent::Briefpapier();
    $this->doctype="inventur";
    $this->doctypeOrig="Inventur";
    parent::Briefpapier($this->app);
  } 


  function GetInventur($id,$als="")
  {
      $adresse = $this->app->DB->Select("SELECT adresse FROM inventur WHERE id='$id' LIMIT 1");
//      $this->setRecipientDB($adresse);
      $this->setRecipientLieferadresse($id,"inventur");

      // OfferNo, customerId, OfferDate

      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' LIMIT 1");
      $auftrag= $this->app->DB->Select("SELECT auftrag FROM inventur WHERE id='$id' LIMIT 1");
      $buchhaltung= $this->app->DB->Select("SELECT buchhaltung FROM inventur WHERE id='$id' LIMIT 1");
      $lieferschein = $this->app->DB->Select("SELECT lieferschein FROM inventur WHERE id='$id' LIMIT 1");
      $lieferscheinid = $lieferschein;
      $this->projekt = $this->app->DB->Select("SELECT projekt FROM inventur WHERE id='$id' LIMIT 1");
      $lieferschein = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$lieferschein' LIMIT 1");
      $bestellbestaetigung = $this->app->DB->Select("SELECT bestellbestaetigung FROM inventur WHERE id='$id' LIMIT 1");
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM inventur WHERE id='$id' LIMIT 1");
      $lieferdatum = $this->app->DB->Select("SELECT DATE_FORMAT(lieferdatum,'%d.%m.%Y') FROM inventur WHERE id='$id' LIMIT 1");
      $belegnr = $this->app->DB->Select("SELECT belegnr FROM inventur WHERE id='$id' LIMIT 1");
      $nameinventur = $this->app->DB->Select("SELECT name FROM inventur WHERE id='$id' LIMIT 1");
      $bearbeiter = $this->app->DB->Select("SELECT bearbeiter FROM inventur WHERE id='$id' LIMIT 1");
      $doppel = $this->app->DB->Select("SELECT doppel FROM inventur WHERE id='$id' LIMIT 1");
      $freitext = $this->app->DB->Select("SELECT freitext FROM inventur WHERE id='$id' LIMIT 1");
      $ustid = $this->app->DB->Select("SELECT ustid FROM inventur WHERE id='$id' LIMIT 1");
      $keinsteuersatz = $this->app->DB->Select("SELECT keinsteuersatz FROM inventur WHERE id='$id' LIMIT 1");
      $soll = $this->app->DB->Select("SELECT soll FROM inventur WHERE id='$id' LIMIT 1");
      $ist = $this->app->DB->Select("SELECT ist FROM inventur WHERE id='$id' LIMIT 1");
      $land = $this->app->DB->Select("SELECT land FROM inventur WHERE id='$id' LIMIT 1");
      $mahnwesen_datum = $this->app->DB->Select("SELECT mahnwesen_datum FROM inventur WHERE id='$id' LIMIT 1");
      $zahlungsweise = $this->app->DB->Select("SELECT zahlungsweise FROM inventur WHERE id='$id' LIMIT 1");
      $zahlungsstatus = $this->app->DB->Select("SELECT zahlungsstatus FROM inventur WHERE id='$id' LIMIT 1");
      $zahlungszieltage = $this->app->DB->Select("SELECT zahlungszieltage FROM inventur WHERE id='$id' LIMIT 1");
      $zahlungszieltageskonto = $this->app->DB->Select("SELECT zahlungszieltageskonto FROM inventur WHERE id='$id' LIMIT 1");
      $zahlungszielskonto = $this->app->DB->Select("SELECT zahlungszielskonto FROM inventur WHERE id='$id' LIMIT 1");
      $ohne_briefpapier = $this->app->DB->Select("SELECT ohne_briefpapier FROM inventur WHERE id='$id' LIMIT 1");

			if($ohne_briefpapier=="1")
			{
			 	$this->logofile = "";
      	$this->briefpapier="";
			}

      $zahlungdatum = $this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD(datum, INTERVAL $zahlungszieltage DAY),'%d.%m.%Y') FROM inventur WHERE id='$id' LIMIT 1");
      $zahlungszielskontodatum = $this->app->DB->Select("SELECT DATE_FORMAT(DATE_ADD(datum, INTERVAL $zahlungszieltageskonto DAY),'%d.%m.%Y') FROM inventur WHERE id='$id' LIMIT 1");

      /*if(!$this->app->erp->InventurMitUmsatzeuer($id)){
        $this->ust_befreit=true;
      }*/
        $this->ust_befreit=true;

/*
      $zahlungsweise = strtolower($zahlungsweise);
      //if($zahlungsweise=="inventur"&&$zahlungsstatus!="bezahlt")
      if($zahlungsweise=="inventur")
      {
				if($zahlungszieltage==0){
				 	$zahlungsweisetext = $this->app->erp->Firmendaten("zahlung_inventur_sofort_de");
					if($zahlungsweisetext=="") $zahlungsweisetext ="Inventur zahlbar sofort. ";
				}
				else {
					$zahlungsweisetext = $this->app->erp->Firmendaten("zahlung_inventur_de");
					if($zahlungsweisetext=="") $zahlungsweisetext ="Inventur zahlbar innerhalb {ZAHLUNGSZIELTAGE} Tage bis zum {ZAHLUNGBISDATUM}. ";
					$zahlungsweisetext = str_replace("{ZAHLUNGSZIELTAGE}",$zahlungszieltage,$zahlungsweisetext);
					$zahlungsweisetext = str_replace("{ZAHLUNGBISDATUM}",$zahlungdatum,$zahlungsweisetext);
//					$zahlungsweisetext = "Inventur zahlbar innerhalb $zahlungszieltage Tage bis zum $zahlungdatum netto.";
				}

				if($zahlungszielskonto!=0)
	  			$zahlungsweisetext .=" (Skonto $zahlungszielskonto % innerhalb $zahlungszieltageskonto Tage bis zum $zahlungszielskontodatum)";	
      } elseif($zahlungsweise=="bar")
      {
				$zahlungsweisetext = "Bezahlung per Barzahlung";
      } elseif($zahlungsweise=="nachnahme")
      {
				$zahlungsweisetext = "Bezahlung per Nachnahme";
      } else {
	  			$zahlungsweisetext = "Bezahlung per ".ucfirst($zahlungsweise);
      }

      if($belegnr<=0) $belegnr = "- Proforma";
			else {
				if($doppel==1 || $als=="doppel")
					$belegnr .= " (Doppel)";
			}
*/ 
      if($als=="zahlungserinnerung") 
      $this->doctypeOrig="Zahlungserinnerung";
      else if($als=="mahnung1") 
      $this->doctypeOrig="1. Mahnung";
      else if($als=="mahnung2") 
      $this->doctypeOrig="2. Mahnung";
      else if($als=="mahnung3") 
      $this->doctypeOrig="3. Mahnung";
      else if($als=="inkasso") 
      $this->doctypeOrig="Inkasso-Mahnung";
      else
      $this->doctypeOrig="Inventur ".$this->app->erp->ReadyForPDF($nameinventur);

      if($inventur=="") $inventur = "-";
      if($kundennummer=="") $kundennummer= "-";

      if($auftrag==0) $auftrag = "-";
      if($lieferschein==0) $lieferschein= "-";

      $datumlieferschein = $this->app->DB->Select("SELECT DATE_FORMAT(datum, '%d.%m.%Y') 
				FROM lieferschein WHERE id='$lieferscheinid' LIMIT 1");

			if($lieferschein!='-')
				$this->setCorrDetails(array("Datum"=>$datum));
			else
				$this->setCorrDetails(array("Bearbeiter"=>$bearbeiter,"Datum"=>$datum));
/*
      if(!$this->app->erp->InventurMitUmsatzeuer($id) && $ustid!="" )
      {
        $this->ust_befreit=true;
				if($keinsteuersatz!="1"){
					$steuer = $this->app->erp->Firmendaten("eu_lieferung_vermerk");
					$steuer = str_replace('{USTID}',$ustid,$steuer);
					$steuer = str_replace('{LAND}',$land,$steuer);
				}
      }
*/
      if($als!="")
      {
        $body = $this->app->erp->MahnwesenBody($id,$als);
				$footer = $this->app->erp->Firmendaten("inventur_footer");
      }
      else {
        $body = $this->app->erp->Firmendaten("inventur_header");
        $footer = "$freitext"."\n\n".$zahlungsweisetext."\n\n".$this->app->erp->Firmendaten("inventur_footer")."\n$steuer";
      }

			$this->setTextDetails(array(
	  		"body"=>$body,
	  		"footer"=>$footer));

      $artikel = $this->app->DB->SelectArr("SELECT * FROM inventur_position WHERE inventur='$id' ORDER By sort");
      $summe_rabatt = $this->app->DB->Select("SELECT SUM(rabatt) FROM inventur_position WHERE inventur='$id'");
      if($summe_rabatt > 0) $this->rabatt=1;

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


        $this->addItem(array('currency'=>$value[waehrung],
          'amount'=>$value[menge],
          'price'=>$value[preis],
          'tax'=>$value[umsatzsteuer],
          'itemno'=>$value[nummer],
          'desc'=>$value[beschreibung],
 				//	'hersteller'=>$value[hersteller],
        //  'herstellernummer'=>trim($value[herstellernummer]),
          "name"=>ltrim($value[bezeichnung]),
          "rabatt"=>$value[rabatt]));

          $netto_gesamt = $value[menge]*($value[preis]-($value[preis]/100*$value[rabatt]));
          $summe = $summe + $netto_gesamt;

          if($value[umsatzsteuer]=="" || $value[umsatzsteuer]=="normal")
          {
            $summeV = $summeV + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzNormal());
          }
          else {
            $summeR = $summeR + (($netto_gesamt/100)*$this->app->erp->GetSteuersatzErmaessigt());
          }

      
			}
/*
      $summe = $this->app->DB->Select("SELECT SUM(menge*preis) FROM inventur_position WHERE inventur='$id'");
      $summeV = $this->app->DB->Select("SELECT SUM(menge*preis) FROM inventur_position WHERE inventur='$id' AND (umsatzsteuer!='ermaessigt')")/100 * 19;
      $summeR = $this->app->DB->Select("SELECT SUM(menge*preis) FROM inventur_position WHERE inventur='$id' AND umsatzsteuer='ermaessigt'")/100 * 7;
*/     
/*
      if($this->app->erp->InventurMitUmsatzeuer($id))
      {
				$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe + $summeV + $summeR,"totalTaxV"=>$summeV,"totalTaxR"=>$summeR));
      } else
			{
      	$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe));
			}
*/
      	$this->setTotals(array("totalArticles"=>$summe,"total"=>$summe));
      /* Dateiname */
      $datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y%m%d') FROM inventur WHERE id='$id' LIMIT 1");
      $belegnr= $this->app->DB->Select("SELECT belegnr FROM inventur WHERE id='$id' LIMIT 1");
      $tmp_name = str_replace(' ','',trim($this->recipient['enterprise']));
      $tmp_name = str_replace('.','',$tmp_name);

      if($als=="")
      $this->filename = $datum."_INVENTUR.pdf";
      else
      $this->filename = $datum."_MA".$belegnr.".pdf";

      $this->setBarcode($belegnr);
  }


}
?>
