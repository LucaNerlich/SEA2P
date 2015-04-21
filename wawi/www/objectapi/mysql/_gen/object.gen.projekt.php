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
  private  $dpdkundennr;
  private  $dhlkundennr;
  private  $dpdpfad;
  private  $dhlpfad;
  private  $abrechnungsart;
  private  $kommissionierverfahren;
  private  $wechselaufeinstufig;
  private  $projektuebergreifendkommisionieren;
  private  $absendeadresse;
  private  $autodruckrechnung;
  private  $autodruckversandbestaetigung;
  private  $automailversandbestaetigung;
  private  $autodrucklieferschein;
  private  $automaillieferschein;
  private  $autodruckstorno;
  private  $autodruckanhang;
  private  $automailanhang;
  private  $absendename;
  private  $absendesignatur;
  private  $eigenernummernkreis;
  private  $next_angebot;
  private  $next_auftrag;
  private  $next_rechnung;
  private  $next_lieferschein;
  private  $next_arbeitsnachweis;
  private  $next_reisekosten;
  private  $next_bestellung;
  private  $next_gutschrift;
  private  $next_kundennummer;
  private  $next_lieferantennummer;
  private  $next_mitarbeiternummer;
  private  $next_waren;
  private  $next_produktion;
  private  $next_sonstiges;
  private  $next_anfrage;
  private  $dhlzahlungmandant;
  private  $dhlretourenschein;
  private  $shopzwangsprojekt;
  private  $dhlformat;
  private  $dpdformat;
  private  $paketmarke_einzeldatei;
  private  $steuersatz_normal;
  private  $steuersatz_zwischen;
  private  $steuersatz_ermaessigt;
  private  $steuersatz_starkermaessigt;
  private  $steuersatz_dienstleistung;
  private  $waehrung;
  private  $eigenesteuer;
  private  $autodruckrechnungmenge;
  private  $autodrucklieferscheinmenge;
  private  $autodruckerrechnung;
  private  $autodruckerlieferschein;
  private  $autodruckeranhang;
  private  $druckerlogistikstufe1;
  private  $druckerlogistikstufe2;
  private  $selbstabholermail;
  private  $next_artikelnummer;
  private  $eanherstellerscan;
  private  $upspfad;
  private  $dhlintodb;
  private  $intraship_testmode;
  private  $intraship_user;
  private  $intraship_signature;
  private  $intraship_ekp;
  private  $intraship_api_user;
  private  $intraship_api_password;
  private  $intraship_company_name;
  private  $intraship_street_name;
  private  $intraship_street_number;
  private  $intraship_zip;
  private  $intraship_country;
  private  $intraship_city;
  private  $intraship_email;
  private  $intraship_phone;
  private  $intraship_internet;
  private  $intraship_contact_person;
  private  $intraship_account_owner;
  private  $intraship_account_number;
  private  $intraship_bank_code;
  private  $intraship_bank_name;
  private  $intraship_iban;
  private  $intraship_bic;
  private  $intraship_enabled;
  private  $intraship_drucker;
  private  $intraship_WeightInKG;
  private  $intraship_LengthInCM;
  private  $intraship_WidthInCM;
  private  $intraship_HeightInCM;
  private  $intraship_PackageType;

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
    $this->dpdkundennr=$result[dpdkundennr];
    $this->dhlkundennr=$result[dhlkundennr];
    $this->dpdpfad=$result[dpdpfad];
    $this->dhlpfad=$result[dhlpfad];
    $this->abrechnungsart=$result[abrechnungsart];
    $this->kommissionierverfahren=$result[kommissionierverfahren];
    $this->wechselaufeinstufig=$result[wechselaufeinstufig];
    $this->projektuebergreifendkommisionieren=$result[projektuebergreifendkommisionieren];
    $this->absendeadresse=$result[absendeadresse];
    $this->autodruckrechnung=$result[autodruckrechnung];
    $this->autodruckversandbestaetigung=$result[autodruckversandbestaetigung];
    $this->automailversandbestaetigung=$result[automailversandbestaetigung];
    $this->autodrucklieferschein=$result[autodrucklieferschein];
    $this->automaillieferschein=$result[automaillieferschein];
    $this->autodruckstorno=$result[autodruckstorno];
    $this->autodruckanhang=$result[autodruckanhang];
    $this->automailanhang=$result[automailanhang];
    $this->absendename=$result[absendename];
    $this->absendesignatur=$result[absendesignatur];
    $this->eigenernummernkreis=$result[eigenernummernkreis];
    $this->next_angebot=$result[next_angebot];
    $this->next_auftrag=$result[next_auftrag];
    $this->next_rechnung=$result[next_rechnung];
    $this->next_lieferschein=$result[next_lieferschein];
    $this->next_arbeitsnachweis=$result[next_arbeitsnachweis];
    $this->next_reisekosten=$result[next_reisekosten];
    $this->next_bestellung=$result[next_bestellung];
    $this->next_gutschrift=$result[next_gutschrift];
    $this->next_kundennummer=$result[next_kundennummer];
    $this->next_lieferantennummer=$result[next_lieferantennummer];
    $this->next_mitarbeiternummer=$result[next_mitarbeiternummer];
    $this->next_waren=$result[next_waren];
    $this->next_produktion=$result[next_produktion];
    $this->next_sonstiges=$result[next_sonstiges];
    $this->next_anfrage=$result[next_anfrage];
    $this->dhlzahlungmandant=$result[dhlzahlungmandant];
    $this->dhlretourenschein=$result[dhlretourenschein];
    $this->shopzwangsprojekt=$result[shopzwangsprojekt];
    $this->dhlformat=$result[dhlformat];
    $this->dpdformat=$result[dpdformat];
    $this->paketmarke_einzeldatei=$result[paketmarke_einzeldatei];
    $this->steuersatz_normal=$result[steuersatz_normal];
    $this->steuersatz_zwischen=$result[steuersatz_zwischen];
    $this->steuersatz_ermaessigt=$result[steuersatz_ermaessigt];
    $this->steuersatz_starkermaessigt=$result[steuersatz_starkermaessigt];
    $this->steuersatz_dienstleistung=$result[steuersatz_dienstleistung];
    $this->waehrung=$result[waehrung];
    $this->eigenesteuer=$result[eigenesteuer];
    $this->autodruckrechnungmenge=$result[autodruckrechnungmenge];
    $this->autodrucklieferscheinmenge=$result[autodrucklieferscheinmenge];
    $this->autodruckerrechnung=$result[autodruckerrechnung];
    $this->autodruckerlieferschein=$result[autodruckerlieferschein];
    $this->autodruckeranhang=$result[autodruckeranhang];
    $this->druckerlogistikstufe1=$result[druckerlogistikstufe1];
    $this->druckerlogistikstufe2=$result[druckerlogistikstufe2];
    $this->selbstabholermail=$result[selbstabholermail];
    $this->next_artikelnummer=$result[next_artikelnummer];
    $this->eanherstellerscan=$result[eanherstellerscan];
    $this->upspfad=$result[upspfad];
    $this->dhlintodb=$result[dhlintodb];
    $this->intraship_testmode=$result[intraship_testmode];
    $this->intraship_user=$result[intraship_user];
    $this->intraship_signature=$result[intraship_signature];
    $this->intraship_ekp=$result[intraship_ekp];
    $this->intraship_api_user=$result[intraship_api_user];
    $this->intraship_api_password=$result[intraship_api_password];
    $this->intraship_company_name=$result[intraship_company_name];
    $this->intraship_street_name=$result[intraship_street_name];
    $this->intraship_street_number=$result[intraship_street_number];
    $this->intraship_zip=$result[intraship_zip];
    $this->intraship_country=$result[intraship_country];
    $this->intraship_city=$result[intraship_city];
    $this->intraship_email=$result[intraship_email];
    $this->intraship_phone=$result[intraship_phone];
    $this->intraship_internet=$result[intraship_internet];
    $this->intraship_contact_person=$result[intraship_contact_person];
    $this->intraship_account_owner=$result[intraship_account_owner];
    $this->intraship_account_number=$result[intraship_account_number];
    $this->intraship_bank_code=$result[intraship_bank_code];
    $this->intraship_bank_name=$result[intraship_bank_name];
    $this->intraship_iban=$result[intraship_iban];
    $this->intraship_bic=$result[intraship_bic];
    $this->intraship_enabled=$result[intraship_enabled];
    $this->intraship_drucker=$result[intraship_drucker];
    $this->intraship_WeightInKG=$result[intraship_WeightInKG];
    $this->intraship_LengthInCM=$result[intraship_LengthInCM];
    $this->intraship_WidthInCM=$result[intraship_WidthInCM];
    $this->intraship_HeightInCM=$result[intraship_HeightInCM];
    $this->intraship_PackageType=$result[intraship_PackageType];
  }

  public function Create()
  {
    $sql = "INSERT INTO projekt (id,name,abkuerzung,verantwortlicher,beschreibung,sonstiges,aktiv,farbe,autoversand,checkok,portocheck,automailrechnung,checkname,zahlungserinnerung,zahlungsmailbedinungen,folgebestaetigung,stornomail,kundenfreigabe_loeschen,autobestellung,speziallieferschein,lieferscheinbriefpapier,speziallieferscheinbeschriftung,firma,geloescht,logdatei,reservierung,gesamtstunden_max,auftragid,kunde,oeffentlich,verkaufszahlendiagram,dpdkundennr,dhlkundennr,dpdpfad,dhlpfad,abrechnungsart,kommissionierverfahren,wechselaufeinstufig,projektuebergreifendkommisionieren,absendeadresse,autodruckrechnung,autodruckversandbestaetigung,automailversandbestaetigung,autodrucklieferschein,automaillieferschein,autodruckstorno,autodruckanhang,automailanhang,absendename,absendesignatur,eigenernummernkreis,next_angebot,next_auftrag,next_rechnung,next_lieferschein,next_arbeitsnachweis,next_reisekosten,next_bestellung,next_gutschrift,next_kundennummer,next_lieferantennummer,next_mitarbeiternummer,next_waren,next_produktion,next_sonstiges,next_anfrage,dhlzahlungmandant,dhlretourenschein,shopzwangsprojekt,dhlformat,dpdformat,paketmarke_einzeldatei,steuersatz_normal,steuersatz_zwischen,steuersatz_ermaessigt,steuersatz_starkermaessigt,steuersatz_dienstleistung,waehrung,eigenesteuer,autodruckrechnungmenge,autodrucklieferscheinmenge,autodruckerrechnung,autodruckerlieferschein,autodruckeranhang,druckerlogistikstufe1,druckerlogistikstufe2,selbstabholermail,next_artikelnummer,eanherstellerscan,upspfad,dhlintodb,intraship_testmode,intraship_user,intraship_signature,intraship_ekp,intraship_api_user,intraship_api_password,intraship_company_name,intraship_street_name,intraship_street_number,intraship_zip,intraship_country,intraship_city,intraship_email,intraship_phone,intraship_internet,intraship_contact_person,intraship_account_owner,intraship_account_number,intraship_bank_code,intraship_bank_name,intraship_iban,intraship_bic,intraship_enabled,intraship_drucker,intraship_WeightInKG,intraship_LengthInCM,intraship_WidthInCM,intraship_HeightInCM,intraship_PackageType)
      VALUES('','{$this->name}','{$this->abkuerzung}','{$this->verantwortlicher}','{$this->beschreibung}','{$this->sonstiges}','{$this->aktiv}','{$this->farbe}','{$this->autoversand}','{$this->checkok}','{$this->portocheck}','{$this->automailrechnung}','{$this->checkname}','{$this->zahlungserinnerung}','{$this->zahlungsmailbedinungen}','{$this->folgebestaetigung}','{$this->stornomail}','{$this->kundenfreigabe_loeschen}','{$this->autobestellung}','{$this->speziallieferschein}','{$this->lieferscheinbriefpapier}','{$this->speziallieferscheinbeschriftung}','{$this->firma}','{$this->geloescht}','{$this->logdatei}','{$this->reservierung}','{$this->gesamtstunden_max}','{$this->auftragid}','{$this->kunde}','{$this->oeffentlich}','{$this->verkaufszahlendiagram}','{$this->dpdkundennr}','{$this->dhlkundennr}','{$this->dpdpfad}','{$this->dhlpfad}','{$this->abrechnungsart}','{$this->kommissionierverfahren}','{$this->wechselaufeinstufig}','{$this->projektuebergreifendkommisionieren}','{$this->absendeadresse}','{$this->autodruckrechnung}','{$this->autodruckversandbestaetigung}','{$this->automailversandbestaetigung}','{$this->autodrucklieferschein}','{$this->automaillieferschein}','{$this->autodruckstorno}','{$this->autodruckanhang}','{$this->automailanhang}','{$this->absendename}','{$this->absendesignatur}','{$this->eigenernummernkreis}','{$this->next_angebot}','{$this->next_auftrag}','{$this->next_rechnung}','{$this->next_lieferschein}','{$this->next_arbeitsnachweis}','{$this->next_reisekosten}','{$this->next_bestellung}','{$this->next_gutschrift}','{$this->next_kundennummer}','{$this->next_lieferantennummer}','{$this->next_mitarbeiternummer}','{$this->next_waren}','{$this->next_produktion}','{$this->next_sonstiges}','{$this->next_anfrage}','{$this->dhlzahlungmandant}','{$this->dhlretourenschein}','{$this->shopzwangsprojekt}','{$this->dhlformat}','{$this->dpdformat}','{$this->paketmarke_einzeldatei}','{$this->steuersatz_normal}','{$this->steuersatz_zwischen}','{$this->steuersatz_ermaessigt}','{$this->steuersatz_starkermaessigt}','{$this->steuersatz_dienstleistung}','{$this->waehrung}','{$this->eigenesteuer}','{$this->autodruckrechnungmenge}','{$this->autodrucklieferscheinmenge}','{$this->autodruckerrechnung}','{$this->autodruckerlieferschein}','{$this->autodruckeranhang}','{$this->druckerlogistikstufe1}','{$this->druckerlogistikstufe2}','{$this->selbstabholermail}','{$this->next_artikelnummer}','{$this->eanherstellerscan}','{$this->upspfad}','{$this->dhlintodb}','{$this->intraship_testmode}','{$this->intraship_user}','{$this->intraship_signature}','{$this->intraship_ekp}','{$this->intraship_api_user}','{$this->intraship_api_password}','{$this->intraship_company_name}','{$this->intraship_street_name}','{$this->intraship_street_number}','{$this->intraship_zip}','{$this->intraship_country}','{$this->intraship_city}','{$this->intraship_email}','{$this->intraship_phone}','{$this->intraship_internet}','{$this->intraship_contact_person}','{$this->intraship_account_owner}','{$this->intraship_account_number}','{$this->intraship_bank_code}','{$this->intraship_bank_name}','{$this->intraship_iban}','{$this->intraship_bic}','{$this->intraship_enabled}','{$this->intraship_drucker}','{$this->intraship_WeightInKG}','{$this->intraship_LengthInCM}','{$this->intraship_WidthInCM}','{$this->intraship_HeightInCM}','{$this->intraship_PackageType}')"; 

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
      autoversand='{$this->autoversand}',
      checkok='{$this->checkok}',
      portocheck='{$this->portocheck}',
      automailrechnung='{$this->automailrechnung}',
      checkname='{$this->checkname}',
      zahlungserinnerung='{$this->zahlungserinnerung}',
      zahlungsmailbedinungen='{$this->zahlungsmailbedinungen}',
      folgebestaetigung='{$this->folgebestaetigung}',
      stornomail='{$this->stornomail}',
      kundenfreigabe_loeschen='{$this->kundenfreigabe_loeschen}',
      autobestellung='{$this->autobestellung}',
      speziallieferschein='{$this->speziallieferschein}',
      lieferscheinbriefpapier='{$this->lieferscheinbriefpapier}',
      speziallieferscheinbeschriftung='{$this->speziallieferscheinbeschriftung}',
      firma='{$this->firma}',
      geloescht='{$this->geloescht}',
      logdatei='{$this->logdatei}',
      reservierung='{$this->reservierung}',
      gesamtstunden_max='{$this->gesamtstunden_max}',
      auftragid='{$this->auftragid}',
      kunde='{$this->kunde}',
      oeffentlich='{$this->oeffentlich}',
      verkaufszahlendiagram='{$this->verkaufszahlendiagram}',
      dpdkundennr='{$this->dpdkundennr}',
      dhlkundennr='{$this->dhlkundennr}',
      dpdpfad='{$this->dpdpfad}',
      dhlpfad='{$this->dhlpfad}',
      abrechnungsart='{$this->abrechnungsart}',
      kommissionierverfahren='{$this->kommissionierverfahren}',
      wechselaufeinstufig='{$this->wechselaufeinstufig}',
      projektuebergreifendkommisionieren='{$this->projektuebergreifendkommisionieren}',
      absendeadresse='{$this->absendeadresse}',
      autodruckrechnung='{$this->autodruckrechnung}',
      autodruckversandbestaetigung='{$this->autodruckversandbestaetigung}',
      automailversandbestaetigung='{$this->automailversandbestaetigung}',
      autodrucklieferschein='{$this->autodrucklieferschein}',
      automaillieferschein='{$this->automaillieferschein}',
      autodruckstorno='{$this->autodruckstorno}',
      autodruckanhang='{$this->autodruckanhang}',
      automailanhang='{$this->automailanhang}',
      absendename='{$this->absendename}',
      absendesignatur='{$this->absendesignatur}',
      eigenernummernkreis='{$this->eigenernummernkreis}',
      next_angebot='{$this->next_angebot}',
      next_auftrag='{$this->next_auftrag}',
      next_rechnung='{$this->next_rechnung}',
      next_lieferschein='{$this->next_lieferschein}',
      next_arbeitsnachweis='{$this->next_arbeitsnachweis}',
      next_reisekosten='{$this->next_reisekosten}',
      next_bestellung='{$this->next_bestellung}',
      next_gutschrift='{$this->next_gutschrift}',
      next_kundennummer='{$this->next_kundennummer}',
      next_lieferantennummer='{$this->next_lieferantennummer}',
      next_mitarbeiternummer='{$this->next_mitarbeiternummer}',
      next_waren='{$this->next_waren}',
      next_produktion='{$this->next_produktion}',
      next_sonstiges='{$this->next_sonstiges}',
      next_anfrage='{$this->next_anfrage}',
      dhlzahlungmandant='{$this->dhlzahlungmandant}',
      dhlretourenschein='{$this->dhlretourenschein}',
      shopzwangsprojekt='{$this->shopzwangsprojekt}',
      dhlformat='{$this->dhlformat}',
      dpdformat='{$this->dpdformat}',
      paketmarke_einzeldatei='{$this->paketmarke_einzeldatei}',
      steuersatz_normal='{$this->steuersatz_normal}',
      steuersatz_zwischen='{$this->steuersatz_zwischen}',
      steuersatz_ermaessigt='{$this->steuersatz_ermaessigt}',
      steuersatz_starkermaessigt='{$this->steuersatz_starkermaessigt}',
      steuersatz_dienstleistung='{$this->steuersatz_dienstleistung}',
      waehrung='{$this->waehrung}',
      eigenesteuer='{$this->eigenesteuer}',
      autodruckrechnungmenge='{$this->autodruckrechnungmenge}',
      autodrucklieferscheinmenge='{$this->autodrucklieferscheinmenge}',
      autodruckerrechnung='{$this->autodruckerrechnung}',
      autodruckerlieferschein='{$this->autodruckerlieferschein}',
      autodruckeranhang='{$this->autodruckeranhang}',
      druckerlogistikstufe1='{$this->druckerlogistikstufe1}',
      druckerlogistikstufe2='{$this->druckerlogistikstufe2}',
      selbstabholermail='{$this->selbstabholermail}',
      next_artikelnummer='{$this->next_artikelnummer}',
      eanherstellerscan='{$this->eanherstellerscan}',
      upspfad='{$this->upspfad}',
      dhlintodb='{$this->dhlintodb}',
      intraship_testmode='{$this->intraship_testmode}',
      intraship_user='{$this->intraship_user}',
      intraship_signature='{$this->intraship_signature}',
      intraship_ekp='{$this->intraship_ekp}',
      intraship_api_user='{$this->intraship_api_user}',
      intraship_api_password='{$this->intraship_api_password}',
      intraship_company_name='{$this->intraship_company_name}',
      intraship_street_name='{$this->intraship_street_name}',
      intraship_street_number='{$this->intraship_street_number}',
      intraship_zip='{$this->intraship_zip}',
      intraship_country='{$this->intraship_country}',
      intraship_city='{$this->intraship_city}',
      intraship_email='{$this->intraship_email}',
      intraship_phone='{$this->intraship_phone}',
      intraship_internet='{$this->intraship_internet}',
      intraship_contact_person='{$this->intraship_contact_person}',
      intraship_account_owner='{$this->intraship_account_owner}',
      intraship_account_number='{$this->intraship_account_number}',
      intraship_bank_code='{$this->intraship_bank_code}',
      intraship_bank_name='{$this->intraship_bank_name}',
      intraship_iban='{$this->intraship_iban}',
      intraship_bic='{$this->intraship_bic}',
      intraship_enabled='{$this->intraship_enabled}',
      intraship_drucker='{$this->intraship_drucker}',
      intraship_WeightInKG='{$this->intraship_WeightInKG}',
      intraship_LengthInCM='{$this->intraship_LengthInCM}',
      intraship_WidthInCM='{$this->intraship_WidthInCM}',
      intraship_HeightInCM='{$this->intraship_HeightInCM}',
      intraship_PackageType='{$this->intraship_PackageType}'
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
    $this->dpdkundennr="";
    $this->dhlkundennr="";
    $this->dpdpfad="";
    $this->dhlpfad="";
    $this->abrechnungsart="";
    $this->kommissionierverfahren="";
    $this->wechselaufeinstufig="";
    $this->projektuebergreifendkommisionieren="";
    $this->absendeadresse="";
    $this->autodruckrechnung="";
    $this->autodruckversandbestaetigung="";
    $this->automailversandbestaetigung="";
    $this->autodrucklieferschein="";
    $this->automaillieferschein="";
    $this->autodruckstorno="";
    $this->autodruckanhang="";
    $this->automailanhang="";
    $this->absendename="";
    $this->absendesignatur="";
    $this->eigenernummernkreis="";
    $this->next_angebot="";
    $this->next_auftrag="";
    $this->next_rechnung="";
    $this->next_lieferschein="";
    $this->next_arbeitsnachweis="";
    $this->next_reisekosten="";
    $this->next_bestellung="";
    $this->next_gutschrift="";
    $this->next_kundennummer="";
    $this->next_lieferantennummer="";
    $this->next_mitarbeiternummer="";
    $this->next_waren="";
    $this->next_produktion="";
    $this->next_sonstiges="";
    $this->next_anfrage="";
    $this->dhlzahlungmandant="";
    $this->dhlretourenschein="";
    $this->shopzwangsprojekt="";
    $this->dhlformat="";
    $this->dpdformat="";
    $this->paketmarke_einzeldatei="";
    $this->steuersatz_normal="";
    $this->steuersatz_zwischen="";
    $this->steuersatz_ermaessigt="";
    $this->steuersatz_starkermaessigt="";
    $this->steuersatz_dienstleistung="";
    $this->waehrung="";
    $this->eigenesteuer="";
    $this->autodruckrechnungmenge="";
    $this->autodrucklieferscheinmenge="";
    $this->autodruckerrechnung="";
    $this->autodruckerlieferschein="";
    $this->autodruckeranhang="";
    $this->druckerlogistikstufe1="";
    $this->druckerlogistikstufe2="";
    $this->selbstabholermail="";
    $this->next_artikelnummer="";
    $this->eanherstellerscan="";
    $this->upspfad="";
    $this->dhlintodb="";
    $this->intraship_testmode="";
    $this->intraship_user="";
    $this->intraship_signature="";
    $this->intraship_ekp="";
    $this->intraship_api_user="";
    $this->intraship_api_password="";
    $this->intraship_company_name="";
    $this->intraship_street_name="";
    $this->intraship_street_number="";
    $this->intraship_zip="";
    $this->intraship_country="";
    $this->intraship_city="";
    $this->intraship_email="";
    $this->intraship_phone="";
    $this->intraship_internet="";
    $this->intraship_contact_person="";
    $this->intraship_account_owner="";
    $this->intraship_account_number="";
    $this->intraship_bank_code="";
    $this->intraship_bank_name="";
    $this->intraship_iban="";
    $this->intraship_bic="";
    $this->intraship_enabled="";
    $this->intraship_drucker="";
    $this->intraship_WeightInKG="";
    $this->intraship_LengthInCM="";
    $this->intraship_WidthInCM="";
    $this->intraship_HeightInCM="";
    $this->intraship_PackageType="";
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
  function SetDpdkundennr($value) { $this->dpdkundennr=$value; }
  function GetDpdkundennr() { return $this->dpdkundennr; }
  function SetDhlkundennr($value) { $this->dhlkundennr=$value; }
  function GetDhlkundennr() { return $this->dhlkundennr; }
  function SetDpdpfad($value) { $this->dpdpfad=$value; }
  function GetDpdpfad() { return $this->dpdpfad; }
  function SetDhlpfad($value) { $this->dhlpfad=$value; }
  function GetDhlpfad() { return $this->dhlpfad; }
  function SetAbrechnungsart($value) { $this->abrechnungsart=$value; }
  function GetAbrechnungsart() { return $this->abrechnungsart; }
  function SetKommissionierverfahren($value) { $this->kommissionierverfahren=$value; }
  function GetKommissionierverfahren() { return $this->kommissionierverfahren; }
  function SetWechselaufeinstufig($value) { $this->wechselaufeinstufig=$value; }
  function GetWechselaufeinstufig() { return $this->wechselaufeinstufig; }
  function SetProjektuebergreifendkommisionieren($value) { $this->projektuebergreifendkommisionieren=$value; }
  function GetProjektuebergreifendkommisionieren() { return $this->projektuebergreifendkommisionieren; }
  function SetAbsendeadresse($value) { $this->absendeadresse=$value; }
  function GetAbsendeadresse() { return $this->absendeadresse; }
  function SetAutodruckrechnung($value) { $this->autodruckrechnung=$value; }
  function GetAutodruckrechnung() { return $this->autodruckrechnung; }
  function SetAutodruckversandbestaetigung($value) { $this->autodruckversandbestaetigung=$value; }
  function GetAutodruckversandbestaetigung() { return $this->autodruckversandbestaetigung; }
  function SetAutomailversandbestaetigung($value) { $this->automailversandbestaetigung=$value; }
  function GetAutomailversandbestaetigung() { return $this->automailversandbestaetigung; }
  function SetAutodrucklieferschein($value) { $this->autodrucklieferschein=$value; }
  function GetAutodrucklieferschein() { return $this->autodrucklieferschein; }
  function SetAutomaillieferschein($value) { $this->automaillieferschein=$value; }
  function GetAutomaillieferschein() { return $this->automaillieferschein; }
  function SetAutodruckstorno($value) { $this->autodruckstorno=$value; }
  function GetAutodruckstorno() { return $this->autodruckstorno; }
  function SetAutodruckanhang($value) { $this->autodruckanhang=$value; }
  function GetAutodruckanhang() { return $this->autodruckanhang; }
  function SetAutomailanhang($value) { $this->automailanhang=$value; }
  function GetAutomailanhang() { return $this->automailanhang; }
  function SetAbsendename($value) { $this->absendename=$value; }
  function GetAbsendename() { return $this->absendename; }
  function SetAbsendesignatur($value) { $this->absendesignatur=$value; }
  function GetAbsendesignatur() { return $this->absendesignatur; }
  function SetEigenernummernkreis($value) { $this->eigenernummernkreis=$value; }
  function GetEigenernummernkreis() { return $this->eigenernummernkreis; }
  function SetNext_Angebot($value) { $this->next_angebot=$value; }
  function GetNext_Angebot() { return $this->next_angebot; }
  function SetNext_Auftrag($value) { $this->next_auftrag=$value; }
  function GetNext_Auftrag() { return $this->next_auftrag; }
  function SetNext_Rechnung($value) { $this->next_rechnung=$value; }
  function GetNext_Rechnung() { return $this->next_rechnung; }
  function SetNext_Lieferschein($value) { $this->next_lieferschein=$value; }
  function GetNext_Lieferschein() { return $this->next_lieferschein; }
  function SetNext_Arbeitsnachweis($value) { $this->next_arbeitsnachweis=$value; }
  function GetNext_Arbeitsnachweis() { return $this->next_arbeitsnachweis; }
  function SetNext_Reisekosten($value) { $this->next_reisekosten=$value; }
  function GetNext_Reisekosten() { return $this->next_reisekosten; }
  function SetNext_Bestellung($value) { $this->next_bestellung=$value; }
  function GetNext_Bestellung() { return $this->next_bestellung; }
  function SetNext_Gutschrift($value) { $this->next_gutschrift=$value; }
  function GetNext_Gutschrift() { return $this->next_gutschrift; }
  function SetNext_Kundennummer($value) { $this->next_kundennummer=$value; }
  function GetNext_Kundennummer() { return $this->next_kundennummer; }
  function SetNext_Lieferantennummer($value) { $this->next_lieferantennummer=$value; }
  function GetNext_Lieferantennummer() { return $this->next_lieferantennummer; }
  function SetNext_Mitarbeiternummer($value) { $this->next_mitarbeiternummer=$value; }
  function GetNext_Mitarbeiternummer() { return $this->next_mitarbeiternummer; }
  function SetNext_Waren($value) { $this->next_waren=$value; }
  function GetNext_Waren() { return $this->next_waren; }
  function SetNext_Produktion($value) { $this->next_produktion=$value; }
  function GetNext_Produktion() { return $this->next_produktion; }
  function SetNext_Sonstiges($value) { $this->next_sonstiges=$value; }
  function GetNext_Sonstiges() { return $this->next_sonstiges; }
  function SetNext_Anfrage($value) { $this->next_anfrage=$value; }
  function GetNext_Anfrage() { return $this->next_anfrage; }
  function SetDhlzahlungmandant($value) { $this->dhlzahlungmandant=$value; }
  function GetDhlzahlungmandant() { return $this->dhlzahlungmandant; }
  function SetDhlretourenschein($value) { $this->dhlretourenschein=$value; }
  function GetDhlretourenschein() { return $this->dhlretourenschein; }
  function SetShopzwangsprojekt($value) { $this->shopzwangsprojekt=$value; }
  function GetShopzwangsprojekt() { return $this->shopzwangsprojekt; }
  function SetDhlformat($value) { $this->dhlformat=$value; }
  function GetDhlformat() { return $this->dhlformat; }
  function SetDpdformat($value) { $this->dpdformat=$value; }
  function GetDpdformat() { return $this->dpdformat; }
  function SetPaketmarke_Einzeldatei($value) { $this->paketmarke_einzeldatei=$value; }
  function GetPaketmarke_Einzeldatei() { return $this->paketmarke_einzeldatei; }
  function SetSteuersatz_Normal($value) { $this->steuersatz_normal=$value; }
  function GetSteuersatz_Normal() { return $this->steuersatz_normal; }
  function SetSteuersatz_Zwischen($value) { $this->steuersatz_zwischen=$value; }
  function GetSteuersatz_Zwischen() { return $this->steuersatz_zwischen; }
  function SetSteuersatz_Ermaessigt($value) { $this->steuersatz_ermaessigt=$value; }
  function GetSteuersatz_Ermaessigt() { return $this->steuersatz_ermaessigt; }
  function SetSteuersatz_Starkermaessigt($value) { $this->steuersatz_starkermaessigt=$value; }
  function GetSteuersatz_Starkermaessigt() { return $this->steuersatz_starkermaessigt; }
  function SetSteuersatz_Dienstleistung($value) { $this->steuersatz_dienstleistung=$value; }
  function GetSteuersatz_Dienstleistung() { return $this->steuersatz_dienstleistung; }
  function SetWaehrung($value) { $this->waehrung=$value; }
  function GetWaehrung() { return $this->waehrung; }
  function SetEigenesteuer($value) { $this->eigenesteuer=$value; }
  function GetEigenesteuer() { return $this->eigenesteuer; }
  function SetAutodruckrechnungmenge($value) { $this->autodruckrechnungmenge=$value; }
  function GetAutodruckrechnungmenge() { return $this->autodruckrechnungmenge; }
  function SetAutodrucklieferscheinmenge($value) { $this->autodrucklieferscheinmenge=$value; }
  function GetAutodrucklieferscheinmenge() { return $this->autodrucklieferscheinmenge; }
  function SetAutodruckerrechnung($value) { $this->autodruckerrechnung=$value; }
  function GetAutodruckerrechnung() { return $this->autodruckerrechnung; }
  function SetAutodruckerlieferschein($value) { $this->autodruckerlieferschein=$value; }
  function GetAutodruckerlieferschein() { return $this->autodruckerlieferschein; }
  function SetAutodruckeranhang($value) { $this->autodruckeranhang=$value; }
  function GetAutodruckeranhang() { return $this->autodruckeranhang; }
  function SetDruckerlogistikstufe1($value) { $this->druckerlogistikstufe1=$value; }
  function GetDruckerlogistikstufe1() { return $this->druckerlogistikstufe1; }
  function SetDruckerlogistikstufe2($value) { $this->druckerlogistikstufe2=$value; }
  function GetDruckerlogistikstufe2() { return $this->druckerlogistikstufe2; }
  function SetSelbstabholermail($value) { $this->selbstabholermail=$value; }
  function GetSelbstabholermail() { return $this->selbstabholermail; }
  function SetNext_Artikelnummer($value) { $this->next_artikelnummer=$value; }
  function GetNext_Artikelnummer() { return $this->next_artikelnummer; }
  function SetEanherstellerscan($value) { $this->eanherstellerscan=$value; }
  function GetEanherstellerscan() { return $this->eanherstellerscan; }
  function SetUpspfad($value) { $this->upspfad=$value; }
  function GetUpspfad() { return $this->upspfad; }
  function SetDhlintodb($value) { $this->dhlintodb=$value; }
  function GetDhlintodb() { return $this->dhlintodb; }
  function SetIntraship_Testmode($value) { $this->intraship_testmode=$value; }
  function GetIntraship_Testmode() { return $this->intraship_testmode; }
  function SetIntraship_User($value) { $this->intraship_user=$value; }
  function GetIntraship_User() { return $this->intraship_user; }
  function SetIntraship_Signature($value) { $this->intraship_signature=$value; }
  function GetIntraship_Signature() { return $this->intraship_signature; }
  function SetIntraship_Ekp($value) { $this->intraship_ekp=$value; }
  function GetIntraship_Ekp() { return $this->intraship_ekp; }
  function SetIntraship_Api_User($value) { $this->intraship_api_user=$value; }
  function GetIntraship_Api_User() { return $this->intraship_api_user; }
  function SetIntraship_Api_Password($value) { $this->intraship_api_password=$value; }
  function GetIntraship_Api_Password() { return $this->intraship_api_password; }
  function SetIntraship_Company_Name($value) { $this->intraship_company_name=$value; }
  function GetIntraship_Company_Name() { return $this->intraship_company_name; }
  function SetIntraship_Street_Name($value) { $this->intraship_street_name=$value; }
  function GetIntraship_Street_Name() { return $this->intraship_street_name; }
  function SetIntraship_Street_Number($value) { $this->intraship_street_number=$value; }
  function GetIntraship_Street_Number() { return $this->intraship_street_number; }
  function SetIntraship_Zip($value) { $this->intraship_zip=$value; }
  function GetIntraship_Zip() { return $this->intraship_zip; }
  function SetIntraship_Country($value) { $this->intraship_country=$value; }
  function GetIntraship_Country() { return $this->intraship_country; }
  function SetIntraship_City($value) { $this->intraship_city=$value; }
  function GetIntraship_City() { return $this->intraship_city; }
  function SetIntraship_Email($value) { $this->intraship_email=$value; }
  function GetIntraship_Email() { return $this->intraship_email; }
  function SetIntraship_Phone($value) { $this->intraship_phone=$value; }
  function GetIntraship_Phone() { return $this->intraship_phone; }
  function SetIntraship_Internet($value) { $this->intraship_internet=$value; }
  function GetIntraship_Internet() { return $this->intraship_internet; }
  function SetIntraship_Contact_Person($value) { $this->intraship_contact_person=$value; }
  function GetIntraship_Contact_Person() { return $this->intraship_contact_person; }
  function SetIntraship_Account_Owner($value) { $this->intraship_account_owner=$value; }
  function GetIntraship_Account_Owner() { return $this->intraship_account_owner; }
  function SetIntraship_Account_Number($value) { $this->intraship_account_number=$value; }
  function GetIntraship_Account_Number() { return $this->intraship_account_number; }
  function SetIntraship_Bank_Code($value) { $this->intraship_bank_code=$value; }
  function GetIntraship_Bank_Code() { return $this->intraship_bank_code; }
  function SetIntraship_Bank_Name($value) { $this->intraship_bank_name=$value; }
  function GetIntraship_Bank_Name() { return $this->intraship_bank_name; }
  function SetIntraship_Iban($value) { $this->intraship_iban=$value; }
  function GetIntraship_Iban() { return $this->intraship_iban; }
  function SetIntraship_Bic($value) { $this->intraship_bic=$value; }
  function GetIntraship_Bic() { return $this->intraship_bic; }
  function SetIntraship_Enabled($value) { $this->intraship_enabled=$value; }
  function GetIntraship_Enabled() { return $this->intraship_enabled; }
  function SetIntraship_Drucker($value) { $this->intraship_drucker=$value; }
  function GetIntraship_Drucker() { return $this->intraship_drucker; }
  function SetIntraship_Weightinkg($value) { $this->intraship_WeightInKG=$value; }
  function GetIntraship_Weightinkg() { return $this->intraship_WeightInKG; }
  function SetIntraship_Lengthincm($value) { $this->intraship_LengthInCM=$value; }
  function GetIntraship_Lengthincm() { return $this->intraship_LengthInCM; }
  function SetIntraship_Widthincm($value) { $this->intraship_WidthInCM=$value; }
  function GetIntraship_Widthincm() { return $this->intraship_WidthInCM; }
  function SetIntraship_Heightincm($value) { $this->intraship_HeightInCM=$value; }
  function GetIntraship_Heightincm() { return $this->intraship_HeightInCM; }
  function SetIntraship_Packagetype($value) { $this->intraship_PackageType=$value; }
  function GetIntraship_Packagetype() { return $this->intraship_PackageType; }

}

?>