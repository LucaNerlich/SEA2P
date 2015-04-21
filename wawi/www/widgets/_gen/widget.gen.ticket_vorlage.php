<?php 

class WidgetGenticket_vorlage
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenticket_vorlage($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function ticket_vorlageDelete()
  {
    
    $this->form->Execute("ticket_vorlage","delete");

    $this->ticket_vorlageList();
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
    $this->form = $this->app->FormHandler->CreateNew("ticket_vorlage");
    $this->form->UseTable("ticket_vorlage");
    $this->form->UseTemplate("ticket_vorlage.tpl",$this->parsetarget);

    $field = new HTMLInput("vorlagenname","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("sichtbar","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","30","","","","","","","pflicht","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("vorlage",20,50);   
    $this->form->NewField($field);


  }

}

?>