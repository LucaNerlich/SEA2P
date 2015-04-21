<?php

class ObjGenReisekosten_Protokoll
{

  private  $id;
  private  $reisekosten;
  private  $zeit;
  private  $bearbeiter;
  private  $grund;

  public $app;            //application object 

  public function ObjGenReisekosten_Protokoll($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM reisekosten_protokoll WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->reisekosten=$result[reisekosten];
    $this->zeit=$result[zeit];
    $this->bearbeiter=$result[bearbeiter];
    $this->grund=$result[grund];
  }

  public function Create()
  {
    $sql = "INSERT INTO reisekosten_protokoll (id,reisekosten,zeit,bearbeiter,grund)
      VALUES('','{$this->reisekosten}','{$this->zeit}','{$this->bearbeiter}','{$this->grund}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE reisekosten_protokoll SET
      reisekosten='{$this->reisekosten}',
      zeit='{$this->zeit}',
      bearbeiter='{$this->bearbeiter}',
      grund='{$this->grund}'
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

    $sql = "DELETE FROM reisekosten_protokoll WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->reisekosten="";
    $this->zeit="";
    $this->bearbeiter="";
    $this->grund="";
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
  function SetReisekosten($value) { $this->reisekosten=$value; }
  function GetReisekosten() { return $this->reisekosten; }
  function SetZeit($value) { $this->zeit=$value; }
  function GetZeit() { return $this->zeit; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetGrund($value) { $this->grund=$value; }
  function GetGrund() { return $this->grund; }

}

?>