<?php
include ("_gen/widget.gen.kasse.php");

class WidgetKasse extends WidgetGenKasse 
{
  private $app;
  function WidgetKasse($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenKasse($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {

    $this->app->YUI->DatePicker("datum");


    $this->form->ReplaceFunction("adresse",$this,"ReplaceKunde");
    $this->form->ReplaceFunction("datum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("betrag",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");

    $this->app->YUI->AutoComplete("adresse","kunde");
    $this->app->YUI->AutoComplete("projekt","projektname",1);

    // liste zuweisen
    $action=$this->app->Secure->GetGET("create");
    if($action=="create")
    {
//    $this->app->Secure->POST["datum"]=date('Y-m-d');
//    $field = new HTMLInput("datum","text",date('Y-m-d'));
//    $this->form->NewField($field);
    }
    // liste zuweisen
    $this->app->Secure->POST["bearbeiter"]=$this->app->User->GetName();
    $field = new HTMLInput("bearbeiter","hidden",$this->app->User->GetName());
    $this->form->NewField($field);

    //firma
    $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
    $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
    $this->form->NewField($field);

    $this->app->Tpl->Set(DATUM_BUCHUNG,
        "<img src=\"./themes/[THEME]/images/kalender.png\" onclick=\"displayCalendar(document.forms[0].datum,'dd.mm.yyyy',this)\">");

  }

  function ReplaceKunde($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceKunde($db,$value,$fromform);
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

    $table = new EasyTable($this->app);  
    $this->app->Tpl->Set(INHALT,"");
    $this->app->Tpl->Set(SUBSUBHEADING,"Kasse");
    $table->Query("SELECT if(kasse.adresse>0,a.name,'---') as Kunde, DATE_FORMAT(kasse.datum,'%d.%m.%Y') as datum, betrag, auswahl as typ, grund, kasse.id 
      FROM kasse LEFT JOIN adresse a  ON kasse.adresse = a.id WHERE kasse.exportiert!=1 ORDER BY kasse.datum DESC,kasse.id DESC");
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=kasse&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.png\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=kasse&action=delete&id=%value%';\">
          <img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\"></a>
        ");

    $this->app->Tpl->Set(EXTEND,"<input type=\"button\" value=\"Kasse exportieren\" onclick=\"window.location.href='index.php?module=kasse&action=exportieren'\">");
    $this->app->Tpl->Parse($this->parsetarget,"rahmen70.tpl");

    $this->app->Tpl->Set(INHALT,"");
    $this->app->Tpl->Add(INHALT,"<h2>Bereits exportierte Eintr&auml;ge:</h2>");
    //$table->Query("SELECT a.name, betrag, auswahl as typ, grund FROM kasse, adresse a WHERE kasse.adresse = a.id AND kasse.exportiert=1");
    $table->Query("SELECT if(kasse.adresse>0,a.name,'---') as Kunde, DATE_FORMAT(kasse.datum,'%d.%m.%Y') as datum, betrag, auswahl as typ, grund, kasse.id 
	FROM kasse LEFT JOIN adresse a  ON kasse.adresse = a.id WHERE kasse.exportiert=1 ORDER BY kasse.datum DESC,kasse.id DESC");
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=kasse&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.png\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=kasse&action=delete&id=%value%';\">
          <img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\"></a>
        ");


    //$table->DisplayNew(INHALT, "Grund","noAction");
    $this->app->Tpl->Set(EXTEND,"");
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
