<?php

class ObjGenAngebot
{

  private  $id;
  private  $datum;
  private  $gueltigbis;
  private  $projekt;
  private  $belegnr;
  private  $bearbeiter;
  private  $anfrage;
  private  $auftrag;
  private  $freitext;
  private  $internebemerkung;
  private  $status;
  private  $adresse;
  private  $retyp;
  private  $rechnungname;
  private  $retelefon;
  private  $reansprechpartner;
  private  $retelefax;
  private  $reabteilung;
  private  $reemail;
  private  $reunterabteilung;
  private  $readresszusatz;
  private  $restrasse;
  private  $replz;
  private  $reort;
  private  $reland;
  private  $name;
  private  $abteilung;
  private  $unterabteilung;
  private  $strasse;
  private  $adresszusatz;
  private  $plz;
  private  $ort;
  private  $land;
  private  $ustid;
  private  $email;
  private  $telefon;
  private  $telefax;
  private  $betreff;
  private  $kundennummer;
  private  $versandart;
  private  $vertrieb;
  private  $zahlungsweise;
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
  private  $abweichendelieferadresse;
  private  $abweichenderechnungsadresse;
  private  $liefername;
  private  $lieferabteilung;
  private  $lieferunterabteilung;
  private  $lieferland;
  private  $lieferstrasse;
  private  $lieferort;
  private  $lieferplz;
  private  $lieferadresszusatz;
  private  $lieferansprechpartner;
  private  $liefertelefon;
  private  $liefertelefax;
  private  $liefermail;
  private  $autoversand;
  private  $keinporto;
  private  $ust_befreit;
  private  $firma;
  private  $versendet;
  private  $versendet_am;
  private  $versendet_per;
  private  $versendet_durch;
  private  $inbearbeitung;
  private  $vermerk;
  private  $logdatei;
  private  $ansprechpartner;
  private  $keinsteuersatz;
  private  $ohne_briefpapier;
  private  $pdfarchiviert;
  private  $pdfarchiviertversion;
  private  $schreibschutz;

  public $app;            //application object 

  public function ObjGenAngebot($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM angebot WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->datum=$result[datum];
    $this->gueltigbis=$result[gueltigbis];
    $this->projekt=$result[projekt];
    $this->belegnr=$result[belegnr];
    $this->bearbeiter=$result[bearbeiter];
    $this->anfrage=$result[anfrage];
    $this->auftrag=$result[auftrag];
    $this->freitext=$result[freitext];
    $this->internebemerkung=$result[internebemerkung];
    $this->status=$result[status];
    $this->adresse=$result[adresse];
    $this->retyp=$result[retyp];
    $this->rechnungname=$result[rechnungname];
    $this->retelefon=$result[retelefon];
    $this->reansprechpartner=$result[reansprechpartner];
    $this->retelefax=$result[retelefax];
    $this->reabteilung=$result[reabteilung];
    $this->reemail=$result[reemail];
    $this->reunterabteilung=$result[reunterabteilung];
    $this->readresszusatz=$result[readresszusatz];
    $this->restrasse=$result[restrasse];
    $this->replz=$result[replz];
    $this->reort=$result[reort];
    $this->reland=$result[reland];
    $this->name=$result[name];
    $this->abteilung=$result[abteilung];
    $this->unterabteilung=$result[unterabteilung];
    $this->strasse=$result[strasse];
    $this->adresszusatz=$result[adresszusatz];
    $this->plz=$result[plz];
    $this->ort=$result[ort];
    $this->land=$result[land];
    $this->ustid=$result[ustid];
    $this->email=$result[email];
    $this->telefon=$result[telefon];
    $this->telefax=$result[telefax];
    $this->betreff=$result[betreff];
    $this->kundennummer=$result[kundennummer];
    $this->versandart=$result[versandart];
    $this->vertrieb=$result[vertrieb];
    $this->zahlungsweise=$result[zahlungsweise];
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
    $this->abweichendelieferadresse=$result[abweichendelieferadresse];
    $this->abweichenderechnungsadresse=$result[abweichenderechnungsadresse];
    $this->liefername=$result[liefername];
    $this->lieferabteilung=$result[lieferabteilung];
    $this->lieferunterabteilung=$result[lieferunterabteilung];
    $this->lieferland=$result[lieferland];
    $this->lieferstrasse=$result[lieferstrasse];
    $this->lieferort=$result[lieferort];
    $this->lieferplz=$result[lieferplz];
    $this->lieferadresszusatz=$result[lieferadresszusatz];
    $this->lieferansprechpartner=$result[lieferansprechpartner];
    $this->liefertelefon=$result[liefertelefon];
    $this->liefertelefax=$result[liefertelefax];
    $this->liefermail=$result[liefermail];
    $this->autoversand=$result[autoversand];
    $this->keinporto=$result[keinporto];
    $this->ust_befreit=$result[ust_befreit];
    $this->firma=$result[firma];
    $this->versendet=$result[versendet];
    $this->versendet_am=$result[versendet_am];
    $this->versendet_per=$result[versendet_per];
    $this->versendet_durch=$result[versendet_durch];
    $this->inbearbeitung=$result[inbearbeitung];
    $this->vermerk=$result[vermerk];
    $this->logdatei=$result[logdatei];
    $this->ansprechpartner=$result[ansprechpartner];
    $this->keinsteuersatz=$result[keinsteuersatz];
    $this->ohne_briefpapier=$result[ohne_briefpapier];
    $this->pdfarchiviert=$result[pdfarchiviert];
    $this->pdfarchiviertversion=$result[pdfarchiviertversion];
    $this->schreibschutz=$result[schreibschutz];
  }

  public function Create()
  {
    $sql = "INSERT INTO angebot (id,datum,gueltigbis,projekt,belegnr,bearbeiter,anfrage,auftrag,freitext,internebemerkung,status,adresse,retyp,rechnungname,retelefon,reansprechpartner,retelefax,reabteilung,reemail,reunterabteilung,readresszusatz,restrasse,replz,reort,reland,name,abteilung,unterabteilung,strasse,adresszusatz,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,versandart,vertrieb,zahlungsweise,zahlungszieltage,zahlungszieltageskonto,zahlungszielskonto,gesamtsumme,bank_inhaber,bank_institut,bank_blz,bank_konto,kreditkarte_typ,kreditkarte_inhaber,kreditkarte_nummer,kreditkarte_pruefnummer,kreditkarte_monat,kreditkarte_jahr,abweichendelieferadresse,abweichenderechnungsadresse,liefername,lieferabteilung,lieferunterabteilung,lieferland,lieferstrasse,lieferort,lieferplz,lieferadresszusatz,lieferansprechpartner,liefertelefon,liefertelefax,liefermail,autoversand,keinporto,ust_befreit,firma,versendet,versendet_am,versendet_per,versendet_durch,inbearbeitung,vermerk,logdatei,ansprechpartner,keinsteuersatz,ohne_briefpapier,pdfarchiviert,pdfarchiviertversion,schreibschutz)
      VALUES(DEFAULT, ".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'").", ".(($this->gueltigbis=='' || $this->gueltigbis=='--') ? 'NOW()' : "'".$this->gueltigbis."'").", '{$this->projekt}', '{$this->belegnr}', '{$this->bearbeiter}', '{$this->anfrage}', '{$this->auftrag}', '{$this->freitext}', '{$this->internebemerkung}', '{$this->status}', ".((is_numeric($this->adresse)) ? $this->adresse : '0').", '{$this->retyp}', '{$this->rechnungname}', '{$this->retelefon}', '{$this->reansprechpartner}', '{$this->retelefax}', '{$this->reabteilung}', '{$this->reemail}', '{$this->reunterabteilung}', '{$this->readresszusatz}', '{$this->restrasse}', '{$this->replz}', '{$this->reort}', '{$this->reland}', '{$this->name}', '{$this->abteilung}', '{$this->unterabteilung}', '{$this->strasse}', '{$this->adresszusatz}', '{$this->plz}', '{$this->ort}', '{$this->land}', '{$this->ustid}', '{$this->email}', '{$this->telefon}', '{$this->telefax}', '{$this->betreff}', '{$this->kundennummer}', '{$this->versandart}', '{$this->vertrieb}', '{$this->zahlungsweise}', ".((is_numeric($this->zahlungszieltage)) ? $this->zahlungszieltage : '0').", ".((is_numeric($this->zahlungszieltageskonto)) ? $this->zahlungszieltageskonto : '0').", ".((is_numeric($this->zahlungszielskonto)) ? $this->zahlungszielskonto : '0').", ".((is_numeric($this->gesamtsumme)) ? $this->gesamtsumme : '0').", '{$this->bank_inhaber}', '{$this->bank_institut}', ".((is_numeric($this->bank_blz)) ? $this->bank_blz : '0').", ".((is_numeric($this->bank_konto)) ? $this->bank_konto : '0').", '{$this->kreditkarte_typ}', '{$this->kreditkarte_inhaber}', '{$this->kreditkarte_nummer}', '{$this->kreditkarte_pruefnummer}', ".((is_numeric($this->kreditkarte_monat)) ? $this->kreditkarte_monat : '0').", ".((is_numeric($this->kreditkarte_jahr)) ? $this->kreditkarte_jahr : '0').", ".((is_numeric($this->abweichendelieferadresse)) ? $this->abweichendelieferadresse : '0').", ".((is_numeric($this->abweichenderechnungsadresse)) ? $this->abweichenderechnungsadresse : '0').", '{$this->liefername}', '{$this->lieferabteilung}', '{$this->lieferunterabteilung}', '{$this->lieferland}', '{$this->lieferstrasse}', '{$this->lieferort}', '{$this->lieferplz}', '{$this->lieferadresszusatz}', '{$this->lieferansprechpartner}', '{$this->liefertelefon}', '{$this->liefertelefax}', '{$this->liefermail}', ".((is_numeric($this->autoversand)) ? $this->autoversand : '0').", ".((is_numeric($this->keinporto)) ? $this->keinporto : '0').", ".((is_numeric($this->ust_befreit)) ? $this->ust_befreit : '0').", ".((is_numeric($this->firma)) ? $this->firma : '0').", ".((is_numeric($this->versendet)) ? $this->versendet : '0').", ".(($this->versendet_am=='' || $this->versendet_am=='--') ? 'NOW()' : "'".$this->versendet_am."'").", '{$this->versendet_per}', '{$this->versendet_durch}', ".((is_numeric($this->inbearbeitung)) ? $this->inbearbeitung : '0').", '{$this->vermerk}', ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").", '{$this->ansprechpartner}', ".((is_numeric($this->keinsteuersatz)) ? $this->keinsteuersatz : '0').", ".((is_numeric($this->ohne_briefpapier)) ? $this->ohne_briefpapier : '0').", ".((is_numeric($this->pdfarchiviert)) ? $this->pdfarchiviert : '0').", ".((is_numeric($this->pdfarchiviertversion)) ? $this->pdfarchiviertversion : '0').", ".((is_numeric($this->schreibschutz)) ? $this->schreibschutz : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE angebot SET
      datum=".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'").",
      gueltigbis=".(($this->gueltigbis=='' || $this->gueltigbis=='--') ? 'NOW()' : "'".$this->gueltigbis."'").",
      projekt='{$this->projekt}',
      belegnr='{$this->belegnr}',
      bearbeiter='{$this->bearbeiter}',
      anfrage='{$this->anfrage}',
      auftrag='{$this->auftrag}',
      freitext='{$this->freitext}',
      internebemerkung='{$this->internebemerkung}',
      status='{$this->status}',
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      retyp='{$this->retyp}',
      rechnungname='{$this->rechnungname}',
      retelefon='{$this->retelefon}',
      reansprechpartner='{$this->reansprechpartner}',
      retelefax='{$this->retelefax}',
      reabteilung='{$this->reabteilung}',
      reemail='{$this->reemail}',
      reunterabteilung='{$this->reunterabteilung}',
      readresszusatz='{$this->readresszusatz}',
      restrasse='{$this->restrasse}',
      replz='{$this->replz}',
      reort='{$this->reort}',
      reland='{$this->reland}',
      name='{$this->name}',
      abteilung='{$this->abteilung}',
      unterabteilung='{$this->unterabteilung}',
      strasse='{$this->strasse}',
      adresszusatz='{$this->adresszusatz}',
      plz='{$this->plz}',
      ort='{$this->ort}',
      land='{$this->land}',
      ustid='{$this->ustid}',
      email='{$this->email}',
      telefon='{$this->telefon}',
      telefax='{$this->telefax}',
      betreff='{$this->betreff}',
      kundennummer='{$this->kundennummer}',
      versandart='{$this->versandart}',
      vertrieb='{$this->vertrieb}',
      zahlungsweise='{$this->zahlungsweise}',
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
      abweichendelieferadresse=".((is_numeric($this->abweichendelieferadresse)) ? $this->abweichendelieferadresse : '0').",
      abweichenderechnungsadresse=".((is_numeric($this->abweichenderechnungsadresse)) ? $this->abweichenderechnungsadresse : '0').",
      liefername='{$this->liefername}',
      lieferabteilung='{$this->lieferabteilung}',
      lieferunterabteilung='{$this->lieferunterabteilung}',
      lieferland='{$this->lieferland}',
      lieferstrasse='{$this->lieferstrasse}',
      lieferort='{$this->lieferort}',
      lieferplz='{$this->lieferplz}',
      lieferadresszusatz='{$this->lieferadresszusatz}',
      lieferansprechpartner='{$this->lieferansprechpartner}',
      liefertelefon='{$this->liefertelefon}',
      liefertelefax='{$this->liefertelefax}',
      liefermail='{$this->liefermail}',
      autoversand=".((is_numeric($this->autoversand)) ? $this->autoversand : '0').",
      keinporto=".((is_numeric($this->keinporto)) ? $this->keinporto : '0').",
      ust_befreit=".((is_numeric($this->ust_befreit)) ? $this->ust_befreit : '0').",
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
      versendet=".((is_numeric($this->versendet)) ? $this->versendet : '0').",
      versendet_am=".(($this->versendet_am=='' || $this->versendet_am=='--') ? 'NOW()' : "'".$this->versendet_am."'").",
      versendet_per='{$this->versendet_per}',
      versendet_durch='{$this->versendet_durch}',
      inbearbeitung=".((is_numeric($this->inbearbeitung)) ? $this->inbearbeitung : '0').",
      vermerk='{$this->vermerk}',
      logdatei=".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").",
      ansprechpartner='{$this->ansprechpartner}',
      keinsteuersatz=".((is_numeric($this->keinsteuersatz)) ? $this->keinsteuersatz : '0').",
      ohne_briefpapier=".((is_numeric($this->ohne_briefpapier)) ? $this->ohne_briefpapier : '0').",
      pdfarchiviert=".((is_numeric($this->pdfarchiviert)) ? $this->pdfarchiviert : '0').",
      pdfarchiviertversion=".((is_numeric($this->pdfarchiviertversion)) ? $this->pdfarchiviertversion : '0').",
      schreibschutz=".((is_numeric($this->schreibschutz)) ? $this->schreibschutz : '0')."
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

    $sql = "DELETE FROM angebot WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->datum="";
    $this->gueltigbis="";
    $this->projekt="";
    $this->belegnr="";
    $this->bearbeiter="";
    $this->anfrage="";
    $this->auftrag="";
    $this->freitext="";
    $this->internebemerkung="";
    $this->status="";
    $this->adresse="";
    $this->retyp="";
    $this->rechnungname="";
    $this->retelefon="";
    $this->reansprechpartner="";
    $this->retelefax="";
    $this->reabteilung="";
    $this->reemail="";
    $this->reunterabteilung="";
    $this->readresszusatz="";
    $this->restrasse="";
    $this->replz="";
    $this->reort="";
    $this->reland="";
    $this->name="";
    $this->abteilung="";
    $this->unterabteilung="";
    $this->strasse="";
    $this->adresszusatz="";
    $this->plz="";
    $this->ort="";
    $this->land="";
    $this->ustid="";
    $this->email="";
    $this->telefon="";
    $this->telefax="";
    $this->betreff="";
    $this->kundennummer="";
    $this->versandart="";
    $this->vertrieb="";
    $this->zahlungsweise="";
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
    $this->abweichendelieferadresse="";
    $this->abweichenderechnungsadresse="";
    $this->liefername="";
    $this->lieferabteilung="";
    $this->lieferunterabteilung="";
    $this->lieferland="";
    $this->lieferstrasse="";
    $this->lieferort="";
    $this->lieferplz="";
    $this->lieferadresszusatz="";
    $this->lieferansprechpartner="";
    $this->liefertelefon="";
    $this->liefertelefax="";
    $this->liefermail="";
    $this->autoversand="";
    $this->keinporto="";
    $this->ust_befreit="";
    $this->firma="";
    $this->versendet="";
    $this->versendet_am="";
    $this->versendet_per="";
    $this->versendet_durch="";
    $this->inbearbeitung="";
    $this->vermerk="";
    $this->logdatei="";
    $this->ansprechpartner="";
    $this->keinsteuersatz="";
    $this->ohne_briefpapier="";
    $this->pdfarchiviert="";
    $this->pdfarchiviertversion="";
    $this->schreibschutz="";
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
  function SetGueltigbis($value) { $this->gueltigbis=$value; }
  function GetGueltigbis() { return $this->gueltigbis; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetBelegnr($value) { $this->belegnr=$value; }
  function GetBelegnr() { return $this->belegnr; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetAnfrage($value) { $this->anfrage=$value; }
  function GetAnfrage() { return $this->anfrage; }
  function SetAuftrag($value) { $this->auftrag=$value; }
  function GetAuftrag() { return $this->auftrag; }
  function SetFreitext($value) { $this->freitext=$value; }
  function GetFreitext() { return $this->freitext; }
  function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  function GetInternebemerkung() { return $this->internebemerkung; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetRetyp($value) { $this->retyp=$value; }
  function GetRetyp() { return $this->retyp; }
  function SetRechnungname($value) { $this->rechnungname=$value; }
  function GetRechnungname() { return $this->rechnungname; }
  function SetRetelefon($value) { $this->retelefon=$value; }
  function GetRetelefon() { return $this->retelefon; }
  function SetReansprechpartner($value) { $this->reansprechpartner=$value; }
  function GetReansprechpartner() { return $this->reansprechpartner; }
  function SetRetelefax($value) { $this->retelefax=$value; }
  function GetRetelefax() { return $this->retelefax; }
  function SetReabteilung($value) { $this->reabteilung=$value; }
  function GetReabteilung() { return $this->reabteilung; }
  function SetReemail($value) { $this->reemail=$value; }
  function GetReemail() { return $this->reemail; }
  function SetReunterabteilung($value) { $this->reunterabteilung=$value; }
  function GetReunterabteilung() { return $this->reunterabteilung; }
  function SetReadresszusatz($value) { $this->readresszusatz=$value; }
  function GetReadresszusatz() { return $this->readresszusatz; }
  function SetRestrasse($value) { $this->restrasse=$value; }
  function GetRestrasse() { return $this->restrasse; }
  function SetReplz($value) { $this->replz=$value; }
  function GetReplz() { return $this->replz; }
  function SetReort($value) { $this->reort=$value; }
  function GetReort() { return $this->reort; }
  function SetReland($value) { $this->reland=$value; }
  function GetReland() { return $this->reland; }
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
  function SetVersandart($value) { $this->versandart=$value; }
  function GetVersandart() { return $this->versandart; }
  function SetVertrieb($value) { $this->vertrieb=$value; }
  function GetVertrieb() { return $this->vertrieb; }
  function SetZahlungsweise($value) { $this->zahlungsweise=$value; }
  function GetZahlungsweise() { return $this->zahlungsweise; }
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
  function SetAbweichendelieferadresse($value) { $this->abweichendelieferadresse=$value; }
  function GetAbweichendelieferadresse() { return $this->abweichendelieferadresse; }
  function SetAbweichenderechnungsadresse($value) { $this->abweichenderechnungsadresse=$value; }
  function GetAbweichenderechnungsadresse() { return $this->abweichenderechnungsadresse; }
  function SetLiefername($value) { $this->liefername=$value; }
  function GetLiefername() { return $this->liefername; }
  function SetLieferabteilung($value) { $this->lieferabteilung=$value; }
  function GetLieferabteilung() { return $this->lieferabteilung; }
  function SetLieferunterabteilung($value) { $this->lieferunterabteilung=$value; }
  function GetLieferunterabteilung() { return $this->lieferunterabteilung; }
  function SetLieferland($value) { $this->lieferland=$value; }
  function GetLieferland() { return $this->lieferland; }
  function SetLieferstrasse($value) { $this->lieferstrasse=$value; }
  function GetLieferstrasse() { return $this->lieferstrasse; }
  function SetLieferort($value) { $this->lieferort=$value; }
  function GetLieferort() { return $this->lieferort; }
  function SetLieferplz($value) { $this->lieferplz=$value; }
  function GetLieferplz() { return $this->lieferplz; }
  function SetLieferadresszusatz($value) { $this->lieferadresszusatz=$value; }
  function GetLieferadresszusatz() { return $this->lieferadresszusatz; }
  function SetLieferansprechpartner($value) { $this->lieferansprechpartner=$value; }
  function GetLieferansprechpartner() { return $this->lieferansprechpartner; }
  function SetLiefertelefon($value) { $this->liefertelefon=$value; }
  function GetLiefertelefon() { return $this->liefertelefon; }
  function SetLiefertelefax($value) { $this->liefertelefax=$value; }
  function GetLiefertelefax() { return $this->liefertelefax; }
  function SetLiefermail($value) { $this->liefermail=$value; }
  function GetLiefermail() { return $this->liefermail; }
  function SetAutoversand($value) { $this->autoversand=$value; }
  function GetAutoversand() { return $this->autoversand; }
  function SetKeinporto($value) { $this->keinporto=$value; }
  function GetKeinporto() { return $this->keinporto; }
  function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  function GetUst_Befreit() { return $this->ust_befreit; }
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
  function SetVermerk($value) { $this->vermerk=$value; }
  function GetVermerk() { return $this->vermerk; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  function GetAnsprechpartner() { return $this->ansprechpartner; }
  function SetKeinsteuersatz($value) { $this->keinsteuersatz=$value; }
  function GetKeinsteuersatz() { return $this->keinsteuersatz; }
  function SetOhne_Briefpapier($value) { $this->ohne_briefpapier=$value; }
  function GetOhne_Briefpapier() { return $this->ohne_briefpapier; }
  function SetPdfarchiviert($value) { $this->pdfarchiviert=$value; }
  function GetPdfarchiviert() { return $this->pdfarchiviert; }
  function SetPdfarchiviertversion($value) { $this->pdfarchiviertversion=$value; }
  function GetPdfarchiviertversion() { return $this->pdfarchiviertversion; }
  function SetSchreibschutz($value) { $this->schreibschutz=$value; }
  function GetSchreibschutz() { return $this->schreibschutz; }

}

?>