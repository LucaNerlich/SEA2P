<?php 

class WidgetGenreisekosten
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenreisekosten($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function reisekostenDelete()
  {
    
    $this->form->Execute("reisekosten","delete");

    $this->reisekostenList();
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
    $this->form = $this->app->FormHandler->CreateNew("reisekosten");
    $this->form->UseTable("reisekosten");
    $this->form->UseTemplate("reisekosten.tpl",$this->parsetarget);

    $field = new HTMLInput("adresse","text","","10","","","","","","","pflicht","0");
    $this->form->NewField($field);

    $field = new HTMLInput("name","text","","30","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("name","notempty","Pflichfeld!",MSGNAME);

    $field = new HTMLInput("mitarbeiter","text","","30","","","","","","","pflicht","0");
    $this->form->NewField($field);

    $field = new HTMLInput("anlass","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("von","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("von_zeit","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bis","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("bis_zeit","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("auftragid","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("datum","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("schreibschutz","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("freitext",5,110);   
    $this->form->NewField($field);

    $field = new HTMLInput("bearbeiter","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("ohne_briefpapier","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("internebemerkung",2,110);   
    $this->form->NewField($field);


  }

}

?>