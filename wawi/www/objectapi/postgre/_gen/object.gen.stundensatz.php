<?php

class ObjGenStundensatz
{

  private  $id;
  private  $adresse;
  private  $satz;
  private  $typ;
  private  $projekt;
  private  $datum;

  public $app;            //application object 

  public function ObjGenStundensatz($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM stundensatz WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->satz=$result[satz];
    $this->typ=$result[typ];
    $this->projekt=$result[projekt];
    $this->datum=$result[datum];
  }

  public function Create()
  {
    $sql = "INSERT INTO stundensatz (id,adresse,satz,typ,projekt,datum)
      VALUES(DEFAULT, ".((is_numeric($this->adresse)) ? $this->adresse : '0').", '{$this->satz}', '{$this->typ}', ".((is_numeric($this->projekt)) ? $this->projekt : '0').", ".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'").")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE stundensatz SET
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      satz='{$this->satz}',
      typ='{$this->typ}',
      projekt=".((is_numeric($this->projekt)) ? $this->projekt : '0').",
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

    $sql = "DELETE FROM stundensatz WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->satz="";
    $this->typ="";
    $this->projekt="";
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
  function SetSatz($value) { $this->satz=$value; }
  function GetSatz() { return $this->satz; }
  function SetTyp($value) { $this->typ=$value; }
  function GetTyp() { return $this->typ; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }

}

?>