<?php 

class GenAnfrage { 

  function GenAnfrage(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","AnfrageCreate");
    $this->app->ActionHandler("edit","AnfrageEdit");
    $this->app->ActionHandler("copy","AnfrageCopy");
    $this->app->ActionHandler("list","AnfrageList");
    $this->app->ActionHandler("delete","AnfrageDelete");

    $this->app->Tpl->Set(HEADING,"Anfrage");    $this->app->ActionHandlerListen($app);
  }

  function AnfrageCreate(){
    $this->app->Tpl->Set(HEADING,"Anfrage (Anlegen)");
      $this->app->PageBuilder->CreateGen("anfrage_create.tpl");
  }

  function AnfrageEdit(){
    $this->app->Tpl->Set(HEADING,"Anfrage (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("anfrage_edit.tpl");
  }

  function AnfrageCopy(){
    $this->app->Tpl->Set(HEADING,"Anfrage (Kopieren)");
      $this->app->PageBuilder->CreateGen("anfrage_copy.tpl");
  }

  function AnfrageDelete(){
    $this->app->Tpl->Set(HEADING,"Anfrage (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("anfrage_delete.tpl");
  }

  function AnfrageList(){
    $this->app->Tpl->Set(HEADING,"Anfrage (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("anfrage_list.tpl");
  }

} 
?>