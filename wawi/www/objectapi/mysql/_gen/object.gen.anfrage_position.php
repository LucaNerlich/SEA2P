<?php

class ObjGenAnfrage_Position
{

  private  $id;
  private  $anfrage;
  private  $artikel;
  private  $projekt;
  private  $nummer;
  private  $bezeichnung;
  private  $beschreibung;
  private  $internerkommentar;
  private  $menge;
  private  $sort;
  private  $bemerkung;
  private  $preis;
  private  $logdatei;
  private  $geliefert;
  private  $lieferdatum;
  private  $vpe;
  private  $einheit;

  public $app;            //application object 

  public function ObjGenAnfrage_Position($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM anfrage_position WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->anfrage=$result[anfrage];
    $this->artikel=$result[artikel];
    $this->projekt=$result[projekt];
    $this->nummer=$result[nummer];
    $this->bezeichnung=$result[bezeichnung];
    $this->beschreibung=$result[beschreibung];
    $this->internerkommentar=$result[internerkommentar];
    $this->menge=$result[menge];
    $this->sort=$result[sort];
    $this->bemerkung=$result[bemerkung];
    $this->preis=$result[preis];
    $this->logdatei=$result[logdatei];
    $this->geliefert=$result[geliefert];
    $this->lieferdatum=$result[lieferdatum];
    $this->vpe=$result[vpe];
    $this->einheit=$result[einheit];
  }

  public function Create()
  {
    $sql = "INSERT INTO anfrage_position (id,anfrage,artikel,projekt,nummer,bezeichnung,beschreibung,internerkommentar,menge,sort,bemerkung,preis,logdatei,geliefert,lieferdatum,vpe,einheit)
      VALUES('','{$this->anfrage}','{$this->artikel}','{$this->projekt}','{$this->nummer}','{$this->bezeichnung}','{$this->beschreibung}','{$this->internerkommentar}','{$this->menge}','{$this->sort}','{$this->bemerkung}','{$this->preis}','{$this->logdatei}','{$this->geliefert}','{$this->lieferdatum}','{$this->vpe}','{$this->einheit}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE anfrage_position SET
      anfrage='{$this->anfrage}',
      artikel='{$this->artikel}',
      projekt='{$this->projekt}',
      nummer='{$this->nummer}',
      bezeichnung='{$this->bezeichnung}',
      beschreibung='{$this->beschreibung}',
      internerkommentar='{$this->internerkommentar}',
      menge='{$this->menge}',
      sort='{$this->sort}',
      bemerkung='{$this->bemerkung}',
      preis='{$this->preis}',
      logdatei='{$this->logdatei}',
      geliefert='{$this->geliefert}',
      lieferdatum='{$this->lieferdatum}',
      vpe='{$this->vpe}',
      einheit='{$this->einheit}'
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

    $sql = "DELETE FROM anfrage_position WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->anfrage="";
    $this->artikel="";
    $this->projekt="";
    $this->nummer="";
    $this->bezeichnung="";
    $this->beschreibung="";
    $this->internerkommentar="";
    $this->menge="";
    $this->sort="";
    $this->bemerkung="";
    $this->preis="";
    $this->logdatei="";
    $this->geliefert="";
    $this->lieferdatum="";
    $this->vpe="";
    $this->einheit="";
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
  function SetAnfrage($value) { $this->anfrage=$value; }
  function GetAnfrage() { return $this->anfrage; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetNummer($value) { $this->nummer=$value; }
  function GetNummer() { return $this->nummer; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetInternerkommentar($value) { $this->internerkommentar=$value; }
  function GetInternerkommentar() { return $this->internerkommentar; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetSort($value) { $this->sort=$value; }
  function GetSort() { return $this->sort; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetPreis($value) { $this->preis=$value; }
  function GetPreis() { return $this->preis; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetGeliefert($value) { $this->geliefert=$value; }
  function GetGeliefert() { return $this->geliefert; }
  function SetLieferdatum($value) { $this->lieferdatum=$value; }
  function GetLieferdatum() { return $this->lieferdatum; }
  function SetVpe($value) { $this->vpe=$value; }
  function GetVpe() { return $this->vpe; }
  function SetEinheit($value) { $this->einheit=$value; }
  function GetEinheit() { return $this->einheit; }

}

?>