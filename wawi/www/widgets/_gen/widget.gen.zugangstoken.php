<?php 

class WidgetGenzugangstoken
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenzugangstoken($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function zugangstokenDelete()
  {
    
    $this->form->Execute("zugangstoken","delete");

    $this->zugangstokenList();
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
    $this->form = $this->app->FormHandler->CreateNew("zugangstoken");
    $this->form->UseTable("zugangstoken");
    $this->form->UseTemplate("zugangstoken.tpl",$this->parsetarget);

    $field = new HTMLInput("bezeichnung","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichfeld!",MSGBEZEICHNUNG);

    $field = new HTMLSelect("art",0);
    $field->AddOption('Uhrzeit','uhrzeit');
    $field->AddOption('Periodisch','periodisch');
    $this->form->NewField($field);

    $field = new HTMLInput("startzeit","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("letzteausfuerhung","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("periode","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("typ",0);
    $field->AddOption('Cronjob','cronjob');
    $field->AddOption('URL','url');
    $this->form->NewField($field);

    $field = new HTMLInput("parameter","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("parameter","notempty","Pflichfeld!",MSGPARAMETER);

    $field = new HTMLCheckbox("aktiv","","","1","0");
    $this->form->NewField($field);


  }

}

?>