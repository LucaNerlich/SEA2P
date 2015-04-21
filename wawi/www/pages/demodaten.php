<?php
class Demodaten 
{

  function Demodaten(&$app)
  {
    $this->app=&$app; 
  
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","DemodatenList");

    $this->app->DefaultActionHandler("list");
    
    $this->app->ActionHandlerListen($app);
  }


	function DemodatenList()
	{
		$sumbit_generator = $this->app->Secure->GetPOST("generator");
		$sumbit_reset = $this->app->Secure->GetPOST("reset");

		if($sumbit_generator!="")
		{
			$this->app->Tpl->Set(MESSAGETAB1,"<div class=\"error2\">Demodaten mit aktuellen Daten erzeugt</div>");

			$projekt = $this->app->DB->Select("SELECT id FROM projekt LIMIT 1");

			// Test Kunden
			$kundennamen = array('Testkunde', 'Muster Kunde GmbH','Max Mustermann');
			for($i=0;$i<count($kundennamen);$i++)
			{
				$kundennummer = $this->app->erp->GetNextKundennummer();
				$adresse = $this->app->erp->ImportCreateAdresse(
					array('name'=>$kundennamen[$i],'kundennummer'=>$kundennummer,'projekt'=>$projekt));
	    	$this->app->erp->AddRolleZuAdresse($adresse, "Kunde", "von", "Projekt", $projekt);

				$kunden[] = $adresse;
			}

			// Test Mitarbeiter
			$mitarbeitername = array('Mitarbeiter 1', 'Mitarbeiter 2','Mitarbeiter 3');
			for($i=0;$i<count($mitarbeitername);$i++)
			{
				$mitarbeiternummer = $this->app->erp->GetNextMitarbeiternummer();
				$adresse = $this->app->erp->ImportCreateAdresse(
					array('name'=>$mitarbeitername[$i],'mitarbeiternummer'=>$mitarbeiternummer,'projekt'=>$projekt,'land'=>'DE'));
	    	$this->app->erp->AddRolleZuAdresse($adresse, "Mitarbeiter", "von", "Projekt", $projekt);
				$this->app->erp->ImportCreateUser(array('adresse'=>$adresse,'activ'=>'1'));
			}

			// Test Lieferant
			$lieferantname = array('Lieferant 1', 'Lieferant 2');
			for($i=0;$i<count($lieferantname);$i++)
			{
				$lieferantnummer = $this->app->erp->GetNextLieferantennummer();
				$adresse = $this->app->erp->ImportCreateAdresse(
					array('name'=>$lieferantname[$i],'lieferantnummer'=>$lieferantnummer,'projekt'=>$projekt,'land'=>'DE'));
	    	$this->app->erp->AddRolleZuAdresse($adresse, "Lieferant", "von", "Projekt", $projekt);
			}

			$branche = $this->app->Secure->GetPOST("branche");
			if($branche=="") $branche="Ware";
			// Artikel anlegen
			for($i=1;$i<=3;$i++)
			{
					$artikelnummer = $this->app->erp->GetNextArtikelnummer();
				  $artikel = $this->app->erp->ImportCreateArtikel(array('name_de'=>"$branche $i",
						'projekt'=>$projekt,'firma'=>1,'typ'=>'fremdleistung','nummer'=>$artikelnummer));

					$preis = rand(50,150);
					$this->app->erp->ImportCreateVerkaufspreis(
						array('artikel'=>$artikel,'ab_menge'=>1,'preis'=>$preis,'waehrung'=>'EUR'));
		
					$this->app->erp->ImportCreateVerkaufspreis(
						array('artikel'=>$artikel,'ab_menge'=>10,'preis'=>$preis-($preis/10),'waehrung'=>'EUR'));

					//Spezial Preis
					if($i==1)
					{
						$this->app->erp->ImportCreateVerkaufspreis(
							array('artikel'=>$artikel,'ab_menge'=>1,'preis'=>$preis-($preis/5),'waehrung'=>'EUR','adresse'=>$kunden[0]));
					}
			}
			


			
		}
		else if($sumbit_reset!="")
		{
			//TRUNCATE reset autoid
			if($this->app->DB->Select("SELECT COUNT(id) FROM adresse WHERE geloescht!=1")<=0)
			{
				$res = mysql_query('SHOW TABLES');
				while ($row = mysql_fetch_array($res, MYSQL_NUM))
				{
  				$res2 = mysql_query("TRUNCATE TABLE `$row[0]`");
				}
				$this->app->erp->InitialSetup();
				header("Location: index.php?module=welcome&action=logout");
				exit;
			} else 
			$this->app->Tpl->Set(MESSAGETAB4,"<div class=\"error\">Datenbank kann nicht zur&uuml;ck gesetzt werden. Erst alle Adressen l&ouml;schen.</div>");
		}

		$this->app->Tpl->Parse(PAGE,"demodaten_list.tpl");
	}


}
?>
