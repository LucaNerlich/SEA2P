<?php

class ObjGenArtikel_Shop
{

  private  $id;
  private  $artikel;
  private  $shop;
  private  $checksum;

  public $app;            //application object 

  public function ObjGenArtikel_Shop($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM artikel_shop WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->artikel=$result[artikel];
    $this->shop=$result[shop];
    $this->checksum=$result[checksum];
  }

  public function Create()
  {
    $sql = "INSERT INTO artikel_shop (id,artikel,shop,checksum)
      VALUES('','{$this->artikel}','{$this->shop}','{$this->checksum}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE artikel_shop SET
      artikel='{$this->artikel}',
      shop='{$this->shop}',
      checksum='{$this->checksum}'
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

    $sql = "DELETE FROM artikel_shop WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->artikel="";
    $this->shop="";
    $this->checksum="";
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
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetShop($value) { $this->shop=$value; }
  function GetShop() { return $this->shop; }
  function SetChecksum($value) { $this->checksum=$value; }
  function GetChecksum() { return $this->checksum; }

}

?>