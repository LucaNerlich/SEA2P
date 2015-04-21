<?php

class ObjGenDrucker
{

  private  $id;
  private  $name;
  private  $bezeichnung;
  private  $befehl;
  private  $aktiv;
  private  $firma;
  private  $tomail;
  private  $tomailtext;
  private  $tomailsubject;
  private  $adapterboxip;
  private  $adapterboxpasswort;
  private  $anbindung;
  private  $faxserver;
  private  $art;
  private  $adapterboxseriennummer;

  public $app;            //application object 

  public function ObjGenDrucker($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM drucker WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->name=$result[name];
    $this->bezeichnung=$result[bezeichnung];
    $this->befehl=$result[befehl];
    $this->aktiv=$result[aktiv];
    $this->firma=$result[firma];
    $this->tomail=$result[tomail];
    $this->tomailtext=$result[tomailtext];
    $this->tomailsubject=$result[tomailsubject];
    $this->adapterboxip=$result[adapterboxip];
    $this->adapterboxpasswort=$result[adapterboxpasswort];
    $this->anbindung=$result[anbindung];
    $this->faxserver=$result[faxserver];
    $this->art=$result[art];
    $this->adapterboxseriennummer=$result[adapterboxseriennummer];
  }

  public function Create()
  {
    $sql = "INSERT INTO drucker (id,name,bezeichnung,befehl,aktiv,firma,tomail,tomailtext,tomailsubject,adapterboxip,adapterboxpasswort,anbindung,faxserver,art,adapterboxseriennummer)
      VALUES('','{$this->name}','{$this->bezeichnung}','{$this->befehl}','{$this->aktiv}','{$this->firma}','{$this->tomail}','{$this->tomailtext}','{$this->tomailsubject}','{$this->adapterboxip}','{$this->adapterboxpasswort}','{$this->anbindung}','{$this->faxserver}','{$this->art}','{$this->adapterboxseriennummer}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE drucker SET
      name='{$this->name}',
      bezeichnung='{$this->bezeichnung}',
      befehl='{$this->befehl}',
      aktiv='{$this->aktiv}',
      firma='{$this->firma}',
      tomail='{$this->tomail}',
      tomailtext='{$this->tomailtext}',
      tomailsubject='{$this->tomailsubject}',
      adapterboxip='{$this->adapterboxip}',
      adapterboxpasswort='{$this->adapterboxpasswort}',
      anbindung='{$this->anbindung}',
      faxserver='{$this->faxserver}',
      art='{$this->art}',
      adapterboxseriennummer='{$this->adapterboxseriennummer}'
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

    $sql = "DELETE FROM drucker WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->name="";
    $this->bezeichnung="";
    $this->befehl="";
    $this->aktiv="";
    $this->firma="";
    $this->tomail="";
    $this->tomailtext="";
    $this->tomailsubject="";
    $this->adapterboxip="";
    $this->adapterboxpasswort="";
    $this->anbindung="";
    $this->faxserver="";
    $this->art="";
    $this->adapterboxseriennummer="";
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
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetBefehl($value) { $this->befehl=$value; }
  function GetBefehl() { return $this->befehl; }
  function SetAktiv($value) { $this->aktiv=$value; }
  function GetAktiv() { return $this->aktiv; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetTomail($value) { $this->tomail=$value; }
  function GetTomail() { return $this->tomail; }
  function SetTomailtext($value) { $this->tomailtext=$value; }
  function GetTomailtext() { return $this->tomailtext; }
  function SetTomailsubject($value) { $this->tomailsubject=$value; }
  function GetTomailsubject() { return $this->tomailsubject; }
  function SetAdapterboxip($value) { $this->adapterboxip=$value; }
  function GetAdapterboxip() { return $this->adapterboxip; }
  function SetAdapterboxpasswort($value) { $this->adapterboxpasswort=$value; }
  function GetAdapterboxpasswort() { return $this->adapterboxpasswort; }
  function SetAnbindung($value) { $this->anbindung=$value; }
  function GetAnbindung() { return $this->anbindung; }
  function SetFaxserver($value) { $this->faxserver=$value; }
  function GetFaxserver() { return $this->faxserver; }
  function SetArt($value) { $this->art=$value; }
  function GetArt() { return $this->art; }
  function SetAdapterboxseriennummer($value) { $this->adapterboxseriennummer=$value; }
  function GetAdapterboxseriennummer() { return $this->adapterboxseriennummer; }

}

?>