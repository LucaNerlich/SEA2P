<?php

class ObjGenPartner
{

  private  $id;
  private  $adresse;
  private  $ref;
  private  $bezeichnung;
  private  $netto;
  private  $tage;
  private  $projekt;
  private  $geloescht;
  private  $firma;

  public $app;            //application object 

  public function ObjGenPartner($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM partner WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->ref=$result[ref];
    $this->bezeichnung=$result[bezeichnung];
    $this->netto=$result[netto];
    $this->tage=$result[tage];
    $this->projekt=$result[projekt];
    $this->geloescht=$result[geloescht];
    $this->firma=$result[firma];
  }

  public function Create()
  {
    $sql = "INSERT INTO partner (id,adresse,ref,bezeichnung,netto,tage,projekt,geloescht,firma)
      VALUES('','{$this->adresse}','{$this->ref}','{$this->bezeichnung}','{$this->netto}','{$this->tage}','{$this->projekt}','{$this->geloescht}','{$this->firma}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE partner SET
      adresse='{$this->adresse}',
      ref='{$this->ref}',
      bezeichnung='{$this->bezeichnung}',
      netto='{$this->netto}',
      tage='{$this->tage}',
      projekt='{$this->projekt}',
      geloescht='{$this->geloescht}',
      firma='{$this->firma}'
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

    $sql = "DELETE FROM partner WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->ref="";
    $this->bezeichnung="";
    $this->netto="";
    $this->tage="";
    $this->projekt="";
    $this->geloescht="";
    $this->firma="";
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
  function SetRef($value) { $this->ref=$value; }
  function GetRef() { return $this->ref; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetNetto($value) { $this->netto=$value; }
  function GetNetto() { return $this->netto; }
  function SetTage($value) { $this->tage=$value; }
  function GetTage() { return $this->tage; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }

}

?>