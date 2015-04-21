<?php
include ("_gen/artikel.php");

class Artikel extends GenArtikel {
  var $app;

  function Artikel($app) {
    //parent::GenArtikel($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ArtikelCreate");
    $this->app->ActionHandler("edit","ArtikelEdit");
    $this->app->ActionHandler("list","ArtikelList");
    $this->app->ActionHandler("newlist","ArtikelNewList");
    $this->app->ActionHandler("stueckliste","ArtikelStueckliste");
    $this->app->ActionHandler("stuecklisteimport","ArtikelStuecklisteImport");
    $this->app->ActionHandler("stuecklisteupload","ArtikelStuecklisteUpload");
    $this->app->ActionHandler("instueckliste","ArtikelInStueckliste");
    $this->app->ActionHandler("delstueckliste","DelStueckliste");
    $this->app->ActionHandler("stuecklisteempty","ArtikelStuecklisteEmpty");
    $this->app->ActionHandler("upstueckliste","UpStueckliste");
    $this->app->ActionHandler("downstueckliste","DownStueckliste");
    $this->app->ActionHandler("editstueckliste","ArtikelStuecklisteEditPopup");
    $this->app->ActionHandler("verkauf","ArtikelVerkauf");
    $this->app->ActionHandler("copy","ArtikelCopy");
    $this->app->ActionHandler("schliessen","ArtikelSchliessen");
    $this->app->ActionHandler("verkaufeditpopup","ArtikelVerkaufEditPopup");
    $this->app->ActionHandler("verkaufcopy","ArtikelVerkaufCopy");
    $this->app->ActionHandler("verkaufdelete","ArtikelVerkaufDelete");
    $this->app->ActionHandler("verkaufdisable","ArtikelVerkaufDisable");
    $this->app->ActionHandler("einkauf","ArtikelEinkauf");
    $this->app->ActionHandler("einkaufdelete","ArtikelEinkaufDelete");
    $this->app->ActionHandler("einkaufdisable","ArtikelEinkaufDisable");
    $this->app->ActionHandler("einkaufcopy","ArtikelEinkaufCopy");
    $this->app->ActionHandler("einkaufeditpopup","ArtikelEinkaufEditPopup");
    $this->app->ActionHandler("projekte","ArtikelProjekte");
    $this->app->ActionHandler("produktion","ArtikelProduktion");
    $this->app->ActionHandler("lager","ArtikelLager");
    $this->app->ActionHandler("seriennummern","ArtikelSeriennummern");
    $this->app->ActionHandler("mindesthaltbarkeitsdatum","ArtikelMHD");
    $this->app->ActionHandler("mhddelete","ArtikelMHDDelete");
    $this->app->ActionHandler("chargedelete","ArtikelChargeDelete");
    $this->app->ActionHandler("chargen","ArtikelChargen");
    $this->app->ActionHandler("wareneingang","ArtikelWareneingang");
    $this->app->ActionHandler("offenebestellungen","ArtikelOffeneBestellungen");
    $this->app->ActionHandler("statistik","ArtikelStatistik");
    $this->app->ActionHandler("offeneauftraege","ArtikelOffeneAuftraege");
    $this->app->ActionHandler("dateien","ArtikelDateien");
    $this->app->ActionHandler("eigenschaften","ArtikelEigenschaften");
    $this->app->ActionHandler("eigenschaftendelete","ArtikelEigenschaftenDelete");
    $this->app->ActionHandler("eigenschafteneditpopup","ArtikelEigenschaftenEditPopup");
    $this->app->ActionHandler("provision","Artikelprovision");
    $this->app->ActionHandler("delete","ArtikelDelete");
    $this->app->ActionHandler("auslagern","ArtikelAuslagern");
    $this->app->ActionHandler("einlagern","ArtikelEinlagern");
    $this->app->ActionHandler("umlagern","ArtikelUmlagern");
    $this->app->ActionHandler("ausreservieren","ArtikelAusreservieren");
    $this->app->ActionHandler("etiketten","ArtikelEtiketten");
    $this->app->ActionHandler("reservierung","ArtikelReservierung");
    $this->app->ActionHandler("onlineshop","ArtikelOnlineShop");
    $this->app->ActionHandler("ajaxwerte","ArtikelAjaxWerte");
    $this->app->ActionHandler("profisuche","ArtikelProfisuche");
    $this->app->ActionHandler("lagerlampe","ArtikelLagerlampe");
    $this->app->ActionHandler("shopexport","ArtikelShopexport");
    $this->app->ActionHandler("shopexportfiles","ArtikelShopexportFiles");
    $this->app->ActionHandler("stuecklisteetiketten","ArtikelStuecklisteEtiketten");
    $this->app->ActionHandler("minidetail","ArtikelMiniDetail");
    $this->app->ActionHandler("multilevel","ArtikelMultilevel");
    $this->app->ActionHandler("lagersync","ArtikelLagerSync");

    $id = $this->app->Secure->GetGET("id");
    $nummer = $this->app->Secure->GetPOST("nummer");

    if(is_numeric($id)) 
      $artikel = $this->app->DB->Select("SELECT CONCAT(name_de,' (',nummer,')') FROM artikel WHERE id='$id' LIMIT 1");
    else
      $artikel = $nummer; 
    if($artikel!="")
      $this->app->Tpl->Set(UEBERSCHRIFT,"Artikel: ".$artikel);
    else $this->app->Tpl->Set(UEBERSCHRIFT,"Artikel");
    $this->app->Tpl->Set(FARBE,"[FARBE1]");


    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }


  function ArtikelEigenschaften()
  {
    //     $this->ArtikelMenu();


    $this->app->Tpl->Add(UEBERSCHRIFT," (Verkauf)");
    $this->app->Tpl->Set(SUBSUBHEADING,"Verkaufspreise");    $this->ArtikelMenu();
    $id = $this->app->Secure->GetGET("id");    // neues arbeitspaket

    $this->app->YUI->AutoComplete("einheit","artikeleinheit");
    $this->app->YUI->AutoComplete("einheit2","artikeleinheit");
    $this->app->YUI->AutoComplete("einheit3","artikeleinheit");

    $widget = new WidgetEigenschaften($this->app,TAB2);
    $widget->form->SpecialActionAfterExecute("none",
        "index.php?module=artikel&action=eigenschaften&id=$id");
    if($this->app->Secure->GetPOST("submit")!="")      $this->app->erp->EnableTab("tabs-2");

    $widget->Create();

    $this->app->YUI->TableSearch(TAB1,"eigenschaften");
    // echo huhuh
    $this->app->Tpl->Parse(PAGE,"eigenschaftenuebersicht.tpl");
  }

  function ArtikelEigenschaftenEditPopup()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(OPENDISABLE,"<!--");
    $this->app->Tpl->Set(CLOSEDISABLE,"-->");

    $this->app->YUI->AutoComplete("einheit","artikeleinheit");
    $this->app->YUI->AutoComplete("einheit2","artikeleinheit");
    $this->app->YUI->AutoComplete("einheit3","artikeleinheit");

    $sid = $this->app->DB->Select("SELECT artikel FROM eigenschaften WHERE id='$id' LIMIT 1");
    $this->ArtikelMenu($sid);

    $this->app->Tpl->Set(ABBRECHEN,"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=artikel&action=eigenschaften&id=$sid';\">");

    $widget = new WidgetEigenschaften($this->app,TAB1);
    $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=artikel&action=eigenschaften&id=$sid&&22#tabs-1");
    $widget->Edit();

    $this->app->Tpl->Add(TAB2,"Sie bearbeiten gerade eine Eigenschaft. Erst nach dem Speichern k&ouml;nnen neue Preise angelegt werden.");
    $this->app->Tpl->Add(TAB3,"Sie bearbeiten gerade eine Eigenschaft. Erst nach dem Speichern k&ouml;nnen Statistiken betrachtet werden.");
    $this->app->Tpl->Parse(PAGE,"eigenschaftenuebersicht.tpl");
  }

 
  function ArtikelEigenschaftenDelete()
  {

    $id = $this->app->Secure->GetGET("id");

    $sid = $this->app->DB->Select("SELECT artikel FROM eigenschaften WHERE id='$id' LIMIT 1");
    $this->app->DB->Update("DELETE FROM eigenschaften WHERE id='$id' LIMIT 1");
    header("Location: index.php?module=artikel&action=eigenschaften&id=".$sid);
    exit;
  }


  function ArtikelLagerSync()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->DB->Update("UPDATE artikel SET cache_lagerplatzinhaltmenge='-100' WHERE id='$id'");
    $sync =  $this->app->erp->LagerSync($id,true);
    if($sync==1) echo "gruen";
    else echo "gelb";
    exit;
  }

  function Preisrechner()
  {
    $this->app->Tpl->Set(PREISRECHNER,"<input type=\"button\" value=\"+19\" onclick=\"this.form.preis.value=parseFloat(this.form.preis.value.split(',').join('.'))*1.19;\">");
    $this->app->Tpl->Add(PREISRECHNER,"<input type=\"button\" value=\"-19\" onclick=\"this.form.preis.value=parseFloat(this.form.preis.value.split(',').join('.'))/1.19;\">");
    $this->app->Tpl->Add(PREISRECHNER,"<input type=\"button\" value=\"+7\" onclick=\"this.form.preis.value=parseFloat(this.form.preis.value.split(',').join('.'))*1.07;\">");
    $this->app->Tpl->Add(PREISRECHNER,"<input type=\"button\" value=\"-7\" onclick=\"this.form.preis.value=parseFloat(this.form.preis.value.split(',').join('.'))/1.07;\">");

  }


  function ArtikelMultilevel()
  {
    $this->ArtikelMenu();
    $id = $this->app->Secure->GetGET("id");
    //$this->app->Tpl->Set(TABTEXT,"MLM Optionen");

    if($this->app->Secure->GetPOST("mlmsubmit"))
    {
      $mlmpunkte = $this->app->Secure->GetPOST("mlmpunkte");
      $mlmbonuspunkte = $this->app->Secure->GetPOST("mlmbonuspunkte");
      $mlmkeinepunkteeigenkauf = $this->app->Secure->GetPOST("mlmkeinepunkteeigenkauf");
      $mlmdirektpraemie = $this->app->Secure->GetPOST("mlmdirektpraemie");

      if($mlmkeinepunkteeigenkauf!="1") $mlmkeinepunkteeigenkauf = "0";

      $mlmdirektpraemie = str_replace(',','.',$mlmdirektpraemie);

      $this->app->DB->Update("UPDATE artikel SET mlmpunkte='$mlmpunkte',mlmkeinepunkteeigenkauf='$mlmkeinepunkteeigenkauf',mlmdirektpraemie='$mlmdirektpraemie',
          mlmbonuspunkte='$mlmbonuspunkte' WHERE id='$id' LIMIT 1");

      $this->app->Tpl->Set(MESSAGE,"<div class=\"error2\">Die MLM Optionen wurden gespeichert!</div>");
    }

    $mlmpunkte = $this->app->DB->Select("SELECT mlmpunkte FROM artikel WHERE id='$id' LIMIT 1");
    $mlmbonuspunkte = $this->app->DB->Select("SELECT mlmbonuspunkte FROM artikel WHERE id='$id' LIMIT 1");
    $mlmkeinepunkteeigenkauf = $this->app->DB->Select("SELECT mlmkeinepunkteeigenkauf FROM artikel WHERE id='$id' LIMIT 1");
    $mlmdirektpraemie = $this->app->DB->Select("SELECT mlmdirektpraemie FROM artikel WHERE id='$id' LIMIT 1");

    $this->app->Tpl->Set(MLMPUNKTE,$mlmpunkte);
    $this->app->Tpl->Set(MLMBONUSPUNKTE,$mlmbonuspunkte);
    $this->app->Tpl->Set(MLMDIREKTPRAEMIE,$mlmdirektpraemie);

    if($mlmkeinepunkteeigenkauf=="1")
      $this->app->Tpl->Set(MLMKEINEPUNKTEEIGENKAUF,"checked");


    $this->app->Tpl->Parse(TAB1,"artikel_multilevel.tpl");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }


  function ArtikelMiniDetail($parsetarget="",$menu=true)
  {
    $id=$this->app->Secure->GetGET("id");

    $this->app->Tpl->Set(ID,$id);

    $kurztext_de = $this->app->DB->Select("SELECT kurztext_de FROM artikel WHERE id='$id' LIMIT 1");
    if($kurztext_de=="")
      $kurztext_de = $this->app->DB->Select("SELECT anabregs_text FROM artikel WHERE id='$id' LIMIT 1");

    $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");
    $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set(NAME_DE,$name_de);
    $this->app->Tpl->Set(NUMMER,$nummer);

  $standardbild = $this->app->DB->Select("SELECT id FROM datei WHERE id='$standardbild' AND geloescht!=1 LIMIT 1");

    if($standardbild=="")
      $standardbild = $this->app->DB->Select("SELECT datei FROM datei_stichwoerter WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='$id' LIMIT 1");

    if($standardbild > 0)
      $this->app->Tpl->Set(ARTIKELBILD,"<img src=\"index.php?module=dateien&action=send&id=$standardbild\" width=\"200\" align=\"left\" style=\"margin-right:10px; margin-bottom:10px;\">");


    $lagerartikel = $this->app->DB->Select("SELECT lagerartikel FROM artikel WHERE id='$id' LIMIT 1");
    $lager_platz = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id='$id' LIMIT 1");

    if($lagerartikel>0)
      $this->app->Tpl->Set(LAGERLINK,"&nbsp;<a href=\"index.php?module=artikel&action=lager&id=$id\">&rArr;</a>");
    else
      $this->app->Tpl->Set(LAGERLINK,"");

    $this->app->Tpl->Set(KURZTEXT,$kurztext_de);



    // easy table mit arbeitspaketen YUI als template 
    $table = new EasyTable($this->app);
    $table->Query("SELECT CONCAT(l.bezeichnung,' / ',lp.kurzbezeichnung) as lager, lpi.menge as menge
        FROM lager_platz_inhalt lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id  
        LEFT JOIN lager l ON l.id=lp.lager WHERE lpi.artikel='$id' ");

    $table->DisplayNew(ARTIKEL,"Menge","noAction");

    if($lager_platz > 0)
    {
      $lager = $this->app->DB->Select("SELECT lager FROM lager_platz WHERE id='$lager_platz' LIMIT 1");
      $lagerhauptbezeichung = $this->app->DB->Select("SELECT bezeichnung FROM lager WHERE id='$lager' LIMIT 1");
      $lagerbezeichnung = $this->app->DB->Select("SELECT kurzbezeichnung FROM lager_platz WHERE id='$lager_platz' LIMIT 1");
      if($lagerbezeichnung=="") $lagerbezeichnung="kein Standardlager eingestellt";
      $this->app->Tpl->Add(ARTIKEL,"<br>Standardlager: $lagerhauptbezeichung / $lagerbezeichnung<br><br>");
    }

    $this->app->Tpl->Add(ARTIKEL,$this->app->erp->ArtikelLagerInfo($id));

    $table = new EasyTable($this->app);
    $table->Query("SELECT adr.name as kunde, adr.kundennummer as kdnr, r.menge,p.abkuerzung as projekt,r.grund  FROM lager_reserviert r LEFT JOIN artikel a ON a.id=r.artikel LEFT JOIN projekt p ON 
        p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id WHERE r.firma='{$this->app->User->GetFirma()}' AND a.id='$id'");

    $table->DisplayNew(RESERVIERT,"Grund","noAction");

    $table = new EasyTable($this->app);
    $table->Query("SELECT p.belegnr,a.name,p.status,po.menge FROM produktion_position po LEFT JOIN produktion p ON p.id=po.produktion LEFT JOIN adresse a ON a.id=p.adresse  WHERE po.artikel='$id' AND (p.status!='abgeschlossen' AND p.status!='storniert')");


    $table->DisplayNew(PRODUKTIONEN,"Menge","noAction");

    $table = new EasyTable($this->app);
    $table->Query("SELECT p.belegnr,a.name, a.kundennummer as kdnr, p.status,po.menge FROM auftrag_position po LEFT JOIN auftrag p ON p.id=po.auftrag LEFT JOIN adresse a ON a.id=p.adresse  WHERE po.artikel='$id' AND (p.status!='abgeschlossen' AND p.status!='storniert')");


    $table->DisplayNew(AUFTRAG,"Menge","noAction");

    $table = new EasyTable($this->app);
/* $table->Query("SELECT DATE_FORMAT(b.datum,'%d.%m.%Y') as datum, CONCAT('<a href=\"index.php?module=bestellung&action=edit&id=',b.id,'\" target=\"_blank\">',b.belegnr,'</a>') as 'bestellung Nr.', bp.bestellnummer as Nummer, bp.menge, bp.geliefert, bp.vpe as VPE, a.lieferantennummer as lieferant, a.name as name, if(bp.lieferdatum!='0000-00-00', DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum, b.status as status
        FROM bestellung_position bp LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN adresse a ON b.adresse=a.id
        WHERE artikel='$id' AND b.status!='storniert' AND b.status!='abgeschlossen' AND bp.geliefert<bp.menge ORDER by bp.lieferdatum DESC");
*/

    $table->Query("SELECT DATE_FORMAT(b.datum,'%d.%m.%Y') as datum, CONCAT('<a href=\"index.php?module=bestellung&action=edit&id=',b.id,'\" target=\"_blank\">',b.belegnr,'</a>') as 'bestellung', bp.menge, bp.geliefert, LEFT(a.name,20) as name, if(bp.lieferdatum!='0000-00-00', DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum
        FROM bestellung_position bp LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN adresse a ON b.adresse=a.id
        WHERE artikel='$id' AND b.status!='storniert' AND b.status!='abgeschlossen' AND bp.geliefert<bp.menge ORDER by bp.lieferdatum DESC");

    $table->DisplayNew(BESTELLUNG,"Lieferdatum","noAction");

    $table = new EasyTable($this->app);
    $table->Query("SELECT a.name as lieferant, e.ab_menge ab, e.preis, e.waehrung FROM einkaufspreise e LEFT JOIN adresse a ON a.id=e.adresse
        WHERE e.artikel='$id' AND e.geloescht!=1");

    $table->DisplayNew(EINKAUFSPREISE,"Waehrung","noAction");


    $table = new EasyTable($this->app);
    $table->Query("SELECT if(a.name='' OR a.id IS NULL,'Alle',a.name) as kunde, v.ab_menge ab, v.preis, v.waehrung FROM verkaufspreise v LEFT JOIN adresse a ON a.id=v.adresse
        WHERE v.artikel='$id' AND v.geloescht!=1 AND (v.gueltig_bis >= NOW() OR v.gueltig_bis='0000-00-00')");

    $table->DisplayNew(VERKAUFSPREISE,"Waehrung","noAction");


    $table = new EasyTable($this->app);
    $table->Query("SELECT e.bezeichnung, e.beschreibung, e.wert, e.einheit, e.wert2 as `wert<!--2-->`, e.einheit2 as `einheit<!--2-->`, e.wert3 as `wert<!--3-->`, e.einheit3 
        as `einheit<!--3-->` FROM eigenschaften e LEFT JOIN artikel a ON a.id=e.artikel 
        WHERE a.id='$id' ORDER by e.bezeichnung");
    $table->DisplayNew(EIGENSCHAFTEN,"Einheit","noAction");

    $table = new EasyTable($this->app);
    $table->Query("SELECT a.nummer, LEFT(a.name_de,30) as artikel, s.menge FROM stueckliste s 
        LEFT JOIN artikel a ON s.artikel=a.id 
        WHERE s.stuecklistevonartikel='$id' ORDER by a.nummer");
    $table->DisplayNew(STUECKLISTE,"Menge","noAction");






    $this->app->Tpl->Output("artikel_minidetail.tpl");
    exit;
  }


  function ArtikelShopexport()
  {
    $id = $this->app->Secure->GetGET("id"); 
    $shop = $this->app->Secure->GetGET("shop"); 
    $artikel = array($id);

    if($shop=="1")
      $shop = $this->app->DB->Select("SELECT shop FROM artikel WHERE id='$id' LIMIT 1");
    elseif($shop=="2")
      $shop = $this->app->DB->Select("SELECT shop2 FROM artikel WHERE id='$id' LIMIT 1");
    elseif($shop=="3")
      $shop = $this->app->DB->Select("SELECT shop3 FROM artikel WHERE id='$id' LIMIT 1");


    if($this->app->remote->RemoteSendArticleList($shop,$artikel))
      $msg = $this->app->erp->base64_url_encode("<div class=info>Der Artikel wurde im Shop aktualisiert!</div>"); 
    else 
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Es gab einen Fehler beim Aktualisieren des Artikels im Shop! Bzw. stellen Sie sicher, das im Shop die Optionen f&uuml;r das &Uuml;bertragen der Attribute und Lagerzahlen aktiv sind!</div>"); 

    header("Location: index.php?module=artikel&action=edit&id=$id&msg=$msg#tabs-5");
    exit;
  }

  function ArtikelShopexportFiles()
  {
    $id = $this->app->Secure->GetGET("id"); 
    $shop = $this->app->Secure->GetGET("shop"); 

    if($shop=="1")
      $shop = $this->app->DB->Select("SELECT shop FROM artikel WHERE id='$id' LIMIT 1");
    elseif($shop=="2")
      $shop = $this->app->DB->Select("SELECT shop2 FROM artikel WHERE id='$id' LIMIT 1");
    elseif($shop=="3")
      $shop = $this->app->DB->Select("SELECT shop3 FROM artikel WHERE id='$id' LIMIT 1");

    if($this->app->remote->RemoteUpdateFilesArtikel($id,$shop))
      $msg = $this->app->erp->base64_url_encode("<div class=info>Der Artikel wurde im Shop aktualisiert!</div>"); 
    else 
      $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Es gab einen Fehler beim Aktualisieren des Artikels im Shop!</div>"); 

    header("Location: index.php?module=artikel&action=edit&id=$id&msg=$msg#tabs-5");
    exit;
  }


  function ArtikelProduktion()
  {
    $id = $this->app->Secure->GetGET("id");

    $this->app->Tpl->Add(UEBERSCHRIFT," (Wareneingang)");
    $this->ArtikelMenu();
    $this->app->Tpl->Set(TABTEXT,"Produktion");

    $this->app->YUI->AutoComplete("smt","artikelnummer");
    $this->app->YUI->AutoComplete("filling","artikelnummer");
    $this->app->YUI->AutoComplete("tht","artikelnummer");
    $this->app->YUI->AutoComplete("kunde","kunde",1);

    if($this->app->Secure->GetPOST("speichern")!="")
    {           
      $smt = $this->app->Secure->GetPOST("smt");
      $filling = $this->app->Secure->GetPOST("filling");
      $tht = $this->app->Secure->GetPOST("tht");

      $tmp = array('smt'=>$smt,'filling'=>$filling,'tht'=>$tht);
      $tmp_base64 = $this->app->erp->base64_url_encode(serialize($tmp));
      $this->app->DB->Update("UPDATE artikel SET produktioninfo='$tmp_base64' WHERE id='$id' LIMIT 1"); 
    }

    if($this->app->Secure->GetPOST("produktion_anlegen")!="")
    {
      $menge_smt = $this->app->Secure->GetPOST("menge_smt");
      $menge_filling = $this->app->Secure->GetPOST("menge_filling");
      $menge_tht = $this->app->Secure->GetPOST("menge_tht");
      $kunde = $this->app->Secure->GetPOST("kunde");

      //neue produktion anlegen
      //artikel hinzufügen      
      //gemeinsamer baum
      if($menge_smt>0 && $kunde > 0)
      {
        //                              $id = $this->app->erp->CreateProduktion();
        //                                      $adresse = app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kunde' LIMIT 1");
        //                                      $bezeichnung = date('Ymd)'."-".$nummer
        //                              $this->app->DB->Update("UPDATE produktion SET bezeichnung='$bezeichnung',status='freigegeben',adresse");        
      }

    }


    if($this->app->Secure->GetPOST("produktion_anlegen_einzeln")!="")
    {
      $menge = $this->app->Secure->GetPOST("menge");
      $kunde = $this->app->Secure->GetPOST("kunde");
      $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kunde' LIMIT 1");

      if($menge > 0 && $adresse > 0)
      {
        $produktion = $this->app->erp->CreateProduktion($adresse);
        $this->app->erp->LoadProduktionStandardwerte($produktion,$adresse);
        $this->app->erp->AddArtikelProduktion($id,$menge,$produktion);
        $msg = $this->app->erp->base64_url_encode("<div class=error>Die Produktion wurde angelegt. Bitte geben Sie diese jetzt frei!</div>"); 
        //                              $this->app->DB->Update("UPDATE produktion SET status='freigegeben' WHERE id='$produktion'");    
        header("Location: /index.php?module=produktion&action=edit&msg=$msg&id=$produktion");
        exit;
      }
    }

    $tmp = $this->app->DB->Select("SELECT produktioninfo FROM artikel WHERE id='$id' LIMIT 1");
    $tmp_base64 = unserialize($this->app->erp->base64_url_decode($tmp));

    $this->app->Tpl->Set(SMT,$tmp_base64['smt']);
    $this->app->Tpl->Set(FILLING,$tmp_base64['filling']);
    $this->app->Tpl->Set(THT,$tmp_base64['tht']);

    $this->app->Tpl->Parse(TAB1,"artikel_produktion.tpl");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }


  function ArtikelStuecklisteEtiketten()
  {
    $id = $this->app->Secure->GetGET("id"); 
    $artikel = $this->app->DB->SelectArr("SELECT * FROM stueckliste WHERE stuecklistevonartikel='$id'");                    
    for($i=0;$i<count($artikel);$i++)                                                                            
    {                                                                                                   
      $artikelid = $artikel[$i]['artikel'];     
      //$name_de = $this->app->erp->UmlauteEntfernen($this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikelid' LIMIT 1"));
      //$name_base64_de = $this->app->erp->base64_url_encode($name_de);
      //$nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikelid' LIMIT 1");
      $this->app->erp->EtikettenDrucker("artikel_klein",1,"artikel",$artikelid);        
    } 
    header("Location: index.php?module=artikel&action=stueckliste&id=$id");
    exit;
  }

  function ArtikelSchliessen()                                                                       
  {
    $id = $this->app->Secure->GetGET("id");                                                              
    if($id > 0 && is_numeric($id))
      $this->app->DB->Update("UPDATE bestellung_position SET abgeschlossen='1' WHERE artikel='$id'");

    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
  }

  function ArtikelLagerlampe()
  {
    $aktivieren = $this->app->Secure->GetPOST("aktivieren");
    $deaktivieren = $this->app->Secure->GetPOST("deaktivieren");
    //$jetztnichtlagerndrot = $this->app->Secure->GetPOST("jetztnichtlagerndrot");
    $jetztgruen = $this->app->Secure->GetPOST("jetztgruen");
    $jetztgelb = $this->app->Secure->GetPOST("jetztgelb");
    $jetztrot = $this->app->Secure->GetPOST("jetztrot");
    $tab3gruen = $this->app->Secure->GetPOST("tab3gruen");
    $neuweg = $this->app->Secure->GetPOST("neuweg");
    $artikelmarkiert = $this->app->Secure->GetPOST("artikelmarkiert");
    $artikelmarkierthidden = $this->app->Secure->GetPOST("artikelmarkierthidden");

    if($jetztgruen!="") 
    {
      for($i=0;$i < count($artikelmarkiert); $i++)
        $this->app->DB->Update("UPDATE artikel SET lieferzeit='green',ausverkauft='0' WHERE id='".$artikelmarkiert[$i]."'  LIMIT 1");
    }

    else if($jetztgelb!="") 
    {
      for($i=0;$i < count($artikelmarkiert); $i++)
        $this->app->DB->Update("UPDATE artikel SET lieferzeit='yellow',ausverkauft='0' WHERE id='".$artikelmarkiert[$i]."'  LIMIT 1");
    }

    else if($jetztrot!="") 
    {
      for($i=0;$i < count($artikelmarkiert); $i++)
        $this->app->DB->Update("UPDATE artikel SET lieferzeit='red' WHERE id='".$artikelmarkiert[$i]."'  LIMIT 1");
    }

    else if($aktivieren!="") 
    {
      foreach($artikelmarkierthidden as $key=>$value)
      {
        if($artikelmarkiert[$key]=="1")
        {
          $this->app->DB->Update("UPDATE artikel SET autolagerlampe='1' WHERE id='".$key."'  LIMIT 1");
        }
        else {
          $this->app->DB->Update("UPDATE artikel SET autolagerlampe='0' WHERE id='".$key."'  LIMIT 1");
        }
      }
    }


    else if($neuweg!="")
    {
      for($i=0;$i < count($artikelmarkiert); $i++)
        $this->app->DB->Update("UPDATE artikel SET neu='0' WHERE id='".$artikelmarkiert[$i]."' LIMIT 1");
    } 

    else if($jetztnichtlagernd!="")
    {
      for($i=0;$i < count($artikelmarkiert); $i++)
        $this->app->DB->Update("UPDATE artikel SET lieferzeit='bestellt' WHERE id='".$artikelmarkiert[$i]."' LIMIT 1");
    } 
    else if($jetztnichtlagerndrot!="")
    {
      for($i=0;$i < count($artikelmarkiert); $i++)
        $this->app->DB->Update("UPDATE artikel SET lieferzeit='nichtlieferbar' WHERE id='".$artikelmarkiert[$i]."' LIMIT 1");
    } 

    //    $this->app->erp->MenuEintrag("index.php?module=artikel&action=create","Neuen Artikel anlegen");
    $this->app->erp->MenuEintrag("index.php?module=lager&action=list","zur&uuml;ck zur &Uuml;bersicht");

    $this->app->Tpl->Set(TAB1,"<div class=\"info\">Hier werden alle Artikel die als nicht lagernd Online-Shop markierten Artikel angezeigt.</div>");
    $this->app->Tpl->Set(TAB2,"<div class=\"info\">Hier werden alle Artikel die als lagernd im Online-Shop markiert sind jedoch nicht im Lager liegen.</div>");
    $this->app->Tpl->Set(TAB3,"<div class=\"info\">Hier werden alle Artikel die als ausverkauf im Online-Shop markierten sind jedoch im Lager liegen.</div>");

    $this->app->YUI->TableSearch(TAB1,"manuellagerlampe");                                                  
    $this->app->YUI->TableSearch(TAB2,"autolagerlampe");                                                  
    //    $this->app->YUI->TableSearch(TAB2,"artikeltabellelagerndabernichtlagernd");                                                  
    //   $this->app->YUI->TableSearch(TAB3,"artikeltabellehinweisausverkauft");                                                  
    $this->app->YUI->TableSearch(TAB3,"artikeltabelleneu");                                                  

    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Lagerlampen berechnen");
    $this->app->Tpl->Set(TABTEXT,"Lagerlampen berechnen");

    $this->app->Tpl->Parse(MANUELLCHECKBOX,"checkbox.tpl");
    $this->app->Tpl->Parse(AUTOCHECKBOX,"checkbox2.tpl");
    $this->app->Tpl->Parse(PAGE,"lagerlampen.tpl");
  }


  function ArtikelProfisuche()
  {
    $id = $this->app->Secure->GetGET("id"); // abhaengig von cmd
    $cmd = $this->app->Secure->GetGET("cmd");
    $anlegen = $this->app->Secure->GetPOST("anlegen");

    $projekt = $this->app->Secure->GetPOST("projekt");
    $adresse = $this->app->Secure->GetPOST("adresse");
    $menge = $this->app->Secure->GetPOST("menge");
    $preis = $this->app->Secure->GetPOST("preis");
    $bestellnummer = $this->app->Secure->GetPOST("bestellnummer");
    $bezeichnunglieferant = $this->app->Secure->GetPOST("bezeichnunglieferant");
    $typ = $this->app->Secure->GetPOST("typ");
    $name_de = $this->app->Secure->GetPOST("name_de");
    $kurztext_de = $this->app->Secure->GetPOST("kurztext_de");
    $umsatzsteuerklasse = $this->app->Secure->GetPOST("umsatzsteuerklasse");
    $internerkommentar = $this->app->Secure->GetPOST("internerkommentar");


    $insert = $this->app->Secure->GetGET("insert");

    if($insert=="true")
    {
      // hole alles anhand der verkaufspreis id

      $id = $this->app->Secure->GetGET("sid");
      $vid = $this->app->Secure->GetGET("id");
      $cmd = $this->app->Secure->GetGET("cmd");

      if($cmd!="bestellung" && $cmd!="anfrage")
      {
        $artikel_id = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$vid' LIMIT 1");
        $menge = $this->app->DB->Select("SELECT ab_menge FROM verkaufspreise WHERE id='$vid' LIMIT 1");
        $preis = $this->app->DB->Select("SELECT preis FROM verkaufspreise WHERE id='$vid' LIMIT 1");
        $projekt = $this->app->DB->Select("SELECT projekt FROM verkaufspreise WHERE id='$vid' LIMIT 1");
      } else {
        $artikel_id = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE id='$vid' LIMIT 1");
        $menge = $this->app->DB->Select("SELECT ab_menge FROM einkaufspreise WHERE id='$vid' LIMIT 1");
        $preis = $this->app->DB->Select("SELECT preis FROM einkaufspreise WHERE id='$vid' LIMIT 1");
        $projekt = $this->app->DB->Select("SELECT projekt FROM einkaufspreise WHERE id='$vid' LIMIT 1");
      }
      $lieferdatum = "0000-00-00";
      $waehrung = "EUR";
      $vpe = "";
      $bezeichnung = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel_id' LIMIT 1");
      $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$artikel_id' LIMIT 1");
      $umsatzsteuerklasse = $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id='$artikel_id' LIMIT 1");

      $sort = $this->app->DB->Select("SELECT MAX(sort) FROM {$cmd}_position WHERE {$cmd}='$id' LIMIT 1");
      $sort = $sort + 1;

      $mlmpunkte = $this->app->DB->Select("SELECT mlmpunkte FROM artikel WHERE id='$artikel_id' LIMIT 1");
      $mlmbonuspunkte = $this->app->DB->Select("SELECT mlmbonuspunkte FROM artikel WHERE id='$artikel_id' LIMIT 1");
      $mlmdirektpraemie = $this->app->DB->Select("SELECT mlmdirektpraemie FROM artikel WHERE id='$artikel_id' LIMIT 1");

      if($cmd=="lieferschein")
      {
        $this->app->DB->Insert("INSERT INTO lieferschein_position (id,{$cmd},artikel,bezeichnung,beschreibung,nummer,menge,sort,lieferdatum, status,projekt,vpe)
            VALUES ('','$id','$artikel_id','$bezeichnung','$kurztext_de','$nummer','$menge','$sort','$lieferdatum','angelegt','$projekt','$vpe')");
      } 
      else if($cmd=="anfrage")
      {
        $this->app->DB->Insert("INSERT INTO anfrage_position (id,{$cmd},artikel,bezeichnung,beschreibung,nummer,menge,sort,lieferdatum, status,projekt,vpe)
            VALUES ('','$id','$artikel_id','$bezeichnunglieferant','$kurztext_de','$nummer','$menge','$sort','$lieferdatum','angelegt','$projekt','$vpe')");
      } 
      else if($cmd=="bestellung")
      {
        $bestellnummer = $this->app->DB->Select("SELECT bestellnummer FROM einkaufspreise WHERE id='$vid' LIMIT 1");
        $bezeichnunglieferant = $this->app->DB->Select("SELECT bezeichnunglieferant FROM einkaufspreise WHERE id='$vid' LIMIT 1");

        $this->app->DB->Insert("INSERT INTO bestellung_position (id,{$cmd},artikel,beschreibung,menge,sort,lieferdatum, status,projekt,vpe,bestellnummer,bezeichnunglieferant,preis,waehrung,umsatzsteuer)
            VALUES ('','$id','$artikel_id','$kurztext_de','$menge','$sort','$lieferdatum','angelegt','$projekt','$vpe','$bestellnummer','$bezeichnunglieferant','$preis','$waehrung','$umsatzsteuerklasse')");


      }
      else if ($cmd=="auftrag" || $cmd=="angebot" || $cmd=="rechnung")
      {
        $this->app->DB->Insert("INSERT INTO {$cmd}_position (id,{$cmd},artikel,bezeichnung,beschreibung,
          nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe,punkte,bonuspunkte,mlmdirektpraemie)
            VALUES ('','$id','$artikel_id','$bezeichnung','$kurztext_de','$nummer','$menge','$preis','$waehrung','$sort',
              '$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$vpe','$mlmpunkte','$mlmbonuspunkte','$mlmdirektpraemie')");
      } 
      else {
        $this->app->DB->Insert("INSERT INTO {$cmd}_position (id,{$cmd},artikel,bezeichnung,beschreibung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe)
            VALUES ('','$id','$artikel_id','$bezeichnung','$kurztext_de','$nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$vpe')");
      }


      header("Location: index.php?module={$cmd}&action=positionen&id=$id");
      exit;
    }

    if($anlegen!="")
    {
      // speichern ??
      //echo "speichern";

      if($cmd=="lieferschein")
      {
        if($name_de=="" || $menge=="")
        {
          $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Artikel (Deutsch) und Menge sind Pflichtfelder!</div>");
          $error = 1;
        }
      } else {
        if($name_de=="" || $menge=="" || $preis=="")
        {
          $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Artikel (Deutsch), Preis und Menge sind Pflichtfelder!</div>");
          $error = 1;
        }
      }
      if($error!=1)
      {
        $sort = $this->app->DB->Select("SELECT MAX(sort) FROM {$cmd}_position WHERE {$cmd}='$id' LIMIT 1");
        $sort = $sort + 1;


        $tmp = trim($adresse);
        $rest = $this->app->erp->FirstTillSpace($tmp);

        if($rest > 0)
          $adresse =  $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='$rest' AND geloescht=0 AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
        else $adresse="";

        $artikelart = $typ;
        $lieferant = $adresse;
        $bezeichnung = $name_de;
        $waehrung = "EUR";
        $lieferdatum = "00.00.0000";
        $vpe = "";
        $preis = str_replace(",",".",$preis);

        if($projekt!="") 
          $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");
        else $projekt="";

        $neue_nummer = $this->app->erp->GetNextArtikelnummer($artikelart,$this->app->User->GetFirma(),$projekt);

        // anlegen als artikel
        $this->app->DB->Insert("INSERT INTO artikel (id,typ,nummer,projekt,name_de,kurztext_de,umsatzsteuer,adresse,firma)
            VALUES ('','$artikelart','$neue_nummer','$projekt','$bezeichnung','$kurztext_de','$umsatzsteuerklasse','$lieferant','".$this->app->User->GetFirma()."')");

        $artikel_id = $this->app->DB->GetInsertID();
        // einkaufspreis anlegen

        $lieferdatum = $this->app->String->Convert($lieferdatum,"%1.%2.%3","%3-%2-%1");

        if($cmd=="lieferschein")
        {
          $this->app->DB->Insert("INSERT INTO lieferschein_position (id,{$cmd},artikel,bezeichnung,beschreibung,nummer,menge,sort,lieferdatum, status,projekt,vpe)
              VALUES ('','$id','$artikel_id','$bezeichnung','$kurztext_de','$neue_nummer','$menge','$sort','$lieferdatum','angelegt','$projekt','$vpe')");
        }
        else if($cmd=="anfrage")
        {
          $this->app->DB->Insert("INSERT INTO anfrage_position (id,{$cmd},artikel,bezeichnung,beschreibung,nummer,menge,sort,lieferdatum, projekt,vpe)
              VALUES ('','$id','$artikel_id','$bezeichnung','$kurztext_de','$neue_nummer','$menge','$sort','$lieferdatum','$projekt','$vpe')");

          $this->app->erp->AddEinkaufspreis($artikel_id,$menge,$lieferant,$bestellnummer,$bezeichnunglieferant,$preis,$waehrung);
        }

        else if($cmd=="bestellung")
        {
          if($bezeichnunglieferant=="") $bezeichnunglieferant=$bezeichnung;
          $this->app->DB->Insert("INSERT INTO bestellung_position (id,{$cmd},artikel,beschreibung,menge,sort,lieferdatum, status,projekt,vpe,bestellnummer,bezeichnunglieferant,preis,waehrung,umsatzsteuer)
              VALUES ('','$id','$artikel_id','$kurztext_de','$menge','$sort','$lieferdatum','angelegt','$projekt','$vpe','$bestellnummer','$bezeichnunglieferant','$preis','$waehrung','$umsatzsteuerklasse')");

          //      $this->app->DB->Insert("INSERT INTO einkaufspreise (id,artikel,adresse,objekt,projekt,preis,ab_menge,angelegt_am,bearbeiter,bestellnummer,bezeichnunglieferant)
          //          VALUES ('','$artikel_id','$lieferant','Standard','$projekt','$preis','$menge',NOW(),'".$this->app->User->GetName()."','$bestellnummer','$bezeichnunglieferant')");

          $this->app->erp->AddEinkaufspreis($artikel_id,$menge,$lieferant,$bestellnummer,$bezeichnunglieferant,$preis,$waehrung);

        } else { // angebot auftrag rechnung gutschrift
          $this->app->DB->Insert("INSERT INTO verkaufspreise (id,artikel,adresse,objekt,projekt,preis,ab_menge,angelegt_am,bearbeiter)
              VALUES ('','$artikel_id','$lieferant','Standard','$projekt','$preis','$menge',NOW(),'".$this->app->User->GetName()."')");

          $this->app->DB->Insert("INSERT INTO {$cmd}_position (id,{$cmd},artikel,bezeichnung,beschreibung,nummer,menge,preis, waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe)
              VALUES ('','$id','$artikel_id','$bezeichnung','$kurztext_de','$neue_nummer','$menge','$preis','$waehrung','$sort','$lieferdatum','$umsatzsteuerklasse','angelegt','$projekt','$vpe')");
        }

        header("Location: index.php?module={$cmd}&action=positionen&id=$id");
        exit;
      } 
    }

    $this->app->Tpl->Set(PROJEKT,$projekt);
    $this->app->Tpl->Set(ADRESSE,$adresse);
    $this->app->Tpl->Set(MENGE,$menge);
    $this->app->Tpl->Set(PREIS,$preis);
    $this->app->Tpl->Set(BESTELLNUMMER,$bestellnummer);
    $this->app->Tpl->Set(BEZEICHNUNGLIEFERANT,$bezeichnunglieferant);
    $this->app->Tpl->Set(NAME_DE,$name_de);
    $this->app->Tpl->Set(KURZTEXT_DE,$kurztext_de);
    $this->app->Tpl->Set(INTERNERKOMMENTAR,$internerkommentar);


    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->app->YUI->AutoComplete("adresse","lieferant");




    if($cmd=="auftrag" || $cmd=="rechnung" || $cmd=="lieferschein" || $cmd=="angebot" || $cmd=="gutschrift")
    {
      $adresse = $this->app->DB->Select("SELECT adresse FROM {$cmd} WHERE id='$id' LIMIT 1");
      $kunde = $this->app->DB->Select("SELECT CONCAT(name,' ',kundennummer,'') FROM adresse WHERE id='$adresse' LIMIT 1");
    } else if ($cmd=="bestellung" || $cmd=="anfrage") {
      $adresse = $this->app->DB->Select("SELECT adresse FROM {$cmd} WHERE id='$id' LIMIT 1");
      $kunde = $this->app->DB->Select("SELECT CONCAT(name,' ',lieferantennummer,'') FROM adresse WHERE id='$adresse' LIMIT 1");
    }


    if($cmd=="lieferschein")
      $this->app->YUI->ParserVarIf(LIEFERSCHEIN,1);
    else
      $this->app->YUI->ParserVarIf(LIEFERSCHEIN,0);


    $this->app->Tpl->Set(KUNDE,$kunde);

    if($cmd=="bestellung" || $cmd=="anfrage")
      $this->app->YUI->TableSearch(ARTIKEL,"lieferantartikelpreise");
    else
      $this->app->YUI->TableSearch(ARTIKEL,"kundeartikelpreise");


    $this->app->Tpl->Set(PAGE,"<br><center><a href=\"index.php?module=$cmd&action=positionen&id=$id\"><img src=\"./themes/{$this->app->Conf->WFconf[defaulttheme]}/images/back.png\" border=\"0\"></a></center>");


    $artikelart = $this->app->erp->GetArtikelgruppe($projekt);
    $typ = $this->app->Secure->GetPOST("typ");
    $this->app->Tpl->Set(ARTIKELGRUPPE,$this->app->erp->GetSelectAsso($artikelart, $typ));


    if ($cmd=="bestellung" || $cmd=="anfrage") 
      $this->app->Tpl->Parse(PAGE,"aarlg_artikelbestellungneu.tpl");
    else
      $this->app->Tpl->Parse(PAGE,"aarlg_artikelneu.tpl");

    $this->app->BuildNavigation=false;

  }





  function ArtikelAjaxWerte()
  {
    $id = $this->app->Secure->GetGET("id");
    $name = $this->app->Secure->GetGET("name");
    $sid = $this->app->Secure->GetGET("sid");
    $smodule = $this->app->Secure->GetGET("smodule");
    $menge = $this->app->Secure->GetGET("menge");

    $cmd = $this->app->Secure->GetGET("cmd");
    $adresse = $this->app->Secure->GetGET("adresse");

    //          if($id=="") exit;

    if($smodule=="bestellung")
    { 
      if($name!=""){
        $id = $this->app->DB->Select("SELECT id FROM artikel WHERE name_de='$name' AND geloescht!=1 LIMIT 1");
        if($id<=0)
          $id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$name' AND geloescht!=1 LIMIT 1");
      } else {
        $commandline = $id;
        $tmp_id = explode(" ",$commandline);
        $tmp_id = $tmp_id[0];
        //     $id = substr($id,0,6);
        if($tmp_id!="")
        {
          $id = $tmp_id;
          $tmp_id = $commandline;
          // hole ab menge aus

          $n = strpos($tmp_id, $id." ");
          if ( false!==$n ) {
            $tmp_id = substr($tmp_id, 0, $n);
          } 
          $start_pos = strpos ($commandline, "ab Menge ");
          $commandline = substr($commandline,$start_pos + strlen("ab Menge "));
          $end_pos = strpos ($commandline, " ");
          if(trim(substr($commandline,0,$end_pos)) > 0)
            $menge = trim(substr($commandline,0,$end_pos));

        } else exit;
        $id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$id' AND geloescht!=1 LIMIT 1");
      }
      if(!is_numeric($id))
      {
        echo "#*##*##*##*##*##*##*#";
        exit;
      }

      $adresse = $this->app->DB->Select("SELECT adresse FROM $smodule WHERE id='$sid' LIMIT 1");
      //      $id = substr($id,0,6);

      $name = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");
      $bestellnummer = $this->app->DB->Select("SELECT bestellnummer FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 LIMIT 1");
      $bezeichnunglieferant = $this->app->DB->Select("SELECT bezeichnunglieferant FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 LIMIT 1");
      $nummer = $this->app->DB->Select("SELECT bestellnummer FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 LIMIT 1");
      $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT p.abkuerzung FROM artikel a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.id='$id' LIMIT 1");
      $projekt_id = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$id' LIMIT 1");
      $ab_menge = $this->app->DB->Select("SELECT ab_menge FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 LIMIT 1");
      $vpe = $this->app->DB->Select("SELECT vpe FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 LIMIT 1");
      $ek = $this->app->DB->Select("SELECT preis FROM einkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND ab_menge<='$menge' AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 ORDER by ab_menge DESC LIMIT 1");

      $name = html_entity_decode($name, ENT_QUOTES, 'UTF-8');
      if($vpe > 1)
      {
        if($menge < $vpe) $menge = $vpe;        
        else {
          $menge_vpe = $menge / $vpe;
          $menge = ceil($menge_vpe)*$vpe;       
        }
        //$ek = $menge*$ek;
      }

      // bei Bestellung
      echo "$name#*#$nummer#*#$projekt#*#$ek#*#$menge#*#$bestellnummer#*#$bezeichnunglieferant#*#$vpe";
    } else {
      //Pinguio fehler
      if($id=="")
      {
        $name = $this->app->Secure->GetGET("name");
        if(trim($name)!="")
        {
          $id = $this->app->DB->Select("SELECT nummer FROM artikel WHERE nummer LIKE '$name' LIMIT 1");
          if($id =="")
          {
            $id = $this->app->DB->Select("SELECT nummer FROM artikel WHERE name_de LIKE '$name' LIMIT 1");

            if($id=="")
            {
              $name = str_replace(' ','&nbsp;',$name);
              $id = $this->app->DB->Select("SELECT nummer FROM artikel WHERE name_de LIKE '$name' LIMIT 1");
              //naechster fall
            }   
          }
        } else {
          if(trim($name)!="")
          {
            // wenn name leer ist hole max position id
            $id = $this->app->DB->Select("SELECT MAX(id) FROM ".$smodule."_position WHERE $smodule='$sid'");
            $id = $this->app->DB->Select("SELECT artikel FROM ".$smodule."_position WHERE id='$id' LIMIT 1");
            $id = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
          }

        }
        if($id =="")
          exit;

      }
      //      $id = substr($id,0,6);
      //echo $id;
      //      if(!is_numeric($id))
      //        exit;
      $tmp_id = explode(" ",$id);
      $id = $tmp_id[0];


      $id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$id' LIMIT 1");

      $adresse = $this->app->DB->Select("SELECT adresse FROM $smodule WHERE id='$sid' LIMIT 1");


      $name = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");
      $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT p.abkuerzung FROM artikel a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.id='$id' LIMIT 1");

      $projekt_id = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$id' LIMIT 1");

      //      $ab_menge = $this->app->DB->Select("SELECT ab_menge FROM verkaufspreise WHERE artikel='$id' AND ab_menge=1  AND geloescht=0 LIMIT 1");
      $ab_menge = $menge;

      if($smodule=="inventur")
        $preis = $this->app->erp->GetEinkaufspreis($id,$menge,$adresse);
      else
        $preis = $this->app->erp->GetVerkaufspreis($id,$menge,$adresse);
      /*
      // gibt es spezial preis?
      $vk = $this->app->DB->Select("SELECT preis FROM verkaufspreise WHERE artikel='$id' AND adresse='$adresse' AND ab_menge<=$menge AND (gueltig_bis>NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 ORDER by ab_menge DESC LIMIT 1");

      if($vk <= 0)
      {
      $vk = $this->app->DB->Select("SELECT preis FROM verkaufspreise WHERE artikel='$id' AND ab_menge<=$menge AND (adresse='0' OR adresse='') AND (gueltig_bis>NOW() OR gueltig_bis='0000-00-00') AND geloescht=0 ORDER by ab_menge DESC LIMIT 1");
      }
       */

      //                        if($ab_menge<=0) $ab_menge=1;

      $name = html_entity_decode($name, ENT_QUOTES, 'UTF-8');
      echo "$name#*#$nummer#*#$projekt#*#$preis#*#$ab_menge";
    }
    exit;
  }

  function ArtikelWareneingang()
  {
    $this->app->Tpl->Add(UEBERSCHRIFT," (Wareneingang)");
    $this->ArtikelMenu();
    $this->app->Tpl->Set(PAGE,"wareneingang");
  }

  function ArtikelReservierung()
  {
    $this->app->Tpl->Add(UEBERSCHRIFT," (Reservierungen)");
    $this->ArtikelMenu();
    $this->app->Tpl->Set(PAGE,"reservierung");
  }


  function ArtikelOffeneAuftraege()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Add(TABTEXT,"Auftr&auml;ge");
    $this->ArtikelMenu();

    // easy table mit arbeitspaketen YUI als template 

    $this->app->YUI->TableSearch(TAB1,"artikel_auftraege_offen");
    /*
       $table = new EasyTable($this->app);
       $table->Query("SELECT CONCAT('<a href=\"index.php?module=auftrag&action=edit&id=',a.id,'\">',a.belegnr,'</a>') as belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.status, a.zahlungsweise, adr.kundenfreigabe as freigabe, CONCAT(a.name,'<br>', a.email) as Kunde, 
       a.zahlungsweise, ap.menge, ap.geliefert_menge as gelieferte, FORMAT(ap.preis*(100-ap.rabatt)/100,2) as preis  
       FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag, adresse adr WHERE adr.id=a.adresse 
       AND ap.artikel='$id' AND ap.geliefert_menge < ap.menge AND a.status='freigegeben'");
    //$table->DisplayNew(INHALT,"<a href=\"index.php?module=bestellung&action=edit&id=%value%\">Bestellung</a>");
    $table->DisplayNew(TAB1,"Preis","noAction");
     */
    $summe = $this->app->DB->Select("SELECT SUM(ap.menge)-SUM(ap.geliefert_menge) FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag WHERE ap.artikel='$id' AND ap.geliefert_menge < ap.menge AND a.status='freigegeben'");
    $euro= $this->app->DB->Select("SELECT SUM(ap.preis*(100-ap.rabatt)/100*ap.menge) FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag WHERE ap.artikel='$id' AND ap.geliefert_menge < ap.menge AND a.status='freigegeben'");

    $this->app->Tpl->Add(TAB1,"<table width=\"100%\"><tr><td align=\"right\">Summe offen: $summe St&uuml;ck (Summe EUR: $euro EUR)</td></tr></table>");

    $this->app->YUI->TableSearch(TAB2,"artikel_auftraege_versendet");
    /*
       $table = new EasyTable($this->app);
       $table->Query("SELECT a.belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') as datum2, a.status, a.zahlungsweise, CONCAT(a.name,'<br>', a.email) as Kunde, a.zahlungsweise, DATE_FORMAT(l.datum,'%d.%m.%Y') as lieferung, ap.menge, ap.geliefert_menge as gelieferte, FORMAT(ap.preis*(100-ap.rabatt)/100,2) as preis  FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag, adresse adr, lieferschein l WHERE l.auftragid=a.id AND adr.id=a.adresse AND ap.artikel='$id' AND a.status='abgeschlossen' ORDER by l.datum DESC LIMIT 10");
       $table->DisplayNew(TAB2,"Preis","noAction");
     */

    $this->app->Tpl->Parse(PAGE,"artikel_auftraege.tpl");

  }

  function ArtikelDateien()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->ArtikelMenu();
    $this->app->Tpl->Add(UEBERSCHRIFT," (Dateien)");
    $this->app->YUI->DateiUpload(PAGE,"Artikel",$id);
  }

  function ArtikelVerkauf()
  {
    // rechne gueltig_bis gestern aus
    // erstelle array objekt, adressse, ab_menge,preis
    // wenn es doppelte gibt rote meldung!!!
    //$this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Achtung es gibt f&uuml;r eine Kundengruppe bei einer gleichen Menge den Preis &ouml;fters! Deaktvieren oder l&ouml;schen Sie doppelte Preise!</div>");



    $this->app->Tpl->Add(UEBERSCHRIFT," (Verkauf)");
    $this->app->Tpl->Set(SUBSUBHEADING,"Verkaufspreise");
    $this->ArtikelMenu();
    $this->Preisrechner();
    $id = $this->app->Secure->GetGET("id");
    // neues arbeitspaket
    $widget = new WidgetVerkaufspreise($this->app,TAB2);
    $widget->form->SpecialActionAfterExecute("none",
        "index.php?module=artikel&action=verkauf&id=$id");

    if($this->app->Secure->GetPOST("submit")!="")
      $this->app->erp->EnableTab("tabs-2");

    $widget->Create();


    $this->app->YUI->TableSearch(TAB1,"verkaufspreise");


    $max_preis = $this->app->DB->Select("SELECT MAX(preis) FROM einkaufspreise WHERE artikel='$id' AND gueltig_bis='0000-00-00' 
        OR gueltig_bis >= NOW() LIMIT 1");

    $min_preis = $this->app->DB->Select("SELECT MIN(preis) FROM einkaufspreise WHERE artikel='$id' AND gueltig_bis='0000-00-00' 
        OR gueltig_bis >= NOW() LIMIT 1");

    $min_preis = $this->app->erp->EUR($min_preis*(($this->app->erp->GetStandardMarge()/100.0)+1.0)*1.0);
    $max_preis = $this->app->erp->EUR($max_preis*(($this->app->erp->GetStandardMarge()/100.0)+1.0)*1.0);


    $porto = $this->app->DB->Select("SELECT porto FROM artikel WHERE id='$id' LIMIT 1");
    if($porto=="1")
    {
      $this->app->Tpl->Add(TAB1,"<div class=\"warning\">Kundenspezifische Preise werden immer priorisiert!</div>");
    } else {

      if($this->app->erp->GetStandardMarge() > 0)
      {
        $this->app->Tpl->Add(TAB1,"<div class=\"warning\">Empfohlener Verkaufspreis netto (f&uuml;r teuersten VK Preis): <b>$max_preis EUR</b>!</div>");
        $this->app->Tpl->Add(TAB1,"<div class=\"warning\">Empfohlener Verkaufspreis netto (f&uuml;r billigsten VK Preis): <b>$min_preis EUR</b>!</div>");

        $this->app->Tpl->Add(TAB2,"<div class=\"warning\">Empfohlener Verkaufspreis netto (f&uuml;r teuersten VK Preis): <b>$max_preis EUR</b>!</div>");
        $this->app->Tpl->Add(TAB2,"<div class=\"warning\">Empfohlener Verkaufspreis netto (f&uuml;r billigsten VK Preis): <b>$min_preis EUR</b>!</div>");
      }
    }


    $this->app->Tpl->Parse(PAGE,"verkaufspreiseuebersicht.tpl");
  }


  function ArtikelVerkaufDisable()
  {

    $id = $this->app->Secure->GetGET("id");

    $this->app->DB->Update("UPDATE verkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) WHERE id='$id' LIMIT 1");
    $sid = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$id' LIMIT 1");
    header("Location: index.php?module=artikel&action=verkauf&id=".$sid);
    exit;
  }


  function ArtikelVerkaufDelete()
  {

    $id = $this->app->Secure->GetGET("id");

    $this->app->DB->Update("UPDATE verkaufspreise SET geloescht='1', gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) WHERE id='$id' LIMIT 1");
    $sid = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$id' LIMIT 1");
    header("Location: index.php?module=artikel&action=verkauf&id=".$sid);
    exit;
  }


  function ArtikelVerkaufCopy()
  {
    $id = $this->app->Secure->GetGET("id");

    $id = $this->app->DB->MysqlCopyRow("verkaufspreise","id",$id);
    $this->app->DB->Update("UPDATE verkaufspreise SET geloescht='0', gueltig_bis='0000-00-00' WHERE id='$id' LIMIT 1");

    $sid = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$id' LIMIT 1");
    header("Location: index.php?module=artikel&action=verkauf&id=".$sid);
    exit;
  }



  function ArtikelVerkaufEditPopup()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(OPENDISABLE,"<!--");
    $this->app->Tpl->Set(CLOSEDISABLE,"-->");


    $this->Preisrechner();
    $sid = $this->app->DB->Select("SELECT artikel FROM verkaufspreise WHERE id='$id' LIMIT 1");
    $this->ArtikelMenu($sid);
    $artikel = $this->app->DB->Select("SELECT CONCAT(name_de,' (',nummer,')') FROM artikel WHERE id='$sid' LIMIT 1");
    $this->app->Tpl->Set(UEBERSCHRIFT,"Artikel: ".$artikel);
    $this->app->Tpl->Add(UEBERSCHRIFT," (Verkauf)");

    $this->app->Tpl->Set(ABBRECHEN,"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=artikel&action=verkauf&id=$sid';\">");

    $widget = new WidgetVerkaufspreise($this->app,TAB1);
    $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=artikel&action=verkauf&id=$sid&&22#tabs-1");
    $widget->Edit();

    $this->app->Tpl->Add(TAB2,"Sie bearbeiten gerade einen Verkaufspreis. Erst nach dem Speichern k&ouml;nnen neue Preise angelegt werden.");
    $this->app->Tpl->Add(TAB3,"Sie bearbeiten gerade einen Verkaufspreis. Erst nach dem Speichern k&ouml;nnen Statistiken betrachtet werden.");
    $this->app->Tpl->Parse(PAGE,"verkaufspreiseuebersicht.tpl");
  }

  function ArtikelEinkauf()
  {
    $this->app->Tpl->Add(UEBERSCHRIFT," (Einkauf)");
    //    $this->app->Tpl->Set(SUBSUBHEADING,"Einkaufspreise");

    // rechne gueltig_bis gestern aus
    //$this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Achtung es gibt f&uuml;r diesen Artikel bei einem Lieferanten bei gleiche Menge den Preis &ouml;fters! Deaktvieren oder l&ouml;schen Sie doppelte Preise!</div>");

    $this->Preisrechner();
    $this->ArtikelMenu();
    $id = $this->app->Secure->GetGET("id");

    $standardlieferant = $this->app->DB->Select("SELECT CONCAT(adr.lieferantennummer,' ',adr.name) FROM artikel a LEFT
        JOIN adresse adr ON adr.id=a.adresse WHERE a.id='$id'");

    $herstellernummer = $this->app->DB->Select("SELECT herstellernummer FROM artikel WHERE id='$id' LIMIT 1");
    $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");


    $this->app->Tpl->Set(BUTTONLADEN,"<input type=\"button\" value=\"Standard laden\" 
        onclick=\"document.getElementById('adresse').value='$standardlieferant';
        document.getElementById('standard').checked=true;
        document.getElementById('bezeichnunglieferant').value='$name_de';
        document.getElementById('ab_menge').value='1';
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();
        if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} today = dd+'.'+mm+'.'+yyyy;
        document.getElementById('preis_anfrage_vom').value=today;
        document.getElementById('bestellnummer').value='$herstellernummer';
        \">");


    if($this->app->Conf->WFdbType=="postgre") {
      if(is_numeric($id)) {
        $stueckliste = $this->app->DB->Select("SELECT stueckliste FROM artikel WHERE id='$id' LIMIT 1");
        $produktion = $this->app->DB->Select("SELECT produktion FROM artikel WHERE id='$id' LIMIT 1");
      }} else {
        $stueckliste = $this->app->DB->Select("SELECT stueckliste FROM artikel WHERE id='$id' LIMIT 1");
        $produktion = $this->app->DB->Select("SELECT produktion FROM artikel WHERE id='$id' LIMIT 1");
      }

    // neues arbeitspaket
    $widget = new WidgetEinkaufspreise($this->app,TAB2);
    $widget->form->SpecialActionAfterExecute("none",
        "index.php?module=artikel&action=einkauf&id=$id");

    if($this->app->Secure->GetPOST("submit")!="")
      $this->app->erp->EnableTab("tabs-2");             

    $widget->Create();

    if($this->app->Secure->GetPOST("objekt")!="")
      $this->app->Tpl->Set(AKTIV_TAB2,"selected");
    else
      $this->app->Tpl->Set(AKTIV_TAB1,"selected");


    $tmp = $this->app->DB->Select("SELECT produktioninfo FROM artikel WHERE id='$id' LIMIT 1");
    if ($produktion=="1" && $tmp!="") { 
      $tmp_base64 = unserialize($this->app->erp->base64_url_decode($tmp));

      $keys = array_keys($tmp_base64);

      // START SCHLEIFE
      for($i=0;$i<count($tmp_base64);$i++) {
        $artikelnummer = $tmp_base64[$keys[$i]];
        if($artikelnummer<=0) continue;

        $artikelid  = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$artikelnummer."' LIMIT 1");
        $name_de  = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='".$artikelid."' LIMIT 1");
        $this->app->Tpl->Set(SUBSUBHEADING,"Artikelnummer: $artikelnummer $name_de");

        $table = new EasyTable($this->app);
        $table->Query("SELECT a.name_de as Artikel, a.nummer, s.menge, s.place, s.layer, REPLACE(
          if(a.stueckliste,(SELECT MIN(v.preis) FROM verkaufspreise v WHERE v.artikel=s.artikel AND (v.objekt='Standard' OR v.objekt=''))*s.menge,(SELECT MIN(e.preis) FROM einkaufspreise e WHERE e.artikel=s.artikel AND (e.objekt='Standard'))*s.menge),'.',',') as Preis, s.id
            FROM stueckliste s
            LEFT JOIN artikel a ON a.id=s.artikel 
            WHERE s.stuecklistevonartikel='$artikelid' ORDER by s.sort");
        $table->DisplayNew(INHALT,"",""); 


        $sql = "SELECT FORMAT(SUM( 
          (SELECT MIN(e.preis) FROM einkaufspreise e WHERE e.artikel=s.artikel AND (e.objekt='Standard' OR e.objekt=''))*s.menge)
          ,2)
          FROM stueckliste s
          LEFT JOIN artikel a ON a.id=s.artikel 
          WHERE s.stuecklistevonartikel='$artikelid'";


        $preis = $this->app->DB->Select($sql);
        /*
           $sql = "SELECT FORMAT(SUM(
           (SELECT MIN(v.preis) FROM verkaufspreise v WHERE v.artikel=s.artikel AND a.stueckliste=1 AND (v.objekt='Standard' OR v.objekt=''))*s.menge)
           ,2)
           FROM stueckliste s
           LEFT JOIN artikel a ON a.id=s.artikel
           WHERE s.stuecklistevonartikel='$artikelid'";

           $preis = $preis + $this->app->DB->Select($sql);
         */
        $sql = "SELECT FORMAT(SUM( 
          (SELECT MAX(e.preis) FROM einkaufspreise e WHERE e.artikel=s.artikel AND (e.objekt='Standard' OR e.objekt=''))*s.menge)
          ,2)
          FROM stueckliste s
          LEFT JOIN artikel a ON a.id=s.artikel 
          WHERE s.stuecklistevonartikel='$artikelid'";

        $preis_max = $this->app->DB->Select($sql);
        /*
           $sql = "SELECT FORMAT(SUM(
           (SELECT MAX(v.preis) FROM verkaufspreise v WHERE v.artikel=s.artikel AND a.stueckliste=1 AND (v.objekt='Standard' OR v.objekt=''))*s.menge)
           ,2)
           FROM stueckliste s
           LEFT JOIN artikel a ON a.id=s.artikel
           WHERE s.stuecklistevonartikel='$artikelid'";

           $preis_max = $preis_max + $this->app->DB->Select($sql);
         */

        $this->app->Tpl->Add(INHALT,"<div class=\"info\">St&uuml;cklisten Grundpreis bei Menge 1: <b>$preis_max bis $preis EUR</b></div>");
        $gesamtpreis = $gesamtpreis + $preis;
        $gesamtpreis_max = $gesamtpreis_max + $preis_max;
        $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");
        $this->app->Tpl->Set(INHALT,"");
      }
      //ENDE SCHLEIFE
      $this->app->Tpl->Set(SUBSUBHEADING,"Gesamtpreis:");
      $this->app->Tpl->Add(INHALT,"<div class=\"info\">Gesamtpreis bei Menge 1: <b>$gesamtpreis_max bis $gesamtpreis EUR</b></div>");
      $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");
      $this->app->Tpl->Parse(PAGE,"einkaufspreiseuebersicht_stueckliste.tpl");
    }


    else if ($stueckliste=="1") { 

      $table = new EasyTable($this->app);


      $table->Query("SELECT a.name_de as Artikel, a.nummer, s.menge, 

          REPLACE(
            if(a.stueckliste,
              (SELECT MIN(v.preis) FROM verkaufspreise v WHERE v.artikel=s.artikel AND (v.objekt='Standard' OR v.objekt='')),
              (SELECT MIN(e.preis) FROM einkaufspreise e WHERE e.artikel=s.artikel AND (e.objekt='Standard' OR e.objekt='') )),'.',',') as 'Preis pro Stk. (Min)', 

          REPLACE(
            if(a.stueckliste,(SELECT MIN(v.preis) FROM verkaufspreise v WHERE v.artikel=s.artikel AND (v.objekt='Standard' OR v.objekt='')),
              (SELECT MAX(e.preis) FROM einkaufspreise e WHERE e.artikel=s.artikel AND (e.objekt='Standard' OR e.objekt=''))),'.',',') as 'Preis Max'


          FROM stueckliste s
          LEFT JOIN artikel a ON a.id=s.artikel 
          WHERE s.stuecklistevonartikel='$id' ORDER by s.sort");

      $table->DisplayNew(INHALT,"Preis pro Stk. (Max)","noAction"); 

      $sql = "SELECT SUM( 
        (SELECT MAX(e.preis) FROM einkaufspreise e WHERE e.artikel=s.artikel AND (e.objekt='Standard' OR e.objekt=''))*s.menge)
        FROM stueckliste s
        LEFT JOIN artikel a ON a.id=s.artikel 
        WHERE s.stuecklistevonartikel='$id'";

      $preis_max = $this->app->DB->Select($sql);

      $sql = "SELECT SUM( 
        (SELECT MIN(e.preis) FROM einkaufspreise e WHERE e.artikel=s.artikel AND (e.objekt='Standard' OR e.objekt=''))*s.menge)
        FROM stueckliste s
        LEFT JOIN artikel a ON a.id=s.artikel 
        WHERE s.stuecklistevonartikel='$id'";

      $preis = $this->app->DB->Select($sql);

      $this->app->Tpl->Add(INHALT,"<div class=\"info\">St&uuml;cklisten Grundpreis bei Menge 1: <b>$preis EUR - $preis_max EUR</b></div>");
      $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");
      $this->app->Tpl->Parse(PAGE,"einkaufspreiseuebersicht_stueckliste.tpl");
    }

    else 
    {
      // easy table mit arbeitspaketen YUI als template 
      $this->app->YUI->TableSearch(TAB1,"einkaufspreise");

      if($this->app->Conf->WFdbType=="postgre") {
        if(is_numeric($id)) {           
          $adresse = $this->app->DB->Select("SELECT adresse FROM artikel WHERE id='$id' LIMIT 1"); 
          $hauptlieferant = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' LIMIT 1");

          if($this->app->Conf->WFdbType=="postgre") {
            if(is_numeric($id))
              $min_preis = $this->app->DB->Select("SELECT ROUND(MIN(preis),2) FROM verkaufspreise WHERE artikel='$id' AND objekt='Standard' AND adresse='' LIMIT 1");
          } else {
            $min_preis = $this->app->DB->Select("SELECT FORMAT(MIN(preis),2) FROM verkaufspreise WHERE artikel='$id' AND objekt='Standard' AND adresse='' LIMIT 1");
          }
        }} else {
          $adresse = $this->app->DB->Select("SELECT adresse FROM artikel WHERE id='$id' LIMIT 1");
          $hauptlieferant = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' LIMIT 1");
          $min_preis = $this->app->DB->Select("SELECT FORMAT(MIN(preis),2) FROM verkaufspreise WHERE artikel='$id' AND gueltig_bis='0000-00-00' 
              OR gueltig_bis >= NOW() LIMIT 1");
          $max_preis = $this->app->DB->Select("SELECT FORMAT(MAX(preis),2) FROM verkaufspreise WHERE artikel='$id' AND gueltig_bis='0000-00-00' 
              OR gueltig_bis >= NOW() LIMIT 1");

        }
      $this->app->Tpl->Add(TAB1,"<div class=\"info\">Der Hauptlieferant ist <b>$hauptlieferant</b></div>");

      $min_preis = $this->app->erp->EUR($min_preis/(($this->app->erp->GetStandardMarge()/100.0)+1.0)*1.0);
      $max_preis = $this->app->erp->EUR($max_preis/(($this->app->erp->GetStandardMarge()/100.0)+1.0)*1.0);

      /*      $this->app->Tpl->Add(TAB1,"<div class=\"warning\">Empfohlener Einkaufspreis (f&uuml;r geringsten VK Preis): <b>$min_preis EUR</b>!</div>");
              $this->app->Tpl->Add(TAB2,"<div class=\"warning\">Empfohlener Einkaufspreis (f&uuml;r geringsten VK Preis): <b>$min_preis EUR</b>!</div>");
              $this->app->Tpl->Add(TAB1,"<div class=\"warning\">Empfohlener Einkaufspreis (f&uuml;r max VK Preis): <b>$max_preis EUR</b>!</div>");
              $this->app->Tpl->Add(TAB2,"<div class=\"warning\">Empfohlener Einkaufspreis (f&uuml;r max VK Preis): <b>$max_preis EUR</b>!</div>");
       */
      $this->app->Tpl->Parse(PAGE,"einkaufspreiseuebersicht.tpl");



    }


  }


  function ArtikelEinkaufEditPopup()
  {
    //$frame = $this->app->Secure->GetGET("frame");
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(OPENDISABLE,"<!--");
    $this->app->Tpl->Set(CLOSEDISABLE,"-->");
    $this->Preisrechner();


    $sid = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE id='$id' LIMIT 1");
    $this->ArtikelMenu($sid);
    $artikel = $this->app->DB->Select("SELECT CONCAT(name_de,' (',nummer,')') FROM artikel WHERE id='$sid' LIMIT 1");
    $this->app->Tpl->Set(UEBERSCHRIFT,"Artikel: ".$artikel);
    $this->app->Tpl->Add(UEBERSCHRIFT," (Einkauf)");

    $this->app->Tpl->Set(ABBRECHEN,"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=artikel&action=einkauf&id=$sid';\">");

    $widget = new WidgetEinkaufspreise($this->app,TAB1);
    $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=artikel&action=einkauf&id=$sid#tabs-1");
    $widget->Edit();



    $this->app->Tpl->Add(TAB2,"Sie bearbeiten gerade einen Einkaufspreis. Erst nach dem Speichern k&ouml;nnen neue Preise angelegt werden.");
    $this->app->Tpl->Add(TAB3,"Sie bearbeiten gerade einen Einkaufspreis. Erst nach dem Speichern k&ouml;nnen Statistiken betrachtet werden.");
    /*
       $widget = new WidgetEinkaufspreise(&$this->app,TAB2);
       $widget->form->SpecialActionAfterExecute("none",
       "index.php?module=artikel&action=einkauf&id=$id");
       $widget->Create();
     */
    $this->app->Tpl->Parse(PAGE,"einkaufspreiseuebersicht.tpl");
  }

  function ArtikelEinkaufDisable()
  {
    //   $this->ArtikelMenu();
    $id = $this->app->Secure->GetGET("id");


    $this->app->DB->Update("UPDATE einkaufspreise SET gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) WHERE id='$id' LIMIT 1");
    $sid = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE id='$id' LIMIT 1");
    header("Location: index.php?module=artikel&action=einkauf&id=".$sid);
    exit;


  }

  function ArtikelEinkaufDelete()
  {
    //    $this->ArtikelMenu();
    $id = $this->app->Secure->GetGET("id");


    $this->app->DB->Update("UPDATE einkaufspreise SET geloescht='1',gueltig_bis=DATE_SUB(NOW(),INTERVAL 1 DAY) WHERE id='$id' LIMIT 1");
    $sid = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE id='$id' LIMIT 1");
    header("Location: index.php?module=artikel&action=einkauf&id=".$sid);
    exit;


  }



  function ArtikelEinkaufCopy()
  {
    $id = $this->app->Secure->GetGET("id");

    $id = $this->app->DB->MysqlCopyRow("einkaufspreise","id",$id);
    $this->app->DB->Update("UPDATE einkaufspreise SET geloescht='0', gueltig_bis='0000-00-00' WHERE id='$id' LIMIT 1");


    //$this->app->DB->Update("UPDATE einkaufspreise SET geloescht='1' WHERE id='$id' LIMIT 1");
    $sid = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE id='$id' LIMIT 1");
    header("Location: index.php?module=artikel&action=einkauf&id=".$sid);
    exit;


  }


  function ArtikelCopy()
  {
    $id = $this->app->Secure->GetGET("id");

    $this->app->DB->MysqlCopyRow("artikel","id",$id);

    $idnew = $this->app->DB->GetInsertID();
    $this->app->DB->Update("UPDATE artikel SET nummer='' WHERE id='$idnew' LIMIT 1");

    // wenn stueckliste
    $stueckliste = $this->app->DB->Select("SELECT stueckliste FROM artikel WHERE id='$id' LIMIT 1");
    if($stueckliste==1)
    {

      $artikelarr = $this->app->DB->SelectArr("SELECT * FROM stueckliste WHERE stuecklistevonartikel='$id'");
      for($i=0;$i<count($artikelarr);$i++)
      {
        $sort = $artikelarr[$i]['sort'];        
        $artikel = $artikelarr[$i]['artikel'];  
        $referenz = $artikelarr[$i]['referenz'];        
        $place = $artikelarr[$i]['place'];      
        $layer = $artikelarr[$i][layer];        
        $stuecklistevonartikel = $idnew;        
        $menge = $artikelarr[$i][menge];
        $firma = $artikelarr[$i][firma];

        $this->app->DB->Insert("INSERT INTO stueckliste (id,sort,artikel,referenz,place,layer,stuecklistevonartikel,menge,firma) VALUES
            ('','$sort','$artikel','$referenz','$place','$layer','$stuecklistevonartikel','$menge','$firma')"); 
      }
    }


    //TODO hinweis es wuren keine Preise kopiert


    // artikelbilder kopieren



    // eventuell einkaufspreise verkaufspreise und stueckliste kopieren?
    $msg = $this->app->erp->base64_url_encode("<div class=error>Sie befinden sich in der neuen Kopie des Artikel. Bitte legen Sie Verkaufs- und Einkaufspreise und Bilder bzw. Dateien an! Dies wurden nicht kopiert!</div>"); 
    header("Location: index.php?module=artikel&action=edit&msg=$msg&id=".$idnew);
    exit;

  }





  function ArtikelProjekte()
  {
    $this->app->Tpl->Add(UEBERSCHRIFT," (Projekte)");
    $this->ArtikelMenu();
    $this->app->Tpl->Set(PAGE,"hier sieht man in welchen projekten es verwendet wird");
  }

  function ArtikelLager()
  {
    $id = $this->app->Secure->GetGET("id");
    $msg = $this->app->Secure->GetGET("msg");

    $this->app->erp->LagerArtikelZusammenfassen($id);

    $msg = $this->app->erp->base64_url_decode($msg);
    $this->app->Tpl->Set(MESSAGE,$msg);

    $this->ArtikelMenu();
    $this->app->Tpl->Add(TAB1,"<h2>Lagerbestand</h2>");

    // easy table mit arbeitspaketen YUI als template 
    $table = new EasyTable($this->app);

    $mindesthaltbarkeitsdatum = $this->app->DB->Select("SELECT mindesthaltbarkeitsdatum FROM artikel WHERE id='$id' LIMIT 1");
    $seriennummern = $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id='$id' LIMIT 1");
    $chargenverwaltung= $this->app->DB->Select("SELECT chargenverwaltung FROM artikel WHERE id='$id' LIMIT 1");


    if($seriennummern!="vomprpodukteinlagern" && $chargenverwaltung <2 && $mindesthaltbarkeitsdatum!="1")
    {
      $table->Query("SELECT CONCAT(l.bezeichnung,' ',lp.kurzbezeichnung) as lager , lpi.menge as menge, lpi.vpe as VPE,p.abkuerzung as projekt, 
          lpi.id FROM lager_platz_inhalt lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id  
          LEFT JOIN lager l ON l.id=lp.lager WHERE lpi.artikel='$id' ");


      if($this->app->erp->RechteVorhanden("artikel","auslagern") || $this->app->erp->RechteVorhanden("artikel","einlagern") 
          || $this->app->erp->RechteVorhanden("artikel","umlagern"))
        $table->DisplayNew(INHALT,"<a onclick=\"var menge =  prompt('St&uuml;ckzahl der Artikel die aus diesem Regal genommen werden sollen:',1); var grund =  prompt('Auslagerungsgrund:','Muster'); if(menge > 0 && (grund!=null && grund!='')) { window.location.href='index.php?module=artikel&action=auslagern&id=$id&lid=%value%&menge='+menge+'&grund='+grund;}\" href=\"#\"><img src=\"./themes/[THEME]/images/loeschen.png\" border=\"0\"></a>
            <a onclick=\"var menge =  prompt('St&uuml;ckzahl der Artikel in dieses Regal legen:',1); var grund =  prompt('Einlagerungsgrund:','Anpassung im Artikel'); if(menge > 0 && (grund!=null && grund!='')) { window.location.href='index.php?module=artikel&action=einlagern&id=$id&lid=%value%&menge='+menge+'&grund='+grund;}\" href=\"#\"><img src=\"./themes/[THEME]/images/einlagern.png\" border=\"0\"></a>
            <a onclick=\"var menge =  prompt('St&uuml;ckzahl der Artikel in dieses Regal umlagern:',%field1%); var grund =  prompt('Grund:','Anpassung im Artikel'); if(menge > 0 && (grund!=null && grund!='')) { window.location.href='index.php?module=artikel&action=umlagern&id=$id&lid=%value%&menge='+menge+'&grund='+grund;}\" href=\"#\"><img src=\"./themes/[THEME]/images/forward.png\" border=\"0\"></a>
            ");
      else
        $table->DisplayNew(INHALT,"");
    } else {

      $table->Query("SELECT lp.kurzbezeichnung, lpi.menge as menge, lpi.vpe as VPE,p.abkuerzung as projekt 
          FROM lager_platz_inhalt lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id  WHERE lpi.artikel='$id' ");



      if($this->app->erp->RechteVorhanden("artikel","auslagern") || $this->app->erp->RechteVorhanden("artikel","einlagern") 
          || $this->app->erp->RechteVorhanden("artikel","umlagern"))
        $table->DisplayNew(INHALT,"Projekt","noAction");
      else
        $table->DisplayNew(INHALT,"");


    }


    $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");

    $this->app->Tpl->Set(INHALT,"");

    $mindesthaltbarkeitsdatum = $this->app->DB->Select("SELECT mindesthaltbarkeitsdatum FROM artikel WHERE id='$id' LIMIT 1");  
    $chargenverwaltung = $this->app->DB->Select("SELECT chargenverwaltung FROM artikel WHERE id='$id' LIMIT 1");        
    $seriennummern = $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id='$id' LIMIT 1");        

    if($seriennummern=="vomproduktlagereinlager" || $mindesthaltbarkeitsdatum=="1" || $chargenverwaltung=="2")
    {
      $this->app->Tpl->Add(TAB1,"<center>
          <input type=\"button\" value=\"Einlagern\" onclick=\"window.location.href='index.php?module=lager&action=bucheneinlagern&artikelid=$id&back=artikel'\">
          <input type=\"button\" value=\"Auslagern\" onclick=\"window.location.href='index.php?module=lager&action=buchenauslagern&artikelid=$id&back=artikel'\">
          <input type=\"button\" value=\"Umlagern\" onclick=\"window.location.href='index.php?module=lager&action=buchenauslagern&cmd=umlagern&artikelid=$id&back=artikel'\">
          </center>");
    } else {
      $this->app->Tpl->Add(TAB1,"<center><input type=\"button\" value=\"Artikel in neuen Lagerplatz einlagern\" onclick=\"window.location.href='index.php?module=lager&action=bucheneinlagern&artikelid=$id&back=artikel'\"></center>");
    }
    //    $this->app->Tpl->Set(SUBSUBHEADING,"Reservierungen Stand ".date('d.m.Y'));
    $this->app->Tpl->Add(TAB1,$this->app->erp->ArtikelLagerInfo($id));

    if($this->app->erp->Version()!="stock")     
    {
      $this->app->Tpl->Add(TAB1,"<h2>Reservierungen</h2>");

      // easy table mit arbeitspaketen YUI als template 
      $table = new EasyTable($this->app);
      $table->Query("SELECT adr.name as kunde, r.menge, if(r.datum='0000-00-00','Kein Datum hinterlegt',r.datum) as bis,
          p.abkuerzung as projekt,r.grund, r.id FROM lager_reserviert r LEFT JOIN artikel a ON a.id=r.artikel LEFT JOIN projekt p ON 
          p.id=r.projekt LEFT JOIN adresse adr ON r.adresse=adr.id WHERE r.firma='{$this->app->User->GetFirma()}' AND a.id='$id'");


      $summe = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$id'");
      $reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='$id' AND datum >= NOW()");
      //    if($this->app->User->GetType()=="admin")
      if($this->app->erp->RechteVorhanden("artikel","ausreservieren"))
        $table->DisplayNew(INHALT,"<a onclick=\"var menge =  prompt('Anzahl Artikel aus Reservierung entfernen:',1); if(menge > 0) window.location.href='index.php?module=artikel&action=ausreservieren&id=$id&lid=%value%&menge='+menge;\" href=\"#\"><img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\"></a>");
      else
        $table->DisplayNew(INHALT, "");
      $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");

      $this->app->Tpl->Set(INHALT,"");

      $this->app->Tpl->Add(TAB1,"<h2>Offene Auftr&auml;ge</h2>");
      // easy table mit arbeitspaketen YUI als template 
      $table = new EasyTable($this->app);
      $table->Query("SELECT 

          CONCAT('<a href=\"index.php?module=auftrag&action=edit&id=',a.id,'\">',a.belegnr,'</a>') as belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.status, 

          ap.menge,
          CONCAT((SELECT SUM(li.menge) FROM lager_reserviert li WHERE li.objekt='auftrag' AND li.parameter=a.id AND li.artikel='$id'),'&nbsp;

            <a onclick=\"var menge =  prompt(\'Anzahl Artikel aus Reservierung entfernen:\',1); if(menge > 0) window.location.href=\'index.php?module=artikel&action=ausreservieren&id=$id&lid=',
            (SELECT li.id FROM lager_reserviert li WHERE li.objekt='auftrag' AND li.parameter=a.id AND li.artikel='$id' LIMIT 1)
            ,'&menge=\'+menge;\" href=\"#\"><img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\"></a>

            ') as reserviert,


          a.zahlungsweise, adr.kundenfreigabe as freigabe, CONCAT(a.name,'<br>', a.email) as Kunde, a.zahlungsweise, 
          ap.geliefert_menge as gelieferte, 
          FORMAT(ap.preis,2) as preis  FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag, adresse adr WHERE adr.id=a.adresse AND ap.artikel='$id' AND ap.geliefert_menge < ap.menge AND a.status='freigegeben' 


          ");
      //$table->DisplayNew(INHALT,"<a href=\"index.php?module=bestellung&action=edit&id=%value%\">Bestellung</a>");
      $table->DisplayNew(INHALT,"Preis","noAction");

      $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");

      $this->app->Tpl->Set(INHALT,"");
      $this->app->Tpl->Add(TAB1,"<h2>Offene Produktionen</h2>");
      // easy table mit arbeitspaketen YUI als template 
      $table = new EasyTable($this->app);
      $table->Query("SELECT a.belegnr, DATE_FORMAT(a.datum,'%d.%m.%Y') as datum, a.status, CONCAT(a.name,'<br>', a.email) as Kunde, ap.menge FROM produktion_position ap LEFT JOIN produktion a ON a.id=ap.produktion, adresse adr WHERE adr.id=a.adresse AND ap.artikel='$id' AND (a.status!='abgeschlossen' AND a.status!='storniert')");
      //$table->DisplayNew(INHALT,"<a href=\"index.php?module=bestellung&action=edit&id=%value%\">Bestellung</a>");
      $table->DisplayNew(INHALT,"Menge","noAction");

      $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");

      $this->app->Tpl->Set(INHALT,"");
      $this->app->Tpl->Add(TAB1,"<h2>Offene Bestellungen</h2>");

      $table = new EasyTable($this->app);
      $table->Query("SELECT DATE_FORMAT(b.datum,'%d.%m.%Y') as datum, CONCAT('<a href=\"index.php?module=bestellung&action=edit&id=',b.id,'\" target=\"_blank\">',b.belegnr,'</a>') as 'bestellung Nr.', bp.bestellnummer as Nummer, bp.menge, bp.geliefert, bp.vpe as VPE, a.lieferantennummer as lieferant, a.name as name, if(bp.lieferdatum!='0000-00-00', DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum, b.status as status_Bestellung, bp.bestellung
          FROM bestellung_position bp LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN adresse a ON b.adresse=a.id
          WHERE artikel='$id' AND b.status!='storniert' AND b.status!='abgeschlossen' AND bp.geliefert<bp.menge ORDER by bp.lieferdatum DESC");
      $table->DisplayNew(INHALT,"<a href=\"index.php?module=bestellung&action=pdf&id=%value%\"><img src=\"./themes/new/images/pdf.png\" border=\"0\"></a>&nbsp;      <a href=\"index.php?module=bestellung&action=edit&id=%value%\" target=\"_blank\"><img src=\"./themes/new/images/edit.png\" border=\"0\"></a>");
      $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");


    }   
    $this->app->Tpl->Set(INHALT,"");
    $this->app->Tpl->Add(TAB1,"<h2>Lagerplatz Bewegungen</h2>");
    // easy table mit arbeitspaketen YUI als template 
    /*
       $table = new EasyTable($this->app);
       if($this->app->Conf->WFdbType=="postgre") {
       if(is_numeric($id))
       $table->Query("SELECT to_char(lpi.zeit,'DD.MM.YYYY') as datum, lp.kurzbezeichnung as lager, lpi.menge as menge, lpi.vpe as VPE, 
       CASE WHEN lpi.eingang='1' THEN 'Eingang' ELSE 'Ausgang'
       END as Richtung, substring(lpi.referenz,1,60) as referenz, lpi.bearbeiter as bearbeiter, p.abkuerzung as projekt, lpi.id FROM lager_bewegung lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id WHERE lpi.artikel='1' order by lpi.zeit DESC");
       } else {
       $table->Query("SELECT DATE_FORMAT(lpi.zeit,'%d.%m.%Y') as datum, lp.kurzbezeichnung as lager, lpi.menge as menge, lpi.vpe as VPE, if(lpi.eingang,'Eingang','Ausgang') as Richtung, substring(lpi.referenz,1,60) as referenz, lpi.bearbeiter as bearbeiter, p.abkuerzung as projekt, 
       lpi.id FROM lager_bewegung lpi LEFT JOIN lager_platz as lp ON lpi.lager_platz=lp.id LEFT JOIN projekt p ON lpi.projekt=p.id  WHERE lpi.artikel='$id' order by lpi.zeit DESC");
       }
    //$table->DisplayNew(INHALT,"<a href=\"index.php?module=bestellung&action=edit&id=%value%\">Bestellung</a>");
    $table->DisplayNew(INHALT,"");
    $this->app->Tpl->Parse(TAB1,"rahmen70.tpl");
     */
    $this->app->YUI->TableSearch(TAB1,"lagerbewegungartikel");


    $this->app->Tpl->Set(INHALT,"");

    //$this->app->Tpl->Set(TABTEXT,"Lagerbestand");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");

  }


  function ArtikelChargeDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    $sid = $this->app->Secure->GetGET("sid");

    $data = $this->app->DB->SelectArr("SELECT * FROM lager_charge WHERE id='$sid' LIMIT 1");
    $lager_platz = $data[0]['lager_platz'];
    $artikel = $data[0]['artikel'];
    $menge = $data[0]['menge'];

    //$lager_platz_inhalt_id = $this->app->DB->Select("UPDATE lager_platz_inhalt SET menge=menge-$menge WHERE lager_platz='$lager_platz' AND artikel='$artikel'
    //                  AND menge >= '$menge' LIMIT 1");

    $this->app->erp->LagerAuslagernRegal($artikel,$lager_platz,$menge,$projekt,"Auslagern bzw Lageranpassung");

    $this->app->DB->Delete("DELETE FROM lager_charge WHERE id='$sid' LIMIT 1");
    header("Location: index.php?module=artikel&action=chargen&id=$id");
    exit;
  }     

  function ArtikelMHDDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    $sid = $this->app->Secure->GetGET("sid");
    $tmp = rand();


    $data = $this->app->DB->SelectArr("SELECT * FROM lager_mindesthaltbarkeitsdatum WHERE id='$sid' LIMIT 1");
    $lager_platz = $data[0]['lager_platz'];
    $artikel = $data[0]['artikel'];
    $menge = $data[0]['menge'];

    /*
       $lager_platz_inhalt_menge = $this->app->DB->Select("SELECT menge FROM lager_platz_inhalt WHERE lager_platz='$lager_platz' 
       AND artikel='$artikel' AND menge >= '$menge' LIMIT 1");

       $this->app->erp->DumpVar("test $tmp menge $menge artikel $artikel lager_platz $lager_platz alte menge $lager_platz_inhalt_menge");
     */

    //          $lager_platz_inhalt_id = $this->app->DB->Update("UPDATE lager_platz_inhalt SET menge=menge-$menge WHERE lager_platz='$lager_platz' AND artikel='$artikel'
    //                          AND menge >= '$menge' LIMIT 1");

    $this->app->erp->LagerAuslagernRegal($artikel,$lager_platz,$menge,$projekt,"Auslagern bzw Lageranpassung");

    $this->app->DB->Delete("DELETE FROM lager_mindesthaltbarkeitsdatum WHERE id='$sid' LIMIT 1");

    header("Location: index.php?module=artikel&action=mindesthaltbarkeitsdatum&id=$id");
    exit;
  }     

  function ArtikelChargen()
  {
    $this->ArtikelMenu();
    $this->app->Tpl->Set(TABTEXT,"Chargen im Lager");

    $this->app->YUI->AutoComplete("lagerplatz","lagerplatz");

    $id = $this->app->Secure->GetGET("id");

    if($this->app->Secure->GetPOST("anlegen")!="")
    {
      $menge = $this->app->Secure->GetPOST("menge");
      $charge = $this->app->Secure->GetPOST("charge");
      $lagerplatz = $this->app->Secure->GetPOST("lagerplatz");
      $lagerplatz = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$lagerplatz' LIMIT 1");

      if(is_numeric($menge) && is_numeric($lagerplatz) && $charge!="")
      {
        $this->app->erp->AddChargeLager($id,$menge,$lagerplatz,date('Y-m-d'),$charge);
      } else {
        $this->app->Tpl->Add(TAB1,"<div class=error>Fehler: Bitte Menge, Charge und Lager angeben!</div>");     
      }
    }

    $menge = $this->app->erp->ArtikelImLager($id);
    $charge = $this->app->DB->Select("SELECT SUM(menge) FROM lager_charge WHERE artikel='$id'");
    if($menge > $charge)
      $this->app->Tpl->Add(TAB1,"<div class=error>Achtung: Es sind ".($menge-$charge)." Eintr&auml;ge zu wenig vorhanden!</div>");      
    else if ($menge < $charge)
      $this->app->Tpl->Add(TAB1,"<div class=error>Achtung: Es sind ".($charge-$menge)." Eintr&auml;ge zu viel vorhanden!</div>");       

    $this->app->Tpl->Add(TAB1,"<br><center><form method=\"post\" action=\"\">Menge:&nbsp;<input name=\"menge\" type=\"text\" size=\"5\" value=\"1\">&nbsp;Lager:&nbsp;<input type=\"text\" size=\"20\" id=\"lagerplatz\" name=\"lagerplatz\">&nbsp;Charge:&nbsp;<input type=text size=\"15\" id=\"charge\" name=\"charge\">&nbsp;<input type=\"submit\" value=\"anlegen\" name=\"anlegen\"></form></center>");


    $this->app->YUI->TableSearch(TAB1,"chargen");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }


  function ArtikelMHD()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->ArtikelMenu();
    $this->app->Tpl->Set(TABTEXT,"Mindesthaltbarkeitsdatum");

    $this->app->YUI->DatePicker("datum");
    $this->app->YUI->AutoComplete("lagerplatz","lagerplatz");

    if($this->app->Secure->GetPOST("anlegen")!="")
    {
      $menge = $this->app->Secure->GetPOST("menge");
      $datum = $this->app->Secure->GetPOST("datum");
      $charge = $this->app->Secure->GetPOST("charge");
      $lagerplatz = $this->app->Secure->GetPOST("lagerplatz");
      $datum = $this->app->String->Convert($datum,"%1.%2.%3","%3-%2-%1");
      $lagerplatz = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$lagerplatz' LIMIT 1");

      if(is_numeric($menge) && is_numeric($lagerplatz) && $datum!="--")
      {
        $this->app->erp->AddMindesthaltbarkeitsdatumLager($id,$menge,$lagerplatz,$datum,$charge);
      } else {
        $this->app->Tpl->Add(TAB1,"<div class=error>Fehler: Bitte Menge, MHD und Lager angeben!</div>");        
      }
    }

    $menge = $this->app->erp->ArtikelImLager($id);
    $mhd = $this->app->DB->Select("SELECT SUM(menge) FROM lager_mindesthaltbarkeitsdatum WHERE artikel='$id'");
    if($menge > $mhd)
      $this->app->Tpl->Add(TAB1,"<div class=error>Achtung: Es sind ".($menge-$mhd)." Eintr&auml;ge zu wenig vorhanden!</div>"); 
    else if ($menge < $mhd)
      $this->app->Tpl->Add(TAB1,"<div class=error>Achtung: Es sind ".($mhd-$menge)." Eintr&auml;ge zu viel vorhanden!</div>");  

    $this->app->Tpl->Add(TAB1,"<br><center><form method=\"post\" action=\"\">Menge:&nbsp;<input name=\"menge\" type=\"text\" size=\"5\" value=\"1\">&nbsp;MHD:&nbsp;<input type=text size=\"15\" id=\"datum\" name=\"datum\">&nbsp;Lager:&nbsp;<input type=\"text\" size=\"20\" id=\"lagerplatz\" name=\"lagerplatz\">&nbsp;Charge (optional):&nbsp;<input type=text size=\"15\" id=\"charge\" name=\"charge\">&nbsp;<input type=\"submit\" value=\"anlegen\" name=\"anlegen\"></form></center>");


    $this->app->YUI->TableSearch(TAB1,"mindesthaltbarkeitsdatum");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function ArtikelSeriennummern()
  {
    $this->ArtikelMenu();
    $this->app->Tpl->Set(TABTEXT,"Seriennummern");

    $id = $this->app->Secure->GetGET("id");
    $etiketten = $this->app->Secure->GetPOST("etiketten");

    $letzteseriennummer = $this->app->DB->Select("SELECT letzteseriennummer FROM artikel WHERE id='$id' LIMIT 1");

    $seriennummern = $this->app->DB->Select("SELECT seriennummern FROM artikel WHERE id='$id' LIMIT 1");        
    if($seriennummern=="eigene")
    {
      if($this->app->Secure->GetPOST("erstellen")!="")
      {
        $menge = $this->app->Secure->GetPOST("menge");
        $lager_platz = $this->app->Secure->GetPOST("lager_platz");
        $lager = $this->app->Secure->GetPOST("lager");
        $startnummer = $this->app->Secure->GetPOST("startnummer");
        $etikettendrucker = $this->app->Secure->GetPOST("etikettendrucker");

        if($menge <=0)
        {
          $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Ung&uuml;ltige Menge! Es wurden keine Seriennummern erzeugt!</div>");
          header("Location: index.php?module=artikel&action=seriennummern&id=$id&msg=$msg#tabs-3");
          exit;
        }


        if($lager=="1")
        {
          $checklager = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung='$lager_platz' AND kurzbezeichnung!='' LIMIT 1");
          if($checklager <=0)
          {
            $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Lager gibt es nicht! Es wurden keine Seriennummern erzeugt!</div>");
            header("Location: index.php?module=artikel&action=seriennummern&id=$id&msg=$msg#tabs-3");
            exit;
          }
        }
        
        $drucken = $this->app->Secure->GetPOST("drucken");

        if($startnummer=="") $startnummer="10000000";
        $startnummer_length = strlen($startnummer);
        
        $seriennummer = $startnummer;
        for($imenge=0;$imenge < $menge;$imenge++)
        {
          $seriennummer = str_pad($seriennummer, $startnummer_length, "0", STR_PAD_LEFT); 
          if($drucken=="1")
          {
            $this->app->erp->EtikettenDrucker($etiketten,1,"artikel",$id,array('seriennummer'=>$seriennummer),"",$etikettendrucker);
          }

          if($lager=="1")
          {
            $this->app->erp->AddSeriennummerLager($id,$checklager,$seriennummer,$internebemerkung="Automatisch erzeugt ");
            $this->app->erp->LagerEinlagern($id,1,$checklager,"","Automatisch durch Seriennummerngenerator");
          }
          $this->app->DB->Update("UPDATE artikel SET letzteseriennummer='$seriennummer' WHERE id='$id'");
          $seriennummer = $seriennummer + 1;
        }
        $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Seriennummern erstellt!</div>");
        header("Location: index.php?module=artikel&action=seriennummern&id=$id&msg=$msg#tabs-3");
        exit;
      } 
    } else {
      $this->app->Tpl->Set(STARTDISABLE,"<!--");
      $this->app->Tpl->Set(ENDEDISABLE,"-->");
      $this->app->Tpl->Set(TAB3,"<div class=\"info\">Seriennummer Generator inaktiv, da Seriennummer im Artikel nicht auf eigene erzeugen eingestellt ist.</div>");

    }

    if($letzteseriennummer!="")
      $this->app->Tpl->Set(LETZTESERIENNUMMER,"<br>Zuletzt vergebene Nr.: ".$letzteseriennummer);

    $this->app->Tpl->Set(ETIKETTEN,$this->app->erp->GetSelectEtiketten("seriennummer",$etiketten)); 
    $this->app->Tpl->Set(ETIKETTENDRUCKER,$this->app->erp->GetSelectEtikettenDrucker()); 

    $this->app->YUI->AutoComplete('lager_platz','lagerplatz');
    $this->app->YUI->TableSearch(TAB1,"seriennummernlager");
    $this->app->YUI->TableSearch(TAB2,"seriennummern");
    $this->app->Tpl->Parse(PAGE,"artikel_seriennnummern.tpl");
  }

  function ArtikelStueckliste()
  {
    $this->app->Tpl->Add(UEBERSCHRIFT," (St&uuml;ckliste)");
    $this->ArtikelMenu();
    $id = $this->app->Secure->GetGET("id");

    if($this->app->Secure->GetPOST("artikel")!="")
      $this->app->Tpl->Set(AKTIV_TAB2,"selected");
    else
      $this->app->Tpl->Set(AKTIV_TAB1,"selected");

    // neues arbeitspaket
    $widget = new WidgetStueckliste($this->app,TAB2);
    $widget->form->SpecialActionAfterExecute("none",
        "index.php?module=artikel&action=stueckliste&id=$id");
    $this->app->Tpl->Set(TMPSCRIPT,"<script type=\"text/javascript\">$(document).ready(function(){ $('#tabs').tabs('select', 1); });</script>");
    $widget->form->SpecialActionAfterExecute("none",
        "index.php?module=artikel&action=stueckliste&id=$id#tabs-1");



    $widget->Create();


    $this->app->YUI->TableSearch(TAB1,"stueckliste");

    $stueck = $this->app->erp->ArtikelAnzahlLagerStueckliste($id);

    $this->ArtikelStuecklisteImport(TAB3);

    $this->app->Tpl->Add(TAB1,"<center><button onclick=\"if(!confirm('Wirklich St&uuml;ckliste leeren?')) return false; else window.location.href='index.php?module=artikel&action=stuecklisteempty&id=$id';\">St&uuml;ckliste leeren</button></center><br><br>");

    $this->app->Tpl->Add(TAB1,"<div class=\"info\">Aktuell k&ouml;nnen $stueck St&uuml;ck produziert werden (<a href=\"index.php?module=artikel&action=stuecklisteetiketten&id=".$id."\">Etiketten f&uuml;r St&uuml;ckliste drucken</a>)</div>");

    $this->app->Tpl->Parse(PAGE,"stuecklisteuebersicht.tpl");
  }

  function ArtikelStuecklisteEmpty()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->DB->Delete("DELETE FROM stueckliste WHERE stuecklistevonartikel='$id'");
    $this->ArtikelStueckliste();
  }


  function UpStueckliste()
  {
    $this->app->YUI->SortListEvent("up","stueckliste","stuecklistevonartikel");
    $this->ArtikelStueckliste();
  }

  function DownStueckliste()
  {
    $this->app->YUI->SortListEvent("down","stueckliste","stuecklistevonartikel");
    $this->ArtikelStueckliste();
  }


  function DelStueckliste()
  {
    $id = $this->app->Secure->GetGET("id");
    $sort = $this->app->DB->Select("SELECT sort FROM stueckliste WHERE id='$id' LIMIT 1");
    $sid = $this->app->DB->Select("SELECT stuecklistevonartikel FROM stueckliste WHERE id='$id' LIMIT 1");

    $this->app->DB->Delete("DELETE FROM stueckliste WHERE id='$id'");

    $this->app->DB->Delete("UPDATE stueckliste SET sort=sort-1 WHERE stuecklistevonartikel='$sid' AND sort > $sort LIMIT 1");

    header("Location: index.php?module=artikel&action=stueckliste&id=".$sid);
    exit;
  }


  function ArtikelInStueckliste()
  {
    $this->ArtikelMenu();
    $this->app->Tpl->Set(TABTEXT,"In St&uuml;ckliste von folgenden Artikel vorhanden");
    $this->app->YUI->TableSearch(TAB1,"instueckliste");
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function ArtikelStuecklisteEditPopup()
  {
    $id = $this->app->Secure->GetGET("id");

    $sid = $this->app->DB->Select("SELECT stuecklistevonartikel FROM stueckliste WHERE id='$id' LIMIT 1");
    $this->ArtikelMenu($sid);
    $artikel = $this->app->DB->Select("SELECT CONCAT(name_de,' (',nummer,')') FROM artikel WHERE id='$sid' LIMIT 1");
    $this->app->Tpl->Set(UEBERSCHRIFT,"Artikel: ".$artikel);
    $this->app->Tpl->Add(UEBERSCHRIFT," (St&uuml;ckliste)");

    $this->app->Tpl->Set(ABBRECHEN,"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=artikel&action=stueckliste&id=$sid';\">");

    $widget = new WidgetStueckliste($this->app,TAB1);
    $widget->form->SpecialActionAfterExecute("close_refresh",
        "index.php?module=artikel&action=stueckliste&id=$sid#tabs-1");
    $widget->Edit();

    $this->app->Tpl->Add(TAB2,"Sie bearbeiten gerade einen Position der St&uuml;ckliste. Erst nach dem Speichern k&ouml;nnen neue Positionen angelegt werden.");
    //$this->app->Tpl->Add(TAB3,"Sie bearbeiten gerade einen Verkaufspreis. Erst nach dem Speichern k&ouml;nnen Statistiken betrachtet werden.");
    $this->app->Tpl->Parse(PAGE,"stuecklisteuebersicht.tpl");
  }



  function ArtikelStatistik()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->ArtikelMenu();

    //$this->app->Tpl->Set(TABTEXT,"Statistik");
    $this->app->Tpl->Set(TAB1,"<h2>Abgeschlossene Auftr&auml;ge</h2>");
    // easy table mit arbeitspaketen YUI als template 
    $table = new EasyTable($this->app);
    $table->Query("SELECT  EXTRACT(YEAR FROM a.datum) as jahr,  EXTRACT(MONTH FROM a.datum) as monat, SUM(ap.menge) as menge
        FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag WHERE ap.artikel='$id' AND a.status='abgeschlossen' GROUP By monat,jahr ORDER by jahr DESC, monat DESC");
    //$table->DisplayNew(INHALT,"<a href=\"index.php?module=bestellung&action=edit&id=%value%\">Bestellung</a>");
    $table->DisplayNew(TAB1,"Gelieferte","noAction");


    $gesamt = $this->app->DB->Select("SELECT SUM(ap.menge) as menge
        FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag WHERE ap.artikel='$id' AND a.status='abgeschlossen' ");
    $this->app->Tpl->Add(TAB1,"Gesamt: $gesamt St&uuml;ck");


    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }



  function ArtikelOffeneBestellungen()
  {
    $id = $this->app->Secure->GetGET("id");
    //$this->app->Tpl->Set(TABTEXT,"Bestellungen");
    $this->ArtikelMenu();

    // easy table mit arbeitspaketen YUI als template 
    $table = new EasyTable($this->app);
    $table->Query("SELECT DATE_FORMAT(b.datum,'%d.%m.%Y') as datum, CONCAT('<a href=\"index.php?module=bestellung&action=edit&id=',b.id,'\" target=\"_blank\">',b.belegnr,'</a>') as 'bestellung Nr.', bp.bestellnummer as Nummer, bp.menge, bp.geliefert, bp.vpe as VPE, a.lieferantennummer as lieferant, a.name as name, if(bp.lieferdatum!='0000-00-00', DATE_FORMAT(bp.lieferdatum,'%d.%m.%Y'),'sofort') as lieferdatum, b.status as status_Bestellung, bp.bestellung
        FROM bestellung_position bp LEFT JOIN bestellung b ON bp.bestellung=b.id LEFT JOIN adresse a ON b.adresse=a.id
        WHERE artikel='$id' AND b.status!='storniert' ORDER by b.datum DESC");
    $table->DisplayNew(TAB1,"<a href=\"index.php?module=bestellung&action=pdf&id=%value%\"><img src=\"./themes/new/images/pdf.png\" border=\"0\"></a>&nbsp;
        <a href=\"index.php?module=bestellung&action=edit&id=%value%\"><img src=\"./themes/new/images/edit.png\" border=\"0\"></a>");

    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }


  function ArtikelEinlagern()
  {
    $id = $this->app->Secure->GetGET("id");
    $lid = $this->app->Secure->GetGET("lid");
    $menge = $this->app->Secure->GetGET("menge");
    $grund = $this->app->Secure->GetGET("grund");

    // menge holen in lagerregaplplatz
    $menge_lager = $this->app->DB->Select("SELECT menge FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
    $lager_platz = $this->app->DB->Select("SELECT lager_platz FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");

    $neuemenge = $menge_lager + $menge;

    //echo "menge_lager = $menge_lager; menge raus = $menge; neuemenge = $neuemenge; lid=$lid";

    $this->app->DB->Update("UPDATE lager_platz_inhalt SET menge='$neuemenge' WHERE id='$lid' LIMIT 1");

    // protokoll eintrag in bewegung
    $this->app->DB->Insert("INSERT INTO lager_bewegung (id,lager_platz,artikel,menge,eingang,zeit,referenz,bearbeiter,firma,projekt) 
        VALUES ('','$lager_platz','$id','$menge','1',NOW(),'Manuell Bestand angepasst (".$grund.")','".$this->app->User->GetName()."','".$this->app->User->GetFirma()."','$projekt')");

    //  if($menge_lager < $menge) $menge = $menge_lager;

    $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");
    $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Artikel \"$name_de\" wurde $menge mal eingelagert.</div>");

    header("Location: index.php?module=artikel&action=lager&id=$id&msg=$msg");
    exit;
  }

  function ArtikelUmlagern()
  {
    $id = $this->app->Secure->GetGET("id");
    $lid = $this->app->Secure->GetGET("lid");
    $menge = $this->app->Secure->GetGET("menge");
    $grund = $this->app->Secure->GetGET("grund");

    // menge holen in lagerregaplplatz
    $menge_lager = $this->app->DB->Select("SELECT menge FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
    $lager_platz = $this->app->DB->Select("SELECT lager_platz FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");

    $neuemenge = $menge_lager - $menge;

    //echo "menge_lager = $menge_lager; menge raus = $menge; neuemenge = $neuemenge; lid=$lid";

    if($menge_lager <= $menge)
    {
      $this->app->DB->Delete("DELETE FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
      $menge = $menge_lager;
    }
    else 
      $this->app->DB->Update("UPDATE lager_platz_inhalt SET menge='$neuemenge' WHERE id='$lid' LIMIT 1");

    // protokoll eintrag in bewegung
    $this->app->DB->Insert("INSERT INTO lager_bewegung (id,lager_platz,artikel,menge,eingang,zeit,referenz,bearbeiter,firma,projekt) 
        VALUES ('','$lager_platz','$id','$menge','0',NOW(),'Manuell Bestand angepasst (".$grund.")','".$this->app->User->GetName()."','".$this->app->User->GetFirma()."','$projekt')");

    if($menge_lager < $menge) $menge = $menge_lager;


    //   $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");
    $grund = $this->app->erp->base64_url_encode($grund);
    header("Location: index.php?module=lager&action=bucheneinlagern&artikelid=$id&menge=$menge&cmd=umlagern&back=artikel&grund=$grund");

    //    header("Location: index.php?module=artikel&action=lager&id=$id&msg=$msg");
    exit;
  }



  function ArtikelAuslagern()
  {
    $id = $this->app->Secure->GetGET("id");
    $lid = $this->app->Secure->GetGET("lid");
    $menge = $this->app->Secure->GetGET("menge");
    $grund = $this->app->Secure->GetGET("grund");

    // menge holen in lagerregaplplatz
    $menge_lager = $this->app->DB->Select("SELECT menge FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
    $lager_platz = $this->app->DB->Select("SELECT lager_platz FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");

    $neuemenge = $menge_lager - $menge;

    //echo "menge_lager = $menge_lager; menge raus = $menge; neuemenge = $neuemenge; lid=$lid";

    if($menge_lager <= $menge)
    {
      $this->app->DB->Delete("DELETE FROM lager_platz_inhalt WHERE id='$lid' LIMIT 1");
      $menge = $menge_lager;
    }
    else 
      $this->app->DB->Update("UPDATE lager_platz_inhalt SET menge='$neuemenge' WHERE id='$lid' LIMIT 1");

    // protokoll eintrag in bewegung
    $this->app->DB->Insert("INSERT INTO lager_bewegung (id,lager_platz,artikel,menge,eingang,zeit,referenz,bearbeiter,firma,projekt) 
        VALUES ('','$lager_platz','$id','$menge','0',NOW(),'Manuell Bestand angepasst (".$grund.")','".$this->app->User->GetName()."','".$this->app->User->GetFirma()."','$projekt')");

    if($menge_lager < $menge) $menge = $menge_lager;


    $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");
    $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Der Artikel \"$name_de\" wurde $menge mal ausgelagert.</div>");

    header("Location: index.php?module=artikel&action=lager&id=$id&msg=$msg");
    exit;
  }

  function ArtikelAusreservieren()                                                                                                                                                                                   
  {                                                                                                                                                                   
    $id = $this->app->Secure->GetGET("id");                                                                                                                           
    $lid = $this->app->Secure->GetGET("lid");                                                                                                                         
    $menge = $this->app->Secure->GetGET("menge");                                                                                                                                                               
    // menge holen in lagerregaplplatz                                                                                                                                                                          
    $menge_lager = $this->app->DB->Select("SELECT menge FROM lager_reserviert WHERE id='$lid' LIMIT 1");                                                                                                      
    $neuemenge = $menge_lager - $menge;                                                                                                                           
    //echo "menge_lager = $menge_lager; menge raus = $menge; neuemenge = $neuemenge; lid=$lid";                                                                                                                     
    if($menge_lager <= $menge)                                                                                                                                                                                  
      $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE id='$lid' LIMIT 1");                                                                                                                         
    else                                                                                                                                                                                                        
      $this->app->DB->Update("UPDATE lager_reserviert SET menge='$neuemenge' WHERE id='$lid' LIMIT 1");                                                                                                       
    if($menge_lager < $menge) $menge = $menge_lager;                                                                                                                                                            

    $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");                                                                                                                    
    $msg = $this->app->erp->base64_url_encode("<div class=\"error\">Die Reservierung \"$name_de\" wurde $menge mal entfernt.</div>");                                                                                                  
    header("Location: index.php?module=artikel&action=lager&id=$id&msg=$msg");                                                                                                                                  
    exit;                                                                                                                                                                                                       
  }

  function ArtikelDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->DB->Update("UPDATE artikel SET geloescht='1', nummer='DEL' WHERE id='$id'");
    $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");

    // Lager reseten
    $this->app->DB->Delete("DELETE FROM lager_platz_inhalt WHERE artikel='$id'");
    $this->app->DB->Delete("DELETE FROM lager_reserviert WHERE artikel='$id'");
    $this->app->DB->Delete("DELETE FROM lager_charge WHERE artikel='$id'");
    $this->app->DB->Delete("DELETE FROM lager_mindesthaltbarkeitsdatum WHERE artikel='$id'");
    $this->app->DB->Delete("DELETE FROM lager_seriennummern WHERE artikel='$id'");

    //TODO vielleicht besser machen? mit Hinweis oder so
    $this->app->DB->Update("UPDATE artikel SET variante=0,variante_von=0 WHERE variante_von='$id' AND variante_von > 0");

    $this->app->Tpl->Set(MESSAGE,"<div class=\"error\">Der Artikel \"$name_de\" und der Lagerbestand wurde gel&ouml;scht</div>");

    $this->ArtikelList();
  }

  function ArtikelCreate()
  {
    $this->app->Tpl->Set(UEBERSCHRIFT,"Artikel (Neu anlegen)");

    if($this->app->Secure->GetPOST("name_de")=="")
      $this->app->Tpl->Set(MESSAGE,"<div class=\"info\">M&ouml;chten Sie den <a href=\"index.php?module=wizard&action=create\">Artikel-Assistent</a> zum Anlegen verwenden?</div>");

    $this->app->Tpl->Set(ABBRECHEN,"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=artikel&action=list';\">");
    //    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Artikel anlegen");
    $this->app->erp->MenuEintrag("index.php?module=artikel&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    parent::ArtikelCreate();
  }

  function ArtikelList()
  {

    //$this->app->Tpl->Add(TABS,"<li><h2 class=\"allgemein\">Allgemein</h2></li>");
    $this->app->erp->MenuEintrag("index.php?module=artikel&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=wizard&action=create","Artikel-Assistent");
    $this->app->erp->MenuEintrag("index.php?module=artikel&action=create","Neuen Artikel anlegen");
    //    $this->app->erp->MenuEintrag("index.php?module=artikel&action=lagerlampe","Lagerlampen berechnen");
    //$this->app->Tpl->Add(TABS,"<li><a  href=\"index.php\">Zur&uuml;ck zur &Uuml;bersicht</a></li>");

    //    $this->app->Tpl->Set(UEBERSCHRIFT,"Artikelsuche");
    //    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Artikelsuche");

    $this->app->YUI->TableSearch(TAB1,"artikeltabelle");
    $this->app->Tpl->Parse(PAGE,"artikeluebersicht.tpl");


  }


  function ArtikelMenu($id="")
  {
    if(!is_numeric($id))
      $id = $this->app->Secure->GetGET("id");

    $action = $this->app->Secure->GetGET("action");

    if($this->app->Conf->WFdbType=="postgre") {
      if(is_numeric($id)) {
        $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1"); 
        $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1"); 
      }} else {
        $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
        $name_de = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1");
      }


    //$this->app->Tpl->Set(KURZUEBERSCHRIFT,"Artikel $nummer");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT2,$this->app->erp->LimitChar($name_de,100)." (Artikel $nummer)");

    if($this->app->Conf->WFdbType=="postgre") {
      if(is_numeric($id)) 
        $tmp = $this->app->DB->SelectArr("SELECT * FROM artikel WHERE id='$id' LIMIT 1");
    } else {
      $tmp = $this->app->DB->SelectArr("SELECT * FROM artikel WHERE id='$id' LIMIT 1");
    }

    $this->app->erp->MenuEintrag("index.php?module=artikel&action=edit&id=$id","Details");

    if($tmp[0][stueckliste]==1)
      $this->app->erp->MenuEintrag("index.php?module=artikel&action=stueckliste&id=$id","St&uuml;ckliste");

    $rabatt = $this->app->DB->Select("SELECT rabatt FROM artikel WHERE id='$id' LIMIT 1");

    $this->app->erp->MenuEintrag("index.php?module=artikel&action=eigenschaften&id=$id","Eigenschaften");

    $this->app->erp->MenuEintrag("index.php?module=artikel&action=dateien&id=$id","Dateien");
    if($rabatt!="1")    
      $this->app->erp->MenuEintrag("index.php?module=artikel&action=einkauf&id=$id","Einkauf");

    if($this->app->erp->Version()!="stock" && $rabatt!="1")     
      $this->app->erp->MenuEintrag("index.php?module=artikel&action=verkauf&id=$id","Verkauf");

    $this->app->erp->MenuEintrag("index.php?module=artikel&action=statistik&id=$id","Statistik");



    if($tmp[0][lagerartikel]=="1")
    {
      $this->app->erp->MenuEintrag("index.php?module=artikel&action=lager&id=$id","Lager");
    }


    if($tmp[0][mindesthaltbarkeitsdatum]=="1" && $tmp[0][chargenverwaltung]<=0)
    {
      $this->app->erp->MenuEintrag("index.php?module=artikel&action=mindesthaltbarkeitsdatum&id=$id","Mindesthalt.");
    }

    if($tmp[0][mindesthaltbarkeitsdatum]=="1" && $tmp[0][chargenverwaltung]>0)
    {
      $this->app->erp->MenuEintrag("index.php?module=artikel&action=mindesthaltbarkeitsdatum&id=$id","Mindesthalt. + Charge");
    }


    if($tmp[0][chargenverwaltung]>0 && $tmp[0][mindesthaltbarkeitsdatum]!="1")
    {
      $this->app->erp->MenuEintrag("index.php?module=artikel&action=chargen&id=$id","Chargen");
    }



    if($this->app->DB->Select("SELECT COUNT(id) FROM stueckliste WHERE artikel='$id' AND stuecklistevonartikel!='$id'") > 0){
      $this->app->erp->MenuEintrag("index.php?module=artikel&action=instueckliste&id=$id","In St&uuml;ckliste");
    }
    if($tmp[0][seriennummern]!="keine")
      $this->app->erp->MenuEintrag("index.php?module=artikel&action=seriennummern&id=$id","Seriennummern");


    //  if($tmp[0][provisionsartikel]=="1")
    //  $this->app->erp->MenuEintrag("index.php?module=artikel&action=provisionen&id=$id","Provisionen");

    //    if($tmp[0][lagerartikel]=="1")
    $this->app->erp->MenuEintrag("index.php?module=artikel&action=etiketten&id=$id","Etikett");

    $this->app->erp->MenuEintrag("index.php?module=artikel&action=offenebestellungen&id=$id","Bestellungen");

    if($this->app->erp->Version()!="stock")     
      $this->app->erp->MenuEintrag("index.php?module=artikel&action=offeneauftraege&id=$id","Auftr&auml;ge");

    //  if($tmp[0][lagerartikel]=="1")
    //    $this->app->erp->MenuEintrag("index.php?module=artikel&action=reservierung&id=$id","Reservierungen");

    //   if($tmp[0][stueckliste]!="1")
    //     $this->app->erp->MenuEintrag("index.php?module=artikel&action=wareneingang&id=$id","Wareneingang");

    if($tmp[0][produktion]=="1")
      $this->app->erp->MenuEintrag("index.php?module=artikel&action=produktion&id=$id","Produktion");

    //   $this->app->erp->MenuEintrag("index.php?module=artikel&action=projekte&id=$id","Projekte");
    //    $this->app->erp->MenuEintrag("index.php?module=artikel&action=create","Neuen Artikel anlegen");

    if($this->app->erp->RechteVorhanden("multilevel","list"))
      $this->app->erp->MenuEintrag("index.php?module=artikel&action=multilevel&id=$id","MLM");

    $this->app->erp->MenuEintrag("index.php?module=artikel&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }


  function ArtikelEdit()
  {
    $id = $this->app->Secure->GetGET("id"); 

    if($this->app->erp->DisableModul("artikel",$id))
    {
      //$this->app->erp->MenuEintrag("index.php?module=auftrag&action=list","Zur&uuml;ck zur &Uuml;bersicht");
      $this->ArtikelMenu();
      return;
    }   // Einzelposten im gleichen LagerRegal zusammenführen
    $this->app->erp->LagerArtikelZusammenfassen($id);

    $nummer = $this->app->Secure->GetGET("nummer"); 
    if(!is_numeric($id) && $nummer!="")
    {
      $id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$nummer."' LIMIT 1");
      header("Location: index.php?module=artikel&action=edit&id=$id");
    }
    /*
       $msg = $this->app->Secure->GetGET("msg"); 
       $msg = $this->app->erp->base64_url_decode($msg);

       $this->app->Tpl->Set(MESSAGE,$msg);
     */
    $mark = $this->app->Secure->GetPOST('bookmark');
    if($mark!='' && !in_array($id, $_SESSION['bookmarked'])) {
      $_SESSION['bookmarked'][] = $id; 
    }


    $juststueckliste = $this->app->DB->Select("SELECT juststueckliste FROM artikel WHERE id='$id' LIMIT 1");
    $lagerartikel = $this->app->DB->Select("SELECT lagerartikel FROM artikel WHERE id='$id' LIMIT 1");
    $shop= $this->app->DB->Select("SELECT shop FROM artikel WHERE id='$id' LIMIT 1");

    if($shop > 0)
    {
      $this->app->Tpl->Set(SHOPEXPORBUTTON,'
          <script>
          $(function() {
            $( "#aktualisieren").button();
            });
          </script>

          <a id="aktualisieren" style="font-size: 8pt; " href="index.php?module=artikel&action=shopexport&shop=1&id='.$id.'#tabs-5" title="Artikel im Shop aktualisieren">Artikel im Shop (1) aktualisieren</a>');

      $this->app->Tpl->Add(SHOPEXPORBUTTON,'
          <script>
          $(function() {
            $( "#aktualisierenfiles").button();
            });
          </script>

          <a id="aktualisierenfiles" style="font-size: 8pt; " href="index.php?module=artikel&action=shopexportfiles&shop=1&id='.$id.'#tabs-5" title="Bilder zum Artikel im Shop aktualisieren">Bilder im Shop (1) aktualisieren</a>');

    }
    $shop2= $this->app->DB->Select("SELECT shop2 FROM artikel WHERE id='$id' LIMIT 1");

    if($shop2 > 0)
    {
      $this->app->Tpl->Add(SHOPEXPORBUTTON,'
          <script>
          $(function() {
            $( "#aktualisieren2").button();
            });
          </script>

          <a id="aktualisieren2" style="font-size: 8pt; " href="index.php?module=artikel&action=shopexport&shop=2&id='.$id.'#tabs-5" title="Artikel im Shop aktualisieren">Artikel im Shop (2) aktualisieren</a>');

      $this->app->Tpl->Add(SHOPEXPORBUTTON,'
          <script>
          $(function() {
            $( "#aktualisierenfiles2").button();
            });
          </script>

          <a id="aktualisierenfiles2" style="font-size: 8pt; " href="index.php?module=artikel&action=shopexportfiles&shop=2&id='.$id.'#tabs-5" title="Bilder zum Artikel im Shop aktualisieren">Bilder im Shop (2) aktualisieren</a>');

    }

    $shop3= $this->app->DB->Select("SELECT shop3 FROM artikel WHERE id='$id' LIMIT 1");

    if($shop3 > 0)
    {
      $this->app->Tpl->Add(SHOPEXPORBUTTON,'
          <script>
          $(function() {
            $( "#aktualisieren3").button();
            });
          </script>

          <a id="aktualisieren3" style="font-size: 8pt; " href="index.php?module=artikel&action=shopexport&shop=3&id='.$id.'#tabs-5" title="Artikel im Shop aktualisieren">Artikel im Shop (3) aktualisieren</a>');

      $this->app->Tpl->Add(SHOPEXPORBUTTON,'
          <script>
          $(function() {
            $( "#aktualisierenfiles3").button();
            });
          </script>

          <a id="aktualisierenfiles3" style="font-size: 8pt; " href="index.php?module=artikel&action=shopexportfiles&shop=3&id='.$id.'#tabs-5" title="Bilder zum Artikel im Shop aktualisieren">Bilder im Shop (3) aktualisieren</a>');

    }



    $this->app->Tpl->Set(ABBRECHEN,"<input type=\"button\" value=\"Abbrechen\" onclick=\"window.location.href='index.php?module=artikel&action=list';\">");


    /*
       if($this->app->Conf->WFdbType=="postgre")
       $anzahl_verkaufspreise = $this->app->DB->Select("SELECT SUM(id) FROM verkaufspreise WHERE artikel='$id' AND geloescht='0' AND (gueltig_bis IS NOT NULL OR gueltig_bis >=NOW())");
       else
       $anzahl_verkaufspreise = $this->app->DB->Select("SELECT SUM(id) FROM verkaufspreise WHERE artikel='$id' AND geloescht='0' AND (gueltig_bis='0000-00-00' OR gueltig_bis >=NOW())");
       if($anzahl_verkaufspreise<1)
       $this->app->Tpl->Add(MESSAGE,"<div class=\"success\">Achtung: Der Artikel hat noch keinen Verkaufspreis!</div>");
     */
    if($lagerartikel=="1" && $juststueckliste=="1")
    {
      $this->app->Tpl->Add(MESSAGE,"<div class=\"error\">Dieser Artikel ist als Lagerartikel und <i>Explodiert im Auftrag</i> markiert. Bitte nur eine Option w&auml;hlen!</div>");
    }

    //          $this->app->erp->Standardprojekt("artikel",$id);

    //$this->app->erp->SeitenSperrInfo("Diese Seite wird soeben von Benedikt Sauter bearbeitet.<br><br>Bitte sprechen Sie sich vor &Auml;nderungen an dieser Seite entsprechend ab.");

    $this->app->Tpl->Set(OPTIONEN,'

        <script>
        $(function() {
          $( "#mehr").button();
          });
        </script>

        <a id="mehr" style="font-size: 8pt; " href="index.php?module=artikel&action=onlineshop&id='.$id.'" class="popup" title="Warengruppen / Bildauswahl">Warengruppen/Bildauswahl</a>');

    parent::ArtikelEdit();

    /* anzeige formular */ 
    $this->ArtikelMenu();
    $artikel = $this->app->DB->Select("SELECT CONCAT(name_de,' (',nummer,')') FROM artikel WHERE id='$id' LIMIT 1");
    $this->app->Tpl->Set(UEBERSCHRIFT,"Artikel: ".$artikel);


    $this->app->erp->MessageHandlerStandardForm();
    /*

       if($this->app->Secure->GetPOST("speichern")!="")
       {
       if($this->app->Secure->GetGET("msg")=="")
       {
       $msg = $this->app->Secure->GetGET("msg");
       $msg = $msg.$this->app->Tpl->Get(MESSAGE)." ";
       $msg = $this->app->erp->base64_url_encode($msg);
       } else {
       $msg = $this->app->Secure->GetGET("msg");
       }

       header("Location: index.php?module=artikel&action=edit&id=$id&msg=$msg");
       exit;
       } 
     */

    /* sperrmeldung */
    $intern_gesperrt = $this->app->DB->Select("SELECT intern_gesperrt FROM artikel WHERE id='$id' LIMIT 1");
    if($intern_gesperrt)
    {
      if($this->app->erp->CheckSamePage())
      {
        $intern_gesperrtgrund = $this->app->DB->Select("SELECT intern_gesperrtgrund FROM artikel WHERE id='$id' LIMIT 1");
        $this->app->erp->SeitenSperrAuswahl("Wichtiger Hinweis",$intern_gesperrtgrund);
      }
    }
  }



  function ArtikelEtiketten()
  {
    $this->app->Tpl->Add(UEBERSCHRIFT," (Etiketten)");
    $id = $this->app->Secure->GetGET("id");
    $menge = $this->app->Secure->GetPOST("menge");
    $this->ArtikelMenu();
    $this->app->Tpl->Set(TAB1,"<form action=\"\" method=\"post\">Menge:&nbsp;<input type=\"text\" name=\"menge\">&nbsp;&nbsp;&nbsp;<input type=\"submit\" value=\"Drucken\"></form><br>");

    $standardbild = $this->app->DB->Select("SELECT standardbild FROM artikel WHERE id='$id' LIMIT 1");

    $standardbild = $this->app->DB->Select("SELECT id FROM datei WHERE id='$standardbild' AND geloescht!=1 LIMIT 1");

    if($standardbild=="")
      $standardbild = $this->app->DB->Select("SELECT datei FROM datei_stichwoerter WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='$id' LIMIT 1");

    if($standardbild > 0)
      $this->app->Tpl->Add(TAB1,"<img src=\"index.php?module=dateien&action=send&id=$standardbild\" width=\"200\">");

    if($menge!="")
    {
      $nummer = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
      $projekt = $this->app->DB->Select("SELECT projekt FROM artikel WHERE id='$id' LIMIT 1");
      $name_de = $this->app->erp->UmlauteEntfernen($this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$id' LIMIT 1"));
      $name_de_base64 = $this->app->erp->base64_url_encode($name_de);

      if(is_numeric($menge))$druckanzahl=$menge;

      $this->app->erp->EtikettenDrucker("artikel_klein",$druckanzahl,"artikel",$id);
    }

    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function ArtikelOnlineShop()
  {
    $frame = $this->app->Secure->GetGET("frame");
    $id = $this->app->Secure->GetGET("id");
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

    $this->app->BuildNavigation=false;
    //}
  }

  function ArtikelNewList()
  {

    $this->app->Tpl->Parse(PAGE,"datatable.tpl");

  }

  function ArtikelStuecklisteUpload()
  {

    $this->app->Tpl->Set(TAB1,"

        <table><tr><td>Datei:</td><td><input type=\"file\"></td></tr></table>");        
    $this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }

  function ArtikelStuecklisteImport($parsetarget="")
  {
    $id = $this->app->Secure->GetGET("id");
    //$this->app->BuildNavigation=false;

    $vorlage = $this->app->Secure->GetPOST("vorlage");
    if($vorlage=="altium"){
      $result = $this->StuecklisteImport(
          array('menge'=>'Menge','nummer'=>'Artikelnummer','wert'=>'Wert','bauform'=>'Package','referenz'=>'Referenz'),
          array('menge'=>2,'nummer'=>13,'bauform'=>5,'wert'=>6,'referenz'=>3),
          ";",$parsetarget);

    } 
    else if($vorlage=="minimal"){
      $result = $this->StuecklisteImport(
          array('nummer'=>'Artikelnummer','menge'=>'Menge'),
          array('nummer'=>1,'menge'=>2),
          ";",$parsetarget);
    } 
    else {
      $result = $this->StuecklisteImport(
          array('nummer'=>'Artikelnummer','menge'=>'Menge'),
          array('nummer'=>1,'menge'=>2),
          ";",$parsetarget);
      /*
         $result = $this->StuecklisteImport(
         array('menge'=>'Menge','nummer'=>'Artikelnummer','wert'=>'Wert','bauform'=>'Package','referenz'=>'Referenz'),
         array(),
         ";",$parsetarget);
       */
    }

    if(is_array($result))
    {
      //echo "import";
      //print_r($result);
      foreach($result as $key=>$value)
      {
        //echo $value[menge];
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$value[nummer]."' LIMIT 1");
        $maxsort = $this->app->DB->Select("SELECT MAX(sort) FROM stueckliste WHERE stuecklistevonartikel='".$id."'") + 1;
        if($artikelid > 0)
          $this->app->DB->Insert("INSERT INTO stueckliste 
              (id,sort,artikel,menge,wert,bauform,referenz,stuecklistevonartikel,firma) VALUE ('','$maxsort','$artikelid','".$value[menge]."',
                '".$value[wert]."','".$value[bauform]."','".$value[referenz]."','$id','".$this->app->User->GetFirma()."')");
        else
          $fehlerhaftes_bauteil .= "Unbekannte Artikelnummer: ".$value[nummer]." (Menge ".$value[menge]." St&uuml;ck)<br>";
      }
      if($fehlerhaftes_bauteil!="")
      {
        $this->app->Tpl->Set($parsetarget,"<div class=\"error\">$fehlerhaftes_bauteil</div>");
      }

    }

  }

  function StuecklisteImport($fields, $preselected="",$startdelimititer=";",$parsetarget)
  {
    session_start();

    $stueckliste_csv = $this->app->erp->GetTMP()."stueckliste".$this->app->User->GetID();

    $quote = htmlentities($this->app->Secure->GetPOST("quote"));
    $delimiter = htmlentities($this->app->Secure->GetPOST("delimiter"));
    $cancel = $this->app->Secure->GetPOST("cancel");
    if($cancel!="")
    {
      unlink($stueckliste_csv);
      $_SESSION["importfilename"]="";
    }

    $import = $this->app->Secure->GetPOST("import");
    if($import!="")
    {
      $row_post = $this->app->Secure->GetPOST("row");
      $cols = $this->app->Secure->GetPOST("cols");

      $importerror=0;
      if($row_post=="")
      {
        $findcols .= "<div class=\"error\">Zeile wählen</div>";
        $importerror++;
      } 

      for($i=0;$i<count($cols);$i++)
      {
        if($cols[$i]!="") $colcounter++;
      }
      if($colcounter<count($fields))
      {
        $findcols .= "<div class=\"error\">Alle Spalten müssen auswählt werden</div>";
        $importerror++;
      }

      if($importerror==0)
      {
        $findcols .= "<div class=\"info\">Erfolgreich importiert</div>";
        if (($handle = fopen($stueckliste_csv, "r")) !== FALSE) {
          $rowcounter = 1;
          while (($data = fgetcsv($handle, 1000, $_SESSION["delimiter"])) !== FALSE) {
            $rowcounter++;
            $num = count($data);

            if($rowcounter > $row_post){        
              for ($c=0; $c < $num; $c++) {
                // wenn schluessel vorhanden feld uebernehmen
                if($cols[$c]!="")
                  $singlerow[$cols[$c]]=$data[$c];
              }
              $result[] = $singlerow;
              $singlerow="";
            }   
          }
        }
        fclose($handle);
        unlink($stueckliste_csv);
        $_SESSION["importfilename"]="";
        //      $this->app->Tpl->Set(PAGE,$findcols);
      }
    }


    $_SESSION["quote"]=$quote;
    $_SESSION["delimiter"]=$delimiter;


    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $stueckliste_csv)) {
      $_SESSION["importfilename"] = $_FILES['userfile']['name'];
    }

    $row = 1;
    if (($handle = fopen($stueckliste_csv, "r")) !== FALSE) {
      $findcols .= "
        <table width=\"1070\"><tr><td>
        <h2>Datei: ".$_SESSION["importfilename"]."</h2> (Die Anzeige ist limitiert auf max 10 Zeilen)</td><td>

        <form action=\"#tabs-3\" method=\"post\" enctype=\"multipart/form-data\">
        <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"30000\" />
        <input name=\"userfile\" type=\"file\" />

        </td><td align=\"right\">
        Trennzeichen: &nbsp;<input type=\"text\" size=\"3\" value=\"".html_entity_decode($_SESSION["delimiter"])."\" name=\"delimiter\">&nbsp;
      <!--Daten: &nbsp;<input type=\"text\" size=\"3\" value=\"".html_entity_decode($_SESSION["quote"])."\" name=\"quote\">&nbsp;-->
        <input type=\"submit\" value=\"aktualisieren\">
        </td></tr></table>
        ";


      $findcols .= "
        <div style=\"background: #eeeeee;
height: 350px;
overflow: scroll;
          font-size:7pt;
width: 1050px;
border: 1px solid #000;
padding: 10px;\">
           <table border=0 cellpadding=0 cellspacing=0>";       
           while (($data = fgetcsv($handle, 1000, $_SESSION["delimiter"])) !== FALSE) {
             $num = count($data);

             if($row==1)
             {
               $findcols .= "<tr><td></td><td colspan=\"".($num)."\" 
                 style=\"border: 1px solid black; background-color:#ffcc00;font-size:10pt;\">&nbsp;Spalten ausw&auml;hlen</td></tr>";
               $findcols .= "<tr><td style=\"border: 1px solid black; background-color:#ff6666; font-size:10pt;\" nowrap>&nbsp;Erste Zeile mit Daten&nbsp;<br>&nbsp;ausw&auml;hlen</td>";
               for ($c=0; $c < $num; $c++) {
                 $findcols .= "<td style=\"border: 1px solid black; background-color:#FFCC00; padding:5px;\">
                   &nbsp;&nbsp;<select name=\"cols[$c]\"><option></option>";

                 foreach($fields as $key=>$value){
                   if(count($cols)==0) { 
                     if($preselected[$key]==($c+1)) $selected="selected"; else $selected="";
                   } else {
                     if($cols[$c]==$key) $selected="selected"; else $selected="";
                   }    
                   $findcols .="<option value=\"$key\" $selected>$value</option>";
                 }

                 $findcols .="</select>&nbsp;</td>";
               }
               $findcols .= "</tr>";
             }
             if($row_post==$row) $checked="checked"; else $checked="";
             $findcols .= "<tr><td style=\"border: 1px solid black; background-color:#ff6666; padding:5px;\" align=\"center\">
               <input type=\"radio\" value=\"$row\" name=\"row\" $checked></td>";
             $row++;
             for ($c=0; $c < $num; $c++) {
               $findcols .= "<td style=\"border: 1px solid black;\">".$data[$c] . "&nbsp;</td>";
             }
             $findcols .= "</tr>";
             if($row > 10) break;
           }
         fclose($handle);
         $findcols .= "</table></div>
           <table width=\"1080\"><tr><td>
           <br><br>
           Bitte w&auml;hlen Sie aus:
           <ul><li>Die erste Zeile die Daten Ihrer Stueckliste enthält</li>
           <li>Die Spalten: Menge und Artikelnummer</li>
           </ul></td><td align=\"right\">
           <input type=\"submit\" value=\"Import abbrechen\" name=\"cancel\">
           <input type=\"submit\" value=\"Jetzt importieren\" name=\"import\">
           </td></tr></table>
           </form>
           ";
    } else {
      $findcols .= "
        <form action=\"#tabs-3\" method=\"post\" enctype=\"multipart/form-data\">
        <table width=\"1070\"><tr><td>
        Datei:&nbsp;
      <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"30000\" />
        <input name=\"userfile\" type=\"file\" />
        Vorlage: <select name=\"vorlage\">
        <!--<option></option>-->
        <option value=\"minimal\">Artikelnummer; Menge</option>
        <option value=\"altium\">Altium Designer</option>
        <!--<option value=\"eagle\">Eagle (Cadsoft) </option>-->
        </select> 
        </td><td align=\"right\">
        Trennzeichen: &nbsp;<input type=\"text\" size=\"3\" name=\"delimiter\" value=\";\">&nbsp;
      <!--Daten: &nbsp;<input type=\"text\" size=\"3\" value=\"&quot;\" name=\"quote\">&nbsp;-->
        <input type=\"submit\" value=\"St&uuml;ckliste laden\">
        </td></tr></table>
        </form>";



    }
    $this->app->Tpl->Set($parsetarget,$findcols);
    if(is_array($result)) return $result;
  }


}

?>
