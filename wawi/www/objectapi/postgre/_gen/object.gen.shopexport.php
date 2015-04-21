<?php

class ObjGenShopexport
{

  private  $id;
  private  $bezeichnung;
  private  $typ;
  private  $url;
  private  $passwort;
  private  $token;
  private  $challenge;
  private  $projekt;
  private  $cms;
  private  $firma;
  private  $logdatei;
  private  $geloescht;

  public $app;            //application object 

  public function ObjGenShopexport($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM shopexport WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->bezeichnung=$result[bezeichnung];
    $this->typ=$result[typ];
    $this->url=$result[url];
    $this->passwort=$result[passwort];
    $this->token=$result[token];
    $this->challenge=$result[challenge];
    $this->projekt=$result[projekt];
    $this->cms=$result[cms];
    $this->firma=$result[firma];
    $this->logdatei=$result[logdatei];
    $this->geloescht=$result[geloescht];
  }

  public function Create()
  {
    $sql = "INSERT INTO shopexport (id,bezeichnung,typ,url,passwort,token,challenge,projekt,cms,firma,logdatei,geloescht)
      VALUES(DEFAULT, '{$this->bezeichnung}', '{$this->typ}', '{$this->url}', '{$this->passwort}', '{$this->token}', '{$this->challenge}', ".((is_numeric($this->projekt)) ? $this->projekt : '0').", ".((is_numeric($this->cms)) ? $this->cms : '0').", ".((is_numeric($this->firma)) ? $this->firma : '0').", ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").", ".((is_numeric($this->geloescht)) ? $this->geloescht : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE shopexport SET
      bezeichnung='{$this->bezeichnung}',
      typ='{$this->typ}',
      url='{$this->url}',
      passwort='{$this->passwort}',
      token='{$this->token}',
      challenge='{$this->challenge}',
      projekt=".((is_numeric($this->projekt)) ? $this->projekt : '0').",
      cms=".((is_numeric($this->cms)) ? $this->cms : '0').",
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
      logdatei=".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").",
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

    $sql = "DELETE FROM shopexport WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->bezeichnung="";
    $this->typ="";
    $this->url="";
    $this->passwort="";
    $this->token="";
    $this->challenge="";
    $this->projekt="";
    $this->cms="";
    $this->firma="";
    $this->logdatei="";
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
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetTyp($value) { $this->typ=$value; }
  function GetTyp() { return $this->typ; }
  function SetUrl($value) { $this->url=$value; }
  function GetUrl() { return $this->url; }
  function SetPasswort($value) { $this->passwort=$value; }
  function GetPasswort() { return $this->passwort; }
  function SetToken($value) { $this->token=$value; }
  function GetToken() { return $this->token; }
  function SetChallenge($value) { $this->challenge=$value; }
  function GetChallenge() { return $this->challenge; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetCms($value) { $this->cms=$value; }
  function GetCms() { return $this->cms; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }

}

?>