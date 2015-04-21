<?php 

class WidgetGenpartner
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenpartner($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function partnerDelete()
  {
    
    $this->form->Execute("partner","delete");

    $this->partnerList();
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
    $this->form = $this->app->FormHandler->CreateNew("partner");
    $this->form->UseTable("partner");
    $this->form->UseTemplate("partner.tpl",$this->parsetarget);

    $field = new HTMLInput("adresse","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bezeichnung","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("ref","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("netto","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("tage","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","50","","","","","","","0");
    $this->form->NewField($field);


  }

}

?>