<?php

class ObjGenGruppen
{

  private  $id;
  private  $name;
  private  $kennziffer;
  private  $internebemerkung;
  private  $grundrabatt;
  private  $rabatt1;
  private  $rabatt2;
  private  $rabatt3;
  private  $rabatt4;
  private  $rabatt5;
  private  $bonus1;
  private  $bonus1_ab;
  private  $bonus2;
  private  $bonus2_ab;
  private  $bonus3;
  private  $bonus3_ab;
  private  $bonus4;
  private  $bonus4_ab;
  private  $bonus5;
  private  $bonus5_ab;
  private  $bonus6;
  private  $bonus6_ab;
  private  $bonus7;
  private  $bonus7_ab;
  private  $bonus8;
  private  $bonus8_ab;
  private  $bonus9;
  private  $bonus9_ab;
  private  $bonus10;
  private  $bonus10_ab;
  private  $zahlungszieltage;
  private  $zahlungszielskonto;
  private  $zahlungszieltageskonto;
  private  $portoartikel;
  private  $portofreiab;
  private  $erweiterteoptionen;
  private  $zentralerechnung;
  private  $gruppe;
  private  $preisgruppe;
  private  $verbandsgruppe;
  private  $rechnung_name;
  private  $rechnung_strasse;
  private  $rechnung_ort;
  private  $rechnung_plz;
  private  $rechnung_abteilung;
  private  $rechnung_land;
  private  $rechnung_email;
  private  $webid;
  private  $art;
  private  $provision;
  private  $portofrei_aktiv;
  private  $zentralregulierung;
  private  $rechnung_periode;
  private  $rechnung_anzahlpapier;
  private  $rechnung_permail;
  private  $kundennummer;
  private  $dta_aktiv;
  private  $dta_periode;
  private  $dta_dateiname;
  private  $dta_mail;
  private  $dta_mail_betreff;
  private  $dta_mail_text;
  private  $dta_variante;
  private  $partnerid;
  private  $dtavariablen;
  private  $sonderrabatt_skonto;

  public $app;            //application object 

  public function ObjGenGruppen($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM gruppen WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->name=$result[name];
    $this->kennziffer=$result[kennziffer];
    $this->internebemerkung=$result[internebemerkung];
    $this->grundrabatt=$result[grundrabatt];
    $this->rabatt1=$result[rabatt1];
    $this->rabatt2=$result[rabatt2];
    $this->rabatt3=$result[rabatt3];
    $this->rabatt4=$result[rabatt4];
    $this->rabatt5=$result[rabatt5];
    $this->bonus1=$result[bonus1];
    $this->bonus1_ab=$result[bonus1_ab];
    $this->bonus2=$result[bonus2];
    $this->bonus2_ab=$result[bonus2_ab];
    $this->bonus3=$result[bonus3];
    $this->bonus3_ab=$result[bonus3_ab];
    $this->bonus4=$result[bonus4];
    $this->bonus4_ab=$result[bonus4_ab];
    $this->bonus5=$result[bonus5];
    $this->bonus5_ab=$result[bonus5_ab];
    $this->bonus6=$result[bonus6];
    $this->bonus6_ab=$result[bonus6_ab];
    $this->bonus7=$result[bonus7];
    $this->bonus7_ab=$result[bonus7_ab];
    $this->bonus8=$result[bonus8];
    $this->bonus8_ab=$result[bonus8_ab];
    $this->bonus9=$result[bonus9];
    $this->bonus9_ab=$result[bonus9_ab];
    $this->bonus10=$result[bonus10];
    $this->bonus10_ab=$result[bonus10_ab];
    $this->zahlungszieltage=$result[zahlungszieltage];
    $this->zahlungszielskonto=$result[zahlungszielskonto];
    $this->zahlungszieltageskonto=$result[zahlungszieltageskonto];
    $this->portoartikel=$result[portoartikel];
    $this->portofreiab=$result[portofreiab];
    $this->erweiterteoptionen=$result[erweiterteoptionen];
    $this->zentralerechnung=$result[zentralerechnung];
    $this->gruppe=$result[gruppe];
    $this->preisgruppe=$result[preisgruppe];
    $this->verbandsgruppe=$result[verbandsgruppe];
    $this->rechnung_name=$result[rechnung_name];
    $this->rechnung_strasse=$result[rechnung_strasse];
    $this->rechnung_ort=$result[rechnung_ort];
    $this->rechnung_plz=$result[rechnung_plz];
    $this->rechnung_abteilung=$result[rechnung_abteilung];
    $this->rechnung_land=$result[rechnung_land];
    $this->rechnung_email=$result[rechnung_email];
    $this->webid=$result[webid];
    $this->art=$result[art];
    $this->provision=$result[provision];
    $this->portofrei_aktiv=$result[portofrei_aktiv];
    $this->zentralregulierung=$result[zentralregulierung];
    $this->rechnung_periode=$result[rechnung_periode];
    $this->rechnung_anzahlpapier=$result[rechnung_anzahlpapier];
    $this->rechnung_permail=$result[rechnung_permail];
    $this->kundennummer=$result[kundennummer];
    $this->dta_aktiv=$result[dta_aktiv];
    $this->dta_periode=$result[dta_periode];
    $this->dta_dateiname=$result[dta_dateiname];
    $this->dta_mail=$result[dta_mail];
    $this->dta_mail_betreff=$result[dta_mail_betreff];
    $this->dta_mail_text=$result[dta_mail_text];
    $this->dta_variante=$result[dta_variante];
    $this->partnerid=$result[partnerid];
    $this->dtavariablen=$result[dtavariablen];
    $this->sonderrabatt_skonto=$result[sonderrabatt_skonto];
  }

  public function Create()
  {
    $sql = "INSERT INTO gruppen (id,name,kennziffer,internebemerkung,grundrabatt,rabatt1,rabatt2,rabatt3,rabatt4,rabatt5,bonus1,bonus1_ab,bonus2,bonus2_ab,bonus3,bonus3_ab,bonus4,bonus4_ab,bonus5,bonus5_ab,bonus6,bonus6_ab,bonus7,bonus7_ab,bonus8,bonus8_ab,bonus9,bonus9_ab,bonus10,bonus10_ab,zahlungszieltage,zahlungszielskonto,zahlungszieltageskonto,portoartikel,portofreiab,erweiterteoptionen,zentralerechnung,gruppe,preisgruppe,verbandsgruppe,rechnung_name,rechnung_strasse,rechnung_ort,rechnung_plz,rechnung_abteilung,rechnung_land,rechnung_email,webid,art,provision,portofrei_aktiv,zentralregulierung,rechnung_periode,rechnung_anzahlpapier,rechnung_permail,kundennummer,dta_aktiv,dta_periode,dta_dateiname,dta_mail,dta_mail_betreff,dta_mail_text,dta_variante,partnerid,dtavariablen,sonderrabatt_skonto)
      VALUES('','{$this->name}','{$this->kennziffer}','{$this->internebemerkung}','{$this->grundrabatt}','{$this->rabatt1}','{$this->rabatt2}','{$this->rabatt3}','{$this->rabatt4}','{$this->rabatt5}','{$this->bonus1}','{$this->bonus1_ab}','{$this->bonus2}','{$this->bonus2_ab}','{$this->bonus3}','{$this->bonus3_ab}','{$this->bonus4}','{$this->bonus4_ab}','{$this->bonus5}','{$this->bonus5_ab}','{$this->bonus6}','{$this->bonus6_ab}','{$this->bonus7}','{$this->bonus7_ab}','{$this->bonus8}','{$this->bonus8_ab}','{$this->bonus9}','{$this->bonus9_ab}','{$this->bonus10}','{$this->bonus10_ab}','{$this->zahlungszieltage}','{$this->zahlungszielskonto}','{$this->zahlungszieltageskonto}','{$this->portoartikel}','{$this->portofreiab}','{$this->erweiterteoptionen}','{$this->zentralerechnung}','{$this->gruppe}','{$this->preisgruppe}','{$this->verbandsgruppe}','{$this->rechnung_name}','{$this->rechnung_strasse}','{$this->rechnung_ort}','{$this->rechnung_plz}','{$this->rechnung_abteilung}','{$this->rechnung_land}','{$this->rechnung_email}','{$this->webid}','{$this->art}','{$this->provision}','{$this->portofrei_aktiv}','{$this->zentralregulierung}','{$this->rechnung_periode}','{$this->rechnung_anzahlpapier}','{$this->rechnung_permail}','{$this->kundennummer}','{$this->dta_aktiv}','{$this->dta_periode}','{$this->dta_dateiname}','{$this->dta_mail}','{$this->dta_mail_betreff}','{$this->dta_mail_text}','{$this->dta_variante}','{$this->partnerid}','{$this->dtavariablen}','{$this->sonderrabatt_skonto}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE gruppen SET
      name='{$this->name}',
      kennziffer='{$this->kennziffer}',
      internebemerkung='{$this->internebemerkung}',
      grundrabatt='{$this->grundrabatt}',
      rabatt1='{$this->rabatt1}',
      rabatt2='{$this->rabatt2}',
      rabatt3='{$this->rabatt3}',
      rabatt4='{$this->rabatt4}',
      rabatt5='{$this->rabatt5}',
      bonus1='{$this->bonus1}',
      bonus1_ab='{$this->bonus1_ab}',
      bonus2='{$this->bonus2}',
      bonus2_ab='{$this->bonus2_ab}',
      bonus3='{$this->bonus3}',
      bonus3_ab='{$this->bonus3_ab}',
      bonus4='{$this->bonus4}',
      bonus4_ab='{$this->bonus4_ab}',
      bonus5='{$this->bonus5}',
      bonus5_ab='{$this->bonus5_ab}',
      bonus6='{$this->bonus6}',
      bonus6_ab='{$this->bonus6_ab}',
      bonus7='{$this->bonus7}',
      bonus7_ab='{$this->bonus7_ab}',
      bonus8='{$this->bonus8}',
      bonus8_ab='{$this->bonus8_ab}',
      bonus9='{$this->bonus9}',
      bonus9_ab='{$this->bonus9_ab}',
      bonus10='{$this->bonus10}',
      bonus10_ab='{$this->bonus10_ab}',
      zahlungszieltage='{$this->zahlungszieltage}',
      zahlungszielskonto='{$this->zahlungszielskonto}',
      zahlungszieltageskonto='{$this->zahlungszieltageskonto}',
      portoartikel='{$this->portoartikel}',
      portofreiab='{$this->portofreiab}',
      erweiterteoptionen='{$this->erweiterteoptionen}',
      zentralerechnung='{$this->zentralerechnung}',
      gruppe='{$this->gruppe}',
      preisgruppe='{$this->preisgruppe}',
      verbandsgruppe='{$this->verbandsgruppe}',
      rechnung_name='{$this->rechnung_name}',
      rechnung_strasse='{$this->rechnung_strasse}',
      rechnung_ort='{$this->rechnung_ort}',
      rechnung_plz='{$this->rechnung_plz}',
      rechnung_abteilung='{$this->rechnung_abteilung}',
      rechnung_land='{$this->rechnung_land}',
      rechnung_email='{$this->rechnung_email}',
      webid='{$this->webid}',
      art='{$this->art}',
      provision='{$this->provision}',
      portofrei_aktiv='{$this->portofrei_aktiv}',
      zentralregulierung='{$this->zentralregulierung}',
      rechnung_periode='{$this->rechnung_periode}',
      rechnung_anzahlpapier='{$this->rechnung_anzahlpapier}',
      rechnung_permail='{$this->rechnung_permail}',
      kundennummer='{$this->kundennummer}',
      dta_aktiv='{$this->dta_aktiv}',
      dta_periode='{$this->dta_periode}',
      dta_dateiname='{$this->dta_dateiname}',
      dta_mail='{$this->dta_mail}',
      dta_mail_betreff='{$this->dta_mail_betreff}',
      dta_mail_text='{$this->dta_mail_text}',
      dta_variante='{$this->dta_variante}',
      partnerid='{$this->partnerid}',
      dtavariablen='{$this->dtavariablen}',
      sonderrabatt_skonto='{$this->sonderrabatt_skonto}'
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

    $sql = "DELETE FROM gruppen WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->name="";
    $this->kennziffer="";
    $this->internebemerkung="";
    $this->grundrabatt="";
    $this->rabatt1="";
    $this->rabatt2="";
    $this->rabatt3="";
    $this->rabatt4="";
    $this->rabatt5="";
    $this->bonus1="";
    $this->bonus1_ab="";
    $this->bonus2="";
    $this->bonus2_ab="";
    $this->bonus3="";
    $this->bonus3_ab="";
    $this->bonus4="";
    $this->bonus4_ab="";
    $this->bonus5="";
    $this->bonus5_ab="";
    $this->bonus6="";
    $this->bonus6_ab="";
    $this->bonus7="";
    $this->bonus7_ab="";
    $this->bonus8="";
    $this->bonus8_ab="";
    $this->bonus9="";
    $this->bonus9_ab="";
    $this->bonus10="";
    $this->bonus10_ab="";
    $this->zahlungszieltage="";
    $this->zahlungszielskonto="";
    $this->zahlungszieltageskonto="";
    $this->portoartikel="";
    $this->portofreiab="";
    $this->erweiterteoptionen="";
    $this->zentralerechnung="";
    $this->gruppe="";
    $this->preisgruppe="";
    $this->verbandsgruppe="";
    $this->rechnung_name="";
    $this->rechnung_strasse="";
    $this->rechnung_ort="";
    $this->rechnung_plz="";
    $this->rechnung_abteilung="";
    $this->rechnung_land="";
    $this->rechnung_email="";
    $this->webid="";
    $this->art="";
    $this->provision="";
    $this->portofrei_aktiv="";
    $this->zentralregulierung="";
    $this->rechnung_periode="";
    $this->rechnung_anzahlpapier="";
    $this->rechnung_permail="";
    $this->kundennummer="";
    $this->dta_aktiv="";
    $this->dta_periode="";
    $this->dta_dateiname="";
    $this->dta_mail="";
    $this->dta_mail_betreff="";
    $this->dta_mail_text="";
    $this->dta_variante="";
    $this->partnerid="";
    $this->dtavariablen="";
    $this->sonderrabatt_skonto="";
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
  function SetKennziffer($value) { $this->kennziffer=$value; }
  function GetKennziffer() { return $this->kennziffer; }
  function SetInternebemerkung($value) { $this->internebemerkung=$value; }
  function GetInternebemerkung() { return $this->internebemerkung; }
  function SetGrundrabatt($value) { $this->grundrabatt=$value; }
  function GetGrundrabatt() { return $this->grundrabatt; }
  function SetRabatt1($value) { $this->rabatt1=$value; }
  function GetRabatt1() { return $this->rabatt1; }
  function SetRabatt2($value) { $this->rabatt2=$value; }
  function GetRabatt2() { return $this->rabatt2; }
  function SetRabatt3($value) { $this->rabatt3=$value; }
  function GetRabatt3() { return $this->rabatt3; }
  function SetRabatt4($value) { $this->rabatt4=$value; }
  function GetRabatt4() { return $this->rabatt4; }
  function SetRabatt5($value) { $this->rabatt5=$value; }
  function GetRabatt5() { return $this->rabatt5; }
  function SetBonus1($value) { $this->bonus1=$value; }
  function GetBonus1() { return $this->bonus1; }
  function SetBonus1_Ab($value) { $this->bonus1_ab=$value; }
  function GetBonus1_Ab() { return $this->bonus1_ab; }
  function SetBonus2($value) { $this->bonus2=$value; }
  function GetBonus2() { return $this->bonus2; }
  function SetBonus2_Ab($value) { $this->bonus2_ab=$value; }
  function GetBonus2_Ab() { return $this->bonus2_ab; }
  function SetBonus3($value) { $this->bonus3=$value; }
  function GetBonus3() { return $this->bonus3; }
  function SetBonus3_Ab($value) { $this->bonus3_ab=$value; }
  function GetBonus3_Ab() { return $this->bonus3_ab; }
  function SetBonus4($value) { $this->bonus4=$value; }
  function GetBonus4() { return $this->bonus4; }
  function SetBonus4_Ab($value) { $this->bonus4_ab=$value; }
  function GetBonus4_Ab() { return $this->bonus4_ab; }
  function SetBonus5($value) { $this->bonus5=$value; }
  function GetBonus5() { return $this->bonus5; }
  function SetBonus5_Ab($value) { $this->bonus5_ab=$value; }
  function GetBonus5_Ab() { return $this->bonus5_ab; }
  function SetBonus6($value) { $this->bonus6=$value; }
  function GetBonus6() { return $this->bonus6; }
  function SetBonus6_Ab($value) { $this->bonus6_ab=$value; }
  function GetBonus6_Ab() { return $this->bonus6_ab; }
  function SetBonus7($value) { $this->bonus7=$value; }
  function GetBonus7() { return $this->bonus7; }
  function SetBonus7_Ab($value) { $this->bonus7_ab=$value; }
  function GetBonus7_Ab() { return $this->bonus7_ab; }
  function SetBonus8($value) { $this->bonus8=$value; }
  function GetBonus8() { return $this->bonus8; }
  function SetBonus8_Ab($value) { $this->bonus8_ab=$value; }
  function GetBonus8_Ab() { return $this->bonus8_ab; }
  function SetBonus9($value) { $this->bonus9=$value; }
  function GetBonus9() { return $this->bonus9; }
  function SetBonus9_Ab($value) { $this->bonus9_ab=$value; }
  function GetBonus9_Ab() { return $this->bonus9_ab; }
  function SetBonus10($value) { $this->bonus10=$value; }
  function GetBonus10() { return $this->bonus10; }
  function SetBonus10_Ab($value) { $this->bonus10_ab=$value; }
  function GetBonus10_Ab() { return $this->bonus10_ab; }
  function SetZahlungszieltage($value) { $this->zahlungszieltage=$value; }
  function GetZahlungszieltage() { return $this->zahlungszieltage; }
  function SetZahlungszielskonto($value) { $this->zahlungszielskonto=$value; }
  function GetZahlungszielskonto() { return $this->zahlungszielskonto; }
  function SetZahlungszieltageskonto($value) { $this->zahlungszieltageskonto=$value; }
  function GetZahlungszieltageskonto() { return $this->zahlungszieltageskonto; }
  function SetPortoartikel($value) { $this->portoartikel=$value; }
  function GetPortoartikel() { return $this->portoartikel; }
  function SetPortofreiab($value) { $this->portofreiab=$value; }
  function GetPortofreiab() { return $this->portofreiab; }
  function SetErweiterteoptionen($value) { $this->erweiterteoptionen=$value; }
  function GetErweiterteoptionen() { return $this->erweiterteoptionen; }
  function SetZentralerechnung($value) { $this->zentralerechnung=$value; }
  function GetZentralerechnung() { return $this->zentralerechnung; }
  function SetGruppe($value) { $this->gruppe=$value; }
  function GetGruppe() { return $this->gruppe; }
  function SetPreisgruppe($value) { $this->preisgruppe=$value; }
  function GetPreisgruppe() { return $this->preisgruppe; }
  function SetVerbandsgruppe($value) { $this->verbandsgruppe=$value; }
  function GetVerbandsgruppe() { return $this->verbandsgruppe; }
  function SetRechnung_Name($value) { $this->rechnung_name=$value; }
  function GetRechnung_Name() { return $this->rechnung_name; }
  function SetRechnung_Strasse($value) { $this->rechnung_strasse=$value; }
  function GetRechnung_Strasse() { return $this->rechnung_strasse; }
  function SetRechnung_Ort($value) { $this->rechnung_ort=$value; }
  function GetRechnung_Ort() { return $this->rechnung_ort; }
  function SetRechnung_Plz($value) { $this->rechnung_plz=$value; }
  function GetRechnung_Plz() { return $this->rechnung_plz; }
  function SetRechnung_Abteilung($value) { $this->rechnung_abteilung=$value; }
  function GetRechnung_Abteilung() { return $this->rechnung_abteilung; }
  function SetRechnung_Land($value) { $this->rechnung_land=$value; }
  function GetRechnung_Land() { return $this->rechnung_land; }
  function SetRechnung_Email($value) { $this->rechnung_email=$value; }
  function GetRechnung_Email() { return $this->rechnung_email; }
  function SetWebid($value) { $this->webid=$value; }
  function GetWebid() { return $this->webid; }
  function SetArt($value) { $this->art=$value; }
  function GetArt() { return $this->art; }
  function SetProvision($value) { $this->provision=$value; }
  function GetProvision() { return $this->provision; }
  function SetPortofrei_Aktiv($value) { $this->portofrei_aktiv=$value; }
  function GetPortofrei_Aktiv() { return $this->portofrei_aktiv; }
  function SetZentralregulierung($value) { $this->zentralregulierung=$value; }
  function GetZentralregulierung() { return $this->zentralregulierung; }
  function SetRechnung_Periode($value) { $this->rechnung_periode=$value; }
  function GetRechnung_Periode() { return $this->rechnung_periode; }
  function SetRechnung_Anzahlpapier($value) { $this->rechnung_anzahlpapier=$value; }
  function GetRechnung_Anzahlpapier() { return $this->rechnung_anzahlpapier; }
  function SetRechnung_Permail($value) { $this->rechnung_permail=$value; }
  function GetRechnung_Permail() { return $this->rechnung_permail; }
  function SetKundennummer($value) { $this->kundennummer=$value; }
  function GetKundennummer() { return $this->kundennummer; }
  function SetDta_Aktiv($value) { $this->dta_aktiv=$value; }
  function GetDta_Aktiv() { return $this->dta_aktiv; }
  function SetDta_Periode($value) { $this->dta_periode=$value; }
  function GetDta_Periode() { return $this->dta_periode; }
  function SetDta_Dateiname($value) { $this->dta_dateiname=$value; }
  function GetDta_Dateiname() { return $this->dta_dateiname; }
  function SetDta_Mail($value) { $this->dta_mail=$value; }
  function GetDta_Mail() { return $this->dta_mail; }
  function SetDta_Mail_Betreff($value) { $this->dta_mail_betreff=$value; }
  function GetDta_Mail_Betreff() { return $this->dta_mail_betreff; }
  function SetDta_Mail_Text($value) { $this->dta_mail_text=$value; }
  function GetDta_Mail_Text() { return $this->dta_mail_text; }
  function SetDta_Variante($value) { $this->dta_variante=$value; }
  function GetDta_Variante() { return $this->dta_variante; }
  function SetPartnerid($value) { $this->partnerid=$value; }
  function GetPartnerid() { return $this->partnerid; }
  function SetDtavariablen($value) { $this->dtavariablen=$value; }
  function GetDtavariablen() { return $this->dtavariablen; }
  function SetSonderrabatt_Skonto($value) { $this->sonderrabatt_skonto=$value; }
  function GetSonderrabatt_Skonto() { return $this->sonderrabatt_skonto; }

}

?>