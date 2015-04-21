<?php

class ObjGenReisekosten
{

  private  $id;
  private  $datum;
  private  $projekt;
  private  $prefix;
  private  $reisekostenart;
  private  $belegnr;
  private  $bearbeiter;
  private  $auftrag;
  private  $auftragid;
  private  $freitext;
  private  $status;
  private  $adresse;
  private  $mitarbeiter;
  private  $name;
  private  $abteilung;
  private  $unterabteilung;
  private  $strasse;
  private  $adresszusatz;
  private  $ansprechpartner;
  private  $plz;
  private  $ort;
  private  $land;
  private  $ustid;
  private  $email;
  private  $telefon;
  private  $telefax;
  private  $betreff;
  private  $kundennummer;
  private  $versandart;
  private  $versand;
  private  $firma;
  private  $versendet;
  private  $versendet_am;
  private  $versendet_per;
  private  $versendet_durch;
  private  $inbearbeitung_user;
  private  $logdatei;
  private  $ohne_briefpapier;
  private  $schreibschutz;
  private  $pdfarchiviert;
  private  $pdfarchiviertversion;
  private  $anlass;
  private  $von;
  private  $bis;
  private  $von_zeit;
  private  $bis_zeit;
  private  $ust_befreit;

  public $app;            //application object 

  public function ObjGenReisekosten($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM reisekosten WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->datum=$result[datum];
    $this->projekt=$result[projekt];
    $this->prefix=$result[prefix];
    $this->reisekostenart=$result[reisekostenart];
    $this->belegnr=$result[belegnr];
    $this->bearbeiter=$result[bearbeiter];
    $this->auftrag=$result[auftrag];
    $this->auftragid=$result[auftragid];
    $this->freitext=$result[freitext];
    $this->status=$result[status];
    $this->adresse=$result[adresse];
    $this->mitarbeiter=$result[mitarbeiter];
    $this->name=$result[name];
    $this->abteilung=$result[abteilung];
    $this->unterabteilung=$result[unterabteilung];
    $this->strasse=$result[strasse];
    $this->adresszusatz=$result[adresszusatz];
    $this->ansprechpartner=$result[ansprechpartner];
    $this->plz=$result[plz];
    $this->ort=$result[ort];
    $this->land=$result[land];
    $this->ustid=$result[ustid];
    $this->email=$result[email];
    $this->telefon=$result[telefon];
    $this->telefax=$result[telefax];
    $this->betreff=$result[betreff];
    $this->kundennummer=$result[kundennummer];
    $this->versandart=$result[versandart];
    $this->versand=$result[versand];
    $this->firma=$result[firma];
    $this->versendet=$result[versendet];
    $this->versendet_am=$result[versendet_am];
    $this->versendet_per=$result[versendet_per];
    $this->versendet_durch=$result[versendet_durch];
    $this->inbearbeitung_user=$result[inbearbeitung_user];
    $this->logdatei=$result[logdatei];
    $this->ohne_briefpapier=$result[ohne_briefpapier];
    $this->schreibschutz=$result[schreibschutz];
    $this->pdfarchiviert=$result[pdfarchiviert];
    $this->pdfarchiviertversion=$result[pdfarchiviertversion];
    $this->anlass=$result[anlass];
    $this->von=$result[von];
    $this->bis=$result[bis];
    $this->von_zeit=$result[von_zeit];
    $this->bis_zeit=$result[bis_zeit];
    $this->ust_befreit=$result[ust_befreit];
  }

  public function Create()
  {
    $sql = "INSERT INTO reisekosten (id,datum,projekt,prefix,reisekostenart,belegnr,bearbeiter,auftrag,auftragid,freitext,status,adresse,mitarbeiter,name,abteilung,unterabteilung,strasse,adresszusatz,ansprechpartner,plz,ort,land,ustid,email,telefon,telefax,betreff,kundennummer,versandart,versand,firma,versendet,versendet_am,versendet_per,versendet_durch,inbearbeitung_user,logdatei,ohne_briefpapier,schreibschutz,pdfarchiviert,pdfarchiviertversion,anlass,von,bis,von_zeit,bis_zeit,ust_befreit)
      VALUES(DEFAULT, ".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'").", '{$this->projekt}', '{$this->prefix}', '{$this->reisekostenart}', '{$this->belegnr}', '{$this->bearbeiter}', '{$this->auftrag}', ".((is_numeric($this->auftragid)) ? $this->auftragid : '0').", '{$this->freitext}', '{$this->status}', ".((is_numeric($this->adresse)) ? $this->adresse : '0').", ".((is_numeric($this->mitarbeiter)) ? $this->mitarbeiter : '0').", '{$this->name}', '{$this->abteilung}', '{$this->unterabteilung}', '{$this->strasse}', '{$this->adresszusatz}', '{$this->ansprechpartner}', '{$this->plz}', '{$this->ort}', '{$this->land}', '{$this->ustid}', '{$this->email}', '{$this->telefon}', '{$this->telefax}', '{$this->betreff}', '{$this->kundennummer}', '{$this->versandart}', '{$this->versand}', ".((is_numeric($this->firma)) ? $this->firma : '0').", ".((is_numeric($this->versendet)) ? $this->versendet : '0').", ".(($this->versendet_am=='' || $this->versendet_am=='--') ? 'NOW()' : "'".$this->versendet_am."'").", '{$this->versendet_per}', '{$this->versendet_durch}', ".((is_numeric($this->inbearbeitung_user)) ? $this->inbearbeitung_user : '0').", ".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").", ".((is_numeric($this->ohne_briefpapier)) ? $this->ohne_briefpapier : '0').", ".((is_numeric($this->schreibschutz)) ? $this->schreibschutz : '0').", ".((is_numeric($this->pdfarchiviert)) ? $this->pdfarchiviert : '0').", ".((is_numeric($this->pdfarchiviertversion)) ? $this->pdfarchiviertversion : '0').", '{$this->anlass}', ".(($this->von=='' || $this->von=='--') ? 'NOW()' : "'".$this->von."'").", ".(($this->bis=='' || $this->bis=='--') ? 'NOW()' : "'".$this->bis."'").", '{$this->von_zeit}', '{$this->bis_zeit}', ".((is_numeric($this->ust_befreit)) ? $this->ust_befreit : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE reisekosten SET
      datum=".(($this->datum=='' || $this->datum=='--') ? 'NOW()' : "'".$this->datum."'").",
      projekt='{$this->projekt}',
      prefix='{$this->prefix}',
      reisekostenart='{$this->reisekostenart}',
      belegnr='{$this->belegnr}',
      bearbeiter='{$this->bearbeiter}',
      auftrag='{$this->auftrag}',
      auftragid=".((is_numeric($this->auftragid)) ? $this->auftragid : '0').",
      freitext='{$this->freitext}',
      status='{$this->status}',
      adresse=".((is_numeric($this->adresse)) ? $this->adresse : '0').",
      mitarbeiter=".((is_numeric($this->mitarbeiter)) ? $this->mitarbeiter : '0').",
      name='{$this->name}',
      abteilung='{$this->abteilung}',
      unterabteilung='{$this->unterabteilung}',
      strasse='{$this->strasse}',
      adresszusatz='{$this->adresszusatz}',
      ansprechpartner='{$this->ansprechpartner}',
      plz='{$this->plz}',
      ort='{$this->ort}',
      land='{$this->land}',
      ustid='{$this->ustid}',
      email='{$this->email}',
      telefon='{$this->telefon}',
      telefax='{$this->telefax}',
      betreff='{$this->betreff}',
      kundennummer='{$this->kundennummer}',
      versandart='{$this->versandart}',
      versand='{$this->versand}',
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
      versendet=".((is_numeric($this->versendet)) ? $this->versendet : '0').",
      versendet_am=".(($this->versendet_am=='' || $this->versendet_am=='--') ? 'NOW()' : "'".$this->versendet_am."'").",
      versendet_per='{$this->versendet_per}',
      versendet_durch='{$this->versendet_durch}',
      inbearbeitung_user=".((is_numeric($this->inbearbeitung_user)) ? $this->inbearbeitung_user : '0').",
      logdatei=".(($this->logdatei=='' || $this->logdatei=='--') ? 'NOW()' : "'".$this->logdatei."'").",
      ohne_briefpapier=".((is_numeric($this->ohne_briefpapier)) ? $this->ohne_briefpapier : '0').",
      schreibschutz=".((is_numeric($this->schreibschutz)) ? $this->schreibschutz : '0').",
      pdfarchiviert=".((is_numeric($this->pdfarchiviert)) ? $this->pdfarchiviert : '0').",
      pdfarchiviertversion=".((is_numeric($this->pdfarchiviertversion)) ? $this->pdfarchiviertversion : '0').",
      anlass='{$this->anlass}',
      von=".(($this->von=='' || $this->von=='--') ? 'NOW()' : "'".$this->von."'").",
      bis=".(($this->bis=='' || $this->bis=='--') ? 'NOW()' : "'".$this->bis."'").",
      von_zeit='{$this->von_zeit}',
      bis_zeit='{$this->bis_zeit}',
      ust_befreit=".((is_numeric($this->ust_befreit)) ? $this->ust_befreit : '0')."
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

    $sql = "DELETE FROM reisekosten WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->datum="";
    $this->projekt="";
    $this->prefix="";
    $this->reisekostenart="";
    $this->belegnr="";
    $this->bearbeiter="";
    $this->auftrag="";
    $this->auftragid="";
    $this->freitext="";
    $this->status="";
    $this->adresse="";
    $this->mitarbeiter="";
    $this->name="";
    $this->abteilung="";
    $this->unterabteilung="";
    $this->strasse="";
    $this->adresszusatz="";
    $this->ansprechpartner="";
    $this->plz="";
    $this->ort="";
    $this->land="";
    $this->ustid="";
    $this->email="";
    $this->telefon="";
    $this->telefax="";
    $this->betreff="";
    $this->kundennummer="";
    $this->versandart="";
    $this->versand="";
    $this->firma="";
    $this->versendet="";
    $this->versendet_am="";
    $this->versendet_per="";
    $this->versendet_durch="";
    $this->inbearbeitung_user="";
    $this->logdatei="";
    $this->ohne_briefpapier="";
    $this->schreibschutz="";
    $this->pdfarchiviert="";
    $this->pdfarchiviertversion="";
    $this->anlass="";
    $this->von="";
    $this->bis="";
    $this->von_zeit="";
    $this->bis_zeit="";
    $this->ust_befreit="";
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
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetPrefix($value) { $this->prefix=$value; }
  function GetPrefix() { return $this->prefix; }
  function SetReisekostenart($value) { $this->reisekostenart=$value; }
  function GetReisekostenart() { return $this->reisekostenart; }
  function SetBelegnr($value) { $this->belegnr=$value; }
  function GetBelegnr() { return $this->belegnr; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetAuftrag($value) { $this->auftrag=$value; }
  function GetAuftrag() { return $this->auftrag; }
  function SetAuftragid($value) { $this->auftragid=$value; }
  function GetAuftragid() { return $this->auftragid; }
  function SetFreitext($value) { $this->freitext=$value; }
  function GetFreitext() { return $this->freitext; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetMitarbeiter($value) { $this->mitarbeiter=$value; }
  function GetMitarbeiter() { return $this->mitarbeiter; }
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetAbteilung($value) { $this->abteilung=$value; }
  function GetAbteilung() { return $this->abteilung; }
  function SetUnterabteilung($value) { $this->unterabteilung=$value; }
  function GetUnterabteilung() { return $this->unterabteilung; }
  function SetStrasse($value) { $this->strasse=$value; }
  function GetStrasse() { return $this->strasse; }
  function SetAdresszusatz($value) { $this->adresszusatz=$value; }
  function GetAdresszusatz() { return $this->adresszusatz; }
  function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  function GetAnsprechpartner() { return $this->ansprechpartner; }
  function SetPlz($value) { $this->plz=$value; }
  function GetPlz() { return $this->plz; }
  function SetOrt($value) { $this->ort=$value; }
  function GetOrt() { return $this->ort; }
  function SetLand($value) { $this->land=$value; }
  function GetLand() { return $this->land; }
  function SetUstid($value) { $this->ustid=$value; }
  function GetUstid() { return $this->ustid; }
  function SetEmail($value) { $this->email=$value; }
  function GetEmail() { return $this->email; }
  function SetTelefon($value) { $this->telefon=$value; }
  function GetTelefon() { return $this->telefon; }
  function SetTelefax($value) { $this->telefax=$value; }
  function GetTelefax() { return $this->telefax; }
  function SetBetreff($value) { $this->betreff=$value; }
  function GetBetreff() { return $this->betreff; }
  function SetKundennummer($value) { $this->kundennummer=$value; }
  function GetKundennummer() { return $this->kundennummer; }
  function SetVersandart($value) { $this->versandart=$value; }
  function GetVersandart() { return $this->versandart; }
  function SetVersand($value) { $this->versand=$value; }
  function GetVersand() { return $this->versand; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetVersendet($value) { $this->versendet=$value; }
  function GetVersendet() { return $this->versendet; }
  function SetVersendet_Am($value) { $this->versendet_am=$value; }
  function GetVersendet_Am() { return $this->versendet_am; }
  function SetVersendet_Per($value) { $this->versendet_per=$value; }
  function GetVersendet_Per() { return $this->versendet_per; }
  function SetVersendet_Durch($value) { $this->versendet_durch=$value; }
  function GetVersendet_Durch() { return $this->versendet_durch; }
  function SetInbearbeitung_User($value) { $this->inbearbeitung_user=$value; }
  function GetInbearbeitung_User() { return $this->inbearbeitung_user; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetOhne_Briefpapier($value) { $this->ohne_briefpapier=$value; }
  function GetOhne_Briefpapier() { return $this->ohne_briefpapier; }
  function SetSchreibschutz($value) { $this->schreibschutz=$value; }
  function GetSchreibschutz() { return $this->schreibschutz; }
  function SetPdfarchiviert($value) { $this->pdfarchiviert=$value; }
  function GetPdfarchiviert() { return $this->pdfarchiviert; }
  function SetPdfarchiviertversion($value) { $this->pdfarchiviertversion=$value; }
  function GetPdfarchiviertversion() { return $this->pdfarchiviertversion; }
  function SetAnlass($value) { $this->anlass=$value; }
  function GetAnlass() { return $this->anlass; }
  function SetVon($value) { $this->von=$value; }
  function GetVon() { return $this->von; }
  function SetBis($value) { $this->bis=$value; }
  function GetBis() { return $this->bis; }
  function SetVon_Zeit($value) { $this->von_zeit=$value; }
  function GetVon_Zeit() { return $this->von_zeit; }
  function SetBis_Zeit($value) { $this->bis_zeit=$value; }
  function GetBis_Zeit() { return $this->bis_zeit; }
  function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  function GetUst_Befreit() { return $this->ust_befreit; }

}

?>