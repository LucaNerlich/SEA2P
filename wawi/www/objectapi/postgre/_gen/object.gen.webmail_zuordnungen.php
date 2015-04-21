<?php

class ObjGenWebmail_Zuordnungen
{

  private  $id;
  private  $mail;
  private  $zuordnung;
  private  $parameter;

  public $app;            //application object 

  public function ObjGenWebmail_Zuordnungen($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM webmail_zuordnungen WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->mail=$result[mail];
    $this->zuordnung=$result[zuordnung];
    $this->parameter=$result[parameter];
  }

  public function Create()
  {
    $sql = "INSERT INTO webmail_zuordnungen (id,mail,zuordnung,parameter)
      VALUES(DEFAULT, ".((is_numeric($this->mail)) ? $this->mail : '0').", '{$this->zuordnung}', ".((is_numeric($this->parameter)) ? $this->parameter : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE webmail_zuordnungen SET
      mail=".((is_numeric($this->mail)) ? $this->mail : '0').",
      zuordnung='{$this->zuordnung}',
      parameter=".((is_numeric($this->parameter)) ? $this->parameter : '0')."
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

    $sql = "DELETE FROM webmail_zuordnungen WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->mail="";
    $this->zuordnung="";
    $this->parameter="";
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
  function SetMail($value) { $this->mail=$value; }
  function GetMail() { return $this->mail; }
  function SetZuordnung($value) { $this->zuordnung=$value; }
  function GetZuordnung() { return $this->zuordnung; }
  function SetParameter($value) { $this->parameter=$value; }
  function GetParameter() { return $this->parameter; }

}

?>