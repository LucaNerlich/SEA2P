<?php

class ObjGenAdresse_Rolle
{

  private  $id;
  private  $adresse;
  private  $projekt;
  private  $subjekt;
  private  $praedikat;
  private  $objekt;
  private  $parameter;
  private  $von;
  private  $bis;

  public $app;            //application object 

  public function ObjGenAdresse_Rolle($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM adresse_rolle WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->projekt=$result[projekt];
    $this->subjekt=$result[subjekt];
    $this->praedikat=$result[praedikat];
    $this->objekt=$result[objekt];
    $this->parameter=$result[parameter];
    $this->von=$result[von];
    $this->bis=$result[bis];
  }

  public function Create()
  {
    $sql = "INSERT INTO adresse_rolle (id,adresse,projekt,subjekt,praedikat,objekt,parameter,von,bis)
      VALUES(DEFAULT, ".((is_numeric($this->adresse)) ? $this->adresse : '0').", ".((is_numeric($this->projekt)) ? $this->projekt : '0').", '{$this->subjekt}', '{$this->praedikat}', '{$this->objekt}', '{$this->parameter}', ".(($this->von=='' || $this->von=='--') ? 'NOW()' : "'".$this->von."'").", ".(($this->bis=='' || $this->bis=='--') ? 'NOW()' : "'".$this->bis."'").")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE adresse_rolle SET
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      projekt=".((is_numeric($this->projekt)) ? $this->projekt : '0').",
      subjekt='{$this->subjekt}',
      praedikat='{$this->praedikat}',
      objekt='{$this->objekt}',
      parameter='{$this->parameter}',
      von=".(($this->von=='' || $this->von=='--') ? 'NOW()' : "'".$this->von."'").",
      bis=".(($this->bis=='' || $this->bis=='--') ? 'NOW()' : "'".$this->bis."'")."
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

    $sql = "DELETE FROM adresse_rolle WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->projekt="";
    $this->subjekt="";
    $this->praedikat="";
    $this->objekt="";
    $this->parameter="";
    $this->von="";
    $this->bis="";
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
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetSubjekt($value) { $this->subjekt=$value; }
  function GetSubjekt() { return $this->subjekt; }
  function SetPraedikat($value) { $this->praedikat=$value; }
  function GetPraedikat() { return $this->praedikat; }
  function SetObjekt($value) { $this->objekt=$value; }
  function GetObjekt() { return $this->objekt; }
  function SetParameter($value) { $this->parameter=$value; }
  function GetParameter() { return $this->parameter; }
  function SetVon($value) { $this->von=$value; }
  function GetVon() { return $this->von; }
  function SetBis($value) { $this->bis=$value; }
  function GetBis() { return $this->bis; }

}

?>