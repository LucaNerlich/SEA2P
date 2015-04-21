<?php 

class WidgetGeneigenschaften
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGeneigenschaften($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function eigenschaftenDelete()
  {
    
    $this->form->Execute("eigenschaften","delete");

    $this->eigenschaftenList();
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
    $this->form = $this->app->FormHandler->CreateNew("eigenschaften");
    $this->form->UseTable("eigenschaften");
    $this->form->UseTemplate("eigenschaften.tpl",$this->parsetarget);

    $field = new HTMLInput("bezeichnung","text","","72","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichtfeld!",MSGBEZEICHNUNG);

    $field = new HTMLInput("beschreibung","text","","72","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("wert","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("einheit","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("wert2","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("einheit2","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("wert3","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("einheit3","text","","30","","","","","","","0");
    $this->form->NewField($field);


  }

}

?>