<?php

class YUI
{

  function YUI(&$app)
  {
    $this->app = &$app;
  }

        function Stroke($fieldstroke, $field)
        {
                 return "if(".$fieldstroke.",CONCAT('<s>',".$field.",'</s>'),".$field.")"; 
        }


  function AARLGEditable()
  {
    $module = $this->app->Secure->GetGET("module");

    $table = $this->AARLGPositionenModule2Tabelle();

    $id = $this->app->Secure->GetPOST("id"); //ACHTUNG auftrag_positions tabelle id
   
    $tmp = split('split',$id);

    $id = $tmp[0];
   
    $column = $tmp[1];

    $value = $this->app->Secure->GetPOST("value");
    $cmd = $this->app->Secure->GetGET("cmd");

                $column = $column - 1;

                if($module=="arbeitsnachweis")
                {
                         switch($column)
    {

      case 1: // ort
        $this->app->DB->Update("UPDATE $table SET ort='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT ort FROM $table WHERE id='$id' LIMIT 1");
      break;

      case 2: // Datum
        $value = $this->app->String->Convert($value,"%1.%2.%3","%3-%2-%1");
        $this->app->DB->Update("UPDATE $table SET datum='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT datum FROM $table WHERE id='$id' LIMIT 1");
        $result = $this->app->String->Convert($result,"%3-%2-%1","%1.%2.%3");
      break;

      case 3: // von
        $this->app->DB->Update("UPDATE $table SET von='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT von FROM $table WHERE id='$id' LIMIT 1");
      break;
                        case 4: // bis
        $this->app->DB->Update("UPDATE $table SET bis='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT bis FROM $table WHERE id='$id' LIMIT 1");
      break;
      case 5: //bezeichnung
        $this->app->DB->Update("UPDATE $table SET bezeichnung='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT bezeichnung FROM $table WHERE id='$id' LIMIT 1");
      break;
      default:
      ;
    }
                }
                else if($module=="reisekosten")
                {
                         switch($column)
    {

      case 0: //Datum
        $value = $this->app->String->Convert($value,"%1.%2.%3","%3-%2-%1");
        $this->app->DB->Update("UPDATE $table SET datum='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT datum FROM $table WHERE id='$id' LIMIT 1");
        $result = $this->app->String->Convert($result,"%3-%2-%1","%1.%2.%3");
      break;

      case 2: // Betrag
                        $value = str_replace(",",".",$value);
        $this->app->DB->Update("UPDATE $table SET betrag='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT betrag FROM $table WHERE id='$id' LIMIT 1");

      break;

      case 6: // bezeichnung
        $this->app->DB->Update("UPDATE $table SET bezeichnung='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT bezeichnung FROM $table WHERE id='$id' LIMIT 1");
      break;
      default:
      ;
    }
                }

                else if($module=="inventur")
                {
                         switch($column)
    {

      case 0: //Bezeichnung
        $this->app->DB->Update("UPDATE $table SET bezeichnung='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT bezeichnung FROM $table WHERE id='$id' LIMIT 1");
      break;

      case 2: // Nummer
        $this->app->DB->Update("UPDATE $table SET nummer='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT nummer FROM $table WHERE id='$id' LIMIT 1");
      break;

                case 3: // Menge
        $this->app->DB->Update("UPDATE $table SET menge='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT menge FROM $table WHERE id='$id' LIMIT 1");
      break;

      case 4: // preis
                        $value = str_replace(",",".",$value);
        $this->app->DB->Update("UPDATE $table SET preis='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT preis FROM $table WHERE id='$id' LIMIT 1");

      break;

      default:
      ;
    }
                }


                else if($module=="produktion")
                {
                         switch($column)
    {
      case 3: // Menge
        $value = str_replace(",",".",$value);
        $this->app->DB->Update("UPDATE $table SET menge='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT menge FROM $table WHERE id='$id' LIMIT 1");
      break;

                }       

                } else {

    switch($column)
    {
      case 3: // Datum
        $value = $this->app->String->Convert($value,"%1.%2.%3","%3-%2-%1");
        $this->app->DB->Update("UPDATE $table SET lieferdatum='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT lieferdatum FROM $table WHERE id='$id' LIMIT 1");
        $result = $this->app->String->Convert($result,"%3-%2-%1","%1.%2.%3");
      break;

      case 4: // Menge
        $value = str_replace(",",".",$value);

                                if($table=="bestellung_position")
                                {
                                        // schau was mindestmenge bei diesem lieferant ist
                        //$tmpartikel = $this->app->DB->Select("SELECT artikel FROM $table WHERE id='$id' LIMIT 1");
                                }

        $this->app->DB->Update("UPDATE $table SET menge='$value' WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT menge FROM $table WHERE id='$id' LIMIT 1");

                                // Menge im Lager reserviert anpassen
                                if($table=="auftrag_position")
                                {
          $tmpartikel = $this->app->DB->Select("SELECT artikel FROM $table WHERE id='$id' LIMIT 1");
          $tmptable_value = $this->app->DB->Select("SELECT auftrag FROM $table WHERE id='$id' LIMIT 1");
          $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE artikel='$tmpartikel' AND objekt='auftrag' AND parameter='$tmptable_value'");
                $this->app->erp->AuftragEinzelnBerechnen($tmptable_value);

                                        //header("Location: index.php?module=auftrag&action=edit&id=$id#tabs-2");
                                        //exit;
                                }
                                
                                


      break;
      case 5: //preis
        $value = str_replace(",",".",$value);
        $this->app->DB->Update("UPDATE $table SET preis='$value' WHERE id='$id' LIMIT 1");
        $this->app->DB->Update("UPDATE $table SET keinrabatterlaubt='1',rabatt=0 WHERE id='$id' LIMIT 1");
        $result = $this->app->DB->Select("SELECT preis FROM $table WHERE id='$id' LIMIT 1");
      break;
      default:
      ;
    }
                }



    if($cmd=="load")
      echo "Load";
    else
      echo $result;
    exit;

  }



  function AARLGPositionenModule2Tabelle()
  {
    $module = $this->app->Secure->GetGET("module");
    if($module=="auftrag") $table = "auftrag_position";
    else if($module=="angebot") $table = "angebot_position";
    else if($module=="lieferschein") $table = "lieferschein_position";
    else if($module=="rechnung") $table = "rechnung_position";
    else if($module=="gutschrift") $table = "gutschrift_position";
    else if($module=="bestellung") $table = "bestellung_position";
    else if($module=="produktion") $table = "produktion_position";
    else if($module=="arbeitsnachweis") $table = "arbeitsnachweis_position";
    else if($module=="reisekosten") $table = "reisekosten_position";
    else if($module=="inventur") $table = "inventur_position";
    else if($module=="anfrage") $table = "anfrage_position";
    else exit;
    return $table;


  }



  function AARLGPositionen($iframe=true)
  {
    $module = $this->app->Secure->GetGET("module");
    
                if($this->app->erp->Firmendaten("mysql55")=="1")
                {
                        $extended_mysql55 = ",'de_DE'";
                }

                $id = $this->app->Secure->GetGET("id");

                $this->app->DB->Select("set @order = 0;");
    $this->app->DB->Update("update ".$module."_position set sort=@order:= @order + 1 WHERE ".$module."='$id' order by sort asc");

    if($iframe)
    {
      $this->app->Tpl->Set(POS,"<iframe name=\"framepositionen\" id=\"framepositionen\" style=\"\" src=\"index.php?module=$module&action=positionen&id=$id\" frameborder=\"no\" width=\"100%\" height=\"850\"></iframe>");
    }
    else {

    $table = $this->AARLGPositionenModule2Tabelle();
  
    /* neu anlegen formular */
    $artikelart = $this->app->Secure->GetPOST("artikelart");
    $bezeichnung = $this->app->Secure->GetPOST("bezeichnung");
    $vpe = $this->app->Secure->GetPOST("vpe");
    $umsatzsteuerklasse = $this->app->Secure->GetPOST("umsatzsteuerklasse");
    $waehrung = $this->app->Secure->GetPOST("waehrung");
    $projekt= $this->app->Secure->GetPOST("projekt");
    $preis = $this->app->Secure->GetPOST("preis");
    $preis = str_replace(',','.',$preis);
    $menge = $this->app->Secure->GetPOST("menge");
    $ort = $this->app->Secure->GetPOST("ort");
    $lieferdatum = $this->app->Secure->GetPOST("lieferdatum");
    $lieferdatum  = $this->app->String->Convert($lieferdatum,"%1.%2.%3","%3-%2-%1");
    $datum = $this->app->Secure->GetPOST("datum");
    $datum  = $this->app->String->Convert($datum,"%1.%2.%3","%3-%2-%1");

    if($lieferdatum=="") $lieferdatum="00.00.0000";

    $ajaxbuchen = $this->app->Secure->GetPOST("ajaxbuchen");
    if($ajaxbuchen!="")
    { 
      $artikel = $this->app->Secure->GetPOST("artikel");
      $nummer = $this->app->Secure->GetPOST("nummer");
      $projekt = $this->app->Secure->GetPOST("projekt");
      $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM $table WHERE $module='$id' LIMIT 1");
      $sort = $sort + 1;
      $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1");
      $bezeichnung = $artikel;
      $neue_nummer = $nummer;
      $waehrung = $this->app->DB->Select("SELECT waehrung FROM $module WHERE id='$id' LIMIT 1");

      $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id='$artikel_id' LIMIT 1");
      $beschreibung = $this->app->DB->Select("SELECT anabregs_text FROM artikel WHERE id='$artikel_id' LIMIT 1");

      $mlmpunkte = $this->app->DB->Select("SELECT mlmpunkte FROM artikel WHERE id='$artikel_id' LIMIT 1");
      $mlmbonuspunkte = $this->app->DB->Select("SELECT mlmbonuspunkte FROM artikel WHERE id='$artikel_id' LIMIT 1");
      $mlmdirektpraemie = $this->app->DB->Select("SELECT mlmdirektpraemie FROM artikel WHERE id='$artikel_id' LIMIT 1");

                        if($vpe < 1 || !is_numeric($vpe))
        $vpe = '1';

      if($module=="lieferschein" && $artikel_id > 0)
      {
        $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,bezeichnung,beschreibung,nummer,menge,sort,lieferdatum, status,projekt,vpe)
          VALUES ('','$id','$artikel_id','$bezeichnung','$beschreibung','$neue_nummer','$menge','$sort','$lieferdatum','angelegt','$projekt','$vpe')");

      }
                else if($module=="arbeitsnachweis")
      {
                $bezeichnung = $this->app->Secure->GetPOST("bezeichnung");
                $von = $this->app->Secure->GetPOST("von");
                $bis = $this->app->Secure->GetPOST("bis");
                $adresse = $this->app->Secure->GetPOST("adresse");

                                $adresse =explode(' ',$adresse);
                                $adresse = $adresse[0];
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer='$adresse' LIMIT 1");
                $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,bezeichnung,nummer,menge,sort,datum, status,projekt,ort,von,bis,adresse)
          VALUES ('','$id','$artikel_id','$bezeichnung','$neue_nummer','$menge','$sort','$datum','angelegt','$projekt','$ort','$von','$bis','$adresse')");

      }

                else if($module=="reisekosten")
      {
                $bezeichnung = $this->app->Secure->GetPOST("bezeichnung");
                $betrag = $this->app->Secure->GetPOST("betrag");
                $betrag = str_replace(',','.',$betrag);
                $reisekostenart = $this->app->Secure->GetPOST("reisekostenart");
                $abrechnen = $this->app->Secure->GetPOST("abrechnen");
                $keineust = $this->app->Secure->GetPOST("keineust");
                $uststeuersatz = $this->app->Secure->GetPOST("uststeuersatz");
                $bezahlt_wie = $this->app->Secure->GetPOST("bezahlt_wie");
                                /*adresse = $this->app->Secure->GetPOST("adresse");
                                $adresse =explode(' ',$adresse);
                                $adresse = $adresse[0];
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer='$adresse' LIMIT 1");
                                */
                $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,bezeichnung,nummer,menge,sort,datum, status,projekt,ort,von,bis,betrag,bezahlt_wie,reisekostenart,abrechnen,keineust,uststeuersatz)
          VALUES ('','$id','$artikel_id','$bezeichnung','$neue_nummer','$menge','$sort','$datum','angelegt','$projekt','$ort','$von','$bis','$betrag','$bezahlt_wie','$reisekostenart','$abrechnen','$keineust','$uststeuersatz')");

      }

                else if($module=="inventur" && $artikel_id>0)
      {
                $bezeichnung = $this->app->Secure->GetPOST("artikel");
                $preis = $this->app->Secure->GetPOST("preis");
                $preis = str_replace(',','.',$preis);
        $nummer = $this->app->Secure->GetPOST("nummer");
                                /*adresse = $this->app->Secure->GetPOST("adresse");
                                $adresse =explode(' ',$adresse);
                                $adresse = $adresse[0];
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer='$adresse' LIMIT 1");
                                */
                                $projekt = $this->app->Secure->GetPOST("projekt");
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
        $sort = $this->app->DB->Select("SELECT MAX(sort) FROM $table WHERE $module='$id' LIMIT 1");
        $sort = $sort + 1;
        $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1");

                $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,bezeichnung,nummer,menge,sort,projekt,preis)
          VALUES ('','$id','$artikel_id','$bezeichnung','$nummer','$menge','$sort','$projekt','$preis')");
      }

                else if($module=="anfrage" && $artikel_id > 0)
      {
/*
                $bezeichnung = $this->app->Secure->GetPOST("artikel");
                $preis = $this->app->Secure->GetPOST("preis");
                $preis = str_replace(',','.',$preis);
        $nummer = $this->app->Secure->GetPOST("nummer");
*/
                                /*adresse = $this->app->Secure->GetPOST("adresse");
                                $adresse =explode(' ',$adresse);
                                $adresse = $adresse[0];
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE mitarbeiternummer='$adresse' LIMIT 1");
                                */
                                $projekt = $this->app->Secure->GetPOST("projekt");
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
        $sort = $this->app->DB->Select("SELECT MAX(sort) FROM $table WHERE $module='$id' LIMIT 1");
        $sort = $sort + 1;
        $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1");

                $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,bezeichnung,nummer,menge,sort,projekt,preis)
          VALUES ('','$id','$artikel_id','$bezeichnung','$nummer','$menge','$sort','$projekt','$preis')");
      }


      else if($module=="bestellung" && $artikel_id > 0)
      {
                                $bestellnummer = $this->app->Secure->GetPOST("bestellnummer");
                                $bezeichnunglieferant = $this->app->Secure->GetPOST("bezeichnunglieferant");
                                //hier muesste man beeichnung bei lieferant auch noch speichern .... oder beides halt

                                $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,bezeichnunglieferant,beschreibung,bestellnummer,menge,sort,lieferdatum, status,projekt,vpe,preis,waehrung,umsatzsteuer)
          VALUES ('','$id','$artikel_id','$bezeichnunglieferant','$beschreibung','$bestellnummer','$menge','$sort','$lieferdatum','angelegt','$projekt','$vpe','$preis','$waehrung','$umsatzsteuer')");
      }
      else if($module=="produktion" && $artikel_id > 0) {
                                // mlm punkte bei angebot, auftrag und rechnung
        $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,beschreibung,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe)
          VALUES ('','$id','$artikel_id','$beschreibung','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuer','angelegt','$projekt','$vpe')");
      }

      else if($module=="gutschrift" && $artikel_id > 0) {
                                // mlm punkte bei angebot, auftrag und rechnung
        $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,beschreibung,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe)
          VALUES ('','$id','$artikel_id','$beschreibung','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuer','angelegt','$projekt','$vpe')");

                                    $this->app->erp->GutschriftNeuberechnen($id);
      }

      else if($module=="auftrag"||$module=="rechnung"||$module=="angebot") {
                                if($artikel_id > 0){
                                // mlm punkte bei angebot, auftrag und rechnung
        $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,beschreibung,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe,punkte,bonuspunkte,mlmdirektpraemie)
          VALUES ('','$id','$artikel_id','$beschreibung','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuer','angelegt','$projekt','$vpe','$mlmpunkte','$mlmbonuspunkte','$mlmdirektpraemie')");

                                        switch($module)
                                        {
                                                case "angebot": $this->app->erp->AngebotNeuberechnen($id); break;
                                                case "auftrag": $this->app->erp->AuftragNeuberechnen($id); $this->app->erp->AuftragEinzelnBerechnen($id); break;
                                                case "rechnung": $this->app->erp->RechnungNeuberechnen($id); break;
                                        }       
                                }
      }


      else {
                                if($artikel_id > 0){
                                // mlm punkte bei angebot, auftrag und rechnung
        $this->app->DB->Insert("INSERT INTO $table (id,$module,artikel,beschreibung,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe,punkte,bonuspunkte,mlmdirektpraemie)
          VALUES ('','$id','$artikel_id','$beschreibung','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuer','angelegt','$projekt','$vpe','$mlmpunkte','$mlmbonuspunkte','$mlmdirektpraemie')");
                                }
      }


    }

                if($module=="produktion")
                {
                        $this->app->erp->AuftragExplodieren($id,"produktion");
                        $this->app->erp->AuftragReservieren($id,"produktion");
        $this->app->erp->ProduktionEinzelnBerechnen($id);
        $this->app->erp->ProduktionNeuberechnen($id);
                }

                if($module=="auftrag")
                {
                        $this->app->erp->AuftragExplodieren($id,"auftrag");
                }



      /* ende neu anlegen formular */


      $this->app->Tpl->Set(SUBSUBHEADING,"Positionen");


      $menu = array("up"=>"up{$module}position",
                          "down"=>"down{$module}position",
                          //"add"=>"addstueckliste",
                          "edit"=>"positioneneditpopup",
                          "del"=>"del{$module}position");

      if($module=="auftrag")
      {
 $sql = "SELECT 
        b.sort,
                                if(b.explodiert_parent,if(b.beschreibung!='',
                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT('<i>',SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...','</i>'),CONCAT('<i>',b.bezeichnung,' *','</i>')),
                                                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT('<i>',SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung()."),'...','</i>'),CONCAT('<i>',b.bezeichnung,' (zu St&uuml;ckliste ',(SELECT ba.nummer FROM $table ba WHERE ba.id=b.explodiert_parent LIMIT 1),')</i>'))),
if(b.beschreibung!='',
 if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),CONCAT(b.bezeichnung,' *')),
            if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),b.bezeichnung))
)
                                                as Artikel,



                p.abkuerzung as projekt, b.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, b.menge as menge, b.preis as preis, b.rabatt as rabatt, b.id as id
                FROM $table b
                LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                WHERE b.$module='$id' ";
                //WHERE b.$module='$id' AND b.explodiert_parent='0'";
      }

      else if($module=="lieferschein")
      {
  $sql = "SELECT 
b.sort,
if(b.beschreibung!='',
                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),CONCAT(b.bezeichnung,' *')),
                                                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),b.bezeichnung))
                                                as Artikel,


                p.abkuerzung as projekt, b.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, b.menge as menge, if(b.geliefert, b.geliefert,'-') as geliefert, b.id as id
                FROM $table b
                LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                WHERE b.$module='$id'";
      } 

                        else if($module=="inventur")
      {
  $sql = "SELECT 
b.sort,
if(b.beschreibung!='',
                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),CONCAT(b.bezeichnung,' *')),
                                                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),b.bezeichnung))
                                                as Artikel,


                p.abkuerzung as projekt, b.nummer as nummer, b.menge as menge, 
b.preis,

b.id as id
                FROM $table b
                LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                WHERE b.$module='$id'";
      } 
                        else if($module=="anfrage")
      {
  $sql = "SELECT 
b.sort,
if(b.beschreibung!='',
                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),CONCAT(b.bezeichnung,' *')),
                                                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),b.bezeichnung))
                                                as Artikel,


                p.abkuerzung as projekt, b.nummer as nummer, DATE_FORMAT(b.lieferdatum,'%d.%m.%Y') as lieferdatum, b.menge as menge, if(b.geliefert, b.geliefert,'-') as geliefert,

b.id as id
                FROM $table b
                LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                WHERE b.$module='$id'";
      } 


      else if($module=="bestellung")
      {
  $sql = "SELECT b.sort,if(b.beschreibung!='',
                if(CHAR_LENGTH(a.name_de)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(CONCAT(a.name_de,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),CONCAT(a.name_de,' *')),
                                                if(CHAR_LENGTH(a.name_de)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(a.name_de,1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),a.name_de))
                                                as Artikel,
                p.abkuerzung as projekt,  a.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, b.menge as menge, b.preis as preis, b.id as id
                FROM $table b
                LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                WHERE b.$module='$id'";

      } 


      else if($module=="arbeitsnachweis")
      {
 $sql = "SELECT b.sort,
                adr.name as name,
                b.ort as ort,
                DATE_FORMAT(datum,'%d.%m.%Y') as rdatum,
                b.von as von,
                b.bis as bis,

                if(b.beschreibung!='',
                                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),CONCAT(b.bezeichnung,' *')),
                                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),b.bezeichnung))
                                                as Artikel,
                 b.id as id
                FROM $table b
                LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN adresse adr ON adr.id=b.adresse LEFT JOIN projekt p ON b.projekt=p.id
                WHERE b.$module='$id'";
      } 


      else if($module=="reisekosten")
      {
                 $sql = "SELECT b.sort,
                DATE_FORMAT(datum,'%d.%m.%Y') as rdatum,
                CONCAT(rk.nummer,'- ',rk.beschreibung) as kostenart,
                FORMAT(b.betrag,2{$extended_mysql55}) as betrag,
                if(b.abrechnen,'ja','') as abrechnen,
                if(b.keineust,'keine MwSt','') as keine,
                CONCAT(b.uststeuersatz,' %') as uststeuersatz,

                if(b.beschreibung!='',
                                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung(-20).",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung(-20)."),'...'),CONCAT(b.bezeichnung,' *')),
                                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung(-20).",CONCAT(SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung(-20)."),'...'),b.bezeichnung))
                                                as Artikel,
                b.bezahlt_wie as bezahlt,
                 b.id as id
                FROM $table b
                LEFT JOIN projekt p ON b.projekt=p.id LEFT JOIN reisekostenart rk ON rk.id=b.reisekostenart
                WHERE b.$module='$id'";
      } 


      else if($module=="produktion")
      {
      $sql = "
SELECT 
b.sort,
       if(b.explodiert_parent,if(b.beschreibung!='',
    if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT('<i>',SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...','</i>'),CONCAT('<i>',b.bezeichnung,' *','</i>')),
            if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT('<i>',SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung()."),'...','</i>'),CONCAT('<i>',b.bezeichnung,' (zu St&uuml;ckliste ',(SELECT ba.nummer FROM $table ba WHERE ba.id=b.explodiert_parent LIMIT 1),')</i>'))),
if(b.beschreibung!='',
 if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),CONCAT(b.bezeichnung,' *')),
            if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),b.bezeichnung))
)
            as Artikel,



        p.abkuerzung as projekt,        a.nummer as nummer, b.nummer as nummer, b.menge as menge, 
                (SELECT SUM(l.menge) FROM lager_platz_inhalt l  WHERE l.artikel=a.id) as lager, 
if((SELECT SUM(l.menge) FROM lager_reserviert l WHERE l.artikel=a.id AND l.objekt='produktion' AND l.parameter='$id'),
                CONCAT((SELECT SUM(l.menge) FROM lager_reserviert l WHERE l.artikel=a.id AND l.objekt='produktion' AND l.parameter='$id'),'&nbsp;(',
(SELECT SUM(l.menge) FROM lager_reserviert l WHERE l.artikel=a.id),')'
                ),CONCAT('<font color=red>0&nbsp;(',(SELECT SUM(l.menge) FROM lager_reserviert l WHERE l.artikel=a.id),')</font>')) as reserviert, 

b.id as id
                FROM $table b
                LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                WHERE b.$module='$id'";

      } 



      else if($module=="rechnung" || $module=="angebot" || $module=="gutschrift") {
      //$sql = "SELECT if(b.beschreibung!='',if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),CONCAT(b.bezeichnung,' *'),SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung().")) as Artikel,
      $sql = "SELECT b.sort, if(b.beschreibung!='',
                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),CONCAT(b.bezeichnung,' *')),
                                                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),b.bezeichnung))
                                                as Artikel,
                p.abkuerzung as projekt, a.nummer as nummer, b.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, b.menge as menge, b.preis as preis, b.rabatt as rabatt, b.id as id
                FROM $table b
                LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                WHERE b.$module='$id'";
      }


      else {
      //$sql = "SELECT if(b.beschreibung!='',if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),CONCAT(b.bezeichnung,' *'),SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung().")) as Artikel,
      $sql = "SELECT b.sort, if(b.beschreibung!='',
                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(CONCAT(b.bezeichnung,' *'),1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),CONCAT(b.bezeichnung,' *')),
                                                if(CHAR_LENGTH(b.bezeichnung)>".$this->app->erp->MaxArtikelbezeichnung().",CONCAT(SUBSTR(b.bezeichnung,1,".$this->app->erp->MaxArtikelbezeichnung()."),'...'),b.bezeichnung))
                                                as Artikel,
                p.abkuerzung as projekt, a.nummer as nummer, b.nummer as nummer, DATE_FORMAT(lieferdatum,'%d.%m.%Y') as lieferdatum, b.menge as menge, b.preis as preis, b.id as id
                FROM $table b
                LEFT JOIN artikel a ON a.id=b.artikel LEFT JOIN projekt p ON b.projekt=p.id
                WHERE b.$module='$id'";
      }

      //$this->app->Tpl->Add(EXTEND,"<input type=\"submit\" value=\"Gleiche Positionen zusammenf&uuml;gen\">");

      $this->app->YUI->SortListAdd(TAB1,$this,$menu,$sql);
                
                        $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM $module WHERE id='$id'");
                        if($schreibschutz!="1")
                        {
      $this->app->Tpl->Add(TAB1,"<br><center><!--<input type=\"button\" value=\"Gleiche Positionen zusammenf&uuml;gen\">&nbsp;-->
          &nbsp;<input type=\"button\" value=\"Artikel: Suche oder Neu anlegen\" onclick=\"window.location.href='index.php?module=artikel&action=profisuche&cmd={$module}&id=$id';\"></center>");
                        }
      $this->app->BuildNavigation=false;

      $this->app->Tpl->Add(PAGE,"<br><fieldset><legend>Positionen</legend>");
                        if($module=="arbeitsnachweis")
        $this->app->Tpl->Parse(PAGE,"arbeitsnachweis_positionuebersicht.tpl");
                        else
        $this->app->Tpl->Parse(PAGE,"auftrag_positionuebersicht.tpl");
      $this->app->Tpl->Add(PAGE,"</fieldset>");



    }


  }


  function ParserVarIf($parsvar,$choose)
  {
    if($choose==0)
    {
        $this->app->Tpl->Set($parsvar."IF","<!--");
        $this->app->Tpl->Set($parsvar."ELSE","-->");
        $this->app->Tpl->Set($parsvar."ENDIF","");
    } else {
        $this->app->Tpl->Set($parsvar."IF","");
        $this->app->Tpl->Set($parsvar."ELSE","<!--");
        $this->app->Tpl->Set($parsvar."ENDIF","-->");
    }

  }
  

  function ColorPicker($name)
  {
          $this->app->Tpl->Add(JQUERY,'$( "#'.$name.'" ).colorPicker();');  
  }



  function DatePicker($name)
  {
          $this->app->Tpl->Add(JQUERY,'$( "#'.$name.'" ).datepicker({ dateFormat: \'dd.mm.yy\',dayNamesMin: [\'SO\', \'MO\', \'DI\', \'MI\', \'DO\', \'FR\', \'SA\'], firstDay:1,
                        showWeek: true, monthNames: [\'Januar\', \'Februar\', \'März\', \'April\', \'Mai\', 
        \'Juni\', \'Juli\', \'August\', \'September\', \'Oktober\',  \'November\', \'Dezember\'], });');  
  }


  function TimePicker($name)
  {
          $this->app->Tpl->Add(JQUERY,'$( "#'.$name.'" ).timepicker();');  
  }


  function Message($class,$msg)
  {
    $this->app->Tpl->Add(MESSAGE,"<div class=\"$class\">$msg</div>");
  }



  function IconsSQLAll()
  {

//  $go_lager = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/lagergo.png\" border=\"0\">";
//  $stop_lager = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/lagerstop.png\" border=\"0\">";

    $abgeschlossen = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/grey.png\" border=\"0\">";
    $angelegt = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/dokumentoffen.png\" border=\"0\">";
    $storniert = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/storno.png\" border=\"0\">";
    $go_lager = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/dokumentok.png\" border=\"0\">";

    for($i=0;$i<1;$i++)
      $tmp .= $abgeschlossen;

    for($i=0;$i<1;$i++)
      $tmpblue .= $angelegt;


    for($i=0;$i<1;$i++)
      $tmpstorno .= $storniert;

                return "if(a.status='angelegt','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmpblue</td></tr></table>',
        if(a.status='abgeschlossen' or a.status='storniert',
    if(a.status='abgeschlossen','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmp</td></tr></table>','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmpstorno</td></tr></table>'),

    CONCAT('<table cellpadding=0 cellspacing=0><tr><td nowrap>',
  if(1,'$go_lager','$stop_lager'),'</td></tr></table>'
  )))";


  }  


  function IconsSQLProduktion()
  {
                // es gibt noch lagerwait.png
    $go_lager = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/lagergo.png\" title=\"Artikel ist im Lager\" border=\"0\">";
    $stop_lager = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/lagerstop.png\" title=\"Artikel fehlt im Lager\" border=\"0\">";

    $abgeschlossen = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/grey.png\" title=\"Produktion abgeschlossen\" border=\"0\">";
    $storniert = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/storno.png\" title=\"Produktion storniert!\" border=\"0\">";

    $angelegt = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/blue.png\" title=\"Produktion noch nicht freigegeben!\" border=\"0\">";
    $gestartet = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/orange.png\" title=\"Produktion ist bereits gestartet!\" border=\"0\">";

    for($i=0;$i<1;$i++)
      $tmp .= $abgeschlossen;

    for($i=0;$i<1;$i++)
      $tmpblue .= $angelegt;


    for($i=0;$i<1;$i++)
      $tmpstorno .= $storniert;

if($this->app->Conf->WFdbType=="postgre") {
    return "if(a.status='angelegt','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmpblue</td></tr></table>',
        if(a.status='abgeschlossen' or a.status='storniert',
    if(a.status='abgeschlossen','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmp</td></tr></table>','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmpstorno</td></tr></table>'),

    CONCAT('<table cellpadding=0 cellspacing=0><tr><td nowrap>',
  if(a.lager_ok,'$go_lager','$stop_lager'),'</td></tr></table>'
  )))";
        } else {
                return "if(a.status='angelegt',
                                                                        '<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmpblue</td></tr></table>',
                                                if(a.status='abgeschlossen' or a.status='storniert',
                                                                                                if(a.status='abgeschlossen',
                                                                                                                                                '<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmp</td></tr></table>',
                                                                                                                                                '<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmpstorno</td></tr></table>'),

                                                                                                if(a.status='gestartet',
                                                                                                                                CONCAT('<table cellpadding=0 cellspacing=0><tr><td nowrap>$gestartet</td></tr></table>'),
                                                                                                                                CONCAT('<table cellpadding=0 cellspacing=0><tr><td nowrap>',if(a.lager_ok,'$go_lager','$stop_lager'),'</td></tr></table>'))
                                                                        )
                                                        )";
        }

  }  


  function IconsSQL()
  {

    $go_lager = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/lagergo.png\" style=\"margin-right:1px\" title=\"Artikel ist im Lager\" border=\"0\">";
    $stop_lager = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/lagerstop.png\" style=\"margin-right:1px\" title=\"Artikel fehlt im Lager\" border=\"0\">";

    $go_porto = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/portogo.png\" style=\"margin-right:1px\" title=\"Porto Check OK\" border=\"0\">";
    $stop_porto = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/portostop.png\" style=\"margin-right:1px\" title=\"Porto fehlt!\" border=\"0\">";

    $go_ust = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/ustgo.png\" title=\"UST Check OK\" border=\"0\" style=\"margin-right:1px\">";
    $stop_ust = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/uststop.png\" title=\"UST-Pr&uuml;fung fehlgeschlagen!\" border=\"0\" style=\"margin-right:1px\">";


    $go_vorkasse = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/vorkassego.png\" title=\"Zahlungscheck OK\" border=\"0\" style=\"margin-right:1px\">";
    $stop_vorkasse = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/vorkassestop.png\" title=\"Zahlungseingang bei Vorkasse fehlt!\" border=\"0\" style=\"margin-right:1px\">";

    $go_nachnahme = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/nachnahmego.png\" title=\"Nachnahme Check OK\" border=\"0\" style=\"margin-right:1px\">";
    $stop_nachnahme = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/nachnahmestop.png\" title=\"Nachnahmegeb&uuml;hr fehlt!\" border=\"0\" style=\"margin-right:1px\">";


    $go_autoversand = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/autoversandgo.png\" title=\"Autoversand erlaubt\" border=\"0\" style=\"margin-right:1px\">";
    $stop_autoversand = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/autoversandstop.png\" title=\"Kein Autoversand\" border=\"0\" style=\"margin-right:1px\">";


    $go_check = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/checkgo.png\" title=\"Kundencheck OK\" border=\"0\" style=\"margin-right:1px\">";
    $stop_check = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/checkstop.png\" title=\"Kundencheck fehlgeschlagen\" border=\"0\" style=\"margin-right:1px\">";


    $go_liefertermin = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/termingo.png\" title=\"Liefertermin OK\" border=\"0\" style=\"margin-right:1px\">";
    $stop_liefertermin = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/terminstop.png\" title=\"Liefertermin in Zukunft\" border=\"0\" style=\"margin-right:1px\">";

    $go_kreditlimit = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/kreditlimitgo.png\" title=\"Kreditlimit OK\" border=\"0\" style=\"margin-right:1px\">";
    $stop_kreditlimit = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/kreditlimitstop.png\" title=\"Krein Kreditlimit mehr verf&uuml;gbar!\" border=\"0\" style=\"margin-right:1px\">";

    $go_liefersperre = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/liefersperrego.png\" title=\"Liefersperre OK\" border=\"0\" style=\"margin-right:1px\">";
    $stop_liefersperre = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/liefersperrestop.png\" title=\"Liefersperre gesetzt\" border=\"0\" style=\"margin-right:1px\">";



    $reserviert = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/reserviert.png\" border=\"0\" style=\"margin-right:1px\">";
    $check = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/mail-mark-important.png\" border=\"0\" style=\"margin-right:1px\">";
    
    $abgeschlossen = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/grey.png\" title=\"Auftrag abgeschlossen\" border=\"0\" style=\"margin-right:1px\">";
    $angelegt = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/blue.png\" title=\"Auftrag noch nicht freigegeben!\" border=\"0\" style=\"margin-right:1px\">";
    $storniert = "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/storno.png\" title=\"Auftrag storniert!\" border=\"0\" style=\"margin-right:1px\">";

    for($i=0;$i<10;$i++)
      $tmp .= $abgeschlossen;

    for($i=0;$i<10;$i++)
      $tmpblue .= $angelegt;


    for($i=0;$i<10;$i++)
      $tmpstorno .= $storniert;


    return "if(a.status='angelegt','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmpblue</td></tr></table>',
              if(a.status='abgeschlossen' or a.status='storniert',
          if(a.status='abgeschlossen','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmp</td></tr></table>','<table cellpadding=0 cellspacing=0><tr><td nowrap>$tmpstorno</td></tr></table>'),

          CONCAT('<table cellpadding=0 cellspacing=0><tr><td nowrap>',
        if(a.lager_ok,'$go_lager','$stop_lager'),if(a.porto_ok,'$go_porto','$stop_porto'),if(a.ust_ok,'$go_ust',CONCAT('<a href=\"/index.php?module=adresse&action=ustprf&id=',a.adresse,'\">','$stop_ust','</a>')),
        if(a.vorkasse_ok,'$go_vorkasse','$stop_vorkasse'),if(a.nachnahme_ok,'$go_nachnahme','$stop_nachnahme'),if(a.autoversand,'$go_autoversand','$stop_autoversand'),
        if(a.check_ok,'$go_check','$stop_check'),if(a.liefertermin_ok,'$go_liefertermin','$stop_liefertermin'),if(a.kreditlimit_ok,'$go_kreditlimit','$stop_kreditlimit'),if(a.liefersperre_ok,'$go_liefersperre','$stop_liefersperre'),'</td></tr></table>'
        )))";
  }  


  function IconsSQLVerbindlichkeit()
  {
    $go_ware= "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/ware_go.png\" style=\"margin-right:1px\" title=\"Wareneingangspr&uuml;fung OK\" border=\"0\">";
    $stop_ware= "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/ware_stop.png\" style=\"margin-right:1px\" title=\"Wareneingangspr&uuml;fung fehlt\" border=\"0\">";
    $go_summe= "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/summe_go.png\" style=\"margin-right:1px\" title=\"Rechnungseingangspr&uuml;fung OK\" border=\"0\">";
    $stop_summe= "<img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/summe_stop.png\" style=\"margin-right:1px\" title=\"Rechnungseingangspr&uuml;fung fehlt\" border=\"0\">";

                return "CONCAT(if(v.freigabe,'$go_ware','$stop_ware'),if(v.rechnungsfreigabe,'$go_summe','$stop_summe'))";
  }  



  function TablePositionSearch($parsetarget,$name,$callback="show",$gener)
  {
          $id = $this->app->Secure->GetGET("id");

          switch($name)
          {
            case "auftragpositionen":
/*
              // headings
              $heading =  array('Nummer','Artikel','Projekt','Menge','Einzelpreis','Men&uuml;');
              $width   =  array('10%','45%','15%','10%','10%','10%');
              $findcols = array('nummer','name_de','projekt','menge','preis','id');
              $searchsql = array('a.bezeichnung','a.nummer','p.abkuerzung');

              $menu =  "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer as nummer, a.bezeichnung as name_de, p.abkuerzung as projekt, a.menge as menge, a.preis as preis, a.id as menu
                  FROM  auftrag_position a LEFT JOIN projekt p ON p.id=a.projekt ";

              // fester filter
              $w;h;ere = " a.auftrag='$id'";

              $count = "SELECT COUNT(id) FROM auftrag_position WHERE auftrag='$id'";
*/
            break;
   
            default:
              break;
          }



          if($callback=="show")
          {

            $this->app->Tpl->Add(ADDITIONALCSS,"

.ex_highlight #$name tbody tr.even:hover, #example tbody tr.even td.highlighted {
  background-color: [TPLFIRMENFARBEHELL]; 
}

.ex_highlight_row #$name tr.even:hover {
  background-color: [TPLFIRMENFARBEHELL];
}

.ex_highlight_row #$name tr.even:hover td.sorting_1 {
  background-color: [TPLFIRMENFARBEHELL];
}

.ex_highlight_row #$name tr.odd:hover {
  background-color: [TPLFIRMENFARBEHELL];
}

.ex_highlight_row #$name tr.odd:hover td.sorting_1 {
  background-color: [TPLFIRMENFARBEHELL];
}
");

                      //"sPaginationType": "full_numbers",
                  //"aLengthMenu": [[10, 25, 50, 200, 10000], [10, 25, 50, 200, "All"]],

if($name=="versandoffene") {
    $bStateSave="false";
    $cookietime=0; }
else {
  $cookietime=10*60;
    $bStateSave="true";
 }

$this->app->Tpl->Add(JAVASCRIPT," var oTable".$name."; var oMoreData1".$name."=0; var oMoreData2".$name."=0; var oMoreData3".$name."=0; var oMoreData4".$name."=0; var oMoreData5".$name."=0;  var aData;
  ");

                        $iframe = $this->app->Secure->GetGET("iframe");
            $this->app->Tpl->Add(DATATABLES,
'
             oTable'.$name.' = $(\'#'.$name.'\').dataTable( {
                  "bProcessing": true,
      "iCookieDuration": '.$cookietime.', //60*60*24,// 1 day (in seconds)
                  "iDisplayLength": 10,
                      "bStateSave": '.$bStateSave.',
                  "bServerSide": true,
"fnInitComplete": function (){
        $(oTable'.$name.'.fnGetNodes()).click(function (){
          alert(\'Demo\');// my js window....
        });},
    "fnServerData": function ( sSource, aoData, fnCallback ) {
      /* Add some extra data to the sender */
      aoData.push( { "name": "more_data1", "value": oMoreData1'.$name.' } );
      aoData.push( { "name": "more_data2", "value": oMoreData2'.$name.' } );
      aoData.push( { "name": "more_data3", "value": oMoreData3'.$name.' } );
      aoData.push( { "name": "more_data4", "value": oMoreData4'.$name.' } );
      aoData.push( { "name": "more_data5", "value": oMoreData5'.$name.' } );
      $.getJSON( sSource, aoData, function (json) { 
        /* Do whatever additional processing you want on the callback, then tell DataTables */
        fnCallback(json)
      } );
    },
                  "sAjaxSource": "./index.php?module=ajax&action=tableposition&cmd='.$name.'&id='.$id.'&iframe='.$iframe.'"
                } );



              ');
if($moreinfo)
{
$this->app->Tpl->Add(DATATABLES,
'
$(\'#'.$name.' tbody td img.details\').live( \'click\', function () {
    var nTr = this.parentNode.parentNode;
    aData =  oTable'.$name.'.fnGetData( nTr );

    if ( this.src.match(\'details_close\') )
    {
      /* This row is already open - close it */
      this.src = "./themes/'.$this->app->Conf->WFconf[defaulttheme].'/images/details_open.png";
      oTable'.$name.'.fnClose( nTr );
    }
    else
    {
      /* Open this row */
      this.src = "./themes/'.$this->app->Conf->WFconf[defaulttheme].'/images/details_close.png";
      oTable'.$name.'.fnOpen( nTr, '.$name.'fnFormatDetails(nTr), \'details\' );
    }
  });
');
/*  $.get("index.php?module=auftrag&action=minidetail&id=2", function(text){
    spin=0; 
    miniauftrag = text;
  });
*/

$module = $this->app->Secure->GetGET("module");

$this->app->Tpl->Add(JAVASCRIPT,'function '.$name.'fnFormatDetails ( nTr ) {
  //var aData =  oTable'.$name.'.fnGetData( nTr );
  var str = aData['.$menucol.'];
  var match = str.match(/[1-9]{1}[0-9]*/);

  var auftrag = parseInt(match[0], 10);

  var miniauftrag;
  var strUrl = "index.php?module='.$module.'&action=minidetail&id="+auftrag; //whatever URL you need to call
  var strReturn = "";

  jQuery.ajax({
    url:strUrl, success:function(html){strReturn = html;}, async:false
  });

  miniauftrag = strReturn;

  var sOut = \'<table cellpadding="0" cellspacing="0" border="0" align="center" style="padding-left: 30px; padding-right:30px; width:100%;">\';
  sOut += \'<tr><td>\'+miniauftrag+\'</td></tr>\';
  sOut += \'</table>\';
  return sOut;
}
');
  


}



      $colspan = count($heading);

      $this->app->Tpl->Add($parsetarget,'
        <br><br>
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="'.$name.'">
          <thead>
            <tr><th colspan="'.$colspan .'"><br></th></tr>
            <tr>');

        for($i=0;$i<count($heading);$i++)
        {
            $this->app->Tpl->Add($parsetarget,'<th width="'.$width[$i].'">'.$heading[$i].'</th>');
        }

      $this->app->Tpl->Add($parsetarget,'</tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="'.$colspan .'" class="dataTables_empty">Lade Daten</td>
            </tr>
          </tbody>

          <tfoot>
            <tr>
        ');


        for($i=0;$i<count($heading);$i++)
        {
            $this->app->Tpl->Add($parsetarget,'<th>'.$heading[$i].'</th>');
        }


        $this->app->Tpl->Add($parsetarget,'
            </tr>
          </tfoot>
        </table>
        <br>
        <br>
        <br>
        ');
            } else if ($callback=="sql")
              return $sql;
            else if ($callback=="searchsql")
              return $searchsql; 
            else if ($callback=="searchfulltext")
              return $searchfulltext; 
            else if ($callback=="heading")
              return $heading; 
           else if ($callback=="menu")
              return $menu; 
          else if ($callback=="findcols")
              return $findcols; 
          else if ($callback=="where")
              return $where; 
          else if ($callback=="count")
              return $count; 




  }




  function TableSearch($parsetarget,$name,$callback="show",$generic_sql="",$generic_menu="")
  {
          $id = $this->app->Secure->GetGET("id");
                $groupby="";
                $searchfulltext="";

                if($this->app->erp->Firmendaten("mysql55")=="1")
                {
                        $extended_mysql55 = ",'de_DE'";
                }

          switch($name)
          {
            case "kundeartikelpreise":
              // alle artikel die ein Kunde kaufen kann mit preisen netto brutto

              $cmd = $this->app->Secure->GetGET("smodule");
              $adresse = $this->app->DB->Select("SELECT adresse FROM {$cmd} WHERE id='$id' LIMIT 1");


              // headings
              $heading =  array('Nummer','Artikel','Ab','Preis','Lager','Reservierungen','Projekt','Men&uuml;');
              $width   =  array('10%','45%','10%','10%','10%','15%','10%');
              $findcols = array('nummer','name_de','abmenge','preis','lager','reserviert','projekt','id');
              $searchsql = array('a.name_de','a.nummer','p.abkuerzung');


              $menu =
                  "<a href=\"#\" onclick=InsertDialog(\"index.php?module=artikel&action=profisuche&id=%value%&cmd=$cmd&sid=$id&insert=true\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/add.png\" border=\"0\"></a>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer as nummer, 
                                CONCAT(a.name_de,' (',v.art,')') 
                                as name_de, v.ab_menge as abmenge,v.preis as preis,
                        (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lager, 
                        (SELECT SUM(l.menge) FROM lager_reserviert l WHERE l.artikel=a.id) as reserviert, 
                        p.abkuerzung as projekt, v.id as menu
                  FROM  verkaufspreise v, artikel a LEFT JOIN projekt p ON p.id=a.projekt  ";

        $gruppenarr = $this->app->erp->GetGruppen($adresse);
        for($i=0;$i<count($gruppenarr);$i++)
        {
        if($gruppenarr[$i]>0)
                $gruppen .= " OR v.gruppe='".$gruppenarr[$i]."' ";
        }

              // fester filter
              $where = "a.geloescht=0 AND v.artikel=a.id AND ((v.adresse='$adresse' $gruppen) OR ((v.adresse='' OR v.adresse='0') AND v.art='Kunde')) ";

              $count = "SELECT COUNT(v.id) FROM verkaufspreise v, artikel a WHERE a.geloescht=0 AND v.artikel=a.id AND ((v.adresse='$adresse' $gruppen) OR ((v.adresse='' OR v.adresse='0') AND v.art='Kunde')) ";

            break;

            case "lieferantartikelpreise":
              // alle artikel die ein Kunde kaufen kann mit preisen netto brutto

              $cmd = $this->app->Secure->GetGET("smodule");
              $adresse = $this->app->DB->Select("SELECT adresse FROM {$cmd} WHERE id='$id' LIMIT 1");

              // headings
                        $heading =  array('Nummer','Artikel','Ab','Preis','Lager','Reservierungen','Projekt','Men&uuml;');
              $width   =  array('10%','45%','10%','10%','10%','15%','10%');
              $findcols = array('nummer','name_de','abmenge','preis','lager','reserviert','projekt','id');
              $searchsql = array('a.name_de','a.nummer','p.abkuerzung');

              $menu =
                  "<a href=\"#\" onclick=InsertDialog(\"index.php?module=artikel&action=profisuche&id=%value%&cmd=$cmd&sid=$id&insert=true\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/add.png\" border=\"0\"></a>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer as nummer, a.name_de as name_de, v.ab_menge as abmenge,v.preis as preis,
(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lager,
                        (SELECT SUM(l.menge) FROM lager_reserviert l WHERE l.artikel=a.id) as reserviert, 
                        p.abkuerzung as projekt, v.id as menu
                  FROM  einkaufspreise v, artikel a LEFT JOIN projekt p ON p.id=a.projekt  ";

              // fester filter
              $where = "a.geloescht=0 AND v.artikel=a.id AND (v.adresse='' OR v.adresse='$adresse' OR v.adresse='0') AND (v.gueltig_bis='0000-00-00' OR v.gueltig_bis >=NOW()) ";

              $count = "SELECT COUNT(v.id) FROM einkaufspreise v, artikel a WHERE a.geloescht=0 AND v.artikel=a.id AND (v.adresse='' OR v.adresse='$adresse')";

            break;
    
    case "lagerdifferenzen": 
        // headings 
        $heading =  array('Artikel-Nr.','Artikel','Eingang','Ausgang','Berechnet','Bestand','Differenz','Men&uuml;'); 
        $width   =  array('10%','40%','10%','10%','10%','10%','10%','10%'); 
        $findcols = array('a.nummer','a.name_de','l.eingang','l.ausgang','l.berechnet','l.bestand','l.differenz','a.id'); 
        $searchsql = array('kurzbezeichnung'); 

                        $defaultorder=6;
                                $defaultorderdesc=1;



        

        $menu =  "<a href=\"index.php?module=artikel&action=lager&id=%value%\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>". 
      "&nbsp;";
/*
              ifnull((SELECT SUM(l.menge) FROM lager_bewegung l WHERE l.artikel=a.id AND l.eingang=1),0)-
              ifnull((SELECT SUM(l.menge) FROM lager_bewegung l WHERE l.artikel=a.id AND l.eingang=0),0)-
              ifnull((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),0) as differenz,
*/ 
 
        // SQL statement 
        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer, a.name_de, FORMAT(l.eingang,0),FORMAT(l.ausgang,0),
              FORMAT(l.berechnet,0),FORMAT(l.bestand,0),
              if(l.bestand > l.berechnet, CONCAT('<font color=red>',FORMAT(l.differenz,0),'</font>'),FORMAT(l.differenz,0)), a.id FROM lager_differenzen l 
              LEFT JOIN artikel a ON a.id=l.artikel";

        // fester filter
        $where = " l.user='".$this->app->User->GetID()."' ";

        $count = "SELECT COUNT(l.id) FROM lager_differenzen l WHERE l.user='".$this->app->User->GetID()."' ";

      break;


      case "lagerplatzinventurtabelle": 
        // headings 
        $heading =  array('Bezeichnung','Men&uuml;'); 
        $width   =  array('30%','20%','20%','20%'); 
        $findcols = array('kurzbezeichnung','id'); 
        $searchsql = array('kurzbezeichnung'); 
 
 
        $menu =  "<a href=\"index.php?module=lager&action=platzeditpopup&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>". 
      "&nbsp;";
 
        // SQL statement 
        $sql = "SELECT SQL_CALC_FOUND_ROWS id, kurzbezeichnung, id as menu FROM lager_platz "; 
 
        // fester filter
        $where = " geloescht=0 AND id!=0";

        $count = "SELECT COUNT(id) FROM lager_platz WHERE geloescht=0";

      break;


      case "lagerplatztabelle": 
        // headings 
        $heading =  array('Bezeichnung','Nachschublager','Verbrauchslager','Men&uuml;'); 
        $width   =  array('30%','20%','20%','20%'); 
        $findcols = array('kurzbezeichnung','autolagersperre','verbrauchslager','id'); 
        $searchsql = array('kurzbezeichnung'); 
 
 
                                $defaultorder=4;
                                $defaultorderdesc=1;


        $menu =  "<a href=\"index.php?module=lager&action=platzeditpopup&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>". 
      "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=lager&action=deleteplatz&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
      "&nbsp;<a href=\"#\" onclick=PrintDialog(\"index.php?module=lager&action=regaletiketten&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/labelprinter.png\" border=\"0\"></a>"; 
 
        // SQL statement 
        $sql = "SELECT SQL_CALC_FOUND_ROWS id, kurzbezeichnung, if(autolagersperre,'kein Versand aus diesem Lager','') as autolagersperre, if(verbrauchslager,'ja','') as verbrauchslager,
                                        id as menu FROM lager_platz "; 
 
                                $id=$this->app->Secure->GetGET("id");
        // fester filter
        $where = " geloescht=0 AND id!=0 AND lager='$id' ";

        $count = "SELECT COUNT(id) FROM lager_platz WHERE geloescht=0 AND lager='$id' ";

      break;

                        case 'abrechnungsartikel':
                                $heading = array('Artikel','Nummer','Ab','Preis','Projekt','Startdatum','Menge','Aktion');
                                $width = array('20%','10%','10%','10%','10%','10%','10%','10%');
                                $findcols = array('a.name_de','a.nummer','ab','v.preis','projekt','lieferdatum','menge','v.id');
                                $searchsql = array('a.name_de','a.nummer');

                                $id = $this->app->Secure->GetGET('id');
                                $menu = '<center><input type="button" value="anlegen" onclick="anlegen('.$id.',%value%)"></center>';


                                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,a.name_de, a.nummer, v.ab_menge AS ab, v.preis, p.abkuerzung as projekt,
                                                                CONCAT('<center><input type=\"text\" size=\"10\" value=\"',DATE_FORMAT(NOW(),'%d.%m.%Y'),'\" id=\"datum',v.id,'\"></center>') AS lieferdatum,
                                                                CONCAT('<center><input type=\"text\" size=\"3\" value=\"\" id=\"menge',v.id,'\"><select name=\"art',v.id,'\" id=\"art',v.id,'\"><option value=\"abo\">Abo</option><option value=\"einmalig\">Einmalig</option></select></center>') AS menge,
                                                                v.id 
                                                                FROM artikel AS a 
                                                                LEFT JOIN verkaufspreise AS v ON v.artikel=a.id
                                                                LEFT JOIN projekt AS p ON p.id=v.projekt ";

                                $where = " v.ab_menge>=1 AND a.geloescht!=1 AND a.lagerartikel!=1 AND a.juststueckliste!=1 AND a.stueckliste!=1";

                                $count = "SELECT COUNT(a.id) FROM artikel AS a 
                                                                        LEFT JOIN verkaufspreise AS v ON v.artikel=a.id
                                                                        LEFT JOIN projekt AS p ON p.id=v.projekt
                                                                        WHERE v.ab_menge>=1 AND a.geloescht!=1 AND a.lagerartikel!=1 AND a.juststueckliste!=1 AND a.stueckliste!=1";
                        break;


            case "lieferantartikel":
                                // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#offen').click( function() { fnFilterColumn1( 0 ); } );");
                        
                                for($r=1;$r<2;$r++)
    {
      $this->app->Tpl->Add(JAVASCRIPT,'
        function fnFilterColumn'.$r.' ( i )
        {
          if(oMoreData'.$r.$name.'==1)
          oMoreData'.$r.$name.' = 0;
          else
          oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
        }
      ');
    }
 
              // headings
              $heading =  array('Nummer','Artikel','Verkauf','LA','AB','BE','Reserv.','Fehlende','Gesamt','aktueller Monate','letzter Monat','Status','Men&uuml;');
              $width   =  array('5%','40%','5%','5%','5%','5%','5%','5%','5%','5%','5%','5%','15%');
              $findcols = array('nummer','name_de','verkauf','CAST(`lager` as SIGNED)','CAST(`offen` as SIGNED)','CAST(`bestellung` as SIGNED)','CAST(`res` as SIGNED)','CAST(`fehlende` as SIGNED)','CAST(`gesamt` as SIGNED)','CAST(`monat` as SIGNED)','CAST(`monat_last` as SIGNED)','status','id');
              $searchsql = array('a.name_de',"IFNULL((SELECT e.bestellnummer FROM einkaufspreise e WHERE e.artikel=a.id AND e.adresse='$id' AND e.geloescht!=1 AND e.bestellnummer!='' LIMIT 1),'')",'a.nummer');

              $menu =  "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>";

                                $aktuellermonat =  $this->app->DB->Select("SELECT CONCAT(YEAR(NOW()),'-',MONTH(NOW()))");
                                $letztermonat = $this->app->DB->Select("SELECT CONCAT(YEAR( DATE_SUB( NOW() , INTERVAL 1 MONTH )),'-',DATE_FORMAT( DATE_SUB( NOW() , INTERVAL 1 MONTH ) ,'%m'))");

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer as nummer, 

                                        CONCAT(if( (SELECT SUM(ap.menge) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND auf.status='freigegeben') > IFNULL((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),0) + IFNULL((SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung WHERE bp.artikel=a.id AND b.status='versendet'),0)

        , CONCAT('<font color=red><b>',a.name_de,'</b></font>'),a.name_de),'<br>Best-Nr.:',IFNULL((SELECT e.bestellnummer FROM einkaufspreise e WHERE e.artikel=a.id AND e.adresse='$id' AND e.geloescht!=1 AND e.bestellnummer!='' LIMIT 1),'-')) as name, 


ifnull((SELECT MAX(auftrag.datum) FROM auftrag LEFT JOIN
              auftrag_position ON auftrag.id=auftrag_position.auftrag WHERE auftrag_position.artikel=a.id
              ),0) as verkauf,



      ifnull(if( (SELECT SUM(ap.menge) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND auf.status='freigegeben') > IFNULL((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),0) + IFNULL((SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung WHERE bp.artikel=a.id AND b.status='versendet'),0)
  ,
                                        CONCAT('<font color=red>',if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0,(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),'-'),'</font>'),

                                        if((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0,(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),'-')),0)
 as lager,

                                        ifnull((SELECT SUM(ap.menge) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND auf.status='freigegeben'),0) as offen,

                                        ifnull((SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung WHERE bp.artikel=a.id AND b.status='versendet'),0) as bestellung,

                                        ifnull((if((SELECT SUM(l.menge) FROM lager_reserviert l WHERE l.artikel=a.id) > 0,(SELECT SUM(l.menge) FROM lager_reserviert l WHERE l.artikel=a.id),'-')),0) as res,

                                        ifnull(IF((SELECT SUM(ap.menge) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND auf.status='freigegeben') - ( IFNULL((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),0) + IFNULL((SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung WHERE bp.artikel=a.id AND b.status='versendet'),0)) > 0,(SELECT SUM(ap.menge) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND auf.status='freigegeben') - ( IFNULL((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),0) + IFNULL((SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung WHERE bp.artikel=a.id AND b.status='versendet'),0)),'-'),0) as fehlende,



                                        ifnull(if((SELECT SUM(ap.menge) FROM auftrag_position ap WHERE ap.artikel=a.id) > 0, (SELECT SUM(ap.menge) FROM auftrag_position ap WHERE ap.artikel=a.id),'-'),0) as gesamt,
                                        ifnull((SELECT SUM(ap.menge) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND DATE_FORMAT(auf.datum,'%Y-%m')='$aktuellermonat'),0) as monat,
                                        ifnull((SELECT SUM(ap.menge) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND DATE_FORMAT(auf.datum,'%Y-%m')='$letztermonat'),0) as monat_last,

                                        ifnull(if( (SELECT SUM(ap.menge) FROM auftrag_position ap LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE ap.artikel=a.id AND auf.status='freigegeben') > IFNULL((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),0)+IFNULL((SELECT SUM(bp.menge-bp.geliefert) FROM bestellung_position bp LEFT JOIN bestellung b ON b.id=bp.bestellung WHERE bp.artikel=a.id AND b.status='versendet'),0), 'fehlt','ok'),'') as status, 
                                                a.id as menu
                  FROM artikel a ";

       // START EXTRA more
        $more_data1 = $this->app->Secure->GetGET("more_data1"); 
                                        if($more_data1==1) $subwhere[] = " (((SELECT MAX(auftrag.datum) FROM auftrag LEFT JOIN
                                                        auftrag_position ON auftrag.id=auftrag_position.auftrag WHERE auftrag_position.artikel=a.id
                                                        ) < DATE_SUB(NOW(),INTERVAL 6 MONTH)) AND (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0 )";

        for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];


              // fester filter
              $where = " a.adresse='$id' AND a.geloescht!=1 $tmp";

              $count = "SELECT COUNT(a.id) FROM artikel a WHERE a.adresse='$id' AND a.geloescht!=1 $tmp";

            break;
  
                        case "artikel_auftraege_offen":
              // headings
              $heading =  array('Auftrag','Datum','Status','Zahlweise','Freigabe','Kunde','Menge','Geliefert','Preis','Men&uuml;');
              $width   =  array('10%','10%','15%','10%','10%','30%','10%','10%');
              $findcols = array('a.id','a.belegnr','a.datum','a.status','a.zahlungsweise','adr.kundenfreigabe','adr.name','ap.menge','ap.geliefert_menge',
                                        "FORMAT(ap.preis*(100-ap.rabatt)/100,2)");
              $searchsql = array('a.belegnr',"DATE_FORMAT(a.datum,'%d.%m.%Y')",'a.status','a.zahlungsweise','adr.kundenfreigabe','adr.name','ap.menge','ap.geliefert_menge',          
                                                "FORMAT(ap.preis*(100-ap.rabatt)/100,2)");

              $menu =  "<a href=\"index.php?module=auftrag&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT('<a href=\"index.php?module=auftrag&action=edit&id=',a.id,'\">',a.belegnr,'</a>') as belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.status, a.zahlungsweise, adr.kundenfreigabe as freigabe, CONCAT(a.name,'<br>', a.email) as Kunde, 
     ap.menge, ap.geliefert_menge as gelieferte, FORMAT(ap.preis*(100-ap.rabatt)/100,2) as preis, a.id 
      FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag, adresse adr";


                                if($name=="artikel_auftraege_offen")
                                {
                // fester filter
                $where = " adr.id=a.adresse AND ap.artikel='$id' AND ap.geliefert_menge < ap.menge AND a.status='freigegeben'";
                $count = "SELECT COUNT(a.id) FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag, adresse adr 
                                                WHERE adr.id=a.adresse AND ap.artikel='$id' AND ap.geliefert_menge < ap.menge AND a.status='freigegeben'";
                                } else {
                                        // fester filter
                $where = " adr.id=a.adresse AND ap.artikel='$id' AND a.status='abgeschlossen'";
                $count = "SELECT COUNT(a.id) FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag, adresse adr 
                                                WHERE adr.id=a.adresse AND ap.artikel='$id' AND a.status='abgeschlosse'";
                                }
            break;

                        case "artikel_auftraege_versendet":

              // headings
              $heading =  array('Auftrag','Datum','Status','Zahlweise','Freigabe','Kunde','Menge','Geliefert','Preis','Men&uuml;');
              $width   =  array('10%','10%','15%','10%','10%','30%','10%','10%');
              $findcols = array('a.id','a.belegnr','a.datum','a.status','a.zahlungsweise','adr.kundenfreigabe','adr.name','ap.menge','ap.geliefert_menge',
                                        "FORMAT(ap.preis*(100-ap.rabatt)/100,2)");
              $searchsql = array('a.belegnr',"DATE_FORMAT(a.datum,'%d.%m.%Y')",'a.status','a.zahlungsweise','adr.kundenfreigabe','adr.name',
                                        'ap.menge','ap.geliefert_menge',          "FORMAT(ap.preis*(100-ap.rabatt)/100,2)");

              $menu =  "<a href=\"index.php?module=auftrag&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT('<a href=\"index.php?module=auftrag&action=edit&id=',a.id,'\">',a.belegnr,'</a>') as belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.status, a.zahlungsweise, adr.kundenfreigabe as freigabe, CONCAT(a.name,'<br>', a.email) as Kunde, 
     ap.menge, ap.geliefert_menge as gelieferte, FORMAT(ap.preis*(100-ap.rabatt)/100,2) as preis, a.id 
      FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag, adresse adr";

                                        // fester filter
                $where = " adr.id=a.adresse AND ap.artikel='$id' AND a.status='abgeschlossen'";
                $count = "SELECT COUNT(a.id) FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag, adresse adr 
                                                WHERE adr.id=a.adresse AND ap.artikel='$id' AND a.status='abgeschlosse'";
            break;





 
                        case "adresse_artikel_serviceartikel":
              // headings
              $heading =  array('Nummer','Artikel','Rechnung','Datum','Menge','Einzelpreis','Rabatt','Men&uuml;');
              $width   =  array('10%','45%','15%','10%','10%','10%','10%','10%');
              $findcols = array('nummer','name_de','rechnung','belegnr','menge','preis','rabatt','id');
              $searchsql = array('a.bezeichnung','a.nummer','auf.belegnr',"DATE_FORMAT(auf.datum,'%d.%m.%Y')",'a.preis','a.rabatt');

              $menu =  "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer as nummer, a.bezeichnung as name_de, auf.belegnr as rechnung, DATE_FORMAT(auf.datum,'%d.%m.%Y'), a.menge as menge, a.preis as preis, 
                                a.rabatt as rabatt, a.rechnung as menu
                  FROM rechnung_position a LEFT JOIN rechnung auf ON a.rechnung=auf.id LEFT JOIN artikel art ON art.id=a.artikel";
              // fester filter
              $where = " auf.adresse='$id' AND art.serviceartikel=1";

              $count = "SELECT COUNT(a.id) FROM rechnung_position a LEFT JOIN rechnung auf ON a.rechnung=auf.id WHERE auf.adresse='$id'";
            break;

                        case "adresse_artikel_geraet":
              // headings
              $heading =  array('Nummer','Artikel','Rechnung','Datum','Menge','Einzelpreis','Rabatt','Men&uuml;');
              $width   =  array('10%','45%','15%','10%','10%','10%','10%','10%');
              $findcols = array('nummer','name_de','rechnung','belegnr','menge','preis','rabatt','id');
              $searchsql = array('a.bezeichnung','a.nummer','auf.belegnr',"DATE_FORMAT(auf.datum,'%d.%m.%Y')",'a.preis','a.rabatt');

              $menu =  "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer as nummer, a.bezeichnung as name_de, auf.belegnr as rechnung, DATE_FORMAT(auf.datum,'%d.%m.%Y'), a.menge as menge, a.preis as preis, 
                                a.rabatt as rabatt, a.rechnung as menu
                  FROM rechnung_position a LEFT JOIN rechnung auf ON a.rechnung=auf.id LEFT JOIN artikel art ON art.id=a.artikel";
              // fester filter
              $where = " auf.adresse='$id' AND art.geraet=1";

              $count = "SELECT COUNT(a.id) FROM rechnung_position a LEFT JOIN rechnung auf ON a.rechnung=auf.id WHERE auf.adresse='$id'";

            break;
  

            case "adresseartikel":

              // headings
              $heading =  array('Nummer','Artikel','Rechnung','Datum','Menge','Einzelpreis','Rabatt','Gesamt','Men&uuml;');
              $width   =  array('10%','45%','15%','10%','10%','10%','10%','10%','10%');
              $findcols = array('nummer','name_de','rechnung','belegnr','menge','preis','rabatt','gesamt','id');
              $searchsql = array('a.bezeichnung','a.nummer','auf.belegnr',"DATE_FORMAT(auf.datum,'%d.%m.%Y')",'a.preis','a.rabatt');
              $sumcol = 8;

              $menu =  "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.nummer as nummer, a.bezeichnung as name_de, 
                  auf.belegnr as rechnung, DATE_FORMAT(auf.datum,'%d.%m.%Y'), a.menge as menge, a.preis as preis, 
                                a.rabatt as rabatt, 
              FORMAT(a.preis*a.menge*(IF(a.rabatt > 0, (100-a.rabatt)/100, 1)),2{$extended_mysql55}) as gesamt,
                      a.rechnung as menu
                  FROM rechnung_position a LEFT JOIN rechnung auf ON a.rechnung=auf.id ";

              // fester filter
              $where = " auf.adresse='$id' ";

              $count = "SELECT COUNT(a.id) FROM rechnung_position a LEFT JOIN rechnung auf ON a.rechnung=auf.id WHERE auf.adresse='$id'";

            break;
  

            case "lagertabelle":
              // headings
              $heading =  array('Bezeichnung','Beschreibung','Manuell','Men&uuml;');
              $width   =  array('30%','20%','5%','20%');
              $findcols = array('bezeichnung','Beschreibung','manuell','id');
              $searchsql = array('bezeichnung','Beschreibung','manuell');

                                $defaultorder=4;
                                $defaultorderdesc=1;


              $menu =  "<a href=\"index.php?module=lager&action=platz&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=lager&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>"."&nbsp;<a href=\"#\" onclick=PrintDialog(\"index.php?module=lager&action=regaletiketten&id=%value%&cmd=all\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/labelprinter.png\" border=\"0\"></a>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS id, bezeichnung, beschreibung, 
                                        if(manuell<=0,'ja','-') as manuell, id as menu FROM lager ";

              // fester filter
              $where = " geloescht=0 AND id!=0";

              $count = "SELECT COUNT(id) FROM lager WHERE geloescht=0";

            break;
   
                        case "adressestundensatz":
                                $heading = array("Projekt-ID", "Projekt", "Typ", "Stundensatz", "Men&uuml;");
                                $width = array("10%", "50%", "10%", "15%", "15%");
                                $findcols = array("p.id", "p.name", "typ", "satz", "ssid");
                                $searchsql = array("p.name");
                                $sql = "SELECT SQL_CALC_FOUND_ROWS  p.id, p.abkuerzung, p.name, IFNULL(ss.typ,'Standard') AS typ, 
                                                                IFNULL(ss.satz, (SELECT satz 
                                                                                                                                 FROM stundensatz
                                                                                 WHERE typ='Standard' AND adresse='$id'
                                                                                 ORDER BY datum DESC LIMIT 1)) AS satz,
                                                                IFNULL(ss.id,CONCAT('&projekt=',p.id)) AS ssid
                                                                FROM adresse_rolle ar
                                                                LEFT JOIN projekt as p
                                                                ON ar.parameter=p.id
                                                                LEFT JOIN (SELECT * FROM stundensatz AS dss ORDER BY dss.datum DESC) AS ss
                                                                ON p.id=ss.projekt AND ss.adresse=ar.adresse ";
                                $where = " ar.adresse='$id' AND subjekt='Mitarbeiter' AND objekt='Projekt' GROUP BY p.id ";
                                $count = "SELECT COUNT(parameter) FROM adresse_rolle WHERE adresse='$id' AND subjekt='Mitarbeiter' AND objekt='Projekt'";
                                $menu = "<a href=\"index.php?module=adresse&action=stundensatzedit&user=$id&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                        "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse&action=stundensatzdelete&user=$id&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>";
                                $moreinfo=false;
                        break;

                        case "adresselohn":
                                $heading = array('Monat','Stunden','Men&uuml;');
                                $width = array('20%','20%','20%','40%');
                                $findcols = array('monat','stunden');
                                $searchsql = array('monat');

                                $sql = "SELECT SQL_CALC_FOUND_ROWS id,DATE_FORMAT(von,'%Y-%m') AS monat,  
                                                                SUM(ROUND((UNIX_TIMESTAMP(bis) - UNIX_TIMESTAMP(von))/3600,2)) as stunden
                                                                FROM zeiterfassung ";
                                $where = " adresse='$id' GROUP by monat";//ORDER BY STR_TO_DATE(CONCAT(MONTH(von),',',YEAR(von)), '%m,%Y') ";


                                //$where = " adresse='$id' GROUP BY monat,jahr ORDER BY STR_TO_DATE(CONCAT(MONTH(von),',',YEAR(von)), '%m,%Y') ";
                                $count = "SELECT FOUND_ROWS() AS treffer,MONTHNAME(von) AS monat, YEAR(von) AS jahr
                                                                        FROM zeiterfassung WHERE adresse='$id' GROUP BY monat,jahr ";// ORDER BY STR_TO_DATE(CONCAT(MONTH(von),',',YEAR(von)), '%m,%Y');";
//                                                                      SELECT FOUND_ROWS();";

                                $menu = "test";
                                $moreinfo=false;
        
                        break;          

                        case "backuplist":
                                $heading = array('Name','Dateiname','Datum','Men&uuml;');
                                $width = array('30%','30%','20%','20%');
                                $findcols = array('name','dateiname','datum','id');
                                $searchsql = array('name','datum');
                                $sql = "SELECT SQL_CALC_FOUND_ROWS id, name, dateiname, datum, id as menu FROM backup";
        $defaultorder = 4;  //Optional wenn andere Reihenfolge gewuenscht

                                $where = "";
                                $count = "SELECT COUNT(id) FROM backup";
                                $menu = "<a href=\"#\" onclick=BackupDialog(\"index.php?module=backup&action=recover&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=backup&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>";
                        break;

            case "projekttabelle":
              // headings
              $heading =  array('Name','Abkuerzung','Verantwortlicher','&Ouml;ffentlich','Men&uuml;');
              $width   =  array('30%','20%','20%','5%','15%');
              $findcols = array('name','abkuerzung','verantwortlicher','oeffentlich','id');
              $searchsql = array('name','abkuerzung','verantwortlicher');

        $defaultorder = 5;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;


              $menu =  "<a href=\"index.php?module=projekt&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
              "&nbsp;<a href=\"index.php?module=projekt&action=arbeitspaket&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=projekt&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "<!--&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=projekt&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>-->";

              // SQL statement
                                if($this->app->Conf->WFdbType=="postgre")
              $sql = "SELECT id, name, abkuerzung, verantwortlicher, id as menu FROM projekt ";
                                else
              $sql = "SELECT SQL_CALC_FOUND_ROWS p.id, p.name, p.abkuerzung, p.verantwortlicher,
                                if(p.oeffentlich,'ja','-') as oeffentlich, p.id as menu FROM projekt p";
              // fester filter
              $where = " p.geloescht=0 AND p.id!=0 ".$this->app->erp->ProjektRechte();

              $count = "SELECT COUNT(id) FROM projekt WHERE geloescht=0";

            break;
   

            case "emailtabelle":

                                // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#ungelesen').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add(JQUERYREADY,"$('#antworten').click( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add(JQUERYREADY,"$('#warteschlange').click( function() { fnFilterColumn3( 0 ); } );");

                for($r=1;$r<4;$r++)
                {
                $this->app->Tpl->Add(JAVASCRIPT,'
                function fnFilterColumn'.$r.' ( i )
                {
                if(oMoreData'.$r.$name.'==1)
                        oMoreData'.$r.$name.' = 0;
                else
                        oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
                }
        ');
         }
        // ENDE EXTRA checkboxen


              // headings
              $heading =  array('','Datum','Absender','Betreff','Men&uuml;');
              $width   =  array('1%','14%','35%','45%','5%');
              $findcols = array('open','eingang','sender','subject','id');
             // $searchsql = array("DATE_FORMAT(e.empfang, '%d.%m.%Y %H:%i')","e.id");
           //   $searchsql = array("DATE_FORMAT(e.empfang, '%d.%m.%Y')");
              //$searchfulltext = array('e.subject','e.sender','e.action','e.action_html');
              //$searchfulltext = 'e.subject,e.sender,e.action,e.action_html';
              $searchsql = array("DATE_FORMAT(e.empfang, '%d.%m.%Y')",'e.sender','e.subject','e.action');

                                $defaultorder=5;
                                $defaultorderdesc=1;

              $menu =  "<a href=\"index.php?module=webmail&action=view&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=webmail&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a><!--".
                  "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=email&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>-->";


              $adresse = $this->app->User->GetAdresse();
    
              $sql = "SELECT SQL_CALC_FOUND_ROWS  e.id, '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, DATE_FORMAT(e.empfang, '%Y-%m-%d %H:%i' ) as  eingang, 

if(e.antworten='1',CONCAT('<b style=\"color:red\">',e.sender,'</b>'),

if(e.warteschlange='2',CONCAT('<b style=\"color:blue\">',e.sender,'</b>'),
                (if(e.gelesen!='1',CONCAT('<b>',e.sender,'</b>'),e.sender)))

) as sender,

e.subject,
                      e.id as menu FROM emailbackup_mails e LEFT JOIN emailbackup eb ON eb.id=e.webmail  ";

                         $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " e.gelesen!='1' ";
        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " e.antworten='1' ";
        $more_data3 = $this->app->Secure->GetGET("more_data3"); if($more_data3==1) $subwhere[] = " e.warteschlange='1' ";


        for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];


              // fester filter
              $where = " eb.adresse='$adresse' AND e.spam!='1' AND e.geloescht!='1' $tmp";

              $count = "SELECT COUNT(e.id) FROM emailbackup_mails e LEFT JOIN emailbackup eb ON eb.id=e.webmail WHERE eb.adresse='$adresse' AND e.spam!='1' AND e.geloescht!='1' ";

        $menucol=4;
              $moreinfo = true;


            break;
   

            case "paketannahme":
              // headings
              $heading =  array('Name','Kunde','Lieferant','Land','PLZ','Ort','E-Mail','Projekt','Men&uuml;');
              $width   =  array('18%','10%','5%','5%','5%','5%','5%','15%','10%');
              $findcols = array('name','kundennummer','lieferantennummer','land','plz','ort','email','projekt','id');
                        
                        if($this->app->erp->Firmendaten("adresse_freitext1_suche"))
              $searchsql = array('a.ort','a.name','p.abkuerzung','a.land','a.plz','a.email','a.kundennummer','a.lieferantennummer','a.ansprechpartner','a.freifeld1');
                        else
              $searchsql = array('a.ort','a.name','p.abkuerzung','a.land','a.plz','a.email','a.kundennummer','a.lieferantennummer','a.ansprechpartner');

                                $defaultorder=2;
                                $defaultorderdesc=1;

              $menu =  "<a href=\"index.php?module=wareneingang&action=paketannahme&id=%value%&vorlage=adresse\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.png\" border=\"0\"></a>";

              // SQL statement

                                if($this->app->Conf->WFdbType=="postgre") {
              $sql = "SELECT a.id, 

a.name as name,

CASE WHEN a.kundennummer > 0 THEN  a.kundennummer
ELSE 0 
END as kundennummer,
CASE WHEN a.lieferantennummer > 0 THEN a.lieferantennummer 
ELSE 0 
END as lieferantennummer, 
a.land as land,
a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt ";
                                } else {
//if(a.typ = 'herr' OR a.typ = 'frau',CONCAT(a.vorname,' ',a.name),a.name) as name,
                                if($this->app->erp->RechteVorhanden("multilevel","list") && $this->app->erp->Firmendaten("modul_mlm")=="1")
                {
                                        $sql = 
        "SELECT SQL_CALC_FOUND_ROWS a.id, a.name as name,
CONCAT(if(a.kundennummer,a.kundennummer,'-'),if(a.mlmaktiv,' (LN)','')) as kundennummer,
        if(a.lieferantennummer,a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
      FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt ";
} else {

                        if($this->app->erp->Firmendaten("adresse_freitext1_suche"))
                        {
                                        $sql = 
        "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT(a.name,if(a.freifeld1!='',CONCAT(' (',a.freifeld1,')'),'')) as name,
if(a.kundennummer,a.kundennummer,'-') as kundennummer,
        if(a.lieferantennummer,a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
      FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt ";
                        } else {
                $sql = 
        "SELECT SQL_CALC_FOUND_ROWS a.id, a.name as name,
if(a.kundennummer,a.kundennummer,'-') as kundennummer,
        if(a.lieferantennummer,a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
      FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt ";

                        }

}
                                }
              // fester filter
              $where = "a.geloescht=0 ".$this->app->erp->ProjektRechte();

              $count = "SELECT COUNT(a.id) FROM adresse a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.geloescht=0 ".$this->app->erp->ProjektRechte();

            break;
                   

            case "adresse_import":
              // headings
              $heading =  array('Name','Land','PLZ','Ort','E-Mail','Men&uuml;');
              $width   =  array('18%','5%','5%','5%','5%','5%');
              $findcols = array('name','land','plz','ort','email','id');

   //     $defaultorder = 9;  //Optional wenn andere Reihenfolge gewuenscht                
   //     $defaultorderdesc=1;
                        
              $searchsql = array('a.ort','a.name','a.land','a.plz','a.email','a.ansprechpartner');

              $menu =  "<a href=\"index.php?module=adresse_import&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse_import&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>";

              // SQL statement

                                $sql = 
                                        "SELECT SQL_CALC_FOUND_ROWS a.id, a.name as name,
        a.land as land, a.plz as plz, a.ort as ort, a.email as email, a.id FROM  adresse_import a ";

              // fester filter
              $where = "a.abgeschlossen!=1 ";//.$this->app->erp->ProjektRechte();

              $count = "SELECT COUNT(a.id) FROM adresse_import a WHERE a.abgeschlossen!=1 ";
            break;

            case "vertretertabelle":
              // headings
              $heading =  array('Name','Kunden','Land','PLZ','Ort','E-Mail','Projekt','Men&uuml;');
              $width   =  array('25%','5%','5%','5%','5%','20%','10%','3%');
              $findcols = array('name','kunden','land','plz','ort','email','projekt','id');

              $defaultorder = 1;  //Optional wenn andere Reihenfolge gewuenscht                
              $defaultorderdesc=0;
                        
              $searchsql = array('a.ort','a.name','p.abkuerzung','a.land','a.plz','a.email','a.kundennummer','a.lieferantennummer','a.ansprechpartner');

              $menu =  "<a href=\"index.php?module=vertreter&action=download&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/download.png\" border=\"0\"></a>";

                $sql = 
        "SELECT SQL_CALC_FOUND_ROWS a2.id, a2.name as name, COUNT(a.id) as kunden,
        a2.land as land, a2.plz as plz, a2.ort as ort, a2.email as email, p.abkuerzung as projekt, a2.id as menu
      FROM adresse a LEFT JOIN adresse a2 ON a2.id=a.vertrieb LEFT JOIN projekt p ON p.id=a2.projekt ";

          $groupby = "GROUP BY a.vertrieb";
          $where = "";
//SELECT  a2.name FROM adresse a LEFT JOIN adresse a2 ON a2.id=a.vertrieb GROUP by a.vertrieb
                                
              // fester filter
              //$where = "a2.geloescht=0 AND a.geloescht=0 ".$this->app->erp->ProjektRechte();

              $count = "SELECT Count( DISTINCT a.vertrieb ) FROM adresse a";

            break;

            case "adressetabelle":
              // headings
              $heading =  array('Name','Kunde','Lieferant','Land','PLZ','Ort','E-Mail','Projekt','Men&uuml;');
              $width   =  array('18%','10%','5%','5%','5%','5%','5%','15%','10%');
              $findcols = array('name','kundennummer','lieferantennummer','land','plz','ort','email','projekt','id');

        $defaultorder = 9;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;
                        
                        if($this->app->erp->Firmendaten("adresse_freitext1_suche"))
              $searchsql = array('a.ort','a.name','p.abkuerzung','a.land','a.plz','a.email','a.kundennummer','a.lieferantennummer','a.ansprechpartner','a.freifeld1');
                        else
              $searchsql = array('a.ort','a.name','p.abkuerzung','a.land','a.plz','a.email','a.kundennummer','a.lieferantennummer','a.ansprechpartner');

              $menu =  "<a href=\"index.php?module=adresse&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>";

              // SQL statement
//if(a.typ = 'herr' OR a.typ = 'frau',CONCAT(a.vorname,' ',a.name),a.name) as name,
     if($this->app->erp->RechteVorhanden("multilevel","list") && $this->app->erp->Firmendaten("modul_mlm")=="1")
                {
                                        $sql = 
        "SELECT SQL_CALC_FOUND_ROWS a.id, a.name as name,
CONCAT(if(a.kundennummer,a.kundennummer,'-'),if(a.mlmaktiv,' (LN)','')) as kundennummer,
        if(a.lieferantennummer,a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
      FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt ";
} else {

                        if($this->app->erp->Firmendaten("adresse_freitext1_suche"))
                        {
                                        $sql = 
        "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT(a.name,if(a.freifeld1!='',CONCAT(' (',a.freifeld1,')'),'')) as name,
if(a.kundennummer,a.kundennummer,'-') as kundennummer,
        if(a.lieferantennummer,a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
      FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt ";
                        } else {
                $sql = 
        "SELECT SQL_CALC_FOUND_ROWS a.id, a.name as name,
if(a.kundennummer,a.kundennummer,'-') as kundennummer,
        if(a.lieferantennummer,a.lieferantennummer,'-') as lieferantennummer, a.land as land, a.plz as plz, a.ort as ort, a.email as email, p.abkuerzung as projekt, a.id as menu
      FROM  adresse AS a LEFT JOIN projekt p ON p.id=a.projekt ";

                        }

}
                                
              // fester filter
              $where = "a.geloescht=0 ".$this->app->erp->ProjektRechte();

              $count = "SELECT COUNT(a.id) FROM adresse a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.geloescht=0 ".$this->app->erp->ProjektRechte();

            break;
                   
            case "artikeltabelleneu":                                                                                                                                                                  
              // headings                                                                                                                                                                                       
              $heading =  array('','Nummer','Artikel','Im Lager','Projekt','Men&uuml;');                                                                                                                                      
              $width   =  array('5%','10%','45%','8%','15%','10%');                                                                                                                                                       
              $findcols = array('nummer','name_de','projekt','lager','id');                                                                                                                                             
              $searchsql = array('a.name_de','a.nummer','p.abkuerzung');                                                                                                                                        
                                                                                                                                                                                                                
              $menu =  "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
          "<!--&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
          "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>-->";


              // SQL statement                                                                                                                                                                                  
                                                        if($this->app->Conf->WFdbType=="postgre") {
              $sql = "SELECT a.id, CONCAT('<input type=\"checkbox\" name=\"artikelmarkiert[]\" value=\"',a.id,'\">') as wahl, a.nummer as nummer, 
              a.name_de as name_de, (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lager, p.abkuerzung as projekt, a.id as menu                                                                          
              FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt ";                                                                                                                                          
                                                        }else{
$sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT('<input type=\"checkbox\" name=\"artikelmarkiert[]\" value=\"',a.id,'\">') as wahl, a.nummer as nummer, 
              a.name_de as name_de, (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lager, p.abkuerzung as projekt, a.id as menu                                                                          
              FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt ";
                                                        }
        

              // fester filter                                                                                                                                                                                  
              $where = "a.geloescht=0 AND a.neu='1' AND a.shop >0 ".$this->app->erp->ProjektRechte(); 

              $count = "SELECT COUNT(a.id) FROM artikel a WHERE a.geloescht=0 AND a.shop > 0 AND a.neu=1 ".$this->app->erp->ProjektRechte();
            break;                                                                                                                                                       
 

   
            case "artikeltabellehinweisausverkauft":                                                                                                                                                                  
              // headings                                                                                                                                                                                       
              $heading =  array('','Nummer','Artikel','Im Lager','Projekt','Men&uuml;');                                                                                                                                      
              $width   =  array('5%','10%','45%','8%','15%','10%');                                                                                                                                                       
              $findcols = array('nummer','name_de','projekt','id');                                                                                                                                             
              $searchsql = array('a.name_de','a.nummer','p.abkuerzung');                                                                                                                                        
                                                                                                                                                                                                                
              $menu =  "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
          "<!--&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
          "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>-->";


              // SQL statement                                                                                                                                                                                  
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT('<input type=\"checkbox\" name=\"artikelmarkiert[]\" value=\"',a.id,'\">') as wahl, a.nummer as nummer, a.name_de as name_de, (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lager, p.abkuerzung as projekt, a.id as menu                                                                          
              FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt ";                                                                                                                                          
              // fester filter                                                                                                                                                                                  
              $where = "a.geloescht=0 AND (a.ausverkauft='1' OR a.gesperrt=1) AND a.shop > 0 AND (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0"; 
              $count = "SELECT COUNT(a.id) FROM artikel a WHERE a.geloescht=0 AND a.shop > 0 AND (a.ausverkauft=1 OR a.gesperrt=1) AND (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0";                                                                                                                                       
            break;                                                                                                                                                       
 


           case "artikeltabellelagerndabernichtlagernd":                                                                                                                                                                  
              // headings                                                                                                                                                                                       
              $heading =  array('','Nummer','Artikel','Im Lager','Projekt','Men&uuml;');                                                                                                                                      
              $width   =  array('5%','10%','45%','8%','15%','10%');                                                                                                                                                       
              $findcols = array('nummer','name_de','projekt','id');                                                                                                                                             
              $searchsql = array('a.name_de','a.nummer','p.abkuerzung');                                                                                                                                        
                                                                                                                                                                                                                
              $menu =  "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
          "<!--&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
          "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>-->";


              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, CONCAT('<input type=\"checkbox\" name=\"artikelmarkiert[]\" value=\"',a.id,'\">') as wahl, a.nummer as nummer, a.name_de as name_de, (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lager, p.abkuerzung as projekt, a.id as menu                                                                          
              FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt ";                                                                                                                                          
              
              $where = "a.geloescht=0 AND (a.lieferzeit='lager' || a.lieferzeit='') AND a.lagerartikel=1  AND (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) IS NULL 
                        AND a.shop!=0 AND a.gesperrt=0 AND a.ausverkauft!='1' AND a.inaktiv!='1'"; 
                        
              $count = "SELECT COUNT(a.id) FROM artikel a WHERE a.geloescht=0 AND (a.lieferzeit='lager' || a.lieferzeit='') AND (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) IS NULL 
                AND a.shop!=0 AND a.gesperrt=0 AND a.ausverkauft!='1' AND a.inaktiv!='1'";                                                                                                                                       
            break;                                                                                                                                                       
 

           case "manuellagerlampe":                                                                                                                                                                  
                                        $this->app->Tpl->Add(JQUERYREADY,"$('#green').click( function() { fnFilterColumn1( 0 ); } );");
                                        $this->app->Tpl->Add(JQUERYREADY,"$('#yellow').click( function() { fnFilterColumn2( 0 ); } );");
                                        $this->app->Tpl->Add(JQUERYREADY,"$('#red').click( function() { fnFilterColumn3( 0 ); } );");
                                        $this->app->Tpl->Add(JQUERYREADY,"$('#imlager').click( function() { fnFilterColumn4( 0 ); } );");
                                        $this->app->Tpl->Add(JQUERYREADY,"$('#nichtimlager').click( function() { fnFilterColumn5( 0 ); } );");

              // headings                                                                                                                                                                                       
              $heading =  array('','Ampel','Nummer','Artikel','Lieferant','Im Lager','Projekt','Men&uuml;');                                                                                                                                      
              $width   =  array('3%','5%','10%','35%','20%','8%','15%','10%');                                                                                                                                                       
              $findcols = array('wahl','a.lieferzeit','a.nummer','a.name_de','a.lieferant','lager','projekt','a.id');                                                                                                                                             
              $searchsql = array('a.name_de','a.nummer','adr.name','p.abkuerzung');                                                                                                                                        
                                                 $menu =  "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
          "<!--&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
          "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>-->";

              // SQL statement                                                                                                                                                                                  
/*                      CONCAT('<input type=\"checkbox\" class=\"chcktbl2\" name=\"artikelmarkiert[]\" value=\"',a.id,'\">') as wahl, 
                                                CONCAT('<img src=./themes/new/images/shop_stock',a.lieferzeit,'.png>') as ampel, 
                                */
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, 
                                        CONCAT('<input type=\"checkbox\" class=\"chcktbl\" name=\"artikelmarkiert[]\" value=\"',a.id,'\">') as wahl, 
                                        CONCAT('<img src=./themes/new/images/shop_stock',a.lieferzeit,'.png>') as ampel, 

                If(a.inaktiv,CONCAT('<strike>',a.nummer,'</strike>'),a.nummer) as nummer, 
                If(a.inaktiv,CONCAT('<strike>',a.name_de,'</strike>'),a.name_de) as name_de, 
                If(a.inaktiv,CONCAT('<strike>',adr.name,'</strike>'),adr.name) as lieferant, 

                (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lager, 

                If(a.inaktiv,CONCAT('<strike>',p.abkuerzung,'</strike>'),p.abkuerzung) as projekt, 
                                         a.id as menu                                                                          
              FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN  adresse adr ON a.adresse=adr.id ";                                                                                                                                          
                for($r=1;$r<9;$r++)
    {
      $this->app->Tpl->Add(JAVASCRIPT,'
        function fnFilterColumn'.$r.' ( i )
        {
          if(oMoreData'.$r.$name.'==1)
          oMoreData'.$r.$name.' = 0;
          else
          oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
        }
      ');
    }
        // START EXTRA more
        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " a.lieferzeit='green' ";
        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " a.lieferzeit='yellow' ";
        $more_data3 = $this->app->Secure->GetGET("more_data3"); if($more_data3==1) $subwhere[] = " a.lieferzeit='red' ";
        $more_data4 = $this->app->Secure->GetGET("more_data4"); if($more_data4==1) $subwhere[] = " (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0 ";
        $more_data5 = $this->app->Secure->GetGET("more_data5"); if($more_data5==1) $subwhere[] = " (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) IS NULL ";
                                // ENDE EXTRA more

                                for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];


//            $where = " l.id!='' $tmp";
              // fester filter                                                                                                                                                                                  
              $where = "a.geloescht=0 AND a.shop > 0 AND a.lagerartikel=1 AND autolagerlampe!=1 ".$tmp; 
              $count = "SELECT COUNT(id) FROM artikel WHERE geloescht=0 AND shop > 0  AND lagerartikel=1";                                                                                                                                       
            break;                                                                                                                                                       

           case "autolagerlampe":                                                                                                                                                                  
              // headings                                                                                                                                                                                       
              $heading =  array('','Ampel','Art','Nummer','Artikel','Lieferant','Im Lager','Projekt','Men&uuml;');                                                                                                                                      
              $width   =  array('1%','7%','3%','10%','30%','20%','10%','10%');                                                                                                                                                       
              $findcols = array('a.id','a.lieferzeit','art','a.nummer','a.name_de','lieferant','projekt','a.id');                                                                                                                                             
              $searchsql = array('a.name_de','a.nummer','adr.name','p.abkuerzung');                                                                                                                                        
                                                 $menu =  "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
          "<!--&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
          "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>-->";

              // SQL statement                                                                                                                                                                                  
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, 
                                                
                                if(a.autolagerlampe,CONCAT('<input type=\"checkbox\" class=\"chcktbl2\" name=\"artikelmarkiert[',a.id,']\" checked value=\"1\"><input type=\"hidden\" name=\"artikelmarkierthidden[',a.id,']\" value=\"1\">'),
                                        CONCAT('<input type=\"checkbox\" class=\"chcktbl2\" name=\"artikelmarkiert[',a.id,']\" value=\"1\"><input type=\"hidden\" name=\"artikelmarkierthidden[',a.id,']\" value=\"0\">')) as wahl, 
                                                CONCAT('<img src=./themes/new/images/shop_stock',a.lieferzeit,'.png>') as ampel, 

                                                        if(a.autolagerlampe,'auto','manuell') as art,

                                                                If(a.inaktiv,CONCAT('<strike>',a.nummer,'</strike>'),a.nummer) as nummer, 
                                                                If(a.inaktiv,CONCAT('<strike>',a.name_de,'</strike>'),a.name_de) as name_de, 
                                                                If(a.inaktiv,CONCAT('<strike>',adr.name,'</strike>'),adr.name) as lieferant, 
                                                                (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lager, 
                                                                If(a.inaktiv,CONCAT('<strike>',p.abkuerzung,'</strike>'),p.abkuerzung) as projekt, 

                                                                a.id as menu                                                                          
              FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN  adresse adr ON a.adresse=adr.id ";                                                                                                                                          
              $where = "a.geloescht=0 AND a.shop > 0 AND a.lagerartikel=1 "; 
              $count = "SELECT COUNT(id) FROM artikel WHERE geloescht=0 AND shop > 0  AND lagerartikel=1";                                                                                                                                       
            break;                                                                                                                                                       
 

  
            case "wareneingangartikelmanuellerfassen":
              // headings
              $heading =  array('','Nummer','Artikel','Lagerbestand','Projekt','Men&uuml;');
              $width   =  array('open','10%','60%','5%','15%','10%');
              $findcols = array('open','nummer','name_de','CAST((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as SIGNED)','projekt','id');

        $menucol=5;

                                if($this->app->erp->Firmendaten("artikel_suche_kurztext")=="1")
                                {
                $searchsql = array('a.name_de','kurztext_de','a.nummer','p.abkuerzung',"a.hersteller","a.herstellernummer","a.anabregs_text",
                                                "(SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1)");
                                }
                                else {
                $searchsql = array('a.name_de','a.nummer','p.abkuerzung',"a.hersteller","a.herstellernummer","(SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1)");
                                }
                                $paket = $this->app->Secure->GetGET("id");
              $menu =  "<a href=\"index.php?module=wareneingang&action=distrietiketten&id=$paket&pos=%value%&menge=1&cmd=manuell\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.png\" border=\"0\"></a>";

              // SQL statement
                                if($this->app->Conf->WFdbType=="postgre") {
              $sql = "SELECT a.id, '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, a.nummer as nummer, 
CASE WHEN a.intern_gesperrt='1'
THEN CONCAT('<strike>',a.name_de,'</strike>',a.name_de)
END as name_de, 
(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lagerbestand,  p.abkuerzung as projekt, a.id as menu FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt ";
                                } else {
                        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, a.nummer as nummer, 
              CONCAT('<span style=display:none>',a.name_de,'</span><a style=\"font-weight: normal;\" href=\"index.php?module=artikel&action=edit&id=',a.id,'\">',if(a.intern_gesperrt,CONCAT('<strike>',

if(a.variante,CONCAT(name_de,' <font color=#848484>(Variante von ',(SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1),')</font>'),name_de)

                                                                ,'</strike>'),

if(a.variante,CONCAT(name_de,' <font color=#848484>(Variante von ',(SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1),')</font>'),name_de)
                                                                
),'</a>') as name_de, 
                                                        CONCAT('<span style=display:none>',a.name_de,'</span><a style=\"font-weight: normal;\" href=\"index.php?module=artikel&action=lager&id=',a.id,'\">',(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),'</a>') as lagerbestand,  
                                                        p.abkuerzung as projekt, a.id as menu 
                                                        FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt ";        
        
                                }
              // fester filter
//if(a.variante,CONCAT('Variante von ',(SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1),': ',a.name_de),a.name_de)
              //$where = "a.geloescht=0 ".$this->app->erp->ProjektRechte();
              $where = "a.geloescht=0 ".$this->app->erp->ProjektRechte();

                                $moreinfo = true;
              $count = "SELECT COUNT(a.id) FROM artikel a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.geloescht=0 ".$this->app->erp->ProjektRechte();

            break;
   
  

            case "artikeltabelle":
              // headings
              $heading =  array('','Nummer','Artikel','Lagerbestand','Projekt','Men&uuml;');
              $width   =  array('open','10%','60%','5%','15%','10%');
              $findcols = array('open','nummer','name_de','CAST((SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as SIGNED)','projekt','id');
         $menucol=5;

        $defaultorder = 6;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;


                                if($this->app->erp->Firmendaten("artikel_suche_kurztext")=="1")
                                {
                $searchsql = array('a.name_de','kurztext_de','a.nummer','p.abkuerzung',"a.hersteller","a.herstellernummer","a.ean","a.anabregs_text",
                                                "(SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1)");
  
                                }
                                else {
                $searchsql = array('a.name_de','a.nummer','p.abkuerzung',"a.hersteller","a.herstellernummer","(SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1)","a.ean");
                                }

              $menu =  "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>";

              // SQL statement
                                if($this->app->Conf->WFdbType=="postgre") {
              $sql = "SELECT a.id, '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, a.nummer as nummer, 
CASE WHEN a.intern_gesperrt='1'
THEN CONCAT('<strike>',a.name_de,'</strike>',a.name_de)
END as name_de, 
(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) as lagerbestand,  p.abkuerzung as projekt, a.id as menu FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt ";
                                } else {
                        $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, a.nummer as nummer, 
              CONCAT('<span style=display:none>',a.name_de,'</span><a style=\"font-weight: normal;\" href=\"index.php?module=artikel&action=edit&id=',a.id,'\">',if(a.intern_gesperrt,CONCAT('<strike>',

if(a.variante,CONCAT(name_de,' <font color=#848484>(Variante von ',(SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1),')</font>'),name_de)

                                                                ,'</strike>'),

if(a.variante,CONCAT(name_de,' <font color=#848484>(Variante von ',(SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1),')</font>'),name_de)
                                                                
),'</a>') as name_de, 
                                                        CONCAT('<span style=display:none>',a.name_de,'</span><a style=\"font-weight: normal;\" href=\"index.php?module=artikel&action=lager&id=',a.id,'\">',(SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id),'</a>') as lagerbestand,  
                                                        p.abkuerzung as projekt, a.id as menu 
                                                        FROM  artikel a LEFT JOIN projekt p ON p.id=a.projekt ";        
        
                                }
              // fester filter
//if(a.variante,CONCAT('Variante von ',(SELECT tmp.nummer FROM artikel tmp WHERE a.variante_von=tmp.id LIMIT 1),': ',a.name_de),a.name_de)
              //$where = "a.geloescht=0 ".$this->app->erp->ProjektRechte();
              $where = "a.geloescht=0 ".$this->app->erp->ProjektRechte();

                                $moreinfo = true;
              $count = "SELECT COUNT(a.id) FROM artikel a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.geloescht=0 ".$this->app->erp->ProjektRechte();

            break;
   
       
   case "ueberweisung":
              // headings
              $heading =  array('','Zahlbar bis','Name/Verwendungszweck','BIC','IBAN','Brutto','W&auml;hrung','Men&uuml;');
              $width   =  array('1%','10%','40%','10%','18%','5%','3%','5%');
              $findcols = array('d.id','d.datum','a.name','a.swift','a.iban','d.betrag','d.waehrung','v.id');
              //$searchsql = array('d.id',"DATE_FORMAT(d.datum,'%d.%m.%Y')",'a.name','a.swift','a.iban','d.betrag','d.waehrung','v.id');
              $searchsql = array('a.name',"DATE_FORMAT(d.datum,'%d.%m.%Y')",'a.swift');

                                $defaultorder=7;
                                $defaultorderdesc=1;

                $menu = "<table><tr><td nowrap><a href=\"index.php?module=zahlungsverkehr&action=dtaedit&id=%value%\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\"></a>&nbsp;<!--<a onclick=DeleteDialog(\"index.php?module=dta&action=delete&id=%value%\")><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>--><a onclick=BezahltDialog(\"index.php?module=verbindlichkeit&action=bezahlt&id=%value%&cmd=ueberweisung\")><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/ack.png\" border=\"0\"></a></td></tr></table>";


              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS d.id, 
                        CONCAT('<input type=\"checkbox\" ',if(d.datum<=NOW() AND a.iban!='','checked',''),' name=\"dta[]\" value=\"',d.id,'\">') as auswahl, 
                        DATE_FORMAT(d.datum,'%d.%m.%Y') as datum,
      CONCAT('<b>',a.name,'</b><br><i>',d.vz1,'</i>') as name, 
      if(a.swift='',CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',a.id,'#tabs-2\" target=\"_blank\"><font color=red>fehlt - bitte nachtragen</font></a>'),a.swift) as BIC, 
      if(a.iban='',CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',a.id,'#tabs-2\" target=\"_blank\"><font color=red>fehlt - bitte nachtragen</font></a>'),a.iban) as IBAN, 
      d.betrag, d.waehrung,
      d.id FROM dta d LEFT JOIN adresse a ON d.adresse = a.id ";

              // Fester filter
              $where = " d.datei <=0 AND d.lastschrift!=1";

              // gesamt anzahl
              $count = "SELECT COUNT(d.id) FROM dta d WHERE d.datei <=0 AND d.lastschrift!=1";

            break;
 
       
   case "ueberweisungarchiv":
 // headings
              $heading =  array('Bezahlt am','Zahlbar bis','Name/Verwendungszweck','BIC','IBAN','Brutto','W&auml;hrung','Men&uuml;');
              $width   =  array('15%','10%','30%','10%','18%','5%','3%','5%');
              $findcols = array('dta.datum','d.datum','a.name','a.swift','a.iban','d.betrag','d.waehrung','v.id');
              //$searchsql = array('d.id',"DATE_FORMAT(d.datum,'%d.%m.%Y')",'a.name','a.swift','a.iban','d.betrag','d.waehrung','v.id');
              $searchsql = array('a.name',"DATE_FORMAT(d.datum,'%d.%m.%Y')","DATE_FORMAT(dta.datum,'%d.%m.%Y')",'a.swift');

                                $defaultorder=7;
                                $defaultorderdesc=1;

                $menu = "<table><tr><td nowrap>".
                                                "<a href=\"index.php?module=dta&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\"></a>".
                                                "<a onclick=DeleteDialog(\"index.php?module=dta&action=delete&id=%value%\")><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                "<a onclick=UndoDialog(\"index.php?module=zahlungsverkehr&action=undodta&id=%value%\")><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.png\" border=\"0\"></a></td></tr></table>";


              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS d.id, 
                        DATE_FORMAT(dta.datum,'%d.%m.%Y') as dtadatum,
                        DATE_FORMAT(d.datum,'%d.%m.%Y') as datum,
      CONCAT('<b>',a.name,'</b><br><i>',d.vz1,'</i>') as name, 
      if(a.swift='',CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',a.id,'#tabs-2\" target=\"_blank\"><font color=red>fehlt - bitte nachtragen</font></a>'),a.swift) as BIC, 
      if(a.iban='',CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',a.id,'#tabs-2\" target=\"_blank\"><font color=red>fehlt - bitte nachtragen</font></a>'),a.iban) as IBAN, 
      d.betrag, d.waehrung,
      d.id FROM dta d LEFT JOIN adresse a ON d.adresse = a.id LEFT JOIN dta_datei dta ON d.datei=dta.id ";

              // Fester filter
              $where = " d.datei > 0 AND d.lastschrift!=1";

              // gesamt anzahl
              $count = "SELECT COUNT(d.id) FROM dta d WHERE d.datei > 0  AND d.lastschrift!=1";

          break;


 
   
   case "verbindlichkeiten":
              // headings
              $heading =  array('Nr','Lieferant','BIC','IBAN','Betrag','Rechnung','Skonto Bis','Zahlbar Bis','Skonto','Monitor','Men&uuml;');
              $width   =  array('5%','20%','20%','20%','5%','20%','5%','1%','1%','1%','1%','10%');
              $findcols = array('v.id','a.name','a.swift','a.iban','v.betrag','v.rechnung','v.skontobis','v.zahlbarbis','v.skonto','v.status','v.id');
              $searchsql = array('a.name','v.status','v.rechnung','v.betrag','v.kostenstelle');

                                $defaultorder=11;
                                $defaultorderdesc=1;

                                $alignright=array(5);


                $menu = "<table><tr><td nowrap><a href=\"index.php?module=verbindlichkeit&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\"></a><a onclick=DeleteDialog(\"index.php?module=verbindlichkeit&action=delete&id=%value%\")><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a><a onclick=BezahltDialog(\"index.php?module=verbindlichkeit&action=bezahlt&id=%value%\")><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/ack.png\" border=\"0\"></a></td></tr></table>";

              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS v.id, 
       v.id as 'nr.', a.name as lieferant, 
 if(a.swift='',CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',a.id,'#tabs-2\" target=\"_blank\"><font color=red>fehlt - bitte nachtragen</font></a>'),a.swift) as BIC, 
      if(a.iban='',CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',a.id,'#tabs-2\" target=\"_blank\"><font color=red>fehlt - bitte nachtragen</font></a>'),a.iban) as IBAN, 
      v.betrag, if(v.betrag <0, CONCAT('<font color=red>',v.verwendungszweck,' ',if(v.rechnung!='','RE ',''),v.rechnung,'</font>'),v.rechnung), 
    if(v.skontobis='0000-00-00','-',if(v.skontobis >=NOW(),
      CONCAT('<font color=red>',DATE_FORMAT(v.skontobis,'%d.%m.%Y'),'</font>'),DATE_FORMAT(v.skontobis,'%d.%m.%Y'))) as skonto_bis,
    if(v.zahlbarbis='0000-00-00','-',DATE_FORMAT(v.zahlbarbis,'%d.%m.%Y')) as zahlbar_bis,
    if(v.skonto > 0,CONCAT(v.skonto,' %'),'-') as skonto, 
                        (".$this->IconsSQLVerbindlichkeit().")  as icons,
      v.id FROM verbindlichkeit v LEFT JOIN adresse a ON v.adresse = a.id ";

              // Fester filter
              $where = " v.status!='bezahlt' ";

              // gesamt anzahl
              $count = "SELECT COUNT(v.id) FROM verbindlichkeit v WHERE v.status!='bezahlt' ";

            break;

 
   
   case "verbindlichkeitenarchiv":
              // headings
              $heading =  array('Nr','Lieferant','BIC','IBAN','Brutto','Rechnung','Skonto Bis','Zahlbar bis','Skonto','Status','Abger.','Men&uuml;');
              $width   =  array('5%','20%','5%','5%','5%','20%','5%','5%','5%','5%','2%','50');
              $findcols = array('v.id','a.name','a.swift','a.iban','v.betrag','v.rechnung','v.skontobis','v.zahlbarbis','v.skonto','v.status','v.bezahlt','v.id');
              $searchsql = array('a.name','v.status','v.rechnung','v.betrag','v.bezahlt');


                                $defaultorder=12;
                                $defaultorderdesc=1;


                $menu = "<a href=\"index.php?module=verbindlichkeit&action=editreadonly&id=%value%\"  target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a><!--<a href=\"index.php?module=verbindlichkeit&action=delete&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>--><a href=\"index.php?module=verbindlichkeit&action=kostenstelle&id=%value%#tabs-2\" ><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/ack.png\" border=\"0\"></a><a onclick=UndoDialog(\"index.php?module=verbindlichkeit&action=offen&id=%value%\")><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.png\" border=\"0\"></a>";
                                /*<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                                        */

              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS v.id, v.id, a.name as name, a.swift as bic, 
                                                a.iban, v.betrag, v.rechnung, if(v.skontobis='0000-00-00','-',v.skontobis) as skontobis, v.zahlbarbis,
                                                if(v.skonto > 0,CONCAT(v.skonto,' %'),'-') as skonto,if(v.status='','offen',v.status) as status,CONCAT(IFNULL(v.kostenstelle,''),if(v.bezahlt,' (ja)','')) as kostenstelle,v.id FROM verbindlichkeit v LEFT JOIN adresse a ON a.id=v.adresse";

              // Fester filter
              $where = " v.status='bezahlt' ";

              // gesamt anzahl
              $count = "SELECT COUNT(v.id) FROM verbindlichkeit v WHERE v.status='bezahlt' ";

            break;

 
   case "chargenverwaltung":
              // headings
              $heading =  array('Artikel','Nummer','Lager','Menge','Charge','Eingang','Bemerkung','Men&uuml;');
              $width   =  array('25%','5%','5%','5%','20%','10%','20%','5%');
              $findcols = array('a.name_de','a.nummer','l.kurzbezeichnung','lm.menge','lm.charge','lm.datum','lm.internebemerkung','lm.id');
              $searchsql = array('a.name_de','a.nummer','l.kurzbezeichnung','lm.charge',"DATE_FORMAT(lm.datum,'%d.%m.%Y')",'lm.internebemerkung');

                                $defaultorder=9;
                                $defaultorderdesc=1;

                $menu = "<a href=\"\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a><!--<a href=\"\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>-->";
                                /*<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                                        */

              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS lm.id, a.name_de as name, a.nummer, IFNULL(l.kurzbezeichnung,'Zwischenlager') as lager, 
                                                lm.menge, lm.charge, lm.datum as eingang, lm.internebemerkung,  lm.id
                                FROM lager_charge lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz LEFT JOIN artikel a ON a.id=lm.artikel";

              // Fester filter
//            $where = " ";

              // gesamt anzahl
              $count = "SELECT COUNT(lm.id) FROM lager_charge lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz ";

            break;

   case "chargen":
              // headings
              $heading =  array('Lager','Menge','Charge','Eingang','Bemerkung','Men&uuml;');
              $width   =  array('10%','30%','20%','10%','20%','5%');
              $findcols = array('l.kurzbezeichnung','lm.menge','lm.charge','lm.datum','lm.internebemerkung','id');
              $searchsql = array('l.kurzbezeichnung','lm.charge',"DATE_FORMAT(lm.datum,'%d.%m.%Y')",'lm.internebemerkung');

                $menu = "<a href=\"index.php?module=artikel&action=chargedelete&id=$id&sid=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>";
                                /*<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                                        */

              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS lm.id, IFNULL(l.kurzbezeichnung,'Zwischenlager') as lager, lm.menge, lm.charge, lm.datum as eingang, lm.internebemerkung,  lm.id
                                FROM lager_charge lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz";

              // Fester filter
              $where = " lm.artikel='$id' ";

              // gesamt anzahl
              $count = "SELECT COUNT(lm.id) FROM lager_charge lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz WHERE lm.artikel='$id'";


            break;

   case "mindesthaltbarkeitsdatum":
              // headings
              $heading =  array('Lager','Menge','Verfallsdatum','Charge','Bemerkung','Men&uuml;');
              $width   =  array('10%','10%','20%','10%','30%','5%');
              $findcols = array('l.kurzbezeichnung','lm.menge','lm.mhddatum','lm.charge','lm.internebemerkung','lm.id');
              $searchsql = array('l.kurzbezeichnung',"menge","DATE_FORMAT(lm.mhddatum,'%d.%m.%Y')",'lm.charge','lm.internebemerkung');

                                $defaultorder=3;
                                $defaultorderdesc=0;



                $menu = "<a href=\"index.php?module=artikel&action=mhddelete&id=$id&sid=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>";
                        /*<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        */
              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS lm.id, IFNULL(l.kurzbezeichnung,'Zwischenlager') as lager, lm.menge, if(lm.mhddatum <= NOW(),CONCAT('<font color=red>',lm.mhddatum,'</font> abgelaufen seit ',DATEDIFF(NOW(),lm.mhddatum),' Tag(en)'),lm.mhddatum), lm.charge, lm.internebemerkung, lm.id
                                FROM lager_mindesthaltbarkeitsdatum lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz ";

              // Fester filter
              $where = " lm.artikel='$id' ";

              // gesamt anzahl
              $count = "SELECT COUNT(lm.id) FROM lager_mindesthaltbarkeitsdatum lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz WHERE lm.artikel='$id'";

            break;



   case "mhdwarning":
        // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#nurabgelaufen').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add(JQUERYREADY,"$('#nurbaldabgelaufen').click( function() { fnFilterColumn2( 0 ); } );");
                        
                                for($r=1;$r<3;$r++)
    {
      $this->app->Tpl->Add(JAVASCRIPT,'
        function fnFilterColumn'.$r.' ( i )
        {
          if(oMoreData'.$r.$name.'==1)
          oMoreData'.$r.$name.' = 0;
          else
          oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
        }
      ');
    }

              $heading =  array('Artikel','Nummer','Lager','Menge','Verfallsdatum','Charge','Bemerkung','Men&uuml;');
              $width   =  array('25%','5%','5%','5%','30%','10%','10%','5%');
              $findcols = array('a.name_de','a.nummer','l.kurzbezeichnung','lm.menge','lm.mhddatum','lm.charge','lm.internebemerkung','lm.id');
              $searchsql = array('a.name_de','a.nummer','l.kurzbezeichnung',"DATE_FORMAT(lm.mhddatum,'%d.%m.%Y')",'lm.charge','lm.internebemerkung');

                $menu = "<a href=\"index.php?module=artikel&action=mindesthaltbarkeitsdatum&id=%value%\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";

                        /*
                                        <a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        */

                                $defaultorder=5;
                                $defaultorderdesc=0;


      //$this->app->erp->Firmendaten("mhd_warnung_tage");
              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS lm.id, a.name_de, a.nummer, IFNULL(l.kurzbezeichnung,'Zwischenlager') as lager, lm.menge, 
                                        if(lm.mhddatum <= NOW(),CONCAT('<font color=red>',lm.mhddatum,'</font> abgelaufen seit ',DATEDIFF(NOW(),lm.mhddatum),' Tag(en)'),lm.mhddatum), lm.charge, lm.internebemerkung, a.id
                                FROM lager_mindesthaltbarkeitsdatum lm LEFT JOIN artikel a ON a.id=lm.artikel LEFT JOIN lager_platz l ON l.id=lm.lager_platz ";

        // START EXTRA more
        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " DATEDIFF(NOW(),lm.mhddatum) > 0 ";
        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " DATEDIFF(NOW(),lm.mhddatum) + ".($this->app->erp->Firmendaten("mhd_warnung_tage")+1)." > 0 ";
        // ENDE EXTRA more

        for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];

              // Fester filter
              $where = " lm.mhddatum IS NOT NULL $tmp";

              // gesamt anzahl
              $count = "SELECT COUNT(lm.id) FROM lager_mindesthaltbarkeitsdatum lm WHERE lm.mhddatum IS NOT NULL ";

            break;


   case "mlm_geworbenvon":
              // headings
                                                        $heading =  array('Lizenznehmer','Name','Men&uuml;');
              $width   =  array('10%','60%','5%');
              $findcols = array('a.kundennummer','a.name','a.id');
              $searchsql = array('kundennummer','name');

                $menu = "<a href=\"index.php?module=adresse&action=multilevel&id=%value%#tabs-1\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        /*<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        */
              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.kundennummer, a.name, a.id FROM
                                adresse a ";

              // Fester filter
              $where = " a.geworbenvon='$id' ";

              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM adresse a WHERE a.geworbenvon='$id'";
         break;
  case "mlmintranetdownline":        // headings
             $heading =  array('','Kunde/Lizenznehmer','Name','Strasse','PLZ','Ort','Telefon','E-Mail','Men&uuml;');
              $width   =  array('5%','10%','20%','20%','5%');
              $findcols = array('open','a.kundennummer','a.name','a.strasse','a.plz','a.ort','a.telefon','a.email','a.id');
              $searchsql = array('a.kundennummer','a.name','a.strasse','a.plz','a.ort','a.telefon','a.email');
              $defaultorder=1;

                $menu = "<!--%value%-->";                        /*<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        */
              // SQL statement 

 $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, a.kundennummer, CONCAT(a.name,' ',ifnull(if(a.mlmfestsetzen=1 AND (a.mlmfestsetzenbis > NOW() OR a.mlmfestsetzenbis IS NULL OR a.mlmfestsetzenbis='0000-00-00'),CONCAT(a.mlmpositionierung,'F'),(SELECT xy.positionierung FROM mlm_positionierung xy WHERE xy.adresse=a.id ORDER by xy.datum DESC LIMIT 1)),''),'') as name, a.strasse, a.plz, a.ort, a.telefon, a.email, a.id FROM
                                adresse a ";

              $menucol=8;

              $moreinfo=true;

              include_once("pages/multilevel.php");
              $mlm = new Multilevel($this->app,true);
              $result = $mlm->MultilevelDownlineTree($this->app->User->GetAdresse(),true);
              for($ir=0;$ir < count($result);$ir++)
                $tmpwhere .= " OR a.id='".$result[$ir]."' ";            

              $eigenestufe = $this->app->DB->Select("SELECT mlmpositionierung FROM adresse WHERE id='".$this->app->User->GetAdresse()."' LIMIT 1");

              $where = " (a.sponsor='".$this->app->User->GetAdresse()."' $tmpwhere ) AND a.mlmpositionierung < $eigenestufe ";

              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM adresse a WHERE (a.sponsor='".$this->app->User->GetAdresse()."' $tmpwhere ) AND a.mlmpositionierung < $eigenestufe";

              // Fester filter
              //$where = " a.sponsor='".$this->app->User->GetAdresse()."' ";
              // gesamt anzahl
              //$count = "SELECT COUNT(a.id) FROM adresse a WHERE a.sponsor='".$this->app->User->GetAdresse()."'";
   break;

   case "mlm_downline":
              // headings
                                                        $heading =  array('Lizenznehmer','Name','Men&uuml;');
              $width   =  array('10%','60%','5%');
              $findcols = array('a.kundennummer','a.name','a.id');
              $searchsql = array('kundennummer','name');

                $menu = "<a href=\"index.php?module=adresse&action=multilevel&id=%value%#tabs-1\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        /*<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        */
              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.kundennummer, a.name, a.id FROM
                                adresse a ";

              // Fester filter
              $where = " a.sponsor='$id' ";

              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM adresse a WHERE a.sponsor='$id'";
            break;



            case "mlm_ohnefreigabe":
              // headings
              //$heading =  array('','Rechnung','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Zahlung','Status','Men&uuml;');
              $heading =  array('Rechnung','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Zahlung','Status','Men&uuml;');
              //$width   =  array('1%','2%','5%','10%','45%','1%','1%','1%','1%','1%','1%');
              $width   =  array('2%','5%','10%','45%','1%','1%','1%','1%','1%','1%');
              //$findcols = array('open','belegnr','vom','kundennummer','kunde','land','projekt','zahlungsweise','soll','zahlung','status','id');
              $findcols = array('belegnr','vom','kundennummer','kunde','land','projekt','zahlungsweise','soll','zahlung','status','id');
              $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')','r.belegnr','adr.kundennummer','r.name','r.land','p.abkuerzung','r.zahlungsweise','r.status',"FORMAT(r.soll,2{$extended_mysql55})",'r.zahlungsstatus','adr.freifeld1');

        //$defaultorder = 12;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorder = 11;  //Optional wenn andere Reihenfolge gewuenscht                
                                $defaultorderdesc=1;
                                $sumcol = 8;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=rechnung&action=multilevel&id=%value%\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=11;
              // SQL statement
              //$sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, r.belegnr as belegnr, DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, 
              $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,r.belegnr as belegnr, DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, 
              adr.kundennummer, ".$this->app->erp->MarkerUseredit("r.name","r.useredittimestamp")." as kunde, r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
             FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                  FROM  rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";
              // Fester filter

              $where = " r.id!='' AND (r.provdatum='0000-00-00' OR r.provdatum='' OR r.provdatum IS NULL) ".$this->app->erp->ProjektRechte();
              
              // gesamt anzahl
              $count = "SELECT COUNT(r.id) FROM rechnung r WHERE r.provdatum='0000-00-00'";

//            $moreinfo = true;
            break;
                        case "mlm_abrechnung_adresse_intranet":
        // headings
             $heading =  array('Belegnr','Name','Zeitraum','Betrag (netto)','Stufe','Men&uuml;');
              $width   =  array('10%','300%','180%','1%','1%','1%');
              $findcols = array('ma.belegnr','ma.adresse','m.von','ma.betrag_netto','ma.neueposition','m.id');
              $searchsql = array('ma.belegnr',"DATE_FORMAT(m.von,'%d.%m.%Y')","DATE_FORMAT(m.bis,'%d.%m.%Y')","a.name","a.kundennummer");

              $defaultorder=1;
              $defaultorderdesc=1;

                $menu = "<a href=\"index.php?module=mlmintranet&action=abrechnungpdf&type=provision&id=%value%#tabs-2\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/provision.png\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=mlmintranet&action=abrechnungpdf&type=abrechnung&id=%value%#tabs-2\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a>";
                        /*<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        */
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS ma.id, ma.belegnr, CONCAT(a.kundennummer,' ',a.name), 
            CONCAT(DATE_FORMAT(m.von,'%d.%m.%Y'),' - ',DATE_FORMAT(m.bis,'%d.%m.%Y')), ma.betrag_netto,ma.neueposition,ma.id
                FROM mlm_abrechnung_adresse ma LEFT JOIN mlm_abrechnung m ON m.id=ma.abrechnung LEFT JOIN adresse a ON a.id=ma.adresse ";

              // Fester filter
              $id=$this->app->User->GetAdresse();

              $where = " ma.adresse='$id' ";

              // gesamt anzahl
              $count = "SELECT COUNT(m.id) FROM mlm_abrechnung_adresse m WHERE m.adresse='$id' ";
      break;
   case "mlm_abrechnung_adresse":
              // headings
             $heading =  array('Belegnr','Name','Zeitraum','Betrag (netto)','Stufe','Men&uuml;');
              $width   =  array('10%','300%','180%','1%','1%','1%');
              $findcols = array('ma.belegnr','ma.adresse','m.von','ma.betrag_netto','ma.neueposition','m.id');
              $searchsql = array('ma.belegnr',"DATE_FORMAT(m.von,'%d.%m.%Y')","DATE_FORMAT(m.bis,'%d.%m.%Y')","a.name","a.kundennummer");

                                                        $defaultorder=1;
                                                        $defaultorderdesc=1;

                $menu = "<a href=\"index.php?module=multilevel&action=abrechnungadresse&id=%value%#tabs-2\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/provision.png\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=multilevel&action=abrechnungadressepdf&id=%value%#tabs-2\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a>";
                        /*<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        */
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS ma.id, ma.belegnr, CONCAT(a.kundennummer,' ',a.name), 
            CONCAT(DATE_FORMAT(m.von,'%d.%m.%Y'),' - ',DATE_FORMAT(m.bis,'%d.%m.%Y')), ma.betrag_netto,ma.neueposition,ma.id
                FROM mlm_abrechnung_adresse ma LEFT JOIN mlm_abrechnung m ON m.id=ma.abrechnung LEFT JOIN adresse a ON a.id=ma.adresse ";

              // Fester filter
              $id=$this->app->Secure->GetGET("id");
              $where = " ma.adresse='$id' ";

              // gesamt anzahl
              $count = "SELECT COUNT(m.id) FROM mlm_abrechnung_adresse m WHERE m.adresse='$id' ";
            break;


   case "mlm_abrechnung_adresse_log":
              // headings
                                                        $heading =  array('Meldung','Datum','Abrechnungszeitraum','Men&uuml;');
              $width   =  array('50%','10%','30%','1%');
              $findcols = array('m.meldung','ab.datum','m.id');
              $searchsql = array("DATE_FORMAT(m.von,'%d.%m.%Y')","DATE_FORMAT(m.bis,'%d.%m.%Y')");

                                                        $defaultorder=1;
                                                        $defaultorderdesc=1;

 //               $menu = "<a href=\"index.php?module=multilevel&action=abrechnung&id=%value%#tabs-2\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=multilevel&action=abrechnungdel&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>";
                        /*<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        */
              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS m.id, m.meldung,DATE_FORMAT(ab.datum,'%d.%m.%Y'),CONCAT(DATE_FORMAT(ab.von,'%d.%m.%Y'),' - ',DATE_FORMAT(ab.bis,'%d.%m.%Y')), m.id
                                FROM mlm_abrechnung_log m LEFT JOIN mlm_abrechnung ab ON ab.id=m.abrechnung";
              $id=$this->app->Secure->GetGET("id");
              $where = " m.adresse='$id' ";


              // Fester filter
//              $where = " ";

              // gesamt anzahl
              $count = "SELECT COUNT(m.id) FROM mlm_abrechnung_log m WHERE m.adresse='$id'";
            break;


   case "mlm_abrechnung_log":
              // headings
                                                        $heading =  array('Datum','Lizenznehmer','Meldung','Abrechnungszeitraum','Men&uuml;');
              $width   =  array('10%','30%','30%','10%','1%');
              $findcols = array('ab.datum','a.kundennummer','m.meldung','ab.von','a.id');
              $searchsql = array("DATE_FORMAT(ab.datum,'%d.%m.%Y')","CONCAT(DATE_FORMAT(ab.von,'%d.%m.%Y'),' - ',DATE_FORMAT(ab.bis,'%d.%m.%Y'))","m.meldung","a.kundennummer","a.name");

                                                        $defaultorder=1;
                                                        $defaultorderdesc=1;

                $menu = "<a href=\"index.php?module=adresse&action=multilevel&id=%value%\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";//&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=multilevel&action=abrechnungdel&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>";
                        /*<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        */
              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS m.id, DATE_FORMAT(ab.datum,'%d.%m.%Y'), CONCAT(a.kundennummer,' ',a.name), 
                                m.meldung,CONCAT(DATE_FORMAT(ab.von,'%d.%m.%Y'),' - ',DATE_FORMAT(ab.bis,'%d.%m.%Y')), a.id
                                FROM mlm_abrechnung_log m LEFT JOIN mlm_abrechnung ab ON ab.id=m.abrechnung LEFT JOIN adresse a ON a.id=m.adresse";
              $id=$this->app->Secure->GetGET("id");
              //$where = " m.adresse='$id' ";


              // Fester filter
//              $where = " ";

              // gesamt anzahl
              $count = "SELECT COUNT(m.id) FROM mlm_abrechnung_log m";
            break;


   case "mlm_abrechnung":
              // headings
                                                        $heading =  array('','Von','Bis','Auszahlung (netto)','Punkte','Bonuspunkte','Men&uuml;');
              $width   =  array('5%','20%','20%','10%','10%','10%','10%');
              $findcols = array('open','m.von','m.bis','m.id');
              $searchsql = array("DATE_FORMAT(m.von,'%d.%m.%Y')","DATE_FORMAT(m.bis,'%d.%m.%Y')");

                                                        $defaultorder=1;
                                                        $defaultorderdesc=1;
              $menucol = 6;

                $menu = "<a href=\"index.php?module=multilevel&action=abrechnungdownload&id=%value%#tabs-2\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=multilevel&action=abrechnunguebersichtsepa&id=%value%#tabs-2\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/sepa.png\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=multilevel&action=abrechnungdel&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>";
                        /*<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        */
              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS m.id, '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, m.von,m.bis,FORMAT(m.summeauszahlung,2{$extended_mysql55}), m.punkte, m.bonuspunkte,m.id
                                FROM mlm_abrechnung m ";

              // Fester filter
//              $where = " ";
              $moreinfo=true;

              // gesamt anzahl
              $count = "SELECT COUNT(m.id) FROM mlm_abrechnung m";
            break;


   case "mlm_wartekonto":
              // headings
                                                        $heading =  array('Lizenznehmer','Name','Betrag','Men&uuml;');
              $width   =  array('10%','30%','30%','5%');
              $findcols = array('a.kundennummer','a.name','betrag','a.id');
              $searchsql = array('kundennummer','name');

                $menu = "<a href=\"index.php?module=adresse&action=multilevel&id=%value%#tabs-2\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        /*<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        */
              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS w.id, a.kundennummer, a.name, SUM(w.betrag) as betrag, a.id FROM
                                mlm_wartekonto w LEFT JOIN adresse a ON a.id=w.adresse ";

              // Fester filter
              $where = " w.abgerechnet=0 GROUP BY w.adresse ";

              // gesamt anzahl
              $count = "SELECT COUNT(w.id) FROM mlm_wartekonto w WHERE w.betrag > 0 AND w.abrechnung=0 GROUP by w.adresse ";
            break;


   case "seriennummernlager":
              // headings
              $heading =  array('Lager','Seriennummer','Bemerkung','Men&uuml;');
              $width   =  array('10%','30%','30%','5%');
              $findcols = array('kurzbezeichnung','seriennummer','internebemerkung','id');
              $searchsql = array('l.kurzbezeichnung','lm.seriennummer','lm.internebemerkung');

                $menu = "<a href=\"\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a><a href=\"\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>";
                        /*<a href=\"index.php?module=artikel&action=edit&id=%value%\" target=\"_blank\">
                                        <img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
                        */
              // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS lm.id, IFNULL(l.kurzbezeichnung,'Zwischenlager') as lager, lm.seriennummer, lm.internebemerkung, lm.id
                                FROM lager_seriennummern lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz ";

              // Fester filter
              $where = " lm.artikel='$id' ";

              // gesamt anzahl
              $count = "SELECT COUNT(lm.id) FROM lager_seriennummern lm LEFT JOIN lager_platz l ON l.id=lm.lager_platz WHERE lm.artikel='$id'";

            break;


   case "adresseseriennummern":
              // headings
              $heading =  array('Datum','Artikel-Nr.','Artikel','Seriennummer','Lieferschein','Men&uuml;');
              $width   =  array('10%','10%','300px','20%','5%','5%');
              $findcols = array('datum','nummer','name_de','seriennummer','belegnr','id');
              $searchsql = array('a.name_de','a.nummer','l.belegnr',"DATE_FORMAT(s.lieferung,'%d.%m.%Y')",'s.seriennummer');


             //$menu = "<a href=\"index.php?module=lieferschein&action=edit&id=%value%\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
             $menu = "<a href=\"#\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";

              // SQL statement
/*                              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, l.datum, adr.name, lp.seriennummer, 
                                if(l.belegnr > 0,l.belegnr,'-') as belegnr,
                                l.status as status, l.id
                                FROM lieferschein_position lp LEFT JOIN lieferschein l ON l.id=lp.lieferschein
                                LEFT JOIN adresse adr ON adr.id=l.adresse ";
*/
                                        $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, DATE_FORMAT(s.lieferung,'%d.%m.%Y') as datum, a.nummer,a.name_de, s.seriennummer, CONCAT('<a href=\"index.php?module=lieferschein&action=pdf&id=',s.lieferschein,'\">',l.belegnr,'</a>') as belegnr, s.id
                                                FROM seriennummern s LEFT JOIN artikel a ON a.id=s.artikel LEFT JOIN lieferschein l ON l.id=s.lieferschein ";
              // Fester filter
              $where = " s.adresse='$id' ";

              // gesamt anzahl
              $count = "SELECT COUNT(s.id) FROM seriennummern s WHERE s.adresse='$id' ";


            break;


   case "seriennummern":
              // headings
              $heading =  array('Datum','Kunde','Seriennummer','Lieferschein','Men&uuml;');
              $width   =  array('10%','30%','30%','5%','5%');
              $findcols = array('datum','name','seriennummer','belegnr','id');
              $searchsql = array('adr.name','l.belegnr',"DATE_FORMAT(s.lieferung,'%d.%m.%Y')",'s.seriennummer');


             //$menu = "<a href=\"index.php?module=lieferschein&action=edit&id=%value%\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";
             $menu = "<a href=\"#\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";

              // SQL statement
/*                              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, l.datum, adr.name, lp.seriennummer, 
                                if(l.belegnr > 0,l.belegnr,'-') as belegnr,
                                l.status as status, l.id
                                FROM lieferschein_position lp LEFT JOIN lieferschein l ON l.id=lp.lieferschein
                                LEFT JOIN adresse adr ON adr.id=l.adresse ";
*/
                                        $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, DATE_FORMAT(s.lieferung,'%d.%m.%Y') as datum, IFNULL(adr.name,''), s.seriennummer, l.belegnr as belegnr, s.id
                                                FROM seriennummern s LEFT JOIN adresse adr ON adr.id=s.adresse LEFT JOIN lieferschein l ON l.id=s.lieferschein";
              // Fester filter
              $where = " s.artikel='$id' ";

              // gesamt anzahl
              $count = "SELECT COUNT(s.id) FROM seriennummern s WHERE s.artikel='$id' ";


            break;


   case "instueckliste":
              // headings
              $heading =  array('Artikel','Nummer','Menge','Men&uuml;');
              $width   =  array('70%','10%','5%','10%');
              $findcols = array('artikel','nummer','menge','id');
              $searchsql = array('a.name_de','a.nummer','s.menge');

                                $defaultorder=4;
                                $defaultorderdesc=1;


             $menu = "<a href=\"index.php?module=artikel&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>";

              // SQL statement
                                if($this->app->Conf->WFdbType=="postgre") {
              $sql = "SELECT s.id, a.name_de as artikel,a.nummer as nummer, s.menge as menge, 
CASE WHEN (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0
THEN (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id)
ELSE 0
END  as lager, s.artikel as menu
FROM stueckliste s LEFT JOIN artikel a ON s.artikel=a.id ";
              } else {
                                $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, a.name_de as artikel,a.nummer as nummer, s.menge as menge,
                        s.stuecklistevonartikel
 as menu
      FROM stueckliste s LEFT JOIN artikel a ON s.stuecklistevonartikel=a.id ";
                                }
              // Fester filter
              $where = "s.artikel='$id' ";

              // gesamt anzahl
              $count = "SELECT COUNT(s.id) FROM stueckliste s WHERE s.stuecklistevonartikel='$id' ";


            break;



            case "stueckliste":
              // headings
              $heading =  array('Artikel','Nummer','Menge','Lager','Reserviert','Men&uuml;');
              $width   =  array('50%','10%','5%','5%','5%','10%');
              $findcols = array('artikel','nummer','menge','lager','reserviert','id');
              $searchsql = array('a.name_de','a.nummer','s.menge');


             $menu = "<a href=\"index.php?module=artikel&action=editstueckliste&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=delstueckliste&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=einkaufcopy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>";

              // SQL statement
              $sql = "SELECT s.id, CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',a.id,'\" target=\"_blank\">',a.name_de,'</a>') as artikel,
                        CONCAT('<a href=\"index.php?module=artikel&action=edit&id=',a.id,'\" target=\"_blank\">',a.nummer,'</a>') as nummer, s.menge as menge, 

                                CASE WHEN (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id) > 0
                                THEN (SELECT SUM(l.menge) FROM lager_platz_inhalt l WHERE l.artikel=a.id)
                                ELSE 0
                                END  as lager, 

                                CASE WHEN (SELECT SUM(lr.menge) FROM lager_reserviert lr WHERE lr.artikel=a.id)  > 0 
                                THEN (SELECT SUM(lr.menge) FROM lager_reserviert lr WHERE lr.artikel=a.id)  
                                ELSE 0
                                END as reserviert, 

                                s.id as menu
                                FROM stueckliste s LEFT JOIN artikel a ON s.artikel=a.id ";
              // Fester filter
              $where = "s.stuecklistevonartikel='$id' ";

              // gesamt anzahl
              $count = "SELECT COUNT(s.id) FROM stueckliste s WHERE s.stuecklistevonartikel='$id' ";


            break;


            case "arbeitsnachweiseinbearbeitung":
              $heading =  array('','Arbeitsnachweis','Prefix','Vom','Kd-Nr.','Kunde','Land','Projekt','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','10%','40%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','prefix','vom','kundennummer','kunde','land','projekt','status','id');
              $searchsql = array('DATE_FORMAT(l.datum,\'%d.%m.%Y\')','l.belegnr','adr.kundennummer','l.name','l.prefix','l.land','p.abkuerzung','l.status');

        $defaultorder = 10;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=arbeitsnachweis&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=arbeitsnachweis&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=arbeitsnachweis&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

         $menucol=9;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 'ENTWURF' as belegnr, l.prefix as prefix, DATE_FORMAT(l.datum,'%Y-%m-%d') as vom, adr.kundennummer as kundennummer,
              l.name as name,  l.land as land, p.abkuerzung as projekt, 
              UPPER(l.status) as status, l.id
                  FROM  arbeitsnachweis l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.adresse=adr.id  ";
              // Fester filter

              $where = " ( l.status='angelegt' OR l.belegnr=0) ".$this->app->erp->ProjektRechte();
              
              // gesamt anzahl
              $count = "SELECT COUNT(l.id) FROM arbeitsnachweis l WHERE ( l.status='angelegt' OR l.belegnr=0)";

              $moreinfo = true;
              
            break;
        

            case "reisekosteninbearbeitung":

                          $heading =  array('','Reisekosten','Vom','Kd-Nr.','Kunde','Anlass','Projekt','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','30%','30%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','kunde','anlass','projekt','status','id');
        $searchsql = array('DATE_FORMAT(l.datum,\'%d.%m.%Y\')','l.belegnr','adr.kundennummer','l.name','l.anlass','p.abkuerzung','l.status');                                                                   

                                $defaultorder=9;
                                $defaultorderdesc=1;
                            
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=reisekosten&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=reisekosten&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=reisekosten&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

             $menucol=8;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,
 'ENTWURF' as belegnr, DATE_FORMAT(l.datum,'%Y-%m-%d') as vom, adr.kundennummer as kundennummer, 
              l.name as name, l.anlass as anlass, p.abkuerzung as projekt,
              UPPER(l.status) as status, l.id
                  FROM  reisekosten l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.adresse=adr.id 
                        LEFT JOIN adresse ma ON ma.id=l.mitarbeiter ";
              // Fester filter


              $where = " ( l.status='angelegt' OR l.belegnr=0) ".$this->app->erp->ProjektRechte();
              
              // gesamt anzahl
              $count = "SELECT COUNT(l.id) FROM reisekosten l WHERE ( l.status='angelegt' OR l.belegnr=0)";

              $moreinfo = true;
              
            break;
        


            case "lieferscheineinbearbeitung":
 // headings
              $heading =  array('','Lieferschein','Vom','Kd-Nr.','Kunde','Land','Projekt','Versand','Art','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','35%','5%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','kunde','land','projekt','versandart','art','status','id');
              $searchsql = array('l.id','DATE_FORMAT(l.datum,\'%d.%m.%Y\')','l.belegnr','adr.kundennummer','l.name','l.land','p.abkuerzung','l.status','l.plz','l.id','adr.freifeld1','l.ihrebestellnummer');
        $defaultorder = 11;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=lieferschein&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=lieferschein&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
     //             "&nbsp;<a href=\"index.php?module=paketmarke&action=create&frame=false&sid=lieferschein&id=%value%\" class=\"popup\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/stamp.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=lieferschein&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 'ENTWURF' as belegnr, DATE_FORMAT(l.datum,'%Y-%m-%d') as vom, adr.kundennummer as kundennummer,
              ".$this->app->erp->MarkerUseredit("l.name","l.useredittimestamp")." as kunde, l.land as land, p.abkuerzung as projekt, l.versandart as versandart,  
              l.lieferscheinart as art, UPPER(l.status) as status, l.id
                  FROM  lieferschein l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.adresse=adr.id  ";
        

              $where = " ( l.status='angelegt' OR l.belegnr=0) ".$this->app->erp->ProjektRechte();
              
              // gesamt anzahl
              $count = "SELECT COUNT(l.id) FROM lieferschein l WHERE ( l.status='angelegt' OR l.belegnr=0)";

              $moreinfo = true;
              
            break;
        

            case "arbeitsnachweiseoffene":
              $heading =  array('','Arbeitsnachweis','Prefix','Vom','Kd-Nr.','Kunde','Land','Projekt','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','10%','40%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','prefix','vom','kundennummer','kunde','land','projekt','status','id');
              $searchsql = array('DATE_FORMAT(l.datum,\'%d.%m.%Y\')','l.belegnr','adr.kundennummer','l.name','l.prefix','l.land','p.abkuerzung','l.status');

        $defaultorder = 10;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=arbeitsnachweis&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=arbeitsnachweis&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=arbeitsnachweis&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

         $menucol=9;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, l.belegnr as belegnr, l.prefix as prefix, DATE_FORMAT(l.datum,'%Y-%m-%d') as vom, adr.kundennummer as kundennummer,
              l.name as name,  l.land as land, p.abkuerzung as projekt, 
              UPPER(l.status) as status, l.id
                  FROM  arbeitsnachweis l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.adresse=adr.id  ";
              // Fester filter

 

              $where = " l.id!='' AND l.status='freigegeben' ".$this->app->erp->ProjektRechte();
              
              // gesamt anzahl
              $count = "SELECT COUNT(l.id) FROM arbeitsnachweis l WHERE l.status='freigegeben'";

              $moreinfo = true;
            break;


            case "reisekostenoffene":
                          $heading =  array('','Reisekosten','Vom','Kd-Nr.','Kunde','Anlass','Projekt','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','30%','30%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','kunde','anlass','projekt','status','id');
        $searchsql = array('DATE_FORMAT(l.datum,\'%d.%m.%Y\')','l.belegnr','adr.kundennummer','l.name','l.anlass','p.abkuerzung','l.status');                                                                   

                                $defaultorder=9;
                                $defaultorderdesc=1;
                            
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=reisekosten&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=reisekosten&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=reisekosten&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

             $menucol=8;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,
 l.belegnr as belegnr, DATE_FORMAT(l.datum,'%Y-%m-%d') as vom, adr.kundennummer as kundennummer, 
              l.name as name, l.anlass as anlass, p.abkuerzung as projekt,
              UPPER(l.status) as status, l.id
                  FROM  reisekosten l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.adresse=adr.id 
                        LEFT JOIN adresse ma ON ma.id=l.mitarbeiter ";
              // Fester filter


              $where = " l.id!='' AND l.status='abgeschickt' ".$this->app->erp->ProjektRechte();
              
              // gesamt anzahl
              $count = "SELECT COUNT(l.id) FROM reisekosten l WHERE l.status='abgeschickt'";

              $moreinfo = true;
            break;



            case "lieferscheineoffene":
              // headings
              $heading =  array('','Lieferschein','Vom','Kd-Nr.','Kunde','Land','Projekt','Versand','Art','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','35%','5%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','kunde','land','projekt','versandart','art','status','id');
              $searchsql = array('l.id','DATE_FORMAT(l.datum,\'%d.%m.%Y\')','l.belegnr','adr.kundennummer','l.name','l.land','p.abkuerzung','l.status','l.plz','l.id','adr.freifeld1','l.ihrebestellnummer');

        $defaultorder = 11;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=lieferschein&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=lieferschein&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
     //             "&nbsp;<a href=\"index.php?module=paketmarke&action=create&frame=false&sid=lieferschein&id=%value%\" class=\"popup\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/stamp.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=lieferschein&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, l.belegnr, DATE_FORMAT(l.datum,'%Y-%m-%d') as vom, adr.kundennummer as kundennummer,
              ".$this->app->erp->MarkerUseredit("l.name","l.useredittimestamp")." as kunde, l.land as land, p.abkuerzung as projekt, l.versandart as versandart,  
              l.lieferscheinart as art, UPPER(l.status) as status, l.id
                  FROM  lieferschein l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.adresse=adr.id  ";
        
              $where = " l.id!='' AND l.status='freigegeben' ".$this->app->erp->ProjektRechte();
              
              // gesamt anzahl
              $count = "SELECT COUNT(l.id) FROM lieferschein l WHERE l.status='freigegeben'";

              $moreinfo = true;
            break;


            case "kontoauszuege":

              // headings
              $heading =  array('','Vom','Vorgang','SOLL','HABEN','Gebuehr','Abgeschlossen','Men&uuml;');
              $width   =  array('1%','1%','40%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','vom','vorgang','soll','haben','gebuehr','fertig','id');
              $searchsql = array('k.id','k.buchung','k.vorgang','k.soll','k.haben');

                                $defaultorder=8;
                                $defaultorderdesc=1;


              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=zahlungseingang&action=editzeile&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=zahlungseingang&action=editzeile&cmd=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "</td></tr></table>";

              $menucol=7;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS k.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, DATE_FORMAT(k.buchung,'%Y-%m-%d') as vom, 
              if(k.importfehler,LEFT(CONCAT('<s>',REPLACE(REPLACE(k.vorgang, '\r', ''), '\n', ''),'</s>'),80),LEFT(REPLACE(REPLACE(k.vorgang, '\r', ''), '\n', ''),80)) as vorgang, 
              k.soll, k.haben, k.gebuehr,
               if(k.fertig=1,'abgeschlossen','pr&uuml;fen') as fertig, k.id
                  FROM  kontoauszuege k ";
              // Fester filter


              //k.vorgang, k.soll, k.haben, k.gebuehr,  
              $where = " k.konto='$id' ";

              // gesamt anzahl
              $count = "SELECT COUNT(k.id) FROM kontoauszuege k WHERE k.konto='$id'";

              $moreinfo = true;

            break;


            case "arbeitsnachweise":
                                // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#arbeitsnachweisoffen').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add(JQUERYREADY,"$('#arbeitsnachweisheute').click( function() { fnFilterColumn2( 0 ); } );");

        $defaultorder = 11;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;
                        
                                for($r=1;$r<3;$r++)
    {
      $this->app->Tpl->Add(JAVASCRIPT,'
        function fnFilterColumn'.$r.' ( i )
        {
          if(oMoreData'.$r.$name.'==1)
          oMoreData'.$r.$name.' = 0;
          else
          oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
        }
      ');
    }
        // ENDE EXTRA checkboxen
              $heading =  array('','','Arbeitsnachweis','Prefix','Vom','Kd-Nr.','Kunde','Land','Projekt','Status','Men&uuml;');
              $width   =  array('1%','1%','10%','10%','10%','10%','40%','1%','1%','1%','1%','1%');
              $findcols = array('open','id','belegnr','prefix','vom','kundennummer','kunde','land','projekt','status','id');
              $searchsql = array('DATE_FORMAT(l.datum,\'%d.%m.%Y\')','l.belegnr','adr.kundennummer','l.name','l.prefix','l.land','p.abkuerzung','l.status');


              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=arbeitsnachweis&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=arbeitsnachweis&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=arbeitsnachweis&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

         $menucol=10;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 

CONCAT('<input type=\"checkbox\" name=\"arbeitsnachweis[]\" class=\"checkall\" value=\"',l.id,'\" ',if(l.status!='abgerechnet','checked',''),'>') as auswahl,

l.belegnr as belegnr, l.prefix as prefix, DATE_FORMAT(l.datum,'%Y-%m-%d') as vom, adr.kundennummer as kundennummer,
              l.name as name,  l.land as land, p.abkuerzung as projekt, 
              UPPER(l.status) as status, l.id
                  FROM  arbeitsnachweis l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.adresse=adr.id  ";
              // Fester filter

 

                                // START EXTRA more
        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " l.status='freigegeben' ";
        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " l.datum=CURDATE() ";
                                // ENDE EXTRA more

                                for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];


              $where = " l.id!='' AND l.status!='angelegt' $tmp ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(l.id) FROM arbeitsnachweis l";

              $moreinfo = true;

            break;


            case "inventur":
                                // START EXTRA checkboxen

                        $heading =  array('Vom','Name','Projekt','Status','Men&uuml;');
              $width   =  array('15%','50%','15%','10%','10%');
              $findcols = array('vom','name','projekt','status','id');
        $searchsql = array('l.id','l.name','l.datum','p.abkuerzung','l.status','l.id');                                                                   


        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=inventur&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=inventur&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=inventur&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, DATE_FORMAT(l.datum,'%Y-%m-%d') as vom, 
                                l.name,
              p.abkuerzung as projekt,
              UPPER(l.status) as status, l.id
                  FROM  inventur l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.adresse=adr.id 
                        LEFT JOIN adresse ma ON ma.id=l.mitarbeiter ";
              // Fester filter


              $where = " l.id!='' $tmp ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(l.id) FROM inventur l";

              //$moreinfo = true;

            break;

            case "anfrage":
                                // START EXTRA checkboxen

                        $heading =  array('Vom','Mitarbeiter','Kd-Nr.','Kunde','Projekt','Status','Men&uuml;');
              $width   =  array('15%','15%','5%','30%','15%','10%','10%');
              $findcols = array('vom','mitarbeiter','kundennummer','name','projekt','status','id');
        $searchsql = array('l.datum','adr.name','adr2.kundennummer','l.name','p.abkuerzung','l.status','l.id');                                                                   

        $defaultorder = 7;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;
                            
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=anfrage&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=anfrage&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
 "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=anfrage&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=anfrage&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, DATE_FORMAT(l.datum,'%Y-%m-%d') as vom, 
                                adr.name as mitarbeiter,
                                adr2.kundennummer,
                                l.name,
              p.abkuerzung as projekt,
              UPPER(l.status) as status, l.id
                  FROM  anfrage l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.bearbeiterid=adr.id 
                        LEFT JOIN adresse adr2 ON l.adresse=adr2.id
                        LEFT JOIN adresse ma ON ma.id=l.mitarbeiter ";
              
                                // Fester filter
                                if($this->app->User->GetType()!="admin"){
                                        if($this->app->User->GetProjektleiter())
                                        {
                                                        // normaler angestellter
                                $where = " l.id!='' $tmp ".$this->app->erp->ProjektleiterRechte();
                                $count = "SELECT COUNT(l.id) FROM anfrage l WHERE  l.id!='' ".$this->app->erp->ProjektleiterRechte();

                                        } else {                
                                                        // normaler angestellter
                                $where = " l.id!='' AND l.status!='abgerechnet' AND l.bearbeiterid='".$this->app->User->GetAdresse()."'  
                                                                $tmp ".$this->app->erp->ProjektRechte();
                                $count = "SELECT COUNT(l.id) FROM anfrage l WHERE 
                                                                l.bearbeiterid='".$this->app->User->GetAdresse()."' AND l.status!='abgerechnet'";
                                                }
                                }
                                else {
                $where = " l.id!='' $tmp ".$this->app->erp->ProjektRechte();
                $count = "SELECT COUNT(l.id) FROM anfrage l";
                                }

              // gesamt anzahl

              //$moreinfo = true;

            break;


            case "anfrageinbearbeitung":
                                // START EXTRA checkboxen

                        $heading =  array('Vom','Mitarbeiter','Kd-Nr.','Kunde','Projekt','Status','Men&uuml;');
              $width   =  array('15%','15%','5%','30%','15%','10%','10%');
              $findcols = array('vom','mitarbeiter','kundennummer','name','projekt','status','id');
        $searchsql = array('l.datum','adr.name','adr2.kundennummer','l.name','p.abkuerzung','l.status','l.id');                                                                   
                            
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=anfrage&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=anfrage&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=anfrage&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, DATE_FORMAT(l.datum,'%Y-%m-%d') as vom, 
                                adr.name as mitarbeiter,
                                adr2.kundennummer,
                                l.name,
              p.abkuerzung as projekt,
              UPPER(l.status) as status, l.id
                  FROM  anfrage l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.bearbeiterid=adr.id 
                        LEFT JOIN adresse adr2 ON l.adresse=adr2.id
                        LEFT JOIN adresse ma ON ma.id=l.mitarbeiter ";
              
                                // Fester filter
                                if($this->app->User->GetType()!="admin"){
                                        if($this->app->User->GetProjektleiter())
                                        {
                                                        // normaler angestellter
                                $where = " l.status='angelegt' AND l.id!='' $tmp ".$this->app->erp->ProjektleiterRechte();
                                $count = "SELECT COUNT(l.id) FROM anfrage l WHERE  l.id!='' ".$this->app->erp->ProjektleiterRechte();

                                        } else {                
                                                        // normaler angestellter
                                $where = " l.id!='' AND l.status='angelegt' AND l.bearbeiterid='".$this->app->User->GetAdresse()."'  
                                                                $tmp ".$this->app->erp->ProjektRechte();
                                $count = "SELECT COUNT(l.id) FROM anfrage l WHERE 
                                                                l.bearbeiterid='".$this->app->User->GetAdresse()."' AND l.status='angelegt'";
                                                }
                                }
                                else {
                $where = "l.status='angelegt' AND l.id!='' $tmp ".$this->app->erp->ProjektRechte();
                $count = "SELECT COUNT(l.id) FROM anfrage l WHERE l.status='angelegt'";
                                }

              // gesamt anzahl

              //$moreinfo = true;

            break;


            case "reisekosten":
                                // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#reisekostenoffen').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add(JQUERYREADY,"$('#reisekostenheute').click( function() { fnFilterColumn2( 0 ); } );");
                        
                                for($r=1;$r<3;$r++)
    {
      $this->app->Tpl->Add(JAVASCRIPT,'
        function fnFilterColumn'.$r.' ( i )
        {
          if(oMoreData'.$r.$name.'==1)
          oMoreData'.$r.$name.' = 0;
          else
          oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
        }
      ');
    }
        // ENDE EXTRA checkboxen

                          $heading =  array('','Reisekosten','Vom','Kd-Nr.','Kunde','Anlass','Projekt','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','30%','30%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','kunde','anlass','projekt','status','id');
        $searchsql = array('DATE_FORMAT(l.datum,\'%d.%m.%Y\')','l.belegnr','adr.kundennummer','l.name','l.anlass','p.abkuerzung','l.status');                                                                   

                                $defaultorder=9;
                                $defaultorderdesc=1;
                            
        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=reisekosten&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=reisekosten&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=reisekosten&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

             $menucol=8;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,
 l.belegnr, DATE_FORMAT(l.datum,'%Y-%m-%d') as vom, adr.kundennummer as kundennummer, 
              l.name as name, l.anlass as anlass, p.abkuerzung as projekt,
              UPPER(l.status) as status, l.id
                  FROM  reisekosten l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.adresse=adr.id 
                        LEFT JOIN adresse ma ON ma.id=l.mitarbeiter ";
              // Fester filter


                                // START EXTRA more
        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " l.status='abgeschickt' ";
        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " l.datum=CURDATE() ";
                                // ENDE EXTRA more

                                for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];


              $where = " l.id!='' AND l.status!='angelegt' $tmp ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(l.id) FROM reisekosten l";

              $moreinfo = true;

            break;




            case "lieferscheine":
                                // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#lieferscheinoffen').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add(JQUERYREADY,"$('#lieferscheinheute').click( function() { fnFilterColumn2( 0 ); } );");
        $this->app->Tpl->Add(JQUERYREADY,"$('#anlieferanten').click( function() { fnFilterColumn3( 0 ); } );");

        $defaultorder = 11;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;
                        
                                for($r=1;$r<4;$r++)
    {
      $this->app->Tpl->Add(JAVASCRIPT,'
        function fnFilterColumn'.$r.' ( i )
        {
          if(oMoreData'.$r.$name.'==1)
          oMoreData'.$r.$name.' = 0;
          else
          oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
        }
      ');
    }
        // ENDE EXTRA checkboxen

              // headings
              $heading =  array('','Lieferschein','Vom','Kd-Nr.','Kunde','Land','Projekt','Versand','Art','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','35%','5%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','kunde','land','projekt','versandart','art','status','id');
              $searchsql = array('l.id','DATE_FORMAT(l.datum,\'%d.%m.%Y\')','l.belegnr','adr.kundennummer','l.name','l.land','p.abkuerzung','l.status','l.plz','l.id','adr.freifeld1','l.ihrebestellnummer');


              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=lieferschein&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=lieferschein&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
 "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=lieferschein&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>".
     //             "&nbsp;<a href=\"index.php?module=paketmarke&action=create&frame=false&sid=lieferschein&id=%value%\" class=\"popup\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/stamp.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=lieferschein&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, l.belegnr, DATE_FORMAT(l.datum,'%Y-%m-%d') as vom, adr.kundennummer as kundennummer,
              ".$this->app->erp->MarkerUseredit("l.name","l.useredittimestamp")." as kunde, l.land as land, p.abkuerzung as projekt, l.versandart as versandart,  
              l.lieferscheinart as art, UPPER(l.status) as status, l.id
                  FROM  lieferschein l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.adresse=adr.id  ";
              // Fester filter

                                // START EXTRA more
        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " l.status='freigegeben' ";
        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " l.datum=CURDATE() ";
        $more_data3 = $this->app->Secure->GetGET("more_data3"); if($more_data3==1) $subwhere[] = " l.lieferantenretoure=1 ";
                                // ENDE EXTRA more

                                for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];


              $where = " l.id!='' AND l.status!='angelegt' $tmp ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(l.id) FROM lieferschein l";

              $moreinfo = true;

            break;


            case "gutschrifteninbearbeitung":
              $heading =  array('','Gutschrift','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlweise','Betrag','bezahlt','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','35%','5%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','kunde','land','projekt','zahlungsweise','soll','zahlung','status','id');
              $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')','r.belegnr','adr.kundennummer','r.name','r.land','p.abkuerzung','r.zahlungsweise','r.status',"FORMAT(r.soll,2{$extended_mysql55})",'r.zahlungsstatus','adr.freifeld1','r.ihrebestellnummer');

        $defaultorder = 12;  //Optional wenn andere Reihenfolge gewuenscht                
                                $alignright= array('9');
                                $sumcol= 9;
        $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=gutschrift&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=gutschrift&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=gutschrift&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=11;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 
                        'ENTWURF' as belegnr,
                                DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, adr.kundennummer as kundennummer,
             ".$this->app->erp->MarkerUseredit("r.name","r.useredittimestamp")." as kunde, r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
              FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                  FROM  gutschrift r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";
              // Fester filter


              $where = " ( r.status='angelegt' OR r.belegnr=0) ".$this->app->erp->ProjektRechte();
              
              // gesamt anzahl
              $count = "SELECT COUNT(r.id) FROM gutschrift r WHERE ( r.status='angelegt' OR r.belegnr=0) ";

              $moreinfo = true;

            break;
        
        

            case "lastschriften_gutschriften":
              $heading =  array('Gutschrift','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlweise','Betrag','Skonto','Men&uuml;');
              $width   =  array('5%','5%','5%','300px','5%','5%','5%','10%','5%','1%');
              $findcols = array('belegnr','vom','kundennummer','kunde','land','projekt','zahlungsweise','soll','zahlungszielskonto','id');
              $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')','r.belegnr','adr.kundennummer','r.name','r.land','p.abkuerzung','r.zahlungsweise','r.status',
                                        "FORMAT(r.soll*(1.0-(r.zahlungszielskonto/100)),2{$extended_mysql55})",'r.zahlungsstatus','adr.freifeld1','r.ihrebestellnummer');
        $defaultorder = 10;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;
                                $alignright= array('8');
                                $sumcol= 8;
              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=gutschrift&action=edit&id=%value%\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=gutschrift&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=gutschrift&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=9;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,
                        r.belegnr,
                                DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, adr.kundennummer as kundennummer,
              ".$this->app->erp->MarkerUseredit("r.name","r.useredittimestamp")." as kunde, r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
              FORMAT(r.soll*(1.0-(r.zahlungszielskonto/100)),2{$extended_mysql55}) as soll, r.zahlungszielskonto,r.id
                  FROM  gutschrift r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";
              // Fester filter

                        $where = " r.id!='' AND (r.zahlungsweise='lastschrift' OR r.zahlungsweise='einzugsermaechtigung') 
            AND (r.status='freigegeben' OR r.status='versendet') AND r.dta_datei <=0 
                                                AND (r.manuell_vorabbezahlt='' OR r.manuell_vorabbezahlt='0000-00-00') ".$this->app->erp->ProjektRechte();

        // gesamt anzahl
              $count = "SELECT COUNT(r.id) FROM gutschrift r WHERE 
                                                (r.zahlungsweise='lastschrift' OR r.zahlungsweise='einzugsermaechtigung') 
                                                AND (r.status='freigegeben' OR r.status='versendet') AND (r.manuell_vorabbezahlt='' OR r.manuell_vorabbezahlt='0000-00-00') AND r.dta_datei <=0 ".$this->app->erp->ProjektRechte();

            break;


            case "gutschriftenoffene":
              $heading =  array('','Gutschrift','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlweise','Betrag','bezahlt','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','35%','5%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','kunde','land','projekt','zahlungsweise','soll','zahlung','status','id');
              $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')','r.belegnr','adr.kundennummer','r.name','r.land','p.abkuerzung','r.zahlungsweise','r.status',"FORMAT(r.soll,2{$extended_mysql55})",'r.zahlungsstatus','adr.freifeld1','r.ihrebestellnummer');
        $defaultorder = 12;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;
                                $alignright= array('9');
                                $sumcol= 9;
              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=gutschrift&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=gutschrift&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=gutschrift&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=11;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 
                        r.belegnr,
                                DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, adr.kundennummer as kundennummer,
              ".$this->app->erp->MarkerUseredit("r.name","r.useredittimestamp")." as kunde, r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
              FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                  FROM  gutschrift r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";
              // Fester filter


              $where = " r.id!='' AND r.status='freigegeben' ".$this->app->erp->ProjektRechte();
              
              // gesamt anzahl
              $count = "SELECT COUNT(r.id) FROM gutschrift r";

              $moreinfo = true;
            break;


            case "gutschriften":
              $heading =  array('','Gutschrift','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlweise','Betrag','bezahlt','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','35%','5%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','kunde','land','projekt','zahlungsweise','soll','zahlung','status','id');
              $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')','r.belegnr','adr.kundennummer','r.name','r.land','p.abkuerzung','r.status',"FORMAT(r.soll,2{$extended_mysql55})",'adr.freifeld1','r.ihrebestellnummer');

        $defaultorder = 12;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;
                                $alignright= array('9');
                                $sumcol= 9;
              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=gutschrift&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=gutschrift&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
 "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=gutschrift&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=gutschrift&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=11;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 
                        r.belegnr,
                                DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, adr.kundennummer as kundennummer,
              ".$this->app->erp->MarkerUseredit("r.name","r.useredittimestamp")." as kunde, r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
              FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                  FROM  gutschrift r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";
              // Fester filter


              //if($tmp!="")$tmp .= " AND r.belegnr!='' ";

              $where = " r.status!='angelegt' AND  r.id!='' ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(r.id) FROM gutschrift r WHERE r.status='freigegeben'";

              $moreinfo = true;
/*
              // headings
              $heading =  array('','Vom','Kunde','Gutschrift','Land','Projekt','Zahlung','Betrag','Zahlung','Status','Men&uuml;');
              $width   =  array('1%','1%','40%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','vom','name','land','projekt','zahlungsweise','betrag','zahlung','status','icons','id');
              $searchsql = array('r.id','r.datum','r.belegnr','adr.kundennummer','r.name','r.land','p.abkuerzung','r.zahlungsweise','r.status','r.soll','r.ist','r.zahlungsstatus','r.plz','r.id');

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=gutschrift&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=gutschrift&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=gutschrift&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=/themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, 
              r.name as name, r.belegnr, r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
              r.soll as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                  FROM  gutschrift r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";
              // Fester filter


              $where = " r.id!='' AND r.status='freigegeben' ";

              // gesamt anzahl
              $count = "SELECT COUNT(r.id) FROM gutschrift r WHERE r.status='freigegeben'";

              $moreinfo = true;
*/
            break;





            case "rechnungeninbearbeitung":

              // headings
              $heading =  array('','Rechnung','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Zahlung','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','35%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','kunde','land','projekt','zahlungsweise','soll','zahlung','status','id');
              $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')','r.belegnr','adr.kundennummer','r.name','r.land','p.abkuerzung','r.zahlungsweise','r.status',"FORMAT(r.soll,2{$extended_mysql55})",'r.zahlungsstatus','adr.freifeld1','r.ihrebestellnummer');

        $defaultorder = 12;  //Optional wenn andere Reihenfolge gewuenscht        
                                $defaultorderdesc=1;
                                $alignright= array('9');
                                $sumcol=9;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=rechnung&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=11;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 'ENTWURF' as belegnr, DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, 
              adr.kundennummer, ".$this->app->erp->MarkerUseredit("r.name","r.useredittimestamp")." as kunde, r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
              FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                  FROM  rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";
              // Fester filter


              $where = " ( r.status='angelegt' OR r.belegnr=0) ".$this->app->erp->ProjektRechte();
              
              // gesamt anzahl
              $count = "SELECT COUNT(r.id) FROM rechnung r WHERE ( r.status='angelegt' OR r.belegnr=0) ";

              $moreinfo = true;

            break;
                        case "autorechnungsdruckwarteschlange":
                                $id = $this->app->Secure->GetGET('id');
                                $heading =  array('Datum','Erstellt&nbsp;von','Status','Men&uuml;');
                                $width =                array('15%','55%','15%','15%');
        $findcols = array('datum','erstellt_von','status','id');
                                $searchsql = array('datum','erstellt_von','status','id');
                                $menu = "<a href=\"%value%&action=edit\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>&nbsp;"
                                                         ."<a href=\"%value%&action=pdf\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a>&nbsp;"
                                                         ."<a href=\"#\" onclick=DeleteDialog(\"%value%&action=delete\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>";
                                $sql = "SELECT datum, erstellt_von, gesperrt, id FROM autorechnungsdruck ";
                                $where = " art='0' ";
                                $count = "SELECT COUNT(id) FROM autorechnungsdruck WHERE art='0'";
                                $moreinfo=false;
                        break; 
        
                        case "autorechnungsdruckkunden":
                                $id = $this->app->Secure->GetGET('id');
                                $heading =  array('Datum','Erstellt&nbsp;von','Status','Men&uuml;');
                                $width =                array('15%','55%','15%','15%');
        $findcols = array('datum','erstellt_von','status','id');
                                $searchsql = array('datum','erstellt_von','status','id');
                                $menu = "<a href=\"%value%&action=edit\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>&nbsp;"
                                                         ."<a href=\"%value%&action=pdf\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a>&nbsp;"
                                                         ."<a href=\"#\" onclick=DeleteDialog(\"%value%&action=delete\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>";
                                $sql = "SELECT datum, erstellt_von, gesperrt, id FROM autorechnungsdruck ";
                                $where = " art='0' ";
                                $count = "SELECT COUNT(id) FROM autorechnungsdruck WHERE art='0'";
                                $moreinfo=false;
                        break; 
        
                        case "autorechnungsdruckzentralregulierungen":
                                $id = $this->app->Secure->GetGET('id');
                                $heading =  array('Datum','Verband','Erstellt&nbsp;von','Status','Men&uuml;');
                                $width =                array('15%','40%','25%','5%','15%');
        $findcols = array('datum','gruppe','erstellt_von','status','id');
                                $searchsql = array('datum','gruppe','erstellt_von','status','id');
                                $menu = "<a href=\"%value%&action=edit\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>&nbsp;"
                                                         ."<a href=\"%value%&action=pdf\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a>&nbsp;"
                                                         ."<a href=\"#\" onclick=DeleteDialog(\"%value%&action=delete\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>";
                                $sql = "SELECT datum, gruppe,erstellt_von,gesperrt, id FROM autorechnungsdruck ";
                                $where = " art='0' ";
                                $count = "SELECT COUNT(id) FROM autorechnungsdruck WHERE art='1'";
                                $moreinfo=false;
                        break; 


            case "autorechnungsdruckoffene":
              // headings
              $heading =  array('Rechnung','Vom','Kd-Nr.','Kunde','Land','Projekt','Periode','Betrag','Zahlung','Status','Men&uuml;');
              $width   =  array('10%','10%','10%','35%','1%','1%','10%','1%','1%','1%');
              $findcols = array('belegnr','vom','kundennummer','kunde','land','projekt','autodruck_periode2','soll','zahlung','status','icons','id');
              $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')','r.belegnr','adr.kundennummer','r.name','r.land','p.abkuerzung','r.autodruck_periode2','r.status',"FORMAT(r.soll,2{$extended_mysql55})",'r.zahlungsstatus');

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=rechnung&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=11;
   // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,r.belegnr as belegnr, DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, 
        adr.kundennummer, CONCAT(r.name,'<br>Außendienst: ',IFNULL(r.vertriebid,0),'<br>Provision: ',IFNULL(r.provision,0),'<br>Verband: ',r.gruppe) as kunde, r.land as land, p.abkuerzung as projekt, 
        CONCAT(if(r.autodruck_periode=1,'T',if(r.autodruck_periode=2,'W',if(r.autodruck_periode=4,'14T',if(r.autodruck_periode=5,'M',if(r.autodruck_periode=6,'E','unbekannt'))))),' K:',r.autodruck_anzahlkunde,' V:',r.autodruck_anzahlverband,' ZR:',r.autodruck_rz) as autodruck_periode2,
       FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
      FROM  rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";


              // Fester filter

        $where = " r.id!='' AND r.autodruck_done!='1' AND r.status!='angelegt'  ".$this->app->erp->ProjektRechte();
              
        // gesamt anzahl
        $count = "SELECT COUNT(r.id) FROM rechnung r WHERE r.autodruck_done!='1' AND r.status!='angelegt'";

              $moreinfo = false;
            break;

            case "lastschriftenarchiv":
              // headings
              $heading =  array('Rechnung','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Skonto','Zahlung','Status','Men&uuml;');
              $width   =  array('10%','10%','10%','35%','1%','1%','1%','1%','1%','1%');
              $findcols = array('belegnr','vom','kundennummer','kunde','land','projekt','zahlungsweise','soll','skonto','zahlung','status','id');
              $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')','r.belegnr','adr.kundennummer','r.name','r.land','r.zahlungszielskonto','p.abkuerzung','r.zahlungsweise','r.status',"FORMAT(r.soll,2{$extended_mysql55})",'r.zahlungsstatus','adr.freifeld1');

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

                                $defaultorder=12;
                                $defaultorderdesc=1;

              $menucol=11;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS r.id, r.belegnr as belegnr, DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, 
              adr.kundennummer, ".$this->app->erp->MarkerUseredit("r.name","r.useredittimestamp")." as kunde, r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
             FORMAT(r.soll*(1.0-(r.zahlungszielskonto/100)),2{$extended_mysql55}) as soll, r.zahlungszielskonto as skonto, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                  FROM  rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";
              // Fester filter

              $where = " r.id!='' AND (r.zahlungsweise='lastschrift' OR r.zahlungsweise='einzugsermaechtigung') AND r.zahlungsstatus='abgebucht' ".$this->app->erp->ProjektRechte();
              
              // gesamt anzahl
              $count = "SELECT COUNT(r.id) FROM rechnung r WHERE (r.zahlungsweise='lastschrift' OR r.zahlungsweise='einzugsermaechtigung') AND r.zahlungsstatus='abgebucht'";

            break;

            case "lastschriften":
                                // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#faellig').click( function() { fnFilterColumn1( 0 ); } );");

                        
                                for($r=1;$r<2;$r++)
    {
      $this->app->Tpl->Add(JAVASCRIPT,'
        function fnFilterColumn'.$r.' ( i )
        {
          if(oMoreData'.$r.$name.'==1)
          oMoreData'.$r.$name.' = 0;
          else
          oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
        }
      ');
    }
 
              // headings
              $heading =  array('','Rechnung','Vom','Datum Abbuchung','Kd-Nr.','Kunde','BIC','IBAN','Projekt','Betrag','Skonto','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','10%','35%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','einzugsdatum','kundennummer','kunde','swift','iban','projekt','soll','id');
              $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')','r.belegnr','adr.kundennummer','r.name','r.land','p.abkuerzung','r.status',"FORMAT(r.soll,2{$extended_mysql55})",'r.zahlungsstatus','adr.freifeld1');

                                $defaultorder=10;
                                $defaultorderdesc=1;
        $sumcol=10;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=12;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,CONCAT('<input type=\"checkbox\" name=\"rechnung[]\" ',if(r.einzugsdatum='0000-00-00' OR r.einzugsdatum <= NOW(),'checked',''),' value=\"',r.id,'\">') as open, 
                                        r.belegnr as belegnr, DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, if(r.einzugsdatum!='0000-00-00',DATE_FORMAT(r.einzugsdatum,'%Y-%m-%d'),'-') as einzugsdatum,
              adr.kundennummer, ".$this->app->erp->MarkerUseredit("r.name","r.useredittimestamp")." as kunde, adr.swift, adr.iban, p.abkuerzung,  
             FORMAT(r.soll*(1.0-(r.zahlungszielskonto/100)),2{$extended_mysql55}) as soll, CONCAT(r.zahlungszielskonto,if(g.sonderrabatt_skonto > 0,CONCAT('/',g.sonderrabatt_skonto),'')), r.id
                  FROM  rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id LEFT JOIN gruppen g ON r.gruppe=g.id  ";
              // Fester filter
 
                                $more_data1 = $this->app->Secure->GetGET("more_data1"); 
                                if($more_data1==1) 
                                        $subwhere[] = " ((DATE_ADD(r.datum, INTERVAL r.zahlungszieltage DAY) >= DATE_SUB(NOW(), INTERVAL 10 DAY)) OR (r.einzugsdatum >= DATE_SUB(NOW(), INTERVAL 10 DAY)))  ";

        for($j=0;$j<count($subwhere);$j++)
                $tmp .=  " AND ".$subwhere[$j];

              $where = " r.id!='' AND (r.zahlungsweise='lastschrift' OR r.zahlungsweise='einzugsermaechtigung') AND (r.zahlungsstatus!='bezahlt' AND r.dta_datei <= 0) ".$this->app->erp->ProjektRechte()." ".$tmp;
              
              // gesamt anzahl
              //$count = "SELECT COUNT(r.id) FROM rechnung r WHERE (r.zahlungsweise='lastschrift' OR r.zahlungsweise='einzugsermaechtigung') AND r.zahlungsstatus!='abgebucht' AND r.zahlungsstatus!='bezahlt'";
              $count = "SELECT COUNT(r.id) FROM rechnung r WHERE (r.zahlungsweise='lastschrift' OR r.zahlungsweise='einzugsermaechtigung') AND (r.zahlungsstatus!='bezahlt' AND r.dta_datei <= 0) ";

              $moreinfo = false;
            break;


       case "vertreterumsatzstatistik":
      // headings
        $id = $this->app->Secure->GetGET('id');
        $heading =  array('Kd.-Nr.','Name','PLZ','Ort','Umsatz (netto)','Provision','%','DB in %','Anzahl Belege','Men&uuml;');
        $width   =  array('10%','25%','5%','10%','8%','5%','1%','8%','5%','3%');
        $findcols = array('a.kundennummer','a.name','a.plz','a.ort','SUM(u.umsatz_netto)','SUM(u.provision_summe)','a.provision',"FORMAT(SUM(u.deckungsbeitrag)/COUNT(u.id),2{$extended_mysql55})","COUNT(u.id)",'a.id');
        $searchsql = array('a.name','a.kundennummer','a.verbandsnummer',"DATE_FORMAT(v.datum,'%d.%m.%Y')");

        $defaultorder = 1;  //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc=0;
        $sumcol=6;
        $alignright=array(5,6,7,8,9);

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=adresse&action=belege&id=%value%\" target=\"_blank\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a></td></tr></table>";

        $sql = "SELECT SQL_CALC_FOUND_ROWS u.id, 
        CONCAT('<a href=index.php?module=adresse&action=edit&id=',a.id,' target=_blank>',a.kundennummer,' / ',a.verbandsnummer,'</a>'), a.name, a.plz,a.ort, 
        SUM(u.umsatz_netto),SUM(u.provision_summe),a.provision, FORMAT(SUM(u.deckungsbeitrag)/COUNT(u.id),2), 
        a.id
        FROM belege u LEFT JOIN adresse a ON a.id=u.adresse ";


        $where = " u.vertriebid='".$this->app->User->GetParameter("vertreter_list_id")."' AND u.datum >='".$this->app->User->GetParameter("vertreter_list_von")."'
          AND u.datum <= '".$this->app->User->GetParameter("vertreter_list_bis")."' ";//.$this->app->erp->ProjektRechte();

        $groupby=" GROUP by u.adresse ";

        // gesamt anzahl
        $count = "SELECT COUNT(u.adresse) FROM belege u WHERE u.vertriebid='".$this->app->User->GetParameter("vertreter_list_id")."' 
          AND u.datum >='".$this->app->User->GetParameter("vertreter_list_von")."'
          AND u.datum <= '".$this->app->User->GetParameter("vertreter_list_bis")."' ";
//        ".$this->app->erp->ProjektRechte()." GROUP by u.adresse";


        $moreinfo = false;
      break;


      case "gruppeumsatzstatistiknetto":
       $id = $this->app->Secure->GetGET('id');
        $heading =  array('Datum','Kd.-/Verbands-Nr.','Name','Abteilung','PLZ','Ort','Betrag','Erl&ouml;s','DB%','Men&uuml;');
        $width   =  array('5%','10%','35%','25%','5%','5%','5%','5%','5%');
        $findcols = array('u.datum','a.kundennummer','a.name',"a.abteilung","a.plz","a.ort","u.umsatz_netto","u.erloes_netto","u.deckungsbeitrag",'u.id');
        $searchsql = array("DATE_FORMAT(u.datum,'%d.%m.%Y')",'a.kundennummer','a.name','u.belegnr','u.belegnr',"u.umsatz_netto","a.abteilung","a.ort","a.plz");

        $defaultorder = 1;  //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc=0;
        $sumcol=7;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=adresse&action=belege&id=%value%\" target=\"_blank\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a></td></tr></table>";

        $menucol=4;
        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS u.id,
        DATE_FORMAT(u.datum,'%d.%m.%Y') as datum, CONCAT(a.kundennummer,' / ',a.verbandsnummer), 
        a.name, a.abteilung, a.plz, a.ort, SUM(u.umsatz_netto), SUM(u.erloes_netto), FORMAT(SUM(u.deckungsbeitrag)/COUNT(u.id),2),a.id
        FROM belege u LEFT JOIN adresse a ON a.id=u.adresse ";

        $where = " u.gruppe='".$this->app->User->GetParameter("umsatzstatistik_gruppe")."' AND u.datum >='".$this->app->User->GetParameter("umsatzstatistik_gruppe_von")."'
          AND u.datum <= '".$this->app->User->GetParameter("umsatzstatistik_gruppe_bis")."' ";//.$this->app->erp->ProjektRechte();

        $groupby=" GROUP by u.adresse ";

        // gesamt anzahl
        $count = "SELECT COUNT(u.id) FROM belege u WHERE u.adresse='".$this->app->User->GetParameter("umsatzstatistik_gruppe")."' 
          AND u.datum >='".$this->app->User->GetParameter("umsatzstatistik_gruppe_von")."'
          AND u.datum <= '".$this->app->User->GetParameter("umsatzstatistik_gruppe_bis")."'
        ".$this->app->erp->ProjektRechte();

      break;



        case "umsatzstatistiknetto":
      // headings
        $id = $this->app->Secure->GetGET('id');
        $heading =  array('Datum','Kd.-Nr:','Name','Art','Belege','Betrag','Erl&ouml;s','DB%','Men&uuml;');
        $width   =  array('5%','10%','35%','5%','5%','5%','5%','5%','5%');
        $findcols = array('u.datum','a.kundennummer','u.name','u.objekt','u.belegnr',"u.betrag_$steuer","u.erloes_netto","u.deckungsbeitrag",'u.id');
        $searchsql = array("DATE_FORMAT(u.datum,'%d.%m.%Y')",'a.kundennummer','u.name','u.objekt','u.belegnr',"u.betrag_$steuer");

        $defaultorder = 1;  //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc=0;
                                $sumcol=6;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=%value%\" target=\"_blank\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a></td></tr></table>";

        $menucol=4;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS u.id,
        DATE_FORMAT(u.datum,'%d.%m.%Y') as datum, a.kundennummer, a.name, u.typ, u.belegnr, u.umsatz_netto, u.erloes_netto, u.deckungsbeitrag,CONCAT(u.typ,'&action=edit&id=',u.id)
      FROM belege u LEFT JOIN adresse a ON a.id=u.adresse ";

        $where = " u.adresse='".$this->app->User->GetParameter("umsatzstatistik_kunde")."' AND u.datum >='".$this->app->User->GetParameter("umsatzstatistik_kunde_von")."'
          AND u.datum <= '".$this->app->User->GetParameter("umsatzstatistik_kunde_bis")."' ";//.$this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(u.id) FROM belege u WHERE u.adresse='".$this->app->User->GetParameter("umsatzstatistik_kunde")."' 
          AND u.datum >='".$this->app->User->GetParameter("umsatzstatistik_kunde_von")."' AND u.datum <= '".$this->app->User->GetParameter("umsatzstatistik_kunde_bis")."'
          ".$this->app->erp->ProjektRechte();

      //  $moreinfo = true;
      break;


                        case "zahlungsavis":
      // headings
        $id = $this->app->Secure->GetGET('id');
        $heading =  array('','Datum','Avis','Kunden-Nr.','Kunde','Versendet','Versendet am','Per','Betrag','Men&uuml;');
        $width   =  array('5%','15%','15%','20%','15%','15%','10%','5%','5%');
        $findcols = array('open','z.datum','z.id','a.kundennummer','a.name','z.versendet','z.versendet_am','z.versendet_per','z.betrag','a.email');
        $searchsql = array("DATE_FORMAT(z.datum,'%d.%m.%Y')","CONCAT(DATE_FORMAT(z.datum,'%Y%m%d'),'/',z.id)","DATE_FORMAT(z.versendet_am,'%d.%m.%Y')",'a.name','a.kundennummer',"FORMAT(z.betrag,2{$extended_mysql55})");

                                //defaultorder=6;
                                //defaultorderdesc=1;
                                $alignright = array('9');

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><!--<a href=\"#\" onclick=DeleteDialog(\"index.php?module=zahlungsverkehr&action=deleteavis&id=%value%\")><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/loeschen.png\" border=\"0\"></a>-->&nbsp;<a href=\"index.php?module=zahlungsverkehr&action=downloadavis&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";


//&nbsp;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/loeschen.png\" border=\"0\"></a></td></tr></table>";
        $menucol=4;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS z.id, CONCAT('<input type=\"checkbox\" name=\"avis[]\" ',if(z.versendet!='1','checked',''),' value=\"',z.id,'\">') as open,
                                        DATE_FORMAT(z.datum,'%d.%m.%Y') as datum, CONCAT(DATE_FORMAT(z.datum,'%Y%m%d'),'/',z.id),  a.kundennummer, a.name, if(z.versendet,'ja','-') as versendet2, 
                                        if(z.versendet,DATE_FORMAT(z.datum,'%d.%m.%Y'),'') as versendet_am2, z.versendet_per, FORMAT(z.betrag,2{$extended_mysql55}), z.id
                FROM zahlungsavis z LEFT JOIN adresse a ON a.id=z.adresse ";

        //$where = " (d.art='Lastschrift' OR d.art='lastschrift') AND d.status!='versendet' ".$this->app->erp->ProjektRechte();
                                $where="";

        // gesamt anzahl
        $count = "SELECT COUNT(z.id) FROM zahlungsavis z";

        $moreinfo = false;
      break;

                        case "dta_datei_lastschrift":
      // headings
        $id = $this->app->Secure->GetGET('id');
        $heading =  array('','Datum','Bezeichnung','Berarbeiter','Konto','Status','Summe','Men&uuml;');
        $width   =  array('5%','15%','40%','15%','15%','10%','5%');
        $findcols = array('open','d.datum','d.bezeichnung','d.bearbeiter','k.bezeichnung','d.status','summe','d.id');
        $searchsql = array('g.kennziffer','d.bezeichnung','d.bearbeiter','k.bezeichnung',"DATE_FORMAT(d.datum,'%d.%m.%Y')");

                                $defaultorder=8;
                                $defaultorderdesc=1;


        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"#\" onclick=DeleteDialog(\"index.php?module=zahlungsverkehr&action=deletedtalastschrift&id=%value%\")><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/loeschen.png\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=zahlungsverkehr&action=downloaddtalastschrift&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/download.png\" border=\"0\"></a></td></tr></table>";


//&nbsp;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/loeschen.png\" border=\"0\"></a></td></tr></table>";
        $menucol=7;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS d.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,
                                        DATE_FORMAT(d.datum,'%d.%m.%Y %H:%i:%s') as datum, d.bezeichnung, d.bearbeiter, k.bezeichnung as konto, d.status,
                                (SELECT SUM(dd.betrag) FROM dta dd WHERE dd.datei=d.id ) as summe,d.id 
      FROM dta_datei d LEFT JOIN konten k ON k.id=d.konto ";

        $where = " (d.art='Lastschrift' OR d.art='lastschrift') AND d.status!='versendet' ".$this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(d.id) FROM dta_datei d WHERE d.status!='versendet' AND (d.art='Lastschrift' OR d.art='lastschrift') ".$this->app->erp->ProjektRechte();

        $moreinfo = true;
      break;


                        case "dta_datei_ueberweisung":
      // headings
        $id = $this->app->Secure->GetGET('id');
        $heading =  array('','Datum','Bezeichnung','Berarbeiter','Konto','Status','Summe','Men&uuml;');
        $width   =  array('5%','15%','40%','15%','15%','10%','5%','5%');
        $findcols = array('open','d.datum','d.bezeichnung','d.bearbeiter','k.bezeichnung','d.status','summe','d.id');
        $searchsql = array('d.bezeichnung','d.bearbeiter','k.bezeichnung',"DATE_FORMAT(d.datum,'%d.%m.%Y')");

                                $defaultorder=8;
                                $defaultorderdesc=1;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"#\" onclick=DeleteDialog(\"index.php?module=zahlungsverkehr&action=deletedtaueberweisung&id=%value%\")><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/loeschen.png\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=zahlungsverkehr&action=downloaddta&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/download.png\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=zahlungsverkehr&action=downloaddtastatus&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.png\" border=\"0\"></a></td></tr></table>";


//&nbsp;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/loeschen.png\" border=\"0\"></a></td></tr></table>";
        $menucol=7;
                                $alignright= array('7');

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS d.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,
                                        DATE_FORMAT(d.datum,'%d.%m.%Y %H:%i:%s') as datum, d.bezeichnung, d.bearbeiter, k.bezeichnung as konto, d.status, 
                        (SELECT SUM(dd.betrag) FROM dta dd WHERE dd.datei=d.id ) as summe, d.id 
      FROM dta_datei d LEFT JOIN konten k ON k.id=d.konto ";

        $where = " (d.art='Sammelueberweisung' OR d.art='sammelueberweisung') AND d.status!='versendet' ".$this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(d.id) FROM dta_datei d WHERE d.status!='versendet' AND (d.art='Sammelueberweisung' OR d.art='sammelueberweisung') ".$this->app->erp->ProjektRechte();

        $moreinfo = true;
      break;


            case "rechnungenoffene":
              // headings
              $heading =  array('','Rechnung','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Zahlung','Status','Men&uuml;');
              //$width   =  array('1%','2%','5%','5%','50%','3%','3%','3%','3%','3%','3%','3%');
              $width   =  array('1%','10%','10%','10%','35%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','kunde','land','projekt','zahlungsweise','soll','zahlung','status','id');
              $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')','r.belegnr','adr.kundennummer','r.name','r.land','p.abkuerzung','r.zahlungsweise','r.status',"FORMAT(r.soll,2{$extended_mysql55})",'r.zahlungsstatus','adr.freifeld1','r.ihrebestellnummer');

        $defaultorder = 12;  //Optional wenn andere Reihenfolge gewuenscht                
                                $defaultorderdesc=1;
                                $alignright= array('9');
                                $sumcol = 9;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=rechnung&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=11;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, r.belegnr as belegnr, DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, 
              adr.kundennummer, ".$this->app->erp->MarkerUseredit("r.name","r.useredittimestamp")." as kunde, r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
             FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                  FROM  rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";
              // Fester filter

              $where = " r.id!='' AND r.status='freigegeben' ".$this->app->erp->ProjektRechte();
              
              // gesamt anzahl
              $count = "SELECT COUNT(r.id) FROM rechnung r WHERE r.status='freigegeben'";

              $moreinfo = true;
            break;


            case "rechnungen":
              $this->app->Tpl->Add(JQUERYREADY,"$('#zahlungseingang').click( function() { fnFilterColumn1( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#zahlungseingangfehlt').click( function() { fnFilterColumn2( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#rechnungenheute').click( function() { fnFilterColumn3( 0 ); } );");
        $defaultorder = 12;  //Optional wenn andere Reihenfolge gewuenscht                
                                $defaultorderdesc=1;

                                $alignright= array('9');
                                $sumcol = 9;

                for($r=1;$r<4;$r++)
                {
                  $this->app->Tpl->Add(JAVASCRIPT,'
                                function fnFilterColumn'.$r.' ( i )
                                {
                                        if(oMoreData'.$r.$name.'==1)
                                        oMoreData'.$r.$name.' = 0;
                                        else
                                        oMoreData'.$r.$name.' = 1;

                                        $(\'#'.$name.'\').dataTable().fnFilter( 
                                        \'A\',
                                        i, 
                                        0,0
                                        );
                                }
                        ');
                }


              // headings
              $heading =  array('','Rechnung','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Zahlung','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','35%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','kunde','land','projekt','zahlungsweise','soll','zahlung','status','id');
              $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')','r.belegnr','adr.kundennummer','r.name','r.land','p.abkuerzung','r.zahlungsweise','r.status',"FORMAT(r.soll,2{$extended_mysql55})",'r.zahlungsstatus','adr.freifeld1','r.ihrebestellnummer');



              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=rechnung&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
 "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=rechnung&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=11;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, r.belegnr, DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, 
              adr.kundennummer, ".$this->app->erp->MarkerUseredit("r.name","r.useredittimestamp")." as kunde, r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
              FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                  FROM  rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";
              // Fester filter

              $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " r.zahlungsstatus='bezahlt' ";
        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " r.zahlungsstatus!='bezahlt' ";
        $more_data3 = $this->app->Secure->GetGET("more_data3"); if($more_data3==1) { $subwhere[] = " r.datum=CURDATE() "; $ignore=true; }

        for($j=0;$j<count($subwhere);$j++)
                $tmp .=  " AND ".$subwhere[$j];

        if($tmp!="" && !$ignore)$tmp .= " AND r.belegnr!='' ";

        $where = " r.id!='' AND r.status!='angelegt' $tmp ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(r.id) FROM rechnung r ";

              $moreinfo = true;

            break;



            case "bestellungeninbearbeitung":

              // headings
              $heading =  array('','Bestellung','Vom','Lf-Nr.','Lieferant','Land','Projekt','Betrag','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','40%','1%','1%','1%','1%','1%','1%','1%');
        $findcols = array('open','belegnr','vom','lieferantennummer','lieferant','land','projekt','summe','status','icons','id');
        $searchsql = array('DATE_FORMAT(b.datum,\'%d.%m.%Y\')','b.belegnr','adr.lieferantennummer','b.name','b.land','p.abkuerzung','b.status','b.gesamtsumme');

        $defaultorder = 11;  //Optional wenn andere Reihenfolge gewuenscht                
                                $alignright=array('8');
                                $sumcol= 8;
        $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=bestellung&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=bestellung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";
              $menucol=9;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS b.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 'ENTWURF' as belegnr, DATE_FORMAT(b.datum,'%Y-%m-%d') as vom, adr.lieferantennummer as lieferantennummer,
              ".$this->app->erp->MarkerUseredit("b.name","b.useredittimestamp")." as lieferant,  b.land as land, p.abkuerzung as projekt,  
              FORMAT(b.gesamtsumme,2{$extended_mysql55}) as summe, UPPER(b.status) as status, b.id
                  FROM  bestellung b LEFT JOIN projekt p ON p.id=b.projekt LEFT JOIN adresse adr ON b.adresse=adr.id  ";
              // Fester filter
        $where = " ( b.status='angelegt' OR b.belegnr=0) ".$this->app->erp->ProjektRechte();
              
        // gesamt anzahl
        $count = "SELECT COUNT(b.id) FROM bestellung b WHERE ( b.status='angelegt' OR b.belegnr=0) ";

              $moreinfo = true;

            break;
        

            case "bestellungenoffene":

              // headings
              $heading =  array('','Vom','Lf-Nr.','Lieferant','Land','Projekt','Betrag','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','40%','1%','1%','1%','1%','1%','1%','1%');
        $findcols = array('open','vom','lieferantennummer','lieferant','land','projekt','betrag','status','icons','id');
        $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')','a.belegnr','adr.lieferantennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status','a.gesamtsumme','a.status');

        $defaultorder = 10;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;
              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=bestellung&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=bestellung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=8;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS b.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, DATE_FORMAT(b.datum,'%Y-%m-%d') as vom, adr.lieferantennummer as lieferantennummer,
              ".$this->app->erp->MarkerUseredit("b.name","b.useredittimestamp")." as lieferant,  b.land as land, p.abkuerzung as projekt,  
              b.gesamtsumme as summe, UPPER(b.status) as status, b.id
                  FROM  bestellung b LEFT JOIN projekt p ON p.id=b.projekt LEFT JOIN adresse adr ON b.adresse=adr.id  ";
              // Fester filter

              $where = " b.id!='' ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(b.id) FROM bestellung b";

              $moreinfo = true;

              // gesamt anzahl
              $count = "SELECT COUNT(b.id) FROM bestellung b WHERE b.status='freigegeben'";

              $moreinfo = true;
            break;


            //offene

            case "bestellungen":
                                // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#bestellungenoffen').click( function() { fnFilterColumn1( 0 ); } );");

        $defaultorder = 11;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;
                                $sumcol=8;
                                $alignright=array(8);


    for($r=1;$r<2;$r++)
    {
      $this->app->Tpl->Add(JAVASCRIPT,'
        function fnFilterColumn'.$r.' ( i )
        {
          if(oMoreData'.$r.$name.'==1)
          oMoreData'.$r.$name.' = 0;
          else
          oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
        }
      ');
    }
        // ENDE EXTRA checkboxen

              // headings
              $heading =  array('','Bestellung','Vom','Lf-Nr.','Lieferant','Land','Projekt','Betrag','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','10%','30%','1%','1%','1%','1%','1%');
        $findcols = array('open','belegnr','vom','lieferantennummer','lieferant','land','projekt','summe','status','icons','id');
        $searchsql = array('DATE_FORMAT(b.datum,\'%d.%m.%Y\')','b.belegnr','adr.lieferantennummer','b.name','b.land','p.abkuerzung','b.status','b.gesamtsumme');

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=bestellung&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
 "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=bestellung&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=bestellung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=9;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS b.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 
                                if(b.status='storniert',CONCAT(b.belegnr),b.belegnr) as belegnr, 
                                if(b.status='storniert',CONCAT(DATE_FORMAT(b.datum,'%Y-%m-%d')),DATE_FORMAT(b.datum,'%Y-%m-%d')) as vom, 
              if(b.status='storniert',CONCAT(adr.lieferantennummer),adr.lieferantennummer) as lieferantennummer,  
              if(b.status='storniert',CONCAT(".$this->app->erp->MarkerUseredit("b.name","b.useredittimestamp")."),".$this->app->erp->MarkerUseredit("b.name","b.useredittimestamp").") as lieferant,  
                                if(b.status='storniert',CONCAT(b.land),b.land) as land, 
                                if(b.status='storniert',CONCAT(p.abkuerzung),p.abkuerzung) as projekt,
              if(b.status='storniert',CONCAT(FORMAT(b.gesamtsumme,2{$extended_mysql55})),FORMAT(b.gesamtsumme,2{$extended_mysql55})) as summe, 
                                if(b.status='storniert',CONCAT('<font color=red>',UPPER(b.status),'</font>'),UPPER(b.status)) as status, b.id
                  FROM  bestellung b LEFT JOIN projekt p ON p.id=b.projekt LEFT JOIN adresse adr ON b.adresse=adr.id  ";
              // Fester filter

                                //FORMAT(b.gesamtsumme,2,'de_DE')

                        
        // START EXTRA more
                                // TODO: status abgeschlossen muss noch umgesetzt werden
        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " b.status!='abgeschlossen' ";
                                for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];
        // START EXTRA more

              $where = " b.id!='' AND b.status!='angelegt' $tmp ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(b.id) FROM bestellung b WHERE b.status!='angelegt'";

              $moreinfo = true;

            break;



                        case "vertreter":
      // headings
        $id = $this->app->Secure->GetGET('id');
        $heading =  array('','Kennziffer','Name','Men&uuml;');
        $width   =  array('1%','5%','90%','5%');
        $findcols = array('open','g.kennziffer','g.name','g.id');
        $searchsql = array('g.kennziffer','g.name');

        //$defaultorder = 6;  //Optional wenn andere Reihenfolge gewuenscht
        //$defaultorderdesc=1;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=versanderzeugen&action=einzel&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a></td></tr></table>";


//&nbsp;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/loeschen.png\" border=\"0\"></a></td></tr></table>";
        $menucol=6;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS g.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 
                                        g.kennziffer, g.name, g.id 
      FROM  gruppen g ";

        $where = " g.art='verband' ".$this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(g.id) FROM gruppen g WHERE g.art='verband' ".$this->app->erp->ProjektRechte();

        $moreinfo = false;

      break;


                        case "dta_verband_versenden":
      // headings
        $id = $this->app->Secure->GetGET('id');
        $heading =  array('Datum','Kennziffer','Verband','Men&uuml;');
        $width   =  array('5%','5%','70%','5%');
        $findcols = array('d.datum','g.kennziffer','g.name','g.id');
        $searchsql = array('g.kennziffer','g.name',"DATE_FORMAT(d.datum,'%d.%m.%Y')");

                                $defaultorder=4;
                                $defaultorderdesc=1;

        //$defaultorder = 0;  //Optional wenn andere Reihenfolge gewuenscht
        //$defaultorderdesc=1;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"#\" onclick=DeleteDialog(\"index.php?module=verband&action=delete&id=%value%\")><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/loeschen.png\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=verband&action=versenden&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/mail.png\" border=\"0\"></a></td></tr></table>";


//&nbsp;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/loeschen.png\" border=\"0\"></a></td></tr></table>";
        $menucol=4;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS g.id,
                                        DATE_FORMAT(d.datum,'%d.%m.%Y') as datum, g.kennziffer, g.name, d.id 
      FROM  dta_datei_verband d LEFT JOIN gruppen g ON g.id=d.verband";

        $where = " d.status!='versendet' ".$this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(d.id) FROM  dta_datei_verband d WHERE d.status!='versendet' ".$this->app->erp->ProjektRechte();

        $moreinfo = true;
      break;


                        case "dta_verband_archiv":
                        // headings
        $id = $this->app->Secure->GetGET('id');
        $heading =  array('Datum','Kennziffer','Verband','versendet&nbsp;am','Men&uuml;');
        $width   =  array('5%','5%','70%','5%','5%');
        $findcols = array('d.datum','g.kennziffer','g.name','d.datum_versendet','g.id');
        $searchsql = array('g.kennziffer','g.name',"DATE_FORMAT(d.datum,'%d.%m.%Y')","DATE_FORMAT(d.datum_versendet,'%d.%m.%Y')");

        $defaultorder = 5;  //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc=1;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=verband&action=datei&id=%value%\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/disk.png\" border=\"0\"></a>&nbsp;<a href=\"index.php?module=verband&action=versenden&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/mail.png\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=verband&action=delete&id=%value%\")><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/loeschen.png\" border=\"0\"></a></td></tr></table>";


//&nbsp;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/loeschen.png\" border=\"0\"></a></td></tr></table>";
        $menucol=4;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS g.id,
                                        DATE_FORMAT(d.datum,'%d.%m.%Y') as datum, g.kennziffer, g.name, DATE_FORMAT(d.datum_versendet,'%d.%m.%Y') as versendet_am, d.id 
      FROM  dta_datei_verband d LEFT JOIN gruppen g ON g.id=d.verband";

        $where = " d.status='versendet' ".$this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(d.id) FROM  dta_datei_verband d WHERE d.status='versendet' ".$this->app->erp->ProjektRechte();
      break;

                        case "wareneingang_kunde":
      // headings
        $id = $this->app->Secure->GetGET('id');
                    $adresse= $this->app->DB->Select("SELECT adresse FROM paketannahme WHERE id='$id' LIMIT 1");

        $heading =  array('Nummer','Beschreibung','Projekt','Menge','Geliefert','Lieferschein','Datum','Aktion');
        $width   =  array('5%','30%','5%','5%','5%','5%','5%','5%');
        $findcols = array('lp.nummer','lp.beschreibung','p.abkuerzung','lp.menge','lp.geliefert','l.belegnr','l.datum','lp.id');
        $searchsql = array('lp.nummer','lp.beschreibung','p.abkuerzung','lp.menge','lp.geliefert','l.belegnr',"DATE_FORMAT(l.datum,'%d.%m.%Y')",'lp.id');

        //$defaultorder = 6;  //Optional wenn andere Reihenfolge gewuenscht
        //$defaultorderdesc=1;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><form style=\"padding: 0px; margin: 0px;\" action=\"\" method=\"post\" name=\"eprooform\">Menge:&nbsp;<input type=\"text\" size=\"1\" name=\"pos[%value%]\">&nbsp;<input type=\"submit\" value=\"zuordnen\" name=\"submitkunde\"></form></td></tr></table>";


//&nbsp;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/loeschen.png\" border=\"0\"></a></td></tr></table>";
        $menucol=4;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS lp.id,lp.nummer, CONCAT(LEFT(lp.bezeichnung,20),'...') as beschreibung, p.abkuerzung as projekt, 
      lp.menge, lp.geliefert, l.belegnr as lieferschein, DATE_FORMAT(l.datum,'%d.%m.%Y') as datum, lp.id FROM lieferschein_position lp
      LEFT JOIN lieferschein l ON lp.lieferschein=l.id LEFT JOIN projekt p ON lp.projekt=p.id ";


        $where = " l.adresse='$adresse' AND (l.status='versendet' OR l.status='freigegeben') ".$this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(lp.id) FROM lieferschein_position lp
                LEFT JOIN lieferschein l ON lp.lieferschein=l.id LEFT JOIN projekt p ON lp.projekt=p.id ".$this->app->erp->ProjektRechte();

        $moreinfo = false;
      break;



                        case "wareneingang_lieferant":
      // headings
        $id = $this->app->Secure->GetGET('id');
                    $adresse= $this->app->DB->Select("SELECT adresse FROM paketannahme WHERE id='$id' LIMIT 1");

        $heading =  array('Bestellnummer','Nummer','Bestellung','Beschreibung','Lieferdatum','Projekt','Menge','Geliefert','Offen','Aktion');
        $width   =  array('5%','5%','5%','30%','5%','5%','5%','5%','5%','5%');
        $findcols = array('bp.bestellnummer','art.nummer','b.belegnr','art.name_de','bp.lieferdatum','p.abkuerzung','bp.menge','bp.geliefert','offen','bp.id');
        $searchsql = array('bp.bestellnummer','art.nummer','b.belegnr','art.name_de','bp.lieferdatum','p.abkuerzung','bp.menge','bp.geliefert');

        //$defaultorder = 6;  //Optional wenn andere Reihenfolge gewuenscht
        //$defaultorderdesc=1;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><form style=\"padding: 0px; margin: 0px;\" action=\"\" method=\"post\" name=\"eprooform\">Menge:&nbsp;<input type=\"text\" size=\"1\" name=\"pos[%value%]\">&nbsp;<input type=\"submit\" value=\"zuordnen\" name=\"submit\"></form></td></tr></table>";


//&nbsp;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/loeschen.png\" border=\"0\"></a></td></tr></table>";
        $menucol=4;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS bp.id, bp.bestellnummer, art.nummer, b.belegnr as `Bestellung`, CONCAT(LEFT(art.name_de,40),'<br>Bei Lieferant: ',LEFT(bp.bezeichnunglieferant,40)) as beschreibung, if(bp.lieferdatum,DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum, p.abkuerzung as projekt, 
      bp.menge, bp.geliefert, bp.menge -  bp.geliefert as offen, bp.id FROM bestellung_position bp
      LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN artikel art ON art.id=bp.artikel LEFT JOIN projekt p ON b.projekt=p.id ";


        $where = " b.adresse='$adresse' AND b.belegnr > 0 
                AND bp.geliefert < bp.menge AND (bp.abgeschlossen IS NULL OR bp.abgeschlossen=0)  AND (b.status='versendet' OR b.status='freigegeben') ".$this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "
SELECT COUNT(bp.id) FROM bestellung_position bp LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN artikel art ON art.id=bp.artikel LEFT JOIN projekt p ON bp.projekt=p.id WHERE b.adresse='$adresse' AND b.belegnr > 0 AND bp.geliefert < bp.menge AND (bp.abgeschlossen IS NULL OR bp.abgeschlossen=0) AND (b.status='versendet' OR b.status='freigegeben') ".$this->app->erp->ProjektRechte();

        $moreinfo = false;
      break;




                        case "verbaende":
      // headings
        $id = $this->app->Secure->GetGET('id');
        $heading =  array('','Kennziffer','Name','ZR','Men&uuml;');
        $width   =  array('1%','5%','90%','5%','5%');
        $findcols = array('open','g.kennziffer','g.name','zr','g.id');
        $searchsql = array('g.kennziffer','g.name');

                                $defaultorder=5;
                                $defaultorderdesc=1;

        //$defaultorder = 1;  //Optional wenn andere Reihenfolge gewuenscht
        //$defaultorderdesc=1;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=gruppen&action=edit&id=%value%\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=VerbandAbrechnen(\"index.php?module=verband&action=starten&id=%value%\")><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.png\" border=\"0\"></a></td></tr></table>";


//&nbsp;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/loeschen.png\" border=\"0\"></a></td></tr></table>";
        $menucol=4;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS g.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 
                                        g.kennziffer, g.name, if(g.zentralregulierung,'ZR','-') as zr, g.id 
      FROM  gruppen g ";

        $where = " g.art='verband' ".$this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(g.id) FROM gruppen g WHERE g.art='verband' ".$this->app->erp->ProjektRechte();

        $moreinfo = true;
      break;




                        case "versandfertig":
      // headings
        $id = $this->app->Secure->GetGET('id');
        $heading =  array('Lieferschein','Auftrag','Datum','Lieferung','Projekt','Versandart','Tracking','Men&uuml;');
        $width   =  array('1%','5%','5%','50%','10%','5%','5%');
        $findcols = array('l.belegnr','auf.belegnr','l.datum','l.name','p.abkuerzung','v.versandart','v.id');
         $searchsql = array('DATE_FORMAT(l.datum,\'%d.%m.%Y\')','l.belegnr','auf.belegnr','l.datum','l.name','p.abkuerzung','v.versandart');

        $defaultorder = 1;  //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc=1;

        $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=versanderzeugen&action=einzel&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.png\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=versanderzeugen&action=delete&id=%value%\")><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/loeschen.png\" border=\"0\"></a></td></tr></table>";


//&nbsp;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/loeschen.png\" border=\"0\"></a></td></tr></table>";
        $menucol=6;

        // SQL statement
        $sql = "SELECT SQL_CALC_FOUND_ROWS v.id, l.belegnr as lieferschein,auf.belegnr as auftrag, 
        DATE_FORMAT(v.versendet_am,'%d.%m.%Y') as datum, l.name as lieferung, p.abkuerzung, v.versandart, v.tracking, v.id
      FROM  versand v LEFT JOIN lieferschein l ON v.lieferschein=l.id LEFT JOIN adresse adr ON v.adresse=adr.id  
      LEFT JOIN auftrag auf ON auf.id=l.auftragid LEFT JOIN projekt p ON p.id=v.id ";

        $where = " v.abgeschlossen='1' ".$this->app->erp->ProjektRechte();

        // gesamt anzahl
        $count = "SELECT COUNT(v.id) FROM versand v WHERE v.abgeschlossen='1' ".$this->app->erp->ProjektRechte();

        $moreinfo = false;

      break;



                        case "versandoffene":
                        // headings
                                $id = $this->app->Secure->GetGET('id');
              $heading =  array('Lieferschein','Auftrag','Datum','Lieferung','Projekt','Versandart','Men&uuml;');
              $width   =  array('1%','5%','5%','50%','10%','5%','5%');
              $findcols = array('l.belegnr','auf.belegnr','l.datum','l.name','p.abkuerzung','v.versandart','v.id');
                                $searchsql = array('DATE_FORMAT(l.datum,\'%d.%m.%Y\')','l.belegnr','auf.belegnr','l.datum','l.name','p.abkuerzung','v.versandart');

              $defaultorder = 1;  //Optional wenn andere Reihenfolge gewuenscht
                                $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=versanderzeugen&action=einzel&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.png\" border=\"0\"></a>&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=versanderzeugen&action=delete&id=%value%\")><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/loeschen.png\" border=\"0\"></a></td></tr></table>";


//&nbsp;<a href=\"#\" onclick=\"if(!confirm('Auftrag wirklich aus dem Versand nehmen?')) return false; else window.location.href='index.php?module=versanderzeugen&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/loeschen.png\" border=\"0\"></a></td></tr></table>";
              $menucol=6;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS v.id, l.belegnr as lieferschein,auf.belegnr as auftrag, 
                                DATE_FORMAT(l.datum,'%d.%m.%Y') as datum, l.name as lieferung, p.abkuerzung, v.versandart, v.id
                  FROM  versand v LEFT JOIN lieferschein l ON v.lieferschein=l.id LEFT JOIN adresse adr ON v.adresse=adr.id  
                        LEFT JOIN auftrag auf ON auf.id=l.auftragid LEFT JOIN projekt p ON p.id=v.projekt ";
              
              $where = " v.abgeschlossen!='1' ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(v.id) FROM versand v WHERE v.abgeschlossen!='1' ".$this->app->erp->ProjektRechte();

              $moreinfo = false;
                        break; 


                        case "adresse_angebot":
                        // headings
                                $id = $this->app->Secure->GetGET('id');
              $heading =  array('Angebot','Vom','Anfrage','Projekt','Zahlung','Betrag','Status','Men&uuml;');
              $width   =  array('1%','10%','40%','50%','5%','1%','1%','1%');
              $findcols = array('belegnr','vom','name','projekt','zahlungsweise','betrag','status','id');
              $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')','a.belegnr','a.anfrage','a.status','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status',"FORMAT(a.gesamtsumme,2{$extended_mysql55})");
              $defaultorder = 8;  //Optional wenn andere Reihenfolge gewuenscht
                                $defaultorderdesc=1;
                                $sumcol = 6;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=angebot&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=angebot&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";
              $menucol=9;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,
                        if(belegnr='','ENTWURF',belegnr) as belegnr, DATE_FORMAT(a.datum,'%Y-%m-%d') as vom, 
                        a.anfrage as name, 
                                LEFT(UPPER( p.abkuerzung),10) as projekt, a.zahlungsweise as zahlungsweise,  
              FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag, a.status as status,  a.id
                  FROM  angebot a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              
              $where = " a.adresse='$id' AND  a.id!='' ".$this->app->erp->ProjektRechte();
              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM angebot a WHERE a.belegnr!=0 AND a.adresse='$id' ";

              $moreinfo = false;
                        break; 


                        case "adresse_auftrag":
                        // headings
                                $id = $this->app->Secure->GetGET('id');
              $heading =  array('Auftrag','Vom','Kommission/Bestellnummer','Projekt','Zahlung','Betrag','Status','Monitor','Men&uuml;');
              $width   =  array('1%','10%','40%','50%','5%','1%','1%','1%','1%');
              $findcols = array('belegnr','vom','name','projekt','zahlungsweise','betrag','status','status','id');
              $searchsql = array('a.datum','a.belegnr','a.ihrebestellnummer','internet','adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status','a.gesamtsumme');
                                 $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')','a.belegnr','a.ihrebestellnummer','internet','a.status','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status',"FORMAT(a.gesamtsumme,2{$extended_mysql55})");
              $defaultorder = 9;  //Optional wenn andere Reihenfolge gewuenscht
                                $defaultorderdesc=1;
                                $sumcol = 6;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=auftrag&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=auftrag&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";
              $menucol=9;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,
                        if(belegnr='','ENTWURF',belegnr) as belegnr, DATE_FORMAT(a.datum,'%Y-%m-%d') as vom, 
                        a.ihrebestellnummer as name, 
                                LEFT(UPPER( p.abkuerzung),10) as projekt, a.zahlungsweise as zahlungsweise,  
              FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag, a.status as status, (".$this->IconsSQL().")  as icons, a.id
                  FROM  auftrag a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              
              $where = " a.adresse='$id' AND  a.id!='' ".$this->app->erp->ProjektRechte();
              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM auftrag a WHERE a.belegnr!=0 AND a.lager_ok='1' AND a.vorkasse_ok='1' AND a.check_ok='1' AND a.inbearbeitung=0 AND a.nachlieferung!='1' AND a.ust_ok='1' AND a.adresse='$id' ";

              $moreinfo = false;
                        break; 


                        case "adresse_rechnung":
                        // headings
                                $id = $this->app->Secure->GetGET('id');
              $heading =  array('Rechnung','Vom','Kommission/Internetnummer','Projekt','Zahlung','Betrag','Zahlungsstatus','Status','Men&uuml;');
              $width   =  array('1%','10%','40%','5%','5%','1%','1%','1%','1%');
              $findcols = array('r.belegnr','r.datum','a.ihrebestellnummer','r.projekt','r.zahlungsweise','r.soll','r.zahlungsstatus','r.status','r.id');
                                $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')','r.belegnr','a.ihrebestellnummer','r.status','r.name','r.land','p.abkuerzung','r.zahlungsweise','r.status',"FORMAT(r.ist,2{$extended_mysql55})",
                "FORMAT(r.soll,2{$extended_mysql55})",'r.zahlungsstatus',"if(r.zahlungsstatus='offen',
        if(DATEDIFF(NOW(),DATE_ADD(r.datum, INTERVAL r.zahlungszieltage day)) > 0,
        CONCAT('<font color=red>',upper(substring(r.mahnwesen,1,1)),lower(substring(r.mahnwesen,2)),'</font>'),
        'offen')

      ,if(r.zahlungsstatus='','offen',r.zahlungsstatus))");

                                $defaultorder=9;
                                $defaultorderdesc=1;
                                $sumcol=6;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";
              $menucol=1;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS r.id,
                        if(r.belegnr='','ENTWURF',r.belegnr) as belegnr, 
                        
                                CONCAT(DATE_FORMAT(r.datum,'%Y-%m-%d'),' ',if(r.zahlungsstatus='offen',
                                if(DATE_ADD(r.datum, INTERVAL r.zahlungszieltage day) >= NOW(),CONCAT('<br><font color=blue>f&auml;llig in ',DATEDIFF(DATE_ADD(r.datum, INTERVAL r.zahlungszieltage day),NOW()),' Tagen</font>'),CONCAT('<br><font color=red>f&auml;llig seit ',DATEDIFF(NOW(),DATE_ADD(r.datum, INTERVAL r.zahlungszieltage day)),' Tagen</font>'))
                                ,'')) as vom, 

                        a.ihrebestellnummer, 
                                LEFT(UPPER( p.abkuerzung),10) as projekt, r.zahlungsweise as zahlungsweise,  
              FORMAT(r.soll,2{$extended_mysql55}) as betrag, if(r.zahlungsstatus='offen',
                                if(DATEDIFF(NOW(),DATE_ADD(r.datum, INTERVAL r.zahlungszieltage day)) > 0,
                                CONCAT('<font color=red>',upper(substring(r.mahnwesen,1,1)),lower(substring(r.mahnwesen,2)),'</font>'),
                                'offen')

                        ,if(r.zahlungsstatus='','offen',r.zahlungsstatus)) as zahlungsstatus, r.status, r.id
                  FROM  rechnung r LEFT JOIN auftrag a ON r.auftragid=a.id LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";

              
              $where = " r.adresse='$id' AND  r.id!='' ".$this->app->erp->ProjektRechte();
              // gesamt anzahl
              $count = "SELECT COUNT(r.id) FROM rechnung r WHERE r.adresse='$id' ";

              $moreinfo = false;
                        break; 

                        case "adresse_gutschrift":
                        // headings
                                $id = $this->app->Secure->GetGET('id');
              $heading =  array('Gutschrift','Vom','Projekt','Zahlung','Betrag','Zahlungsstatus','Status','Men&uuml;');
              $width   =  array('1%','10%','5%','5%','1%','1%','1%','1%');
              $findcols = array('g.belegnr','g.datum','g.projekt','g.zahlungsweise','g.soll','g.zahlungsstatus','g.status','g.id');
                                $searchsql = array("DATE_FORMAT(g.datum,'%d.%m.%Y')",'g.belegnr','g.status','g.name','g.land','p.abkuerzung','g.zahlungsweise','g.status',"FORMAT(g.ist,2{$extended_mysql55})",
                "FORMAT(g.soll,2{$extended_mysql55})");

      $defaultorder=8;
        $defaultorderdesc=1;
                                $defaultsum = 5;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=gutschrift&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=gutschrift&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";
              $menucol=1;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS g.id,
                        if(g.belegnr='','ENTWURF',g.belegnr) as belegnr, DATE_FORMAT(g.datum,'%Y-%m-%d') as vom, 
                                LEFT(UPPER( p.abkuerzung),10) as projekt, g.zahlungsweise as zahlungsweise,  
              FORMAT(g.soll,2{$extended_mysql55}) as betrag, g.zahlungsstatus as zahlungsstatus, g.status, g.id
                  FROM  gutschrift g LEFT JOIN projekt p ON p.id=g.projekt LEFT JOIN adresse adr ON g.adresse=adr.id  ";
              
              $where = " g.adresse='$id' AND  g.id!='' ".$this->app->erp->ProjektRechte();
              // gesamt anzahl
              $count = "SELECT COUNT(g.id) FROM gutschrift g WHERE g.adresse='$id' ";

              $moreinfo = false;
                        break; 


                        case "adresse_lieferschein":
                        // headings
                                $id = $this->app->Secure->GetGET('id');
              $heading =  array('Lieferschein','Auftrag','Kommission/Bestellnummer','Vom','Projekt','Versandart','Tracking','Status','Men&uuml;');
              $width   =  array('5%','5%','30%','10%','5%','10%','1%','1%');
              $findcols = array('l.belegnr','a.belegnr','a.ihrebestellnummer','l.datum','l.projekt','l.versandart','v.tracking','l.status','l.id');
                                $searchsql = array("DATE_FORMAT(l.datum,'%d.%m.%Y')",'a.belegnr','a.ihrebestellnummer','l.belegnr','a.ihrebestellnummer','l.status','v.tracking','l.name','l.land','p.abkuerzung','l.versandart','l.status');
      
                        $defaultorder=9;
        $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=lieferschein&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=lieferschein&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";
              $menucol=1;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS l.id,
                        if(l.belegnr='','ENTWURF',l.belegnr) as belegnr, a.belegnr, a.ihrebestellnummer, DATE_FORMAT(l.datum,'%Y-%m-%d') as vom, 
                                LEFT(UPPER( p.abkuerzung),10) as projekt, l.versandart, if(v.tracking,v.tracking,'-'), l.status, l.id
                  FROM  lieferschein l LEFT JOIN projekt p ON p.id=l.projekt LEFT JOIN adresse adr ON l.adresse=adr.id 
                        LEFT JOIN auftrag a ON l.auftragid=a.id LEFT JOIN versand v ON v.lieferschein=l.id ";
              
              $where = " l.adresse='$id' AND  l.id!='' ".$this->app->erp->ProjektRechte();
              // gesamt anzahl
              $count = "SELECT COUNT(l.id) FROM lieferschein l WHERE l.adresse='$id' ";

              $moreinfo = false;
                        break; 



            case "angeboteinbearbeitung":
 // headings
              $heading =  array('','Angebot','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Status','Men&uuml;');
              $width   =  array('1%','1%','10%','10%','40%','5%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','name','land','projekt','zahlungsweise','betrag','status','id');
              $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')','a.belegnr','adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status',"FORMAT(a.gesamtsumme,2{$extended_mysql55})",'a.status','adr.freifeld1');

                                $defaultorder=11;
                                $defaultorderdesc=1;
                                $sumcol=9;
                                $alignright= array('9');

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=angebot&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=angebot&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=angebot&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 'ENTWURF' as belegnr, DATE_FORMAT(a.datum,'%Y-%m-%d') as vom, 
              adr.kundennummer as kundennummer, ".$this->app->erp->MarkerUseredit("a.name","a.useredittimestamp")." as name, a.land as land, p.abkuerzung as projekt, a.zahlungsweise as zahlungsweise,  
              FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag, UPPER(a.status) as status, a.id
                  FROM  angebot a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              

              $where = " ( a.status='angelegt' OR a.belegnr=0) ".$this->app->erp->ProjektRechte();
              
              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM angebot a WHERE ( a.status='angelegt' OR a.belegnr=0) ";

              $moreinfo = true;

            break;
        

            case "angeboteoffene":
 // headings
              $heading =  array('','Angebot','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Status','Men&uuml;');
              $width   =  array('1%','1%','10%','10%','40%','5%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','name','land','projekt','zahlungsweise','betrag','status','id');
              $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')','a.belegnr','adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status',"FORMAT(a.gesamtsumme,2{$extended_mysql55})",'a.status','adr.freifeld1');

                        $defaultorder =11;  //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc=1;

                                $alignright= array('9');
                                $sumcol=9;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=angebot&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=angebot&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=angebot&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, a.belegnr as belegnr, DATE_FORMAT(a.datum,'%Y-%m-%d') as vom, 
              adr.kundennummer as kundennummer, ".$this->app->erp->MarkerUseredit("a.name","a.useredittimestamp")." as name, a.land as land, p.abkuerzung as projekt, a.zahlungsweise as zahlungsweise,  
              FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag, UPPER(a.status) as status, a.id
                  FROM  angebot a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              

                $where = " a.id!='' AND a.status='freigegeben' ".$this->app->erp->ProjektRechte();
              
                // gesamt anzahl
                $count = "SELECT COUNT(a.id) FROM angebot a WHERE a.status='freigegeben'";

              $moreinfo = true;
            break;

            case "aufgabenwdh":

              // headings
              $heading =  array('Aufgabe','Mitarbeiter','Projekt','Art','Status','Men&uuml;');
              $width   =  array('35%','20%','5%','1%','1%','1%','1%');
              $findcols = array('aufgabe','mitarbeiter','projekt','art','status','id');
              $searchsql = array('a.aufgabe','adr.name','p.abkuerzung','a.status','a.id');


              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=aufgaben&action=editwdh&id=%value%#tabs-3\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=aufgabe&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=angebot&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=9;
              // SQL statement
              $sql = "SELECT a.id, a.aufgabe,adr.name as mitarbeiter,p.abkuerzung as projekt, 'art' as art, a.status as status, a.id
                                FROM  aufgabe a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              // Fester filter

        // START EXTRA more

        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " a.status='offen' ";


        for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];

              $where = " (a.adresse='".$this->app->User->GetAdresse()."' OR a.initiator='".$this->app->User->GetAdresse()."') AND a.startdatum!='0000-00-00' AND a.id!='' $tmp";

              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM aufgabe a";


            break;



            case "abrechnungzeit":

                                $id = $this->app->Secure->GetGET('id');
                                // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#archiviert').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add(JQUERYREADY,"$('#abrechnung').click( function() { fnFilterColumn2( 0 ); } );");

                for($r=1;$r<3;$r++)
                {
                $this->app->Tpl->Add(JAVASCRIPT,'
                function fnFilterColumn'.$r.' ( i )
                {
                if(oMoreData'.$r.$name.'==1)
                        oMoreData'.$r.$name.' = 0;
                else
                        oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
                }
        ');
         }
        // ENDE EXTRA checkboxen

              // headings
              $heading =  array('','Auswahl','Aufgabe','Mitarbeiter','Von','Bis','Stunden','Status','Men&uuml;');
              $width   =  array('1%','1%','25%','15%','15%','25%','1%','1%','1%');
              $findcols = array('open','auswahl','aufgabe','name','von','bis','status','id');
              $searchsql = array('aufgabe','name','von','bis','a.id');

                                $defaultorder=5;
                                $defaultorderdesc=1;

                                $id = $this->app->Secure->GetGET("id");

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=zeiterfassung&action=create&id=%value%&back=adresse&back_id=$id#tabs-3\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=FinalDialog(\"index.php?module=adresse&action=abrechnungzeitabgeschlossen&id=$id&sid=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/versand.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse&action=abrechnungzeitdelete&id=$id&sid=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "</td></tr></table>";

//            $menucol=9;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS z.id, 
'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,
CONCAT('<input type=\"checkbox\" name=\"zeit[]\" value=\"',z.id,'\" ',if(z.abrechnen,if(z.abgerechnet!=1 AND z.abrechnen='1','checked',''),''),'>') as auswahl,
z.aufgabe as aufgabe, a.name as name, z.von as von, z.bis as bis, 
         CONCAT(LPAD(HOUR(TIMEDIFF(bis, von)),2,'0'),':',LPAD(MINUTE(TIMEDIFF(bis, von)),2,'0')) AS dauer


,if(z.abrechnen,if(z.abgerechnet!=1 AND z.abrechnen='1','offen','abgerechnet'),'abgeschlossen') as staus,z.id  as id
                                FROM zeiterfassung z LEFT JOIN adresse a ON a.id=z.adresse ";
              // Fester filter

        // START EXTRA more

//        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " z.abrechnen='1' "; 
        $more_data1 = $this->app->Secure->GetGET("more_data1"); 
                                        if($more_data1==1)   { $subwhere[] = " (z.abgerechnet='1' OR z.abrechnen!='1') ";  }
                                        else $subwhere[] = " z.abgerechnet!=1 AND z.abrechnen='1' ";



        for($j=0;$j<count($subwhere);$j++)
         $tmp .=  " AND ".$subwhere[$j];

              $where = " z.adresse_abrechnung='".$id."' $tmp";

                $count = "SELECT COUNT(z.id) FROM zeiterfassung z WHERE  z.adresse_abrechnung='".$id."' $tmp";
              // gesamt anzahl

                        $menucol = 8;
                        $moreinfo = true;


            break;



            case "aufgaben":

                                // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#aufgabenoffen').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add(JQUERYREADY,"$('#aufgabenoffeneigene').click( function() { fnFilterColumn2( 0 ); } );");

                for($r=1;$r<3;$r++)
                {
                $this->app->Tpl->Add(JAVASCRIPT,'
                function fnFilterColumn'.$r.' ( i )
                {
                if(oMoreData'.$r.$name.'==1)
                        oMoreData'.$r.$name.' = 0;
                else
                        oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
                }
        ');
         }
        // ENDE EXTRA checkboxen

              // headings
              $heading =  array('Aufgabe','Mitarbeiter','Projekt','Prio','Abgabe-Termin','Startseite','Status','Men&uuml;');
              $width   =  array('35%','20%','1%','1%','1%','1%','1%');
              $findcols = array('aufgabe','mitarbeiter','projekt','prio','abgabe','status','id');
              $searchsql = array('a.aufgabe','p.abkuerzung','adr.name','a.status','a.abgabe_bis','a.id');


              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=aufgaben&action=edit&id=%value%#tabs-3\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=FinalDialog(\"index.php?module=aufgaben&action=abschluss&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/versand.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=aufgaben&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "</td></tr></table>";

//            $menucol=9;
              // SQL statement
              $sql = "SELECT a.id, 
                                        if(a.prio,CONCAT('<b><font color=red>',a.aufgabe,'</font></b>'),a.aufgabe) as aufgabe,
                                        adr.name as mitarbeiter,
                                p.abkuerzung as projekt,
                                if(a.prio,'<b><font color=red>Prio</font></b>','Normal') as prio, 
                                if(a.abgabe_bis,DATE_FORMAT(abgabe_bis,'%d.%m.%Y'),'') as abgabe,
                                if(a.startseite,'Ja','Nein') as startseite,if((angelegt_am AND a.status='offen'), CONCAT(a.status,' (',DATE_FORMAT(angelegt_am,'%d.%m.%Y'),')'),a.status) as status, a.id
                                FROM  aufgabe a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              // Fester filter

        // START EXTRA more

        $more_data1 = $this->app->Secure->GetGET("more_data1"); 

                                        if($more_data1==1) { 
                                                $subwhere[] = " a.status='abgeschlossen' ";
                        $count = "SELECT COUNT(a.id) FROM aufgabe a WHERE  (a.adresse='".$this->app->User->GetAdresse()."' OR a.initiator='".$this->app->User->GetAdresse()."') AND a.startdatum='0000-00-00' AND a.status='abgeschlossen'";
                                        }
                                        else {
                                                $subwhere[] = " a.status='offen' ";
                        $count = "SELECT COUNT(a.id) FROM aufgabe a WHERE  (a.adresse='".$this->app->User->GetAdresse()."' OR a.initiator='".$this->app->User->GetAdresse()."') AND a.startdatum='0000-00-00' AND a.status='offen'";
                                        }

        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1)   $subwhere[] = " a.adresse='".$this->app->User->GetAdresse()."' ";

        for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];

              $where = " (a.adresse='".$this->app->User->GetAdresse()."' OR a.initiator='".$this->app->User->GetAdresse()."' OR a.oeffentlich='1') AND a.startdatum='0000-00-00' AND a.id!='' $tmp";

              // gesamt anzahl


            break;


            case "angebote":

                                // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#angeboteoffen').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add(JQUERYREADY,"$('#angeboteheute').click( function() { fnFilterColumn2( 0 ); } );");

                for($r=1;$r<3;$r++)
                {
                $this->app->Tpl->Add(JAVASCRIPT,'
                function fnFilterColumn'.$r.' ( i )
                {
                if(oMoreData'.$r.$name.'==1)
                        oMoreData'.$r.$name.' = 0;
                else
                        oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
                }
        ');
         }
        // ENDE EXTRA checkboxen

              // headings
              $heading =  array('','Angebot','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Status','Men&uuml;');
              $width   =  array('1%','1%','10%','10%','40%','5%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','name','land','projekt','zahlungsweise','betrag','status','id');
              $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')','a.belegnr','adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status',"FORMAT(a.gesamtsumme,2{$extended_mysql55})",'a.status','adr.freifeld1');


                        $defaultorder =11;  //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc=1;
        $sumcol=9;
                                $alignright= array('9');



              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=angebot&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=angebot&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=angebot&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=angebot&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, a.belegnr as belegnr, DATE_FORMAT(a.datum,'%Y-%m-%d') as vom, 
              adr.kundennummer as kundennummer, ".$this->app->erp->MarkerUseredit("a.name","a.useredittimestamp")." as name, a.land as land, p.abkuerzung as projekt, a.zahlungsweise as zahlungsweise,  
              FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag, UPPER(a.status) as status, a.id
                  FROM  angebot a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              // Fester filter

        // START EXTRA more

        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " a.status='freigegeben' ";
        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " a.datum=CURDATE() AND a.status='freigegeben'";


        for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];

              $where = " a.id!='' AND a.status!='angelegt' $tmp ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM angebot a";

              $moreinfo = true;

            break;


            case "produktioninbearbeitung":

                                $heading =  array('','Produktion','Kd-Nr.','Kunde','Vom','Bezeichnung','Menge','Projekt','Status','Monitor','Men&uuml;');
              $width   =  array('1%','1%','1%','15%','5%','20%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','kunde','name','vom','bezeichnung','menge','projekt','status','icons','id');
              $searchsql = array('a.id','a.datum','a.belegnr','internet',"(SELECT CONCAT(ar.name_de,' (',ar.nummer,')') FROM produktion_position pos LEFT JOIN artikel ar ON ar.id=pos.artikel WHERE pos.produktion=a.id AND pos.explodiert=1 LIMIT 1)",'adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status','a.gesamtsumme',
                                                                                                         'a.status');

        $defaultorder = 11;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;


             $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=produktion&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=produktion&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=produktion&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;
              // SQL statement
  $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 'ENTWURF', adr.kundennummer as kunde,adr.name as name, DATE_FORMAT(a.datum,'%Y-%m-%d') as vom, 
(SELECT CONCAT(ar.name_de,' (',ar.nummer,')') FROM produktion_position pos LEFT JOIN artikel ar ON ar.id=pos.artikel WHERE pos.produktion=a.id AND pos.explodiert=1 LIMIT 1) as bezeichnung,
(SELECT pos.menge FROM produktion_position pos LEFT JOIN artikel ar ON ar.id=pos.artikel WHERE pos.produktion=a.id AND pos.explodiert=1 LIMIT 1) as menge,
p.abkuerzung as projekt,
                                UPPER(a.status) as status,  (".$this->IconsSQLProduktion().")  as icons, a.id
                  FROM  produktion a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";

              $where = " ( a.status='angelegt' OR a.belegnr=0) ";

              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM produktion a WHERE ( a.status='angelegt' OR a.belegnr=0) ";

              $moreinfo = true;

            break;
        

            case "produktionoffene":
              // headings
              $heading =  array('','Produktion','Kunde','Vom','Land','Projekt','Artikel','Menge','S','Monitor','Men&uuml;');
              $width   =  array('1%','5%','35%','1%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','name','vom','land','projekt','zahlungsweise','betrag','status','icons','id');
              $searchsql = array('a.id','DATE_FORMAT(a.datum,\'%d.%m.%Y\')','a.belegnr','internet','adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status','a.gesamtsumme','a.status','a.plz','a.id');

        $defaultorder = 11;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;


             $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=produktion&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=produktion&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=produktion&action=pdf&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, a.belegnr, adr.name as name, a.datum as vom, 
              IF(a.internet !='', CONCAT(a.land,' (I)'), a.land) as land, 
              p.abkuerzung as projekt, a.zahlungsweise as zahlungsweise, '1' as menge, UPPER(a.status) as status,  (".$this->IconsSQLProduktion().")  as icons, a.id 
                  FROM  produktion a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id ";
              // Fester filter
              $where = " a.id!='' AND a.belegnr!=0 AND a.autoversand='0' AND a.status='freigegeben' AND a.inbearbeitung=0 ";

              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM produktion a WHERE a.belegnr!=0 AND a.lager_ok='1' AND a.vorkasse_ok='1' AND a.check_ok='1' AND a.status='freigegeben' AND a.inbearbeitung=0 AND a.nachlieferung!='1' AND a.ust_ok='1' ";

              $moreinfo = true;

            break;


            case "produktionoffeneauto":

              $heading =  array('','Produktion','Kd-Nr.','Kunde','Vom','Bezeichnung','Menge','Projekt','Status','Monitor','Men&uuml;');
              $width   =  array('1%','1%','1%','15%','5%','20%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','kunde','name','vom','bezeichnung','menge','projekt','status','icons','id');
              $searchsql = array('a.id','a.datum','a.belegnr','internet',"(SELECT CONCAT(ar.name_de,' (',ar.nummer,')') FROM produktion_position pos LEFT JOIN artikel ar ON ar.id=pos.artikel WHERE pos.produktion=a.id AND pos.explodiert=1 LIMIT 1)",'adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status','a.gesamtsumme',
                                                                                                         'a.status');

                                $defaultorder=11;
                                $defaultorderdesc=1;

             $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=produktion&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=produktion&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=produktion&action=pdf&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;
              // SQL statement
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, a.belegnr, adr.kundennummer as kunde,adr.name as name, DATE_FORMAT(a.datum,'%Y-%m-%d') as vom, 
(SELECT CONCAT(ar.name_de,' (',ar.nummer,')') FROM produktion_position pos LEFT JOIN artikel ar ON ar.id=pos.artikel WHERE pos.produktion=a.id AND pos.explodiert=1 LIMIT 1) as bezeichnung,
(SELECT pos.menge FROM produktion_position pos LEFT JOIN artikel ar ON ar.id=pos.artikel WHERE pos.produktion=a.id AND pos.explodiert=1 LIMIT 1) as menge,
p.abkuerzung as projekt,
                                UPPER(a.status) as status,  (".$this->IconsSQLProduktion().")  as icons, a.id
                  FROM  produktion a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
        
              // Fester filter
              $where = " a.id!='' AND a.belegnr!=0 AND a.lager_ok='1' AND a.status='freigegeben' AND a.inbearbeitung=0 AND a.nachlieferung!='1' AND a.ust_ok='1' ";

              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM produktion a WHERE  a.id!='' AND a.belegnr!=0 AND a.lager_ok='1' AND a.status='freigegeben' 
                AND a.inbearbeitung=0 AND a.nachlieferung!='1' AND a.ust_ok='1'  ";

              $moreinfo = true;

            break;


            case "produktion":
              // START EXTRA checkboxen
              $this->app->Tpl->Add(JQUERYREADY,"$('#artikellager').click( function() { fnFilterColumn1( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#ustpruefung').click( function() { fnFilterColumn2( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#zahlungseingang').click( function() { fnFilterColumn3( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#zahlungseingangfehlt').click( function() { fnFilterColumn5( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#manuellepruefung').click( function() { fnFilterColumn4( 0 ); } );");

              $this->app->Tpl->Add(JQUERYREADY,"$('#auftragheute').click( function() { fnFilterColumn6( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#auftragoffene').click( function() { fnFilterColumn7( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#auftragstornierte').click( function() { fnFilterColumn8( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#auftragabgeschlossene').click( function() { fnFilterColumn9( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#auftraggestartete').click( function() { fnFilterColumn10( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#artikelfehlen').click( function() { fnFilterColumn11( 0 ); } );");
//            $this->app->Tpl->Add(JQUERYREADY,"$('#artikellager').click( function() {  oTable".$name.".fnDraw(); } );");


                for($r=1;$r<12;$r++)
                {
                  $this->app->Tpl->Add(JAVASCRIPT,'
                                function fnFilterColumn'.$r.' ( i )
                                {
                                        if(oMoreData'.$r.$name.'==1)
                                        oMoreData'.$r.$name.' = 0;
                                        else
                                        oMoreData'.$r.$name.' = 1;

                                        $(\'#'.$name.'\').dataTable().fnFilter( 
                                        \'A\',
                                        i, 
                                        0,0
                                        );
                                }
                        ');
                }
              // ENDE EXTRA checkboxen

              // headings
              $heading =  array('','Produktion','Kd-Nr.','Kunde','Vom','Bezeichnung','Menge','Projekt','Status','Monitor','Men&uuml;');
              $width   =  array('1%','1%','1%','15%','5%','20%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','kunde','name','vom','bezeichnung','menge','projekt','status','icons','id');
              $searchsql = array('a.id','a.datum','a.belegnr','internet',"(SELECT CONCAT(ar.name_de,' (',ar.nummer,')') FROM produktion_position pos LEFT JOIN artikel ar ON ar.id=pos.artikel WHERE pos.produktion=a.id AND pos.explodiert=1 LIMIT 1)",'adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status','a.gesamtsumme',
                                                                                                         'a.status');

              $defaultorder = 11;  //Optional wenn andere Reihenfolge gewuenscht
                                $defaultorderdesc=1;


              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=produktion&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=produktion&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=produktion&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, a.belegnr, adr.kundennummer as kunde,adr.name as name, DATE_FORMAT(a.datum,'%Y-%m-%d') as vom, 

(SELECT CONCAT(ar.name_de,' (',ar.nummer,')') FROM produktion_position pos LEFT JOIN artikel ar ON ar.id=pos.artikel WHERE pos.produktion=a.id AND pos.explodiert=1 LIMIT 1) as bezeichnung,

(SELECT pos.menge FROM produktion_position pos LEFT JOIN artikel ar ON ar.id=pos.artikel WHERE pos.produktion=a.id AND pos.explodiert=1 LIMIT 1) as menge,

p.abkuerzung as projekt,

                                UPPER(a.status) as status,  (".$this->IconsSQLProduktion().")  as icons, a.id
                  FROM  produktion a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              // Fester filter
              
              // START EXTRA more
            
              $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " a.lager_ok=1 ";
              $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " a.ust_ok=0 ";
              $more_data3 = $this->app->Secure->GetGET("more_data3"); if($more_data3==1) $subwhere[] = " a.vorkasse_ok=1 ";
              $more_data4 = $this->app->Secure->GetGET("more_data4"); if($more_data4==1) $subwhere[] = " (a.check_ok=0 OR a.liefersperre_ok=0 OR a.kreditlimit_ok='0') ";
              $more_data5 = $this->app->Secure->GetGET("more_data5"); if($more_data5==1) $subwhere[] = " a.vorkasse_ok=0 ";

                                $more_data6 = $this->app->Secure->GetGET("more_data6"); if($more_data6==1) { $subwhere[] = " a.datum=CURDATE() "; $ignore = true; }

                                $more_data7 = $this->app->Secure->GetGET("more_data7"); if($more_data7==1) { $orsubwhere[] = " a.status='freigegeben' "; $ignore = true; }
                                $more_data8 = $this->app->Secure->GetGET("more_data8"); if($more_data8==1) { $orsubwhere[] = " a.status='storniert' "; $ignore = true; }
                                $more_data9 = $this->app->Secure->GetGET("more_data9"); if($more_data9==1) { $orsubwhere[] = " a.status='abgeschlossen' "; $ignore = true; }
                                $more_data10 = $this->app->Secure->GetGET("more_data10"); if($more_data10==1) { $orsubwhere[] = " a.status='gestartet' "; $ignore = true; }

              $more_data11 = $this->app->Secure->GetGET("more_data11"); if($more_data11==1) $subwhere[] = " a.lager_ok=0 ";

              for($j=0;$j<count($subwhere);$j++)
                                        $tmp .=  " AND ".$subwhere[$j]; 


                                if(count($orsubwhere) > 0){
                                $tmp .=" AND(";
              for($j=0;$j<count($orsubwhere);$j++)
                                {
                                        $tmp .=  $orsubwhere[$j]; 
                                        if($j<count($orsubwhere)-1) $tmp .= " OR ";
                                }
                                $tmp .=" ) ";
                                }


              if($tmp!="" && !$ignore)$tmp .= " AND a.status='freigegeben' ";

              // ENDE EXTRA more

              $where = " a.id!='' AND a.status!='angelegt' $tmp";

              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM produktion a ";

              $moreinfo = true; // EXTRA

            break;

            case "lagerletztebewegungen":
              // headings

              // headings
              $heading =  array('Datum','Lager','Menge','Nummer','Artikel','Richtung','Referenz','Bearbeiter','Projekt','Men&uuml;');
              $width   =  array('1%','5%','5%','5%','5%','5%','40%','20%','5%','1%');
              $findcols = array('zeit','lager','menge','nummer','name_de','Richtung','referenz','bearbeiter','projekt','id');
              $searchsql = array('lpi.referenz','lpi.bearbeiter','p.abkuerzung','DATE_FORMAT(lpi.zeit,\'%d.%m.%Y\')','lp.kurzbezeichnung','a.name_de','a.nummer');

        $defaultorder = 10;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;
        
                                $menu = "-";

             //$menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap></td></tr></table>";
//<a href=\"#\"onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=dateien&action=delete&id=%value%';\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\" ></a>

//            $menucol=3;
/*
SELECT DATE_FORMAT(lpi.zeit,'%d.%m.%Y') as datum, lp.kurzbezeichnung as lager, lpi.menge as menge, lpi.vpe as VPE, if(lpi.eingang,'Eingang','Ausgang') as Richtung, substring(lpi.referenz,1,60) as referenz, lpi.bearbeiter as bearbeiter, p.abkuerzung as projekt, 
      lpi.id FROM lager_bewegung lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id  WHERE lpi.artikel='$id' order by lpi.zeit DESC*/

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS lpi.id,
DATE_FORMAT(lpi.zeit,'%d.%m.%Y') as datum, lp.kurzbezeichnung as lager, lpi.menge as menge, 
                        a.nummer, a.name_de, if(lpi.eingang,'Eingang','Ausgang') as Richtung, substring(lpi.referenz,1,60) as referenz, lpi.bearbeiter as bearbeiter, p.abkuerzung as projekt, 
      lpi.id FROM lager_bewegung lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id LEFT JOIN artikel a ON a.id=lpi.artikel";

                        // Fester filter
              //$where = " ";

              // gesamt anzahl
              $count = "SELECT COUNT(lpi.id) FROM lager_bewegung lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id ";

            break;


            case "lagerbewegungartikel":
              // headings

              // headings
              $heading =  array('Datum','Lager','Menge','VPE','Richtung','Referenz','Bearbeiter','Projekt','Men&uuml;');
              $width   =  array('1%','5%','5%','5%','5%','40%','20%','5%','1%');
              $findcols = array('zeit','lager','menge','vpe','Richtung','referenz','bearbeiter','projekt','id');
              $searchsql = array('lpi.referenz','lpi.bearbeiter','p.abkuerzung','DATE_FORMAT(lpi.zeit,\'%d.%m.%Y\')','lp.kurzbezeichnung');

        $defaultorder = 9;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=1;
        
                                $menu = "-";

             //$menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap></td></tr></table>";
//<a href=\"#\"onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=dateien&action=delete&id=%value%';\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\" ></a>

//            $menucol=3;
/*
SELECT DATE_FORMAT(lpi.zeit,'%d.%m.%Y') as datum, lp.kurzbezeichnung as lager, lpi.menge as menge, lpi.vpe as VPE, if(lpi.eingang,'Eingang','Ausgang') as Richtung, substring(lpi.referenz,1,60) as referenz, lpi.bearbeiter as bearbeiter, p.abkuerzung as projekt, 
      lpi.id FROM lager_bewegung lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id  WHERE lpi.artikel='$id' order by lpi.zeit DESC*/

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS lpi.id,
DATE_FORMAT(lpi.zeit,'%d.%m.%Y') as datum, lp.kurzbezeichnung as lager, lpi.menge as menge, lpi.vpe as VPE, if(lpi.eingang,'Eingang','Ausgang') as Richtung, substring(lpi.referenz,1,60) as referenz, lpi.bearbeiter as bearbeiter, p.abkuerzung as projekt, 
      lpi.id FROM lager_bewegung lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id ";

              
                        // Fester filter
              $where = " lpi.artikel='$id'  ";

              // gesamt anzahl
              $count = "SELECT COUNT(lpi.id) FROM lager_bewegung lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id  WHERE lpi.artikel='$id'";

            break;




            case "ticketspam":
              // headings

              // headings
              $heading =  array('Zeit','Verfasser','Betreff','Men&uuml;');
              $width   =  array('17%','40%','42%','1%');
              $findcols = array('tn.zeit','verfasser','tn.betreff','tn.id');
              $searchsql = array('zeit','verfasser','mail','betreff','id');

                                $defaultorder=4;
                                $defaultorderdesc=1;



             $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=ticket&action=assistent&id=%value%&lesen=1&back=archiv\"><img src=\"./themes/new/images/edit.png\"></a></td></tr></table>";


//<a href=\"index.php?module=ticket&action=assistent&id=%value%&lesen=1\">Lesen</a>&nbsp;<a href=\"index.php?module=ticket&action=assistent&id=%value%\">Bearbeiten</a>

//            $menucol=3;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS tn.id, DATE_FORMAT(tn.zeit,'%d.%m.%Y %H:%i') as empfang, CONCAT(tn.verfasser,' ',tn.mail) as verfasser, tn.betreff, tn.id FROM ticket_nachricht tn ";
              
                        // Fester filter
              $where = " tn.id!=0 AND tn.status='spam' ";

              // gesamt anzahl
              $count = "SELECT COUNT(tn.id) FROM ticket_nachricht tn WHERE tn.status='spam' ";

            break;

            case "ticketarchiv":
              // headings

              // headings
              $heading =  array('Zeit','Verfasser','Betreff','Men&uuml;');
              $width   =  array('17%','40%','42%','1%');
              $findcols = array('tn.zeit','verfasser','tn.betreff','tn.id');
              $searchsql = array('zeit','verfasser','mail','betreff','id');


                                $defaultorder=1;
                                $defaultorderdesc=0;

             $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=ticket&action=assistent&id=%value%&lesen=1&back=archiv\"><img src=\"./themes/new/images/edit.png\"></a></td></tr></table>";


//<a href=\"index.php?module=ticket&action=assistent&id=%value%&lesen=1\">Lesen</a>&nbsp;<a href=\"index.php?module=ticket&action=assistent&id=%value%\">Bearbeiten</a>

//            $menucol=3;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS tn.id, DATE_FORMAT(tn.zeit,'%d.%m.%Y %H:%i') as empfang, CONCAT(tn.verfasser,' ',tn.mail) as verfasser, tn.betreff, tn.id FROM ticket_nachricht tn ";
              
                        // Fester filter
              $where = " tn.id!=0  ";

              // gesamt anzahl
              $count = "SELECT COUNT(tn.id) FROM ticket_nachricht tn ";

            break;




            case "mlmwartekonto":
              // headings

              // headings
              $heading =  array('Bezeichnung','Betrag','Men&uuml;');
              $width   =  array('80%','15%','5%');
              $findcols = array('bezeichnung','betrag','id');
              $searchsql = array('bezeichnung','betrag','id');

        $id=$this->app->Secure->GetGET("id");

             $menu = "<a href=\"index.php?module=adresse&action=multilevel&cmd=edit&id=$id&sid=%value%#tabs-2\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\"></a><a href=\"index.php?module=adresse&action=multilevel&cmd=delete&id=$id&sid=%value%#tabs-2\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\" ></a>";

//            $menucol=3;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS m.id, m.bezeichnung, m.betrag, m.id FROM mlm_wartekonto m ";//LEFT JOIN artikel a ON a.id=m.artikel ";
              
                        // Fester filter
              $where = " m.adresse='$id' AND m.abgerechnet=0 ";

              // gesamt anzahl
              $count = "SELECT COUNT(id) FROM mlm_wartekonto WHERE adresse='$id' AND abgerechnet=0";

            break;





            case "wareneingangarchiv":
              // headings

              // headings
              $heading =  array('Datum','Nr','Name','Bearbeiter','Men&uuml;');
              $width   =  array('1%','5%','55%','40%','1%');
              $findcols = array('p.datum','nr','name','bearbeiter_distribution','id');
              $searchsql = array('p.datum','a.name','p.bearbeiter_distribution','p.id');


             $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=wareneingang&action=distribution&id=%value%\">Betrachten</a></td></tr></table>";

//            $menucol=3;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS p.id, DATE_FORMAT(p.datum,'%d.%m.%Y') as datum,p.id as Nr, 
                                        a.name, p.bearbeiter_distribution,p.id  FROM paketannahme p
                                        LEFT JOIN adresse a ON a.id=p.adresse ";
              
                        // Fester filter
              $where = " p.status='abgeschlossen'  ";

              // gesamt anzahl
              $count = "SELECT COUNT(p.id) FROM paketannahme p WHERE p.status='abgeschlossen'";

            break;



            case "auftraegeinbearbeitungimport":
 // headings
              $heading =  array('','Auftrag','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Monitor','Men&uuml;');
              $width   =  array('1%','1%','10%','10%','40%','5%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kunde','name','land','projekt','zahlungsweise','gesamtsumme','status','id');
              $searchsql = array('a.datum','a.belegnr','a.ihrebestellnummer','internet','adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status','a.gesamtsumme');
                                 $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')','a.belegnr','a.ihrebestellnummer','internet','adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status',"FORMAT(a.gesamtsumme,2{$extended_mysql55})",'adr.freifeld1');

        $sumcol=9;
                                $alignright= array('9');

              $defaultorder = 11;  //Optional wenn andere Reihenfolge gewuenscht
                                $defaultorderdesc=1;


              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=auftrag&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=auftrag&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=auftrag&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 
                        if(belegnr='','ENTWURF',belegnr) as belegnr, DATE_FORMAT(a.datum,'%Y-%m-%d') as vom, adr.kundennummer as kunde,
                        CONCAT(".$this->app->erp->MarkerUseredit("a.name","a.useredittimestamp").",if(a.internebemerkung='','',' <font color=red><strong>*</strong></font>'),if(a.freitext='','',' <font color=blue><strong>*</strong></font>')) as name, 
              IF(a.internet !='', CONCAT(a.land,' (I)'), a.land) as land,LEFT(UPPER( p.abkuerzung),10) as projekt, a.zahlungsweise as zahlungsweise,  
              FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag,  (".$this->IconsSQL().")  as icons, a.id
                  FROM  auftrag a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              
              $where = " a.id!='' AND a.status='angelegt' AND (a.shopextid > 0 OR a.shopextid!='')  ".$this->app->erp->ProjektRechte();

        $count = "SELECT COUNT(a.id) FROM auftrag a WHERE a.status='angelegt' AND (a.shopextid > 0 OR a.shopextid!='') ";

              $moreinfo = true;

            break;

            case "auftraegeinbearbeitung":
 // headings
              $heading =  array('','Auftrag','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Monitor','Men&uuml;');
              $width   =  array('1%','1%','10%','10%','40%','5%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kunde','name','land','projekt','zahlungsweise','gesamtsumme','status','id');
              $searchsql = array('a.datum','a.belegnr','a.ihrebestellnummer','internet','adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status','a.gesamtsumme');
                                 $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')','a.belegnr','a.ihrebestellnummer','internet','adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status',"FORMAT(a.gesamtsumme,2{$extended_mysql55})",'adr.freifeld1');


                                $alignright= array('9');
              $defaultorder = 11;  //Optional wenn andere Reihenfolge gewuenscht
                                $defaultorderdesc=1;


              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=auftrag&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=auftrag&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=auftrag&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 
                        if(belegnr='','ENTWURF',belegnr) as belegnr, DATE_FORMAT(a.datum,'%Y-%m-%d') as vom, adr.kundennummer as kunde,
                        CONCAT(".$this->app->erp->MarkerUseredit("a.name","a.useredittimestamp").",if(a.internebemerkung='','',' <font color=red><strong>*</strong></font>'),if(a.freitext='','',' <font color=blue><strong>*</strong></font>')) as name, 
              IF(a.internet !='', CONCAT(a.land,' (I)'), a.land) as land,LEFT(UPPER( p.abkuerzung),10) as projekt, a.zahlungsweise as zahlungsweise,  
              FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag,  (".$this->IconsSQL().")  as icons, a.id
                  FROM  auftrag a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              
              $where = " a.id!='' AND a.status='angelegt' AND a.shopextid <= 0  ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
        $count = "SELECT COUNT(a.id) FROM auftrag a WHERE a.status='angelegt' AND a.shopextid <= 0 ";
              $moreinfo = true;

            break;




            case "auftraegeoffene":

              // headings
              $heading =  array('','','Auftrag','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Monitor','Men&uuml;');
              $width   =  array('1%','1%','10%','12%','10%','35%','1%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','versand','belegnr','vom','kundennummer','kunde','land','projekt','zahlungsweise','gesamtsumme','status','icons','id');
              $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')','a.belegnr','a.ihrebestellnummer','internet','adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','adr.freifeld1','a.status',"FORMAT(a.gesamtsumme,2{$extended_mysql55})");


             $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=auftrag&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=auftrag&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=auftrag&action=pdf&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";


              $defaultorder=11;
              $defaultorderdesc=1;

              $menucol=11;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 
                                        CONCAT('<!--',if(a.autoversand='1' AND a.vorkasse_ok='1' AND a.liefertermin_ok='1' AND a.porto_ok='1' AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok='1','checked',''),'--><input type=\"checkbox\" name=\"auftraegemarkiert[]\" value=\"',a.id,'\"',
                                                        if(a.autoversand AND a.vorkasse_ok AND a.liefertermin_ok='1' AND a.porto_ok AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok,'checked',''),'>') as versand, 
                                                CONCAT(a.belegnr,if(a.autoversand,'','')), a.datum as vom, adr.kundennummer as kundennummer, CONCAT(".$this->app->erp->MarkerUseredit("a.name","a.useredittimestamp").",if(a.internebemerkung='','',' <font color=red><strong>*</strong></font>',if(a.freitext='','',' <font color=blue><strong>*</strong></font>'))) as kunde, 
              IF(a.internet !='', CONCAT(a.land,' (I)'), a.land) as land, 
              p.abkuerzung as projekt, a.zahlungsweise as zahlungsweise, FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag, (".$this->IconsSQL().")  as icons, a.id 
                  FROM  auftrag a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              // Fester filter
              $where = " a.id!='' AND (a.belegnr!=0 OR a.belegnr!='') AND a.status='freigegeben' AND a.inbearbeitung=0 AND a.nachlieferung!='1'  
                                                AND a.vorkasse_ok='1' AND a.porto_ok='1' AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok='1' ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM auftrag a WHERE  a.id!='' AND (a.belegnr!=0 OR a.belegnr!='') AND a.status='freigegeben' 
                AND a.inbearbeitung=0 AND a.nachlieferung!='1' AND a.vorkasse_ok='1' AND a.porto_ok='1' AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok='1' ";

              $moreinfo = true;
                

            break;


            case "auftraegeoffeneauto":

              // headings
              $heading =  array('','','Auftrag','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Monitor','Men&uuml;');
              $width   =  array('1%','1%','10%','12%','10%','35%','1%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','kunde','land','projekt','zahlungsweise','gesamtsumme','status','icons','id');
              $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')','a.belegnr','a.ihrebestellnummer','internet','adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status',"FORMAT(a.gesamtsumme,2{$extended_mysql55})",'adr.freifeld1');

              $defaultorder=12;
              $defaultorderdesc=0;


             $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=auftrag&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=auftrag&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=auftrag&action=pdf&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=11;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 
                                        CONCAT('<!--',if(a.autoversand='1' AND a.vorkasse_ok='1' AND a.liefertermin_ok='1' AND a.porto_ok='1' AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok='1','checked',''),'--><input type=\"checkbox\" name=\"auftraegemarkiert[]\" value=\"',a.id,'\"',
                                                        if(a.autoversand AND a.vorkasse_ok AND a.liefertermin_ok='1' AND a.porto_ok AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok,'checked',''),'>') as versand, 
                                                CONCAT(a.belegnr,if(a.autoversand,'','')), a.datum as vom, adr.kundennummer as kundennummer, CONCAT(".$this->app->erp->MarkerUseredit("a.name","a.useredittimestamp").",if(a.internebemerkung='','',' <font color=red><strong>*</strong></font>'),if(a.freitext='','',' <font color=blue><strong>*</strong></font>')) as kunde, 
              IF(a.internet !='', CONCAT(a.land,' (I)'), a.land) as land, 
              p.abkuerzung as projekt, a.zahlungsweise as zahlungsweise, FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag, (".$this->IconsSQL().")  as icons, a.id 
                  FROM  auftrag a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              // Fester filter
              $where = " a.id!='' AND (a.belegnr!=0 OR a.belegnr!='') AND a.status='freigegeben' AND a.inbearbeitung=0 AND a.nachlieferung!='1' AND a.autoversand='1'  AND a.liefertermin_ok='1' AND kreditlimit_ok='1' AND liefersperre_ok='1'
                                                AND a.vorkasse_ok='1' AND a.porto_ok='1' AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok='1' ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM auftrag a WHERE  a.id!='' AND (a.belegnr!=0 OR a.belegnr!='') AND a.status='freigegeben' AND a.autoversand='1'
                AND a.inbearbeitung=0 AND a.nachlieferung!='1' AND a.vorkasse_ok='1' AND a.porto_ok='1' AND a.lager_ok='1' AND a.check_ok='1' AND a.ust_ok='1' AND a.liefertermin_ok='1' AND kreditlimit_ok='1' AND liefersperre_ok='1' ";

              $moreinfo = true;

            break;


            case "auftraege":
              // START EXTRA checkboxen
              $this->app->Tpl->Add(JQUERYREADY,"$('#artikellager').click( function() { fnFilterColumn1( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#ustpruefung').click( function() { fnFilterColumn2( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#zahlungseingang').click( function() { fnFilterColumn3( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#zahlungseingangfehlt').click( function() { fnFilterColumn5( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#manuellepruefung').click( function() { fnFilterColumn4( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#ohnerechnung').click( function() { fnFilterColumn10( 0 ); } );");

              $this->app->Tpl->Add(JQUERYREADY,"$('#auftragheute').click( function() { fnFilterColumn6( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#auftragoffene').click( function() { fnFilterColumn7( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#auftragstornierte').click( function() { fnFilterColumn8( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#auftragabgeschlossene').click( function() { fnFilterColumn9( 0 ); } );");
              $this->app->Tpl->Add(JQUERYREADY,"$('#teillieferung').click( function() { fnFilterColumn11( 0 ); } );");
//            $this->app->Tpl->Add(JQUERYREADY,"$('#artikellager').click( function() {  oTable".$name.".fnDraw(); } );");


                for($r=1;$r<12;$r++)
                {
                  $this->app->Tpl->Add(JAVASCRIPT,'
                                function fnFilterColumn'.$r.' ( i )
                                {
                                        if(oMoreData'.$r.$name.'==1)
                                        oMoreData'.$r.$name.' = 0;
                                        else
                                        oMoreData'.$r.$name.' = 1;

                                        $(\'#'.$name.'\').dataTable().fnFilter( 
                                        \'A\',
                                        i, 
                                        0,0
                                        );
                                }
                        ');
                }
              // ENDE EXTRA checkboxen

              // headings
              $heading =  array('','Auftrag','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Monitor','Men&uuml;');
              $width   =  array('1%','1%','10%','10%','40%','5%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kunde','name','land','projekt','zahlungsweise','gesamtsumme','status','icons','id');
         $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')','a.belegnr','a.ihrebestellnummer','a.internet','adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status',"FORMAT(a.gesamtsumme,2{$extended_mysql55})",'adr.freifeld1');

                                $alignright= array('9');

                        $defaultorder = 12;  //Optional wenn andere Reihenfolge gewuenscht
                                $defaultorderdesc=1;
                                $sumcol = 9;


              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=auftrag&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=auftrag&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
 "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=auftrag&action=copy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=auftrag&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, 
                                if(a.status='angelegt','ENTWURF',a.belegnr), DATE_FORMAT(a.datum,'%Y-%m-%d') as vom, adr.kundennummer as kunde,CONCAT(".$this->app->erp->MarkerUseredit("a.name","a.useredittimestamp").",if(a.internebemerkung='','',' <font color=red><strong>*</strong></font>'),if(a.freitext='','',' <font color=blue><strong>*</strong></font>')) as name, 
              IF(a.internet !='', CONCAT(a.land,' (I)'), a.land) as land,LEFT(UPPER( p.abkuerzung),10) as projekt, a.zahlungsweise as zahlungsweise,  
              FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag,  (".$this->IconsSQL().")  as icons, a.id
                  FROM  auftrag a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              // Fester filter
              
              // START EXTRA more
            
              $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " a.lager_ok=0 ";
              $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " a.ust_ok=0 ";
              $more_data3 = $this->app->Secure->GetGET("more_data3"); if($more_data3==1) $subwhere[] = " a.vorkasse_ok=1 ";
              //$more_data4 = $this->app->Secure->GetGET("more_data4"); if($more_data4==1) $subwhere[] = " a.check_ok=0 ";
        $more_data4 = $this->app->Secure->GetGET("more_data4"); if($more_data4==1) $subwhere[] = " (a.check_ok=0 OR a.liefersperre_ok=0 OR a.kreditlimit_ok='0') ";
              $more_data5 = $this->app->Secure->GetGET("more_data5"); if($more_data5==1) $subwhere[] = " a.vorkasse_ok=0 ";

                                $more_data6 = $this->app->Secure->GetGET("more_data6"); if($more_data6==1) { $subwhere[] = " a.datum=CURDATE() "; $ignore = true; }
                                $more_data7 = $this->app->Secure->GetGET("more_data7"); if($more_data7==1) { $subwhere[] = " a.status='freigegeben' "; $ignore = true; }
                                $more_data8 = $this->app->Secure->GetGET("more_data8"); if($more_data8==1) { $subwhere[] = " a.status='storniert' "; $ignore = true; }
                                $more_data9 = $this->app->Secure->GetGET("more_data9"); if($more_data9==1) { $subwhere[] = " a.status='abgeschlossen' "; $ignore = true; }
                                $more_data10 = $this->app->Secure->GetGET("more_data10"); if($more_data10==1) { 
                                        $subwhere[] = " (SELECT COUNT(r.id) FROM rechnung r WHERE r.auftragid=a.id) <= 0 AND a.gesamtsumme > 0 "; $ignore = true; }
                                $more_data11 = $this->app->Secure->GetGET("more_data11"); if($more_data11==1) { $subwhere[] = " a.teillieferung_moeglich='1' "; }


              for($j=0;$j<count($subwhere);$j++)
                                        $tmp .=  " AND ".$subwhere[$j]; 

              if($tmp!="" && !$ignore)$tmp .= " AND a.status='freigegeben' ";

              // ENDE EXTRA more

              $where = " a.id!='' $tmp ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM auftrag a ";

              $moreinfo = true; // EXTRA

            break;


                case 'inhaltsseiten':
                        // EXTRA CHECKBOXEN
                        $this->app->Tpl->Add(JQUERYREADY,"$('#onlyde').click( function() { fnFilterColumn1( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlyen').click( function() { fnFilterColumn2( 0 ); } );");
                        $this->app->Tpl->Add(JQUERYREADY,"$('#onlyonline').click( function() { fnFilterColumn3( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlyoffline').click( function() { fnFilterColumn4( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlyhtml').click( function() { fnFilterColumn5( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlyemail').click( function() { fnFilterColumn6( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlygroups').click( function() { fnFilterColumn7( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlyteaser').click( function() { fnFilterColumn8( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlynews').click( function() { fnFilterColumn9( 0 ); } );");

                        for($r=1;$r<10;$r++)
                        {
                                $this->app->Tpl->Add(JAVASCRIPT,'
                                        function fnFilterColumn'.$r.' ( i )
                                        {
                                                if(oMoreData'.$r.$name.'==1)
                                                        oMoreData'.$r.$name.' = 0;
                                                else
                                                        oMoreData'.$r.$name.' = 1;

                                                $(\'#'.$name.'\').dataTable().fnFilter( 
                                                \'A\',
                                                i, 
                                                0,0
                                                );
                                        }
                                ');
                        }
                        // ENDE EXTRA CHECKBOXEN
        
                        $heading =  array('Inhalts-ID','Typ','Sprache','Shop','Erstellt','Sichtbar bis','Status','Men&uuml;');
      $width   =  array('10%','10%','1%','15%','10%','10%','7%','10%');
      $findcols = array('inhalt', 'inhaltstyp','sprache','shop','datum','sichtbarbis','aktiv','id');
      $searchsql = array('i.inhalt','i.inhaltstyp','s.bezeichnung','i.datum','i.sichtbarbis');

                        $menu = "<a href=\"index.php?module=inhalt&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                        "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=inhalt&action=delete&id=%value%\");>".
                                                        "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                                                        "&nbsp;<a href=\"index.php?module=inhalt&action=copy&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>";

                        $sql = "SELECT SQL_CALC_FOUND_ROWS i.id, i.inhalt, i.inhaltstyp, i.sprache, s.bezeichnung AS shop, i.datum, DATE(i.sichtbarbis) as sichtbarbis, 
                                                        IF(i.aktiv=1,'<span style=\"background-color:green;color: #FFF;\">ONLINE</span>','OFFLINE') AS aktiv, i.id 
                                                        FROM inhalt AS i LEFT JOIN shopexport AS s ON s.id=i.shop ";

                        $subwhere = array();
                        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " i.sprache='de' ";
      $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " i.sprache='en' ";
                        $more_data3 = $this->app->Secure->GetGET("more_data3"); if($more_data3==1) $subwhere[] = " i.aktiv=1 ";
      $more_data4 = $this->app->Secure->GetGET("more_data4"); if($more_data4==1) $subwhere[] = " i.aktiv=1 ";
      $more_data5 = $this->app->Secure->GetGET("more_data5"); if($more_data5==1) $subwhere[] = " i.inhaltstyp='page' ";
      $more_data6 = $this->app->Secure->GetGET("more_data6"); if($more_data6==1) $subwhere[] = " i.inhaltstyp='email' ";
      $more_data7 = $this->app->Secure->GetGET("more_data7"); if($more_data7==1) $subwhere[] = " i.inhaltstyp='group' ";
      $more_data8 = $this->app->Secure->GetGET("more_data8"); if($more_data8==1) $subwhere[] = " i.inhaltstyp='teaser' ";
      $more_data9 = $this->app->Secure->GetGET("more_data9"); if($more_data9==1) $subwhere[] = " i.inhaltstyp='news' ";
                        for($j=0;$j<count($subwhere);$j++) $tmp .=  " AND ".$subwhere[$j];

                        
                        $where = "i.id $tmp";
                        $count = "SELECT COUNT(i.id) FROM inhalt i";
                        $moreinfo = false;              
                break;

        case 'inhaltsseitenshop':
      // EXTRA CHECKBOXEN
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlyde').click( function() { fnFilterColumn1( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlyen').click( function() { fnFilterColumn2( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlyonline').click( function() { fnFilterColumn3( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlyoffline').click( function() { fnFilterColumn4( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlyhtml').click( function() { fnFilterColumn5( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlyemail').click( function() { fnFilterColumn6( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlygroups').click( function() { fnFilterColumn7( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlyteaser').click( function() { fnFilterColumn8( 0 ); } );");
      $this->app->Tpl->Add(JQUERYREADY,"$('#onlynews').click( function() { fnFilterColumn9( 0 ); } );");

      for($r=1;$r<10;$r++)
      {
        $this->app->Tpl->Add(JAVASCRIPT,'
          function fnFilterColumn'.$r.' ( i )
          {
            if(oMoreData'.$r.$name.'==1)
              oMoreData'.$r.$name.' = 0;
            else
              oMoreData'.$r.$name.' = 1;

            $(\'#'.$name.'\').dataTable().fnFilter( 
            \'A\',
            i, 
            0,0
            );
          }
        ');
      }
      // ENDE EXTRA CHECKBOXEN

      $heading =  array('Inhalts-ID','Typ','Sprache','Erstellt','Sichtbar bis','Status','Men&uuml;');
      $width   =  array('10%','10%','1%','10%','10%','7%','10%');
      $findcols = array('inhalt', 'inhaltstyp','sprache','datum','sichtbarbis','aktiv','id');
      $searchsql = array('i.inhalt','i.inhaltstyp','s.bezeichnung','i.datum','i.sichtbarbis');

      $menu = "<a href=\"index.php?module=inhalt&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
              "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=inhalt&action=delete&id=%value%\");>".
              "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
              "&nbsp;<a href=\"index.php?module=inhalt&action=copy&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>";

      $sql = "SELECT SQL_CALC_FOUND_ROWS i.id, i.inhalt, i.inhaltstyp, i.sprache, i.datum, DATE(i.sichtbarbis) as sichtbarbis, 
              IF(i.aktiv=1,'<span style=\"background-color:green;color: #FFF;\">ONLINE</span>','OFFLINE') AS aktiv, i.id 
              FROM inhalt AS i LEFT JOIN shopexport AS s ON s.id=i.shop ";

      $subwhere = array();
      $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " i.sprache='de' ";
      $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " i.sprache='en' ";
      $more_data3 = $this->app->Secure->GetGET("more_data3"); if($more_data3==1) $subwhere[] = " i.aktiv=1 ";
      $more_data4 = $this->app->Secure->GetGET("more_data4"); if($more_data4==1) $subwhere[] = " i.aktiv=1 ";
      $more_data5 = $this->app->Secure->GetGET("more_data5"); if($more_data5==1) $subwhere[] = " i.inhaltstyp='page' ";
      $more_data6 = $this->app->Secure->GetGET("more_data6"); if($more_data6==1) $subwhere[] = " i.inhaltstyp='email' ";
      $more_data7 = $this->app->Secure->GetGET("more_data7"); if($more_data7==1) $subwhere[] = " i.inhaltstyp='group' ";
      $more_data8 = $this->app->Secure->GetGET("more_data8"); if($more_data8==1) $subwhere[] = " i.inhaltstyp='teaser' ";
      $more_data9 = $this->app->Secure->GetGET("more_data9"); if($more_data9==1) $subwhere[] = " i.inhaltstyp='news' ";
      for($j=0;$j<count($subwhere);$j++) $tmp .=  " AND ".$subwhere[$j];

                        $shop = $this->app->Secure->GetGET('id');
      $where = "i.id AND i.shop='$shop' $tmp";
      $count = "SELECT COUNT(i.id) FROM inhalt i WHERE i.shop='$shop'";
      $moreinfo = false;
    break;

                case "arbeitsnachweiseprojekt":
   // headings
              $heading =  array('Datum','Dauer','Teilprojekt/Aufgabe','Men&uuml;');
              $width   =  array('10%','10%','75%','5%');
              $findcols = array('Datum','Dauer','aufgabe','id');
              $searchsql = array('z.id','z.bis');

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=projekt&action=arbeitsnachweispdf&date=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a>".
                  "&nbsp;</td></tr></table>";

              $menucol=11;
              // SQL statement
                //'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,
              $sql = "SELECT
'leer',
DATE_FORMAT(z.bis, '%Y-%m-%d') AS Datum, SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600) as Dauer, ap.aufgabe, CONCAT(DATE_FORMAT(z.bis, '%Y-%m-%d'),'-',ap.id) as id

FROM zeiterfassung z LEFT JOIN arbeitspaket ap ON ap.id=z.arbeitspaket
                                ";

              // Fester filter

        // START EXTRA more

//        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " a.status='freigegeben' ";
//        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " a.datum=CURDATE() AND a.status='freigegeben'";


        for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];

                                $id = $this->app->Secure->GetGET("id");
              $where = " ap.aufgabe IS NOT NULL $tmp AND ap.projekt='$id' GROUP by Datum,ap.id ";

              // gesamt anzahl
              $count = "SELECT COUNT(z.id) FROM zeiterfassung z";

          //    $moreinfo = true;


            break;




                case "arbeitspakete":
                        $this->app->Tpl->Add(JQUERYREADY,"$('#altearbeitspaket').click( function() { fnFilterColumn1( 0 ); } );");

    for($r=1;$r<2;$r++)
    {
      $this->app->Tpl->Add(JAVASCRIPT,'
        function fnFilterColumn'.$r.' ( i )
        {
          if(oMoreData'.$r.$name.'==1)
          oMoreData'.$r.$name.' = 0;
          else
          oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
        }
      ');
    }  

              // headings
              $heading =  array('Art','Aufgabe','Verantwortlicher','Abgabe','geplant','gebucht','Status','Men&uuml;');
              $width   =  array('5%','25%','25%','3%','3%','3%','1%','10%');
              $findcols = array('art','aufgabe','name','abgabedatum','geplant','gebucht','status','id');
              $searchsql = array('adr.name','ap.aufgabe','ap.abgabedatum','ap.status');

                                $id = $this->app->Secure->GetGET("id");

             $menu = "<a href=\"index.php?module=projekt&action=arbeitspaketeditpopup&id=%value%&sid=$id\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DisableDialog(\"index.php?module=projekt&action=arbeitspaketdisable&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/versand.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=projekt&action=arbeitspaketdelete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=projekt&action=arbeitspaketcopy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>";

              // SQL statement
                                                                //'<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,
              $sql = "SELECT  SQL_CALC_FOUND_ROWS  ap.id, 
                                                                if(ap.abgenommen,CONCAT('<i>',UCASE(ap.art),'</i>'),UCASE(ap.art)) as art, 
                                                                if(ap.abgenommen,CONCAT('<i>',ap.aufgabe,'</i>'),ap.aufgabe) as aufgabe, 
                                                                if(ap.abgenommen,CONCAT('<i>',adr.name,'</i>'),adr.name) as name, 
                                                                if(ap.abgenommen,CONCAT('<i>',ap.abgabedatum,'</i>'),ap.abgabedatum) as abgabedatum, 
                                                                if(ap.abgenommen,CONCAT('<i>',ap.zeit_geplant,'</i>'),ap.zeit_geplant) as geplant, 
                                                                if(ap.abgenommen,CONCAT('<i>',

        (SELECT FORMAT(SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von)))/3600,2) FROM zeiterfassung z WHERE z.arbeitspaket=ap.id)


,'</i>'),

        (SELECT FORMAT(SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von)))/3600,2) FROM zeiterfassung z WHERE z.arbeitspaket=ap.id)

) as gebucht,
                                                                ap.status as status,
                                                                ap.id 
                  FROM arbeitspaket ap LEFT JOIN adresse adr ON ap.adresse=adr.id  ";
               
        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " OR ( ap.abgenommen='1')  ";

              for($j=0;$j<count($subwhere);$j++)
                $tmp .=  "  ".$subwhere[$j];

//              if($tmp!="")$tmp .= " AND e.geloescht='1' ";

              // Fester filter
              $where = "ap.projekt='$id' AND (ap.geloescht='0' OR ap.geloescht IS NULL) AND ap.abgenommen!='1'$tmp";

              // Fester filter
//            $where = "e.artikel='$id' AND e.geloescht='0' ";

              // gesamt anzahl
              $count = "SELECT COUNT(ap.id) FROM arbeitspaket ap WHERE ap.projekt='$id' AND (ap.geloescht='0' OR  ap.geloescht IS NULL)";

//                      $menucol = 6;
//      $moreinfo = true;
            break;



   case "einkaufspreise":
    $this->app->Tpl->Add(JQUERYREADY,"$('#alteeinkaufspreise').click( function() { fnFilterColumn1( 0 ); } );");

        $defaultorder = 4;  //Optional wenn andere Reihenfolge gewuenscht                
    $defaultorderdesc=0;

    for($r=1;$r<2;$r++)
    {
      $this->app->Tpl->Add(JAVASCRIPT,'
        function fnFilterColumn'.$r.' ( i )
        {
          if(oMoreData'.$r.$name.'==1)
          oMoreData'.$r.$name.' = 0;
          else
          oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
        }
      ');
    }  

              // headings
              $heading =  array('Lieferant','Bezeichnung','Bestellnummer','ab','VPE','Preis','W&auml;hrung','bis','Men&uuml;');
              $width   =  array('35%','35%','3%','3%','1%','1%','1%','1%','20%');
              $findcols = array('lieferant','bezeichnunglieferant','bestellnummer','ab_menge','vpe','preis','waehrung','gueltig_bis','id');
              $searchsql = array('adr.name','e.bezeichnunglieferant','e.bestellnummer','e.ab_menge','e.vpe');


             $menu = "<a href=\"index.php?module=artikel&action=einkaufeditpopup&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DisableDialog(\"index.php?module=artikel&action=einkaufdisable&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/disable.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=einkaufdelete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=einkaufcopy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>";

              // SQL statement
                                if($this->app->Conf->WFdbType=="postgre") {
              $sql = "SELECT e.id, adr.name as lieferant, e.bezeichnunglieferant, e.bestellnummer, 
      e.ab_menge as ab_menge ,e.vpe as vpe,e.preis as preis,e.waehrung as waehrung, 
CASE WHEN e.gueltig_bis IS NULL 
THEN NULL ELSE e.gueltig_bis END as gueltig_bis, 
e.id as menu
FROM  einkaufspreise e 
LEFT JOIN projekt p ON CAST(p.id as text)=e.projekt 
LEFT JOIN adresse adr ON e.adresse=adr.id ";
                                } else {
                                $sql = "SELECT SQL_CALC_FOUND_ROWS e.id, adr.name as lieferant, e.bezeichnunglieferant, e.bestellnummer, 
      e.ab_menge as ab_menge ,e.vpe as vpe,e.preis as preis,e.waehrung as waehrung, if(e.gueltig_bis='0000-00-00','-',e.gueltig_bis) as gueltig_bis, e.id as menu
      FROM  einkaufspreise e LEFT JOIN projekt p ON p.id=e.projekt LEFT JOIN adresse adr ON e.adresse=adr.id  ";
                                }              

        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " OR ( e.artikel='$id' AND e.gueltig_bis !='0000-00-00' AND e.gueltig_bis < NOW() AND e.geloescht=0)  ";

              for($j=0;$j<count($subwhere);$j++)
                $tmp .=  "  ".$subwhere[$j];

//              if($tmp!="")$tmp .= " AND e.geloescht='1' ";

              // Fester filter
                                if($this->app->Conf->WFdbType=="postgre")
              $where = "e.artikel='$id' AND e.geloescht='0' AND (e.gueltig_bis>CURRENT_DATE OR e.gueltig_bis IS NULL) $tmp";
                                else
                                $where = "e.artikel='$id' AND e.geloescht='0' AND (e.gueltig_bis>NOW() OR e.gueltig_bis='0000-00-00') $tmp";
              // Fester filter
//            $where = "e.artikel='$id' AND e.geloescht='0' ";

              // gesamt anzahl
              $count = "SELECT COUNT(e.id) FROM einkaufspreise e WHERE e.artikel='$id'  AND e.geloescht='0'";

            break;

  
            case "eigenschaften":

        $defaultorder = 1;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=0;

        $alignright = array(3,5,7);

        $heading =  array('Bezeichnung','Beschreibung','Wert','Einheit','Wert','Einheit','Wert','Einheit','Men&uuml;');
        $width   =  array('20%','20%','12%','8%','12%','8%','12%','8%','5%');
        $findcols = array('e.bezeichnung','e.beschreibung','e.wert','e.einheit','e.wert2','e.einheit2','e.wert3','e.einheit3','e.id');
        $searchsql = array('e.bezeichnung','e.beschreibung','e.wert','e.einheit','e.wert2','e.einheit2','e.wert3','e.einheit3');

              $menu = "<a href=\"index.php?module=artikel&action=eigenschafteneditpopup&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=eigenschaftendelete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS e.id, e.bezeichnung, e.beschreibung, CONCAT(e.wert,'&nbsp;&nbsp;'),
              e.einheit,CONCAT(e.wert2,'&nbsp;&nbsp;'),e.einheit2,CONCAT(e.wert3,'&nbsp;&nbsp;'),e.einheit3, e.id FROM eigenschaften e ";
             /* 
        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " OR (v.gueltig_bis !='0000-00-00' AND v.gueltig_bis < NOW() AND v.geloescht='0' AND v.artikel='$id')";

              for($j=0;$j<count($subwhere);$j++)
                $tmp .=  "  ".$subwhere[$j];
*/
//              if($tmp!="")$tmp .= " AND (v.gueltig_bis>NOW() OR v.gueltig_bis='0000-00-00') ";

              // Fester filter
              $where = "e.artikel='$id' ";

              // gesamt anzahl
              $count = "SELECT COUNT(e.id) FROM eigenschaften e WHERE e.artikel='$id' ";

            break;


            case "verkaufspreise":
                                  $this->app->Tpl->Add(JQUERYREADY,"$('#alteverkaufspreise').click( function() { fnFilterColumn1( 0 ); } );");

        $defaultorder = 3;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=0;


    for($r=1;$r<2;$r++)
    {
      $this->app->Tpl->Add(JAVASCRIPT,'
        function fnFilterColumn'.$r.' ( i )
        {
          if(oMoreData'.$r.$name.'==1)
          oMoreData'.$r.$name.' = 0;
          else
          oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
        }
      ');
    }
        $heading =  array('Kunde/Gruppe','Hinweis','ab','Preis','G&uuml;ltig bis','Men&uuml;');
        $width   =  array('40%','15%','10%','5%','10%','15%');
        $findcols = array('kunde','hinweis','ab_menge','preis','gueltig_bis','id');
        $searchsql = array('adr.name','g.name','v.ab_menge','v.gueltig_bis','v.preis');

              $menu = "<a href=\"index.php?module=artikel&action=verkaufeditpopup&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DisableDialog(\"index.php?module=artikel&action=verkaufdisable&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/disable.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikel&action=verkaufdelete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=artikel&action=verkaufcopy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS v.id, 
                                if(v.art='Kunde',if(v.adresse='' or v.adresse=0,'Standardpreis',CONCAT(adr.kundennummer,' ',adr.name)),CONCAT(g.name,' ',g.kennziffer)) as kunde,  
                                        if(v.adresse > 0 OR v.gruppe >0,'(Keine Rabatte m&ouml;glich)','') as hinweis,
                                v.ab_menge as ab_menge, v.preis as preis,v.gueltig_bis as gueltig_bis, v.id as menu
                  FROM  verkaufspreise v LEFT JOIN adresse adr ON v.adresse=adr.id  LEFT JOIN gruppen g ON g.id=v.gruppe ";
              
        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " OR (v.gueltig_bis !='0000-00-00' AND v.gueltig_bis < NOW() AND v.geloescht='0' AND v.artikel='$id')";

              for($j=0;$j<count($subwhere);$j++)
                $tmp .=  "  ".$subwhere[$j];

//              if($tmp!="")$tmp .= " AND (v.gueltig_bis>NOW() OR v.gueltig_bis='0000-00-00') ";

              // Fester filter
              $where = "v.artikel='$id' AND v.geloescht='0' AND (v.gueltig_bis>NOW() OR v.gueltig_bis='0000-00-00') $tmp";

              // gesamt anzahl
              $count = "SELECT COUNT(v.id) FROM verkaufspreise v WHERE v.artikel='$id' AND v.geloescht='0'";

            break;


            case "projektzeiterfassung":

                $this->app->Tpl->Add(JQUERYREADY,"$('#altearbeitspaket').click( function() { fnFilterColumn1( 0 ); } );");

    for($r=1;$r<2;$r++)
    {
      $this->app->Tpl->Add(JAVASCRIPT,'
        function fnFilterColumn'.$r.' ( i )
        {
          if(oMoreData'.$r.$name.'==1)
          oMoreData'.$r.$name.' = 0;
          else
          oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
        }
      ');
    }  

            // headings
            $heading =  array('','Art','Bezeichnung','Verantwortlicher','Abgabe','geplant','gebucht','Status','Men&uuml;');
              $width   =  array('1%','5%','25%','25%','3%','8%','3%','1%','10%');
              $findcols = array('open','art','bezeichnung','name','abgabedatum','geplant','gebucht','status','id');
              $searchsql = array('adr.name','ap.aufgabe','ap.abgabedatum','ap.status');

                                $id = $this->app->Secure->GetGET("id");

             $menu = "<a href=\"index.php?module=projekt&action=arbeitspaketeditpopup&id=%value%&sid=$id\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "<!--&nbsp;<a href=\"#\" onclick=DisableDialog(\"index.php?module=projekt&action=arbeitspaketdisable&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/versand.png\" border=\"0\"></a>-->".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=projekt&action=arbeitspaketdelete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=CopyDialog(\"index.php?module=projekt&action=arbeitspaketcopy&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/copy.png\" border=\"0\"></a>";

              // SQL statement
              $sql = "SELECT  SQL_CALC_FOUND_ROWS  ap.id, 
                                                                '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,
                                                                if(ap.abgenommen,CONCAT('<i>',UCASE(ap.art),'</i>'),UCASE(ap.art)) as art, 
                                                                if(ap.abgenommen,CONCAT('<i>',ap.aufgabe,'</i>'),ap.aufgabe) as 'Bezeichnung', 
                                                                if(ap.abgenommen,CONCAT('<i>',adr.name,'</i>'),adr.name) as name, 
                                                                if(ap.abgenommen,CONCAT('<i>',ap.abgabedatum,'</i>'),ap.abgabedatum) as abgabedatum, 
                                                                if(ap.abgenommen,CONCAT('<i>',if(ap.art='material',CONCAT(FORMAT(ap.kosten_geplant,2{$extended_mysql55}),' &euro;'),ap.zeit_geplant),'</i>'),
                                        if(ap.art='material' OR ap.kosten_geplant!=0,CONCAT(FORMAT(ap.kosten_geplant,2{$extended_mysql55}),' &euro;'),CONCAT(ap.zeit_geplant,' h'))) as geplant, 
                                                                if(ap.abgenommen,CONCAT('<i>',
                                (SELECT FORMAT(SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von)))/3600,2) FROM zeiterfassung z WHERE z.arbeitspaket=ap.id)

                        ,'</i>'),

                                (SELECT FORMAT(SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von)))/3600,2) FROM zeiterfassung z WHERE z.arbeitspaket=ap.id)

                                ) as gebucht,
                                                                ap.status as status,
                                                                ap.id 
                  FROM arbeitspaket ap LEFT JOIN adresse adr ON ap.adresse=adr.id  ";
               
        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " OR ( ap.abgenommen='1' AND ap.projekt='$id' AND (ap.geloescht='0' OR ap.geloescht IS NULL) )  ";

              for($j=0;$j<count($subwhere);$j++)
                $tmp .=  "  ".$subwhere[$j];

//              if($tmp!="")$tmp .= " AND e.geloescht='1' ";
//           FORMAT(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600,2) AS Dauer,


              // Fester filter
              $where = "ap.projekt='$id' AND (ap.geloescht='0' OR ap.geloescht IS NULL) AND ap.abgenommen!='1'$tmp";

              // Fester filter
//            $where = "e.artikel='$id' AND e.geloescht='0' ";

              // gesamt anzahl
              $count = "SELECT COUNT(ap.id) FROM arbeitspaket ap WHERE ap.projekt='$id' AND (ap.geloescht='0' OR  ap.geloescht IS NULL)";

                        $menucol = 8;
//      $moreinfo = true;
         
              $moreinfo = true;

            break;


            case "mlm_baum_cache":

        $this->app->Tpl->Add(JQUERYREADY,"$('#qualifiziert').click( function() { fnFilterColumn1( 0 ); } );");
        $this->app->Tpl->Add(JQUERYREADY,"$('#abfallenden').click( function() { fnFilterColumn2( 0 ); } );");

                for($r=1;$r<3;$r++)
                {
                $this->app->Tpl->Add(JAVASCRIPT,'
                function fnFilterColumn'.$r.' ( i )
                {
                if(oMoreData'.$r.$name.'==1)
                        oMoreData'.$r.$name.' = 0;
                else
                        oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
                }
        ');
         }
        // ENDE EXTRA checkboxen

              // headings
              //$heading =  array('','A','Datum','Von','Bis','Dauer','Mitarbeiter','Aufabe','Projekt','Men&uuml;');
        $heading =  array('','Vertriebspartner','Kennung','Min. Punkte','Punkte','Bonus Direkt','Bonus Stufe','Wartekonto','Gesamt','Qualifiziert','Aktiv','Men&uuml;');
              //$width   =  array('1%','1%','1%','1%','1%','5%','20%','40%','10%','1%');
              $width   =  array('1%','50%','30%','1%','1%','1%','1%','1%','1%','1%');
              //$findcols = array('open','Auswahl','z.von','von','bis','Dauer','Mitarbeiter','id');
        $findcols = array('open','a.name','kundennummer','mbc.mindestpunkte','mbc.punkte','mbc.bonuspunkte','mbc.bonuspunktekomplett','wartekonto','betrag','mbc.qualifiziert','mbc.mlmaktiv','id');
              $searchsql = array('a.name','kundennummer','mbc.punkte','mbc.bonuspunkte');

         $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=adresse&action=multilevel&id=%value%\" target=\"_blank\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;</td></tr></table>";

                                //CONCAT('<input type=\"checkbox\">') as auswahl,
              //$menucol=9;
              $menucol=11;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS mbc.id,
                '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,
                                                                a.name,
                                                        CONCAT(a.kundennummer,' (',if(a.mlmfestsetzen=1 AND (a.mlmfestsetzenbis > NOW() OR a.mlmfestsetzenbis IS NULL OR a.mlmfestsetzenbis='0000-00-00'),CONCAT(a.mlmpositionierung,'F'),(SELECT xy.positionierung FROM mlm_positionierung xy WHERE xy.adresse=a.id ORDER by xy.datum DESC LIMIT 1)),')') as kundennummer,
                mbc.mindestpunkte,mbc.punkte,mbc.bonuspunkte, mbc.bonuspunktekomplett,
                        (SELECT SUM(xz.betrag) FROM mlm_wartekonto xz WHERE xz.adresse=a.id AND xz.abgerechnet=0) as wartekonto,
                                                                gesamtauszahlung as betrag,
                                                                if(mbc.qualifiziert=1,'qualifiziert','nicht erreicht') as qualifiziert, if(mbc.aktiv='1','aktiv','nicht aktiv.') as aktiv,
                                                                mbc.lizenznehmer
                                                                FROM mlm_baum_cache mbc LEFT JOIN adresse a ON mbc.lizenznehmer=a.id";

              // Fester filter
        // START EXTRA more


        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " mbc.qualifiziert=1 ";
        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " mbc.gesamtauszahlung >= 50";

                                $sumcol=9;

        
        for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];

          $where =" mbc.mlm_abrechnung=0 ".$tmp;

//              $where = " z.id!='' AND z.adresse='".$this->app->User->GetAdresse()."' $tmp";
                $count = "SELECT COUNT(id) FROM mlm_baum_cache WHERE mlm_abrechnung='0'";

                      $moreinfo = true;
            break;




            case "zeiterfassungmitarbeiter":

                                // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#offen').click( function() { fnFilterColumn1( 0 ); } );");
        //$this->app->Tpl->Add(JQUERYREADY,"$('#abrechnung').click( function() { fnFilterColumn2( 0 ); } );");

                for($r=1;$r<2;$r++)
                {
                $this->app->Tpl->Add(JAVASCRIPT,'
                function fnFilterColumn'.$r.' ( i )
                {
                if(oMoreData'.$r.$name.'==1)
                        oMoreData'.$r.$name.' = 0;
                else
                        oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
                }
        ');
         }
        // ENDE EXTRA checkboxen

              // headings
              //$heading =  array('','A','Datum','Von','Bis','Dauer','Mitarbeiter','Aufabe','Projekt','Men&uuml;');
              $heading =  array('','Datum','Von','Bis','Dauer','Mitarbeiter','Aufgabe','Projekt','Men&uuml;');
              //$width   =  array('1%','1%','1%','1%','1%','5%','20%','40%','10%','1%');
              $width   =  array('1%','1%','1%','1%','5%','20%','40%','10%','1%');
              //$findcols = array('open','Auswahl','z.von','von','bis','Dauer','Mitarbeiter','id');
              $findcols = array('open','z.von','von','bis','Dauer','Mitarbeiter','id');
              $searchsql = array('z.id','z.bis','z.aufgabe','a.name');

                                $defaultorder=9;
                                $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=zeiterfassung&action=create&id=%value%&back=zeiterfassungmitarbeiter&sid=$id\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=zeiterfassung&action=listuser&do=stornieren&id=$id&lid=%value%&back=zeiterfassungmitarbeiter\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;</td></tr></table>";

                                //CONCAT('<input type=\"checkbox\">') as auswahl,
              //$menucol=9;
              $menucol=8;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS z.id,
                '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,

                                DATE_FORMAT(z.bis, GET_FORMAT(DATE,'EUR')) AS Datum, 
                                DATE_FORMAT(z.von,'%H:%i') as von, DATE_FORMAT(z.bis,'%H:%i') as bis,
        CONCAT(LPAD(HOUR(TIMEDIFF(z.bis, z.von)),2,'0'),':',LPAD(MINUTE(TIMEDIFF(z.bis, z.von)),2,'0')) AS Dauer,

                                a.name as Mitarbeiter,
                if(z.adresse_abrechnung!=0,CONCAT('<i>Kunde: ',b.name,' (',b.kundennummer,')</i><br>',z.aufgabe),z.aufgabe) as Taetigkeit,
                                p.abkuerzung,
                                z.id
        
                                FROM zeiterfassung z 
                                        LEFT JOIN adresse a ON a.id=z.adresse 
                                        LEFT JOIN adresse b ON b.id=z.adresse_abrechnung
                                        LEFT JOIN projekt p ON p.id=z.projekt 
                                        LEFT JOIN arbeitspaket ap ON z.arbeitspaket=ap.id";

              // Fester filter

        // START EXTRA more

        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " z.abrechnen='1' AND z.abgerechnet!='1' ";
//        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " a.datum=CURDATE() AND a.status='freigegeben'";


        for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];

                $where = " z.id!='' AND z.adresse='".$id."' $tmp";
                $count = "SELECT COUNT(z.id) FROM zeiterfassung z WHERE z.adresse='".$id."'";


                      $moreinfo = true;

            break;


            case "zeiterfassunguser":

                                // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#offen').click( function() { fnFilterColumn1( 0 ); } );");
        //$this->app->Tpl->Add(JQUERYREADY,"$('#abrechnung').click( function() { fnFilterColumn2( 0 ); } );");
                for($r=1;$r<2;$r++)
                {
                $this->app->Tpl->Add(JAVASCRIPT,'
                function fnFilterColumn'.$r.' ( i )
                {
                if(oMoreData'.$r.$name.'==1)
                        oMoreData'.$r.$name.' = 0;
                else
                        oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
                }
        ');
         }
        // ENDE EXTRA checkboxen

              // headings
              //$heading =  array('','A','Datum','Von','Bis','Dauer','Mitarbeiter','Aufabe','Projekt','Men&uuml;');
              $heading =  array('','Datum','Von','Bis','Dauer','Mitarbeiter','Aufgabe','Projekt','Men&uuml;');
              //$width   =  array('1%','1%','1%','1%','1%','5%','20%','40%','10%','1%');
              $width   =  array('1%','1%','1%','1%','5%','20%','40%','10%','1%');
              //$findcols = array('open','Auswahl','z.von','von','bis','Dauer','Mitarbeiter','id');
              $findcols = array('open','z.von','von','bis','Dauer','Mitarbeiter','id');
              $searchsql = array('z.id','z.bis','z.aufgabe','a.name',"if(z.adresse_abrechnung!=0,CONCAT('<i>Kunde: ',b.name,' (',b.kundennummer,')</i><br>',z.aufgabe),z.aufgabe)");

                                $defaultorder=7;
        $defaultorderdesc=1;



              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=zeiterfassung&action=create&id=%value%&back=zeiterfassunguser\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=zeiterfassung&action=listuser&do=stornieren&lid=%value%&back=zeiterfassunguser\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;</td></tr></table>";

                                //CONCAT('<input type=\"checkbox\">') as auswahl,
              //$menucol=9;
              $menucol=8;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS z.id,
                '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,

                                DATE_FORMAT(z.bis, GET_FORMAT(DATE,'EUR')) AS Datum, 
                                DATE_FORMAT(z.von,'%H:%i') as von, DATE_FORMAT(z.bis,'%H:%i') as bis,
        CONCAT(LPAD(HOUR(TIMEDIFF(z.bis, z.von)),2,'0'),':',LPAD(MINUTE(TIMEDIFF(z.bis, z.von)),2,'0')) AS Dauer,

                                a.name as Mitarbeiter,
                if(z.adresse_abrechnung!=0,CONCAT('<i>Kunde: ',b.name,' (',b.kundennummer,')</i><br>',z.aufgabe),z.aufgabe) as Taetigkeit,
                                p.abkuerzung,
                                z.id
        
                                FROM zeiterfassung z 
                                        LEFT JOIN adresse a ON a.id=z.adresse 
                                        LEFT JOIN adresse b ON b.id=z.adresse_abrechnung
                                        LEFT JOIN projekt p ON p.id=z.projekt 
                                        LEFT JOIN arbeitspaket ap ON z.arbeitspaket=ap.id";

              // Fester filter

        // START EXTRA more

        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " z.abrechnen='1' AND z.abgerechnet!='1' ";
//        $more_data2 = $this->app->Secure->GetGET("more_data2"); if($more_data2==1) $subwhere[] = " a.datum=CURDATE() AND a.status='freigegeben'";


        for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];

                $where = " z.id!='' AND z.adresse='".$this->app->User->GetAdresse()."' $tmp";
                $count = "SELECT COUNT(z.id) FROM zeiterfassung z WHERE z.adresse='".$this->app->User->GetAdresse()."'";


                      $moreinfo = true;

            break;




            case "lohnabrechnung":
                                // nach kunden sortiert
                                // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#offen').click( function() { fnFilterColumn1( 0 ); } );");
        //$this->app->Tpl->Add(JQUERYREADY,"$('#kunden').click( function() { fnFilterColumn2( 0 ); } );");

                for($r=1;$r<2;$r++)
                {
                $this->app->Tpl->Add(JAVASCRIPT,'
                function fnFilterColumn'.$r.' ( i )
                {
                if(oMoreData'.$r.$name.'==1)
                        oMoreData'.$r.$name.' = 0;
                else
                        oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
                }
        ');
         }

        // ENDE EXTRA checkboxen

              // headings
              //$heading =  array('','A','Datum','Von','Bis','Dauer','Mitarbeiter','Aufabe','Projekt','Men&uuml;');
              $heading =  array('','Mitarbeiter','Mitarbeiternr','Gesamt','Men&uuml;');
              //$width   =  array('1%','1%','1%','1%','1%','1%','5%','20%','40%','10%','1%');
              $width   =  array('1%','60%','10%','10%','10%');
              //$findcols = array('open','Auswahl','z.von','von','bis','Dauer','Mitarbeiter','id');
              $findcols = array('open','a.name','a.mitarbeiternummer','offen','id');
       $searchsql = array('a.name');

                                $defaultorder=5;
                                $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=adresse&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;</td></tr></table>";


      $von = $this->app->Secure->GetPOST("von");
      $bis = $this->app->Secure->GetPOST("bis");

                        if($von!=""){
        $von = $this->app->String->Convert($von,"%1.%2.%3","%3-%2-%1");  
        $bis = $this->app->String->Convert($bis,"%1.%2.%3","%3-%2-%1");  
                                $this->app->User->SetParameter("lohnabrechnung_von",$von);
                                $this->app->User->SetParameter("lohnabrechnung_bis",$bis);
                        }       

                        $von =  $this->app->User->GetParameter("lohnabrechnung_von");
                        $bis =  $this->app->User->GetParameter("lohnabrechnung_bis");

                                //CONCAT('<input type=\"checkbox\">') as auswahl,
              //$menucol=9;
              $menucol=4;
              // SQL statement
              $sql = "SELECT 
                                                                                                SQL_CALC_FOUND_ROWS z.id,
                '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,
                                                a.name,a.mitarbeiternummer,

FORMAT(SUM(UNIX_TIMESTAMP(bis) - UNIX_TIMESTAMP(von))/3600,2) as offen,

                                                z.adresse
                                                 FROM zeiterfassung z LEFT JOIN adresse a ON a.id=z.adresse ";
//SELECT SQL_CALC_FOUND_ROWS z.id, a.name,a.kundennummer, FORMAT(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600,2) as offen FROM zeiterfassung z  LEFT JOIN adresse a ON a.id=z.adresse_abrechnung WHERE z.abrechnen=1 AND z.ist_abgrechnet!=1 GROUP by z.adresse_abrechnung

              // Fester filter
        // START EXTRA more
/*
        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " z.abrechnen='1' AND z.abgerechnet!='1' ";

        for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];
*/
//              $where = " z.id!='' $tmp ";
                                                //$where = " z.abrechnen=1 AND z.ist_abgerechnet!=1 AND a.id > 0 ";
                                $where = " DATE_FORMAT(z.von,'%Y-%m-%d') >='$von' AND DATE_FORMAT(z.bis,'%Y-%m-%d') <='$bis' AND a.mitarbeiternummer!='' ";


                                        $groupby=" GROUP by z.adresse ";
                                // gesamt anzahl
                                        $count = "SELECT COUNT(z.id) FROM zeiterfassung z";


                      $moreinfo = true;

                        break;


// Administration-tables:
            case "userlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
              $heading =  array('Login','Beschreibung','Aktiv','Extern','Hardware','Men&uuml;');
              $width   =  array('40%','20%','20%','10%','10%','10%');
              $findcols = array('u.username','a.name','u.activ','u.externlogin','u.hwtoken','u.id'); //'a.name','a.kundennummer',"SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von)))/3600",'id');
        $searchsql = array('u.username','a.name','u.activ','u.externlogin','u.hwtoken');

        $defaultorder = 1;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=benutzer&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=benutzer&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS u.id, u.username as login,  a.name as beschreibung, if(u.activ,'ja','-') as aktiv,  if(u.externlogin,'erlaubt','-') as extern, if(u.hwtoken,u.hwtoken,'') as 'Hardware', u.id FROM user u LEFT JOIN adresse a ON a.id=u.adresse ";

                                $where = "";// z.abrechnen=1 AND z.abgerechnet!=1 AND a.id > 0 ";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                $count = "SELECT COUNT(id) FROM user";

                        break;

                        case "geschaeftsbrief_vorlagenlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Typ','Betreff','Projekt','Sprache','Men&uuml;');
              $width   =  array('10%','50%','20%','10%','10%');
              $findcols = array('g.subjekt','g.betreff','p.abkuerzung','g.sprache','g.id');
        $searchsql = array('g.subjekt','g.betreff','p.abkuerzung','g.sprache');

                $menucol=4;        
                                $defaultorder = 1;  //Optional wenn andere Reihenfolge gewuenscht                
        $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=geschaeftsbrief_vorlagen&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=geschaeftsbrief_vorlagen&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS g.id, g.subjekt as typ, g.betreff, if(g.projekt<=0,'Standard Vorlage / ohne Projekt',p.abkuerzung) as projekt, g.sprache, g.id FROM geschaeftsbrief_vorlagen g 
                                        LEFT JOIN projekt p ON g.projekt=p.id ";

                                $where = "g.firma='".$this->app->User->GetFirma()."' ".$this->app->erp->ProjektRechte();

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM geschaeftsbrief_vorlagen";

                        break;



                        case "emailbackuplist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('email','Benutzername','Server','Emailbackup','Ticket','Autoresponder','Men&uuml;');
              $width   =  array('20%','30%','20%','20%','10%','10%','10%');
              $findcols = array('email','benutzername','server','emailbackup','ticket','autoresponder','id');
        $searchsql = array('email','benutzername','server','emailbackup','ticket','autoresponder');

                                $defaultorder=2;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=emailbackup&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=emailbackup&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS e.id, e.email, e.benutzername, e.server, if(e.emailbackup,'Backup','') as backup, if(e.ticket,'Ticket','') as ticket, if(e.autoresponder,'Autoresponder','') as autoresponder, e.id FROM emailbackup e LEFT JOIN projekt p ON e.id=p.id ";
                                $where = "e.firma='".$this->app->User->GetFirma()."' AND e.geloescht=0 ".$this->app->erp->ProjektRechte();

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM emailbackup";

                        break;



                        case "ticket_vorlagenlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Vorlage','Projekt','Sichtbar','Men&uuml;');
              $width   =  array('30%','30%','20%','10%');
              $findcols = array('t.vorlagenname','p.abkuerzung','t.sichtbar','t.id');
        $searchsql = array('t.vorlagenname','p.abkuerzung','t.sichtbar');

                                $defaultorder=1;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=ticket_vorlage&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=ticket_vorlage&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS t.id, t.vorlagenname as vorlage, p.abkuerzung as projekt, if(t.sichtbar,'-','ja') as sichtbar, t.id FROM ticket_vorlage t LEFT JOIN projekt p ON t.id=p.id ";
                                
                                $where = "t.firma='".$this->app->User->GetFirma()."' AND t.id=p.id ".$this->app->erp->ProjektRechte();
                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM ticket_vorlage";

                        break;



                        case "warteschlangenlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Warteschlange','Label','Men&uuml;');
              $width   =  array('30%','20%','10%');
              $findcols = array('w.warteschlange','w.label','w.id');
        $searchsql = array('w.warteschlange','w.label');

                                $defaultorder=1;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=warteschlangen&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=warteschlangen&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS w.id, w.warteschlange, w.label, w.id FROM warteschlangen w ";
                                
                                $where = "";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM warteschlangen";

                        break;



                        case "artikeleinheitlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Einheit','Men&uuml;');
              $width   =  array('40%','20%');
              $findcols = array('a.einheit_de','a.id');
        $searchsql = array('a.einheit_de');

                                $defaultorder=1;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=artikeleinheit&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikeleinheit&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.einheit_de as einheit, a.id FROM artikeleinheit a ";

                                $where = "";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM artikeleinheit";

                        break;



                        case "gruppenlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Kennziffer','Bezeichnung','Art','Men&uuml;');
              $width   =  array('20%','30%','20%','10%');
              $findcols = array('g.kennziffer','g.name','g.art','g.id');
        $searchsql = array('g.kennziffer','g.name','g.art');

                                $defaultorder=2;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=gruppen&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=gruppen&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS g.id, g.kennziffer, g.name, g.art, g.id FROM gruppen g ";
                                
                                $where = "";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM gruppen";

                        break;



                        case "uservorlagelist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Bezeichnung','Men&uuml;');
              $width   =  array('30%','10%');
              $findcols = array('u.bezeichnung','u.id');
        $searchsql = array('u.bezeichnung');

                                $defaultorder=1;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=uservorlage&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=uservorlage&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS u.id, u.bezeichnung, u.id FROM uservorlage u ";
                                
                                $where = "";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM uservorlage";

                        break;


                        case "kassenbuecher":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Bezeichnung','Typ','Buchhaltung','Bestand','Men&uuml;');
              $width   =  array('50%','20%','10%','10%','5%');
              $findcols = array('k.bezeichnung','k.type','k.datevkonto','bestand','k.id');
        $searchsql = array('k.bezeichnung','k.type','k.datevkonto');

                                $alignright=array('4');

                                $defaultorder=2;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=kasse&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS k.id, k.bezeichnung, k.type as typ, if(k.datevkonto > 0, k.datevkonto,'-') as Buchhaltung, (SELECT ka.wert FROM kasse ka WHERE ka.konto=k.id ORDER by id DESC LIMIT 1) as bestand, k.id FROM konten k ";
                                
                                $where = "k.firma='".$this->app->User->GetFirma()."' AND k.type='kasse'";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM konten WHERE type='kasse'";

                        break;



                        case "kasse":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Nr.','Datum','Name/Verwendungszweck','Konto','Betrag','Steuer','Kassenstand','Men&uuml;');
              $width   =  array('1%','10%','40%','20%','10%','1%');
              $findcols = array('k.nummer','k.datum','k.grund','k.sachkonto','k.betrag','k.steuersatz','k.wert');
        $searchsql = array("k.nummer","DATE_FORMAT(k.datum,'%d.%m.%Y')",'k.grund','k.sachkonto','k.auswahl','k.betrag','k.wert','k.bearbeiter','a.kundennummer','a.name','k.steuersatz');

                                $defaultorder=1;
              $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=kasse&action=detail&id=$id&sid=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                //"<a href=\"#\" onclick=StornoDialog(\"index.php?module=kasse&action=storno&id=$id&sid=%value%\")>".
                                                                //"<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                                                                "</td></tr></table>";
                                //$menu ="<center>-</center>";

                                $alignright=array(5,6);
                                $sumcol = 5;
                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS k.id, 
                                                        k.nummer,
                                                        DATE_FORMAT(k.datum,'%d.%m.%Y'),
                                                        CONCAT(
                                                        k.grund, if(k.adresse > 0,CONCAT(' (',a.kundennummer,' ',a.name,')<br>'),'<br>'),if(k.storniert,
              CONCAT(' <b>Korrekturbuchung:</b> ',k.storniert_grund,'<br>'),'')),
                                                        CONCAT(k.sachkonto,' ',LEFT(ko.beschriftung,30)),
                                                        if(k.auswahl='einnahme',k.betrag,k.betrag*-1),
                                                        CONCAT(k.steuersatz,'%'),
                                                        k.wert,
                                                        k.id FROM kasse k 
                                                        LEFT JOIN adresse a ON a.id=k.adresse LEFT JOIN kontorahmen ko ON ko.sachkonto=k.sachkonto";
                                
                                $where = "k.firma='".$this->app->User->GetFirma()."' AND k.konto='$id'";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                $count = "SELECT COUNT(id) FROM kasse WHERE konto='$id'";

                        break;



                        case "kontenlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Bezeichnung','Typ','Buchhaltung','Men&uuml;');
              $width   =  array('40%','20%','40%','10%');
              $findcols = array('k.bezeichnung','k.type','k.datevkonto','k.id');
        $searchsql = array('k.bezeichnung','k.type','k.datevkonto');

                                $defaultorder=2;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=konten&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=konten&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS k.id, k.bezeichnung, k.type as typ, k.datevkonto as Buchhaltung, k.id FROM konten k ";
                                
                                $where = "k.firma='".$this->app->User->GetFirma()."'";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM konten";

                        break;



                        case "artikelkategorienlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Bezeichnung','N&auml;chste Nummer','Zentrale Nummer','Projekt','Men&uuml;');
              $width   =  array('20%','40%','10%','10%','10%');
              $findcols = array('k.bezeichnung','k.next_nummer','k.externenummer','p.abkuerzung','k.id');
              $searchsql = array('k.bezeichnung','k.next_nummer','p.abkuerzung');

                                $defaultorder=1;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=artikelkategorien&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikelkategorien&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS k.id, k.bezeichnung,k.next_nummer,if(k.externenummer,'ja','-'),p.abkuerzung, k.id FROM  artikelkategorien k 
                                    LEFT JOIN projekt p ON p.id=k.projekt ";

                                $where = " k.geloescht!=1 ";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM artikelkategorien WHERE geloescht!=1";

                        break;


                        case "etikettenlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Nummer','Verwenden als','Men&uuml;');
              $width   =  array('20%','40%','10%');
              $findcols = array('k.name','k.verwendenals','k.id');
              $searchsql = array('k.name','k.verwendenals');

                                $defaultorder=1;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=etiketten&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=etiketten&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS k.id, k.name,k.verwendenals, k.id FROM etiketten k ";

                                $where = "";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM etiketten";

                        break;


                        case "kontorahmenlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Nummer','Beschreibung','Men&uuml;');
              $width   =  array('20%','40%','10%');
              $findcols = array('k.sachkonto','k.beschriftung','k.id');
        $searchsql = array('k.sachkonto','k.beschriftung');


                                $defaultorder=1;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=kontorahmen&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=kontorahmen&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS k.id, 
                                                ".$this->Stroke("k.ausblenden","k.sachkonto").",
                                                ".$this->Stroke("k.ausblenden","k.beschriftung").",
                                                k.id FROM kontorahmen k ";

                                $where = "";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM kontorahmen";

                        break;


                        case "kostenstellenlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Nummer','Beschreibung','Men&uuml;');
              $width   =  array('20%','40%','10%');
              $findcols = array('k.nummer','k.beschreibung','k.id');
        $searchsql = array('k.nummer','k.beschreibung');


                                $defaultorder=1;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=kostenstellen&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=kostenstellen&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS k.id, k.nummer, k.beschreibung, k.id FROM kostenstellen k ";

                                $where = "";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM kostenstellen";

                        break;



                        case "verrechnungsartlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Nummer','Beschreibung','Men&uuml;');
              $width   =  array('10%','30%','10%');
              $findcols = array('v.nummer','v.beschreibung','v.id');
        $searchsql = array('v.nummer','v.beschreibung');

                                $defaultorder=1;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=verrechnungsart&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=verrechnungsart&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS v.id, v.nummer, v.beschreibung, v.id FROM verrechnungsart v ";

                                $where = "";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM verrechnungsart";

                        break;



                        case "reisekostenartlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Numer','Beschreibung','Men&uuml;');
              $width   =  array('10%','30%','10%');
              $findcols = array('r.nummer','r.beschreibung','r.id');
        $searchsql = array('r.nummer','r.beschreibung');

                                $defaultorder=1;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=reisekostenart&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=reisekostenart&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS r.id, r.nummer, r.beschreibung, r.id FROM reisekostenart r ";

                                $where = "";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM reisekostenart";

                        break;



                        case "onlineshopslist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Bezeichnung','Url','Projekt','Aktiv','Men&uuml;');
              $width   =  array('30%','20%','10%','10%','10%');
              $findcols = array('s.bezeichnung','s.url','p.name','s.aktiv','s.id');
        $searchsql = array('s.bezeichnung','s.url','p.name','s.aktiv');

                                $defaultorder=1;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=onlineshops&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=onlineshops&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, s.bezeichnung, s.url, p.name, if(s.aktiv,'ja','nein') as aktiv, s.id FROM shopexport s LEFT JOIN projekt p ON s.projekt=p.id";
                                
                                $where = "".$this->app->erp->ProjektRechte();

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM shopexport";

                        break;



                        case "artikelgruppenlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Artikelgruppe','Gruppe','Bezeichnung','Aktiv','Men&uuml;');
              $width   =  array('30%','10%','20%','10%','10%');
              $findcols = array('a.bezeichnung','a.id','s.bezeichnung','a.aktiv','a.id');
        $searchsql = array('a.bezeichnung','a.id','s.bezeichnung','a.aktiv');

                                $defaultorder=2;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=artikelgruppen&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=artikelgruppen&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.bezeichnung as artikelgruppe, a.id as gruppe, s.bezeichnung, if(a.aktiv,'online','') as aktiv, a.id FROM artikelgruppen a LEFT JOIN shopexport s ON a.shop=s.id ";                   

                                $where = "s.firma='".$this->app->User->GetFirma()."' AND s.id=a.shop";

                                        //$groupby=" GROUP by z.adresse_abrechnung ";

                                // gesamt anzahl
                                        $count = "SELECT COUNT(id) FROM artikelgruppen";

                        break;



                        case "prozessstarterlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Bezeichnung','Art','Periode','Aktiviert','Laeuft','Letzte Ausfuehrung','Typ','Parameter','Men&uuml;');
              $width   =  array('15%','10%','10%','10%','10%','20%','5%','10%','10%');
              $findcols = array('p.bezeichnung','p.art','p.periode','p.aktiv','p.mutex','p.letzteausfuerhung','p.typ','p.parameter','p.id');
        $searchsql = array('p.bezeichnung','p.art','p.periode','p.aktiv','p.mutex','p.letzteausfuerhung','p.typ','p.parameter');

                                $defaultorder=2;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=prozessstarter&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=prozessstarter&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS p.id, p.bezeichnung, p.art, p.periode, if(p.aktiv,'ja','-') as aktiviert, if(p.mutex,CONCAT('ja (Anzahl Versuche ',mutexcounter,')'),'-') as laeuft, p.letzteausfuerhung as 'letzte Ausf&uuml;hrung', p.typ, p.parameter, p.id FROM prozessstarter p ";

                                $where = "p.firma='".$this->app->User->GetFirma()."'";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(id) FROM prozessstarter";

                        break;



            case "aktionscodes_gutschrift":
              // headings
              $heading =  array('Gutschrift','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Zahlung','Status','Men&uuml;');
              //$width   =  array('1%','2%','5%','5%','50%','3%','3%','3%','3%','3%','3%','3%');
              $width   =  array('10%','10%','10%','35%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('belegnr','vom','kundennummer','kunde','land','projekt','zahlungsweise','soll','zahlung','status','id');
              $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')','r.belegnr','adr.kundennummer','r.name','r.land','p.abkuerzung','r.zahlungsweise','r.status',"FORMAT(r.soll,2{$extended_mysql55})",'r.zahlungsstatus','adr.freifeld1');

        $defaultorder = 11;  //Optional wenn andere Reihenfolge gewuenscht                
                                $defaultorderdesc=1;
                                $alignright= array('8');
                                $sumcol = 8;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=gutschrift&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=gutschrift&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=gutschrift&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=11;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS r.id, r.belegnr as belegnr, DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, 
              adr.kundennummer, ".$this->app->erp->MarkerUseredit("r.name","r.useredittimestamp")." as kunde, r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
             FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                  FROM  gutschrift r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";
              // Fester filter

                                $actionscode_code = $this->app->User->GetParameter("aktionscodes_code");
                                $actionscode_von = $this->app->User->GetParameter("aktionscodes_von");
                                $actionscode_bis = $this->app->User->GetParameter("aktionscodes_bis");

        // START EXTRA more
              $where = " r.aktion='$actionscode_code' AND r.datum >='$actionscode_von' AND r.datum <='$actionscode_bis' ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(r.id) FROM gutschrift r WHERE r.aktion='$actionscode_code' AND r.datum >='$actionscode_von' AND r.datum <='$actionscode_bis' ";

            break;


            case "aktionscodes_rechnung":
              // headings
              $heading =  array('Rechnung','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Zahlung','Status','Men&uuml;');
              //$width   =  array('1%','2%','5%','5%','50%','3%','3%','3%','3%','3%','3%','3%');
              $width   =  array('10%','10%','10%','35%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('belegnr','vom','kundennummer','kunde','land','projekt','zahlungsweise','soll','zahlung','status','id');
              $searchsql = array('DATE_FORMAT(r.datum,\'%d.%m.%Y\')','r.belegnr','adr.kundennummer','r.name','r.land','p.abkuerzung','r.zahlungsweise','r.status',"FORMAT(r.soll,2{$extended_mysql55})",'r.zahlungsstatus','adr.freifeld1');

        $defaultorder = 11;  //Optional wenn andere Reihenfolge gewuenscht                
                                $defaultorderdesc=1;
                                $alignright= array('8');
                                $sumcol = 8;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=rechnung&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=rechnung&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=11;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS r.id, r.belegnr as belegnr, DATE_FORMAT(r.datum,'%Y-%m-%d') as vom, 
              adr.kundennummer, ".$this->app->erp->MarkerUseredit("r.name","r.useredittimestamp")." as kunde, r.land as land, p.abkuerzung as projekt, r.zahlungsweise as zahlungsweise,  
             FORMAT(r.soll,2{$extended_mysql55}) as soll, r.zahlungsstatus as zahlung, UPPER(r.status) as status, r.id
                  FROM  rechnung r LEFT JOIN projekt p ON p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id  ";
              // Fester filter

                                $actionscode_code = $this->app->User->GetParameter("aktionscodes_code");
                                $actionscode_von = $this->app->User->GetParameter("aktionscodes_von");
                                $actionscode_bis = $this->app->User->GetParameter("aktionscodes_bis");

        // START EXTRA more
              $where = " r.aktion='$actionscode_code' AND r.datum >='$actionscode_von' AND r.datum <='$actionscode_bis' ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(r.id) FROM rechnung r WHERE r.aktion='$actionscode_code' AND r.datum >='$actionscode_von' AND r.datum <='$actionscode_bis' ";

            break;



            case "aktionscodes_auftrag":
              // headings
              $heading =  array('Auftrag','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','40%','5%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('open','belegnr','vom','kundennummer','name','land','projekt','zahlungsweise','betrag','status','id');
              $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')','a.belegnr','adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status',"FORMAT(a.gesamtsumme,2{$extended_mysql55})",'a.status','adr.freifeld1');

                        $defaultorder =10;  //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc=1;

        $sumcol=8;
                                $alignright= array('8');

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=auftrag&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=auftrag&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=auftrag&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.belegnr as belegnr, DATE_FORMAT(a.datum,'%Y-%m-%d') as vom, 
              adr.kundennummer as kundennummer, ".$this->app->erp->MarkerUseredit("a.name","a.useredittimestamp")." as name, a.land as land, p.abkuerzung as projekt, a.zahlungsweise as zahlungsweise,  
              FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag, UPPER(a.status) as status, a.id
                  FROM  auftrag a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              // Fester filter

                                $actionscode_code = $this->app->User->GetParameter("aktionscodes_code");
                                $actionscode_von = $this->app->User->GetParameter("aktionscodes_von");
                                $actionscode_bis = $this->app->User->GetParameter("aktionscodes_bis");

        // START EXTRA more
              $where = " a.aktion='$actionscode_code' AND a.datum >='$actionscode_von' AND a.datum <='$actionscode_bis' ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM auftrag a WHERE a.aktion='$actionscode_code' AND a.datum >='$actionscode_von' AND a.datum <='$actionscode_bis' ";

            break;



            case "aktionscodes_angebot":
              // headings
              $heading =  array('Angebot','Vom','Kd-Nr.','Kunde','Land','Projekt','Zahlung','Betrag','Status','Men&uuml;');
              $width   =  array('1%','10%','10%','40%','5%','1%','1%','1%','1%','1%','1%','1%');
              $findcols = array('belegnr','vom','kundennummer','name','land','projekt','zahlungsweise','betrag','status','id');
              $searchsql = array('DATE_FORMAT(a.datum,\'%d.%m.%Y\')','a.belegnr','adr.kundennummer','a.name','a.land','p.abkuerzung','a.zahlungsweise','a.status',"FORMAT(a.gesamtsumme,2{$extended_mysql55})",'a.status','adr.freifeld1');

                        $defaultorder =10;  //Optional wenn andere Reihenfolge gewuenscht
        $defaultorderdesc=1;

        $sumcol=8;
                                $alignright= array('8');

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=angebot&action=edit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=angebot&action=delete&id=%value%\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;<a href=\"index.php?module=angebot&action=pdf&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/pdf.png\" border=\"0\"></a></td></tr></table>";

              $menucol=10;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.belegnr as belegnr, DATE_FORMAT(a.datum,'%Y-%m-%d') as vom, 
              adr.kundennummer as kundennummer, ".$this->app->erp->MarkerUseredit("a.name","a.useredittimestamp")." as name, a.land as land, p.abkuerzung as projekt, a.zahlungsweise as zahlungsweise,  
              FORMAT(a.gesamtsumme,2{$extended_mysql55}) as betrag, UPPER(a.status) as status, a.id
                  FROM  angebot a LEFT JOIN projekt p ON p.id=a.projekt LEFT JOIN adresse adr ON a.adresse=adr.id  ";
              // Fester filter

                                $actionscode_code = $this->app->User->GetParameter("aktionscodes_code");
                                $actionscode_von = $this->app->User->GetParameter("aktionscodes_von");
                                $actionscode_bis = $this->app->User->GetParameter("aktionscodes_bis");

        // START EXTRA more
              $where = " a.aktion='$actionscode_code' AND a.datum >='$actionscode_von' AND a.datum <='$actionscode_bis' ".$this->app->erp->ProjektRechte();

              // gesamt anzahl
              $count = "SELECT COUNT(a.id) FROM angebot a WHERE a.aktion='$actionscode_code' AND a.datum >='$actionscode_von' AND a.datum <='$actionscode_bis' ";

            break;

                case "lagerbestandsberechnung":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Artikel-Nr.','Bezeichnung','Menge','Letzter Preis','Gesamt','Men&uuml;');
              $width   =  array('30%','30%','20%','10%','10%','10%','10%');
              $findcols = array('a.nummer','a.name_de','l.menge',"IFNULL((SELECT e.preis FROM einkaufspreise e WHERE e.geloescht!=1 AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis <=NOW()) AND e.artikel=l.artikel ORDER by e.preis LIMIT 1),0)","IFNULL((SELECT e.preis FROM einkaufspreise e WHERE e.geloescht!=1 AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis <=NOW()) AND e.artikel=l.artikel ORDER by e.preis LIMIT 1),0)*l.menge",'l.id');
        $searchsql = array('a.nummer','a.name_de');

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=artikel&action=lager&id=%value%\" target=\"_blank\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.png\" border=\"0\"></a>".
                                                                "</td></tr></table>";

                                $sumcol = 5;
                                $alignright= array('4','5');

                                // SQL statement

   $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, a.nummer, a.name_de, l.menge, FORMAT(IFNULL((SELECT e.preis FROM einkaufspreise e WHERE e.geloescht!=1 AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis <=NOW()) AND e.artikel=l.artikel ORDER by e.id DESC LIMIT 1),0),2{$extended_mysql55}) as preis, FORMAT(IFNULL((SELECT e.preis FROM einkaufspreise e WHERE e.geloescht!=1 AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis <=NOW()) AND e.artikel=l.artikel ORDER by e.id DESC LIMIT 1),0)*l.menge,2{$extended_mysql55}) as wert, a.id FROM lager_platz_inhalt l LEFT JOIN artikel a ON a.id=l.artikel ";
                                
                                $where = " a.id > 0 ";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(l.id) FROM lager_platz_inhalt l LEFT JOIN artikel a ON a.id=l.artikel WHERE a.id > 0";

                        break;

                case "aktionscodes_angebot_nummern":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Actionscode','Anzahl');
              $width   =  array('90%','10%');
              $findcols = array('a.aktion','COUNT(a.id)');
        $searchsql = array('a.aktion');
                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.aktion, COUNT(a.id) as anzahl FROM angebot a ";
                                
                                $where = " a.aktion!='' ";//d.firma='".$this->app->User->GetFirma()."'";
              $menu = "%value%";

                                $groupby=" GROUP by a.aktion ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(a.id) FROM angebot a WHERE a.aktion!='' GROUP by a.aktion";

                        break;

                case "aktionscodes_auftrag_nummern":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Actionscode','Anzahl');
              $width   =  array('90%','10%');
              $findcols = array('a.aktion','COUNT(a.id)');
        $searchsql = array('a.aktion');
                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.aktion, COUNT(a.id) as anzahl FROM auftrag a ";
                                
                                $where = " a.aktion!='' ";//d.firma='".$this->app->User->GetFirma()."'";
              $menu = "%value%";

                                $groupby=" GROUP by a.aktion ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(a.id) FROM auftrag a WHERE a.aktion!='' GROUP by a.aktion";

                        break;

                case "aktionscodes_rechnung_nummern":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Actionscode','Anzahl');
              $width   =  array('90%','10%');
              $findcols = array('a.aktion','COUNT(a.id)');
        $searchsql = array('a.aktion');
                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.aktion, COUNT(a.id) as anzahl FROM rechnung a ";
                                
                                $where = " a.aktion!='' ";//d.firma='".$this->app->User->GetFirma()."'";
              $menu = "%value%";

                                $groupby=" GROUP by a.aktion ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(a.id) FROM rechnung a WHERE a.aktion!='' GROUP by a.aktion";

                        break;

                case "aktionscodes_gutschrift_nummern":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Actionscode','Anzahl');
              $width   =  array('90%','10%');
              $findcols = array('a.aktion','COUNT(a.id)');
        $searchsql = array('a.aktion');
                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.aktion, COUNT(a.id) as anzahl FROM gutschrift a ";
                                
                                $where = " a.aktion!='' ";//d.firma='".$this->app->User->GetFirma()."'";
              $menu = "%value%";

                                $groupby=" GROUP by a.aktion ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(a.id) FROM gutschrift a WHERE a.aktion!='' GROUP by a.aktion";
                        break;



                case "protokoll":
        // START EXTRA checkboxen
                               // ENDE EXTRA checkboxen

              // headings
               $heading =  array('','ID','Zeit','Bearbeiter','Module','Action','Paramater','Funktion','Meldung','Men&uuml;');
              $width   =  array('4%','4%','15%','10%','10%','10%','10%','10%','40%','10%','5%');
              $findcols = array('open','a.id','a.datum','a.bearbeiter','a.module','a.action','a.paramater','a.funktionsname','a.meldung','a.id');
        $searchsql = array("DATE_FORMAT(a.datum,'%d.%m.%Y %H:%i:%s')",'a.bearbeiter','a.module','a.meldung','a.action','a.parameter','a.funktionsname');

                                $defaultorder=2;
              $defaultorderdesc=1;
							$menucol = 1;
							$moreinfo=true;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                              "<a href=\"index.php?%value%\" target=\"_blank\">".
                                                              "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                //                                              "&nbsp;".
//                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=logfile&action=delete&id=%value%\");>".
 //                                                               "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, a.id,DATE_FORMAT(a.datum,'%d.%m.%Y %H:%i:%s'), a.bearbeiter, 
										a.module, a.action, a.parameter, a.funktionsname,a.meldung,CONCAT('module=',a.module,'&action=',a.action,'&id=',a.parameter) FROM protokoll a";
                                
                                //$where = "d.firma='".$this->app->User->GetFirma()."'";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(a.id) FROM protokoll a";

                        break;



                case "logfile":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Zeit','Module','Action','Funktion','Meldung','Men&uuml;');
              $width   =  array('15%','10%','10%','10%','40%','10%');
              $findcols = array('a.datum','a.module','a.action','a.funktionsname','a.meldung','a.id');
        $searchsql = array("DATE_FORMAT(a.datum,'%d.%m.%Y %H:%i:%s')",'a.module','a.meldung','a.action','a.funktionsname');

                                $defaultorder=6;
              $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                        //                                      "<a href=\"index.php?module=drucker&action=edit&id=%value%\">".
                        //                                      "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                //                                              "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=logfile&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, DATE_FORMAT(a.datum,'%d.%m.%Y %H:%i:%s'), a.module, a.action, a.funktionsname,a.meldung, a.id FROM logfile a";
                                
                                //$where = "d.firma='".$this->app->User->GetFirma()."'";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(a.id) FROM logfile a";

                        break;


                case "adapterbox_log":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Zeit','IP','Seriennummer','Meldung','Men&uuml;');
              $width   =  array('15%','15%','15%','40%','10%');
              $findcols = array('a.datum','a.ip','a.seriennummer','a.meldung','a.id');
        $searchsql = array("DATE_FORMAT(a.datum,'%d.%m.%Y %H:%i:%s')",'a.ip','a.meldung','a.seriennummer');

                                $defaultorder=5;
              $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                        //                                      "<a href=\"index.php?module=drucker&action=edit&id=%value%\">".
                        //                                      "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                //                                              "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adapterbox&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, DATE_FORMAT(a.datum,'%d.%m.%Y %H:%i:%s'), a.ip, a.seriennummer, a.meldung, a.id FROM adapterbox_log a";
                                
                                //$where = "d.firma='".$this->app->User->GetFirma()."'";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(a.id) FROM adapterbox_log a";

                        break;

                case "exportvorlage":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Name','Ziel','Men&uuml;');
              $width   =  array('30%','30%','10%');
              $findcols = array('i.bezeichnung','i.ziel','i.id');
        $searchsql = array('i.ziel','i.bezeichnung');

//                              $defaultorder=2;
//            $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=exportvorlage&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"index.php?module=exportvorlage&action=export&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/download.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=exportvorlage&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS i.id, i.bezeichnung, i.ziel, i.id FROM exportvorlage i ";
                                
                                $where = "";//d.firma='".$this->app->User->GetFirma()."'";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(id) FROM exportvorlage";

                        break;


                case "importvorlage":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Name','Ziel','Men&uuml;');
              $width   =  array('30%','30%','10%');
              $findcols = array('i.bezeichnung','i.ziel','i.id');
        $searchsql = array('i.ziel','i.bezeichnung');

//                              $defaultorder=2;
//            $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=importvorlage&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"index.php?module=importvorlage&action=import&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/download.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=importvorlage&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS i.id, i.bezeichnung, i.ziel, i.id FROM importvorlage i ";
                                
                                $where = "";//d.firma='".$this->app->User->GetFirma()."'";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(id) FROM importvorlage";

                        break;


                case "mahnwesen_faellige":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('','Rechnung','Vom','KD-Nr.','Name','Betrag','Ist','Zahlweise','Status nach Mahnlauf','Versendet','Men&uuml;');
              $width   =  array('20px','50px','50px','50px','100px','50px','50px','50px','50px','50px','100px');
                                $findcols = array('r.id','r.belegnr',"r.datum",'a.kundennummer','a.name','r.soll','r.ist','r.zahlungsweise','r.mahnwesen','r.versendet_mahnwesen','r.id');
                                $searchsql = array('r.belegnr',"DATE_FORMAT(r.datum,'%d.%m.%Y')",'a.kundennummer','a.name','r.soll','r.ist','r.zahlungsweise','r.mahnwesen','r.versendet_mahnwesen');
             // $findcols = array('d.name','d.bezeichnung','d.aktiv','d.id');
       //       $searchsql = array('d.name','d.bezeichnung','d.aktiv');

                                $defaultorder=3;
              $defaultorderdesc=1;

              $menu = "".
                                                                "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>".
                                        "<a href=\"index.php?module=mahnwesen&action=mahnpdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>".
        "<a href=\"index.php?module=rechnung&action=pdf&id=%value%&doppel=1\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>".
 "<a onclick=DialogGutschrift(\"index.php?module=rechnung&action=gutschrift&id=%value%\")>".
        "<img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Gutschrift/Stornorechnung\"></a>".
  "<a onclick=DialogDifferenz(\"index.php?module=mahnwesen&action=skonto&id=%value%\")>".
        "<img src=\"./themes/new/images/skonto.png\" border=\"0\" alt=\"Differenz als Skonto buchen\"></a>".
  "<a onclick=DialogMahnwesen(\"index.php?module=mahnwesen&action=stop&id=%value%\")>".
        "<img src=\"./themes/new/images/stopmahnung.png\" border=\"0\" alt=\"Aus Mahnwesen nehmen\"></a>".
                                                                "&nbsp;";

                                if($enddatum=="") $enddatum = "0000-00-00";
                if($startdatum=="") $startdatum = "9999-99-99";
                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS r.id, if(r.datum < '$enddatum' AND r.datum > '$startdatum' $add_sql,CONCAT('<input class=\"chcktbl\" type=\"checkbox\" value=\"',r.id,'\" name=\"rechnung[]\" >'),CONCAT('<input class=\"chcktbl\" type=\"checkbox\" value=\"',r.id,'\" name=\"rechnung[]\" checked>')) as auswahl, CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\">',r.belegnr,'</a>') as rechnung, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',r.adresse,'\" target=\"_blank\">',a.kundennummer,'</a>') as 'kd-Nr.', LEFT(r.name,20) as name, FORMAT(r.soll,2{$extended_mysql55}) as betrag, FORMAT(r.ist,2{$extended_mysql55}) as ist, r.zahlungsweise, r.mahnwesen as 'Status nach Mahnlauf',if(r.versendet_mahnwesen,'ja','nein') as versendet, r.id
    FROM rechnung r LEFT JOIN adresse a ON a.id=r.adresse ";
                                
                                $where = " r.zahlungsstatus='offen' AND r.belegnr!=0 AND r.mahnwesen!=''  AND r.versendet_mahnwesen!='1' AND r.mahnwesen_gesperrt='0' AND r.mahnwesenfestsetzen!=1";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(r.id) FROM rechnung r WHERE $where";

                        break;

                case "mahnwesen_mahnung":
                case "mahnwesen_inkasso":
                case "mahnwesen_zahlungserinnerung":
                case "mahnwesen_forderungsverlust":
                case "mahnwesen_gesperrt":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

                                switch($name)
                                {
                                        case "mahnwesen_mahnung": $tmp = "(r.mahnwesen='mahnung1' OR r.mahnwesen='mahnung2' OR r.mahnwesen='mahnung3') AND r.zahlungsstatus='offen' AND r.versendet_mahnwesen='1' AND r.mahnwesen_gesperrt='0'"; break;
                                        case "mahnwesen_zahlungserinnerung": $tmp = " r.mahnwesen='zahlungserinnerung' AND r.zahlungsstatus='offen' AND r.versendet_mahnwesen='1' AND r.mahnwesen_gesperrt='0'"; break;
                                        case "mahnwesen_inkasso": $tmp = " r.mahnwesen='inkasso' AND r.zahlungsstatus='offen' AND r.versendet_mahnwesen='1' AND r.mahnwesen_gesperrt='0'"; break;
                                        case "mahnwesen_forderungsverlust": $tmp = " r.mahnwesen='forderungsverlust' "; break;
                                        case "mahnwesen_gesperrt": $tmp = " r.mahnwesen_gesperrt=1 AND r.mahnwesen!='forderungsverlust' AND r.zahlungsstatus!='bezahlt'"; break;
                                }

              // headings
                                $heading =  array('Rechnung','Vom','KD-Nr.','Name','Betrag','Ist','Zahlweise','Status','Versendet am','Men&uuml;');
              $width   =  array('5%','5%','5%','5%','5%','5%','5%','5%','5%','5%');
                                $findcols = array('r.belegnr',"r.datum",'a.kundennummer','a.name','r.soll','r.ist','r.zahlungsweise','r.mahnwesen','r.versendet_mahnwesen','r.id');
                                $searchsql = array('r.belegnr',"DATE_FORMAT(r.datum,'%d.%m.%Y')",'a.kundennummer','a.name','r.soll','r.ist','r.zahlungsweise','r.mahnwesen','r.versendet_mahnwesen');
             // $findcols = array('d.name','d.bezeichnung','d.aktiv','d.id');
       //       $searchsql = array('d.name','d.bezeichnung','d.aktiv');

                                $defaultorder=1;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=rechnung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/new/images/edit.png\"></a>".
                                        "<a href=\"index.php?module=mahnwesen&action=mahnpdf&id=%value%\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>".
        "<a href=\"index.php?module=rechnung&action=pdf&id=%value%&doppel=1\"><img border=\"0\" src=\"./themes/new/images/pdf.png\"></a>".
 "<a onclick=DialogGutschrift(\"index.php?module=rechnung&action=gutschrift&id=%value%\")>".
        "<img src=\"./themes/new/images/lieferung.png\" border=\"0\" alt=\"weiterf&uuml;hren als Gutschrift/Stornorechnung\"></a>".
  "<a onclick=DialogDifferenz(\"index.php?module=mahnwesen&action=skonto&id=%value%\")>".
        "<img src=\"./themes/new/images/skonto.png\" border=\"0\" alt=\"Differenz als Skonto buchen\"></a>".
  "<a onclick=DialogMahnwesen(\"index.php?module=mahnwesen&action=stop&id=%value%\")>".
        "<img src=\"./themes/new/images/stopmahnung.png\" border=\"0\" alt=\"Aus Mahnwesen nehmen\"></a>".
                                                                "&nbsp;</td></tr></table>";

                                if($enddatum=="") $enddatum = "0000-00-00";
                if($startdatum=="") $startdatum = "9999-99-99";
                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS r.id, CONCAT('<a href=\"index.php?module=rechnung&action=edit&id=',r.id,'\" target=\"_blank\">',r.belegnr,'</a>') as rechnung, DATE_FORMAT(r.datum,'%d.%m.%Y') as vom, CONCAT('<a href=\"index.php?module=adresse&action=edit&id=',r.adresse,'\" target=\"_blank\">',a.kundennummer,'</a>') as 'kd-Nr.', LEFT(r.name,20) as name, FORMAT(r.soll,2{$extended_mysql55}) as betrag, FORMAT(r.ist,2{$extended_mysql55}) as ist, r.zahlungsweise, r.mahnwesen as 'Status nach Mahnlauf',DATE_FORMAT(r.mahnwesen_datum,'%d.%m.%Y') as versendet, r.id
    FROM rechnung r LEFT JOIN adresse a ON a.id=r.adresse ";
                                
                                $where = " r.belegnr!=0 AND $tmp ";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(r.id) FROM rechnung r WHERE $where";

                        break;



                case "rma_list":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('KD-Nr.','Name','Artikel','Men&uuml;');
              $width   =  array('10%','30%','30%','10%');
              $findcols = array('a.kundennummer','a.name','COUNT(r.id)','a.id');
        $searchsql = array('a.name','a.kundennummer');

//dr.name, COUNT(rma.id) as artikel, adr.id FROM rma_artikel rma, artikel a, adresse adr 
//  WHERE rma.adresse=adr.id AND rma.artikel=a.id AND rma.status!='abgeschlossen' GROUP by adr.id

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=rma&action=detail&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.png\" border=\"0\"></a>".
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS r.id, a.kundennummer, a.name, COUNT(r.id), a.id FROM rma_artikel r LEFT JOIN adresse a ON a.id=r.adresse";
                                
                                $where = " r.status!='abgeschlossen' ";//d.firma='".$this->app->User->GetFirma()."'";

                                $groupby=" GROUP by r.adresse  ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(r.id) FROM rma_artikel r WHERE r.status!='abgeschlossen' GROUP by r.adresse ";

                        break;


                case "adresse_service":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Ticket','Datum','Betreff','Artikel-Nr.','Artikel','Status','Prio','Men&uuml;');
              $width   =  array('10%','15%','40%','5%','5%','5%','25%');
              $findcols = array('s.nummer','s.datum','s.betreff','art.nummer','art.name_de','s.status','s.prio','m.name','s.id');
        $searchsql = array('s.nummer','s.betreff',"DATE_FORMAT(s.datum,'%d.%m.%Y %H:%m Uhr')",'s.status','m.name','art.nummer','art.name_de','s.seriennummer');

//                              $defaultorder=2;
//            $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=service&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=service&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, s.nummer,DATE_FORMAT(s.datum,'%d.%m.%Y %H:%i Uhr'), s.betreff, art.nummer, 
                                        CONCAT(art.name_de,if(s.seriennummer!='',CONCAT('<br>SN: ',s.seriennummer),'')), 
                                                s.status, if(s.prio='hoch' or s.prio='notfall',CONCAT('<font color=red style=font-weight:bold>',s.prio,'</font>'),s.prio),  s.id FROM service s
                                                                LEFT JOIN adresse a ON a.id=s.adresse LEFT JOIN adresse m ON m.id=s.zuweisen LEFT JOIN artikel art ON art.id=s.artikel";
                                
                                $where = " s.status!='abgeschlossen' AND s.adresse='$id'";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(id) FROM service WHERE status!='abgeschlossen' AND adresse='$id'";

                        break;





                case "service_list_meine":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('','Ticket','Datum','Betreff','Kunde','Prio','Zugewiesen an','Men&uuml;');
              $width   =  array('1%','80','80','200','150','5%','25%','5%');
              $findcols = array('open','s.nummer','s.datum','s.betreff','a.name','s.prio','m.name','s.id');
        $searchsql = array('s.nummer','s.betreff',"DATE_FORMAT(s.datum,'%d.%m.%Y %H:%m Uhr')",'a.name','m.name');

//                              $defaultorder=2;
//            $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=service&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=service&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DialogAnfrageStart(\"index.php?module=service&action=start&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.png\" border=\"0\"></a>". 

                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, s.nummer,DATE_FORMAT(s.datum,'%d.%m.%Y %H:%i Uhr'), s.betreff, a.name, 

		if(s.prio='hoch' or s.prio='notfall',
					CONCAT('<font color=red style=font-weight:bold>',s.prio,'</font>'),s.prio), 
			m.name, s.id FROM service s
                                                                LEFT JOIN adresse a ON a.id=s.adresse LEFT JOIN adresse m ON m.id=s.zuweisen ";
                                
                                $where = " s.zuweisen='".$this->app->User->GetAdresse()."' AND s.status!='abgeschlossen' ";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(id) FROM service WHERE  zuweisen='".$this->app->User->GetAdresse()."' AND status!='abgeschlossen'";

                                $moreinfo=true;
                                $menucol=7;

                        break;


                case "service_list":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('','Ticket','Datum','Betreff','Kunde','Prio','Zugewiesen an','Men&uuml;');
              $width   =  array('1%','10%','15%','30%','20%','5%','25%','5%');
              $findcols = array('open','s.nummer','s.datum','s.betreff','a.name','s.prio','m.name','s.id');
        $searchsql = array('s.nummer','s.betreff',"DATE_FORMAT(s.datum,'%d.%m.%Y %H:%m Uhr')",'a.name','m.name');

//                              $defaultorder=2;
//            $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=service&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=service&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DialogAnfrageStart(\"index.php?module=service&action=start&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.png\" border=\"0\"></a>". 

                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open, s.nummer,DATE_FORMAT(s.datum,'%d.%m.%Y %H:%i Uhr'), s.betreff, a.name, if(s.prio='hoch' or s.prio='notfall',CONCAT('<font color=red style=font-weight:bold>',s.prio,'</font>'),s.prio), m.name, s.id FROM service s
                                                               LEFT JOIN adresse a ON a.id=s.adresse LEFT JOIN adresse m ON m.id=s.zuweisen ";
                                
                                $where = " s.status!='abgeschlossen' AND s.status!='gestartet' ";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(id) FROM service WHERE status!='abgeschlossen' AND status!='gestartet' ";

                                $moreinfo=true;
                                $menucol=7;

                        break;



                case "service_list_gestartet":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Ticket','Datum','Betreff','Kunde','Prio','Zugewiesen an','Men&uuml;');
              $width   =  array('10%','15%','30%','20%','5%','25%','5%');
              $findcols = array('s.nummer','s.datum','s.betreff','a.name','s.prio','m.name','s.id');
        $searchsql = array('s.nummer','s.betreff',"DATE_FORMAT(s.datum,'%d.%m.%Y %H:%m Uhr')",'a.name','m.name');

//                              $defaultorder=2;
//            $defaultorderdesc=0;


              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=service&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=service&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DialogAnfrageAbschluss(\"index.php?module=service&action=abschluss&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/forward.png\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, s.nummer,DATE_FORMAT(s.datum,'%d.%m.%Y %H:%i Uhr'), s.betreff, a.name, s.prio, m.name, s.id FROM service s
                                                                LEFT JOIN adresse a ON a.id=s.adresse LEFT JOIN adresse m ON m.id=s.zuweisen ";
                                
                                $where = " s.status='gestartet' ";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(id) FROM service WHERE status='gestartet' ";

                        break;

                case "service_list_abgeschlossen":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Ticket','Datum','Betreff','Kunde','Prio','Zugewiesen an','Men&uuml;');
              $width   =  array('10%','15%','30%','20%','5%','25%','5%');
              $findcols = array('s.nummer','s.datum','s.betreff','a.name','s.prio','m.name','s.id');
        $searchsql = array('s.nummer','s.betreff',"DATE_FORMAT(s.datum,'%d.%m.%Y %H:%m Uhr')",'a.name','m.name');

//                              $defaultorder=2;
//            $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=service&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=service&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, s.nummer,DATE_FORMAT(s.datum,'%d.%m.%Y %H:%i Uhr'), s.betreff, a.name, s.prio, m.name, s.id FROM service s
                                                                LEFT JOIN adresse a ON a.id=s.adresse LEFT JOIN adresse m ON m.id=s.zuweisen ";
                                
                                $where = " s.status='abgeschlossen' ";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(id) FROM service WHERE status='abgeschlossen' ";

                        break;


                case "druckerlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen

              // headings
                                $heading =  array('Name','Bezeichnung','Anbindung','Aktiv','Men&uuml;');
              $width   =  array('30%','30%','20%','10%','10%');
              $findcols = array('d.name','d.bezeichnung','d.anbindung','d.aktiv','d.id');
        $searchsql = array('d.name','d.bezeichnung','d.anbindung','d.aktiv');

                                $defaultorder=2;
              $defaultorderdesc=0;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=drucker&action=edit&id=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=drucker&action=delete&id=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>". 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS d.id, d.name, d.bezeichnung, 
                                        CONCAT(d.anbindung,if(d.adapterboxseriennummer='','',' SN:'),d.adapterboxseriennummer), if(d.aktiv,'ja','nein') as aktiv, d.id FROM drucker d ";
                                
                                $where = "d.firma='".$this->app->User->GetFirma()."'";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(id) FROM drucker";

                        break;

                        case "adresse_ansprechpartnerlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen
                                $id = $this->app->Secure->GetGET('id');
                                $lid = $this->app->Secure->GetGET("lid");
                                $iframe = $this->app->Secure->GetGET("iframe");

                                if($lid > 0)
                                        $id = $this->app->DB->Select("SELECT adresse FROM ansprechpartner WHERE id='$lid' LIMIT 1");


              // headings
                                $heading =  array('Name','Bereich','Email','Telefon','Telefax','Mobil','Men&uuml;');
              $width   =  array('20%','15%','15%','10%','10%','10%','10%','5%');
              $findcols = array('a.name','a.bereich','a.email','a.telefon','a.telefax','a.mobil','a.id');
        $searchsql = array('a.name','a.bereich','a.email','a.telefon','a.telefax','a.mobil');

                                $defaultorder=1;
              $defaultorderdesc=0;

        if($iframe=="true") $einfuegen = "<a onclick=AnsprechpartnerIframe(\"%value%\");><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/down.png\" border=\"0\"></a>";

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=adresse&action=ansprechpartner&edit&id=$id&iframe=$iframe&lid=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse&action=ansprechpartner&delete=1&id=$id&iframe=$iframe&lid=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".$einfuegen.
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.name, a.bereich, a.email, a.telefon, a.telefax, a.mobil, a.id FROM ansprechpartner a ";
                        
                                $where = "a.adresse='".$id."' AND a.name!='Neuer Datensatz' ";

                                //$orderby = "a.name,a.strasse";        
                                
                                //$orderby = "l.name, l.strasse";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(id) FROM ansprechpartner";

                        break;



                        case "adresse_lieferadressenlist":
        // START EXTRA checkboxen
                                // ENDE EXTRA checkboxen
                                $id = $this->app->Secure->GetGET("id");
                                $iframe = $this->app->Secure->GetGET("iframe");
                                $lid = $this->app->Secure->GetGET("lid");

                                if($lid > 0)
                                        $id = $this->app->DB->Select("SELECT adresse FROM lieferadressen WHERE id='$lid' LIMIT 1");

                                
              // headings
                                $heading =  array('Name','Strasse','Land','Plz','Ort','Email','Men&uuml;');
              $width   =  array('15%','20%','15%','5%','15%','20%','10%');
              $findcols = array('l.name','l.strasse','l.land','l.plz','l.ort','l.email','l.id');
        $searchsql = array('l.name','l.strasse','l.land','l.plz','l.ort','l.email');

                                $defaultorder=1;
              $defaultorderdesc=0;

//                              $id = $this->app->Secure->GetGET("sid");

        if($iframe=="true") $einfuegen = "<a onclick=LieferadresseIframe(\"%value%\");><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/down.png\" border=\"0\"></a>";

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap>".
                                                                "<a href=\"index.php?module=adresse&action=lieferadresse&edit&id=$id&iframe=$iframe&lid=%value%\">".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                                                                "&nbsp;".
                                                                "<a href=\"#\" onclick=DeleteDialog(\"index.php?module=adresse&action=lieferadresse&delete=1&id=$id&iframe=$iframe&lid=%value%\");>".
                                                                "<img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".$einfuegen. 
                                                                "&nbsp;</td></tr></table>";

                                // SQL statement
                                $sql = "SELECT SQL_CALC_FOUND_ROWS l.id, if(l.standardlieferadresse,CONCAT('<strong>',l.name,' (Standardlieferadresse)</strong>'),l.name) as name2, l.strasse, 
                                                l.land, l.plz, l.ort, l.email, l.id FROM lieferadressen l ";
                                
                                $where = " l.adresse='".$id."' AND l.name!='Neuer Datensatz' ";
                                
                                //$orderby = "l.name, l.strasse";

                                //$groupby=" GROUP by z.adresse_abrechnung ";

                        // gesamt anzahl
                                $count = "SELECT COUNT(id) FROM lieferadressen";

                        break;



            case "zeiterfassungkundenoffen":
                                // nach kunden sortiert
                                // START EXTRA checkboxen
        //$this->app->Tpl->Add(JQUERYREADY,"$('#offen').click( function() { fnFilterColumn1( 0 ); } );");
        //$this->app->Tpl->Add(JQUERYREADY,"$('#kunden').click( function() { fnFilterColumn2( 0 ); } );");
/*
                for($r=1;$r<3;$r++)
                {
                $this->app->Tpl->Add(JAVASCRIPT,'
                function fnFilterColumn'.$r.' ( i )
                {
                if(oMoreData'.$r.$name.'==1)
                        oMoreData'.$r.$name.' = 0;
                else
                        oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
                }
        ');
         }
*/
        // ENDE EXTRA checkboxen

              // headings
              //$heading =  array('','A','Datum','Von','Bis','Dauer','Mitarbeiter','Aufabe','Projekt','Men&uuml;');
              $heading =  array('Kunde','Kundennr','Offen','Men&uuml;');
              //$width   =  array('1%','1%','1%','1%','1%','5%','20%','40%','10%','1%');
              $width   =  array('60%','10%','10%','10%');
              //$findcols = array('open','Auswahl','z.von','von','bis','Dauer','Mitarbeiter','id');
              $findcols = array('a.name','a.kundennummer',"SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von)))/3600",'id');
       $searchsql = array('a.name');

                                $defaultorder=3;
        $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=adresse&action=abrechnungzeit&id=%value%\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;</td></tr></table>";


//CONCAT(CASE WHEN FLOOR(SUM(FORMAT(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600,2)))>0 THEN CONCAT(LPAD(FLOOR(SUM(FORMAT(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600,2))),2,'0'),':') ELSE '00:' END,LPAD(60*(SUM(FORMAT(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600,2))-FLOOR(SUM(FORMAT(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600,2)))),2,'0')) as offen,


                                //CONCAT('<input type=\"checkbox\">') as auswahl,
              //$menucol=9;
//            $menucol=8;
              // SQL statement
              $sql = "SELECT 'leer',
                                                a.name,a.kundennummer,
FORMAT(SUM(TIME_TO_SEC(TIMEDIFF(z.bis, z.von)))/3600,2) as offen,

a.id
                                                 FROM zeiterfassung z LEFT JOIN adresse a ON a.id=z.adresse_abrechnung ";
//SELECT SQL_CALC_FOUND_ROWS z.id, a.name,a.kundennummer, FORMAT(TIME_TO_SEC(TIMEDIFF(z.bis, z.von))/3600,2) as offen FROM zeiterfassung z  LEFT JOIN adresse a ON a.id=z.adresse_abrechnung WHERE z.abrechnen=1 AND z.ist_abgrechnet!=1 GROUP by z.adresse_abrechnung

              // Fester filter

        // START EXTRA more
/*
        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " z.abrechnen='1' AND z.abgerechnet!='1' ";

        for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];
*/
//              $where = " z.id!='' $tmp ";
                                                $where = " z.abrechnen=1 AND z.abgerechnet!=1 AND a.id > 0 ";

                                        $groupby=" GROUP by z.adresse_abrechnung ";
                                // gesamt anzahl
                                        $count = "SELECT COUNT(z.id) FROM zeiterfassung z";


//                    $moreinfo = true;

                        break;

            case "zeiterfassung":

                                // START EXTRA checkboxen
        $this->app->Tpl->Add(JQUERYREADY,"$('#offen').click( function() { fnFilterColumn1( 0 ); } );");
                for($r=1;$r<2;$r++)
                {
                $this->app->Tpl->Add(JAVASCRIPT,'
                function fnFilterColumn'.$r.' ( i )
                {
                if(oMoreData'.$r.$name.'==1)
                        oMoreData'.$r.$name.' = 0;
                else
                        oMoreData'.$r.$name.' = 1;

          $(\'#'.$name.'\').dataTable().fnFilter( 
          \'A\',
          i, 
          0,0
          );
                }
        ');
         }
        // ENDE EXTRA checkboxen

              // headings
              //$heading =  array('','A','Datum','Von','Bis','Dauer','Mitarbeiter','Aufabe','Projekt','Men&uuml;');
              $heading =  array('','Datum','Von','Bis','Dauer','Mitarbeiter','Aufgabe','Projekt','Men&uuml;');
              //$width   =  array('1%','1%','1%','1%','1%','5%','20%','40%','10%','1%');
              $width   =  array('1%','1%','1%','1%','5%','20%','40%','10%','1%');
              //$findcols = array('open','Auswahl','z.von','von','bis','Dauer','Mitarbeiter','id');
              $findcols = array('open',"z.von",'z.von','z.bis','Dauer','Mitarbeiter','Taetigkeit','p.abkuerzung','z.id');
              $searchsql = array('z.id','z.bis','z.aufgabe','a.name',"DATE_FORMAT(z.bis, GET_FORMAT(DATE,'EUR'))");

                                $defaultorder=2;
        $defaultorderdesc=1;

              $menu = "<table cellpadding=0 cellspacing=0><tr><td nowrap><a href=\"index.php?module=zeiterfassung&action=create&id=%value%&back=zeiterfassung\"><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>".
                  "&nbsp;<a href=\"#\" onclick=DeleteDialog(\"index.php?module=zeiterfassung&action=list&do=stornieren&lid=%value%&back=zeiterfassung\");><img src=\"themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\"></a>".
                  "&nbsp;</td></tr></table>";

                                //CONCAT('<input type=\"checkbox\">') as auswahl,
              //$menucol=9;
              $menucol=8;
              // SQL statement
              $sql = "SELECT SQL_CALC_FOUND_ROWS z.id,
                '<img src=./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/details_open.png class=details>' as open,

                                CONCAT('<!--',DATE_FORMAT(z.von,'%Y%m%d'),'-->',DATE_FORMAT(z.bis, GET_FORMAT(DATE,'EUR'))) AS Datum, 
                                DATE_FORMAT(z.von,'%H:%i') as von, DATE_FORMAT(z.bis,'%H:%i') as bis,
         CONCAT(LPAD(HOUR(TIMEDIFF(z.bis, z.von)),2,'0'),':',LPAD(MINUTE(TIMEDIFF(z.bis, z.von)),2,'0')) AS Dauer,

                                a.name as Mitarbeiter,
                if(z.adresse_abrechnung!=0,CONCAT('<i>Kunde: ',b.name,' (',b.kundennummer,')</i><br>',z.aufgabe),z.aufgabe) as Taetigkeit,
                                p.abkuerzung,
                                z.id
        
                                FROM zeiterfassung z 
                                        LEFT JOIN adresse a ON a.id=z.adresse 
                                        LEFT JOIN adresse b ON b.id=z.adresse_abrechnung
                                        LEFT JOIN projekt p ON p.id=z.projekt 
                                        LEFT JOIN arbeitspaket ap ON z.arbeitspaket=ap.id";

              // Fester filter

        // START EXTRA more

        $more_data1 = $this->app->Secure->GetGET("more_data1"); if($more_data1==1) $subwhere[] = " z.abrechnen='1' AND z.abgerechnet!='1' ";


        for($j=0;$j<count($subwhere);$j++)
          $tmp .=  " AND ".$subwhere[$j];

                $where = " z.id!='' $tmp";
                                // gesamt anzahl
                $count = "SELECT COUNT(z.id) FROM zeiterfassung z";


                      $moreinfo = true;

            break;



            default:
              break;
          }



          if($callback=="show")
          {

            $this->app->Tpl->Add(ADDITIONALCSS,"

.ex_highlight #$name tbody tr.even:hover, #example tbody tr.even td.highlighted {
  background-color: [TPLFIRMENFARBEHELL]; 
}

.ex_highlight_row #$name tr.even:hover {
  background-color: [TPLFIRMENFARBEHELL];
}

.ex_highlight_row #$name tr.even:hover td.sorting_1 {
  background-color: [TPLFIRMENFARBEHELL];
}

.ex_highlight_row #$name tr.odd:hover {
  background-color: [TPLFIRMENFARBEHELL];
}

.ex_highlight_row #$name tr.odd:hover td.sorting_1 {
  background-color: [TPLFIRMENFARBEHELL];
}
");

                      //"sPaginationType": "full_numbers",
                  //"aLengthMenu": [[10, 25, 50, 10000], [10, 25, 50, "All"]],


$this->app->Tpl->Add(JAVASCRIPT," var oTable".$name."; var oMoreData1".$name."=0; var oMoreData2".$name."=0; var oMoreData3".$name."=0; var oMoreData4".$name."=0; var oMoreData5".$name."=0;  var oMoreData6".$name."=0; var oMoreData7".$name."=0; var oMoreData8".$name."=0; var oMoreData9".$name."=0; var oMoreData10".$name."=0; var oMoreData11".$name."=0; var aData;
  ");

$smodule = $this->app->Secure->GetGET("cmd");


  if($this->app->Secure->GetGET("module")=="artikel")
  {
    $sort = '"aaSorting": [[ 0, "desc" ]],';
  } else {
    $sort = '"aaSorting": [[ 1, "desc" ]],';
  }

for($aligni=0;$aligni<count($alignright);$aligni++)
{
$this->app->Tpl->Add(YUICSS,'
#'.$name.' td:nth-child('.$alignright[$aligni].') {
    text-align: right;
}
');
}

                        $this->app->Tpl->Add(YUICSS,'
/*
 * Row highlighting example
 */
.ex_highlight #'.$name.' tbody tr.even:hover, #'.$name.' tbody tr.even td.highlighted {
  background-color: [TPLFIRMENFARBEHELL];
}
.ex_highlight #'.$name.' tbody tr.odd:hover, #'.$name.' tbody tr.odd td.highlighted {
  background-color: [TPLFIRMENFARBEHELL];
}
.ex_highlight_row #'.$name.' tr.even:hover {
  background-color: [TPLFIRMENFARBEHELL];
}
.ex_highlight_row #'.$name.' tr.even:hover td.sorting_1 {
  background-color: [TPLFIRMENFARBEHELL];
}
.ex_highlight_row #'.$name.' tr.even:hover td.sorting_2 {
  background-color: [TPLFIRMENFARBEHELL];
}
.ex_highlight_row #'.$name.' tr.even:hover td.sorting_3 {
  background-color: [TPLFIRMENFARBEHELL];
}
.ex_highlight_row #'.$name.' tr.odd:hover {
  background-color: [TPLFIRMENFARBEHELL];
}
.ex_highlight_row #'.$name.' tr.odd:hover td.sorting_1 {
  background-color: [TPLFIRMENFARBEHELL];
}
.ex_highlight_row #'.$name.' tr.odd:hover td.sorting_2 {
  background-color: #E0FF84;
}
.ex_highlight_row #'.$name.' tr.odd:hover td.sorting_3 {
  background-color: #DBFF70;
}
');


if($name=="artikeltabelle")
{
        //$js="alert($(nTds[0]).text()); //window.location.href='index.php?module=artikel&action=edit&nummer='+$(nTds[0]).text();";

} else $js="";

                        $anzahl_datensaetze = $this->app->erp->Firmendaten("standard_datensaetze_datatables");
                        if($anzahl_datensaetze !=0 && $anzahl_datensaetze!="10" && $anzahl_datensaetze!="25" && $anzahl_datensaetze!="50" && $anzahl_datensaetze!="200" &&$anzahl_datensaetze!="1000" && $anzahl_datensaetze!="")
                                        $extra_anzahl_datensaetze=$anzahl_datensaetze.",";
      else { 

                        if($anzahl_datensaetze > 0)
                        {       
                                //$extra_anzahl_datensaetze=$anzahl_datensaetze.",";
                                
                        } else {
                                $extra_anzahl_datensaetze=""; $anzahl_datensaetze="10";
                        }

                        }


                        if($sumcol >= 1)
                        {
                                $sumcol = $sumcol - 1;

                        $footercallback = '"footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === \'string\' ?
                    i.replace(/[\$,.]/g, \'\')*1 :
                    typeof i === \'number\' ?
                        i : 0;
            };
 
            // Total over all pages
            data = api.column( '.$sumcol.' ).data();
            total = data.length ?
                data.reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                } ) :
                0;
 
            // Total over this page
            data = api.column( '.$sumcol.', { page: \'current\'} ).data();
            pageTotal = data.length ?
                data.reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                } ) :
                0;

//                                      if(typeof pageTotal === \'int\')
                                        if(data.length > 1)
                                        {
                                        pageTotal = pageTotal / 100.0;  
                                        text = pageTotal.toString();

                                        var parts = text.toString().split(".");
                        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        showTotal =  parts.join(",");
                                        }
                                        else if(data.length > 0) { 
                                                pageTotal = pageTotal.replace(/,/, "A");
                                                pageTotal = pageTotal.replace(/A/, "\,");
                                                showTotal = pageTotal;
                                        }
                                        else showTotal = 0;

            $( api.column( '.$sumcol.' ).footer() ).html(
                \'<font color=red>&Sigma;&nbsp;\' + showTotal + \'</font>\' 
            );
        },
                                        ';
                        }
                        
if($name=="versandoffene") {
    $bStateSave="false";
    $cookietime=0; }
else {
  $cookietime=10*60;
    $bStateSave="true";
 }
                        $iframe = $this->app->Secure->GetGET("iframe");

            $this->app->Tpl->Add(DATATABLES,

'
var currentdate = new Date();
var datetime = "Stand: " + currentdate.getDay() + "."+currentdate.getMonth() 
+ "." + currentdate.getFullYear() + " um " 
+ currentdate.getHours() + ":" 
+ currentdate.getMinutes() + ":" + currentdate.getSeconds() + " von '.$this->app->User->GetName().'";

             oTable'.$name.' = $(\'#'.$name.'\').dataTable( {
                  "bProcessing": true,
        "iCookieDuration": '.$cookietime.', //60*60*24,// 1 day (in seconds)
                  "aLengthMenu": [['.$extra_anzahl_datensaetze.'10, 25, 50,200,1000], ['.$extra_anzahl_datensaetze.'10, 25, 50, 200,1000]],
                  "iDisplayLength": '.$anzahl_datensaetze.',
                      "bStateSave": '.$bStateSave.',
                        '.$sort.'
                  "bServerSide": true,
                        "dom": \'lfrtip<"clear spacer">T\',
        "tableTools": {
                                                        "aButtons": [
                                                                "copy",
                "csv",
                {
                    "sExtends": "pdf",
                    "sPdfOrientation": "landscape",
                                                                                "sPdfMessage": datetime
                }
                                        
            ],
            "sSwfPath": "./plugins/datatables/copy_csv_xls_pdf.swf"
        },

                "fnInitComplete": function (){
        $(oTable'.$name.'.fnGetNodes()).click(function (){
                                        var nTds = $(\'td\', this);
          '.$js.' //alert($(nTds[1]).text());// my js window....
        });},
'.$footercallback.'
        
    "fnServerData": function ( sSource, aoData, fnCallback ) {
      /* Add some extra data to the sender */
      aoData.push( { "name": "more_data1", "value": oMoreData1'.$name.' } );
      aoData.push( { "name": "more_data2", "value": oMoreData2'.$name.' } );
      aoData.push( { "name": "more_data3", "value": oMoreData3'.$name.' } );
      aoData.push( { "name": "more_data4", "value": oMoreData4'.$name.' } );
      aoData.push( { "name": "more_data5", "value": oMoreData5'.$name.' } );
      aoData.push( { "name": "more_data6", "value": oMoreData6'.$name.' } );
      aoData.push( { "name": "more_data7", "value": oMoreData7'.$name.' } );
      aoData.push( { "name": "more_data8", "value": oMoreData8'.$name.' } );
      aoData.push( { "name": "more_data9", "value": oMoreData9'.$name.' } );
      aoData.push( { "name": "more_data10", "value": oMoreData10'.$name.' } );
      aoData.push( { "name": "more_data11", "value": oMoreData11'.$name.' } );
      $.getJSON( sSource, aoData, function (json) { 
        /* Do whatever additional processing you want on the callback, then tell DataTables */
        fnCallback(json)
      } );
    },

                  "sAjaxSource": "./index.php?module=ajax&action=table&smodule='.$smodule.'&cmd='.$name.'&id='.$id.'&iframe='.$iframe.'"
                } );

              ');
if($moreinfo)
{

#auftraege > tbody:nth-child(2) > tr:nth-child(1) > td:nth-child(1) > img:nth-child(1)

$this->app->Tpl->Add(DATATABLES,
'
$(\'#'.$name.' tbody td img.details\').live( \'click\', function () {
    var nTr = this.parentNode.parentNode;
    aData =  oTable'.$name.'.fnGetData( nTr );

    if ( this.src.match(\'details_close\') )
    {
      /* This row is already open - close it */
      this.src = "./themes/'.$this->app->Conf->WFconf[defaulttheme].'/images/details_open.png";
      oTable'.$name.'.fnClose( nTr );
    }
    else
    {
      /* Open this row */
      this.src = "./themes/'.$this->app->Conf->WFconf[defaulttheme].'/images/details_close.png";
      oTable'.$name.'.fnOpen( nTr, '.$name.'fnFormatDetails(nTr), \'details\' );
    }
  });
');
/*  $.get("index.php?module=auftrag&action=minidetail&id=2", function(text){
    spin=0; 
    miniauftrag = text;
  });
*/

$module = $this->app->Secure->GetGET("module");

$this->app->Tpl->Add(JAVASCRIPT,'function '.$name.'fnFormatDetails ( nTr ) {
  //var aData =  oTable'.$name.'.fnGetData( nTr );
  var str = aData['.$menucol.'];
  var match = str.match(/[1-9]{1}[0-9]*/);

  var auftrag = parseInt(match[0], 10);

  var miniauftrag;
  var strUrl = "index.php?module='.$module.'&action=minidetail&id="+auftrag; //whatever URL you need to call
  var strReturn = "";

  jQuery.ajax({
    url:strUrl, success:function(html){strReturn = html;}, async:false
  });

  miniauftrag = strReturn;

  var sOut = \'<table cellpadding="0" cellspacing="0" border="0" align="center" style="padding-left: 30px; padding-right:30px; width:100%;">\';
  sOut += \'<tr><td>\'+miniauftrag+\'</td></tr>\';
  sOut += \'</table>\';
  return sOut;
}
');
  


}



      $colspan = count($heading);

      $this->app->Tpl->Add($parsetarget,'
        <br><br>
        <table cellpadding="0" cellspacing="0" border="0" style="width:" class="display" id="'.$name.'">
          <thead>
            <tr><th colspan="'.$colspan .'"><br></th></tr>
            <tr>');

        for($i=0;$i<count($heading);$i++)
        {
            $this->app->Tpl->Add($parsetarget,'<th width="'.$width[$i].'">'.$heading[$i].'</th>');
        }

      $this->app->Tpl->Add($parsetarget,'</tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="'.$colspan .'" class="dataTables_empty">Lade Daten</td>
            </tr>
          </tbody>

          <tfoot>
            <tr>
        ');


        for($i=0;$i<count($heading);$i++)
        {
            $this->app->Tpl->Add($parsetarget,'<th>'.$heading[$i].'</th>');
        }


        $this->app->Tpl->Add($parsetarget,'
            </tr>
          </tfoot>
        </table>
        <br>
        <br>
        <br>
        ');
            } else if ($callback=="sql")
              return $sql;
            else if ($callback=="searchsql")
              return $searchsql; 
            else if ($callback=="searchfulltext")
              return $searchfulltext; 
    else if ($callback=="defaultorder")
              return $defaultorder; 
            else if ($callback=="defaultorderdesc")
              return $defaultorderdesc; 
            else if ($callback=="heading")
              return $heading; 
           else if ($callback=="menu")
              return $menu; 
          else if ($callback=="findcols")
              return $findcols; 
                else if ($callback=="moreinfo")
              return $moreinfo; 
          else if ($callback=="where")
              return $where; 
          else if ($callback=="groupby")
              return $groupby; 
          else if ($callback=="count")
              return $count; 




  }



  function AutoCompleteAuftrag($fieldname,$filter,$onlyfirst=0,$extendurl="")
  {
                $module = $this->app->Secure->GetGET("module");
                $id = $this->app->Secure->GetGET("id");

                if($onlyfirst)
                {
                        $tpl ='
                                                        $( "#'.$fieldname.'" ).autocomplete({
                                                        source: "index.php?module=ajax&action=filter&filtername='.$filter.$extendurl.'&smodule='.$module.'&sid='.$id.'",
                                                        select: function( event, ui ) {
                                                                var i = ui.item.value;
                                                                var zahl = i.indexOf(" ");
                                                                var text = i.slice(0, zahl);
                                                                $( "#'.$fieldname.'" ).val( ui.item.value );
                                                                $( "#'.$fieldname.'" ).blur();
                                                                return false;
                                                                },
                                                        create: function () {
                        $(this).data(\'ui-autocomplete\')._renderItem = function (ul, item) {
                                                                        var suchstring = /(Aktuell kein Lagerbestand)/g;
                                                                        var suchergebnis = suchstring.test( item.label );
                                                                        if (suchergebnis != false)
                                                                        {
                                return $(\'<li>\')
                    .append(\'<a style="color:red">\' + item.label + \'</a>\')
                    .appendTo(ul);
                                                                        }
                                                                        else
                                                                        {
                                return $(\'<li>\')
                    .append(\'<a>\' + item.label + \'</a>\')
                    .appendTo(ul);
                                                                        }
            };
        }
                                                        });';
                } else {
                        //TODO
                        $tpl ='
                        $( "#'.$fieldname.'" ).autocomplete({
                                source: "index.php?module=ajax&action=filter&filtername='.$filter.$extendurl.'"
                        });';
                }

                $this->app->Tpl->Add(AUTOCOMPLETE,$tpl);
                $this->app->Tpl->Set(strtoupper($fieldname).START,'<div style="font-size: 8pt;"><div class="ui-widget" style="font-size: 8pt;">');
                $this->app->Tpl->Set(strtoupper($fieldname).ENDE,'</div></div>');
  }

  function AutoCompleteBestellung($fieldname,$filter,$onlyfirst=0,$extendurl="")
  {
                if($onlyfirst)
                {
                        $tpl ='
                                                        $( "#'.$fieldname.'" ).autocomplete({
                                                        source: "index.php?module=ajax&action=filter&filtername='.$filter.$extendurl.'",
                                                        select: function( event, ui ) {
                                                                var i = ui.item.value;
                                                                var zahl = i.indexOf(" ");
                                                                var text = i.slice(0, zahl);
                                                                $( "#'.$fieldname.'" ).val( ui.item.value );
                                                                $( "#'.$fieldname.'" ).blur();
                                                                return false;
                                                                }
                                                        });';
                } else {
                        $tpl ='

                        $( "#'.$fieldname.'" ).autocomplete({
                                source: "index.php?module=ajax&action=filter&filtername='.$filter.'"
                        });';
                }

                $this->app->Tpl->Add(AUTOCOMPLETE,$tpl);
                $this->app->Tpl->Set(strtoupper($fieldname).START,'<div style="font-size: 8pt;"><div class="ui-widget" style="font-size: 8pt;">');
                $this->app->Tpl->Set(strtoupper($fieldname).ENDE,'</div></div>');
  }


  function AutoCompleteAddCut($fieldname,$filter,$onlyfirst=0,$extendurl="")
  {
                if($onlyfirst)
                {
                        $tpl ='
                                                        $( "#'.$fieldname.'" ).autocomplete({
                                                        source: "index.php?module=ajax&action=filter&filtername='.$filter.$extendurl.'",
                                                        select: function( event, ui ) {
                                                                var j = ui.item.value;
                                                                var i = $( "#'.$fieldname.'" ).val()+ui.item.value;
                                                                var zahl = i.indexOf(",");
                                                                var zahl2 = j.indexOf(" ");
                                                                var text = i.slice(0, zahl);
                                                                var text2 = j.slice(0, zahl2);
                                                                if(zahl <=0)
                                                                $( "#'.$fieldname.'" ).val( text2 );
                                                                else {
                                                                var j = $( "#'.$fieldname.'" ).val();
                var zahlletzte = j.lastIndexOf(",");
                var text3 = j.substring(0,zahlletzte); 

                                                                $( "#'.$fieldname.'" ).val( text3 +","+ text2 );
                                                                }
                                                                return false;
                                                                }
                                                        });';
                } else {
                        $tpl ='
                                                        $( "#'.$fieldname.'" ).autocomplete({
                                                        source: "index.php?module=ajax&action=filter&filtername='.$filter.$extendurl.'",
                                                        select: function( event, ui ) {
                                                                var i = $( "#'.$fieldname.'" ).val()+ui.item.value;
                                                                var zahl = i.indexOf(",");
                                                
                                                                var text = i.slice(0, zahl);
                                                                if(zahl <=0)
                                                                $( "#'.$fieldname.'" ).val( ui.item.value );
                                                                else {
                                                                var j = $( "#'.$fieldname.'" ).val();
                                                                var zahlletzte = j.lastIndexOf(",");
                                                                var text2 = j.substring(0,zahlletzte); 
        
                                                                $( "#'.$fieldname.'" ).val( text2 + "," + ui.item.value );
                                                                }
                                                                return false;
                                                                }
                                                        });';
                }

                $this->app->Tpl->Add(AUTOCOMPLETE,$tpl);
                $this->app->Tpl->Set(strtoupper($fieldname).START,'<div style="font-size: 8pt;"><div class="ui-widget" style="font-size: 8pt;">');
                $this->app->Tpl->Set(strtoupper($fieldname).ENDE,'</div></div>');
  }



  function AutoCompleteAdd($fieldname,$filter,$onlyfirst=0,$extendurl="")
  {
                if($onlyfirst)
                {
                        $tpl ='
                                                        $( "#'.$fieldname.'" ).autocomplete({
                                                        source: "index.php?module=ajax&action=filter&filtername='.$filter.$extendurl.'",
                                                        select: function( event, ui ) {
                                                                var j = ui.item.value;
                                                                var i = $( "#'.$fieldname.'" ).val()+ui.item.value;
                                                                var zahl = i.indexOf(",");
                                                                var zahl2 = j.indexOf(" ");
                                                                var text = i.slice(0, zahl);
                                                                var text2 = j.slice(0, zahl2);
                                                                if(zahl <=0)
                                                                $( "#'.$fieldname.'" ).val( text2 );
                                                                else {
                                                                var j = $( "#'.$fieldname.'" ).val();
                var zahlletzte = j.lastIndexOf(",");
                var text3 = j.substring(0,zahlletzte); 

                                                                $( "#'.$fieldname.'" ).val( text3 +","+ text2 );
                                                                }
                                                                return false;
                                                                }
                                                        });';
                } else {
                        $tpl ='
                                                        $( "#'.$fieldname.'" ).autocomplete({
                                                        source: "index.php?module=ajax&action=filter&filtername='.$filter.$extendurl.'",
                                                        select: function( event, ui ) {
                                                                var i = $( "#'.$fieldname.'" ).val()+ui.item.value;
                                                                var zahl = i.indexOf(",");
                                                
                                                                var text = i.slice(0, zahl);
                                                                if(zahl <=0)
                                                                $( "#'.$fieldname.'" ).val( ui.item.value );
                                                                else {
                                                                var j = $( "#'.$fieldname.'" ).val();
                                                                var zahlletzte = j.lastIndexOf(",");
                                                                var text2 = j.substring(0,zahlletzte); 
        
                                                                $( "#'.$fieldname.'" ).val( text2 + "," + ui.item.value );
                                                                }
                                                                return false;
                                                                }
                                                        });';
                }

                $this->app->Tpl->Add(AUTOCOMPLETE,$tpl);
                $this->app->Tpl->Set(strtoupper($fieldname).START,'<div style="font-size: 8pt;"><div class="ui-widget" style="font-size: 8pt;">');
                $this->app->Tpl->Set(strtoupper($fieldname).ENDE,'</div></div>');
  }


  function AutoCompleteAddEvent($fieldname,$filter,$onlyfirst=0,$extendurl="")
  {
                if($onlyfirst)
                {
                        $tpl ='
                                                        $( "#'.$fieldname.'" ).autocomplete({
                                                        source: "index.php?module=ajax&action=filter&filtername='.$filter.$extendurl.'"
                                                        });';
                } else {
                        $tpl ='
                        $( "#'.$fieldname.'" ).autocomplete({
                                source: "index.php?module=ajax&action=filter&filtername='.$filter.$extendurl.'"
                        });';
                }

                $this->app->Tpl->Add(AUTOCOMPLETE,$tpl);
                $this->app->Tpl->Set(strtoupper($fieldname).START,'<div style="font-size: 8pt;"><div class="ui-widget" style="font-size: 8pt;">');
                $this->app->Tpl->Set(strtoupper($fieldname).ENDE,'</div></div>');
  }


  function AutoComplete($fieldname,$filter,$onlyfirst=0,$extendurl="")
  {
                if($onlyfirst)
                {
                        $tpl ='
                                                        $( "#'.$fieldname.'" ).autocomplete({
                                                        source: "index.php?module=ajax&action=filter&filtername='.$filter.$extendurl.'",
                                                        select: function( event, ui ) {
                                                                var i = ui.item.value;
                                                                var zahl = i.indexOf(" ");
                                                                var text = i.slice(0, zahl);
                                                                $( "#'.$fieldname.'" ).val( text );
                                                                return false;
                                                                }
                                                        });';
                } else {
                        $tpl ='

                        $( "#'.$fieldname.'" ).autocomplete({
                                source: "index.php?module=ajax&action=filter&filtername='.$filter.$extendurl.'",
                        });';
                }

                $this->app->Tpl->Add(AUTOCOMPLETE,$tpl);
                $this->app->Tpl->Set(strtoupper($fieldname).START,'<div style="font-size: 8pt;"><div class="ui-widget" style="font-size: 8pt;">');
                $this->app->Tpl->Set(strtoupper($fieldname).ENDE,'</div></div>');
  }

  function ChartDB($sql,$parsetarget,$width,$height,$limitmin=0,$limitmax=100,$gridy=5)
  {
    $result = $this->app->DB->SelectArr($sql);
    for($i=0;$i<count($result);$i++)
    {
      $lables[] = $result[$i]['legende'];
      $values[] = $result[$i]['wert'];
    }

    $values = array_reverse($values,false);
    $lables  = array_reverse($lables,false);

    $this->app->YUI->ChartAdd("#4040FF",$values);
    $this->app->YUI->Chart(TAB3,$lables,$width,$height,$limitmin,$limitmax,$gridy);

  }

  function Chart($parsetarget,$labels,$width=400,$height=200,$limitmin=0,$limitmax=100,$gridy=5)
  {
    $values = $labels;
     for($i=0;$i<count($values)-1;$i++)
     {
        $werte = $werte."'".$values[$i]."',";
     }
     $werte = $werte."'".$values[$i+1]."'";
     $this->app->Tpl->Set(LABELS,"[".$werte."]");

        $this->app->Tpl->Set(CHART_WIDTH,$width);
        $this->app->Tpl->Set(CHART_HEIGHT,$height);
      
        $this->app->Tpl->Set(LIMITMIN,$limitmin);
        $this->app->Tpl->Set(LIMITMAX,$limitmax);

        $this->app->Tpl->Set(GRIDX,count($values));
        $this->app->Tpl->Set(GRIDY,$gridy);

        $this->app->Tpl->Parse($parsetarget,"chart.tpl");
  }

  function ChartAdd($color,$values)
  {
     for($i=0;$i<count($values)-1;$i++)
     {
        $werte = $werte.$values[$i].",";
     }
     $werte = $werte.$values[$i+1];
     $this->app->Tpl->Add(CHARTS,"c.add('', '$color', [ $werte]);");
  } 


  function DateiUploadNeuVersion($parsetarget,$datei)
  {

    $speichern = $this->app->Secure->GetPOST("speichern");
    $module = $this->app->Secure->GetGET("module");
    $action = $this->app->Secure->GetGET("action");
    $id = $this->app->Secure->GetGET("id");

    if($speichern !="")
    {
      $titel= $this->app->Secure->GetPOST("titel");
      $beschreibung= $this->app->Secure->GetPOST("beschreibung");
      $stichwort= $this->app->Secure->GetPOST("stichwort");

      $this->app->Tpl->Set(TITLE,$titel);
      $this->app->Tpl->Set(BESCHREIBUNG,$beschreibung);

      if($_FILES['upload']['tmp_name']=="")
      {
        $this->app->Tpl->Set(ERROR,"<div class=\"info\">Bitte w&auml;hlen Sie eine Datei aus und laden Sie diese herauf!</div>");
                 $this->app->erp->EnableTab("tabs-2");


      } else {
        //$fileid = $this->app->erp->CreateDatei($_FILES['upload']['name'],$titel,$beschreibung,"",$_FILES['upload']['tmp_name'],$this->app->User->GetName());
                                $this->app->erp->AddDateiVersion($datei,$this->app->User->GetName(),$_FILES['upload']['name'], "Neue Version",$_FILES['upload']['tmp_name']);


        header("Location: index.php?module=$module&action=$action&id=$id");
                                exit;
      }

    }

    $this->app->Tpl->Set(STARTDISABLE,"<!--");
    $this->app->Tpl->Set(ENDEDISABLE,"-->");

    $this->app->Tpl->Parse($parsetarget,"datei_neudirekt.tpl");
  } 




  function DateiUpload($parsetarget,$objekt,$parameter)
  {

    $speichern = $this->app->Secure->GetPOST("speichern");
    $module = $this->app->Secure->GetGET("module");
    $action = $this->app->Secure->GetGET("action");
    $id = $this->app->Secure->GetGET("id");

    if($speichern !="")
    {
      $titel= $this->app->Secure->GetPOST("titel");
      $beschreibung= $this->app->Secure->GetPOST("beschreibung");
      $stichwort= $this->app->Secure->GetPOST("stichwort");

      $this->app->Tpl->Set(TITLE,$titel);
      $this->app->Tpl->Set(BESCHREIBUNG,$beschreibung);

      if($_FILES['upload']['tmp_name']=="")
      {
        $this->app->Tpl->Set(ERROR,"<div class=\"error\">Keine Datei ausgew&auml;hlt!</div>");
       $this->app->erp->EnableTab("tabs-2");

      } else {
        $fileid = $this->app->erp->CreateDatei($_FILES['upload']['name'],$titel,$beschreibung,"",$_FILES['upload']['tmp_name'],$this->app->User->GetName());

        // stichwoerter hinzufuegen
        $this->app->erp->AddDateiStichwort($fileid,$stichwort,$objekt,$parameter);
        header("Location: index.php?module=$module&action=$action&id=$id");
      }

    }

                if($objekt!="" && $parameter!="")
                {
        $table = new EasyTable($this->app);
        $table->Query("SELECT d.titel, s.subjekt, v.version, v.ersteller, v.bemerkung, d.id FROM datei d LEFT JOIN datei_stichwoerter s ON d.id=s.datei  
      LEFT JOIN datei_version v ON v.datei=d.id
      WHERE s.objekt='$objekt' AND s.parameter='$parameter' AND d.geloescht=0");

        $table->DisplayNew(INHALT,"<!--<a href=\"index.php?module=dateien&action=send&fid=%value%&ext=.jpg\"  rel=\"group\" class=\"zoom2\">
        <img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/vorschau.png\" border=\"0\"></a>-->
&nbsp;<a href=\"index.php?module=dateien&action=send&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/download.png\" border=\"0\"></a>&nbsp;
        <!--<a href=\"index.php?module=dateien&action=edit&id=%value%\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\" border=\"0\"></a>&nbsp;-->
        <a href=\"#\"onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=dateien&action=delete&id=%value%';\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\" border=\"0\" ></a>
        ");
                }

 
    $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");

    $this->app->Tpl->Parse(TAB2,"datei_neudirekt.tpl");


    $this->app->Tpl->Set(AKTIV_TAB1,"selected");
    $this->app->Tpl->Parse($parsetarget,"dateienuebersicht.tpl");

  } 


  function SortListAdd($parsetarget,&$ref,$menu,$sql,$sort=true)
  {
    $module = $this->app->Secure->GetGET("module");
    $id = $this->app->Secure->GetGET("id");

                $projekt = $this->app->DB->Select("SELECT projekt FROM `$module` WHERE id='$id' LIMIT 1");


                $schreibschutz = $this->app->DB->Select("SELECT schreibschutz FROM $module WHERE id='$id'");

    $table = new EasyTable($this->app);
    if($sort)
      $table->Query($sql." ORDER by sort"); 
    else
      $table->Query($sql); 


    // letzte zeile anzeigen

                


    if($module=="lieferschein")
    {
                if($schreibschutz!=1){
    $table->AddRow(array(
        '<form action="" method="post">',
        '[ARTIKELSTART]<input type="text" autofocus="autofocus" size="30" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',200);">[ARTIKELENDE]',
        '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()" >',
        '<input type="text" name="nummer" id="nummer" size="7">',
        '<input type="text" size="10" name="lieferdatum" id="lieferdatum">',
        '<input type="text" name="menge" id="menge" size="5" onblur="window.setTimeout(\'selectafterblurmenge()\',200);">',
        '<input type="hidden" name="preis" id="preis" size="5" onclick="checkhere();">',
        '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen">

<script type="text/javascript">
document.onkeydown = function(evt) {
    evt = evt || window.event;
    if (evt.keyCode == 27) {
        document.getElementById("artikel").focus();
                                document.getElementById("artikel").value="";
                                document.getElementById("projekt").value="";
                                document.getElementById("nummer").value="";
                                document.getElementById("lieferdatum").value="";
                                document.getElementById("menge").value="";
    }
};
</script>

</form>'));
    $this->app->YUI->AutoCompleteAuftrag("artikel","artikelnummer",1,"&projekt=$projekt");
}
    } 
    else if($module=="inventur")
    {
                if($schreibschutz!=1){
    $table->AddRow(array(
        '<form action="" method="post">',
        '[ARTIKELSTART]<input type="text" size="30" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',200);" [COMMONREADONLYINPUT]>[ARTIKELENDE]',
        '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()" >',
        '<input type="text" name="nummer" id="nummer" size="7">',
        '<input type="text" name="menge" id="menge" size="5" onblur="window.setTimeout(\'selectafterblurmenge()\',200);">',
        '<input type="text" name="preis" id="preis" size="5" onclick="checkhere();">',
        '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen">

<script type="text/javascript">
document.onkeydown = function(evt) {
    evt = evt || window.event;
    if (evt.keyCode == 27) {
        document.getElementById("artikel").focus();
                                document.getElementById("projekt").value="";
                                document.getElementById("artikel").value="";
                                document.getElementById("nummer").value="";
                                document.getElementById("menge").value="";
                                document.getElementById("preis").value="";
    }
};
</script>

</form>'));
    $this->app->YUI->AutoCompleteAddEvent("artikel","artikelnummer",1);
}
    } 
    else if($module=="anfrage")
    {
                if($schreibschutz!=1){
    $table->AddRow(array(
        '<form action="" method="post">',
        '[ARTIKELSTART]<input type="text" size="30" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',200);" [COMMONREADONLYINPUT]>[ARTIKELENDE]',
        '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()" >',
        '<input type="text" name="nummer" id="nummer" size="7">',
        '<input type="text" size="10" name="lieferdatum" id="lieferdatum">',
        '<input type="text" name="menge" id="menge" size="5" onblur="window.setTimeout(\'selectafterblurmenge()\',200);">',
        '<input type="hidden" name="preis" id="preis" size="5" onclick="checkhere();">',
        '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen">
<script type="text/javascript">
document.onkeydown = function(evt) {
    evt = evt || window.event;
    if (evt.keyCode == 27) {
        document.getElementById("artikel").focus();
                                document.getElementById("artikel").value="";
                                document.getElementById("nummer").value="";
                                document.getElementById("lieferdatum").value="";
                                document.getElementById("menge").value="";
                                document.getElementById("preis").value="";
    }
};
</script>
</form>'));
    $this->app->YUI->AutoCompleteAddEvent("artikel","artikelnummer",1,"&projekt=$projekt");
}
    } 

    else if ($module=="arbeitsnachweis") {
                if($schreibschutz!=1){
    $table->AddRow(array(
        '<form action="" method="post">',
        '[ADRESSESTART]<input type="text" size="20" name="adresse" id="adresse">[ADRESSEENDE]',
        '<input type="text" name="ort" id="ort" size="10">',
        '<input type="text" name="datum" id="datum" size="10">',
        '<input type="text" name="von" id="von" size="5">',
        '<input type="text" name="bis" id="bis" size="5">',
        '<input type="text" name="bezeichnung" id="bezeichnung" size="30">',
        '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen"> <input type="hidden" name="bezeichnunglieferant" id="bezeichnunglieferant"><input type="hidden" name="bestellnummer" id="bestellnummer">
<script type="text/javascript">
document.onkeydown = function(evt) {
    evt = evt || window.event;
    if (evt.keyCode == 27) {
        document.getElementById("adresse").focus();
                                document.getElementById("adresse").value="";
                                document.getElementById("ort").value="";
                                document.getElementById("datum").value="";
                                document.getElementById("von").value="";
                                document.getElementById("bis").value="";
                                document.getElementById("bezeichnung").value="";
    }
};
</script>
</form>'));

    $this->app->YUI->AutoCompleteAddEvent("adresse","mitarbeiter");
                        }
    }

    else if ($module=="reisekosten") {
                if($schreibschutz!=1){
    $table->AddRow(array(
        '<form action="" method="post">',
        '<input type="text" name="datum" id="datum" size="10">',
        '<select name="reisekostenart">'.$this->app->erp->GetSelectReisekostenart().'</select>',
        '<input type="text" name="betrag" id="betrag" size="8">',
        '<input type="checkbox" name="abrechnen" id="abrechnen" value="1">',
        '<input type="checkbox" name="keineust" id="keineust" value="1">',
        '<select name="uststeuersatz">'.$this->app->erp->GetSelectSteuersatz("",$id,"reisekosten").'</select>',
        '<input type="text" name="bezeichnung" id="bezeichnung" size="30">',
        '<select name="bezahlt_wie">'.$this->app->erp->GetSelectBezahltWie().'
                        </select>',
        '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen"> <input type="hidden" name="bezeichnunglieferant" id="bezeichnunglieferant"><input type="hidden" name="bestellnummer" id="bestellnummer">
<script type="text/javascript">
document.onkeydown = function(evt) {
    evt = evt || window.event;
    if (evt.keyCode == 27) {
        document.getElementById("datum").focus();
                                document.getElementById("datum").value="";
                                document.getElementById("betrag").value="";
                                document.getElementById("umsatzsteuer").value="";
                                document.getElementById("bezeichnung").value="";
    }
};
</script></form>'));

    $this->app->YUI->AutoCompleteAddEvent("adresse","mitarbeiter");
                        }
    }



    else if ($module=="produktion") {
                if($schreibschutz!=1){
    $table->AddRow(array(
        '<form action="" method="post">',
        '[ARTIKELSTART]<input type="text" size="30" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',200);" >[ARTIKELENDE]',
        '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()" >',
        '<input type="text" name="nummer" id="nummer" size="12">',
        '<input type="text" name="menge" id="menge" size="5" onblur="window.setTimeout(\'selectafterblurmenge()\',200);">',
        '',
        '',
        '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen"> <input type="hidden" name="bezeichnunglieferant" id="bezeichnunglieferant"><input type="hidden" name="bestellnummer" id="bestellnummer">
<script type="text/javascript">
document.onkeydown = function(evt) {
    evt = evt || window.event;
    if (evt.keyCode == 27) {
        document.getElementById("artikel").focus();
                                document.getElementById("nummer").value="";
                                document.getElementById("menge").value="";
    }
};
</script>
</form>'));
                }

    $this->app->YUI->AutoCompleteAddEvent("artikel","artikelnummer",1);
    }

    else if ($module=="bestellung") {
                if($schreibschutz!=1){
    $table->AddRow(array(
        '<form action="" method="post">',
        '[ARTIKELSTART]<input type="text" size="30" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',300);"  >[ARTIKELENDE]',
        '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()" >',
        '<input type="text" name="nummer" id="nummer" size="7">',
        '<input type="text" size="8" name="lieferdatum" id="lieferdatum">',
        '<input type="text" name="menge" id="menge" size="5" onblur="window.setTimeout(\'selectafterblurmenge()\',200);"><input type="hidden" name="vpe" id="vpe">',
        '<input type="text" name="preis" id="preis" size="10" onclick="checkhere();">',
        '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen"> <input type="hidden" name="bezeichnunglieferant" id="bezeichnunglieferant"><input type="hidden" name="bestellnummer" id="bestellnummer">
<script type="text/javascript">
document.onkeydown = function(evt) {
    evt = evt || window.event;
    if (evt.keyCode == 27) {
        document.getElementById("artikel").focus();
                                document.getElementById("artikel").value="";
                                document.getElementById("projekt").value="";
                                document.getElementById("nummer").value="";
                                document.getElementById("lieferdatum").value="";
                                document.getElementById("menge").value="";
                                document.getElementById("preis").value="";
                                document.getElementById("vpe").value="";
    }
};
</script>
</form>'));
                }

    $adresse = $this->app->DB->Select("SELECT adresse FROM bestellung WHERE id='$id' LIMIT 1");

    $this->app->YUI->AutoCompleteBestellung("artikel","einkaufartikelnummerprojekt",1,"&adresse=$adresse");
    }

    else if ($module=="angebot" || $module=="auftrag" ||$module=="rechnung" ||$module=="gutschrift") {
                if($schreibschutz!=1)
                {
    $table->AddRow(array(
        '<form action="" method="post" id="myform">',
        '[ARTIKELSTART]<input type="text" autofocus="autofocus" size="30" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',200);">[ARTIKELENDE]',
        '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()" >',
        '<input type="text" name="nummer" id="nummer" size="7">',
        '<input type="text" size="8" name="lieferdatum" id="lieferdatum">',
        '<input type="text" name="menge" id="menge" size="5" onblur="window.setTimeout(\'selectafterblur()\',1); document.getElementById(\'preis\').style.background =\'#FE2E2E\';">',
        '<input type="text" name="preis" id="preis" size="10" onclick="checkhere();">',
        '',
        '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen">
<script type="text/javascript">
document.onkeydown = function(evt) {
    evt = evt || window.event;
    if (evt.keyCode == 27) {
                                document.getElementById("artikel").focus();
                                document.getElementById("artikel").value="";
                                document.getElementById("projekt").value="";
                                document.getElementById("nummer").value="";
                                document.getElementById("lieferdatum").value="";
                                document.getElementById("menge").value="";
                                document.getElementById("preis").value="";
    }

    if (evt.keyCode == 160) { // pfeil rechts
                                document.getElementById("menge").focus();
                                document.getElementById("menge").select();
                }
    if (evt.keyCode == 39) { // pfeil rechts
                        //checkhere();
                        //document.getElementById("menge").focus();
                        //checkhere();
                }
};
</script></form>'));
    $adresse = $this->app->DB->Select("SELECT adresse FROM $module WHERE id='$id' LIMIT 1");
    $this->app->YUI->AutoCompleteAuftrag("artikel","verkaufartikelnummerprojekt",1,"&projekt=$projekt&adresse=$adresse");
                }
   }
    else {
                if($schreibschutz!=1)
                {
    $table->AddRow(array(
        '<form action="" method="post">',
        '[ARTIKELSTART]<input type="text" autofocus="autofocus" size="30" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',200);">[ARTIKELENDE]',
        '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()" >',
        '<input type="text" name="nummer" id="nummer" size="7">',
        '<input type="text" size="8" name="lieferdatum" id="lieferdatum">',
        '<input type="text" name="menge" id="menge" size="5" onblur="window.setTimeout(\'selectafterblurmenge()\',200);">',
        '<input type="text" name="preis" id="preis" size="10" onclick="checkhere();">',
        '<input type="submit" value="einf&uuml;gen" name="ajaxbuchen">
<script type="text/javascript">
document.onkeydown = function(evt) {
    evt = evt || window.event;
    if (evt.keyCode == 27) {
                                document.getElementById("artikel").focus();
                                document.getElementById("artikel").value="";
                                document.getElementById("projekt").value="";
                                document.getElementById("nummer").value="";
                                document.getElementById("lieferdatum").value="";
                                document.getElementById("menge").value="";
                                document.getElementById("preis").value="";
    }
};
</script></form>'));
    $adresse = $this->app->DB->Select("SELECT adresse FROM $module WHERE id='$id' LIMIT 1");
    $this->app->YUI->AutoCompleteAuftrag("artikel","verkaufartikelnummerprojekt",1,"&projekt=$projekt&adresse=$adresse");
                }
   }


    $table->headings[0]= 'Pos';
    $table->headings[1]= 'Artikel';
    $table->headings[2]= 'Projekt';
    $table->headings[3]= 'Nummer';
    $table->headings[4]= 'Lieferung';
    $table->headings[5]= 'Menge';

    if($module=="lieferschein" || $module=="anfrage")
                {
        $table->headings[6]= 'ausgeliefert';
                }
    else if($module=="inventur")
                {
        $table->headings[4]= 'Menge';
      $table->headings[5]= 'Preis';
                }
    else if ($module=="produktion")
                {
    $table->headings[2]= 'Projekt';
    $table->headings[3]= 'Nummer';
    $table->headings[4]= 'Menge';
    $table->headings[5]= 'Lager';
    $table->headings[6]= 'Reserviert';
                }
                else if ($module=="arbeitsnachweis")
                {
    $table->headings[0]= 'Pos';
    $table->headings[1]= 'Mitarbeiter';
    $table->headings[2]= 'Ort';
    $table->headings[3]= 'Datum';
    $table->headings[4]= 'Von';
    $table->headings[5]= 'Bis';
    $table->headings[6]= 'Tätigkeit';
                }
                else if ($module=="reisekosten")
                {
    $table->headings[0]= 'Pos';
    $table->headings[1]= 'Datum';
    $table->headings[2]= 'Kostenart';
    $table->headings[3]= 'Betrag';
    $table->headings[4]= 'Abr. bei Kd'; 
    $table->headings[5]= 'sonst. MwSt'; // kann man auch umbenennen in Keine
    $table->headings[6]= 'MwSt';
    $table->headings[7]= 'Kommentar';
    $table->headings[8]= 'Bezahlt';
                }
                else {
        $table->headings[6]= 'Preis';
                        if($module=="angebot" || $module=="auftrag" ||$module=="rechnung" ||$module=="gutschrift")
                $table->headings[7]= 'Rabatt';
                }       


    $table->widths[0]= '5%';
    $table->widths[1]= '25%';
    $table->widths[2]= '10%';
    $table->widths[3]= '10%';
    $table->widths[4]= '10%';
    $table->widths[5]= '10%';
    $table->widths[6]= '10%';

                if ($module=="produktion" || $module=="angebot" || $module=="auftrag" ||$module=="rechnung" ||$module=="gutschrift")
                        $table->widths[7]= '10%';


    $this->app->YUI->DatePicker("lieferdatum");
    $this->app->YUI->DatePicker("datum");
    $this->app->YUI->TimePicker("von");
    $this->app->YUI->TimePicker("bis");

    //$this->app->YUI->AutoComplete(ARTIKELAUTO,"artikel",array('name_de','warengruppe'),"nummer");

    if($module=="bestellung")
    $fillArtikel = "fillArtikelBestellung";
    elseif($module=="inventur")
    $fillArtikel = "fillArtikelInventur";
    elseif($module=="lieferschein" || $module=="anfrage")
    $fillArtikel = "fillArtikelLieferschein";
    elseif($module=="produktion")
    $fillArtikel = "fillArtikelProduktion";
    else
    $fillArtikel = "fillArtikel";


if($fillArtikel=="fillArtikelBestellung")
{

    $this->app->Tpl->Add($parsetarget,
'<script type="text/javascript">
var Tastencode;

var status=1;

var nureinmal=0;

function selectafterblurmenge()
{
      '.$fillArtikel.'(document.getElementById("nummer").value,document.getElementById("menge").value);
}


function selectafterblur()
{
//  if(document.getElementById("artikel").value))
  {
//      nureinmal=1;
      '.$fillArtikel.'(document.getElementById("artikel").value,document.getElementById("menge").value);
  }
}

function TasteGedrueckt (Ereignis) {
  if (!Ereignis)
    Ereignis = window.event;
  if (Ereignis.which) {
    Tastencode = Ereignis.which;
  } else if (Ereignis.keyCode) {
    Tastencode = Ereignis.keyCode;
  }
  //if((Tastencode=="9" || Tastencode=="13") && !isNaN(document.getElementById("artikel").value) )
  if((Tastencode=="9" || Tastencode=="13"))
  {
    '.$fillArtikel.'(document.getElementById("artikel").value,document.getElementById("menge").value);
    //document.myform.konto.focus();
    status=1;
  }
}
document.onkeydown = TasteGedrueckt;


function updatehere()
{
  //    '.$fillArtikel.'(document.getElementById("nummer").value);

}


function checkhere()
{
//var test = document.getElementById("artikel").value;
//if(!isNaN(test.substr(0,6)))
   // '.$fillArtikel.'(document.getElementById("nummer").value,document.getElementById("menge").value);

//if(!isNaN(test.substr(0,6))
//      fillArtikel(document.getElementById("artikel").value);
// wenn ersten 6 stellen nummer dann update
//if(!isNaN(document.getElementById("artikel").value))
//if(document.getElementById("artikel").value)
 //     fillArtikel(document.getElementById("artikel").value);

}

</script>

');
} else {


    $this->app->Tpl->Add($parsetarget,
'<script type="text/javascript">
var Tastencode;

var status=1;

var nureinmal=0;

function selectafterblurmenge()
{
      '.$fillArtikel.'(document.getElementById("nummer").value,document.getElementById("menge").value);
}

var oldvalue;
function selectafterblur()
{
 // if(nureinmal==0)// || !isNaN(document.getElementById("artikel").value))
//      if(document.getElementById("artikel")=="") nureinmal=0;

        if(document.getElementById("nummer").value!="" && nureinmal==1)
      '.$fillArtikel.'(document.getElementById("nummer").value+ " " +document.getElementById("artikel").value,document.getElementById("menge").value);
        else
      '.$fillArtikel.'(document.getElementById("artikel").value,document.getElementById("menge").value);
  
  nureinmal=1;
        if(oldvalue!=document.getElementById("artikel").value) nureinmal=0;
                        oldvalue=document.getElementById("artikel").value;
}

function TasteGedrueckt (Ereignis) {
  if (!Ereignis)
    Ereignis = window.event;
  if (Ereignis.which) {
    Tastencode = Ereignis.which;
  } else if (Ereignis.keyCode) {
    Tastencode = Ereignis.keyCode;
  }
  if((Tastencode=="9" || Tastencode=="13") && !isNaN(document.getElementById("artikel").value) )
  {
                if(document.getElementById("nummer").value!="")
    '.$fillArtikel.'(document.getElementById("artikel").value,document.getElementById("menge").value);
                else
    '.$fillArtikel.'(document.getElementById("nummer").value+ " " + document.getElementById("artikel").value,document.getElementById("menge").value);
    //document.myform.konto.focus();
    status=1;
  }
}
document.onkeydown = TasteGedrueckt;


function updatehere()
{
      '.$fillArtikel.'(document.getElementById("artikel").value);

}

function checkhere()
{
//var test = document.getElementById("artikel").value;
//if(!isNaN(test.substr(0,6)))
//      '.$fillArtikel.'(document.getElementById("artikel").value,document.getElementById("menge").value);

//if(!isNaN(test.substr(0,6))
//      fillArtikel(document.getElementById("artikel").value);
// wenn ersten 6 stellen nummer dann update
//if(!isNaN(document.getElementById("artikel").value))
//if(document.getElementById("artikel").value)
 //     fillArtikel(document.getElementById("artikel").value);

}

</script>

');



}
    //$this->app->YUI->AutoComplete(NUMMERAUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");


                if($schreibschutz!=1)
                {
    foreach($menu as $key=>$value)
    {

      // im popup öffnen
      if($key=="add")
        $tmp .= "<a href=\"index.php?module=$module&action=$value&id=%value%&frame=false&pid=$id\" 
          onclick=\"makeRequest(this);return false\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/new.png\"></a>&nbsp;";
      else if($key=="del")
        $tmp .= "<a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=$module&action=$value&sid=%value%&id=$id';\" href=\"#\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\"></a>&nbsp;";
      else if($key=="edit")
        $tmp .= "<a href=\"index.php?module=$module&action=$value&id=%value%&frame=false&pid=$id\" 
        class=\"popup\" title=\"Artikel &auml;ndern\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/edit.png\"></a>&nbsp;";

      // nur aktion ausloesen und liste neu anzeigen
      else
                                $tmp .= "<a href=\"index.php?module=$module&action=$value&sid=%value%&id=$id\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/$key.png\"></a>&nbsp;";
    }
        $table->DisplayEditable($parsetarget, $tmp);
                }
                else 
        $table->DisplayNew($parsetarget, $tmp);
  }
/*
  function SortListAddProduktion($parsetarget,&$ref,$menu,$sql,$sort=true,$adresse)
  {
    $module = $this->app->Secure->GetGET("module");
    $id = $this->app->Secure->GetGET("id");

    $table = new able(&$this->app);
    if($sort)
      $table->Query($sql." ORDER by sort"); 
    else
      $table->Query($sql); 


    // letzte zeile anzeigen
    $table->AddRow(array(
        '<form action="" method="post">[ARTIKELAUTOSTART]<input type="text" size="20" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',200);" >[ARTIKELAUTOEND]',
        '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()">',
        '<input type="text" name="nummer" id="nummer" size="10" readonly onclick="checkhere()">',
        '<input type="text" size="10" name="lieferdatum" onclick="checkhere()">',
        '<input type="text" name="menge" id="menge" size="5" onclick="checkhere()">',
        '<input type="text" name="preis" id="preis" size="7" onclick="checkhere()">',
        '<input type="submit" value="buchen" name="ajaxbuchen"></form>'));

    $table->headings[0]= '<table width=200 cellpadding=0 cellspacing=0><tr><td>Artikel</td></tr></table>';
    $table->headings[1]= 'Projekt';
    $table->headings[2]= 'Nummer';
    $table->headings[3]= 'Lieferdatum';
    $table->headings[4]= 'Menge';
    $table->headings[5]= 'Preis';

    $this->app->YUI->AutoComplete(ARTIKELAUTO,"artikel",array('name_de','warengruppe'),"id");

    $this->app->Tpl->Add($parsetarget,
'<script type="text/javascript">
var Tastencode;

var status=1;

var nureinmal=0;

function selectafterblur()
{
  if(nureinmal==0 || !isNaN(document.getElementById("artikel").value))
  {
      nureinmal=1;
      fillArtikelBestellung(document.getElementById("artikel").value,'.$adresse.');
  }
}

function TasteGedrueckt (Ereignis) {
  if (!Ereignis)
    Ereignis = window.event;
  if (Ereignis.which) {
    Tastencode = Ereignis.which;
  } else if (Ereignis.keyCode) {
    Tastencode = Ereignis.keyCode;
  }
  if((Tastencode=="9" || Tastencode=="13") && !isNaN(document.getElementById("artikel").value) )
  {
    fillArtikelBestellung(document.getElementById("artikel").value,'.$adresse.');
    //document.myform.konto.focus();
    status=1;
  }
}
document.onkeydown = TasteGedrueckt;


function checkhere()
{
if(!isNaN(document.getElementById("artikel").value))
      fillArtikel(document.getElementById("artikel").value);

}

</script>

');

    //$this->app->YUI->AutoComplete(NUMMERAUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");


    foreach($menu as $key=>$value)
    {

      // im popup öffnen
      if($key=="add")
        $tmp .= "<a href=\"index.php?module=$module&action=$value&id=%value%&frame=false&pid=$id\" 
          onclick=\"makeRequest(this);return false\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/new.png\"></a>&nbsp;";
      else if($key=="del")
        $tmp .= "<a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=$module&action=$value&sid=%value%&id=$id';\" href=\"#\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\"></a>&nbsp;";
      else if($key=="edit")
        $tmp .= "<a href=\"index.php?module=$module&action=$value&id=%value%&frame=false&pid=$id\" 
        onclick=\"makeRequest(this);return false\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/$key.png\"></a>&nbsp;";

      // nur aktion ausloesen und liste neu anzeigen
      else
        $tmp .= "<a href=\"index.php?module=$module&action=$value&sid=%value%&id=$id\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/$key.png\"></a>&nbsp;";
    }
    $table->DisplayNew($parsetarget, $tmp);
  }


  function SortListAddBestellung($parsetarget,&$ref,$menu,$sql,$sort=true,$adresse)
  {
    $module = $this->app->Secure->GetGET("module");
    $id = $this->app->Secure->GetGET("id");

    $table = new Table($this->app);
    if($sort)
      $table->Query($sql." ORDER by sort"); 
    else
      $table->Query($sql); 


    // letzte zeile anzeigen
    $table->AddRow(array(
        '<form action="" method="post">[ARTIKELAUTOSTART]<input type="text" size="20" name="artikel" id="artikel" onblur="window.setTimeout(\'selectafterblur()\',200);" >[ARTIKELAUTOEND]',
        '<input type="text" name="projekt" id="projekt" size="10" readonly onclick="checkhere()">',
        '<input type="text" name="nummer" id="nummer" size="10" readonly onclick="checkhere()">',
        '<input type="text" size="10" name="lieferdatum" onclick="checkhere()">',
        '<input type="text" name="menge" id="menge" size="5" onclick="checkhere()">',
        '<input type="text" name="preis" id="preis" size="7" onclick="checkhere()">',
        '<input type="submit" value="buchen" name="ajaxbuchen"></form>'));

    $table->headings[0]= '<table width=200 cellpadding=0 cellspacing=0><tr><td>Artikel</td></tr></table>';
    $table->headings[1]= 'Projekt';
    $table->headings[2]= 'Nummer';
    $table->headings[3]= 'Lieferdatum';
    $table->headings[4]= 'Menge';
    $table->headings[5]= 'Preis';

    $this->app->YUI->AutoComplete(ARTIKELAUTO,"artikel",array('name_de','warengruppe'),"id");

    $this->app->Tpl->Add($parsetarget,
'<script type="text/javascript">
var Tastencode;

var status=1;

var nureinmal=0;

function selectafterblur()
{
  if(nureinmal==0 || !isNaN(document.getElementById("artikel").value))
  {
      nureinmal=1;
      fillArtikelBestellung(document.getElementById("artikel").value,'.$adresse.');
  }
}

function TasteGedrueckt (Ereignis) {
  if (!Ereignis)
    Ereignis = window.event;
  if (Ereignis.which) {
    Tastencode = Ereignis.which;
  } else if (Ereignis.keyCode) {
    Tastencode = Ereignis.keyCode;
  }
  if((Tastencode=="9" || Tastencode=="13") && !isNaN(document.getElementById("artikel").value) )
  {
    fillArtikelBestellung(document.getElementById("artikel").value,'.$adresse.');
    //document.myform.konto.focus();
    status=1;
  }
}
document.onkeydown = TasteGedrueckt;


function checkhere()
{
if(!isNaN(document.getElementById("artikel").value))
      fillArtikel(document.getElementById("artikel").value);

}

</script>

');

    //$this->app->YUI->AutoComplete(NUMMERAUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");


    foreach($menu as $key=>$value)
    {

      // im popup öffnen
      if($key=="add")
        $tmp .= "<a href=\"index.php?module=$module&action=$value&id=%value%&frame=false&pid=$id\" 
          onclick=\"makeRequest(this);return false\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/new.png\"></a>&nbsp;";
      else if($key=="del")
        $tmp .= "<a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=$module&action=$value&sid=%value%&id=$id';\" href=\"#\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\"></a>&nbsp;";
      else if($key=="edit")
        $tmp .= "<a href=\"index.php?module=$module&action=$value&id=%value%&frame=false&pid=$id\" 
        onclick=\"makeRequest(this);return false\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/$key.png\"></a>&nbsp;";

      // nur aktion ausloesen und liste neu anzeigen
      else
        $tmp .= "<a href=\"index.php?module=$module&action=$value&sid=%value%&id=$id\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/$key.png\"></a>&nbsp;";
    }
    $table->DisplayNew($parsetarget, $tmp);
  }

*/


  function SortList($parsetarget,&$ref,$menu,$sql,$sort=true)
  {
    $module = $this->app->Secure->GetGET("module");
    $id = $this->app->Secure->GetGET("id");

    $table = new EasyTable($this->app);
    if($sort)
      $table->Query($sql." ORDER by sort"); 
    else
      $table->Query($sql); 

    foreach($menu as $key=>$value)
    {

      // im popup öffnen
      if($key=="add")
        $tmp .= "<a href=\"index.php?module=$module&action=$value&id=%value%&frame=false&pid=$id\" 
          onclick=\"makeRequest(this);return false\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/new.png\"></a>&nbsp;";
      else if($key=="del")
        $tmp .= "<a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=$module&action=$value&sid=%value%&id=$id';\" href=\"#\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/delete.gif\"></a>&nbsp;";
      else if($key=="edit")
        $tmp .= "<a href=\"index.php?module=$module&action=$value&id=%value%&frame=false&pid=$id\" class=\"popup\" title=\"Artikel &auml;ndern\">
        <img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/$key.png\"></a>&nbsp;";

      // nur aktion ausloesen und liste neu anzeigen
      else
        $tmp .= "<a href=\"index.php?module=$module&action=$value&sid=%value%&id=$id\"><img border=\"0\" src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/$key.png\"></a>&nbsp;";
    }
    $table->DisplayNew($parsetarget, $tmp);
  }

  function SortListEvent($event,$table,$fremdschluesselindex)
  {
    $sid = $this->app->Secure->GetGET("sid");
    $id = $this->app->Secure->GetGET("id");
    
    $sort = $this->app->DB->Select("SELECT sort FROM $table WHERE id='$sid' LIMIT 1");   

    if($event=="up")
    {
      //gibt es ein element an hoeherer stelle?
      $nextsort = $this->app->DB->Select("SELECT sort FROM $table WHERE $fremdschluesselindex='$id' AND sort ='".($sort+1)."' LIMIT 1");
      if($nextsort > $sort)
      {
                                $nextid = $this->app->DB->Select("SELECT id FROM $table WHERE $fremdschluesselindex='$id' AND sort = '".($sort+1)."' LIMIT 1");
                                $this->app->DB->Update("UPDATE $table SET sort='$nextsort' WHERE id='$sid' LIMIT 1");
                                $this->app->DB->Update("UPDATE $table SET sort='$sort' WHERE id='$nextid' LIMIT 1");
      } else {
        // element ist bereits an oberster stelle
      }
    }
    else if($event=="down")
    {
      //gibt es ein element an hoeherer stelle?
      $prevsort = $this->app->DB->Select("SELECT sort FROM $table WHERE $fremdschluesselindex='$id' AND sort = '".($sort-1)."' LIMIT 1");
      if($prevsort < $sort && $prevsort!=0)
      {
                                $previd = $this->app->DB->Select("SELECT id FROM $table WHERE $fremdschluesselindex='$id' AND sort = '".($sort-1)."' LIMIT 1");
                                $this->app->DB->Update("UPDATE $table SET sort='$prevsort' WHERE id='$sid' LIMIT 1");
                                $this->app->DB->Update("UPDATE $table SET sort='$sort' WHERE id='$previd' LIMIT 1");
      } else {
        // element ist bereits an oberster stelle
      }
    }
    else if($event=="del")
    {
                        if($sid>0)
                        {

                                if($table=="auftrag_position" || $table=="produktion_position" || $table=="lieferschein_position")
                                {
                                        switch($table)
                                        {
                                                case "auftrag_position"; $tmptable = "auftrag"; break;
                                                case "lieferschein_position"; $tmptable = "lieferschein"; break;
                                                case "produktion_position"; $tmptable = "produktion"; break;
                                        }
                                        
                                        // alle reservierungen fuer die eine position loeschen  
                                        $tmpartikel = $this->app->DB->Select("SELECT artikel FROM $table WHERE id='$sid' LIMIT 1");
                                        $tmptable_value = $this->app->DB->Select("SELECT $tmptable FROM $table WHERE id='$sid' LIMIT 1");
                                        $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE artikel='$tmpartikel' AND objekt='$tmptable' AND parameter='$tmptable_value'");

                                }
                                
        $this->app->DB->Delete("DELETE FROM $table WHERE id='$sid' LIMIT 1");
        $this->app->DB->Delete("UPDATE $table SET sort=sort-1 WHERE id='$sid' AND sort > $sort LIMIT 1");

                                if($tmptable=="auftrag") $this->app->erp->AuftragEinzelnBerechnen($tmptable_value);
                        }
    }
    else {}

  }

  function IframeDialog($width,$height,$src="")
  {
    $id = $this->app->Secure->GetGET("id");
    $sid = $this->app->Secure->GetGET("sid");
    $module = $this->app->Secure->GetGET("module");
    $action = $this->app->Secure->GetGET("action");
    if($src!="")
      $this->app->Tpl->Set(PAGE,"<iframe name=\"framepositionen\" id=\"framepositionen\" width=\"$width\"  height=\"$height\" frameborder=\"0\" src=\"$src&iframe=true\"></iframe>");
    else
      $this->app->Tpl->Set(PAGE,"<iframe name=\"framepositionen\" id=\"framepositionen\" width=\"$width\"  height=\"$height\" frameborder=\"0\" src=\"index.php?module=$module&action=$action&id=$id&sid=$sid&iframe=true\"></iframe>");
    $this->app->BuildNavigation=false;

  }






}
?>
