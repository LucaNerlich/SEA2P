<?php
include ("_gen/kostenstellen.php");

class Kostenstellen extends GenKostenstellen {
  var $app;
  
  function Kostenstellen($app) {
    //parent::GenKostenstellen($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","KostenstellenCreate");
    $this->app->ActionHandler("edit","KostenstellenEdit");
   	$this->app->ActionHandler("list","KostenstellenList");
   	$this->app->ActionHandler("delete","KostenstellenDelete");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }


  function KostenstellenCreate()
  {
    $this->KostenstellenMenu();
    parent::KostenstellenCreate();
  }

	function KostenstellenDelete()
	{
		$id = $this->app->Secure->GetGET("id");
		if(is_numeric($id))
		{
			$this->app->DB->Delete("DELETE FROM kostenstellen WHERE id='$id'");
		}

		$this->KostenstellenList();
	}


  function KostenstellenList()
  {
    $this->KostenstellenMenu();
    parent::KostenstellenList();
  }

  function KostenstellenMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag("index.php?module=kostenstellen&action=create","Kostenstellen anlegen");
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=kostenstellen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }

  function KostenstellenEdit()
  {
    $this->KostenstellenMenu();
    parent::KostenstellenEdit();
  }





}

?>
