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
  private  $verbrauchslager;
  private  $sperrlager;

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
    $this->verbrauchslager=$result[verbrauchslager];
    $this->sperrlager=$result[sperrlager];
  }

  public function Create()
  {
    $sql = "INSERT INTO lager_platz (id,lager,kurzbezeichnung,bemerkung,projekt,firma,geloescht,logdatei,autolagersperre,verbrauchslager,sperrlager)
      VALUES('','{$this->lager}','{$this->kurzbezeichnung}','{$this->bemerkung}','{$this->projekt}','{$this->firma}','{$this->geloescht}','{$this->logdatei}','{$this->autolagersperre}','{$this->verbrauchslager}','{$this->sperrlager}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE lager_platz SET
      lager='{$this->lager}',
      kurzbezeichnung='{$this->kurzbezeichnung}',
      bemerkung='{$this->bemerkung}',
      projekt='{$this->projekt}',
      firma='{$this->firma}',
      geloescht='{$this->geloescht}',
      logdatei='{$this->logdatei}',
      autolagersperre='{$this->autolagersperre}',
      verbrauchslager='{$this->verbrauchslager}',
      sperrlager='{$this->sperrlager}'
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
    $this->verbrauchslager="";
    $this->sperrlager="";
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
  function SetVerbrauchslager($value) { $this->verbrauchslager=$value; }
  function GetVerbrauchslager() { return $this->verbrauchslager; }
  function SetSperrlager($value) { $this->sperrlager=$value; }
  function GetSperrlager() { return $this->sperrlager; }

}

?>