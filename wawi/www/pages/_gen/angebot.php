<?php 

class GenAngebot { 

  function GenAngebot(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","AngebotCreate");
    $this->app->ActionHandler("edit","AngebotEdit");
    $this->app->ActionHandler("copy","AngebotCopy");
    $this->app->ActionHandler("list","AngebotList");
    $this->app->ActionHandler("delete","AngebotDelete");

    $this->app->Tpl->Set(HEADING,"Angebot");    $this->app->ActionHandlerListen($app);
  }

  function AngebotCreate(){
    $this->app->Tpl->Set(HEADING,"Angebot (Anlegen)");
      $this->app->PageBuilder->CreateGen("angebot_create.tpl");
  }

  function AngebotEdit(){
    $this->app->Tpl->Set(HEADING,"Angebot (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("angebot_edit.tpl");
  }

  function AngebotCopy(){
    $this->app->Tpl->Set(HEADING,"Angebot (Kopieren)");
      $this->app->PageBuilder->CreateGen("angebot_copy.tpl");
  }

  function AngebotDelete(){
    $this->app->Tpl->Set(HEADING,"Angebot (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("angebot_delete.tpl");
  }

  function AngebotList(){
    $this->app->Tpl->Set(HEADING,"Angebot (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("angebot_list.tpl");
  }

} 
?>