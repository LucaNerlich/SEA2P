<?php

class ObjGenProjekt_Inventar
{

  private  $id;
  private  $artikel;
  private  $menge;
  private  $bestellung;
  private  $projekt;
  private  $adresse;
  private  $mitarbeiter;
  private  $vpe;
  private  $zeit;
  private  $logdatei;

  public $app;            //application object 

  public function ObjGenProjekt_Inventar($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM projekt_inventar WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->artikel=$result[artikel];
    $this->menge=$result[menge];
    $this->bestellung=$result[bestellung];
    $this->projekt=$result[projekt];
    $this->adresse=$result[adresse];
    $this->mitarbeiter=$result[mitarbeiter];
    $this->vpe=$result[vpe];
    $this->zeit=$result[zeit];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO projekt_inventar (id,artikel,menge,bestellung,projekt,adresse,mitarbeiter,vpe,zeit,logdatei)
      VALUES(DEFAULT, ".((is_numeric($this->artikel)) ? $this->artikel : '0').", ".((is_numeric($this->menge)) ? $this->menge : '0').", ".((is_numeric($this->bestellung)) ? $this->bestellung : '0').", ".((is_numeric($this->projekt)) ? $this->projekt : '0').", ".((is_numeric($this->adresse)) ? $this->adresse : '0').", '{$this->mitarbeiter}', '{$this->vpe}', ".(($this->zeit=='' || $this->zeit=='--') ? 'NOW()' : "'".$this->zeit."'").", ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE projekt_inventar SET
      artikel=".((is_numeric($this->artikel)) ? $this->artikel : '0').",
      menge=".((is_numeric($this->menge)) ? $this->menge : '0').",
      bestellung=".((is_numeric($this->bestellung)) ? $this->bestellung : '0').",
      projekt=".((is_numeric($this->projekt)) ? $this->projekt : '0').",
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      mitarbeiter='{$this->mitarbeiter}',
      vpe='{$this->vpe}',
      zeit=".(($this->zeit=='' || $this->zeit=='--') ? 'NOW()' : "'".$this->zeit."'").",
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

    $sql = "DELETE FROM projekt_inventar WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->artikel="";
    $this->menge="";
    $this->bestellung="";
    $this->projekt="";
    $this->adresse="";
    $this->mitarbeiter="";
    $this->vpe="";
    $this->zeit="";
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
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetBestellung($value) { $this->bestellung=$value; }
  function GetBestellung() { return $this->bestellung; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetMitarbeiter($value) { $this->mitarbeiter=$value; }
  function GetMitarbeiter() { return $this->mitarbeiter; }
  function SetVpe($value) { $this->vpe=$value; }
  function GetVpe() { return $this->vpe; }
  function SetZeit($value) { $this->zeit=$value; }
  function GetZeit() { return $this->zeit; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>