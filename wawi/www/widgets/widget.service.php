<?php
include ("_gen/widget.gen.service.php");

class WidgetService extends WidgetGenService 
{
  private $app;
  function WidgetService(&$app,$parsetarget)
  {
    $this->app = &$app;
    $this->parsetarget = $parsetarget;
    parent::WidgetGenService($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {
		$action = $this->app->Secure->GetGET("action");

    $this->app->YUI->AutoComplete("adresse","kunde");
    $this->app->YUI->AutoCompleteAdd("ansprechpartner","emailname");
    $this->app->YUI->AutoComplete("zuweisen","mitarbeiter");

    $this->app->YUI->AutoComplete("artikel","artikelname");

    $this->app->YUI->AutoCompleteAdd("antwortankundenempfaenger","emailname");
    $this->app->YUI->AutoCompleteAdd("antwortankundenkopie","emailname");
    $this->app->YUI->AutoCompleteAdd("antwortankundenblindkopie","emailname");
    $this->app->YUI->AutoComplete("seriennummer","seriennummer");

 		if($action=="create")
    {
      // liste zuweisen
			$pid = $this->app->DB->Select("SELECT UUID_SHORT()");
      $this->app->Secure->POST["nummer"]=$pid;
      $field = new HTMLInput("nummer","hidden",$pid);
      $this->form->NewField($field);

			$pid=date('Y-m-d H:i:s');
      $this->app->Secure->POST["datum"]=$pid;
      $field = new HTMLInput("datum","hidden",$pid);
      $this->form->NewField($field);

			$this->app->Secure->POST["angelegtvonuser"] = $this->app->User->GetID();
			$field = new HTMLInput("angelegtvonuser","hidden",$this->app->User->GetID());
			$this->form->NewField($field);
    }


    //$this->app->YUI->DatePicker("datum");
    $this->app->YUI->DatePicker("erledigenbis");


    $this->form->ReplaceFunction("erledigenbis",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("zuweisen",$this,"ReplaceMitarbeiter");
    $this->form->ReplaceFunction("artikel",$this,"ReplaceArtikel");
    $this->form->ReplaceFunction("adresse",$this,"ReplaceKunde");
  }

  function ReplaceMitarbeiter($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceMitarbeiter($db,$value,$fromform);
  }

  function ReplaceArtikel($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceArtikel($db,$value,$fromform);
  }

  function ReplaceKunde($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceKunde($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }



}
?>
