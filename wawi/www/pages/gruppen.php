<?php
include ("_gen/gruppen.php");

class gruppen extends Gengruppen {
  var $app;
  
  function Gruppen($app) {
    //parent::Gengruppen($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","gruppenCreate");
    $this->app->ActionHandler("edit","gruppenEdit");
   	$this->app->ActionHandler("list","gruppenList");
   	$this->app->ActionHandler("delete","gruppenDelete");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }


  function GruppenCreate()
  {
    $this->gruppenMenu();
    parent::gruppenCreate();
  }

	function GruppenDelete()
	{
		$id = $this->app->Secure->GetGET("id");
		if(is_numeric($id))
		{
			$this->app->DB->Delete("DELETE FROM gruppen WHERE id='$id'");
		}

		$this->gruppenList();
	}


  function GruppenList()
  {
    $this->gruppenMenu();
    parent::gruppenList();
  }

  function GruppenMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag("index.php?module=gruppen&action=create","Gruppe anlegen");
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=gruppen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function GruppenEdit()
  {
    $this->gruppenMenu();
    parent::gruppenEdit();
  }





}

?>
