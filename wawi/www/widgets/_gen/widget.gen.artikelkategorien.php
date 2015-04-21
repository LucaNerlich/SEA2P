<?php 

class WidgetGenartikelkategorien
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenartikelkategorien($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function artikelkategorienDelete()
  {
    
    $this->form->Execute("artikelkategorien","delete");

    $this->artikelkategorienList();
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
    $this->form = $this->app->FormHandler->CreateNew("artikelkategorien");
    $this->form->UseTable("artikelkategorien");
    $this->form->UseTemplate("artikelkategorien.tpl",$this->parsetarget);

    $field = new HTMLInput("bezeichnung","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichfeld!",MSGBEZEICHNUNG);

    $field = new HTMLInput("next_nummer","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("externenummer","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","40","","","","","","","0");
    $this->form->NewField($field);


  }

}

?>