<?php
include ("_gen/widget.gen.artikelkategorien.php");

class WidgetArtikelkategorien extends WidgetGenArtikelkategorien 
{
  private $app;
  function WidgetArtikelkategorien($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenArtikelkategorien($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {

    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");

  }
  
  public function Table()
  {
		$this->app->YUI->TableSearch($this->parsetarget,"artikelkategorienlist");
  }



  public function Search()
  {
//    $this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }



}
?>
