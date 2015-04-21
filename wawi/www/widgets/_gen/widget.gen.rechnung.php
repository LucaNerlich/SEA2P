<?php 

class WidgetGenrechnung
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenrechnung($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function rechnungDelete()
  {
    
    $this->form->Execute("rechnung","delete");

    $this->rechnungList();
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
    $this->form = $this->app->FormHandler->CreateNew("rechnung");
    $this->form->UseTable("rechnung");
    $this->form->UseTemplate("rechnung.tpl",$this->parsetarget);

    $field = new HTMLInput("adresse","text","","10","","","","","","","pflicht","0");
    $this->form->NewField($field);

    $field = new HTMLInput("aktion","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("auftragid","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("ihrebestellnummer","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferdatum","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferschein","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("datum","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("doppel","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("schreibschutz","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("mahnwesenfestsetzen","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("zahlungsstatus",0,"zahlungsstatus");
    $field->AddOption('offen','offen');
    $field->AddOption('bezahlt','bezahlt');
    $field->AddOption('forderungsverlust','forderungsverlust');
    $field->AddOption('abgebucht (bei Lastschrift)','abgebucht');
    $this->form->NewField($field);

    $field = new HTMLInput("ist","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("soll","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("skonto_gegeben","text","","3","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("mahnwesen",0,"mahnwesen");
    $field->AddOption('','');
    $field->AddOption('Zahlungserinnerung','zahlungserinnerung');
    $field->AddOption('Mahnung 1','mahnung1');
    $field->AddOption('Mahnung 2','mahnung2');
    $field->AddOption('Mahnung 3','mahnung3');
    $field->AddOption('Inkasso','inkasso');
    $field->AddOption('Forderungsverlust','forderungsverlust');
    $this->form->NewField($field);

    $field = new HTMLInput("mahnwesen_datum","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("mahnwesen_gesperrt","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("mahnwesen_internebemerkung",2,30);   
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

    $field = new HTMLInput("buchhaltung","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("vertrieb","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bearbeiter","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ohne_briefpapier","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltage","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltageskonto","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("einzugsdatum","text","","30","","","","","","","0");
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

    $field = new HTMLInput("zahlungszielskonto","text","","30","","","","","","","0");
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

    $field = new HTMLCheckbox("ustbrief","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ustbrief_eingang","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("ustbrief_eingang_am","text","","","","","","","","","0");
    $this->form->NewField($field);


  }

}

?>