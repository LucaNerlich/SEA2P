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
  }

  public function Create()
  {
    $sql = "INSERT INTO verbindlichkeit (id,rechnung,zahlbarbis,betrag,umsatzsteuer,summenormal,summeermaessigt,skonto,skontobis,freigabe,freigabemitarbeiter,bestellung,adresse,status,bezahlt,kontoauszuege,firma,logdatei)
      VALUES(DEFAULT, '{$this->rechnung}', ".(($this->zahlbarbis=='' || $this->zahlbarbis=='--') ? 'NOW()' : "'".$this->zahlbarbis."'").", ".((is_numeric($this->betrag)) ? $this->betrag : '0').", '{$this->umsatzsteuer}', ".((is_numeric($this->summenormal)) ? $this->summenormal : '0').", ".((is_numeric($this->summeermaessigt)) ? $this->summeermaessigt : '0').", ".((is_numeric($this->skonto)) ? $this->skonto : '0').", ".(($this->skontobis=='' || $this->skontobis=='--') ? 'NOW()' : "'".$this->skontobis."'").", ".((is_numeric($this->freigabe)) ? $this->freigabe : '0').", '{$this->freigabemitarbeiter}', ".((is_numeric($this->bestellung)) ? $this->bestellung : '0').", ".((is_numeric($this->adresse)) ? $this->adresse : '0').", '{$this->status}', ".((is_numeric($this->bezahlt)) ? $this->bezahlt : '0').", ".((is_numeric($this->kontoauszuege)) ? $this->kontoauszuege : '0').", ".((is_numeric($this->firma)) ? $this->firma : '0').", ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE verbindlichkeit SET
      rechnung='{$this->rechnung}',
      zahlbarbis=".(($this->zahlbarbis=='' || $this->zahlbarbis=='--') ? 'NOW()' : "'".$this->zahlbarbis."'").",
      betrag=".((is_numeric($this->betrag)) ? $this->betrag : '0').",
      umsatzsteuer='{$this->umsatzsteuer}',
      summenormal=".((is_numeric($this->summenormal)) ? $this->summenormal : '0').",
      summeermaessigt=".((is_numeric($this->summeermaessigt)) ? $this->summeermaessigt : '0').",
      skonto=".((is_numeric($this->skonto)) ? $this->skonto : '0').",
      skontobis=".(($this->skontobis=='' || $this->skontobis=='--') ? 'NOW()' : "'".$this->skontobis."'").",
      freigabe=".((is_numeric($this->freigabe)) ? $this->freigabe : '0').",
      freigabemitarbeiter='{$this->freigabemitarbeiter}',
      bestellung=".((is_numeric($this->bestellung)) ? $this->bestellung : '0').",
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      status='{$this->status}',
      bezahlt=".((is_numeric($this->bezahlt)) ? $this->bezahlt : '0').",
      kontoauszuege=".((is_numeric($this->kontoauszuege)) ? $this->kontoauszuege : '0').",
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
      logdatei=".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'")."
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

}

?>