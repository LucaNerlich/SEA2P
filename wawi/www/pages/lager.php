<?php
include ("_gen/lager.php");
class Lager extends GenLager {
  var $app;
  function Lager($app) {
    //parent::GenLager($app);
    $this->app = & $app;
    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler("create", "LagerCreate");
    $this->app->ActionHandler("edit", "LagerEdit");
    $this->app->ActionHandler("list", "LagerList");
    $this->app->ActionHandler("platz", "LagerPlatz");
    $this->app->ActionHandler("bewegung", "LagerBewegung");
    $this->app->ActionHandler("bewegungpopup", "LagerBewegungPopup");
    $this->app->ActionHandler("bestand", "LagerBestand");
    $this->app->ActionHandler("inhalt", "LagerInhalt");
    $this->app->ActionHandler("wert", "LagerWert");
    $this->app->ActionHandler("ausgehend", "LagerAusgehend");
    $this->app->ActionHandler("inventur", "LagerInventur");
    $this->app->ActionHandler("platzeditpopup", "LagerPlatzEditPopup");
    $this->app->ActionHandler("delete", "LagerDelete");
    $this->app->ActionHandler("deleteplatz", "LagerPlatzDelete");
    $this->app->ActionHandler("etiketten", "LagerEtiketten");
    $this->app->ActionHandler("zwischenlager", "LagerZwischenlager");
    $this->app->ActionHandler("artikelfuerlieferungen", "LagerArtikelfuerlieferungen");
    $this->app->ActionHandler("produktionslager", "LagerProduktionslager");
    $this->app->ActionHandler("regaletiketten", "LagerRegalEtiketten");
    $this->app->ActionHandler("reservierungen", "LagerReservierungen");
    $this->app->ActionHandler("buchen", "LagerBuchen");
    $this->app->ActionHandler("buchenzwischenlager", "LagerBuchenZwischenlager");
    $this->app->ActionHandler("buchenzwischenlagerdelete", "LagerBuchenZwischenlagerDelete");
    $this->app->ActionHandler("bucheneinlagern", "LagerBuchenEinlagern");
    $this->app->ActionHandler("buchenauslagern", "LagerBuchenAuslagern");
    $this->app->ActionHandler("artikelentfernen", "LagerArtikelEntfernen");
    $this->app->ActionHandler("artikelentfernenreserviert", "LagerArtikelEntfernenReserviert");
    $this->app->ActionHandler("auslagernproduktion", "LagerAuslagernArtikellisteProduktion");
    $this->app->ActionHandler("lagerpdfsammelentnahme", "LagerPDFSammelentnahme");
    $this->app->ActionHandler("inventurladen", "LagerInventurLaden");
    $this->app->ActionHandler("nachschublager", "LagerNachschublager");
    $this->app->ActionHandler("letztebewegungen", "LagerLetzteBewegungen");
    $this->app->ActionHandler("schnelleinlagern", "LagerSchnellEinlagern");
    $this->app->ActionHandler("schnellumlagern", "LagerSchnellUmlagern");
    $this->app->ActionHandler("schnellauslagern", "LagerSchnellAuslagern");
    $this->app->ActionHandler("differenzen", "LagerDifferenzen");

    $id = $this->app->Secure->GetGET("id");
    $nummer = $this->app->Secure->GetPOST("nummer");
    if ($nummer == "") $lager = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id='$id' LIMIT 1");
    else $lager = $nummer;
    $woher = $this->app->Secure->GetPOST("woher");
    $action = $this->app->Secure->GetGET("action");
    $cmd = $this->app->Secure->GetGET("cmd");
    if ($action == "bucheneinlagern") if ($cmd == "zwischenlager") $lager = "Zwischenlager";
    else $lager = "Manuelle Lageranpassung";
    $this->app->Tpl->Set(UEBERSCHRIFT, "Lager: " . $lager);
    $this->app->ActionHandlerListen($app);
    $this->app = $app;
  }

  function LagerSchnellEinlagern()
  {
    $this->LagerBuchenMenu();
    $submit = $this->app->Secure->GetPOST("submit");
    $nummer = $this->app->Secure->GetPOST("nummer");
    $menge = $this->app->Secure->GetPOST("menge");
    $grundreferenz = $this->app->Secure->GetPOST("grundreferenz");

    if($submit!="")
    {


    }



    $this->app->YUI->AutoComplete('nummer','lagerartikelnummer');
    $this->app->YUI->AutoComplete('grundreferenz','lagergrund');
    $this->app->Tpl->Parse(TAB1,"lager_schnelleinlagern.tpl");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }	


  function LagerDifferenzen()
  {
    $this->LagerHauptmenu();

    $cmd = $this->app->Secure->GetGET("cmd");

    if($cmd=="berechnen")
    {
      $artikelarr = $this->app->DB->SelectArr("SELECT id FROM artikel WHERE lagerartikel=1 AND geloescht!=1"); 


      $this->app->DB->Delete("DELETE FROM lager_differenzen WHERE user='".$this->app->User->GetID()."'");

      for($i=0;$i<count($artikelarr);$i++)
      {
        $artikelarr[$i] = $artikelarr[$i]['id'];
        $eingang = $this->app->DB->Select("SELECT SUM(menge) FROM lager_bewegung WHERE artikel='".$artikelarr[$i]."' AND eingang=1");
        $ausgang = $this->app->DB->Select("SELECT SUM(menge) FROM lager_bewegung WHERE artikel='".$artikelarr[$i]."' AND eingang=0");
        $bestand = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='".$artikelarr[$i]."'");

        $differenz = $eingang - $ausgang - $bestand;
        $berechnet = $eingang - $ausgang;

        if($differenz  != 0  )
        {
          $this->app->DB->Insert("INSERT INTO lager_differenzen (id,eingang,ausgang,berechnet,bestand,differenz,artikel,user) VALUES 
            ('','$eingang','$ausgang','$berechnet','$bestand','$differenz','".$artikelarr[$i]."','".$this->app->User->GetID()."')"); 
        }

      }

    } 

    $this->app->YUI->TableSearch(TAB1,"lagerdifferenzen");

    
    $this->app->Tpl->Add(TAB1,"<center><input type=\"button\" value=\"Lager Differenzen neu berechnen\" 
        onclick=\"window.location.href='index.php?module=lager&action=differenzen&cmd=berechnen'\"></center>");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function LagerSchnellUmlagern()
  {
    $this->LagerBuchenMenu();
    $submit = $this->app->Secure->GetPOST("submit");
    $nummer = $this->app->Secure->GetPOST("nummer");
    $get_nummer = $this->app->Secure->GetGET("nummer");
    $menge = $this->app->Secure->GetPOST("menge");
    $grundreferenz = $this->app->Secure->GetPOST("grundreferenz");
    $ziellager = $this->app->Secure->GetPOST("ziellager");

    if($get_nummer!="")
      $this->app->Tpl->Set(FOCUS,"ziellager");
    else
      $this->app->Tpl->Set(FOCUS,"nummer");

    if($grundreferenz!="") $this->app->User->SetParameter("lager_schnellumlagern_grund",$grundreferenz);
    if($ziellager!="") $this->app->User->SetParameter("lager_schnellumlagern_ziellager",$ziellager);

    if($submit!="")
    {
      $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND nummer!='' LIMIT 1");			
      if($artikelid <=0)
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='$nummer' AND ean!='' LIMIT 1");			
      if($artikelid <=0)
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE herstellernummer='$nummer' herstellernummer!='' LIMIT 1");			

      $lager_platz = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$ziellager' AND kurzbezeichnung!='' LIMIT 1");			

      $name_de = $this->app->DB->Select("SELECT CONCAT(nummer,' ',name_de) FROM artikel WHERE id='$artikelid' LIMIT 1");
      //$projekt = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$artikelid' LIMIT 1");

      if($artikelid > 0 && $lager_platz > 0)
      {
        $anzahl_artikel = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikelid'");
        if($anzahl_artikel >= $menge)
        {
          // auslagern bevorzugt aus lager_platz ansonsten von den anderen
          $this->app->erp->LagerAutoAuslagernArtikel($artikelid,$menge,$grundreferenz);

          // einlagern lager_platz
          $this->app->erp->LagerEinlagern($artikelid,$menge,$lager_platz,$projekt,$grundreferenz);

          $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Der Artikel $name_de wurde $menge mal umgelagert!</div>");
          header("Location: index.php?module=lager&action=schnellumlagern&msg=$msg");
          exit;
        } else {
          if($anzahl_artikel > 0)
            $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Artikel ist nur maximal $anzahl_artikel im Lager vorhanden! Bitte korrekte Menge angeben!</div>");
          else
            $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Artikel hat keinen Bestand im Lager!</div>");
          header("Location: index.php?module=lager&action=schnellumlagern&msg=$msg");
          exit;
        }	

      } else {
        if($artikelid<=0)
        {
          $msg = "<div class=\"error\">Der Artikel mit der Nummer $nummer wurde nicht gefunden!</div>";
          $get_nummer = "";
        }
        else
          $get_nummer = $nummer;

        if($lager_platz<=0)
        {
          $msg .= "<div class=\"error\">Das Ziellager $ziellager wurde nicht gefunden!</div>";
          $this->app->User->SetParameter("lager_schnellumlagern_ziellager","");
        }
        $msg = $this->app->erp->base64_url_encode($msg);
        header("Location: index.php?module=lager&action=schnellumlagern&msg=$msg&nummer=$get_nummer");
        exit;
      }
    } else {
      $msg = $this->app->Secure->GetGET("msg");
      if($msg=="")
        $this->app->Tpl->Set(MESSAGE,'<div class="info">Der Artikel wird wenn vorhanden aus dem Standardlager ausgelagert.</div>');
    }

    if($grundreferenz=="") $grundreferenz=$this->app->User->GetParameter("lager_schnellumlagern_grund");
    if($ziellager=="") $ziellager=$this->app->User->GetParameter("lager_schnellumlagern_ziellager");
    $this->app->Tpl->Set(GRUNDREFERENZ,$grundreferenz);
    $this->app->Tpl->Set(ZIELLAGER,$ziellager);

    $this->app->Tpl->Set(NUMMER,$get_nummer);
    $this->app->YUI->AutoComplete('nummer','lagerartikelnummer',1);
    $this->app->YUI->AutoComplete('ziellager','lagerplatz');
    $this->app->YUI->AutoComplete('grundreferenz','lagergrund');
    $this->app->Tpl->Parse(PAGE,"lager_schnellumlagern.tpl");
  }	



  function LagerSchnellAuslagern()
  {
    $this->LagerBuchenMenu();
    $submit = $this->app->Secure->GetPOST("submit");
    $nummer = $this->app->Secure->GetPOST("nummer");
    $menge = $this->app->Secure->GetPOST("menge");
    $grundreferenz = $this->app->Secure->GetPOST("grundreferenz");

    if($grundreferenz!="") $this->app->User->SetParameter("lager_schnellauslagern_grund",$grundreferenz);

    if($submit!="")
    {
      $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND nummer!='' LIMIT 1");			
      if($artikelid <=0)
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='$nummer' AND ean!='' LIMIT 1");			
      if($artikelid <=0)
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE herstellernummer='$nummer' herstellernummer!='' LIMIT 1");			

      $name_de = $this->app->DB->Select("SELECT CONCAT(nummer,' ',name_de) FROM artikel WHERE id='$artikelid' LIMIT 1");

      if($artikelid > 0)
      {
        $anzahl_artikel = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikelid'");
        if($anzahl_artikel >= $menge )
        {
          // auslagern bevorzugt aus lager_platz ansonsten von den anderen
          $this->app->erp->LagerAutoAuslagernArtikel($artikelid,$menge,$grundreferenz);
          $msg = $this->app->erp->base64_url_encode("<div class=\"warning\">Der Artikel $name_de wurde $menge mal ausgelagert!</div>");
          header("Location: index.php?module=lager&action=schnellauslagern&msg=$msg");
          exit;
        } else {
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Artikel ist nur maximal $anzahl_artikel im Lager vorhanden! Bitte korrekte Menge angeben!</div>");
          header("Location: index.php?module=lager&action=schnellauslagern&msg=$msg");
          exit;
        }	

      } else {
        $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Artikel mit der Nummer $nummer wurde nicht gefunden!</div>");
        header("Location: index.php?module=lager&action=schnellauslagern&msg=$msg");
        exit;
      }
    } else {
      $msg = $this->app->Secure->GetGET("msg");
      if($msg=="")
        $this->app->Tpl->Set(MESSAGE,'<div class="info">Der Artikel wird wenn vorhanden aus dem Standardlager ausgelagert.</div>');
    }

    if($grundreferenz=="") $grundreferenz=$this->app->User->GetParameter("lager_schnellauslagern_grund");
    $this->app->Tpl->Set(GRUNDREFERENZ,$grundreferenz);

    $this->app->YUI->AutoComplete('nummer','lagerartikelnummer',1);
    $this->app->YUI->AutoComplete('grundreferenz','lagergrund');
    $this->app->Tpl->Parse(PAGE,"lager_schnellauslagern.tpl");
  }	



  function LagerWert()
  {
    $this->LagerHauptmenu();
    $this->app->Tpl->Set(TABTEXT,"Lagerbestandsberechnung");
    $summelager_zuletzt = $this->app->DB->SelectArr("SELECT FORMAT(SUM(IFNULL((SELECT e.preis FROM einkaufspreise e WHERE e.geloescht!=1 AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis <=NOW()) AND e.artikel=l.artikel ORDER by e.id DESC LIMIT 1),0)*l.menge),2,'de_DE') as wert, FORMAT(SUM(IFNULL((SELECT e.preis FROM einkaufspreise e WHERE e.geloescht!=1 AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis <=NOW()) AND e.artikel=l.artikel ORDER by e.preis DESC LIMIT 1),0)*l.menge),2,'de_DE') as wert2 FROM lager_platz_inhalt l LEFT JOIN artikel a ON a.id=l.artikel WHERE a.id > 0");

    //      $summelager_max = $this->app->DB->Select("SELECT FORMAT(SUM(IFNULL((SELECT e.preis FROM einkaufspreise e WHERE e.geloescht!=1 AND (e.gueltig_bis='0000-00-00' OR e.gueltig_bis <=NOW()) AND e.artikel=l.artikel ORDER by e.menge LIMIT 1),0)*l.menge),2,'de_DE') as wert, a.id FROM lager_platz_inhalt l LEFT JOIN artikel a ON a.id=l.artikel WHERE a.id > 0");


    $summelager_max = $summelager_zuletzt[0]['wert2'];
    $summelager_zuletzt = $summelager_zuletzt[0]['wert'];

    $this->app->Tpl->Set(TAB1,"<div class=\"info\">Lagerbestandswert: $summelager_zuletzt (zuletzt eingegebener EK-Preis) $summelager_max (berechnet auf teuersten EK-Preis)</div>");

    $this->app->YUI->TableSearch(TAB1,"lagerbestandsberechnung");

    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function LagerBuchenZwischenlagerDelete()
  {
    $id = $this->app->Secure->GetGET('id');

    $this->app->DB->Delete("DELETE FROM zwischenlager WHERE id='$id' LIMIT 1");
    header("Location: index.php?module=lager&action=buchenzwischenlager&top=TGFnZXI=");
    exit;
  }


  function LagerPlatzDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    //if(is_numeric($id))
    //  $this->app->DB->Delete("DELETE FROM lager WHERE id='$id' LIMIT 1");

    $numberofarticles = $this->app->DB->Select("SELECT COUNT(id) FROM lager_platz_inhalt WHERE lager_platz='$id' LIMIT 1");

    if($numberofarticles > 0)
    {
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">In diesem Lager existieren Artikel. Es k&ouml;nnen nur leere Lagerpl&auml;tze gel&ouml;scht werden!</div>");
    }
    else {
      $this->app->DB->Select("DELETE FROM lager_platz WHERE id='$id' LIMIT 1");
      $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Der Lagerplatz wurde gel&ouml;scht!</div>");
    }

    $ref = $_SERVER['HTTP_REFERER'];
    header("Location: $ref&msg=$msg");
    exit;
  }


  function LagerDelete()
  {
    $id = $this->app->Secure->GetGET('id');
    //if(is_numeric($id))
    //  $this->app->DB->Delete("DELETE FROM lager WHERE id='$id' LIMIT 1");

    $numberofarticles = $this->app->DB->Select("SELECT COUNT(id) FROM lager_platz WHERE lager='$id' LIMIT 1");

    if($numberofarticles > 0)
    {
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">In diesem Lager existieren noch Lagerpl&auml;tze. Es k&ouml;nnen nur leere Lager gel&ouml;scht werden!</div>");
    }
    else {
      $this->app->DB->Select("DELETE FROM lager WHERE id='$id' LIMIT 1");
      $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Das Lager wurde gel&ouml;scht!</div>");
    }

    $ref = $_SERVER['HTTP_REFERER'];
    header("Location: $ref&msg=$msg");
    exit;
  }


  function LagerArtikelEntfernenReserviert() {
    $reservierung = $this->app->Secure->GetGET("reservierung");
    if (is_numeric($reservierung)) $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE id='$reservierung'");
    header("Location: index.php?module=lager&action=reservierungen");
    exit;
  }

  function LagerArtikelEntfernen() {
    $artikel = $this->app->Secure->GetGET("artikel");
    $projekt = $this->app->Secure->GetGET("projekt");
    $cmd = $this->app->Secure->GetGET("cmd");
    $produktion = $this->app->Secure->GetGET("produktion");
    if($cmd=="produktion"){
      if (is_numeric($artikel)) $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE artikel='$artikel' AND objekt='produktion' AND parameter='$produktion'");
    } else {
      // alle reservierungen loeschen da diese zu mehreren auftraegen gehoeren
      if (is_numeric($artikel)) $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE artikel='$artikel'");
    }
    //    header("Location: index.php?module=lager&action=artikelfuerlieferungen");
    header("Location: index.php?module=lager&action=auslagernproduktion&id=$produktion&cmd=$cmd");
    exit;
  }
  function LagerBuchen() {
    //$this->LagerBuchenMenu();
    //$this->app->Tpl->Set(TABTEXT,"&Uuml;bersicht");
    //$this->app->Tpl->Parse(PAGE,"tabview.tpl");
    $this->LagerBuchenZwischenlager();
  }
  function LagerKalkMenu() {
    $id = $this->app->Secure->GetGET("id");
    //    $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Lagerkalkulation");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Bestellvorschlag");

    $this->app->erp->MenuEintrag("index.php?module=lager&action=ausgehend","Artikel&uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=bestellvorschlag&action=list","Bestellvorschlag");
    //    $this->app->Tpl->Add(TABS,"<li><h2>Lagerkalkulation</h2></li>");
    //    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=lager&action=ausgehend\">Ausgehende Artikel</a></li>");
    //    $this->app->Tpl->Add(TABS,"<li><a  href=\"index.php?module=lager&action=buchen\">Zur&uuml;ck zur &Uuml;bersicht</a></li>");

  }
  function LagerAusgehend() {
    $cmd = $this->app->Secure->GetGET("cmd");
    $produktionid = $this->app->Secure->GetGET("produktionid");
    if ($cmd == "produktion") {
      $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Produktion");
      //      $this->app->erp->MenuEintrag("index.php?module=produktion&action=create", "Neue Produktion anlegen");
      //     $this->app->erp->MenuEintrag("index.php?module=produktion&action=berechnen", "Materialbestand berechnen");
      //    $this->app->erp->MenuEintrag("index.php?module=lager&action=ausgehend&cmd=produktion","Bauteile fehlende");

      if($produktionid>0)
        $this->app->erp->MenuEintrag("index.php?module=produktion&action=berechnen", "Zur&uuml;ck zur &Uuml;bersicht");
      else
        $this->app->erp->MenuEintrag("index.php?module=produktion&action=list", "Zur&uuml;ck zur &Uuml;bersicht");

      $this->app->erp->LagerAusgehend(TAB1, true, true);
      //      $this->app->erp->LagerAusgehend(TAB2, false, true);
    } else {
      $this->LagerKalkMenu();
      //     $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Ausgehende Artikel");
      $this->app->erp->LagerAusgehend(TAB1, true);
      //     $this->app->erp->LagerAusgehend(TAB2, false);

    }

    $this->app->Tpl->Set(TABTEXT, "Fehlende Artikel");
    $this->app->Tpl->Set(TABTEXT2, "Nachbestellte Artikel");
    $this->app->Tpl->Parse(TAB1, "rahmen70.tpl");

    $this->app->Tpl->Parse(PAGE, "lagerausgehend.tpl");
  }

  function LagerBuchenZwischenlager() {
    $this->LagerBuchenMenu();
    //$this->app->Tpl->Set(TABTEXT, "Zwischenlager");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Zwischenlager");
    $this->app->Tpl->Set(SUBSUBHEADING, "EINGANG Zwischenlager Stand " . date('d.m.Y'));
    // easy table mit arbeitspaketen YUI als template
    if($this->app->User->GetType()=="admin")
      $delete = "<a href=\"#\" onclick=\"if(!confirm('Artikelwirklich aus dem Zwischenlager nehmen?')) return false; else window.location.href='index.php?module=lager&action=buchenzwischenlagerdelete&id=%value%';\"><img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\"></a>";

    $table = new EasyTable($this->app);
    $table->Query("SELECT a.name_de as artikel,a.nummer as nummer,z.menge,z.vpe,z.grund, p.abkuerzung as projekt, z.id FROM zwischenlager z LEFT JOIN artikel a ON a.id=z.artikel LEFT JOIN projekt p ON 
        p.id=z.projekt WHERE z.firma='{$this->app->User->GetFirma() }' AND z.richtung='eingang'");
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=lager&action=bucheneinlagern&cmd=zwischenlager&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/forward.png\"></a>$delete");
    $this->app->Tpl->Parse(TAB1, "rahmen70.tpl");
    $this->app->Tpl->Set(INHALT, "");
    $this->app->Tpl->Set(SUBSUBHEADING, "AUSGANG Zwischenlager Stand " . date('d.m.Y'));
    // easy table mit arbeitspaketen YUI als template
    $table = new EasyTable($this->app);
    $table->Query("SELECT a.name_de as artikel,z.menge,z.vpe,z.grund, p.abkuerzung as projekt, z.id FROM zwischenlager z LEFT JOIN artikel a ON a.id=z.artikel LEFT JOIN projekt p ON 
        p.id=z.projekt WHERE z.firma='{$this->app->User->GetFirma() }' AND z.richtung='ausgang'");
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=lager&action=bucheneinlagern&cmd=zwischenlager&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/forward.png\"></a>$delete");
    $this->app->Tpl->Parse(TAB1, "rahmen70.tpl");
    $this->app->Tpl->Set(AKTIV_TAB1, "selected");
    $this->app->Tpl->Parse(PAGE, "tabview.tpl");
  }

  function LagerBuchenEinlagern() {
    session_start();
    $this->LagerBuchenMenu();
    $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Einlagern");
    $id = $this->app->Secure->GetGET("id");
    $cmd = $this->app->Secure->GetGET("cmd"); // vom zwischen lager!
    $menge = $this->app->Secure->GetPOST("menge");
    $submit = $this->app->Secure->GetPOST("submit");

    $grund = $this->app->Secure->GetPOST("grund");
    $artikelid = $this->app->Secure->GetGET("artikelid");

    $this->app->YUI->AutoComplete('projekt','projektname');
    $this->app->YUI->AutoComplete('nummer','lagerartikelnummer');
    $this->app->YUI->AutoComplete('regal','lagerplatz');
    $this->app->YUI->AutoComplete('grundreferenz','lagergrund');

    if($cmd=="zwischenlager")
    {
      $this->app->Tpl->Set(MENGEREADONLY,"readonly");
      $this->app->Tpl->Set(WOHERREADONLYSTART,"<!--");
      $this->app->Tpl->Set(WOHERREADONLYENDE,"-->");

      $mhd = $this->app->DB->SelectArr("SELECT * FROM lager_mindesthaltbarkeitsdatum WHERE zwischenlagerid='$id'");
      for($i=1;$i<=count($mhd);$i++)
      {
        $this->app->Tpl->Add(SRNINFO,"<tr><td></td><td>MHD: ".$mhd[$i-1][mhddatum]."</td></tr>");
      }
      $charge = $this->app->DB->SelectArr("SELECT * FROM lager_charge WHERE zwischenlagerid='$id'");
      for($i=1;$i<=count($charge);$i++)
      {
        $this->app->Tpl->Add(SRNINFO,"<tr><td></td><td>Charge: ".$charge[$i-1][charge]."</td></tr>");
      }
      $srn = $this->app->DB->SelectArr("SELECT * FROM lager_seriennummern WHERE zwischenlagerid='$id'");
      for($i=1;$i<=count($srn);$i++)
      {
        $this->app->Tpl->Add(SRNINFO,"<tr><td></td><td>Seriennummer: ".$srn[$i-1][seriennummer]."</td></tr>");
      }
    } else {
      $this->app->Tpl->Set(WOHERREADONLYSTART2,"<!--");
      $this->app->Tpl->Set(WOHERREADONLYENDE2,"-->");
    }

    // wenn projekt angeben
    if ($this->app->Secure->GetPOST("projekt") != "") 
    {
      $projekt = $this->app->Secure->GetPOST("projekt");
      $projekt = explode(' ', $projekt);
      $projekt = $projekt[0];
      if(!is_numeric($projekt))
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");

      $_SESSION[projekt] = $projekt;
    }

    $projekt = $_SESSION[projekt];
    $regal = $this->app->Secure->GetPOST("regal");

    if($regal!=""){
      $regal_id = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$regal' LIMIT 1");
    }


    if(is_numeric($regal_id))
      $regal = $regal_id;

    $nummer = $this->app->Secure->GetPOST("nummer");
    $nummer = explode(' ', $nummer);
    $nummer = $nummer[0];

    if ($nummer == "" && $cmd != "zwischenlager" && $artikelid=="") {
      $this->app->Tpl->Set(MSGARTIKEL, "<br>Jetzt Artikel abscannen!");
      $this->app->Tpl->Set(ARTIKELSTYLE, "style=\"border: 2px solid red;width:200px;\"");
    }

    $woher = $this->app->Secure->GetPOST("woher");
    $zwischenlagerid = $this->app->Secure->GetPOST("zwischenlager");
    $menge = $this->app->Secure->GetPOST("menge");
    $grundreferenz = $this->app->Secure->GetPOST("grundreferenz");
    // hier nur rein wenn artikel lager und projekt sinn machen sonst 	
    //message ausgeben und artikel wirklich aus zwischenlager
    $alles_komplett = 0;
    if ($woher == "Zwischenlager" && $zwischenlagerid <= 0) {
      $grund.= "<li>Artikel kommt nicht aus Zwischenlager!</li>";
      $alles_komplett++;
    }



    $artikel_tmp = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND geloescht!=1 LIMIT 1");
    $ean = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='$nummer' AND ean!='' AND geloescht!=1 LIMIT 1");
    if($artikel_tmp <=0 && $ean > 0) 
    { 
      $artikel_tmp = $ean;
      $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$ean' LIMIT 1");
    }
    $artikelcheck = $this->app->DB->Select("SELECT id FROM artikel WHERE id='$artikel_tmp' LIMIT 1");

    $artikel_quickcheck = 0;
    if ($submit !="" && ($artikelcheck != $artikel_tmp || $artikel_tmp == "" || $artikel_tmp == 0)) {
      $grund.= "<li>Artikel-Nummer gibt es nicht!</li>";
      $alles_komplett++;
      $artikel_quickcheck = 1;
    }

    // gibts regal
    $regalcheck = $this->app->DB->Select("SELECT id FROM lager_platz WHERE id='$regal' LIMIT 1");
    if ($regalcheck != $regal || $regal == "" || $regal == 0) {
      $grund.= "<li>Regal gibt es nicht!</li>";
      $alles_komplett++;
    }

    if ($alles_komplett > 0 && $regal != "") {
      $this->app->Tpl->Set(MESSAGELAGER, "<div class=\"error\">Artikel wurde nicht gebucht! Grund:<ul>$grund</ul> </div>");
    } else {
      if ($artikel_quickcheck == 1 && $nummer != "") {
        $this->app->Tpl->Set(MESSAGELAGER, "<div class=\"error\">Achtung! Artikelnummer  
            gibt es nicht! </div>");
        $nummer =""; 
      }
    }
    if ($nummer == "" && $cmd == "" && $woher == "") $_SESSION[woher] = 'Manuelle Lageranpassung';

    $chargenverwaltung= $this->app->DB->Select("SELECT chargenverwaltung FROM artikel WHERE id='$artikel_tmp' LIMIT 1");
    $mindesthaltbarkeitsdatum = $this->app->DB->Select("SELECT mindesthaltbarkeitsdatum FROM artikel WHERE id='$artikel_tmp' LIMIT 1");
    $seriennummern = $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id='$artikel_tmp' LIMIT 1");

    // pruefen einlagern

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
    if($seriennummern !="keine" && $seriennummern !="vomprodukt" && $seriennummern !="eigene" && $seriennummern!="")
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
      $alles_komplett++;
      //$this->app->Tpl->Add(MESSAGE,"<div class=\"error\">Achtung! Bitte alle Pflichfelder (Regal, MHD, Charge, Seriennummer) ausf&uuml;llen!</div>");
    }

    if ($alles_komplett == 0 && $regal != "") {
      $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND geloescht!=1 LIMIT 1");
      // pruefe ob es einen ek fuers projekt gibt sonst meckern!!!
      //echo "buchen entweder aus zwischenlager, prpoduktion oder so";
      if ($woher == "Zwischenlager") {
        $this->app->erp->LagerEinlagerVomZwischenlager($zwischenlagerid, $menge, $regal, $projekt,$grundreferenz);
        header("Location: index.php?module=lager&action=buchenzwischenlager");
        exit;
      }
      if ($woher == "Manuelle Lageranpassung"){
        $_SESSION[projekt] = $projekt;
        $this->app->erp->LagerEinlagernDifferenz($artikel, $menge, $regal, $projekt,$grundreferenz);

        // Mindesthaltbarkeitsdatum buchen
        $chargemindest = $this->app->Secure->GetPOST("charge");
        $mhd = $this->app->String->Convert($this->app->Secure->GetPOST("mhd"),"%1.%2.%3","%3-%2-%1");
        $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($artikel,$menge,$regal,$mhd,$chargemindest,"");

        if($chargenverwaltung > 0)
        {  
          $datum = date('Y-m-d');
          $this->app->erp->AddChargeLagerOhneBewegung($artikel,$menge,$regal,$datum,$chargemindest,"");
        }

        //Seriennummern buchen
        $tmpcheck = $this->app->Secure->GetPOST("seriennummern");

        for($checkser=0;$checkser < $menge; $checkser++)
        {
          if($tmpcheck[$checkser]!="")
            $this->app->erp->AddSeriennummerLager($artikel,$regal,$tmpcheck[$checkser],"","",$mhd,$chargemindest);
        }



        if($artikelid!="")
          header("Location: index.php?module=artikel&action=lager&id=$artikelid");
        else
          header("Location: index.php?module=lager&action=bucheneinlagern");
        exit;
      }
      // wenn von zwischenlager dann header location nach zwischenlager
      // sonst einlagern
    }

    // kommt direkt vom zwischenlager
    if ($cmd == "zwischenlager") {
      $_SESSION[woher] = "Zwischenlager";
      $projekt = $this->app->DB->Select("SELECT projekt FROM zwischenlager WHERE id='$id' LIMIT 1");
      $menge = $this->app->DB->Select("SELECT menge FROM zwischenlager WHERE id='$id' LIMIT 1");
      $artikel = $this->app->DB->Select("SELECT artikel FROM zwischenlager WHERE id='$id' LIMIT 1");
      $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1");
      $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");
      $lagerplatz = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$artikel' LIMIT 1");
      $lagerbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$lagerplatz' LIMIT 1");
      $vpe = $this->app->DB->Select("SELECT vpe FROM zwischenlager WHERE id='$id' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT projekt FROM zwischenlager WHERE id='$id' LIMIT 1");
      if ($projekt == "" || $projekt == 0) $projekt = 1; // default projekt
      $standardbild = $this->app->DB->Select("SELECT standardbild FROM artikel WHERE id='$artikel' LIMIT 1");
      if ($standardbild == "") $standardbild = $this->app->DB->Select("SELECT datei FROM datei_stichwoerter WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='$artikel' LIMIT 1");
      $this->app->Tpl->Add(ZWISCHENLAGERINFO, "<tr valign=\"top\"><td>Bezeichnung:</td><td>$name_de</td></tr>");
      if ($standardbild > 0) $this->app->Tpl->Add(ZWISCHENLAGERINFO, "<tr valign=\"top\"><td>Bild:</td><td align=\"center\"><img src=\"index.php?module=dateien&action=send&id=$standardbild\" width=\"110\"></td></tr>");

      if($lagerbezeichnung!="")
      {
        $this->app->Tpl->Add(ZWISCHENLAGERINFO, "<tr valign=\"top\"><td></td><td><br></td></tr><tr ><td>Regalvorschlag:</td><td><font size=\"5\"><b onclick=\"document.getElementById('regal').value='$lagerbezeichnung'\";>$lagerbezeichnung</b></font></td></tr>");
      } else {
        $lagermeist = $this->app->DB->SelectArr("SELECT lager_platz, SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel' GROUP BY lager_platz ORDER by 1 DESC LIMIT 1");
        $lagerplatz = $lagermeist[0]['lager_platz'];
        $lagerbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='{$lagermeist[0]['lager_platz']}' LIMIT 1");
        //$lagerplatz = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$artikel' LIMIT 1");
        //$lagerbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$lagerplatz' LIMIT 1");

        if ($lagerplatz == "" || $lagerplatz == 0) $lagerbezeichnung = "Regal frei w&auml;hlen";

        $this->app->Tpl->Add(ZWISCHENLAGERINFO, "<tr valign=\"top\"><td></td><td><br></td></tr><tr ><td>Regalvorschlag:</td><td><font size=\"5\"><b onclick=\"document.getElementById('regal').value='$lagerbezeichnung'\";>$lagerbezeichnung</b></font></td></tr>");

      }
      $this->app->Tpl->Add(ZWISCHENLAGERINFO, "<tr valign=\"top\"><td><br><br><b>Regal:</b></td><td><br><br><input type=\"text\" name=\"regal\" id=\"regal\" style=\"border: 2px solid;width:200px;\"><br>Jetzt Regal abscannen!</td></tr>
          <input type=\"hidden\" name=\"zwischenlager\" value=\"$id\">");
      $this->app->Tpl->Add(ZWISCHENLAGERINFO, '<script type="text/javascript">
          document.getElementById("regal").focus();
          </script>');



    } else {

      if (($menge == "" || $menge == 0) && $cmd!="umlagern") $menge = 1;

      if ($this->app->Secure->GetPOST("woher") != "") {
        $_SESSION[woher] = $this->app->Secure->GetPOST("woher");
      }

      if ($this->app->Secure->GetPOST("nummer") != "" || $artikelid > 0) {
        $nummer = $this->app->Secure->GetPOST("nummer");
        $nummer = explode(' ', $nummer);
        $nummer = $nummer[0];

        if($artikelid > 0){
          $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikelid' LIMIT 1");
          $this->app->Tpl->Set(NUMMER, $nummer);
        }

        $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND geloescht!=1 LIMIT 1");

        $ean = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='$nummer' AND ean!='' AND geloescht!=1 LIMIT 1");
        if($artikel <=0 && $ean > 0) 
        { 
          $artikel = $ean;
          $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$ean' LIMIT 1");
        }

        if($artikel > 0)
        {

          $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");
          $lagermeist = $this->app->DB->SelectArr("SELECT lager_platz, SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel' GROUP BY lager_platz ORDER by 1 DESC LIMIT 1");
          $lagermeist = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='{$lagermeist[0]['lager_platz']}' LIMIT 1");
          $lagerplatz = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$artikel' LIMIT 1");
          $lagerbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$lagerplatz' LIMIT 1");
          if ($lagerplatz == "" || $lagerplatz == 0) $lagerbezeichnung = "Regal frei w&auml;hlen";
          //$vpe  = $this->app->DB->Select("SELECT vpe FROM artikel WHERE id='$id' LIMIT 1");
          $vpe = 'einzeln';
          //$projekt = $this->app->DB->Select("SELECT projekt FROM zwischenlager WHERE id='$id' LIMIT 1");
          if ($projekt == "" || $projekt == 0) $projekt = 1; // default projekt


          if($chargenverwaltung !="2")
          {
            $this->app->Tpl->Set(SHOWCHRSTART,"<!--");
            $this->app->Tpl->Set(SHOWCHREND,"-->");
          } else {
            //        $this->app->YUI->DatePicker("mhd");
            $this->app->Tpl->Set(CHARGEVALUE,$this->app->Secure->GetPOST("charge"));
          }


          if($mindesthaltbarkeitsdatum !="1")
          {
            $this->app->Tpl->Set(SHOWMHDSTART,"<!--");
            $this->app->Tpl->Set(SHOWMHDEND,"-->");
          } else {
            $this->app->YUI->DatePicker("mhd");
            $this->app->Tpl->Set(MHDVALUE,$this->app->Secure->GetPOST("mhd"));
          }


          if($seriennummern == "keine" || $seriennummern =="vomprodukt" || $seriennummern =="eigene" || $menge <= 0 ||  $seriennummern=="")
          {
            $this->app->Tpl->Set(SHOWSRNSTART,"<!--");
            $this->app->Tpl->Set(SHOWSRNEND,"-->");
          } else {
            // Generator felder fuer seriennummern
            $this->app->Tpl->Add(SERIENNUMMERN,"<table><tr><td>Nr.</td><td>Seriennummer</td></tr>");
            $tmp = $this->app->Secure->GetPOST("seriennummern");
            for($ij=1;$ij<=$menge;$ij++)
            {
              $value = $tmp[$ij-1];
              $this->app->Tpl->Add(SERIENNUMMERN,"<tr><td>$ij</td><td><input type=\"text\" name=\"seriennummern[]\" size=\"30\" value=\"$value\"></td></tr>");
            }
            $this->app->Tpl->Add(SERIENNUMMERN,"</table>");
          }

          $standardbild = $this->app->DB->Select("SELECT standardbild FROM artikel WHERE id='$artikel' LIMIT 1");

          if ($standardbild == "") $standardbild = $this->app->DB->Select("SELECT datei FROM datei_stichwoerter WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='$artikel' LIMIT 1");

          if ($standardbild > 0)
            $this->app->Tpl->Set('STANDARDBILD', "<tr valign=\"top\"><td>Bild:</td><td align=\"center\"><img src=\"index.php?module=dateien&action=send&id=$standardbild\" width=\"110\"></td></tr>");

          $this->app->Tpl->Set('NAMEDE',$name_de);
          if($lagermeist!="" || $lagermeist!=0){
            $this->app->Tpl->Set('LAGERMEIST',"<b onclick=\"document.getElementById('regal').value='$lagermeist'\";>$lagermeist</b> (aktuell am meisten im Lager)");
            if($lagerbezeichnung!="" && $lagerbezeichnung!="Regal frei w&auml;hlen")
              $this->app->Tpl->Add('LAGERMEIST',"<br><b onclick=\"document.getElementById('regal').value='$lagerbezeichnung'\";>$lagerbezeichnung</b> (Standardlager)");
          } else {
            $this->app->Tpl->Set('LAGERBEZEICHNUNG',"<b onclick=\"document.getElementById('regal').value='$lagerbezeichnung'\";>$lagerbezeichnung</b>");

          }

          $this->app->Tpl->Set(REGALVALUE,$this->app->Secure->GetPOST("regal"));

          $this->app->Tpl->Parse('ZWISCHENLAGERINFO', 'lager_regal.tpl');
        } else {

          //falsche artikelnummer	
          $nummer = "";
          $this->app->Tpl->Set(MSGARTIKEL, "<br>Jetzt Artikel abscannen!");
          $this->app->Tpl->Set(ARTIKELSTYLE, "style=\"border: 2px solid red\"");
          $this->app->Tpl->Set('ZWISCHENLAGERINFO', '<script type="text/javascript">document.getElementById("nummer").focus();</script>');

        }

      } else {
        $this->app->Tpl->Set('ZWISCHENLAGERINFO', '<script type="text/javascript">document.getElementById("nummer").focus();</script>');
        if($artikel <=0)
        {
          $this->app->Tpl->Set(SHOWCHRSTART,"<!--");
          $this->app->Tpl->Set(SHOWCHREND,"-->");
          $this->app->Tpl->Set(SHOWMHDSTART,"<!--");
          $this->app->Tpl->Set(SHOWMHDEND,"-->");
          $this->app->Tpl->Set(SHOWSRNSTART,"<!--");
          $this->app->Tpl->Set(SHOWSRNEND,"-->");
        }


      }
    }
    $this->app->Tpl->Set(NAME, $name_de);
    if ($_SESSION[woher] == "") $_SESSION[woher] = "Manuelle Lageranpassung";
    if ($_SESSION[woher] == "Zwischenlager") $this->app->Tpl->Set(ZWISCHENLAGER, "selected");
    if ($_SESSION[woher] == "Produktion") $this->app->Tpl->Set(PRODUKTION, "selected");
    if ($_SESSION[woher] == "Manuelle Lageranpassung") $this->app->Tpl->Set(DIFFERENZ, "selected");
    if ($_SESSION[woher] == "Umlagern") $this->app->Tpl->Set(UMLAGERN, "selected");
    $projekt = $_SESSION[projekt];

    if($cmd=="umlagern" && $this->app->Secure->GetPOST("menge")=="")
      $menge = $this->app->Secure->GetGET("menge");

    if($cmd=="umlagern" && $this->app->Secure->GetPOST("grund")=="")
      $grundreferenz = $this->app->erp->base64_url_decode($this->app->Secure->GetGET("grund"));

    $this->app->Tpl->Set(MENGE, $menge);
    $this->app->Tpl->Set(GRUNDREFERENZ, $grundreferenz);
    $this->app->Tpl->Set(NUMMER, $nummer);

    $this->app->Tpl->Set(VPE, $vpe);
    $pr_name = $this->app->DB->Select("SELECT CONCAT(abkuerzung) FROM projekt WHERE id='$projekt' LIMIT 1");
    $this->app->Tpl->Set(PROJEKT, $pr_name);//$this->app->erp->GetProjektSelect($projekt, &$color_selected));
    //$this->app->Tpl->Set(TABTEXT, "Einlagern");

    $this->app->Tpl->Parse(TAB1, "einlagern.tpl");
    $this->app->Tpl->Parse(PAGE, "tabview.tpl");
  }

  function LagerBuchenAuslagern() {
    $this->LagerBuchenMenu();
    $cmd = $this->app->Secure->GetGET("cmd");
    $action = $this->app->Secure->GetGET("action");


    $this->app->Tpl->Set(CMD,$cmd);
    $this->app->Tpl->Set(ACTION,$action);


    if($this->app->erp->Version()=="stock")
    {
      $this->app->Tpl->Set(STARTDISABLESTOCK, "<!--");
      $this->app->Tpl->Set(ENDEDISABLESTOCK, "-->");
    }


    session_start();
    if($cmd=="umlagern") {
      //$this->app->Tpl->Set(TABTEXT, "Auslagern");

      $this->app->Tpl->Set(STARTNICHTUMLAGERN, "<!--");
      $this->app->Tpl->Set(ENDENICHTUMLAGERN, "-->");

    }
    else {
      //$this->app->Tpl->Set(TABTEXT, "Auslagern");
      $this->app->Tpl->Set(STARTUMLAGERN, "<!--");
      $this->app->Tpl->Set(ENDEUMLAGERN, "-->");
    }

    $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Auslagern");
    // checken ob die daten passen
    $nummer = $this->app->Secure->GetPOST("nummer");
    $grund = $this->app->Secure->GetPOST("grund");
    $grundreferenz = $this->app->Secure->GetPOST("grundreferenz");
    $adresse = $this->app->Secure->GetPOST("adresse");
    $projekt = $this->app->Secure->GetPOST("projekt");
    $menge = $this->app->Secure->GetPOST("menge");
    $submit = $this->app->Secure->GetPOST("submit");
    $artikelid = $this->app->Secure->GetGET("artikelid");
    $regal = $this->app->Secure->GetPOST("regal");
    $regalneu = $this->app->Secure->GetPOST("regalneu");
    if ($menge == "" || $menge == "0") $menge = 1;
    //session_close();


    if($projekt!="")
      $_SESSION[projekt] = $projekt;

    $projekt= $_SESSION[projekt];

    //	$nummer = explode(' ', $nummer);
    //		$nummer = $nummer[0];

    if ($this->app->Secure->GetPOST("nummer") != "" || $artikelid > 0) {
      $nummer = $this->app->Secure->GetPOST("nummer");
      $nummer = explode(' ', $nummer);
      $nummer = $nummer[0];

      if($artikelid > 0){
        $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikelid' LIMIT 1");
        $checkartikel = $this->app->DB->Select("SELECT id FROM artikel WHERE id='$artikelid' LIMIT 1");
        $artikel = $artikelid;
        $submit="1";
        $this->app->Tpl->Set(NUMMER, $nummer);
      }
    }

    $projekt = explode(' ', $projekt);
    $projekt = $projekt[0];

    $regal_id = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$regal' AND kurzbezeichnung!='' LIMIT 1");
    if(is_numeric($regal_id))
      $regal = $regal_id;

    $regalneu_id = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$regalneu' AND kurzbezeichnung!='' LIMIT 1");
    if(is_numeric($regalneu_id))
      $regalneu = $regalneu_id;


    if ($submit != "") {
      //projekt pruefen

      $checkprojekt = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
      $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
      if ($projekt == "" || $checkprojekt != $projekt) {
        //$error++;
        //$this->app->Tpl->Set(MSGPROJEKT,"<font color=\"red\">Projekt gibt es nicht!</font>");
        $projektid = $this->app->DB->Select("SELECT standardprojekt  FROM firma WHERE id='" . $this->app->User->GetFirma() . "' LIMIT 1");
      }

      //adresse pruefen
      $adressearray = split(' ', $adresse);
      $checkadresse = $this->app->DB->Select("SELECT id FROM adresse WHERE id='{$adressearray[0]}' LIMIT 1");
      $checkname = $this->app->DB->Select("SELECT name FROM adresse WHERE id='{$adressearray[0]}' LIMIT 1");

      /*
         if (!is_numeric($adressearray[0]) || $adressearray[0] != $checkadresse) {
         $error++;
         $this->app->Tpl->Set(MESSAGE, "<div class=\"error\">Bitte eine g&uuml;ltige Adresse angeben!</div>");
         }
       */

      if (!is_numeric($menge) || $menge == 0) {
        $error++;
        $this->app->Tpl->Set(MSGMENGE, "<font color=\"red\">Wert ist keine Zahl oder Null.</font>");
      }
      $ean = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='$nummer' AND ean!='' AND geloescht!=1 LIMIT 1");
      $artikel_tmp = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND geloescht!=1 LIMIT 1");
      if($artikel_tmp <=0 && $ean > 0)
      {
        $artikel_tmp = $ean;
        $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$ean' LIMIT 1");
      }

      $checkartikel = $this->app->DB->Select("SELECT nummer FROM artikel WHERE nummer='{$nummer}' AND geloescht!=1 LIMIT 1");
      $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='{$nummer}' AND geloescht!=1 LIMIT 1");
      $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE nummer='{$nummer}' AND geloescht!=1 LIMIT 1");
      $seriennummer = $this->app->DB->Select("SELECT seriennummer FROM artikel WHERE nummer='{$nummer}' AND geloescht!=1 LIMIT 1");

      if ($nummer != $checkartikel && ($nummer!=""||$nummer!=0)) {
        $error++;
        $this->app->Tpl->Set(MESSAGE, "<div class=\"error\">Diese Artikelnummer gibt es nicht!</div>");
        $nummer = "";

      }
      //z.B. es liegen 1 1 5 und man will 6 haben
      $checkregal = $this->app->DB->Select("SELECT id FROM lager_platz WHERE id='$regal' LIMIT 1");
      $checkregalneu = $this->app->DB->Select("SELECT id FROM lager_platz WHERE id='$regalneu' LIMIT 1");
      if (($regal != "" && $checkregal == $regal) && $error == 0) {
        //regal gibt schon mal liegt jetzt der artikel noch in diesem regal?
        $summe = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE lager_platz='$regal' AND artikel='$artikel'");
        if ($summe <= 0) {
          $this->app->Tpl->Set(MESSAGELAGER, "<div class=\"error\">Artikel gibt es in diesem Regal nicht!</div>");
        } else if ($summe < $menge) {
          $this->app->Tpl->Set(MESSAGELAGER, "<div class=\"error\">Zu wenig Artikel im Regal! Bitte kleinere Menge w&auml;hlen! (Summe: $summe)</div>");
        } else {
          // zeige alle in dem Lager an sortiert nach MHD
          $tmpsrn = $this->app->DB->SelectArr("SELECT * FROM lager_seriennummern WHERE 
              lager_platz='$regal' AND artikel='$artikel' ORDER by mhddatum");

          $tmpmhd = $this->app->DB->SelectArr("SELECT * FROM lager_mindesthaltbarkeitsdatum WHERE 
              lager_platz='$regal' AND artikel='$artikel' ORDER by mhddatum");

          $tmpcharge = $this->app->DB->SelectArr("SELECT * FROM lager_charge WHERE 
              lager_platz='$regal' AND artikel='$artikel' ORDER by id");

          if(count($tmpsrn) > 0)  { 
            $this->app->Tpl->Add(SRNINFO,"<tr><td></td><td>MHD</td><td>Seriennummer</td><td>Charge</td></tr>");
          } else if (count($tmpmhd) > 0) {
            $this->app->Tpl->Add(SRNINFO,"<tr><td></td><td>Mindesthalt.</td><td width=30></td><td>Charge</td></tr>");
          } else if (count($tmpcharge) > 0) {
            $this->app->Tpl->Add(SRNINFO,"<tr><td></td><td>Charge</td></tr>");
          }

          $check_seriennummer = $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id='$artikel' LIMIT 1");
          $check_charge = $this->app->DB->Select("SELECT chargenverwaltung FROM artikel WHERE id='$artikel' LIMIT 1");
          $check_mhd = $this->app->DB->Select("SELECT mindesthaltbarkeitsdatum FROM artikel WHERE id='$artikel' LIMIT 1");
          $regaltreffer="1";

          if($check_seriennummer=="vomprodukteinlagern")
          {
            for($y=0;$y<count($tmpsrn);$y++)
            {
              $regaltreffer="1";
              if($y < $menge) $checked="checked"; else $checked="";

              if($tmpsrn[$y][mhddatum]=="") $tmpsrn[$y][mhddatum] = " - "; else $tmpsrn[$y][mhddatum] = $this->app->String->Convert($tmpsrn[$y][mhddatum],"%1-%2-%3","%3.%2.%1");
              if($tmpsrn[$y][seriennummer]=="") $tmpsrn[$y][seriennummer] = " - ";
              if($tmpsrn[$y][charge]=="") $tmpsrn[$y][charge] = " - ";

              $this->app->Tpl->Add(SRNINFO,"<tr>
                  <td><input type=\"checkbox\" onclick=\"countChecks(this)\" name=\"lager_srn_id[]\" value=\"".$tmpsrn[$y][id]."\" $checked>&nbsp;$out</td>
                  <td>".$tmpsrn[$y][mhddatum]."</td>
                  <td>".$tmpsrn[$y][seriennummer]."</td>
                  <td>".$tmpsrn[$y][charge]."</td></tr>");
            }
          } else if ($check_mhd=="1")
          {
            for($y=0;$y<count($tmpmhd);$y++)
            {
              $regaltreffer="1";
              if($y < $menge) $checked="checked"; else $checked="";

              if($tmpmhd[$y][mhddatum]=="") $tmpmhd[$y][mhddatum] = " - "; else $tmpmhd[$y][mhddatum] = $this->app->String->Convert($tmpmhd[$y][mhddatum],"%1-%2-%3","%3.%2.%1");
              if($tmpmhd[$y][charge]=="") $tmpmhd[$y][charge] = " - ";

              $this->app->Tpl->Add(SRNINFO,"<tr>
                  <td><input type=\"checkbox\" onclick=\"countChecks(this)\" name=\"lager_mhd_id[]\" value=\"".$tmpmhd[$y][id]."\" $checked>&nbsp;$out</td>
                  <td>".$tmpmhd[$y][mhddatum]."</td><td></td>
                  <td>".$tmpmhd[$y][charge]."</td></tr>");
            }


          } else if ($check_charge=="2")
          {
            for($y=0;$y<count($tmpcharge);$y++)
            {
              $regaltreffer="1";
              if($y < $menge) $checked="checked"; else $checked="";

              if($tmpcharge[$y][charge]=="") $tmpcharge[$y][charge] = " - ";

              $this->app->Tpl->Add(SRNINFO,"<tr>
                  <td><input type=\"checkbox\" onclick=\"countChecks(this)\" name=\"lager_charge_id[]\" value=\"".$tmpcharge[$y][id]."\" $checked>&nbsp;$out</td>
                  <td>".$tmpmhd[$y][charge]."</td></tr>");
            }
          }

          //$regaltreffer="1";

          $this->app->Tpl->Add(ZWISCHENLAGERINFO,"<input type=\"hidden\" name=\"abschluss_auslagern\" value=\"1\">");

          $allow = 0;

          if($check_seriennummer!="keine" || $check_charge=="2" || $check_mhd=="1")
          {
            if($this->app->Secure->GetPOST("abschluss_auslagern")=="1")
              $allow=1;
          } else $allow=1;

          if($cmd=="umlagern" && $regal  > 0 && $checkregalneu!=$regalneu) { $allow=0; }
          if($cmd=="umlagern" && $regalneu =="" ) { $allow=0; }


          if($allow){
            $lager_srn_id = $this->app->Secure->GetPOST("lager_srn_id");
            //Seriennummern umbuchen auf den Menschen
            for($q=0;$q<count($lager_srn_id);$q++){
              $seriennummer = $this->app->DB->Select("SELECT seriennummer FROM lager_seriennummern WHERE  id='".$lager_srn_id[$q]."' LIMIT 1");
              if($seriennummer!="")
                $this->app->DB->Insert("INSERT INTO seriennummern (id,seriennummer,artikel,adresse,bearbeiter,lieferung,logdatei) VALUES ('','$seriennummer','$artikel','$checkadresse','".$this->app->User->GetName()."',NOW(),NOW())");
              $this->app->DB->Delete("DELETE FROM lager_seriennummern WHERE id='".$lager_srn_id[$q]."' LIMIT 1");
              // umlagern3
              if($cmd=="umlagern")
                $this->app->erp->AddSeriennummerLager($artikel,$regalneu,$seriennummer);
            }

            $lager_mhd_id = $this->app->Secure->GetPOST("lager_mhd_id");
            for($q=0;$q<count($lager_mhd_id);$q++){
              $passende_charge = $this->app->DB->Select("SELECT charge FROM lager_mindesthaltbarkeitsdatum WHERE id='".$lager_mhd_id[$q]."' LIMIT 1");
              $passende_mhd = $this->app->DB->Select("SELECT mhddatum FROM lager_mindesthaltbarkeitsdatum WHERE id='".$lager_mhd_id[$q]."' LIMIT 1");
              $passende_lager_platz = $this->app->DB->Select("SELECT lager_platz FROM lager_mindesthaltbarkeitsdatum WHERE id='".$lager_mhd_id[$q]."' LIMIT 1");
              $passende_artikel = $this->app->DB->Select("SELECT artikel FROM lager_mindesthaltbarkeitsdatum WHERE id='".$lager_mhd_id[$q]."' LIMIT 1");
              $this->app->DB->Delete("DELETE FROM lager_mindesthaltbarkeitsdatum WHERE id='".$lager_mhd_id[$q]."' LIMIT 1");
              $this->app->DB->Delete("DELETE FROM lager_charge WHERE charge='".$passende_charge."' 
                  AND lager_platz='$passende_lager_platz' AND artikel='$passende_artikel' LIMIT 1");
              // umlagern3
              if($cmd=="umlagern")
                $this->app->erp->AddMindesthaltbarkeitsdatumLagerOhneBewegung($passende_artikel,1,$regalneu,$passende_mhd,$passende_charge);
            }

            $lager_charge_id = $this->app->Secure->GetPOST("lager_charge_id");
            for($q=0;$q<count($lager_charge_id);$q++){
              $passende_artikel = $this->app->DB->Select("SELECT artikel FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
              $passende_datum = $this->app->DB->Select("SELECT datum FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
              $passende_charge = $this->app->DB->Select("SELECT charge FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
              $this->app->DB->Delete("DELETE FROM lager_charge WHERE id='".$lager_charge_id[$q]."' LIMIT 1");
              //umlagern3
              if($cmd=="umlagern")
                $this->app->erp->AddChargeLagerOhneBewegung($passende_artikel,1,$regalneu,$passende_datum,$passende_charge);
            }

            if($seriennummer!="") $tmp_sn = " SN:".$seriennummer; else $tmp_sn = "";

            if($grundreferenz!=""){
              $this->app->DB->Insert("INSERT INTO lager_bewegung (id,lager_platz,artikel,menge,vpe,eingang,zeit,referenz,bearbeiter,projekt,firma,adresse)
                  VALUES('','$regal','$artikel','$menge','einzeln','0',NOW(),'$grund f&uuml; $checkname: $grundreferenz $tmp_sn',
                    '" . $this->app->User->GetName() . "','$projektid','" . $this->app->User->GetFirma() . "','$checkadresse')");
            } else {
              $this->app->DB->Insert("INSERT INTO lager_bewegung (id,lager_platz,artikel,menge,vpe,eingang,zeit,referenz,bearbeiter,projekt,firma,adresse)
                  VALUES('','$regal','$artikel','$menge','einzeln','0',NOW(),'$grund $checkname $tmp_sn',
                    '" . $this->app->User->GetName() . "','$projektid','" . $this->app->User->GetFirma() . "','$checkadresse')");
            }


            // umlagern3 lager_bewegung buchen

            // wenn enticklung auf mitarbeiter buchen
            if ($grund == "Entwicklungsmuster") {
              $this->app->DB->Insert("INSERT INTO projekt_inventar (id,artikel,menge,bestellung, projekt,   
                adresse,	mitarbeiter,   vpe,zeit) VALUES ('','$artikel','$menge','','$projekt','$adresse','" . $this->app->User->GetName() . "', 'einzeln',NOW())");
            }
            //ziehe menge ab von lager_platz_inhalt
            $tmpcheck = $this->app->DB->Select("SELECT id FROM lager_platz_inhalt WHERE lager_platz='$regal' AND artikel='$artikel' AND menge >='$menge' LIMIT 1");
            // wenn es ein lager mit genug gibt nimm dieses
            if ($tmpcheck > 0) {
              $summezumchecken = $this->app->DB->Select("SELECT menge FROM lager_platz_inhalt WHERE id='$tmpcheck' LIMIT 1");
              $summezumcheckenneu = $summezumchecken - $menge;
              if ($summezumcheckenneu <= 0) $this->app->DB->Delete("DELETE FROM lager_platz_inhalt WHERE id='$tmpcheck' LIMIT 1");
              else $this->app->DB->Update("UPDATE lager_platz_inhalt SET menge='$summezumcheckenneu' WHERE id='$tmpcheck' LIMIT 1");
            } else {
              // lager solange aus bis genug ausgelagert sind
              $nochoffen = $menge;
              while ($nochoffen > 0) {
                $tmpcheck = $this->app->DB->Select("SELECT id FROM lager_platz_inhalt WHERE lager_platz='$regal' AND artikel='$artikel' LIMIT 1");
                $tmpcheckmenge = $this->app->DB->Select("SELECT menge FROM lager_platz_inhalt WHERE id='$tmpcheck' LIMIT 1");
                if ($tmpcheckmenge <= $nochoffen) {
                  $this->app->DB->Delete("DELETE FROM lager_platz_inhalt WHERE id='$tmpcheck' LIMIT 1");
                  $nochoffen = $nochoffen - $tmpcheckmenge;
                } else {
                  $summezumcheckenneu = $tempcheckmenge - $nochoffen;
                  $this->app->DB->Update("UPDATE lager_platz_inhalt SET menge='$summezumcheckenneu' WHERE id='$tmpcheck' LIMIT 1");
                  $nochoffen = 0;
                }
              }
            }
            // umlagern3 in lager_platz_inhalt buchen
            if($cmd=="umlagern")
              $this->app->erp->LagerEinlagernDifferenz($artikel,$menge,$regalneu,$projekt,"Umlagern");

            if($artikelid > 0)
            {
              header("Location: index.php?module=artikel&action=lager&id=$artikelid");
            } else {
              $name = $this->app->DB->Select("SELECT CONCAT(nummer,' ',name_de) FROM artikel WHERE id='$artikel' LIMIT 1");
              $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Artikel $name wurde umgelagert. Der n&auml;chste Artikel kann jetzt umgelagert werden.</div>");
              if($cmd=="umlagern")
                header("Location: index.php?module=lager&action=buchenauslagern&cmd=umlagern&msg=$msg");
              else
                header("Location: index.php?module=lager&action=buchenauslagern");
            }
            exit;
          } // ende allow
          if ($regalneu != "" && $regal > 0 && $cmd=="umlagern") {
            $msgregal = "Dieses Regal gibt es nicht!";
            $this->app->Tpl->Set(MESSAGELAGER, "<div class=\"error\">$msgregal</div>");
            $regalcheck = 0;
          }

          // ende auslagern
        }
      } else {
        //$error++;
        if ($regal != "") {
          $msgregal = "Dieses Regal gibt es nicht!";
          $this->app->Tpl->Set(MESSAGELAGER, "<div class=\"error\">$msgregal</div>");
          $regalcheck = 0;
        }





      }
      if ($error == 0 && $regalcheck == 0) {
        $standardbild = $this->app->DB->Select("SELECT standardbild FROM artikel WHERE id='$artikel' LIMIT 1");
        if ($standardbild == "") $standardbild = $this->app->DB->Select("SELECT datei FROM datei_stichwoerter WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='$artikel' LIMIT 1");

        $this->app->Tpl->Add(BEZEICHNUNG, "<tr valign=\"top\"><td>Aktueller Artikel:</td><td>$name_de</td></tr>"); //BENE
        if ($standardbild > 0) $this->app->Tpl->Add(BEZEICHNUNG, "<tr valign=\"top\"><td>Bild:</td><td align=\"center\"><img src=\"index.php?module=dateien&action=send&id=$standardbild\" width=\"110\"></td></tr>");

        $lagermeist = $this->app->DB->SelectArr("SELECT lager_platz, SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel' GROUP BY lager_platz ORDER by 2 DESC LIMIT 1");
        $lagerbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='{$lagermeist[0]['lager_platz']}' LIMIT 1");

        $standard_lagerplatz = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$artikel' LIMIT 1");
        $standard_lagerbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$standard_lagerplatz' LIMIT 1");

        if($lagerbezeichnung!=$standard_lagerbezeichnung && $standard_lagerbezeichnung!="")
          $standardlageranzeigen = "<b onclick=\"document.getElementById('regal').value='$standard_lagerbezeichnung'\";>$standard_lagerbezeichnung</b> (Standardlager)";

        //echo "huhuh $cmd regal $regal regalvalue $regalvalue checkregal $checkregal regaltreffer $regaltreffer";
        if($regaltreffer=="1") $regalvalue=$this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$regal' LIMIT 1"); else $regalvalue="";
        //if($regal !="" && $regalvalue=="") $regalvalue=$regal;
        if($regalvalue!="" && $cmd=="umlagern" && $regal > 0 && $regal==$checkregal)
        {
          if($this->app->erp->Version()!="stock")
          {
            $this->app->Tpl->Add(ZWISCHENLAGERINFO, "<tr ><td>Regalvorschlag:</td><td align=\"left\"><input type=\"button\"  onclick=\"document.getElementById('regal').value='$regalvalue'\"; value=\"$regalvalue\"></td></tr>");
          }
          $this->app->Tpl->Add(ZWISCHENLAGERINFO, "<tr valign=\"top\"><td><b>Zielregal:</b></td><td align=\"left\"><input type=\"text\" style=\"width:200px;border: 2px solid red\" name=\"regalneu\" id=\"regal\" value=\"\"><br>Jetzt Regal abscannen!<script type=\"text/javascript\">document.getElementById('menge').style.backgroundColor='#ececec'; document.getElementById('nummer').style.backgroundColor='#ececec'; document.getElementById('grundreferenz').style.backgroundColor='#ececec';
              document.getElementById('grundreferenz').readOnly=true;
              document.getElementById('menge').readOnly=true;
              document.getElementById('nummer').readOnly=true;
              </script>
              <input type=\"hidden\" name=\"regal\" value=\"$regalvalue\"></td></tr>");
        } else {
          if($this->app->erp->Version()=="stock")
          {
            if($this->app->Secure->GetPOST("regal")=="" && $this->app->Secure->GetGET("regal")=="") //TODO
              $regalvalue = $lagerbezeichnung; //TODO
            //						$regalvaluestock=$regalvalue;

            $this->app->Tpl->Add(ZWISCHENLAGERINFO, "<tr ><td>Regalvorschlag:</td><td align=\"left\"><input type=\"button\" onclick=\"document.getElementById('regal').value='$lagerbezeichnung'\" value=\"$lagerbezeichnung\" > (Standardlager)<br>$standardlageranzeigen</td></tr>");
          }
          else {

            $this->app->Tpl->Add(ZWISCHENLAGERINFO, "<tr ><td>Regalvorschlag:</td><td align=\"left\"><input type=\"button\" onclick=\"document.getElementById('regal').value='$lagerbezeichnung'\" value=\"$lagerbezeichnung\" > (aktuell am meisten im Lager)<br>$standardlageranzeigen</td></tr>");
          }
          $this->app->Tpl->Add(ZWISCHENLAGERINFO, "<tr valign=\"top\"><td><b>Entnahmeregal:</b></td><td align=\"left\"><input type=\"text\" style=\"width:200px;border: 2px solid red;\" name=\"regal\" id=\"regal\" value=\"$regalvaluestock\"><br>Jetzt Regal abscannen!</td></tr>");
        }
        // letzt einstellung von grad
        $this->app->Tpl->Add(ZWISCHENLAGERINFO, '<script type="text/javascript">
            document.getElementById("regal").focus();
            </script>');
      } else if ($error == 0) {
        echo "speichern adresse $checkadresse projekt $projekt menge $menge";
      }
    }
    if ($nummer == "") $this->app->Tpl->Set(ARTIKELSTYLE, "style=\"border: 2px solid red\"");

    $this->app->Tpl->Set(MENGE, $menge);
    $this->app->Tpl->Set(GRUNDREFERENZ, $grundreferenz);

    $art_name = $this->app->DB->Select("SELECT CONCAT(nummer) FROM artikel WHERE nummer='$nummer' AND geloescht!=1 LIMIT 1");
    $this->app->Tpl->Set(NUMMER, $art_name);

    $pr_name = $this->app->DB->Select("SELECT CONCAT(abkuerzung) FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
    $this->app->Tpl->Set(ADRESSE, $adresse);
    if ($_SESSION[grund] == "Interner Entwicklungsbedarf") $this->app->Tpl->Set(MUSTER, "selected");
    if ($_SESSION[grund] == "RMA / Reparatur / Reklamation") $this->app->Tpl->Set(RMA, "selected");
    if ($_SESSION[grund] == "Alte Bestellung") $this->app->Tpl->Set(ALTE, "selected");
    if ($_SESSION[grund] == "Kundenauftrag / Produktion") $this->app->Tpl->Set(PRODUKTION, "selected");
    if ($_SESSION[grund] == "Manuelle Lageranpassung") $this->app->Tpl->Set(DIFFERENZ, "selected");
    if ($_SESSION[grund] == "Umlagern") $this->app->Tpl->Set(UMLAGERN, "selected");
    //$this->app->YUI->AutoComplete(PROJEKTAUTO,"projekt",array('name','abkuerzung'),"abkuerzung");
    $this->app->YUI->AutoComplete("projekt", "projektname", 1);
    $this->app->YUI->AutoComplete("adresse", "adresse");
    $this->app->YUI->AutoComplete('nummer','lagerartikelnummer');
    $this->app->YUI->AutoComplete('regal','lagerplatz');
    $this->app->YUI->AutoComplete('grundreferenz','lagergrund');
    //$this->app->YUI->AutoComplete(ADRESSEAUTO,"adresse",array('id','name','kundennummer'),"CONCAT(id,' ',name)");
    $this->app->Tpl->Set(PROJEKT, $pr_name);
    $this->app->Tpl->Parse(TAB1, "auslagern.tpl");
    $this->app->Tpl->Parse(PAGE, "tabview.tpl");
  }

  function LagerLetzteBewegungen()
  {
    $this->LagerBuchenMenu();

    $this->app->YUI->TableSearch(TAB1, "lagerletztebewegungen");	

    $this->app->Tpl->Parse(PAGE, "tabview.tpl");
  }	


  function LagerBuchenMenu() {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Lager");
    //$this->app->erp->MenuEintrag("index.php?module=lager&action=artikelfuerlieferungen&id=$id","Artikel f&uuml;r Lieferungen");
    if($this->app->erp->Version()=="stock")
    {
      //$this->app->erp->MenuEintrag("index.php?module=lager&action=buchenauslagern&id=$id", "Lager anpassen");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=buchenauslagern&cmd=umlagern&id=$id", "Lagerentnahme");
    }
    else
    {
      $this->app->erp->MenuEintrag("index.php?module=lager&action=buchenauslagern&cmd=umlagern&id=$id", "Umlagern");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=buchenauslagern&id=$id", "Auslagern");

      $this->app->erp->MenuEintrag("index.php?module=lager&action=bucheneinlagern&id=$id", "Einlagern");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=buchenzwischenlager&id=$id", "Zwischenlager");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=buchen", "Zur&uuml;ck zur &Uuml;bersicht");
    }
    $this->app->erp->MenuEintrag("index.php?module=lager&action=schnellauslagern", "Schnell-Auslagern");
    $this->app->erp->MenuEintrag("index.php?module=lager&action=schnellumlagern", "Schnell-Umlagern");
    //    $this->app->erp->MenuEintrag("index.php?module=lager&action=schnelleinlagern", "Schnell-Einlagern");
    $this->app->erp->MenuEintrag("index.php?module=lager&action=letztebewegungen", "Letzte Bewegungen");
  }
  function LagerReservierungen() {
    $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Reservierungen");
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(TABNAME, "Inhalt");
    $this->app->Tpl->Set(SUBSUBHEADING, "Reservierungen Stand " . date('d.m.Y'));
    // easy table mit arbeitspaketen YUI als template
    $table = new EasyTable($this->app);
    $table->Query("SELECT adr.name as kunde, a.name_de as Artikel,r.menge,p.abkuerzung as projekt,r.grund, r.id FROM lager_reserviert r LEFT JOIN artikel a ON a.id=r.artikel LEFT JOIN projekt p ON 
        p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id WHERE r.firma='{$this->app->User->GetFirma() }'");
    $table->DisplayNew(INHALT, "<a href=\"#\" onclick=\"if(!confirm('Artikel aus Reservierungen nehmen?')) return false; else window.location.href='index.php?module=lager&action=artikelentfernenreserviert&reservierung=%value%';\"><img src=\"./themes/[THEME]/images/delete.gif\"></a>");
    $this->app->Tpl->Parse(TAB1, "rahmen70.tpl");
    $this->app->Tpl->Set(AKTIV_TAB1, "selected");
    $this->app->Tpl->Parse(PAGE, "tabeinzeln.tpl");
  }
  function LagerProduktionslager() {
    $this->app->Tpl->Add(TABS, "<li><h2>Produktionslager</h2></li>");
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(TABNAME, "Inhalt");
    $this->app->Tpl->Set(SUBSUBHEADING, "Produktionslager Stand " . date('d.m.Y'));
    // easy table mit arbeitspaketen YUI als template
    $table = new EasyTable($this->app);
    $table->Query("SELECT a.name_de as Artikel,z.menge,z.vpe, p.abkuerzung, z.id FROM produktionslager z LEFT JOIN artikel a ON a.id=z.artikel LEFT JOIN projekt p ON 
        p.id=z.projekt WHERE z.firma='{$this->app->User->GetFirma() }'");
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=lager&action=bewegungpopup&frame=false&id=%value%\" 
        onclick=\"makeRequest(this);return false\">Info</a>");
    $this->app->Tpl->Parse(TAB1, "rahmen70.tpl");
    $this->app->Tpl->Set(AKTIV_TAB1, "selected");
    $this->app->Tpl->Parse(PAGE, "tabeinzeln.tpl");
  }

  function LagerRegalEtiketten() {
    $id = $this->app->Secure->GetGET("id");
    $platz = $this->app->Secure->GetGET("platz");
    $cmd = $this->app->Secure->GetGET("cmd");

    if ($cmd=="all") $arr = $this->app->DB->SelectArr("SELECT id,kurzbezeichnung FROM lager_platz WHERE lager='$id'");
    else $arr = $this->app->DB->SelectArr("SELECT id,kurzbezeichnung FROM lager_platz WHERE id='$id' LIMIT 1");

    for ($i = 0;$i < count($arr);$i++) {
      //$arr[$i][kurzbezeichnung] = trim($arr[$i][kurzbezeichnung]);
      //$arr[$i][id] = str_pad($arr[$i][id], 7, '0', STR_PAD_LEFT);
      $this->app->erp->EtikettenDrucker("lagerplatz_klein",1,"lager_platz",$arr[$i]['id']);
    }
    $ref = $_SERVER['HTTP_REFERER'];
    header("Location: $ref");
    exit;

  }


  function LagerPDFSammelentnahme()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->LagerAuslagernArtikellisteProduktionSammel($artikelliste,$projekt="",$id,"pdf");
    exit;
  }


  function LagerArtikelfuerlieferungen() {
    //$this->LagerBuchenMenu();
    $artikel = $this->app->Secure->GetGET("artikel");
    $menge = $this->app->Secure->GetGET("menge");
    $lager = $this->app->Secure->GetGET("lager");
    $projekt = $this->app->Secure->GetGET("projekt");
    $produktion = $this->app->Secure->GetGET("produktion");
    $lagerplatzid = $this->app->Secure->GetGET("lagerplatzid");
    $cmd = $this->app->Secure->GetGET("cmd");

    if($cmd=="produktion")
    {
      $this->app->Tpl->Set(TABTEXT, "Artikel f&uuml;r Produktion");
      $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Artikel f&uuml;r Produktion");
    } else {
      $this->app->Tpl->Set(TABTEXT, "Artikel f&uuml;r Lieferungen");
      $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Artikel f&uuml;r Lieferungen");
    }

    //echo "lager $lager artikel $artikel menge $menge lp $lagerplatzid";

    // altes auslagern

    if ($lager != "" && $artikel != "" && $menge != "" && $lagerplatzid!="") {
      // schaue obs den artikel in der menge in dem lager gibt
      //echo "nehme $artikel insgesamt $menge mal aus $lager";
      if($cmd=="produktion")
      {
        $checklagerplatz = $this->app->DB->Select("SELECT li.lager_platz FROM lager_platz_inhalt li LEFT JOIN lager_platz lp ON lp.id=li.lager_platz
            WHERE li.lager_platz='$lagerplatzid' AND li.artikel='$artikel' AND lp.autolagersperre!='1'");

        $checklagerplatzid = $this->app->DB->Select("SELECT li.id FROM lager_platz_inhalt li LEFT JOIN lager_platz lp ON lp.id=li.lager_platz  
            WHERE li.lager_platz='$lagerplatzid' AND li.artikel='$artikel' AND lp.autolagersperre!='1' ");

        $checkmenge = $this->app->DB->Select("SELECT li.menge FROM lager_platz_inhalt li LEFT JOIN lager_platz lp ON lp.id=li.lager_platz 
            WHERE li.lager_platz='$lagerplatzid' AND li.artikel='$artikel' AND lp.autolagersperre!='1'");
        //$checkmenge = $this->app->DB->Select("SELECT menge FROM lager_platz_inhalt WHERE lager_platz='$lagerplatzid' AND artikel='$artikel'");
      } else {

        $checklagerplatz = $this->app->DB->Select("SELECT li.lager_platz FROM lager_platz_inhalt li 
            LEFT JOIN lager_platz lp ON lp.id=li.lager_platz 
            WHERE li.lager_platz='$lagerplatzid' AND li.artikel='$artikel' AND lp.autolagersperre!='1' LIMIT 1");

        $checkmenge = $this->app->DB->Select("SELECT SUM(li.menge) FROM lager_platz_inhalt li
            LEFT JOIN lager_platz lp ON lp.id=li.lager_platz 
            WHERE li.lager_platz='$lagerplatzid' AND li.artikel='$artikel' AND lp.autolagersperre!='1' LIMIT 1");

        $lagerplatzid = $this->app->DB->Select("SELECT li.id FROM lager_platz_inhalt li 
            LEFT JOIN lager_platz lp ON lp.id=li.lager_platz 
            WHERE li.lager_platz='$lagerplatzid' AND li.artikel='$artikel' AND lp.autolagersperre!='1' LIMIT 1");
      }


      if ($checklagerplatz != $lager) {
        $this->app->Tpl->Set(MESSAGE, "<div class=\"error\">Falsches Regal gescannt!</div>");
      } else if ($menge > $checkmenge) {
        $this->app->Tpl->Set(MESSAGE, "<div class=\"error\">Problem: Es gibt nicht soviele im Regal wie angegeben!</div>");
      } else {
        // platz anzahl anpassen
        $mengeneu = $checkmenge - $menge;

        if($cmd=="produktion"){
          if ($menge < $checkmenge) $this->app->DB->Update("UPDATE lager_platz_inhalt SET menge='$mengeneu' WHERE id='$checklagerplatzid' LIMIT 1");
          else $this->app->DB->Update("DELETE FROM lager_platz_inhalt WHERE id='$checklagerplatzid' LIMIT 1");
        } else {
          if ($menge < $checkmenge) {
            /**
             * Um die Mengen nicht immer in einem Eintrag auf einem Lagerplatz haben zu müssen
             * ist die Ausbuchung hier bearbeitet ÖK 01.09.2014
             */
            //$this->app->DB->Update("UPDATE lager_platz_inhalt SET menge='$mengeneu' WHERE id='$lagerplatzid' LIMIT 1");
            $aTmpLagerArtikel = $this->app->DB->SelectArr("SELECT li.id, li.menge FROM lager_platz_inhalt li
                LEFT JOIN lager_platz lp ON lp.id=li.lager_platz
                WHERE li.lager_platz='$lager' AND li.artikel='$artikel' AND lp.autolagersperre!='1'");

            if (sizeof($aTmpLagerArtikel) > 0) {
              $iRestMenge = (int)$menge;
              for ($u = 0;$u < count($aTmpLagerArtikel);$u++) {
                if (!$iRestMenge > 0) {
                  break;
                }
                $iFreieLagermenge = $aTmpLagerArtikel[$u]['menge'];
                $iLagerPlatzId = $aTmpLagerArtikel[$u]['id'];

                if ($iFreieLagermenge > abs($iRestMenge)) {
                  $iNeueLagerartikelMenge = (int)$iFreieLagermenge - $iRestMenge;
                  $iRestMenge = 0;
                } else {
                  $iNeueLagerartikelMenge = 0;
                  $iRestMenge -= $iFreieLagermenge;
                }
                $this->app->DB->Update("UPDATE lager_platz_inhalt SET menge='$iNeueLagerartikelMenge' WHERE id='$iLagerPlatzId' LIMIT 1");
              }
            }
          }
          else $this->app->DB->Update("DELETE FROM lager_platz_inhalt WHERE id='$lagerplatzid' LIMIT 1");
        }
        // rein ins zwischenlager
        if($cmd!="produktion")
        {
          $grund = "Artikel f&uuml;r Lieferungen " . date('d.m.Y');
          $this->app->DB->Insert("INSERT INTO zwischenlager (id,bearbeiter,projekt,artikel,menge,vpe,grund,richtung,objekt,parameter,firma) 
              VALUES ('','" . $this->app->User->GetName() . "','$projekt','$artikel','$menge','$vpe','$grund','Ausgang','lieferung',DATE_FORMAT(NOW(),'%Y-%m-%d'),'" . $this->app->User->GetFirma() . "')");
          $tmparrres = $this->app->DB->SelectArr("SELECT * FROM lager_reserviert WHERE objekt='lieferschein' AND artikel='$artikel' AND firma='" . $this->app->User->GetFirma() . "'");
        } else {
          $produktionsnummer = $this->app->DB->Select("SELECT belegnr FROM produktion WHERE id='$produktion' LIMIT 1");
          $grund = "Artikel f&uuml;r Produktion $produktionsnummer " . date('d.m.Y');
          $tmparrres = $this->app->DB->SelectArr("SELECT * FROM lager_reserviert WHERE objekt='produktion' AND parameter='$produktion' AND artikel='$artikel' AND firma='" . $this->app->User->GetFirma() . "'");
        }

        // bewegung buchen
        $this->app->DB->Insert("INSERT INTO lager_bewegung (id,lager_platz,artikel,menge,vpe,eingang,zeit,referenz,bearbeiter,projekt,firma) VALUES 
            ('','$lager','$artikel','$menge','$vpe','0',NOW(),'$grund','" . $this->app->User->GetName() . "','$projekt','" . $this->app->User->GetFirma() . "')");

        $geloescht = 0;
        for ($u = 0;$u < count($tmparrres);$u++) {
          // wenn die menge der lieferung kleiner als menge dann menge loeschen
          if ($tmparrres[$u][menge] >= ($menge - $geloescht)) {
            // DELETE
            $neuerwert = $tmparrres[$u][menge] - ($menge - $geloescht);
            $this->app->DB->Update("UPDATE lager_reserviert SET menge='$neuerwert' WHERE id='{$tmparrres[$u][id]}' LIMIT 1");
            $this->app->DB->Update("DELETE FROM lager_reserviert WHERE menge='0' LIMIT 1");
            break;
          } else {
            //wieviel muss man denn abziehen?
            //$soviel = $menge - $geloescht;
            $geloescht = $geloescht + $tmparrres[$u][menge];
            $this->app->DB->Update("DELETE FROM lager_reserviert WHERE id='{$tmparrres[$u][id]}' LIMIT 1");
            //break;
          }
          if ($geloescht >= $menge) break;
        }
        if($cmd=="produktion"){
          header("Location: index.php?module=lager&action=auslagernproduktion&id=$produktion&cmd=produktion");
        }
        else {
          header("Location: index.php?module=lager&action=artikelfuerlieferungen&cmd=$cmd");
        }
        exit;
      }
    }

    // Anzeige weitere Artikel zum Auslagern

    if($cmd!="produktion"){
      $this->LagerAuslagernProjektbasiert();
    }
    else {
      if($lager=="")
      {
        $this->LagerAuslagernProduktionbasiert();
      }
      else
      {
        $this->LagerAuslagernArtikellisteProduktionEinzel($produktion);
      }
    }
  }

  function LagerAuslagernProduktionbasiert()
  {

    // offene auslagerungen
    $result = $this->app->DB->SelectArr("SELECT r.parameter FROM lager_reserviert r LEFT JOIN produktion p ON p.id=r.parameter
        WHERE r.objekt='produktion' AND (p.status='abgeschlossen' OR p.status='gestartet') GROUP BY r.parameter");
    $gesamtanzahlartikel = 0;
    $this->app->Tpl->Set(TAB1, "<table border=0 width=100% class=\"display\">
        <tr><td><b>Produktion</b></td><td><b>Bezeichnung</b></td><td><b>Bearbeiten</b></td><td align=center><b>Auslagern</b></td></tr>");
    for ($w = 0;$w < count($result);$w++) {
      $produktion = $result[$w][parameter];

      $bezeichnung = $this->app->DB->Select("SELECT CONCAT(ar.name_de,' (',ar.nummer,')') FROM 
        produktion_position pos LEFT JOIN artikel ar ON ar.id=pos.artikel WHERE pos.produktion='$produktion' AND pos.explodiert=1 LIMIT 1");

          $nummer = $this->app->DB->Select("SELECT belegnr FROM produktion WHERE id='$produktion' LIMIT 1");
          $this->app->Tpl->Add(TAB1, "<tr><td>Produktion $nummer</td><td>$bezeichnung</td><td><a href=\"index.php?module=produktion&action=edit&id=$produktion\"><img src=\"./themes/[THEME]/images/edit.png\"></a>&nbsp;<!--<a href=\"index.php?module=produktion&action=pdf&id=$produktion\"><img src=\"./themes/[THEME]/images/pdf.png\"></a>&nbsp;&nbsp;--><a href=\"index.php?module=lager&action=lagerpdfsammelentnahme&id=$produktion\"><img src=\"./themes/[THEME]/images/pdf.png\"></a></td><td align=center><a href=\"index.php?module=lager&action=auslagernproduktion&id=$produktion&cmd=produktion\"><img src=\"./themes/[THEME]/images/forward.png\"></a></td></tr>");
          $artikellistesumm = $this->app->DB->SelectArr("SELECT DISTINCT artikel FROM lager_reserviert WHERE objekt='produktion' AND parameter='$produktion'");
          if (count($artikellistesumm) == 0) continue;
          $artikelliste = $this->app->DB->SelectArr("SELECT DISTINCT artikel FROM lager_reserviert WHERE objekt='produktion' AND parameter='$produktion'");

          $gesamtanzahlartikel  = $gesamtanzahlartikel + count($artikelliste);
          }
          $this->app->Tpl->Add(TAB1, "</table>");

          if ($gesamtanzahlartikel <= 0) {
          $this->app->Tpl->Set(MESSAGE, "<div class=\"info\">Aktuell gibt es keine Artikel f&uuml;r Produktionen, da keine offenen Produktionen vorhanden sind.</div>");
          $this->app->Tpl->Set(TAB1,"");
          }

          $this->app->Tpl->Parse(PAGE, "tabview.tpl");
          }


  function LagerAuslagernProjektbasiert()
  {
    $projekt = 1;
    $projektearr = $this->app->DB->SelectArr("SELECT id FROM projekt WHERE firma='" . $this->app->User->GetFirma() . "'");
    $projektearr[] = 0;
    $gesamtanzahlartikel = 0;
    // start projekt schleife
    for ($w = 0;$w < count($projektearr);$w++) {
      $this->app->Tpl->Set(INHALT, "");
      $projekt = $projektearr[$w][id];
      $projektName = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$projekt' LIMIT 1");
      if ($projekt == 0 || $projekt == "") $projektName = "Ohne Projekt";
      $artikellistesumm = $this->app->DB->SelectArr("SELECT DISTINCT artikel FROM lager_reserviert WHERE objekt='lieferschein' AND projekt='$projekt' AND firma='" . $this->app->User->GetFirma() . "'");
      if (count($artikellistesumm) == 0) continue;
      $this->app->Tpl->Add(INHALT, "<h2>$projektName Lieferungen Stand " . date('d.m.Y') . "</h2>");

      $artikelliste = $this->app->DB->SelectArr("SELECT DISTINCT artikel FROM lager_reserviert WHERE objekt='lieferschein' AND projekt='$projekt' AND firma='" . $this->app->User->GetFirma() . "'");
      $artikelliste = $this->LagerAuslagernReihenfolge($artikelliste,$projekt);
      $gesamtanzahlartikel =$this->LagerAuslagernArtikelliste($artikelliste,$projekt);

    } // ende projekt schleife
    if ($gesamtanzahlartikel <= 0) $this->app->Tpl->Set(MESSAGE, "<div class=\"info\">Aktuell gibt es keine Artikel f&uuml;r Lieferungen, da keine offenen Auftr&auml;ge im Autoversand sind.</div>");
    $this->app->Tpl->Parse(PAGE, "tabview.tpl");
  }

  //Einzelmenge
  function LagerAuslagernArtikellisteProduktion($artikelliste,$projekt="",$produktion="")
  {

    $id = $this->app->Secure->GetGET("id");

    $produktion = $id;

    $auslagerart = $this->app->DB->Select("SELECT auslagerart FROM produktion WHERE id='$produktion' LIMIT 1");

    if($auslagerart=="sammel")
      $this->LagerAuslagernArtikellisteProduktionSammel($artikelliste,$projekt,$produktion);
    else
      $this->LagerAuslagernArtikellisteProduktionEinzel($produktion);

  }



  //Einzelmenge
  function LagerAuslagernArtikellisteProduktionEinzel($produktion="")
  {
    $id = $this->app->Secure->GetGET("id");

    if($produktion<0)
      $produktion = $id;

    $auslagerart = $this->app->DB->Select("SELECT auslagerart FROM produktion WHERE id='$produktion' LIMIT 1");

    $artikelliste = $this->app->DB->SelectArr("SELECT DISTINCT artikel FROM lager_reserviert WHERE objekt='produktion' AND parameter='$produktion'");


    $cmd = $this->app->Secure->GetGET("cmd");
    $htmltable = new HTMLTable(0, "100%", "", 3, 1);
    if ($this->app->User->GetType() == "admin") $htmltable->AddRowAsHeading(array('Menge', 'Nummer', 'Artikel', 'Projekt', 'Regal', 'Regal', 'Aktion', 'Entfernen'));
    else $htmltable->AddRowAsHeading(array('Menge', 'Nummer', 'Artikel', 'Projekt', 'Regal', 'Regal', 'Aktion'));
    $htmltable->ChangingRowColors('#e0e0e0', '#fff');
    $tmpanzahl = 0;

    for ($i = 0;$i < count($artikelliste);$i++) {
      $gesamtanzahlartikel++;
      $artikel = $artikelliste[$i][artikel];
      $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1");
      $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");
      //wieviel stueck braucht man denn von dem artikel?
      //if(is_numeric($projekt))
      //	$gesamtbedarf = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE objekt='lieferschein' AND projekt='$projekt' AND artikel='$artikel' AND firma='" . $this->app->User->GetFirma() . "'");
      //else
      $gesamtbedarf = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE objekt='produktion' AND parameter='$produktion' AND artikel='$artikel' AND firma='" . $this->app->User->GetFirma() . "'");
      //$artikel_in_regalen = $this->app->DB->SelectArr("SELECT * FROM lager_platz_inhalt WHERE artikel='$artikel' AND projekt='$projekt'");

      // standardlager artikel 
      $standardlagerartikel = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$artikel'");
      // Zeige nur Artikel an die im Lager sind!

      $tmp_check_standardlager = $this->app->DB->Select("SELECT SUM(li.menge) FROM lager_platz_inhalt li LEFT JOIN lager_platz lp ON lp.id=li.lager_platz WHERE 
          li.artikel='$artikel' AND li.lager_platz='$standardlagerartikel' AND lp.autolagersperre!=1");

      // erst standarlager ausraeumen bis zu wenig drin ist
      // und dann die lager an denene am meisten ist
      if($tmp_check_standardlager>=$gesamtbedarf)
        $artikel_in_regalen = $this->app->DB->SelectArr("SELECT * FROM lager_platz_inhalt li LEFT JOIN lager_platz lp ON lp.id=li.lager_platz
            WHERE li.artikel='$artikel' AND li.lager_platz='$standardlagerartikel' AND lp.autolagersperre!=1 ORDER by li.menge DESC");
      else
        $artikel_in_regalen = $this->app->DB->SelectArr("SELECT * FROM lager_platz_inhalt li LEFT JOIN lager_platz lp ON lp.id=li.lager_platz WHERE li.artikel='$artikel' 
            AND lp.autolagersperre!=1 ORDER by li.menge DESC");

      for ($j = 0;$j < count($artikel_in_regalen);$j++) {
        $htmltable->NewRow();
        $tmpanzahl++;
        $menge_im_platz = $artikel_in_regalen[$j][menge];
        $kurzbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='{$artikel_in_regalen[$j][lager_platz]}' LIMIT 1");
        if ($menge_im_platz <= $gesamtbedarf) {
          $tmpmenge = $menge_im_platz;
        } else {
          $tmpmenge = $gesamtbedarf;
        }
        $rest = $menge_im_platz - $tmpmenge; 
        //$this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel' AND firma='".$this->app->User->GetFirma()."'") - $tmpmenge;
        if ($rest == 0) $rest = "-";
        if ($tmpanzahl == 1) $erstes = "erstes";
        else $erstes = "";
        $htmltable->AddCol($tmpmenge);
        $htmltable->AddCol($nummer);
        $htmltable->AddCol($name_de);
        $htmltable->AddCol($this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$projekt' LIMIT 1"));
        $htmltable->AddCol($kurzbezeichnung);
        //$htmltable->AddCol($rest);

        $htmltable->AddCol("Regal: <input type=\"text\" size=\"10\" id=\"$erstes\" 
            onchange=\"if(!confirm('Artikelnummer $nummer wurde $tmpmenge mal entnommen?')) return false; else window.location.href='index.php?module=lager&action=artikelfuerlieferungen&cmd=$cmd&artikel=$artikel&menge=$tmpmenge&projekt=$projekt&produktion=$produktion&lagerplatzid={$artikel_in_regalen[$j][id]}&lager='+this.value;\">");
        $htmltable->AddCol("<img src=\"./themes/[THEME]/images/forward.png\">");
        if ($this->app->User->GetType() == "admin") $htmltable->AddCol("<a href=\"#\" onclick=\"if(!confirm('Artikel aus Produktion und Reservierungen nehmen?')) return false; else window.location.href='index.php?module=lager&action=artikelentfernen&produktion=$produktion&projekt=$projekt&artikel=$artikel&cmd=$cmd';\"><img src=\"./themes/[THEME]/images/delete.gif\"></a>");
        $gesamtbedarf = $gesamtbedarf - $tmpmenge;
        if ($gesamtbedarf == 0) break;
      }
    }
    //bestimme regalplaetze fuer artikel
    $this->app->Tpl->Add(TAB1, $htmltable->Get());
    // und enter abfangen!!!
    $this->app->Tpl->Add(TAB1, "<script type=\"text/javascript\">document.getElementById(\"erstes\").focus(); </script>");
    //$table->DisplayNew(INHALT, "<a href=\"index.php?module=lager&action=bucheneinlagern&cmd=zwischenlager&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/einlagern.png\"></a>");


    $belegnr_produktion = $this->app->DB->Select("SELECT belegnr FROM produktion WHERE id='$produktion' LIMIT 1");

    $bezeichnung = $this->app->DB->Select("SELECT CONCAT(ar.name_de,' (',ar.nummer,')') FROM produktion_position pos LEFT JOIN artikel ar ON ar.id=pos.artikel WHERE pos.produktion='$produktion' AND pos.explodiert=1 LIMIT 1");

    $this->app->erp->MenuEintrag("index.php?module=lager&action=artikelfuerlieferungen&cmd=produktion","Zur&uuml;ck zur &Uuml;bersicht");

    $this->app->Tpl->Set(TABTEXT,"Produktion ".$belegnr_produktion." - ".$bezeichnung);
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,$bezeichnung);

    $this->app->Tpl->Parse(PAGE, "tabview.tpl");
  }


  //Einzelmenge
  function LagerAuslagernArtikellisteProduktionSammel($artikelliste,$projekt="",$produktion="",$type="html")
  {

    $id = $this->app->Secure->GetGET("id");
    $lageranpassen = $this->app->Secure->GetPOST("lageranpassen");
    $artikelanpassen = $this->app->Secure->GetPOST("artikelanpassen");
    $lagerplatzanpassen = $this->app->Secure->GetPOST("lagerplatzanpassen");
    $produktion = $id;

    $belegnr_produktion = $this->app->DB->Select("SELECT belegnr FROM produktion WHERE id='$produktion' LIMIT 1");
    $freitext = $this->app->DB->Select("SELECT freitext FROM produktion WHERE id='$produktion' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM produktion WHERE id='$produktion' LIMIT 1");
    $adresse = $this->app->DB->Select("SELECT adresse FROM produktion WHERE id='$produktion' LIMIT 1");
    $adresse_name = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' LIMIT 1");
    $projektabkz = $this->app->DB->Select("SELECT CONCAT(abkuerzung,' (',name,')') FROM projekt WHERE id='$projekt' LIMIT 1");

    if($lageranpassen!="")
    {
      if(count($artikelanpassen) > 0)
      {
        foreach($artikelanpassen as $key=>$value)
        {
          //echo $key." ".$value."<br>";
          $form_lagerplatz =  $lagerplatzanpassen[$key];
          $form_artikel =  $key;
          $form_menge = $value;

          // pruefe ob genug im lager sind sonst lasse diese Zeile da! und geben fehlermeldung pro Artikel aus
          $check_menge = $this->app->DB->Select("SELECT menge FROM lager_platz_inhalt WHERE lager_platz='$form_lagerplatz' AND artikel='$form_artikel' LIMIT 1");

          if($check_menge >= $form_menge)
          {
            // resvierung loeschen
            $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE artikel='$form_artikel' AND objekt='produktion' AND parameter='$id'");
            $grund = "Artikel f&uuml;r Produktion $belegnr_produktion";
            // lager anpassen
            $this->app->erp->LagerAuslagernRegal($form_artikel,$form_lagerplatz,$form_menge,$projekt,$grund);
          } else {
            $artikel_name = $this->app->DB->Select("SELECT CONCAT(name_de,' (',nummer,')') FROM artikel WHERE id='$form_artikel' LIMIT 1");
            $this->app->Tpl->Add(MESSAGE,"<div class=\"error\">Artikel $artikel_name ist nur $check_menge in diesem Lagerplatz vorhanden!</div>");
          }
        }
      }
    }

    $auslagerart = $this->app->DB->Select("SELECT auslagerart FROM produktion WHERE id='$produktion' LIMIT 1");

    $artikelliste = $this->app->DB->SelectArr("SELECT DISTINCT artikel FROM lager_reserviert WHERE objekt='produktion' AND parameter='$produktion'");

    if($type=="html")
      $this->app->Tpl->Add(TAB1, "<form action=\"\" method=\"post\">");

    if($type=="pdf")
    {
      //Create a new PDF file
      $pdf=new FPDFWAWISION();
      $pdf->AddPage();

      $pdf->SetFillColor(255,255,255);
      $pdf->SetTextColor(0);
      $pdf->SetDrawColor(0,0,0);
      $pdf->SetLineWidth(.3);
      $pdf->SetFont('Arial','B',14);
      $pdf->Cell(array_sum($w),7,"Produktion $belegnr_produktion (Ausdruck vom ".date("d.m.Y").")",1,0,'L',true);
      $pdf->Ln();
      $pdf->SetFont('Arial','B',11);


      $header = array('Nummer','Artikel','Regal','Menge','Rest');
      $w = array(40,70,30,30,20);
      $pdf->Cell($w[0],7,$header[0],1,0,'L',true);
      $pdf->Cell($w[1],7,$header[1],1,0,'L',true);
      $pdf->Cell($w[2],7,$header[2],1,0,'L',true);
      $pdf->Cell($w[3],7,$header[3],1,0,'C',true);
      $pdf->Cell($w[4],7,$header[4],1,0,'C',true);
      $pdf->Ln();
      $pdf->SetFont('Arial','',10);
    }

    $cmd = $this->app->Secure->GetGET("cmd");
    if($type=="html"){
      $htmltable = new HTMLTable(0, "100%", "", 3, 1);
      $htmltable->AddRowAsHeading(array('Menge geplant','Menge tatsächlich','Menge im Lagerplatz', 'Nummer', 'Artikel',  'Regal'));
      $htmltable->ChangingRowColors('#e0e0e0', '#fff');
    }
    $tmpanzahl = 0;

    if(count($artikelliste)<=0)
    {
      header("Location: index.php?module=lager&action=artikelfuerlieferungen&cmd=produktion");
      exit;
    }

    for ($i = 0;$i < count($artikelliste);$i++) {
      $gesamtanzahlartikel++;
      $artikel = $artikelliste[$i][artikel];
      $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1");
      $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");
      $gesamtbedarf = $this->app->DB->Select("SELECT SUM(menge) 
          FROM lager_reserviert WHERE objekt='produktion' AND parameter='$produktion' AND artikel='$artikel' AND firma='" . $this->app->User->GetFirma() . "'");
      //$artikel_in_regalen = $this->app->DB->SelectArr("SELECT * FROM lager_platz_inhalt WHERE artikel='$artikel' AND projekt='$projekt'");

      // standardlager artikel 
      $standardlagerartikel = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$artikel'");
      // Zeige nur Artikel an die im Lager sind!

      $tmp_check_standardlager = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel' AND lager_platz='$standardlagerartikel'");

      // erst standarlager ausraeumen bis zu wenig drin ist
      // und dann die lager an denene am meisten ist
      if($tmp_check_standardlager>=$gesamtbedarf)
        $artikel_in_regalen = $this->app->DB->SelectArr("SELECT * FROM lager_platz_inhalt WHERE artikel='$artikel' AND lager_platz='$standardlagerartikel' ORDER by menge DESC");
      else
        $artikel_in_regalen = $this->app->DB->SelectArr("SELECT * FROM lager_platz_inhalt WHERE artikel='$artikel' ORDER by menge DESC");

      for ($j = 0;$j < count($artikel_in_regalen);$j++) {
        if($type=="html"){
          $htmltable->NewRow();
        }
        $tmpanzahl++;
        $menge_im_platz = $artikel_in_regalen[$j][menge];
        $kurzbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='{$artikel_in_regalen[$j][lager_platz]}' LIMIT 1");
        if ($menge_im_platz <= $gesamtbedarf) {
          $tmpmenge = $menge_im_platz;
        } else {
          $tmpmenge = $gesamtbedarf;
        }
        $rest = $menge_im_platz - $tmpmenge; 

        if ($rest == 0) $rest = "-";
        if ($tmpanzahl == 1) $erstes = "erstes";
        else $erstes = "";

        if($type=="html"){
          $htmltable->AddCol($tmpmenge);
          $htmltable->AddCol("<input type=\"text\" value=\"$tmpmenge\" name=\"artikelanpassen[$artikel]\" size=\"10\">");
          $htmltable->AddCol($menge_im_platz);
          $htmltable->AddCol($nummer);
          $htmltable->AddCol($name_de);
          $htmltable->AddCol($kurzbezeichnung."<input type=\"hidden\" name=\"lagerplatzanpassen[$artikel]\" value=\"{$artikel_in_regalen[$j][lager_platz]}\">");
        }

        if($type=="pdf")
        {
          //$header = array('Nummer','Artikel','Regal','Menge','Rest im Lager');

          $pdf->Cell($w[0],6,$nummer,'LRTB',0,'L',$fill);
          $pdf->Cell($w[1],6,$this->app->erp->LimitChar($name_de,30),'LRTB',0,'L',$fill);
          $pdf->Cell($w[2],6,$kurzbezeichnung,'LRTB',0,'L',$fill);
          $pdf->Cell($w[3],6,$tmpmenge,'LRTB',0,'C',$fill);
          $pdf->Cell($w[4],6,$menge_im_platz - $tmpmenge,'LRTB',0,'C',$fill);
          $pdf->Ln();

        }
        $gesamtbedarf = $gesamtbedarf - $tmpmenge;
        if ($gesamtbedarf == 0) break;
      }
    }
    //bestimme regalplaetze fuer artikel
    if($type=="html"){
      $this->app->Tpl->Add(TAB1, $htmltable->Get());
      $this->app->Tpl->Add(TAB1, "<br><center><input type=\"submit\" value=\"Lager anpassen\" name=\"lageranpassen\"></center></form>");
    }
    if($type=="pdf")
    {
      $pdf->Ln();
      $pdf->MultiCell(array_sum($w),5,
          "Produktion:            ".$belegnr_produktion."\r\n".
          "Projekt:                  ".$projektabkz."\r\n".
          "Kunde:                   ".$adresse_name."\r\n".
          //							"Verantwortlicher:   ".$adresse_name."\r\n".
          "\r\nBemerkung:\r\n".
          $freitext,0,'L');
      //		  $pdf->Ln();
      //   		$pdf->SetFont('Arial','',8);
      //    		$pdf->Cell(array_sum($w),0,"\r\nProduktion ".$belegnr_produktion." Datei: ".date('Ymd')."_".$belegnr_produktion.".pdf",'',0,'R');
      $pdf->Output(date('Ymd')."_PR".$belegnr_produktion.".pdf",'D');
      exit;
    }

    $bezeichnung = $this->app->DB->Select("SELECT CONCAT(ar.name_de,' (',ar.nummer,')') 
      FROM produktion_position pos LEFT JOIN artikel ar ON ar.id=pos.artikel WHERE pos.produktion='$produktion' AND pos.explodiert=1 LIMIT 1");

        if($type=="html"){
        $this->app->erp->MenuEintrag("index.php?module=lager&action=artikelfuerlieferungen&cmd=produktion","Zur&uuml;ck zur &Uuml;bersicht");

        $this->app->erp->MenuEintrag("index.php?module=produktion&action=pdf&id=$id","PDF");
        $this->app->erp->MenuEintrag("index.php?module=produktion&action=edit&id=$id","zur Produktion");
        $this->app->Tpl->Set(TABTEXT,"Produktion ".$belegnr_produktion." - ".$bezeichnung);
        $this->app->Tpl->Set(KURZUEBERSCHRIFT,$bezeichnung);

        $this->app->Tpl->Parse(PAGE, "tabview.tpl");
        }
        }


        function LagerReihenfolgeArtikelliste($artikelliste,$projekt="")
        {
        //$orderarray = $this->LagerAuslagernArtikelliste($artikelliste,$projekt,true);
        print_r($orderarray);
        for ($i = 0;$i < count($artikelliste);$i++) {
          $artikel = $artikelliste[$i][artikel];
          echo $artikel."<br>";
        }

        }

    function LagerAuslagernReihenfolge($artikelliste,$projekt="")
    {
      // Reihenfolge abholen
      $orderarray = $this->LagerAuslagernArtikelliste($artikelliste,$projekt,true);
      for($i=0;$i<count($orderarray);$i++)
      {
        $artikel = $orderarray[$i]["artikel"];	
        $kurzbezeichnung = $orderarray[$i]["kurzbezeichnung"];	
        $tmparray[$artikel]=$kurzbezeichnung;
      }
      // neu sortieren
      asort($tmparray);
      if(count($tmparray)>0)
      {
        foreach($tmparray as $key=>$value)
        {
          $newartikelliste[]=array("artikel"=>$key);
        }
      }
      return $newartikelliste;
    }

    //function LagerAuslagernList($artikelliste,$projekt="",$getorder=false)
    function LagerAuslagernArtikelliste($artikelliste,$projekt="",$getorder=false)
    {
      $cmd = $this->app->Secure->GetGET("cmd");

      if(!$getorder){
        $htmltable = new HTMLTable(0, "100%", "", 3, 1);
        if ($this->app->User->GetType() == "admin") $htmltable->AddRowAsHeading(array('Menge', 'Nummer', 'Artikel', 'Projekt', 'Regal', 'Regal', 'Aktion', 'Entfernen'));
        else $htmltable->AddRowAsHeading(array('Menge', 'Nummer', 'Artikel', 'Projekt', 'Regal', 'Regal', 'Aktion'));
        $htmltable->ChangingRowColors('#e0e0e0', '#fff');
      }
      $tmpanzahl = 0;

      for ($i = 0;$i < count($artikelliste);$i++) {
        $gesamtanzahlartikel++;
        $artikel = $artikelliste[$i][artikel];
        $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1");
        $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");
        //wieviel stueck braucht man denn von dem artikel?


        if(is_numeric($projekt))
          $gesamtbedarf = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE objekt='lieferschein' AND projekt='$projekt' AND artikel='$artikel' AND firma='" . $this->app->User->GetFirma() . "'");
        else
          $gesamtbedarf = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE objekt='lieferschein' AND projekt='0' AND artikel='$artikel' AND firma='" . $this->app->User->GetFirma() . "'");

        //$artikel_in_regalen = $this->app->DB->SelectArr("SELECT * FROM lager_platz_inhalt WHERE artikel='$artikel' AND projekt='$projekt'");

        // standardlager artikel 
        $standardlagerartikel = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$artikel'");
        // Zeige nur Artikel an die im Lager sind!

        $tmp_check_standardlager = $this->app->DB->Select("SELECT SUM(lpi.menge) FROM lager_platz_inhalt lpi LEFT JOIN lager_platz l ON l.id=lpi.lager_platz WHERE 
            lpi.artikel='$artikel' AND lpi.lager_platz='$standardlagerartikel' AND l.autolagersperre!='1'");

        // erst standarlager ausraeumen bis zu wenig drin ist
        // und dann die lager an denene am meisten ist
        if($tmp_check_standardlager>=$gesamtbedarf)
          $artikel_in_regalen = $this->app->DB->SelectArr("SELECT * FROM lager_platz_inhalt lpi LEFT JOIN lager_platz l ON l.id=lpi.lager_platz WHERE 
              lpi.artikel='$artikel' AND lager_platz='$standardlagerartikel' AND l.autolagersperre!='1' ORDER by lpi.menge DESC");
        else
          $artikel_in_regalen = $this->app->DB->SelectArr("SELECT * FROM lager_platz_inhalt lpi LEFT JOIN lager_platz l ON l.id=lpi.lager_platz WHERE 
              lpi.artikel='$artikel' AND l.autolagersperre!='1' ORDER by lpi.menge DESC");


        for ($j = 0;$j < count($artikel_in_regalen);$j++) {
          if(!$getorder){
            $htmltable->NewRow();
          }
          $tmpanzahl++;
          $menge_im_platz = $artikel_in_regalen[$j][menge];
          $kurzbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='{$artikel_in_regalen[$j][lager_platz]}' LIMIT 1");
          $lagerplatzid = $artikel_in_regalen[$j][lager_platz];

          if ($menge_im_platz <= $gesamtbedarf) {
            $tmpmenge = $menge_im_platz;
          } else {
            $tmpmenge = $gesamtbedarf;
          }
          $rest = $menge_im_platz - $tmpmenge; //$this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$artikel' AND firma='".$this->app->User->GetFirma()."'") - $tmpmenge;
          if ($rest == 0) $rest = "-";
          if ($tmpanzahl == 1) $erstes = "erstes";
          else $erstes = "";

          if(!$getorder){
            $htmltable->AddCol($tmpmenge);
            $htmltable->AddCol($nummer);
            $htmltable->AddCol($name_de);
            $htmltable->AddCol($this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$projekt' LIMIT 1"));
            $htmltable->AddCol($kurzbezeichnung);
            //$htmltable->AddCol($rest);


            $htmltable->AddCol("Regal: <input type=\"text\" size=\"10\" id=\"$erstes\" 
                onchange=\"if(!confirm('Artikelnummer $nummer wurde $tmpmenge mal entnommen?')) return false; else window.location.href='index.php?module=lager&action=artikelfuerlieferungen&cmd=$cmd&artikel=$artikel&menge=$tmpmenge&projekt=$projekt&produktion=$produktion&lagerplatzid={$artikel_in_regalen[$j][id]}&lager='+this.value;\">");
            $htmltable->AddCol("<a href=\"#\" onclick=\"if(!confirm('Artikelnummer $nummer wurde $tmpmenge mal entnommen?')) return false; else window.location.href='index.php?module=lager&action=artikelfuerlieferungen&cmd=$cmd&artikel=$artikel&menge=$tmpmenge&projekt=$projekt&produktion=$produktion&lagerplatzid={$artikel_in_regalen[$j][id]}&lager=$lagerplatzid';\"><img src=\"./themes/[THEME]/images/forward.png\"></a>");
            if ($this->app->User->GetType() == "admin") $htmltable->AddCol("<a href=\"#\" onclick=\"if(!confirm('Artikel aus Lieferungen und Reservierungen nehmen?')) return false; else window.location.href='index.php?module=lager&action=artikelentfernen&produktion=$produktion&projekt=$projekt&artikel=$artikel&cmd=$cmd';\"><img src=\"./themes/[THEME]/images/delete.gif\"></a>");
          } else {

            $orderarray[]=array("tmpmenge"=>$tmpmenge,"artikel"=>$artikel,"nummer"=>$nummer,"lager_platz"=>$lagerplatzid,"kurzbezeichnung"=>$kurzbezeichnung);
          }

          $gesamtbedarf = $gesamtbedarf - $tmpmenge;
          if ($gesamtbedarf == 0) break;
        }
      }
      if(!$getorder){
        //bestimme regalplaetze fuer artikel
        $this->app->Tpl->Add(INHALT, $htmltable->Get());
        // und enter abfangen!!!
        $this->app->Tpl->Add(INHALT, "<script type=\"text/javascript\">document.getElementById(\"erstes\").focus(); </script>");
        //$table->DisplayNew(INHALT, "<a href=\"index.php?module=lager&action=bucheneinlagern&cmd=zwischenlager&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/einlagern.png\"></a>");
        $this->app->Tpl->Parse(TAB1, "rahmen70_ohneform.tpl");
        return $gesamtanzahlartikel;
      } else {
        return $orderarray;
      }
    }

    function LagerZwischenlager() {
      $this->app->Tpl->Add(TABS, "<li><h2>Zwischenlager</h2></li>");
      $id = $this->app->Secure->GetGET("id");
      $this->app->Tpl->Set(TABNAME, "Inhalt");
      $this->app->Tpl->Set(SUBSUBHEADING, "Zwischenlager Stand " . date('d.m.Y'));
      // easy table mit arbeitspaketen YUI als template
      $table = new EasyTable($this->app);
      $table->Query("SELECT a.name_de,z.menge,z.vpe,z.grund,z.richtung, p.abkuerzung, z.id FROM zwischenlager z LEFT JOIN artikel a ON a.id=z.artikel LEFT JOIN projekt p ON 
          p.id=z.projekt WHERE z.firma='{$this->app->User->GetFirma() }'");
      $table->DisplayNew(INHALT, "<a href=\"index.php?module=lager&action=bewegungpopup&frame=false&id=%value%\" 
          onclick=\"makeRequest(this);return false\">Info</a>");
      $this->app->Tpl->Parse(TAB1, "rahmen70.tpl");
      $this->app->Tpl->Set(AKTIV_TAB1, "selected");
      $this->app->Tpl->Parse(PAGE, "tabeinzeln.tpl");
    }
    function LagerBewegung() {
      $this->LagerMenu();
      $id = $this->app->Secure->GetGET("id");
      $this->app->Tpl->Set(TABNAME, "Lager Bewegungen");
      $lager = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id='$id' ");
      $this->app->Tpl->Set(SUBSUBHEADING, "Bewegungen Lager: $lager bis zum " . date('d.m.Y'));
      // easy table mit arbeitspaketen YUI als template
      $table = new EasyTable($this->app);
      $table->Query("SELECT p.kurzbezeichnung as Lagerplatz, 
          p.id FROM lager_platz p 
          WHERE lager='$id' ORDER by 1");
      $table->DisplayNew(INHALT, "<a href=\"index.php?module=lager&action=bewegungpopup&frame=false&id=%value%\" 
          onclick=\"makeRequest(this);return false\">Info</a>");
      $this->app->Tpl->Parse(TAB1, "rahmen70.tpl");
      $this->app->Tpl->Set(AKTIV_TAB1, "selected");
      $this->app->Tpl->Parse(PAGE, "tabeinzeln.tpl");
    }
    function LagerBewegungPopup() {
      $id = $this->app->Secure->GetGET("id");

      $lager = $this->app->DB->Select("SELECT lager FROM lager_platz WHERE id='$id'");

      $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Lager Bewegungen");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=bewegung&id=$lager", "Zur&uuml;ck zur &Uuml;bersicht");

      $id = $this->app->Secure->GetGET("id");
      $this->app->Tpl->Set(TABNAME, "Lager Bewegungen");
      $lager = $this->app->DB->Select("SELECT l.bezeichnung FROM lager_platz p LEFT JOIN lager l ON p.lager=l.id WHERE p.id='$id'");
      $platz = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz p WHERE id='$id'");
      $this->app->Tpl->Set(SUBSUBHEADING, "Bewegungen Lager: $lager, Platz: $platz bis zum " . date('d.m.Y'));
      // easy table mit arbeitspaketen YUI als template
      $table = new EasyTable($this->app);
      $table->Query("SELECT p.kurzbezeichnung as Lagerplatz, a.nummer, a.name_de, i.menge, if(i.eingang,'Eingang','Ausgang') as Richtung, DATE_FORMAT(i.zeit,'%d.%m.%Y') as datum, i.referenz,
          i.id FROM lager_bewegung i LEFT JOIN lager_platz p ON p.id=i.lager_platz LEFT JOIN artikel a ON i.artikel=a.id
          WHERE p.id='$id' Order by i.zeit DESC");
      $table->DisplayNew(TAB1, "<a href=\"index.php?module=lager&action=platzeditpopup&frame=false&id=%value%\" 
          onclick=\"makeRequest(this);return false\">Info</a>");
      //$this->app->Tpl->Parse(PAGE, "rahmen70.tpl");
      $this->app->Tpl->Parse(PAGE, "tabeinzeln.tpl");
    }

    function LagerInhalt() {

      $this->LagerMenu();

      $id = $this->app->Secure->GetGET("id");
      $msg = $this->app->Secure->GetGET("msg");


      $this->app->Tpl->Set(TABNAME, "Lager Inventur-Liste");
      $lager = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id='$id' ");

      $this->app->Tpl->Set(LAGERNAME, $lager);
      $this->app->Tpl->Set(ID, $id);
      $this->app->Tpl->Set(KURZUEBERSCHRIFT2, "$lager (Stand " . date('d.m.Y').")");
      $this->app->Tpl->Set(STAND, date('d.m.Y'));

      $this->app->YUI->AutoComplete('regal','lagerplatz');

      $regal = $this->app->Secure->GetPOST("regal");

      $table = new EasyTable($this->app);


      // wenn regal angeben dies als lager_platz nutzen
      if(is_numeric($regal))
        $lager_platz = $regal;
      else {
        if($regal!="")
          $lager_platz = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$regal' ");
      }

      //Hauptlager platz id
      $lager_platz_get = $this->app->Secure->GetGET("lager_platz");
      if($lager_platz_get!="")
      {
        $regal = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$lager_platz_get'");
        $lager_platz = $lager_platz_get;
      }

      $this->app->Tpl->Set(LAGERPLATZID, $lager_platz);
      $this->app->Tpl->Set(LAGERPLATZID, $lager_platz);

      $this->app->Tpl->Set(REGAL, $regal);


      $lager_platz_inhalt = $this->app->Secure->GetPOST("lager_platz_inhalt");


      if($lager_platz > 0)
      {

        $this->app->Tpl->Set(LAGERPLATZ,$regal);
        $table->Query("SELECT p.kurzbezeichnung as lagerplatz, 
            LEFT(a.name_de,50) as artikel,
            a.nummer as nummer, pro.abkuerzung as projekt,
            i.menge as menge, SUM(r.menge) as reserviert
            FROM lager_platz p LEFT JOIN lager_platz_inhalt i ON p.id=i.lager_platz LEFT JOIN artikel a ON i.artikel=a.id
            LEFT JOIN lager_reserviert r ON r.artikel=a.id LEFT JOIN projekt pro ON pro.id=a.projekt
            WHERE p.id='$lager_platz' GROUP by p.kurzbezeichnung,a.nummer");
      } else {
        $table->Query("SELECT p.kurzbezeichnung as lagerplatz, 

            LEFT(a.name_de,50) as artikel,
            a.nummer as nummer, pro.abkuerzung as projekt,
            i.menge as menge, SUM(r.menge) as reserviert
            FROM lager_platz p LEFT JOIN lager_platz_inhalt i ON p.id=i.lager_platz LEFT JOIN artikel a ON i.artikel=a.id
            LEFT JOIN lager_reserviert r ON r.artikel=a.id LEFT JOIN projekt pro ON pro.id=a.projekt
            WHERE lager='$id' GROUP by p.kurzbezeichnung,a.nummer");
      }

      $table->DisplayNew(TAB1, "Reserviert","noAction");//<a href=\"index.php?module=artikel&action=lager&id=%value%\" target=\"_blank\">Lagerbestand</a>");
      // $this->app->Tpl->Parse(TAB1, "rahmen70.tpl");
      // $this->app->Tpl->Set(AKTIV_TAB1, "selected");
      // $this->app->Tpl->Parse(PAGE, "tabeinzeln.tpl");

      if($regal=="")
        $this->app->Tpl->Parse(PAGE, "lager_inhalt.tpl");
      else {
        $this->app->Tpl->Parse(PAGE, "lager_inhalt_regal.tpl");
      }


    }

    function LagerBestand() {

      $this->LagerMenu();

      $id = $this->app->Secure->GetGET("id");
      $msg = $this->app->Secure->GetGET("msg");


      // alle lerren inventur Zeilen loeschen
      $this->app->DB->Delete("DELETE FROM lager_platz_inhalt WHERE menge<=0 AND (inventur<=0 OR inventur IS NULL)");

      //Menu ausblenden wenn keine Rechte
      if(!$this->app->erp->RechteVorhanden("lager","inventur"))
      {
        $this->app->Tpl->Set(PERMISSIONINVENTURSTART,"<!--");
        $this->app->Tpl->Set(PERMISSIONINVENTURENDE,"//-->");
      }


      $inventurspeichern = $this->app->Secure->GetPOST("inventurspeichern");

      if($inventurspeichern!="")
        $this->app->Tpl->Set(MESSAGE,"");

      $this->app->Tpl->Set(TABNAME, "Lager Inventur-Liste");
      $lager = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id='$id' ");

      $this->app->Tpl->Set(LAGERNAME, $lager);
      $this->app->Tpl->Set(ID, $id);
      $this->app->Tpl->Set(KURZUEBERSCHRIFT2, "$lager (Stand " . date('d.m.Y').")");
      $this->app->Tpl->Set(STAND, date('d.m.Y'));

      $this->app->YUI->AutoComplete('regal','lagerplatz');
      $this->app->YUI->AutoComplete('artikel','lagerartikelnummer',1);

      $regal = $this->app->Secure->GetPOST("regal");

      $table = new EasyTable($this->app);


      // wenn regal angeben dies als lager_platz nutzen
      if(is_numeric($regal))
        $lager_platz = $regal;
      else {
        if($regal!="")
          $lager_platz = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$regal' ");
      }

      //Hauptlager platz id
      $lager_platz_get = $this->app->Secure->GetGET("lager_platz");
      if($lager_platz_get!="")
      {
        $regal = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$lager_platz_get'");
        $lager_platz = $lager_platz_get;
      }

      $artikelbuchen = $this->app->Secure->GetPOST("artikelbuchen");

      if($artikelbuchen!="")
      {
        $artikel = $this->app->Secure->GetPOST("artikel");
        if($artikel=="back")
        {
          header("Location: index.php?module=lager&action=bestand&id=$id");
          exit;
        }

        // pruefe ob es artikel in lager gibt // wenn nicht lege artikel neu in lager an 
        //echo $artikel;
        $artikelnummer = $this->app->erp->FirstTillSpace($artikel);
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$artikelnummer' AND geloescht!=1 LIMIT 1");

        $check = $this->app->DB->Select("SELECT COUNT(id) FROM lager_platz_inhalt WHERE lager_platz='$lager_platz' AND artikel='$artikelid'");

        if($artikelid<=0)
        {
          $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Artikel unbekannt! Bitte anderen scannen!</div>");
        }
        else if($check > 0)
        {
          $this->app->DB->Update("UPDATE lager_platz_inhalt SET inventur=if(inventur IS NULL,1,inventur+1) WHERE
              lager_platz='$lager_platz' AND artikel='$artikelid' LIMIT 1");

        } else {
          $this->app->DB->Insert("INSERT INTO lager_platz_inhalt (id,lager_platz,artikel,inventur) VALUES
              ('','$lager_platz','$artikelid','1')");

          $this->app->Tpl->Set(MESSAGE,"<div class=\"info\">Artikel ist aktuell nicht in diesem Lager wird aber neu eingelagert!</div>");
        }

        //			echo "Laherplatz ".$lager_platz;	
      }




      $this->app->Tpl->Set(LAGERPLATZID, $lager_platz);
      $this->app->Tpl->Set(LAGERPLATZID, $lager_platz);

      $this->app->Tpl->Set(REGAL, $regal);

      if($this->app->erp->RechteVorhanden("lager","inventurabschluss"))
        $this->app->Tpl->Set(ABSCHLUSS,'<input type="submit" value="Inventur zur&uuml;cksetzten" name="reset">&nbsp;
            <input type="submit" value="Inventur jetzt abschlie&szlig;en" name="abschluss">');

      $lager_platz_inhalt = $this->app->Secure->GetPOST("lager_platz_inhalt");

      if($inventurspeichern!="")
      {
        if(is_array($lager_platz_inhalt))
        {
          foreach($lager_platz_inhalt as $key=>$menge)
          {
            if(is_numeric($menge))
              $this->app->DB->Update("UPDATE lager_platz_inhalt SET inventur='$menge' WHERE id='$key' LIMIT 1");
            else
              $this->app->DB->Update("UPDATE lager_platz_inhalt SET inventur=NULL WHERE id='$key' LIMIT 1");
          }
        }

      }


      if($lager_platz > 0)
      {

        $this->app->Tpl->Set(LAGERPLATZ,$regal);
        $table->Query("SELECT p.kurzbezeichnung as lagerplatz, 
            if(i.inventur IS NULL, CONCAT('<font color=red>',a.name_de,'</font>'),a.name_de) as artikel,
            if(i.inventur IS NULL, CONCAT('<font color=red>',a.nummer,'</font>'),a.nummer) as nummer,
            i.menge as menge, SUM(r.menge) as reserviert,
            CONCAT('<input type=\"text\" name=\"lager_platz_inhalt[',i.id,']\" value=\"',if(i.inventur IS NULL,'',i.inventur),'\" size=\"8\"') as inventur
            FROM lager_platz p LEFT JOIN lager_platz_inhalt i ON p.id=i.lager_platz LEFT JOIN artikel a ON i.artikel=a.id
            LEFT JOIN lager_reserviert r ON r.artikel=a.id
            WHERE p.id='$lager_platz' GROUP by p.kurzbezeichnung,a.nummer");
      } else {
        $table->Query("SELECT p.kurzbezeichnung as lagerplatz, 

            CONCAT('<a href=\"index.php?module=artikel&action=lager&id=',a.id,'\" target=\"_blank\">',
              if(i.inventur IS NULL, CONCAT('<font color=red>',a.name_de,'</font>'),
                if(i.inventur!=i.menge,CONCAT('<font color=blue>',a.name_de,'</font>'),a.name_de)
                )
              ,'</a>') as artikel,


            CONCAT('<a href=\"index.php?module=artikel&action=lager&id=',a.id,'\" target=\"_blank\">',
              if(i.inventur IS NULL, CONCAT('<font color=red>',a.nummer,'</font>'),
                if(i.inventur!=i.menge,CONCAT('<font color=blue>',a.nummer,'</font>'),a.nummer)
                )
              ,'</a>') as nummer,

            i.menge as menge, SUM(r.menge) as reserviert, 
            CONCAT('<input type=\"text\" name=\"lager_platz_inhalt[',i.id,']\" value=\"',if(i.inventur IS NULL,'',i.inventur),'\" size=\"8\"') as inventur
            FROM lager_platz p LEFT JOIN lager_platz_inhalt i ON p.id=i.lager_platz LEFT JOIN artikel a ON i.artikel=a.id
            LEFT JOIN lager_reserviert r ON r.artikel=a.id
            WHERE lager='$id' GROUP by p.kurzbezeichnung,a.nummer");
      }

      $table->DisplayNew(TAB1, "Inventur","noAction");//<a href=\"index.php?module=artikel&action=lager&id=%value%\" target=\"_blank\">Lagerbestand</a>");
      // $this->app->Tpl->Parse(TAB1, "rahmen70.tpl");
      // $this->app->Tpl->Set(AKTIV_TAB1, "selected");
      // $this->app->Tpl->Parse(PAGE, "tabeinzeln.tpl");

      if($regal=="")
        $this->app->Tpl->Parse(PAGE, "lager_regal_suchen_inventur.tpl");
      else {
        $this->app->Tpl->Parse(PAGE, "lager_regal_artikel_inventur.tpl");
      }


    }
    function LagerInventurLaden()
    {
      $id = $this->app->Secure->GetGET("id");
      $tmp = $this->app->DB->SelectArr("SELECT lpi.artikel, lpi.id, lpi.lager_platz,lpi.menge,lpi.inventur 
          FROM lager_platz_inhalt lpi 
          LEFT JOIN lager_platz lp ON lp.id=lpi.lager_platz WHERE lp.lager='$id'");

      for($i=0;$i<count($tmp);$i++)
      {
        $tmpid = $tmp[$i][id];

        $this->app->DB->Update("UPDATE lager_platz_inhalt SET inventur=menge
            WHERE id='$tmpid' LIMIT 1");
      }
      header("Location: index.php?module=lager&action=bestand&id=$id");
      exit;
    }


    function LagerInventur() {

      $cmd = $this->app->Secure->GetGET("cmd");

      //   $this->LagerMenu();
      $id = $this->app->Secure->GetGET("id");
      //    $this->app->Tpl->Set(TABNAME, "Lager Inventur");
      //    $this->app->Tpl->Set(SUBSUBHEADING, "Inventur " . date('d.m.Y'));

      switch($cmd)
      {

        case "einfrieren":
          // aktuelles lager mit allen regalen
          $tmp = $this->app->DB->SelectArr("SELECT lpi.artikel, lpi.id, lpi.lager_platz,lpi.menge,lpi.inventur 
              FROM lager_platz_inhalt lpi 
              LEFT JOIN lager_platz lp ON lp.id=lpi.lager_platz WHERE lp.lager='$id'");

          for($i=0;$i<count($tmp);$i++)
          {

            $artikel = $tmp[$i][artikel];
            $regal = $tmp[$i][lager_platz];
            $menge = $tmp[$i][menge];
            $menge_inventur = $tmp[$i][inventur];
            $tmpid = $tmp[$i][id];

            $referenz = "Inventur am ".date('d.m.Y')." Menge alt: ".$menge." neu: ".$menge_inventur;
            // INSERT 
            if($menge >=0 && $menge_inventur > 0)
            {
              $this->app->DB->Update("UPDATE lager_platz_inhalt SET menge=inventur, inventur=NULL
                  WHERE id='$tmpid' LIMIT 1");
            } else if ($menge >=0 && $menge_inventur <=0)	
            {
              $this->app->DB->Delete("DELETE FROM lager_platz_inhalt WHERE id='$tmpid' LIMIT 1");
            }
            else {
              $this->app->DB->Insert("INSERT INTO lager_platz_inhalt (id,lager_platz,artikel,menge,firma,
                logdatei,inventur)
                  VALUES ('','$regal','$artikel','$menge','".$this->app->User->GetFirma()."',NOW(),NULL)");
            }

            // Bewegung
            $this->app->DB->Insert("INSERT INTO lager_bewegung (id,lager_platz, 
              artikel, menge,vpe, eingang,zeit,referenz, bearbeiter,projekt,firma,logdatei)
                VALUES('','$regal','$artikel','$menge','$vpe','1',NOW(),
                  '$referenz','".$this->app->User->GetName()."','$projekt',
                  '".$this->app->User->GetFirma()."',NOW())");
          }



          $tmp = $this->app->DB->SelectArr("SELECT lpi.artikel as artikel, SUM(lpi.menge) as menge FROM lager_platz_inhalt lpi 
              LEFT JOIN lager_platz lp ON lp.id=lpi.lager_platz WHERE 
              lp.lager='$id' GROUP BY lpi.artikel");


          if(count($tmp)>0)
          {
            $inventur = $this->app->erp->CreateInventur();

            for($i=0;$i<count($tmp);$i++)
            {
              $artikel = $tmp[$i][artikel];
              $menge = $tmp[$i][menge];
              $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel' LIMIT 1");
              $bezeichnung = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");
              $sort = $i+1;

              $preis = $this->app->erp->GetEinkaufspreis($artikel,$menge);

              $this->app->DB->Insert("INSERT INTO inventur_position (id,inventur,artikel,
                nummer,projekt,bezeichnung,menge,sort,preis,logdatei) VALUES 
                  ('','$inventur','$artikel','$nummer','$projekt','$bezeichnung','$menge','$sort',
                   '$preis',NOW())");
            }

            $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Die Inventur wurde durchgef&uuml;hrt und automatisch gespeichert.</div>");
            header("Location: index.php?module=inventur&action=edit&id=$inventur&msg=$msg");
            exit;
          } else {
            $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Es kann keine Inventur angelegt werden, 
                da keine Artikel in diesem Lager vorhanden sind.</div>");
            header("Location: index.php?module=lager&action=bestand&id=$id&msg=$msg");
            exit;
          }
          break;

        case "resetlagerplatz":
          $lager_platz_id_tmp = $this->app->Secure->GetGET("lager_platz");
          if($lager_platz_id_tmp>0)
          {
            $this->app->DB->Update("UPDATE lager_platz_inhalt SET inventur=NULL WHERE lager_platz='$lager_platz_id_tmp'");
            $this->app->DB->Delete("DELETE FROM lager_platz_inhalt WHERE lager_platz='$lager_platz_id_tmp' AND menge <= 0 
                AND (inventur<=0 OR inventur IS NULL)");
          }
          $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Die Inventur wurde zur&uuml;ckgesetzt.</div>");
          header("LOcation: index.php?module=lager&action=bestand&id=$id&msg=$msg&lager_platz=$lager_platz_id_tmp");
          exit;
          break;

        case "resetalle":
          // Hole alle regal_plaetze
          $tmp = $this->app->DB->SelectArr("SELECT id FROM lager_platz WHERE lager='$id'");
          for($i=0;$i<count($tmp);$i++)
          {
            $lager_platz_id_tmp = $tmp[$i][id];	
            if($lager_platz_id_tmp>0)
            {
              $this->app->DB->Update("UPDATE lager_platz_inhalt SET inventur=NULL WHERE lager_platz='$lager_platz_id_tmp'");
              $this->app->DB->Delete("DELETE FROM lager_platz_inhalt WHERE lager_platz='$lager_platz_id_tmp' AND menge <= 0 
                  AND (inventur<=0 OR inventur IS NULL)");
            }
          }
          $msg = $this->app->erp->base64_url_encode("<div class=\"error2\">Die Inventur wurde zur&uuml;ckgesetzt.</div>");
          header("LOcation: index.php?module=lager&action=bestand&id=$id&msg=$msg");
          exit;
          break;
      }

      /*

      // easy table mit arbeitspaketen YUI als template
      $table = new EasyTable($this->app);
      $table->Query("SELECT p.kurzbezeichnung, a.nummer, i.menge, '<input type=text size=4>' as Inventur,
      i.id FROM lager_platz p LEFT JOIN lager_platz_inhalt i ON p.id=i.lager_platz LEFT JOIN artikel a ON i.artikel=a.id
      LEFT JOIN artikel_reserviert r ON r.artikel=a.id
      WHERE lager='$id'");
      $table->DisplayNew(INHALT, "<a href=\"index.php?module=lager&action=platzeditpopup&frame=false&id=%value%\" 
      onclick=\"makeRequest(this);return false\">Info</a>");

      $this->app->Tpl->Parse(TAB1, "rahmen_submit.tpl");
      $this->app->Tpl->Set(AKTIV_TAB1, "selected");
       */
      $this->app->Tpl->Parse(PAGE, "tabeinzeln.tpl");
    }

    function LagerPlatz() {
      $this->LagerMenu();
      $id = $this->app->Secure->GetGET("id");
      // neues arbeitspaket


      $import = $this->app->Secure->GetPOST("import");
      if($import!="")
      {
        $lagerimport = $this->app->Secure->GetPOST("lagerimport");

        $lagerimport  = str_replace('\\r\\n',"\r\n",$lagerimport);
        $lagerimport = str_replace('"','',$lagerimport);
        $tmp = split(',',$lagerimport);
        $neue=0;
        for($i=0;$i<count($tmp);$i++)
        {
          $lagerabkuerzung = $tmp[$i];
          // new line + spaces entfernen
          $lagerabkuerzung = trim(preg_replace('/\s+/', ' ', $lagerabkuerzung));

          $check = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$lagerabkuerzung' LIMIT 1");
          if($check <= 0)
          {
            // Anlegen
            $this->app->erp->CreateLagerplatz($id,$lagerabkuerzung);
            $neue++;
          }
        }

        $this->app->Tpl->Set(IMPORT,$lagerimport);

        if(count($tmp) > 0)
        {
          if($neue == 1)
            $this->app->Tpl->Set(MESSAGE3,"<div class=\"error2\">$neue Lagerplatz wurde neu angelegt!</div>");
          else if($neue > 1)
            $this->app->Tpl->Set(MESSAGE3,"<div class=\"error2\">$neue Lagerpl&auml;tze wurden neu angelegt!</div>");
          else
            $this->app->Tpl->Set(MESSAGE3,"<div class=\"error2\">Keine neuen Lagerpl&auml;tze angelegt! Alle bereits gefunden.</div>");
        }
        else
          $this->app->Tpl->Set(MESSAGE3,"<div class=\"error\">Es wurden keine Lagerpl&auml;tze angegeben!</div>");
      } 

      $speichern = $this->app->Secure->GetPOST("speichern");

      if($speichern!="")
      {

        $kurzbezeichnung = $this->app->Secure->GetPOST("kurzbezeichnung");
        $autolagersperre=$this->app->Secure->GetPOST("autolagersperre");
        $verbrauchslager=$this->app->Secure->GetPOST("verbrauchslager");
        $sperrlager=$this->app->Secure->GetPOST("sperrlager");

        $allowed = "/[^a-z0-9A-Z]/i";
        $kurzbezeichnung = preg_replace($allowed,"",$kurzbezeichnung);
        $kurzbezeichnung =  substr($kurzbezeichnung,0,15);



        $check = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$kurzbezeichnung' AND kurzbezeichnung!='' LIMIT 1");
        if($check<=0 && $kurzbezeichnung!="")
        {
          $this->app->DB->Insert("INSERT INTO lager_platz (id,lager,kurzbezeichnung,autolagersperre,verbrauchslager,sperrlager)
            VALUES ('','$id','$kurzbezeichnung','$autolagersperre','$verbrauchslager','$sperrlager')");

          $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Lagerplatz wurde angelegt!</div>");
          header("Location: index.php?module=lager&action=platz&id=$id&msg=$msg");
          exit;
        } else {
          if($kurzbezeichnung=="")
            $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Der Lagerplatz wurde nicht angelegt! Bitte geben Sie einen Namen an!</div>");
          else
            $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Der Lagerplatz wurde nicht angelegt! Der Name existiert bereits in diesem oder einem anderem Lager. 
                Bitte einen anderen w&auml;hlen!</div>");
        }
      }

      $this->app->Tpl->Set(KURZBEZEICHNUNG,$kurzbezeichnung);
      if($autolagersperre=="1") $this->app->Tpl->Set(AUTOLAGERSPERRE,"checked");
      if($verbrauchslager=="1") $this->app->Tpl->Set(VERBRAUCHSLAGER,"checked");
      if($sperrlager=="1") $this->app->Tpl->Set(SPERRLAGER,"checked");
      $this->app->Tpl->Parse(TAB2, "lager_platz.tpl");


      $this->app->Tpl->Set(SUBSUBHEADING, "Lagerpl&auml;tze");

      $this->app->YUI->TableSearch(TAB1, "lagerplatztabelle");

      $this->app->Tpl->Parse(PAGE, "lagerplatzuebersicht.tpl");
    }
    function LagerPlatzEditPopup() {
      $frame = $this->app->Secure->GetGET("frame");
      $id = $this->app->Secure->GetGET("id");
      // nach page inhalt des dialogs ausgeben
//      $widget = new WidgetLager_platz($this->app,TAB1);
      $sid = $this->app->DB->Select("SELECT lager FROM lager_platz WHERE id='$id' LIMIT 1");

//      $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Lagerplatz bearbeiten");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=platz&id=$sid","zur&uuml;ck zur &Uuml;bersicht");

      $this->app->Tpl->Set(ABBRECHEN,"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=lager&action=platz&id=$sid';\">");
//      $widget->form->SpecialActionAfterExecute("close_refresh", "index.php?module=lager&action=platz&id=$sid");
//      $widget->Edit();

       $speichern = $this->app->Secure->GetPOST("speichern");

      if($speichern!="")
      {

        $kurzbezeichnung = $this->app->Secure->GetPOST("kurzbezeichnung");
        $autolagersperre=$this->app->Secure->GetPOST("autolagersperre");
        $verbrauchslager=$this->app->Secure->GetPOST("verbrauchslager");
        $sperrlager=$this->app->Secure->GetPOST("sperrlager");

        $allowed = "/[^a-z0-9A-Z]/i";
        $kurzbezeichnung = preg_replace($allowed,"",$kurzbezeichnung);
        $kurzbezeichnung =  substr($kurzbezeichnung,0,15);

        $check = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$kurzbezeichnung' AND kurzbezeichnung!='' AND id!='$id' LIMIT 1");
        if($check<=0 && $kurzbezeichnung!="")
        {

          $this->app->DB->Insert("UPDATE lager_platz 
            SET kurzbezeichnung='$kurzbezeichnung',autolagersperre='$autolagersperre',verbrauchslager='$verbrauchslager',sperrlager='$sperrlager' WHERE id='$id' LIMIT 1");

          $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Der Lagerplatz wurde ge&auml;ndert!</div>");
          header("Location: index.php?module=lager&action=platz&id=$sid&msg=$msg");
          exit;
        } else {
          if($kurzbezeichnung=="")
            $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Der Lagerplatz wurde nicht ge&auml;ndert! Bitte geben Sie einen Namen an!</div>");
          else
            $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Der Lagerplatz wurde nicht ge&auml;ndert! Der Name existiert in diesem oder einen anderem Lager bereits. Bitte einen anderen w&auml;hlen!</div>");
        }
        $this->app->Tpl->Set(KURZBEZEICHNUNG,$kurzbezeichnung);
        if($autolagersperre=="1") $this->app->Tpl->Set(AUTOLAGERSPERRE,"checked");
        if($verbrauchslager=="1") $this->app->Tpl->Set(VERBRAUCHSLAGER,"checked");
        if($sperrlager=="1") $this->app->Tpl->Set(SPERRLAGER,"checked");

      } else {

        $tmp = $this->app->DB->SelectArr("SELECT * FROM lager_platz WHERE id='$id' LIMIT 1");
        $kurzbezeichnung = $tmp[0]['kurzbezeichnung'];
        $autolagersperre = $tmp[0]['autolagersperre'];
        $verbrauchslager = $tmp[0]['verbrauchslager'];
        $sperrlager = $tmp[0]['sperrlager'];

        $this->app->Tpl->Set(KURZBEZEICHNUNG,$kurzbezeichnung);
        if($autolagersperre=="1") $this->app->Tpl->Set(AUTOLAGERSPERRE,"checked");
        if($verbrauchslager=="1") $this->app->Tpl->Set(VERBRAUCHSLAGER,"checked");
        if($sperrlager=="1") $this->app->Tpl->Set(SPERRLAGER,"checked");
      }

      $this->app->Tpl->Parse(TAB1, "lager_platz.tpl");
 
      $this->app->Tpl->Set(TABNAME, "Lagerplatz");
      $this->app->Tpl->Parse(PAGE, "tabview.tpl");
    }

    function LagerCreate() {

      $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Hauptlager anlegen");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
      parent::LagerCreate();
    }

    function LagerHauptmenu() {
      //    $this->app->Tpl->Add(KURZUEBERSCHRIFT,"Lager&uuml;bersicht");
      //    $this->app->erp->MenuEintrag("index.php?module=lager&action=create","Neues Lager anlegen");
      //parent::LagerList();
      //$this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\">Allgemein</h2></li>");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=list", "&Uuml;bersicht");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=create", "Neues Hauptlager anlegen");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=wert", "Lagerbestandsberechnung");

      if($this->app->erp->Version()!="stock")
      {
        $this->app->erp->MenuEintrag("index.php?module=lager&action=nachschublager", "Nachschublager Liste");
        $this->app->erp->MenuEintrag("index.php?module=lager&action=differenzen", "Lager Differenzen");
        //$this->app->erp->MenuEintrag("index.php?module=artikel&action=lagerlampe", "Lagerlampen");
      }
      $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Lagerverwaltung");
    }


    function LagerList() {
      //    $this->app->Tpl->Add(KURZUEBERSCHRIFT,"Lager&uuml;bersicht");
      //    $this->app->erp->MenuEintrag("index.php?module=lager&action=create","Neues Lager anlegen");
      //parent::LagerList();
      //$this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\">Allgemein</h2></li>");
      $this->LagerHauptmenu();

      $this->app->YUI->TableSearch(TAB1, "lagertabelle");
      $this->app->Tpl->Parse(PAGE, "lageruebersicht.tpl");
    }

    function LagerNachschublager()
    {
      $this->LagerHauptmenu();
      //$this->app->erp->MenuEintrag("index.php?module=lager&action=list", "&Uuml;bersicht");
      //$this->app->erp->MenuEintrag("index.php?module=lager&action=create", "Neues Hauptlager anlegen");

      if($this->app->erp->Version()!="stock")
      {
        //$this->app->erp->MenuEintrag("index.php?module=lager&action=nachschublager", "Nachschublager Liste");
        //$this->app->erp->MenuEintrag("index.php?module=artikel&action=lagerlampe", "Lagerlampen");
      }
      //$this->app->Tpl->Add(TABS,"<li><a  href=\"index.php\">Zur&uuml;ck zur &Uuml;bersicht</a></li>");
      $this->app->Tpl->Set(UEBERSCHRIFT, "Lagerverwaltung");
      $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Lagerverwaltung");

      $artikel = $this->app->DB->SelectArr("SELECT ap.artikel as artikel,SUM(ap.menge) as menge 
          FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag WHERE a.status='freigegeben' AND a.nachnahme_ok='1' AND
          a.vorkasse_ok=1 AND a.porto_ok=1 AND a.ust_ok=1 AND a.autoversand=1 AND a.kreditlimit_ok=1
          AND a.liefersperre_ok=1 AND a.liefertermin_ok=1 GROUP By ap.artikel");

      $output = "<table border=1 class=tableborder width=100%><tr><td><b>Artikel-Nr.</b></td><td><b>Artikel</b></td>
        <td width=200><b>Nachschublager</b></td>
        <td width=200><b>Lagervorschlag</b></td><td align=right><b>Umzulagernde Menge</b></td></tr>";

      for($i=0;$i<count($artikel);$i++)
      {
        $artikelid = $artikel[$i][artikel];
        $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikelid' LIMIT 1");
        $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikelid' LIMIT 1");
        $soll = $artikel[$i][menge];
        $lager =  $this->app->erp->ArtikelAnzahlLagerOhneNachschublager($artikelid);

        $gebraucht = $soll - $lager;
        if($gebraucht > 0)
        {
          // pruefe ob genug im nachschublager sind 
          if($this->app->erp->ArtikelAnzahlLagerNurNachschublager($artikelid) > 0)
          {
            // ganze menge holen so lange es geht ansonst den rest
            // alle lager mit dem artikel im nachschublager

            $nachschublager_arr = $this->app->DB->SelectArr("SELECT SUM(lpi.menge) as menge, lpi.lager_platz as lager_platz 
                FROM lager_platz_inhalt lpi LEFT JOIN lager_platz lp ON lp.id=lpi.lager_platz
                WHERE lpi.artikel='$artikelid' AND lp.autolagersperre='1' GROUP by lp.id");

            for($j=0;$j < count($nachschublager_arr);$j++)
            {
              $lager_auslagern_bezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='".$nachschublager_arr[$j][lager_platz]."' LIMIT 1");

              $lager_einlagern_bezeichnung = $this->app->DB->Select("SELECT lp.kurzbezeichnung FROM lager_platz lp LEFT JOIN lager_platz_inhalt lpi
                  WHERE lpi.artikel='".$artikelid."' AND lp.autolagersperre!='1' LIMIT 1");

              if($lager_einlagern_bezeichnung=="")
              {
                $standardlagerartikel=$this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='".$artikelid."' LIMIT 1");
                $lager_einlagern_bezeichnung = $this->app->DB->Select("SELECT lp.kurzbezeichnung FROM lager_platz lp WHERE lp.id='".$standardlagerartikel."' LIMIT 1");
              }
              else {
                $standardlagerartikel="";
              }
              if($lager_einlagern_bezeichnung=="") $lager_einlagern_bezeichnung="freie Platzwahl";

              $alle_nachschublager = $this->app->DB->SelectArr("SELECT lp.kurzbezeichnung as name,lpi.menge as menge
                  FROM lager_platz_inhalt lpi LEFT JOIN lager_platz lp ON lp.id=lpi.lager_platz WHERE lpi.artikel='".$artikelid."' AND lp.autolagersperre='1'");
              $nachschublager_string="";

              for($allei=0;$allei<count($alle_nachschublager);$allei++)
                $nachschublager_string .= $alle_nachschublager[$allei]['name']." <br><i>-Lagerbestand ".$alle_nachschublager[$allei]['menge']."</i><br>";

              // komplett alles
              if($gebraucht >= $nachschublager_arr[$j][menge] && $gebraucht > 0)
              {

                $output .= "<tr><td>".$nummer."</td><td>".$name_de."</td><td>".$nachschublager_string."</td>
                  <td>".$lager_einlagern_bezeichnung." <br><i>-Lagerbestand ".$this->app->erp->ArtikelAnzahlLagerPlatz($artikelid,$standardlagerartikel)."</i></td><td align=right>".$nachschublager_arr[$j][menge]."</td></tr>";
                $gebraucht = $gebraucht - $nachschublager_arr[$j][menge];
              } else if ( $gebraucht <= $nachschublager_arr[$j][menge] && $gebraucht > 0)
              {
                // weniger als im lager ist
                $output .= "<tr><td>".$nummer."</td><td>".$name_de."</td><td>".$nachschublager_string."</td>
                  <td>".$lager_einlagern_bezeichnung." <br><i>-Lagerbestand ".$this->app->erp->ArtikelAnzahlLagerPlatz($artikelid,$standardlagerartikel)."</i></td><td align=right>".$gebraucht."</td></tr>";
                $gebraucht = 0;
              } else {
                // kein bedarf mehr
              }
            }
          }
        }
      }
      $output .="</table>";
      $this->app->Tpl->Set(INHALT, $output);
      $this->app->Tpl->Set(TABTEXT, "Nachschublager");
      $this->app->Tpl->Parse(TAB1, "rahmen70.tpl");
      $this->app->Tpl->Set(AKTIV_TAB1, "selected");
      $this->app->Tpl->Parse(PAGE, "tabview.tpl");
    }

    function LagerMenu() {
      $id = $this->app->Secure->GetGET("id");
      $this->app->Tpl->Set(KURZUEBERSCHRIFT, "Hauptlager");

      $bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id='$id' LIMIT 1");

      $this->app->Tpl->Set(KURZUEBERSCHRIFT2, $bezeichnung);

      $this->app->erp->MenuEintrag("index.php?module=lager&action=platz&id=$id", "Lagerpl&auml;tze");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=inhalt&id=$id", "Bestand");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=bewegung&id=$id", "Bewegungen");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=bestand&id=$id", "Inventur");
      //    $this->app->erp->MenuEintrag("index.php?module=lager&action=inventur&id=$id", "Inventur");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=edit&id=$id", "Einstellungen");
      $this->app->erp->MenuEintrag("index.php?module=lager&action=list", "Zur&uuml;ck zur &Uuml;bersicht");
    }
    function LagerEdit() {
      //$this->app->Tpl->Set(STEUERSATZOPTIONS,$this->app->erp->GetSelect($this->app->erp->GetSteuersatz(),$steuersatz);
      // aktiviere tab 1
      $this->app->Tpl->Set(AKTIV_TAB1, "selected");
      $this->app->Tpl->Set(ABBRECHEN,"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=lager&action=list';\">");
      parent::LagerEdit();
      $this->LagerMenu();
    }
    function LagerEtiketten() {
      $id = $this->app->Secure->GetGET("id");
      $this->LagerMenu();
      $this->app->Tpl->Set(PAGE, "<br><br><br>Etiketten");
      /*
         $barcode = $this->app->DB->Select("SELECT barcode FROM lager WHERE id='{$id}' LIMIT 1");
         $nummer = $this->app->DB->Select("SELECT nummer FROM lager WHERE id='{$id}' LIMIT 1");

         $tmp = new etiketten(&$app);
         $tmp->Lager($barcode,$nummer,65);
         $tmp->Druck();
         exit;
       */
    }
}
?>
