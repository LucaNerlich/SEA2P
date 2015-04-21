<?php

class ObjGenRma_Artikel
{

  private  $id;
  private  $adresse;
  private  $wareneingang;
  private  $bearbeiter;
  private  $lieferschein;
  private  $pos;
  private  $wunsch;
  private  $bemerkung;
  private  $artikel;
  private  $status;
  private  $angelegtam;
  private  $menge;
  private  $techniker;
  private  $buchhaltung;
  private  $abgeschlossen;
  private  $firma;

  public $app;            //application object 

  public function ObjGenRma_Artikel($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM rma_artikel WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->wareneingang=$result[wareneingang];
    $this->bearbeiter=$result[bearbeiter];
    $this->lieferschein=$result[lieferschein];
    $this->pos=$result[pos];
    $this->wunsch=$result[wunsch];
    $this->bemerkung=$result[bemerkung];
    $this->artikel=$result[artikel];
    $this->status=$result[status];
    $this->angelegtam=$result[angelegtam];
    $this->menge=$result[menge];
    $this->techniker=$result[techniker];
    $this->buchhaltung=$result[buchhaltung];
    $this->abgeschlossen=$result[abgeschlossen];
    $this->firma=$result[firma];
  }

  public function Create()
  {
    $sql = "INSERT INTO rma_artikel (id,adresse,wareneingang,bearbeiter,lieferschein,pos,wunsch,bemerkung,artikel,status,angelegtam,menge,techniker,buchhaltung,abgeschlossen,firma)
      VALUES(DEFAULT, ".((is_numeric($this->adresse)) ? $this->adresse : '0').", ".((is_numeric($this->wareneingang)) ? $this->wareneingang : '0').", '{$this->bearbeiter}', ".((is_numeric($this->lieferschein)) ? $this->lieferschein : '0').", ".((is_numeric($this->pos)) ? $this->pos : '0').", '{$this->wunsch}', '{$this->bemerkung}', ".((is_numeric($this->artikel)) ? $this->artikel : '0').", '{$this->status}', ".(($this->angelegtam=='' || $this->angelegtam=='--') ? 'NOW()' : "'".$this->angelegtam."'").", ".((is_numeric($this->menge)) ? $this->menge : '0').", '{$this->techniker}', '{$this->buchhaltung}', ".((is_numeric($this->abgeschlossen)) ? $this->abgeschlossen : '0').", ".((is_numeric($this->firma)) ? $this->firma : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE rma_artikel SET
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      wareneingang=".((is_numeric($this->wareneingang)) ? $this->wareneingang : '0').",
      bearbeiter='{$this->bearbeiter}',
      lieferschein=".((is_numeric($this->lieferschein)) ? $this->lieferschein : '0').",
      pos=".((is_numeric($this->pos)) ? $this->pos : '0').",
      wunsch='{$this->wunsch}',
      bemerkung='{$this->bemerkung}',
      artikel=".((is_numeric($this->artikel)) ? $this->artikel : '0').",
      status='{$this->status}',
      angelegtam=".(($this->angelegtam=='' || $this->angelegtam=='--') ? 'NOW()' : "'".$this->angelegtam."'").",
      menge=".((is_numeric($this->menge)) ? $this->menge : '0').",
      techniker='{$this->techniker}',
      buchhaltung='{$this->buchhaltung}',
      abgeschlossen=".((is_numeric($this->abgeschlossen)) ? $this->abgeschlossen : '0').",
      firma=".((is_numeric($this->firma)) ? $this->firma : '0')."
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

    $sql = "DELETE FROM rma_artikel WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->wareneingang="";
    $this->bearbeiter="";
    $this->lieferschein="";
    $this->pos="";
    $this->wunsch="";
    $this->bemerkung="";
    $this->artikel="";
    $this->status="";
    $this->angelegtam="";
    $this->menge="";
    $this->techniker="";
    $this->buchhaltung="";
    $this->abgeschlossen="";
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
  function SetWareneingang($value) { $this->wareneingang=$value; }
  function GetWareneingang() { return $this->wareneingang; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetLieferschein($value) { $this->lieferschein=$value; }
  function GetLieferschein() { return $this->lieferschein; }
  function SetPos($value) { $this->pos=$value; }
  function GetPos() { return $this->pos; }
  function SetWunsch($value) { $this->wunsch=$value; }
  function GetWunsch() { return $this->wunsch; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetAngelegtam($value) { $this->angelegtam=$value; }
  function GetAngelegtam() { return $this->angelegtam; }
  function SetMenge($value) { $this->menge=$value; }
  function GetMenge() { return $this->menge; }
  function SetTechniker($value) { $this->techniker=$value; }
  function GetTechniker() { return $this->techniker; }
  function SetBuchhaltung($value) { $this->buchhaltung=$value; }
  function GetBuchhaltung() { return $this->buchhaltung; }
  function SetAbgeschlossen($value) { $this->abgeschlossen=$value; }
  function GetAbgeschlossen() { return $this->abgeschlossen; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }

}

?>