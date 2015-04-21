<?php
include ("_gen/widget.gen.berichte.php");

class WidgetBerichte extends WidgetGenBerichte 
{
  private $app;
  function WidgetBerichte($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenBerichte($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {

  }
  
  public function Table()
  {
    //$table->Query("SELECT nummer,beschreibung, id FROM berichte");
 		$table = new EasyTable($this->app);
    $this->app->Tpl->Set(INHALT,"");
    $table->Query("SELECT name, id FROM berichte");
    $table->DisplayNew($this->parsetarget, "
				<a href=\"index.php?module=berichte&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/pdf.png\"></a>
				<a href=\"index.php?module=berichte&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.png\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=berichte&action=delete&id=%value%';\">
          <img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\"></a>

        ");

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
