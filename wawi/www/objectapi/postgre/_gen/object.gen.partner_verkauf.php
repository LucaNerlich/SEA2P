<?php

class ObjGenPartner_Verkauf
{

  private  $id;
  private  $auftrag;
  private  $artikel;
  private  $menge;
  private  $partner;
  private  $freigabe;
  private  $abgerechnet;

  public $app;            //application object 

  public function ObjGenPartner_Verkauf($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM partner_verkauf WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->auftrag=$result[auftrag];
    $this->artikel=$result[artikel];
    $this->menge=$result[menge];
    $this->partner=$result[partner];
    $this->freigabe=$result[freigabe];
    $this->abgerechnet=$result[abgerechnet];
  }

  public function Create()
  {
    $sql = "INSERT INTO partner_verkauf (id,auftrag,artikel,menge,partner,freigabe,abgerechnet)
      VALUES(DEFAULT, ".((is_numeric($this->auftrag)) ? $this->auftrag : '0').", ".((is_numeric($this->artikel)) ? $this->artikel : '0').", ".((is_numeric($this->menge)) ? $this->menge : '0').", ".((is_numeric($this->partner)) ? $this->partner : '0').", ".((is_numeric($this->freigabe)) ? $this->freigabe : '0').", ".((is_numeric($this->abgerechnet)) ? $this->abgerechnet : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE partner_verkauf SET
      auftrag=".((is_numeric($this->auftrag)) ? $this->auftrag : '0').",
      artikel=".((is_numeric($this->artikel)) ? $this->artikel : '0').",
      menge=".((is_numeric($this->menge)) ? $this->menge : '0').",
      partner=".((is_numeric($this->partner)) ? $this->partner : '0').",
      freigabe=".((is_numeric($this->freigabe)) ? $this->freigabe : '0').",
      abgerechnet=".((is_numeric($this->abgerechnet)) ? $this->abgerechnet : '0')."
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

    $sql = "DELETE FROM partner_verkauf WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->auftrag="";
    $this->artikel="";
    $this->menge="";
    $this->partner="";
    $this->freigabe="";
    $this->abgerechnet="";
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
  function SetAuftrag($value) { $this->auftrag=$value; }
  function GetAuftrag() { return $this->auftrag; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetPartner($value) { $this->partner=$value; }
  function GetPartner() { return $this->partner; }
  function SetFreigabe($value) { $this->freigabe=$value; }
  function GetFreigabe() { return $this->freigabe; }
  function SetAbgerechnet($value) { $this->abgerechnet=$value; }
  function GetAbgerechnet() { return $this->abgerechnet; }

}

?>