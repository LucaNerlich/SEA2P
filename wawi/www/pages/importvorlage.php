<?php
include ("_gen/importvorlage.php");

class Importvorlage extends GenImportvorlage {
  var $app;
  var $limit_datensaetze;

  function Importvorlage($app) {
    //parent::GenImportvorlage($app);
    $this->app=&$app;

    $this->limit_datensaetze=50;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ImportvorlageCreate");
    $this->app->ActionHandler("edit","ImportvorlageEdit");
    $this->app->ActionHandler("import","ImportvorlageImport");
    $this->app->ActionHandler("list","ImportvorlageList");
    $this->app->ActionHandler("delete","ImportvorlageDelete");
    $this->app->ActionHandler("uebersicht","ImportvorlageUebersicht");
    $this->app->ActionHandler("adressen","ImportvorlageAdressen");
    $this->app->ActionHandler("adresseedit","ImportvorlageAdresseEdit");

    $this->app->ActionHandlerListen($app);

    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Daten Import");

    $this->app = $app;
  }

  function ImportvorlageAdresseEdit()
  {
    $this->app->Tpl->Parse(TAB1,"importvorlage_uebersicht.tpl");
    $this->app->Tpl->Set(TABTEXT,"Import");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function ImportvorlageUebersicht()
  {
    $this->app->Tpl->Parse(TAB1,"importvorlage_uebersicht.tpl");
    $this->app->Tpl->Set(TABTEXT,"Import");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function ImportvorlageAdressen()
  {
    //$this->app->Tpl->Parse(TAB1,"importvorlage_adressen.tpl");
    $this->app->YUI->TableSearch(TAB1,"adresse_import");
    $this->app->erp->MenuEintrag("index.php?module=importvorlage&action=uebersicht","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->Tpl->Set(TABTEXT,"Import");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }



  function ImportvorlageCreate()
  {
    $this->ImportvorlageMenu();
    parent::ImportvorlageCreate();
  }

  function ImportvorlageDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id))
    {
      $this->app->DB->Delete("DELETE FROM importvorlage WHERE id='$id'");
    }
    $this->ImportvorlageList();
  }

  function ImportvorlageList()
  {
    $this->ImportvorlageMenu();
    if($this->app->DB->Select("SELECT COUNT(id) FROM importvorlage") <=0)
    {
      $this->app->DB->Insert("INSERT INTO `importvorlage` (`id`, `bezeichnung`, `fields`, `internebemerkung`, `ziel`, `letzterimport`, `mitarbeiterletzterimport`, `importtrennzeichen`, `importerstezeilenummer`, `importdatenmaskierung`, `importzeichensatz`) VALUES
          ('', 'Standard Artikel Import (Format siehe Wiki)', '1:nummer;\r\n2:name_de;\r\n3:name_en;\r\n4:beschreibung_de;\r\n5:beschreibung_en;\r\n6:kurztext_de;\r\n7:kurztext_en;\r\n8:internerkommentar;\r\n9:hersteller;\r\n10:herstellernummer;\r\n11:herstellerlink;\r\n12:ean;', '', 'artikel', '0000-00-00 00:00:00', '', 'semikolon', 2, 'keine', '');");

      $this->app->DB->Insert("INSERT INTO `importvorlage` (`id`, `bezeichnung`, `fields`, `internebemerkung`, `ziel`, `letzterimport`, `mitarbeiterletzterimport`, `importtrennzeichen`, `importerstezeilenummer`, `importdatenmaskierung`, `importzeichensatz`) VALUES
('', 'Shopware Artikel CSV (Ohne Lager)', '1:nummer;\r\n2:variante_von;\r\n3:name_de;\r\n4:kurztext_de;\r\n4:anabregs_text;\r\n5:lieferantname;\r\n6:umsatzsteuer;\r\n11:lieferanteinkaufnetto;\r\n8:verkaufspreis1netto;\r\n12:pseudopreis;\r\n15:aktiv;\r\n18:beschreibung_de;\r\n25:topseller;\r\n38:herstellernummer;\r\n42:gewicht;\r\n46:ean;\r\n47:einheit;', '', 'artikel', '0000-00-00 00:00:00', '', 'semikolon', 2, 'gaensefuesschen', '');");
    }

    parent::ImportvorlageList();
  }

  function ImportvorlageMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM importvorlage WHERE id='$id' LIMIT 1");

    $this->app->Tpl->Set(KURZUEBERSCHRIFT2,$bezeichnung);

    if($this->app->Secure->GetGET("action")=="list")
    {
      $this->app->erp->MenuEintrag("index.php?module=importvorlage&action=create","Neue Importvorlage anlegen");
      $this->app->erp->MenuEintrag("index.php?module=importvorlage&action=uebersicht","Zur&uuml;ck zur &Uuml;bersicht");
    }
    else
    {
      $this->app->erp->MenuEintrag("index.php?module=importvorlage&action=edit&id=$id","Details");
      //if($this->app->Secure->GetGET("action")!="create")
      $this->app->erp->MenuEintrag("index.php?module=importvorlage&action=import&id=$id","Import starten: CSV Datei heraufladen");
      $this->app->erp->MenuEintrag("index.php?module=importvorlage&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    }
  }

  function ImportvorlageEdit()
  {
    $this->ImportvorlageMenu();
    parent::ImportvorlageEdit();
  }

  function ImportvorlageGetFields($id)
  {
    $fields = $this->app->DB->Select("SELECT fields FROM importvorlage WHERE id='$id' LIMIT 1");

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

  function ImportvorlageImport()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag("index.php?module=importvorlage&action=edit&id=$id","Details");
    $this->app->erp->MenuEintrag("index.php?module=importvorlage&action=import&id=$id","Import starten: CSV Datei heraufladen");

    $bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM importvorlage WHERE id='$id' LIMIT 1");
    $importtrennzeichen = $this->app->DB->Select("SELECT importtrennzeichen FROM importvorlage WHERE id='$id' LIMIT 1");
    $importerstezeilenummer = $this->app->DB->Select("SELECT importerstezeilenummer FROM importvorlage WHERE id='$id' LIMIT 1");
    $importdatenmaskierung = $this->app->DB->Select("SELECT importdatenmaskierung FROM importvorlage WHERE id='$id' LIMIT 1");
    $importzeichensatz = $this->app->DB->Select("SELECT importzeichensatz FROM importvorlage WHERE id='$id' LIMIT 1");
    $fields = $this->app->DB->Select("SELECT fields FROM importvorlage WHERE id='$id' LIMIT 1");
    $ziel = $this->app->DB->Select("SELECT ziel FROM importvorlage WHERE id='$id' LIMIT 1");

    $fieldsarray = explode(';',$fields);
    for($i=0;$i<count($fieldsarray);$i++)
    {
      $fieldsarray_items = explode(':',$fieldsarray[$i]);
      if($fieldsarray_items[1]!=""){
        $csv_fields[$fieldsarray_items[0]]= $fieldsarray_items[1];
        $csv_fields_keys[] = $fieldsarray_items[0];
      }
    }           

    if($importtrennzeichen=="semikolon") $importtrennzeichen=';';
    if($importtrennzeichen=="komma") $importtrennzeichen=',';

    if($importdatenmaskierung=="gaensefuesschen") $importdatenmaskierung='"';
    $number_of_fields = count($csv_fields);

    $upload = $this->app->Secure->GetPOST("upload");

    if($upload!="")
    {

      //print_r($csv_fields);
      $stueckliste_csv = $this->app->erp->GetTMP()."importvorlage".$this->app->User->GetID();


      if (move_uploaded_file($_FILES['userfile']['tmp_name'], $stueckliste_csv)) {
        $importfilename = $_FILES['userfile']['name'];
      }
      ini_set("auto_detect_line_endings", true);
      if (($handle = fopen($stueckliste_csv, "r")) !== FALSE) 
      {
        $rowcounter = 0;
        $rowcounter_real = 0;

        $this->ImportPrepareHeader($ziel,$csv_fields_keys,$csv_fields);
        while (($data = fgetcsv($handle, 0, $importtrennzeichen)) !== FALSE) {
          $rowcounter++;
          $num = count($data);
          if($rowcounter >= $importerstezeilenummer)
          {
            $rowcounter_real++;
            //$data = array_map("utf8_encode", $data);
            //print_r($data);
            foreach($data as $key=>$value) {
              //        $data[$key] = str_replace('"','',$data[$key]);
              //        $data[$key] = mb_convert_encoding($data[$key], "utf-8", "windows-1251");
              /* Detect character encoding with current detect_order */
              $data[$key] = html_entity_decode($this->app->erp->fixeUmlaute( $data[$key] ));
              $data[$key] = trim( $data[$key] );
              // $data[$key] = iconv('UCS-2', 'UTF-8', $data[$key]."\0") ;
              $data[$key] = str_replace('""', '"', $data[$key]);
              $data[$key] = preg_replace("/^\"(.*)\"$/sim", "$1", $data[$key]);
              //                                                                $data[$key]= mb_convert_encoding($data[$key], "Windows-1252");
            }
            if($limit_erreicht!=true)
              $this->ImportPrepareRow($rowcounter_real,$ziel,$data,$csv_fields_keys,$csv_fields);

            if($rowcounter_real >= $this->limit_datensaetze) {
              $limit_erreicht = true;
              //break;
            }

          }
        }
      }
      fclose($handle);

      if($rowcounter_real < $this->limit_datensaetze)
        unlink($stueckliste_csv);

      if($limit_erreicht)
        $this->app->Tpl->Add(IMPORTBUTTON,'<input type="submit" name="import" value="importieren"> <i>Hinweis: Es werden nur 50 von <b>'.$rowcounter_real.'</b> Datens&auml;tze angezeigt da mehr vorhanden waren! Importiert werden alle Datens&auml;tze.</i> <input type="hidden" name="importdateiname" value="'.$stueckliste_csv.'">');
      else
        $this->app->Tpl->Add(IMPORTBUTTON,'<input type="submit" name="import" value="importieren">');

      $this->app->Tpl->Add(IMPORTBUTTON,'<input type="submit" name="import" value="importieren">');
    }


    $import = $this->app->Secure->GetPOST("import");
    if($import!="")
    {
      $this->ImportvorlageDo();
      $this->app->erp->Tpl->Set(MESSAGE,"<div class=\"info\">Import durchgef&uuml;hrt.</div>");
    }



    $this->app->Tpl->Set(KURZUEBERSCHRIFT2,$bezeichnung);
    $this->app->Tpl->Parse(TAB1,"importvorlage_import.tpl");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function ImportvorlageGetCSV($stueckliste_csv,$id)
  {
    // einlesen von der CSV Datei
    $fieldstmp = $this->app->DB->Select("SELECT fields FROM importvorlage WHERE id='$id' LIMIT 1");

    $importtrennzeichen = $this->app->DB->Select("SELECT importtrennzeichen FROM importvorlage WHERE id='$id' LIMIT 1");
    $importerstezeilenummer = $this->app->DB->Select("SELECT importerstezeilenummer FROM importvorlage WHERE id='$id' LIMIT 1");
    $importdatenmaskierung = $this->app->DB->Select("SELECT importdatenmaskierung FROM importvorlage WHERE id='$id' LIMIT 1");
    $importzeichensatz = $this->app->DB->Select("SELECT importzeichensatz FROM importvorlage WHERE id='$id' LIMIT 1");

    $fieldsarray = explode(';',$fieldstmp);
    for($i=0;$i<count($fieldsarray);$i++)
    {
      $fieldsarray_items = explode(':',$fieldsarray[$i]);
      if(trim($fieldsarray_items[1])!=""){
        $csv_fields[trim($fieldsarray_items[0])]= trim($fieldsarray_items[1]);
        $csv_fields_keys[] = trim($fieldsarray_items[0]);
      }
    }

    if($importtrennzeichen=="semikolon") $importtrennzeichen=';';
    if($importtrennzeichen=="komma") $importtrennzeichen=',';

    if($importdatenmaskierung=="gaensefuesschen") $importdatenmaskierung='"';
    $number_of_fields = count($csv_fields);
    if (($handle = fopen($stueckliste_csv, "r")) !== FALSE)
    {
      $rowcounter = 0;
      $rowcounter_real = 0;

      while (($data = fgetcsv($handle, 0, $importtrennzeichen)) !== FALSE) {
        $rowcounter++;
        $num = count($data);
        if($rowcounter >= $importerstezeilenummer)
        {  
          $rowcounter_real++;
          //$data = array_map("utf8_encode", $data);
          //print_r($data);
          foreach($data as $key=>$value) {
            //  $data[$key] = str_replace('"','',$data[$key]);
            //  $data[$key] = mb_convert_encoding($data[$key], "utf-8", "windows-1251");
            /* Detect character encoding with current detect_order */

            $data[$key] = html_entity_decode($this->app->erp->fixeUmlaute($data[$key] ));
            $data[$key] = trim( $data[$key] );
            // $data[$key] = iconv('UCS-2', 'UTF-8', $data[$key]."\0") ;
            $data[$key] = str_replace('""', '"', $data[$key]);
            $data[$key] = preg_replace("/^\"(.*)\"$/sim", "$1", $data[$key]);
            //$data[$key]= mb_convert_encoding($data[$key], "Windows-1252");
          }
          //$this->ImportPrepareRow($rowcounter_real,$ziel,$data,$csv_fields_keys,$csv_fields);
          for($j=0;$j<=$number_of_fields;$j++)
          {  
            $value = trim($data[($csv_fields_keys[$j]-1)]);
            $fieldname = $csv_fields[$csv_fields_keys[$j]];
            $tmp[$fieldname][$rowcounter+1] = $value;
            $tmp['cmd'][$rowcounter+1] = 'create';
            $tmp['checked'][$rowcounter+1] = 1;
          }
        }
      }
      $number_of_rows = $rowcounter;
      fclose($handle);
      unlink($stueckliste_csv);
    }
    return $tmp;
  }


  function ImportvorlageDo()
  {
    $id = $this->app->Secure->GetGET("id");
    $ziel = $this->app->DB->Select("SELECT ziel FROM importvorlage WHERE id='$id' LIMIT 1");
    $fields = $this->ImportvorlageGetFields($id);

    $ekpreisaenderungen = 0;
    $vkpreisaenderungen = 0;

    $tmp = $this->app->Secure->GetPOST("row");


    $stueckliste_csv = $this->app->Secure->GetPOST("importdateiname");
    if($stueckliste_csv !="")
    {

      $tmp = $this->ImportvorlageGetCSV($stueckliste_csv,$id);
    }

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

          // pruefe ob es artikelnummer schon gibt
          if($artikelid > 0)
            $tmp[cmd][$i]="update";

          // wenn es artikel nicht gibt muss man diesen neu anlegen
          if($tmp[cmd][$i]=="create" && $tmp[checked][$i]=="1")
          {
            if($tmp[name_de]!="")
            {
              foreach($fields as $key=>$value)
                $felder[$value]=html_entity_decode($this->app->erp->fixeUmlaute($tmp[$value][$i]));
            }

            if($tmp[nummer][$i]=="")
              $felder[nummer]=$this->app->erp->GetNextArtikelnummer($tmp[typ][$i]);
            else
              $felder[nummer]=$tmp[nummer][$i];

            // ek preis
            if($lieferantid <=0 && $tmp[lieferantname][$i]!="")
            {
              $lieferantid = $this->app->erp->CreateAdresse($tmp[lieferantname][$i]);
              -                                               $this->app->erp->AddRolleZuAdresse($lieferantid, "Lieferant", "von","Projekt",$tmp[projekt][$i]);
            }
            if($lieferantid>0)
              $felder[adresse]=$lieferantid;
            // mit welcher Artikelgruppe?
            $artikelid = $this->app->erp->ImportCreateArtikel($felder);

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
            if($tmp[variante_von][$i]!="")
            {
              // schaue ob              
              $this->app->erp->LogFile("test");
              $tmpartikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$tmp[variante_von][$i]."' AND nummer!='' LIMIT 1");
              $this->app->erp->LogFile("test 2 $artikelid");
              if($tmpartikelid > 0)
              {
                $this->app->DB->Update("UPDATE artikel SET variante_von='".$tmpartikelid."',variante=1 
                    WHERE id='".$artikelid."' AND id!='".$tmpartikelid."' LIMIT 1");

              } 
            }
            if($tmp[aktiv][$i]=="1")
            {
              $this->app->DB->Update("UPDATE artikel SET inaktiv=0 WHERE id='".$artikelid."' LIMIT 1");
            } 
            if($tmp[aktiv][$i]=="0")
            {
              $this->app->DB->Update("UPDATE artikel SET inaktiv=1 WHERE id='".$artikelid."' LIMIT 1");
            } 
            // prozentzeichen entfernen
            $tmp[umsatzsteuer][$i] = str_replace('%','',$tmp[umsatzsteuer][$i]);
/*
            if($tmp[umsatzsteuer][$i]=="" || $tmp[umsatzsteuer][$i]=="19.00" || $this->app->erp->Firmendaten("steuersatz_normal")==$tmp[umsatzsteuer][$i] 
              || $tmp[umsatzsteuer][$i]=="19%" || $tmp[umsatzsteuer][$i]=="19.00%" || $tmp[umsatzsteuer][$i]=="19" || $tmp[umsatzsteuer][$i]=="normal")
            {
            } 
 */           
            // standard standardsteuersatz 
            $this->app->DB->Update("UPDATE artikel SET umsatzsteuer='normal' WHERE id='".$artikelid."' LIMIT 1");

            if($tmp[umsatzsteuer][$i]=="7.00" || $tmp[umsatzsteuer][$i]=="7%" || $tmp[umsatzsteuer][$i]=="7.00%" || $tmp[umsatzsteuer][$i]=="7" 
                || $this->app->erp->Firmendaten("steuersatz_ermaessigt")==$tmp[umsatzsteuer][$i]
              || $tmp[umsatzsteuer][$i]=="ermaessigt")
            {
              $this->app->DB->Update("UPDATE artikel SET umsatzsteuer='ermaessigt' WHERE id='".$artikelid."' LIMIT 1");
            } 

            $lager_id = $this->app->DB->Select("SELECT id FROM lager WHERE geloescht!='1' LIMIT 1");
            $this->app->erp->LogFile("Lager: ".$tmp[lager_platz][$i]." test 2 $artikelid");
            if($tmp[lager_platz][$i]!=""){
            $this->app->erp->LogFile("Lager 2: ".$tmp[lager_platz][$i]." test 2 $artikelid");
              $this->app->DB->Update("UPDATE artikel SET lagerartikel='1' WHERE id='$artikelid' LIMIT 1");
              $regal = $this->app->erp->CreateLagerplatz($lager_id,$tmp[lager_platz][$i]);
              $this->app->erp->LagerEinlagernDifferenz($artikelid,$tmp[lager_menge][$i],$regal,"","Erstbef&uuml;llung",1);
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
                  case "umsatzsteuer":
                    if($tmp[$value][$i]=="" || $tmp[$value][$i]=="19.00" || 
                        $tmp[$value][$i]=="19%" || $tmp[$value][$i]=="19.00%" || $tmp[$value][$i]=="19")
                    {
                      $this->app->DB->Update("UPDATE artikel SET umsatzsteuer='normal' WHERE id='".$artikelid."' LIMIT 1");
                    } 
                    if($tmp[$value][$i]=="7.00" || $tmp[$value][$i]=="7%" || $tmp[$value][$i]=="7.00%" || $tmp[$value][$i]=="7")
                    {
                      $this->app->DB->Update("UPDATE artikel SET umsatzsteuer='ermaessigt' WHERE id='".$artikelid."' LIMIT 1");
                    } 
                    break;
                  case "aktiv":
                    if($tmp[$value][$i]=="0")
                      $this->app->DB->Update("UPDATE artikel SET inaktiv=1 WHERE id='".$artikelid."' LIMIT 1");
                    else
                      $this->app->DB->Update("UPDATE artikel SET inaktiv=0 WHERE id='".$artikelid."' LIMIT 1");
                    break;
                  case "variante_von":
                    if($tmp[$value][$i]!="")
                    {
                      // schaue ob              
                      $tmpartikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$tmp[$value][$i]."' AND nummer!='' LIMIT 1");
                      if($tmpartikelid > 0)
                      {
                        $this->app->DB->Update("UPDATE artikel SET variante_von='".$tmpartikelid."',variante=1 
                            WHERE id='".$artikelid."' AND id!='".$tmpartikelid."' LIMIT 1");
                      } 
                    }
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
                        //$tmp[lieferanteinkaufnetto][$i] = $tmp[lieferanteinkaufnetto][$i] / $tmp[lieferanteinkaufmenge][$i]; // wieder raus
                        //$tmp[lieferanteinkaufmenge][$i] = 1; // wieder raus
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

          $tmp[checked][$i]=1;

          // automatisch create und update erkennen
          if($tmp[kundennummer][$i]=="" && $tmp[lieferantennummer][$i]=="" && $tmp[name][$i]=="")
          {
            $tmp[cmd][$i]="none";
            $tmp[checked][$i]=0;
          }
          else if($tmp[kundennummer][$i]=="" && $tmp[name][$i]!="" && $tmp[lieferantennummer][$i]=="")
          {
            $tmp[cmd][$i]="create";
          }
          else if($tmp[lieferantennummer][$i]!="" || $tmp[kundennummer][$i]!="")
          {
            $checkkunde = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$tmp[kundennummer][$i]."' AND kundennummer!='' LIMIT 1");
            if($checkkunde <= 0)
              $tmp[cmd][$i]="create";
            else 
              $tmp[cmd][$i]="update";

            $checklieferant = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$tmp[kundennummer][$i]."' AND lieferantennummer!='' LIMIT 1");
            if($checklieferant <= 0)
              $tmp[cmd][$i]="create";
            else 
              $tmp[cmd][$i]="update";
          }

          // automatisch create und update erkennen

          if($tmp[cmd][$i]=="create" && $tmp[checked][$i]=="1")
          {
            $this->app->erp->LogFile("create");
            $adresse=0;
            foreach($fields as $key=>$value)
              $felder[$value]=trim($tmp[$value][$i]);

            if($tmp[kundennummer][$i]!="" || $tmp[lieferantennummer][$i]!="")
            {
              $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$tmp[kundennummer][$i]."' AND kundennummer!='' LIMIT 1");
              if($adresse <=0)
                $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$tmp[lieferantennummer][$i]."' AND lieferantennummer!='' LIMIT 1");
            }
            if($felder[name]!="")
              //if($adresse <=0 && $felder[name]!="")
            { 
              $this->app->erp->LogFile("Lieferantennummer: ".$tmp[lieferantennummer][$i]);
              $this->app->erp->LogFile("Kundennummer: ".$tmp[kundennummer][$i]);
              //adresse anlegen
              $felder['strasse'] = $felder['strasse']." ".$felder['hausnummer'];
              if($felder['strasse_hausnummer']!="") $felder['strasse'] = $felder['strasse_hausnummer'];

              $felder['email'] = str_replace(" ","",$felder['email']);

              switch($felder['typ'])
              {
                case "mr": $felder['typ']="herr"; break;
                case "mr.": $felder['typ']="herr"; break;
                case "ms": $felder['typ']="frau"; break;
                case "mrs": $felder['typ']="frau"; break;
                case "mrs.": $felder['typ']="frau"; break;
              }

              if($felder['firma']!="")
              {
                if($felder['vorname']!="")
                  $felder['ansprechpartner']=$felder['vorname']." ".$felder['name'];
                else
                  $felder['ansprechpartner']=$felder['name'];

                $felder['name']=$felder['firma'];
                $felder['typ']='firma';
              } else 
                if($felder['vorname']!="")
                  $felder['name']=$felder['vorname']." ".$felder['name'];

              $loeschen_lfr_new=false;
              if(strtoupper($felder['lieferantennummer'])=="NEW" || strtoupper($felder['lieferantennummer'])=="NEU")
                $loeschen_lfr_new=true;

              $loeschen_kd_new=false;
              if(strtoupper($felder['kundennummer'])=="NEW" || strtoupper($felder['kundennummer'])=="NEU" )
                $loeschen_kd_new=true;

              if($loeschen_lfr_new) $felder['lieferantennummer']="";
              if($loeschen_kd_new) $felder['kundennummer']="";

              $adresse =$this->app->erp->ImportCreateAdresse($felder);
              if($felder['lieferantennummer']!="" || $loeschen_lfr_new)
              {
                $this->app->erp->AddRolleZuAdresse($adresse, "Lieferant", "von","Projekt",$tmp[projekt][$i]);
              }
              if($felder['kundennummer']!="" || $loeschen_kd_new)
              {
                $this->app->erp->AddRolleZuAdresse($adresse, "Kunde", "von","Projekt",$tmp[projekt][$i]);
              }
              //rolle verpassen
            }



            if($tmp[liefername][$i]!="")
            {
              $tmp['liefername'][$i] = $tmp['liefervorname'][$i]." ".$tmp['liefername'][$i];
              $tmp['lieferstrasse'][$i] = $tmp['lieferstrasse'][$i]." ".$tmp['lieferhausnummer'][$i];

              if($tmp['lieferfirma'][$i]!="")
              {
                $tmp['lieferadresszusatz'][$i]=$tmp['liefervorname'][$i]." ".$tmp['liefername'][$i];
                $tmp['liefername'][$i]=$tmp['lieferfirma'][$i];
                $tmp['liefertyp'][$i]='firma';
              }

              $this->app->DB->Insert("INSERT INTO lieferadressen 
                  (id,name,abteilung,unterabteilung,land,strasse,ort,plz,telefon,telefax,email,ansprechpartner,adresse,typ,adresszusatz,standardlieferadresse)
                  VALUES ('','{$tmp['liefername'][$i]}','{$tmp['lieferabteilung'][$i]}','{$tmp['lieferunterabteilung'][$i]}',
                    '{$tmp['lieferland'][$i]}','{$tmp['lieferstrasse'][$i]}','{$tmp['lieferort'][$i]}',
                    '{$tmp['lieferplz'][$i]}','{$tmp['liefertelefon'][$i]}','{$tmp['liefertelefax'][$i]}','{$tmp['lieferemail'][$i]}',
                    '{$tmp['lieferansprechpartner'][$i]}','$adresse','{$tmp['liefertyp'][$i]}','{$tmp['lieferadresszusatz'][$i]}',1)");
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

                if($key=="typ")
                {
                  switch($tmp[$value][$i])
                  {
                    case "mr": $tmp[$value][$i]="herr"; break;
                    case "mr.": $tmp[$value][$i]="herr"; break;
                    case "mrs": $tmp[$value][$i]="frau"; break;
                    case "mrs.": $tmp[$value][$i]="frau"; break;
                  }
                }

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
      $msg=$this->app->erp->base64_url_encode("<div class=\"info\">Import durchgef&uuml;hrt.</div>");
      header("Location: index.php?module=importvorlage&action=import&id=$id&msg=$msg");
      exit;
    } else {    
      $msg=$this->app->erp->base64_url_encode("<div class=\"info\">Import durchgef&uuml;hrt.</div>");
      header("Location: index.php?module=importvorlage&action=import&id=$id&msg=$msg");
      exit;
    }
  }     


  function ImportPrepareHeader($ziel,$csv_fields_keys,$csv_fields)
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

  function ImportPrepareRow($rowcounter,$ziel,$data,$csv_fields_keys,$csv_fields)
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
          //                                                    if($fields[herstellernummer]<=0) $fields[herstellernummer]="";
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
          //    $value = $data[($csv_fields_keys[$j]-1)];
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
