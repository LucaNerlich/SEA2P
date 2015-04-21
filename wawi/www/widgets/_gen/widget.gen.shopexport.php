<?php 

class WidgetGenshopexport
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenshopexport($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function shopexportDelete()
  {
    
    $this->form->Execute("shopexport","delete");

    $this->shopexportList();
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
    $this->form = $this->app->FormHandler->CreateNew("shopexport");
    $this->form->UseTable("shopexport");
    $this->form->UseTemplate("shopexport.tpl",$this->parsetarget);

    $field = new HTMLCheckbox("aktiv","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bezeichnung","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichfeld!",MSGBEZEICHNUNG);

    $field = new HTMLSelect("typ",0,"typ");
    $field->AddOption('waWision Online Shop','wawision');
    $field->AddOption('Shopware 4.x','shopware4');
    $this->form->NewField($field);

    $field = new HTMLInput("url","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("demomodus","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("multiprojekt","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("artikelexport","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("lagerexport","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("artikelimport","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("artikelimporteinzeln","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("artikelporto","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("artikelnachnahme","text","","","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("passwort","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("token","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("challenge","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("cms","","","1","0");
    $this->form->NewField($field);


  }

}

?>