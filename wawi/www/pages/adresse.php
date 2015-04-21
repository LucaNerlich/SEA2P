<?php
include ("_gen/adresse.php");

class Adresse extends GenAdresse {
  var $app;
  
  function Adresse($app) {
    //parent::GenAdresse($app);
    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","AdresseCreate");
    $this->app->ActionHandler("edit","AdresseEdit");
    $this->app->ActionHandler("getid","AdresseGetid");
    $this->app->ActionHandler("open","AdresseOpen");
    $this->app->ActionHandler("list","AdresseList");
    $this->app->ActionHandler("delete","AdresseDelete");
    $this->app->ActionHandler("ustprf","AdresseUstprf");
    $this->app->ActionHandler("ustprfneu","AdresseUstprfNeu");
    $this->app->ActionHandler("ustprfedit","AdresseUstprfEdit");
    $this->app->ActionHandler("lieferantvorlage","AdresseLieferantvorlage");
    $this->app->ActionHandler("kundevorlage","AdresseKundevorlage");
    $this->app->ActionHandler("zeiterfassung","AdresseZeiterfassung");
    $this->app->ActionHandler("abrechnungzeit","AdresseAbrechnungzeit");
    $this->app->ActionHandler("abrechnungzeitabgeschlossen","AdresseAbrechnungzeitabgeschlossen");
    $this->app->ActionHandler("abrechnungzeitdelete","AdresseAbrechnungzeitdelete");

    $this->app->ActionHandler("lieferadresse","AdresseLieferadresse");
    $this->app->ActionHandler("lieferadresseneditpopup","AdresseLieferadressenEditPopup");
    $this->app->ActionHandler("ansprechpartner","AdresseAnsprechpartner");
    $this->app->ActionHandler("ansprechpartnereditpopup","AdresseAnsprechpartnerEditPopup");
    $this->app->ActionHandler("ansprechpartnerpopup","AdresseAnsprechpartnerPopup");
    $this->app->ActionHandler("lieferadressepopup","AdresseLieferadressePopup");
    $this->app->ActionHandler("ustpopup","AdresseUSTPopup");
    $this->app->ActionHandler("rollen","AdresseRollen");
    $this->app->ActionHandler("kontakthistorie","AdresseKontakthistorie");
    $this->app->ActionHandler("kontakthistorieeditpopup","AdresseKontakthistorieEditPopup");
    $this->app->ActionHandler("rolecreate","AdresseRolleAnlegen");
    $this->app->ActionHandler("rolledatum","AdresseRolleDatum");
    $this->app->ActionHandler("roledel","AdresseRolleLoeschen");
    $this->app->ActionHandler("addposition","AdresseAddPosition");
    $this->app->ActionHandler("suchmaske","AdresseSuchmaske");
    $this->app->ActionHandler("dateien","AdresseDateien");
    $this->app->ActionHandler("brief","AdresseBrief");
    $this->app->ActionHandler("briefdelete","AdresseBriefDelete");
    $this->app->ActionHandler("briefpdf","AdresseBriefPDF");
    $this->app->ActionHandler("briefeditpopup","AdresseBriefEditPopup");
    $this->app->ActionHandler("email","AdresseEmail");
    $this->app->ActionHandler("belege","AdresseBelege");
    $this->app->ActionHandler("positioneneditpopup","AdresseArtikelEditPopup");
    $this->app->ActionHandler("emaileditpopup","AdresseEmailEditPopup");
    $this->app->ActionHandler("artikel","AdresseArtikelPosition");
    $this->app->ActionHandler("lieferantartikel","AdresseLieferantArtikel");
    $this->app->ActionHandler("kundeartikel","AdresseKundeArtikel");
    $this->app->ActionHandler("delartikel","DelArtikel");
    $this->app->ActionHandler("upartikel","UpArtikel");
    $this->app->ActionHandler("downartikel","DownArtikel");

    $this->app->ActionHandler("rolledelete","AdresseRolleDelete");
    $this->app->ActionHandler("artikeleditpopup","AdresseArtikelEditPopup");
    $this->app->ActionHandler("kontakthistorie","AdresseKontaktHistorie");
    $this->app->ActionHandler("offenebestellungen","AdresseOffeneBestellungen");
    $this->app->ActionHandler("adressebestellungmarkieren","AdresseBestellungMarkiert");
    $this->app->ActionHandler("autocomplete","AdresseAutoComplete");

    $this->app->ActionHandler("lohn","AdresseLohnStundensatzUebersicht");
    $this->app->ActionHandler("stundensatz","AdresseStundensatz");
    $this->app->ActionHandler("stundensatzedit","AdresseStundensatzEdit");
    $this->app->ActionHandler("stundensatzdelete","AdresseStundensatzDelete");
    $this->app->ActionHandler("createdokument","AdresseCreateDokument");
    $this->app->ActionHandler("newkontakt","AdresseNewKontakt");
    $this->app->ActionHandler("delkontakt","AdresseDelKontakt");
    $this->app->ActionHandler("multilevel","AdresseMultilevel");
    $this->app->ActionHandler("minidetail","AdresseMiniDetailZeit");
    $this->app->ActionHandler("service","AdresseService");
    


    $id = $this->app->Secure->GetGET("id");
    //$nummer = $this->app->Secure->GetPOST("nummer");

    //if($nummer=="")
      //$name = $this->app->DB->Select("SELECT CONCAT(name,'&nbsp;&nbsp;',
    if($this->app->Conf->WFdbType=="postgre") {
      if(is_numeric($id))
        $nummer = $this->app->DB->Select("SELECT CONCAT( CASE WHEN kundennummer IS NOT NULL THEN CONCAT('Kunde: ',kundennummer) ELSE '' END, CASE WHEN lieferantennummer IS NOT NULL THEN CONCAT(' Lieferant: ',lieferantennummer) ELSE '' END) FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");

    } else {

      $nummer = $this->app->DB->Select("SELECT CONCAT(
        if(kundennummer,CONCAT('Kunde: ',kundennummer),''),
          if(lieferantennummer,CONCAT(' Lieferant: ',lieferantennummer),'')) FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");

    }
    if(is_numeric($id))
      $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");

    // else
    //   $name = $nummer;

    if($name!="")
      $this->app->Tpl->Set(UEBERSCHRIFT,"Adresse von: ".$name);
    else
      $this->app->Tpl->Set(UEBERSCHRIFT,"Adressen");

    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Adresse");

    if($name!="" && $nummer!="")
      $this->app->Tpl->Set(KURZUEBERSCHRIFT2,"$name ($nummer)");
    else
      $this->app->Tpl->Set(KURZUEBERSCHRIFT2,"$name");


    $this->app->Tpl->Set(FARBE,"[FARBE1]");


    $this->app->ActionHandlerListen($app);
    $this->app = $app;
  }

  function AdresseMultilevel()
  {
    $this->AdresseMenu();
    $id = $this->app->Secure->GetGET("id");
    $sid = $this->app->Secure->GetGET("sid");
    $cmd = $this->app->Secure->GetGET("cmd");
    $really= $this->app->Secure->GetGET("really");

    if($cmd=="delete")
    {
      if($really=="true")
      {
        $this->app->DB->Delete("DELETE FROM mlm_wartekonto WHERE id='$sid' LIMIT 1");
        header("Location: index.php?module=adresse&action=multilevel&id=$id#tabs-2");
        exit;
      } else {

        $bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM mlm_wartekonto WHERE id='$sid' LIMIT 1");
        $this->app->Tpl->Set(MESSAGE2,"<div class=info>Soll der Eintrag \"$bezeichnung\" jetzt aus dem Wartekonto gel&ouml;scht werden?&nbsp;
            <input type=\"button\" value=\"Jetzt l&ouml;schen\" onclick=\"window.location.href='index.php?module=adresse&action=multilevel&cmd=delete&really=true&id=$id&sid=$sid#tabs-2';\">&nbsp;<input type=\"button\" value=\"Nein doch nicht\" onclick=\"window.location.href='index.php?module=adresse&action=multilevel&id=$id#tabs-2';\"></div>");

      }

    }


    if($this->app->Secure->GetPOST("mlmsubmitwartekonto"))
    {
      $bezeichnung = $this->app->Secure->GetPOST("bezeichnung");
      $beschreibung = $this->app->Secure->GetPOST("beschreibung");
      $betrag = $this->app->Secure->GetPOST("betrag");

      if($bezeichnung!="" && $betrag!="")
      {
        $betrag = str_replace(",",'.',$betrag);
        if($cmd=="edit")
        {
          $this->app->DB->Update("UPDATE mlm_wartekonto SET bezeichnung='$bezeichnung',beschreibung='$beschreibung',
              betrag='$betrag' WHERE id='$sid' LIMIT 1");
        } else 
        {
          $this->app->DB->Insert("INSERT INTO mlm_wartekonto (id,bezeichnung,beschreibung,adresse,betrag)
              VALUES ('','$bezeichnung','$beschreibung','$id','$betrag')");
        }
        header("Location: index.php?module=adresse&action=multilevel&id=$id#tabs-2");
        exit;
      } else {
        $this->app->Tpl->Set(MESSAGE2,"<div class=error>Bitte alle Felder ausf&uuml;llen!</div>");
      }
    } else {
      if($cmd=="edit")
      {
        $bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM mlm_wartekonto WHERE id='$sid' LIMIT 1");
        $beschreibung = $this->app->DB->Select("SELECT beschreibung FROM mlm_wartekonto WHERE id='$sid' LIMIT 1");
        $betrag = $this->app->DB->Select("SELECT betrag FROM mlm_wartekonto WHERE id='$sid' LIMIT 1");
      }
    }

    if($cmd=="edit"){
      $this->app->Tpl->Set(BUTTON,"&Auml;nderung speichern");
      $this->app->Tpl->Set(ABBRECHEN,"<input type=button onclick=\"window.location.href='index.php?module=adresse&action=multilevel&id=$id#tabs-2'\" value=\"Abbrechen\">");
    }
    else
      $this->app->Tpl->Set(BUTTON,"Neu anlegen");

    $this->app->Tpl->Set(BEZEICHNUNG,$bezeichnung);
    $this->app->Tpl->Set(BESCHREIBUNG,$beschreibung);
    $this->app->Tpl->Set(BETRAG,$betrag);



    $this->app->Tpl->Set(TABTEXT,"MLM Optionen");
    $this->app->Tpl->Set(TABTEXT2,"Wartekonto");
    $this->app->Tpl->Set(TABTEXT3,"Abrechnungen");

    $this->app->YUI->AutoComplete("sponsor","kunde");
    $this->app->YUI->AutoComplete("geworbenvon","kunde");
    $this->app->YUI->AutoComplete("mlmauszahlungprojekt","projektname",1);
    $this->app->YUI->DatePicker("mlmvertragsbeginn");
    $this->app->YUI->DatePicker("mlmlizenzgebuehrbis");
    $this->app->YUI->DatePicker("mlmfestsetzenbis");


    $this->app->YUI->DatePicker("rolledatum");


    if($this->app->Secure->GetPOST("mlmsubmit"))
    {
      $sponsor = $this->app->Secure->GetPOST("sponsor");
      $geworbenvon = $this->app->Secure->GetPOST("geworbenvon");
      $mlmaktiv = $this->app->Secure->GetPOST("mlmaktiv");
      $mlmpositionierung = $this->app->Secure->GetPOST("mlmpositionierung");
      $mlmabrechnung = $this->app->Secure->GetPOST("mlmabrechnung");
      $mlmwaehrungauszahlung = $this->app->Secure->GetPOST("mlmwaehrungauszahlung");
      $mlmauszahlungprojekt = $this->app->Secure->GetPOST("mlmauszahlungprojekt");
      $mlmvertragsbeginn = $this->app->Secure->GetPOST("mlmvertragsbeginn");
      $mlmlizenzgebuehrbis = $this->app->Secure->GetPOST("mlmlizenzgebuehrbis");
      $mlmfestsetzenbis = $this->app->Secure->GetPOST("mlmfestsetzenbis");
      $steuernummer = $this->app->Secure->GetPOST("steuernummer");
      $mlmmitmwst = $this->app->Secure->GetPOST("mlmmitmwst");
      $rolledatum = $this->app->Secure->GetPOST("rolledatum");

      $mlmfestsetzen = $this->app->Secure->GetPOST("mlmfestsetzen");
      $mlmmindestpunkte = $this->app->Secure->GetPOST("mlmmindestpunkte");

      $mlmabrechnung = $this->app->Secure->GetPOST("mlmabrechnung");
      $mlmauszwahlungwaehrung = $this->app->Secure->GetPOST("mlmauszwahlungwaehrung");

      $mlmvertragsbeginn = $this->app->String->Convert($mlmvertragsbeginn,"%1.%2.%3","%3-%2-%1");
      $mlmfestsetzenbis = $this->app->String->Convert($mlmfestsetzenbis,"%1.%2.%3","%3-%2-%1");
      $mlmlizenzgebuehrbis = $this->app->String->Convert($mlmlizenzgebuehrbis,"%1.%2.%3","%3-%2-%1");
      $rolledatum = $this->app->String->Convert($rolledatum,"%1.%2.%3","%3-%2-%1");


      $sponsor = $this->app->erp->ReplaceKundennummer($sponsor,$sponsor,1);
      $geworbenvon = $this->app->erp->ReplaceKundennummer($geworbenvon,$geworbenvon,1);
      $mlmauszahlungprojekt = $this->app->erp->ReplaceProjekt($mlmauszahlungprojekt,$mlmauszahlungprojekt,1);

      if($mlmaktiv!="1") $mlmaktiv = "0";
      if($mlmmitmwst!="1") $mlmmitmwst = "0";
      $this->app->DB->Update("UPDATE adresse SET sponsor='$sponsor',rolledatum='$rolledatum',steuernummer='$steuernummer', geworbenvon='$geworbenvon',mlmaktiv='$mlmaktiv',mlmpositionierung='$mlmpositionierung',mlmmitmwst='$mlmmitmwst',mlmfestsetzen='$mlmfestsetzen',mlmmindestpunkte='$mlmmindestpunkte',
          mlmabrechnung='$mlmabrechnung', mlmlizenzgebuehrbis='$mlmlizenzgebuehrbis', mlmvertragsbeginn='$mlmvertragsbeginn',mlmfestsetzenbis='$mlmfestsetzenbis', mlmwaehrungauszahlung='$mlmwaehrungauszahlung', mlmauszahlungprojekt='$mlmauszahlungprojekt' WHERE id='$id' LIMIT 1");

      $anzahl_positionierungen = $this->app->DB->Select("SELECT COUNT(id) FROM mlm_positionierung WHERE adresse='$id'");

      if($mlmaktiv =="1" && $anzahl_positionierungen <=0)
      {
        $this->app->DB->Update("UPDATE adresse SET mlmvertragsbeginn=NOW() WHERE id='$id' AND mlmvertragsbeginn='0000-00-00'");
        if($mlmfestsetzen!="1")
          $this->app->DB->Insert("INSERT INTO mlm_positionierung (id,adresse,positionierung,datum) VALUES ('','$id','1',NOW())");

        $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$id'");
        $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$id'");

        if($kundennummer=="")
          $this->app->erp->AddRolleZuAdresse($id, "Kunde", "von", "Projekt", $projekt);
      }
      if($mlmaktiv =="1" && $mlmfestsetzen=="1")
      {
        $check = $this->app->DB->Select("SELECT positionierung FROM mlm_positionierung WHERE adresse='$id' ORDER by id DESC LIMIT 1");
        if($check!=$mlmpositionierung)
          $this->app->DB->Insert("INSERT INTO mlm_positionierung (adresse,positionierung,datum) VALUES ('$id','$mlmpositionierung',NOW())");
      }

      $this->app->Tpl->Set(MESSAGE,"<div class=\"error2\">Die MLM Optionen wurden gespeichert!</div>");
    }

    $sponsor = $this->app->DB->Select("SELECT sponsor FROM adresse WHERE id='$id' LIMIT 1");
    $sponsor = $this->app->DB->Select("SELECT CONCAT(kundennummer,' ',name) FROM adresse WHERE id='$sponsor' LIMIT 1");
    $geworbenvon = $this->app->DB->Select("SELECT geworbenvon FROM adresse WHERE id='$id' LIMIT 1");
    $geworbenvon = $this->app->DB->Select("SELECT CONCAT(kundennummer,' ',name) FROM adresse WHERE id='$geworbenvon' LIMIT 1");
    $mlmaktiv = $this->app->DB->Select("SELECT mlmaktiv FROM adresse WHERE id='$id' LIMIT 1");
    $steuernummer = $this->app->DB->Select("SELECT steuernummer FROM adresse WHERE id='$id' LIMIT 1");
    $mlmmitmwst = $this->app->DB->Select("SELECT mlmmitmwst FROM adresse WHERE id='$id' LIMIT 1");
    $mlmvertragsbeginn = $this->app->DB->Select("SELECT mlmvertragsbeginn FROM adresse WHERE id='$id' LIMIT 1");
    $mlmlizenzgebuehrbis = $this->app->DB->Select("SELECT mlmlizenzgebuehrbis FROM adresse WHERE id='$id' LIMIT 1");
    $mlmfestsetzenbis = $this->app->DB->Select("SELECT mlmfestsetzenbis FROM adresse WHERE id='$id' LIMIT 1");

    $mlmfestsetzen = $this->app->DB->Select("SELECT mlmfestsetzen FROM adresse WHERE id='$id' LIMIT 1");
    $mlmpositionierung = $this->app->DB->Select("SELECT mlmpositionierung FROM adresse WHERE id='$id' LIMIT 1");
    $mlmmindestpunkte = $this->app->DB->Select("SELECT mlmmindestpunkte FROM adresse WHERE id='$id' LIMIT 1");
    $mlmwartekonto = $this->app->DB->Select("SELECT mlmwartekonto FROM adresse WHERE id='$id' LIMIT 1");

    $mlmabrechnung = $this->app->DB->Select("SELECT mlmabrechnung FROM adresse WHERE id='$id' LIMIT 1");
    $mlmauszahlungwaehrung = $this->app->DB->Select("SELECT mlmauszwahlungwaehrung FROM adresse WHERE id='$id' LIMIT 1");
    $mlmauszahlungprojekt = $this->app->DB->Select("SELECT p.abkuerzung FROM adresse a LEFT JOIN projekt p ON p.id=a.mlmauszahlungprojekt WHERE a.id='$id' LIMIT 1");


    $mlmvertragsbeginn = $this->app->DB->Select("SELECT mlmvertragsbeginn FROM adresse WHERE id='$id' LIMIT 1");
    $rolledatum = $this->app->DB->Select("SELECT rolledatum FROM adresse WHERE id='$id' LIMIT 1");
    $mlmvertragsbeginn = $this->app->String->Convert($mlmvertragsbeginn,"%3-%2-%1","%1.%2.%3");
    $mlmlizenzgebuehrbis = $this->app->String->Convert($mlmlizenzgebuehrbis,"%3-%2-%1","%1.%2.%3");
    $mlmfestsetzenbis = $this->app->String->Convert($mlmfestsetzenbis,"%3-%2-%1","%1.%2.%3");
    $rolledatum = $this->app->String->Convert($rolledatum,"%3-%2-%1","%1.%2.%3");

    $this->app->Tpl->Set(MLMABRECHNUNG,$this->app->erp->GetSelectAsso($this->app->erp->GetMLMAbrechnung(),$mlmabrechnung));
    $this->app->Tpl->Set(MLMWAEHRUNGAUSZAHLUNG,$this->app->erp->GetSelect($this->app->erp->GetMLMAuszahlungWaehrung(),$mlmwaehrungauszahlung));
    $this->app->Tpl->Set(MLMPOSITIONIERUNG,$this->app->erp->GetSelectAsso($this->app->erp->GetMLMPositionierung(),$mlmpositionierung));
    $this->app->Tpl->Set(MLMAUSZAHLUNGPROJEKT,$mlmauszahlungprojekt);


    $this->app->Tpl->Set(STEUERNUMMER,$steuernummer);
    $this->app->Tpl->Set(SPONSOR,$sponsor);
    $this->app->Tpl->Set(MLMVERTRAGSBEGINN,$mlmvertragsbeginn);
    $this->app->Tpl->Set(MLMLIZENZGEBUEHRBIS,$mlmlizenzgebuehrbis);
    $this->app->Tpl->Set(MLMFESTSETZENBIS,$mlmfestsetzenbis);
    $this->app->Tpl->Set(ROLLEDATUM,$rolledatum);
    $this->app->Tpl->Set(GEWORBENVON,$geworbenvon);

    $this->app->Tpl->Set(MLMWARTEKONTO,$mlmwartekonto);

    if($mlmmitmwst=="1")
      $this->app->Tpl->Set(MLMMITMWST,"checked");

    if($mlmfestsetzen=="1")
      $this->app->Tpl->Set(MLMFESTSETZEN,"checked");

    if($mlmaktiv=="1")
      $this->app->Tpl->Set(MLMAKTIV,"checked");

    if($mlmmindestpunkte=="1")
      $this->app->Tpl->Set(MLMMINDESTPUNKTE,"checked");

    //Formula ansprechpartner
    $table = new EasyTable($this->app);
    $table->Query("SELECT positionierung, DATE_FORMAT(datum,'%d.%m.%Y') as seit, DATE_FORMAT(erneuert,'%d.%m.%Y') as erneuert FROM mlm_positionierung WHERE adresse='$id' ORDER by id DESC");
    $table->DisplayNew(HISTORIE,"Datum","noAction");

    //downline
    /*
       $table = new EasyTable($this->app);
       $table->Query("SELECT name, kundennummer FROM adresse WHERE sponsor='$id'");
       $table->DisplayNew(DOWNLINETABELLE,"Datum","noAction");
     */
    $this->app->YUI->TableSearch(TAB3,"mlm_downline");
    /*
    //geworben
    $table = new EasyTable($this->app);
    $table->Query("SELECT name, kundennummer FROM adresse WHERE geworbenvon='$id'");
    $table->DisplayNew(GEWORBENVONTABELLE,"Datum","noAction");
     */
    $this->app->YUI->TableSearch(TAB4,"mlm_geworbenvon");

    $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$id'");
    if($kundennummer=="")
      $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Achtung: Bitte vergeben Sie die Rolle Kunde, damit dieser im MLM System genutzt werden kann!</div>");

    $this->app->YUI->TableSearch(TAB5,"mlm_abrechnung_adresse");
    $this->app->YUI->TableSearch(TAB6,"mlm_abrechnung_adresse_log");

    $this->app->Tpl->Parse(TAB1,"adresse_multilevel.tpl");

    $this->app->YUI->TableSearch(WARTEKONTO,"mlmwartekonto");
    $this->app->Tpl->Parse(TAB2,"adresse_multilevel_wartekonto.tpl");

    //$this->app->YUI->TableSearch(TAB3,"zeiterfassung");



    $this->app->Tpl->Parse(PAGE,"adresse_multilevel_uebersicht.tpl");
  }



  function AdresseMiniDetailZeit()
  {
    $id = $this->app->Secure->GetGET("id");


    $tmp = $this->app->DB->SelectArr("SELECT * FROM zeiterfassung WHERE id='$id'");
    $tmp = $tmp[0];
    $teilprojekt = $this->app->DB->Select("SELECT aufgabe FROM arbeitspaket WHERE id='".$tmp[arbeitspaket]."'");

    echo "<table width=\"710\">";
    echo "<tr><td width=\"200\"><b>Ort:</b></td><td>".$tmp[ort]."</td></tr>";
    echo "<tr><td><b>Tätigkeit:</b></td><td>".$tmp[aufgabe]."</td></tr>";
    echo "<tr valign=\"top\"><td><b>Beschreibung:</b></td><td>".nl2br($tmp[beschreibung])."</td></tr>";
    echo "<tr><td><b>Teilprojekt:</b></td><td>".$teilprojekt."</td></tr>";
    echo "<tr><td><b>Kostenstelle:</b></td><td>".$tmp[kostenstelle]."</td></tr>";
    echo "<tr><td><b>Verrechnungsart:</b></td><td>".$tmp[verrechnungsart]."</td></tr>";
    echo "</table>";

    exit;
  }

  function AdresseAbrechnungzeitabgeschlossen()
  {
    $sid = $this->app->Secure->GetGET("sid");
    $id = $this->app->Secure->GetGET("id");
    $this->app->DB->Update("UPDATE zeiterfassung SET ist_abgerechnet='1', abgerechnet='1' WHERE id='$sid' AND adresse_abrechnung='$id' LIMIT 1");
    $this->AdresseAbrechnungzeit();
  }


  function AdresseAbrechnungzeitdelete()
  {
    $sid = $this->app->Secure->GetGET("sid");
    $id = $this->app->Secure->GetGET("id");
    $this->app->DB->Delete("DELETE FROM zeiterfassung WHERE id='$sid' AND adresse_abrechnung='$id' LIMIT 1");
    $this->AdresseAbrechnungzeit();
  }



  function AdresseZeiterfassung()
  {
    $this->AdresseMenu();
    $this->app->Tpl->Add(OFFENE,"<form action=\"\" method=\"post\">");
    $this->app->YUI->TableSearch(OFFENE,"zeiterfassungmitarbeiter");

    $id = $this->app->Secure->GetGET("id");

    $back=$this->app->erp->base64_url_encode("index.php?module=adresse&action=abrechnungzeit&id=$id");

    $this->app->Tpl->Set(BACK,$back);
    $this->app->Tpl->Set(ID,$id);
    /*
       $this->app->Tpl->Add(OFFENE,
       "<center>
       <input type=\"submit\" value=\"markierte Zeiten in Rechnung oder Auftrag &uuml;berf&uuml;hren\">
       <input type=\"submit\" value=\"als abgerechnet markieren\" name=\"abgerechnetmarkiert\">
       <input type=\"submit\" value=\"als offen markieren\" name=\"offenmarkiert\">
       </center>");
     */

    $this->app->Tpl->Parse(PAGE,"adresse_zeiterfassung.tpl");
  }



  function AdresseAbrechnungzeit()
  {
    $this->AdresseMenu();
    $this->app->Tpl->Add(OFFENE,"<form action=\"\" method=\"post\">");
    $this->app->YUI->TableSearch(OFFENE,"abrechnungzeit");

    $id = $this->app->Secure->GetGET("id");

    $back=$this->app->erp->base64_url_encode("index.php?module=adresse&action=abrechnungzeit&id=$id");

    $this->app->Tpl->Set(BACK,$back);
    $this->app->Tpl->Set(ID,$id);

    $this->app->Tpl->Add(OFFENE,
        "<center>
        <input type=\"submit\" value=\"markierte Zeiten mit Zeitangabe in Rechnung oder Auftrag &uuml;berf&uuml;hren\" name=\"inklzeit\">
        <input type=\"submit\" value=\"markierte Zeiten ohne Zeitangabe in Rechnung oder Auftrag &uuml;berf&uuml;hren\">
        <br>
        <br>
        <input type=\"submit\" value=\"als abgerechnet markieren\" name=\"abgerechnetmarkiert\">
        <input type=\"submit\" value=\"als offen markieren\" name=\"offenmarkiert\">
        </center>");


    $this->app->Tpl->Parse(PAGE,"adresse_zeitkonto.tpl");

  }


  function AdresseCreateDokument()
  {
    $id = $this->app->Secure->GetGET("id");
    $cmd = $this->app->Secure->GetGET("cmd");

    $relocation = true;

    switch($cmd)
    {	
      case 'auftrag': $newid = $this->app->erp->CreateAuftrag($id); $this->app->erp->LoadAuftragStandardwerte($newid,$id); break;
      case 'angebot': $newid = $this->app->erp->CreateAngebot($id); $this->app->erp->LoadAngebotStandardwerte($newid,$id); break;
      case 'rechnung': $newid = $this->app->erp->CreateRechnung($id); $this->app->erp->LoadRechnungStandardwerte($newid,$id); break;
      case 'lieferschein': $newid = $this->app->erp->CreateLieferschein($id); $this->app->erp->LoadLieferscheinStandardwerte($newid,$id); break;
      case 'gutschrift': $newid = $this->app->erp->CreateGutschrift($id); $this->app->erp->LoadGutschriftStandardwerte($newid,$id); break;
      case 'bestellung': $newid = $this->app->erp->CreateBestellung($id); $this->app->erp->LoadBestellungStandardwerte($newid,$id);break;
      default: $relocation = false;
    }

    if($relocation)
    {
      header("Location: index.php?module=$cmd&action=edit&id=$newid");
      exit;
    }

  }

  function AdresseLohnStundensatzUebersicht()
  {
    $this->AdresseMenu();	

    $msg = $this->app->erp->base64_url_decode($this->app->Secure->GetGET("msg"));
    if($msg!="") $this->app->Tpl->Set(MESSAGE, $msg);

    $this->AdresseLohn();
    $this->AdresseStundensatz();

    $this->app->Tpl->Parse(PAGE,"adresse_lohn.tpl");
  }

  function AdresseLohn()
  {
    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id))
    {
      $this->app->YUI->TableSearch(TAB1,"adresselohn");
    }else
      $this->app->Tpl->Set(MESSAGE, "<div class=\"error\">Mitarbeiter-ID konnte nicht gefunden werden.</div>");

  }

  function AdresseStundensatz($id)
  {
    $id = $this->app->Secure->GetGET("id");	

    if(is_numeric($id))
    {
      $stundensatz = $this->app->Secure->GetPOST("Stundensatz_StandardStundensatz");
      $submit = $this->app->Secure->GetPOST("Stundensatz_Submit");

      // Speichere neuen Stundensatz
      if($submit!="")
      {
        $this->app->DB->Insert("INSERT INTO stundensatz (adresse, satz, typ, projekt, datum) VALUES ('$id', '$stundensatz', 'Standard', '0', NOW())");
        $this->app->Tpl->Set(MESSAGE, "<div class=\"success\">Der neue Standard-Stundensatz wurde &uuml;bernommen.</div>");
      }

      // Hole neuesten Stundensatz
      $standard = $this->app->DB->Select("SELECT satz 
          FROM stundensatz 
          WHERE typ='standard'
          AND adresse='$id'
          ORDER BY datum DESC LIMIT 1");
      $this->app->Tpl->Set(STANDARDSTUNDENSATZ, $standard);

      // Fülle Projekt-Tabelle
      $this->app->YUI->TableSearch(TAB2,"adressestundensatz");
    }else
      $this->app->Tpl->Set(MESSAGE, "<div class=\"error\">Mitarbeiter-ID konnte nicht gefunden werden.</div>");
  }

  function AdresseStundensatzEdit()
  {
    $this->AdresseMenu();

    $user = $this->app->Secure->GetGET("user");
    $id = $this->app->Secure->GetGET("id");	
    $projekt = $this->app->Secure->GetGET("projekt");

    $satz = $this->app->Secure->GetPOST("Stundensatz_Angepasst");
    $adapt = $this->app->Secure->GetPOST("Stundensatz_Adapt");
    $cancel = $this->app->Secure->GetPOST("Stundensatz_Angepasst_Cancel");
    $submit = $this->app->Secure->GetPOST("Stundensatz_Angepasst_Submit");


    if($cancel!="")
    {
      header("Location: ./index.php?module=adresse&action=lohn&id=$user");
      exit;
    }


    // Hole neuesten Standard-Stundensatz
    $standard = $this->app->DB->Select("SELECT satz 
        FROM stundensatz 
        WHERE typ='standard'
        AND adresse='$user'
        ORDER BY datum DESC LIMIT 1");

    if(is_numeric($id))
    {
      // Stundensatz existiert bereits, hole Daten
      $stundensatz = $this->app->DB->SelectArr("SELECT * FROM stundensatz WHERE id='$id' LIMIT 1");
      $this->app->Tpl->Set(STUNDENSATZANGEPASST, $stundensatz[0][satz]);

      if($submit!="")	
      {
        $projekt = $this->app->DB->Select("SELECT projekt FROM stundensatz WHERE id='$id' LIMIT 1");

        if($adapt!="")
          $this->app->DB->Update("UPDATE stundensatz SET satz='$satz' WHERE adresse='$user' AND projekt='$projekt'");

        $this->app->DB->Insert("INSERT INTO stundensatz (adresse, satz, typ, projekt, datum)
            VALUES ('$user', '$satz', 'Angepasst', '$projekt', NOW())");
        header("Location: ./index.php?module=adresse&action=lohn&id=$user&msg=$msg");
        exit;		
      }


      $this->app->Tpl->Set(MODE, "Stundensatz editieren");
    }else
    {
      // Stundensatz existiert noch nicht
      $this->app->Tpl->Set(STUNDENSATZANGEPASST, $standard);
      $this->app->Tpl->Set(ADAPTDISABLED, "DISABLED");

      if($submit!="")
      {
        // Schreibe neuen Satz
        $this->app->DB->Insert("INSERT INTO stundensatz (adresse, satz, typ, projekt, datum)
            VALUES ('$user', '$satz', 'Angepasst', '$projekt', NOW())");

        $msg = $this->app->erp->base64_url_encode("<div class=\"success\">Der Stundensatz wurde erfolgreich gespeichert.</div>");
        header("Location: ./index.php?module=adresse&action=lohn&id=$user&msg=$msg");
        exit;
      }	

      $this->app->Tpl->Set(MODE, "Stundensatz erstellen");
    }

    $this->app->Tpl->Parse(PAGE, "adresse_stundensatz_edit.tpl");	
  }

  function AdresseStundensatzDelete()
  {
    $user = $this->app->Secure->GetGET("user");
    $id = $this->app->Secure->GetGET("id");

    if(is_numeric($id))
      $this->app->DB->Delete("DELETE FROM stundensatz WHERE id='$id' LIMIT 1");
    else
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Stundensatz-ID konnte nicht gefunden werden. Standard-Stundens&auml;tze k&ouml;nnen nicht gel&ouml;scht werden.</div>"); 

    header("Location: ./index.php?module=adresse&action=lohn&id=$user&msg=$msg");
    exit; 
  }



  function AdresseAutoComplete()
  {

    $table = $this->app->Secure->GetGET("table");
    $filter = $this->app->Secure->GetGET("filter");
    $name = $this->app->Secure->GetGET("name");
    $query = $this->app->Secure->GetGET("query");
    $colsstring = $this->app->erp->base64_url_decode($this->app->Secure->GetGET("colsstring"));
    $returncol= $this->app->erp->base64_url_decode($this->app->Secure->GetGET("returncol"));
    if($table=="")
      $table=$name;

    if($filter=="kunde")
      $filter = "LEFT JOIN adresse_rolle ON adresse_rolle.adresse=adresse.id WHERE adresse_rolle.subjekt='Kunde' AND adresse.kundennummer!=0 AND adresse.geloescht=0 AND adresse.name LIKE '%$query%'";

    if($filter=="mitarbeiter")
      $filter = "LEFT JOIN adresse_rolle ON adresse_rolle.adresse=adresse.id WHERE (adresse_rolle.subjekt='Mitarbeiter' OR adresse_rolle.subjekt='Externer Mitarbeiter') AND adresse.mitarbeiternummer!=0 AND adresse.geloescht=0 
        AND adresse.name LIKE '%$query%'";


    if($filter=="lieferant")
      $filter = "LEFT JOIN adresse_rolle ON adresse_rolle.adresse=adresse.id WHERE adresse_rolle.subjekt='Lieferant' AND adresse.geloescht=0 AND adresse.name LIKE '%$query%'";

    if($filter=="kunde_auftrag")
      $filter = "LEFT JOIN adresse_rolle ON adresse_rolle.adresse=adresse.id LEFT JOIN auftrag ON auftrag.adresse=adresse.id WHERE adresse_rolle.subjekt='Kunde' AND ((auftrag.status='freigegeben' OR auftrag.status='storniert') OR (auftrag.vorkasse_ok=0 AND (auftrag.zahlungsweise='paypal' OR auftrag.zahlungsweise='vorkasse' OR auftrag.zahlungsweise='kreditkarte'))) AND adresse.geloescht=0
        AND adresse.name LIKE '%$query%'";

    if($filter=="kunde_rechnung")
      $filter = "LEFT JOIN adresse_rolle ON adresse_rolle.adresse=adresse.id LEFT JOIN rechnung ON rechnung.adresse=adresse.id WHERE adresse_rolle.subjekt='Kunde' AND rechnung.ist < rechnung.soll AND adresse.geloescht=0 AND adresse.name LIKE '%$query%'";

    if($filter=="kunde_gutschrift")
      $filter = "LEFT JOIN adresse_rolle ON adresse_rolle.adresse=adresse.id LEFT JOIN gutschrift ON gutschrift.adresse=adresse.id WHERE adresse_rolle.subjekt='Kunde' AND adresse.geloescht=0 AND adresse.name LIKE '%$query%'";


    if($table=="artikel")
      $filter = "WHERE name_de LIKE '%$query%'";


    if(($filter=="" || $filter=="adresse") && $name=="adresse")
      $filter = "WHERE adresse.geloescht=0 AND adresse.name LIKE '%$query%'";

    $arr = $this->app->DB->SelectArr("SELECT DISTINCT $colsstring, $returncol FROM $table $filter ORDER by 1 LIMIT 10");
    //      echo "SELECT DISTINCT $colsstring, $returncol FROM $table $filter ORDER by 1";

    $cols = split(',',$colsstring);
    foreach($arr as $key=>$value){
      //$tpl_end .= '{id:"'.$value[$returncol].'", cola:"'.$value[$cols[0]].'", colb:"'.$value[$cols[1]].'", colc:"'.$value[$cols[2]].'"},';
      echo $value[$returncol]."!*!".$value[$cols[0]].' '.$value[$cols[1]].' '.$value[$cols[2]]."\n";
      //echo $value[$cols[0]].' '.$value[$cols[1]].' '.$value[$cols[2]]."\n";
      //echo $value[$cols[0]]."\n";
    } 


    exit;

  }


  function AdresseDateien()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->AdresseMenu();
    $this->app->Tpl->Add(UEBERSCHRIFT," (Dateien)");
    $this->app->YUI->DateiUpload(PAGE,"Adressen",$id);
  }


  function AdresseDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->DB->Update("UPDATE adresse SET geloescht='1',kundennummer=CONCAT('DEL-',kundennummer), lieferantennummer=CONCAT('DEL-',lieferantennummer), 
        mitarbeiternummer=CONCAT('DEL-',mitarbeiternummer) WHERE id='$id' LIMIT 1");
    $this->AdresseList();
  }


  function AdresseRolleDatum()
  {
    $id = $this->app->Secure->GetGET("id");
    $sid = $this->app->Secure->GetGET("sid");
    $von = $this->app->Secure->GetGET("von");
    $bis = $this->app->Secure->GetGET("bis");
    $von = $this->app->String->Convert($von,"%1.%2.%3","%3-%2-%1");
    $bis = $this->app->String->Convert($bis,"%1.%2.%3","%3-%2-%1");

    $this->app->DB->Delete("UPDATE adresse_rolle SET von='$von', bis='$bis' WHERE id='$sid' AND adresse='$id' LIMIT 1");

    $gruppe = $this->app->DB->Select("SELECT parameter FROM adresse_rolle WHERE id='$sid' AND adresse='$id' LIMIT 1");

    if($gruppe > 0)
    {
      if($von!="--" && $bis!="--")
      {
        $this->app->DB->Update("UPDATE auftrag SET gruppe='$gruppe' WHERE datum<='$bis' AND datum >='$von'	AND adresse='$id'");	
        $this->app->DB->Update("UPDATE rechnung SET gruppe='$gruppe' WHERE datum<='$bis' AND datum >='$von'	AND adresse='$id'");	
        $this->app->DB->Update("UPDATE gutschrift SET gruppe='$gruppe' WHERE datum<='$bis' AND datum >='$von'	AND adresse='$id'");	
      } else if($von!="--" && $bis=="--")
      {
        $this->app->DB->Update("UPDATE auftrag SET gruppe='$gruppe' WHERE datum>='$von' AND adresse='$id'");	
        $this->app->DB->Update("UPDATE rechnung SET gruppe='$gruppe' WHERE datum>='$von' AND adresse='$id'");	
        $this->app->DB->Update("UPDATE gutschrift SET gruppe='$gruppe' WHERE datum>='$von' AND adresse='$id'");	
      }	
    }

    $this->AdresseRollen();
  }




  function AdresseRolleDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    $sid = $this->app->Secure->GetGET("sid");

    if($this->app->Conf->WFdbType=="postgre")
      $this->app->DB->Delete("DELETE FROM adresse_rolle WHERE id='$sid' AND adresse='$id'");
    else
      $this->app->DB->Delete("DELETE FROM adresse_rolle WHERE id='$sid' AND adresse='$id' LIMIT 1");

    //    $this->app->Secure->POST['rolleanlegen'] = "true";
    //$this->AdresseEdit();
    $this->AdresseRollen();
    //$this->app->Tpl->Set(AKTIV_TAB3,"selected");
  }

  function AdresseCreate()
  {
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Adresse anlegen");
    $this->app->Tpl->Set(TOPHEADING,"Adresse anlegen");

    $this->app->erp->MenuEintrag("index.php?module=adresse&action=list","Zur&uuml;ck zur &Uuml;bersicht");

    parent::AdresseCreate();
  }


  function AdresseList()
  {

    //$this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\">Allgemein</h2></li>");
    $this->app->erp->MenuEintrag("index.php?module=adresse&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=adresse&action=create","Neue Adresse anlegen");
    //$this->app->Tpl->Add(TABS,"<li><a  href=\"index.php\">Zur&uuml;ck zur &Uuml;bersicht</a></li>");

    //    $this->app->Tpl->Set(UEBERSCHRIFT,"Adresssuche");
    //    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Adressen");

    $this->app->YUI->TableSearch(TAB1,"adressetabelle");
    $this->app->Tpl->Parse(PAGE,"adresseuebersicht.tpl");

    /*

       $action=$this->app->Secure->GetGET("action");

       $this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\" style=\"background-color: [FARBE1]\">Allgemein</h2></li>");
       $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=adresse&action=create\">Neue Adresse anlegen</a></li>");
       $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=adresse&action=list\">Adresse suchen</a></li>");


       if($action=="list")
       $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=welcome&action=main\">Zur&uuml;ck zur &Uuml;bersicht</a></li>");
       else 
       $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=adresse&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a></li>");
       $this->app->Tpl->Add(TABS,"<li><br><br></li>");

       $this->app->erp->EasylogPaketmarke('name','adresszusatz','strasse','plzi','ort','land',1);


    //kunden
    $sql = $this->app->erp->AdressSuche(TAB1);
    $table = new EasyTable($this->app);
    $table->Query($sql,10);
    $table->DisplayOwn(INHALT,"
    <a href=\"index.php?module=adresse&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.png\"></a>
    <a onclick=\"if(!confirm('Adresse wirklich l&ouml;schen?')) return false; else window.location.href='index.php?module=adresse&action=delete&id=%value%';\"><img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\"></a>
    <a href=\"index.php?module=adresse&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/copy.png\"></a>
    ",30,"mid");
    $this->app->Tpl->Parse(TAB1,"rahmen100.tpl");
    $this->app->Tpl->Set(INHALT,"");
     */
    /*
    //Lieferant
    $sql = $this->app->erp->AdressSuche(TAB2,"Lieferant");
    $table = new EasyTable($this->app);
    $table->Query($sql,10);
    $table->DisplayOwn(INHALT,"
    <a href=\"index.php?module=adresse&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.png\"></a>
    <a href=\"index.php?module=adresse&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/delete.gif\"></a>
    <a href=\"index.php?module=adresse&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/copy.png\"></a>
    ",30,"mid");
    $this->app->Tpl->Parse(TAB2,"rahmen100.tpl");
    $this->app->Tpl->Set(INHALT,"");

    //mitarbeiter
    $table = new EasyTable($this->app);
    $table->Query("SELECT DISTINCT a.name, a.ort, a.telefon, a.email, a.id
    FROM adresse a LEFT JOIN adresse_rolle r ON a.id=r.adresse WHERE r.subjekt='Mitarbeiter'",10);
    $table->DisplayNew(INHALT,"
    <a href=\"index.php?module=adresse&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.png\"></a>
    ","");
    $this->app->Tpl->Parse(TAB3,"rahmen100.tpl");
    $this->app->Tpl->Set(INHALT,"");

    //ohne rolle
    $table = new EasyTable($this->app);
    $table->Query("SELECT DISTINCT a.name, a.ort, a.telefon, a.email, a.id
    FROM adresse a LEFT JOIN adresse_rolle r ON a.id=r.adresse WHERE r.subjekt IS NULL");
    $table->DisplayNew(INHALT,"
    <a href=\"index.php?module=adresse&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.png\"></a>
    ","");
    $this->app->Tpl->Parse(TAB4,"rahmen100.tpl");
     */

    //  $this->app->Tpl->Set(AKTIV_TAB1,"selected");

    //   $this->app->Tpl->Parse(PAGE,"adresseuebersicht.tpl");
    //parent::AdresseList();
  }


  function AdresseMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $action= $this->app->Secure->GetGET("action");

    //    $this->app->Tpl->Add(TABS,"<li><h2 style=\"background-color: [FARBE1];\">Adresse</h2></li>");
    $this->app->erp->MenuEintrag("index.php?module=adresse&action=edit&id=$id","Details");
    $this->app->erp->MenuEintrag("index.php?module=adresse&action=rollen&id=$id","Rollen");
    $this->app->erp->MenuEintrag("index.php?module=adresse&action=dateien&id=$id","Dateien");


    if($this->app->erp->RechteVorhanden("multilevel","list") && $this->app->erp->Firmendaten("modul_mlm")=="1")
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=multilevel&id=$id","MLM");


    // Ist Benutzer ein Mitarbeiter?
    if(is_numeric($id))
      $mitarbeiter = $this->app->DB->Select("SELECT id FROM adresse_rolle WHERE adresse='$id' AND subjekt='Mitarbeiter' LIMIT 1");
    if(is_numeric($mitarbeiter))
    {
      //		$this->app->erp->MenuEintrag("index.php?module=adresse&action=lohn&id=$id","Lohn");
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=zeiterfassung&id=$id","Zeit");
    }

    $this->app->erp->MenuEintrag("index.php?module=adresse&action=ansprechpartner&id=$id","Ansprechpart.");
    $this->app->erp->MenuEintrag("index.php?module=adresse&action=lieferadresse&id=$id","Lieferadressen");
    //$this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=adresse&action=kontakthistorie&id=$id\">Kontakthistorie</a></li>");

    $this->app->erp->MenuEintrag("index.php?module=adresse&action=brief&id=$id","Korresp.");

    if($this->app->erp->IsAdresseSubjekt($id,"Kunde"))
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=belege&id=$id","Belege");



    //$this->app->Tpl->Add("index.php?module=adresse&action=email&id=$id\">E-Mail schreiben</a></li>");


    $this->app->erp->MenuEintrag("index.php?module=adresse&action=kundeartikel&id=$id","Artikel");

    //    if($this->app->erp->IsAdresseSubjekt($id,"Kunde"))
    //      $this->app->erp->MenuEintrag("index.php?module=adresse&action=kundevorlage&id=$id","Zahlungsweise");

    if($this->app->erp->IsAdresseSubjekt($id,"Kunde"))
    {
      //      $this->app->erp->MenuEintrag("index.php?module=adresse&action=email&id=$id","RMAs");
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=abrechnungzeit&id=$id","Zeitkonto");
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=artikel&id=$id","Abos");
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=service&id=$id","Service");
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=ustprf&id=$id","USt");
      //$this->app->Tpl->Add("index.php?module=adresse&action=email&id=$id\">Rabatte</a></li>");
    }

    if($this->app->erp->IsAdresseSubjekt($id,"Lieferant"))
    {
      //				$this->app->Tpl->Add(TABS,"<br><br>");
      //      $this->app->erp->MenuEintrag("index.php?module=adresse&action=email&id=$id","RMAs</a></li>");
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=lieferantartikel&id=$id","Lieferprogramm");
      $this->app->erp->MenuEintrag("index.php?module=adresse&action=offenebestellungen&id=$id","Bestellungen");
    }

    //    $this->app->erp->MenuEintrag("index.php?module=adresse&action=create","Neue Adresse anlegen");
    /*
       if($action=="list")
       $this->app->erp->MenuEintrag("index.php?module=welcome&action=main","Zur&uuml;ck zur &Uuml;bersicht");
       else 
       $this->app->erp->MenuEintrag("index.php?module=adresse&action=list","Zur&uuml;ck zur &Uuml;bersicht");
     */
    /*
       $this->app->Tpl->Add(TABS,"<li><br><br></li>");
       $this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\">Allgemein</h2></li>");
       $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=adresse&action=create\">Neue Adresse anlegen</a></li>");
       $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=adresse&action=list\">Adresse suchen</a></li>");

       if($action=="list")
       $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=welcome&action=main\">Zur&uuml;ck zur &Uuml;bersicht</a></li>");
       else 
       $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=adresse&action=list\">Zur&uuml;ck zur &Uuml;bersicht</a></li>");

     */


    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=adresse&action=kosten&id=$id\">Gesamtkalkulation</a></li>");
  }

  /*
     function AdresseKontaktHistorie()
     {
     $this->AdresseMenu();

     $this->app->Tpl->Set(SUBSUBHEADING,"Gespr&auml;che");
     $id = $this->app->Secure->GetGET("id");

  //Formula lieferadresse
  $table = new EasyTable($this->app);
  $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%Y %H:%i') as Kontakt, grund,bearbeiter 
  FROM adresse_kontakhistorie WHERE adresse='$id' order by datum DESC");
  $table->DisplayNew(INHALT, "<a href=\"index.php?module=bestellung&action=edit&id=%value%\">Lesen</a>&nbsp;
  <a href=\"index.php?module=bestellung&action=edit&id=%value%\">Antworten</a>&nbsp;");


  // easy table mit arbeitspaketen YUI als template 
  $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");
  $this->app->Tpl->Set(AKTIV_TAB1,"selected");
  $this->app->Tpl->Set(TABTEXT,"Gespr&auml;che");
  $this->app->Tpl->Parse(PAGE,"tabview.tpl");

  } 
   */
  function AdresseKontaktHistorie()
  {
    $this->AdresseMenu();
    $this->app->Tpl->Add(UEBERSCHRIFT," (Kontakthistorie)");
    $this->app->Tpl->Set(SUBSUBHEADING,"Adressen");
    $id = $this->app->Secure->GetGET("id");

    // neues arbeitspaket
    $widget = new WidgetAnsprechpartner($this->app,TAB2);
    $widget->form->SpecialActionAfterExecute("none",
        "index.php?module=adresse&action=ansprechpartner&id=$id");
    $widget->Create();


    //Formula ansprechpartner
    $table = new EasyTable($this->app);
    $table->Query("SELECT name, bereich, telefon, email,id FROM ansprechpartner WHERE adresse='$id'");
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=adresse&action=ansprechpartnereditpopup&frame=false&id=%value%\" 
        onclick=\"makeRequest(this);return false\">Bearbeiten</a>");

    // easy table mit arbeitspaketen YUI als template 
    $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");
    $this->app->Tpl->Set(AKTIV_TAB1,"selected");
    $this->app->Tpl->Parse(PAGE,"ansprechpartneruebersicht.tpl");
  }

  function AdresseKontaktHistorieEditPopup()
  {
    $frame = $this->app->Secure->GetGET("frame");
    $id = $this->app->Secure->GetGET("id");

    if($frame=="false")
    {
      // hier nur fenster größe anpassen
      $this->app->YUI->IframeDialog(600,320);
    } else {
      // nach page inhalt des dialogs ausgeben
      $widget = new WidgetAnsprechpartner($this->app,PAGE);
      $adresse = $this->app->DB->Select("SELECT adresse FROM ansprechpartner WHERE id='$id' LIMIT 1");
      $widget->form->SpecialActionAfterExecute("close_refresh",
          "index.php?module=adresse&action=ansprechpartner&id=$adresse");

      $widget->Edit();
      $this->app->BuildNavigation=false;
    }
  }




  function AdresseRolle()
  {


  } 

  function AdresseNummern($id)
  {
    if(is_numeric($id)) {
      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
      $lieferantennummer = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
      $mitarbeiternummer= $this->app->DB->Select("SELECT mitarbeiternummer FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
      $projekt= $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    }

    if($kundennummer==0 || $kundennummer==""){
      // pruefe ob rolle kunden vorhanden
      if(is_numeric($id))
        $check = $this->app->DB->Select("SELECT adresse FROM adresse_rolle WHERE adresse='$id' AND subjekt='Kunde' LIMIT 1");
      if($check!="")
      {
        $kundennummer = $this->app->erp->GetNextKundennummer($projekt);
        if($this->app->Conf->WFdbType=="postgre")
          $this->app->DB->Update("UPDATE adresse SET kundennummer='$kundennummer' WHERE id='$id' AND (kundennummer='0' OR kundennummer='')");
        else
          $this->app->DB->Update("UPDATE adresse SET kundennummer='$kundennummer' WHERE id='$id' AND (kundennummer='0' OR kundennummer='') LIMIT 1");
      } else 
        $kundennummer="noch keine";
    }

    if($lieferantennummer==0){
      if(is_numeric($id))
        $check = $this->app->DB->Select("SELECT adresse FROM adresse_rolle WHERE adresse='$id' AND subjekt='Lieferant' LIMIT 1");
      if($check!="")
      {
        $lieferantennummer= $this->app->erp->GetNextLieferantennummer($projekt);
        if(is_numeric($id))
          if($this->app->Conf->WFdbType=="postgre")
            $this->app->DB->Update("UPDATE adresse SET lieferantennummer='$lieferantennummer' WHERE id='$id' AND (lieferantennummer='0' OR lieferantennummer='')");
          else
            $this->app->DB->Update("UPDATE adresse SET lieferantennummer='$lieferantennummer' WHERE id='$id' AND (lieferantennummer='0' OR lieferantennummer='') LIMIT 1");
      } else 
        $lieferantennummer="noch keine";
    }

    if($mitarbeiternummer==0){
      if(is_numeric($id))
        $check = $this->app->DB->Select("SELECT adresse FROM adresse_rolle WHERE adresse='$id' AND (subjekt='Mitarbeiter' OR subjekt='Externer Mitarbeiter') LIMIT 1");
      if($check!="")
      {
        $mitarbeiternummer= $this->app->erp->GetNextMitarbeiternummer($projekt);
        if(is_numeric($id))
          if($this->app->Conf->WFdbType=="postgre")
            $this->app->DB->Update("UPDATE adresse SET mitarbeiternummer='$mitarbeiternummer' WHERE id='$id' AND (mitarbeiternummer='0' OR mitarbeiternummer='')");
          else
            $this->app->DB->Update("UPDATE adresse SET mitarbeiternummer='$mitarbeiternummer' WHERE id='$id' AND (mitarbeiternummer='0' OR mitarbeiternummer='') LIMIT 1");

      } else 
        $mitarbeiternummer="noch keine";
    }
  }


  function AdresseDelKontakt()
  {
    $id = $this->app->Secure->GetGET("id");
    $lid = $this->app->Secure->GetGET("lid");

    //INSERT
    $this->app->DB->Delete("DELETE FROM adresse_kontakte WHERE id='$lid' LIMIT 1");

    //$this->AdresseEdit();
    header("Location: index.php?module=adresse&action=edit&id=$id");
    exit;
  }



  function AdresseNewKontakt()
  {
    $bezeichnung = $this->app->Secure->GetGET("bezeichnung");
    $kontakt = $this->app->Secure->GetGET("kontakt");
    $id = $this->app->Secure->GetGET("id");

    //INSERT
    $this->app->DB->Insert("INSERT INTO adresse_kontakte (id,adresse,bezeichnung,kontakt) VALUES ('','$id','$bezeichnung','$kontakt')");

    //$this->AdresseEdit();
    header("Location: index.php?module=adresse&action=edit&id=$id");
    exit;
  }

  function AdresseOpen()
  {

    $kundennummer=$this->app->Secure->GetGET("kundennummer");
    $projekt=$this->app->Secure->GetGET("projekt");

    if($projekt!="")
    {
      $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
      $id = $this->app->DB->Select("SELECT id FROM adresse WHERE projekt='$projektid' AND kundennummer='$kundennummer' LIMIT 1");
    } else {
      $id = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' LIMIT 1");
    }

    $cmd=$this->app->Secure->GetGET("cmd");
    header("Location: index.php?module=adresse&action=".$cmd."&id=".$id);
    exit;
  }	


  function AdresseGetid()
  {

    $kundennummer=$this->app->Secure->GetGET("kundennummer");
    $projekt=$this->app->Secure->GetGET("projekt");

    if($projekt!="")
    {
      $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
      $id = $this->app->DB->Select("SELECT id FROM adresse WHERE projekt='$projektid' AND kundennummer='$kundennummer' LIMIT 1");
    } else {
      $id = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' LIMIT 1");
    }

    echo $id;
    exit;
  }	


  function AdresseEdit()
  {
    $id = $this->app->Secure->GetGET("id");

    if($this->app->erp->DisableModul("adresse",$id))
    {
      //$this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->AdresseMenu();
      return;
    } 

    $adresse_kontakte = $this->app->Secure->GetPOST("adresse_kontakte");
    if(count($adresse_kontakte) > 0)
    {
      foreach($adresse_kontakte as $key=>$value)
        $this->app->DB->Update("UPDATE adresse_kontakte SET kontakt='$value' WHERE id='$key' LIMIT 1");
    }
    if(is_numeric($id)) {
      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
      $lieferantennummer = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
      $mitarbeiternummer= $this->app->DB->Select("SELECT mitarbeiternummer FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
      $logfile = $this->app->DB->Select("SELECT logfile FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
      $logfile  = str_replace(';',"\r\n",$logfile);
      $this->app->Tpl->Set(LOGFILE,"<textarea cols=\"60\" rows=\"5\">$logfile</textarea>");
    }



    //Weitere Kontakte
    $buttons_kontakte = "

      <script>

      $(document).ready(function(){

          $(\".button\").button();

          $('button#clipboard-dynamic').zclip({
path:'./js/ZeroClipboard.swf',
copy:function(){return '".$this->app->erp->AdresseAnschriftString($id)."';}
});
          });

</script>

<button type=button id=\"clipboard-dynamic\" class=\"button\">Adresse in Zwischenspeicher</button>

<a href=\"#\" class=\"button\" onclick=\"var bezeichnung =  prompt('Etikett bzw. Bezeichnung( (z.B. E-Mail, Skype, ICQ, ...):','Telefon Privat'); 
if((bezeichnung !=null && bezeichnung!='')) {var kontakt =  prompt('Kontakt:',''); if((bezeichnung !=null && bezeichnung!='') && (kontakt!=null && kontakt!='')) { window.location.href='index.php?module=adresse&action=newkontakt&id=".$id."&bezeichnung='+bezeichnung+'&kontakt='+kontakt;}}\">
Weitere Kontaktinfos</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

$kontakte = $this->app->DB->SelectArr("SELECT * FROM adresse_kontakte WHERE adresse='$id'");
for($i=0;$i<count($kontakte);$i++)
{
  $table_kontakte .= "<tr><td width=260>".$kontakte[$i]['bezeichnung']."&nbsp;<a href=\"#\" onclick=\"if(!confirm('".$kontakte[$i]['bezeichnung']." wirklich entfernen?')) 
    return false; else window.location.href='index.php?module=adresse&action=delkontakt&id=".$id."&lid=".$kontakte[$i]['id']."';\">x</a></td><td><input type=text name=\"adresse_kontakte[".$kontakte[$i]['id']."]\" value=\"".$kontakte[$i]['kontakt']."\" size=\"30\"></td></tr>";
}


$this->app->Tpl->Set(BUTTON_KONTAKTE,"<table width=100%>$table_kontakte</table><br>");

$this->app->Tpl->Add(BUTTON_KONTAKTE,$buttons_kontakte);



$things = array('angebot','auftrag','rechnung','lieferschein','gutschrift');

foreach($things as $key=>$value)
  $buttons_kunde .= '
  <a href="#" onclick="if(!confirm(\''.ucfirst($value).' wirklich anlegen?\')) return false; else window.location.href=\'index.php?module=adresse&action=createdokument&id='.$id.'&cmd='.$value.'\';">
  <table width="110"><tr><td>'.ucfirst($value).'</td></tr></table></a>';

  $things = array('bestellung');
foreach($things as $key=>$value)
  $buttons_lieferant .= '
  <a href="#" onclick="if(!confirm(\''.ucfirst($value).' wirklich anlegen?\')) return false; else window.location.href=\'index.php?module=adresse&action=createdokument&id='.$id.'&cmd='.$value.'\';">
  <table width="110"><tr><td>'.ucfirst($value).'</td></tr></table></a>';



  if($kundennummer > 0) $buttons = $buttons_kunde;
  if($lieferantennummer > 0) $buttons .= $buttons_lieferant;

  if($buttons !=""){
    $this->app->Tpl->Set(BUTTONS,'<fieldset><legend>Neu Anlegen</legend>
        <div class="tabsbutton" align="center">'.$buttons.'</div></fieldset>');
  }

if(is_numeric($id))
  $anzahl_rollen = $this->app->DB->Select("SELECT SUM(id) FROM adresse_rolle WHERE adresse='$id'");
if($anzahl_rollen<1)
  $this->app->Tpl->Set(MESSAGE,"</form>
      <div class=\"success\">Die Adresse hat noch keine Rolle. Soll eine <a href=\"index.php?module=adresse&action=rollen&id=$id\">Rolle</a> anlegt werden: <form action=\"index.php?module=adresse&action=rollen&id=$id\" method=\"post\">
      <input type=\"checkbox\" value=\"1\" name=\"kunde\" checked>&nbsp;als Kunde markieren
      <input type=\"checkbox\" value=\"1\" name=\"lieferant\">&nbsp;als Lieferant markieren
      <input type=\"checkbox\" value=\"1\" name=\"mitarbeiter\">&nbsp;als Mitarbeiter markieren
      <input type=\"submit\" value=\"Jetzt markieren\" name=\"submitrolle\">
      </form></div>
      <form action=\"\" method=\"post\" name=\"eprooform\">");

  /* google maps */
  //$this->app->Tpl->Set(ONLOAD,'onload="load()" onunload="GUnload()"');

  //$key = "ABQIAAAAF-3x19QGjrDnM0qot_5RLhRPMKv2yVfFADlvP9s78xqAFkzplRTXptJWNlxCNcnzn7tujwTd6WlJDQ";
  /*
     $adresse= $this->app->DB->Select("SELECT CONCAT(strasse,'+,',plz,'+',ort,',+',land) FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
     $adresse = str_replace(' ','+',$adresse);
  //$adresse = "1600+Amphitheatre+Parkway,+Mountain+View,+CA";
  //$adresse = "Holzbachstrasse+4,+Augsburg";
  $geo = implode(file("http://maps.google.com/maps/geo?q=".$adresse."&output=xml&hl=de&key=".$key));  $geo = utf8_encode($geo);

  $xml = xml_parser_create();  xml_parse_into_struct($xml, $geo, $ausgabe);  xml_parser_free($xml);

  foreach($ausgabe as $a) {    
  if($a["tag"] == "COORDINATES") $position = $a["value"];    
  }
  $position = explode(",", $position);  $position = $position[1].",".$position[0];

  $this->app->Tpl->Set(JAVASCRIPT,'  
  <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAF-3x19QGjrDnM0qot_5RLhRPMKv2yVfFADlvP9s78xqAFkzplRTXptJWNlxCNcnzn7tujwTd6WlJDQ"
  type="text/javascript"></script>
  <script type="text/javascript">

  function load() {
  if (GBrowserIsCompatible()) {
  var map = new GMap2(document.getElementById("map"));
  map.setCenter(new GLatLng('.$position.'), 13);
  }
  }
  </script>    
  <div id="map" style="width: 500px; height: 300px"></div>
  '); 
   */
  // aktiviere tab 1
  $this->app->Tpl->Set(AKTIV_ADRESSE,"selected");
  $this->AdresseNummern($id);
  if($kundennummer==0) $kundennummer = "keine Kundennummer vorhanden";
  if($lieferantennummer==0)$lieferantennummer = "keine Lieferantennummer vorhanden";
  if($mitarbeiternummer==0)$mitarbeiternummer = "keine Mitarbeiternummer vorhanden";


  $this->app->Tpl->Set(KUNDENNUMMERANZEIGE,$kundennummer);
  $this->app->Tpl->Set(LIEFERANTENNUMMERANZEIGE,$lieferantennummer);
  $this->app->Tpl->Set(MITARBEITERNUMMERANZEIGE,$mitarbeiternummer);

  $this->AdresseMenu();
  $this->app->Tpl->Set(TABLE_ADRESSE_KONTAKTHISTORIE,"TDB");
  $this->app->Tpl->Set(TABLE_ADRESSE_ROLLEN,"TDB");

  $this->app->Tpl->Set(TABLE_ADRESSE_USTID,"TDB");


  $this->app->Tpl->Set(SUBSUBHEADING,"Rolle anlegen");
  if($this->app->Secure->GetPOST("rolleanlegen")!="")
  $this->app->Tpl->Set(AKTIV_TAB3,"selected");
  else
  $this->app->Tpl->Set(AKTIV_TAB1,"selected");

  $abweichende_rechnungsadresse= $this->app->DB->Select("SELECT abweichende_rechnungsadresse FROM adresse WHERE id='$id' LIMIT 1");
  $this->app->Tpl->Set(ABWEICHENDERECHNUNGSADRESSESTYLE,"none");
  if($abweichende_rechnungsadresse=="1") $this->app->Tpl->Set(ABWEICHENDERECHNUNGSADRESSESTYLE,"");


  if($this->app->erp->RechteVorhanden("multilevel","list"))
{		

  $sponsor= $this->app->DB->Select("SELECT sponsor FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
  if($sponsor <= 0 && $this->app->erp->Firmendaten("modul_mlm")=="1")
  {
    $this->app->Tpl->Add(MESSAGE,"
        <div class=\"error\">Achtung! Diese Adresse hat noch keinen Sponsor! Bitte stellen Sie einen Sponsor in den MLM Optionen ein!</div>");
  }
}

$liefersperre= $this->app->DB->Select("SELECT liefersperre FROM adresse WHERE id='$id' LIMIT 1");
if($liefersperre=="1")
{
  $this->app->Tpl->Add(MESSAGE,"<div class=\"error\">Achtung! Bei dieser Adresse ist die Liefersperre gesetzt!</div>");
}

parent::AdresseEdit();
$this->app->erp->MessageHandlerStandardForm();

}

function AdresseRollen()
{ 
  $this->AdresseMenu();

  $id = $this->app->Secure->GetGET("id");
  $reload = $this->app->Secure->GetGET("reload");
  $submitrolle = $this->app->Secure->GetPOST("submitrolle");

  $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$id' LIMIT 1");

  if($submitrolle!="")
  {

    if($this->app->Secure->GetPOST("kunde")=="1")
      $this->app->erp->AddRolleZuAdresse($id, "Kunde", "von", "Projekt", $projekt); 

    if($this->app->Secure->GetPOST("mitarbeiter")=="1")
      $this->app->erp->AddRolleZuAdresse($id, "Mitarbeiter", "von", "Projekt", $projekt); 

    if($this->app->Secure->GetPOST("lieferant")=="1")
      $this->app->erp->AddRolleZuAdresse($id, "Lieferant", "von", "Projekt", $projekt); 
  }

  $this->AdresseNummern($id);

  if($submitrolle!="")
  {
    header("Location: index.php?module=adresse&action=edit&id=$id");
    exit;
  }
  /*
     $widget = new WidgetAdresse_rolle($this->app,TAB1);
     $widget->form->SpecialActionAfterExecute("close_refresh",
     "index.php?module=adresse&action=rollen&id=$id&reload=true");

     $widget->Create();
   */

  if($this->app->Secure->GetPOST("rolleanlegen")!="")
  {
    $subjekt = $this->app->Secure->GetPOST("subjekt");
    $objekt = $this->app->Secure->GetPOST("objekt");
    if($objekt=="Projekt")
    {
      $projekt =  $this->app->Secure->GetPOST("parameter");
      $parameter = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
    } else {
      $gruppe=  $this->app->Secure->GetPOST("gruppe");
      $parameter = $this->app->DB->Select("SELECT id FROM gruppen WHERE CONCAT(name,' ',kennziffer)='$gruppe' LIMIT 1");
    }

    if(!($objekt=="Gruppe" && $parameter <=0))
    {
      $checkrolle_verband = $this->app->DB->Select("SELECT a.id FROM adresse_rolle a LEFT JOIN gruppen g ON g.id=a.parameter WHERE 
          (a.bis='0000-00-00' OR a.bis <=NOW()) AND a.adresse='$id' AND a.objekt='Gruppe' AND g.art='verband' LIMIT 1");

      $gruppe_is_verband = $this->app->DB->Select("SELECT id FROM gruppen WHERE id='$parameter' AND art='verband' LIMIT 1");
      /*
         if($checkrolle_verband > 0 && $gruppe_is_verband)
         {
         $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Jede Adresse (Kunde) darf nur in einem Verband sein! L&ouml;schen oder
         deaktivieren Sie die bestehende Rolle.</div>");
         header("Location: ./index.php?module=adresse&action=rollen&id=$id&msg=$msg");
         exit;
         } else 
       */
      $this->app->erp->AddRolleZuAdresse($id, $subjekt, "von", $objekt, $parameter); 
    }
    else {
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Rolle nicht gespeichert! Bitte geben Sie eine Gruppe an!</div>");
      header("Location: ./index.php?module=adresse&action=rollen&id=$id&msg=$msg");
      exit;
    }
  }	

  $this->app->YUI->AutoComplete("parameter","projektname",1);
  $this->app->YUI->AutoComplete("gruppe","gruppe");

  $subjekt= $this->app->erp->GetAdressSubject();
  $this->app->Tpl->Set(ROLLE_SELECT,$this->app->erp->GetSelect($subjekt,""));

  $this->app->Tpl->Parse(TAB1,"adresse_rolle.tpl");
  if($this->app->Secure->GetPOST("rolleanlegen")!="" || $reload=="true")
  {
    header("Location: index.php?module=adresse&action=rollen&id=$id");
    exit;
  } 


  $this->app->Tpl->Set(SUBSUBHEADING,"Rollen der Adresse");
  $this->app->Tpl->Set(TABTEXT,"Rollen");

  $table = new EasyTable($this->app);
  if($this->app->Conf->WFdbType=="postgre") {
    if(is_numeric($id))
      $table->Query("SELECT a.subjekt as Rolle, 
          CASE WHEN a.objekt='' THEN 'ALLE' ELSE a.objekt END as Zuordnung, 
          CASE WHEN a.parameter='' THEN 'ALLE' ELSE p.abkuerzung END as projekt, 
          to_char(a.von,'DD.MM.YYYY') as seit, a.id FROM adresse_rolle a 
          LEFT JOIN projekt p ON a.parameter=CAST(p.id AS text) WHERE a.adresse='$id'"); 
  } else {
    $table->Query("SELECT a.subjekt as Rolle, 
        if(a.objekt='','ALLE',a.objekt) as Zuordnung, 
        if(a.objekt='Projekt',if(a.parameter='','ALLE',p.abkuerzung),CONCAT(g.name,' ',g.kennziffer)) as auswahl, 
        DATE_FORMAT(a.von,'%d.%m.%Y') as seit, if(a.bis='0000-00-00','aktuell',DATE_FORMAT(a.bis,'%d.%m.%Y')) as bis,  a.id
        FROM adresse_rolle a  LEFT JOIN projekt p ON a.parameter=p.id 
        LEFT JOIN gruppen g ON g.id=a.parameter
        WHERE a.adresse='$id'");
  }

  $table->DisplayNew(TAB1NEXT, "<!--<a href=\"index.php?module=adresse&action=rolleeditpopup&frame=false&id=%value%\" 
      onclick=\"makeRequest(this);return false\"><img src=\"./themes/[THEME]/images/edit.png\" border=\"0\"></a>&nbsp;-->
      <a onclick=\"if(!confirm('Rolle wirklich l&ouml;schen?')) return false; else window.location.href='index.php?module=adresse&action=rolledelete&id=$id&sid=%value%';\"><img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\"></a><a onclick=\"var von =  prompt('Von Datum:','');   if((von !=null && von!='')) {var bis =  prompt('Bis Datum:',''); if((von !=null && von!='') ) { window.location.href='index.php?module=adresse&action=rolledatum&sid=%value%&id=".$id."&von='+von+'&bis='+bis;}}\"><img src=\"./themes/[THEME]/images/edit.png\" border=\"0\"></a>");


  $this->app->Tpl->Parse(PAGE,"tabview.tpl");
} 

function AdresseUSTPopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  $id = $this->app->Secure->GetGET("id");
  if($frame=="false")
  {
    // hier nur fenster größe anpassen
    $this->app->YUI->IframeDialog(650,530);
  } else {

    // nach page inhalt des dialogs ausgeben
    //$sid = $this->app->DB->Select("SELECT shop FROM shopexport_kampange WHERE id='$id' LIMIT 1");

    $this->AdresseUstprf();
    //$widget = new WidgetShopexport_kampange(&$this->app,PAGE);

    //$widget->form->SpecialActionAfterExecute("close_refresh",
    //  "index.php?module=marketing&action=kampangenedit&id=$sid");

    //$widget->Edit();

    $this->app->BuildNavigation=false;
  }
}



function AdresseUstprf()
{

  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET("id");

  //$this->app->Tpl->Set(HEADING,"Adresse (USTID-Pr&uuml;fung)");
  //$this->app->Tpl->Set(SUBSUBHEADING,"Umstatzsteuerpr&uuml;fungen");
  //Formula lieferadresse

  $table = new EasyTable($this->app);
  $table->Query("SELECT DATE_FORMAT(datum_online,GET_FORMAT(DATE,'EUR')) AS Datum, ustid,strasse, plz, ort,status,id FROM ustprf WHERE adresse='$id'");

  $table->DisplayNew(INHALT,"
      <a href=\"index.php?module=adresse&action=ustprfedit&id=$id&lid=%value%\">edit</a>
      <a href=\"index.php?module=adresse&action=ustprfneu&id=$id\">new</a>
      ","");
  //"<a href=\"index.php?module=adresse&action=ustprfneu&id=$id\">Neue USTID-Pr&uuml;fung anlegen</a>");

  $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");
  $this->app->Tpl->Set(AKTIV_TAB1,"selected");

  $this->app->Tpl->Set(INHALT,"");


  if($this->app->Secure->GetPOST("name")!="")
  {
    //speichern
    $lid = $this->app->FormHandler->FormToDatabase("ustprf","adresse",$id);
    //$this->AdresseUstprf();
    //$lid = $this->app->DB->GetInsertID();
    header("Location: index.php?module=adresse&action=ustprfedit&id=$id&lid=$lid");
    exit;
  }

  //$this->app->Tpl->Set(MESSAGE,"<div class=\"warning\">Adresse unterschiedlich!!! <br>Soll Adressdatensatz vom Kunden angepasst werden?</div>");

  //$this->app->Tpl->Set(SUBHEADING,"UST-ID Pr&uuml;fung neu anlegen");
  $this->app->FormHandler->FormGetVars("adresse",$id);

  $this->app->Tpl->Parse(INHALT,"ustprfneu.tpl");
  $this->app->Tpl->Parse(TAB2,"rahmen_submit.tpl");

  $this->app->Tpl->Parse(PAGE,"ustuebersicht.tpl");
}

function AdresseUstprfEdit()
{
  $id = $this->app->Secure->GetGET("id");
  $lid = $this->app->Secure->GetGET("lid");

  // $this->app->Tpl->Set(HEADING,"Adresse (USTID-Pr&uuml;fung)");

  $ust = $this->app->Secure->GetPOST("ustid");  
  //$ust = $this->app->Secure->GetPOST("ustid2");
  $name = $this->app->Secure->GetPOST("name");
  $ort = $this->app->Secure->GetPOST("ort");
  $strasse = $this->app->Secure->GetPOST("strasse");
  $plz = $this->app->Secure->GetPOST("plz");
  //$druck = $this->app->Secure->GetPOST("druck");

  if($this->app->Secure->GetPOST("aendern") != "")
  {
    //firmenname
    //ort
    //strasse
    //plz
    $this->app->DB->Update("UPDATE auftrag SET name='$name', ort='$ort',ustid='$ust', strasse='$strasse', plz='$plz' WHERE status='freigegeben' AND adresse='$id'");
    $this->app->DB->Update("UPDATE adresse SET name='$name', ort='$ort',ustid='$ust', strasse='$strasse', plz='$plz' WHERE id='$id'");
    $this->app->DB->Update("UPDATE ustprf SET name='$name' WHERE id='$lid'");
    $this->app->DB->Update("UPDATE ustprf SET plz='$plz' WHERE id='$lid'");
    $this->app->DB->Update("UPDATE ustprf SET ort='$ort' WHERE id='$lid'");
    $this->app->DB->Update("UPDATE ustprf SET ustid='$ust' WHERE id='$lid'");
    $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">Adresse und USTID bei Kunde und offenen Auftraegen genaendert!</div>");  
  }


  $ust = str_replace(" ","",$ust);
  $status = $this->app->DB->Select("SELECT status FROM ustprf WHERE id='$lid' LIMIT 1");

  $datum_online = $this->app->DB->Select("SELECT datum_online FROM ustprf WHERE id='$lid' LIMIT 1");
  if($this->app->Secure->GetPOST("online")!="")
  {
    if($status!="erfolgreich" && $status!="fehlgeschlagen")
    {     

      if(!$this->app->erp->CheckUSTFormat($ust)){
        $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">UST-Nr. bzw. Format fuer Land ist nicht korrekt</div>");
      }else{
        //$UstStatus = $this->app->erp->CheckUst($ust,"SE556459933901","Wind River AB","Kista","Finlandsgatan 52","16493","ja");	

        $UstStatus = $this->app->erp->CheckUst("DE263136143", $ust, $name, $ort, $strasse, $plz, $druck="nein");

        if(is_array($UstStatus) && !is_numeric($UstStatus))
        {
          $tmp = new USTID();
          $msg = $tmp->errormessages($UstStatus['ERROR_CODE']);

          if($UstStatus['ERROR_CODE']==200)
            $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">UST g&uuml;ltig aber Name, Ort oder PLZ wird anders geschrieben!</div>");  
          else
            $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"error\">Fehlgeschlagen Code:<br>{$UstStatus['ERROR_CODE']}($msg)</div>");  

          $this->app->Tpl->Set(ERG_NAME, $UstStatus['ERG_NAME']);
          $this->app->Tpl->Set(ERG_PLZ, $UstStatus['ERG_PLZ']);
          $this->app->Tpl->Set(ERG_STR, $UstStatus['ERG_STR']);
          $this->app->Tpl->Set(ERG_ORT, $UstStatus['ERG_ORT']);

        } else if($UstStatus==1){
          $this->app->Tpl->Set(STATUS,"<div style=\"background-color: green;\">Vollst&auml;ndig</div>");
          // jetzt brief bestellen! 
          // $UstStatus = $this->app->erp->CheckUst("DE263136143", $ust, $name, $ort, $strasse, $plz, $druck="ja");
          // $this->app->DB->Insert('INSERT INTO ustprf_protokoll (ustprf_id, zeit, bemerkung, bearbeiter)  VALUES ('.$lid.',"'.date("Y-m-d H:i:s").'","Online-Abfrage OK + Brief bestellt", "'.$this->app->User->GetName().'")');
          $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">Online-Pr&uuml;fung erfolgreich!</div>");
          $this->app->DB->Update('UPDATE ustprf SET datum_online=NOW(),	status="erfolgreich" WHERE id='.$lid.'');
        } else {
          $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">Allgemeiner Fehler! Es wurde kein Brief bestellt!<br><br>".$UstStatus."</div>");
          $this->app->DB->Insert('INSERT INTO ustprf_protokoll (ustprf_id, zeit, bemerkung, bearbeiter)  VALUES ('.$lid.',"'.date("Y-m-d H:i:s").'","'.$UstStatus.'", "'.$this->app->User->GetName().'")');
          $this->app->DB->Update('UPDATE ustprf SET datum_online=NOW(),	status="allgemeiner fehler" WHERE id='.$lid.'');
        }

      }			
    } else {

      if($status=="fehlgeschlagen")
        $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">Die Abfrage ist inaktiv da sie als fehlgeschlagen bereits markiert worden ist!</div>");
      else
      {
        $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">Online-Pr&uuml;fung erfolgreich!</div>");
      }
    }
  } 

  $briefbestellt = $this->app->DB->Select("SELECT briefbestellt FROM ustprf WHERE id='$lid' LIMIT 1");

  if($this->app->Secure->GetPOST("brief")!="" && $briefbestellt=="0000-00-00" && $datum_online!="0000-00-00 00:00:00")
  {
    $UstStatus = $this->app->erp->CheckUst("DE263136143", $ust, $name, $ort, $strasse, $plz, $druck="ja");
    $this->app->DB->Insert('INSERT INTO ustprf_protokoll (ustprf_id, zeit, bemerkung, bearbeiter)  VALUES ('.$lid.',"'.date("Y-m-d H:i:s").'","Online-Abfrage OK + Brief bestellt", "'.$this->app->User->GetName().'")');
    $this->app->DB->Update('UPDATE ustprf SET briefbestellt=NOW() WHERE id='.$lid.'');
    $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">Brief wurde bestellt!</div>");
  } else if ($briefbestellt!="0000-00-00")
  {
    $briefbestellt = $this->app->String->Convert($briefbestellt,"%1-%2-%3","%3.%2.%1");
    $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">Brief wurde bereits am $briefbestellt bestellt!</div>");
    $this->app->Tpl->Set(BESTELLT,$briefbestellt);
  }
  else if ($datum_online=="0000-00-00 00:00:00")
  {
    $briefbestellt = $this->app->String->Convert($briefbestellt,"%1-%2-%3","%3.%2.%1");
    $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">Brief kann auf Grund erfolgloser Online-Pr&uuml;fung nicht bestellt werden!</div>");
    $this->app->Tpl->Set(BESTELLT,$briefbestellt);
  }


  if($this->app->Secure->GetPOST("benachrichtigen") != "")
  {

    if($status=="benachrichtig" || $status=="fehlgeschlagen")
    {
      if($status=="fehlgeschlagen")
        $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">UST-Pr&uuml;fung wurde bereits als fehlgeschlagen markiert! Kunde wurde ebenfalls bereits benachrichtigt!</div>");
      else
        $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">Kunde wurde bereits benachrichtigt!</div>");
    } else {
      //echo "ACHTUNG hier muss noch eine MAIL versendet werden!!!!";

      $mailtext = $this->app->Secure->GetPOST("mailtext");

      $to = $this->app->DB->Select("SELECT email FROM adresse WHERE id='$id' LIMIT 1"); 
      $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$id' LIMIT 1"); 
      $to_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$id' LIMIT 1"); 

      $this->app->erp->MailSend($this->app->erp->GetFirmaMail(),$this->app->erp->GetFirmaName(),$to,$to_name,"Your Tax ID number",$mailtext);
      $this->app->DB->Insert("INSERT INTO dokumente_send (id,dokument,zeit,bearbeiter,adresse,projekt,art,betreff,text) VALUES('','vatid',NOW(),'".$this->app->User->GetName()."','$id','$projekt','email','Your Tax ID number','$mailtext')");

      $this->app->DB->Insert('INSERT INTO ustprf_protokoll (ustprf_id, zeit, bemerkung, bearbeiter)  VALUES ('.$lid.',"'.date("Y-m-d H:i:s").'","Kunde wurde benachrichtigt", "'.$this->app->User->GetName().'")');
      $this->app->DB->Update('UPDATE ustprf SET datum_online=NOW(),	status="benachrichtig" WHERE id='.$lid.'');
      $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">Kunde wurde per Mail benachrichtigt!</div>");
    }
  }

  if($this->app->Secure->GetPOST("manuellok") != "")
  {
    $this->app->DB->Insert('INSERT INTO ustprf_protokoll (ustprf_id, zeit, bemerkung, bearbeiter)  VALUES ('.$lid.',"'.date("Y-m-d H:i:s").'","Manuell auf OK gesetzt", "'.$this->app->User->GetName().'")');
    $this->app->DB->Update('UPDATE ustprf SET briefbestellt=NOW(),datum_online=NOW(),status="erfolgreich" WHERE id='.$lid.'');
    $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">Wurde manuell auf OK gesetzt!</div>");
  }



  if($this->app->Secure->GetPOST("fehlgeschlagen") != "" && $briefbestellt=="0000-00-00")
  {
    if($status=="fehlgeschlagen")
    {
      $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">UST-Pr&uuml;fung wurde bereits als fehlgeschlagen markiert! Kunde wurde ebenfalls bereits benachrichtigt!</div>");
    } else 
    {
      echo "ACHTUNG hier muss noch eine MAIL versendet werden!!!! wenn man das will??????";
      $this->app->DB->Update('UPDATE ustprf SET datum_online=NOW(),	status="fehlgeschlagen" WHERE id='.$lid.'');
      $this->app->DB->Insert('INSERT INTO ustprf_protokoll (ustprf_id, zeit, bemerkung, bearbeiter)  VALUES ('.$lid.',"'.date("Y-m-d H:i:s").'","Abfrage als fehlgeschlagen markiert", "'.$this->app->User->GetName().'")');
      $this->app->Tpl->Set(STATUSMELDUNG,"<div class=\"warning\">UST-Pr&uuml;fung wurde als fehlgeschlagen markiert! Kunde wurde per Mail benachrichtigt!</div>");
    }
  }

  $datum_brief = $this->app->DB->Select("SELECT datum_brief FROM ustprf WHERE id='$lid' LIMIT 1");
  if($datum_brief!='0000-00-00')
  {
    $datum_brief = $this->app->String->Convert($datum_brief ,"%1-%2-%3","%3.%2.%1");
    $this->app->Tpl->Set(EINGANG,$datum_brief);

  }


  $this->AdresseMenu();

  //$this->app->Tpl->Set(SUBHEADING,"UST-ID Pr&uuml;fung neu anlegen");
  $this->app->FormHandler->FormGetVars("ustprf",$lid);

  $name = $this->app->DB->Select("SELECT name FROM ustprf WHERE id='$lid'");
  $ort = $this->app->DB->Select("SELECT ort FROM ustprf WHERE id='$lid'");
  $land = $this->app->DB->Select("SELECT land FROM ustprf WHERE id='$lid'");

  $this->app->Tpl->Set(SUCHE,"$name+$ort+$land");

  $this->app->Tpl->Set(ID,$id);

  if($ust != "")
    $this->app->Tpl->Set(USTID, $ust);

  if($this->app->Secure->GetPOST("name") != "")
    $this->app->Tpl->Set(NAME, $this->app->Secure->GetPOST("name"));  

  if($this->app->Secure->GetPOST("ort") != "")
    $this->app->Tpl->Set(ORT, $this->app->Secure->GetPOST("ort"));

  if($this->app->Secure->GetPOST("plz") != "")
    $this->app->Tpl->Set(PLZ, $this->app->Secure->GetPOST("plz"));

  if($this->app->Secure->GetPOST("strasse") != "")
    $this->app->Tpl->Set(STRASSE, $this->app->Secure->GetPOST("strasse"));


  $this->app->Tpl->Set(ID,$this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1"));
  $this->app->Tpl->Set(LAND,$this->app->DB->Select("SELECT land FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1"));
  $this->app->Tpl->Set(STATUS,$this->app->DB->Select("SELECT status FROM ustprf WHERE id='$lid' LIMIT 1"));

  //$this->AdresseProtokoll($lid);

  $this->app->Tpl->Parse(INHALT,"ustprfedit.tpl");
  $this->app->Tpl->Parse(PAGE,"rahmen_submit.tpl");
}

function AdresseProtokoll($lid)
{
  if($lid!=""){
    $table = new EasyTable($this->app);

    $table->Query("SELECT DATE_FORMAT(zeit, '%d.%m.%Y %H:%i') AS Datum, bemerkung,bearbeiter FROM ustprf_protokoll WHERE ustprf_id='$lid' ORDER BY zeit DESC", 0, "noAction");

    $table->DisplayNew(PROTOKOLL,"", "noAction");

  }
}


function AdresseKundeArtikel()
{

  $this->AdresseMenu();

  $this->app->YUI->TableSearch(TAB1,"adresseartikel");
  $this->app->YUI->TableSearch(TAB2,"adresseseriennummern");
  $this->app->YUI->TableSearch(TAB3,"adresse_artikel_geraet");
  $this->app->YUI->TableSearch(TAB4,"adresse_artikel_serviceartikel");


  $this->app->Tpl->Parse(PAGE,"adresse_artikel.tpl");

}

function AdresseLieferantArtikel()
{
  $this->AdresseMenu();

  $this->app->YUI->TableSearch(TAB1,"lieferantartikel");
  $this->app->Tpl->Parse(PAGE,"adresse_lieferprogramm.tpl");

}

function AdresseBestellungMarkiert()
{
  $id = $this->app->Secure->GetGET("id");
  $sid = $this->app->Secure->GetGET("sid");


  // markieren

  $geliefert = $this->app->DB->Select("SELECT geliefert FROM bestellung_position WHERE id='$sid' LIMIT 1");
  $menge  = $this->app->DB->Select("SELECT menge FROM bestellung_position WHERE id='$sid' LIMIT 1");
  $tmp = $menge - $geliefert;
  if($tmp < 0) $tmp=0;
  $this->app->DB->Update("UPDATE bestellung_position SET abgeschlossen='1', mengemanuellgeliefertaktiviert='$tmp', geliefert='$menge',manuellgeliefertbearbeiter='".$this->app->User->GetName()."' WHERE id='$sid' LIMIT 1");

  header("Location: index.php?module=adresse&action=offenebestellungen&id=$id&cmd=offeneartikel");
  exit;


}


function AdresseOffeneBestellungen()
{

  $cmd = $this->app->Secure->GetGET("cmd");
  $id = $this->app->Secure->GetGET("id");
  $this->app->Tpl->Set(ID,$id);


  $this->AdresseMenu();


  $this->app->Tpl->Set(UEBERSCHRIFT1,"Bestellungen");
  $this->app->Tpl->Set(INFORMATIONSTEXT,"Alle Bestellungen bei aktuellem Lieferant.");
  //Formula lieferadresse
  $table = new EasyTable($this->app);
  $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%Y') as Bestelldatum, if(belegnr,belegnr,'ohne Nummer') as beleg, name, status, 
      DATE_FORMAT(versendet_am,'%d.%m.%Y') versendet_am, versendet_durch, versendet_per, id
      FROM bestellung WHERE adresse='$id' order by datum DESC, id DESC LIMIT 10");
  $table->DisplayNew(INHALT, "<a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.png\"></a>
      <a href=\"index.php?module=bestellung&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/pdf.png\"></a>
      <a onclick=\"if(!confirm('Wirklich kopieren?')) return false; else window.location.href='index.php?module=bestellung&action=copy&id=%value%';\">
      <img src=\"./themes/[THEME]/images/copy.png\" border=\"0\"></a>
      ");
  $summe = $this->app->DB->Select("SELECT SUM(bp.menge*bp.preis) FROM bestellung_position bp LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN projekt p ON p.id=bp.projekt WHERE b.adresse='$id'");
  $this->app->Tpl->Set(EXTEND,"Gesamt: $summe EUR");

  // easy table mit arbeitspaketen YUI als template 
  $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");
  $this->app->Tpl->Set(TAB1SELECT,"selected");
  $this->app->Tpl->Set(EXTEND,"");
  $this->app->Tpl->Set(INHALT,"");




  $this->app->Tpl->Set(UEBERSCHRIFT1,"Offen Artikel");
  $this->app->Tpl->Set(INFORMATIONSTEXT,"Alle noch nicht gelieferten Artikel bei aktuellem Lieferant. Jederzeit kann mit dem Pfeil eine Artikel als geliefert markiert werden. <br>Hinweis: Eigentlich
      sollte jeder Artikel durch die Paketdistribution aus dieser Liste bei der Lieferung verschwinden.");

  $this->app->Tpl->Set(SUBSUBHEADING,"Offene Artikel");
  //Formula lieferadresse
  $table = new EasyTable($this->app);
  $table->Query("SELECT DATE_FORMAT(b.datum,'%d.%m.%Y') as Bestellung, LEFT(bp.bezeichnunglieferant,20) as name, a.nummer as 'Artikel-Nr.', bp.bestellnummer as 'best.-Nr',
      if(bp.lieferdatum,DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum,
      p.abkuerzung, bp.menge, bp.geliefert, FORMAT(bp.preis,2) as preis, bp.id
      FROM bestellung_position bp LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN artikel a ON a.id=bp.artikel LEFT JOIN projekt p ON p.id=bp.projekt WHERE b.adresse='$id' AND bp.geliefert < bp.menge AND bp.abgeschlossen IS NULL ORDER by datum DESC");
  $table->DisplayNew(INHALT, "
      <a onclick=\"if(!confirm('Wirklich als geliefert markieren?')) return false; else window.location.href='index.php?module=adresse&action=adressebestellungmarkieren&sid=%value%&id=[ID]';\">
      <img src=\"./themes/[THEME]/images/right.png\"  width=\"18\"border=\"0\"></a>
      ","geliefert");

  // easy table mit arbeitspaketen YUI als template 
  $this->app->Tpl->Parse(TAB2,"rahmen70.tpl");

  $this->app->Tpl->Set(TAB2SELECT,"selected");
  $this->app->Tpl->Set(INHALT,"");



  $this->app->Tpl->Set(UEBERSCHRIFT1,"Abgeschlossene Artikel");
  $this->app->Tpl->Set(INFORMATIONSTEXT,"Alle abgeschlossenen Artikel der Bestellungen.");

  //Formula lieferadresse
  $table = new EasyTable($this->app);
  $table->Query("SELECT DATE_FORMAT(b.datum,'%d.%m.%Y') as Bestellung, b.belegnr, LEFT(bp.bezeichnunglieferant,20) as name, a.nummer as 'Artikel-Nr.', bp.bestellnummer as nummer, 
      if(bp.lieferdatum,DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum, p.abkuerzung as projekt, bp.menge, bp.geliefert, bp.id
      FROM bestellung_position bp LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN artikel a ON a.id=bp.artikel LEFT JOIN projekt p ON p.id=bp.projekt WHERE b.adresse='$id' AND (bp.geliefert >= bp.menge  OR bp.abgeschlossen='1') ORDER by datum DESC");
  $table->DisplayNew(INHALT,"preis", "noAction");

  // easy table mit arbeitspaketen YUI als template 
  $this->app->Tpl->Parse(TAB3,"rahmen70.tpl");

  $this->app->Tpl->Parse(PAGE,"adressebestellung.tpl");

}


function AdresseAnsprechpartnerPopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  $id = $this->app->Secure->GetGET("id");
  /*
  //if($frame=="false")
  //{
  // hier nur fenster größe anpassen
  //  $this->app->YUI->IframeDialog(500,400);
  //} else {
  // nach page inhalt des dialogs ausgeben
  //      $sid = $this->app->DB->Select("SELECT artikel FROM artikel_artikelgruppe WHERE id='$id' LIMIT 1");
  //$widget = new WidgetVerkaufspreise(&$this->app,PAGE);
  //$widget->form->SpecialActionAfterExecute("close_refresh",
  //  "index.php?module=artikel&action=verkauf&id=$sid");


  // neue warengruppe hinzugefuegt
  $artikelgruppe = $this->app->Secure->GetPOST("artikelgruppe");
  $ok= $this->app->Secure->GetPOST("ok");
  if($artikelgruppe!="" && $ok=="") $this->app->DB->Insert("INSERT INTO artikel_artikelgruppe (id,artikel,artikelgruppe) VALUES ('','$id','$artikelgruppe')");


  //warengruppe geloescht
  $sid= $this->app->Secure->GetGET("sid");
  $cmd= $this->app->Secure->GetGET("cmd");
  if($sid!="" && $cmd=="del") $this->app->DB->DELETE("DELETE FROM artikel_artikelgruppe WHERE id='$sid' LIMIT 1");
  if($sid!="" && $cmd=="image") $this->app->DB->DELETE("UPDATE artikel SET standardbild='$sid' WHERE id='$id' LIMIT 1");

  $name = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");
  $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
  $this->app->Tpl->Set(SUBSUBHEADING,"Online-Shop Attribute: $name ($nummer)");
  $this->app->Tpl->Set(AKTIV_TAB1,"selected");

  //Warengruppen
  $tmp = new EasyTable($this->app);
  $tmp->Query("SELECT a.bezeichnung, aa.id FROM artikel_artikelgruppe aa LEFT JOIN artikelgruppen a ON a.id=aa.artikelgruppe WHERE artikel='$id'");
  $tmp->DisplayNew(WARENGRUPPEN,"<a href=\"#\" onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=artikel&cmd=del&action=onlineshop&id=$id&sid=%value%';\"><img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\"></a>");

  $shop = $this->app->DB->Select("SELECT shop FROM artikel WHERE id='$id' LIMIT 1");

  $arr = $this->app->DB->SelectArr("SELECT bezeichnung,id FROM artikelgruppen WHERE shop='$shop'");

  foreach($arr as $key=>$value)
  $html.="<option value=\"{$value[id]}\">{$value[bezeichnung]}</option>";

  $this->app->Tpl->Add(WARENGRUPPEN,"<center><select name=\"artikelgruppe\">$html</select>");
  $this->app->Tpl->Add(WARENGRUPPEN,"<input type=submit value=\"hinzuf&uuml;gen\"></center>");

  // standard bild
  $standardbild = $this->app->DB->Select("SELECT standardbild FROM artikel WHERE id='$id'");
  $tmp = new EasyTable($this->app);
  $tmp->Query("SELECT d.titel, d.id FROM datei d LEFT JOIN datei_stichwoerter s ON d.id=s.datei  
  LEFT JOIN datei_version v ON v.datei=d.id
  WHERE s.objekt='Artikel' AND s.parameter='$id' AND s.subjekt='Shopbild' AND d.geloescht=0");

  $tmp->DisplayNew(HAUPTBILD,
  "<a href=\"#\" onclick=\"if(!confirm('Als Standard definieren?')) return false; else window.location.href='index.php?module=artikel&action=onlineshop&cmd=image&id=$id&sid=%value%';\"><img src=\"./themes/[THEME]/images/ack.png\" border=\"0\"></a>");

  $standardbild_name = $this->app->DB->Select("SELECT titel FROM datei WHERE id='$standardbild'");
  $this->app->Tpl->Add(HAUPTBILD,"<br>Standardbild: <b>$standardbild_name</b>");



  $this->app->Tpl->Parse(PAGE,"onlineshop.tpl");
   */
  $this->AdresseAnsprechpartner();
  $this->app->BuildNavigation=false;

}



function AdresseAnsprechpartner()
{
  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET("id");
  $lid = $this->app->Secure->GetGET("lid");
  $delete = $this->app->Secure->GetGET("delete");
  $create= $this->app->Secure->GetGET("create");

  $iframe = $this->app->Secure->GetGET("iframe");

  if($iframe=="true")
    $this->app->BuildNavigation=false;


  if($delete==1)
  {
    $this->app->DB->Delete("DELETE FROM ansprechpartner WHERE id='$lid' AND adresse='$id' LIMIT 1"); 
    header("Location: index.php?module=adresse&action=ansprechpartner&id=$id&iframe=$iframe");
    exit;
  }
  /*
     if($create==1)
     {
     $this->app->DB->Delete("INSERT INTO lieferadressen (id,name,adresse) VALUES ('','Neuer Datensatz','$id')"); 
     $lid = $this->app->DB->GetInsertID();
     header("Location: index.php?module=adresse&action=lieferadresse&id=$id&lid=$lid&iframe=$iframe");
     exit;
     }
   */

  // neues arbeitspaket
  $widget = new WidgetAnsprechpartner($this->app,TAB1);
  $widget->form->SpecialActionAfterExecute("none",
      "index.php?module=adresse&action=ansprechpartner&id=$id&iframe=$iframe");
  if($lid > 0)
  {
    $widget->form->SpecialActionAfterExecute("none",
        "index.php?module=adresse&action=ansprechpartner&id=$id&iframe=$iframe");
    $widget->Edit();
  }
  else
  {
    $widget->Create();
  }

  if($iframe=="true") $einfuegen = "<a onclick=\"Ansprechpartner('%value%'); parent.closeIframe();\"><img src=\"./themes/[THEME]/images/down.png\" border=\"0\"></a>";
  //Formula lieferadresse
  /*    $table = new EasyTable($this->app);
        $table->Query("SELECT name, bereich, email, telefon, telefax, mobil, id FROM ansprechpartner WHERE adresse='$id' AND name!='Neuer Datensatz' ORDER by name,strasse");
        $table->DisplayNew(TABNEXT, "<a href=\"index.php?module=adresse&action=ansprechpartner&id=$id&iframe=$iframe&lid=%value%\">
        <img src=\"./themes/[THEME]/images/edit.png\" border=\"0\"></a><a onclick=\"if(!confirm('Ansprechpartner wirklich l&ouml;schen?')) return false; else window.location.href='index.php?module=adresse&action=ansprechpartner&delete=1&iframe=$iframe&id=$id&lid=%value%';\"><img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\"></a>$einfuegen","","id",$id);
   */
  // easy table mit arbeitspaketen YUI als template 
  $this->app->YUI->TableSearch(TAB1,"adresse_ansprechpartnerlist");
  //$this->app->Tpl->Parse(TAB1,"rahmen70.tpl");
  $this->app->Tpl->Set(AKTIV_TAB1,"selected");

  if($iframe=="true")
    $this->app->Tpl->Parse(PAGE,"ansprechpartneruebersicht_popup.tpl");
  else
    $this->app->Tpl->Parse(PAGE,"ansprechpartneruebersicht.tpl");
}

function AdresseAnsprechpartnerEditPopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  $id = $this->app->Secure->GetGET("id");

  if($frame=="false")
  {
    // hier nur fenster größe anpassen
    $this->app->YUI->IframeDialog(600,320);
  } else {
    // nach page inhalt des dialogs ausgeben
    $widget = new WidgetAnsprechpartner($this->app,PAGE);
    $adresse = $this->app->DB->Select("SELECT adresse FROM ansprechpartner WHERE id='$id' LIMIT 1");
    $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=adresse&action=ansprechpartner&id=$adresse");

    $widget->Edit();
    $this->app->BuildNavigation=false;
  }
}



function AdresseLieferadressePopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  $id = $this->app->Secure->GetGET("id");
  $this->AdresseLieferadresse();
  $this->app->BuildNavigation=false;
}


function AdresseLieferadresse()
{
  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET("id");
  $lid = $this->app->Secure->GetGET("lid");
  $delete = $this->app->Secure->GetGET("delete");
  $create= $this->app->Secure->GetGET("create");
  $module= $this->app->Secure->GetGET("module");
  $action= $this->app->Secure->GetGET("action");

  $iframe = $this->app->Secure->GetGET("iframe");

  if($iframe=="true")
    $this->app->BuildNavigation=false;


  if($delete==1)
  {
    $this->app->DB->Delete("DELETE FROM lieferadressen WHERE id='$lid' AND adresse='$id' LIMIT 1"); 
    header("Location: index.php?module=adresse&action=lieferadresse&id=$id&iframe=$iframe");
    exit;
  }

  // neues arbeitspaket
  $widget = new WidgetLieferadressen($this->app,TAB1);

  $widget->form->SpecialActionAfterExecute("none",
      "index.php?module=adresse&action=lieferadresse&id=$id&iframe=$iframe");

  if($lid > 0)
  {
    $widget->form->SpecialActionAfterExecute("none",
        "index.php?module=adresse&action=lieferadresse&id=$id&iframe=$iframe");
    $widget->Edit();
  }
  else
  {
    $widget->Create();
  }

  //Formula lieferadresse
  //		if($action=="lieferadressepopup")
  //		{
  //   	if($iframe=="true") $einfuegen = "<a onclick=\"LieferadresseLS('%value%'); parent.closeIframe();\"><img src=\"./themes/[THEME]/images/down.png\" border=\"0\"></a>";
  //	} else {
  if($iframe=="true") $einfuegen = "<a onclick=\"Lieferadresse('%value%'); parent.closeIframe();\"><img src=\"./themes/[THEME]/images/down.png\" border=\"0\"></a>";
  //	}
  /*
     $table = new EasyTable($this->app);
     $table->Query("SELECT name, strasse, land, plz, ort,id FROM lieferadressen WHERE adresse='$id' AND name!='Neuer Datensatz' ORDER by name,strasse");
     $table->DisplayNew(TABNEXT, "<a href=\"index.php?module=adresse&action=lieferadresse&id=$id&iframe=$iframe&lid=%value%\">
     <img src=\"./themes/[THEME]/images/edit.png\" border=\"0\"></a><a onclick=\"if(!confirm('Lieferadresse wirklich l&ouml;schen?')) return false; else window.location.href='index.php?module=adresse&action=lieferadresse&delete=1&iframe=$iframe&id=$id&lid=%value%';\"><img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\"></a>$einfuegen","","id",$id);
   */
  // easy table mit arbeitspaketen YUI als template 
  $this->app->YUI->TableSearch(TAB1,"adresse_lieferadressenlist");
  //	$this->app->Tpl->Parse(TAB1,"rahmen70.tpl");
  $this->app->Tpl->Set(AKTIV_TAB1,"selected");

  if($iframe=="true")
    $this->app->Tpl->Parse(PAGE,"lieferadressenuebersicht_popup.tpl");
  else
    $this->app->Tpl->Parse(PAGE,"lieferadressenuebersicht.tpl");

}

function AdresseLieferadressenEditPopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  $id = $this->app->Secure->GetGET("id");

  if($frame=="false")
  {
    // hier nur fenster größe anpassen
    $this->app->YUI->IframeDialog(600,320);
  } else {
    // nach page inhalt des dialogs ausgeben
    $widget = new WidgetLieferadressen($this->app,PAGE);
    $adresse = $this->app->DB->Select("SELECT adresse FROM lieferadressen WHERE id='$id' LIMIT 1");
    $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=adresse&action=lieferadresse&id=$adresse");

    $widget->Edit();
    $this->app->BuildNavigation=false;
  }
}


function AdresseBrief()
{
  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET("id");
  $sid = $this->app->Secure->GetGET("sid");


  // NEU füllen
  //   $widget = new WidgetBrief(&$this->app,TAB2);
  //   $widget->form->SpecialActionAfterExecute("none",
  //       "index.php?module=adresse&action=brief&id=$id");
  //   $widget->Create();

  /* START */
  $adresse = $id;
  //$this->app->erp->DokumentMask(TAB2,"brieffax",$sid,$adresse);
  /* ENDE */



  // UEBERSICHT füllen
  $this->app->Tpl->Set(HEADING,"Adresse");
  //    $this->app->Tpl->Set(SUBHEADING,"Korrespondenzen <a class=\"fancy_briefeditpopup\" data-fancybox-type=\"iframe\" href=\"./index.php?module=korrespondenz&action=create&user=$id\">[Neu]</a>");

  $buttons .= "
    <a onclick=\"KorrPopup('./index.php?module=korrespondenz&action=create&user=$id')\" href=\"#\">
    <table width=\"110\"><tr><td>Korrespondenz</td></tr></table></a>";
  $buttons .= "
    <a onclick=\"KorrPopup('./index.php?module=korrespondenz&action=create&user=$id')\" href=\"#\">
    <table width=\"110\"><tr><td>E-Mail</td></tr></table></a>";
  $buttons .= "
    <a onclick=\"KorrPopup('./index.php?module=korrespondenz&action=create&user=$id')\" href=\"#\">
    <table width=\"110\"><tr><td>Telefonat</td></tr></table></a>";
  $buttons .= "
    <a onclick=\"KorrPopup('./index.php?module=korrespondenz&action=create&user=$id')\" href=\"#\">
    <table width=\"110\"><tr><td>Notiz</td></tr></table></a>";





  $this->app->Tpl->Set(SUBHEADING,'<fieldset><legend>Neu Anlegen</legend><div class="tabsbutton" align="center">'.$buttons.'</div></fieldset><br><br>Korrespondenzen');


  $adresse = $this->app->User->GetAdresse();


  //Korrespondenzen
  $table = new EasyTable($this->app);
  $table->Query("SELECT DATE_FORMAT(d.created,'%d.%m.%Y %H:%i') as 'Erstellt am', 													
      d.betreff, d.von, d.send_as as Art, if(d.sent,'<center>ja</center>','') as versendet,d.id FROM dokumente d
      WHERE d.adresse_to='$id' AND (d.deleted=0 OR d.deleted IS NULL) AND d.typ='brieffax' ORDER by d.created DESC");

  $table->DisplayNew(INHALT, "<a onClick=\"javascript:KorrPopup('index.php?module=korrespondenz&action=edit&user=$id&id=%value%');makeRequest(this);return false\" href=\"#\">
      <img src=\"./themes/[THEME]/images/edit.png\" border=\"0\"></a>
      <a onclick=\"if(!confirm('Wirklich  l&ouml;schen?')) return false; else window.location.href='index.php?module=korrespondenz&action=delete&id=%value%';\">
      <img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\"></a>
      <a href=\"index.php?module=korrespondenz&action=pdf&id=%value%\"><img src=\"./themes/[THEME]/images/pdf.png\" border=\"0\"></a>");

  /*
     $table->Query("SELECT DATE_FORMAT(d.zeit,'%d.%m.%Y %H:%i') as datum, d.betreff, d.bearbeiter,p.abkuerzung as projekt,d.art, if(d.versendet,'<center>ja</center>','') as versendet,d.id FROM dokumente_send d  LEFT JOIN projekt p ON p.id=d.projekt
     WHERE d.adresse='$id' AND d.geloescht=0 AND d.dokument='brieffax' ORDER by d.zeit DESC");
     $table->DisplayNew(INHALT, "<a class=\"fancy_briefeditpopup\" data-fancybox-type=\"iframe\" href=\"index.php?module=adresse&action=briefeditpopup&frame=false&id=$id&sid=%value%\" 
     onclick=\"makeRequest(this);return false\"><img src=\"./themes/[THEME]/images/edit.png\" border=\"0\"></a>
     <a onclick=\"if(!confirm('Wirklich  l&ouml;schen?')) return false; else window.location.href='index.php?module=adresse&action=briefdelete&id=$id&sid=%value%';\">
     <img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\"></a>
     <a href=\"index.php?module=adresse&action=briefpdf&sid=%value%&id=$id\"><img src=\"./themes/[THEME]/images/pdf.png\" border=\"0\"></a>");
   */
  $this->app->Tpl->Parse(TAB1,"rahmen.tpl");

  $this->app->Tpl->Set(INHALT,"");
  $this->app->Tpl->Set(SUBHEADING,"Begleitschreiben zur RE, LS, AB, AN, BE, GS");
  $adresse = $this->app->User->GetAdresse();

  //Korrespondenzen
  $table = new EasyTable($this->app);
  $table->Query("SELECT DATE_FORMAT(d.zeit,'%d.%m.%Y %H:%i') as datum, d.betreff, 
      d.bearbeiter,p.abkuerzung as projekt,d.art, if(d.versendet,'<center>ja</center>','') as versendet,
      CONCAT('<a href=\"index.php?module=adresse&action=briefpdf&sid=',d.id,'&id=$id&type=',d.dokument,'&typeid=',d.parameter,'\"><img src=\"./themes/[THEME]/images/pdf.png\" border=\"0\"></a>','<a href=\"index.php?module=',d.dokument,'&action=abschicken&id=',d.parameter,'\"><img src=\"./themes/[THEME]/images/edit.png\" border=\"0\"></a>') as aktion
      FROM dokumente_send d  LEFT JOIN projekt p ON p.id=d.projekt
      WHERE d.adresse='$id' AND d.geloescht=0 ORDER by d.zeit DESC");
  $table->DisplayNew(INHALT, "<!--<a href=\"#\" onClick=\"javascript:KorrPopup('index.php?module=korrespondenz&action=edit&user=$id&id=%value%');makeRequest(this);return false\"><img src=\"./themes/[THEME]/images/edit.png\" border=\"0\"></a>-->
      <!--<a href=\"index.php?module=adresse&action=briefpdf&sid=%value%&id=$id\"><img src=\"./themes/[THEME]/images/pdf.png\" border=\"0\"></a>-->
      ","noAction");
  $this->app->Tpl->Parse(TAB1,"rahmen.tpl");

  $this->app->Tpl->Set(INHALT,"");

  $this->app->Tpl->Set(SUBHEADING,"Tickets");

  //Korrespondenzen
  $table = new EasyTable($this->app);
  $table->Query("SELECT DATE_FORMAT(tn.zeit,'%d.%m.%Y') as zeit, t.prio, CONCAT(LEFT(tn.betreff,30),'...') as betreff, tn.verfasser, 
      (SELECT COUNT(tn.id) FROM ticket_nachricht as tn WHERE tn.ticket=t.schluessel) as Antworten,
      tn.id FROM ticket as t, ticket_nachricht as tn WHERE t.schluessel=tn.ticket 
      AND t.adresse='$id'
      ORDER by t.prio, tn.zeit");

  $table->DisplayNew(INHALT,"<a href=\"index.php?module=ticket&action=assistent&id=%value%&lesen=1\" target=\"_blank\">Lesen</a>");

  $this->app->Tpl->Parse(TAB1,"rahmen.tpl");


  $this->app->Tpl->Set(INHALT,"");
  $this->app->Tpl->Set(SUBHEADING,"E-Mails");

  //Korrespondenzen
  $table = new EasyTable($this->app);
  $table->Query("SELECT DATE_FORMAT(empfang,'%d.%m.%Y') as zeit, CONCAT(LEFT(subject,30),'...') as betreff, sender as verfasser, id FROM emailbackup_mails WHERE adresse='$id'");
  $table->DisplayNew(INHALT,"<a href=\"index.php?module=webmail&action=view&id=%value%\">Lesen</a>");

  $this->app->Tpl->Parse(TAB1,"rahmen.tpl");



  // PARSE
  $this->app->Tpl->Set(AKTIV_TAB1,"selected");

  $this->app->Tpl->Parse(PAGE,"briefuebersicht.tpl");

}



function AdresseBriefEditPopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  $id= $this->app->Secure->GetGET("id");
  $sid= $this->app->Secure->GetGET("sid");

  if($frame=="false")
  {
    // hier nur fenster größe anpassen
    $this->app->YUI->IframeDialog(800,650,"index.php?module=adresse&action=briefeditpopup&id=$id&sid=$sid");
  } else {

    $adresse = $id;

    $typ = $this->app->DB->Select("SELECT dokument FROM dokumente_send WHERE id='$sid' LIMIT 1");
    //$parameter = $this->app->DB->Select("SELECT parameter FROM dokumente_send WHERE id='$sid' LIMIT 1");
    $parameter = $sid;

    //echo "typ = $typ ".$parameter;

    $this->app->erp->DokumentMask(PAGE,$typ,$parameter,$adresse,'',true);

    $this->app->BuildNavigation=false;
  }
}


function AdresseBriefDelete()
{
  $sid = $this->app->Secure->GetGET("sid");
  $id = $this->app->Secure->GetGET("id");

  $this->app->DB->Update("UPDATE dokumente_send SET geloescht=1 WHERE id='$sid' LIMIT 1");

  $this->AdresseBrief();
}

function AdresseBriefPDF()
{
  $sid = $this->app->Secure->GetGET("sid");
  $id = $this->app->Secure->GetGET("id");

  //$Brief = new Geschaeftsbrief(&$this->app,$sid);
  $Brief = new BriefPDF($this->app);
  $Brief->GetBrief($sid);
  $Brief->displayDocument();

  $this->AdresseBrief();
}

function AdresseService()
{
  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET("id");

  $this->app->YUI->TableSearch(TAB1,"adresse_service");
  $this->app->Tpl->Parse(PAGE,"adresse_service.tpl");
}

function AdresseBelege()
{
  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET("id");

  $kreditlimit = $this->app->DB->Select("SELECT kreditlimit FROM adresse WHERE id='$id' LIMIT 1");
  $saldo = $this->app->erp->SaldoAdresse($id);

  $kreditlimit_frei = $kreditlimit - $saldo;

  if($kreditlimit <=0) { 
    $kreditlimit="kein Limit";
    $kreditlimit_frei = "kein Limit";
  } else $kreditlimit = number_format($kreditlimit,2,',','.');

  $this->app->Tpl->Set(KREDITLIMIT,$kreditlimit);
  $this->app->Tpl->Set(KREDITLIMITFREI,$kreditlimit_frei);


  $this->app->Tpl->Set(UMSATZ,number_format($this->app->erp->UmsatzAdresseAuftragJahr($id),2,',','.'));

  if($saldo > 0)
    $saldo = "<font color=red>-".number_format($saldo,2,',','.')."</font>";
  else if ($saldo==0) $saldo="0,00";
  else $saldo = number_format($saldo,2,',','.');

  $this->app->Tpl->Set(SALDO,$saldo);


  $this->app->YUI->TableSearch(TAB1,"adresse_angebot");
  $this->app->YUI->TableSearch(TAB2,"adresse_auftrag");
  $this->app->YUI->TableSearch(TAB3,"adresse_rechnung");
  $this->app->YUI->TableSearch(TAB4,"adresse_gutschrift");
  $this->app->YUI->TableSearch(TAB5,"adresse_lieferschein");

  $this->app->Tpl->Parse(PAGE,"adresse_belege.tpl");

}



function AdresseEmail()
{
  $this->AdresseMenu();


  // NEU füllen
  $widget = new WidgetEmail($this->app,TAB2);
  $widget->Create();

  // UEBERSICHT füllen
  $this->app->Tpl->Set(HEADING,"Adresse");
  $this->app->Tpl->Set(SUBHEADING,"Email schreiben");
  $adresse = $this->app->User->GetAdresse();

  //Offene Aufgaben
  $table = new EasyTable($this->app);
  $table->Query("SELECT betreff, id FROM email");
  $table->DisplayNew(INHALT, "<a href=\"index.php?module=adresse&action=emaileditpopup&frame=false&id=%value%\" 
      onclick=\"makeRequest(this);return false\">Bearbeiten</a>");
  $this->app->Tpl->Parse(TAB1,"rahmen.tpl");

  // PARSE
  $this->app->Tpl->Set(AKTIV_TAB1,"selected");

  $this->app->Tpl->Parse(PAGE,"emailuebersicht.tpl");

}



function AdresseEmailEditPopup()
{
  $frame = $this->app->Secure->GetGET("frame");
  if($frame=="false")
  {
    // hier nur fenster größe anpassen
    $this->app->YUI->IframeDialog(510,610);
  } else {
    // nach page inhalt des dialogs ausgeben
    $widget = new WidgetEmail($this->app,PAGE);
    $widget->Edit();
    $this->app->BuildNavigation=false;
  }
}




function AdresseSuchmaske()
{
  $typ=$this->app->Secure->GetGET("typ");

  $this->app->Tpl->Set(HEADING,"Suchmaske f&uuml;r Adressen");
  $table = new EasyTable($this->app);
  switch($typ) {
    case "auftragrechnung":
      $table->Query("SELECT typ,name, ort, plz, strasse, abteilung, unterabteilung, ustid, email, adresszusatz, id as kundeadressid, id FROM adresse WHERE geloescht=0
          order by name");
      break;
    case "auftraglieferschein":
      $table->Query("SELECT typ as liefertyp, name as liefername, ort as lieferort, plz as lieferplz, strasse as lieferstrasse, abteilung as lieferabteilung, unterabteilung
          as lieferunterabteilung, adresszusatz as lieferadresszusatz, id as lieferadressid  FROM adresse WHERE geloescht=0 order by name");
      break;
    default:
      $table->Query("SELECT typ,name, ort, plz, strasse, abteilung, unterabteilung, ustid, email, adresszusatz, id as kundeadressid, id FROM adresse WHERE geloescht=0 order by name");
  }

  $table->DisplayWithDelivery(PAGE);

  $this->app->BuildNavigation=false;
}



function AdresseKundevorlage()
{
  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET("id");
  // prufe ob es schon einen eintrag gibt
  $check = $this->app->DB->Select("SELECT id FROM kundevorlage WHERE adresse='$id' LIMIT 1");
  if( !($check > 0 && is_numeric($check)))
  {
    $this->app->DB->Insert("INSERT INTO kundevorlage (id,adresse) VALUES ('','$id')");
  }

  $check = $this->app->DB->Select("SELECT id FROM kundevorlage WHERE adresse='$id' LIMIT 1");
  $this->app->Secure->GET['id']=$check;
  $this->app->Tpl->Set(AKTIV_TAB1,"selected");
  $widget = new WidgetKundevorlage($this->app,PAGE);
  $widget->Edit();
  $this->app->Secure->GET['id']=$id;
}

function AdresseAddPosition()
{
  $sid = $this->app->Secure->GetGET("sid");
  $id = $this->app->Secure->GetGET("id");
  $menge = $this->app->Secure->GetGET("menge");
  $datum  = $this->app->Secure->GetGET("datum");
  $datum  = $this->app->String->Convert($datum,"%1.%2.%3","%3-%2-%1");
  $tmpid = $this->app->erp->AddAdressePosition($id, $sid,$menge,$datum);

  $art  = $this->app->Secure->GetGET("art");

  if($datum=='0000-00-00' || $datum=='--' || $datum=="") $datum=date('Y-m-d');
  if($art=="abo") $this->app->DB->Update("UPDATE abrechnungsartikel SET wiederholend=1,startdatum='$datum',zahlzyklus=1 WHERE id='$tmpid' LIMIT 1");

  header("Location: index.php?module=adresse&action=artikel&id=$id");
  exit;
}

function AdresseLieferantvorlage()
{

  //zahlungsweise   zahlungszieltage  zahlungszieltageskonto  zahlungszielskonto  versandart
  //zahlungsweiselieferant  zahlungszieltagelieferant   zahlungszieltageskontolieferant   zahlungszielskontolieferant   versandartlieferant
  $arr = $this->app->DB->SelectArr("SELECT id,kundennummerlieferant FROM adresse WHERE lieferantennummer >0");

  foreach($arr as $key=>$value)
  {
    if($value['kundennummerlieferant']=="")
    {
      $id = $value['id'];
      $kundennummer = $this->app->DB->Select("SELECT kundennummer FROM lieferantvorlage WHERE adresse='$id' LIMIT 1");
      $zahlungsweiselieferant = $this->app->DB->Select("SELECT zahlungsweise FROM lieferantvorlage WHERE adresse='$id' LIMIT 1");
      $zahlungszieltagelieferant = $this->app->DB->Select("SELECT zahlungszieltage FROM lieferantvorlage WHERE adresse='$id' LIMIT 1");
      $zahlungszieltageskontolieferant = $this->app->DB->Select("SELECT zahlungszielskonto FROM lieferantvorlage WHERE adresse='$id' LIMIT 1");
      $versandartlieferant = $this->app->DB->Select("SELECT versandart FROM lieferantvorlage WHERE adresse='$id' LIMIT 1");

      if($kundennummer !="")	
      {
        echo "UPDATE adresse SET kundennummerlieferant='$kundennummer',zahlungsweiselieferant='$zahlungsweiselieferant',
             zahlungszieltagelieferant='$zahlungszieltagelieferant',zahlungszieltageskontolieferant='$zahlungszieltageskontolieferant',
             versandartlieferant='$versandartlieferant' WHERE id='$id';";
      } 
    }

  }


  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET("id");
  // prufe ob es schon einen eintrag gibt
  $check = $this->app->DB->Select("SELECT id FROM lieferantvorlage WHERE adresse='$id' LIMIT 1");
  if( !($check > 0 && is_numeric($check)))
  {
    $this->app->DB->Insert("INSERT INTO lieferantvorlage (id,adresse) VALUES ('','$id')");
  }

  $check = $this->app->DB->Select("SELECT id FROM lieferantvorlage WHERE adresse='$id' LIMIT 1");
  $this->app->Secure->GET['id']=$check;
  $this->app->Tpl->Set(AKTIV_TAB1,"selected");
  $widget = new WidgetLieferantvorlage($this->app,PAGE);
  $widget->Edit();
  $this->app->Secure->GET['id']=$id;
}




function AdresseArtikelPosition()
{
  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET("id");
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
  $art = $this->app->Secure->GetPOST("art");
  $lieferdatum = $this->app->Secure->GetPOST("lieferdatum");
  $zahlzyklus = $this->app->Secure->GetPOST("zahlzyklus");
  $wiederholend= $this->app->Secure->GetPOST("wiederholend");
  $startdatum= $this->app->Secure->GetPOST("startdatum");

  if($lieferdatum=="") $lieferdatum=date("d.m.Y");


  $anlegen_artikelneu = $this->app->Secure->GetPOST("anlegen_artikelneu");

  if($anlegen_artikelneu!="")
  { 

    if($bezeichnung!="" && $menge!="" && $preis!="")
    { 

      $neue_nummer = $this->app->erp->NeueArtikelNummer($artikelart,$this->app->User->GetFirma(),$projekt);

      // anlegen als artikel
      $this->app->DB->Insert("INSERT INTO artikel (id,typ,nummer,projekt,name_de,umsatzsteuer,adresse,firma)  
          VALUES ('','$artikelart','$neue_nummer','$projekt','$bezeichnung','$umsatzsteuerklasse','$lieferant','".$this->app->User->GetFirma()."')");

      $artikel_id = $this->app->DB->GetInsertID();
      // einkaufspreis anlegen

      $this->app->DB->Insert("INSERT INTO verkaufspreise (id,artikel,adresse,objekt,projekt,preis,ab_menge,angelegt_am,bearbeiter)
          VALUES ('','$artikel_id','$id','Standard','$projekt','$preis','$menge',NOW(),'".$this->app->User->GetName()."')");

      $lieferdatum = $this->app->String->Convert($lieferdatum,"%1.%2.%3","%3-%2-%1");
      $startdatum= $this->app->String->Convert($startdatum,"%1.%2.%3","%3-%2-%1");

      if($art=="abo") $wiederholend=1; else $wiederholend=0;

      $this->app->DB->Insert("INSERT INTO abrechnungsartikel (id,artikel,bezeichnung,nummer,menge,preis, sort,lieferdatum, steuerklasse, status,projekt,wiederholend,zahlzyklus,adresse,startdatum) 
          VALUES ('','$artikel_id','$bezeichnung','$neue_nummer','$menge','$preis','$sort','$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$wiederholend','$zahlzyklus','$id','$startdatum')");

      header("Location: index.php?module=adresse&action=artikel&id=$id");
      exit;
    } else
      $this->app->Tpl->Set(NEUMESSAGE,"<div class=\"error\">Bestellnummer, bezeichnung, Menge und Preis sind Pflichfelder!</div>");

  }

  $ajaxbuchen = $this->app->Secure->GetPOST("ajaxbuchen");
  if($ajaxbuchen!="")
  {
    $artikel = $this->app->Secure->GetPOST("artikel");
    $nummer = $this->app->Secure->GetPOST("nummer");
    $projekt = $this->app->Secure->GetPOST("projekt");
    $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
    $sort = $this->app->DB->Select("SELECT MAX(sort) FROM angebot_position WHERE auftrag='$id' LIMIT 1");
    $sort = $sort + 1;
    $artikel_id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1");
    $bezeichnung = $artikel;
    $neue_nummer = $nummer;
    $waehrung = 'EUR';
    $umsatzsteuerklasse = $this->app->DB->Select("SELECT umsatzsteuerklasse FROM artikel WHERE nummer='$nummer' LIMIT 1");
    $vpe = 'einzeln';

    //        $this->app->DB->Insert("INSERT INTO angebot_position (id,angebot,artikel,bezeichnung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe) 
    //          VALUES ('','$id','$artikel_id','$bezeichnung','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$vpe')");
  }


  if(1)
  {
    $this->app->Tpl->Set(ARTIKELART,$this->app->erp->GetSelect($this->app->erp->GetArtikelart(),$artikelart));
    $this->app->Tpl->Set(VPE,$this->app->erp->GetSelect($this->app->erp->GetVPE(),$vpe));
    $this->app->Tpl->Set(WAEHRUNG,$this->app->erp->GetSelect($this->app->erp->GetWaehrung(),$vpe));
    $this->app->Tpl->Set(UMSATZSTEUERKLASSE,$this->app->erp->GetSelect($this->app->erp->GetUmsatzsteuerklasse(),$umsatzsteuerklasse));
    $this->app->Tpl->Set(PROJEKT,$this->app->erp->GetProjektSelect($projekt));
    $this->app->Tpl->Set(PREIS,$preis);
    $this->app->Tpl->Set(MENGE,$menge);
    $this->app->Tpl->Set(LIEFERDATUM,$lieferdatum);
    $this->app->Tpl->Set(ZAHLZYKLUS,$zahlzyklus);
    $this->app->Tpl->Set(BEZEICHNUNG,$bezeichung);

    $this->app->Tpl->Set(SUBSUBHEADING,"Neuen Artikel anlegen");
    //      $this->app->Tpl->Parse(INHALT,"aboabrechnungsartikel_artikelneu.tpl");
    //     $this->app->Tpl->Set(EXTEND,"<input type=\"submit\" value=\"Artikel anlegen\" name=\"anlegen_artikelneu\">");
    $this->app->Tpl->Parse(UEBERSICHT,"rahmen70.tpl");
    $this->app->Tpl->Set(EXTEND,"");
    $this->app->Tpl->Set(INHALT,"");

    /* ende neu anlegen formular */
    /* ende neu anlegen formular */
    $this->app->YUI->TableSearch(TAB2,'abrechnungsartikel');
    // child table einfuegen

    $menu = array(//"up"=>"upartikel",
        //                          "down"=>"downartikel",
        //"add"=>"addstueckliste",
        "edit"=>"positioneneditpopup",
        "del"=>"delartikel");

    // wiederholende artikel
    $sql = "SELECT aa.bezeichnung,aa.nummer, DATE_FORMAT(aa.abgerechnetbis,'%d.%m.%Y') as abgerechnet, 
      aa.preis as preis, aa.menge as menge, aa.id as id
      FROM abrechnungsartikel aa
      WHERE aa.adresse='$id' AND aa.wiederholend=1";
    $this->app->YUI->SortList(TAB1,$this,$menu,$sql,false);


    // einmalige artikel
    $sql = "SELECT aa.bezeichnung, aa.nummer, DATE_FORMAT(aa.abgerechnetbis,'%d.%m.%Y') as abgerechnet, 
      aa.preis as preis, aa.menge as menge, aa.id as id
      FROM abrechnungsartikel aa
      WHERE aa.adresse='$id' AND aa.wiederholend=0 AND aa.abgerechnet=0";
    $this->app->YUI->SortList(TAB11,$this,$menu,$sql,false);


    $this->app->Tpl->Parse(PAGE,"adresse_abo.tpl");
  }
}


function AdresseArtikel()
{
  $this->AdresseMenu();
  $id = $this->app->Secure->GetGET("id");

  // neues arbeitspaket
  //$widget = new WidgetAbrechnungsartikel(&$this->app,TAB2);
  //$widget->Create();


  // child table einfuegen

  $menu = array("up"=>"upartikel",
      "down"=>"downartikel",
      //"add"=>"addstueckliste",
      "edit"=>"artikeleditpopup",
      "del"=>"delartikel");

  // wiederholende artikel
  $this->app->Tpl->Set(SUBSUBHEADING,"wiederholende Artikel");
  $sql = "SELECT aa.bezeichnung, DATE_FORMAT(aa.abgerechnetbis,'%d.%m.%Y') as abgerechnet, 
    aa.preis as preis, aa.menge as menge, aa.id as id
    FROM abrechnungsartikel aa
    WHERE aa.adresse='$id' AND aa.wiederholend=1";
  $this->app->YUI->SortList(INHALT,$this,$menu,$sql,false);
  $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");
  $this->app->Tpl->Set(INHALT,"");


  // einmalige artikel
  $this->app->Tpl->Set(SUBSUBHEADING,"einmalige Artikel");
  $sql = "SELECT aa.bezeichnung, DATE_FORMAT(aa.abgerechnetbis,'%d.%m.%Y') as abgerechnet, 
    aa.preis as preis, aa.menge as menge, aa.id as id
    FROM abrechnungsartikel aa
    WHERE aa.adresse='$id' AND aa.wiederholend=0 AND aa.abgerechnet=0";
  $this->app->YUI->SortList(INHALT,$this,$menu,$sql,false);
  $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");

  $this->app->Tpl->Set(AKTIV_TAB1,"selected");
  $this->app->Tpl->Parse(PAGE,"artikeluebersicht.tpl");
}

function AdresseArtikelEditPopup()
{
  $id = $this->app->Secure->GetGET("id");

  // nach page inhalt des dialogs ausgeben
  $widget = new WidgetAbrechnungsartikel($this->app,PAGE);
  $sid = $this->app->DB->Select("SELECT adresse FROM abrechnungsartikel WHERE id='$id' LIMIT 1");
  $widget->form->SpecialActionAfterExecute("close_refresh",
      "index.php?module=adresse&action=artikel&id=$sid");
  $widget->Edit();
  $this->app->BuildNavigation=false;
}

function UpArtikel()
{
  $this->app->YUI->SortListEvent("up","abrechnungsartikel","adresse");
  $this->AdresseArtikel();
}

function DownArtikel()
{
  $this->app->YUI->SortListEvent("down","abrechnungsartikel","adresse");
  $this->AdresseArtikel();
}


function DelArtikel()
{
  $this->app->YUI->SortListEvent("del","abrechnungsartikel","adresse");
  $this->AdresseArtikelPosition();
}



}

?>
