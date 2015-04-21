<?php
include ("_gen/verrechnungsart.php");

class Verrechnungsart extends GenVerrechnungsart {
  var $app;
  
  function Verrechnungsart($app) {
    //parent::GenVerrechnungsart($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","VerrechnungsartCreate");
    $this->app->ActionHandler("edit","VerrechnungsartEdit");
    $this->app->ActionHandler("list","VerrechnungsartList");
    $this->app->ActionHandler("delete","VerrechnungsartDelete");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }


  function VerrechnungsartCreate()
  {
    $this->VerrechnungsartMenu();
    parent::VerrechnungsartCreate();
  }


  function VerrechnungsartDelete()
  {
    $id = $this->app->Secure->GetGET("id");
    if(is_numeric($id))
    {
      $this->app->DB->Delete("DELETE FROM verrechnungsart WHERE id='$id'");
    }

    $this->VerrechnungsartList();
  }
  


  function VerrechnungsartList()
  {
    $this->VerrechnungsartMenu();
    parent::VerrechnungsartList();
  }

  function VerrechnungsartMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->erp->MenuEintrag("index.php?module=verrechnungsart&action=create","Verrechnungsart anlegen");
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=verrechnungsart&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }


  function VerrechnungsartEdit()
  {
    $this->VerrechnungsartMenu();
    parent::VerrechnungsartEdit();
  }





}

?>
