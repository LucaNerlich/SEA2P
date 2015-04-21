<?php 

class WidgetGenberichte
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenberichte($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function berichteDelete()
  {
    
    $this->form->Execute("berichte","delete");

    $this->berichteList();
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
    $this->form = $this->app->FormHandler->CreateNew("berichte");
    $this->form->UseTable("berichte");
    $this->form->UseTemplate("berichte.tpl",$this->parsetarget);

    $field = new HTMLInput("name","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("name","notempty","Pflichfeld!",MSGNAME);

    $field = new HTMLTextarea("beschreibung",5,80);   
    $this->form->NewField($field);

    $field = new HTMLTextarea("struktur",5,80);   
    $this->form->NewField($field);

    $field = new HTMLInput("spaltennamen","text","","80","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("spaltenbreite","text","","80","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("spaltenausrichtung","text","","80","","","","","","","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("internebemerkung",5,80);   
    $this->form->NewField($field);


  }

}

?>