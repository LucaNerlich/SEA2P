<?php
include ("_gen/etiketten.php");

class Etiketten extends GenEtiketten {
  var $app;
  
  function Etiketten($app) {
    //parent::GenEtiketten($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","EtikettenCreate");
    $this->app->ActionHandler("edit","EtikettenEdit");
   	$this->app->ActionHandler("list","EtikettenList");
   	$this->app->ActionHandler("delete","EtikettenDelete");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }


  function EtikettenCreate()
  {
    $this->EtikettenMenu();
    parent::EtikettenCreate();
  }

	function EtikettenDelete()
	{
		$id = $this->app->Secure->GetGET("id");
		if(is_numeric($id))
		{
			$this->app->DB->Delete("DELETE FROM etiketten WHERE id='$id'");
		}

		$this->EtikettenList();
	}


  function EtikettenList()
  {
    $this->EtikettenMenu();
    parent::EtikettenList();
  }

  function EtikettenMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag("index.php?module=etiketten&action=create","Neues Etikett anlegen");
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=etiketten&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function EtikettenEdit()
  {
    $this->EtikettenMenu();
    parent::EtikettenEdit();
  }





}

?>
