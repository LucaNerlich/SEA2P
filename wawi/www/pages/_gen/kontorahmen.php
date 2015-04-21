<?php 

class GenKontorahmen { 

  function GenKontorahmen(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","KontorahmenCreate");
    $this->app->ActionHandler("edit","KontorahmenEdit");
    $this->app->ActionHandler("copy","KontorahmenCopy");
    $this->app->ActionHandler("list","KontorahmenList");
    $this->app->ActionHandler("delete","KontorahmenDelete");

    $this->app->Tpl->Set(HEADING,"Kontorahmen");    $this->app->ActionHandlerListen($app);
  }

  function KontorahmenCreate(){
    $this->app->Tpl->Set(HEADING,"Kontorahmen (Anlegen)");
      $this->app->PageBuilder->CreateGen("kontorahmen_create.tpl");
  }

  function KontorahmenEdit(){
    $this->app->Tpl->Set(HEADING,"Kontorahmen (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("kontorahmen_edit.tpl");
  }

  function KontorahmenCopy(){
    $this->app->Tpl->Set(HEADING,"Kontorahmen (Kopieren)");
      $this->app->PageBuilder->CreateGen("kontorahmen_copy.tpl");
  }

  function KontorahmenDelete(){
    $this->app->Tpl->Set(HEADING,"Kontorahmen (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("kontorahmen_delete.tpl");
  }

  function KontorahmenList(){
    $this->app->Tpl->Set(HEADING,"Kontorahmen (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("kontorahmen_list.tpl");
  }

} 
?>