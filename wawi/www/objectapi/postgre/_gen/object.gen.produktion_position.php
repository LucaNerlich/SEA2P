<?php

class ObjGenProduktion_Position
{

  private  $id;
  private  $produktion;
  private  $artikel;
  private  $projekt;
  private  $bezeichnung;
  private  $beschreibung;
  private  $internerkommentar;
  private  $nummer;
  private  $menge;
  private  $preis;
  private  $waehrung;
  private  $lieferdatum;
  private  $vpe;
  private  $sort;
  private  $status;
  private  $umsatzsteuer;
  private  $bemerkung;
  private  $geliefert;
  private  $geliefert_menge;
  private  $explodiert;
  private  $explodiert_parent;
  private  $logdatei;
  private  $nachbestelltexternereinkauf;

  public $app;            //application object 

  public function ObjGenProduktion_Position($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM produktion_position WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->produktion=$result[produktion];
    $this->artikel=$result[artikel];
    $this->projekt=$result[projekt];
    $this->bezeichnung=$result[bezeichnung];
    $this->beschreibung=$result[beschreibung];
    $this->internerkommentar=$result[internerkommentar];
    $this->nummer=$result[nummer];
    $this->menge=$result[menge];
    $this->preis=$result[preis];
    $this->waehrung=$result[waehrung];
    $this->lieferdatum=$result[lieferdatum];
    $this->vpe=$result[vpe];
    $this->sort=$result[sort];
    $this->status=$result[status];
    $this->umsatzsteuer=$result[umsatzsteuer];
    $this->bemerkung=$result[bemerkung];
    $this->geliefert=$result[geliefert];
    $this->geliefert_menge=$result[geliefert_menge];
    $this->explodiert=$result[explodiert];
    $this->explodiert_parent=$result[explodiert_parent];
    $this->logdatei=$result[logdatei];
    $this->nachbestelltexternereinkauf=$result[nachbestelltexternereinkauf];
  }

  public function Create()
  {
    $sql = "INSERT INTO produktion_position (id,produktion,artikel,projekt,bezeichnung,beschreibung,internerkommentar,nummer,menge,preis,waehrung,lieferdatum,vpe,sort,status,umsatzsteuer,bemerkung,geliefert,geliefert_menge,explodiert,explodiert_parent,logdatei,nachbestelltexternereinkauf)
      VALUES(DEFAULT, ".((is_numeric($this->produktion)) ? $this->produktion : '0').", ".((is_numeric($this->artikel)) ? $this->artikel : '0').", ".((is_numeric($this->projekt)) ? $this->projekt : '0').", '{$this->bezeichnung}', '{$this->beschreibung}', '{$this->internerkommentar}', '{$this->nummer}', '{$this->menge}', ".((is_numeric($this->preis)) ? $this->preis : '0').", '{$this->waehrung}', ".(($this->lieferdatum=='' || $this->lieferdatum=='--') ? 'NOW()' : "'".$this->lieferdatum."'").", '{$this->vpe}', ".((is_numeric($this->sort)) ? $this->sort : '0').", '{$this->status}', '{$this->umsatzsteuer}', '{$this->bemerkung}', ".((is_numeric($this->geliefert)) ? $this->geliefert : '0').", ".((is_numeric($this->geliefert_menge)) ? $this->geliefert_menge : '0').", ".((is_numeric($this->explodiert)) ? $this->explodiert : '0').", ".((is_numeric($this->explodiert_parent)) ? $this->explodiert_parent : '0').", ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").", ".((is_numeric($this->nachbestelltexternereinkauf)) ? $this->nachbestelltexternereinkauf : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE produktion_position SET
      produktion=".((is_numeric($this->produktion)) ? $this->produktion : '0').",
      artikel=".((is_numeric($this->artikel)) ? $this->artikel : '0').",
      projekt=".((is_numeric($this->projekt)) ? $this->projekt : '0').",
      bezeichnung='{$this->bezeichnung}',
      beschreibung='{$this->beschreibung}',
      internerkommentar='{$this->internerkommentar}',
      nummer='{$this->nummer}',
      menge='{$this->menge}',
      preis=".((is_numeric($this->preis)) ? $this->preis : '0').",
      waehrung='{$this->waehrung}',
      lieferdatum=".(($this->lieferdatum=='' || $this->lieferdatum=='--') ? 'NOW()' : "'".$this->lieferdatum."'").",
      vpe='{$this->vpe}',
      sort=".((is_numeric($this->sort)) ? $this->sort : '0').",
      status='{$this->status}',
      umsatzsteuer='{$this->umsatzsteuer}',
      bemerkung='{$this->bemerkung}',
      geliefert=".((is_numeric($this->geliefert)) ? $this->geliefert : '0').",
      geliefert_menge=".((is_numeric($this->geliefert_menge)) ? $this->geliefert_menge : '0').",
      explodiert=".((is_numeric($this->explodiert)) ? $this->explodiert : '0').",
      explodiert_parent=".((is_numeric($this->explodiert_parent)) ? $this->explodiert_parent : '0').",
      logdatei=".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").",
      nachbestelltexternereinkauf=".((is_numeric($this->nachbestelltexternereinkauf)) ? $this->nachbestelltexternereinkauf : '0')."
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

    $sql = "DELETE FROM produktion_position WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->produktion="";
    $this->artikel="";
    $this->projekt="";
    $this->bezeichnung="";
    $this->beschreibung="";
    $this->internerkommentar="";
    $this->nummer="";
    $this->menge="";
    $this->preis="";
    $this->waehrung="";
    $this->lieferdatum="";
    $this->vpe="";
    $this->sort="";
    $this->status="";
    $this->umsatzsteuer="";
    $this->bemerkung="";
    $this->geliefert="";
    $this->geliefert_menge="";
    $this->explodiert="";
    $this->explodiert_parent="";
    $this->logdatei="";
    $this->nachbestelltexternereinkauf="";
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
  function SetProduktion($value) { $this->produktion=$value; }
  function GetProduktion() { return $this->produktion; }
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
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetPreis($value) { $this->preis=$value; }
  function GetPreis() { return $this->preis; }
  function SetWaehrung($value) { $this->waehrung=$value; }
  function GetWaehrung() { return $this->waehrung; }
  function SetLieferdatum($value) { $this->lieferdatum=$value; }
  function GetLieferdatum() { return $this->lieferdatum; }
  function SetVpe($value) { $this->vpe=$value; }
  function GetVpe() { return $this->vpe; }
  function SetSort($value) { $this->sort=$value; }
  function GetSort() { return $this->sort; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetUmsatzsteuer($value) { $this->umsatzsteuer=$value; }
  function GetUmsatzsteuer() { return $this->umsatzsteuer; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetGeliefert($value) { $this->geliefert=$value; }
  function GetGeliefert() { return $this->geliefert; }
  function SetGeliefert_Menge($value) { $this->geliefert_menge=$value; }
  function GetGeliefert_Menge() { return $this->geliefert_menge; }
  function SetExplodiert($value) { $this->explodiert=$value; }
  function GetExplodiert() { return $this->explodiert; }
  function SetExplodiert_Parent($value) { $this->explodiert_parent=$value; }
  function GetExplodiert_Parent() { return $this->explodiert_parent; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetNachbestelltexternereinkauf($value) { $this->nachbestelltexternereinkauf=$value; }
  function GetNachbestelltexternereinkauf() { return $this->nachbestelltexternereinkauf; }

}

?>