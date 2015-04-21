<?php 

class GenRechnung { 

  function GenRechnung(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","RechnungCreate");
    $this->app->ActionHandler("edit","RechnungEdit");
    $this->app->ActionHandler("copy","RechnungCopy");
    $this->app->ActionHandler("list","RechnungList");
    $this->app->ActionHandler("delete","RechnungDelete");

    $this->app->Tpl->Set(HEADING,"Rechnung");    $this->app->ActionHandlerListen($app);
  }

  function RechnungCreate(){
    $this->app->Tpl->Set(HEADING,"Rechnung (Anlegen)");
      $this->app->PageBuilder->CreateGen("rechnung_create.tpl");
  }

  function RechnungEdit(){
    $this->app->Tpl->Set(HEADING,"Rechnung (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("rechnung_edit.tpl");
  }

  function RechnungCopy(){
    $this->app->Tpl->Set(HEADING,"Rechnung (Kopieren)");
      $this->app->PageBuilder->CreateGen("rechnung_copy.tpl");
  }

  function RechnungDelete(){
    $this->app->Tpl->Set(HEADING,"Rechnung (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("rechnung_delete.tpl");
  }

  function RechnungList(){
    $this->app->Tpl->Set(HEADING,"Rechnung (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("rechnung_list.tpl");
  }

} 
?>