<?php

class ObjGenShopimport_Auftraege
{

  private  $id;
  private  $extid;
  private  $sessionid;
  private  $warenkorb;
  private  $imported;
  private  $trash;
  private  $projekt;
  private  $bearbeiter;
  private  $logdatei;

  public $app;            //application object 

  public function ObjGenShopimport_Auftraege($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM shopimport_auftraege WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->extid=$result[extid];
    $this->sessionid=$result[sessionid];
    $this->warenkorb=$result[warenkorb];
    $this->imported=$result[imported];
    $this->trash=$result[trash];
    $this->projekt=$result[projekt];
    $this->bearbeiter=$result[bearbeiter];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO shopimport_auftraege (id,extid,sessionid,warenkorb,imported,trash,projekt,bearbeiter,logdatei)
      VALUES(DEFAULT, ".((is_numeric($this->extid)) ? $this->extid : '0').", '{$this->sessionid}', '{$this->warenkorb}', ".((is_numeric($this->imported)) ? $this->imported : '0').", ".((is_numeric($this->trash)) ? $this->trash : '0').", ".((is_numeric($this->projekt)) ? $this->projekt : '0').", '{$this->bearbeiter}', ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE shopimport_auftraege SET
      extid=".((is_numeric($this->extid)) ? $this->extid : '0').",
      sessionid='{$this->sessionid}',
      warenkorb='{$this->warenkorb}',
      imported=".((is_numeric($this->imported)) ? $this->imported : '0').",
      trash=".((is_numeric($this->trash)) ? $this->trash : '0').",
      projekt=".((is_numeric($this->projekt)) ? $this->projekt : '0').",
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

    $sql = "DELETE FROM shopimport_auftraege WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->extid="";
    $this->sessionid="";
    $this->warenkorb="";
    $this->imported="";
    $this->trash="";
    $this->projekt="";
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
  function SetExtid($value) { $this->extid=$value; }
  function GetExtid() { return $this->extid; }
  function SetSessionid($value) { $this->sessionid=$value; }
  function GetSessionid() { return $this->sessionid; }
  function SetWarenkorb($value) { $this->warenkorb=$value; }
  function GetWarenkorb() { return $this->warenkorb; }
  function SetImported($value) { $this->imported=$value; }
  function GetImported() { return $this->imported; }
  function SetTrash($value) { $this->trash=$value; }
  function GetTrash() { return $this->trash; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>