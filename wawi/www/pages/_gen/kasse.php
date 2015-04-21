<?php 

class GenKasse { 

  function GenKasse(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","KasseCreate");
    $this->app->ActionHandler("edit","KasseEdit");
    $this->app->ActionHandler("copy","KasseCopy");
    $this->app->ActionHandler("list","KasseList");
    $this->app->ActionHandler("delete","KasseDelete");

    $this->app->Tpl->Set(HEADING,"Kasse");    $this->app->ActionHandlerListen($app);
  }

  function KasseCreate(){
    $this->app->Tpl->Set(HEADING,"Kasse (Anlegen)");
      $this->app->PageBuilder->CreateGen("kasse_create.tpl");
  }

  function KasseEdit(){
    $this->app->Tpl->Set(HEADING,"Kasse (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("kasse_edit.tpl");
  }

  function KasseCopy(){
    $this->app->Tpl->Set(HEADING,"Kasse (Kopieren)");
      $this->app->PageBuilder->CreateGen("kasse_copy.tpl");
  }

  function KasseDelete(){
    $this->app->Tpl->Set(HEADING,"Kasse (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("kasse_delete.tpl");
  }

  function KasseList(){
    $this->app->Tpl->Set(HEADING,"Kasse (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("kasse_list.tpl");
  }

} 
?>