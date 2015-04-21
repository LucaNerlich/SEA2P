<?php 

class WidgetGenverkaufspreise
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenverkaufspreise($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function verkaufspreiseDelete()
  {
    
    $this->form->Execute("verkaufspreise","delete");

    $this->verkaufspreiseList();
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
    $this->form = $this->app->FormHandler->CreateNew("verkaufspreise");
    $this->form->UseTable("verkaufspreise");
    $this->form->UseTemplate("verkaufspreise.tpl",$this->parsetarget);

    $field = new HTMLSelect("art",0,"art");
    $field->AddOption('Kunde','Kunde');
    $field->AddOption('Gruppe','Gruppe');
    $this->form->NewField($field);

    $field = new HTMLInput("adresse","text","","70","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("gruppe","text","","70","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kundenartikelnummer","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("objekt",0,"objekt");
    $field->AddOption('Standard','Standard');
    $field->AddOption('Rahmenvetrag','Rahmenvertrag');
    $field->AddOption('Abrufbestellung','Abrufbestellung');
    $this->form->NewField($field);

    $field = new HTMLInput("ab_menge","text","","10","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("ab_menge","notempty","Pflichfeld!",MSGAB_MENGE);

    $field = new HTMLInput("vpe","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("preis","text","","10","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("preis","notempty","Pflichfeld!",MSGPREIS);

    $field = new HTMLSelect("waehrung",0,"waehrung");
    $field->AddOption('EUR','EUR');
    $field->AddOption('USD','USD');
    $field->AddOption('CHF','CHF');
    $field->AddOption('CAD','CAD');
    $this->form->NewField($field);

    $field = new HTMLInput("gueltig_bis","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("bemerkung",3,70);   
    $this->form->NewField($field);


  }

}

?>