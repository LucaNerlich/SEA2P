<?php 

class GenProzessstarter { 

  function GenProzessstarter(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ProzessstarterCreate");
    $this->app->ActionHandler("edit","ProzessstarterEdit");
    $this->app->ActionHandler("copy","ProzessstarterCopy");
    $this->app->ActionHandler("list","ProzessstarterList");
    $this->app->ActionHandler("delete","ProzessstarterDelete");

    $this->app->Tpl->Set(HEADING,"Prozessstarter");    $this->app->ActionHandlerListen($app);
  }

  function ProzessstarterCreate(){
    $this->app->Tpl->Set(HEADING,"Prozessstarter (Anlegen)");
      $this->app->PageBuilder->CreateGen("prozessstarter_create.tpl");
  }

  function ProzessstarterEdit(){
    $this->app->Tpl->Set(HEADING,"Prozessstarter (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("prozessstarter_edit.tpl");
  }

  function ProzessstarterCopy(){
    $this->app->Tpl->Set(HEADING,"Prozessstarter (Kopieren)");
      $this->app->PageBuilder->CreateGen("prozessstarter_copy.tpl");
  }

  function ProzessstarterDelete(){
    $this->app->Tpl->Set(HEADING,"Prozessstarter (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("prozessstarter_delete.tpl");
  }

  function ProzessstarterList(){
    $this->app->Tpl->Set(HEADING,"Prozessstarter (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("prozessstarter_list.tpl");
  }

} 
?>