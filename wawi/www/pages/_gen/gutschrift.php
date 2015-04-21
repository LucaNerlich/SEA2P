<?php 

class GenGutschrift { 

  function GenGutschrift(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","GutschriftCreate");
    $this->app->ActionHandler("edit","GutschriftEdit");
    $this->app->ActionHandler("copy","GutschriftCopy");
    $this->app->ActionHandler("list","GutschriftList");
    $this->app->ActionHandler("delete","GutschriftDelete");

    $this->app->Tpl->Set(HEADING,"Gutschrift");    $this->app->ActionHandlerListen($app);
  }

  function GutschriftCreate(){
    $this->app->Tpl->Set(HEADING,"Gutschrift (Anlegen)");
      $this->app->PageBuilder->CreateGen("gutschrift_create.tpl");
  }

  function GutschriftEdit(){
    $this->app->Tpl->Set(HEADING,"Gutschrift (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("gutschrift_edit.tpl");
  }

  function GutschriftCopy(){
    $this->app->Tpl->Set(HEADING,"Gutschrift (Kopieren)");
      $this->app->PageBuilder->CreateGen("gutschrift_copy.tpl");
  }

  function GutschriftDelete(){
    $this->app->Tpl->Set(HEADING,"Gutschrift (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("gutschrift_delete.tpl");
  }

  function GutschriftList(){
    $this->app->Tpl->Set(HEADING,"Gutschrift (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("gutschrift_list.tpl");
  }

} 
?>