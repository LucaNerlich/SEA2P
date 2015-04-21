<?php 

class GenGruppen { 

  function GenGruppen(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","GruppenCreate");
    $this->app->ActionHandler("edit","GruppenEdit");
    $this->app->ActionHandler("copy","GruppenCopy");
    $this->app->ActionHandler("list","GruppenList");
    $this->app->ActionHandler("delete","GruppenDelete");

    $this->app->Tpl->Set(HEADING,"Gruppen");    $this->app->ActionHandlerListen($app);
  }

  function GruppenCreate(){
    $this->app->Tpl->Set(HEADING,"Gruppen (Anlegen)");
      $this->app->PageBuilder->CreateGen("gruppen_create.tpl");
  }

  function GruppenEdit(){
    $this->app->Tpl->Set(HEADING,"Gruppen (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("gruppen_edit.tpl");
  }

  function GruppenCopy(){
    $this->app->Tpl->Set(HEADING,"Gruppen (Kopieren)");
      $this->app->PageBuilder->CreateGen("gruppen_copy.tpl");
  }

  function GruppenDelete(){
    $this->app->Tpl->Set(HEADING,"Gruppen (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("gruppen_delete.tpl");
  }

  function GruppenList(){
    $this->app->Tpl->Set(HEADING,"Gruppen (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("gruppen_list.tpl");
  }

} 
?>