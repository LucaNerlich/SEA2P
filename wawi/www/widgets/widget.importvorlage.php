<?php
include ("_gen/widget.gen.importvorlage.php");

class WidgetImportvorlage extends WidgetGenImportvorlage 
{
  private $app;
  function WidgetImportvorlage($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenImportvorlage($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {

  }
  
  public function Table()
  {
    //$table->Query("SELECT nummer,beschreibung, id FROM importvorlage");
 		$table = new EasyTable($this->app);
    $this->app->Tpl->Set(INHALT,"");
		$this->app->YUI->TableSearch($this->parsetarget,"importvorlage");
  }



  public function Search()
  {
//    $this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }


}
?>
