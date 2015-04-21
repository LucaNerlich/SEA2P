<?php
include ("_gen/kontorahmen.php");

class Kontorahmen extends GenKontorahmen {
  var $app;
  
  function Kontorahmen($app) {
    //parent::GenKontorahmen($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","KontorahmenCreate");
    $this->app->ActionHandler("edit","KontorahmenEdit");
   	$this->app->ActionHandler("list","KontorahmenList");
   	$this->app->ActionHandler("delete","KontorahmenDelete");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }


  function KontorahmenCreate()
  {
    $this->KontorahmenMenu();
    parent::KontorahmenCreate();
  }

	function KontorahmenDelete()
	{
		$id = $this->app->Secure->GetGET("id");
		if(is_numeric($id))
		{
			$this->app->DB->Delete("DELETE FROM kontorahmen WHERE id='$id'");
		}

		$this->KontorahmenList();
	}


  function KontorahmenList()
  {
    $this->KontorahmenMenu();
    parent::KontorahmenList();
  }

  function KontorahmenMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag("index.php?module=kontorahmen&action=create","Neues Sachkonto anlegen");
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=kontorahmen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function KontorahmenEdit()
  {
    $this->KontorahmenMenu();
    parent::KontorahmenEdit();
  }





}

?>
