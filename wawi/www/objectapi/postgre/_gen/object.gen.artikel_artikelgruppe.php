<?php

class ObjGenArtikel_Artikelgruppe
{

  private  $id;
  private  $artikel;
  private  $artikelgruppe;
  private  $position;
  private  $geloescht;

  public $app;            //application object 

  public function ObjGenArtikel_Artikelgruppe($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM artikel_artikelgruppe WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->artikel=$result[artikel];
    $this->artikelgruppe=$result[artikelgruppe];
    $this->position=$result[position];
    $this->geloescht=$result[geloescht];
  }

  public function Create()
  {
    $sql = "INSERT INTO artikel_artikelgruppe (id,artikel,artikelgruppe,position,geloescht)
      VALUES(DEFAULT, ".((is_numeric($this->artikel)) ? $this->artikel : '0').", ".((is_numeric($this->artikelgruppe)) ? $this->artikelgruppe : '0').", ".((is_numeric($this->position)) ? $this->position : '0').", ".((is_numeric($this->geloescht)) ? $this->geloescht : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE artikel_artikelgruppe SET
      artikel=".((is_numeric($this->artikel)) ? $this->artikel : '0').",
      artikelgruppe=".((is_numeric($this->artikelgruppe)) ? $this->artikelgruppe : '0').",
      position=".((is_numeric($this->position)) ? $this->position : '0').",
      geloescht=".((is_numeric($this->geloescht)) ? $this->geloescht : '0')."
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

    $sql = "DELETE FROM artikel_artikelgruppe WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->artikel="";
    $this->artikelgruppe="";
    $this->position="";
    $this->geloescht="";
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
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetArtikelgruppe($value) { $this->artikelgruppe=$value; }
  function GetArtikelgruppe() { return $this->artikelgruppe; }
  function SetPosition($value) { $this->position=$value; }
  function GetPosition() { return $this->position; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }

}

?>