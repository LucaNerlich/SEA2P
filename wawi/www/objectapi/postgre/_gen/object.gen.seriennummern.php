<?php

class ObjGenSeriennummern
{

  private  $id;
  private  $seriennummer;
  private  $adresse;
  private  $artikel;
  private  $beschreibung;
  private  $lieferung;
  private  $lieferschein;
  private  $bearbeiter;
  private  $logdatei;

  public $app;            //application object 

  public function ObjGenSeriennummern($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM seriennummern WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->seriennummer=$result[seriennummer];
    $this->adresse=$result[adresse];
    $this->artikel=$result[artikel];
    $this->beschreibung=$result[beschreibung];
    $this->lieferung=$result[lieferung];
    $this->lieferschein=$result[lieferschein];
    $this->bearbeiter=$result[bearbeiter];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO seriennummern (id,seriennummer,adresse,artikel,beschreibung,lieferung,lieferschein,bearbeiter,logdatei)
      VALUES(DEFAULT, '{$this->seriennummer}', ".((is_numeric($this->adresse)) ? $this->adresse : '0').", ".((is_numeric($this->artikel)) ? $this->artikel : '0').", '{$this->beschreibung}', ".(($this->lieferung=='' || $this->lieferung=='--') ? 'NOW()' : "'".$this->lieferung."'").", ".((is_numeric($this->lieferschein)) ? $this->lieferschein : '0').", '{$this->bearbeiter}', ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE seriennummern SET
      seriennummer='{$this->seriennummer}',
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      artikel=".((is_numeric($this->artikel)) ? $this->artikel : '0').",
      beschreibung='{$this->beschreibung}',
      lieferung=".(($this->lieferung=='' || $this->lieferung=='--') ? 'NOW()' : "'".$this->lieferung."'").",
      lieferschein=".((is_numeric($this->lieferschein)) ? $this->lieferschein : '0').",
      bearbeiter='{$this->bearbeiter}',
      logdatei=".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'")."
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

    $sql = "DELETE FROM seriennummern WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->seriennummer="";
    $this->adresse="";
    $this->artikel="";
    $this->beschreibung="";
    $this->lieferung="";
    $this->lieferschein="";
    $this->bearbeiter="";
    $this->logdatei="";
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
  function SetSeriennummer($value) { $this->seriennummer=$value; }
  function GetSeriennummer() { return $this->seriennummer; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetLieferung($value) { $this->lieferung=$value; }
  function GetLieferung() { return $this->lieferung; }
  function SetLieferschein($value) { $this->lieferschein=$value; }
  function GetLieferschein() { return $this->lieferschein; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>