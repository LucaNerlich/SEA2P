<?php

class ObjGenVerbindlichkeit
{

  private  $id;
  private  $rechnung;
  private  $zahlbarbis;
  private  $betrag;
  private  $umsatzsteuer;
  private  $summenormal;
  private  $summeermaessigt;
  private  $skonto;
  private  $skontobis;
  private  $freigabe;
  private  $freigabemitarbeiter;
  private  $bestellung;
  private  $adresse;
  private  $status;
  private  $bezahlt;
  private  $kontoauszuege;
  private  $firma;
  private  $logdatei;
  private  $rechnungsdatum;
  private  $kostenstelle;
  private  $beschreibung;
  private  $verwendungszweck;
  private  $art;
  private  $dta_datei;
  private  $waehrung;
  private  $rechnungsfreigabe;
  private  $frachtkosten;
  private  $sachkonto;
  private  $bestellung1;
  private  $bestellung1betrag;
  private  $bestellung1bemerkung;
  private  $bestellung2;
  private  $bestellung2betrag;
  private  $bestellung2bemerkung;
  private  $bestellung3;
  private  $bestellung3betrag;
  private  $bestellung3bemerkung;
  private  $bestellung4;
  private  $bestellung4betrag;
  private  $bestellung4bemerkung;
  private  $bestellung5;
  private  $bestellung5betrag;
  private  $bestellung5bemerkung;
  private  $bestellung6;
  private  $bestellung6betrag;
  private  $bestellung6bemerkung;
  private  $bestellung7;
  private  $bestellung7betrag;
  private  $bestellung7bemerkung;
  private  $bestellung8;
  private  $bestellung8betrag;
  private  $bestellung8bemerkung;
  private  $bestellung9;
  private  $bestellung9betrag;
  private  $bestellung9bemerkung;
  private  $bestellung10;
  private  $bestellung10betrag;
  private  $bestellung10bemerkung;
  private  $bestellung11;
  private  $bestellung11betrag;
  private  $bestellung11bemerkung;
  private  $bestellung12;
  private  $bestellung12betrag;
  private  $bestellung12bemerkung;
  private  $bestellung13;
  private  $bestellung13betrag;
  private  $bestellung13bemerkung;
  private  $bestellung14;
  private  $bestellung14betrag;
  private  $bestellung14bemerkung;
  private  $bestellung15;
  private  $bestellung15betrag;
  private  $bestellung15bemerkung;

  public $app;            //application object 

  public function ObjGenVerbindlichkeit($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM verbindlichkeit WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->rechnung=$result[rechnung];
    $this->zahlbarbis=$result[zahlbarbis];
    $this->betrag=$result[betrag];
    $this->umsatzsteuer=$result[umsatzsteuer];
    $this->summenormal=$result[summenormal];
    $this->summeermaessigt=$result[summeermaessigt];
    $this->skonto=$result[skonto];
    $this->skontobis=$result[skontobis];
    $this->freigabe=$result[freigabe];
    $this->freigabemitarbeiter=$result[freigabemitarbeiter];
    $this->bestellung=$result[bestellung];
    $this->adresse=$result[adresse];
    $this->status=$result[status];
    $this->bezahlt=$result[bezahlt];
    $this->kontoauszuege=$result[kontoauszuege];
    $this->firma=$result[firma];
    $this->logdatei=$result[logdatei];
    $this->rechnungsdatum=$result[rechnungsdatum];
    $this->kostenstelle=$result[kostenstelle];
    $this->beschreibung=$result[beschreibung];
    $this->verwendungszweck=$result[verwendungszweck];
    $this->art=$result[art];
    $this->dta_datei=$result[dta_datei];
    $this->waehrung=$result[waehrung];
    $this->rechnungsfreigabe=$result[rechnungsfreigabe];
    $this->frachtkosten=$result[frachtkosten];
    $this->sachkonto=$result[sachkonto];
    $this->bestellung1=$result[bestellung1];
    $this->bestellung1betrag=$result[bestellung1betrag];
    $this->bestellung1bemerkung=$result[bestellung1bemerkung];
    $this->bestellung2=$result[bestellung2];
    $this->bestellung2betrag=$result[bestellung2betrag];
    $this->bestellung2bemerkung=$result[bestellung2bemerkung];
    $this->bestellung3=$result[bestellung3];
    $this->bestellung3betrag=$result[bestellung3betrag];
    $this->bestellung3bemerkung=$result[bestellung3bemerkung];
    $this->bestellung4=$result[bestellung4];
    $this->bestellung4betrag=$result[bestellung4betrag];
    $this->bestellung4bemerkung=$result[bestellung4bemerkung];
    $this->bestellung5=$result[bestellung5];
    $this->bestellung5betrag=$result[bestellung5betrag];
    $this->bestellung5bemerkung=$result[bestellung5bemerkung];
    $this->bestellung6=$result[bestellung6];
    $this->bestellung6betrag=$result[bestellung6betrag];
    $this->bestellung6bemerkung=$result[bestellung6bemerkung];
    $this->bestellung7=$result[bestellung7];
    $this->bestellung7betrag=$result[bestellung7betrag];
    $this->bestellung7bemerkung=$result[bestellung7bemerkung];
    $this->bestellung8=$result[bestellung8];
    $this->bestellung8betrag=$result[bestellung8betrag];
    $this->bestellung8bemerkung=$result[bestellung8bemerkung];
    $this->bestellung9=$result[bestellung9];
    $this->bestellung9betrag=$result[bestellung9betrag];
    $this->bestellung9bemerkung=$result[bestellung9bemerkung];
    $this->bestellung10=$result[bestellung10];
    $this->bestellung10betrag=$result[bestellung10betrag];
    $this->bestellung10bemerkung=$result[bestellung10bemerkung];
    $this->bestellung11=$result[bestellung11];
    $this->bestellung11betrag=$result[bestellung11betrag];
    $this->bestellung11bemerkung=$result[bestellung11bemerkung];
    $this->bestellung12=$result[bestellung12];
    $this->bestellung12betrag=$result[bestellung12betrag];
    $this->bestellung12bemerkung=$result[bestellung12bemerkung];
    $this->bestellung13=$result[bestellung13];
    $this->bestellung13betrag=$result[bestellung13betrag];
    $this->bestellung13bemerkung=$result[bestellung13bemerkung];
    $this->bestellung14=$result[bestellung14];
    $this->bestellung14betrag=$result[bestellung14betrag];
    $this->bestellung14bemerkung=$result[bestellung14bemerkung];
    $this->bestellung15=$result[bestellung15];
    $this->bestellung15betrag=$result[bestellung15betrag];
    $this->bestellung15bemerkung=$result[bestellung15bemerkung];
  }

  public function Create()
  {
    $sql = "INSERT INTO verbindlichkeit (id,rechnung,zahlbarbis,betrag,umsatzsteuer,summenormal,summeermaessigt,skonto,skontobis,freigabe,freigabemitarbeiter,bestellung,adresse,status,bezahlt,kontoauszuege,firma,logdatei,rechnungsdatum,kostenstelle,beschreibung,verwendungszweck,art,dta_datei,waehrung,rechnungsfreigabe,frachtkosten,sachkonto,bestellung1,bestellung1betrag,bestellung1bemerkung,bestellung2,bestellung2betrag,bestellung2bemerkung,bestellung3,bestellung3betrag,bestellung3bemerkung,bestellung4,bestellung4betrag,bestellung4bemerkung,bestellung5,bestellung5betrag,bestellung5bemerkung,bestellung6,bestellung6betrag,bestellung6bemerkung,bestellung7,bestellung7betrag,bestellung7bemerkung,bestellung8,bestellung8betrag,bestellung8bemerkung,bestellung9,bestellung9betrag,bestellung9bemerkung,bestellung10,bestellung10betrag,bestellung10bemerkung,bestellung11,bestellung11betrag,bestellung11bemerkung,bestellung12,bestellung12betrag,bestellung12bemerkung,bestellung13,bestellung13betrag,bestellung13bemerkung,bestellung14,bestellung14betrag,bestellung14bemerkung,bestellung15,bestellung15betrag,bestellung15bemerkung)
      VALUES('','{$this->rechnung}','{$this->zahlbarbis}','{$this->betrag}','{$this->umsatzsteuer}','{$this->summenormal}','{$this->summeermaessigt}','{$this->skonto}','{$this->skontobis}','{$this->freigabe}','{$this->freigabemitarbeiter}','{$this->bestellung}','{$this->adresse}','{$this->status}','{$this->bezahlt}','{$this->kontoauszuege}','{$this->firma}','{$this->logdatei}','{$this->rechnungsdatum}','{$this->kostenstelle}','{$this->beschreibung}','{$this->verwendungszweck}','{$this->art}','{$this->dta_datei}','{$this->waehrung}','{$this->rechnungsfreigabe}','{$this->frachtkosten}','{$this->sachkonto}','{$this->bestellung1}','{$this->bestellung1betrag}','{$this->bestellung1bemerkung}','{$this->bestellung2}','{$this->bestellung2betrag}','{$this->bestellung2bemerkung}','{$this->bestellung3}','{$this->bestellung3betrag}','{$this->bestellung3bemerkung}','{$this->bestellung4}','{$this->bestellung4betrag}','{$this->bestellung4bemerkung}','{$this->bestellung5}','{$this->bestellung5betrag}','{$this->bestellung5bemerkung}','{$this->bestellung6}','{$this->bestellung6betrag}','{$this->bestellung6bemerkung}','{$this->bestellung7}','{$this->bestellung7betrag}','{$this->bestellung7bemerkung}','{$this->bestellung8}','{$this->bestellung8betrag}','{$this->bestellung8bemerkung}','{$this->bestellung9}','{$this->bestellung9betrag}','{$this->bestellung9bemerkung}','{$this->bestellung10}','{$this->bestellung10betrag}','{$this->bestellung10bemerkung}','{$this->bestellung11}','{$this->bestellung11betrag}','{$this->bestellung11bemerkung}','{$this->bestellung12}','{$this->bestellung12betrag}','{$this->bestellung12bemerkung}','{$this->bestellung13}','{$this->bestellung13betrag}','{$this->bestellung13bemerkung}','{$this->bestellung14}','{$this->bestellung14betrag}','{$this->bestellung14bemerkung}','{$this->bestellung15}','{$this->bestellung15betrag}','{$this->bestellung15bemerkung}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE verbindlichkeit SET
      rechnung='{$this->rechnung}',
      zahlbarbis='{$this->zahlbarbis}',
      betrag='{$this->betrag}',
      umsatzsteuer='{$this->umsatzsteuer}',
      summenormal='{$this->summenormal}',
      summeermaessigt='{$this->summeermaessigt}',
      skonto='{$this->skonto}',
      skontobis='{$this->skontobis}',
      freigabe='{$this->freigabe}',
      freigabemitarbeiter='{$this->freigabemitarbeiter}',
      bestellung='{$this->bestellung}',
      adresse='{$this->adresse}',
      status='{$this->status}',
      bezahlt='{$this->bezahlt}',
      kontoauszuege='{$this->kontoauszuege}',
      firma='{$this->firma}',
      logdatei='{$this->logdatei}',
      rechnungsdatum='{$this->rechnungsdatum}',
      kostenstelle='{$this->kostenstelle}',
      beschreibung='{$this->beschreibung}',
      verwendungszweck='{$this->verwendungszweck}',
      art='{$this->art}',
      dta_datei='{$this->dta_datei}',
      waehrung='{$this->waehrung}',
      rechnungsfreigabe='{$this->rechnungsfreigabe}',
      frachtkosten='{$this->frachtkosten}',
      sachkonto='{$this->sachkonto}',
      bestellung1='{$this->bestellung1}',
      bestellung1betrag='{$this->bestellung1betrag}',
      bestellung1bemerkung='{$this->bestellung1bemerkung}',
      bestellung2='{$this->bestellung2}',
      bestellung2betrag='{$this->bestellung2betrag}',
      bestellung2bemerkung='{$this->bestellung2bemerkung}',
      bestellung3='{$this->bestellung3}',
      bestellung3betrag='{$this->bestellung3betrag}',
      bestellung3bemerkung='{$this->bestellung3bemerkung}',
      bestellung4='{$this->bestellung4}',
      bestellung4betrag='{$this->bestellung4betrag}',
      bestellung4bemerkung='{$this->bestellung4bemerkung}',
      bestellung5='{$this->bestellung5}',
      bestellung5betrag='{$this->bestellung5betrag}',
      bestellung5bemerkung='{$this->bestellung5bemerkung}',
      bestellung6='{$this->bestellung6}',
      bestellung6betrag='{$this->bestellung6betrag}',
      bestellung6bemerkung='{$this->bestellung6bemerkung}',
      bestellung7='{$this->bestellung7}',
      bestellung7betrag='{$this->bestellung7betrag}',
      bestellung7bemerkung='{$this->bestellung7bemerkung}',
      bestellung8='{$this->bestellung8}',
      bestellung8betrag='{$this->bestellung8betrag}',
      bestellung8bemerkung='{$this->bestellung8bemerkung}',
      bestellung9='{$this->bestellung9}',
      bestellung9betrag='{$this->bestellung9betrag}',
      bestellung9bemerkung='{$this->bestellung9bemerkung}',
      bestellung10='{$this->bestellung10}',
      bestellung10betrag='{$this->bestellung10betrag}',
      bestellung10bemerkung='{$this->bestellung10bemerkung}',
      bestellung11='{$this->bestellung11}',
      bestellung11betrag='{$this->bestellung11betrag}',
      bestellung11bemerkung='{$this->bestellung11bemerkung}',
      bestellung12='{$this->bestellung12}',
      bestellung12betrag='{$this->bestellung12betrag}',
      bestellung12bemerkung='{$this->bestellung12bemerkung}',
      bestellung13='{$this->bestellung13}',
      bestellung13betrag='{$this->bestellung13betrag}',
      bestellung13bemerkung='{$this->bestellung13bemerkung}',
      bestellung14='{$this->bestellung14}',
      bestellung14betrag='{$this->bestellung14betrag}',
      bestellung14bemerkung='{$this->bestellung14bemerkung}',
      bestellung15='{$this->bestellung15}',
      bestellung15betrag='{$this->bestellung15betrag}',
      bestellung15bemerkung='{$this->bestellung15bemerkung}'
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

    $sql = "DELETE FROM verbindlichkeit WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->rechnung="";
    $this->zahlbarbis="";
    $this->betrag="";
    $this->umsatzsteuer="";
    $this->summenormal="";
    $this->summeermaessigt="";
    $this->skonto="";
    $this->skontobis="";
    $this->freigabe="";
    $this->freigabemitarbeiter="";
    $this->bestellung="";
    $this->adresse="";
    $this->status="";
    $this->bezahlt="";
    $this->kontoauszuege="";
    $this->firma="";
    $this->logdatei="";
    $this->rechnungsdatum="";
    $this->kostenstelle="";
    $this->beschreibung="";
    $this->verwendungszweck="";
    $this->art="";
    $this->dta_datei="";
    $this->waehrung="";
    $this->rechnungsfreigabe="";
    $this->frachtkosten="";
    $this->sachkonto="";
    $this->bestellung1="";
    $this->bestellung1betrag="";
    $this->bestellung1bemerkung="";
    $this->bestellung2="";
    $this->bestellung2betrag="";
    $this->bestellung2bemerkung="";
    $this->bestellung3="";
    $this->bestellung3betrag="";
    $this->bestellung3bemerkung="";
    $this->bestellung4="";
    $this->bestellung4betrag="";
    $this->bestellung4bemerkung="";
    $this->bestellung5="";
    $this->bestellung5betrag="";
    $this->bestellung5bemerkung="";
    $this->bestellung6="";
    $this->bestellung6betrag="";
    $this->bestellung6bemerkung="";
    $this->bestellung7="";
    $this->bestellung7betrag="";
    $this->bestellung7bemerkung="";
    $this->bestellung8="";
    $this->bestellung8betrag="";
    $this->bestellung8bemerkung="";
    $this->bestellung9="";
    $this->bestellung9betrag="";
    $this->bestellung9bemerkung="";
    $this->bestellung10="";
    $this->bestellung10betrag="";
    $this->bestellung10bemerkung="";
    $this->bestellung11="";
    $this->bestellung11betrag="";
    $this->bestellung11bemerkung="";
    $this->bestellung12="";
    $this->bestellung12betrag="";
    $this->bestellung12bemerkung="";
    $this->bestellung13="";
    $this->bestellung13betrag="";
    $this->bestellung13bemerkung="";
    $this->bestellung14="";
    $this->bestellung14betrag="";
    $this->bestellung14bemerkung="";
    $this->bestellung15="";
    $this->bestellung15betrag="";
    $this->bestellung15bemerkung="";
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
  function SetRechnung($value) { $this->rechnung=$value; }
  function GetRechnung() { return $this->rechnung; }
  function SetZahlbarbis($value) { $this->zahlbarbis=$value; }
  function GetZahlbarbis() { return $this->zahlbarbis; }
  function SetBetrag($value) { $this->betrag=$value; }
  function GetBetrag() { return $this->betrag; }
  function SetUmsatzsteuer($value) { $this->umsatzsteuer=$value; }
  function GetUmsatzsteuer() { return $this->umsatzsteuer; }
  function SetSummenormal($value) { $this->summenormal=$value; }
  function GetSummenormal() { return $this->summenormal; }
  function SetSummeermaessigt($value) { $this->summeermaessigt=$value; }
  function GetSummeermaessigt() { return $this->summeermaessigt; }
  function SetSkonto($value) { $this->skonto=$value; }
  function GetSkonto() { return $this->skonto; }
  function SetSkontobis($value) { $this->skontobis=$value; }
  function GetSkontobis() { return $this->skontobis; }
  function SetFreigabe($value) { $this->freigabe=$value; }
  function GetFreigabe() { return $this->freigabe; }
  function SetFreigabemitarbeiter($value) { $this->freigabemitarbeiter=$value; }
  function GetFreigabemitarbeiter() { return $this->freigabemitarbeiter; }
  function SetBestellung($value) { $this->bestellung=$value; }
  function GetBestellung() { return $this->bestellung; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetBezahlt($value) { $this->bezahlt=$value; }
  function GetBezahlt() { return $this->bezahlt; }
  function SetKontoauszuege($value) { $this->kontoauszuege=$value; }
  function GetKontoauszuege() { return $this->kontoauszuege; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetRechnungsdatum($value) { $this->rechnungsdatum=$value; }
  function GetRechnungsdatum() { return $this->rechnungsdatum; }
  function SetKostenstelle($value) { $this->kostenstelle=$value; }
  function GetKostenstelle() { return $this->kostenstelle; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetVerwendungszweck($value) { $this->verwendungszweck=$value; }
  function GetVerwendungszweck() { return $this->verwendungszweck; }
  function SetArt($value) { $this->art=$value; }
  function GetArt() { return $this->art; }
  function SetDta_Datei($value) { $this->dta_datei=$value; }
  function GetDta_Datei() { return $this->dta_datei; }
  function SetWaehrung($value) { $this->waehrung=$value; }
  function GetWaehrung() { return $this->waehrung; }
  function SetRechnungsfreigabe($value) { $this->rechnungsfreigabe=$value; }
  function GetRechnungsfreigabe() { return $this->rechnungsfreigabe; }
  function SetFrachtkosten($value) { $this->frachtkosten=$value; }
  function GetFrachtkosten() { return $this->frachtkosten; }
  function SetSachkonto($value) { $this->sachkonto=$value; }
  function GetSachkonto() { return $this->sachkonto; }
  function SetBestellung1($value) { $this->bestellung1=$value; }
  function GetBestellung1() { return $this->bestellung1; }
  function SetBestellung1Betrag($value) { $this->bestellung1betrag=$value; }
  function GetBestellung1Betrag() { return $this->bestellung1betrag; }
  function SetBestellung1Bemerkung($value) { $this->bestellung1bemerkung=$value; }
  function GetBestellung1Bemerkung() { return $this->bestellung1bemerkung; }
  function SetBestellung2($value) { $this->bestellung2=$value; }
  function GetBestellung2() { return $this->bestellung2; }
  function SetBestellung2Betrag($value) { $this->bestellung2betrag=$value; }
  function GetBestellung2Betrag() { return $this->bestellung2betrag; }
  function SetBestellung2Bemerkung($value) { $this->bestellung2bemerkung=$value; }
  function GetBestellung2Bemerkung() { return $this->bestellung2bemerkung; }
  function SetBestellung3($value) { $this->bestellung3=$value; }
  function GetBestellung3() { return $this->bestellung3; }
  function SetBestellung3Betrag($value) { $this->bestellung3betrag=$value; }
  function GetBestellung3Betrag() { return $this->bestellung3betrag; }
  function SetBestellung3Bemerkung($value) { $this->bestellung3bemerkung=$value; }
  function GetBestellung3Bemerkung() { return $this->bestellung3bemerkung; }
  function SetBestellung4($value) { $this->bestellung4=$value; }
  function GetBestellung4() { return $this->bestellung4; }
  function SetBestellung4Betrag($value) { $this->bestellung4betrag=$value; }
  function GetBestellung4Betrag() { return $this->bestellung4betrag; }
  function SetBestellung4Bemerkung($value) { $this->bestellung4bemerkung=$value; }
  function GetBestellung4Bemerkung() { return $this->bestellung4bemerkung; }
  function SetBestellung5($value) { $this->bestellung5=$value; }
  function GetBestellung5() { return $this->bestellung5; }
  function SetBestellung5Betrag($value) { $this->bestellung5betrag=$value; }
  function GetBestellung5Betrag() { return $this->bestellung5betrag; }
  function SetBestellung5Bemerkung($value) { $this->bestellung5bemerkung=$value; }
  function GetBestellung5Bemerkung() { return $this->bestellung5bemerkung; }
  function SetBestellung6($value) { $this->bestellung6=$value; }
  function GetBestellung6() { return $this->bestellung6; }
  function SetBestellung6Betrag($value) { $this->bestellung6betrag=$value; }
  function GetBestellung6Betrag() { return $this->bestellung6betrag; }
  function SetBestellung6Bemerkung($value) { $this->bestellung6bemerkung=$value; }
  function GetBestellung6Bemerkung() { return $this->bestellung6bemerkung; }
  function SetBestellung7($value) { $this->bestellung7=$value; }
  function GetBestellung7() { return $this->bestellung7; }
  function SetBestellung7Betrag($value) { $this->bestellung7betrag=$value; }
  function GetBestellung7Betrag() { return $this->bestellung7betrag; }
  function SetBestellung7Bemerkung($value) { $this->bestellung7bemerkung=$value; }
  function GetBestellung7Bemerkung() { return $this->bestellung7bemerkung; }
  function SetBestellung8($value) { $this->bestellung8=$value; }
  function GetBestellung8() { return $this->bestellung8; }
  function SetBestellung8Betrag($value) { $this->bestellung8betrag=$value; }
  function GetBestellung8Betrag() { return $this->bestellung8betrag; }
  function SetBestellung8Bemerkung($value) { $this->bestellung8bemerkung=$value; }
  function GetBestellung8Bemerkung() { return $this->bestellung8bemerkung; }
  function SetBestellung9($value) { $this->bestellung9=$value; }
  function GetBestellung9() { return $this->bestellung9; }
  function SetBestellung9Betrag($value) { $this->bestellung9betrag=$value; }
  function GetBestellung9Betrag() { return $this->bestellung9betrag; }
  function SetBestellung9Bemerkung($value) { $this->bestellung9bemerkung=$value; }
  function GetBestellung9Bemerkung() { return $this->bestellung9bemerkung; }
  function SetBestellung10($value) { $this->bestellung10=$value; }
  function GetBestellung10() { return $this->bestellung10; }
  function SetBestellung10Betrag($value) { $this->bestellung10betrag=$value; }
  function GetBestellung10Betrag() { return $this->bestellung10betrag; }
  function SetBestellung10Bemerkung($value) { $this->bestellung10bemerkung=$value; }
  function GetBestellung10Bemerkung() { return $this->bestellung10bemerkung; }
  function SetBestellung11($value) { $this->bestellung11=$value; }
  function GetBestellung11() { return $this->bestellung11; }
  function SetBestellung11Betrag($value) { $this->bestellung11betrag=$value; }
  function GetBestellung11Betrag() { return $this->bestellung11betrag; }
  function SetBestellung11Bemerkung($value) { $this->bestellung11bemerkung=$value; }
  function GetBestellung11Bemerkung() { return $this->bestellung11bemerkung; }
  function SetBestellung12($value) { $this->bestellung12=$value; }
  function GetBestellung12() { return $this->bestellung12; }
  function SetBestellung12Betrag($value) { $this->bestellung12betrag=$value; }
  function GetBestellung12Betrag() { return $this->bestellung12betrag; }
  function SetBestellung12Bemerkung($value) { $this->bestellung12bemerkung=$value; }
  function GetBestellung12Bemerkung() { return $this->bestellung12bemerkung; }
  function SetBestellung13($value) { $this->bestellung13=$value; }
  function GetBestellung13() { return $this->bestellung13; }
  function SetBestellung13Betrag($value) { $this->bestellung13betrag=$value; }
  function GetBestellung13Betrag() { return $this->bestellung13betrag; }
  function SetBestellung13Bemerkung($value) { $this->bestellung13bemerkung=$value; }
  function GetBestellung13Bemerkung() { return $this->bestellung13bemerkung; }
  function SetBestellung14($value) { $this->bestellung14=$value; }
  function GetBestellung14() { return $this->bestellung14; }
  function SetBestellung14Betrag($value) { $this->bestellung14betrag=$value; }
  function GetBestellung14Betrag() { return $this->bestellung14betrag; }
  function SetBestellung14Bemerkung($value) { $this->bestellung14bemerkung=$value; }
  function GetBestellung14Bemerkung() { return $this->bestellung14bemerkung; }
  function SetBestellung15($value) { $this->bestellung15=$value; }
  function GetBestellung15() { return $this->bestellung15; }
  function SetBestellung15Betrag($value) { $this->bestellung15betrag=$value; }
  function GetBestellung15Betrag() { return $this->bestellung15betrag; }
  function SetBestellung15Bemerkung($value) { $this->bestellung15bemerkung=$value; }
  function GetBestellung15Bemerkung() { return $this->bestellung15bemerkung; }

}

?>