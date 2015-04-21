<?php
include ("_gen/widget.gen.verbindlichkeit.php");

class WidgetVerbindlichkeit extends WidgetGenVerbindlichkeit 
{
  private $app;
  function WidgetVerbindlichkeit($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenVerbindlichkeit($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {
    $this->form->ReplaceFunction("adresse",$this,"ReplaceLieferant");
    $this->form->ReplaceFunction("zahlbarbis",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("rechnungsdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("skontobis",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("betrag",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("skonto",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("frachtkosten",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("summenormal",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("summeermaessigt",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("bestellung",$this,"ReplaceBestellung");

		for($i=1;$i<=15;$i++)
		{
    	$this->form->ReplaceFunction("bestellung".$i,$this,"ReplaceBestellung");
			$this->app->YUI->AutoComplete("bestellung".$i,"bestellung",1);
    	$this->form->ReplaceFunction("bestellung".$i."betrag",$this,"ReplaceDecimal");
		}

		$this->app->YUI->AutoComplete("adresse","lieferant");
		$this->app->YUI->AutoComplete("kunde","kunde");
		$this->app->YUI->AutoComplete("mitarbeiter","mitarbeiter");
		$this->app->YUI->AutoComplete("sonstige","adresse");
    $this->app->YUI->AutoComplete("kostenstelle","kostenstelle",1);
		$this->app->YUI->AutoComplete("bestellung","bestellung",1);

    $this->app->YUI->AutoComplete("sachkonto","sachkonto",1);

    $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
    $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
    $this->form->NewField($field);

		$this->app->YUI->DatePicker("zahlbarbis");
		$this->app->YUI->DatePicker("skontobis");
		$this->app->YUI->DatePicker("rechnungsdatum");


    $field = new HTMLSelect("art",0);
    $field->onchange="onchange_art(this.form.art.options[this.form.art.selectedIndex].value);";
    $field->AddOptionsSimpleArray(array('lieferant'=>'Lieferant','kunde'=>'Kunde','sonstige'=>'Sonstige'));
    $this->form->NewField($field);



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

 	function ReplaceBestellung($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceBestellung($db,$value,$fromform);
  }


 	function ReplaceLieferant($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceLieferant($db,$value,$fromform);
  }

 	function ReplaceKunde($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceKunde($db,$value,$fromform);
  }


 	function ReplaceMitarbeiter($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceMitarbeiter($db,$value,$fromform);
  }

 	function ReplaceAdresse($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceAdresse($db,$value,$fromform);
  }



  public function Table()
  {
    $table = new EasyTable($this->app);  
    $this->app->Tpl->Set($this->parsetarget,"<form action=\"\" method=\"post\">");
    $this->app->Tpl->Set(SUBSUBHEADING,"");
//    $table->Query("SELECT a.name, verbindlichkeit.betrag, verbindlichkeit.rechnung, DATE_FORMAT(verbindlichkeit.skontobis,'%d.%m.%Y') as bis,verbindlichkeit.id FROM verbindlichkeit, adresse a WHERE verbindlichkeit.adresse = a.id AND verbindlichkeit.bezahlt!=1 AND verbindlichkeit.skontobis <= NOW() AND verbindlichkeit.status!='bezahlt' AND verbindlichkeit.skonto > 0 order by verbindlichkeit.skontobis");

    $table->Query("SELECT 
		CONCAT('<input type=\"checkbox\" ',if(verbindlichkeit.zahlbarbis<=NOW(),'checked',''),if(verbindlichkeit.skontobis>=NOW(),'checked','')
			,' name=\"verbindlichkeit[]\" value=\"',verbindlichkeit.id,'\">') as auswahl, verbindlichkeit.id as 'nr.', a.name as lieferant, 
			if(a.swift='','fehlt - bitte nachtragen',a.swift) as BIC, 
			if(a.iban='','fehlt - bitte nachtragen',a.iban) as IBAN, 
			betrag,verbindlichkeit.betrag, verbindlichkeit.rechnung, 
		if(verbindlichkeit.skontobis='0000-00-00','-',if(verbindlichkeit.skontobis >=NOW(),
			CONCAT('<font color=red>',DATE_FORMAT(verbindlichkeit.skontobis,'%d.%m.%Y'),'</font>'),DATE_FORMAT(verbindlichkeit.skontobis,'%d.%m.%Y'))) as skonto_bis,
		if(verbindlichkeit.zahlbarbis='0000-00-00','-',DATE_FORMAT(verbindlichkeit.zahlbarbis,'%d.%m.%Y')) as zahlbar_bis,
		if(verbindlichkeit.skonto > 0,CONCAT(verbindlichkeit.skonto,' %'),'-') as skonto,	
			if(verbindlichkeit.status='','offen',verbindlichkeit.status) as status,
			verbindlichkeit.id FROM verbindlichkeit LEFT JOIN adresse a ON verbindlichkeit.adresse = a.id 
			WHERE verbindlichkeit.status!='bezahlt' Order by verbindlichkeit.skontobis, verbindlichkeit.zahlbarbis ");

 
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=verbindlichkeit&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.png\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=verbindlichkeit&action=delete&id=%value%';\">
          <img src=\"./themes/[THEME]/images/delete.gif\" border=\"0\"></a>
        <a onclick=\"if(!confirm('Wirklich als bezahlt markieren?')) return false; else window.location.href='index.php?module=verbindlichkeit&action=bezahlt&id=%value%';\">
        <img src=\"./themes/[THEME]/images/ack.png\" border=\"0\"></a>
        ");


    $this->app->Tpl->Parse($this->parsetarget,"rahmen70_ohne_form.tpl");

    $this->app->Tpl->Set(SUBSUBHEADING,"");
    $this->app->Tpl->Set(INHALT,"<center>Auswahl Konto:&nbsp;
				<select name=\"konto\">".$this->app->erp->GetSelectBICKonto()."</select>&nbsp;<input type=\"submit\" name=\"submit\" value=\"Sammel&uuml;berweisung herunterladen und Verbindlichkeit als bezahlt markieren\"></center></form>");
    $this->app->Tpl->Parse($this->parsetarget,"rahmen70_ohne_form.tpl");
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
