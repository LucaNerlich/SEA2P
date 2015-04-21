<?php

class ObjGenArtikel
{

  private  $id;
  private  $typ;
  private  $nummer;
  private  $checksum;
  private  $projekt;
  private  $inaktiv;
  private  $ausverkauft;
  private  $warengruppe;
  private  $name_de;
  private  $name_en;
  private  $kurztext_de;
  private  $kurztext_en;
  private  $beschreibung_de;
  private  $beschreibung_en;
  private  $uebersicht_de;
  private  $uebersicht_en;
  private  $links_de;
  private  $links_en;
  private  $startseite_de;
  private  $startseite_en;
  private  $standardbild;
  private  $herstellerlink;
  private  $hersteller;
  private  $teilbar;
  private  $nteile;
  private  $seriennummern;
  private  $lager_platz;
  private  $lieferzeit;
  private  $lieferzeitmanuell;
  private  $sonstiges;
  private  $gewicht;
  private  $endmontage;
  private  $funktionstest;
  private  $artikelcheckliste;
  private  $stueckliste;
  private  $juststueckliste;
  private  $barcode;
  private  $hinzugefuegt;
  private  $pcbdecal;
  private  $lagerartikel;
  private  $porto;
  private  $chargenverwaltung;
  private  $provisionsartikel;
  private  $gesperrt;
  private  $sperrgrund;
  private  $geloescht;
  private  $gueltigbis;
  private  $umsatzsteuer;
  private  $klasse;
  private  $adresse;
  private  $shopartikel;
  private  $unishopartikel;
  private  $journalshopartikel;
  private  $shop;
  private  $katalog;
  private  $katalogtext_de;
  private  $katalogtext_en;
  private  $katalogbezeichnung_de;
  private  $katalogbezeichnung_en;
  private  $neu;
  private  $topseller;
  private  $startseite;
  private  $wichtig;
  private  $mindestlager;
  private  $mindestbestellung;
  private  $partnerprogramm_sperre;
  private  $internerkommentar;
  private  $intern_gesperrt;
  private  $intern_gesperrtuser;
  private  $intern_gesperrtgrund;
  private  $inbearbeitung;
  private  $inbearbeitunguser;
  private  $cache_lagerplatzinhaltmenge;
  private  $internkommentar;
  private  $firma;
  private  $logdatei;
  private  $anabregs_text;
  private  $autobestellung;
  private  $produktion;
  private  $herstellernummer;
  private  $restmenge;
  private  $lieferzeitmanuell_en;
  private  $produktioninfo;
  private  $sonderaktion;
  private  $sonderaktion_en;
  private  $autolagerlampe;
  private  $variante;
  private  $variante_von;
  private  $freifeld1;
  private  $freifeld2;
  private  $freifeld3;
  private  $freifeld4;
  private  $freifeld5;
  private  $freifeld6;
  private  $nachbestellt;
  private  $keinepunkte;
  private  $punkte;
  private  $bonuspunkte;

  public $app;            //application object 

  public function ObjGenArtikel($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM artikel WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->typ=$result[typ];
    $this->nummer=$result[nummer];
    $this->checksum=$result[checksum];
    $this->projekt=$result[projekt];
    $this->inaktiv=$result[inaktiv];
    $this->ausverkauft=$result[ausverkauft];
    $this->warengruppe=$result[warengruppe];
    $this->name_de=$result[name_de];
    $this->name_en=$result[name_en];
    $this->kurztext_de=$result[kurztext_de];
    $this->kurztext_en=$result[kurztext_en];
    $this->beschreibung_de=$result[beschreibung_de];
    $this->beschreibung_en=$result[beschreibung_en];
    $this->uebersicht_de=$result[uebersicht_de];
    $this->uebersicht_en=$result[uebersicht_en];
    $this->links_de=$result[links_de];
    $this->links_en=$result[links_en];
    $this->startseite_de=$result[startseite_de];
    $this->startseite_en=$result[startseite_en];
    $this->standardbild=$result[standardbild];
    $this->herstellerlink=$result[herstellerlink];
    $this->hersteller=$result[hersteller];
    $this->teilbar=$result[teilbar];
    $this->nteile=$result[nteile];
    $this->seriennummern=$result[seriennummern];
    $this->lager_platz=$result[lager_platz];
    $this->lieferzeit=$result[lieferzeit];
    $this->lieferzeitmanuell=$result[lieferzeitmanuell];
    $this->sonstiges=$result[sonstiges];
    $this->gewicht=$result[gewicht];
    $this->endmontage=$result[endmontage];
    $this->funktionstest=$result[funktionstest];
    $this->artikelcheckliste=$result[artikelcheckliste];
    $this->stueckliste=$result[stueckliste];
    $this->juststueckliste=$result[juststueckliste];
    $this->barcode=$result[barcode];
    $this->hinzugefuegt=$result[hinzugefuegt];
    $this->pcbdecal=$result[pcbdecal];
    $this->lagerartikel=$result[lagerartikel];
    $this->porto=$result[porto];
    $this->chargenverwaltung=$result[chargenverwaltung];
    $this->provisionsartikel=$result[provisionsartikel];
    $this->gesperrt=$result[gesperrt];
    $this->sperrgrund=$result[sperrgrund];
    $this->geloescht=$result[geloescht];
    $this->gueltigbis=$result[gueltigbis];
    $this->umsatzsteuer=$result[umsatzsteuer];
    $this->klasse=$result[klasse];
    $this->adresse=$result[adresse];
    $this->shopartikel=$result[shopartikel];
    $this->unishopartikel=$result[unishopartikel];
    $this->journalshopartikel=$result[journalshopartikel];
    $this->shop=$result[shop];
    $this->katalog=$result[katalog];
    $this->katalogtext_de=$result[katalogtext_de];
    $this->katalogtext_en=$result[katalogtext_en];
    $this->katalogbezeichnung_de=$result[katalogbezeichnung_de];
    $this->katalogbezeichnung_en=$result[katalogbezeichnung_en];
    $this->neu=$result[neu];
    $this->topseller=$result[topseller];
    $this->startseite=$result[startseite];
    $this->wichtig=$result[wichtig];
    $this->mindestlager=$result[mindestlager];
    $this->mindestbestellung=$result[mindestbestellung];
    $this->partnerprogramm_sperre=$result[partnerprogramm_sperre];
    $this->internerkommentar=$result[internerkommentar];
    $this->intern_gesperrt=$result[intern_gesperrt];
    $this->intern_gesperrtuser=$result[intern_gesperrtuser];
    $this->intern_gesperrtgrund=$result[intern_gesperrtgrund];
    $this->inbearbeitung=$result[inbearbeitung];
    $this->inbearbeitunguser=$result[inbearbeitunguser];
    $this->cache_lagerplatzinhaltmenge=$result[cache_lagerplatzinhaltmenge];
    $this->internkommentar=$result[internkommentar];
    $this->firma=$result[firma];
    $this->logdatei=$result[logdatei];
    $this->anabregs_text=$result[anabregs_text];
    $this->autobestellung=$result[autobestellung];
    $this->produktion=$result[produktion];
    $this->herstellernummer=$result[herstellernummer];
    $this->restmenge=$result[restmenge];
    $this->lieferzeitmanuell_en=$result[lieferzeitmanuell_en];
    $this->produktioninfo=$result[produktioninfo];
    $this->sonderaktion=$result[sonderaktion];
    $this->sonderaktion_en=$result[sonderaktion_en];
    $this->autolagerlampe=$result[autolagerlampe];
    $this->variante=$result[variante];
    $this->variante_von=$result[variante_von];
    $this->freifeld1=$result[freifeld1];
    $this->freifeld2=$result[freifeld2];
    $this->freifeld3=$result[freifeld3];
    $this->freifeld4=$result[freifeld4];
    $this->freifeld5=$result[freifeld5];
    $this->freifeld6=$result[freifeld6];
    $this->nachbestellt=$result[nachbestellt];
    $this->keinepunkte=$result[keinepunkte];
    $this->punkte=$result[punkte];
    $this->bonuspunkte=$result[bonuspunkte];
  }

  public function Create()
  {
    $sql = "INSERT INTO artikel (id,typ,nummer,checksum,projekt,inaktiv,ausverkauft,warengruppe,name_de,name_en,kurztext_de,kurztext_en,beschreibung_de,beschreibung_en,uebersicht_de,uebersicht_en,links_de,links_en,startseite_de,startseite_en,standardbild,herstellerlink,hersteller,teilbar,nteile,seriennummern,lager_platz,lieferzeit,lieferzeitmanuell,sonstiges,gewicht,endmontage,funktionstest,artikelcheckliste,stueckliste,juststueckliste,barcode,hinzugefuegt,pcbdecal,lagerartikel,porto,chargenverwaltung,provisionsartikel,gesperrt,sperrgrund,geloescht,gueltigbis,umsatzsteuer,klasse,adresse,shopartikel,unishopartikel,journalshopartikel,shop,katalog,katalogtext_de,katalogtext_en,katalogbezeichnung_de,katalogbezeichnung_en,neu,topseller,startseite,wichtig,mindestlager,mindestbestellung,partnerprogramm_sperre,internerkommentar,intern_gesperrt,intern_gesperrtuser,intern_gesperrtgrund,inbearbeitung,inbearbeitunguser,cache_lagerplatzinhaltmenge,internkommentar,firma,logdatei,anabregs_text,autobestellung,produktion,herstellernummer,restmenge,lieferzeitmanuell_en,produktioninfo,sonderaktion,sonderaktion_en,autolagerlampe,variante,variante_von,freifeld1,freifeld2,freifeld3,freifeld4,freifeld5,freifeld6,nachbestellt,keinepunkte,punkte,bonuspunkte)
      VALUES(DEFAULT, '{$this->typ}', '{$this->nummer}', '{$this->checksum}', ".((is_numeric($this->projekt)) ? $this->projekt : '0').", '{$this->inaktiv}', ".((is_numeric($this->ausverkauft)) ? $this->ausverkauft : '0').", '{$this->warengruppe}', '{$this->name_de}', '{$this->name_en}', '{$this->kurztext_de}', '{$this->kurztext_en}', '{$this->beschreibung_de}', '{$this->beschreibung_en}', '{$this->uebersicht_de}', '{$this->uebersicht_en}', '{$this->links_de}', '{$this->links_en}', '{$this->startseite_de}', '{$this->startseite_en}', '{$this->standardbild}', '{$this->herstellerlink}', '{$this->hersteller}', '{$this->teilbar}', '{$this->nteile}', '{$this->seriennummern}', '{$this->lager_platz}', '{$this->lieferzeit}', '{$this->lieferzeitmanuell}', '{$this->sonstiges}', '{$this->gewicht}', '{$this->endmontage}', '{$this->funktionstest}', '{$this->artikelcheckliste}', ".((is_numeric($this->stueckliste)) ? $this->stueckliste : '0').", ".((is_numeric($this->juststueckliste)) ? $this->juststueckliste : '0').", '{$this->barcode}', '{$this->hinzugefuegt}', '{$this->pcbdecal}', ".((is_numeric($this->lagerartikel)) ? $this->lagerartikel : '0').", ".((is_numeric($this->porto)) ? $this->porto : '0').", ".((is_numeric($this->chargenverwaltung)) ? $this->chargenverwaltung : '0').", ".((is_numeric($this->provisionsartikel)) ? $this->provisionsartikel : '0').", ".((is_numeric($this->gesperrt)) ? $this->gesperrt : '0').", '{$this->sperrgrund}', ".((is_numeric($this->geloescht)) ? $this->geloescht : '0').", ".(($this->gueltigbis=='' || $this->gueltigbis=='--') ? 'NOW()' : "'".$this->gueltigbis."'").", '{$this->umsatzsteuer}', '{$this->klasse}', ".((is_numeric($this->adresse)) ? $this->adresse : '0').", ".((is_numeric($this->shopartikel)) ? $this->shopartikel : '0').", ".((is_numeric($this->unishopartikel)) ? $this->unishopartikel : '0').", ".((is_numeric($this->journalshopartikel)) ? $this->journalshopartikel : '0').", ".((is_numeric($this->shop)) ? $this->shop : '0').", ".((is_numeric($this->katalog)) ? $this->katalog : '0').", '{$this->katalogtext_de}', '{$this->katalogtext_en}', '{$this->katalogbezeichnung_de}', '{$this->katalogbezeichnung_en}', ".((is_numeric($this->neu)) ? $this->neu : '0').", ".((is_numeric($this->topseller)) ? $this->topseller : '0').", ".((is_numeric($this->startseite)) ? $this->startseite : '0').", ".((is_numeric($this->wichtig)) ? $this->wichtig : '0').", ".((is_numeric($this->mindestlager)) ? $this->mindestlager : '0').", ".((is_numeric($this->mindestbestellung)) ? $this->mindestbestellung : '0').", ".((is_numeric($this->partnerprogramm_sperre)) ? $this->partnerprogramm_sperre : '0').", '{$this->internerkommentar}', ".((is_numeric($this->intern_gesperrt)) ? $this->intern_gesperrt : '0').", ".((is_numeric($this->intern_gesperrtuser)) ? $this->intern_gesperrtuser : '0').", '{$this->intern_gesperrtgrund}', ".((is_numeric($this->inbearbeitung)) ? $this->inbearbeitung : '0').", ".((is_numeric($this->inbearbeitunguser)) ? $this->inbearbeitunguser : '0').", ".((is_numeric($this->cache_lagerplatzinhaltmenge)) ? $this->cache_lagerplatzinhaltmenge : '0').", '{$this->internkommentar}', ".((is_numeric($this->firma)) ? $this->firma : '0').", ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").", '{$this->anabregs_text}', ".((is_numeric($this->autobestellung)) ? $this->autobestellung : '0').", ".((is_numeric($this->produktion)) ? $this->produktion : '0').", '{$this->herstellernummer}', ".((is_numeric($this->restmenge)) ? $this->restmenge : '0').", '{$this->lieferzeitmanuell_en}', '{$this->produktioninfo}', '{$this->sonderaktion}', '{$this->sonderaktion_en}', ".((is_numeric($this->autolagerlampe)) ? $this->autolagerlampe : '0').", ".((is_numeric($this->variante)) ? $this->variante : '0').", ".((is_numeric($this->variante_von)) ? $this->variante_von : '0').", '{$this->freifeld1}', '{$this->freifeld2}', '{$this->freifeld3}', '{$this->freifeld4}', '{$this->freifeld5}', '{$this->freifeld6}', ".((is_numeric($this->nachbestellt)) ? $this->nachbestellt : '0').", ".((is_numeric($this->keinepunkte)) ? $this->keinepunkte : '0').", ".((is_numeric($this->punkte)) ? $this->punkte : '0').", ".((is_numeric($this->bonuspunkte)) ? $this->bonuspunkte : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE artikel SET
      typ='{$this->typ}',
      nummer='{$this->nummer}',
      checksum='{$this->checksum}',
      projekt=".((is_numeric($this->projekt)) ? $this->projekt : '0').",
      inaktiv='{$this->inaktiv}',
      ausverkauft=".((is_numeric($this->ausverkauft)) ? $this->ausverkauft : '0').",
      warengruppe='{$this->warengruppe}',
      name_de='{$this->name_de}',
      name_en='{$this->name_en}',
      kurztext_de='{$this->kurztext_de}',
      kurztext_en='{$this->kurztext_en}',
      beschreibung_de='{$this->beschreibung_de}',
      beschreibung_en='{$this->beschreibung_en}',
      uebersicht_de='{$this->uebersicht_de}',
      uebersicht_en='{$this->uebersicht_en}',
      links_de='{$this->links_de}',
      links_en='{$this->links_en}',
      startseite_de='{$this->startseite_de}',
      startseite_en='{$this->startseite_en}',
      standardbild='{$this->standardbild}',
      herstellerlink='{$this->herstellerlink}',
      hersteller='{$this->hersteller}',
      teilbar='{$this->teilbar}',
      nteile='{$this->nteile}',
      seriennummern='{$this->seriennummern}',
      lager_platz='{$this->lager_platz}',
      lieferzeit='{$this->lieferzeit}',
      lieferzeitmanuell='{$this->lieferzeitmanuell}',
      sonstiges='{$this->sonstiges}',
      gewicht='{$this->gewicht}',
      endmontage='{$this->endmontage}',
      funktionstest='{$this->funktionstest}',
      artikelcheckliste='{$this->artikelcheckliste}',
      stueckliste=".((is_numeric($this->stueckliste)) ? $this->stueckliste : '0').",
      juststueckliste=".((is_numeric($this->juststueckliste)) ? $this->juststueckliste : '0').",
      barcode='{$this->barcode}',
      hinzugefuegt='{$this->hinzugefuegt}',
      pcbdecal='{$this->pcbdecal}',
      lagerartikel=".((is_numeric($this->lagerartikel)) ? $this->lagerartikel : '0').",
      porto=".((is_numeric($this->porto)) ? $this->porto : '0').",
      chargenverwaltung=".((is_numeric($this->chargenverwaltung)) ? $this->chargenverwaltung : '0').",
      provisionsartikel=".((is_numeric($this->provisionsartikel)) ? $this->provisionsartikel : '0').",
      gesperrt=".((is_numeric($this->gesperrt)) ? $this->gesperrt : '0').",
      sperrgrund='{$this->sperrgrund}',
      geloescht=".((is_numeric($this->geloescht)) ? $this->geloescht : '0').",
      gueltigbis=".(($this->gueltigbis=='' || $this->gueltigbis=='--') ? 'NOW()' : "'".$this->gueltigbis."'").",
      umsatzsteuer='{$this->umsatzsteuer}',
      klasse='{$this->klasse}',
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      shopartikel=".((is_numeric($this->shopartikel)) ? $this->shopartikel : '0').",
      unishopartikel=".((is_numeric($this->unishopartikel)) ? $this->unishopartikel : '0').",
      journalshopartikel=".((is_numeric($this->journalshopartikel)) ? $this->journalshopartikel : '0').",
      shop=".((is_numeric($this->shop)) ? $this->shop : '0').",
      katalog=".((is_numeric($this->katalog)) ? $this->katalog : '0').",
      katalogtext_de='{$this->katalogtext_de}',
      katalogtext_en='{$this->katalogtext_en}',
      katalogbezeichnung_de='{$this->katalogbezeichnung_de}',
      katalogbezeichnung_en='{$this->katalogbezeichnung_en}',
      neu=".((is_numeric($this->neu)) ? $this->neu : '0').",
      topseller=".((is_numeric($this->topseller)) ? $this->topseller : '0').",
      startseite=".((is_numeric($this->startseite)) ? $this->startseite : '0').",
      wichtig=".((is_numeric($this->wichtig)) ? $this->wichtig : '0').",
      mindestlager=".((is_numeric($this->mindestlager)) ? $this->mindestlager : '0').",
      mindestbestellung=".((is_numeric($this->mindestbestellung)) ? $this->mindestbestellung : '0').",
      partnerprogramm_sperre=".((is_numeric($this->partnerprogramm_sperre)) ? $this->partnerprogramm_sperre : '0').",
      internerkommentar='{$this->internerkommentar}',
      intern_gesperrt=".((is_numeric($this->intern_gesperrt)) ? $this->intern_gesperrt : '0').",
      intern_gesperrtuser=".((is_numeric($this->intern_gesperrtuser)) ? $this->intern_gesperrtuser : '0').",
      intern_gesperrtgrund='{$this->intern_gesperrtgrund}',
      inbearbeitung=".((is_numeric($this->inbearbeitung)) ? $this->inbearbeitung : '0').",
      inbearbeitunguser=".((is_numeric($this->inbearbeitunguser)) ? $this->inbearbeitunguser : '0').",
      cache_lagerplatzinhaltmenge=".((is_numeric($this->cache_lagerplatzinhaltmenge)) ? $this->cache_lagerplatzinhaltmenge : '0').",
      internkommentar='{$this->internkommentar}',
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
      logdatei=".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").",
      anabregs_text='{$this->anabregs_text}',
      autobestellung=".((is_numeric($this->autobestellung)) ? $this->autobestellung : '0').",
      produktion=".((is_numeric($this->produktion)) ? $this->produktion : '0').",
      herstellernummer='{$this->herstellernummer}',
      restmenge=".((is_numeric($this->restmenge)) ? $this->restmenge : '0').",
      lieferzeitmanuell_en='{$this->lieferzeitmanuell_en}',
      produktioninfo='{$this->produktioninfo}',
      sonderaktion='{$this->sonderaktion}',
      sonderaktion_en='{$this->sonderaktion_en}',
      autolagerlampe=".((is_numeric($this->autolagerlampe)) ? $this->autolagerlampe : '0').",
      variante=".((is_numeric($this->variante)) ? $this->variante : '0').",
      variante_von=".((is_numeric($this->variante_von)) ? $this->variante_von : '0').",
      freifeld1='{$this->freifeld1}',
      freifeld2='{$this->freifeld2}',
      freifeld3='{$this->freifeld3}',
      freifeld4='{$this->freifeld4}',
      freifeld5='{$this->freifeld5}',
      freifeld6='{$this->freifeld6}',
      nachbestellt=".((is_numeric($this->nachbestellt)) ? $this->nachbestellt : '0').",
      keinepunkte=".((is_numeric($this->keinepunkte)) ? $this->keinepunkte : '0').",
      punkte=".((is_numeric($this->punkte)) ? $this->punkte : '0').",
      bonuspunkte=".((is_numeric($this->bonuspunkte)) ? $this->bonuspunkte : '0')."
      WHERE (id='{$this->id}')";

    $this->app->DB->Update($sql);
  }

  public function Delete($id="")
  {
    if(is_numeric($id))
    {
      $this->id=$id;
    }
    else
      return -1;

    $sql = "DELETE FROM artikel WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->typ="";
    $this->nummer="";
    $this->checksum="";
    $this->projekt="";
    $this->inaktiv="";
    $this->ausverkauft="";
    $this->warengruppe="";
    $this->name_de="";
    $this->name_en="";
    $this->kurztext_de="";
    $this->kurztext_en="";
    $this->beschreibung_de="";
    $this->beschreibung_en="";
    $this->uebersicht_de="";
    $this->uebersicht_en="";
    $this->links_de="";
    $this->links_en="";
    $this->startseite_de="";
    $this->startseite_en="";
    $this->standardbild="";
    $this->herstellerlink="";
    $this->hersteller="";
    $this->teilbar="";
    $this->nteile="";
    $this->seriennummern="";
    $this->lager_platz="";
    $this->lieferzeit="";
    $this->lieferzeitmanuell="";
    $this->sonstiges="";
    $this->gewicht="";
    $this->endmontage="";
    $this->funktionstest="";
    $this->artikelcheckliste="";
    $this->stueckliste="";
    $this->juststueckliste="";
    $this->barcode="";
    $this->hinzugefuegt="";
    $this->pcbdecal="";
    $this->lagerartikel="";
    $this->porto="";
    $this->chargenverwaltung="";
    $this->provisionsartikel="";
    $this->gesperrt="";
    $this->sperrgrund="";
    $this->geloescht="";
    $this->gueltigbis="";
    $this->umsatzsteuer="";
    $this->klasse="";
    $this->adresse="";
    $this->shopartikel="";
    $this->unishopartikel="";
    $this->journalshopartikel="";
    $this->shop="";
    $this->katalog="";
    $this->katalogtext_de="";
    $this->katalogtext_en="";
    $this->katalogbezeichnung_de="";
    $this->katalogbezeichnung_en="";
    $this->neu="";
    $this->topseller="";
    $this->startseite="";
    $this->wichtig="";
    $this->mindestlager="";
    $this->mindestbestellung="";
    $this->partnerprogramm_sperre="";
    $this->internerkommentar="";
    $this->intern_gesperrt="";
    $this->intern_gesperrtuser="";
    $this->intern_gesperrtgrund="";
    $this->inbearbeitung="";
    $this->inbearbeitunguser="";
    $this->cache_lagerplatzinhaltmenge="";
    $this->internkommentar="";
    $this->firma="";
    $this->logdatei="";
    $this->anabregs_text="";
    $this->autobestellung="";
    $this->produktion="";
    $this->herstellernummer="";
    $this->restmenge="";
    $this->lieferzeitmanuell_en="";
    $this->produktioninfo="";
    $this->sonderaktion="";
    $this->sonderaktion_en="";
    $this->autolagerlampe="";
    $this->variante="";
    $this->variante_von="";
    $this->freifeld1="";
    $this->freifeld2="";
    $this->freifeld3="";
    $this->freifeld4="";
    $this->freifeld5="";
    $this->freifeld6="";
    $this->nachbestellt="";
    $this->keinepunkte="";
    $this->punkte="";
    $this->bonuspunkte="";
  }

  public function Copy()
  {
    $this->id = "";
    $this->Create();
  }

 /** 
   Mit dieser Funktion kann man einen Datensatz suchen 
   dafuer muss man die Attribute setzen nach denen gesucht werden soll
   dann kriegt man als ergebnis den ersten Datensatz der auf die Suche uebereinstimmt
   zurueck. Mit Next() kann man sich alle weiteren Ergebnisse abholen
   **/ 

  public function Find()
  {
    //TODO Suche mit den werten machen
  }

  public function FindNext()
  {
    //TODO Suche mit den alten werten fortsetzen machen
  }

 /** Funktionen um durch die Tabelle iterieren zu koennen */ 

  public function Next()
  {
    //TODO: SQL Statement passt nach meiner Meinung nach noch nicht immer
  }

  public function First()
  {
    //TODO: SQL Statement passt nach meiner Meinung nach noch nicht immer
  }

 /** dank dieser funktionen kann man die tatsaechlichen werte einfach 
  ueberladen (in einem Objekt das mit seiner klasse ueber dieser steht)**/ 

  function SetId($value) { $this->id=$value; }
  function GetId() { return $this->id; }
  function SetTyp($value) { $this->typ=$value; }
  function GetTyp() { return $this->typ; }
  function SetNummer($value) { $this->nummer=$value; }
  function GetNummer() { return $this->nummer; }
  function SetChecksum($value) { $this->checksum=$value; }
  function GetChecksum() { return $this->checksum; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetInaktiv($value) { $this->inaktiv=$value; }
  function GetInaktiv() { return $this->inaktiv; }
  function SetAusverkauft($value) { $this->ausverkauft=$value; }
  function GetAusverkauft() { return $this->ausverkauft; }
  function SetWarengruppe($value) { $this->warengruppe=$value; }
  function GetWarengruppe() { return $this->warengruppe; }
  function SetName_De($value) { $this->name_de=$value; }
  function GetName_De() { return $this->name_de; }
  function SetName_En($value) { $this->name_en=$value; }
  function GetName_En() { return $this->name_en; }
  function SetKurztext_De($value) { $this->kurztext_de=$value; }
  function GetKurztext_De() { return $this->kurztext_de; }
  function SetKurztext_En($value) { $this->kurztext_en=$value; }
  function GetKurztext_En() { return $this->kurztext_en; }
  function SetBeschreibung_De($value) { $this->beschreibung_de=$value; }
  function GetBeschreibung_De() { return $this->beschreibung_de; }
  function SetBeschreibung_En($value) { $this->beschreibung_en=$value; }
  function GetBeschreibung_En() { return $this->beschreibung_en; }
  function SetUebersicht_De($value) { $this->uebersicht_de=$value; }
  function GetUebersicht_De() { return $this->uebersicht_de; }
  function SetUebersicht_En($value) { $this->uebersicht_en=$value; }
  function GetUebersicht_En() { return $this->uebersicht_en; }
  function SetLinks_De($value) { $this->links_de=$value; }
  function GetLinks_De() { return $this->links_de; }
  function SetLinks_En($value) { $this->links_en=$value; }
  function GetLinks_En() { return $this->links_en; }
  function SetStartseite_De($value) { $this->startseite_de=$value; }
  function GetStartseite_De() { return $this->startseite_de; }
  function SetStartseite_En($value) { $this->startseite_en=$value; }
  function GetStartseite_En() { return $this->startseite_en; }
  function SetStandardbild($value) { $this->standardbild=$value; }
  function GetStandardbild() { return $this->standardbild; }
  function SetHerstellerlink($value) { $this->herstellerlink=$value; }
  function GetHerstellerlink() { return $this->herstellerlink; }
  function SetHersteller($value) { $this->hersteller=$value; }
  function GetHersteller() { return $this->hersteller; }
  function SetTeilbar($value) { $this->teilbar=$value; }
  function GetTeilbar() { return $this->teilbar; }
  function SetNteile($value) { $this->nteile=$value; }
  function GetNteile() { return $this->nteile; }
  function SetSeriennummern($value) { $this->seriennummern=$value; }
  function GetSeriennummern() { return $this->seriennummern; }
  function SetLager_Platz($value) { $this->lager_platz=$value; }
  function GetLager_Platz() { return $this->lager_platz; }
  function SetLieferzeit($value) { $this->lieferzeit=$value; }
  function GetLieferzeit() { return $this->lieferzeit; }
  function SetLieferzeitmanuell($value) { $this->lieferzeitmanuell=$value; }
  function GetLieferzeitmanuell() { return $this->lieferzeitmanuell; }
  function SetSonstiges($value) { $this->sonstiges=$value; }
  function GetSonstiges() { return $this->sonstiges; }
  function SetGewicht($value) { $this->gewicht=$value; }
  function GetGewicht() { return $this->gewicht; }
  function SetEndmontage($value) { $this->endmontage=$value; }
  function GetEndmontage() { return $this->endmontage; }
  function SetFunktionstest($value) { $this->funktionstest=$value; }
  function GetFunktionstest() { return $this->funktionstest; }
  function SetArtikelcheckliste($value) { $this->artikelcheckliste=$value; }
  function GetArtikelcheckliste() { return $this->artikelcheckliste; }
  function SetStueckliste($value) { $this->stueckliste=$value; }
  function GetStueckliste() { return $this->stueckliste; }
  function SetJuststueckliste($value) { $this->juststueckliste=$value; }
  function GetJuststueckliste() { return $this->juststueckliste; }
  function SetBarcode($value) { $this->barcode=$value; }
  function GetBarcode() { return $this->barcode; }
  function SetHinzugefuegt($value) { $this->hinzugefuegt=$value; }
  function GetHinzugefuegt() { return $this->hinzugefuegt; }
  function SetPcbdecal($value) { $this->pcbdecal=$value; }
  function GetPcbdecal() { return $this->pcbdecal; }
  function SetLagerartikel($value) { $this->lagerartikel=$value; }
  function GetLagerartikel() { return $this->lagerartikel; }
  function SetPorto($value) { $this->porto=$value; }
  function GetPorto() { return $this->porto; }
  function SetChargenverwaltung($value) { $this->chargenverwaltung=$value; }
  function GetChargenverwaltung() { return $this->chargenverwaltung; }
  function SetProvisionsartikel($value) { $this->provisionsartikel=$value; }
  function GetProvisionsartikel() { return $this->provisionsartikel; }
  function SetGesperrt($value) { $this->gesperrt=$value; }
  function GetGesperrt() { return $this->gesperrt; }
  function SetSperrgrund($value) { $this->sperrgrund=$value; }
  function GetSperrgrund() { return $this->sperrgrund; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetGueltigbis($value) { $this->gueltigbis=$value; }
  function GetGueltigbis() { return $this->gueltigbis; }
  function SetUmsatzsteuer($value) { $this->umsatzsteuer=$value; }
  function GetUmsatzsteuer() { return $this->umsatzsteuer; }
  function SetKlasse($value) { $this->klasse=$value; }
  function GetKlasse() { return $this->klasse; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetShopartikel($value) { $this->shopartikel=$value; }
  function GetShopartikel() { return $this->shopartikel; }
  function SetUnishopartikel($value) { $this->unishopartikel=$value; }
  function GetUnishopartikel() { return $this->unishopartikel; }
  function SetJournalshopartikel($value) { $this->journalshopartikel=$value; }
  function GetJournalshopartikel() { return $this->journalshopartikel; }
  function SetShop($value) { $this->shop=$value; }
  function GetShop() { return $this->shop; }
  function SetKatalog($value) { $this->katalog=$value; }
  function GetKatalog() { return $this->katalog; }
  function SetKatalogtext_De($value) { $this->katalogtext_de=$value; }
  function GetKatalogtext_De() { return $this->katalogtext_de; }
  function SetKatalogtext_En($value) { $this->katalogtext_en=$value; }
  function GetKatalogtext_En() { return $this->katalogtext_en; }
  function SetKatalogbezeichnung_De($value) { $this->katalogbezeichnung_de=$value; }
  function GetKatalogbezeichnung_De() { return $this->katalogbezeichnung_de; }
  function SetKatalogbezeichnung_En($value) { $this->katalogbezeichnung_en=$value; }
  function GetKatalogbezeichnung_En() { return $this->katalogbezeichnung_en; }
  function SetNeu($value) { $this->neu=$value; }
  function GetNeu() { return $this->neu; }
  function SetTopseller($value) { $this->topseller=$value; }
  function GetTopseller() { return $this->topseller; }
  function SetStartseite($value) { $this->startseite=$value; }
  function GetStartseite() { return $this->startseite; }
  function SetWichtig($value) { $this->wichtig=$value; }
  function GetWichtig() { return $this->wichtig; }
  function SetMindestlager($value) { $this->mindestlager=$value; }
  function GetMindestlager() { return $this->mindestlager; }
  function SetMindestbestellung($value) { $this->mindestbestellung=$value; }
  function GetMindestbestellung() { return $this->mindestbestellung; }
  function SetPartnerprogramm_Sperre($value) { $this->partnerprogramm_sperre=$value; }
  function GetPartnerprogramm_Sperre() { return $this->partnerprogramm_sperre; }
  function SetInternerkommentar($value) { $this->internerkommentar=$value; }
  function GetInternerkommentar() { return $this->internerkommentar; }
  function SetIntern_Gesperrt($value) { $this->intern_gesperrt=$value; }
  function GetIntern_Gesperrt() { return $this->intern_gesperrt; }
  function SetIntern_Gesperrtuser($value) { $this->intern_gesperrtuser=$value; }
  function GetIntern_Gesperrtuser() { return $this->intern_gesperrtuser; }
  function SetIntern_Gesperrtgrund($value) { $this->intern_gesperrtgrund=$value; }
  function GetIntern_Gesperrtgrund() { return $this->intern_gesperrtgrund; }
  function SetInbearbeitung($value) { $this->inbearbeitung=$value; }
  function GetInbearbeitung() { return $this->inbearbeitung; }
  function SetInbearbeitunguser($value) { $this->inbearbeitunguser=$value; }
  function GetInbearbeitunguser() { return $this->inbearbeitunguser; }
  function SetCache_Lagerplatzinhaltmenge($value) { $this->cache_lagerplatzinhaltmenge=$value; }
  function GetCache_Lagerplatzinhaltmenge() { return $this->cache_lagerplatzinhaltmenge; }
  function SetInternkommentar($value) { $this->internkommentar=$value; }
  function GetInternkommentar() { return $this->internkommentar; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetAnabregs_Text($value) { $this->anabregs_text=$value; }
  function GetAnabregs_Text() { return $this->anabregs_text; }
  function SetAutobestellung($value) { $this->autobestellung=$value; }
  function GetAutobestellung() { return $this->autobestellung; }
  function SetProduktion($value) { $this->produktion=$value; }
  function GetProduktion() { return $this->produktion; }
  function SetHerstellernummer($value) { $this->herstellernummer=$value; }
  function GetHerstellernummer() { return $this->herstellernummer; }
  function SetRestmenge($value) { $this->restmenge=$value; }
  function GetRestmenge() { return $this->restmenge; }
  function SetLieferzeitmanuell_En($value) { $this->lieferzeitmanuell_en=$value; }
  function GetLieferzeitmanuell_En() { return $this->lieferzeitmanuell_en; }
  function SetProduktioninfo($value) { $this->produktioninfo=$value; }
  function GetProduktioninfo() { return $this->produktioninfo; }
  function SetSonderaktion($value) { $this->sonderaktion=$value; }
  function GetSonderaktion() { return $this->sonderaktion; }
  function SetSonderaktion_En($value) { $this->sonderaktion_en=$value; }
  function GetSonderaktion_En() { return $this->sonderaktion_en; }
  function SetAutolagerlampe($value) { $this->autolagerlampe=$value; }
  function GetAutolagerlampe() { return $this->autolagerlampe; }
  function SetVariante($value) { $this->variante=$value; }
  function GetVariante() { return $this->variante; }
  function SetVariante_Von($value) { $this->variante_von=$value; }
  function GetVariante_Von() { return $this->variante_von; }
  function SetFreifeld1($value) { $this->freifeld1=$value; }
  function GetFreifeld1() { return $this->freifeld1; }
  function SetFreifeld2($value) { $this->freifeld2=$value; }
  function GetFreifeld2() { return $this->freifeld2; }
  function SetFreifeld3($value) { $this->freifeld3=$value; }
  function GetFreifeld3() { return $this->freifeld3; }
  function SetFreifeld4($value) { $this->freifeld4=$value; }
  function GetFreifeld4() { return $this->freifeld4; }
  function SetFreifeld5($value) { $this->freifeld5=$value; }
  function GetFreifeld5() { return $this->freifeld5; }
  function SetFreifeld6($value) { $this->freifeld6=$value; }
  function GetFreifeld6() { return $this->freifeld6; }
  function SetNachbestellt($value) { $this->nachbestellt=$value; }
  function GetNachbestellt() { return $this->nachbestellt; }
  function SetKeinepunkte($value) { $this->keinepunkte=$value; }
  function GetKeinepunkte() { return $this->keinepunkte; }
  function SetPunkte($value) { $this->punkte=$value; }
  function GetPunkte() { return $this->punkte; }
  function SetBonuspunkte($value) { $this->bonuspunkte=$value; }
  function GetBonuspunkte() { return $this->bonuspunkte; }

}

?>