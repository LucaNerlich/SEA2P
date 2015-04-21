<?php 

class WidgetGeninhalt
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGeninhalt($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function inhaltDelete()
  {
    
    $this->form->Execute("inhalt","delete");

    $this->inhaltList();
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
    $this->form = $this->app->FormHandler->CreateNew("inhalt");
    $this->form->UseTable("inhalt");
    $this->form->UseTemplate("inhalt.tpl",$this->parsetarget);

    $field = new HTMLInput("shop","text","","40","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("inhaltstyp",0);
    $field->AddOption('HTML Seite','page');
    $field->AddOption('Teaser','teaser');
    $field->AddOption('Newsmeldung','news');
    $field->AddOption('E-Mail Text','email');
    $field->AddOption('Artikelgruppe','group');
    $this->form->NewField($field);

    $field = new HTMLSelect("sprache",0);
    $field->AddOption('Deutsch','de');
    $field->AddOption('Englisch','en');
    $this->form->NewField($field);

    $field = new HTMLInput("inhalt","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("aktiv","","","1","0");
    $this->form->NewField($field);

    $field = new HTMLInput("template","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("finalparse","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("navigation","text","","20","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("datum","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("sichtbarbis","text","","10","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("title","text","","80","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("kurztext",5,70);   
    $this->form->NewField($field);

    $field = new HTMLTextarea("html",20,100);   
    $this->form->NewField($field);


    $field = new HTMLTextarea("description",5,70);   
    $this->form->NewField($field);

    $field = new HTMLTextarea("keywords",5,70);   
    $this->form->NewField($field);


  }

}

?>