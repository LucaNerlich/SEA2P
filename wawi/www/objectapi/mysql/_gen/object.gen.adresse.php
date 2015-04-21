<?php

class ObjGenAdresse
{

  private  $id;
  private  $typ;
  private  $marketingsperre;
  private  $trackingsperre;
  private  $rechnungsadresse;
  private  $sprache;
  private  $name;
  private  $abteilung;
  private  $unterabteilung;
  private  $ansprechpartner;
  private  $land;
  private  $strasse;
  private  $ort;
  private  $plz;
  private  $telefon;
  private  $telefax;
  private  $mobil;
  private  $email;
  private  $ustid;
  private  $ust_befreit;
  private  $passwort_gesendet;
  private  $sonstiges;
  private  $adresszusatz;
  private  $kundenfreigabe;
  private  $steuer;
  private  $logdatei;
  private  $kundennummer;
  private  $lieferantennummer;
  private  $mitarbeiternummer;
  private  $konto;
  private  $blz;
  private  $bank;
  private  $inhaber;
  private  $swift;
  private  $iban;
  private  $waehrung;
  private  $paypal;
  private  $paypalinhaber;
  private  $paypalwaehrung;
  private  $projekt;
  private  $partner;
  private  $zahlungsweise;
  private  $zahlungszieltage;
  private  $zahlungszieltageskonto;
  private  $zahlungszielskonto;
  private  $versandart;
  private  $kundennummerlieferant;
  private  $zahlungsweiselieferant;
  private  $zahlungszieltagelieferant;
  private  $zahlungszieltageskontolieferant;
  private  $zahlungszielskontolieferant;
  private  $versandartlieferant;
  private  $geloescht;
  private  $firma;
  private  $webid;
  private  $internetseite;
  private  $vorname;
  private  $kalender_aufgaben;
  private  $titel;
  private  $anschreiben;
  private  $logfile;
  private  $mlmaktiv;
  private  $mlmvertragsbeginn;
  private  $geburtstag;
  private  $liefersperre;
  private  $mlmpositionierung;
  private  $steuernummer;
  private  $steuerbefreit;
  private  $mlmmitmwst;
  private  $mlmabrechnung;
  private  $sponsor;
  private  $geworbenvon;
  private  $liefersperregrund;
  private  $verrechnungskontoreisekosten;
  private  $rolledatum;
  private  $mlmwaehrungauszahlung;
  private  $mlmfestsetzen;
  private  $mlmmindestpunkte;
  private  $mlmwartekonto;
  private  $abweichende_rechnungsadresse;
  private  $rechnung_vorname;
  private  $rechnung_name;
  private  $rechnung_titel;
  private  $rechnung_typ;
  private  $rechnung_strasse;
  private  $rechnung_ort;
  private  $rechnung_land;
  private  $rechnung_abteilung;
  private  $rechnung_unterabteilung;
  private  $rechnung_adresszusatz;
  private  $rechnung_telefon;
  private  $rechnung_telefax;
  private  $rechnung_anschreiben;
  private  $rechnung_email;
  private  $rechnung_plz;
  private  $rechnung_ansprechpartner;
  private  $kennung;
  private  $vertrieb;
  private  $innendienst;
  private  $rabatt;
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
  private  $verbandsnummer;
  private  $portofreiab;
  private  $zahlungskonditionen_festschreiben;
  private  $rabatte_festschreiben;
  private  $provision;
  private  $portofrei_aktiv;
  private  $rabattinformation;
  private  $freifeld1;
  private  $rechnung_periode;
  private  $rechnung_anzahlpapier;
  private  $rechnung_permail;
  private  $usereditid;
  private  $useredittimestamp;
  private  $infoauftragserfassung;
  private  $mandatsreferenz;
  private  $glaeubigeridentnr;
  private  $kreditlimit;
  private  $tour;
  private  $freifeld2;
  private  $freifeld3;
  private  $abweichendeemailab;
  private  $mlmfestsetzenbis;
  private  $filiale;
  private  $mlmlizenzgebuehrbis;
  private  $mandatsreferenzdatum;
  private  $mandatsreferenzaenderung;
  private  $sachkonto;
  private  $mlmauszahlungprojekt;
  private  $folgebestaetigungsperre;

  public $app;            //application object 

  public function ObjGenAdresse($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->typ=$result[typ];
    $this->marketingsperre=$result[marketingsperre];
    $this->trackingsperre=$result[trackingsperre];
    $this->rechnungsadresse=$result[rechnungsadresse];
    $this->sprache=$result[sprache];
    $this->name=$result[name];
    $this->abteilung=$result[abteilung];
    $this->unterabteilung=$result[unterabteilung];
    $this->ansprechpartner=$result[ansprechpartner];
    $this->land=$result[land];
    $this->strasse=$result[strasse];
    $this->ort=$result[ort];
    $this->plz=$result[plz];
    $this->telefon=$result[telefon];
    $this->telefax=$result[telefax];
    $this->mobil=$result[mobil];
    $this->email=$result[email];
    $this->ustid=$result[ustid];
    $this->ust_befreit=$result[ust_befreit];
    $this->passwort_gesendet=$result[passwort_gesendet];
    $this->sonstiges=$result[sonstiges];
    $this->adresszusatz=$result[adresszusatz];
    $this->kundenfreigabe=$result[kundenfreigabe];
    $this->steuer=$result[steuer];
    $this->logdatei=$result[logdatei];
    $this->kundennummer=$result[kundennummer];
    $this->lieferantennummer=$result[lieferantennummer];
    $this->mitarbeiternummer=$result[mitarbeiternummer];
    $this->konto=$result[konto];
    $this->blz=$result[blz];
    $this->bank=$result[bank];
    $this->inhaber=$result[inhaber];
    $this->swift=$result[swift];
    $this->iban=$result[iban];
    $this->waehrung=$result[waehrung];
    $this->paypal=$result[paypal];
    $this->paypalinhaber=$result[paypalinhaber];
    $this->paypalwaehrung=$result[paypalwaehrung];
    $this->projekt=$result[projekt];
    $this->partner=$result[partner];
    $this->zahlungsweise=$result[zahlungsweise];
    $this->zahlungszieltage=$result[zahlungszieltage];
    $this->zahlungszieltageskonto=$result[zahlungszieltageskonto];
    $this->zahlungszielskonto=$result[zahlungszielskonto];
    $this->versandart=$result[versandart];
    $this->kundennummerlieferant=$result[kundennummerlieferant];
    $this->zahlungsweiselieferant=$result[zahlungsweiselieferant];
    $this->zahlungszieltagelieferant=$result[zahlungszieltagelieferant];
    $this->zahlungszieltageskontolieferant=$result[zahlungszieltageskontolieferant];
    $this->zahlungszielskontolieferant=$result[zahlungszielskontolieferant];
    $this->versandartlieferant=$result[versandartlieferant];
    $this->geloescht=$result[geloescht];
    $this->firma=$result[firma];
    $this->webid=$result[webid];
    $this->internetseite=$result[internetseite];
    $this->vorname=$result[vorname];
    $this->kalender_aufgaben=$result[kalender_aufgaben];
    $this->titel=$result[titel];
    $this->anschreiben=$result[anschreiben];
    $this->logfile=$result[logfile];
    $this->mlmaktiv=$result[mlmaktiv];
    $this->mlmvertragsbeginn=$result[mlmvertragsbeginn];
    $this->geburtstag=$result[geburtstag];
    $this->liefersperre=$result[liefersperre];
    $this->mlmpositionierung=$result[mlmpositionierung];
    $this->steuernummer=$result[steuernummer];
    $this->steuerbefreit=$result[steuerbefreit];
    $this->mlmmitmwst=$result[mlmmitmwst];
    $this->mlmabrechnung=$result[mlmabrechnung];
    $this->sponsor=$result[sponsor];
    $this->geworbenvon=$result[geworbenvon];
    $this->liefersperregrund=$result[liefersperregrund];
    $this->verrechnungskontoreisekosten=$result[verrechnungskontoreisekosten];
    $this->rolledatum=$result[rolledatum];
    $this->mlmwaehrungauszahlung=$result[mlmwaehrungauszahlung];
    $this->mlmfestsetzen=$result[mlmfestsetzen];
    $this->mlmmindestpunkte=$result[mlmmindestpunkte];
    $this->mlmwartekonto=$result[mlmwartekonto];
    $this->abweichende_rechnungsadresse=$result[abweichende_rechnungsadresse];
    $this->rechnung_vorname=$result[rechnung_vorname];
    $this->rechnung_name=$result[rechnung_name];
    $this->rechnung_titel=$result[rechnung_titel];
    $this->rechnung_typ=$result[rechnung_typ];
    $this->rechnung_strasse=$result[rechnung_strasse];
    $this->rechnung_ort=$result[rechnung_ort];
    $this->rechnung_land=$result[rechnung_land];
    $this->rechnung_abteilung=$result[rechnung_abteilung];
    $this->rechnung_unterabteilung=$result[rechnung_unterabteilung];
    $this->rechnung_adresszusatz=$result[rechnung_adresszusatz];
    $this->rechnung_telefon=$result[rechnung_telefon];
    $this->rechnung_telefax=$result[rechnung_telefax];
    $this->rechnung_anschreiben=$result[rechnung_anschreiben];
    $this->rechnung_email=$result[rechnung_email];
    $this->rechnung_plz=$result[rechnung_plz];
    $this->rechnung_ansprechpartner=$result[rechnung_ansprechpartner];
    $this->kennung=$result[kennung];
    $this->vertrieb=$result[vertrieb];
    $this->innendienst=$result[innendienst];
    $this->rabatt=$result[rabatt];
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
    $this->verbandsnummer=$result[verbandsnummer];
    $this->portofreiab=$result[portofreiab];
    $this->zahlungskonditionen_festschreiben=$result[zahlungskonditionen_festschreiben];
    $this->rabatte_festschreiben=$result[rabatte_festschreiben];
    $this->provision=$result[provision];
    $this->portofrei_aktiv=$result[portofrei_aktiv];
    $this->rabattinformation=$result[rabattinformation];
    $this->freifeld1=$result[freifeld1];
    $this->rechnung_periode=$result[rechnung_periode];
    $this->rechnung_anzahlpapier=$result[rechnung_anzahlpapier];
    $this->rechnung_permail=$result[rechnung_permail];
    $this->usereditid=$result[usereditid];
    $this->useredittimestamp=$result[useredittimestamp];
    $this->infoauftragserfassung=$result[infoauftragserfassung];
    $this->mandatsreferenz=$result[mandatsreferenz];
    $this->glaeubigeridentnr=$result[glaeubigeridentnr];
    $this->kreditlimit=$result[kreditlimit];
    $this->tour=$result[tour];
    $this->freifeld2=$result[freifeld2];
    $this->freifeld3=$result[freifeld3];
    $this->abweichendeemailab=$result[abweichendeemailab];
    $this->mlmfestsetzenbis=$result[mlmfestsetzenbis];
    $this->filiale=$result[filiale];
    $this->mlmlizenzgebuehrbis=$result[mlmlizenzgebuehrbis];
    $this->mandatsreferenzdatum=$result[mandatsreferenzdatum];
    $this->mandatsreferenzaenderung=$result[mandatsreferenzaenderung];
    $this->sachkonto=$result[sachkonto];
    $this->mlmauszahlungprojekt=$result[mlmauszahlungprojekt];
    $this->folgebestaetigungsperre=$result[folgebestaetigungsperre];
  }

  public function Create()
  {
    $sql = "INSERT INTO adresse (id,typ,marketingsperre,trackingsperre,rechnungsadresse,sprache,name,abteilung,unterabteilung,ansprechpartner,land,strasse,ort,plz,telefon,telefax,mobil,email,ustid,ust_befreit,passwort_gesendet,sonstiges,adresszusatz,kundenfreigabe,steuer,logdatei,kundennummer,lieferantennummer,mitarbeiternummer,konto,blz,bank,inhaber,swift,iban,waehrung,paypal,paypalinhaber,paypalwaehrung,projekt,partner,zahlungsweise,zahlungszieltage,zahlungszieltageskonto,zahlungszielskonto,versandart,kundennummerlieferant,zahlungsweiselieferant,zahlungszieltagelieferant,zahlungszieltageskontolieferant,zahlungszielskontolieferant,versandartlieferant,geloescht,firma,webid,internetseite,vorname,kalender_aufgaben,titel,anschreiben,logfile,mlmaktiv,mlmvertragsbeginn,geburtstag,liefersperre,mlmpositionierung,steuernummer,steuerbefreit,mlmmitmwst,mlmabrechnung,sponsor,geworbenvon,liefersperregrund,verrechnungskontoreisekosten,rolledatum,mlmwaehrungauszahlung,mlmfestsetzen,mlmmindestpunkte,mlmwartekonto,abweichende_rechnungsadresse,rechnung_vorname,rechnung_name,rechnung_titel,rechnung_typ,rechnung_strasse,rechnung_ort,rechnung_land,rechnung_abteilung,rechnung_unterabteilung,rechnung_adresszusatz,rechnung_telefon,rechnung_telefax,rechnung_anschreiben,rechnung_email,rechnung_plz,rechnung_ansprechpartner,kennung,vertrieb,innendienst,rabatt,rabatt1,rabatt2,rabatt3,rabatt4,rabatt5,bonus1,bonus1_ab,bonus2,bonus2_ab,bonus3,bonus3_ab,bonus4,bonus4_ab,bonus5,bonus5_ab,bonus6,bonus6_ab,bonus7,bonus7_ab,bonus8,bonus8_ab,bonus9,bonus9_ab,bonus10,bonus10_ab,verbandsnummer,portofreiab,zahlungskonditionen_festschreiben,rabatte_festschreiben,provision,portofrei_aktiv,rabattinformation,freifeld1,rechnung_periode,rechnung_anzahlpapier,rechnung_permail,usereditid,useredittimestamp,infoauftragserfassung,mandatsreferenz,glaeubigeridentnr,kreditlimit,tour,freifeld2,freifeld3,abweichendeemailab,mlmfestsetzenbis,filiale,mlmlizenzgebuehrbis,mandatsreferenzdatum,mandatsreferenzaenderung,sachkonto,mlmauszahlungprojekt,folgebestaetigungsperre)
      VALUES('','{$this->typ}','{$this->marketingsperre}','{$this->trackingsperre}','{$this->rechnungsadresse}','{$this->sprache}','{$this->name}','{$this->abteilung}','{$this->unterabteilung}','{$this->ansprechpartner}','{$this->land}','{$this->strasse}','{$this->ort}','{$this->plz}','{$this->telefon}','{$this->telefax}','{$this->mobil}','{$this->email}','{$this->ustid}','{$this->ust_befreit}','{$this->passwort_gesendet}','{$this->sonstiges}','{$this->adresszusatz}','{$this->kundenfreigabe}','{$this->steuer}','{$this->logdatei}','{$this->kundennummer}','{$this->lieferantennummer}','{$this->mitarbeiternummer}','{$this->konto}','{$this->blz}','{$this->bank}','{$this->inhaber}','{$this->swift}','{$this->iban}','{$this->waehrung}','{$this->paypal}','{$this->paypalinhaber}','{$this->paypalwaehrung}','{$this->projekt}','{$this->partner}','{$this->zahlungsweise}','{$this->zahlungszieltage}','{$this->zahlungszieltageskonto}','{$this->zahlungszielskonto}','{$this->versandart}','{$this->kundennummerlieferant}','{$this->zahlungsweiselieferant}','{$this->zahlungszieltagelieferant}','{$this->zahlungszieltageskontolieferant}','{$this->zahlungszielskontolieferant}','{$this->versandartlieferant}','{$this->geloescht}','{$this->firma}','{$this->webid}','{$this->internetseite}','{$this->vorname}','{$this->kalender_aufgaben}','{$this->titel}','{$this->anschreiben}','{$this->logfile}','{$this->mlmaktiv}','{$this->mlmvertragsbeginn}','{$this->geburtstag}','{$this->liefersperre}','{$this->mlmpositionierung}','{$this->steuernummer}','{$this->steuerbefreit}','{$this->mlmmitmwst}','{$this->mlmabrechnung}','{$this->sponsor}','{$this->geworbenvon}','{$this->liefersperregrund}','{$this->verrechnungskontoreisekosten}','{$this->rolledatum}','{$this->mlmwaehrungauszahlung}','{$this->mlmfestsetzen}','{$this->mlmmindestpunkte}','{$this->mlmwartekonto}','{$this->abweichende_rechnungsadresse}','{$this->rechnung_vorname}','{$this->rechnung_name}','{$this->rechnung_titel}','{$this->rechnung_typ}','{$this->rechnung_strasse}','{$this->rechnung_ort}','{$this->rechnung_land}','{$this->rechnung_abteilung}','{$this->rechnung_unterabteilung}','{$this->rechnung_adresszusatz}','{$this->rechnung_telefon}','{$this->rechnung_telefax}','{$this->rechnung_anschreiben}','{$this->rechnung_email}','{$this->rechnung_plz}','{$this->rechnung_ansprechpartner}','{$this->kennung}','{$this->vertrieb}','{$this->innendienst}','{$this->rabatt}','{$this->rabatt1}','{$this->rabatt2}','{$this->rabatt3}','{$this->rabatt4}','{$this->rabatt5}','{$this->bonus1}','{$this->bonus1_ab}','{$this->bonus2}','{$this->bonus2_ab}','{$this->bonus3}','{$this->bonus3_ab}','{$this->bonus4}','{$this->bonus4_ab}','{$this->bonus5}','{$this->bonus5_ab}','{$this->bonus6}','{$this->bonus6_ab}','{$this->bonus7}','{$this->bonus7_ab}','{$this->bonus8}','{$this->bonus8_ab}','{$this->bonus9}','{$this->bonus9_ab}','{$this->bonus10}','{$this->bonus10_ab}','{$this->verbandsnummer}','{$this->portofreiab}','{$this->zahlungskonditionen_festschreiben}','{$this->rabatte_festschreiben}','{$this->provision}','{$this->portofrei_aktiv}','{$this->rabattinformation}','{$this->freifeld1}','{$this->rechnung_periode}','{$this->rechnung_anzahlpapier}','{$this->rechnung_permail}','{$this->usereditid}','{$this->useredittimestamp}','{$this->infoauftragserfassung}','{$this->mandatsreferenz}','{$this->glaeubigeridentnr}','{$this->kreditlimit}','{$this->tour}','{$this->freifeld2}','{$this->freifeld3}','{$this->abweichendeemailab}','{$this->mlmfestsetzenbis}','{$this->filiale}','{$this->mlmlizenzgebuehrbis}','{$this->mandatsreferenzdatum}','{$this->mandatsreferenzaenderung}','{$this->sachkonto}','{$this->mlmauszahlungprojekt}','{$this->folgebestaetigungsperre}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE adresse SET
      typ='{$this->typ}',
      marketingsperre='{$this->marketingsperre}',
      trackingsperre='{$this->trackingsperre}',
      rechnungsadresse='{$this->rechnungsadresse}',
      sprache='{$this->sprache}',
      name='{$this->name}',
      abteilung='{$this->abteilung}',
      unterabteilung='{$this->unterabteilung}',
      ansprechpartner='{$this->ansprechpartner}',
      land='{$this->land}',
      strasse='{$this->strasse}',
      ort='{$this->ort}',
      plz='{$this->plz}',
      telefon='{$this->telefon}',
      telefax='{$this->telefax}',
      mobil='{$this->mobil}',
      email='{$this->email}',
      ustid='{$this->ustid}',
      ust_befreit='{$this->ust_befreit}',
      passwort_gesendet='{$this->passwort_gesendet}',
      sonstiges='{$this->sonstiges}',
      adresszusatz='{$this->adresszusatz}',
      kundenfreigabe='{$this->kundenfreigabe}',
      steuer='{$this->steuer}',
      logdatei='{$this->logdatei}',
      kundennummer='{$this->kundennummer}',
      lieferantennummer='{$this->lieferantennummer}',
      mitarbeiternummer='{$this->mitarbeiternummer}',
      konto='{$this->konto}',
      blz='{$this->blz}',
      bank='{$this->bank}',
      inhaber='{$this->inhaber}',
      swift='{$this->swift}',
      iban='{$this->iban}',
      waehrung='{$this->waehrung}',
      paypal='{$this->paypal}',
      paypalinhaber='{$this->paypalinhaber}',
      paypalwaehrung='{$this->paypalwaehrung}',
      projekt='{$this->projekt}',
      partner='{$this->partner}',
      zahlungsweise='{$this->zahlungsweise}',
      zahlungszieltage='{$this->zahlungszieltage}',
      zahlungszieltageskonto='{$this->zahlungszieltageskonto}',
      zahlungszielskonto='{$this->zahlungszielskonto}',
      versandart='{$this->versandart}',
      kundennummerlieferant='{$this->kundennummerlieferant}',
      zahlungsweiselieferant='{$this->zahlungsweiselieferant}',
      zahlungszieltagelieferant='{$this->zahlungszieltagelieferant}',
      zahlungszieltageskontolieferant='{$this->zahlungszieltageskontolieferant}',
      zahlungszielskontolieferant='{$this->zahlungszielskontolieferant}',
      versandartlieferant='{$this->versandartlieferant}',
      geloescht='{$this->geloescht}',
      firma='{$this->firma}',
      webid='{$this->webid}',
      internetseite='{$this->internetseite}',
      vorname='{$this->vorname}',
      kalender_aufgaben='{$this->kalender_aufgaben}',
      titel='{$this->titel}',
      anschreiben='{$this->anschreiben}',
      logfile='{$this->logfile}',
      mlmaktiv='{$this->mlmaktiv}',
      mlmvertragsbeginn='{$this->mlmvertragsbeginn}',
      geburtstag='{$this->geburtstag}',
      liefersperre='{$this->liefersperre}',
      mlmpositionierung='{$this->mlmpositionierung}',
      steuernummer='{$this->steuernummer}',
      steuerbefreit='{$this->steuerbefreit}',
      mlmmitmwst='{$this->mlmmitmwst}',
      mlmabrechnung='{$this->mlmabrechnung}',
      sponsor='{$this->sponsor}',
      geworbenvon='{$this->geworbenvon}',
      liefersperregrund='{$this->liefersperregrund}',
      verrechnungskontoreisekosten='{$this->verrechnungskontoreisekosten}',
      rolledatum='{$this->rolledatum}',
      mlmwaehrungauszahlung='{$this->mlmwaehrungauszahlung}',
      mlmfestsetzen='{$this->mlmfestsetzen}',
      mlmmindestpunkte='{$this->mlmmindestpunkte}',
      mlmwartekonto='{$this->mlmwartekonto}',
      abweichende_rechnungsadresse='{$this->abweichende_rechnungsadresse}',
      rechnung_vorname='{$this->rechnung_vorname}',
      rechnung_name='{$this->rechnung_name}',
      rechnung_titel='{$this->rechnung_titel}',
      rechnung_typ='{$this->rechnung_typ}',
      rechnung_strasse='{$this->rechnung_strasse}',
      rechnung_ort='{$this->rechnung_ort}',
      rechnung_land='{$this->rechnung_land}',
      rechnung_abteilung='{$this->rechnung_abteilung}',
      rechnung_unterabteilung='{$this->rechnung_unterabteilung}',
      rechnung_adresszusatz='{$this->rechnung_adresszusatz}',
      rechnung_telefon='{$this->rechnung_telefon}',
      rechnung_telefax='{$this->rechnung_telefax}',
      rechnung_anschreiben='{$this->rechnung_anschreiben}',
      rechnung_email='{$this->rechnung_email}',
      rechnung_plz='{$this->rechnung_plz}',
      rechnung_ansprechpartner='{$this->rechnung_ansprechpartner}',
      kennung='{$this->kennung}',
      vertrieb='{$this->vertrieb}',
      innendienst='{$this->innendienst}',
      rabatt='{$this->rabatt}',
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
      verbandsnummer='{$this->verbandsnummer}',
      portofreiab='{$this->portofreiab}',
      zahlungskonditionen_festschreiben='{$this->zahlungskonditionen_festschreiben}',
      rabatte_festschreiben='{$this->rabatte_festschreiben}',
      provision='{$this->provision}',
      portofrei_aktiv='{$this->portofrei_aktiv}',
      rabattinformation='{$this->rabattinformation}',
      freifeld1='{$this->freifeld1}',
      rechnung_periode='{$this->rechnung_periode}',
      rechnung_anzahlpapier='{$this->rechnung_anzahlpapier}',
      rechnung_permail='{$this->rechnung_permail}',
      usereditid='{$this->usereditid}',
      useredittimestamp='{$this->useredittimestamp}',
      infoauftragserfassung='{$this->infoauftragserfassung}',
      mandatsreferenz='{$this->mandatsreferenz}',
      glaeubigeridentnr='{$this->glaeubigeridentnr}',
      kreditlimit='{$this->kreditlimit}',
      tour='{$this->tour}',
      freifeld2='{$this->freifeld2}',
      freifeld3='{$this->freifeld3}',
      abweichendeemailab='{$this->abweichendeemailab}',
      mlmfestsetzenbis='{$this->mlmfestsetzenbis}',
      filiale='{$this->filiale}',
      mlmlizenzgebuehrbis='{$this->mlmlizenzgebuehrbis}',
      mandatsreferenzdatum='{$this->mandatsreferenzdatum}',
      mandatsreferenzaenderung='{$this->mandatsreferenzaenderung}',
      sachkonto='{$this->sachkonto}',
      mlmauszahlungprojekt='{$this->mlmauszahlungprojekt}',
      folgebestaetigungsperre='{$this->folgebestaetigungsperre}'
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

    $sql = "DELETE FROM adresse WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->typ="";
    $this->marketingsperre="";
    $this->trackingsperre="";
    $this->rechnungsadresse="";
    $this->sprache="";
    $this->name="";
    $this->abteilung="";
    $this->unterabteilung="";
    $this->ansprechpartner="";
    $this->land="";
    $this->strasse="";
    $this->ort="";
    $this->plz="";
    $this->telefon="";
    $this->telefax="";
    $this->mobil="";
    $this->email="";
    $this->ustid="";
    $this->ust_befreit="";
    $this->passwort_gesendet="";
    $this->sonstiges="";
    $this->adresszusatz="";
    $this->kundenfreigabe="";
    $this->steuer="";
    $this->logdatei="";
    $this->kundennummer="";
    $this->lieferantennummer="";
    $this->mitarbeiternummer="";
    $this->konto="";
    $this->blz="";
    $this->bank="";
    $this->inhaber="";
    $this->swift="";
    $this->iban="";
    $this->waehrung="";
    $this->paypal="";
    $this->paypalinhaber="";
    $this->paypalwaehrung="";
    $this->projekt="";
    $this->partner="";
    $this->zahlungsweise="";
    $this->zahlungszieltage="";
    $this->zahlungszieltageskonto="";
    $this->zahlungszielskonto="";
    $this->versandart="";
    $this->kundennummerlieferant="";
    $this->zahlungsweiselieferant="";
    $this->zahlungszieltagelieferant="";
    $this->zahlungszieltageskontolieferant="";
    $this->zahlungszielskontolieferant="";
    $this->versandartlieferant="";
    $this->geloescht="";
    $this->firma="";
    $this->webid="";
    $this->internetseite="";
    $this->vorname="";
    $this->kalender_aufgaben="";
    $this->titel="";
    $this->anschreiben="";
    $this->logfile="";
    $this->mlmaktiv="";
    $this->mlmvertragsbeginn="";
    $this->geburtstag="";
    $this->liefersperre="";
    $this->mlmpositionierung="";
    $this->steuernummer="";
    $this->steuerbefreit="";
    $this->mlmmitmwst="";
    $this->mlmabrechnung="";
    $this->sponsor="";
    $this->geworbenvon="";
    $this->liefersperregrund="";
    $this->verrechnungskontoreisekosten="";
    $this->rolledatum="";
    $this->mlmwaehrungauszahlung="";
    $this->mlmfestsetzen="";
    $this->mlmmindestpunkte="";
    $this->mlmwartekonto="";
    $this->abweichende_rechnungsadresse="";
    $this->rechnung_vorname="";
    $this->rechnung_name="";
    $this->rechnung_titel="";
    $this->rechnung_typ="";
    $this->rechnung_strasse="";
    $this->rechnung_ort="";
    $this->rechnung_land="";
    $this->rechnung_abteilung="";
    $this->rechnung_unterabteilung="";
    $this->rechnung_adresszusatz="";
    $this->rechnung_telefon="";
    $this->rechnung_telefax="";
    $this->rechnung_anschreiben="";
    $this->rechnung_email="";
    $this->rechnung_plz="";
    $this->rechnung_ansprechpartner="";
    $this->kennung="";
    $this->vertrieb="";
    $this->innendienst="";
    $this->rabatt="";
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
    $this->verbandsnummer="";
    $this->portofreiab="";
    $this->zahlungskonditionen_festschreiben="";
    $this->rabatte_festschreiben="";
    $this->provision="";
    $this->portofrei_aktiv="";
    $this->rabattinformation="";
    $this->freifeld1="";
    $this->rechnung_periode="";
    $this->rechnung_anzahlpapier="";
    $this->rechnung_permail="";
    $this->usereditid="";
    $this->useredittimestamp="";
    $this->infoauftragserfassung="";
    $this->mandatsreferenz="";
    $this->glaeubigeridentnr="";
    $this->kreditlimit="";
    $this->tour="";
    $this->freifeld2="";
    $this->freifeld3="";
    $this->abweichendeemailab="";
    $this->mlmfestsetzenbis="";
    $this->filiale="";
    $this->mlmlizenzgebuehrbis="";
    $this->mandatsreferenzdatum="";
    $this->mandatsreferenzaenderung="";
    $this->sachkonto="";
    $this->mlmauszahlungprojekt="";
    $this->folgebestaetigungsperre="";
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
  function SetTyp($value) { $this->typ=$value; }
  function GetTyp() { return $this->typ; }
  function SetMarketingsperre($value) { $this->marketingsperre=$value; }
  function GetMarketingsperre() { return $this->marketingsperre; }
  function SetTrackingsperre($value) { $this->trackingsperre=$value; }
  function GetTrackingsperre() { return $this->trackingsperre; }
  function SetRechnungsadresse($value) { $this->rechnungsadresse=$value; }
  function GetRechnungsadresse() { return $this->rechnungsadresse; }
  function SetSprache($value) { $this->sprache=$value; }
  function GetSprache() { return $this->sprache; }
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetAbteilung($value) { $this->abteilung=$value; }
  function GetAbteilung() { return $this->abteilung; }
  function SetUnterabteilung($value) { $this->unterabteilung=$value; }
  function GetUnterabteilung() { return $this->unterabteilung; }
  function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  function GetAnsprechpartner() { return $this->ansprechpartner; }
  function SetLand($value) { $this->land=$value; }
  function GetLand() { return $this->land; }
  function SetStrasse($value) { $this->strasse=$value; }
  function GetStrasse() { return $this->strasse; }
  function SetOrt($value) { $this->ort=$value; }
  function GetOrt() { return $this->ort; }
  function SetPlz($value) { $this->plz=$value; }
  function GetPlz() { return $this->plz; }
  function SetTelefon($value) { $this->telefon=$value; }
  function GetTelefon() { return $this->telefon; }
  function SetTelefax($value) { $this->telefax=$value; }
  function GetTelefax() { return $this->telefax; }
  function SetMobil($value) { $this->mobil=$value; }
  function GetMobil() { return $this->mobil; }
  function SetEmail($value) { $this->email=$value; }
  function GetEmail() { return $this->email; }
  function SetUstid($value) { $this->ustid=$value; }
  function GetUstid() { return $this->ustid; }
  function SetUst_Befreit($value) { $this->ust_befreit=$value; }
  function GetUst_Befreit() { return $this->ust_befreit; }
  function SetPasswort_Gesendet($value) { $this->passwort_gesendet=$value; }
  function GetPasswort_Gesendet() { return $this->passwort_gesendet; }
  function SetSonstiges($value) { $this->sonstiges=$value; }
  function GetSonstiges() { return $this->sonstiges; }
  function SetAdresszusatz($value) { $this->adresszusatz=$value; }
  function GetAdresszusatz() { return $this->adresszusatz; }
  function SetKundenfreigabe($value) { $this->kundenfreigabe=$value; }
  function GetKundenfreigabe() { return $this->kundenfreigabe; }
  function SetSteuer($value) { $this->steuer=$value; }
  function GetSteuer() { return $this->steuer; }
  function SetLogdatei($value) { $this->logdatei=$value; }
  function GetLogdatei() { return $this->logdatei; }
  function SetKundennummer($value) { $this->kundennummer=$value; }
  function GetKundennummer() { return $this->kundennummer; }
  function SetLieferantennummer($value) { $this->lieferantennummer=$value; }
  function GetLieferantennummer() { return $this->lieferantennummer; }
  function SetMitarbeiternummer($value) { $this->mitarbeiternummer=$value; }
  function GetMitarbeiternummer() { return $this->mitarbeiternummer; }
  function SetKonto($value) { $this->konto=$value; }
  function GetKonto() { return $this->konto; }
  function SetBlz($value) { $this->blz=$value; }
  function GetBlz() { return $this->blz; }
  function SetBank($value) { $this->bank=$value; }
  function GetBank() { return $this->bank; }
  function SetInhaber($value) { $this->inhaber=$value; }
  function GetInhaber() { return $this->inhaber; }
  function SetSwift($value) { $this->swift=$value; }
  function GetSwift() { return $this->swift; }
  function SetIban($value) { $this->iban=$value; }
  function GetIban() { return $this->iban; }
  function SetWaehrung($value) { $this->waehrung=$value; }
  function GetWaehrung() { return $this->waehrung; }
  function SetPaypal($value) { $this->paypal=$value; }
  function GetPaypal() { return $this->paypal; }
  function SetPaypalinhaber($value) { $this->paypalinhaber=$value; }
  function GetPaypalinhaber() { return $this->paypalinhaber; }
  function SetPaypalwaehrung($value) { $this->paypalwaehrung=$value; }
  function GetPaypalwaehrung() { return $this->paypalwaehrung; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetPartner($value) { $this->partner=$value; }
  function GetPartner() { return $this->partner; }
  function SetZahlungsweise($value) { $this->zahlungsweise=$value; }
  function GetZahlungsweise() { return $this->zahlungsweise; }
  function SetZahlungszieltage($value) { $this->zahlungszieltage=$value; }
  function GetZahlungszieltage() { return $this->zahlungszieltage; }
  function SetZahlungszieltageskonto($value) { $this->zahlungszieltageskonto=$value; }
  function GetZahlungszieltageskonto() { return $this->zahlungszieltageskonto; }
  function SetZahlungszielskonto($value) { $this->zahlungszielskonto=$value; }
  function GetZahlungszielskonto() { return $this->zahlungszielskonto; }
  function SetVersandart($value) { $this->versandart=$value; }
  function GetVersandart() { return $this->versandart; }
  function SetKundennummerlieferant($value) { $this->kundennummerlieferant=$value; }
  function GetKundennummerlieferant() { return $this->kundennummerlieferant; }
  function SetZahlungsweiselieferant($value) { $this->zahlungsweiselieferant=$value; }
  function GetZahlungsweiselieferant() { return $this->zahlungsweiselieferant; }
  function SetZahlungszieltagelieferant($value) { $this->zahlungszieltagelieferant=$value; }
  function GetZahlungszieltagelieferant() { return $this->zahlungszieltagelieferant; }
  function SetZahlungszieltageskontolieferant($value) { $this->zahlungszieltageskontolieferant=$value; }
  function GetZahlungszieltageskontolieferant() { return $this->zahlungszieltageskontolieferant; }
  function SetZahlungszielskontolieferant($value) { $this->zahlungszielskontolieferant=$value; }
  function GetZahlungszielskontolieferant() { return $this->zahlungszielskontolieferant; }
  function SetVersandartlieferant($value) { $this->versandartlieferant=$value; }
  function GetVersandartlieferant() { return $this->versandartlieferant; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetWebid($value) { $this->webid=$value; }
  function GetWebid() { return $this->webid; }
  function SetInternetseite($value) { $this->internetseite=$value; }
  function GetInternetseite() { return $this->internetseite; }
  function SetVorname($value) { $this->vorname=$value; }
  function GetVorname() { return $this->vorname; }
  function SetKalender_Aufgaben($value) { $this->kalender_aufgaben=$value; }
  function GetKalender_Aufgaben() { return $this->kalender_aufgaben; }
  function SetTitel($value) { $this->titel=$value; }
  function GetTitel() { return $this->titel; }
  function SetAnschreiben($value) { $this->anschreiben=$value; }
  function GetAnschreiben() { return $this->anschreiben; }
  function SetLogfile($value) { $this->logfile=$value; }
  function GetLogfile() { return $this->logfile; }
  function SetMlmaktiv($value) { $this->mlmaktiv=$value; }
  function GetMlmaktiv() { return $this->mlmaktiv; }
  function SetMlmvertragsbeginn($value) { $this->mlmvertragsbeginn=$value; }
  function GetMlmvertragsbeginn() { return $this->mlmvertragsbeginn; }
  function SetGeburtstag($value) { $this->geburtstag=$value; }
  function GetGeburtstag() { return $this->geburtstag; }
  function SetLiefersperre($value) { $this->liefersperre=$value; }
  function GetLiefersperre() { return $this->liefersperre; }
  function SetMlmpositionierung($value) { $this->mlmpositionierung=$value; }
  function GetMlmpositionierung() { return $this->mlmpositionierung; }
  function SetSteuernummer($value) { $this->steuernummer=$value; }
  function GetSteuernummer() { return $this->steuernummer; }
  function SetSteuerbefreit($value) { $this->steuerbefreit=$value; }
  function GetSteuerbefreit() { return $this->steuerbefreit; }
  function SetMlmmitmwst($value) { $this->mlmmitmwst=$value; }
  function GetMlmmitmwst() { return $this->mlmmitmwst; }
  function SetMlmabrechnung($value) { $this->mlmabrechnung=$value; }
  function GetMlmabrechnung() { return $this->mlmabrechnung; }
  function SetSponsor($value) { $this->sponsor=$value; }
  function GetSponsor() { return $this->sponsor; }
  function SetGeworbenvon($value) { $this->geworbenvon=$value; }
  function GetGeworbenvon() { return $this->geworbenvon; }
  function SetLiefersperregrund($value) { $this->liefersperregrund=$value; }
  function GetLiefersperregrund() { return $this->liefersperregrund; }
  function SetVerrechnungskontoreisekosten($value) { $this->verrechnungskontoreisekosten=$value; }
  function GetVerrechnungskontoreisekosten() { return $this->verrechnungskontoreisekosten; }
  function SetRolledatum($value) { $this->rolledatum=$value; }
  function GetRolledatum() { return $this->rolledatum; }
  function SetMlmwaehrungauszahlung($value) { $this->mlmwaehrungauszahlung=$value; }
  function GetMlmwaehrungauszahlung() { return $this->mlmwaehrungauszahlung; }
  function SetMlmfestsetzen($value) { $this->mlmfestsetzen=$value; }
  function GetMlmfestsetzen() { return $this->mlmfestsetzen; }
  function SetMlmmindestpunkte($value) { $this->mlmmindestpunkte=$value; }
  function GetMlmmindestpunkte() { return $this->mlmmindestpunkte; }
  function SetMlmwartekonto($value) { $this->mlmwartekonto=$value; }
  function GetMlmwartekonto() { return $this->mlmwartekonto; }
  function SetAbweichende_Rechnungsadresse($value) { $this->abweichende_rechnungsadresse=$value; }
  function GetAbweichende_Rechnungsadresse() { return $this->abweichende_rechnungsadresse; }
  function SetRechnung_Vorname($value) { $this->rechnung_vorname=$value; }
  function GetRechnung_Vorname() { return $this->rechnung_vorname; }
  function SetRechnung_Name($value) { $this->rechnung_name=$value; }
  function GetRechnung_Name() { return $this->rechnung_name; }
  function SetRechnung_Titel($value) { $this->rechnung_titel=$value; }
  function GetRechnung_Titel() { return $this->rechnung_titel; }
  function SetRechnung_Typ($value) { $this->rechnung_typ=$value; }
  function GetRechnung_Typ() { return $this->rechnung_typ; }
  function SetRechnung_Strasse($value) { $this->rechnung_strasse=$value; }
  function GetRechnung_Strasse() { return $this->rechnung_strasse; }
  function SetRechnung_Ort($value) { $this->rechnung_ort=$value; }
  function GetRechnung_Ort() { return $this->rechnung_ort; }
  function SetRechnung_Land($value) { $this->rechnung_land=$value; }
  function GetRechnung_Land() { return $this->rechnung_land; }
  function SetRechnung_Abteilung($value) { $this->rechnung_abteilung=$value; }
  function GetRechnung_Abteilung() { return $this->rechnung_abteilung; }
  function SetRechnung_Unterabteilung($value) { $this->rechnung_unterabteilung=$value; }
  function GetRechnung_Unterabteilung() { return $this->rechnung_unterabteilung; }
  function SetRechnung_Adresszusatz($value) { $this->rechnung_adresszusatz=$value; }
  function GetRechnung_Adresszusatz() { return $this->rechnung_adresszusatz; }
  function SetRechnung_Telefon($value) { $this->rechnung_telefon=$value; }
  function GetRechnung_Telefon() { return $this->rechnung_telefon; }
  function SetRechnung_Telefax($value) { $this->rechnung_telefax=$value; }
  function GetRechnung_Telefax() { return $this->rechnung_telefax; }
  function SetRechnung_Anschreiben($value) { $this->rechnung_anschreiben=$value; }
  function GetRechnung_Anschreiben() { return $this->rechnung_anschreiben; }
  function SetRechnung_Email($value) { $this->rechnung_email=$value; }
  function GetRechnung_Email() { return $this->rechnung_email; }
  function SetRechnung_Plz($value) { $this->rechnung_plz=$value; }
  function GetRechnung_Plz() { return $this->rechnung_plz; }
  function SetRechnung_Ansprechpartner($value) { $this->rechnung_ansprechpartner=$value; }
  function GetRechnung_Ansprechpartner() { return $this->rechnung_ansprechpartner; }
  function SetKennung($value) { $this->kennung=$value; }
  function GetKennung() { return $this->kennung; }
  function SetVertrieb($value) { $this->vertrieb=$value; }
  function GetVertrieb() { return $this->vertrieb; }
  function SetInnendienst($value) { $this->innendienst=$value; }
  function GetInnendienst() { return $this->innendienst; }
  function SetRabatt($value) { $this->rabatt=$value; }
  function GetRabatt() { return $this->rabatt; }
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
  function SetVerbandsnummer($value) { $this->verbandsnummer=$value; }
  function GetVerbandsnummer() { return $this->verbandsnummer; }
  function SetPortofreiab($value) { $this->portofreiab=$value; }
  function GetPortofreiab() { return $this->portofreiab; }
  function SetZahlungskonditionen_Festschreiben($value) { $this->zahlungskonditionen_festschreiben=$value; }
  function GetZahlungskonditionen_Festschreiben() { return $this->zahlungskonditionen_festschreiben; }
  function SetRabatte_Festschreiben($value) { $this->rabatte_festschreiben=$value; }
  function GetRabatte_Festschreiben() { return $this->rabatte_festschreiben; }
  function SetProvision($value) { $this->provision=$value; }
  function GetProvision() { return $this->provision; }
  function SetPortofrei_Aktiv($value) { $this->portofrei_aktiv=$value; }
  function GetPortofrei_Aktiv() { return $this->portofrei_aktiv; }
  function SetRabattinformation($value) { $this->rabattinformation=$value; }
  function GetRabattinformation() { return $this->rabattinformation; }
  function SetFreifeld1($value) { $this->freifeld1=$value; }
  function GetFreifeld1() { return $this->freifeld1; }
  function SetRechnung_Periode($value) { $this->rechnung_periode=$value; }
  function GetRechnung_Periode() { return $this->rechnung_periode; }
  function SetRechnung_Anzahlpapier($value) { $this->rechnung_anzahlpapier=$value; }
  function GetRechnung_Anzahlpapier() { return $this->rechnung_anzahlpapier; }
  function SetRechnung_Permail($value) { $this->rechnung_permail=$value; }
  function GetRechnung_Permail() { return $this->rechnung_permail; }
  function SetUsereditid($value) { $this->usereditid=$value; }
  function GetUsereditid() { return $this->usereditid; }
  function SetUseredittimestamp($value) { $this->useredittimestamp=$value; }
  function GetUseredittimestamp() { return $this->useredittimestamp; }
  function SetInfoauftragserfassung($value) { $this->infoauftragserfassung=$value; }
  function GetInfoauftragserfassung() { return $this->infoauftragserfassung; }
  function SetMandatsreferenz($value) { $this->mandatsreferenz=$value; }
  function GetMandatsreferenz() { return $this->mandatsreferenz; }
  function SetGlaeubigeridentnr($value) { $this->glaeubigeridentnr=$value; }
  function GetGlaeubigeridentnr() { return $this->glaeubigeridentnr; }
  function SetKreditlimit($value) { $this->kreditlimit=$value; }
  function GetKreditlimit() { return $this->kreditlimit; }
  function SetTour($value) { $this->tour=$value; }
  function GetTour() { return $this->tour; }
  function SetFreifeld2($value) { $this->freifeld2=$value; }
  function GetFreifeld2() { return $this->freifeld2; }
  function SetFreifeld3($value) { $this->freifeld3=$value; }
  function GetFreifeld3() { return $this->freifeld3; }
  function SetAbweichendeemailab($value) { $this->abweichendeemailab=$value; }
  function GetAbweichendeemailab() { return $this->abweichendeemailab; }
  function SetMlmfestsetzenbis($value) { $this->mlmfestsetzenbis=$value; }
  function GetMlmfestsetzenbis() { return $this->mlmfestsetzenbis; }
  function SetFiliale($value) { $this->filiale=$value; }
  function GetFiliale() { return $this->filiale; }
  function SetMlmlizenzgebuehrbis($value) { $this->mlmlizenzgebuehrbis=$value; }
  function GetMlmlizenzgebuehrbis() { return $this->mlmlizenzgebuehrbis; }
  function SetMandatsreferenzdatum($value) { $this->mandatsreferenzdatum=$value; }
  function GetMandatsreferenzdatum() { return $this->mandatsreferenzdatum; }
  function SetMandatsreferenzaenderung($value) { $this->mandatsreferenzaenderung=$value; }
  function GetMandatsreferenzaenderung() { return $this->mandatsreferenzaenderung; }
  function SetSachkonto($value) { $this->sachkonto=$value; }
  function GetSachkonto() { return $this->sachkonto; }
  function SetMlmauszahlungprojekt($value) { $this->mlmauszahlungprojekt=$value; }
  function GetMlmauszahlungprojekt() { return $this->mlmauszahlungprojekt; }
  function SetFolgebestaetigungsperre($value) { $this->folgebestaetigungsperre=$value; }
  function GetFolgebestaetigungsperre() { return $this->folgebestaetigungsperre; }

}

?>