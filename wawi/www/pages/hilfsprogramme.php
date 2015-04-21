<?php

class Hilfsprogramme {
  var $app;
  
  function Hilfsprogramme($app) {
    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","HilfsprogrammeList");

    $this->app->DefaultActionHandler("list");

    $this->app->ActionHandlerListen($app);
  }



  function HilfsprogrammeList()
  {

    $this->app->erp->MenuEintrag("index.php?module=hilfsprogramme&action=list","&Uuml;bersicht");

    $this->app->YUI->TableSearch(TAB1,"druckerlist");
	
		$this->app->Tpl->Parse(PAGE,"tabview.tpl");
  }




}

?>
