<?php

class ObjGenKostenstelle_Buchung
{

  private  $id;
  private  $kostenstelle;
  private  $bearbeiter;
  private  $datum;
  private  $buchungstext;
  private  $sonstiges;

  public $app;            //application object 

  public function ObjGenKostenstelle_Buchung($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM kostenstelle_buchung WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->kostenstelle=$result[kostenstelle];
    $this->bearbeiter=$result[bearbeiter];
    $this->datum=$result[datum];
    $this->buchungstext=$result[buchungstext];
    $this->sonstiges=$result[sonstiges];
  }

  public function Create()
  {
    $sql = "INSERT INTO kostenstelle_buchung (id,kostenstelle,bearbeiter,datum,buchungstext,sonstiges)
      VALUES('','{$this->kostenstelle}','{$this->bearbeiter}','{$this->datum}','{$this->buchungstext}','{$this->sonstiges}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE kostenstelle_buchung SET
      kostenstelle='{$this->kostenstelle}',
      bearbeiter='{$this->bearbeiter}',
      datum='{$this->datum}',
      buchungstext='{$this->buchungstext}',
      sonstiges='{$this->sonstiges}'
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

    $sql = "DELETE FROM kostenstelle_buchung WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->kostenstelle="";
    $this->bearbeiter="";
    $this->datum="";
    $this->buchungstext="";
    $this->sonstiges="";
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
  function SetKostenstelle($value) { $this->kostenstelle=$value; }
  function GetKostenstelle() { return $this->kostenstelle; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetBuchungstext($value) { $this->buchungstext=$value; }
  function GetBuchungstext() { return $this->buchungstext; }
  function SetSonstiges($value) { $this->sonstiges=$value; }
  function GetSonstiges() { return $this->sonstiges; }

}

?>