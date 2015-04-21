<?php
include ("_gen/widget.gen.kontorahmen.php");

class WidgetKontorahmen extends WidgetGenKontorahmen 
{
  private $app;
  function WidgetKontorahmen($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenKontorahmen($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {

  }
  
  public function Table()
  {
		$this->app->YUI->TableSearch($this->parsetarget,"kontorahmenlist");
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
