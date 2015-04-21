<?php
include ("_gen/widget.gen.angebot_position.php");

class WidgetAngebot_position extends WidgetGenAngebot_position 
{
  private $app;
  function WidgetAngebot_position($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenAngebot_position($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {
    $this->app->YUI->AutoComplete("einheit","artikeleinheit");

    //$this->app->YUI->AutoComplete(AUTO,"artikel",array('nummer','name_de','warengruppe'),"nummer");
    $this->form->ReplaceFunction("lieferdatum",$this,"ReplaceDatum");
    $this->app->YUI->DatePicker("lieferdatum");
 		$this->form->ReplaceFunction("preis",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("menge",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("grundrabatt",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("rabatt",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("rabatt1",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("rabatt2",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("rabatt3",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("rabatt4",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("rabatt5",$this,"ReplaceDecimal");
   	$this->form->ReplaceFunction("punkte",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("bonuspunkte",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("mlmdirektpraemie",$this,"ReplaceDecimal");

    $this->form->ReplaceFunction("lieferdatum",$this,"ReplaceDatum");


  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }
  function ReplaceDecimal($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDecimal($db,$value,$fromform);
  }

  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT angebot, id FROM angebot_position");
    $table->Display($this->parsetarget);
  }



  public function Search()
  {
    $this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }


}
?>
