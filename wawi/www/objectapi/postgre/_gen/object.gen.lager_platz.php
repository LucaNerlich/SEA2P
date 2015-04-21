<?php

class ObjGenLager_Platz
{

  private  $id;
  private  $lager;
  private  $kurzbezeichnung;
  private  $bemerkung;
  private  $projekt;
  private  $firma;
  private  $geloescht;
  private  $logdatei;
  private  $autolagersperre;

  public $app;            //application object 

  public function ObjGenLager_Platz($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM lager_platz WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->lager=$result[lager];
    $this->kurzbezeichnung=$result[kurzbezeichnung];
    $this->bemerkung=$result[bemerkung];
    $this->projekt=$result[projekt];
    $this->firma=$result[firma];
    $this->geloescht=$result[geloescht];
    $this->logdatei=$result[logdatei];
    $this->autolagersperre=$result[autolagersperre];
  }

  public function Create()
  {
    $sql = "INSERT INTO lager_platz (id,lager,kurzbezeichnung,bemerkung,projekt,firma,geloescht,logdatei,autolagersperre)
      VALUES(DEFAULT, ".((is_numeric($this->lager)) ? $this->lager : '0').", '{$this->kurzbezeichnung}', '{$this->bemerkung}', ".((is_numeric($this->projekt)) ? $this->projekt : '0').", ".((is_numeric($this->firma)) ? $this->firma : '0').", ".((is_numeric($this->geloescht)) ? $this->geloescht : '0').", ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").", ".((is_numeric($this->autolagersperre)) ? $this->autolagersperre : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE lager_platz SET
      lager=".((is_numeric($this->lager)) ? $this->lager : '0').",
      kurzbezeichnung='{$this->kurzbezeichnung}',
      bemerkung='{$this->bemerkung}',
      projekt=".((is_numeric($this->projekt)) ? $this->projekt : '0').",
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
      geloescht=".((is_numeric($this->geloescht)) ? $this->geloescht : '0').",
      logdatei=".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").",
      autolagersperre=".((is_numeric($this->autolagersperre)) ? $this->autolagersperre : '0')."
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

    $sql = "DELETE FROM lager_platz WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->lager="";
    $this->kurzbezeichnung="";
    $this->bemerkung="";
    $this->projekt="";
    $this->firma="";
    $this->geloescht="";
    $this->logdatei="";
    $this->autolagersperre="";
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
  function SetLager($value) { $this->lager=$value; }
  function GetLager() { return $this->lager; }
  function SetKurzbezeichnung($value) { $this->kurzbezeichnung=$value; }
  function GetKurzbezeichnung() { return $this->kurzbezeichnung; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetAutolagersperre($value) { $this->autolagersperre=$value; }
  function GetAutolagersperre() { return $this->autolagersperre; }

}

?>