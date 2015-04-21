<?php

class ObjGenProjekt
{

  private  $id;
  private  $name;
  private  $abkuerzung;
  private  $verantwortlicher;
  private  $beschreibung;
  private  $sonstiges;
  private  $aktiv;
  private  $farbe;
  private  $autoversand;
  private  $checkok;
  private  $portocheck;
  private  $automailrechnung;
  private  $checkname;
  private  $zahlungserinnerung;
  private  $zahlungsmailbedinungen;
  private  $folgebestaetigung;
  private  $stornomail;
  private  $kundenfreigabe_loeschen;
  private  $autobestellung;
  private  $speziallieferschein;
  private  $lieferscheinbriefpapier;
  private  $speziallieferscheinbeschriftung;
  private  $firma;
  private  $geloescht;
  private  $logdatei;
  private  $reservierung;
  private  $gesamtstunden_max;
  private  $auftragid;
  private  $kunde;
  private  $oeffentlich;
  private  $verkaufszahlendiagram;

  public $app;            //application object 

  public function ObjGenProjekt($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM projekt WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->name=$result[name];
    $this->abkuerzung=$result[abkuerzung];
    $this->verantwortlicher=$result[verantwortlicher];
    $this->beschreibung=$result[beschreibung];
    $this->sonstiges=$result[sonstiges];
    $this->aktiv=$result[aktiv];
    $this->farbe=$result[farbe];
    $this->autoversand=$result[autoversand];
    $this->checkok=$result[checkok];
    $this->portocheck=$result[portocheck];
    $this->automailrechnung=$result[automailrechnung];
    $this->checkname=$result[checkname];
    $this->zahlungserinnerung=$result[zahlungserinnerung];
    $this->zahlungsmailbedinungen=$result[zahlungsmailbedinungen];
    $this->folgebestaetigung=$result[folgebestaetigung];
    $this->stornomail=$result[stornomail];
    $this->kundenfreigabe_loeschen=$result[kundenfreigabe_loeschen];
    $this->autobestellung=$result[autobestellung];
    $this->speziallieferschein=$result[speziallieferschein];
    $this->lieferscheinbriefpapier=$result[lieferscheinbriefpapier];
    $this->speziallieferscheinbeschriftung=$result[speziallieferscheinbeschriftung];
    $this->firma=$result[firma];
    $this->geloescht=$result[geloescht];
    $this->logdatei=$result[logdatei];
    $this->reservierung=$result[reservierung];
    $this->gesamtstunden_max=$result[gesamtstunden_max];
    $this->auftragid=$result[auftragid];
    $this->kunde=$result[kunde];
    $this->oeffentlich=$result[oeffentlich];
    $this->verkaufszahlendiagram=$result[verkaufszahlendiagram];
  }

  public function Create()
  {
    $sql = "INSERT INTO projekt (id,name,abkuerzung,verantwortlicher,beschreibung,sonstiges,aktiv,farbe,autoversand,checkok,portocheck,automailrechnung,checkname,zahlungserinnerung,zahlungsmailbedinungen,folgebestaetigung,stornomail,kundenfreigabe_loeschen,autobestellung,speziallieferschein,lieferscheinbriefpapier,speziallieferscheinbeschriftung,firma,geloescht,logdatei,reservierung,gesamtstunden_max,auftragid,kunde,oeffentlich,verkaufszahlendiagram)
      VALUES(DEFAULT, '{$this->name}', '{$this->abkuerzung}', '{$this->verantwortlicher}', '{$this->beschreibung}', '{$this->sonstiges}', '{$this->aktiv}', '{$this->farbe}', ".((is_numeric($this->autoversand)) ? $this->autoversand : '0').", ".((is_numeric($this->checkok)) ? $this->checkok : '0').", ".((is_numeric($this->portocheck)) ? $this->portocheck : '0').", ".((is_numeric($this->automailrechnung)) ? $this->automailrechnung : '0').", '{$this->checkname}', ".((is_numeric($this->zahlungserinnerung)) ? $this->zahlungserinnerung : '0').", '{$this->zahlungsmailbedinungen}', ".((is_numeric($this->folgebestaetigung)) ? $this->folgebestaetigung : '0').", ".((is_numeric($this->stornomail)) ? $this->stornomail : '0').", ".((is_numeric($this->kundenfreigabe_loeschen)) ? $this->kundenfreigabe_loeschen : '0').", ".((is_numeric($this->autobestellung)) ? $this->autobestellung : '0').", ".((is_numeric($this->speziallieferschein)) ? $this->speziallieferschein : '0').", ".((is_numeric($this->lieferscheinbriefpapier)) ? $this->lieferscheinbriefpapier : '0').", ".((is_numeric($this->speziallieferscheinbeschriftung)) ? $this->speziallieferscheinbeschriftung : '0').", ".((is_numeric($this->firma)) ? $this->firma : '0').", ".((is_numeric($this->geloescht)) ? $this->geloescht : '0').", '{$this->logdatei}', ".((is_numeric($this->reservierung)) ? $this->reservierung : '0').", ".((is_numeric($this->gesamtstunden_max)) ? $this->gesamtstunden_max : '0').", ".((is_numeric($this->auftragid)) ? $this->auftragid : '0').", ".((is_numeric($this->kunde)) ? $this->kunde : '0').", ".((is_numeric($this->oeffentlich)) ? $this->oeffentlich : '0').", ".((is_numeric($this->verkaufszahlendiagram)) ? $this->verkaufszahlendiagram : '0').")"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE projekt SET
      name='{$this->name}',
      abkuerzung='{$this->abkuerzung}',
      verantwortlicher='{$this->verantwortlicher}',
      beschreibung='{$this->beschreibung}',
      sonstiges='{$this->sonstiges}',
      aktiv='{$this->aktiv}',
      farbe='{$this->farbe}',
      autoversand=".((is_numeric($this->autoversand)) ? $this->autoversand : '0').",
      checkok=".((is_numeric($this->checkok)) ? $this->checkok : '0').",
      portocheck=".((is_numeric($this->portocheck)) ? $this->portocheck : '0').",
      automailrechnung=".((is_numeric($this->automailrechnung)) ? $this->automailrechnung : '0').",
      checkname='{$this->checkname}',
      zahlungserinnerung=".((is_numeric($this->zahlungserinnerung)) ? $this->zahlungserinnerung : '0').",
      zahlungsmailbedinungen='{$this->zahlungsmailbedinungen}',
      folgebestaetigung=".((is_numeric($this->folgebestaetigung)) ? $this->folgebestaetigung : '0').",
      stornomail=".((is_numeric($this->stornomail)) ? $this->stornomail : '0').",
      kundenfreigabe_loeschen=".((is_numeric($this->kundenfreigabe_loeschen)) ? $this->kundenfreigabe_loeschen : '0').",
      autobestellung=".((is_numeric($this->autobestellung)) ? $this->autobestellung : '0').",
      speziallieferschein=".((is_numeric($this->speziallieferschein)) ? $this->speziallieferschein : '0').",
      lieferscheinbriefpapier=".((is_numeric($this->lieferscheinbriefpapier)) ? $this->lieferscheinbriefpapier : '0').",
      speziallieferscheinbeschriftung=".((is_numeric($this->speziallieferscheinbeschriftung)) ? $this->speziallieferscheinbeschriftung : '0').",
      firma=".((is_numeric($this->firma)) ? $this->firma : '0').",
      geloescht=".((is_numeric($this->geloescht)) ? $this->geloescht : '0').",
      logdatei='{$this->logdatei}',
      reservierung=".((is_numeric($this->reservierung)) ? $this->reservierung : '0').",
      gesamtstunden_max=".((is_numeric($this->gesamtstunden_max)) ? $this->gesamtstunden_max : '0').",
      auftragid=".((is_numeric($this->auftragid)) ? $this->auftragid : '0').",
      kunde=".((is_numeric($this->kunde)) ? $this->kunde : '0').",
      oeffentlich=".((is_numeric($this->oeffentlich)) ? $this->oeffentlich : '0').",
      verkaufszahlendiagram=".((is_numeric($this->verkaufszahlendiagram)) ? $this->verkaufszahlendiagram : '0')."
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

    $sql = "DELETE FROM projekt WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->name="";
    $this->abkuerzung="";
    $this->verantwortlicher="";
    $this->beschreibung="";
    $this->sonstiges="";
    $this->aktiv="";
    $this->farbe="";
    $this->autoversand="";
    $this->checkok="";
    $this->portocheck="";
    $this->automailrechnung="";
    $this->checkname="";
    $this->zahlungserinnerung="";
    $this->zahlungsmailbedinungen="";
    $this->folgebestaetigung="";
    $this->stornomail="";
    $this->kundenfreigabe_loeschen="";
    $this->autobestellung="";
    $this->speziallieferschein="";
    $this->lieferscheinbriefpapier="";
    $this->speziallieferscheinbeschriftung="";
    $this->firma="";
    $this->geloescht="";
    $this->logdatei="";
    $this->reservierung="";
    $this->gesamtstunden_max="";
    $this->auftragid="";
    $this->kunde="";
    $this->oeffentlich="";
    $this->verkaufszahlendiagram="";
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
  function SetAbkuerzung($value) { $this->abkuerzung=$value; }
  function GetAbkuerzung() { return $this->abkuerzung; }
  function SetVerantwortlicher($value) { $this->verantwortlicher=$value; }
  function GetVerantwortlicher() { return $this->verantwortlicher; }
  function SetBeschreibung($value) { $this->beschreibung=$value; }
  function GetBeschreibung() { return $this->beschreibung; }
  function SetSonstiges($value) { $this->sonstiges=$value; }
  function GetSonstiges() { return $this->sonstiges; }
  function SetAktiv($value) { $this->aktiv=$value; }
  function GetAktiv() { return $this->aktiv; }
  function SetFarbe($value) { $this->farbe=$value; }
  function GetFarbe() { return $this->farbe; }
  function SetAutoversand($value) { $this->autoversand=$value; }
  function GetAutoversand() { return $this->autoversand; }
  function SetCheckok($value) { $this->checkok=$value; }
  function GetCheckok() { return $this->checkok; }
  function SetPortocheck($value) { $this->portocheck=$value; }
  function GetPortocheck() { return $this->portocheck; }
  function SetAutomailrechnung($value) { $this->automailrechnung=$value; }
  function GetAutomailrechnung() { return $this->automailrechnung; }
  function SetCheckname($value) { $this->checkname=$value; }
  function GetCheckname() { return $this->checkname; }
  function SetZahlungserinnerung($value) { $this->zahlungserinnerung=$value; }
  function GetZahlungserinnerung() { return $this->zahlungserinnerung; }
  function SetZahlungsmailbedinungen($value) { $this->zahlungsmailbedinungen=$value; }
  function GetZahlungsmailbedinungen() { return $this->zahlungsmailbedinungen; }
  function SetFolgebestaetigung($value) { $this->folgebestaetigung=$value; }
  function GetFolgebestaetigung() { return $this->folgebestaetigung; }
  function SetStornomail($value) { $this->stornomail=$value; }
  function GetStornomail() { return $this->stornomail; }
  function SetKundenfreigabe_Loeschen($value) { $this->kundenfreigabe_loeschen=$value; }
  function GetKundenfreigabe_Loeschen() { return $this->kundenfreigabe_loeschen; }
  function SetAutobestellung($value) { $this->autobestellung=$value; }
  function GetAutobestellung() { return $this->autobestellung; }
  function SetSpeziallieferschein($value) { $this->speziallieferschein=$value; }
  function GetSpeziallieferschein() { return $this->speziallieferschein; }
  function SetLieferscheinbriefpapier($value) { $this->lieferscheinbriefpapier=$value; }
  function GetLieferscheinbriefpapier() { return $this->lieferscheinbriefpapier; }
  function SetSpeziallieferscheinbeschriftung($value) { $this->speziallieferscheinbeschriftung=$value; }
  function GetSpeziallieferscheinbeschriftung() { return $this->speziallieferscheinbeschriftung; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetReservierung($value) { $this->reservierung=$value; }
  function GetReservierung() { return $this->reservierung; }
  function SetGesamtstunden_Max($value) { $this->gesamtstunden_max=$value; }
  function GetGesamtstunden_Max() { return $this->gesamtstunden_max; }
  function SetAuftragid($value) { $this->auftragid=$value; }
  function GetAuftragid() { return $this->auftragid; }
  function SetKunde($value) { $this->kunde=$value; }
  function GetKunde() { return $this->kunde; }
  function SetOeffentlich($value) { $this->oeffentlich=$value; }
  function GetOeffentlich() { return $this->oeffentlich; }
  function SetVerkaufszahlendiagram($value) { $this->verkaufszahlendiagram=$value; }
  function GetVerkaufszahlendiagram() { return $this->verkaufszahlendiagram; }

}

?>