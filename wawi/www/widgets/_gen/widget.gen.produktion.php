<?php 

class WidgetGenproduktion
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenproduktion($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function produktionDelete()
  {
    
    $this->form->Execute("produktion","delete");

    $this->produktionList();
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
    $this->form = $this->app->FormHandler->CreateNew("produktion");
    $this->form->UseTable("produktion");
    $this->form->UseTemplate("produktion.tpl",$this->parsetarget);

    $field = new HTMLInput("adresse","text","","30","","","","","","","pflicht","0");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","30","","","","","","","pflicht","0");
    $this->form->NewField($field);

    $field = new HTMLInput("angebot","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("datum","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("schreibschutz","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("abweichendelieferadresse","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("liefername","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferabteilung","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferunterabteilung","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferansprechpartner","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferadresszusatz","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferstrasse","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferplz","text","","5","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferort","text","","22","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bezeichnung","text","","100","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("reservierart",0,"reservierart");
    $field->AddOption('bei Produktionsstart','abschluss');
    $field->AddOption('bei Freigabe','freigabe');
    $field->AddOption('immer auch im Entwurfsmodus ','sofort');
    $this->form->NewField($field);

    $field = new HTMLSelect("auslagerart",0,"auslagerart");
    $field->AddOption('Sammelentnahme','sammel');
    $field->AddOption('Einzelentnahme','einzeln');
    $this->form->NewField($field);

    $field = new HTMLInput("datumproduktion","text","","8","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("freitext",6,100);   
    $this->form->NewField($field);

    $field = new HTMLSelect("art",0,"art");
    $field->AddOption('Warenauftrag (RE und LS)','standardauftrag');
    $field->AddOption('Dienstleistung (RE)','dienstleistung');
    $field->AddOption('Dienstleistung (RE und LS)','dienstleistungls');
    $field->AddOption('kostenlose Lieferung (LS)','beistellung');
    $field->AddOption('Reparatur (LS und optional RE)','rma');
    $this->form->NewField($field);

    $field = new HTMLSelect("versandart",0,"versandart");
    $this->form->NewField($field);

    $field = new HTMLSelect("zahlungsweise",0,"zahlungsweise");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autoversand","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("keinporto","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("keinestornomail","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("keinetrackingmail","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("vertrieb","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bearbeiter","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltage","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltageskonto","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszielskonto","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bank_inhaber","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bank_institut","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bank_blz","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bank_konto","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("kreditkarte_typ",0,"kreditkarte_typ");
    $this->form->NewField($field);

    $field = new HTMLInput("kreditkarte_inhaber","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kreditkarte_nummer","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kreditkarte_pruefnummer","text","","5","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("kreditkarte_monat",0,"kreditkarte_monat");
    $this->form->NewField($field);

    $field = new HTMLSelect("kreditkarte_jahr",0,"kreditkarte_jahr");
    $this->form->NewField($field);

    $field = new HTMLTextarea("internebemerkung",2,100);   
    $this->form->NewField($field);

    $field = new HTMLInput("ustid","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("ust_befreit",0,"ust_befreit");
    $field->AddOption('Deutschland','0');
    $field->AddOption('EU-Lieferung','1');
    $field->AddOption('Export','2');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ust_ok","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("packstation_inhaber","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("packstation_station","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("packstation_ident","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("packstation_plz","text","","5","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("packstation_ort","text","","14","","","","","","","0");
    $this->form->NewField($field);


  }

}

?>