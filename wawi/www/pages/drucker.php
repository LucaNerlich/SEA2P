<?php
include ("_gen/drucker.php");

class Drucker extends GenDrucker {
  var $app;
  
  function Drucker($app) {
    //parent::GenDrucker($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","DruckerCreate");
    $this->app->ActionHandler("edit","DruckerEdit");
    $this->app->ActionHandler("delete","DruckerDelete");
    $this->app->ActionHandler("list","DruckerList");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }


  function DruckerCreate()
  {
    $this->DruckerMenu();
    parent::DruckerCreate();
  }

  function DruckerList()
  {
    $this->DruckerMenu();
    parent::DruckerList();
  }

  function DruckerDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id))
    {
      $this->app->DB->Delete("DELETE FROM drucker WHERE id='$id'");
    }

    //$this->DruckerList();
		header("Location: index.php?module=drucker&action=list");
		exit;
  }


  function DruckerMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    //$this->app->Tpl->Add(KURZUEBERSCHRIFT,"Drucker");
    $this->app->erp->MenuEintrag("index.php?module=drucker&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=drucker&action=create","Neu");
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=drucker&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }


  function DruckerEdit()
  {
    $this->DruckerMenu();

    parent::DruckerEdit();
  }





}

?>
