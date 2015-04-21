<?php 

class WidgetGenreisekosten_position
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenreisekosten_position($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function reisekosten_positionDelete()
  {
    
    $this->form->Execute("reisekosten_position","delete");

    $this->reisekosten_positionList();
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
    $this->form = $this->app->FormHandler->CreateNew("reisekosten_position");
    $this->form->UseTable("reisekosten_position");
    $this->form->UseTemplate("reisekosten_position.tpl",$this->parsetarget);

    $field = new HTMLInput("datum","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("reisekostenart",0);
    $this->form->NewField($field);

    $field = new HTMLInput("betrag","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("abrechnen","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("keineust","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("uststeuersatz",0);
    $this->form->NewField($field);

    $field = new HTMLInput("bezeichnung","text","","50","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("bezeichnung","notempty","Pflichtfeld!",MSGBEZEICHNUNG);

    $field = new HTMLSelect("bezahlt_wie",0);
    $this->form->NewField($field);

    $field = new HTMLInput("mitarbeiter","text","","30","","","","","","","0");
    $this->form->NewField($field);


  }

}

?>