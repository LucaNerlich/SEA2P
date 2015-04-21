<?php 

class GenBerichte { 

  function GenBerichte(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","BerichteCreate");
    $this->app->ActionHandler("edit","BerichteEdit");
    $this->app->ActionHandler("copy","BerichteCopy");
    $this->app->ActionHandler("list","BerichteList");
    $this->app->ActionHandler("delete","BerichteDelete");

    $this->app->Tpl->Set(HEADING,"Berichte");    $this->app->ActionHandlerListen($app);
  }

  function BerichteCreate(){
    $this->app->Tpl->Set(HEADING,"Berichte (Anlegen)");
      $this->app->PageBuilder->CreateGen("berichte_create.tpl");
  }

  function BerichteEdit(){
    $this->app->Tpl->Set(HEADING,"Berichte (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("berichte_edit.tpl");
  }

  function BerichteCopy(){
    $this->app->Tpl->Set(HEADING,"Berichte (Kopieren)");
      $this->app->PageBuilder->CreateGen("berichte_copy.tpl");
  }

  function BerichteDelete(){
    $this->app->Tpl->Set(HEADING,"Berichte (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("berichte_delete.tpl");
  }

  function BerichteList(){
    $this->app->Tpl->Set(HEADING,"Berichte (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("berichte_list.tpl");
  }

} 
?>