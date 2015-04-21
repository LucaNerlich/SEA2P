<?php

class ObjGenRechnung
{

  private  $id;
  private  $datum;
  private  $aborechnung;
  private  $projekt;
  private  $anlegeart;
  private  $belegnr;
  private  $auftrag;
  private  $auftragid;
  private  $bearbeiter;
  private  $freitext;
  private  $status;
  private  $adresse;
  private  $name;
  private  $abteilung;
  private  $unterabteilung;
  private  $strasse;
  private  $adresszusatz;
  private  $ansprechpartner;
  private  $plz;
  private  $ort;
  private  $land;
  private  $ustid;
  private  $ust_befreit;
  private  $ustbrief;
  private  $ustbrief_eingang;
  private  $ustbrief_eingang_am;
  private  $email;
  private  $telefon;
  private  $telefax;
  private  $betreff;
  private  $kundennummer;
  private  $lieferschein;
  private  $versandart;
  private  $lieferdatum;
  private  $buchhaltung;
  private  $zahlungsweise;
  private  $zahlungsstatus;
  private  $ist;
  private  $soll;
  private  $skonto_gegeben;
  private  $zahlungszieltage;
  private  $zahlungszieltageskonto;
  private  $zahlungszielskonto;
  private  $firma;
  private  $versendet;
  private  $versendet_am;
  private  $versendet_per;
  private  $versendet_durch;
  private  $versendet_mahnwesen;
  private  $mahnwesen;
  private  $mahnwesen_datum;
  private  $mahnwesen_gesperrt;
  private  $mahnwesen_internebemerkung;
  private  $inbearbeitung;
  private  $datev_abgeschlossen;
  private  $logdatei;
  private  $doppel;
  private  $keinsteuersatz;
  private  $internebemerkung;
  private  $ohne_briefpapier;
  private  $pdfarchiviert;
  private  $pdfarchiviertversion;
  private  $schreibschutz;
  private  $forderungsverlust_datum;
  private  $forderungsverlust_betrag;
  private  $ihrebestellnummer;

  public $app;            //application object 

  public function ObjGenRechnung($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM rechnung WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->datum=$result[datum];
    $this->aborechnung=$result[aborechnung];
    $this->projekt=$result[projekt];
    $this->anlegeart=$result[anlegeart];
    $this->belegnr=$result[belegnr];
    $this->auftrag=$result[auftrag];
    $this->auftragid=$result[auftragid];
    $this->bearbeiter=$result[bearbeiter];
    $this->freitext=$result[freitext];
    $this->status=$result[status];
    $this->adresse=$result[adresse];
    $this->name=$result[name];
    $this->abteilung=$result[abteilung];
    $this->unterabteilung=$result[unterabteilung];
    $this->strasse=$result[strasse];
    $this->adresszusatz=$result[adresszusatz];
    $this->ansprechpartner=$result[ansprechpartner];
    $this->plz=$result[plz];
    $this->ort=$result[ort];
    $this->land=$result[land];
    $this->ustid=$result[ustid];
    $this->ust_befreit=$result[ust_befreit];
    $this->ustbrief=$result[ustbrief];
    $this->ustbrief_eingang=$result[ustbrief_eingang];
    $this->ustbrief_eingang_am=$result[ustbrief_eingang_am];
    $this->email=$result[email];
    $this->telefon=$result[telefon];
    $this->telefax=$result[telefax];
    $this->betreff=$result[betreff];
    $this->kundennummer=$result[kundennummer];
    $this->lieferschein=$result[lieferschein];
    $this->versandart=$result[versandart];
    $this->lieferdatum=$result[lieferdatum];
    $this->buchhaltung=$result[buchhaltung];
    $this->zahlungsweise=$result[zahlungsweise];
    $this->zahlungsstatus=$result[zahlungsstatus];
    $this->ist=$result[ist];
    $this->soll=$result[soll];
    $this->skonto_gegeben=$result[skonto_gegeben];
    $this->zahlungszieltage=$result[zahlungszieltage];
    $this->zahlungszieltageskonto=$result[zahlungszieltageskonto];
    $this->zahlungszielskonto=$result[zahlungszielskonto];
    $this->firma=$result[firma];
    $this->versendet=$result[versendet];
    $this->versendet_am=$result[versendet_am];
    $this->versendet_per=$result[versendet_per];
    $this->versendet_durch=$result[versendet_durch];
    $this->versendet_mahnwesen=$result[versendet_mahnwesen];
    $this->mahnwesen=$result[mahnwesen];
    $this->mahnwesen_datum=$result[mahnwesen_datum];
    $this->mahnwesen_gesperrt=$result[mahnwesen_gesperrt];
    $this->mahnwesen_internebemerkung=$result[mahnwesen_internebemerkung];
    $this->inbearbeitung=$result[inbearbeitung];
    $this->datev_abgeschlossen=$result[datev_abgeschlossen];
    $this->logdatei=$result[logdatei];
    $this->doppel=$result[doppel];
    $this->keinsteuersatz=$result[keinsteuersatz];
    $this->internebemerkung=$result[internebemerkung];
    $this->ohne_briefpapier=$result[ohne_briefpapier];
    $this->pdfarchiviert=$result[pdfarchiviert];
    $this->pdfarchiviertversion=$result[pdfarchiviertversion];
    $this->schreibschutz=$result[schreibschutz];
    $this->forderungsverlust_datum=$result[forderungsverlust_datum];
    $this->forderungsverlust_betrag=$result[forderungsverlust_betrag];
    $this->ihrebestellnummer=$result[ihrebestellnummer];
  }

  public function Create()
  {
    $sql = "INSERT INTO rechnung (id,datum,aborechnung,projekt,anlegeart,belegnr,auftrag,auftragid,bearbeiter,freitext,status,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,ansprechpartner,plz,ort,land,ustid,ust_befreit,ustbrief,ustbrief_eingang,ustbrief_eingang_am,email,telefon,telefax,betreff,kundennummer,lieferschein,versandart,lieferdatum,buchhaltung,zahlungsweise,zahlungsstatus,ist,soll,skonto_gegeben,zahlungszieltage,zahlungszieltageskonto,zahlungszielskonto,firma,versendet,versendet_am,versendet_per,versendet_durch,versendet_mahnwesen,mahnwesen,mahnwesen_datum,mahnwesen_gesperrt,mahnwesen_internebemerkung,inbearbeitung,datev_abgeschlossen,logdatei,doppel,keinsteuersatz,internebemerkung,ohne_briefpapier,pdfarchiviert,pdfarchiviertversion,schreibschutz,forderungsverlust_datum,forderungsverlust_betrag,ihrebestellnummer)
      VALUES(DEFAULT, ".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'").", ".((is_numeric($this->aborechnung)) ? $this->aborechnung : '0').", '{$this->projekt}', '{$this->anlegeart}', '{$this->belegnr}', ".((is_numeric($this->auftrag)) ? $this->auftrag : '0').", ".((is_numeric($this->auftragid)) ? $this->auftragid : '0').", '{$this->bearbeiter}', '{$this->freitext}', '{$this->status}', ".((is_numeric($this->adresse)) ? $this->adresse : '0').", '{$this->name}', '{$this->abteilung}', '{$this->unterabteilung}', '{$this->strasse}', '{$this->adresszusatz}', '{$this->ansprechpartner}', '{$this->plz}', '{$this->ort}', '{$this->land}', '{$this->ustid}', ".((is_numeric($this->ust_befreit)) ? $this->ust_befreit : '0').", ".((is_numeric($this->ustbrief)) ? $this->ustbrief : '0').", ".((is_numeric($this->ustbrief_eingang)) ? $this->ustbrief_eingang : '0').", ".(($this->ustbrief_eingang_am=='' || $this->ustbrief_eingang_am=='--') ? 'NOW()' : "'".$this->ustbrief_eingang_am."'").", '{$this->email}', '{$this->telefon}', '{$this->telefax}', '{$this->betreff}', '{$this->kundennummer}', ".((is_numeric($this->lieferschein)) ? $this->lieferschein : '0').", '{$this->versandart}', ".(($this->lieferdatum=='' || $this->lieferdatum=='--') ? 'NOW()' : "'".$this->lieferdatum."'").", '{$this->buchhaltung}', '{$this->zahlungsweise}', '{$this->zahlungsstatus}', ".((is_numeric($this->ist)) ? $this->ist : '0').", ".((is_numeric($this->soll)) ? $this->soll : '0').", ".((is_numeric($this->skonto_gegeben)) ? $this->skonto_gegeben : '0').", ".((is_numeric($this->zahlungszieltage)) ? $this->zahlungszieltage : '0').", ".((is_numeric($this->zahlungszieltageskonto)) ? $this->zahlungszieltageskonto : '0').", ".((is_numeric($this->zahlungszielskonto)) ? $this->zahlungszielskonto : '0').", ".((is_numeric($this->firma)) ? $this->firma : '0').", ".((is_numeric($this->versendet)) ? $this->versendet : '0').", ".(($this->versendet_am=='' || $this->versendet_am=='--') ? 'NOW()' : "'".$this->versendet_am."'").", '{$this->versendet_per}', '{$this->versendet_durch}', ".((is_numeric($this->versendet_mahnwesen)) ? $this->versendet_mahnwesen : '0').", '{$this->mahnwesen}', ".(($this->mahnwesen_datum=='' || $this->mahnwesen_datum=='--') ? 'NOW()' : "'".$this->mahnwesen_datum."'").", ".((is_numeric($this->mahnwesen_gesperrt)) ? $this->mahnwesen_gesperrt : '0').", '{$this->mahnwesen_internebemerkung}', ".((is_numeric($this->inbearbeitung)) ? $this->inbearbeitung : '0').", ".((is_numeric($this->datev_abgeschlossen)) ? $this->datev_abgeschlossen : '0').", ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").", ".((is_numeric($this->doppel)) ? $this->doppel : '0').", ".((is_numeric($this->keinsteuersatz)) ? $this->keinsteuersatz : '0').", '{$this->internebemerkung}', ".((is_numeric($this->ohne_briefpapier)) ? $this->ohne_briefpapier : '0').", ".((is_numeric($this->pdfarchiviert)) ? $this->pdfarchiviert : '0').", ".((is_numeric($this->pdfarchiviertversion)) ? $this->pdfarchiviertversion : '0').", ".((is_numeric($this->schreibschutz)) ? $this->schreibschutz : '0').", ".(($this->forderungsverlust_datum=='' || $this->forderungsverlust_datum=='--') ? 'NOW()' : "'".$this->forderungsverlust_datum."'").", ".((is_numeric($this->forderungsverlust_betrag)) ? $this->forderungsverlust_betrag : '0').", '{$this->ihrebestellnummer}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE rechnung SET
      datum=".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'").",
      aborechnung=".((is_numeric($this->aborechnung)) ? $this->aborechnung : '0').",
      projekt='{$this->projekt}',
      anlegeart='{$this->anlegeart}',
      belegnr='{$this->belegnr}',
      auftrag=".((is_numeric($this->auftrag)) ? $this->auftrag : '0').",
      auftragid=".((is_numeric($this->auftragid)) ? $this->auftragid : '0').",
      bearbeiter='{$this->bearbeiter}',
      freitext='{$this->freitext}',
      status='{$this->status}',
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      name='{$this->name}',
      abteilung='{$this->abteilung}',
      unterabteilung='{$this->unterabteilung}',
      strasse='{$this->strasse}',
      adresszusatz='{$this->adresszusatz}',
      ansprechpartner='{$this->ansprechpartner}',
      plz='{$this->plz}',
      ort='{$this->ort}',
      land='{$this->land}',
      ustid='{$this->ustid}',
      ust_befreit=".((is_numeric($this->ust_befreit)) ? $this->ust_befreit : '0').",
      ustbrief=".((is_numeric($this->ustbrief)) ? $this->ustbrief : '0').",
      ustbrief_eingang=".((is_numeric($this->ustbrief_eingang)) ? $this->ustbrief_eingang : '0').",
      ustbrief_eingang_am=".(($this->ustbrief_eingang_am=='' || $this->ustbrief_eingang_am=='--') ? 'NOW()' : "'".$this->ustbrief_eingang_am."'").",
      email='{$this->email}',
      telefon='{$this->telefon}',
      telefax='{$this->telefax}',
      betreff='{$this->betreff}',
      kundennummer='{$this->kundennummer}',
      lieferschein=".((is_numeric($this->lieferschein)) ? $this->lieferschein : '0').",
      versandart='{$this->versandart}',
      lieferdatum=".(($this->lieferdatum=='' || $this->lieferdatum=='--') ? 'NOW()' : "'".$this->lieferdatum."'").",
      buchhaltung='{$this->buchhaltung}',
      zahlungsweise='{$this->zahlungsweise}',
      zahlungsstatus='{$this->zahlungsstatus}',
      ist=".((is_numeric($this->ist)) ? $this->ist : '0').",
      soll=".((is_numeric($this->soll)) ? $this->soll : '0').",
      skonto_gegeben=".((is_numeric($this->skonto_gegeben)) ? $this->skonto_gegeben : '0').",
      zahlungszieltage=".((is_numeric($this->zahlungszieltage)) ? $this->zahlungszieltage : '0').",
      zahlungszieltageskonto=".((is_numeric($this->zahlungszieltageskonto)) ? $this->zahlungszieltageskonto : '0').",
      zahlungszielskonto=".((is_numeric($this->zahlungszielskonto)) ? $this->zahlungszielskonto : '0').",
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
      versendet=".((is_numeric($this->versendet)) ? $this->versendet : '0').",
      versendet_am=".(($this->versendet_am=='' || $this->versendet_am=='--') ? 'NOW()' : "'".$this->versendet_am."'").",
      versendet_per='{$this->versendet_per}',
      versendet_durch='{$this->versendet_durch}',
      versendet_mahnwesen=".((is_numeric($this->versendet_mahnwesen)) ? $this->versendet_mahnwesen : '0').",
      mahnwesen='{$this->mahnwesen}',
      mahnwesen_datum=".(($this->mahnwesen_datum=='' || $this->mahnwesen_datum=='--') ? 'NOW()' : "'".$this->mahnwesen_datum."'").",
      mahnwesen_gesperrt=".((is_numeric($this->mahnwesen_gesperrt)) ? $this->mahnwesen_gesperrt : '0').",
      mahnwesen_internebemerkung='{$this->mahnwesen_internebemerkung}',
      inbearbeitung=".((is_numeric($this->inbearbeitung)) ? $this->inbearbeitung : '0').",
      datev_abgeschlossen=".((is_numeric($this->datev_abgeschlossen)) ? $this->datev_abgeschlossen : '0').",
      logdatei=".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").",
      doppel=".((is_numeric($this->doppel)) ? $this->doppel : '0').",
      keinsteuersatz=".((is_numeric($this->keinsteuersatz)) ? $this->keinsteuersatz : '0').",
      internebemerkung='{$this->internebemerkung}',
      ohne_briefpapier=".((is_numeric($this->ohne_briefpapier)) ? $this->ohne_briefpapier : '0').",
      pdfarchiviert=".((is_numeric($this->pdfarchiviert)) ? $this->pdfarchiviert : '0').",
      pdfarchiviertversion=".((is_numeric($this->pdfarchiviertversion)) ? $this->pdfarchiviertversion : '0').",
      schreibschutz=".((is_numeric($this->schreibschutz)) ? $this->schreibschutz : '0').",
      forderungsverlust_datum=".(($this->forderungsverlust_datum=='' || $this->forderungsverlust_datum=='--') ? 'NOW()' : "'".$this->forderungsverlust_datum."'").",
      forderungsverlust_betrag=".((is_numeric($this->forderungsverlust_betrag)) ? $this->forderungsverlust_betrag : '0').",
      ihrebestellnummer='{$this->ihrebestellnummer}'
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

    $sql = "DELETE FROM rechnung WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->datum="";
    $this->aborechnung="";
    $this->projekt="";
    $this->anlegeart="";
    $this->belegnr="";
    $this->auftrag="";
    $this->auftragid="";
    $this->bearbeiter="";
    $this->freitext="";
    $this->status="";
    $this->adresse="";
    $this->name="";
    $this->abteilung="";
    $this->unterabteilung="";
    $this->strasse="";
    $this->adresszusatz="";
    $this->ansprechpartner="";
    $this->plz="";
    $this->ort="";
    $this->land="";
    $this->ustid="";
    $this->ust_befreit="";
    $this->ustbrief="";
    $this->ustbrief_eingang="";
    $this->ustbrief_eingang_am="";
    $this->email="";
    $this->telefon="";
    $this->telefax="";
    $this->betreff="";
    $this->kundennummer="";
    $this->lieferschein="";
    $this->versandart="";
    $this->lieferdatum="";
    $this->buchhaltung="";
    $this->zahlungsweise="";
    $this->zahlungsstatus="";
    $this->ist="";
    $this->soll="";
    $this->skonto_gegeben="";
    $this->zahlungszieltage="";
    $this->zahlungszieltageskonto="";
    $this->zahlungszielskonto="";
    $this->firma="";
    $this->versendet="";
    $this->versendet_am="";
    $this->versendet_per="";
    $this->versendet_durch="";
    $this->versendet_mahnwesen="";
    $this->mahnwesen="";
    $this->mahnwesen_datum="";
    $this->mahnwesen_gesperrt="";
    $this->mahnwesen_internebemerkung="";
    $this->inbearbeitung="";
    $this->datev_abgeschlossen="";
    $this->logdatei="";
    $this->doppel="";
    $this->keinsteuersatz="";
    $this->internebemerkung="";
    $this->ohne_briefpapier="";
    $this->pdfarchiviert="";
    $this->pdfarchiviertversion="";
    $this->schreibschutz="";
    $this->forderungsverlust_datum="";
    $this->forderungsverlust_betrag="";
    $this->ihrebestellnummer="";
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
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetAborechnung($value) { $this->aborechnung=$value; }
  function GetAborechnung() { return $this->aborechnung; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetAnlegeart($value) { $this->anlegeart=$value; }
  function GetAnlegeart() { return $this->anlegeart; }
  function SetBelegnr($value) { $this->belegnr=$value; }
  function GetBelegnr() { return $this->belegnr; }
  function SetAuftrag($value) { $this->auftrag=$value; }
  function GetAuftrag() { return $this->auftrag; }
  function SetAuftragid($value) { $this->auftragid=$value; }
  function GetAuftragid() { return $this->auftragid; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetFreitext($value) { $this->freitext=$value; }
  function GetFreitext() { return $this->freitext; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetAbteilung($value) { $this->abteilung=$value; }
  function GetAbteilung() { return $this->abteilung; }
  function SetUnterabteilung($value) { $this->unterabteilung=$value; }
  function GetUnterabteilung() { return $this->unterabteilung; }
  function SetStrasse($value) { $this->strasse=$value; }
  function GetStrasse() { return $this->strasse; }
  function SetAdresszusatz($value) { $this->adresszusatz=$value; }
  function GetAdresszusatz() { return $this->adresszusatz; }
  function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  function GetAnsprechpartner() { return $this->ansprechpartner; }
  function SetPlz($value) { $this->plz=$value; }
  function GetPlz() { return $this->plz; }
  function SetOrt($value) { $this->ort=$value; }
  function GetOrt() { return $this->ort; }
  function SetLand($value) { $this->land=$value; }
  function GetLand() { return $this->land; }
  function SetUstid($value) { $this->ustid=$value; }
  function GetUstid() { return $this->ustid; }
  function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  function GetUst_Befreit() { return $this->ust_befreit; }
  function SetUstbrief($value) { $this->ustbrief=$value; }
  function GetUstbrief() { return $this->ustbrief; }
  function SetUstbrief_Eingang($value) { $this->ustbrief_eingang=$value; }
  function GetUstbrief_Eingang() { return $this->ustbrief_eingang; }
  function SetUstbrief_Eingang_Am($value) { $this->ustbrief_eingang_am=$value; }
  function GetUstbrief_Eingang_Am() { return $this->ustbrief_eingang_am; }
  function SetEmail($value) { $this->email=$value; }
  function GetEmail() { return $this->email; }
  function SetTelefon($value) { $this->telefon=$value; }
  function GetTelefon() { return $this->telefon; }
  function SetTelefax($value) { $this->telefax=$value; }
  function GetTelefax() { return $this->telefax; }
  function SetBetreff($value) { $this->betreff=$value; }
  function GetBetreff() { return $this->betreff; }
  function SetKundennummer($value) { $this->kundennummer=$value; }
  function GetKundennummer() { return $this->kundennummer; }
  function SetLieferschein($value) { $this->lieferschein=$value; }
  function GetLieferschein() { return $this->lieferschein; }
  function SetVersandart($value) { $this->versandart=$value; }
  function GetVersandart() { return $this->versandart; }
  function SetLieferdatum($value) { $this->lieferdatum=$value; }
  function GetLieferdatum() { return $this->lieferdatum; }
  function SetBuchhaltung($value) { $this->buchhaltung=$value; }
  function GetBuchhaltung() { return $this->buchhaltung; }
  function SetZahlungsweise($value) { $this->zahlungsweise=$value; }
  function GetZahlungsweise() { return $this->zahlungsweise; }
  function SetZahlungsstatus($value) { $this->zahlungsstatus=$value; }
  function GetZahlungsstatus() { return $this->zahlungsstatus; }
  function SetIst($value) { $this->ist=$value; }
  function GetIst() { return $this->ist; }
  function SetSoll($value) { $this->soll=$value; }
  function GetSoll() { return $this->soll; }
  function SetSkonto_Gegeben($value) { $this->skonto_gegeben=$value; }
  function GetSkonto_Gegeben() { return $this->skonto_gegeben; }
  function SetZahlungszieltage($value) { $this->zahlungszieltage=$value; }
  function GetZahlungszieltage() { return $this->zahlungszieltage; }
  function SetZahlungszieltageskonto($value) { $this->zahlungszieltageskonto=$value; }
  function GetZahlungszieltageskonto() { return $this->zahlungszieltageskonto; }
  function SetZahlungszielskonto($value) { $this->zahlungszielskonto=$value; }
  function GetZahlungszielskonto() { return $this->zahlungszielskonto; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetVersendet($value) { $this->versendet=$value; }
  function GetVersendet() { return $this->versendet; }
  function SetVersendet_Am($value) { $this->versendet_am=$value; }
  function GetVersendet_Am() { return $this->versendet_am; }
  function SetVersendet_Per($value) { $this->versendet_per=$value; }
  function GetVersendet_Per() { return $this->versendet_per; }
  function SetVersendet_Durch($value) { $this->versendet_durch=$value; }
  function GetVersendet_Durch() { return $this->versendet_durch; }
  function SetVersendet_Mahnwesen($value) { $this->versendet_mahnwesen=$value; }
  function GetVersendet_Mahnwesen() { return $this->versendet_mahnwesen; }
  function SetMahnwesen($value) { $this->mahnwesen=$value; }
  function GetMahnwesen() { return $this->mahnwesen; }
  function SetMahnwesen_Datum($value) { $this->mahnwesen_datum=$value; }
  function GetMahnwesen_Datum() { return $this->mahnwesen_datum; }
  function SetMahnwesen_Gesperrt($value) { $this->mahnwesen_gesperrt=$value; }
  function GetMahnwesen_Gesperrt() { return $this->mahnwesen_gesperrt; }
  function SetMahnwesen_Internebemerkung($value) { $this->mahnwesen_internebemerkung=$value; }
  function GetMahnwesen_Internebemerkung() { return $this->mahnwesen_internebemerkung; }
  function SetInbearbeitung($value) { $this->inbearbeitung=$value; }
  function GetInbearbeitung() { return $this->inbearbeitung; }
  function SetDatev_Abgeschlossen($value) { $this->datev_abgeschlossen=$value; }
  function GetDatev_Abgeschlossen() { return $this->datev_abgeschlossen; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetDoppel($value) { $this->doppel=$value; }
  function GetDoppel() { return $this->doppel; }
  function SetKeinsteuersatz($value) { $this->keinsteuersatz=$value; }
  function GetKeinsteuersatz() { return $this->keinsteuersatz; }
  function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  function GetInternebemerkung() { return $this->internebemerkung; }
  function SetOhne_Briefpapier($value) { $this->ohne_briefpapier=$value; }
  function GetOhne_Briefpapier() { return $this->ohne_briefpapier; }
  function SetPdfarchiviert($value) { $this->pdfarchiviert=$value; }
  function GetPdfarchiviert() { return $this->pdfarchiviert; }
  function SetPdfarchiviertversion($value) { $this->pdfarchiviertversion=$value; }
  function GetPdfarchiviertversion() { return $this->pdfarchiviertversion; }
  function SetSchreibschutz($value) { $this->schreibschutz=$value; }
  function GetSchreibschutz() { return $this->schreibschutz; }
  function SetForderungsverlust_Datum($value) { $this->forderungsverlust_datum=$value; }
  function GetForderungsverlust_Datum() { return $this->forderungsverlust_datum; }
  function SetForderungsverlust_Betrag($value) { $this->forderungsverlust_betrag=$value; }
  function GetForderungsverlust_Betrag() { return $this->forderungsverlust_betrag; }
  function SetIhrebestellnummer($value) { $this->ihrebestellnummer=$value; }
  function GetIhrebestellnummer() { return $this->ihrebestellnummer; }

}

?>