<?php
include ("_gen/ticket_vorlage.php");

class Ticket_vorlage extends GenTicket_vorlage {
  var $app;
  
  function Ticket_vorlage($app) {
    //parent::GenTicket_vorlage($app);
    $this->app=&$app;

    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Ticket_vorlageCreate");
    $this->app->ActionHandler("delete","Ticket_vorlageDelete");
    $this->app->ActionHandler("edit","Ticket_vorlageEdit");
    $this->app->ActionHandler("list","Ticket_vorlageList");

    $this->app->ActionHandlerListen($app);

    $this->app = $app;
  }

  function Ticket_vorlageDelete()
  {

    $id = $this->app->Secure->GetGET("id");

    if(is_numeric($id))
      $this->app->DB->Delete("DELETE FROM ticket_vorlage WHERE id='$id' AND firma='".$this->app->User->GetFirma()."' LIMIT 1");

    header("Location: index.php?module=ticket_vorlage&action=list");
    exit;
  }


  function Ticket_vorlageCreate()
  {
    $this->Ticket_vorlageMenu();
    parent::Ticket_vorlageCreate();
  }

  function Ticket_vorlageList()
  {
    $this->Ticket_vorlageMenu();
    parent::Ticket_vorlageList();
  }

  function Ticket_vorlageMenu()
  {
    $id = $this->app->Secure->GetGET("id");
    $this->app->Tpl->Set(KURZUEBERSCHRIFT,"Ticket Vorlagen");
    $this->app->erp->MenuEintrag("index.php?module=ticket_vorlage&action=create","Vorlage anlegen");
    if($this->app->Secure->GetGET("action")=="list")
    $this->app->erp->MenuEintrag("index.php?module=einstellungen&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    else
    $this->app->erp->MenuEintrag("index.php?module=ticket_vorlage&action=list","Zur&uuml;ck zur &Uuml;bersicht");
  }


  function Ticket_vorlageEdit()
  {
    $this->Ticket_vorlageMenu();

    parent::Ticket_vorlageEdit();
  }





}

?>
