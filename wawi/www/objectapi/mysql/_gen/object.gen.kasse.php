<?php

class ObjGenKasse
{

  private  $id;
  private  $datum;
  private  $auswahl;
  private  $betrag;
  private  $adresse;
  private  $grund;
  private  $projekt;
  private  $bearbeiter;
  private  $steuergruppe;
  private  $exportiert;
  private  $exportiert_datum;
  private  $firma;
  private  $logdatei;

  public $app;            //application object 

  public function ObjGenKasse($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM kasse WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->datum=$result[datum];
    $this->auswahl=$result[auswahl];
    $this->betrag=$result[betrag];
    $this->adresse=$result[adresse];
    $this->grund=$result[grund];
    $this->projekt=$result[projekt];
    $this->bearbeiter=$result[bearbeiter];
    $this->steuergruppe=$result[steuergruppe];
    $this->exportiert=$result[exportiert];
    $this->exportiert_datum=$result[exportiert_datum];
    $this->firma=$result[firma];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO kasse (id,datum,auswahl,betrag,adresse,grund,projekt,bearbeiter,steuergruppe,exportiert,exportiert_datum,firma,logdatei)
      VALUES('','{$this->datum}','{$this->auswahl}','{$this->betrag}','{$this->adresse}','{$this->grund}','{$this->projekt}','{$this->bearbeiter}','{$this->steuergruppe}','{$this->exportiert}','{$this->exportiert_datum}','{$this->firma}','{$this->logdatei}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE kasse SET
      datum='{$this->datum}',
      auswahl='{$this->auswahl}',
      betrag='{$this->betrag}',
      adresse='{$this->adresse}',
      grund='{$this->grund}',
      projekt='{$this->projekt}',
      bearbeiter='{$this->bearbeiter}',
      steuergruppe='{$this->steuergruppe}',
      exportiert='{$this->exportiert}',
      exportiert_datum='{$this->exportiert_datum}',
      firma='{$this->firma}',
      logdatei='{$this->logdatei}'
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

    $sql = "DELETE FROM kasse WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->datum="";
    $this->auswahl="";
    $this->betrag="";
    $this->adresse="";
    $this->grund="";
    $this->projekt="";
    $this->bearbeiter="";
    $this->steuergruppe="";
    $this->exportiert="";
    $this->exportiert_datum="";
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
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetAuswahl($value) { $this->auswahl=$value; }
  function GetAuswahl() { return $this->auswahl; }
  function SetBetrag($value) { $this->betrag=$value; }
  function GetBetrag() { return $this->betrag; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetGrund($value) { $this->grund=$value; }
  function GetGrund() { return $this->grund; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetSteuergruppe($value) { $this->steuergruppe=$value; }
  function GetSteuergruppe() { return $this->steuergruppe; }
  function SetExportiert($value) { $this->exportiert=$value; }
  function GetExportiert() { return $this->exportiert; }
  function SetExportiert_Datum($value) { $this->exportiert_datum=$value; }
  function GetExportiert_Datum() { return $this->exportiert_datum; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>