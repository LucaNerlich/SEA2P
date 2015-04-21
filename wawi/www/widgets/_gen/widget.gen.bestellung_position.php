<?php 

class WidgetGenbestellung_position
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenbestellung_position($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function bestellung_positionDelete()
  {
    
    $this->form->Execute("bestellung_position","delete");

    $this->bestellung_positionList();
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
    $this->form = $this->app->FormHandler->CreateNew("bestellung_position");
    $this->form->UseTable("bestellung_position");
    $this->form->UseTemplate("bestellung_position.tpl",$this->parsetarget);

    $field = new HTMLInput("bezeichnunglieferant","text","","50","50","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnunglieferant","notempty","Pflichtfeld!",MSGBEZEICHNUNGLIEFERANT);

    $field = new HTMLInput("bestellnummer","text","","30","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("bestellnummer","notempty","Pflichtfeld!",MSGBESTELLNUMMER);

    $field = new HTMLInput("menge","text","","8","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("menge","notempty","Pflichtfeld!",MSGMENGE);

    $field = new HTMLInput("preis","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("einheit","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("vpe","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("lieferdatum","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("beschreibung",5,30);   
    $this->form->NewField($field);

    $field = new HTMLTextarea("bemerkung",3,30);   
    $this->form->NewField($field);


  }

}

?>