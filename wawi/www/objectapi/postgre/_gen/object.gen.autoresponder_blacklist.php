<?php

class ObjGenAutoresponder_Blacklist
{

  private  $id;
  private  $cachetime;
  private  $mailaddress;

  public $app;            //application object 

  public function ObjGenAutoresponder_Blacklist($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM autoresponder_blacklist WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->cachetime=$result[cachetime];
    $this->mailaddress=$result[mailaddress];
  }

  public function Create()
  {
    $sql = "INSERT INTO autoresponder_blacklist (id,cachetime,mailaddress)
      VALUES(DEFAULT, ".(($this->cachetime=='' || $this->cachetime=='--') ? 'NOW()' : "'".$this->cachetime."'").", '{$this->mailaddress}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE autoresponder_blacklist SET
      cachetime=".(($this->cachetime=='' || $this->cachetime=='--') ? 'NOW()' : "'".$this->cachetime."'").",
      mailaddress='{$this->mailaddress}'
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

    $sql = "DELETE FROM autoresponder_blacklist WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->cachetime="";
    $this->mailaddress="";
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
  function SetCachetime($value) { $this->cachetime=$value; }
  function GetCachetime() { return $this->cachetime; }
  function SetMailaddress($value) { $this->mailaddress=$value; }
  function GetMailaddress() { return $this->mailaddress; }

}

?>