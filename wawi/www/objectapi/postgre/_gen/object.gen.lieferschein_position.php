<?php

class ObjGenLieferschein_Position
{

  private  $id;
  private  $lieferschein;
  private  $artikel;
  private  $projekt;
  private  $bezeichnung;
  private  $beschreibung;
  private  $internerkommentar;
  private  $nummer;
  private  $seriennummer;
  private  $menge;
  private  $lieferdatum;
  private  $vpe;
  private  $sort;
  private  $status;
  private  $bemerkung;
  private  $geliefert;
  private  $abgerechnet;
  private  $logdatei;

  public $app;            //application object 

  public function ObjGenLieferschein_Position($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM lieferschein_position WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->lieferschein=$result[lieferschein];
    $this->artikel=$result[artikel];
    $this->projekt=$result[projekt];
    $this->bezeichnung=$result[bezeichnung];
    $this->beschreibung=$result[beschreibung];
    $this->internerkommentar=$result[internerkommentar];
    $this->nummer=$result[nummer];
    $this->seriennummer=$result[seriennummer];
    $this->menge=$result[menge];
    $this->lieferdatum=$result[lieferdatum];
    $this->vpe=$result[vpe];
    $this->sort=$result[sort];
    $this->status=$result[status];
    $this->bemerkung=$result[bemerkung];
    $this->geliefert=$result[geliefert];
    $this->abgerechnet=$result[abgerechnet];
    $this->logdatei=$result[logdatei];
  }

  public function Create()
  {
    $sql = "INSERT INTO lieferschein_position (id,lieferschein,artikel,projekt,bezeichnung,beschreibung,internerkommentar,nummer,seriennummer,menge,lieferdatum,vpe,sort,status,bemerkung,geliefert,abgerechnet,logdatei)
      VALUES(DEFAULT, '{$this->lieferschein}', '{$this->artikel}', ".((is_numeric($this->projekt)) ? $this->projekt : '0').", '{$this->bezeichnung}', '{$this->beschreibung}', '{$this->internerkommentar}', '{$this->nummer}', '{$this->seriennummer}', '{$this->menge}', ".(($this->lieferdatum=='' || $this->lieferdatum=='--') ? 'NOW()' : "'".$this->lieferdatum."'").", '{$this->vpe}', ".((is_numeric($this->sort)) ? $this->sort : '0').", '{$this->status}', '{$this->bemerkung}', ".((is_numeric($this->geliefert)) ? $this->geliefert : '0').", ".((is_numeric($this->abgerechnet)) ? $this->abgerechnet : '0').", ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE lieferschein_position SET
      lieferschein='{$this->lieferschein}',
      artikel='{$this->artikel}',
      projekt=".((is_numeric($this->projekt)) ? $this->projekt : '0').",
      bezeichnung='{$this->bezeichnung}',
      beschreibung='{$this->beschreibung}',
      internerkommentar='{$this->internerkommentar}',
      nummer='{$this->nummer}',
      seriennummer='{$this->seriennummer}',
      menge='{$this->menge}',
      lieferdatum=".(($this->lieferdatum=='' || $this->lieferdatum=='--') ? 'NOW()' : "'".$this->lieferdatum."'").",
      vpe='{$this->vpe}',
      sort=".((is_numeric($this->sort)) ? $this->sort : '0').",
      status='{$this->status}',
      bemerkung='{$this->bemerkung}',
      geliefert=".((is_numeric($this->geliefert)) ? $this->geliefert : '0').",
      abgerechnet=".((is_numeric($this->abgerechnet)) ? $this->abgerechnet : '0').",
      logdatei=".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'")."
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

    $sql = "DELETE FROM lieferschein_position WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->lieferschein="";
    $this->artikel="";
    $this->projekt="";
    $this->bezeichnung="";
    $this->beschreibung="";
    $this->internerkommentar="";
    $this->nummer="";
    $this->seriennummer="";
    $this->menge="";
    $this->lieferdatum="";
    $this->vpe="";
    $this->sort="";
    $this->status="";
    $this->bemerkung="";
    $this->geliefert="";
    $this->abgerechnet="";
    $this->logdatei="";
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
  function SetLieferschein($value) { $this->lieferschein=$value; }
  function GetLieferschein() { return $this->lieferschein; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetInternerkommentar($value) { $this->internerkommentar=$value; }
  function GetInternerkommentar() { return $this->internerkommentar; }
  function SetNummer($value) { $this->nummer=$value; }
  function GetNummer() { return $this->nummer; }
  function SetSeriennummer($value) { $this->seriennummer=$value; }
  function GetSeriennummer() { return $this->seriennummer; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetLieferdatum($value) { $this->lieferdatum=$value; }
  function GetLieferdatum() { return $this->lieferdatum; }
  function SetVpe($value) { $this->vpe=$value; }
  function GetVpe() { return $this->vpe; }
  function SetSort($value) { $this->sort=$value; }
  function GetSort() { return $this->sort; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetGeliefert($value) { $this->geliefert=$value; }
  function GetGeliefert() { return $this->geliefert; }
  function SetAbgerechnet($value) { $this->abgerechnet=$value; }
  function GetAbgerechnet() { return $this->abgerechnet; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }

}

?>