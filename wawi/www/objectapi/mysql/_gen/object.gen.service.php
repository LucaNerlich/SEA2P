<?php

class ObjGenService
{

  private  $id;
  private  $adresse;
  private  $zuweisen;
  private  $ansprechpartner;
  private  $nummer;
  private  $prio;
  private  $eingangart;
  private  $datum;
  private  $erledigenbis;
  private  $betreff;
  private  $angelegtvonuser;
  private  $status;
  private  $antwortankundenempfaenger;
  private  $antwortankundenkopie;
  private  $antwortankundenblindkopie;
  private  $antwortankundenbetreff;
  private  $internebemerkung;
  private  $antwortankunden;
  private  $artikel;
  private  $seriennummer;
  private  $antwortpermail;
  private  $beschreibung_html;

  public $app;            //application object 

  public function ObjGenService($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM service WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse=$result[adresse];
    $this->zuweisen=$result[zuweisen];
    $this->ansprechpartner=$result[ansprechpartner];
    $this->nummer=$result[nummer];
    $this->prio=$result[prio];
    $this->eingangart=$result[eingangart];
    $this->datum=$result[datum];
    $this->erledigenbis=$result[erledigenbis];
    $this->betreff=$result[betreff];
    $this->angelegtvonuser=$result[angelegtvonuser];
    $this->status=$result[status];
    $this->antwortankundenempfaenger=$result[antwortankundenempfaenger];
    $this->antwortankundenkopie=$result[antwortankundenkopie];
    $this->antwortankundenblindkopie=$result[antwortankundenblindkopie];
    $this->antwortankundenbetreff=$result[antwortankundenbetreff];
    $this->internebemerkung=$result[internebemerkung];
    $this->antwortankunden=$result[antwortankunden];
    $this->artikel=$result[artikel];
    $this->seriennummer=$result[seriennummer];
    $this->antwortpermail=$result[antwortpermail];
    $this->beschreibung_html=$result[beschreibung_html];
  }

  public function Create()
  {
    $sql = "INSERT INTO service (id,adresse,zuweisen,ansprechpartner,nummer,prio,eingangart,datum,erledigenbis,betreff,angelegtvonuser,status,antwortankundenempfaenger,antwortankundenkopie,antwortankundenblindkopie,antwortankundenbetreff,internebemerkung,antwortankunden,artikel,seriennummer,antwortpermail,beschreibung_html)
      VALUES('','{$this->adresse}','{$this->zuweisen}','{$this->ansprechpartner}','{$this->nummer}','{$this->prio}','{$this->eingangart}','{$this->datum}','{$this->erledigenbis}','{$this->betreff}','{$this->angelegtvonuser}','{$this->status}','{$this->antwortankundenempfaenger}','{$this->antwortankundenkopie}','{$this->antwortankundenblindkopie}','{$this->antwortankundenbetreff}','{$this->internebemerkung}','{$this->antwortankunden}','{$this->artikel}','{$this->seriennummer}','{$this->antwortpermail}','{$this->beschreibung_html}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE service SET
      adresse='{$this->adresse}',
      zuweisen='{$this->zuweisen}',
      ansprechpartner='{$this->ansprechpartner}',
      nummer='{$this->nummer}',
      prio='{$this->prio}',
      eingangart='{$this->eingangart}',
      datum='{$this->datum}',
      erledigenbis='{$this->erledigenbis}',
      betreff='{$this->betreff}',
      angelegtvonuser='{$this->angelegtvonuser}',
      status='{$this->status}',
      antwortankundenempfaenger='{$this->antwortankundenempfaenger}',
      antwortankundenkopie='{$this->antwortankundenkopie}',
      antwortankundenblindkopie='{$this->antwortankundenblindkopie}',
      antwortankundenbetreff='{$this->antwortankundenbetreff}',
      internebemerkung='{$this->internebemerkung}',
      antwortankunden='{$this->antwortankunden}',
      artikel='{$this->artikel}',
      seriennummer='{$this->seriennummer}',
      antwortpermail='{$this->antwortpermail}',
      beschreibung_html='{$this->beschreibung_html}'
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

    $sql = "DELETE FROM service WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse="";
    $this->zuweisen="";
    $this->ansprechpartner="";
    $this->nummer="";
    $this->prio="";
    $this->eingangart="";
    $this->datum="";
    $this->erledigenbis="";
    $this->betreff="";
    $this->angelegtvonuser="";
    $this->status="";
    $this->antwortankundenempfaenger="";
    $this->antwortankundenkopie="";
    $this->antwortankundenblindkopie="";
    $this->antwortankundenbetreff="";
    $this->internebemerkung="";
    $this->antwortankunden="";
    $this->artikel="";
    $this->seriennummer="";
    $this->antwortpermail="";
    $this->beschreibung_html="";
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
  function SetZuweisen($value) { $this->zuweisen=$value; }
  function GetZuweisen() { return $this->zuweisen; }
  function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  function GetAnsprechpartner() { return $this->ansprechpartner; }
  function SetNummer($value) { $this->nummer=$value; }
  function GetNummer() { return $this->nummer; }
  function SetPrio($value) { $this->prio=$value; }
  function GetPrio() { return $this->prio; }
  function SetEingangart($value) { $this->eingangart=$value; }
  function GetEingangart() { return $this->eingangart; }
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetErledigenbis($value) { $this->erledigenbis=$value; }
  function GetErledigenbis() { return $this->erledigenbis; }
  function SetBetreff($value) { $this->betreff=$value; }
  function GetBetreff() { return $this->betreff; }
  function SetAngelegtvonuser($value) { $this->angelegtvonuser=$value; }
  function GetAngelegtvonuser() { return $this->angelegtvonuser; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetAntwortankundenempfaenger($value) { $this->antwortankundenempfaenger=$value; }
  function GetAntwortankundenempfaenger() { return $this->antwortankundenempfaenger; }
  function SetAntwortankundenkopie($value) { $this->antwortankundenkopie=$value; }
  function GetAntwortankundenkopie() { return $this->antwortankundenkopie; }
  function SetAntwortankundenblindkopie($value) { $this->antwortankundenblindkopie=$value; }
  function GetAntwortankundenblindkopie() { return $this->antwortankundenblindkopie; }
  function SetAntwortankundenbetreff($value) { $this->antwortankundenbetreff=$value; }
  function GetAntwortankundenbetreff() { return $this->antwortankundenbetreff; }
  function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  function GetInternebemerkung() { return $this->internebemerkung; }
  function SetAntwortankunden($value) { $this->antwortankunden=$value; }
  function GetAntwortankunden() { return $this->antwortankunden; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetSeriennummer($value) { $this->seriennummer=$value; }
  function GetSeriennummer() { return $this->seriennummer; }
  function SetAntwortpermail($value) { $this->antwortpermail=$value; }
  function GetAntwortpermail() { return $this->antwortpermail; }
  function SetBeschreibung_Html($value) { $this->beschreibung_html=$value; }
  function GetBeschreibung_Html() { return $this->beschreibung_html; }

}

?>