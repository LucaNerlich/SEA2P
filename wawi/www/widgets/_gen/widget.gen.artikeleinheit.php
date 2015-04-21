<?php 

class WidgetGenartikeleinheit
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenartikeleinheit($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function artikeleinheitDelete()
  {
    
    $this->form->Execute("artikeleinheit","delete");

    $this->artikeleinheitList();
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
    $this->form = $this->app->FormHandler->CreateNew("artikeleinheit");
    $this->form->UseTable("artikeleinheit");
    $this->form->UseTemplate("artikeleinheit.tpl",$this->parsetarget);

    $field = new HTMLInput("einheit_de","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("einheit_de","notempty","Pflichfeld!",MSGEINHEIT_DE);

    $field = new HTMLTextarea("internebemerkung",5,50);   
    $this->form->NewField($field);


  }

}

?>