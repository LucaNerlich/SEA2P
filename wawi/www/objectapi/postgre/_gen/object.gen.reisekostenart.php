<?php

class ObjGenReisekostenart
{

  private  $id;
  private  $nummer;
  private  $beschreibung;
  private  $internebemerkung;

  public $app;            //application object 

  public function ObjGenReisekostenart($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM reisekostenart WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->nummer=$result[nummer];
    $this->beschreibung=$result[beschreibung];
    $this->internebemerkung=$result[internebemerkung];
  }

  public function Create()
  {
    $sql = "INSERT INTO reisekostenart (id,nummer,beschreibung,internebemerkung)
      VALUES(DEFAULT, '{$this->nummer}', '{$this->beschreibung}', '{$this->internebemerkung}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE reisekostenart SET
      nummer='{$this->nummer}',
      beschreibung='{$this->beschreibung}',
      internebemerkung='{$this->internebemerkung}'
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

    $sql = "DELETE FROM reisekostenart WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->nummer="";
    $this->beschreibung="";
    $this->internebemerkung="";
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
  function SetNummer($value) { $this->nummer=$value; }
  function GetNummer() { return $this->nummer; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  function GetInternebemerkung() { return $this->internebemerkung; }

}

?>