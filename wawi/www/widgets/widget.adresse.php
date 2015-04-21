<?php
include ("_gen/widget.gen.adresse.php");

class WidgetAdresse extends WidgetGenAdresse 
{
  private $app;
  function WidgetAdresse($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenAdresse($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {

    $action = $this->app->Secure->GetGET("action");

    $this->app->YUI->DatePicker("geburtstag");
    $this->app->YUI->DatePicker("mandatsreferenzdatum");
    $this->form->ReplaceFunction("geburtstag",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("mandatsreferenzdatum",$this,"ReplaceDatum");

    $this->app->YUI->AutoComplete("vertrieb","adresse");
    $this->app->YUI->AutoComplete("innendienst","adresse");
    $this->form->ReplaceFunction("vertrieb",$this,"ReplaceAdresse");
    $this->form->ReplaceFunction("innendienst",$this,"ReplaceAdresse");
      
    if($action=="create")
    {
      // liste zuweisen
      $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
      $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
      $this->form->NewField($field);

 			$zahlungsweise = $this->app->erp->GetZahlungsweise();
    	$field = new HTMLSelect("zahlungsweise",0);
			if($this->app->Secure->POST["zahlungsweise"]=="")
      $field->value=$this->app->erp->StandardZahlungsweise($projekt);
    	//$field->onchange="aktion_buchen(this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value);";
    	$field->AddOptionsSimpleArray($zahlungsweise);
    	$this->form->NewField($field);
    }
		else {

    $zahlungsweise = $this->app->erp->GetZahlungsweise();
    $field = new HTMLSelect("zahlungsweise",0);
    //$field->onchange="aktion_buchen(this.form.zahlungsweise.options[this.form.zahlungsweise.selectedIndex].value);";
    $field->AddOptionsSimpleArray($zahlungsweise);
    $this->form->NewField($field);

		}
    $field = new HTMLInput("land","hidden","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_land","hidden","");
    $this->form->NewField($field);


    $versandart = $this->app->erp->GetVersandartAuftrag();
    $field = new HTMLSelect("versandart",0);
    $field->AddOptionsSimpleArray($versandart);
    $this->form->NewField($field);

    $verrechnungskontoreisekosten = $this->app->erp->GetVerrechnungskontenReisekosten();
    $field = new HTMLSelect("verrechnungskontoreisekosten",0);
    $field->AddOptionsAsocSimpleArray($verrechnungskontoreisekosten);
    $this->form->NewField($field);


    $field = new HTMLSelect("zahlungsweiselieferant",0);
    //$field->onchange="aktion_buchen(this.form.zahlungsweiselieferant.options[this.form.zahlungsweise.selectedIndex].value);";
    $field->AddOptionsSimpleArray($zahlungsweise);
    $this->form->NewField($field);

    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");

 		$field = new HTMLCheckbox("abweichende_rechnungsadresse","","","1");
    $field->onclick="abweichend(this.form.abweichende_rechnungsadresse.value);";
    $this->form->NewField($field);



		$typOptions = $this->app->erp->GetTypSelect();
		$field = new HTMLSelect("typ",0);
    $field->onchange="onchange_typ(this.form.typ.options[this.form.typ.selectedIndex].value);";
    $field->AddOptionsSimpleArray($typOptions);
    $this->form->NewField($field);

		/*
		$vorname = $this->app->Secure->GetPOST('vorname');
		if($vorname!='') {
			$id = $this->app->Secure->GetGET('id');

			if(!($id!='' && is_numeric($id)))
				$id = $this->app->DB->Select('SELECT id FROM adresse ORDER BY id DESC LIMIT 1');

			if(is_numeric($id) && $id>0)
				$this->app->DB->Update("UPDATE adresse SET vorname='$vorname' WHERE id='$id' LIMIT 1");
	
		}
		*/

		$field = new HTMLInput("vorname","hidden","");
    $this->form->NewField($field);

/*
		$id = $this->app->Secure->GetGET('id');
    if(is_numeric($id) && $id>0) {
      $vorname = $this->app->DB->Select("SELECT vorname FROM adresse WHERE id='$id' LIMIT 1");
      $typ = $this->app->DB->Select("SELECT typ FROM adresse WHERE id='$id' LIMIT 1");
      $this->app->Tpl->Set('ADRESSEVORNAME', $vorname);
      $this->app->Tpl->Set('ADRESSETYP', $typ);
    }
*/
  }

  function ReplaceAdresse($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceAdresse($db,$value,$fromform);
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }



}
?>
