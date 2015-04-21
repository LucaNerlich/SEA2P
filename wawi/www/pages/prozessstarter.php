<?php
include ("_gen/prozessstarter.php");

class Prozessstarter extends GenProzessstarter {
  var $app;
  
  function Prozessstarter($app) {
    //parent::GenProzessstarter($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ProzessstarterCreate");
    $this->app->ActionHandler("edit","ProzessstarterEdit");
    $this->app->ActionHandler("list","ProzessstarterList");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }


  function ProzessstarterCreate()
  {
    $this->ProzessstarterMenu();
    parent::ProzessstarterCreate();
  }

  function ProzessstarterList()
  {
    $this->ProzessstarterMenu();
    parent::ProzessstarterList();
  }

  function ProzessstarterMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Prozessstarter");
    $this->app->erp->MenuEintrag("index.php?module=prozessstarter&action=create","Prozess anlegen");
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=prozessstarter&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }


  function ProzessstarterEdit()
  {
    $this->ProzessstarterMenu();

    parent::ProzessstarterEdit();
  }





}

?>
