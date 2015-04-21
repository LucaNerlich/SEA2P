<?php
include ("_gen/widget.gen.prozessstarter.php");

class WidgetProzessstarter extends WidgetGenProzessstarter 
{
  private $app;
  function WidgetProzessstarter($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenProzessstarter($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {
/*
    $this->form->ReplaceFunction("adresse",$this,"ReplaceAdresse");
    $this->form->ReplaceFunction("datum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("betrag",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");


    $this->app->YUI->AutoComplete(ADRESSEAUTO,"adresse",array('name'),"name","adresse");
    $this->app->YUI->AutoComplete(PROJEKTAUTO,"projekt",array('name','abkuerzung'),"abkuerzung");

    // liste zuweisen
    $this->app->Secure->POST["datum"]=date('Y-m-d');
    $field = new HTMLInput("datum","text",date('Y-m-d'));
    $this->form->NewField($field);

    // liste zuweisen
    $this->app->Secure->POST["bearbeiter"]=$this->app->User->GetName();
    $field = new HTMLInput("bearbeiter","hidden",$this->app->User->GetName());
    $this->form->NewField($field);
*/
    //firma
    $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
    $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
    $this->form->NewField($field);

    $this->app->Tpl->Set(DATUM_BUCHUNG,
        "<img src=\"./themes/[THEME]/images/kalender.png\" onclick=\"displayCalendar(document.forms[0].datum,'dd.mm.yyyy',this)\">");

  }
  function ReplaceProjekt($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$id' LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$value' LIMIT 1");
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

  function ReplaceDecimal($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden

    return str_replace(",",".",$value);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(strpos($value,'-') > 0) $dbformat = 1;

    // wenn ziel datenbank
    if($db)
    { 
      if($dbformat) return $value;
      else return $this->app->String->Convert($value,"%1.%2.%3","%3-%2-%1");
    }
    // wenn ziel formular
    else
    { 
      if($dbformat) return $this->app->String->Convert($value,"%1-%2-%3","%3.%2.%1");
      else return $value;
    }
  }

  function ReplaceAdresse($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM adresse WHERE name='$value' AND geloescht=0 LIMIT 1");
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

  public function Table()
  {
		$this->app->YUI->TableSearch(INHALT,"prozessstarterlist");
    $this->app->Tpl->Parse($this->parsetarget,"rahmen70.tpl");

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
