<?php 

class WidgetGengeschaeftsbrief_vorlagen
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGengeschaeftsbrief_vorlagen($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function geschaeftsbrief_vorlagenDelete()
  {
    
    $this->form->Execute("geschaeftsbrief_vorlagen","delete");

    $this->geschaeftsbrief_vorlagenList();
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
    $this->form = $this->app->FormHandler->CreateNew("geschaeftsbrief_vorlagen");
    $this->form->UseTable("geschaeftsbrief_vorlagen");
    $this->form->UseTemplate("geschaeftsbrief_vorlagen.tpl",$this->parsetarget);

    $field = new HTMLInput("subjekt","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("subjekt","notempty","Plichtfeld!",MSGSUBJEKT);

    $field = new HTMLSelect("sprache",0,"sprache");
    $field->AddOption('Deutsch','deutsch');
    $field->AddOption('Englisch','englisch');
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("betreff","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("text",20,50);   
    $this->form->NewField($field);


  }

}

?>