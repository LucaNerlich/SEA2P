<?php 

class WidgetGenkundevorlage
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenkundevorlage($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function kundevorlageDelete()
  {
    
    $this->form->Execute("kundevorlage","delete");

    $this->kundevorlageList();
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
    $this->form = $this->app->FormHandler->CreateNew("kundevorlage");
    $this->form->UseTable("kundevorlage");
    $this->form->UseTemplate("kundevorlage.tpl",$this->parsetarget);

    $field = new HTMLSelect("zahlungsweise",0);
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltage","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltageskonto","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszielskonto","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("versandart",0);
    $this->form->NewField($field);



  }

}

?>