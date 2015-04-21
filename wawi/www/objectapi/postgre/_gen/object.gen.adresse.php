<?php

class ObjGenAdresse
{

  private  $id;
  private  $typ;
  private  $marketingsperre;
  private  $trackingsperre;
  private  $rechnungsadresse;
  private  $sprache;
  private  $name;
  private  $abteilung;
  private  $unterabteilung;
  private  $ansprechpartner;
  private  $land;
  private  $strasse;
  private  $ort;
  private  $plz;
  private  $telefon;
  private  $telefax;
  private  $mobil;
  private  $email;
  private  $ustid;
  private  $ust_befreit;
  private  $passwort_gesendet;
  private  $sonstiges;
  private  $adresszusatz;
  private  $kundenfreigabe;
  private  $steuer;
  private  $logdatei;
  private  $kundennummer;
  private  $lieferantennummer;
  private  $mitarbeiternummer;
  private  $konto;
  private  $blz;
  private  $bank;
  private  $inhaber;
  private  $swift;
  private  $iban;
  private  $waehrung;
  private  $paypal;
  private  $paypalinhaber;
  private  $paypalwaehrung;
  private  $projekt;
  private  $partner;
  private  $zahlungsweise;
  private  $zahlungszieltage;
  private  $zahlungszieltageskonto;
  private  $zahlungszielskonto;
  private  $versandart;
  private  $kundennummerlieferant;
  private  $zahlungsweiselieferant;
  private  $zahlungszieltagelieferant;
  private  $zahlungszieltageskontolieferant;
  private  $zahlungszielskontolieferant;
  private  $versandartlieferant;
  private  $geloescht;
  private  $firma;
  private  $webid;
  private  $internetseite;
  private  $vorname;
  private  $kalender_aufgaben;
  private  $titel;
  private  $anschreiben;
  private  $logfile;
  private  $mlmaktiv;
  private  $mlmvertragsbeginn;
  private  $geburtstag;
  private  $liefersperre;
  private  $mlmpositionierung;
  private  $steuernummer;
  private  $steuerbefreit;
  private  $mlmmitmwst;
  private  $mlmabrechnung;
  private  $mlmwaehrungauszwahlung;
  private  $sponsor;
  private  $geworbenvon;
  private  $liefersperregrund;
  private  $verrechnungskontoreisekosten;

  public $app;            //application object 

  public function ObjGenAdresse($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->typ=$result[typ];
    $this->marketingsperre=$result[marketingsperre];
    $this->trackingsperre=$result[trackingsperre];
    $this->rechnungsadresse=$result[rechnungsadresse];
    $this->sprache=$result[sprache];
    $this->name=$result[name];
    $this->abteilung=$result[abteilung];
    $this->unterabteilung=$result[unterabteilung];
    $this->ansprechpartner=$result[ansprechpartner];
    $this->land=$result[land];
    $this->strasse=$result[strasse];
    $this->ort=$result[ort];
    $this->plz=$result[plz];
    $this->telefon=$result[telefon];
    $this->telefax=$result[telefax];
    $this->mobil=$result[mobil];
    $this->email=$result[email];
    $this->ustid=$result[ustid];
    $this->ust_befreit=$result[ust_befreit];
    $this->passwort_gesendet=$result[passwort_gesendet];
    $this->sonstiges=$result[sonstiges];
    $this->adresszusatz=$result[adresszusatz];
    $this->kundenfreigabe=$result[kundenfreigabe];
    $this->steuer=$result[steuer];
    $this->logdatei=$result[logdatei];
    $this->kundennummer=$result[kundennummer];
    $this->lieferantennummer=$result[lieferantennummer];
    $this->mitarbeiternummer=$result[mitarbeiternummer];
    $this->konto=$result[konto];
    $this->blz=$result[blz];
    $this->bank=$result[bank];
    $this->inhaber=$result[inhaber];
    $this->swift=$result[swift];
    $this->iban=$result[iban];
    $this->waehrung=$result[waehrung];
    $this->paypal=$result[paypal];
    $this->paypalinhaber=$result[paypalinhaber];
    $this->paypalwaehrung=$result[paypalwaehrung];
    $this->projekt=$result[projekt];
    $this->partner=$result[partner];
    $this->zahlungsweise=$result[zahlungsweise];
    $this->zahlungszieltage=$result[zahlungszieltage];
    $this->zahlungszieltageskonto=$result[zahlungszieltageskonto];
    $this->zahlungszielskonto=$result[zahlungszielskonto];
    $this->versandart=$result[versandart];
    $this->kundennummerlieferant=$result[kundennummerlieferant];
    $this->zahlungsweiselieferant=$result[zahlungsweiselieferant];
    $this->zahlungszieltagelieferant=$result[zahlungszieltagelieferant];
    $this->zahlungszieltageskontolieferant=$result[zahlungszieltageskontolieferant];
    $this->zahlungszielskontolieferant=$result[zahlungszielskontolieferant];
    $this->versandartlieferant=$result[versandartlieferant];
    $this->geloescht=$result[geloescht];
    $this->firma=$result[firma];
    $this->webid=$result[webid];
    $this->internetseite=$result[internetseite];
    $this->vorname=$result[vorname];
    $this->kalender_aufgaben=$result[kalender_aufgaben];
    $this->titel=$result[titel];
    $this->anschreiben=$result[anschreiben];
    $this->logfile=$result[logfile];
    $this->mlmaktiv=$result[mlmaktiv];
    $this->mlmvertragsbeginn=$result[mlmvertragsbeginn];
    $this->geburtstag=$result[geburtstag];
    $this->liefersperre=$result[liefersperre];
    $this->mlmpositionierung=$result[mlmpositionierung];
    $this->steuernummer=$result[steuernummer];
    $this->steuerbefreit=$result[steuerbefreit];
    $this->mlmmitmwst=$result[mlmmitmwst];
    $this->mlmabrechnung=$result[mlmabrechnung];
    $this->mlmwaehrungauszwahlung=$result[mlmwaehrungauszwahlung];
    $this->sponsor=$result[sponsor];
    $this->geworbenvon=$result[geworbenvon];
    $this->liefersperregrund=$result[liefersperregrund];
    $this->verrechnungskontoreisekosten=$result[verrechnungskontoreisekosten];
  }

  public function Create()
  {
    $sql = "INSERT INTO adresse (id,typ,marketingsperre,trackingsperre,rechnungsadresse,sprache,name,abteilung,unterabteilung,ansprechpartner,land,strasse,ort,plz,telefon,telefax,mobil,email,ustid,ust_befreit,passwort_gesendet,sonstiges,adresszusatz,kundenfreigabe,steuer,logdatei,kundennummer,lieferantennummer,mitarbeiternummer,konto,blz,bank,inhaber,swift,iban,waehrung,paypal,paypalinhaber,paypalwaehrung,projekt,partner,zahlungsweise,zahlungszieltage,zahlungszieltageskonto,zahlungszielskonto,versandart,kundennummerlieferant,zahlungsweiselieferant,zahlungszieltagelieferant,zahlungszieltageskontolieferant,zahlungszielskontolieferant,versandartlieferant,geloescht,firma,webid,internetseite,vorname,kalender_aufgaben,titel,anschreiben,logfile,mlmaktiv,mlmvertragsbeginn,geburtstag,liefersperre,mlmpositionierung,steuernummer,steuerbefreit,mlmmitmwst,mlmabrechnung,mlmwaehrungauszwahlung,sponsor,geworbenvon,liefersperregrund,verrechnungskontoreisekosten)
      VALUES(DEFAULT, '{$this->typ}', '{$this->marketingsperre}', ".((is_numeric($this->trackingsperre)) ? $this->trackingsperre : '0').", ".((is_numeric($this->rechnungsadresse)) ? $this->rechnungsadresse : '0').", '{$this->sprache}', '{$this->name}', '{$this->abteilung}', '{$this->unterabteilung}', '{$this->ansprechpartner}', '{$this->land}', '{$this->strasse}', '{$this->ort}', '{$this->plz}', '{$this->telefon}', '{$this->telefax}', '{$this->mobil}', '{$this->email}', '{$this->ustid}', ".((is_numeric($this->ust_befreit)) ? $this->ust_befreit : '0').", ".((is_numeric($this->passwort_gesendet)) ? $this->passwort_gesendet : '0').", '{$this->sonstiges}', '{$this->adresszusatz}', ".((is_numeric($this->kundenfreigabe)) ? $this->kundenfreigabe : '0').", '{$this->steuer}', ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").", '{$this->kundennummer}', '{$this->lieferantennummer}', '{$this->mitarbeiternummer}', '{$this->konto}', '{$this->blz}', '{$this->bank}', '{$this->inhaber}', '{$this->swift}', '{$this->iban}', '{$this->waehrung}', '{$this->paypal}', '{$this->paypalinhaber}', '{$this->paypalwaehrung}', ".((is_numeric($this->projekt)) ? $this->projekt : '0').", ".((is_numeric($this->partner)) ? $this->partner : '0').", '{$this->zahlungsweise}', '{$this->zahlungszieltage}', '{$this->zahlungszieltageskonto}', '{$this->zahlungszielskonto}', '{$this->versandart}', '{$this->kundennummerlieferant}', '{$this->zahlungsweiselieferant}', '{$this->zahlungszieltagelieferant}', '{$this->zahlungszieltageskontolieferant}', '{$this->zahlungszielskontolieferant}', '{$this->versandartlieferant}', ".((is_numeric($this->geloescht)) ? $this->geloescht : '0').", ".((is_numeric($this->firma)) ? $this->firma : '0').", '{$this->webid}', '{$this->internetseite}', '{$this->vorname}', ".((is_numeric($this->kalender_aufgaben)) ? $this->kalender_aufgaben : '0').", '{$this->titel}', '{$this->anschreiben}', '{$this->logfile}', ".((is_numeric($this->mlmaktiv)) ? $this->mlmaktiv : '0').", ".(($this->mlmvertragsbeginn=='' || $this->mlmvertragsbeginn=='--') ? 'NOW()' : "'".$this->mlmvertragsbeginn."'").", ".(($this->geburtstag=='' || $this->geburtstag=='--') ? 'NOW()' : "'".$this->geburtstag."'").", ".((is_numeric($this->liefersperre)) ? $this->liefersperre : '0').", '{$this->mlmpositionierung}', '{$this->steuernummer}', ".((is_numeric($this->steuerbefreit)) ? $this->steuerbefreit : '0').", ".((is_numeric($this->mlmmitmwst)) ? $this->mlmmitmwst : '0').", '{$this->mlmabrechnung}', '{$this->mlmwaehrungauszwahlung}', ".((is_numeric($this->sponsor)) ? $this->sponsor : '0').", ".((is_numeric($this->geworbenvon)) ? $this->geworbenvon : '0').", '{$this->liefersperregrund}', ".((is_numeric($this->verrechnungskontoreisekosten)) ? $this->verrechnungskontoreisekosten : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE adresse SET
      typ='{$this->typ}',
      marketingsperre='{$this->marketingsperre}',
      trackingsperre=".((is_numeric($this->trackingsperre)) ? $this->trackingsperre : '0').",
      rechnungsadresse=".((is_numeric($this->rechnungsadresse)) ? $this->rechnungsadresse : '0').",
      sprache='{$this->sprache}',
      name='{$this->name}',
      abteilung='{$this->abteilung}',
      unterabteilung='{$this->unterabteilung}',
      ansprechpartner='{$this->ansprechpartner}',
      land='{$this->land}',
      strasse='{$this->strasse}',
      ort='{$this->ort}',
      plz='{$this->plz}',
      telefon='{$this->telefon}',
      telefax='{$this->telefax}',
      mobil='{$this->mobil}',
      email='{$this->email}',
      ustid='{$this->ustid}',
      ust_befreit=".((is_numeric($this->ust_befreit)) ? $this->ust_befreit : '0').",
      passwort_gesendet=".((is_numeric($this->passwort_gesendet)) ? $this->passwort_gesendet : '0').",
      sonstiges='{$this->sonstiges}',
      adresszusatz='{$this->adresszusatz}',
      kundenfreigabe=".((is_numeric($this->kundenfreigabe)) ? $this->kundenfreigabe : '0').",
      steuer='{$this->steuer}',
      logdatei=".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").",
      kundennummer='{$this->kundennummer}',
      lieferantennummer='{$this->lieferantennummer}',
      mitarbeiternummer='{$this->mitarbeiternummer}',
      konto='{$this->konto}',
      blz='{$this->blz}',
      bank='{$this->bank}',
      inhaber='{$this->inhaber}',
      swift='{$this->swift}',
      iban='{$this->iban}',
      waehrung='{$this->waehrung}',
      paypal='{$this->paypal}',
      paypalinhaber='{$this->paypalinhaber}',
      paypalwaehrung='{$this->paypalwaehrung}',
      projekt=".((is_numeric($this->projekt)) ? $this->projekt : '0').",
      partner=".((is_numeric($this->partner)) ? $this->partner : '0').",
      zahlungsweise='{$this->zahlungsweise}',
      zahlungszieltage='{$this->zahlungszieltage}',
      zahlungszieltageskonto='{$this->zahlungszieltageskonto}',
      zahlungszielskonto='{$this->zahlungszielskonto}',
      versandart='{$this->versandart}',
      kundennummerlieferant='{$this->kundennummerlieferant}',
      zahlungsweiselieferant='{$this->zahlungsweiselieferant}',
      zahlungszieltagelieferant='{$this->zahlungszieltagelieferant}',
      zahlungszieltageskontolieferant='{$this->zahlungszieltageskontolieferant}',
      zahlungszielskontolieferant='{$this->zahlungszielskontolieferant}',
      versandartlieferant='{$this->versandartlieferant}',
      geloescht=".((is_numeric($this->geloescht)) ? $this->geloescht : '0').",
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
      webid='{$this->webid}',
      internetseite='{$this->internetseite}',
      vorname='{$this->vorname}',
      kalender_aufgaben=".((is_numeric($this->kalender_aufgaben)) ? $this->kalender_aufgaben : '0').",
      titel='{$this->titel}',
      anschreiben='{$this->anschreiben}',
      logfile='{$this->logfile}',
      mlmaktiv=".((is_numeric($this->mlmaktiv)) ? $this->mlmaktiv : '0').",
      mlmvertragsbeginn=".(($this->mlmvertragsbeginn=='' || $this->mlmvertragsbeginn=='--') ? 'NOW()' : "'".$this->mlmvertragsbeginn."'").",
      geburtstag=".(($this->geburtstag=='' || $this->geburtstag=='--') ? 'NOW()' : "'".$this->geburtstag."'").",
      liefersperre=".((is_numeric($this->liefersperre)) ? $this->liefersperre : '0').",
      mlmpositionierung='{$this->mlmpositionierung}',
      steuernummer='{$this->steuernummer}',
      steuerbefreit=".((is_numeric($this->steuerbefreit)) ? $this->steuerbefreit : '0').",
      mlmmitmwst=".((is_numeric($this->mlmmitmwst)) ? $this->mlmmitmwst : '0').",
      mlmabrechnung='{$this->mlmabrechnung}',
      mlmwaehrungauszwahlung='{$this->mlmwaehrungauszwahlung}',
      sponsor=".((is_numeric($this->sponsor)) ? $this->sponsor : '0').",
      geworbenvon=".((is_numeric($this->geworbenvon)) ? $this->geworbenvon : '0').",
      liefersperregrund='{$this->liefersperregrund}',
      verrechnungskontoreisekosten=".((is_numeric($this->verrechnungskontoreisekosten)) ? $this->verrechnungskontoreisekosten : '0')."
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

    $sql = "DELETE FROM adresse WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->typ="";
    $this->marketingsperre="";
    $this->trackingsperre="";
    $this->rechnungsadresse="";
    $this->sprache="";
    $this->name="";
    $this->abteilung="";
    $this->unterabteilung="";
    $this->ansprechpartner="";
    $this->land="";
    $this->strasse="";
    $this->ort="";
    $this->plz="";
    $this->telefon="";
    $this->telefax="";
    $this->mobil="";
    $this->email="";
    $this->ustid="";
    $this->ust_befreit="";
    $this->passwort_gesendet="";
    $this->sonstiges="";
    $this->adresszusatz="";
    $this->kundenfreigabe="";
    $this->steuer="";
    $this->logdatei="";
    $this->kundennummer="";
    $this->lieferantennummer="";
    $this->mitarbeiternummer="";
    $this->konto="";
    $this->blz="";
    $this->bank="";
    $this->inhaber="";
    $this->swift="";
    $this->iban="";
    $this->waehrung="";
    $this->paypal="";
    $this->paypalinhaber="";
    $this->paypalwaehrung="";
    $this->projekt="";
    $this->partner="";
    $this->zahlungsweise="";
    $this->zahlungszieltage="";
    $this->zahlungszieltageskonto="";
    $this->zahlungszielskonto="";
    $this->versandart="";
    $this->kundennummerlieferant="";
    $this->zahlungsweiselieferant="";
    $this->zahlungszieltagelieferant="";
    $this->zahlungszieltageskontolieferant="";
    $this->zahlungszielskontolieferant="";
    $this->versandartlieferant="";
    $this->geloescht="";
    $this->firma="";
    $this->webid="";
    $this->internetseite="";
    $this->vorname="";
    $this->kalender_aufgaben="";
    $this->titel="";
    $this->anschreiben="";
    $this->logfile="";
    $this->mlmaktiv="";
    $this->mlmvertragsbeginn="";
    $this->geburtstag="";
    $this->liefersperre="";
    $this->mlmpositionierung="";
    $this->steuernummer="";
    $this->steuerbefreit="";
    $this->mlmmitmwst="";
    $this->mlmabrechnung="";
    $this->mlmwaehrungauszwahlung="";
    $this->sponsor="";
    $this->geworbenvon="";
    $this->liefersperregrund="";
    $this->verrechnungskontoreisekosten="";
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
  function SetMarketingsperre($value) { $this->marketingsperre=$value; }
  function GetMarketingsperre() { return $this->marketingsperre; }
  function SetTrackingsperre($value) { $this->trackingsperre=$value; }
  function GetTrackingsperre() { return $this->trackingsperre; }
  function SetRechnungsadresse($value) { $this->rechnungsadresse=$value; }
  function GetRechnungsadresse() { return $this->rechnungsadresse; }
  function SetSprache($value) { $this->sprache=$value; }
  function GetSprache() { return $this->sprache; }
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetAbteilung($value) { $this->abteilung=$value; }
  function GetAbteilung() { return $this->abteilung; }
  function SetUnterabteilung($value) { $this->unterabteilung=$value; }
  function GetUnterabteilung() { return $this->unterabteilung; }
  function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  function GetAnsprechpartner() { return $this->ansprechpartner; }
  function SetLand($value) { $this->land=$value; }
  function GetLand() { return $this->land; }
  function SetStrasse($value) { $this->strasse=$value; }
  function GetStrasse() { return $this->strasse; }
  function SetOrt($value) { $this->ort=$value; }
  function GetOrt() { return $this->ort; }
  function SetPlz($value) { $this->plz=$value; }
  function GetPlz() { return $this->plz; }
  function SetTelefon($value) { $this->telefon=$value; }
  function GetTelefon() { return $this->telefon; }
  function SetTelefax($value) { $this->telefax=$value; }
  function GetTelefax() { return $this->telefax; }
  function SetMobil($value) { $this->mobil=$value; }
  function GetMobil() { return $this->mobil; }
  function SetEmail($value) { $this->email=$value; }
  function GetEmail() { return $this->email; }
  function SetUstid($value) { $this->ustid=$value; }
  function GetUstid() { return $this->ustid; }
  function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  function GetUst_Befreit() { return $this->ust_befreit; }
  function SetPasswort_Gesendet($value) { $this->passwort_gesendet=$value; }
  function GetPasswort_Gesendet() { return $this->passwort_gesendet; }
  function SetSonstiges($value) { $this->sonstiges=$value; }
  function GetSonstiges() { return $this->sonstiges; }
  function SetAdresszusatz($value) { $this->adresszusatz=$value; }
  function GetAdresszusatz() { return $this->adresszusatz; }
  function SetKundenfreigabe($value) { $this->kundenfreigabe=$value; }
  function GetKundenfreigabe() { return $this->kundenfreigabe; }
  function SetSteuer($value) { $this->steuer=$value; }
  function GetSteuer() { return $this->steuer; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetKundennummer($value) { $this->kundennummer=$value; }
  function GetKundennummer() { return $this->kundennummer; }
  function SetLieferantennummer($value) { $this->lieferantennummer=$value; }
  function GetLieferantennummer() { return $this->lieferantennummer; }
  function SetMitarbeiternummer($value) { $this->mitarbeiternummer=$value; }
  function GetMitarbeiternummer() { return $this->mitarbeiternummer; }
  function SetKonto($value) { $this->konto=$value; }
  function GetKonto() { return $this->konto; }
  function SetBlz($value) { $this->blz=$value; }
  function GetBlz() { return $this->blz; }
  function SetBank($value) { $this->bank=$value; }
  function GetBank() { return $this->bank; }
  function SetInhaber($value) { $this->inhaber=$value; }
  function GetInhaber() { return $this->inhaber; }
  function SetSwift($value) { $this->swift=$value; }
  function GetSwift() { return $this->swift; }
  function SetIban($value) { $this->iban=$value; }
  function GetIban() { return $this->iban; }
  function SetWaehrung($value) { $this->waehrung=$value; }
  function GetWaehrung() { return $this->waehrung; }
  function SetPaypal($value) { $this->paypal=$value; }
  function GetPaypal() { return $this->paypal; }
  function SetPaypalinhaber($value) { $this->paypalinhaber=$value; }
  function GetPaypalinhaber() { return $this->paypalinhaber; }
  function SetPaypalwaehrung($value) { $this->paypalwaehrung=$value; }
  function GetPaypalwaehrung() { return $this->paypalwaehrung; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetPartner($value) { $this->partner=$value; }
  function GetPartner() { return $this->partner; }
  function SetZahlungsweise($value) { $this->zahlungsweise=$value; }
  function GetZahlungsweise() { return $this->zahlungsweise; }
  function SetZahlungszieltage($value) { $this->zahlungszieltage=$value; }
  function GetZahlungszieltage() { return $this->zahlungszieltage; }
  function SetZahlungszieltageskonto($value) { $this->zahlungszieltageskonto=$value; }
  function GetZahlungszieltageskonto() { return $this->zahlungszieltageskonto; }
  function SetZahlungszielskonto($value) { $this->zahlungszielskonto=$value; }
  function GetZahlungszielskonto() { return $this->zahlungszielskonto; }
  function SetVersandart($value) { $this->versandart=$value; }
  function GetVersandart() { return $this->versandart; }
  function SetKundennummerlieferant($value) { $this->kundennummerlieferant=$value; }
  function GetKundennummerlieferant() { return $this->kundennummerlieferant; }
  function SetZahlungsweiselieferant($value) { $this->zahlungsweiselieferant=$value; }
  function GetZahlungsweiselieferant() { return $this->zahlungsweiselieferant; }
  function SetZahlungszieltagelieferant($value) { $this->zahlungszieltagelieferant=$value; }
  function GetZahlungszieltagelieferant() { return $this->zahlungszieltagelieferant; }
  function SetZahlungszieltageskontolieferant($value) { $this->zahlungszieltageskontolieferant=$value; }
  function GetZahlungszieltageskontolieferant() { return $this->zahlungszieltageskontolieferant; }
  function SetZahlungszielskontolieferant($value) { $this->zahlungszielskontolieferant=$value; }
  function GetZahlungszielskontolieferant() { return $this->zahlungszielskontolieferant; }
  function SetVersandartlieferant($value) { $this->versandartlieferant=$value; }
  function GetVersandartlieferant() { return $this->versandartlieferant; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetWebid($value) { $this->webid=$value; }
  function GetWebid() { return $this->webid; }
  function SetInternetseite($value) { $this->internetseite=$value; }
  function GetInternetseite() { return $this->internetseite; }
  function SetVorname($value) { $this->vorname=$value; }
  function GetVorname() { return $this->vorname; }
  function SetKalender_Aufgaben($value) { $this->kalender_aufgaben=$value; }
  function GetKalender_Aufgaben() { return $this->kalender_aufgaben; }
  function SetTitel($value) { $this->titel=$value; }
  function GetTitel() { return $this->titel; }
  function SetAnschreiben($value) { $this->anschreiben=$value; }
  function GetAnschreiben() { return $this->anschreiben; }
  function SetLogfile($value) { $this->logfile=$value; }
  function GetLogfile() { return $this->logfile; }
  function SetMlmaktiv($value) { $this->mlmaktiv=$value; }
  function GetMlmaktiv() { return $this->mlmaktiv; }
  function SetMlmvertragsbeginn($value) { $this->mlmvertragsbeginn=$value; }
  function GetMlmvertragsbeginn() { return $this->mlmvertragsbeginn; }
  function SetGeburtstag($value) { $this->geburtstag=$value; }
  function GetGeburtstag() { return $this->geburtstag; }
  function SetLiefersperre($value) { $this->liefersperre=$value; }
  function GetLiefersperre() { return $this->liefersperre; }
  function SetMlmpositionierung($value) { $this->mlmpositionierung=$value; }
  function GetMlmpositionierung() { return $this->mlmpositionierung; }
  function SetSteuernummer($value) { $this->steuernummer=$value; }
  function GetSteuernummer() { return $this->steuernummer; }
  function SetSteuerbefreit($value) { $this->steuerbefreit=$value; }
  function GetSteuerbefreit() { return $this->steuerbefreit; }
  function SetMlmmitmwst($value) { $this->mlmmitmwst=$value; }
  function GetMlmmitmwst() { return $this->mlmmitmwst; }
  function SetMlmabrechnung($value) { $this->mlmabrechnung=$value; }
  function GetMlmabrechnung() { return $this->mlmabrechnung; }
  function SetMlmwaehrungauszwahlung($value) { $this->mlmwaehrungauszwahlung=$value; }
  function GetMlmwaehrungauszwahlung() { return $this->mlmwaehrungauszwahlung; }
  function SetSponsor($value) { $this->sponsor=$value; }
  function GetSponsor() { return $this->sponsor; }
  function SetGeworbenvon($value) { $this->geworbenvon=$value; }
  function GetGeworbenvon() { return $this->geworbenvon; }
  function SetLiefersperregrund($value) { $this->liefersperregrund=$value; }
  function GetLiefersperregrund() { return $this->liefersperregrund; }
  function SetVerrechnungskontoreisekosten($value) { $this->verrechnungskontoreisekosten=$value; }
  function GetVerrechnungskontoreisekosten() { return $this->verrechnungskontoreisekosten; }

}

?>