<?php

class ObjGenLieferantvorlage
{

  private  $id;
  private  $adresse;
  private  $kundennummer;
  private  $zahlungsweise;
  private  $zahlungszieltage;
  private  $zahlungszieltageskonto;
  private  $zahlungszielskonto;
  private  $versandart;
  private  $logdatei;

  public $app;            //application object 

  public function ObjGenLieferantvorlage($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM lieferantvorlage WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->kundennummer=$result[kundennummer];
    $this->zahlungsweise=$result[zahlungsweise];
    $this->zahlungszieltage=$result[zahlungszieltage];
    $this->zahlungszieltageskonto=$result[zahlungszieltageskonto];
    $this->zahlungszielskonto=$result[zahlungszielskonto];
    $this->versandart=$result[versandart];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO lieferantvorlage (id,adresse,kundennummer,zahlungsweise,zahlungszieltage,zahlungszieltageskonto,zahlungszielskonto,versandart,logdatei)
      VALUES(DEFAULT, ".((is_numeric($this->adresse)) ? $this->adresse : '0').", '{$this->kundennummer}', '{$this->zahlungsweise}', ".((is_numeric($this->zahlungszieltage)) ? $this->zahlungszieltage : '0').", ".((is_numeric($this->zahlungszieltageskonto)) ? $this->zahlungszieltageskonto : '0').", ".((is_numeric($this->zahlungszielskonto)) ? $this->zahlungszielskonto : '0').", '{$this->versandart}', ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE lieferantvorlage SET
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      kundennummer='{$this->kundennummer}',
      zahlungsweise='{$this->zahlungsweise}',
      zahlungszieltage=".((is_numeric($this->zahlungszieltage)) ? $this->zahlungszieltage : '0').",
      zahlungszieltageskonto=".((is_numeric($this->zahlungszieltageskonto)) ? $this->zahlungszieltageskonto : '0').",
      zahlungszielskonto=".((is_numeric($this->zahlungszielskonto)) ? $this->zahlungszielskonto : '0').",
      versandart='{$this->versandart}',
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

    $sql = "DELETE FROM lieferantvorlage WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->kundennummer="";
    $this->zahlungsweise="";
    $this->zahlungszieltage="";
    $this->zahlungszieltageskonto="";
    $this->zahlungszielskonto="";
    $this->versandart="";
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
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetKundennummer($value) { $this->kundennummer=$value; }
  function GetKundennummer() { return $this->kundennummer; }
  function SetZahlungsweise($value) { $this->zahlungsweise=$value; }
  function GetZahlungsweise() { return $this->zahlungsweise; }
  function SetZahlungszieltage($value) { $this->zahlungszieltage=$value; }
  function GetZahlungszieltage() { return $this->zahlungszieltage; }
  function SetZahlungszieltageskonto($value) { $this->zahlungszieltageskonto=$value; }
  function GetZahlungszieltageskonto() { return $this->zahlungszieltageskonto; }
  function SetZahlungszielskonto($value) { $this->zahlungszielskonto=$value; }
  function GetZahlungszielskonto() { return $this->zahlungszielskonto; }
  function SetVersandart($value) { $this->versandart=$value; }
  function GetVersandart() { return $this->versandart; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>