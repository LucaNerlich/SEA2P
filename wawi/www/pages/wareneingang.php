<?php

class Wareneingang 
{

  function Wareneingang($app)
  {
    $this->app=&$app; 

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("main","WareneingangMain");
    $this->app->ActionHandler("list","WareneingangList");
    $this->app->ActionHandler("help","WareneingangHelp");
    $this->app->ActionHandler("vorgang","VorgangAnlegen");
    $this->app->ActionHandler("removevorgang","VorgangEntfernen");
    $this->app->ActionHandler("create","WareneingangCreate");
    $this->app->ActionHandler("paketannahme","WareneingangPaketannahme");
    $this->app->ActionHandler("paketabsender","WareneingangPaketAbsender");
    $this->app->ActionHandler("paketzustand","WareneingangPaketZustand");
    $this->app->ActionHandler("paketetikett","WareneingangPaketEtikett");
    $this->app->ActionHandler("paketabschliessen","WareneingangPaketAbschliessen");
    $this->app->ActionHandler("distribution","WareneingangPaketDistribution");
    $this->app->ActionHandler("distriinhalt","WareneingangPaketDistriInhalt");
    $this->app->ActionHandler("distrietiketten","WareneingangPaketDistriEtiketten");
    $this->app->ActionHandler("distriabschluss","WareneingangPaketDistriAbschluss");
    $this->app->ActionHandler("manuellerfassen","WareneingangManuellErfassen");
    $this->app->ActionHandler("minidetail","WareneingangMiniDetail");

    $this->app->DefaultActionHandler("login");

    $this->app->Tpl->Set(UEBERSCHRIFT," Wareneingang");

    $this->app->ActionHandlerListen($app);
  }



  function WareneingangPaketMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(ID,$id);
    $this->app->Tpl->Add(KURZUEBERSCHRIFT," Paketannahme");
    $this->app->erp->MenuEintrag("index.php?module=wareneingang&action=distribution","zur Distribution");
    $this->app->erp->MenuEintrag("index.php?module=wareneingang&action=paketannahme","Paketannahme");
  }

  function WareneingangPaketDistriMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(ID,$id);
    $this->app->Tpl->Add(KURZUEBERSCHRIFT," Paketdistribution");
    $this->app->erp->MenuEintrag("index.php?module=wareneingang&action=paketannahme","zur Paketannahme");
    $this->app->erp->MenuEintrag("index.php?module=wareneingang&action=distriinhalt","Paketannahme");
  }


  function WareneingangMenu()
  {
    $this->app->Tpl->Add(KURZUEBERSCHRIFT," Wareneingang");
    $this->app->erp->MenuEintrag("index.php?module=wareneingang&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=wareneingang&action=create","Paketannahme");
    //$this->app->erp->MenuEintrag("index.php?module=wareneingang&action=create\">Inhalt erfassen</a></li>");
    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=wareneingang&action=create\">weitere Artikel erfassen</a></li>");
    //$this->app->Tpl->Add(TABS,"<li><a href=\"index.php?module=wareneingang&action=search\">Lieferung suchen</a></li>");
  }


  function WareneingangPaketDistriInhalt()
  {
    $id = $this->app->Secure->GetGET("id");
    $submit = $this->app->Secure->GetPOST("submit");
    $submitkunde = $this->app->Secure->GetPOST("submitkunde");

    $this->WareneingangPaketDistriMenu(); 

    if($submit!="")
    {
      $tmp = $this->app->Secure->GetPOST("pos"); 

      $pos = key($tmp);
      $menge = $tmp[$pos];

      if($menge<=0)
      {
        $this->app->Tpl->Set(TAB1,"<div class=\"error\">Bitte geben Sie eine Menge an!</div>");
      } else {
        header("Location: index.php?module=wareneingang&action=distrietiketten&id=$id&pos=$pos&menge=$menge");
        exit;
      }
    }

    if($submitkunde!="")
    {
      $tmp = $this->app->Secure->GetPOST("pos"); 
      $artikelnummer = $this->app->Secure->GetPOST("artikelnummer");

      $pos = key($tmp);
      $menge = $tmp[$pos];

      if($menge<=0 && $pos >0)
      {
        $this->app->Tpl->Set(TAB1,"<div class=\"error\">Bitte geben Sie eine Menge an!</div>");
      } else {
        // weil artikel aus kundenliste gewaehlt wurde ist vorgang ein RMA
        header("Location: index.php?module=wareneingang&action=distrietiketten&id=$id&pos=$pos&menge=$menge&rma=rma&artikelnummer=$artikelnummer");
        exit;
      }
    }


    $adresse= $this->app->DB->Select("SELECT adresse FROM paketannahme WHERE id='$id' LIMIT 1");

    // pruefe ob 
    $lieferant = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
    $kunde= $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");

    $name= $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");

    if(is_numeric($lieferant) && $lieferant!=0)
    {
      $this->app->Tpl->Set(TAB1TEXT,'<li><a href="#tabs-1">Bestellungen</a></li>');
      $this->app->Tpl->Set(TAB1START,"<div id=\"tabs-1\">");
      $this->app->Tpl->Set(TAB1ENDE,"</div>");

      $this->app->Tpl->Add(TAB1,"<br><h1>Offene Artikel von Bestellungen an $name:</h1><br>");
      $this->app->YUI->TableSearch(TAB1,"wareneingang_lieferant");

      /*
         $table = new EasyTable($this->app);
         $table->Query("SELECT bp.bestellnummer, art.nummer, b.belegnr as `Bestellung`, CONCAT(LEFT(art.name_de,40),'<br>Bei Lieferant: ',LEFT(bp.bezeichnunglieferant,40)) as beschreibung, if(bp.lieferdatum,DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum, p.abkuerzung as projekt, 
         bp.menge, bp.geliefert, bp.menge -  bp.geliefert as offen, bp.id FROM bestellung_position bp
         LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN artikel art ON art.id=bp.artikel LEFT JOIN projekt p ON bp.projekt=p.id WHERE b.adresse='$adresse' AND b.belegnr > 0 
         AND bp.geliefert < bp.menge AND (bp.abgeschlossen IS NULL OR bp.abgeschlossen=0)  AND (b.status='versendet' OR b.status='freigegeben')");

         $table->DisplayNew(TAB1,"<form style=\"padding: 0px; margin: 0px;\" action=\"\" method=\"post\" name=\"eprooform\">Menge:&nbsp;<input type=\"text\" size=\"1\" name=\"pos[%value%]\">&nbsp;<input type=\"submit\" value=\"zuordnen\" name=\"submit\"></form>");
      //$this->app->Tpl->Add(TAB1,"<div class=\"info\">Es kann immer nur ein Artikel zugwiesen werden!</h1><br>");
       */
    } 



    if(is_numeric($kunde) && $kunde!=0)
    {
      // Kunde
      $this->app->Tpl->Set(TAB2TEXT,'<li><a href="#tabs-2">Retouren Artikel</a></li>');
      $this->app->Tpl->Set(TAB2START,"<div id=\"tabs-2\">");
      $this->app->Tpl->Set(TAB2ENDE,"</div>");
      $this->app->Tpl->Add(TAB2,"<br><h1>Ausgelieferte Artikel an Kunden $name:</h1><br>");
      $this->app->YUI->TableSearch(TAB2,"wareneingang_kunde");

      /*
         $table = new EasyTable($this->app);
         $table->Query("SELECT lp.nummer, CONCAT(LEFT(lp.bezeichnung,20),'...') as beschreibung, p.abkuerzung as projekt, 
         lp.menge, lp.geliefert, l.belegnr as lieferschein, DATE_FORMAT(l.datum,'%d.%m.%Y') as datum, lp.id FROM lieferschein_position lp
         LEFT JOIN lieferschein l ON lp.lieferschein=l.id LEFT JOIN projekt p ON lp.projekt=p.id WHERE l.adresse='$adresse' AND (l.status='versendet' OR l.status='freigegeben')");
         $table->DisplayNew(TAB2,"<form style=\"padding: 0px; margin: 0px;\" action=\"\" method=\"post\" name=\"eprooform\">Menge:&nbsp;<input type=\"text\" size=\"1\" name=\"pos[%value%]\">&nbsp;<input type=\"submit\" value=\"zuordnen\" name=\"submitkunde\"></form>");
       */
      /*
      //$this->app->Tpl->Add(TAB1,"<br><h1>Artikel Scannen:</h1><br>");
      $this->app->Tpl->Add(BARCODEFELD,
      "<form style=\"padding: 0px; margin: 0px;\" action=\"\" method=\"post\" name=\"eprooform\">
Barcode:&nbsp;<input type=\"text\" id=\"erstes\" name=\"artikelnummer\">&nbsp;<input type=\"submit\" value=\"Ok\" name=\"submitkunde\">
</form><script type=\"text/javascript\">document.getElementById(\"erstes\").focus(); </script>");
       */


    }

    if(!(is_numeric($kunde) && $kunde!=0) && !(is_numeric($lieferant) && $lieferant!=0))
    {
      $this->app->Tpl->Set(TAB1,"<div class=\"error\">Die ausgew&auml;hlte Adresse hat noch keine Rolle Kunde oder Lieferant. Bitte vergeben Sie diese, dann sehen Sie Bestellungen oder versendete Waren.</div>");
    }



    $this->app->Tpl->Set(AKTIV_TAB2,"tabs-1");



    $this->app->Tpl->Parse(PAGE,"wareneingangpaketdistribution.tpl");

    $abschliessen = $this->app->Secure->GetPOST("abschliessen"); 
    if($abschliessen!="")
    {
      // paketannahme auf abgeschlossen setzten
      $this->app->DB->Update("UPDATE paketannahme SET status='abgeschlossen' WHERE id='$id' LIMIT 1");
      //      $typ = $this->app->DB->Update("SELECT typ FROM paketannahme WHERE id='$id' LIMIT 1");
      if($typ=="rma")
      {
        //RMA bericht drucken mit allen artikeln des Kunden


      }
      if($this->app->erp->Firmendaten("wareneingang_kamera_waage")=="1")
        header("Location: index.php?module=wareneingang&action=distribution");
      else
        header("Location: index.php?module=wareneingang&action=paketannahme");
      exit;
    }

    $manuellerfassen = $this->app->Secure->GetPOST("manuellerfassen"); 
    if($manuellerfassen!="")
    {
      header("Location: index.php?module=wareneingang&action=manuellerfassen&id=$id");
      exit;
    }

    $this->app->Tpl->Add(PAGE,"<form action=\"\" method=\"post\"><br><br><center>
        <input type=\"submit\" name=\"manuellerfassen\" value=\"Artikel manuell erfassen\">&nbsp;
        <input type=\"submit\" name=\"abschliessen\" value=\"Paketinhalt ist jetzt komplett erfasst!\"></center></form><br><br>");

  }

  function WareneingangMiniDetail()
  {
    $id = $this->app->Secure->GetGET("id");
    header("Location: index.php?module=artikel&action=minidetail&id=$id");
    exit;
  }

  function WareneingangManuellErfassen()
  {
    $id = $this->app->Secure->GetGET("id");
    $paket = $this->app->Secure->GetGET("paket");
    $this->app->erp->MenuEintrag("index.php?module=wareneingang&action=distriinhalt&id=$id","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=wareneingang&action=manuellerfassen&id=$id","Artikel");

    $cmd = $this->app->Secure->GetGET("cmd");

    if($cmd=="add")
    {
      echo "huhuh";
    } else {
      $this->app->YUI->TableSearch(TAB1,"wareneingangartikelmanuellerfassen");
    }

    //$this->WareneingangPaketMenu();
    $this->app->Tpl->Parse(PAGE,"wareneingangpaketdistribution.tpl");
  }

  function WareneingangPaketDistriEtiketten()
  {
    $id = $this->app->Secure->GetGET("id");  
    $pos = $this->app->Secure->GetGET("pos");  
    $artikelnummer = $this->app->Secure->GetGET("artikelnummer");  
    $menge = $this->app->Secure->GetGET("menge");  
    $rma = $this->app->Secure->GetGET("rma");  

    $submit = $this->app->Secure->GetPOST("submit");  
    $lager = $this->app->Secure->GetPOST("lager");  
    $etiketten = $this->app->Secure->GetPOST("etiketten");  
    $anzahlauswahl = $this->app->Secure->GetPOST("anzahlauswahl");  
    $anzahl_fix = $this->app->Secure->GetPOST("anzahl_fix");  
    $anzahl_dyn = $this->app->Secure->GetPOST("anzahl_dyn");  
    $anzahl = $this->app->Secure->GetPOST("anzahl");  
    $bemerkung = $this->app->Secure->GetPOST("bemerkung");  
    $wunsch= $this->app->Secure->GetPOST("wunsch");  
    $cmd= $this->app->Secure->GetGET("cmd");  

    $this->app->Tpl->Set(ID,$id);

    if($cmd=="manuell"){
      $this->app->DB->Update("UPDATE artikel SET lagerartikel='1' WHERE id='$pos' LIMIT 1");
      $artikel = $pos;

      $this->app->Tpl->Set(ANZAHLAENDERN,"<input type=\"button\" value=\"&auml;ndern\" onclick=\"var menge =  prompt('Neue Menge:',$menge); if(menge > 0) window.location.href=document.URL + '&menge=' + menge;\">");
      $this->app->Tpl->Set(SHOWANZAHLSTART,"<!--");
      $this->app->Tpl->Set(SHOWANZAHLENDE,"-->");
    }

    if($rma=="rma")
    {
      // RMA Artikel
      if($pos>0)
      {
        $artikel = $this->app->DB->Select("SELECT artikel FROM lieferschein_position WHERE id='$pos' LIMIT 1");
        $projekt = $this->app->DB->Select("SELECT projekt FROM lieferschein_position WHERE id='$pos' LIMIT 1");
        $bestellung = $this->app->DB->Select("SELECT lieferschein FROM lieferschein_position WHERE id='$pos' LIMIT 1");
        $vpe= $this->app->DB->Select("SELECT vpe FROM lieferschein_position WHERE id='$pos' LIMIT 1");
        $menge_bestellung = $this->app->DB->Select("SELECT menge FROM lieferschein_position WHERE id='$pos' LIMIT 1");
        $adresse = $this->app->DB->Select("SELECT adresse FROM paketannahme WHERE id='$id' LIMIT 1");
        $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
        $mitarbeiter = $this->app->DB->Select("SELECT bearbeiter FROM lieferschein WHERE id='$bestellung' LIMIT 1");
        $bestellung_belegnr = $this->app->DB->Select("SELECT belegnr FROM lieferschein WHERE id='$bestellung' LIMIT 1");
      } else {
        $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$artikelnummer' LIMIT 1");
        $adresse = $this->app->DB->Select("SELECT adresse FROM paketannahme WHERE id='$id' LIMIT 1");
        $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      }
    } 
    else if($cmd=="manuell")
    {
      $artikel = $pos;
      $mitarbeiter = $this->app->User->GetName();
      $projekt = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$artikel' LIMIT 1");
    }
    else 
    {
      //bestellung
      // bestellung findet man raus ueber pos (bestellung) 
      $artikel = $this->app->DB->Select("SELECT artikel FROM bestellung_position WHERE id='$pos' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT projekt FROM bestellung_position WHERE id='$pos' LIMIT 1");
      $bestellung = $this->app->DB->Select("SELECT bestellung FROM bestellung_position WHERE id='$pos' LIMIT 1");
      $vpe= $this->app->DB->Select("SELECT vpe FROM bestellung_position WHERE id='$pos' LIMIT 1");
      $menge_bestellung = $this->app->DB->Select("SELECT menge FROM bestellung_position WHERE id='$pos' LIMIT 1");
      $adresse = $this->app->DB->Select("SELECT adresse FROM paketannahme WHERE id='$id' LIMIT 1");
      $name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' AND geloescht=0 LIMIT 1");
      $mitarbeiter = $this->app->DB->Select("SELECT bearbeiter FROM bestellung WHERE id='$bestellung' LIMIT 1");
      $bestellung_belegnr = $this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id='$bestellung' LIMIT 1");
    }
    $lagerartikel = $this->app->DB->Select("SELECT lagerartikel FROM artikel WHERE id='$artikel' LIMIT 1");
    $mindesthaltbarkeitsdatum = $this->app->DB->Select("SELECT mindesthaltbarkeitsdatum FROM artikel WHERE id='$artikel' LIMIT 1");
    $seriennummern = $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id='$artikel' LIMIT 1");
    $mitarbeiter_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$mitarbeiter' AND geloescht=0 LIMIT 1");
    $artikelcheckliste = $this->app->DB->Select("SELECT artikelcheckliste FROM artikel WHERE id='$artikel' LIMIT 1");
    $funktionstest = $this->app->DB->Select("SELECT funktionstest FROM artikel WHERE id='$artikel' LIMIT 1");
    $endmontage = $this->app->DB->Select("SELECT endmontage FROM artikel WHERE id='$artikel' LIMIT 1");
    $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");
    $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1");
    $chargenverwaltung= $this->app->DB->Select("SELECT chargenverwaltung FROM artikel WHERE id='$artikel' LIMIT 1");
    $standardbild = $this->app->DB->Select("SELECT standardbild FROM artikel WHERE id='$artikel' LIMIT 1");
    $shopartikel = $this->app->DB->Select("SELECT shop FROM artikel WHERE id='$artikel' LIMIT 1");

    if($standardbild=="")
      $standardbild = $this->app->DB->Select("SELECT datei FROM datei_stichwoerter WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='$artikel' LIMIT 1");


    if(($menge > $menge_bestellung)&&$cmd!="manuell")
      $this->app->Tpl->Add(MESSAGE,"<div class=\"error\">Achtung! Es wurden mehr geliefert als in der aktuellen Position bestellt worden sind!
          &nbsp;<input type=\"button\" onclick=\"window.location.href='index.php?module=wareneingang&action=distriinhalt&id=$id'\"
          value=\"Anzahl anpassen\" /></div>");

    if(!$shopartikel > 0)
    {
      $this->app->Tpl->Set(SHOWIMGSTART,"<!--");
      $this->app->Tpl->Set(SHOWIMGEND,"-->");
    }

    if($chargenverwaltung !="2")
    {
      $this->app->Tpl->Set(SHOWCHRSTART,"<!--");
      $this->app->Tpl->Set(SHOWCHREND,"-->");
    } else {
      //				$this->app->YUI->DatePicker("mhd");
    }


    if($mindesthaltbarkeitsdatum !="1")
    {
      $this->app->Tpl->Set(SHOWMHDSTART,"<!--");
      $this->app->Tpl->Set(SHOWMHDEND,"-->");
    } else {
      $this->app->YUI->DatePicker("mhd");
    }

    if($seriennummern =="keine" || $seriennummern =="vomprodukt" || $seriennummern =="eigene" || $seriennummern=="")
    {
      $this->app->Tpl->Set(SHOWSRNSTART,"<!--");
      $this->app->Tpl->Set(SHOWSRNEND,"-->");
    } else {
      // Generator felder fuer seriennummern

      $this->app->Tpl->Add(SERIENNUMMERN,"<table><tr><td>Nr.</td><td>Seriennummer</td></tr>");
      for($ij=1;$ij<=$menge;$ij++)
      {
        $this->app->Tpl->Add(SERIENNUMMERN,"<tr><td>$ij</td><td><input type=\"text\" name=\"seriennummern[]\" size=\"30\"></td></tr>");
      }
      $this->app->Tpl->Add(SERIENNUMMERN,"</table>");
    }

    $standardlager = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='".$artikel."' LIMIT 1");
    if($standardlager <=0) 
    {
      $this->app->Tpl->Set(STANDARDLAGER,"nicht definiert");
      $this->app->Tpl->Set(LAGER,$this->app->erp->GetSelectAsso($this->app->erp->GetLager(),$lager));
    }
    else
    {
      $this->app->Tpl->Set(STANDARDLAGER,$this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='".$standardlager."' LIMIT 1"));
      $this->app->Tpl->Set(LAGER,$this->app->erp->GetSelectAsso($this->app->erp->GetLager(true),$lager));
    }

    $this->app->Tpl->Set(ETIKETTEN,$this->app->erp->GetSelect($this->app->erp->GetEtikett(),$etiketten));

    $this->app->Tpl->Set(MENGE,$menge);

    if($this->app->erp->Firmendaten("standardetikettendrucker")>0)
    {
      $this->app->Tpl->Set(ETIKETTENDRUCKEN,"Etiketten drucken.");
      $this->app->Tpl->Set(ANZAHL,$menge);
      $this->app->Tpl->Set(TEXTBUTTON,"Etiketten drucken und Artikel einlagern");
    }
    else {
      $this->app->Tpl->Set(SHOWANZAHLSTART,"<!--");
      $this->app->Tpl->Set(SHOWANZAHLENDE,"-->");

      $this->app->Tpl->Set(ETIKETTENDRUCKENSTART,"<!--");
      $this->app->Tpl->Set(ETIKETTENDRUCKENENDE,"-->");
      $this->app->Tpl->Set(TEXTBUTTON,"Artikel einlagern");
      $this->app->Tpl->Set(ANZAHL,0);
      $this->app->Tpl->Set(ANZAHLCHECKED,"checked");
    }

    $this->app->Tpl->Set(LIEFERANT,$name);
    $this->app->Tpl->Set(MITARBEITER,$mitarbeiter_name);
    $this->app->Tpl->Set(VPE,$vpe);
    $this->app->Tpl->Set(NAME,$name_de);
    $this->app->Tpl->Set(NUMMER,$nummer);
    $this->app->Tpl->Set(DATEI,$standardbild);

    $error = 0;
    // Pflichfelder pruefen
    if($mindesthaltbarkeitsdatum=="1" && $this->app->Secure->GetPOST("mhd")=="")
    {
      $error++;
    }

    if($chargenverwaltung=="2" && $this->app->Secure->GetPOST("charge")=="")
    {
      $error++;
    }

    if($seriennummern !="keine" && $seriennummern !="vomprodukt" && $seriennummern !="eigene" && $seriennummern !="")
    {
      $tmpcheck = $this->app->Secure->GetPOST("seriennummern");
      for($checkser=0;$checkser < $menge; $checkser++)
      {
        if($tmpcheck[$checkser]=="")
          $error++;
      }
    }

    if($submit!="" && $error > 0)
    {
      $this->app->Tpl->Add(MESSAGE,"<div class=\"error\">Achtung! Bitte alle Pflichfelder ausf&uuml;llen!</div>");
    }
    // ende pflichtfelder pruefung

    //    $this->app->erp->MenuEintrag("index.php?module=wareneingang&action=distriinhalt&id=$id","zum Paketinhalt");
    $this->app->erp->MenuEintrag("index.php?module=wareneingang&action=manuellerfassen&id=$id","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=wareneingang&action=distrietiketten&id=$id","Artikel",true);

    $typ = "";
    //weiter mit paket bis fertig
    if($rma=="rma")
    {
      $typ = "rma";
      //$this->app->Tpl->Add(TAB1TEXT,"<li><a>Retouren Artikel</a></li>");
      $this->app->Tpl->Parse(TAB1,"wareneingangpaketdistribution_tab3_rma.tpl");
    } else {
      if($lagerartikel && !$artikelcheckliste && !$funktionstest && !$endmontage)
      {
        $typ = "lager";
        //$this->app->Tpl->Add(TAB1TEXT,"<li><a>Lagerartikel</a></li>");
        $this->app->Tpl->Parse(TAB1,"wareneingangpaketdistribution_tab3_lager.tpl");
      } else if($artikelcheckliste || $funktionstest || $endmontage)
      {
        $typ = "produktion";
        //$this->app->Tpl->Add(TAB1TEXT,"<li><a>Artikel f&uuml;r Produktion</a></li>");
        $this->app->Tpl->Parse(TAB1,"wareneingangpaketdistribution_tab3_produktion.tpl");
      } else if (!$lagerartikel && !$artikelcheckliste && !$funktionstest && !$endmontage)
      {
        $typ = "mitarbeiter";
        $this->app->Tpl->Add(TAB1TEXT,"<li><a>Artikel f&uuml;r Mitarbeiter</a></li>");
        $this->app->Tpl->Parse(TAB1,"wareneingangpaketdistribution_tab3_mitarbeiter.tpl");
      } else {echo "Diesen Fall gibt es nicht. WaWision Entwicklung kontaktieren!";}
    }


    //befehl ab ins lager, produktion oder mitarbeiter
    if($submit!="" && $error==0)
    {
      switch($typ)
      {
        case "lager":


          if($anzahlauswahl=="fix") $druckanzahl = $anzahl_fix;
          else $druckanzahl = $anzahl_dyn;
          $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");                     
          $name_de = base64_encode($name_de);  

          //$etiketten AUSWAHL etiketten ob gross oder klein
          if($this->app->erp->Firmendaten("standardetikettendrucker")>0) {
            if($etiketten=="gross")
              HttpClient::quickGet("http://".$this->app->erp->GetIPAdapterbox($this->app->erp->Firmendaten("standardetikettendrucker"))."/druck.php?nr=$nummer&ch=$ch&anzahl=$druckanzahl&etikett=$etiketten&beschriftung=$name_de");
            else
            {      $etiketten ="klein";   
              HttpClient::quickGet("http://".$this->app->erp->GetIPAdapterbox($this->app->erp->Firmendaten("standardetikettendrucker"))."/druck.php?nr=$nummer&ch=$ch&anzahl=$druckanzahl&etikett=$etiketten&beschriftung=$name_de");
            }
          }
          $zid = "";
          // entweder ins zwischenlager 
          if($lager=="zwischenlager")
          {
            $this->app->DB->Insert("INSERT INTO zwischenlager (id,bearbeiter,projekt,artikel,menge,vpe,grund,lager_von,richtung,objekt,parameter,firma)
                VALUES ('','".$this->app->User->GetName()."','$projekt','$artikel','$menge','$vpe','Wareneingang von Bestellung $bestellung_belegnr','Wareneingang','Eingang',
                  'Bestellung','$bestellung','".$this->app->User->GetFirma()."')");
            $typ = "zwischenlager";
            $zid = $this->app->DB->GetInsertID();
          }
          // oder direkt ins manuelle (lagerplatz + lager_bewegung)
          else 
          {
            if($lager=="standardlager")
              $lager = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='".$artikel."' LIMIT 1");

            if($lager<=0)
              $lager = $this->app->DB->Select("SELECT id FROM lager_platz WHERE autolagersperre!=1 AND verbrauchslager!=1 AND geloescht!=1 LIMIT 1");

            if($lager=="zwischenlager")
              $lagerplatz = 0;
            else 
              $lagerplatz = $lager;

            //$charge = $this->app->Secure->GetPOST("charge");
            if($chargenverwaltung=="1")
            {
              // wenn chargenverwaltung dann chargen id holen!!!! und mit bei lagerung und etikett speichern!
              $this->app->DB->Insert("INSERT INTO chargenverwaltung (id,artikel,bestellung,menge,vpe,zeit,bearbeiter) 
                  VALUES ('','$artikel','$bestellung','$menge','$vpe',NOW(),'".$this->app->User->GetName()."')");
              // drucken (inkl. chargennummer)
              $ch = $this->app->DB->GetInsertID();
              $chargemindest = $ch;
            } else if($chargenverwaltung=="2")
            {
              $charge = $this->app->Secure->GetPOST("charge");
              $chargemindest = $charge;	
            }
            else $ch = 0;
            //START
            // Mindesthaltbarkeitsdatum buchen
            $mhd = $this->app->String->Convert($this->app->Secure->GetPOST("mhd"),"%1.%2.%3","%3-%2-%1");
            $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($artikel,$menge,$lagerplatz,$mhd,$chargemindest,$zid);

            if($chargenverwaltung > 0)
            {
              $datum = date('Y-m-d');
              $this->app->erp->AddChargeLagerOhneBewegung($artikel,$menge,$lagerplatz,$datum,$chargemindest,"",$zid);
            }

            //ENDE			
            $this->app->DB->Insert("INSERT INTO lager_bewegung (id,lager_platz,artikel,menge,eingang,zeit,referenz,vpe,bearbeiter) VALUES  
                ('','$lager','$artikel','$menge','1',NOW(),'Wareneingang von Bestellung $bestellung_belegnr','$vpe','".$this->app->User->GetName()."')");	    

              $this->app->DB->Insert("INSERT INTO lager_platz_inhalt (id,lager_platz,artikel,menge,vpe,bearbeiter,bestellung) VALUES  
                  ('','$lager','$artikel','$menge','$vpe','".$this->app->User->GetName()."','$bestellung')");	    

              // die id von lager_platz_inhalt ist die chargenid UND JETZT ERST DRUCKEN!!!!!!!!!!

          }

          //Seriennummern buchen
          $tmpcheck = $this->app->Secure->GetPOST("seriennummern");


          for($checkser=0;$checkser < $menge; $checkser++)
          {
            if($tmpcheck[$checkser]!="")
              $this->app->erp->AddSeriennummerLager($artikel,$lagerplatz,$tmpcheck[$checkser],"",$zid,$mhd,$chargemindest);
            else
              $error++;
          }



          break;

        case "produktion":
          // drucken $anzahl etiketten
          // buchen in produktionstabelle
          // wenn produktionsartikel muss artikel in der maske produktion erscheinen 
          $this->app->DB->Insert("INSERT INTO produktionslager (id,artikel,menge,bemerkung,status,bearbeiter,vpe,bestellung_pos,projekt)
              VALUES ('','$artikel','$menge','$bemerkung','offen','".$this->app->User->GetName()."','$vpe','$pos','$projekt')");
          $tmpid= $this->app->DB->GetInsertID();

          if($this->app->erp->Firmendaten("standardetikettendrucker")>0)
          {
            if($this->app->erp->Firmendaten("standardetikettendrucker")>0)
            { 
              for($i=0;$i<$anzahl;$i++)
                HttpClient::quickGet("http://".$this->app->erp->GetIPAdapterbox($this->app->erp->Firmendaten("standardetikettendrucker"))."/druck_dms.php?nr=$tmpid&label=Produktion");
            }
          }
          break;

        case "mitarbeiter":
          // buchen als mitarbeiter inventar auf das projekt was angegeben ist
          // wenn mitarbeiterartikel muss artikel als inventar dem mitarbeiter gebucht werden fuer projekt bla bla
          $this->app->DB->Insert("INSERT INTO projekt_inventar (id,artikel,menge,projekt,mitarbeiter,bestellung,zeit,vpe)
              VALUES('','$artikel','$menge','$projekt','$mitarbeiter','$bestellung',NOW(),'$vpe')");
          break;


        case "rma":

          $lieferschein = $bestellung;

          $id = $this->app->Secure->GetGET("id");
          /*
             echo "lieferschein: ".$lieferschein;
             echo "<br>";
             echo "wunsch: ".$wunsch;
             echo "<br>";
             echo "bemerkung: ".$bemerkung;
             echo "<br>";
             echo "artikel: ".$artikel;
             echo "<br>";
             echo "pos : ".$pos;
           */

          // wunsch und bemerkung
          // buchen als mitarbeiter inventar auf das projekt was angegeben ist
          // wenn mitarbeiterartikel muss artikel als inventar dem mitarbeiter gebucht werden fuer projekt bla bla

          $this->app->DB->Insert("INSERT INTO rma_artikel (id,adresse,bearbeiter,lieferschein,pos,wunsch,bemerkung,artikel,menge,status,angelegtam,firma,wareneingang)
              VALUES('','$adresse','".$this->app->User->GetName()."','$lieferschein','$pos','$wunsch','$bemerkung','$artikel','$menge','angelegt',NOW(),'".$this->app->User->GetFirma()."','$id')");

          $tmpid= $this->app->DB->GetInsertID();

          if($this->app->erp->Firmendaten("standardetikettendrucker")>0)
            HttpClient::quickGet("http://".$this->app->erp->GetIPAdapterbox($this->app->erp->Firmendaten("standardetikettendrucker"))."/druck_dms.php?nr=$tmpid&label=RMA");

          break;


        default:
          echo "ACHTUNG DAS DARF NICHT PASSIEREN!! WAWISION ENTWICKLUNG HOLEN! FEHLER IM PROGRAMM?";  
      }

      if($typ!="rma")
      {
        // Distribution speichern!
        $this->app->DB->Insert("INSERT INTO paketdistribution (id,bearbeiter,zeit,paketannahme,adresse,artikel,menge,vpe,etiketten,bemerkung,bestellung_position)
            VALUES ('','".$this->app->User->GetName()."',NOW(),'$id','$adresse','$artikel','$menge','$vpe','$etiketten','$bemerkung','$pos')");

        // anzahl gelieferte erhoehen bestellung_position !!!
        $geliefert = $this->app->DB->Select("SELECT geliefert FROM bestellung_position WHERE id='$pos' LIMIT 1");
        $gesamt_erwartet = $this->app->DB->Select("SELECT menge FROM bestellung_position WHERE id='$pos' LIMIT 1");
        $geliefert = $geliefert + $menge;
        $this->app->DB->Update("UPDATE bestellung_position SET geliefert='$geliefert' WHERE id='$pos' LIMIT 1");
      }

      // alles passt weiter im abschluss
      header("Location: index.php?module=wareneingang&action=distriabschluss&id=$id&pos=$pos&typ=$typ&rma=$rma");
      exit;
    }

    $this->app->Tpl->Set(AKTIV_TAB2,"tabs-1");
    $this->app->Tpl->Parse(PAGE,"wareneingangpaketdistribution.tpl");
  }


  function WareneingangPaketDistriAbschluss()
  {
    $id = $this->app->Secure->GetGET("id");
    $typ  = $this->app->Secure->GetGET("typ");
    $submit = $this->app->Secure->GetGET("submit");
    $abschliessen = $this->app->Secure->GetPOST("abschliessen");
    $weiter = $this->app->Secure->GetPOST("weiter");

    $this->WareneingangPaketDistriMenu(); 


    //if($weiter!="")
    //{
    header("Location: index.php?module=wareneingang&action=distriinhalt&id=$id");
    exit;
    //}

    if($abschliessen!="")
    {
      // paketannahme auf abgeschlossen setzten
      $this->app->DB->Update("UPDATE paketannahme SET status='abgeschlossen' WHERE id='$id' LIMIT 1");

      if($typ=="rma")
      {
        //RMA bericht drucken mit allen artikeln des Kunden



      }

      if($this->app->erp->Firmendaten("wareneingang_kamera_waage")=="1")
        header("Location: index.php?module=wareneingang&action=distribution");
      else
        header("Location: index.php?module=wareneingang&action=paketannahme");
      exit;
    }

    if($typ=="rma")
    {
      $this->app->Tpl->Set(PAGE,"<form action=\"\" method=\"post\"><br><br><center>
          <input type=\"submit\" name=\"weiter\" value=\"weitere Artikel aus dem Paket zurordnen\">&nbsp;
          <input type=\"submit\" name=\"abschliessen\" value=\"Paket ist komplett erfasst!\"></center></form>");

    } else {

      $this->app->Tpl->Set(PAGE,"<form action=\"\" method=\"post\"><br><br><center>
          <input type=\"submit\" name=\"weiter\" value=\"weitere Artikel aus dem Paket zurordnen\">&nbsp;
          <input type=\"submit\" name=\"abschliessen\" value=\"Paketinhalt ist jetzt komplett erfasst!\"></center></form>");
    }
  }



  function WareneingangPaketDistribution()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->WareneingangPaketDistriMenu(); 

    if(is_numeric($id))
    {
      //$this->app->FormHandler->FormGetVars("paketannahme",$id);


      if($this->app->Secure->GetPOST("submit")!="")
      {
        $beipack_rechnung = $this->app->Secure->GetPOST("rechnung");
        $beipack_lieferschein = $this->app->Secure->GetPOST("lieferschein");
        $beipack_anschreiben = $this->app->Secure->GetPOST("anschreiben");
        $beipack_gesamt = $this->app->Secure->GetPOST("gesamt");
        $postgrund = $this->app->Secure->GetPOST("postgrund");
        //speichern und weiter
        $this->app->DB->Update("UPDATE paketannahme SET	    
            beipack_rechnung='$beipack_rechnung',beipack_lieferschein='$beipack_lieferschein', 
            beipack_anschreiben='$beipack_anschreiben',beipack_gesamt='$beipack_gesamt',status='distribution',bearbeiter_distribution='{$this->app->User->GetName()}',postgrund='$postgrund'
            WHERE id='$id' LIMIT 1");


        if($beipack_rechnung)
        {
          $file = $this->app->erp->CreateDateiOhneInitialeVersion("Rechnung von Paketannahme $id","Dokument aus Paket","",$this->app->User->GetName());
          $this->app->erp->AddDateiStichwort($file,"Rechnung","Paketannahme",$id);
          if($this->app->erp->Firmendaten("standardetikettendrucker")>0)
            HttpClient::quickGet("http://".$this->app->erp->GetIPAdapterbox($this->app->erp->Firmendaten("standardetikettendrucker"))."/druck_dms.php?nr=$file&label=Rechnung");
        }
        if($beipack_lieferschein)
        {
          $file = $this->app->erp->CreateDateiOhneInitialeVersion("Lieferschein von Paketannahme $id","Dokument aus Paket","",$this->app->User->GetName());
          $this->app->erp->AddDateiStichwort($file,"Lieferschein","Paketannahme",$id);

          if($this->app->erp->Firmendaten("standardetikettendrucker")>0)
            HttpClient::quickGet("http://".$this->app->erp->GetIPAdapterbox($this->app->erp->Firmendaten("standardetikettendrucker"))."/druck_dms.php?nr=$file&label=Lieferschein");
        }
        if($beipack_anschreiben)
        {
          $file = $this->app->erp->CreateDateiOhneInitialeVersion("Anschreiben von Paketannahme $id","Dokument aus Paket","",$this->app->User->GetName());
          $this->app->erp->AddDateiStichwort($file,"Anschreiben","Paketannahme",$id);
          if($this->app->erp->Firmendaten("standardetikettendrucker")>0)
            HttpClient::quickGet("http://".$this->app->erp->GetIPAdapterbox($this->app->erp->Firmendaten("standardetikettendrucker"))."/druck_dms.php?nr=$file&label=Anschreiben");
        }
        // hier dms anlegen!!!!! nummer geben lassen und etikett drucken!
        for($r=0;$r<$beipack_gesamt;$r++)
        {
          $file = $this->app->erp->CreateDateiOhneInitialeVersion("Dokument von Paketannahme $id","Dokument aus Paket","",$this->app->User->GetName());
          $this->app->erp->AddDateiStichwort($file,"Dokument","Paketannahme",$id);

          if($this->app->erp->Firmendaten("standardetikettendrucker")>0)
            HttpClient::quickGet("http://".$this->app->erp->GetIPAdapterbox($this->app->erp->Firmendaten("standardetikettendrucker"))."/druck_dms.php?nr=$file&label=Dokument");
        }


        header("Location: index.php?module=wareneingang&action=distriinhalt&id=$id");
        exit;
      }

      $tmp = $this->app->DB->SelectArr("SELECT * FROM paketannahme WHERE id='$id' LIMIT 1");
      $this->app->Tpl->Set(ADRESSE,$this->app->DB->Select("SELECT name FROM adresse WHERE id='{$tmp[0][adresse]}' AND geloescht=0 LIMIT 1"));
      $kunde = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='{$tmp[0][adresse]}' AND geloescht=0 LIMIT 1");
      $this->app->Tpl->Set(KUNDE,$nr = $kunde?$kunde:"kein Kunde");
      $lieferant = $this->app->DB->Select("SELECT lieferantennummer FROM adresse WHERE id='{$tmp[0][adresse]}' AND geloescht=0 LIMIT 1");
      $this->app->Tpl->Set(LIEFERANT,$lieferant?$lieferant:"kein Lieferant");

      $this->app->Tpl->Set(STATUS,$tmp[0][status]);
      $this->app->Tpl->Set(DATUM,$this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y %H:%i') FROM paketannahme WHERE id='$id' LIMIT 1"));
      $this->app->Tpl->Set(BEARBEITER,$tmp[0][bearbeiter]);
      $this->app->Tpl->Set(GEWICHT,$tmp[0][gewicht]);
      $this->app->Tpl->Set(FOTO,$tmp[0][foto]);


      $anzahl = array(0,1,2,3,4,5);
      $select = $this->app->erp->GetSelect($anzahl,$tmp[0][beipack_gesamt]);

      $this->app->Tpl->Set(RECHNUNG,$tmp[0][beipack_rechnung]?"checked":"");
      $this->app->Tpl->Set(LIEFERSCHEIN,$tmp[0][beipack_lieferschein]?"checked":"");
      $this->app->Tpl->Set(ANSCHREIBEN,$tmp[0][beipack_anschreiben]?"checked":"");
      $this->app->Tpl->Set(GESAMT,$select);

      if($kunde > 0)
      {
        $postgrund = array(""=>"bitte w&auml;hlen","verweigert"=>"Annahme verweigert","unbekannt"=>"Empf&auml;nger unbekannt","porto"=>"Zu wenig frankiert","rma"=>"RMA Paketmarke","zusendung"=>"Zusendung");
        $postgrund= $this->app->erp->GetSelectAsso($postgrund,$tmp[0][postgrund]);
        $this->app->Tpl->Set(POSTGRUND,$postgrund);
      } else {
        // wenn adresse nur lieferant ist
        $this->app->Tpl->Set(POSTGRUNDENABLE,"<!--");
        $this->app->Tpl->Set(POSTGRUNDDISABLE,"-->");
      }



      $this->app->Tpl->Parse(TAB1,"wareneingangpaketdistribution_tab1.tpl");
      $this->app->Tpl->Set(AKTIV_TAB1,"tabs-1");
      $this->app->Tpl->Parse(PAGE,"wareneingangpaketdistribution.tpl");
    } else 
    {

      // pruefen welche pakete auf ausgepackt gesetzt gehoeren //TODO auf abgeschlossen setzen

      $this->app->Tpl->Set(SUBHEADING,"Lieferungen");
      //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
      $table = new EasyTable($this->app);
      $table->Query("SELECT DATE_FORMAT(p.datum,'%d.%m.%Y') as datum,p.id as paket, a.name,bearbeiter as Paketannahme,p.id  FROM paketannahme p LEFT JOIN adresse a ON a.id=p.adresse WHERE status='angenommen'");
      $table->DisplayNew(INHALT,"<a href=\"index.php?module=wareneingang&action=distribution&id=%value%\">auspacken</a>");
      $this->app->Tpl->Parse(TAB1,"rahmen.tpl");
      $this->app->Tpl->Set(INHALT,"");

      $this->app->Tpl->Set(SUBHEADING,"Lieferungen in Bearbeitung");
      //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
      $table = new EasyTable($this->app);
      $table->Query("SELECT DATE_FORMAT(p.datum,'%d.%m.%Y') as datum,p.id as paket, a.name,bearbeiter_distribution as bearbeiter,p.id  FROM paketannahme p LEFT JOIN adresse a ON a.id=p.adresse WHERE status='distribution'");
      $table->DisplayNew(INHALT,"<a href=\"index.php?module=wareneingang&action=distriinhalt&id=%value%\">weiter auspacken</a>&nbsp;<a href=\"index.php?module=wareneingang&action=distriabschluss&id=%value%\">Abschlie&szlig;en</a>");
      $this->app->Tpl->Parse(TAB1,"rahmen.tpl");

      $this->app->Tpl->Set(INHALT,"");



      $this->app->Tpl->Set(SUBHEADING,"Abgeschlossene Lieferungen (letzten 14 Tage)");
      /*
         $table = new EasyTable($this->app);
         $table->Query("SELECT DATE_FORMAT(p.datum,'%d.%m.%Y') as datum,p.id as Nr, a.name,bearbeiter_distribution,p.id  FROM paketannahme p LEFT JOIN adresse a ON a.id=p.adresse WHERE status='abgeschlossen'");
         $table->DisplayNew(INHALT,"<a href=\"index.php?module=wareneingang&action=distribution&id=%value%\">Betrachten</a>&nbsp;<a href=#>&Uuml;bernehmen</a>");
         $this->app->Tpl->Parse(TAB1,"rahmen.tpl");

       */
      $this->app->YUI->TableSearch(TAB2,"wareneingangarchiv");

      $this->app->Tpl->Parse(PAGE,"wareneinganguebersicht.tpl");
    }

  }


  function WareneingangPaketAbsender()
  {
    $id = $this->app->Secure->GetGET("id");
    $submit = $this->app->Secure->GetPOST("submit");
    $zurueck= $this->app->Secure->GetPOST("zurueck");
    $this->WareneingangPaketMenu();

    $adresse = $this->app->DB->Select("SELECT adresse FROM paketannahme WHERE id='$id' LIMIT 1");

    if($zurueck!="")
    {
      $this->app->DB->Delete("DELETE FROM paketannahme WHERE id='$id' LIMIT 1");
      header("Location: index.php?module=wareneingang&action=paketannahme");
      exit;
    }

    if($submit!="")
      header("Location: index.php?module=wareneingang&action=paketzustand&id=$id");


    $this->app->FormHandler->FormGetVars("adresse",$adresse);

    $this->app->Tpl->Parse(TAB1,"wareneingangpaketannahme_tab2.tpl");

    $this->app->Tpl->Set(AKTIV_TAB2,"tabs-1");
    $this->app->Tpl->Parse(PAGE,"wareneingangpaketannahme.tpl");
  }

  function WareneingangPaketZustand()
  {
    $this->WareneingangPaketMenu();
    $id = $this->app->Secure->GetGET("id");
    $submit = $this->app->Secure->GetPOST("submit");
    if($submit!="")
    {
      $this->app->FormHandler->FormUpdateDatabase("paketannahme",$id);

      header("Location: index.php?module=wareneingang&action=paketetikett&id=$id");
      exit;
    }
    //$client = new HttpClient("192.168.0.171");
    $wareneingang_kamera_waage = $this->app->DB->Select("SELECT wareneingang_kamera_waage FROM firmendaten LIMIT 1");

    if($wareneingang_kamera_waage == "1")
      $pageContent = HttpClient::quickGet("http://192.168.0.53/manage.php");

    $gewicht = $pageContent;

    $gewicht = intval($gewicht)-2;

    if($wareneingang_kamera_waage =="1")
      $this->app->Tpl->Set(GEWICHT,$gewicht);
    else
      $this->app->Tpl->Set(GEWICHT,"none");


    if($wareneingang_kamera_waage == "1"){
      $datei = HttpClient::quickGet("http://192.168.0.53/snap.jpg");
      $this->app->Tpl->Set(LIVEFOTO,'<img src="http://localhost/snap.jpg" width="400">');
      //}	

      $ersteller = $this->app->User->GetName();
      $file = $this->app->erp->CreateDatei(date('Ymd')."_paketannahme_$id.jpg","Paketannahme $id","","",$datei,$ersteller);

      $this->app->Tpl->Set(FOTO,$file);

      $this->app->erp->AddDateiStichwort($file,"Bild","Paketannahme",$id);
  }

  if($gewicht <= 0 && $wareneingang_kamera_waage==1)
    $this->app->Tpl->Set(MELDUNG,"<div class=\"error\">Bitte legen Sie das Paket auf die Waage und schie&szlig;en Sie nochmal ein Foto!</div>");
  else if ($gewicht <= 0 && $wareneingang_kamera_waage !=1)
    $this->app->Tpl->Set(MELDUNG,"<div class=\"info\">Status: Ohne Waage und Kamera Funktion</div>");


  $this->app->Tpl->Parse(TAB1,"wareneingangpaketannahme_tab3.tpl");
  $this->app->Tpl->Set(AKTIV_TAB3,"tabs-1");
  $this->app->Tpl->Parse(PAGE,"wareneingangpaketannahme.tpl");
}


function WareneingangPaketEtikett()
{
  $this->WareneingangPaketMenu();
  $id = $this->app->Secure->GetGET("id");
  $submit = $this->app->Secure->GetPOST("submit");
  if($submit!="")
    header("Location: index.php?module=wareneingang&action=paketabschliessen&id=$id");


  $this->app->Tpl->Parse(TAB1,"wareneingangpaketannahme_tab4.tpl");
  $this->app->Tpl->Set(AKTIV_TAB4,"tabs-1");
  $this->app->Tpl->Parse(PAGE,"wareneingangpaketannahme.tpl");
}

function WareneingangPaketAbschliessen()
{
  $this->WareneingangPaketMenu();
  $id = $this->app->Secure->GetGET("id");
  $weiteres= $this->app->Secure->GetPOST("weiteres");
  $abschluss= $this->app->Secure->GetPOST("abschluss");
  $distri= $this->app->Secure->GetPOST("distri");

  if($weiteres!="")
    header("Location: index.php?module=wareneingang&action=paketannahme");
  if($abschluss!="")
  {
    if($this->app->erp->Firmendaten("wareneingang_kamera_waage")=="1")
      header("Location: index.php?module=wareneingang&action=distribution");
    else
      header("Location: index.php?module=wareneingang&action=paketannahme");

  }
  if($distri!="")
    header("Location: index.php?module=wareneingang&action=distribution&id=$id");




  $this->app->Tpl->Parse(TAB1,"wareneingangpaketannahme_tab5.tpl");
  $this->app->Tpl->Set(AKTIV_TAB5,"tabs-1");
  $this->app->Tpl->Parse(PAGE,"wareneingangpaketannahme.tpl");
}




function WareneingangList()
{
  $this->WareneingangMenu();

  $this->app->Tpl->Set(SUBHEADING,"Lieferungen");
  //Jeder der in Nachbesserung war egal ob auto oder manuell wandert anschliessend in Manuelle-Freigabe");
  $table = new EasyTable($this->app);
  $table->Query("SELECT '23.11.2009' as datum, 'Olimex' as lieferant,id FROM aufgabe LIMIT 3");
  $table->DisplayNew(INHALT,"<a href=\"index.php?module=ticket&action=assistent&id=%value%\">Lesen</a>");
  $this->app->Tpl->Parse(TAB1,"rahmen.tpl");
  $this->app->Tpl->Set(INHALT,"");


  $this->app->Tpl->Set(AKTIV_TAB1,"tabs-1");
  $this->app->Tpl->Parse(PAGE,"wareneinganguebersicht.tpl");
}

function WareneingangPaketannahme()
{
  $this->WareneingangPaketMenu();

  $vorlage= $this->app->Secure->GetGET("vorlage");
  $suche= $this->app->Secure->GetPOST("suche");
  $id = $this->app->Secure->GetGET("id");

  if($vorlage!="")
  {
    if($vorlage=="bestellung")
    {
      $vorlageid = $id;
      $adresse = $this->app->DB->Select("SELECT adresse FROM bestellung WHERE id='$id' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT projekt FROM bestellung WHERE id='$id' LIMIT 1");
    } 
    else if ($vorlage=="rma")
    {
      $vorlageid = $id;
      $adresse = $this->app->DB->Select("SELECT adresse FROM rma WHERE id='$id' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT projekt FROM rma WHERE id='$id' LIMIT 1");
    }
    else if ($vorlage=="adresse")
    {
      $adresse = $id;
      $vorlageid = $adresse;
      // standardprojekt von kunde
      $projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    } else exit;

    $bearbeiter = $this->app->User->GetName(); 

    $sql = "INSERT INTO paketannahme (id,datum,adresse,vorlage,vorlageid,projekt,bearbeiter,status) VALUES
      ('',NOW(),'$adresse','$vorlage','$vorlageid','$projekt','$bearbeiter','angenommen')";
    $this->app->DB->Insert($sql);
    $id = $this->app->DB->GetInsertID();

    if($this->app->erp->Firmendaten("wareneingang_kamera_waage")=="1")
      header("Location: index.php?module=wareneingang&action=paketabsender&id=$id");
    else
      header("Location: index.php?module=wareneingang&action=distriinhalt&id=$id");
    exit;
  }

  $this->app->YUI->TableSearch(SUCHE,"paketannahme");
  /*
     if($suche!="")  
     {
     $table = new EasyTable($this->app);
     $this->app->Tpl->Set(SUCHE,"<h2>Trefferliste:</h2><br>");
     $table->Query("SELECT name, plz, ort, strasse, id FROM adresse WHERE (name LIKE '%$suche%' or plz='$suche') AND geloescht=0");
     $table->DisplayNew(SUCHE,"<a href=\"index.php?module=wareneingang&action=paketannahme&id=%value%&vorlage=adresse\">Adresse ausw&auml;hlen</a>");
     } else {
     $letzte_adresse = $this->app->DB->Select("SELECT adresse FROM paketannahme Order by datum DESC LIMIT 1");
     $this->app->Tpl->Set(SUCHE,"<h2>Letzte Paketannahme:</h2><br>");
     $table = new EasyTable($this->app);
     $table->Query("SELECT name, plz, ort, strasse, id FROM adresse WHERE id='$letzte_adresse' AND geloescht=0");
     $table->DisplayNew(SUCHE,"<a href=\"index.php?module=wareneingang&action=paketannahme&id=%value%&vorlage=adresse\">Adresse nochmal ausw&auml;hlen</a>");
     }
   */
  $table = new EasyTable($this->app);
  $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%Y') as datum, name, belegnr as bestellung, id FROM bestellung WHERE status!='geliefert'");
  $table->DisplayNew(BESTELLUNGEN,"<a href=\"index.php?module=wareneingang&action=paketannahme&id=%value%&vorlage=bestellung\">weiter</a>");

  $table = new EasyTable($this->app);
  $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%Y') as datum, name, belegnr as RMA, id FROM rma WHERE status!='geliefert'");
  $table->DisplayNew(RMA,"<a href=\"index.php?module=wareneingang&action=paketannahme&id=%value%&vorlage=rma\">weiter</a>");

  $this->app->YUI->AutoComplete("suche","adressename");
  $this->app->Tpl->Parse(TAB1,"wareneingangpaketannahme_tab1.tpl");

  $this->app->Tpl->Set(AKTIV_TAB1,"tabs-1");
  $this->app->Tpl->Parse(PAGE,"wareneingangpaketannahme.tpl");
}




}
?>
