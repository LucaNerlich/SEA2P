<?php

class ObjGenExportvorlage
{

  private  $id;
  private  $bezeichnung;
  private  $ziel;
  private  $internebemerkung;
  private  $fields;
  private  $letzterexport;
  private  $mitarbeiterletzterexport;
  private  $exporttrennzeichen;
  private  $exporterstezeilenummer;
  private  $exportdatenmaskierung;
  private  $exportzeichensatz;
  private  $fields_where;

  public $app;            //application object 

  public function ObjGenExportvorlage($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM exportvorlage WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->bezeichnung=$result[bezeichnung];
    $this->ziel=$result[ziel];
    $this->internebemerkung=$result[internebemerkung];
    $this->fields=$result[fields];
    $this->letzterexport=$result[letzterexport];
    $this->mitarbeiterletzterexport=$result[mitarbeiterletzterexport];
    $this->exporttrennzeichen=$result[exporttrennzeichen];
    $this->exporterstezeilenummer=$result[exporterstezeilenummer];
    $this->exportdatenmaskierung=$result[exportdatenmaskierung];
    $this->exportzeichensatz=$result[exportzeichensatz];
    $this->fields_where=$result[fields_where];
  }

  public function Create()
  {
    $sql = "INSERT INTO exportvorlage (id,bezeichnung,ziel,internebemerkung,fields,letzterexport,mitarbeiterletzterexport,exporttrennzeichen,exporterstezeilenummer,exportdatenmaskierung,exportzeichensatz,fields_where)
      VALUES('','{$this->bezeichnung}','{$this->ziel}','{$this->internebemerkung}','{$this->fields}','{$this->letzterexport}','{$this->mitarbeiterletzterexport}','{$this->exporttrennzeichen}','{$this->exporterstezeilenummer}','{$this->exportdatenmaskierung}','{$this->exportzeichensatz}','{$this->fields_where}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE exportvorlage SET
      bezeichnung='{$this->bezeichnung}',
      ziel='{$this->ziel}',
      internebemerkung='{$this->internebemerkung}',
      fields='{$this->fields}',
      letzterexport='{$this->letzterexport}',
      mitarbeiterletzterexport='{$this->mitarbeiterletzterexport}',
      exporttrennzeichen='{$this->exporttrennzeichen}',
      exporterstezeilenummer='{$this->exporterstezeilenummer}',
      exportdatenmaskierung='{$this->exportdatenmaskierung}',
      exportzeichensatz='{$this->exportzeichensatz}',
      fields_where='{$this->fields_where}'
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

    $sql = "DELETE FROM exportvorlage WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->bezeichnung="";
    $this->ziel="";
    $this->internebemerkung="";
    $this->fields="";
    $this->letzterexport="";
    $this->mitarbeiterletzterexport="";
    $this->exporttrennzeichen="";
    $this->exporterstezeilenummer="";
    $this->exportdatenmaskierung="";
    $this->exportzeichensatz="";
    $this->fields_where="";
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
  function SetZiel($value) { $this->ziel=$value; }
  function GetZiel() { return $this->ziel; }
  function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  function GetInternebemerkung() { return $this->internebemerkung; }
  function SetFields($value) { $this->fields=$value; }
  function GetFields() { return $this->fields; }
  function SetLetzterexport($value) { $this->letzterexport=$value; }
  function GetLetzterexport() { return $this->letzterexport; }
  function SetMitarbeiterletzterexport($value) { $this->mitarbeiterletzterexport=$value; }
  function GetMitarbeiterletzterexport() { return $this->mitarbeiterletzterexport; }
  function SetExporttrennzeichen($value) { $this->exporttrennzeichen=$value; }
  function GetExporttrennzeichen() { return $this->exporttrennzeichen; }
  function SetExporterstezeilenummer($value) { $this->exporterstezeilenummer=$value; }
  function GetExporterstezeilenummer() { return $this->exporterstezeilenummer; }
  function SetExportdatenmaskierung($value) { $this->exportdatenmaskierung=$value; }
  function GetExportdatenmaskierung() { return $this->exportdatenmaskierung; }
  function SetExportzeichensatz($value) { $this->exportzeichensatz=$value; }
  function GetExportzeichensatz() { return $this->exportzeichensatz; }
  function SetFields_Where($value) { $this->fields_where=$value; }
  function GetFields_Where() { return $this->fields_where; }

}

?>