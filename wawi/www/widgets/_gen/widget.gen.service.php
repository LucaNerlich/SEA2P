<?php 

class WidgetGenservice
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenservice($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function serviceDelete()
  {
    
    $this->form->Execute("service","delete");

    $this->serviceList();
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
    $this->form = $this->app->FormHandler->CreateNew("service");
    $this->form->UseTable("service");
    $this->form->UseTemplate("service.tpl",$this->parsetarget);

    $field = new HTMLInput("adresse","text","","80","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("eingangart",0,"eingangart");
    $field->AddOption('Telefon','telefon');
    $field->AddOption('E-Mail','email');
    $field->AddOption('Fax','fax');
    $field->AddOption('Brief','brief');
    $field->AddOption('Sonstiges','sonstiges');
    $this->form->NewField($field);

    $field = new HTMLTextarea("ansprechpartner",3,140);   
    $this->form->NewField($field);

    $field = new HTMLInput("betreff","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("prio",0,"prio");
    $field->AddOption('Niedrig','niedrig');
    $field->AddOption('Normal','normal');
    $field->AddOption('Hoch','hoch');
    $field->AddOption('Notfall','notfall');
    $field->AddOption('Feature-Request','feature');
    $this->form->NewField($field);

    $field = new HTMLInput("erledigenbis","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("beschreibung_html",30,80);   
    $this->form->NewField($field);

    $field = new HTMLInput("zuweisen","text","","80","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("status",0,"status");
    $field->AddOption('angelegt','angelegt');
    $field->AddOption('gestartet','gestartet');
    $field->AddOption('abgeschlossen','abgeschlossen');
    $this->form->NewField($field);

    $field = new HTMLInput("artikel","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("seriennummer","text","","40","","","","","","","0");
    $this->form->NewField($field);


    $field = new HTMLTextarea("antwortankunden",20,80);   
    $this->form->NewField($field);

    $field = new HTMLCheckbox("antwortpermail","","","1","0");
    $this->form->NewField($field);


  }

}

?>