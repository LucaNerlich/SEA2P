<?php

class ObjGenStueckliste
{

  private  $id;
  private  $sort;
  private  $artikel;
  private  $referenz;
  private  $place;
  private  $layer;
  private  $stuecklistevonartikel;
  private  $menge;
  private  $firma;
  private  $wert;
  private  $bauform;

  public $app;            //application object 

  public function ObjGenStueckliste($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM stueckliste WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->sort=$result[sort];
    $this->artikel=$result[artikel];
    $this->referenz=$result[referenz];
    $this->place=$result[place];
    $this->layer=$result[layer];
    $this->stuecklistevonartikel=$result[stuecklistevonartikel];
    $this->menge=$result[menge];
    $this->firma=$result[firma];
    $this->wert=$result[wert];
    $this->bauform=$result[bauform];
  }

  public function Create()
  {
    $sql = "INSERT INTO stueckliste (id,sort,artikel,referenz,place,layer,stuecklistevonartikel,menge,firma,wert,bauform)
      VALUES('','{$this->sort}','{$this->artikel}','{$this->referenz}','{$this->place}','{$this->layer}','{$this->stuecklistevonartikel}','{$this->menge}','{$this->firma}','{$this->wert}','{$this->bauform}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE stueckliste SET
      sort='{$this->sort}',
      artikel='{$this->artikel}',
      referenz='{$this->referenz}',
      place='{$this->place}',
      layer='{$this->layer}',
      stuecklistevonartikel='{$this->stuecklistevonartikel}',
      menge='{$this->menge}',
      firma='{$this->firma}',
      wert='{$this->wert}',
      bauform='{$this->bauform}'
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

    $sql = "DELETE FROM stueckliste WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->sort="";
    $this->artikel="";
    $this->referenz="";
    $this->place="";
    $this->layer="";
    $this->stuecklistevonartikel="";
    $this->menge="";
    $this->firma="";
    $this->wert="";
    $this->bauform="";
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
  function SetSort($value) { $this->sort=$value; }
  function GetSort() { return $this->sort; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetReferenz($value) { $this->referenz=$value; }
  function GetReferenz() { return $this->referenz; }
  function SetPlace($value) { $this->place=$value; }
  function GetPlace() { return $this->place; }
  function SetLayer($value) { $this->layer=$value; }
  function GetLayer() { return $this->layer; }
  function SetStuecklistevonartikel($value) { $this->stuecklistevonartikel=$value; }
  function GetStuecklistevonartikel() { return $this->stuecklistevonartikel; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetWert($value) { $this->wert=$value; }
  function GetWert() { return $this->wert; }
  function SetBauform($value) { $this->bauform=$value; }
  function GetBauform() { return $this->bauform; }

}

?>