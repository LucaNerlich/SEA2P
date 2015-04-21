<?php
include ("_gen/warteschlangen.php");

class Warteschlangen extends GenWarteschlangen {
  var $app;
  
  function Warteschlangen($app) {
    //parent::GenWarteschlangen($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","WarteschlangenCreate");
    $this->app->ActionHandler("delete","WarteschlangenDelete");
    $this->app->ActionHandler("edit","WarteschlangenEdit");
    $this->app->ActionHandler("list","WarteschlangenList");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }

  function WarteschlangenDelete()
  {

    $id = $this->app->Secure->GetGET("id");

    if(is_numeric($id))
      $this->app->DB->Delete("DELETE FROM warteschlangen WHERE id='$id' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

    header("Location: index.php?module=warteschlangen&action=list");
    exit;
  }


  function WarteschlangenCreate()
  {
    $this->WarteschlangenMenu();
    parent::WarteschlangenCreate();
  }

  function WarteschlangenList()
  {
    $this->WarteschlangenMenu();
    parent::WarteschlangenList();
  }

  function WarteschlangenMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Warteschlangen");
    $this->app->erp->MenuEintrag("index.php?module=warteschlangen&action=create","Warteschlange anlegen");
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=warteschlangen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }


  function WarteschlangenEdit()
  {
    $this->WarteschlangenMenu();

    parent::WarteschlangenEdit();
  }





}

?>
