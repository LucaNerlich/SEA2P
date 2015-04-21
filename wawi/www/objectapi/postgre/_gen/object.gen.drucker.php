<?php

class ObjGenDrucker
{

  private  $id;
  private  $name;
  private  $bezeichnung;
  private  $befehl;
  private  $aktiv;
  private  $firma;

  public $app;            //application object 

  public function ObjGenDrucker($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM drucker WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->name=$result[name];
    $this->bezeichnung=$result[bezeichnung];
    $this->befehl=$result[befehl];
    $this->aktiv=$result[aktiv];
    $this->firma=$result[firma];
  }

  public function Create()
  {
    $sql = "INSERT INTO drucker (id,name,bezeichnung,befehl,aktiv,firma)
      VALUES(DEFAULT, '{$this->name}', '{$this->bezeichnung}', '{$this->befehl}', ".((is_numeric($this->aktiv)) ? $this->aktiv : '0').", ".((is_numeric($this->firma)) ? $this->firma : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE drucker SET
      name='{$this->name}',
      bezeichnung='{$this->bezeichnung}',
      befehl='{$this->befehl}',
      aktiv=".((is_numeric($this->aktiv)) ? $this->aktiv : '0').",
      firma=".((is_numeric($this->firma)) ? $this->firma : '0')."
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

    $sql = "DELETE FROM drucker WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->name="";
    $this->bezeichnung="";
    $this->befehl="";
    $this->aktiv="";
    $this->firma="";
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
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetBefehl($value) { $this->befehl=$value; }
  function GetBefehl() { return $this->befehl; }
  function SetAktiv($value) { $this->aktiv=$value; }
  function GetAktiv() { return $this->aktiv; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }

}

?>