<?php
include ("_gen/widget.gen.lager.php");

class WidgetLager extends WidgetGenLager
{
  private $app;
  function WidgetLager($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenLager($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {
    $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
    $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
    $this->form->NewField($field);
  }

  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT bezeichnung, id FROM lager");
    //$table->Display($this->parsetarget);
    $table->DisplayNew($this->parsetarget, "<a href=\"index.php?module=lager&action=edit&id=%value%\">Bearbeiten</a>&nbsp;<a href=\"#\"
    onclick=\"if(!confirm('Soll wirklich für jedes Regal ein Etikett gedruckt werden?')) return false; else window.location.href='index.php?module=lager&action=regaletiketten&id=%value%'
;\">Regaletiketten</a>");

  }



  public function Search()
  {
    //$this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }


}
?>
