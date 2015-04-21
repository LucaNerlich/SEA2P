<?php 

class GenVerbindlichkeit { 

  function GenVerbindlichkeit(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","VerbindlichkeitCreate");
    $this->app->ActionHandler("edit","VerbindlichkeitEdit");
    $this->app->ActionHandler("copy","VerbindlichkeitCopy");
    $this->app->ActionHandler("list","VerbindlichkeitList");
    $this->app->ActionHandler("delete","VerbindlichkeitDelete");

    $this->app->Tpl->Set(HEADING,"Verbindlichkeit");    $this->app->ActionHandlerListen($app);
  }

  function VerbindlichkeitCreate(){
    $this->app->Tpl->Set(HEADING,"Verbindlichkeit (Anlegen)");
      $this->app->PageBuilder->CreateGen("verbindlichkeit_create.tpl");
  }

  function VerbindlichkeitEdit(){
    $this->app->Tpl->Set(HEADING,"Verbindlichkeit (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("verbindlichkeit_edit.tpl");
  }

  function VerbindlichkeitCopy(){
    $this->app->Tpl->Set(HEADING,"Verbindlichkeit (Kopieren)");
      $this->app->PageBuilder->CreateGen("verbindlichkeit_copy.tpl");
  }

  function VerbindlichkeitDelete(){
    $this->app->Tpl->Set(HEADING,"Verbindlichkeit (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("verbindlichkeit_delete.tpl");
  }

  function VerbindlichkeitList(){
    $this->app->Tpl->Set(HEADING,"Verbindlichkeit (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("verbindlichkeit_list.tpl");
  }

} 
?>