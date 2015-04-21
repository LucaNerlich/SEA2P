<?php 

class WidgetGenwebmail_zuordnung
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenwebmail_zuordnung($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function webmail_zuordnungDelete()
  {
    
    $this->form->Execute("webmail_zuordnung","delete");

    $this->webmail_zuordnungList();
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
    $this->form = $this->app->FormHandler->CreateNew("webmail_zuordnung");
    $this->form->UseTable("webmail_zuordnung");
    $this->form->UseTemplate("webmail_zuordnung.tpl",$this->parsetarget);

    $field = new HTMLInput("projekt","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("adresse","text","","10","","","","","","","0");
    $this->form->NewField($field);


  }

}

?>