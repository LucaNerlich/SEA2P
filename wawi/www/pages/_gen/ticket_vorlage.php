<?php 

class GenTicket_Vorlage { 

  function GenTicket_Vorlage(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","Ticket_VorlageCreate");
    $this->app->ActionHandler("edit","Ticket_VorlageEdit");
    $this->app->ActionHandler("copy","Ticket_VorlageCopy");
    $this->app->ActionHandler("list","Ticket_VorlageList");
    $this->app->ActionHandler("delete","Ticket_VorlageDelete");

    $this->app->Tpl->Set(HEADING,"Ticket_Vorlage");    $this->app->ActionHandlerListen($app);
  }

  function Ticket_VorlageCreate(){
    $this->app->Tpl->Set(HEADING,"Ticket_Vorlage (Anlegen)");
      $this->app->PageBuilder->CreateGen("ticket_vorlage_create.tpl");
  }

  function Ticket_VorlageEdit(){
    $this->app->Tpl->Set(HEADING,"Ticket_Vorlage (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("ticket_vorlage_edit.tpl");
  }

  function Ticket_VorlageCopy(){
    $this->app->Tpl->Set(HEADING,"Ticket_Vorlage (Kopieren)");
      $this->app->PageBuilder->CreateGen("ticket_vorlage_copy.tpl");
  }

  function Ticket_VorlageDelete(){
    $this->app->Tpl->Set(HEADING,"Ticket_Vorlage (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("ticket_vorlage_delete.tpl");
  }

  function Ticket_VorlageList(){
    $this->app->Tpl->Set(HEADING,"Ticket_Vorlage (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("ticket_vorlage_list.tpl");
  }

} 
?>