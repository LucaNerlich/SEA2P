<?php 

class WidgetGenangebot
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenangebot($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function angebotDelete()
  {
    
    $this->form->Execute("angebot","delete");

    $this->angebotList();
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
    $this->form = $this->app->FormHandler->CreateNew("angebot");
    $this->form->UseTable("angebot");
    $this->form->UseTemplate("angebot.tpl",$this->parsetarget);

    $field = new HTMLInput("adresse","text","","10","","","","","","","pflicht","0");
    $this->form->NewField($field);

    $field = new HTMLInput("aktion","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("anfrage","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("datum","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferdatum","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("gueltigbis","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("abweichendelieferadresse","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("schreibschutz","","","1","0");
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

    $field = new HTMLSelect("typ",0,"typ");
    $field->AddOption('Firma','firma');
    $field->AddOption('Herr','herr');
    $field->AddOption('Frau','frau');
    $this->form->NewField($field);

    $field = new HTMLInput("name","text","","30","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("name","notempty","Pflichfeld!",MSGNAME);

    $field = new HTMLInput("telefon","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("ansprechpartner","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("telefax","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("abteilung","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("email","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("unterabteilung","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("anschreiben","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("adresszusatz","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("strasse","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("plz","text","","5","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("ort","text","","19","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("freitext",5,110);   
    $this->form->NewField($field);

    $field = new HTMLSelect("zahlungsweise",0,"zahlungsweise");
    $field->AddOption('Rechnung','rechnung');
    $field->AddOption('Vorkasse','vorkasse');
    $field->AddOption('Nachnahme','nachnahme');
    $field->AddOption('Kreditkarte','kreditkarte');
    $field->AddOption('Einzugsermaechtigung','einzugsermaechtigung');
    $field->AddOption('Bar','bar');
    $field->AddOption('PayPal','paypal');
    $this->form->NewField($field);

    $field = new HTMLSelect("versandart",0,"versandart");
    $this->form->NewField($field);

    $field = new HTMLInput("vertrieb","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bearbeiter","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("autoversand","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("keinporto","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ohne_briefpapier","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltage","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltageskonto","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszielskonto","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bank_inhaber","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bank_institut","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bank_blz","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bank_konto","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("kreditkarte_typ",0,"kreditkarte_typ");
    $this->form->NewField($field);

    $field = new HTMLInput("kreditkarte_inhaber","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kreditkarte_nummer","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kreditkarte_pruefnummer","text","","5","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("kreditkarte_monat",0,"kreditkarte_monat");
    $this->form->NewField($field);

    $field = new HTMLSelect("kreditkarte_jahr",0,"kreditkarte_jahr");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt","text","","4","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt1","text","","4","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt2","text","","4","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt3","text","","4","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt4","text","","4","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt5","text","","4","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("internebemerkung",2,110);   
    $this->form->NewField($field);

    $field = new HTMLInput("ustid","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("ust_befreit",0,"ust_befreit");
    $field->AddOption('Deutschland','0');
    $field->AddOption('EU-Lieferung','1');
    $field->AddOption('Export','2');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("keinsteuersatz","","","1","0");
    $this->form->NewField($field);


  }

}

?>