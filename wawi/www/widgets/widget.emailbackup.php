<?php
include ("_gen/widget.gen.emailbackup.php");

class WidgetEmailbackup extends WidgetGenEmailbackup 
{
  private $app;
  function WidgetEmailbackup($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenEmailbackup($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {

    //firma
    $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
    $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
    $this->form->NewField($field);


    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->app->YUI->AutoComplete("adresse","mitarbeiter");

    $this->form->ReplaceFunction("adresse",$this,"ReplaceMitarbeiter");

    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");

  }

  function ReplaceMitarbeiter($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceMitarbeiter($db,$value,$fromform);
  }


  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }


  function DatumErsetzen($wert)
  {
    return "neuerwerert";
  }

  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT email, emailbackup, ticket,autoresponder,
      id FROM emailbackup order by benutzername");
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
