<?php

class ObjGenAufgabe_Erledigt
{

  private  $id;
  private  $adresse;
  private  $aufgabe;
  private  $abgeschlossen_am;
  private  $logdatei;

  public $app;            //application object 

  public function ObjGenAufgabe_Erledigt($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM aufgabe_erledigt WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->aufgabe=$result[aufgabe];
    $this->abgeschlossen_am=$result[abgeschlossen_am];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO aufgabe_erledigt (id,adresse,aufgabe,abgeschlossen_am,logdatei)
      VALUES('','{$this->adresse}','{$this->aufgabe}','{$this->abgeschlossen_am}','{$this->logdatei}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE aufgabe_erledigt SET
      adresse='{$this->adresse}',
      aufgabe='{$this->aufgabe}',
      abgeschlossen_am='{$this->abgeschlossen_am}',
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

    $sql = "DELETE FROM aufgabe_erledigt WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->aufgabe="";
    $this->abgeschlossen_am="";
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
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetAufgabe($value) { $this->aufgabe=$value; }
  function GetAufgabe() { return $this->aufgabe; }
  function SetAbgeschlossen_Am($value) { $this->abgeschlossen_am=$value; }
  function GetAbgeschlossen_Am() { return $this->abgeschlossen_am; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>