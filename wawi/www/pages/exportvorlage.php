<?php
include ("_gen/exportvorlage.php");

class Exportvorlage extends GenExportvorlage {
  var $app;

  function Exportvorlage($app) {
    //parent::GenExportvorlage($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ExportvorlageCreate");
    $this->app->ActionHandler("edit","ExportvorlageEdit");
    $this->app->ActionHandler("export","ExportvorlageExport");
    $this->app->ActionHandler("list","ExportvorlageList");
    $this->app->ActionHandler("delete","ExportvorlageDelete");
    $this->app->ActionHandler("adressen","ExportvorlageAdressen");
    $this->app->ActionHandler("adresseedit","ExportvorlageAdresseEdit");

    $this->app->ActionHandlerListen($app);

    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Daten Export");


    $this->app = $app;
  }

  function ExportvorlageAdresseEdit()
  {
    $this->app->Tpl->Parse(TAB1,"exportvorlage_uebersicht.tpl");
    $this->app->Tpl->Set(TABTEXT,"Export");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function ExportvorlageAdressen()
  {
    //    $this->app->Tpl->Parse(TAB1,"exportvorlage_adressen.tpl");
    $this->app->YUI->TableSearch(TAB1,"adresse_export");
    $this->app->erp->MenuEintrag("index.php?module=importvorlage&action=uebersicht","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->Tpl->Set(TABTEXT,"Export");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }



  function ExportvorlageCreate()
  {
    $this->ExportvorlageMenu();
    parent::ExportvorlageCreate();
  }

  function ExportvorlageDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id))
    {
      $this->app->DB->Delete("DELETE FROM exportvorlage WHERE id='$id'");
    }
    $this->ExportvorlageList();
  }


  function ExportvorlageList()
  {
    $this->ExportvorlageMenu();
    if($this->app->DB->Select("SELECT COUNT(id) FROM exportvorlage") <=0)
    {
      $this->app->DB->Insert("INSERT INTO `exportvorlage` (`id`, `bezeichnung`, `fields`, `internebemerkung`, `ziel`, `letzterexport`, `mitarbeiterletzterexport`, `exporttrennzeichen`, `exporterstezeilenummer`, `exportdatenmaskierung`, `exportzeichensatz`) VALUES
          ('', 'Standard Artikel Export (Format siehe Wiki)', 'nummer;\r\nname_de;\r\nname_en;\r\nbeschreibung_de;\r\nbeschreibung_en;\r\nkurztext_de;\r\nkurztext_en;\r\ninternerkommentar;\r\nhersteller;\r\nherstellernummer;\r\nherstellerlink;\r\nean;', '', 'artikel', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '');");
    }


    parent::ExportvorlageList();
  }

  function ExportvorlageMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM exportvorlage WHERE id='$id' LIMIT 1");

    $this->app->Tpl->Set(KURZUEBERSCHRIFT2,$bezeichnung);

    if($this->app->Secure->GetGET("action")=="list")
    {
      $this->app->erp->MenuEintrag("index.php?module=exportvorlage&action=create","Neue Exportvorlage anlegen");
      $this->app->erp->MenuEintrag("index.php?module=importvorlage&action=uebersicht","Zur&uuml;ck zur &Uuml;bersicht");
    }
    else
    {
      $this->app->erp->MenuEintrag("index.php?module=exportvorlage&action=edit&id=$id","Details");
      $this->app->erp->MenuEintrag("index.php?module=exportvorlage&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->app->erp->MenuEintrag("index.php?module=exportvorlage&action=export&id=$id","Export starten: CSV Datei herunterladen");
    }
  }


    function ExportvorlageEdit()
    {
      $this->ExportvorlageMenu();
      parent::ExportvorlageEdit();
    }

    function ExportvorlageGetFields($id)
    {
      $fields = $this->app->DB->Select("SELECT fields FROM exportvorlage WHERE id='$id' LIMIT 1");

      $fieldsarray = explode(';',$fields);
      for($i=0;$i<count($fieldsarray);$i++)
      {
        $fieldsarray_items = explode(':',$fieldsarray[$i]);
        if($fieldsarray_items[1]!=""){
          $csv_fields[$fieldsarray_items[0]]= $fieldsarray_items[1];
          $csv_fields_keys[] = $fieldsarray_items[0];
        }
      }         
      return $csv_fields;
    }

    function ExportvorlageExport()
    {
      $id = $this->app->Secure->GetGET("id");
      $this->app->erp->MenuEintrag("index.php?module=exportvorlage&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->app->erp->MenuEintrag("index.php?module=exportvorlage&action=edit&id=$id","Export Einstellungen f&uuml;r aktuellen Export anpassen");

      $bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM exportvorlage WHERE id='$id' LIMIT 1");
      $exporttrennzeichen = $this->app->DB->Select("SELECT exporttrennzeichen FROM exportvorlage WHERE id='$id' LIMIT 1");
      $exporterstezeilenummer = $this->app->DB->Select("SELECT exporterstezeilenummer FROM exportvorlage WHERE id='$id' LIMIT 1");
      $exportdatenmaskierung = $this->app->DB->Select("SELECT exportdatenmaskierung FROM exportvorlage WHERE id='$id' LIMIT 1");
      $exportzeichensatz = $this->app->DB->Select("SELECT exportzeichensatz FROM exportvorlage WHERE id='$id' LIMIT 1");
      $fields = $this->app->DB->Select("SELECT fields FROM exportvorlage WHERE id='$id' LIMIT 1");
      $fields_where = $this->app->DB->Select("SELECT fields_where FROM exportvorlage WHERE id='$id' LIMIT 1");
      $ziel = $this->app->DB->Select("SELECT ziel FROM exportvorlage WHERE id='$id' LIMIT 1");

      $fieldsarray = explode(';',$fields);
      for($i=0;$i<count($fieldsarray);$i++)
      {
        switch(trim($fieldsarray[$i]))
        {
          case "verkaufspreisnetto":
            $fields_array[] = "'VAR:VERKAUFSPREISNETTO' as verkaufspreisnetto";
          break;
          case "einkaufspreisnetto":
            $fields_array[] = "'VAR:EINKAUFSPREISNETTO' as einkaufspreisnetto";
          break;
          case "lieferantname":
            $fields_array[] = "'VAR:LIEFERANTNAME' as lieferantname";
          break;
          case "lieferantnummer":
            $fields_array[] = "'VAR:LIEFERANTNUMMER' as lieferantnummer";
          break;
          case "lager_menge":
            $fields_array[] = "'VAR:LAGER_MENGE' as lager_menge";
          break;
          default:
            $fields_array[] = $fieldsarray[$i];
        }
      }         

      $sql_fields = implode(',',$fields_array);
      $sql_fields = trim($sql_fields);
      $sql_fields = rtrim($sql_fields,',');

      $fields_where = str_replace('&apos;',"'",$fields_where);

      $fieldsarray = explode(';',$fields_where);
      for($i=0;$i<count($fieldsarray);$i++)
      {
        $fields_array_where[] = $fieldsarray[$i];
      }         
      $sql_fields_where = implode(' AND ',$fields_array_where);
      $sql_fields_where = rtrim($sql_fields_where,' AND ');


      switch($ziel)
      {
        case "artikel":
          $sql = "SELECT $sql_fields,id as systemid FROM artikel";
          break;
        case "ansprechpartner":
          $sql = "SELECT $sql_fields FROM ansprechpartner";
          break;
        case "adresse":
          $sql = "SELECT $sql_fields FROM adresse";
          break;
        case "angebot":
          $sql = "SELECT $sql_fields FROM angebote";
          break;
        case "auftrag":
          $sql = "SELECT $sql_fields FROM auftrag";
          break;
        case "rechnung":
          $sql = "SELECT $sql_fields FROM rechnung";
          break;
        case "lieferschein":
          $sql = "SELECT $sql_fields FROM lieferschein";
          break;
        case "bestellung":
          $sql = "SELECT $sql_fields FROM bestellung";
          break;
        case "gutschrift":
          $sql = "SELECT $sql_fields FROM gutschrift";
          break;
      }

      if(count($fields_array_where) > 0 && trim($sql_fields_where)!="")
        $sql = $sql." WHERE ".trim($sql_fields_where);

      $all = $this->app->DB->SelectArr($sql);

      if($exporttrennzeichen=="semikolon") $exporttrennzeichen=';';
      if($exporttrennzeichen=="komma") $exporttrennzeichen=',';
      if($exportdatenmaskierung=="gaensefuesschen") $exportdatenmaskierung='"'; else $exportdatenmaskierung="";

      // wenn beschriftung dann diese hier ausgeben
      header("Content-Type: text/plain;");
      header('Content-Disposition: attachment; filename=export.csv');

      if($exporterstezeilenummer=="1")
      {
        foreach($all[0] as $value=>$tmp)
          echo $exportdatenmaskierung.$value.$exportdatenmaskierung.$exporttrennzeichen;
        echo "\r\n";
      }

      for($resulti=0;$resulti<count($all);$resulti++)
      {
				$systemid = $all[$resulti]['systemid'];
				switch($ziel)
				{
					case "artikel": 
						if($systemid > 0 && is_numeric($systemid))
						{
							$lager_menge = 0;//$this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='".$systemid."'");	
							$lieferantname = 0;//$this->app->erp->ReadyForPDF($this->app->DB->Select(
									//"SELECT adr.name FROM artikel a LEFT JOIN adresse adr ON adr.id=a.adresse WHERE a.id=$systemid "));	
							$lieferantnummer = 0;//$this->app->DB->Select("SELECT adr.lieferantennummer FROM artikel a LEFT JOIN adresse adr ON adr.id=a.adresse WHERE a.id=$systemid");	

							$einkaufspreis = 0;//$this->app->DB->Select("SELECT preis FROM einkaufspreise WHERE artikel='$systemid' AND ab_menge=1 AND adresse=0 AND 
//								(gueltig_bis='0000-00-00' OR gueltig_bis >= NOW()) LIMIT 1");

							//$this->app->erp->GetEinkaufspreis($systemid,1);
							$verkaufspreis = 9;//$this->app->erp->GetVerkaufspreis($systemid,1);
						}
					break;
				}

        foreach($all[$resulti] as $value)
        {
          $value = $this->app->erp->fixeUmlaute($this->app->erp->ReadyForPDF($value));
          // ersetzte platzhalter
					$value = str_replace("VAR:VERKAUFSPREISNETTO",$verkaufspreis,$value);
					$value = str_replace("VAR:EINKAUFSPREISNETTO",$einkaufspreis,$value);
					$value = str_replace("VAR:LIEFERANTNAME",$lieferantname,$value);
					$value = str_replace("VAR:LIEFERANTNUMMER",$lieferantnummer,$value);
					$value = str_replace("VAR:LAGER_MENGE",$lager_menge,$value);

          echo $exportdatenmaskierung.$value.$exportdatenmaskierung.$exporttrennzeichen;
        }

        echo "\r\n";
      } 
      exit;
    }


    function ExportvorlageDo()
    {
      $id = $this->app->Secure->GetGET("id");
      $ziel = $this->app->DB->Select("SELECT ziel FROM exportvorlage WHERE id='$id' LIMIT 1");
      $fields = $this->ExportvorlageGetFields($id);


      $ekpreisaenderungen = 0;
      $vkpreisaenderungen = 0;

      $tmp = $this->app->Secure->GetPOST("row");

      $number_of_rows = count($tmp[cmd]);
      for($i=1;$i<=$number_of_rows;$i++)
      {
        $lieferantid = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$tmp[lieferantennummer][$i]."' 
            AND lieferantennummer!='' LIMIT 1");

        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$tmp[nummer][$i]."' AND nummer!='' LIMIT 1");
        $kundenid = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$tmp[kundennummer][$i]."' AND kundennummer!='' LIMIT 1");
        if($kundenid<=0) $kundenid=0;
        if($lieferantid<=0) $lieferantid=0;

        if($lieferantid<=0)
          $lieferantid = $this->app->DB->Select("SELECT id FROM adresse WHERE name='".$tmp[lieferantname][$i]."' LIMIT 1");

        switch($ziel)
        {
          case "einkauf":
          case "artikel":

            // wenn es artikel nicht gibt muss man diesen neu anlegen
            if($tmp[cmd][$i]=="create" && $tmp[checked][$i]=="1")
            {
              if($tmp[name_de]!="")
              {
                foreach($fields as $key=>$value)
                  $felder[$value]=$tmp[$value][$i];
              }

              if($tmp[nummer][$i]=="")
                $felder[nummer]=$this->app->erp->GetNextArtikelnummer($tmp[typ][$i]);
              else
                $felder[nummer]=$tmp[nummer][$i];

              // ek preis
              if($lieferantid <=0 && $tmp[lieferantname][$i]!="")
              {
                $lieferantid = $this->app->erp->CreateAdresse($tmp[lieferantname][$i]);
                $this->app->erp->AddRolleZuAdresse($lieferantid, "Lieferant", "von","Projekt","");
              }
              if($lieferantid>0)
                $felder[adresse]=$lieferantid;
              // mit welcher Artikelgruppe?
              $artikelid = $this->app->erp->ExportCreateArtikel($felder);

              // vk preis
              if($tmp[lieferanteinkaufnetto][$i]!="" && $lieferantid > 0){

                if($tmp[lieferantbestellnummer][$i]!="") $nr = $tmp[lieferantbestellnummer][$i];
                else if($tmp[herstellernummer][$i]!="") $nr = $tmp[herstellernummer][$i];
                else $nr = $tmp[name_de][$i];

                if($tmp[lieferanteinkaufvpemenge][$i] > 0 && $tmp[lieferanteinkaufmenge][$i]<=0)
                  $tmp[lieferanteinkaufmenge][$i] = $tmp[lieferanteinkaufvpemenge][$i];

                if($tmp[lieferanteinkaufmenge][$i] > 1)
                {
                  $tmp[lieferanteinkaufnetto][$i] = $tmp[lieferanteinkaufnetto][$i] / $tmp[lieferanteinkaufmenge][$i];
                  $tmp[lieferanteinkaufmenge][$i] = 1;
                }

                if($tmp[lieferanteinkaufmenge][$i]<=0)
                  $tmp[lieferanteinkaufmenge][$i] = 1;

                $this->app->erp->AddEinkaufspreis($artikelid,$tmp[lieferanteinkaufmenge][$i],
                    $lieferantid,$nr,$nr,
                    str_replace(',','.',$tmp[lieferanteinkaufnetto][$i]),$tmp[lieferanteinkaufwaehrung][$i],$tmp[lieferanteinkaufvpemenge][$i]);
              }

              if($tmp[verkaufspreis1netto][$i]!=""){
                $this->app->erp->AddVerkaufspreis($artikelid,$tmp[verkaufspreis1menge][$i],
                    $kundenid,str_replace(',','.',$tmp[verkaufspreis1netto][$i]),$tmp[verkaufspreis1waehrung][$i]);
              }
              if($tmp[verkaufspreis2netto][$i]!=""){
                $this->app->erp->AddVerkaufspreis($artikelid,$tmp[verkaufspreis2menge][$i],
                    $kundenid,str_replace(',','.',$tmp[verkaufspreis2netto][$i]),$tmp[verkaufspreis2waehrung][$i]);
              }

              if($tmp[verkaufspreis3netto][$i]!=""){
                $this->app->erp->AddVerkaufspreis($artikelid,$tmp[verkaufspreis3menge][$i],
                    $kundenid,str_replace(',','.',$tmp[verkaufspreis3netto][$i]),$tmp[verkaufspreis3waehrung][$i]);
              }

              $lager_id = $this->app->DB->Select("SELECT id FROM lager WHERE geloescht!='1' LIMIT 1");
              if($tmp[lager][$i]!=""){
                $this->app->DB->Update("UPDATE artikel SET lagerartikel='1' WHERE id='$artikelid' LIMIT 1");
                $regal = $this->app->erp->CreateLagerplatz($lager_id,$tmp[lager][$i]);
                $this->app->erp->LagerEinlagernDifferenz($artikelid,$tmp[lagermenge][$i],$regal,"","Erstbef&uuml;llung",1);
              }
              //17:lieferanteinkaufvpemenge;

            } else if ($tmp[cmd][$i]=="update" && $tmp[checked][$i]=="1") {

              // wenn er vorhanden ist nur ein Update braucht

              if($artikelid > 0)
              {
                foreach($fields as $key=>$value)
                {                       
                  switch($value)
                  {
                    case "name_de":
                    case "name_en":
                    case "kurztext_en":
                    case "kurztext_de":
                    case "beschreibung_de":
                    case "beschreibung_en":
                    case "anabregs_text":
                    case "typ":
                    case "ean":
                    case "gewicht":
                    case "hersteller":
                    case "herstellerlink":
                    case "herstellernummer":
                      $this->app->DB->Update("UPDATE artikel SET ".$value."='".$tmp[$value][$i]."' WHERE id='".$artikelid."' LIMIT 1");
                      break;
                    case  "lieferanteinkaufnetto":
                      $alterek = $this->app->DB->Select("SELECT preis FROM einkaufspreise WHERE ab_menge='".$tmp[lieferanteinkaufmenge][$i]."' 
                          AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW()) AND adresse='".$lieferantid."' LIMIT 1");
                      if($alterek != str_replace(',','.',$tmp[lieferanteinkaufnetto][$i]))
                      {
                        $ekpreisaenderungen++;
                        $this->app->DB->Update("UPDATE einkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) 
                            WHERE adresse='".$lieferantid."' AND artikel='".$artikelid."' 
                            AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW())
                            AND ab_menge='".$tmp[lieferanteinkaufmenge][$i]."' LIMIT 1");

                        if($tmp[lieferantbestellnummer][$i]!="") $nr = $tmp[lieferantbestellnummer][$i];
                        else if($tmp[herstellernummer][$i]!="") $nr = $tmp[herstellernummer][$i];
                        else $nr = $tmp[name_de][$i];

                        if($tmp[lieferanteinkaufvpemenge][$i] > 0 && $tmp[lieferanteinkaufmenge][$i]<=0)
                          $tmp[lieferanteinkaufmenge][$i] = $tmp[lieferanteinkaufvpemenge][$i];

                        if($tmp[lieferanteinkaufmenge][$i] > 1)
                        {
                          $tmp[lieferanteinkaufnetto][$i] = $tmp[lieferanteinkaufnetto][$i] / $tmp[lieferanteinkaufmenge][$i];
                          $tmp[lieferanteinkaufmenge][$i] = 1;
                        }

                        if($tmp[lieferanteinkaufmenge][$i]<=0)
                          $tmp[lieferanteinkaufmenge][$i] = 1;

                        $this->app->erp->AddEinkaufspreis($artikelid,$tmp[lieferanteinkaufmenge][$i],
                            $lieferantid,$nr,$nr,
                            str_replace(',','.',$tmp[lieferanteinkaufnetto][$i]),$tmp[lieferanteinkaufwaehrung][$i],$tmp[lieferanteinkaufvpemenge][$i]);
                      } 
                      break;
                    case  "verkaufspreis1netto":
                      $altervk = $this->app->DB->Select("SELECT preis FROM verkaufspreis WHERE ab_menge='".$tmp[verkaufspreis1menge][$i]."' 
                          AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW() ) WHERE adresse <='$kundenid' LIMIT 1");
                      if($altervk != str_replace(',','.',$tmp[verkaufspreis1netto][$i]))
                      {
                        $vkpreisaenderungen++;
                        $this->app->DB->Update("UPDATE verkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) 
                            WHERE artikel='".$artikelid."' AND adresse='$kundenid'
                            AND ab_menge='".$tmp[verkaufspreis1menge][$i]."' LIMIT 1");

                        $this->app->erp->AddVerkaufspreis($artikelid,$tmp[verkaufspreis1menge][$i],
                            $kundenid,str_replace(',','.',$tmp[verkaufspreis1netto][$i]),$tmp[verkaufspreis1waehrung][$i]);
                      } 
                      break;
                    case  "verkaufspreis2netto":
                      $altervk = $this->app->DB->Select("SELECT preis FROM verkaufspreis WHERE ab_menge='".$tmp[verkaufspreis2menge][$i]."' 
                          AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW() ) WHERE adresse <='$kundenid' LIMIT 1");
                      if($altervk != str_replace(',','.',$tmp[verkaufspreis2netto][$i]))
                      {
                        $vkpreisaenderungen++;
                        $this->app->DB->Update("UPDATE verkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) 
                            WHERE artikel='".$artikelid."' AND adresse='$kundenid'
                            AND ab_menge='".$tmp[verkaufspreis2menge][$i]."' LIMIT 1");

                        $this->app->erp->AddVerkaufspreis($artikelid,$tmp[verkaufspreis2menge][$i],
                            $kundenid,str_replace(',','.',$tmp[verkaufspreis2netto][$i]),$tmp[verkaufspreis2waehrung][$i]);
                      } 
                      break;
                    case  "verkaufspreis3netto":
                      $altervk = $this->app->DB->Select("SELECT preis FROM verkaufspreis WHERE ab_menge='".$tmp[verkaufspreis3menge][$i]."' 
                          AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW() ) WHERE adresse <='$kundenid' LIMIT 1");
                      if($altervk != str_replace(',','.',$tmp[verkaufspreis3netto][$i]))
                      {
                        $vkpreisaenderungen++;
                        $this->app->DB->Update("UPDATE verkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) 
                            WHERE artikel='".$artikelid."' AND adresse='$kundenid'
                            AND ab_menge='".$tmp[verkaufspreis3menge][$i]."' LIMIT 1");

                        $this->app->erp->AddVerkaufspreis($artikelid,$tmp[verkaufspreis3menge][$i],
                            $kundenid,str_replace(',','.',$tmp[verkaufspreis3netto][$i]),$tmp[verkaufspreis3waehrung][$i]);
                      } 
                      break;

                  }
                }
              }
            }   
            break;
          case "zeiterfassung":
            if($tmp[cmd][$i]=="create" && $tmp[checked][$i]=="1")
            {
              if($tmp[nummer][$i]!="")
              {
                foreach($fields as $key=>$value)
                  $felder[$value]=$tmp[$value][$i];
                $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$tmp[nummer][$i]."' LIMIT 1");
              }
              $vonZeit = $felder[datum_von]." ".$felder[zeit_von].":00";
              $bisZeit = $felder[datum_bis]." ".$felder[zeit_bis].":00";
              $ort = "";
              $projekt = "";
              $art = "";
              $kunde = $adresse;
              if($felder[taetigkeit]=="")$felder[taetigkeit]="Zeiterfassung";
              $this->app->erp->AddArbeitszeit($this->app->User->GetID(), $vonZeit, $bisZeit, $felder[taetigkeit], $felder[details],$ort, $projekt, 0,$art,$kunde);
            }
            break;
          case "adresse":

            if($tmp[cmd][$i]=="create" && $tmp[checked][$i]=="1")
            {
              $adresse=0;
              foreach($fields as $key=>$value)
                $felder[$value]=$tmp[$value][$i];

              if($tmp[kundennummer][$i]!="" || $tmp[lieferantennummer][$i]!="")
              {
                $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$tmp[kundennummer][$i]."' AND kundennummer!='' LIMIT 1");
                if($adresse <=0)
                  $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$tmp[lieferantennummer][$i]."' AND lieferantennummer!='' LIMIT 1");
              }
              if($adresse <=0 && $felder[name]!="")
              { 
                //adresse anlegen
                $adresse =$this->app->erp->ExportCreateAdresse($felder);
                if($tmp[lieferantennummer][$i]!="")
                  $this->app->erp->AddRolleZuAdresse($adresse, "Lieferant", "von","Projekt","");
                if($tmp[kundennummer][$i]!="")
                  $this->app->erp->AddRolleZuAdresse($adresse, "Kunde", "von","Projekt","");
                //rolle verpassen
              }

            }
            else if($tmp[cmd][$i]=="update" && $tmp[checked][$i]=="1")
            {
              $adresse=0;
              //            foreach($fields as $key=>$value)
              //              $felder[$value]=$tmp[$value][$i];

              if($tmp[kundennummer][$i]!="" || $tmp[lieferantennummer][$i]!="")
              {
                $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$tmp[kundennummer][$i]."' AND kundennummer!='' LIMIT 1");
                if($adresse <=0)
                  $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$tmp[lieferantennummer][$i]."' AND lieferantennummer!='' LIMIT 1");
              }
              if($adresse > 0)
              {
                foreach($fields as $key=>$value)
                {
                  $felder[$key]=$tmp[$value][$i];
                  if($key=="typ" || $key=="zahlungsweise") $tmp[$value][$i] = strtolower($tmp[$value][$i]);
                  if($key=="land") {
                    if($tmp[$value][$i]=="Deutschland" || $tmp[$value][$i]=="Germany" || $tmp[$value][$i]=="")
                      $tmp[$value][$i] = "DE";
                  }

                  $this->app->DB->Update("UPDATE adresse SET ".$fields[$key]."='".$tmp[$value][$i]."' WHERE id='$adresse' LIMIT 1");
                }

              }
            }



            break;
        }
      }
      if($ziel=="zeiterfassung")
      {
        $msg=$this->app->erp->base64_url_encode("<div class=\"info\">Export durchgef&uuml;hrt.</div>");
        header("Location: index.php?module=exportvorlage&action=export&id=$id&msg=$msg");
        exit;
      } else {  
        $msg=$this->app->erp->base64_url_encode("<div class=\"info\">Export durchgef&uuml;hrt.</div>");
        header("Location: index.php?module=exportvorlage&action=export&id=$id&msg=$msg");
        exit;
      }
    }   


    function ExportPrepareHeader($ziel,$csv_fields_keys,$csv_fields)
    {
      $number_of_fields =count($csv_fields_keys);

      switch($ziel)
      {
        case "einkauf":
        case "artikel":
          $this->app->Tpl->Add(ERGEBNIS,'<tr><td width="100"><b>Auswahl</b></td><td width="100"><b>Aktion</b></td><td><b>Artikel</b></td>');
          break;
        case "adresse":
          $this->app->Tpl->Add(ERGEBNIS,'<tr><td width="100"><b>Auswahl</b></td><td width="100"><b>Aktion</b></td><td><b>Adresse</b></td>');
          break;

        case "zeiterfassung":
          $this->app->Tpl->Add(ERGEBNIS,'<tr><td width="100"><b>Auswahl</b></td>
              <td width="100"><b>Aktion</b></td><td><b>Kunde</b></td>');
          break;
      }

      for($j=0;$j<$number_of_fields;$j++)
      {
        $this->app->Tpl->Add(ERGEBNIS,'<td><b>'.$csv_fields[($csv_fields_keys[$j])].'</b></td>');
      }
      $this->app->Tpl->Add(ERGEBNIS,'</tr>');
    }

    function ExportPrepareRow($rowcounter,$ziel,$data,$csv_fields_keys,$csv_fields)
    {
      $number_of_fields =count($csv_fields_keys);
      //Standard
      $fields[waehrung] = 'EUR';

      for($j=0;$j<$number_of_fields;$j++)
      {
        $value = trim($data[($csv_fields_keys[$j]-1)]);

        $fieldname = $csv_fields[$csv_fields_keys[$j]];
        switch($fieldname)
        {
          case "herstellernummer":
            $fields[herstellernummer] = $value;
            $fields[herstellernummer] = $this->app->DB->Select("SELECT herstellernummer 
                FROM artikel WHERE herstellernummer='".$fields[herstellernummer]."' LIMIT 1");
            //                                                  if($fields[herstellernummer]<=0) $fields[herstellernummer]="";
            break;
          case "nummer":
            $fields[nummer] = $value;
            $fields[nummer] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE nummer='".$fields[nummer]."' LIMIT 1");
            //if($fields[nummer]==0) $fields[nummer]="";
            break;
          case "lieferantennummer":
            $fields[lieferantennummer] = $value;
            $fields[lieferantennummer] = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE lieferantennummer='".$fields[lieferantennummer]."' LIMIT 1");
            $lieferantid = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$fields[lieferantennummer]."' LIMIT 1");
            if($fields[lieferantennummer]<=0) $fields[lieferantennummer]="";
            break;
          case "kundennummer":
            $fields[kundennummer] = $value;
            $fields[kundennummer] = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE lieferantennummer='".$fields[lieferantennummer]."' LIMIT 1");
            if($fields[kundennummer]<=0) $fields[kundennummer]="";
            break;
          case "ab_menge":
            $fields[ab_menge] = $value;
            break;
          case "ean":
            $fields[ab_menge] = $value;
            break;
          case "waehrung":
            $fields[waehrung] = $value;
            break;
          case "ekpreis":
            $value = str_replace('EUR','',$value);
            $value = str_replace(' ','',$value);
            if(preg_match('#^(?<integer>.*)(?<separator>[\.,])(?<decimals>[0-9]+)$#', $value, $matches))
            {
              /* clean integer and append decimals with your own separator */
              $number = ((int) preg_replace('#[^0-9]+#', '', $matches['integer']) . ',' . $matches['decimals']);
            }
            else
            {
              $number = (int) preg_replace('#[^0-9]+#', '', $input);
            }
            // $formatter = new NumberFormatter('de_DE', NumberFormatter::CURRENCY);

            // prüfe von rechts letztes zeichen das keine 0 ist

            // let's print the international format for the en_US locale
            $value = $number;
            $fields[ekpreis] = $value;
            break;
          case "datum_von":
            $value = $this->app->String->Convert($value,"%1.%2.%3","20%3-%2-%1");
            $fields[datum_von] = $value;
            break;
          case "datum_bis":
            $value = $this->app->String->Convert($value,"%1.%2.%3","20%3-%2-%1");
            $fields[datum_bis] = $value;
            break;
          case "kennung":
            $fields[kennung] = $value;
            break;
          case "zeit_bis":
            $fields[zeit_bis] = $value;
            break;
          case "zeit_von":
            $fields[zeit_von] = $value;
            break;



          default:
            $fields[$fieldname] = $value;       
            //$value = $data[($csv_fields_keys[$j]-1)];
            //  $value = $data[($csv_fields_keys[$j]-1)];
        }

        $output .= '<td><input type="text" size="15" name="row['.$fieldname.']['.$rowcounter.']" value="'.$value.'"></td>';
      }


      switch($ziel)
      {
        case "einkauf":
          $checked = "checked";
          if($fields[lieferantennummer]=="")
          {
            $action_anzeige = "Keine (Lieferant fehlt)";
            $action="none";
            $checked="";
          }
          else if($fields[lieferantennummer]!="" && $fields[nummer]!="")
          {
            $nummer = $fields[nummer];
            $action_anzeige = "Update (Artikelnr. gefunden)";
            $action="update";
          }
          else if($fields[lieferantennummer]!="" && $fields[herstellernummer]!="")
          {
            $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE herstellernummer='".$fields[herstellernummer]."' LIMIT 1");
            $action_anzeige = "Update (Herstellernr. gefunden)";
            $action="update";
          } 
          else if($fields[lieferantennummer]!="" && $fields[bestellnummer]!="")
          {
            $artikelid = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE bestellnummer='".$fields[bestellnummer]."'
                AND adresse='".$lieferantid."' LIMIT 1");
            $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='".$artikelid."' LIMIT 1");
            $action_anzeige = "Update (Bestellnr. gefunden)";
            $action="update";
          } 


          else {
            $action_anzeige = "Keine (Artikel- oder Herstellernr. fehlt)";
            $action="none";
            $checked="";
          }
          break;
        case "adresse":
          if($fields[kundennummer]=="" && $fields[lieferantennummer]=="" && $fields[name]=="")
          {
            $action_anzeige = "Keine (Kd.- und Lieferanten-Nr. und name fehlt)";
            $action="none";
            $checked="";
          }
          else if($fields[kundennummer]=="" && $fields[name]!="" && $fields[lieferantennummer]=="")
          {
            $action_anzeige = "Neu (Adresse neu anlegen)";
            $action="create";
            $checked="checked";
          }
          else if($fields[lieferantennummer]!="" || $fields[kundennummer]!="")
          {
            $checkkunde = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$fields[kundennummer]."' AND kundennummer!='' LIMIT 1");
            if($checkkunde <= 0)
            {
              $action_anzeige = "Neu (Adresse neu anlegen)";
              $action="create";
              $checked="checked";
            } else {
              $action_anzeige = "Update (Kundennummer gefunden)";
              $action="update";
              $checked="checked";
            }

            $checklieferant = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$fields[lieferantennummer]."' AND lieferantennummer!='' LIMIT 1");
            if($checklieferant <= 0)
            {
              $action_anzeige = "Neu (Adresse neu anlegen)";
              $action="create";
              $checked="checked";
            } else {
              $action_anzeige = "Update (Lieferantennummer gefunden)";
              $action="update";
              $checked="checked";
            }
          }

          break;

        case "artikel":
          if($fields[nummer]=="" && $fields[name_de]=="")
          {
            $action_anzeige = "Keine (Artikel Nr. und name_de fehlt)";
            $action="none";
            $checked="";
          }
          else if($fields[nummer]=="" && $fields[name_de]!="")
          {
            $action_anzeige = "Neu (Artikel neu anlegen)";
            $action="create";
            $checked="checked";
          }
          else if($fields[nummer]!="")
          {
            $action_anzeige = "Update (Artikel update)";
            $action="update";
            $checked="checked";
          }
          break;
        case "zeiterfassung":
          $checked = "checked";
          if($fields[kennung]!="")
            $nummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE kennung='".$fields[kennung]."' LIMIT 1");
          else $nummer="";
          if($nummer=="")
          {
            $action_anzeige = "Keine (Kennung oder Kundennummer fehlt)";
            $action="none";
            $checked="";
          } else {
            $action="create";
          }
          break;


      }

      $this->app->Tpl->Add(ERGEBNIS,'<tr><td width="100"><input type="hidden" name="row[cmd]['.$rowcounter.']" value="'.$action.'">
          <input type="checkbox" name="row[checked]['.$rowcounter.']" '.$checked.' value="1"></td><td nowrap>'.$action_anzeige.'</td>
          <td>'.$nummer.'<input type="hidden" name="row[nummer]['.$rowcounter.']" value="'.$nummer.'"></td>'.$output);
      $this->app->Tpl->Add(ERGEBNIS,'</tr>');
    }

  }

  ?>
