<?php 

class WidgetGenarbeitspaket
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenarbeitspaket($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function arbeitspaketDelete()
  {
    
    $this->form->Execute("arbeitspaket","delete");

    $this->arbeitspaketList();
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
    $this->form = $this->app->FormHandler->CreateNew("arbeitspaket");
    $this->form->UseTable("arbeitspaket");
    $this->form->UseTemplate("arbeitspaket.tpl",$this->parsetarget);

    $field = new HTMLSelect("art",0);
    $field->AddOption('Teilprojekt','teilprojekt');
    $field->AddOption('Arbeitspaket','arbeitspaket');
    $field->AddOption('Meilenstein','meilenstein');
    $field->AddOption('Material','material');
    $this->form->NewField($field);

    $field = new HTMLInput("aufgabe","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("beschreibung",10,50);   
    $this->form->NewField($field);

    $field = new HTMLSelect("status",0);
    $field->AddOption('offen','offen');
    $field->AddOption('aktiv','aktiv');
    $field->AddOption('abgeschlossen','abgeschlossen');
    $this->form->NewField($field);

    $field = new HTMLSelect("vorgaenger",0);
    $this->form->NewField($field);

    $field = new HTMLInput("abgabedatum","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("adresse","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("auftragid","text","","30","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("zeit_geplant","text","","5","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kosten_geplant","text","","5","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("artikel_geplant","text","","50","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kostenstelle","text","","5","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("abgenommen","","","1","0");
    $this->form->NewField($field);


  }

}

?>