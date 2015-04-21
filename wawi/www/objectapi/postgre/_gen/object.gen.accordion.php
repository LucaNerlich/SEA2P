<?php

class ObjGenAccordion
{

  private  $id;
  private  $name;
  private  $target;
  private  $position;

  public $app;            //application object 

  public function ObjGenAccordion($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM accordion WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->name=$result[name];
    $this->target=$result[target];
    $this->position=$result[position];
  }

  public function Create()
  {
    $sql = "INSERT INTO accordion (id,name,target,position)
      VALUES(DEFAULT, '{$this->name}', '{$this->target}', ".((is_numeric($this->position)) ? $this->position : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE accordion SET
      name='{$this->name}',
      target='{$this->target}',
      position=".((is_numeric($this->position)) ? $this->position : '0')."
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

    $sql = "DELETE FROM accordion WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->name="";
    $this->target="";
    $this->position="";
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
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetTarget($value) { $this->target=$value; }
  function GetTarget() { return $this->target; }
  function SetPosition($value) { $this->position=$value; }
  function GetPosition() { return $this->position; }

}

?>