<?php 

class WidgetGenprojekt
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenprojekt($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function projektDelete()
  {
    
    $this->form->Execute("projekt","delete");

    $this->projektList();
  }

  function Edit()
  {
    $this->form->Edit();
  }

  function Copy()
  {
    $this->form->Copy();
  }

  public function Create()
  {
    $this->form->Create();
  }

  public function Search()
  {
    $this->app->Tpl->Set($this->parsetarget,"SUUUCHEEE");
  }

  public function Summary()
  {
    $this->app->Tpl->Set($this->parsetarget,"grosse Tabelle");
  }

  function Form()
  {
    $this->form = $this->app->FormHandler->CreateNew("projekt");
    $this->form->UseTable("projekt");
    $this->form->UseTemplate("projekt.tpl",$this->parsetarget);

    $field = new HTMLInput("name","text","","50","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("name","notempty","Pflichtfeld!",MSGNAME);

    $field = new HTMLInput("abkuerzung","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("abkuerzung","notempty","Pflichtfeld!",MSGABKUERZUNG);

    $field = new HTMLTextarea("beschreibung",5,50);   
    $this->form->NewField($field);

    $field = new HTMLTextarea("sonstiges",5,50);   
    $this->form->NewField($field);

    $field = new HTMLInput("farbe","text","","15","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("verkaufszahlendiagram","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("oeffentlich","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("zahlungserinnerung","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungsmailbedinungen","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("stornomail","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("speziallieferschein","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("speziallieferscheinbeschriftung","","","1","0");
    $this->form->NewField($field);



    $field = new HTMLInput("verantwortlicher","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("abrechnungsart",0,"abrechnungsart");
    $field->AddOption('Keine Abrechnung','keine');
    $field->AddOption('Pauschalpreis','pauschal');
    $field->AddOption('auf Stundenbasis','stunden');
    $this->form->NewField($field);

    $field = new HTMLInput("kunde","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("auftragid","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("gesamtstunden_max","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("aktiv","","","1","0");
    $this->form->NewField($field);



    $field = new HTMLSelect("kommissionierverfahren",0,"kommissionierverfahren");
    $field->AddOption('ohne Lagerbuchung','rechnungsmail');
    $field->AddOption('einfache Lagerbuchung ohne weiteren Prozess','lieferschein');
    $field->AddOption('Lieferschein mit Lagerplatz + automatische Lagerausbuchung + sofort drucken','lieferscheinlager');
    $field->AddOption('Stapeldruck (automatisches erzeugen und drucken von Paketmarken + LS,RE)','stapeldruck');
    $field->AddOption('WaWision Logistikzentrum (2-stufige Kommissionierung)','zweistufig');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autoversand","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("druckerlogistikstufe1",0,"druckerlogistikstufe1");
    $this->form->NewField($field);

    $field = new HTMLSelect("druckerlogistikstufe2",0,"druckerlogistikstufe2");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("automailversandbestaetigung","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autodruckrechnung","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("autodruckrechnungmenge","text","","3","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("automailrechnung","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autodrucklieferschein","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("autodrucklieferscheinmenge","text","","3","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("automaillieferschein","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autodruckanhang","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("automailanhang","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("stornomail","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("zahlungserinnerung","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("wechselaufeinstufig","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("reservierung","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("eanherstellerscan","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("selbstabholermail","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("projektuebergreifendkommisionieren","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("folgebestaetigung","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("portocheck","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("checkok","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("checkname","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("shopzwangsprojekt","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kundenfreigabe_loeschen","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("paketmarke_einzeldatei","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("dpdkundennr","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("dpdpfad","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("dpdformat",3,50);   
    $this->form->NewField($field);

    $field = new HTMLInput("dhlkundennr","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("dhlpfad","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("dhlformat",3,50);   
    $this->form->NewField($field);

    $field = new HTMLCheckbox("intraship_enabled","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("intraship_drucker",0,"intraship_drucker");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("intraship_testmode","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_user","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_signature","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_ekp","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_api_user","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_api_password","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_company_name","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_street_name","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_street_number","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_zip","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_country","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_city","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_email","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_phone","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_internet","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_contact_person","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_account_owner","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_account_number","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_bank_code","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_bank_name","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_iban","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_bic","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_weightinkg","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_lengthincm","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_widthincm","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_heightincm","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("intraship_packagetype","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("absendeadresse","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("absendename","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("absendesignatur",20,60);   
    $this->form->NewField($field);



    $field = new HTMLCheckbox("eigenernummernkreis","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_angebot","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_auftrag","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_lieferschein","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_rechnung","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_gutschrift","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_bestellung","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_arbeitsnachweis","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_reisekosten","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_produktion","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_anfrage","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_kundennummer","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_lieferantennummer","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_mitarbeiternummer","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("next_artikelnummer","text","","40","","","","","","","0");
    $this->form->NewField($field);



    $field = new HTMLCheckbox("eigenesteuer","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz_normal","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("steuersatz_ermaessigt","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("waehrung","text","","40","","","","","","","0");
    $this->form->NewField($field);



  }

}

?>