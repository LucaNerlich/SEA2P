<?php

class ObjGenKontoauszuege_Zahlungsausgang
{

  private  $id;
  private  $adresse;
  private  $bearbeiter;
  private  $betrag;
  private  $datum;
  private  $objekt;
  private  $parameter;
  private  $kontoauszuege;
  private  $firma;
  private  $abgeschlossen;

  public $app;            //application object 

  public function ObjGenKontoauszuege_Zahlungsausgang($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM kontoauszuege_zahlungsausgang WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->bearbeiter=$result[bearbeiter];
    $this->betrag=$result[betrag];
    $this->datum=$result[datum];
    $this->objekt=$result[objekt];
    $this->parameter=$result[parameter];
    $this->kontoauszuege=$result[kontoauszuege];
    $this->firma=$result[firma];
    $this->abgeschlossen=$result[abgeschlossen];
  }

  public function Create()
  {
    $sql = "INSERT INTO kontoauszuege_zahlungsausgang (id,adresse,bearbeiter,betrag,datum,objekt,parameter,kontoauszuege,firma,abgeschlossen)
      VALUES(DEFAULT, ".((is_numeric($this->adresse)) ? $this->adresse : '0').", '{$this->bearbeiter}', ".((is_numeric($this->betrag)) ? $this->betrag : '0').", ".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'").", '{$this->objekt}', ".((is_numeric($this->parameter)) ? $this->parameter : '0').", ".((is_numeric($this->kontoauszuege)) ? $this->kontoauszuege : '0').", ".((is_numeric($this->firma)) ? $this->firma : '0').", ".((is_numeric($this->abgeschlossen)) ? $this->abgeschlossen : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE kontoauszuege_zahlungsausgang SET
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      bearbeiter='{$this->bearbeiter}',
      betrag=".((is_numeric($this->betrag)) ? $this->betrag : '0').",
      datum=".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'").",
      objekt='{$this->objekt}',
      parameter=".((is_numeric($this->parameter)) ? $this->parameter : '0').",
      kontoauszuege=".((is_numeric($this->kontoauszuege)) ? $this->kontoauszuege : '0').",
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
      abgeschlossen=".((is_numeric($this->abgeschlossen)) ? $this->abgeschlossen : '0')."
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

    $sql = "DELETE FROM kontoauszuege_zahlungsausgang WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->bearbeiter="";
    $this->betrag="";
    $this->datum="";
    $this->objekt="";
    $this->parameter="";
    $this->kontoauszuege="";
    $this->firma="";
    $this->abgeschlossen="";
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
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetBetrag($value) { $this->betrag=$value; }
  function GetBetrag() { return $this->betrag; }
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetObjekt($value) { $this->objekt=$value; }
  function GetObjekt() { return $this->objekt; }
  function SetParameter($value) { $this->parameter=$value; }
  function GetParameter() { return $this->parameter; }
  function SetKontoauszuege($value) { $this->kontoauszuege=$value; }
  function GetKontoauszuege() { return $this->kontoauszuege; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetAbgeschlossen($value) { $this->abgeschlossen=$value; }
  function GetAbgeschlossen() { return $this->abgeschlossen; }

}

?>