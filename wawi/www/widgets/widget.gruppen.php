<?php
include ("_gen/widget.gen.gruppen.php");

class Widgetgruppen extends WidgetGengruppen 
{
  private $app;
  function Widgetgruppen($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGengruppen($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {
   	$this->app->YUI->AutoComplete("portoartikel","artikelnummer",1);
    $this->form->ReplaceFunction("portoartikel",$this,"ReplaceArtikel");
  }
  
  public function Table()
  {
    //$table->Query("SELECT nummer,beschreibung, id FROM gruppen");
		$this->app->YUI->TableSearch($this->parsetarget,"gruppenlist");
  }



  public function Search()
  {
//    $this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }
 function ReplaceArtikel($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceArtikel($db,$value,$fromform);
  }




}
?>
