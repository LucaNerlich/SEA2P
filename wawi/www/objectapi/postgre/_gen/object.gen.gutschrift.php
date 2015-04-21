<?php

class ObjGenGutschrift
{

  private  $id;
  private  $datum;
  private  $projekt;
  private  $anlegeart;
  private  $belegnr;
  private  $rechnung;
  private  $rechnungid;
  private  $bearbeiter;
  private  $freitext;
  private  $status;
  private  $adresse;
  private  $name;
  private  $abteilung;
  private  $unterabteilung;
  private  $strasse;
  private  $adresszusatz;
  private  $plz;
  private  $ort;
  private  $land;
  private  $ustid;
  private  $ustbrief;
  private  $ustbrief_eingang;
  private  $ustbrief_eingang_am;
  private  $ust_befreit;
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
  private  $zahlungszieltage;
  private  $zahlungszieltageskonto;
  private  $zahlungszielskonto;
  private  $gesamtsumme;
  private  $bank_inhaber;
  private  $bank_institut;
  private  $bank_blz;
  private  $bank_konto;
  private  $kreditkarte_typ;
  private  $kreditkarte_inhaber;
  private  $kreditkarte_nummer;
  private  $kreditkarte_pruefnummer;
  private  $kreditkarte_monat;
  private  $kreditkarte_jahr;
  private  $paypalaccount;
  private  $firma;
  private  $versendet;
  private  $versendet_am;
  private  $versendet_per;
  private  $versendet_durch;
  private  $inbearbeitung;
  private  $logdatei;
  private  $keinsteuersatz;
  private  $internebemerkung;
  private  $stornorechnung;
  private  $ohne_briefpapier;
  private  $pdfarchiviert;
  private  $pdfarchiviertversion;
  private  $schreibschutz;
  private  $ihrebestellnummer;

  public $app;            //application object 

  public function ObjGenGutschrift($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM gutschrift WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->datum=$result[datum];
    $this->projekt=$result[projekt];
    $this->anlegeart=$result[anlegeart];
    $this->belegnr=$result[belegnr];
    $this->rechnung=$result[rechnung];
    $this->rechnungid=$result[rechnungid];
    $this->bearbeiter=$result[bearbeiter];
    $this->freitext=$result[freitext];
    $this->status=$result[status];
    $this->adresse=$result[adresse];
    $this->name=$result[name];
    $this->abteilung=$result[abteilung];
    $this->unterabteilung=$result[unterabteilung];
    $this->strasse=$result[strasse];
    $this->adresszusatz=$result[adresszusatz];
    $this->plz=$result[plz];
    $this->ort=$result[ort];
    $this->land=$result[land];
    $this->ustid=$result[ustid];
    $this->ustbrief=$result[ustbrief];
    $this->ustbrief_eingang=$result[ustbrief_eingang];
    $this->ustbrief_eingang_am=$result[ustbrief_eingang_am];
    $this->ust_befreit=$result[ust_befreit];
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
    $this->zahlungszieltage=$result[zahlungszieltage];
    $this->zahlungszieltageskonto=$result[zahlungszieltageskonto];
    $this->zahlungszielskonto=$result[zahlungszielskonto];
    $this->gesamtsumme=$result[gesamtsumme];
    $this->bank_inhaber=$result[bank_inhaber];
    $this->bank_institut=$result[bank_institut];
    $this->bank_blz=$result[bank_blz];
    $this->bank_konto=$result[bank_konto];
    $this->kreditkarte_typ=$result[kreditkarte_typ];
    $this->kreditkarte_inhaber=$result[kreditkarte_inhaber];
    $this->kreditkarte_nummer=$result[kreditkarte_nummer];
    $this->kreditkarte_pruefnummer=$result[kreditkarte_pruefnummer];
    $this->kreditkarte_monat=$result[kreditkarte_monat];
    $this->kreditkarte_jahr=$result[kreditkarte_jahr];
    $this->paypalaccount=$result[paypalaccount];
    $this->firma=$result[firma];
    $this->versendet=$result[versendet];
    $this->versendet_am=$result[versendet_am];
    $this->versendet_per=$result[versendet_per];
    $this->versendet_durch=$result[versendet_durch];
    $this->inbearbeitung=$result[inbearbeitung];
    $this->logdatei=$result[logdatei];
    $this->keinsteuersatz=$result[keinsteuersatz];
    $this->internebemerkung=$result[internebemerkung];
    $this->stornorechnung=$result[stornorechnung];
    $this->ohne_briefpapier=$result[ohne_briefpapier];
    $this->pdfarchiviert=$result[pdfarchiviert];
    $this->pdfarchiviertversion=$result[pdfarchiviertversion];
    $this->schreibschutz=$result[schreibschutz];
    $this->ihrebestellnummer=$result[ihrebestellnummer];
  }

  public function Create()
  {
    $sql = "INSERT INTO gutschrift (id,datum,projekt,anlegeart,belegnr,rechnung,rechnungid,bearbeiter,freitext,status,adresse,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,ustbrief,ustbrief_eingang,ustbrief_eingang_am,ust_befreit,email,telefon,telefax,betreff,kundennummer,lieferschein,versandart,lieferdatum,buchhaltung,zahlungsweise,zahlungsstatus,ist,soll,zahlungszieltage,zahlungszieltageskonto,zahlungszielskonto,gesamtsumme,bank_inhaber,bank_institut,bank_blz,bank_konto,kreditkarte_typ,kreditkarte_inhaber,kreditkarte_nummer,kreditkarte_pruefnummer,kreditkarte_monat,kreditkarte_jahr,paypalaccount,firma,versendet,versendet_am,versendet_per,versendet_durch,inbearbeitung,logdatei,keinsteuersatz,internebemerkung,stornorechnung,ohne_briefpapier,pdfarchiviert,pdfarchiviertversion,schreibschutz,ihrebestellnummer)
      VALUES(DEFAULT, ".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'").", '{$this->projekt}', '{$this->anlegeart}', '{$this->belegnr}', ".((is_numeric($this->rechnung)) ? $this->rechnung : '0').", ".((is_numeric($this->rechnungid)) ? $this->rechnungid : '0').", '{$this->bearbeiter}', '{$this->freitext}', '{$this->status}', ".((is_numeric($this->adresse)) ? $this->adresse : '0').", '{$this->name}', '{$this->abteilung}', '{$this->unterabteilung}', '{$this->strasse}', '{$this->adresszusatz}', '{$this->plz}', '{$this->ort}', '{$this->land}', '{$this->ustid}', ".((is_numeric($this->ustbrief)) ? $this->ustbrief : '0').", ".((is_numeric($this->ustbrief_eingang)) ? $this->ustbrief_eingang : '0').", ".(($this->ustbrief_eingang_am=='' || $this->ustbrief_eingang_am=='--') ? 'NOW()' : "'".$this->ustbrief_eingang_am."'").", ".((is_numeric($this->ust_befreit)) ? $this->ust_befreit : '0').", '{$this->email}', '{$this->telefon}', '{$this->telefax}', '{$this->betreff}', '{$this->kundennummer}', ".((is_numeric($this->lieferschein)) ? $this->lieferschein : '0').", '{$this->versandart}', ".(($this->lieferdatum=='' || $this->lieferdatum=='--') ? 'NOW()' : "'".$this->lieferdatum."'").", '{$this->buchhaltung}', '{$this->zahlungsweise}', '{$this->zahlungsstatus}', ".((is_numeric($this->ist)) ? $this->ist : '0').", ".((is_numeric($this->soll)) ? $this->soll : '0').", ".((is_numeric($this->zahlungszieltage)) ? $this->zahlungszieltage : '0').", ".((is_numeric($this->zahlungszieltageskonto)) ? $this->zahlungszieltageskonto : '0').", ".((is_numeric($this->zahlungszielskonto)) ? $this->zahlungszielskonto : '0').", ".((is_numeric($this->gesamtsumme)) ? $this->gesamtsumme : '0').", '{$this->bank_inhaber}', '{$this->bank_institut}', ".((is_numeric($this->bank_blz)) ? $this->bank_blz : '0').", ".((is_numeric($this->bank_konto)) ? $this->bank_konto : '0').", '{$this->kreditkarte_typ}', '{$this->kreditkarte_inhaber}', '{$this->kreditkarte_nummer}', '{$this->kreditkarte_pruefnummer}', ".((is_numeric($this->kreditkarte_monat)) ? $this->kreditkarte_monat : '0').", ".((is_numeric($this->kreditkarte_jahr)) ? $this->kreditkarte_jahr : '0').", '{$this->paypalaccount}', ".((is_numeric($this->firma)) ? $this->firma : '0').", ".((is_numeric($this->versendet)) ? $this->versendet : '0').", ".(($this->versendet_am=='' || $this->versendet_am=='--') ? 'NOW()' : "'".$this->versendet_am."'").", '{$this->versendet_per}', '{$this->versendet_durch}', ".((is_numeric($this->inbearbeitung)) ? $this->inbearbeitung : '0').", ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").", ".((is_numeric($this->keinsteuersatz)) ? $this->keinsteuersatz : '0').", '{$this->internebemerkung}', ".((is_numeric($this->stornorechnung)) ? $this->stornorechnung : '0').", ".((is_numeric($this->ohne_briefpapier)) ? $this->ohne_briefpapier : '0').", ".((is_numeric($this->pdfarchiviert)) ? $this->pdfarchiviert : '0').", ".((is_numeric($this->pdfarchiviertversion)) ? $this->pdfarchiviertversion : '0').", ".((is_numeric($this->schreibschutz)) ? $this->schreibschutz : '0').", '{$this->ihrebestellnummer}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE gutschrift SET
      datum=".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'").",
      projekt='{$this->projekt}',
      anlegeart='{$this->anlegeart}',
      belegnr='{$this->belegnr}',
      rechnung=".((is_numeric($this->rechnung)) ? $this->rechnung : '0').",
      rechnungid=".((is_numeric($this->rechnungid)) ? $this->rechnungid : '0').",
      bearbeiter='{$this->bearbeiter}',
      freitext='{$this->freitext}',
      status='{$this->status}',
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      name='{$this->name}',
      abteilung='{$this->abteilung}',
      unterabteilung='{$this->unterabteilung}',
      strasse='{$this->strasse}',
      adresszusatz='{$this->adresszusatz}',
      plz='{$this->plz}',
      ort='{$this->ort}',
      land='{$this->land}',
      ustid='{$this->ustid}',
      ustbrief=".((is_numeric($this->ustbrief)) ? $this->ustbrief : '0').",
      ustbrief_eingang=".((is_numeric($this->ustbrief_eingang)) ? $this->ustbrief_eingang : '0').",
      ustbrief_eingang_am=".(($this->ustbrief_eingang_am=='' || $this->ustbrief_eingang_am=='--') ? 'NOW()' : "'".$this->ustbrief_eingang_am."'").",
      ust_befreit=".((is_numeric($this->ust_befreit)) ? $this->ust_befreit : '0').",
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
      zahlungszieltage=".((is_numeric($this->zahlungszieltage)) ? $this->zahlungszieltage : '0').",
      zahlungszieltageskonto=".((is_numeric($this->zahlungszieltageskonto)) ? $this->zahlungszieltageskonto : '0').",
      zahlungszielskonto=".((is_numeric($this->zahlungszielskonto)) ? $this->zahlungszielskonto : '0').",
      gesamtsumme=".((is_numeric($this->gesamtsumme)) ? $this->gesamtsumme : '0').",
      bank_inhaber='{$this->bank_inhaber}',
      bank_institut='{$this->bank_institut}',
      bank_blz=".((is_numeric($this->bank_blz)) ? $this->bank_blz : '0').",
      bank_konto=".((is_numeric($this->bank_konto)) ? $this->bank_konto : '0').",
      kreditkarte_typ='{$this->kreditkarte_typ}',
      kreditkarte_inhaber='{$this->kreditkarte_inhaber}',
      kreditkarte_nummer='{$this->kreditkarte_nummer}',
      kreditkarte_pruefnummer='{$this->kreditkarte_pruefnummer}',
      kreditkarte_monat=".((is_numeric($this->kreditkarte_monat)) ? $this->kreditkarte_monat : '0').",
      kreditkarte_jahr=".((is_numeric($this->kreditkarte_jahr)) ? $this->kreditkarte_jahr : '0').",
      paypalaccount='{$this->paypalaccount}',
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
      versendet=".((is_numeric($this->versendet)) ? $this->versendet : '0').",
      versendet_am=".(($this->versendet_am=='' || $this->versendet_am=='--') ? 'NOW()' : "'".$this->versendet_am."'").",
      versendet_per='{$this->versendet_per}',
      versendet_durch='{$this->versendet_durch}',
      inbearbeitung=".((is_numeric($this->inbearbeitung)) ? $this->inbearbeitung : '0').",
      logdatei=".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").",
      keinsteuersatz=".((is_numeric($this->keinsteuersatz)) ? $this->keinsteuersatz : '0').",
      internebemerkung='{$this->internebemerkung}',
      stornorechnung=".((is_numeric($this->stornorechnung)) ? $this->stornorechnung : '0').",
      ohne_briefpapier=".((is_numeric($this->ohne_briefpapier)) ? $this->ohne_briefpapier : '0').",
      pdfarchiviert=".((is_numeric($this->pdfarchiviert)) ? $this->pdfarchiviert : '0').",
      pdfarchiviertversion=".((is_numeric($this->pdfarchiviertversion)) ? $this->pdfarchiviertversion : '0').",
      schreibschutz=".((is_numeric($this->schreibschutz)) ? $this->schreibschutz : '0').",
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

    $sql = "DELETE FROM gutschrift WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->datum="";
    $this->projekt="";
    $this->anlegeart="";
    $this->belegnr="";
    $this->rechnung="";
    $this->rechnungid="";
    $this->bearbeiter="";
    $this->freitext="";
    $this->status="";
    $this->adresse="";
    $this->name="";
    $this->abteilung="";
    $this->unterabteilung="";
    $this->strasse="";
    $this->adresszusatz="";
    $this->plz="";
    $this->ort="";
    $this->land="";
    $this->ustid="";
    $this->ustbrief="";
    $this->ustbrief_eingang="";
    $this->ustbrief_eingang_am="";
    $this->ust_befreit="";
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
    $this->zahlungszieltage="";
    $this->zahlungszieltageskonto="";
    $this->zahlungszielskonto="";
    $this->gesamtsumme="";
    $this->bank_inhaber="";
    $this->bank_institut="";
    $this->bank_blz="";
    $this->bank_konto="";
    $this->kreditkarte_typ="";
    $this->kreditkarte_inhaber="";
    $this->kreditkarte_nummer="";
    $this->kreditkarte_pruefnummer="";
    $this->kreditkarte_monat="";
    $this->kreditkarte_jahr="";
    $this->paypalaccount="";
    $this->firma="";
    $this->versendet="";
    $this->versendet_am="";
    $this->versendet_per="";
    $this->versendet_durch="";
    $this->inbearbeitung="";
    $this->logdatei="";
    $this->keinsteuersatz="";
    $this->internebemerkung="";
    $this->stornorechnung="";
    $this->ohne_briefpapier="";
    $this->pdfarchiviert="";
    $this->pdfarchiviertversion="";
    $this->schreibschutz="";
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
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetAnlegeart($value) { $this->anlegeart=$value; }
  function GetAnlegeart() { return $this->anlegeart; }
  function SetBelegnr($value) { $this->belegnr=$value; }
  function GetBelegnr() { return $this->belegnr; }
  function SetRechnung($value) { $this->rechnung=$value; }
  function GetRechnung() { return $this->rechnung; }
  function SetRechnungid($value) { $this->rechnungid=$value; }
  function GetRechnungid() { return $this->rechnungid; }
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
  function SetPlz($value) { $this->plz=$value; }
  function GetPlz() { return $this->plz; }
  function SetOrt($value) { $this->ort=$value; }
  function GetOrt() { return $this->ort; }
  function SetLand($value) { $this->land=$value; }
  function GetLand() { return $this->land; }
  function SetUstid($value) { $this->ustid=$value; }
  function GetUstid() { return $this->ustid; }
  function SetUstbrief($value) { $this->ustbrief=$value; }
  function GetUstbrief() { return $this->ustbrief; }
  function SetUstbrief_Eingang($value) { $this->ustbrief_eingang=$value; }
  function GetUstbrief_Eingang() { return $this->ustbrief_eingang; }
  function SetUstbrief_Eingang_Am($value) { $this->ustbrief_eingang_am=$value; }
  function GetUstbrief_Eingang_Am() { return $this->ustbrief_eingang_am; }
  function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  function GetUst_Befreit() { return $this->ust_befreit; }
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
  function SetZahlungszieltage($value) { $this->zahlungszieltage=$value; }
  function GetZahlungszieltage() { return $this->zahlungszieltage; }
  function SetZahlungszieltageskonto($value) { $this->zahlungszieltageskonto=$value; }
  function GetZahlungszieltageskonto() { return $this->zahlungszieltageskonto; }
  function SetZahlungszielskonto($value) { $this->zahlungszielskonto=$value; }
  function GetZahlungszielskonto() { return $this->zahlungszielskonto; }
  function SetGesamtsumme($value) { $this->gesamtsumme=$value; }
  function GetGesamtsumme() { return $this->gesamtsumme; }
  function SetBank_Inhaber($value) { $this->bank_inhaber=$value; }
  function GetBank_Inhaber() { return $this->bank_inhaber; }
  function SetBank_Institut($value) { $this->bank_institut=$value; }
  function GetBank_Institut() { return $this->bank_institut; }
  function SetBank_Blz($value) { $this->bank_blz=$value; }
  function GetBank_Blz() { return $this->bank_blz; }
  function SetBank_Konto($value) { $this->bank_konto=$value; }
  function GetBank_Konto() { return $this->bank_konto; }
  function SetKreditkarte_Typ($value) { $this->kreditkarte_typ=$value; }
  function GetKreditkarte_Typ() { return $this->kreditkarte_typ; }
  function SetKreditkarte_Inhaber($value) { $this->kreditkarte_inhaber=$value; }
  function GetKreditkarte_Inhaber() { return $this->kreditkarte_inhaber; }
  function SetKreditkarte_Nummer($value) { $this->kreditkarte_nummer=$value; }
  function GetKreditkarte_Nummer() { return $this->kreditkarte_nummer; }
  function SetKreditkarte_Pruefnummer($value) { $this->kreditkarte_pruefnummer=$value; }
  function GetKreditkarte_Pruefnummer() { return $this->kreditkarte_pruefnummer; }
  function SetKreditkarte_Monat($value) { $this->kreditkarte_monat=$value; }
  function GetKreditkarte_Monat() { return $this->kreditkarte_monat; }
  function SetKreditkarte_Jahr($value) { $this->kreditkarte_jahr=$value; }
  function GetKreditkarte_Jahr() { return $this->kreditkarte_jahr; }
  function SetPaypalaccount($value) { $this->paypalaccount=$value; }
  function GetPaypalaccount() { return $this->paypalaccount; }
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
  function SetInbearbeitung($value) { $this->inbearbeitung=$value; }
  function GetInbearbeitung() { return $this->inbearbeitung; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetKeinsteuersatz($value) { $this->keinsteuersatz=$value; }
  function GetKeinsteuersatz() { return $this->keinsteuersatz; }
  function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  function GetInternebemerkung() { return $this->internebemerkung; }
  function SetStornorechnung($value) { $this->stornorechnung=$value; }
  function GetStornorechnung() { return $this->stornorechnung; }
  function SetOhne_Briefpapier($value) { $this->ohne_briefpapier=$value; }
  function GetOhne_Briefpapier() { return $this->ohne_briefpapier; }
  function SetPdfarchiviert($value) { $this->pdfarchiviert=$value; }
  function GetPdfarchiviert() { return $this->pdfarchiviert; }
  function SetPdfarchiviertversion($value) { $this->pdfarchiviertversion=$value; }
  function GetPdfarchiviertversion() { return $this->pdfarchiviertversion; }
  function SetSchreibschutz($value) { $this->schreibschutz=$value; }
  function GetSchreibschutz() { return $this->schreibschutz; }
  function SetIhrebestellnummer($value) { $this->ihrebestellnummer=$value; }
  function GetIhrebestellnummer() { return $this->ihrebestellnummer; }

}

?>