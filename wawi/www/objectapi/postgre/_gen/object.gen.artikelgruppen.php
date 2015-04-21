<?php

class ObjGenArtikelgruppen
{

  private  $id;
  private  $bezeichnung;
  private  $bezeichnung_en;
  private  $shop;
  private  $aktiv;
  private  $beschreibung_de;
  private  $beschreibung_en;

  public $app;            //application object 

  public function ObjGenArtikelgruppen($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM artikelgruppen WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->bezeichnung=$result[bezeichnung];
    $this->bezeichnung_en=$result[bezeichnung_en];
    $this->shop=$result[shop];
    $this->aktiv=$result[aktiv];
    $this->beschreibung_de=$result[beschreibung_de];
    $this->beschreibung_en=$result[beschreibung_en];
  }

  public function Create()
  {
    $sql = "INSERT INTO artikelgruppen (id,bezeichnung,bezeichnung_en,shop,aktiv,beschreibung_de,beschreibung_en)
      VALUES(DEFAULT, '{$this->bezeichnung}', '{$this->bezeichnung_en}', ".((is_numeric($this->shop)) ? $this->shop : '0').", ".((is_numeric($this->aktiv)) ? $this->aktiv : '0').", '{$this->beschreibung_de}', '{$this->beschreibung_en}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE artikelgruppen SET
      bezeichnung='{$this->bezeichnung}',
      bezeichnung_en='{$this->bezeichnung_en}',
      shop=".((is_numeric($this->shop)) ? $this->shop : '0').",
      aktiv=".((is_numeric($this->aktiv)) ? $this->aktiv : '0').",
      beschreibung_de='{$this->beschreibung_de}',
      beschreibung_en='{$this->beschreibung_en}'
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

    $sql = "DELETE FROM artikelgruppen WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->bezeichnung="";
    $this->bezeichnung_en="";
    $this->shop="";
    $this->aktiv="";
    $this->beschreibung_de="";
    $this->beschreibung_en="";
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
  function SetBezeichnung_En($value) { $this->bezeichnung_en=$value; }
  function GetBezeichnung_En() { return $this->bezeichnung_en; }
  function SetShop($value) { $this->shop=$value; }
  function GetShop() { return $this->shop; }
  function SetAktiv($value) { $this->aktiv=$value; }
  function GetAktiv() { return $this->aktiv; }
  function SetBeschreibung_De($value) { $this->beschreibung_de=$value; }
  function GetBeschreibung_De() { return $this->beschreibung_de; }
  function SetBeschreibung_En($value) { $this->beschreibung_en=$value; }
  function GetBeschreibung_En() { return $this->beschreibung_en; }

}

?>