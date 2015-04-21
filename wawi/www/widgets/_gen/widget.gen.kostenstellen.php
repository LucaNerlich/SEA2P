<?php 

class WidgetGenkostenstellen
{

  private $app;            //application object  
  public $form;            //store form object  
  private $parsetarget;    //target for content

  public function WidgetGenkostenstellen($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function kostenstellenDelete()
  {
    
    $this->form->Execute("kostenstellen","delete");

    $this->kostenstellenList();
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
    $this->form = $this->app->FormHandler->CreateNew("kostenstellen");
    $this->form->UseTable("kostenstellen");
    $this->form->UseTemplate("kostenstellen.tpl",$this->parsetarget);

    $field = new HTMLInput("nummer","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("nummer","notempty","Pflichfeld!",MSGNUMMER);

    $field = new HTMLInput("beschreibung","text","","40","","","","","","","0");
    $this->form->NewField($field);
    $this->form->AddMandatory("beschreibung","notempty","Pflichfeld!",MSGBESCHREIBUNG);

    $field = new HTMLTextarea("internebemerkung",5,50);   
    $this->form->NewField($field);


  }

}

?>