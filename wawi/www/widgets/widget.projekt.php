<?php
include ("_gen/widget.gen.projekt.php");

class WidgetProjekt extends WidgetGenProjekt 
{
  private $app;
  function WidgetProjekt($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenProjekt($app,$parsetarget);
    $this->ExtendsForm();
    $this->ExtendsOutput();
  }

  function ExtendsForm()
  {
    $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
    $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
    $this->form->NewField($field);

    $this->app->YUI->ColorPicker("farbe");
    $this->app->YUI->AutoComplete("kunde","kunde");

    $this->app->YUI->AutoComplete("abkuerzung","projektname",1);

    $this->app->YUI->AutoComplete("auftragid","auftrag",1);
    $this->form->ReplaceFunction("auftragid",$this,"ReplaceAuftrag");
    $this->form->ReplaceFunction("kunde",$this,"ReplaceKunde");
    $this->form->ReplaceFunction("abkuerzung",$this,"ReplaceAbkuerzung");

    $versandart = $this->app->erp->GetDrucker();
    $field = new HTMLSelect("druckerlogistikstufe1",0);
    $field->AddOptionsAsocSimpleArray($versandart);
    $this->form->NewField($field);


    $versandart = $this->app->erp->GetDrucker();
    $field = new HTMLSelect("druckerlogistikstufe2",0);
    $field->AddOptionsAsocSimpleArray($versandart);
    $this->form->NewField($field);

    $versandart = $this->app->erp->GetDrucker();
    $field = new HTMLSelect("intraship_drucker",0);
    $field->AddOptionsAsocSimpleArray($versandart);
    $this->form->NewField($field);



/*
		$allowed = "/[^a-z0-9]/i"; 
		preg_replace($allowed,"",$this->app->Secure->POST["abkuerzung"]); 
		$this->app->Secure->POST["abkuerzung"]=strtoupper($this->app->Secure->POST["abkuerzung"]);
*/



//    $this->app->YUI->AutoComplete(ADRESSEAUTO,"adresse",array('name','ort','mitarbeiternummer'),"CONCAT(mitarbeiternummer,' ',name)","mitarbeiter");
    $this->app->YUI->AutoComplete("verantwortlicher","mitarbeiter");

    $this->form->ReplaceFunction("adresse",$this,"ReplaceMitarbeiter");

  }

  function ExtendsOutput()
  //function __destruct()
  {
    // formatierte extra ausgaben aus datenbank
    //LIEFERSCHEINBRIEFPAPIER

    $id = $this->app->Secure->GetGET("id");

		if(is_numeric($id))
    $lieferscheinbriefpapier = $this->app->DB->Select("SELECT lieferscheinbriefpapier FROM projekt WHERE id='$id' LIMIT 1");

    $this->app->Tpl->Set(LIEFERSCHEINBRIEFPAPIEROPTIONS,"<option value=1>test $lieferscheinbriefpapier wert 1</option><option value=2>test $lieferscheinbriefpapier wert 2</option>");

  }


  function ReplaceAbkuerzung($db,$abkuerzung,$fromform)
	{
  	$allowed = "/[^a-z0-9]/i";
    $abkuerzung = preg_replace($allowed,"",$abkuerzung);
    return substr(strtoupper($abkuerzung),0,20);
	}


  function ReplaceMitarbeiter($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceMitarbeiter($db,$value,$fromform);
  }

  function ReplaceAuftrag($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceAuftrag($db,$value,$fromform);
  }

  function ReplaceKunde($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceKunde($db,$value,$fromform);
  }




}
?>
