<?php 

class WidgetGenkasse
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenkasse($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function kasseDelete()
  {
    
    $this->form->Execute("kasse","delete");

    $this->kasseList();
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
    $this->form = $this->app->FormHandler->CreateNew("kasse");
    $this->form->UseTable("kasse");
    $this->form->UseTemplate("kasse.tpl",$this->parsetarget);

    $field = new HTMLInput("datum","text","","10","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("datum","notempty","Pflichfeld!",MSGDATUM);

    $field = new HTMLSelect("auswahl",0);
    $field->AddOption('Einnahme','einnahme');
    $field->AddOption('Ausgabe','ausgabe');
    $this->form->NewField($field);

    $field = new HTMLInput("betrag","text","","10","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("betrag","notempty","Pflichfeld!",MSGBETRAG);

    $field = new HTMLSelect("steuergruppe",0);
    $field->AddOption('Standard UST','0');
    $field->AddOption('Erm&auml;ssigte UST (Buch, Literatur, ...)','1');
    $field->AddOption('Ohne UST','2');
    $this->form->NewField($field);

    $field = new HTMLCheckbox("kundenbuchung","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("adresse","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("projekt","notempty","Pflichfeld!",MSGPROJEKT);

    $field = new HTMLInput("grund","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("grund","notempty","Pflichfeld!",MSGGRUND);


  }

}

?>