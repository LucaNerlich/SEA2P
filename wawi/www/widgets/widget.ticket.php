<?php
include ("_gen/widget.gen.ticket.php");

class WidgetTicket extends WidgetGenTicket 
{
  private $app;
  function WidgetTicket($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenTicket($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {
    //$this->app->YUI->AutoComplete("projekt","kunde",1);
    $this->app->YUI->AutoComplete("adresse","kunde");
    //$this->form->ReplaceFunction("projekt",&$this,"ReplaceProjekt");
    //$this->app->YUI->AutoComplete(KUNDEAUTO,"adresse",array('kundennummer','name','logdatei'),"CONCAT(kundennummer,' ',name)","kunde");
    $this->form->ReplaceFunction("adresse",$this,"ReplaceKunde");
  }

  function ReplaceKunde($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT CONCAT(kundennummer,' ',name) FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $kundennummer = substr($value,0,5);
      $name = substr($value,6);
      $id =  $this->app->DB->Select("SELECT id FROM adresse WHERE name='$name' AND kundennummer='$kundennummer'  AND geloescht=0 LIMIT 1");
    }

    // wenn ziel datenbank
    if($db)
    {
      return $id;
    }
    // wenn ziel formular
    else
    {
      return $abkuerzung;
    }
  }



  function DatumErsetzen($wert)
  {
    return "neuerwerert";
  }

  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT zeit, schluessel, kunde, betreff,
      id FROM ticket order by zeit");
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
