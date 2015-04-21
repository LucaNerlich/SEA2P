<?php

class ObjGenProduktionslager
{

  private  $id;
  private  $artikel;
  private  $menge;
  private  $bemerkung;
  private  $status;
  private  $bestellung_pos;
  private  $vpe;
  private  $projekt;
  private  $bearbeiter;
  private  $produzent;
  private  $firma;
  private  $logdatei;

  public $app;            //application object 

  public function ObjGenProduktionslager($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM produktionslager WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->artikel=$result[artikel];
    $this->menge=$result[menge];
    $this->bemerkung=$result[bemerkung];
    $this->status=$result[status];
    $this->bestellung_pos=$result[bestellung_pos];
    $this->vpe=$result[vpe];
    $this->projekt=$result[projekt];
    $this->bearbeiter=$result[bearbeiter];
    $this->produzent=$result[produzent];
    $this->firma=$result[firma];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO produktionslager (id,artikel,menge,bemerkung,status,bestellung_pos,vpe,projekt,bearbeiter,produzent,firma,logdatei)
      VALUES(DEFAULT, ".((is_numeric($this->artikel)) ? $this->artikel : '0').", ".((is_numeric($this->menge)) ? $this->menge : '0').", '{$this->bemerkung}', '{$this->status}', ".((is_numeric($this->bestellung_pos)) ? $this->bestellung_pos : '0').", '{$this->vpe}', ".((is_numeric($this->projekt)) ? $this->projekt : '0').", '{$this->bearbeiter}', '{$this->produzent}', ".((is_numeric($this->firma)) ? $this->firma : '0').", ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE produktionslager SET
      artikel=".((is_numeric($this->artikel)) ? $this->artikel : '0').",
      menge=".((is_numeric($this->menge)) ? $this->menge : '0').",
      bemerkung='{$this->bemerkung}',
      status='{$this->status}',
      bestellung_pos=".((is_numeric($this->bestellung_pos)) ? $this->bestellung_pos : '0').",
      vpe='{$this->vpe}',
      projekt=".((is_numeric($this->projekt)) ? $this->projekt : '0').",
      bearbeiter='{$this->bearbeiter}',
      produzent='{$this->produzent}',
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
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

    $sql = "DELETE FROM produktionslager WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->artikel="";
    $this->menge="";
    $this->bemerkung="";
    $this->status="";
    $this->bestellung_pos="";
    $this->vpe="";
    $this->projekt="";
    $this->bearbeiter="";
    $this->produzent="";
    $this->firma="";
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
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetBestellung_Pos($value) { $this->bestellung_pos=$value; }
  function GetBestellung_Pos() { return $this->bestellung_pos; }
  function SetVpe($value) { $this->vpe=$value; }
  function GetVpe() { return $this->vpe; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetProduzent($value) { $this->produzent=$value; }
  function GetProduzent() { return $this->produzent; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>