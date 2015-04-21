<?php

class ObjGenArtikelkategorien
{

  private  $id;
  private  $bezeichnung;
  private  $next_nummer;
  private  $projekt;
  private  $geloescht;
  private  $externenummer;

  public $app;            //application object 

  public function ObjGenArtikelkategorien($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM artikelkategorien WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->bezeichnung=$result[bezeichnung];
    $this->next_nummer=$result[next_nummer];
    $this->projekt=$result[projekt];
    $this->geloescht=$result[geloescht];
    $this->externenummer=$result[externenummer];
  }

  public function Create()
  {
    $sql = "INSERT INTO artikelkategorien (id,bezeichnung,next_nummer,projekt,geloescht,externenummer)
      VALUES('','{$this->bezeichnung}','{$this->next_nummer}','{$this->projekt}','{$this->geloescht}','{$this->externenummer}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE artikelkategorien SET
      bezeichnung='{$this->bezeichnung}',
      next_nummer='{$this->next_nummer}',
      projekt='{$this->projekt}',
      geloescht='{$this->geloescht}',
      externenummer='{$this->externenummer}'
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

    $sql = "DELETE FROM artikelkategorien WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->bezeichnung="";
    $this->next_nummer="";
    $this->projekt="";
    $this->geloescht="";
    $this->externenummer="";
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
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetNext_Nummer($value) { $this->next_nummer=$value; }
  function GetNext_Nummer() { return $this->next_nummer; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetExternenummer($value) { $this->externenummer=$value; }
  function GetExternenummer() { return $this->externenummer; }

}

?>