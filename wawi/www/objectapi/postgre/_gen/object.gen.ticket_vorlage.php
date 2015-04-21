<?php

class ObjGenTicket_Vorlage
{

  private  $id;
  private  $projekt;
  private  $vorlagenname;
  private  $vorlage;
  private  $firma;
  private  $sichtbar;

  public $app;            //application object 

  public function ObjGenTicket_Vorlage($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM ticket_vorlage WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->projekt=$result[projekt];
    $this->vorlagenname=$result[vorlagenname];
    $this->vorlage=$result[vorlage];
    $this->firma=$result[firma];
    $this->sichtbar=$result[sichtbar];
  }

  public function Create()
  {
    $sql = "INSERT INTO ticket_vorlage (id,projekt,vorlagenname,vorlage,firma,sichtbar)
      VALUES(DEFAULT, ".((is_numeric($this->projekt)) ? $this->projekt : '0').", '{$this->vorlagenname}', '{$this->vorlage}', ".((is_numeric($this->firma)) ? $this->firma : '0').", ".((is_numeric($this->sichtbar)) ? $this->sichtbar : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE ticket_vorlage SET
      projekt=".((is_numeric($this->projekt)) ? $this->projekt : '0').",
      vorlagenname='{$this->vorlagenname}',
      vorlage='{$this->vorlage}',
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
      sichtbar=".((is_numeric($this->sichtbar)) ? $this->sichtbar : '0')."
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

    $sql = "DELETE FROM ticket_vorlage WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->projekt="";
    $this->vorlagenname="";
    $this->vorlage="";
    $this->firma="";
    $this->sichtbar="";
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
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetVorlagenname($value) { $this->vorlagenname=$value; }
  function GetVorlagenname() { return $this->vorlagenname; }
  function SetVorlage($value) { $this->vorlage=$value; }
  function GetVorlage() { return $this->vorlage; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetSichtbar($value) { $this->sichtbar=$value; }
  function GetSichtbar() { return $this->sichtbar; }

}

?>