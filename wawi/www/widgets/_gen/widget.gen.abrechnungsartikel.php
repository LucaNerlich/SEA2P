<?php 

class WidgetGenabrechnungsartikel
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenabrechnungsartikel($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function abrechnungsartikelDelete()
  {
    
    $this->form->Execute("abrechnungsartikel","delete");

    $this->abrechnungsartikelList();
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
    $this->form = $this->app->FormHandler->CreateNew("abrechnungsartikel");
    $this->form->UseTable("abrechnungsartikel");
    $this->form->UseTemplate("abrechnungsartikel.tpl",$this->parsetarget);

    $field = new HTMLInput("bezeichnung","text","","50","50","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichtfeld!",MSGBEZEICHNUNG);

    $field = new HTMLTextarea("beschreibung",5,30);   
    $this->form->NewField($field);

    $field = new HTMLInput("menge","text","","10","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("menge","notempty","Pflichtfeld!",MSGMENGE);

    $field = new HTMLInput("preis","text","","10","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("preis","notempty","Pflichtfeld!",MSGPREIS);

    $field = new HTMLCheckbox("wiederholend","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("startdatum","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlzyklus","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("abgerechnetbis","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("bemerkung",5,40);   
    $this->form->NewField($field);


  }

}

?>