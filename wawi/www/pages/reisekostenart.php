<?php
include ("_gen/reisekostenart.php");

class Reisekostenart extends GenReisekostenart {
  var $app;
  
  function Reisekostenart($app) {
    //parent::GenReisekostenart($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ReisekostenartCreate");
    $this->app->ActionHandler("edit","ReisekostenartEdit");
   	$this->app->ActionHandler("list","ReisekostenartList");
   	$this->app->ActionHandler("delete","ReisekostenartDelete");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }


  function ReisekostenartCreate()
  {
    $this->ReisekostenartMenu();
    parent::ReisekostenartCreate();
  }

	function ReisekostenartDelete()
	{
		$id = $this->app->Secure->GetGET("id");
		if(is_numeric($id))
		{
			$this->app->DB->Delete("DELETE FROM reisekostenart WHERE id='$id'");
		}

		$this->ReisekostenartList();
	}


  function ReisekostenartList()
  {
    $this->ReisekostenartMenu();
    parent::ReisekostenartList();
  }

  function ReisekostenartMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag("index.php?module=reisekostenart&action=create","Reisekostenart anlegen");
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=reisekostenart&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function ReisekostenartEdit()
  {
    $this->ReisekostenartMenu();
    parent::ReisekostenartEdit();
  }





}

?>
