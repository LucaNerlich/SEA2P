<?php

class ObjGenShopexport
{

  private  $id;
  private  $bezeichnung;
  private  $typ;
  private  $url;
  private  $passwort;
  private  $token;
  private  $challenge;
  private  $projekt;
  private  $cms;
  private  $firma;
  private  $logdatei;
  private  $geloescht;
  private  $artikelporto;
  private  $artikelnachnahme;
  private  $artikelimport;
  private  $artikelimporteinzeln;
  private  $aktiv;
  private  $lagerexport;
  private  $artikelexport;
  private  $multiprojekt;
  private  $demomodus;

  public $app;            //application object 

  public function ObjGenShopexport($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM shopexport WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->bezeichnung=$result[bezeichnung];
    $this->typ=$result[typ];
    $this->url=$result[url];
    $this->passwort=$result[passwort];
    $this->token=$result[token];
    $this->challenge=$result[challenge];
    $this->projekt=$result[projekt];
    $this->cms=$result[cms];
    $this->firma=$result[firma];
    $this->logdatei=$result[logdatei];
    $this->geloescht=$result[geloescht];
    $this->artikelporto=$result[artikelporto];
    $this->artikelnachnahme=$result[artikelnachnahme];
    $this->artikelimport=$result[artikelimport];
    $this->artikelimporteinzeln=$result[artikelimporteinzeln];
    $this->aktiv=$result[aktiv];
    $this->lagerexport=$result[lagerexport];
    $this->artikelexport=$result[artikelexport];
    $this->multiprojekt=$result[multiprojekt];
    $this->demomodus=$result[demomodus];
  }

  public function Create()
  {
    $sql = "INSERT INTO shopexport (id,bezeichnung,typ,url,passwort,token,challenge,projekt,cms,firma,logdatei,geloescht,artikelporto,artikelnachnahme,artikelimport,artikelimporteinzeln,aktiv,lagerexport,artikelexport,multiprojekt,demomodus)
      VALUES('','{$this->bezeichnung}','{$this->typ}','{$this->url}','{$this->passwort}','{$this->token}','{$this->challenge}','{$this->projekt}','{$this->cms}','{$this->firma}','{$this->logdatei}','{$this->geloescht}','{$this->artikelporto}','{$this->artikelnachnahme}','{$this->artikelimport}','{$this->artikelimporteinzeln}','{$this->aktiv}','{$this->lagerexport}','{$this->artikelexport}','{$this->multiprojekt}','{$this->demomodus}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE shopexport SET
      bezeichnung='{$this->bezeichnung}',
      typ='{$this->typ}',
      url='{$this->url}',
      passwort='{$this->passwort}',
      token='{$this->token}',
      challenge='{$this->challenge}',
      projekt='{$this->projekt}',
      cms='{$this->cms}',
      firma='{$this->firma}',
      logdatei='{$this->logdatei}',
      geloescht='{$this->geloescht}',
      artikelporto='{$this->artikelporto}',
      artikelnachnahme='{$this->artikelnachnahme}',
      artikelimport='{$this->artikelimport}',
      artikelimporteinzeln='{$this->artikelimporteinzeln}',
      aktiv='{$this->aktiv}',
      lagerexport='{$this->lagerexport}',
      artikelexport='{$this->artikelexport}',
      multiprojekt='{$this->multiprojekt}',
      demomodus='{$this->demomodus}'
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

    $sql = "DELETE FROM shopexport WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->bezeichnung="";
    $this->typ="";
    $this->url="";
    $this->passwort="";
    $this->token="";
    $this->challenge="";
    $this->projekt="";
    $this->cms="";
    $this->firma="";
    $this->logdatei="";
    $this->geloescht="";
    $this->artikelporto="";
    $this->artikelnachnahme="";
    $this->artikelimport="";
    $this->artikelimporteinzeln="";
    $this->aktiv="";
    $this->lagerexport="";
    $this->artikelexport="";
    $this->multiprojekt="";
    $this->demomodus="";
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
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetTyp($value) { $this->typ=$value; }
  function GetTyp() { return $this->typ; }
  function SetUrl($value) { $this->url=$value; }
  function GetUrl() { return $this->url; }
  function SetPasswort($value) { $this->passwort=$value; }
  function GetPasswort() { return $this->passwort; }
  function SetToken($value) { $this->token=$value; }
  function GetToken() { return $this->token; }
  function SetChallenge($value) { $this->challenge=$value; }
  function GetChallenge() { return $this->challenge; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetCms($value) { $this->cms=$value; }
  function GetCms() { return $this->cms; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetArtikelporto($value) { $this->artikelporto=$value; }
  function GetArtikelporto() { return $this->artikelporto; }
  function SetArtikelnachnahme($value) { $this->artikelnachnahme=$value; }
  function GetArtikelnachnahme() { return $this->artikelnachnahme; }
  function SetArtikelimport($value) { $this->artikelimport=$value; }
  function GetArtikelimport() { return $this->artikelimport; }
  function SetArtikelimporteinzeln($value) { $this->artikelimporteinzeln=$value; }
  function GetArtikelimporteinzeln() { return $this->artikelimporteinzeln; }
  function SetAktiv($value) { $this->aktiv=$value; }
  function GetAktiv() { return $this->aktiv; }
  function SetLagerexport($value) { $this->lagerexport=$value; }
  function GetLagerexport() { return $this->lagerexport; }
  function SetArtikelexport($value) { $this->artikelexport=$value; }
  function GetArtikelexport() { return $this->artikelexport; }
  function SetMultiprojekt($value) { $this->multiprojekt=$value; }
  function GetMultiprojekt() { return $this->multiprojekt; }
  function SetDemomodus($value) { $this->demomodus=$value; }
  function GetDemomodus() { return $this->demomodus; }

}

?>