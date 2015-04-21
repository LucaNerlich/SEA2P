<?php

class ObjGenChargenverwaltung
{

  private  $id;
  private  $artikel;
  private  $bestellung;
  private  $menge;
  private  $vpe;
  private  $zeit;
  private  $bearbeiter;
  private  $logdatei;

  public $app;            //application object 

  public function ObjGenChargenverwaltung($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM chargenverwaltung WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->artikel=$result[artikel];
    $this->bestellung=$result[bestellung];
    $this->menge=$result[menge];
    $this->vpe=$result[vpe];
    $this->zeit=$result[zeit];
    $this->bearbeiter=$result[bearbeiter];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO chargenverwaltung (id,artikel,bestellung,menge,vpe,zeit,bearbeiter,logdatei)
      VALUES(DEFAULT, ".((is_numeric($this->artikel)) ? $this->artikel : '0').", ".((is_numeric($this->bestellung)) ? $this->bestellung : '0').", ".((is_numeric($this->menge)) ? $this->menge : '0').", '{$this->vpe}', ".(($this->zeit=='' || $this->zeit=='--') ? 'NOW()' : "'".$this->zeit."'").", '{$this->bearbeiter}', ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE chargenverwaltung SET
      artikel=".((is_numeric($this->artikel)) ? $this->artikel : '0').",
      bestellung=".((is_numeric($this->bestellung)) ? $this->bestellung : '0').",
      menge=".((is_numeric($this->menge)) ? $this->menge : '0').",
      vpe='{$this->vpe}',
      zeit=".(($this->zeit=='' || $this->zeit=='--') ? 'NOW()' : "'".$this->zeit."'").",
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

    $sql = "DELETE FROM chargenverwaltung WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->artikel="";
    $this->bestellung="";
    $this->menge="";
    $this->vpe="";
    $this->zeit="";
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
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetBestellung($value) { $this->bestellung=$value; }
  function GetBestellung() { return $this->bestellung; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetVpe($value) { $this->vpe=$value; }
  function GetVpe() { return $this->vpe; }
  function SetZeit($value) { $this->zeit=$value; }
  function GetZeit() { return $this->zeit; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>