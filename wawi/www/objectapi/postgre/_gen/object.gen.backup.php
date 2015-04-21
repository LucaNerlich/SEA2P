<?php

class ObjGenBackup
{

  private  $id;
  private  $adresse;
  private  $name;
  private  $dateiname;
  private  $datum;

  public $app;            //application object 

  public function ObjGenBackup($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM backup WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->name=$result[name];
    $this->dateiname=$result[dateiname];
    $this->datum=$result[datum];
  }

  public function Create()
  {
    $sql = "INSERT INTO backup (id,adresse,name,dateiname,datum)
      VALUES(DEFAULT, ".((is_numeric($this->adresse)) ? $this->adresse : '0').", '{$this->name}', '{$this->dateiname}', ".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'").")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE backup SET
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      name='{$this->name}',
      dateiname='{$this->dateiname}',
      datum=".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'")."
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

    $sql = "DELETE FROM backup WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->name="";
    $this->dateiname="";
    $this->datum="";
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
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetDateiname($value) { $this->dateiname=$value; }
  function GetDateiname() { return $this->dateiname; }
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }

}

?>