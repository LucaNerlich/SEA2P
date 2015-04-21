<?php

class ObjGenKalender_Event
{

  private  $id;
  private  $kalender;
  private  $bezeichnung;
  private  $beschreibung;
  private  $von;
  private  $bis;
  private  $allDay;
  private  $color;
  private  $public;

  public $app;            //application object 

  public function ObjGenKalender_Event($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM kalender_event WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->kalender=$result[kalender];
    $this->bezeichnung=$result[bezeichnung];
    $this->beschreibung=$result[beschreibung];
    $this->von=$result[von];
    $this->bis=$result[bis];
    $this->allDay=$result[allDay];
    $this->color=$result[color];
    $this->public=$result[public];
  }

  public function Create()
  {
    $sql = "INSERT INTO kalender_event (id,kalender,bezeichnung,beschreibung,von,bis,allDay,color,public)
      VALUES(DEFAULT, ".((is_numeric($this->kalender)) ? $this->kalender : '0').", '{$this->bezeichnung}', '{$this->beschreibung}', ".(($this->von=='' || $this->von=='--') ? 'NOW()' : "'".$this->von."'").", ".(($this->bis=='' || $this->bis=='--') ? 'NOW()' : "'".$this->bis."'").", ".((is_numeric($this->allDay)) ? $this->allDay : '0').", '{$this->color}', ".((is_numeric($this->public)) ? $this->public : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE kalender_event SET
      kalender=".((is_numeric($this->kalender)) ? $this->kalender : '0').",
      bezeichnung='{$this->bezeichnung}',
      beschreibung='{$this->beschreibung}',
      von=".(($this->von=='' || $this->von=='--') ? 'NOW()' : "'".$this->von."'").",
      bis=".(($this->bis=='' || $this->bis=='--') ? 'NOW()' : "'".$this->bis."'").",
      allDay=".((is_numeric($this->allDay)) ? $this->allDay : '0').",
      color='{$this->color}',
      public=".((is_numeric($this->public)) ? $this->public : '0')."
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

    $sql = "DELETE FROM kalender_event WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->kalender="";
    $this->bezeichnung="";
    $this->beschreibung="";
    $this->von="";
    $this->bis="";
    $this->allDay="";
    $this->color="";
    $this->public="";
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
  function SetKalender($value) { $this->kalender=$value; }
  function GetKalender() { return $this->kalender; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetVon($value) { $this->von=$value; }
  function GetVon() { return $this->von; }
  function SetBis($value) { $this->bis=$value; }
  function GetBis() { return $this->bis; }
  function SetAllday($value) { $this->allDay=$value; }
  function GetAllday() { return $this->allDay; }
  function SetColor($value) { $this->color=$value; }
  function GetColor() { return $this->color; }
  function SetPublic($value) { $this->public=$value; }
  function GetPublic() { return $this->public; }

}

?>