<?php

class ObjGenDatev_Buchungen
{

  private  $id;
  private  $wkz;
  private  $umsatz;
  private  $gegenkonto;
  private  $belegfeld1;
  private  $belegfeld2;
  private  $datum;
  private  $konto;
  private  $haben;
  private  $kost1;
  private  $kost2;
  private  $kostmenge;
  private  $skonto;
  private  $buchungstext;
  private  $bearbeiter;
  private  $exportiert;
  private  $firma;
  private  $parent;
  private  $kontoauszug;

  public $app;            //application object 

  public function ObjGenDatev_Buchungen($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM datev_buchungen WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->wkz=$result[wkz];
    $this->umsatz=$result[umsatz];
    $this->gegenkonto=$result[gegenkonto];
    $this->belegfeld1=$result[belegfeld1];
    $this->belegfeld2=$result[belegfeld2];
    $this->datum=$result[datum];
    $this->konto=$result[konto];
    $this->haben=$result[haben];
    $this->kost1=$result[kost1];
    $this->kost2=$result[kost2];
    $this->kostmenge=$result[kostmenge];
    $this->skonto=$result[skonto];
    $this->buchungstext=$result[buchungstext];
    $this->bearbeiter=$result[bearbeiter];
    $this->exportiert=$result[exportiert];
    $this->firma=$result[firma];
    $this->parent=$result[parent];
    $this->kontoauszug=$result[kontoauszug];
  }

  public function Create()
  {
    $sql = "INSERT INTO datev_buchungen (id,wkz,umsatz,gegenkonto,belegfeld1,belegfeld2,datum,konto,haben,kost1,kost2,kostmenge,skonto,buchungstext,bearbeiter,exportiert,firma,parent,kontoauszug)
      VALUES(DEFAULT, '{$this->wkz}', ".((is_numeric($this->umsatz)) ? $this->umsatz : '0').", ".((is_numeric($this->gegenkonto)) ? $this->gegenkonto : '0').", '{$this->belegfeld1}', '{$this->belegfeld2}', ".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'").", '{$this->konto}', ".((is_numeric($this->haben)) ? $this->haben : '0').", '{$this->kost1}', '{$this->kost2}', '{$this->kostmenge}', ".((is_numeric($this->skonto)) ? $this->skonto : '0').", '{$this->buchungstext}', '{$this->bearbeiter}', ".((is_numeric($this->exportiert)) ? $this->exportiert : '0').", ".((is_numeric($this->firma)) ? $this->firma : '0').", ".((is_numeric($this->parent)) ? $this->parent : '0').", ".((is_numeric($this->kontoauszug)) ? $this->kontoauszug : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE datev_buchungen SET
      wkz='{$this->wkz}',
      umsatz=".((is_numeric($this->umsatz)) ? $this->umsatz : '0').",
      gegenkonto=".((is_numeric($this->gegenkonto)) ? $this->gegenkonto : '0').",
      belegfeld1='{$this->belegfeld1}',
      belegfeld2='{$this->belegfeld2}',
      datum=".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'").",
      konto='{$this->konto}',
      haben=".((is_numeric($this->haben)) ? $this->haben : '0').",
      kost1='{$this->kost1}',
      kost2='{$this->kost2}',
      kostmenge='{$this->kostmenge}',
      skonto=".((is_numeric($this->skonto)) ? $this->skonto : '0').",
      buchungstext='{$this->buchungstext}',
      bearbeiter='{$this->bearbeiter}',
      exportiert=".((is_numeric($this->exportiert)) ? $this->exportiert : '0').",
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
      parent=".((is_numeric($this->parent)) ? $this->parent : '0').",
      kontoauszug=".((is_numeric($this->kontoauszug)) ? $this->kontoauszug : '0')."
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

    $sql = "DELETE FROM datev_buchungen WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->wkz="";
    $this->umsatz="";
    $this->gegenkonto="";
    $this->belegfeld1="";
    $this->belegfeld2="";
    $this->datum="";
    $this->konto="";
    $this->haben="";
    $this->kost1="";
    $this->kost2="";
    $this->kostmenge="";
    $this->skonto="";
    $this->buchungstext="";
    $this->bearbeiter="";
    $this->exportiert="";
    $this->firma="";
    $this->parent="";
    $this->kontoauszug="";
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
  function SetWkz($value) { $this->wkz=$value; }
  function GetWkz() { return $this->wkz; }
  function SetUmsatz($value) { $this->umsatz=$value; }
  function GetUmsatz() { return $this->umsatz; }
  function SetGegenkonto($value) { $this->gegenkonto=$value; }
  function GetGegenkonto() { return $this->gegenkonto; }
  function SetBelegfeld1($value) { $this->belegfeld1=$value; }
  function GetBelegfeld1() { return $this->belegfeld1; }
  function SetBelegfeld2($value) { $this->belegfeld2=$value; }
  function GetBelegfeld2() { return $this->belegfeld2; }
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetKonto($value) { $this->konto=$value; }
  function GetKonto() { return $this->konto; }
  function SetHaben($value) { $this->haben=$value; }
  function GetHaben() { return $this->haben; }
  function SetKost1($value) { $this->kost1=$value; }
  function GetKost1() { return $this->kost1; }
  function SetKost2($value) { $this->kost2=$value; }
  function GetKost2() { return $this->kost2; }
  function SetKostmenge($value) { $this->kostmenge=$value; }
  function GetKostmenge() { return $this->kostmenge; }
  function SetSkonto($value) { $this->skonto=$value; }
  function GetSkonto() { return $this->skonto; }
  function SetBuchungstext($value) { $this->buchungstext=$value; }
  function GetBuchungstext() { return $this->buchungstext; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetExportiert($value) { $this->exportiert=$value; }
  function GetExportiert() { return $this->exportiert; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetParent($value) { $this->parent=$value; }
  function GetParent() { return $this->parent; }
  function SetKontoauszug($value) { $this->kontoauszug=$value; }
  function GetKontoauszug() { return $this->kontoauszug; }

}

?>