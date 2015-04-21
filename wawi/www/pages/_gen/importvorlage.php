<?php 

class GenImportvorlage { 

  function GenImportvorlage(&$app) { 

    $this->app=&$app;
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("create","ImportvorlageCreate");
    $this->app->ActionHandler("edit","ImportvorlageEdit");
    $this->app->ActionHandler("copy","ImportvorlageCopy");
    $this->app->ActionHandler("list","ImportvorlageList");
    $this->app->ActionHandler("delete","ImportvorlageDelete");

    $this->app->Tpl->Set(HEADING,"Importvorlage");    $this->app->ActionHandlerListen($app);
  }

  function ImportvorlageCreate(){
    $this->app->Tpl->Set(HEADING,"Importvorlage (Anlegen)");
      $this->app->PageBuilder->CreateGen("importvorlage_create.tpl");
  }

  function ImportvorlageEdit(){
    $this->app->Tpl->Set(HEADING,"Importvorlage (Bearbeiten)");
      $this->app->PageBuilder->CreateGen("importvorlage_edit.tpl");
  }

  function ImportvorlageCopy(){
    $this->app->Tpl->Set(HEADING,"Importvorlage (Kopieren)");
      $this->app->PageBuilder->CreateGen("importvorlage_copy.tpl");
  }

  function ImportvorlageDelete(){
    $this->app->Tpl->Set(HEADING,"Importvorlage (L&ouml;schen)");
      $this->app->PageBuilder->CreateGen("importvorlage_delete.tpl");
  }

  function ImportvorlageList(){
    $this->app->Tpl->Set(HEADING,"Importvorlage (&Uuml;bersicht)");
      $this->app->PageBuilder->CreateGen("importvorlage_list.tpl");
  }

} 
?>