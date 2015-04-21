<?php

class ObjGenImportvorlage
{

  private  $id;
  private  $bezeichnung;
  private  $fields;
  private  $internebemerkung;
  private  $ziel;
  private  $letzterimport;
  private  $mitarbeiterletzterimport;
  private  $importtrennzeichen;
  private  $importerstezeilenummer;
  private  $importdatenmaskierung;
  private  $importzeichensatz;

  public $app;            //application object 

  public function ObjGenImportvorlage($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM importvorlage WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->bezeichnung=$result[bezeichnung];
    $this->fields=$result[fields];
    $this->internebemerkung=$result[internebemerkung];
    $this->ziel=$result[ziel];
    $this->letzterimport=$result[letzterimport];
    $this->mitarbeiterletzterimport=$result[mitarbeiterletzterimport];
    $this->importtrennzeichen=$result[importtrennzeichen];
    $this->importerstezeilenummer=$result[importerstezeilenummer];
    $this->importdatenmaskierung=$result[importdatenmaskierung];
    $this->importzeichensatz=$result[importzeichensatz];
  }

  public function Create()
  {
    $sql = "INSERT INTO importvorlage (id,bezeichnung,fields,internebemerkung,ziel,letzterimport,mitarbeiterletzterimport,importtrennzeichen,importerstezeilenummer,importdatenmaskierung,importzeichensatz)
      VALUES('','{$this->bezeichnung}','{$this->fields}','{$this->internebemerkung}','{$this->ziel}','{$this->letzterimport}','{$this->mitarbeiterletzterimport}','{$this->importtrennzeichen}','{$this->importerstezeilenummer}','{$this->importdatenmaskierung}','{$this->importzeichensatz}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE importvorlage SET
      bezeichnung='{$this->bezeichnung}',
      fields='{$this->fields}',
      internebemerkung='{$this->internebemerkung}',
      ziel='{$this->ziel}',
      letzterimport='{$this->letzterimport}',
      mitarbeiterletzterimport='{$this->mitarbeiterletzterimport}',
      importtrennzeichen='{$this->importtrennzeichen}',
      importerstezeilenummer='{$this->importerstezeilenummer}',
      importdatenmaskierung='{$this->importdatenmaskierung}',
      importzeichensatz='{$this->importzeichensatz}'
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

    $sql = "DELETE FROM importvorlage WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->bezeichnung="";
    $this->fields="";
    $this->internebemerkung="";
    $this->ziel="";
    $this->letzterimport="";
    $this->mitarbeiterletzterimport="";
    $this->importtrennzeichen="";
    $this->importerstezeilenummer="";
    $this->importdatenmaskierung="";
    $this->importzeichensatz="";
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
  function SetFields($value) { $this->fields=$value; }
  function GetFields() { return $this->fields; }
  function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  function GetInternebemerkung() { return $this->internebemerkung; }
  function SetZiel($value) { $this->ziel=$value; }
  function GetZiel() { return $this->ziel; }
  function SetLetzterimport($value) { $this->letzterimport=$value; }
  function GetLetzterimport() { return $this->letzterimport; }
  function SetMitarbeiterletzterimport($value) { $this->mitarbeiterletzterimport=$value; }
  function GetMitarbeiterletzterimport() { return $this->mitarbeiterletzterimport; }
  function SetImporttrennzeichen($value) { $this->importtrennzeichen=$value; }
  function GetImporttrennzeichen() { return $this->importtrennzeichen; }
  function SetImporterstezeilenummer($value) { $this->importerstezeilenummer=$value; }
  function GetImporterstezeilenummer() { return $this->importerstezeilenummer; }
  function SetImportdatenmaskierung($value) { $this->importdatenmaskierung=$value; }
  function GetImportdatenmaskierung() { return $this->importdatenmaskierung; }
  function SetImportzeichensatz($value) { $this->importzeichensatz=$value; }
  function GetImportzeichensatz() { return $this->importzeichensatz; }

}

?>